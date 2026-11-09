<?php
/**
 * Post card.
 *
 * @package GlowUp
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card__image" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'glowup-wide' ); ?>
		<?php else : ?>
			<img src="<?php echo esc_url( GLOWUP_URI . '/assets/img/placeholder-wide.svg' ); ?>" alt="" loading="lazy" decoding="async">
		<?php endif; ?>
	</a>

	<div class="post-card__body">
		<p class="post-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php the_category( ', ' ); ?></p>
		<h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="post-card__excerpt"><?php the_excerpt(); ?></div>
		<a class="text-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Leer nota', 'temaestetica' ); ?></a>
	</div>
</article>
