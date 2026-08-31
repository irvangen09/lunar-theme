<?php
/**
 * Customizer controls for overriding the theme's default colors, fonts,
 * and homepage section text.
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
 * Customizable font tokens: CSS variable name, default family, and Customizer label.
 *
 * @return array<string, array{var: string, default: string, label: string}>
 */
function lunar_get_font_tokens(): array {
	return array(
		'display' => array(
			'var'     => '--font-display',
			'default' => 'Fraunces',
			'label'   => __( 'Display Font', 'lunar' ),
		),
		'body'    => array(
			'var'     => '--font-body',
			'default' => 'Lora',
			'label'   => __( 'Body Font', 'lunar' ),
		),
		'mono'    => array(
			'var'     => '--font-mono',
			'default' => 'IBM Plex Mono',
			'label'   => __( 'Monospace Font', 'lunar' ),
		),
	);
}

/**
 * Returns a font token's active family name: the saved override, or its default.
 *
 * @param string $token_key Key from lunar_get_font_tokens(), e.g. 'display'.
 * @return string Google Fonts family name.
 */
function lunar_get_font_value( string $token_key ): string {
	$tokens = lunar_get_font_tokens();

	if ( ! isset( $tokens[ $token_key ] ) ) {
		return '';
	}

	return get_theme_mod( "lunar_font_{$token_key}", $tokens[ $token_key ]['default'] );
}

/**
 * Returns a font token's active value as a ready-to-use CSS font-family
 * declaration, with the generic fallback resolved from its bundled category.
 *
 * @param string $token_key Key from lunar_get_font_tokens(), e.g. 'display'.
 * @return string e.g. "'Fraunces', serif".
 */
function lunar_get_font_css_value( string $token_key ): string {
	$family   = lunar_get_font_value( $token_key );
	$fonts    = lunar_get_google_fonts();
	$category = $fonts[ $family ] ?? 'sans-serif';

	return "'{$family}', {$category}";
}

/**
 * Sanitizes a font selection, falling back to the setting's own default if
 * the submitted value isn't a recognized bundled Google Fonts family.
 *
 * @param string               $value   Submitted value.
 * @param WP_Customize_Setting $setting Setting instance being sanitized.
 * @return string
 */
function lunar_sanitize_font_choice( string $value, WP_Customize_Setting $setting ): string {
	$fonts = lunar_get_google_fonts();

	if ( isset( $fonts[ $value ] ) ) {
		return $value;
	}

	return $setting->default;
}

/**
 * Customizable homepage section text: default copy and Customizer label.
 * Unlike color/font tokens these have no CSS variable — they're read
 * directly by front-page.php via lunar_get_homepage_text().
 *
 * @return array<string, array{default: string, label: string}>
 */
function lunar_get_homepage_text_tokens(): array {
	return array(
		'games_label'    => array(
			'default' => __( 'Jelajahi Game', 'lunar' ),
			'label'   => __( 'Games Section — Eyebrow Label', 'lunar' ),
		),
		'games_title'    => array(
			'default' => __( 'Pilih judul game', 'lunar' ),
			'label'   => __( 'Games Section — Heading', 'lunar' ),
		),
		'articles_label' => array(
			'default' => __( 'Baru diperbarui', 'lunar' ),
			'label'   => __( 'Articles Section — Eyebrow Label', 'lunar' ),
		),
		'articles_title' => array(
			'default' => __( 'Artikel terbaru', 'lunar' ),
			'label'   => __( 'Articles Section — Heading', 'lunar' ),
		),
	);
}

/**
 * Returns a homepage text token's active value: the saved override, or its default.
 *
 * @param string $token_key Key from lunar_get_homepage_text_tokens(), e.g. 'games_title'.
 * @return string
 */
function lunar_get_homepage_text( string $token_key ): string {
	$tokens = lunar_get_homepage_text_tokens();

	if ( ! isset( $tokens[ $token_key ] ) ) {
		return '';
	}

	return get_theme_mod( "lunar_homepage_{$token_key}", $tokens[ $token_key ]['default'] );
}

/**
 * Registers the Lunar Style Settings panel (Colors and Typography
 * sections), plus a standalone Homepage Content section for section text.
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

	$wp_customize->add_section(
		'lunar_typography',
		array(
			'title' => __( 'Typography', 'lunar' ),
			'panel' => 'lunar_style_settings',
		)
	);

	$font_choices = array_combine( array_keys( lunar_get_google_fonts() ), array_keys( lunar_get_google_fonts() ) );

	foreach ( lunar_get_font_tokens() as $token_key => $token ) {
		$setting_id = "lunar_font_{$token_key}";

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $token['default'],
				'sanitize_callback' => 'lunar_sanitize_font_choice',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'section' => 'lunar_typography',
				'label'   => $token['label'],
				'type'    => 'select',
				'choices' => $font_choices,
			)
		);
	}

	$wp_customize->add_section(
		'lunar_homepage_content',
		array(
			'title'       => __( 'Homepage Content', 'lunar' ),
			'description' => __( 'Edit the section labels shown on the homepage.', 'lunar' ),
			'priority'    => 25,
		)
	);

	foreach ( lunar_get_homepage_text_tokens() as $token_key => $token ) {
		$setting_id = "lunar_homepage_{$token_key}";

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $token['default'],
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'section' => 'lunar_homepage_content',
				'label'   => $token['label'],
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'lunar_customize_register' );