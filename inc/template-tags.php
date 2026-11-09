<?php
/**
 * Template tags and helpers.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default image placeholders. Replace these URLs from the Customizer
 * or directly in this array if you prefer working in code.
 *
 * @return array
 */
function glowup_image_defaults() {
	return array(
		'hero'     => GLOWUP_URI . '/assets/img/placeholder-hero.svg',
		'about'    => GLOWUP_URI . '/assets/img/placeholder-portrait.svg',
		'agustina' => GLOWUP_URI . '/assets/img/placeholder-portrait.svg',
		'estela'   => GLOWUP_URI . '/assets/img/placeholder-portrait.svg',
		'shop'     => GLOWUP_URI . '/assets/img/placeholder-service.svg',
		'booking'  => GLOWUP_URI . '/assets/img/placeholder-wide.svg',
		'cta'      => GLOWUP_URI . '/assets/img/placeholder-wide.svg',
	);
}

/**
 * Get an image URL by key.
 *
 * @param string $key Image key.
 * @return string
 */
function glowup_get_image_url( $key ) {
	if ( $key && preg_match( '#^https?://#i', $key ) ) {
		return $key;
	}

	$defaults = glowup_image_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : $defaults['hero'];
	$url      = get_theme_mod( 'glowup_image_' . $key, $default );

	return $url ? $url : $default;
}

/**
 * Output an image by key.
 *
 * @param string $key   Image key.
 * @param string $alt   Alt text.
 * @param string $class CSS class.
 * @param bool   $eager Whether to load eagerly.
 */
function glowup_image( $key, $alt = '', $class = '', $eager = false ) {
	$url = glowup_get_image_url( $key );

	printf(
		'<img src="%1$s" alt="%2$s" class="%3$s" %4$s decoding="async">',
		esc_url( $url ),
		esc_attr( $alt ),
		esc_attr( $class ),
		$eager ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'
	);
}

/**
 * Sun mark.
 *
 * @param int    $size  Size in pixels.
 * @param string $class CSS class.
 * @return string
 */
function glowup_mark_svg( $size = 44, $class = '' ) {
	return sprintf(
		'<svg class="glow-mark %1$s" width="%2$d" height="%2$d" viewBox="0 0 64 64" fill="none" aria-hidden="true" focusable="false"><g stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="32" cy="32" r="21.5" stroke-dasharray="2 6"/><circle cx="32" cy="32" r="12.5" fill="currentColor" stroke="none"/><path d="M32 4v7M32 53v7M4 32h7M53 32h7M12.2 12.2l5 5M46.8 46.8l5 5M51.8 12.2l-5 5M17.2 46.8l-5 5"/></g></svg>',
		esc_attr( $class ),
		(int) $size
	);
}

/**
 * Inline line icons.
 *
 * @param string $name Icon name.
 * @param int    $size Icon size.
 * @return string
 */
function glowup_icon( $name, $size = 22 ) {
	$paths = array(
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
		'bag'       => '<path d="M6 8h12l1 12H5L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'arrow'     => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
		'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'     => '<path d="m6 6 12 12M18 6 6 18"/>',
		'pin'       => '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'     => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.5 2"/>',
		'mail'      => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'phone'     => '<path d="M8.5 4.5 6 6.5c-.7.6-.8 1.6-.4 2.4 2.1 4.2 4.9 7 9.1 9.1.8.4 1.8.3 2.4-.4l2-2.5c.5-.6.4-1.5-.2-2l-3-2c-.5-.4-1.3-.3-1.8.2l-1 1c-1.7-1-3.1-2.4-4.1-4.1l1-1c.5-.5.6-1.3.2-1.8l-2-3c-.5-.6-1.4-.7-2-.2Z"/>',
		'check'     => '<path d="m5 12 4.5 4.5L19 7"/>',
		'spark'     => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"/><circle cx="12" cy="12" r="2.5" fill="currentColor" stroke="none"/>',
		'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.5"/><circle cx="17" cy="7" r="1" fill="currentColor" stroke="none"/>',
		'tiktok'    => '<path d="M14 4v9.2a4.3 4.3 0 1 1-3.6-4.2"/><path d="M14 4c.6 2.5 2.1 4 4.6 4.4"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="icon icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Site logo: custom logo or the GlowUp wordmark.
 */
function glowup_site_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span class="site-logo__mark"><?php echo glowup_mark_svg( 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="site-logo__text">glowup</span>
	</a>
	<?php
}

/**
 * Price format.
 *
 * @param float|int $amount Amount.
 * @return string
 */
function glowup_price( $amount ) {
	return '$' . number_format( (float) $amount, 0, ',', '.' );
}

/**
 * Duration format.
 *
 * @param int $minutes Minutes.
 * @return string
 */
function glowup_duration( $minutes ) {
	$minutes = (int) $minutes;
	$hours   = intdiv( $minutes, 60 );
	$rest    = $minutes % 60;

	if ( $hours && $rest ) {
		return sprintf( '%d h %02d min', $hours, $rest );
	}
	if ( $hours ) {
		return sprintf( '%d h', $hours );
	}

	return sprintf( '%d min', $rest );
}

/**
 * Contact setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function glowup_contact( $key ) {
	$defaults = array(
		'email'     => 'hola@glowup.com',
		'phone'     => '+54 9 11 0000-0000',
		'address'   => 'Dirección a confirmar, La Rioja capital',
		'hours'     => 'Lunes a viernes de 9 a 20 · Sábados de 9 a 14',
		'instagram' => 'https://instagram.com/',
		'tiktok'    => 'https://tiktok.com/',
	);

	return get_theme_mod( 'glowup_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Booking URL.
 *
 * @return string
 */
function glowup_booking_url() {
	return home_url( '/turnos/' );
}

/**
 * Shop URL.
 *
 * @return string
 */
function glowup_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}

	return home_url( '/tienda/' );
}

/**
 * Account URL.
 *
 * @return string
 */
function glowup_account_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'myaccount' );
	}

	return wp_login_url();
}

/**
 * Cart URL.
 *
 * @return string
 */
function glowup_cart_url() {
	if ( function_exists( 'wc_get_cart_url' ) ) {
		return wc_get_cart_url();
	}

	return home_url( '/carrito/' );
}

/**
 * Cart count.
 *
 * @return int
 */
function glowup_cart_count() {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		return (int) WC()->cart->get_cart_contents_count();
	}

	return 0;
}

/**
 * Social links.
 *
 * @return array
 */
function glowup_social_links() {
	$links = array();

	if ( glowup_contact( 'instagram' ) ) {
		$links[] = array(
			'label' => 'Instagram',
			'url'   => glowup_contact( 'instagram' ),
			'icon'  => 'instagram',
		);
	}

	if ( glowup_contact( 'tiktok' ) ) {
		$links[] = array(
			'label' => 'TikTok',
			'url'   => glowup_contact( 'tiktok' ),
			'icon'  => 'tiktok',
		);
	}

	return $links;
}

/**
 * Fallback menu when no menu is assigned.
 *
 * @param array $args Menu args.
 */
function glowup_menu_fallback( $args = array() ) {
	$items = array(
		array( 'Inicio', home_url( '/' ) ),
		array( 'Servicios', home_url( '/servicios/' ) ),
		array( 'Tienda', glowup_shop_url() ),
		array( 'Nosotras', home_url( '/nosotras/' ) ),
		array( 'Contacto', home_url( '/contacto/' ) ),
	);

	echo '<ul class="menu">';
	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $item[1] ),
			esc_html( $item[0] )
		);
	}
	echo '</ul>';
}

/**
 * Return the page title for hero sections.
 *
 * @param string $fallback Fallback title.
 * @return string
 */
function glowup_page_title( $fallback = '' ) {
	if ( is_singular() ) {
		return get_the_title();
	}
	if ( is_archive() ) {
		return get_the_archive_title();
	}
	if ( is_search() ) {
		return sprintf( 'Resultados para “%s”', get_search_query() );
	}

	return $fallback;
}
