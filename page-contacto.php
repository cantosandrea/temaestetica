<?php
/**
 * Template Name: Contacto
 *
 * @package GlowUp
 */

get_header();
$glowup_faq = glowup_get_faq_items();
?>
<main id="main" class="site-main">
	<section class="page-hero">
		<div class="container page-hero__inner">
			<p class="kicker"><?php esc_html_e( 'Contacto', 'temaestetica' ); ?></p>
			<h1 class="page-hero__title"><?php esc_html_e( 'Hablemos.', 'temaestetica' ); ?></h1>
			<p class="page-hero__lead"><?php esc_html_e( '¿Tenés una duda, querés regalar una gift card o no sabés qué servicio elegir? Escribinos y te ayudamos.', 'temaestetica' ); ?></p>
		</div>
	</section>

	<section class="section contact-section">
		<div class="container contact-grid">
			<div class="contact-card contact-card--cherry">
				<p class="kicker kicker--light"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'La forma más rápida.', 'temaestetica' ); ?></h2>
				<p><?php esc_html_e( 'Respondemos dentro del horario del salón. Si es urgente, llamanos.', 'temaestetica' ); ?></p>
				<a class="contact-link" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>">
					<?php echo glowup_icon( 'mail', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( glowup_contact( 'email' ) ); ?></span>
				</a>
				<a class="contact-link" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', glowup_contact( 'phone' ) ) ); ?>">
					<?php echo glowup_icon( 'phone', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( glowup_contact( 'phone' ) ); ?></span>
				</a>
			</div>

			<div class="contact-card">
				<p class="kicker"><?php esc_html_e( 'Visitanos', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'El estudio.', 'temaestetica' ); ?></h2>
				<address class="contact-address">
					<?php echo glowup_icon( 'pin', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( glowup_contact( 'address' ) ); ?></span>
				</address>
				<p class="contact-hours">
					<?php echo glowup_icon( 'clock', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( glowup_contact( 'hours' ) ); ?></span>
				</p>
				<div class="map-placeholder">
					<span><?php esc_html_e( 'Mapa del estudio', 'temaestetica' ); ?></span>
					<small><?php esc_html_e( 'Reemplazar por el mapa embebido cuando esté la dirección final.', 'temaestetica' ); ?></small>
				</div>
			</div>
		</div>
	</section>

	<section class="section faq-section">
		<div class="container faq-grid">
			<div class="faq-intro">
				<p class="kicker"><?php esc_html_e( 'Preguntas frecuentes', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Antes de reservar.', 'temaestetica' ); ?></h2>
				<p><?php esc_html_e( 'Si tu pregunta no está acá, escribinos. Preferimos responder antes de que llegues al turno.', 'temaestetica' ); ?></p>
				<a class="btn btn--ghost" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></a>
			</div>

			<div class="faq-list">
				<?php foreach ( $glowup_faq as $item ) : ?>
					<details class="faq-item">
						<summary><?php echo esc_html( $item['q'] ); ?></summary>
						<p><?php echo esc_html( $item['a'] ); ?></p>
					</details>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
