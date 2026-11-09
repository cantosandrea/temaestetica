<?php
/**
 * Footer.
 *
 * @package GlowUp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div><!-- #primary -->

<footer class="site-footer">
	<div class="container footer-main">
		<div class="footer-brand">
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<span class="footer-logo__mark"><?php echo glowup_mark_svg( 46 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="footer-logo__text">glowup</span>
			</a>
			<p class="footer-brand__text"><?php esc_html_e( 'Estética integral en Buenos Aires. Uñas, cabello, facial y depilación con turnos que respetan tu tiempo.', 'temaestetica' ); ?></p>

			<?php $social = glowup_social_links(); ?>
			<?php if ( $social ) : ?>
				<ul class="social-list" aria-label="<?php esc_attr_e( 'Redes sociales', 'temaestetica' ); ?>">
					<?php foreach ( $social as $link ) : ?>
						<li>
							<a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo glowup_icon( $link['icon'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="screen-reader-text"><?php echo esc_html( $link['label'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="footer-col">
			<h2><?php esc_html_e( 'Visitanos', 'temaestetica' ); ?></h2>
			<address>
				<?php echo esc_html( glowup_contact( 'address' ) ); ?>
			</address>
			<p><?php echo esc_html( glowup_contact( 'hours' ) ); ?></p>
			<a href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php echo esc_html( glowup_contact( 'email' ) ); ?></a>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', glowup_contact( 'phone' ) ) ); ?>"><?php echo esc_html( glowup_contact( 'phone' ) ); ?></a>
		</div>

		<div class="footer-col">
			<h2><?php esc_html_e( 'Explorar', 'temaestetica' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</div>

		<div class="footer-col">
			<h2><?php esc_html_e( 'Ayuda', 'temaestetica' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</div>
	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> GlowUp. <?php esc_html_e( 'Todos los derechos reservados.', 'temaestetica' ); ?></p>
		<p><?php esc_html_e( 'Hecho con cuidado en Buenos Aires.', 'temaestetica' ); ?></p>
	</div>

	<div class="footer-wordmark" aria-hidden="true">glowup</div>
</footer>

<a class="mobile-booking-bar" href="<?php echo esc_url( glowup_booking_url() ); ?>">
	<span><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></span>
	<?php echo glowup_icon( 'arrow', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</a>

<?php wp_footer(); ?>
</body>
</html>
