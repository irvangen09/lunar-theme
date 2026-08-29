<?php
/**
 * Statically bundled list of selectable Google Fonts.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Fonts available for selection in the Customizer, mapped to their
 * font category so the correct CSS generic fallback can be resolved.
 *
 * This list is a manually maintained snapshot, refreshed periodically —
 * not a live query against the Google Fonts Developer API.
 *
 * @return array<string, string> Font family name => category.
 */
function lunar_get_google_fonts(): array {
	return array(
		'Fraunces'         => 'serif',
		'Lora'             => 'serif',
		'Playfair Display' => 'serif',
		'Merriweather'     => 'serif',
		'Source Serif 4'   => 'serif',
		'Bitter'           => 'serif',
		'Inter'            => 'sans-serif',
		'Work Sans'        => 'sans-serif',
		'Nunito Sans'      => 'sans-serif',
		'Karla'            => 'sans-serif',
		'Rubik'            => 'sans-serif',
		'IBM Plex Mono'    => 'monospace',
		'JetBrains Mono'   => 'monospace',
		'Space Mono'       => 'monospace',
		'Roboto Mono'      => 'monospace',
	);
}