<?php
/**
 * Template Name: Turnos
 *
 * @package GlowUp
 */

get_header();
$glowup_policies = glowup_get_booking_policies();
$glowup_steps    = glowup_get_process_steps();
?>
<main id="main" class="site-main">
	<section class="page-hero page-hero--booking">
		<div class="container page-hero__grid">
			<div class="page-hero__inner">
				<p class="kicker"><?php esc_html_e( 'Turnos online', 'temaestetica' ); ?></p>
				<h1 class="page-hero__title"><?php esc_html_e( 'Reservá tu turno.', 'temaestetica' ); ?></h1>
				<p class="page-hero__lead"><?php esc_html_e( 'Elegí tu servicio, la profesional, el día y el horario. La agenda online se está terminando de configurar; mientras tanto, escribinos y lo coordinamos.', 'temaestetica' ); ?></p>
				<div class="page-hero__actions">
					<a class="btn" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></a>
					<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/servicios/' ) ); ?>"><?php esc_html_e( 'Ver servicios', 'temaestetica' ); ?></a>
				</div>
			</div>
			<div class="page-hero__image">
				<?php glowup_image( 'booking', __( 'Turnos en GlowUp', 'temaestetica' ), 'page-hero__image-el', true ); ?>
			</div>
		</div>
	</section>

	<section class="section booking-status-section">
		<div class="container booking-status">
			<div class="booking-status__copy">
				<p class="kicker"><?php esc_html_e( 'Próximamente', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'La agenda online está en camino.', 'temaestetica' ); ?></h2>
				<p><?php esc_html_e( 'Estamos trabajando en el motor de reservas para que puedas elegir el horario disponible, pagar online y recibir tu confirmación al instante.', 'temaestetica' ); ?></p>
				<p><?php esc_html_e( 'Mientras tanto, escribinos a hola@glowup.com con el servicio que querés y te respondemos con los horarios disponibles.', 'temaestetica' ); ?></p>
				<a class="btn" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Pedir un turno', 'temaestetica' ); ?></a>
			</div>
			<div class="booking-status__list">
				<?php foreach ( $glowup_steps as $step ) : ?>
					<div class="booking-status__step">
						<span><?php echo esc_html( $step['number'] ); ?></span>
						<div>
							<strong><?php echo esc_html( $step['title'] ); ?></strong>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="section policies-section">
		<div class="container">
			<header class="section-head section-head--split">
				<div>
					<p class="kicker"><?php esc_html_e( 'Antes de reservar', 'temaestetica' ); ?></p>
					<h2><?php esc_html_e( 'Las reglas del cuidado.', 'temaestetica' ); ?></h2>
				</div>
				<div class="section-head__aside">
					<p><?php esc_html_e( 'Reservar un turno también es reservar el tiempo de otra persona. Estas son las condiciones para que todo funcione bien.', 'temaestetica' ); ?></p>
				</div>
			</header>

			<div class="policies-grid">
				<article class="policy-card">
					<h3><?php esc_html_e( 'Cancelación', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['cancel'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Reprogramación', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['reschedule'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Demoras', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['late'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Ausencias', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['noshow'] ); ?></p>
				</article>
				<article class="policy-card">
					<h3><?php esc_html_e( 'Reembolsos', 'temaestetica' ); ?></h3>
					<p><?php echo esc_html( $glowup_policies['refund'] ); ?></p>
				</article>
				<article class="policy-card policy-card--accent">
					<h3><?php esc_html_e( 'Pago', 'temaestetica' ); ?></h3>
					<p><?php esc_html_e( 'El pago se realiza online al reservar. Cuando esté activo, vas a poder pagar con Mercado Pago de forma segura.', 'temaestetica' ); ?></p>
				</article>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
