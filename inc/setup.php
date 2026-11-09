<?php
/**
 * Theme setup.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports, menus and image sizes.
 */
function glowup_setup() {
	load_theme_textdomain( 'temaestetica', GLOWUP_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 420,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_columns' => 3,
				'default_rows'    => 3,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'glowup-portrait', 900, 1125, true );
	add_image_size( 'glowup-square', 900, 900, true );
	add_image_size( 'glowup-wide', 1600, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Menú principal', 'temaestetica' ),
			'footer'  => __( 'Menú del pie', 'temaestetica' ),
			'legal'   => __( 'Menú legal', 'temaestetica' ),
		)
	);

	$GLOBALS['content_width'] = 760;
}
 
/**
 * Register widget area.
 */
function glowup_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Barra lateral', 'temaestetica' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Widgets para el blog y las páginas con barra lateral.', 'temaestetica' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

/**
 * Add helpful body classes.
 *
 * @param array $classes Body classes.
 * @return array
 */
function glowup_body_classes( $classes ) {
	$classes[] = 'glowup';

	if ( is_front_page() ) {
		$classes[] = 'glowup-front';
	}

	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$classes[] = 'glowup-woo';
	}

	return $classes;
}

/**
 * Excerpt length.
 *
 * @return int
 */
function glowup_excerpt_length() {
	return 24;
}

/**
 * Excerpt more.
 *
 * @return string
 */
function glowup_excerpt_more() {
	return '…';
}

/**
 * Keep lazy loading defaults; placeholder for future tuning.
 *
 * @param bool   $default Whether lazy loading is enabled.
 * @param string $tag_name Tag name.
 * @param string $context Context.
 * @return bool
 */
function glowup_lazy_loading( $default, $tag_name, $context ) {
	return $default;
}
