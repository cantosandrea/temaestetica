<?php
/**
 * Archive template.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main">
	<div class="container page-content">
		<header class="page-header">
			<p class="kicker"><?php esc_html_e( 'Archivo', 'temaestetica' ); ?></p>
			<h1 class="page-title"><?php the_archive_title(); ?></h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				endwhile;
				?>
			</div>
			<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
