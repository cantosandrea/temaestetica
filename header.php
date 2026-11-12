<?php
/**
 * Header.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Saltar al contenido', 'temaestetica' ); ?></a>

<header class="site-header" data-site-header>
	<div class="announcement">
		<div class="container announcement__inner">
			<?php if ( glowup_store_is_coming_soon() ) : ?>
				<span><?php esc_html_e( 'Reservá tu turno online', 'temaestetica' ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php esc_html_e( 'La Rioja capital', 'temaestetica' ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php esc_html_e( 'Depilación definitiva', 'temaestetica' ); ?></span>
			<?php else : ?>
				<span><?php esc_html_e( 'Envíos a todo el país', 'temaestetica' ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php esc_html_e( 'Retiro en el salón', 'temaestetica' ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php esc_html_e( 'Reservá tu turno online', 'temaestetica' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="container site-header__inner">
		<?php glowup_site_logo(); ?>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'temaestetica' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => 'glowup_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<a class="header-icon" href="<?php echo esc_url( glowup_account_url() ); ?>" aria-label="<?php esc_attr_e( 'Mi cuenta', 'temaestetica' ); ?>">
				<?php echo glowup_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
			<?php if ( ! glowup_store_is_coming_soon() || current_user_can( 'manage_woocommerce' ) ) : ?>
				<a class="header-icon header-icon--cart" href="<?php echo esc_url( glowup_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'Carrito', 'temaestetica' ); ?>">
					<?php echo glowup_icon( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="cart-count<?php echo glowup_cart_count() ? '' : ' is-empty'; ?>" data-cart-count><?php echo esc_html( glowup_cart_count() ); ?></span>
				</a>
			<?php endif; ?>
			<a class="btn btn--small header-cta" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar', 'temaestetica' ); ?></a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" data-nav-toggle>
				<span class="nav-toggle__open"><?php echo glowup_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="nav-toggle__close"><?php echo glowup_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Abrir menú', 'temaestetica' ); ?></span>
			</button>
		</div>
	</div>

	<div id="mobile-menu" class="mobile-menu" hidden>
		<div class="mobile-menu__top container">
			<span class="mobile-menu__label"><?php esc_html_e( 'Menú', 'temaestetica' ); ?></span>
			<button class="mobile-menu__close" type="button" data-nav-close>
				<?php echo glowup_icon( 'close', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Cerrar menú', 'temaestetica' ); ?></span>
			</button>
		</div>
		<nav class="mobile-menu__nav container" aria-label="<?php esc_attr_e( 'Menú móvil', 'temaestetica' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-menu__list',
					'fallback_cb'    => 'glowup_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<div class="mobile-menu__foot container">
			<a class="btn" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></a>
			<div class="mobile-menu__contact">
				<a href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php echo esc_html( glowup_contact( 'email' ) ); ?></a>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', glowup_contact( 'phone' ) ) ); ?>"><?php echo esc_html( glowup_contact( 'phone' ) ); ?></a>
			</div>
		</div>
	</div>
</header>

<div id="primary" class="site-content">
