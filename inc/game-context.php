<?php
/**
 * Determines the current game context and resolves that game's
 * secondary navigation menu.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the specific game term (child-level, e.g. "Friends of Mineral
 * Town") relevant to the current request, or null outside of any game
 * context (e.g. homepage, search results, a franchise-level archive).
 *
 * @return WP_Term|null
 */
function lunar_get_current_game_term(): ?WP_Term {
	if ( ! function_exists( 'lunar_wiki_get_post_type_slug' ) || ! function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		return null;
	}

	$game_taxonomy = lunar_wiki_get_taxonomy_slug_game();

	if ( is_singular( lunar_wiki_get_post_type_slug() ) ) {
		$terms = get_the_terms( get_the_ID(), $game_taxonomy );

		if ( ! is_array( $terms ) ) {
			return null;
		}

		foreach ( $terms as $term ) {
			if ( 0 !== (int) $term->parent ) {
				return $term;
			}
		}

		return null;
	}

	if ( is_tax( $game_taxonomy ) ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term && 0 !== (int) $term->parent ) {
			return $term;
		}
	}

	return null;
}

/**
 * Returns the WordPress menu ID assigned to the current game term, or
 * null if there is no game context, no menu was assigned, or the
 * assigned menu no longer exists.
 *
 * @return int|null
 */
function lunar_get_game_secondary_menu_id(): ?int {
	$term = lunar_get_current_game_term();

	if ( ! $term ) {
		return null;
	}

	$menu_id = (int) get_term_meta( $term->term_id, 'lunar_wiki_secondary_menu_id', true );

	if ( $menu_id <= 0 || ! wp_get_nav_menu_object( $menu_id ) ) {
		return null;
	}

	return $menu_id;
}