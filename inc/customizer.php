<?php
/**
 * Customizer controls for overriding the theme's default colors.
 *
 * @package Lunar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

/**
 * Customizable color tokens: CSS variable name, default hex, and Customizer label.
 *
 * @return array<string, array{var: string, default: string, label: string}>
 */
function lunar_get_color_tokens(): array {
	return array(
		'background'       => array(
			'var'     => '--color-background',
			'default' => '#F2ECDC',
			'label'   => __( 'Background', 'lunar' ),
		),
		'surface'          => array(
			'var'     => '--color-surface',
			'default' => '#FBF8F0',
			'label'   => __( 'Surface', 'lunar' ),
		),
		'surface_2'        => array(
			'var'     => '--color-surface-2',
			'default' => '#F7F1E4',
			'label'   => __( 'Surface (Secondary)', 'lunar' ),
		),
		'border'           => array(
			'var'     => '--color-border',
			'default' => '#D9CBAA',
			'label'   => __( 'Border', 'lunar' ),
		),
		'text'             => array(
			'var'     => '--color-text',
			'default' => '#2B2015',
			'label'   => __( 'Text', 'lunar' ),
		),
		'text_muted'       => array(
			'var'     => '--color-text-muted',
			'default' => '#6B5D46',
			'label'   => __( 'Text (Muted)', 'lunar' ),
		),
		'accent'           => array(
			'var'     => '--color-accent',
			'default' => '#B2512E',
			'label'   => __( 'Accent', 'lunar' ),
		),
		'accent_secondary' => array(
			'var'     => '--color-accent-secondary',
			'default' => '#4B6B43',
			'label'   => __( 'Accent (Secondary)', 'lunar' ),
		),
	);
}

/**
 * Returns a color token's active value: the saved override, or its default.
 *
 * @param string $token_key Key from lunar_get_color_tokens(), e.g. 'accent'.
 * @return string Hex color, including the leading '#'.
 */
function lunar_get_color_value( string $token_key ): string {
	$tokens = lunar_get_color_tokens();

	if ( ! isset( $tokens[ $token_key ] ) ) {
		return '';
	}

	return get_theme_mod( "lunar_color_{$token_key}", $tokens[ $token_key ]['default'] );
}

/**
 * Registers the Lunar Style Settings panel, Colors section, and one
 * color picker control per token from lunar_get_color_tokens().
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function lunar_customize_register( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_panel(
		'lunar_style_settings',
		array(
			'title'       => __( 'Lunar Style Settings', 'lunar' ),
			'description' => __( 'Override the theme\'s default colors and fonts.', 'lunar' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'lunar_colors',
		array(
			'title' => __( 'Colors', 'lunar' ),
			'panel' => 'lunar_style_settings',
		)
	);

	foreach ( lunar_get_color_tokens() as $token_key => $token ) {
		$setting_id = "lunar_color_{$token_key}";

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $token['default'],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$setting_id,
				array(
					'section' => 'lunar_colors',
					'label'   => $token['label'],
				)
			)
		);
	}
}
add_action( 'customize_register', 'lunar_customize_register' );