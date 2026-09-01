<?php
/**
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'pre_get_posts', 'lunar_include_wiki_article_in_author_archive' );

function lunar_include_wiki_article_in_author_archive( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
		return;
	}

	// WordPress's author archive query only includes the native 'post'
	// post type by default; a custom post type has to be added explicitly.
	$post_types = array( 'post' );

	if ( function_exists( 'lunar_wiki_get_post_type_slug' ) ) {
		$post_types[] = lunar_wiki_get_post_type_slug();
	}

	$query->set( 'post_type', $post_types );
}