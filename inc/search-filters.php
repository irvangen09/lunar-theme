<?php
/**
 * Search query modifications: restrict results to Wiki Article, apply
 * the Game filter, and apply the field-sync filters.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lunar_get_field_label( string $field_slug ): string {
	if ( function_exists( 'lunar_wiki_get_recognized_fields' ) ) {
		$fields = lunar_wiki_get_recognized_fields();

		if ( isset( $fields[ $field_slug ] ) ) {
			return $fields[ $field_slug ];
		}
	}

	return ucwords( str_replace( array( '_', '-' ), ' ', $field_slug ) );
}

function lunar_get_selected_game_slugs(): array {
	if ( ! isset( $_GET['games'] ) ) {
		return array();
	}

	return array_filter( array_map( 'sanitize_title', (array) wp_unslash( $_GET['games'] ) ) );
}

// Which fields to offer, and which values, computed from posts matching
// the current search/Game/Content Type filters — not a static list.
function lunar_get_active_field_filters(): array {
	if ( ! function_exists( 'lunar_wiki_get_recognized_fields' ) || ! function_exists( 'lunar_wiki_get_post_type_slug' ) ) {
		return array();
	}

	$args = array(
		'post_type'      => lunar_wiki_get_post_type_slug(),
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	);

	$search_term = get_search_query();
	if ( '' !== $search_term ) {
		$args['s'] = $search_term;
	}

	$tax_query = array();

	$content_type_slug = function_exists( 'lunar_wiki_get_taxonomy_slug_content_type' )
		? lunar_wiki_get_taxonomy_slug_content_type()
		: '';
	$active_tipe        = $content_type_slug ? sanitize_title( (string) get_query_var( $content_type_slug ) ) : '';

	if ( '' !== $active_tipe ) {
		$tax_query[] = array(
			'taxonomy' => $content_type_slug,
			'field'    => 'slug',
			'terms'    => $active_tipe,
		);
	}

	if ( isset( $_GET['games'] ) && function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		$selected_games = lunar_get_selected_game_slugs();

		if ( ! empty( $selected_games ) ) {
			$tax_query[] = array(
				'taxonomy' => lunar_wiki_get_taxonomy_slug_game(),
				'field'    => 'slug',
				'terms'    => $selected_games,
			);
		}
	}

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery
	}

	$matching_ids = get_posts( $args );

	if ( empty( $matching_ids ) ) {
		return array();
	}

	global $wpdb;

	$placeholders   = implode( ', ', array_fill( 0, count( $matching_ids ), '%d' ) );
	$active_filters = array();

	foreach ( lunar_wiki_get_recognized_fields() as $field_slug => $field_label ) {
		$meta_key = lunar_wiki_get_field_meta_key( $field_slug );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $placeholders is a fixed set of %d tokens, not raw input.
		$query = $wpdb->prepare(
			"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != '' AND post_id IN ( {$placeholders} )",
			array_merge( array( $meta_key ), $matching_ids )
		);

		$values = $wpdb->get_col( $query );

		if ( empty( $values ) ) {
			continue;
		}

		// Matched by label, not slug — slug depends on how the term was
		// created in wp-admin, the label is what actually gets typed.
		if ( 0 === strcasecmp( $field_label, 'Tool Tier' ) ) {
			$tier_order = array( 'Kayu', 'Perunggu', 'Perak', 'Emas', 'Mystrile' );
			usort(
				$values,
				static function ( $a, $b ) use ( $tier_order ) {
					return array_search( $a, $tier_order, true ) <=> array_search( $b, $tier_order, true );
				}
			);
		} else {
			sort( $values );
		}

		$active_filters[ $field_slug ] = $values;
	}

	return $active_filters;
}

function lunar_restrict_search_post_type( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	if ( ! function_exists( 'lunar_wiki_get_post_type_slug' ) ) {
		return;
	}

	$query->set( 'post_type', lunar_wiki_get_post_type_slug() );
}
add_action( 'pre_get_posts', 'lunar_restrict_search_post_type' );

// Dedicated "games[]" param — the game taxonomy's own query var only
// supports a single term slug (used on the Game archive page).
function lunar_filter_search_by_game( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	if ( ! function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		return;
	}

	$selected_games = lunar_get_selected_game_slugs();

	if ( empty( $selected_games ) ) {
		return;
	}

	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => lunar_wiki_get_taxonomy_slug_game(),
		'field'    => 'slug',
		'terms'    => $selected_games,
	);

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'lunar_filter_search_by_game' );

function lunar_filter_search_by_fields( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	if ( ! isset( $_GET['fields'] ) || ! is_array( $_GET['fields'] ) ) {
		return;
	}

	if ( ! function_exists( 'lunar_wiki_get_recognized_fields' ) ) {
		return;
	}

	$recognized_fields  = lunar_wiki_get_recognized_fields();
	$submitted_fields   = wp_unslash( $_GET['fields'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$meta_query         = (array) $query->get( 'meta_query' );
	$applied_any_filter = false;

	foreach ( $submitted_fields as $field => $values ) {
		$field = sanitize_key( $field );

		if ( ! array_key_exists( $field, $recognized_fields ) ) {
			continue;
		}

		$meta_key = lunar_wiki_get_field_meta_key( $field );
		$values   = array_filter( array_map( 'sanitize_text_field', (array) $values ) );

		if ( empty( $values ) ) {
			continue;
		}

		$meta_query[]       = array(
			'key'     => $meta_key,
			'value'   => $values,
			'compare' => 'IN',
		);
		$applied_any_filter = true;
	}

	if ( ! $applied_any_filter ) {
		return;
	}

	if ( count( $meta_query ) > 1 && ! isset( $meta_query['relation'] ) ) {
		$meta_query['relation'] = 'AND';
	}

	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'lunar_filter_search_by_fields' );

// Cancels the canonical redirect specifically when it would turn a
// Content Type pill click into a request for the taxonomy archive URL
// — that archive was never built, so the redirect would silently drop
// every other active filter.
function lunar_prevent_search_canonical_redirect( $redirect_url, string $requested_url ) {
	if ( false === $redirect_url || ! function_exists( 'lunar_wiki_get_taxonomy_slug_content_type' ) ) {
		return $redirect_url;
	}

	$content_type_slug = lunar_wiki_get_taxonomy_slug_content_type();

	if ( ! isset( $_GET[ $content_type_slug ] ) ) {
		return $redirect_url;
	}

	$content_type_tax = get_taxonomy( $content_type_slug );

	if ( ! ( $content_type_tax instanceof WP_Taxonomy ) ) {
		return $redirect_url;
	}

	$archive_slug = ( is_array( $content_type_tax->rewrite ) && ! empty( $content_type_tax->rewrite['slug'] ) )
		? $content_type_tax->rewrite['slug']
		: $content_type_tax->name;

	$redirect_path = (string) wp_parse_url( $redirect_url, PHP_URL_PATH );

	if ( false === strpos( $redirect_path, '/' . $archive_slug . '/' ) ) {
		return $redirect_url;
	}

	return false;
}
add_filter( 'redirect_canonical', 'lunar_prevent_search_canonical_redirect', 10, 2 );

// The Content Type archive was never built — redirect a direct visit
// to it into Search with that filter pre-applied instead of a generic
// unstyled template.
function lunar_redirect_content_type_archive(): void {
	if ( ! function_exists( 'lunar_wiki_get_taxonomy_slug_content_type' ) ) {
		return;
	}

	$content_type_slug = lunar_wiki_get_taxonomy_slug_content_type();

	if ( is_admin() || is_search() || ! is_tax( $content_type_slug ) ) {
		return;
	}

	$term = get_queried_object();

	if ( ! ( $term instanceof WP_Term ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/?s=&' . $content_type_slug . '=' . rawurlencode( $term->slug ) ), 301 );
	exit;
}
add_action( 'template_redirect', 'lunar_redirect_content_type_archive' );