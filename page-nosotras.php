<?php
/**
 * Template Name: Nosotras
 *
 * @package GlowUp
 */

get_header();
$glowup_values = glowup_get_values();
?>
<main id="main" class="site-main">
	<section class="page-hero page-hero--split">
		<div class="container page-hero__grid">
			<div class="page-hero__inner">
				<p class="kicker"><?php esc_html_e( 'El estudio', 'temaestetica' ); ?></p>
				<h1 class="page-hero__title"><?php esc_html_e( 'Nosotras', 'temaestetica' ); ?></h1>
				<p class="page-hero__lead"><?php esc_html_e( 'Un estudio de estética integral donde el cuidado se piensa con tiempo, criterio y una buena dosis de obsesión por los detalles.', 'temaestetica' ); ?></p>
			</div>
			<div class="page-hero__image">
				<?php glowup_image( 'about', __( 'Interior del estudio GlowUp', 'temaestetica' ), 'page-hero__image-el', true ); ?>
			</div>
		</div>
	</section>

	<section class="section about-section about-section--page">
		<div class="container about-grid">
			<div class="about-copy">
				<p class="kicker"><?php esc_html_e( 'Cómo trabajamos', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'La belleza no debería apurarse.', 'temaestetica' ); ?></h2>
				<p class="about-lead"><?php esc_html_e( 'Cada turno tiene el espacio que necesita. No trabajamos con agendas imposibles ni con recetas iguales para todas.', 'temaestetica' ); ?></p>
				<p><?php esc_html_e( 'Escuchamos qué te pasa, miramos tu piel o tu cabello y elegimos el mejor camino juntas. Si algo no corresponde, te lo decimos. Si algo necesita una evaluación, te lo advertimos antes de reservar.', 'temaestetica' ); ?></p>
				<p><?php esc_html_e( 'Creemos en los resultados que se sostienen, en los productos que se eligen con criterio y en los espacios que te hacen bajar un cambio.', 'temaestetica' ); ?></p>
			</div>
			<div class="about-visual">
				<div class="about-image-wrap">
					<?php glowup_image( 'hero', __( 'Detalle del estudio GlowUp', 'temaestetica' ), 'about-image' ); ?>
				</div>
				<div class="about-stamp" aria-hidden="true">
					<?php echo glowup_mark_svg( 86 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
	</section>

	<section class="section values-section">
		<div class="container">
			<header class="section-head section-head--center">
				<p class="kicker"><?php esc_html_e( 'Lo que nos importa', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Cuatro cosas que no negociamos.', 'temaestetica' ); ?></h2>
			</header>
			<ul class="values-grid values-grid--large">
				<?php foreach ( $glowup_values as $value ) : ?>
					<li>
						<span class="values-grid__mark"><?php echo glowup_mark_svg( 24 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<strong><?php echo esc_html( $value['title'] ); ?></strong>
						<span><?php echo esc_html( $value['text'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<?php get_template_part( 'template-parts/section', 'professionals' ); ?>
	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
