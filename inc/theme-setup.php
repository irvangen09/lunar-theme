<?php
/**
 * Core theme setup — theme supports, nav menu locations, and content width.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers theme supports and nav menu locations.
 */
function lunar_theme_setup(): void {
	load_theme_textdomain( 'lunar', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/tokens.css' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_post_type_support( 'page', 'excerpt' );

	// "primary" is a normal, admin-managed menu shown outside any game
	// context. Inside a game context, header.php resolves a per-game menu
	// by ID instead (see inc/game-context.php) — "secondary" is registered
	// purely as a label so that menu shows an assigned location rather than
	// appearing unused; it is never read by wp_nav_menu().
	register_nav_menus(
		array(
			'primary'   => __( 'Main Menu', 'lunar' ),
			'secondary' => __( 'Secondary Menu', 'lunar' ),
			'footer'    => __( 'Footer Menu', 'lunar' ),
		)
	);
}
add_action( 'after_setup_theme', 'lunar_theme_setup' );

/**
 * Sets a default content width for oEmbeds and media sizing.
 */
function lunar_content_width(): void {
	$GLOBALS['content_width'] = apply_filters( 'lunar_content_width', 700 );
}
add_action( 'after_setup_theme', 'lunar_content_width', 0 );