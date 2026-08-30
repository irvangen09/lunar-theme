<?php
/**
 * Frontend stylesheet/script enqueuing and Customizer color/font overrides.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues the Google Fonts currently active for the theme's typography
 * tokens (default or Customizer override), deduplicated across tokens.
 */
function lunar_enqueue_google_fonts(): void {
	$families = array();

	foreach ( lunar_get_font_tokens() as $token_key => $token ) {
		$families[] = lunar_get_font_value( $token_key );
	}

	$families = array_unique( array_filter( $families ) );

	if ( empty( $families ) ) {
		return;
	}

	$family_params = array();

	foreach ( $families as $family ) {
		$family_params[] = 'family=' . rawurlencode( $family ) . ':wght@400;700';
	}

	$url = 'https://fonts.googleapis.com/css2?' . implode( '&', $family_params ) . '&display=swap';

	wp_enqueue_style( 'lunar-google-fonts', $url, array(), null );
}

/**
 * Adds a preconnect resource hint for the Google Fonts asset host.
 *
 * @param array<int, mixed> $urls          Resource hint URLs/attributes.
 * @param string            $relation_type Type of hint being processed.
 * @return array<int, mixed>
 */
function lunar_resource_hints( array $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => true,
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'lunar_resource_hints', 10, 2 );

/**
 * Enqueues the default design tokens, the main stylesheet, the shared
 * layout stylesheet, the header navigation script, the active Google
 * Fonts, and outputs any Customizer color/font overrides as inline CSS
 * custom properties on top.
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

	wp_enqueue_style(
		'lunar-layout',
		get_template_directory_uri() . '/assets/css/layout.css',
		array( 'lunar-style' ),
		$version
	);

	wp_enqueue_script(
		'lunar-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	lunar_enqueue_google_fonts();

	$overrides = array();

	foreach ( lunar_get_color_tokens() as $token_key => $token ) {
		$value = lunar_get_color_value( $token_key );

		if ( $value !== $token['default'] ) {
			$overrides[] = sprintf( '%s: %s;', $token['var'], $value );
		}
	}

	foreach ( lunar_get_font_tokens() as $token_key => $token ) {
		$family = lunar_get_font_value( $token_key );

		if ( $family !== $token['default'] ) {
			$overrides[] = sprintf( '%s: %s;', $token['var'], lunar_get_font_css_value( $token_key ) );
		}
	}

	if ( ! empty( $overrides ) ) {
		wp_add_inline_style( 'lunar-style', ':root{' . implode( ' ', $overrides ) . '}' );
	}
}
add_action( 'wp_enqueue_scripts', 'lunar_enqueue_styles' );