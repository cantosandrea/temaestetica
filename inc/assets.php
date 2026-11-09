<?php
/**
 * Assets.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue front-end assets.
 */
function glowup_enqueue_assets() {
	$main_css = GLOWUP_DIR . '/assets/css/main.css';
	$main_js  = GLOWUP_DIR . '/assets/js/main.js';

	wp_enqueue_style(
		'glowup-fonts',
		'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,300..700&family=Instrument+Serif:ital@0;1&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'glowup-main',
		GLOWUP_URI . '/assets/css/main.css',
		array( 'glowup-fonts' ),
		file_exists( $main_css ) ? filemtime( $main_css ) : GLOWUP_VERSION
	);

	wp_enqueue_script(
		'glowup-main',
		GLOWUP_URI . '/assets/js/main.js',
		array(),
		file_exists( $main_js ) ? filemtime( $main_js ) : GLOWUP_VERSION,
		true
	);

	wp_localize_script(
		'glowup-main',
		'glowupData',
		array(
			'homeUrl'     => home_url( '/' ),
			'restUrl'     => esc_url_raw( rest_url( 'glowup/v1/' ) ),
			'storeApiUrl' => esc_url_raw( rest_url( 'wc/store/v1/cart' ) ),
			'nonce'       => wp_create_nonce( 'wp_rest' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          Resource URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function glowup_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'glowup_resource_hints', 10, 2 );

/**
 * Theme color.
 */
function glowup_theme_color_meta() {
	echo '<meta name="theme-color" content="#8E1B3A">' . "\n";
}

/**
 * Fallback favicon until a Site Icon is set in the Customizer.
 */
function glowup_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
		esc_url( GLOWUP_URI . '/assets/img/favicon.svg' )
	);
}
