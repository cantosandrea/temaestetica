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

	if ( is_page_template( 'page-turnos.php' ) ) {
		glowup_enqueue_booking_assets();
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Enqueue the booking wizard only on the Turnos page.
 */
function glowup_enqueue_booking_assets() {
	$booking_js = GLOWUP_DIR . '/assets/js/booking.js';

	wp_enqueue_script(
		'glowup-booking',
		GLOWUP_URI . '/assets/js/booking.js',
		array(),
		file_exists( $booking_js ) ? filemtime( $booking_js ) : GLOWUP_VERSION,
		true
	);

	$settings         = class_exists( 'GlowUp\\Turnos\\Settings' ) ? GlowUp\Turnos\Settings::get() : array();
	$hold_minutes     = isset( $settings['hold_minutes'] ) ? (int) $settings['hold_minutes'] : 15;
	$max_advance_days = isset( $settings['max_advance_days'] ) ? (int) $settings['max_advance_days'] : 60;
	$payment_mode     = isset( $settings['payment_mode'] ) ? $settings['payment_mode'] : 'full';
	$deposit_percent  = isset( $settings['deposit_percent'] ) ? (int) $settings['deposit_percent'] : 100;
	$timezone         = isset( $settings['timezone'] ) ? $settings['timezone'] : wp_timezone_string();

	if ( class_exists( 'GlowUp\\Turnos\\Time' ) ) {
		$booking_now      = GlowUp\Turnos\Time::now();
		$booking_today    = $booking_now->format( 'Y-m-d' );
		$booking_max_date = $booking_now->modify( '+' . $max_advance_days . ' days' )->format( 'Y-m-d' );
	} else {
		$booking_today    = wp_date( 'Y-m-d' );
		$booking_max_date = wp_date( 'Y-m-d', strtotime( '+' . $max_advance_days . ' days' ) );
	}

	$privacy_url = get_privacy_policy_url();

	if ( ! $privacy_url ) {
		$privacy_url = glowup_booking_legal_url( 'politica-de-privacidad' );
	}

	$terms_url = glowup_booking_legal_url( 'terminos-y-condiciones' );

	if ( ! empty( $settings['terms_page_id'] ) && get_post( (int) $settings['terms_page_id'] ) ) {
		$terms_url = get_permalink( (int) $settings['terms_page_id'] );
	}

	wp_localize_script(
		'glowup-booking',
		'glowupBooking',
		array(
			'restUrl'        => esc_url_raw( rest_url( 'glowup/v1/' ) ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'isLoggedIn'     => is_user_logged_in(),
			'canBook'        => glowup_booking_can_book(),
			'bookingUrl'     => glowup_booking_url(),
			'accountUrl'     => glowup_account_url(),
			'loginUrl'       => glowup_booking_url() . '#cuenta',
			'currency'       => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ) : '$',
			'holdMinutes'    => $hold_minutes,
			'maxAdvanceDays' => $max_advance_days,
			'paymentMode'    => $payment_mode,
			'depositPercent' => $deposit_percent,
			'timezone'       => $timezone,
			'today'          => $booking_today,
			'maxDate'        => $booking_max_date,
			'privacyUrl'     => esc_url_raw( $privacy_url ),
			'termsUrl'       => esc_url_raw( $terms_url ),
			'cancelUrl'      => esc_url_raw( glowup_booking_legal_url( 'politica-de-cancelacion' ) ),
			'i18n'           => array(
				'loadingServices'      => __( 'Cargando servicios…', 'temaestetica' ),
				'loadingProfessionals' => __( 'Buscando profesionales…', 'temaestetica' ),
				'loadingSlots'         => __( 'Buscando horarios disponibles…', 'temaestetica' ),
				'chooseDate'           => __( 'Elegí una fecha para ver los horarios disponibles.', 'temaestetica' ),
				'noSlots'              => __( 'No hay horarios disponibles para ese día. Probá con otra fecha.', 'temaestetica' ),
				'servicesError'        => __( 'No pudimos cargar los servicios. Recargá la página.', 'temaestetica' ),
				'professionalsError'   => __( 'No pudimos cargar las profesionales. Probá de nuevo.', 'temaestetica' ),
				'slotsError'           => __( 'No pudimos consultar la disponibilidad. Probá de nuevo.', 'temaestetica' ),
				'slotTaken'            => __( 'Ese horario acaba de ocuparse. Elegí otro.', 'temaestetica' ),
				'holdExpired'          => __( 'La retención del horario venció. Elegí otro horario.', 'temaestetica' ),
				'genericError'         => __( 'Algo no salió como esperábamos. Probá de nuevo.', 'temaestetica' ),
				'sessionExpired'       => __( 'Tu sesión venció. Volvé a iniciar sesión para continuar.', 'temaestetica' ),
				'consentRequired'      => __( 'Aceptá las condiciones y el consentimiento de datos para continuar.', 'temaestetica' ),
				'retry'                => __( 'Reintentar', 'temaestetica' ),
				'login'                => __( 'Iniciar sesión', 'temaestetica' ),
				'restart'              => __( 'Elegir otro horario', 'temaestetica' ),
				'orderCreated'         => __( 'Pedido creado', 'temaestetica' ),
				'holdLabel'            => __( 'Retención activa', 'temaestetica' ),
				'minutesShort'         => __( 'min', 'temaestetica' ),
				'holdExpires'          => __( 'El horario queda retenido hasta que termine el contador. Si no confirmás, se libera automáticamente.', 'temaestetica' ),
				'noCharge'             => __( 'No se realiza ningún cobro en este paso.', 'temaestetica' ),
				'paymentPending'       => __( 'El pago online se habilita en la próxima etapa. Tu pedido queda pendiente de pago.', 'temaestetica' ),
				'morning'              => __( 'Mañana', 'temaestetica' ),
				'afternoon'            => __( 'Tarde', 'temaestetica' ),
				'totalLabel'           => __( 'Total', 'temaestetica' ),
				'depositLabel'         => __( 'Seña', 'temaestetica' ),
			),
		)
	);
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
