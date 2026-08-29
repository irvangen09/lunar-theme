<?php
/**
 * Frontend stylesheet enqueuing and Customizer color overrides.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues the default design tokens, the main stylesheet, and outputs
 * any Customizer color overrides as inline CSS custom properties on top.
 */
function lunar_enqueue_styles(): void {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'lunar-tokens',
		get_template_directory_uri() . '/assets/css/tokens.css',
		array(),
		$version
	);

	wp_enqueue_style(
		'lunar-style',
		get_stylesheet_uri(),
		array( 'lunar-tokens' ),
		$version
	);

	$overrides = array();

	foreach ( lunar_get_color_tokens() as $token_key => $token ) {
		$value = lunar_get_color_value( $token_key );

		if ( $value !== $token['default'] ) {
			$overrides[] = sprintf( '%s: %s;', $token['var'], $value );
		}
	}

	if ( ! empty( $overrides ) ) {
		wp_add_inline_style( 'lunar-style', ':root{' . implode( ' ', $overrides ) . '}' );
	}
}
add_action( 'wp_enqueue_scripts', 'lunar_enqueue_styles' );