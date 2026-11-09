<?php
/**
 * Testimonials section.
 *
 * @package GlowUp
 */

$glowup_testimonials = glowup_get_testimonials();
?>
<section class="section testimonials-section">
	<div class="container">
		<header class="section-head section-head--center">
			<p class="kicker"><?php esc_html_e( 'Lo que dicen', 'temaestetica' ); ?></p>
			<h2><?php esc_html_e( 'El glow se nota. Y se cuenta.', 'temaestetica' ); ?></h2>
		</header>

		<div class="testimonials-grid">
			<?php foreach ( $glowup_testimonials as $testimonial ) : ?>
				<figure class="testimonial">
					<blockquote><?php echo esc_html( $testimonial['quote'] ); ?></blockquote>
					<figcaption>
						<span class="testimonial__name"><?php echo esc_html( $testimonial['name'] ); ?></span>
						<span class="testimonial__detail"><?php echo esc_html( $testimonial['detail'] ); ?></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
