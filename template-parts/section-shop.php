<?php
/**
 * Shop section.
 *
 * @package GlowUp
 */

if ( glowup_store_is_coming_soon() ) :
	?>
	<section class="section shop-section" id="tienda">
		<div class="container">
			<div class="gift-callout shop-coming-soon">
				<div class="shop-coming-soon__copy">
					<p class="kicker"><?php esc_html_e( 'La tienda', 'temaestetica' ); ?></p>
					<h3><?php esc_html_e( 'La tienda online está en camino.', 'temaestetica' ); ?></h3>
					<p><?php esc_html_e( 'Estamos preparando una selección de productos para que el cuidado siga en casa. Muy pronto vas a poder comprarlos online.', 'temaestetica' ); ?></p>
				</div>
				<a class="btn btn--ghost" href="<?php echo esc_url( glowup_shop_url() ); ?>"><?php esc_html_e( 'Ver novedades', 'temaestetica' ); ?></a>
			</div>
		</div>
	</section>
	<?php
	return;
endif;

$glowup_categories = glowup_get_shop_categories();
?>
<section class="section shop-section" id="tienda">
	<div class="container">
		<header class="section-head section-head--split">
			<div>
				<p class="kicker"><?php esc_html_e( 'La tienda', 'temaestetica' ); ?></p>
				<h2><?php esc_html_e( 'Para que el glow siga en casa.', 'temaestetica' ); ?></h2>
			</div>
			<div class="section-head__aside">
				<p><?php esc_html_e( 'Una selección chica y pensada: lo que usamos, lo que recomendamos y lo que vale la pena tener a mano.', 'temaestetica' ); ?></p>
				<a class="text-link" href="<?php echo esc_url( glowup_shop_url() ); ?>"><?php esc_html_e( 'Ver toda la tienda', 'temaestetica' ); ?></a>
			</div>
		</header>

		<div class="shop-categories">
			<?php foreach ( $glowup_categories as $category ) : ?>
				<a class="shop-category" href="<?php echo esc_url( $category['url'] ); ?>">
					<div class="shop-category__image">
						<?php glowup_image( $category['image'], $category['title'], '', false ); ?>
					</div>
					<div class="shop-category__body">
						<h3><?php echo esc_html( $category['title'] ); ?></h3>
						<p><?php echo esc_html( $category['text'] ); ?></p>
						<span class="shop-category__arrow"><?php echo glowup_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				</a>
			<?php endforeach; ?>
		</div>

		<div class="gift-callout">
			<div>
				<p class="kicker"><?php esc_html_e( 'Regalá GlowUp', 'temaestetica' ); ?></p>
				<h3><?php esc_html_e( 'Una gift card también es una forma de decir “te quiero”.', 'temaestetica' ); ?></h3>
			</div>
			<a class="btn btn--ghost" href="<?php echo esc_url( glowup_shop_url() ); ?>"><?php esc_html_e( 'Ver gift cards', 'temaestetica' ); ?></a>
		</div>
	</div>
</section>
