<?php
/**
 * Template Name: Servicios
 *
 * @package GlowUp
 */

get_header();
$glowup_menu = glowup_get_service_menu();
?>
<main id="main" class="site-main">
	<section class="page-hero">
		<div class="container page-hero__inner">
			<p class="kicker"><?php esc_html_e( 'La carta GlowUp', 'temaestetica' ); ?></p>
			<h1 class="page-hero__title"><?php esc_html_e( 'Servicios', 'temaestetica' ); ?></h1>
			<p class="page-hero__lead"><?php esc_html_e( 'Todo lo que hacemos, con duraciones y precios claros. Elegí el servicio que necesitás y reservá el turno que mejor te quede.', 'temaestetica' ); ?></p>
		</div>
	</section>

	<nav class="category-nav" aria-label="<?php esc_attr_e( 'Categorías de servicios', 'temaestetica' ); ?>">
		<div class="container category-nav__inner">
			<?php foreach ( $glowup_menu as $slug => $group ) : ?>
				<a href="#<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $group['title'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</nav>

	<div class="container services-page">
		<?php foreach ( $glowup_menu as $slug => $group ) : ?>
			<section class="services-page__group" id="<?php echo esc_attr( $slug ); ?>">
				<header class="services-page__head">
					<div>
						<p class="kicker"><?php echo esc_html( sprintf( '%02d', array_search( $slug, array_keys( $glowup_menu ), true ) + 1 ) ); ?></p>
						<h2><?php echo esc_html( $group['title'] ); ?></h2>
					</div>
					<p><?php echo esc_html( $group['description'] ); ?></p>
				</header>

				<ul class="service-list service-list--full">
					<?php foreach ( $group['items'] as $item ) : ?>
						<li class="service-item">
							<div class="service-item__main">
								<span class="service-item__name"><?php echo esc_html( $item['name'] ); ?></span>
								<?php if ( ! empty( $item['description'] ) ) : ?>
									<span class="service-item__description"><?php echo esc_html( $item['description'] ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $item['requires_evaluation'] ) ) : ?>
									<span class="tag"><?php esc_html_e( 'Requiere evaluación', 'temaestetica' ); ?></span>
								<?php endif; ?>
							</div>
							<span class="service-item__duration"><?php echo esc_html( glowup_duration( $item['duration'] ) ); ?></span>
							<span class="service-item__price"><?php echo esc_html( glowup_price( $item['price'] ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>

				<a class="btn btn--ghost services-page__cta" href="<?php echo esc_url( add_query_arg( 'servicio', $slug, glowup_booking_url() ) ); ?>">
					<?php
					printf(
						/* translators: %s: category name. */
						esc_html__( 'Reservar %s', 'temaestetica' ),
						esc_html( strtolower( $group['title'] ) )
					);
					?>
				</a>
			</section>
		<?php endforeach; ?>

		<div class="services-page__note">
			<p><?php esc_html_e( 'Los precios son finales e incluyen impuestos cuando corresponda. Los servicios marcados como “requiere evaluación” necesitan una consulta previa. Si tenés alguna condición de salud, alergia o embarazo, contanos antes del tratamiento.', 'temaestetica' ); ?></p>
		</div>
	</div>

	<?php get_template_part( 'template-parts/section', 'cta' ); ?>
</main>
<?php
get_footer();
