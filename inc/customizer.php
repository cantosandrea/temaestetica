<?php
/**
 * Customizer settings for brand images and contact details.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function glowup_customize_register( $wp_customize ) {
	$defaults = glowup_image_defaults();

	$wp_customize->add_section(
		'glowup_brand',
		array(
			'title'       => __( 'GlowUp · Marca y contacto', 'temaestetica' ),
			'description' => __( 'Subí las fotos desde Medios y asignalas acá. También podés reemplazar las URLs por defecto en inc/customizer.php.', 'temaestetica' ),
			'priority'    => 30,
		)
	);

	$images = array(
		'hero'     => __( 'Imagen principal (hero)', 'temaestetica' ),
		'about'    => __( 'Imagen del estudio', 'temaestetica' ),
		'agustina' => __( 'Retrato de Agustina', 'temaestetica' ),
		'estela'   => __( 'Retrato de Estela', 'temaestetica' ),
		'shop'     => __( 'Imagen para la tienda', 'temaestetica' ),
		'booking'  => __( 'Imagen para la página de turnos', 'temaestetica' ),
	);

	foreach ( $images as $key => $label ) {
		$wp_customize->add_setting(
			'glowup_image_' . $key,
			array(
				'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'glowup_image_' . $key,
				array(
					'label'   => $label,
					'section' => 'glowup_brand',
				)
			)
		);
	}

	$text_settings = array(
		'glowup_email'     => array( __( 'Email de contacto', 'temaestetica' ), 'hola@glowup.com', 'sanitize_email' ),
		'glowup_phone'     => array( __( 'Teléfono / WhatsApp', 'temaestetica' ), '+54 9 11 0000-0000', 'sanitize_text_field' ),
		'glowup_address'   => array( __( 'Dirección', 'temaestetica' ), 'Dirección a confirmar, La Rioja capital', 'sanitize_text_field' ),
		'glowup_hours'     => array( __( 'Horarios', 'temaestetica' ), 'Lunes a viernes de 9 a 20 · Sábados de 9 a 14', 'sanitize_text_field' ),
		'glowup_instagram' => array( __( 'URL de Instagram', 'temaestetica' ), 'https://instagram.com/', 'esc_url_raw' ),
		'glowup_tiktok'    => array( __( 'URL de TikTok', 'temaestetica' ), 'https://tiktok.com/', 'esc_url_raw' ),
	);

	foreach ( $text_settings as $setting => $config ) {
		$wp_customize->add_setting(
			$setting,
			array(
				'default'           => $config[1],
				'sanitize_callback' => $config[2],
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting,
			array(
				'label'   => $config[0],
				'section' => 'glowup_brand',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'glowup_customize_register' );
