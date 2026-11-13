<?php
/**
 * WooCommerce integration.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the public storefront is paused behind a "coming soon" screen.
 *
 * WooCommerce stays active for accounts, orders and booking payments. Only
 * the public product catalog, cart and store checkout are paused.
 *
 * @return bool
 */
function glowup_store_is_coming_soon() {
	return (bool) apply_filters( 'glowup_store_coming_soon', true );
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Whether the current request belongs to the public storefront.
 *
 * @return bool
 */
function glowup_store_is_public_page() {
	// A booking order-pay screen is part of the booking flow, not the
	// product storefront. Keep it reachable while the catalog stays paused.
	if ( function_exists( 'glowup_booking_order_from_pay_request' ) && glowup_booking_order_from_pay_request() ) {
		return false;
	}

	return is_shop()
		|| is_product()
		|| is_product_taxonomy()
		|| is_cart()
		|| is_checkout();
}

/**
 * Replace public store templates with the GlowUp "coming soon" screen.
 *
 * Shop managers and administrators keep seeing the real store so they can
 * prepare products and test checkout.
 *
 * @param string $template Template path.
 * @return string
 */
function glowup_store_coming_soon_template( $template ) {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $template;
	}

	if ( ! glowup_store_is_coming_soon() || ! glowup_store_is_public_page() ) {
		return $template;
	}

	if ( current_user_can( 'manage_woocommerce' ) ) {
		return $template;
	}

	$coming_soon = locate_template( 'coming-soon.php' );

	return $coming_soon ? $coming_soon : $template;
}
add_filter( 'template_include', 'glowup_store_coming_soon_template', 99 );

/**
 * Explain the payment state on a booking order-pay screen.
 */
function glowup_booking_pay_order_notice() {
	$order = glowup_booking_order_from_pay_request();

	if ( ! $order ) {
		return;
	}

	if ( ! glowup_booking_payments_available() ) {
		echo '<div class="woocommerce-info glow-booking-pay-notice">' . esc_html__( 'Este pedido corresponde a un turno. El pago online se habilita en la próxima etapa; no se realizó ningún cobro.', 'temaestetica' ) . '</div>';
		return;
	}

	if ( ! glowup_booking_order_hold_is_active( $order ) ) {
		echo '<div class="woocommerce-error glow-booking-pay-notice">';
		echo esc_html__( 'La retención de este horario ya venció y el pedido no se puede pagar.', 'temaestetica' );
		echo ' <a href="' . esc_url( glowup_booking_url() ) . '">' . esc_html__( 'Elegir otro horario', 'temaestetica' ) . '</a>';
		echo '</div>';
		return;
	}

	$appointment = glowup_booking_order_appointment( $order );
	$seconds     = class_exists( 'GlowUp\\Turnos\\Time' ) && ! empty( $appointment['hold_expires_at'] )
		? GlowUp\Turnos\Time::seconds_until( $appointment['hold_expires_at'] )
		: 0;
	$minutes     = max( 1, (int) ceil( $seconds / 60 ) );

	echo '<div class="woocommerce-info glow-booking-pay-notice">' . esc_html(
		sprintf(
			/* translators: %d: minutes remaining in the hold. */
			_n(
				'Tenés %d minuto para completar el pago. Si la retención vence, el horario se libera automáticamente.',
				'Tenés %d minutos para completar el pago. Si la retención vence, el horario se libera automáticamente.',
				$minutes,
				'temaestetica'
			),
			$minutes
		)
	) . '</div>';
}
add_action( 'woocommerce_pay_order_before_payment', 'glowup_booking_pay_order_notice' );

/**
 * Keep an expired booking from reaching WooCommerce's payment form.
 *
 * The plugin also blocks payment server-side; this redirect exists so the
 * customer sees a clear message instead of WooCommerce's generic
 * "cannot be paid" error.
 */
function glowup_booking_guard_pay_request() {
	$order = glowup_booking_order_from_pay_request();

	if ( ! $order ) {
		return;
	}

	if ( ! isset( $_GET['key'] ) || ! in_array( $order->get_status(), array( 'pending', 'failed' ), true ) ) {
		return;
	}

	if ( glowup_booking_order_hold_is_active( $order ) ) {
		return;
	}

	wp_safe_redirect( add_query_arg( 'glowup_pay', 'expired', glowup_booking_url() ) );
	exit;
}
add_action( 'before_woocommerce_pay', 'glowup_booking_guard_pay_request' );

/**
 * Replace WooCommerce's generic "no payment methods" message for bookings.
 *
 * @param string $message Original message.
 * @return string
 */
function glowup_booking_no_payment_methods_message( $message ) {
	$order = glowup_booking_order_from_pay_request();

	if ( ! $order ) {
		return $message;
	}

	if ( ! glowup_booking_payments_available() ) {
		return __( 'El pago online estará disponible próximamente. Tu pedido queda pendiente y el horario se libera cuando vence la retención.', 'temaestetica' );
	}

	return __( 'No hay medios de pago disponibles para este pedido. Volvé a intentarlo en unos minutos o escribinos.', 'temaestetica' );
}
add_filter( 'woocommerce_no_available_payment_methods_message', 'glowup_booking_no_payment_methods_message' );

/**
 * Replace the pay button only when the booking cannot be paid online.
 *
 * @param string $html Button HTML.
 * @return string
 */
function glowup_booking_pay_order_button_html( $html ) {
	$order = glowup_booking_order_from_pay_request();

	if ( ! $order ) {
		return $html;
	}

	if ( glowup_booking_payments_available() ) {
		if ( glowup_booking_order_hold_is_active( $order ) ) {
			return $html;
		}

		return '<a class="button alt" href="' . esc_url( glowup_booking_url() ) . '">' . esc_html__( 'Elegir otro horario', 'temaestetica' ) . '</a>';
	}

	return '<a class="button alt" href="' . esc_url( glowup_account_url() ) . '">' . esc_html__( 'Volver a mi cuenta', 'temaestetica' ) . '</a>';
}
add_filter( 'woocommerce_pay_order_button_html', 'glowup_booking_pay_order_button_html' );

/**
 * Hide the checkout terms checkbox on booking order-pay screens.
 *
 * The booking wizard already collects its own consents.
 *
 * @param string $text Checkbox text.
 * @return string
 */
function glowup_booking_pay_order_terms_text( $text ) {
	if ( glowup_booking_order_from_pay_request() ) {
		return '';
	}

	return $text;
}
add_filter( 'woocommerce_get_terms_and_conditions_checkbox_text', 'glowup_booking_pay_order_terms_text' );

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
