<?php
/**
 * About section.
 *
 * @package GlowUp
 */

$glowup_values = glowup_get_values();
?>
<section class="section about-section" id="nosotras">
	<div class="container about-grid">
		<div class="about-visual">
			<div class="about-image-wrap">
				<?php glowup_image( 'about', __( 'Interior y detalles del estudio GlowUp', 'temaestetica' ), 'about-image' ); ?>
			</div>
			<div class="about-stamp" aria-hidden="true">
				<?php echo glowup_mark_svg( 86 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>

		<div class="about-copy">
			<p class="kicker"><?php esc_html_e( 'El estudio', 'temaestetica' ); ?></p>
			<h2><?php esc_html_e( 'Un lugar para bajar un cambio.', 'temaestetica' ); ?></h2>
			<p class="about-lead"><?php esc_html_e( 'En GlowUp no llenamos la agenda de turnos imposibles. Trabajamos con tiempos reales, productos elegidos con criterio y una atención que no se apura.', 'temaestetica' ); ?></p>
			<p><?php esc_html_e( 'Creemos que el cuidado también es una forma de ordenar la semana. Por eso cada turno tiene el espacio que necesita: para escucharte, revisar tu piel o tu cabello y elegir el mejor camino juntas.', 'temaestetica' ); ?></p>

			<blockquote class="about-quote"><?php esc_html_e( '“La belleza no debería apurarse.”', 'temaestetica' ); ?></blockquote>

			<ul class="values-grid">
				<?php foreach ( $glowup_values as $value ) : ?>
					<li>
						<span class="values-grid__mark"><?php echo glowup_mark_svg( 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<strong><?php echo esc_html( $value['title'] ); ?></strong>
						<span><?php echo esc_html( $value['text'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="about-signature"><?php esc_html_e( 'Agustina & Estela', 'temaestetica' ); ?></p>
		</div>
	</div>
</section>
