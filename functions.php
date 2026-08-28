<?php
/**
 * Theme bootstrap.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers theme supports and loads the text domain.
 */
function lunar_setup(): void {
	load_theme_textdomain( 'lunar', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);
}
add_action( 'after_setup_theme', 'lunar_setup' );

/**
 * Enqueues frontend assets.
 */
function lunar_enqueue_assets(): void {
	// Intentionally empty for now; populated as styles/scripts are ported.
}
add_action( 'wp_enqueue_scripts', 'lunar_enqueue_assets' );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/enqueue.php';