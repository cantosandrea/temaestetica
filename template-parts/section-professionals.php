<?php
/**
 * Professionals section.
 *
 * @package GlowUp
 */

$glowup_people = glowup_get_professionals();
?>
<section class="section professionals-section" id="profesionales">
	<div class="container">
		<header class="section-head section-head--split">
			<div>
				<p class="kicker"><?php esc_html_e( 'Quiénes te reciben', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Dos miradas, un mismo cuidado.', 'temaestetica' ); ?></h2>
			</div>
			<div class="section-head__aside">
				<p><?php esc_html_e( 'Agustina y Estela combinan técnica, criterio y una obsesión sana por los detalles. Elegí con quién querés empezar.', 'temaestetica' ); ?></p>
			</div>
		</header>

		<div class="professionals-grid">
			<?php foreach ( $glowup_people as $index => $person ) : ?>
				<article class="pro-card <?php echo esc_attr( 0 === $index % 2 ? 'pro-card--butter' : 'pro-card--lilac' ); ?>">
					<div class="pro-card__image-wrap">
						<?php glowup_image( $person['image'], sprintf( 'Retrato de %s', $person['name'] ), 'pro-card__image' ); ?>
					</div>
					<div class="pro-card__body">
						<p class="pro-card__role"><?php echo esc_html( $person['role'] ); ?></p>
						<h3><?php echo esc_html( $person['name'] ); ?></h3>
						<p><?php echo esc_html( $person['bio'] ); ?></p>
						<a class="text-link" href="<?php echo esc_url( add_query_arg( 'profesional', $person['slug'], glowup_booking_url() ) ); ?>">
							<?php
							printf(
								/* translators: %s: professional name. */
								esc_html__( 'Reservar con %s', 'temaestetica' ),
								esc_html( $person['name'] )
							);
							?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
