<?php
/**
 * Single post.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--single' ); ?>>
			<header class="page-hero page-hero--compact">
				<div class="container page-hero__inner">
					<p class="kicker"><?php echo esc_html( get_the_date() ); ?></p>
					<h1 class="page-hero__title"><?php the_title(); ?></h1>
					<p class="page-hero__meta"><?php the_category( ' · ' ); ?></p>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="container entry-hero-image">
					<?php the_post_thumbnail( 'glowup-wide' ); ?>
				</div>
			<?php endif; ?>

			<div class="container entry-content-wrap">
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'temaestetica' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>

				<footer class="entry-footer">
					<?php the_tags( '<p class="entry-tags">', ' ', '</p>' ); ?>
					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span class="nav-label">' . esc_html__( 'Anterior', 'temaestetica' ) . '</span><span class="nav-title">%title</span>',
							'next_text' => '<span class="nav-label">' . esc_html__( 'Siguiente', 'temaestetica' ) . '</span><span class="nav-title">%title</span>',
						)
					);
					?>
				</footer>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
