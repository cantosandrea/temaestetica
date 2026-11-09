<?php
/**
 * Main template.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main">
	<div class="container page-content">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<p class="kicker"><?php esc_html_e( 'Novedades', 'temaestetica' ); ?></p>
				<h1 class="page-title"><?php echo esc_html( glowup_page_title( 'Novedades' ) ); ?></h1>
			</header>

			<div class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => esc_html__( 'Anterior', 'temaestetica' ),
					'next_text' => esc_html__( 'Siguiente', 'temaestetica' ),
				)
			);
			?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
