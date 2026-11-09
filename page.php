<?php
/**
 * Default page template.
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
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry entry--page' ); ?>>
			<header class="page-hero page-hero--compact">
				<div class="container page-hero__inner">
					<p class="kicker"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<h1 class="page-hero__title"><?php the_title(); ?></h1>
				</div>
			</header>

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
			</div>
		</article>
		<?php
	endwhile;
	?>
</main>
<?php
get_footer();
