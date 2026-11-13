<?php
/**
 * Public booking wizard helpers and account handlers.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the booking engine is available.
 *
 * @return bool
 */
function glowup_booking_is_available() {
	return class_exists( 'GlowUp\\Turnos\\Rest_API' );
}

/**
 * Whether a user can create bookings.
 *
 * @param int $user_id Optional user ID.
 * @return bool
 */
function glowup_booking_can_book( $user_id = 0 ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return false;
	}

	return user_can( $user_id, 'glowup_book_appointments' ) || user_can( $user_id, 'manage_options' );
}

/**
 * URL for a legal page by slug, with a safe fallback.
 *
 * @param string $slug Page slug.
 * @return string
 */
function glowup_booking_legal_url( $slug ) {
	$page = get_page_by_path( $slug );

	if ( $page ) {
		return get_permalink( $page );
	}

	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Print account notices after login or registration.
 */
function glowup_booking_auth_notice() {
	$welcome = isset( $_GET['glowup_welcome'] ) ? sanitize_key( wp_unslash( $_GET['glowup_welcome'] ) ) : '';

	if ( $welcome ) {
		$message = 'register' === $welcome
			? __( 'Tu cuenta está lista. Ya podés elegir tu servicio y reservar.', 'temaestetica' )
			: __( 'Ingresaste correctamente. Elegí tu servicio para empezar.', 'temaestetica' );

		printf(
			'<div class="booking-alert booking-alert--success" role="status">%s</div>',
			esc_html( $message )
		);

		return;
	}

	$type = '';

	if ( isset( $_GET['glowup_login'] ) && 'error' === sanitize_key( wp_unslash( $_GET['glowup_login'] ) ) ) {
		$type = 'login';
	}

	if ( isset( $_GET['glowup_register'] ) && 'error' === sanitize_key( wp_unslash( $_GET['glowup_register'] ) ) ) {
		$type = 'register';
	}

	if ( ! $type ) {
		return;
	}

	$code     = isset( $_GET['glowup_code'] ) ? sanitize_key( wp_unslash( $_GET['glowup_code'] ) ) : 'unknown';
	$messages = array(
		'invalid_nonce'        => __( 'La sesión del formulario venció. Probá de nuevo.', 'temaestetica' ),
		'empty_credentials'    => __( 'Completá tu email y tu contraseña.', 'temaestetica' ),
		'invalid_username'     => __( 'No encontramos una cuenta con ese usuario o email.', 'temaestetica' ),
		'invalid_email'        => __( 'Ingresá un email válido.', 'temaestetica' ),
		'incorrect_password'   => __( 'La contraseña no es correcta. Probá de nuevo.', 'temaestetica' ),
		'empty_username'       => __( 'Completá tu email o usuario.', 'temaestetica' ),
		'empty_password'       => __( 'Completá tu contraseña.', 'temaestetica' ),
		'authentication_failed'=> __( 'No pudimos iniciar sesión. Revisá tus datos.', 'temaestetica' ),
		'login_failed'         => __( 'No pudimos iniciar sesión. Probá de nuevo.', 'temaestetica' ),
		'email_exists'         => __( 'Ya existe una cuenta con ese email. Iniciá sesión.', 'temaestetica' ),
		'username_exists'      => __( 'Ese nombre de usuaria ya está en uso. Probá con otro email.', 'temaestetica' ),
		'missing_name'         => __( 'Contanos tu nombre para preparar la reserva.', 'temaestetica' ),
		'weak_password'        => __( 'La contraseña necesita al menos 8 caracteres.', 'temaestetica' ),
		'privacy_required'     => __( 'Tenés que aceptar la política de privacidad para crear tu cuenta.', 'temaestetica' ),
		'registration_disabled'=> __( 'La creación de cuentas no está disponible en este momento.', 'temaestetica' ),
		'registration_unavailable' => __( 'No pudimos crear la cuenta en este momento. Probá de nuevo.', 'temaestetica' ),
		'registration_failed'  => __( 'No pudimos crear la cuenta. Revisá los datos e intentá de nuevo.', 'temaestetica' ),
		'unknown'              => __( 'Algo no salió como esperábamos. Probá de nuevo.', 'temaestetica' ),
	);

	$message = isset( $messages[ $code ] ) ? $messages[ $code ] : $messages['unknown'];

	printf(
		'<div class="booking-alert booking-alert--error" role="alert">%s</div>',
		esc_html( $message )
	);
}

/**
 * Redirect back to the booking page with an error code.
 *
 * @param string $type Login or register.
 * @param string $code Error code.
 */
function glowup_booking_auth_error( $type, $code ) {
	$url = add_query_arg(
		array(
			'glowup_' . $type => 'error',
			'glowup_code'     => sanitize_key( $code ),
		),
		glowup_booking_url()
	);

	wp_safe_redirect( $url . '#cuenta' );
	exit;
}

/**
 * Redirect after a successful login or registration.
 *
 * @param string $welcome Welcome context.
 */
function glowup_booking_auth_success( $welcome ) {
	$url = add_query_arg( 'glowup_welcome', sanitize_key( $welcome ), glowup_booking_url() );

	wp_safe_redirect( $url . '#reservar' );
	exit;
}

/**
 * Handle the inline login form.
 */
function glowup_handle_booking_login() {
	if ( ! isset( $_POST['glowup_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['glowup_login_nonce'] ) ), 'glowup_login' ) ) {
		glowup_booking_auth_error( 'login', 'invalid_nonce' );
	}

	if ( is_user_logged_in() ) {
		glowup_booking_auth_success( 'login' );
	}

	$login    = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$password = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : '';
	$remember = ! empty( $_POST['rememberme'] );

	if ( '' === $login || '' === $password ) {
		glowup_booking_auth_error( 'login', 'empty_credentials' );
	}

	$user = wp_signon(
		array(
			'user_login'    => $login,
			'user_password' => $password,
			'remember'      => $remember,
		),
		is_ssl()
	);

	if ( is_wp_error( $user ) ) {
		$code    = $user->get_error_code();
		$allowed = array(
			'invalid_username',
			'invalid_email',
			'incorrect_password',
			'empty_username',
			'empty_password',
			'authentication_failed',
		);

		glowup_booking_auth_error( 'login', in_array( $code, $allowed, true ) ? $code : 'login_failed' );
	}

	wp_set_current_user( $user->ID );
	glowup_booking_auth_success( 'login' );
}
add_action( 'admin_post_nopriv_glowup_login', 'glowup_handle_booking_login' );
add_action( 'admin_post_glowup_login', 'glowup_handle_booking_login' );

/**
 * Handle the inline registration form.
 */
function glowup_handle_booking_register() {
	if ( ! isset( $_POST['glowup_register_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['glowup_register_nonce'] ) ), 'glowup_register' ) ) {
		glowup_booking_auth_error( 'register', 'invalid_nonce' );
	}

	if ( is_user_logged_in() ) {
		glowup_booking_auth_success( 'register' );
	}

	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password  = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '';
	$first     = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last      = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$privacy   = ! empty( $_POST['privacy'] );

	if ( ! is_email( $email ) ) {
		glowup_booking_auth_error( 'register', 'invalid_email' );
	}

	if ( strlen( $password ) < 8 ) {
		glowup_booking_auth_error( 'register', 'weak_password' );
	}

	if ( '' === $first ) {
		glowup_booking_auth_error( 'register', 'missing_name' );
	}

	if ( ! $privacy ) {
		glowup_booking_auth_error( 'register', 'privacy_required' );
	}

	if ( ! get_option( 'users_can_register' ) && ! get_option( 'woocommerce_enable_myaccount_registration' ) ) {
		glowup_booking_auth_error( 'register', 'registration_disabled' );
	}

	if ( ! function_exists( 'wc_create_new_customer' ) ) {
		glowup_booking_auth_error( 'register', 'registration_unavailable' );
	}

	$customer_id = wc_create_new_customer(
		$email,
		'',
		$password,
		array(
			'first_name' => $first,
			'last_name'  => $last,
			'source'     => 'glowup-booking',
		)
	);

	if ( is_wp_error( $customer_id ) ) {
		$map = array(
			'registration-error-email-exists'      => 'email_exists',
			'registration-error-invalid-email'     => 'invalid_email',
			'registration-error-username-exists'   => 'username_exists',
			'registration-error-invalid-username'  => 'username_exists',
			'registration-error-missing-password'  => 'weak_password',
		);

		$code = $map[ $customer_id->get_error_code() ] ?? 'registration_failed';

		glowup_booking_auth_error( 'register', $code );
	}

	update_user_meta( $customer_id, 'billing_first_name', $first );
	update_user_meta( $customer_id, 'billing_last_name', $last );
	update_user_meta( $customer_id, 'billing_email', $email );

	$user = new WP_User( $customer_id );

	if ( ! $user->has_cap( 'glowup_book_appointments' ) ) {
		$user->add_cap( 'glowup_book_appointments' );
	}

	wp_set_current_user( $customer_id );
	wp_set_auth_cookie( $customer_id, true, is_ssl() );
	do_action( 'wp_login', $user->user_login, $user );

	glowup_booking_auth_success( 'register' );
}
add_action( 'admin_post_nopriv_glowup_register', 'glowup_handle_booking_register' );
add_action( 'admin_post_glowup_register', 'glowup_handle_booking_register' );

/**
 * Get a booking order from the current order-pay request.
 *
 * @return \WC_Order|null
 */
function glowup_booking_order_from_pay_request() {
	if ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-pay' ) ) {
		return null;
	}

	if ( ! function_exists( 'wc_get_order' ) ) {
		return null;
	}

	$order_id = absint( get_query_var( 'order-pay' ) );

	if ( ! $order_id ) {
		return null;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return null;
	}

	return 'yes' === $order->get_meta( '_glowup_booking_order' ) ? $order : null;
}

/**
 * Whether a Mercado Pago gateway is configured and available.
 *
 * WooCommerce's available gateways already apply the gateway's own checks,
 * including the Mercado Pago credential check. The wizard only promises online
 * payment when that chain says the gateway can actually charge.
 *
 * @return bool
 */
function glowup_booking_payments_available() {
	static $available = null;

	if ( null !== $available ) {
		return $available;
	}

	$available = false;

	if ( function_exists( 'WC' ) && WC()->payment_gateways() ) {
		foreach ( array_keys( WC()->payment_gateways()->get_available_payment_gateways() ) as $gateway_id ) {
			if ( 0 === strpos( $gateway_id, 'woo-mercado-pago' ) ) {
				$available = true;
				break;
			}
		}
	}

	$available = (bool) apply_filters( 'glowup_booking_payments_available', $available );

	return $available;
}

/**
 * Get the appointment linked to a booking order.
 *
 * @param \WC_Order|mixed $order Order object.
 * @return array|null
 */
function glowup_booking_order_appointment( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return null;
	}

	if ( ! class_exists( 'GlowUp\\Turnos\\Booking_Orders' ) || ! method_exists( 'GlowUp\\Turnos\\Booking_Orders', 'get_appointment_for_order' ) ) {
		return null;
	}

	return GlowUp\Turnos\Booking_Orders::get_appointment_for_order( $order );
}

/**
 * Whether a booking order still has a payable hold.
 *
 * @param \WC_Order $order Order object.
 * @return bool
 */
function glowup_booking_order_hold_is_active( $order ) {
	$appointment = glowup_booking_order_appointment( $order );

	return $appointment
		&& 'pending' === $appointment['status']
		&& ! empty( $appointment['is_hold_active'] );
}
