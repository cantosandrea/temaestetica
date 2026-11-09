<?php
/**
 * WooCommerce integration.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Products per page.
 *
 * @return int
 */
function glowup_products_per_page() {
	return 9;
}
add_filter( 'loop_shop_per_page', 'glowup_products_per_page', 20 );

/**
 * Shop columns.
 *
 * @return int
 */
function glowup_loop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'glowup_loop_columns' );

/**
 * Related products.
 *
 * @param array $args Related product args.
 * @return array
 */
function glowup_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'glowup_related_products_args' );

/**
 * Breadcrumb defaults.
 *
 * @param array $defaults Defaults.
 * @return array
 */
function glowup_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter']   = ' <span aria-hidden="true">/</span> ';
	$defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb glow-breadcrumb" aria-label="' . esc_attr__( 'Migas de pan', 'temaestetica' ) . '">';
	$defaults['wrap_after']  = '</nav>';

	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'glowup_breadcrumb_defaults' );

/**
 * Brand the shop page title.
 *
 * @param string $title Page title.
 * @return string
 */
function glowup_shop_page_title( $title ) {
	if ( is_shop() ) {
		return __( 'Tienda GlowUp', 'temaestetica' );
	}

	return $title;
}
add_filter( 'woocommerce_page_title', 'glowup_shop_page_title' );

/**
 * Add the shop lead under WooCommerce's own archive header.
 */
function glowup_shop_archive_lead() {
	if ( ! is_shop() ) {
		return;
	}
	?>
	<p class="shop-header__lead"><?php esc_html_e( 'Productos elegidos para que el cuidado siga en casa. Retiro en el salón y envíos a todo el país.', 'temaestetica' ); ?></p>
	<?php
}
add_action( 'woocommerce_archive_description', 'glowup_shop_archive_lead', 20 );

/**
 * Breadcrumb on single products.
 */
function glowup_single_product_breadcrumb() {
	if ( function_exists( 'woocommerce_breadcrumb' ) ) {
		woocommerce_breadcrumb();
	}
}
add_action( 'woocommerce_before_single_product', 'glowup_single_product_breadcrumb', 5 );

/**
 * Placeholder image.
 *
 * @return string
 */
function glowup_woocommerce_placeholder( $src ) {
	return GLOWUP_URI . '/assets/img/placeholder-product.svg';
}
add_filter( 'woocommerce_placeholder_img_src', 'glowup_woocommerce_placeholder' );

/**
 * Keep the header cart badge in sync with WooCommerce AJAX fragments.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function glowup_cart_count_fragment( $fragments ) {
	$count = glowup_cart_count();

	$fragments['.cart-count'] = sprintf(
		'<span class="cart-count%1$s" data-cart-count>%2$d</span>',
		$count ? '' : ' is-empty',
		$count
	);

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'glowup_cart_count_fragment' );

/**
 * Add a "Volver a la tienda" link on single products.
 */
function glowup_after_single_product() {
	if ( ! is_product() ) {
		return;
	}
	echo '<p class="glow-back-link"><a class="text-link" href="' . esc_url( glowup_shop_url() ) . '">' . esc_html__( 'Volver a la tienda', 'temaestetica' ) . '</a></p>';
}
add_action( 'woocommerce_after_single_product_summary', 'glowup_after_single_product', 30 );
