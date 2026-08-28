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
 * Enqueues the main stylesheet and outputs any Customizer color overrides
 * as inline CSS custom properties on top of it.
 */
function lunar_enqueue_styles(): void {
	wp_enqueue_style( 'lunar-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );

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