<?php
/**
 * Game taxonomy query helpers for browsing/listing purposes (e.g. the
 * homepage's game tile grid) — distinct from game-context.php, which
 * only resolves the current request's context.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns every "Specific Title" game term (child-level only — Franchise
 * parent terms are excluded), alphabetically.
 *
 * @return WP_Term[]
 */
function lunar_get_game_terms(): array {
	if ( ! function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => lunar_wiki_get_taxonomy_slug_game(),
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( ! is_array( $terms ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$terms,
			static function ( $term ) {
				return 0 !== (int) $term->parent;
			}
		)
	);
}

/**
 * Returns the destination URL for a Game Tile — a custom URL if one is
 * set on the term (e.g. pointing to a Pillar Post instead of the game
 * archive), otherwise the default archive link.
 *
 * @param WP_Term $term A Game term (Specific Title level).
 * @return string
 */
function lunar_get_game_tile_url( WP_Term $term ): string {
	if ( function_exists( 'lunar_wiki_get_game_tile_url_meta_key' ) ) {
		$custom_url = (string) get_term_meta( $term->term_id, lunar_wiki_get_game_tile_url_meta_key(), true );

		if ( '' !== $custom_url ) {
			return $custom_url;
		}
	}

	$term_link = get_term_link( $term );

	return is_wp_error( $term_link ) ? '' : $term_link;
}

/**
 * Returns the attachment ID for a Game Tile's custom media, or 0 if none
 * is set — the caller falls back to the default initials placeholder in
 * that case (see front-page.php).
 *
 * @param WP_Term $term A Game term (Specific Title level).
 * @return int
 */
function lunar_get_game_tile_image_id( WP_Term $term ): int {
	if ( ! function_exists( 'lunar_wiki_get_game_tile_image_meta_key' ) ) {
		return 0;
	}

	return (int) get_term_meta( $term->term_id, lunar_wiki_get_game_tile_image_meta_key(), true );
}