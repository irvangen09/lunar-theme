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
 * filemtime() instead of a shared static Version — so editing a single
 * asset busts its own cache immediately, without needing to remember to
 * bump the theme header on every change.
 */
function lunar_asset_version( string $relative_path ): string {
	$path = get_template_directory() . $relative_path;

	return file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );
}

/**
 * Enqueues the default design tokens, the main stylesheet, the shared
 * layout stylesheet, the header navigation script, any template-specific
 * stylesheets, the active Google Fonts, and outputs any Customizer
 * color/font overrides as inline CSS custom properties on top.
 */
function lunar_enqueue_styles(): void {
	wp_enqueue_style(
		'lunar-tokens',
		get_template_directory_uri() . '/assets/css/tokens.css',
		array(),
		lunar_asset_version( '/assets/css/tokens.css' )
	);

	wp_enqueue_style(
		'lunar-style',
		get_stylesheet_uri(),
		array( 'lunar-tokens' ),
		lunar_asset_version( '/style.css' )
	);

	wp_enqueue_style(
		'lunar-layout',
		get_template_directory_uri() . '/assets/css/layout.css',
		array( 'lunar-style' ),
		lunar_asset_version( '/assets/css/layout.css' )
	);

	wp_enqueue_script(
		'lunar-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		lunar_asset_version( '/assets/js/navigation.js' ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	// Template-specific stylesheets — only loaded on the template that needs them.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'lunar-homepage',
			get_template_directory_uri() . '/assets/css/homepage.css',
			array( 'lunar-style' ),
			lunar_asset_version( '/assets/css/homepage.css' )
		);
	}

	if ( function_exists( 'lunar_wiki_get_taxonomy_slug_game' ) && ( is_tax( lunar_wiki_get_taxonomy_slug_game() ) || is_author() ) ) {
		wp_enqueue_style(
			'lunar-archive',
			get_template_directory_uri() . '/assets/css/archive.css',
			array( 'lunar-style' ),
			lunar_asset_version( '/assets/css/archive.css' )
		);
	}

	$lunar_is_wiki_article = function_exists( 'lunar_wiki_get_post_type_slug' )
		&& is_singular( lunar_wiki_get_post_type_slug() );

	if ( is_author() || $lunar_is_wiki_article ) {
		wp_enqueue_style( 'dashicons' );
	}

	if ( is_author() ) {
		wp_enqueue_style(
			'lunar-author',
			get_template_directory_uri() . '/assets/css/author.css',
			array( 'lunar-style' ),
			lunar_asset_version( '/assets/css/author.css' )
		);
	}

	if ( $lunar_is_wiki_article ) {
		wp_enqueue_style(
			'lunar-single',
			get_template_directory_uri() . '/assets/css/single.css',
			array( 'lunar-style' ),
			lunar_asset_version( '/assets/css/single.css' )
		);
	}

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