<?php
/**
 * Points Game taxonomy archive requests at template-game-archive.php,
 * replacing WordPress's automatic taxonomy-{slug}.php file matching.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'taxonomy_template', 'lunar_game_archive_template' );

/**
 * @param string $template Template path WordPress would otherwise use.
 */
function lunar_game_archive_template( string $template ): string {
	if ( ! function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) ) {
		return $template;
	}

	if ( ! is_tax( lunar_wiki_get_taxonomy_slug_game() ) ) {
		return $template;
	}

	$lunar_template = get_template_directory() . '/template-game-archive.php';

	return file_exists( $lunar_template ) ? $lunar_template : $template;
}