<?php
/**
 * Theme bootstrap.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require get_template_directory() . '/inc/theme-setup.php';
require get_template_directory() . '/inc/google-fonts.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/game-context.php';
require get_template_directory() . '/inc/game-queries.php';
require get_template_directory() . '/inc/breadcrumb.php';
require get_template_directory() . '/inc/author-box.php';
require get_template_directory() . '/inc/author-query.php';
require get_template_directory() . '/inc/infobox-layout.php';
require get_template_directory() . '/inc/single-template.php';
require get_template_directory() . '/inc/search-filters.php';
require get_template_directory() . '/inc/enqueue.php';