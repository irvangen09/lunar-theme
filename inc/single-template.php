<?php
/**
 * Points Wiki Article requests at template-wiki-article.php, replacing
 * WordPress's automatic single-{post_type}.php file matching.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'single_template', 'lunar_wiki_article_template' );

/**
 * @param string $template Template path WordPress would otherwise use.
 */
function lunar_wiki_article_template( string $template ): string {
	if ( ! function_exists( 'lunar_wiki_get_post_type_slug' ) ) {
		return $template;
	}

	if ( ! is_singular( lunar_wiki_get_post_type_slug() ) ) {
		return $template;
	}

	$lunar_template = get_template_directory() . '/template-wiki-article.php';

	return file_exists( $lunar_template ) ? $lunar_template : $template;
}