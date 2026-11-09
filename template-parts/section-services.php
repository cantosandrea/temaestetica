<?php
/**
 * Services section.
 *
 * @package GlowUp
 */

$glowup_menu  = glowup_get_service_menu();
$glowup_limit = 4;
?>
<section class="section services-section" id="servicios">
	<div class="container">
		<header class="section-head section-head--split">
			<div>
				<p class="kicker"><?php esc_html_e( 'La carta GlowUp', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Servicios que se sienten como un respiro.', 'temaestetica' ); ?></h2>
			</div>
			<div class="section-head__aside">
				<p><?php esc_html_e( 'Precios finales, duraciones claras y una profesional asignada para cada servicio. Si algo necesita evaluación, te lo decimos antes de reservar.', 'temaestetica' ); ?></p>
				<a class="text-link" href="<?php echo esc_url( home_url( '/servicios/' ) ); ?>"><?php esc_html_e( 'Ver la carta completa', 'temaestetica' ); ?></a>
			</div>
		</header>

		<div class="service-menu">
			<?php foreach ( $glowup_menu as $slug => $group ) : ?>
				<article class="service-group" id="<?php echo esc_attr( $slug ); ?>">
					<header class="service-group__head">
						<h3><?php echo esc_html( $group['title'] ); ?></h3>
						<p><?php echo esc_html( $group['description'] ); ?></p>
					</header>

					<ul class="service-list">
						<?php foreach ( array_slice( $group['items'], 0, $glowup_limit ) as $item ) : ?>
							<li class="service-item">
								<div class="service-item__main">
									<span class="service-item__name"><?php echo esc_html( $item['name'] ); ?></span>
									<?php if ( ! empty( $item['requires_evaluation'] ) ) : ?>
										<span class="tag"><?php esc_html_e( 'Requiere evaluación', 'temaestetica' ); ?></span>
									<?php endif; ?>
								</div>
								<span class="service-item__duration"><?php echo esc_html( glowup_duration( $item['duration'] ) ); ?></span>
								<span class="service-item__price"><?php echo esc_html( glowup_price( $item['price'] ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>

					<a class="text-link service-group__link" href="<?php echo esc_url( home_url( '/servicios/#' . $slug ) ); ?>">
						<?php
						printf(
							/* translators: %s: service category. */
							esc_html__( 'Ver todos los servicios de %s', 'temaestetica' ),
							esc_html( strtolower( $group['title'] ) )
						);
						?>
					</a>
				</article>
			<?php endforeach; ?>
		</div>

		<p class="services-note"><?php esc_html_e( 'Los precios pueden actualizarse. Los servicios marcados como “requiere evaluación” necesitan una consulta previa.', 'temaestetica' ); ?></p>
	</div>
</section>
