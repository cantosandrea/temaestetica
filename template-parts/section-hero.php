<?php
/**
 * Hero section.
 *
 * @package GlowUp
 */
?>
<section class="hero">
	<div class="hero__aura" aria-hidden="true">
		<?php echo glowup_mark_svg( 620, 'hero__sun' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<div class="container hero__grid">
		<div class="hero__copy">
			<p class="kicker"><?php esc_html_e( 'Estética integral · La Rioja capital', 'temaestetica' ); ?></p>
			<h1 class="hero__title"><?php esc_html_e( 'Tu próximo glow', 'temaestetica' ); ?><br><?php esc_html_e( 'empieza acá.', 'temaestetica' ); ?></h1>
			<p class="hero__lead"><?php esc_html_e( 'Uñas, cabello, facial y depilación definitiva en un mismo lugar. Una pausa para vos, con turnos que respetan tu tiempo.', 'temaestetica' ); ?></p>

			<div class="hero__actions">
				<a class="btn btn--large" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></a>
				<a class="btn btn--ghost btn--large" href="<?php echo esc_url( home_url( '/servicios/' ) ); ?>"><?php esc_html_e( 'Ver servicios', 'temaestetica' ); ?></a>
			</div>

			<ul class="hero__facts">
				<li><?php echo glowup_icon( 'clock', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Turnos online', 'temaestetica' ); ?></span></li>
				<li><?php echo glowup_icon( 'check', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Pago con Mercado Pago', 'temaestetica' ); ?></span></li>
				<li><?php echo glowup_icon( 'pin', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Retiro y envíos', 'temaestetica' ); ?></span></li>
			</ul>
		</div>

		<div class="hero__visual">
			<div class="hero__image-wrap">
				<?php glowup_image( 'hero', __( 'Interior del estudio GlowUp', 'temaestetica' ), 'hero__image', true ); ?>
			</div>
			<div class="hero__sticker">
				<span><?php esc_html_e( 'Reservá online', 'temaestetica' ); ?></span>
				<strong><?php esc_html_e( '24/7', 'temaestetica' ); ?></strong>
			</div>
			<p class="hero__caption"><?php esc_html_e( 'Agustina & Estela · GlowUp', 'temaestetica' ); ?></p>
		</div>
	</div>
</section>
