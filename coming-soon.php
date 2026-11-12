<?php
/**
 * Store coming soon screen.
 *
 * Shown on shop, product, cart and checkout pages while the public
 * storefront is paused. Shop managers keep seeing the real store.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main">
	<section class="page-hero page-hero--compact">
		<div class="container">
			<div class="page-hero__inner">
				<p class="kicker"><?php esc_html_e( 'Tienda GlowUp', 'temaestetica' ); ?></p>
				<h1 class="page-hero__title"><?php esc_html_e( 'Muy pronto.', 'temaestetica' ); ?></h1>
				<p class="page-hero__lead"><?php esc_html_e( 'Estamos preparando una selección de productos para que el cuidado siga en casa. La tienda online todavía no está disponible.', 'temaestetica' ); ?></p>
				<div class="page-hero__actions">
					<a class="btn" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar un turno', 'temaestetica' ); ?></a>
					<a class="btn btn--ghost" href="mailto:<?php echo esc_attr( glowup_contact( 'email' ) ); ?>"><?php esc_html_e( 'Escribinos', 'temaestetica' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<section class="section booking-status-section">
		<div class="container booking-status">
			<div class="booking-status__copy">
				<p class="kicker"><?php esc_html_e( 'Próximamente', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'La tienda online está en camino.', 'temaestetica' ); ?></h2>
				<p><?php esc_html_e( 'La compra online queda para una próxima etapa. Estamos eligiendo productos, definiendo entregas y preparando todo para que la experiencia sea tan cuidada como la del salón.', 'temaestetica' ); ?></p>
				<p><?php esc_html_e( 'Mientras tanto, podés reservar tu turno online o escribirnos si necesitás un producto en particular.', 'temaestetica' ); ?></p>
			</div>
			<div class="booking-status__list">
				<div class="booking-status__step">
					<span>01</span>
					<div>
						<strong><?php esc_html_e( 'Productos elegidos', 'temaestetica' ); ?></strong>
						<p><?php esc_html_e( 'Una selección chica, probada y recomendada por el equipo.', 'temaestetica' ); ?></p>
					</div>
				</div>
				<div class="booking-status__step">
					<span>02</span>
					<div>
						<strong><?php esc_html_e( 'Retiro y envíos', 'temaestetica' ); ?></strong>
						<p><?php esc_html_e( 'Estamos definiendo las opciones de entrega para que lleguen sin vueltas.', 'temaestetica' ); ?></p>
					</div>
				</div>
				<div class="booking-status__step">
					<span>03</span>
					<div>
						<strong><?php esc_html_e( 'Compra simple', 'temaestetica' ); ?></strong>
						<p><?php esc_html_e( 'Cuando abramos, vas a poder comprar online de forma segura.', 'temaestetica' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
