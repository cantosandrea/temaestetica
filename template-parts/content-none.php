<?php
/**
 * Empty state.
 *
 * @package GlowUp
 */
?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'No encontramos resultados', 'temaestetica' ); ?></h1>
	</header>

	<div class="entry-content">
		<p><?php esc_html_e( 'Probá con otra búsqueda o volvé al inicio.', 'temaestetica' ); ?></p>
		<?php get_search_form(); ?>
		<p><a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'temaestetica' ); ?></a></p>
	</div>
</section>
