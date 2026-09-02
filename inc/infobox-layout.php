<?php
/**
 * Splits a Wiki Article's content into its Infobox and the rest.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mirrors the_content()'s reliance on the global $post — only call this
 * from inside the main loop of a singular template.
 *
 * @return array{has_infobox: bool, infobox_html: string, content_html: string}
 */
function lunar_get_article_layout(): array {
	$post = get_post();

	if ( ! $post instanceof WP_Post ) {
		return array(
			'has_infobox'  => false,
			'infobox_html' => '',
			'content_html' => '',
		);
	}

	$blocks = parse_blocks( $post->post_content );

	// parse_blocks() can return a leading entry with blockName === null
	// for stray whitespace between block comments, so the first "real"
	// block isn't always at index 0.
	$first_key = null;

	foreach ( $blocks as $key => $block ) {
		if ( null !== $block['blockName'] ) {
			$first_key = $key;
			break;
		}
	}

	$has_infobox = null !== $first_key
		&& 'lunar-blocks/infobox' === $blocks[ $first_key ]['blockName'];

	if ( ! $has_infobox ) {
		return array(
			'has_infobox'  => false,
			'infobox_html' => '',
			'content_html' => apply_filters( 'the_content', $post->post_content ),
		);
	}

	$infobox_block = $blocks[ $first_key ];
	unset( $blocks[ $first_key ] );

	// Re-serializing the remaining blocks (instead of rendering each one
	// directly) keeps them on the normal the_content pipeline — dynamic
	// blocks, embeds, and shortcodes still work as if Infobox were never
	// removed.
	$remaining_content = serialize_blocks( array_values( $blocks ) );

	return array(
		'has_infobox'  => true,
		'infobox_html' => render_block( $infobox_block ),
		'content_html' => apply_filters( 'the_content', $remaining_content ),
	);
}