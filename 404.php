<?php
/**
 * 404 template.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main">
	<section class="error-404">
		<div class="container error-404__inner">
			<p class="kicker"><?php esc_html_e( 'Error 404', 'temaestetica' ); ?></p>
			<h1><?php esc_html_e( 'Esta página se fue a hacer otro tratamiento.', 'temaestetica' ); ?></h1>
			<p><?php esc_html_e( 'No encontramos lo que buscabas. Probá desde el inicio o escribinos.', 'temaestetica' ); ?></p>
			<div class="error-404__actions">
				<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'temaestetica' ); ?></a>
				<a class="btn btn--ghost" href="<?php echo esc_url( glowup_booking_url() ); ?>"><?php esc_html_e( 'Reservar turno', 'temaestetica' ); ?></a>
			</div>
			<?php get_search_form(); ?>
		</div>
	</section>
</main>
<?php
get_footer();
