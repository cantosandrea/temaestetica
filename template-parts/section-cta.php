<?php
/**
 * Final CTA.
 *
 * @package GlowUp
 */
?>
<section class="cta-section" id="reservar">
	<div class="cta-section__sun" aria-hidden="true">
		<?php echo glowup_mark_svg( 480 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="container cta-section__inner">
		<p class="kicker kicker--light"><?php esc_html_e( 'Tu próximo turno', 'temaestetica' ); ?></p>
		<h2><?php esc_html_e( 'Tu turno te está esperando.', 'temaestetica' ); ?></h2>
		<p><?php esc_html_e( 'Elegí tu servicio, reservá online y vení a disfrutar. Si tenés dudas, escribinos y te ayudamos a elegir.', 'temaestetica' ); ?></p>
		<div class="cta-section__actions">
			<a class="btn btn--butter btn--large" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></a>
			<a class="btn btn--outline-light btn--large" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></a>
		</div>
	</div>
</section>
