<?php
/**
 * Process section.
 *
 * @package GlowUp
 */

$glowup_steps = glowup_get_process_steps();
?>
<section class="process-section">
	<div class="container">
		<header class="section-head section-head--light">
			<p class="kicker"><?php esc_html_e( 'Así funciona', 'temaestetica' ); ?></p>
			<h2><?php esc_html_e( 'Reservar tiene que ser fácil.', 'temaestetica' ); ?></h2>
		</header>

		<div class="process-grid">
			<?php foreach ( $glowup_steps as $step ) : ?>
				<article class="process-step">
					<span class="process-step__number"><?php echo esc_html( $step['number'] ); ?></span>
					<h3><?php echo esc_html( $step['title'] ); ?></h3>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="process-cta">
			<a class="btn btn--butter" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Empezar mi reserva', 'temaestetica' ); ?></a>
			<span><?php esc_html_e( 'Reservá online las 24 horas.', 'temaestetica' ); ?></span>
		</div>
	</div>
</section>
