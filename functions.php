<?php
/**
 * GlowUp theme bootstrap.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLOWUP_VERSION', '0.1.0' );
define( 'GLOWUP_DIR', get_template_directory() );
define( 'GLOWUP_URI', get_template_directory_uri() );

require_once GLOWUP_DIR . '/inc/setup.php';
require_once GLOWUP_DIR . '/inc/assets.php';
require_once GLOWUP_DIR . '/inc/template-tags.php';
require_once GLOWUP_DIR . '/inc/content.php';
require_once GLOWUP_DIR . '/inc/customizer.php';
require_once GLOWUP_DIR . '/inc/woocommerce.php';

add_action( 'after_setup_theme', 'glowup_setup' );
add_action( 'widgets_init', 'glowup_widgets_init' );
add_action( 'wp_enqueue_scripts', 'glowup_enqueue_assets' );
add_action( 'wp_head', 'glowup_theme_color_meta', 5 );
add_action( 'wp_head', 'glowup_favicon', 6 );
add_filter( 'body_class', 'glowup_body_classes' );
add_filter( 'excerpt_length', 'glowup_excerpt_length' );
add_filter( 'excerpt_more', 'glowup_excerpt_more' );
add_filter( 'wp_lazy_loading_enabled', 'glowup_lazy_loading', 10, 3 );
