<?php
/**
 * Front page.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main front-page">
	<?php
	get_template_part( 'template-parts/section', 'hero' );
	get_template_part( 'template-parts/section', 'marquee' );
	get_template_part( 'template-parts/section', 'services' );
	get_template_part( 'template-parts/section', 'about' );
	get_template_part( 'template-parts/section', 'professionals' );
	get_template_part( 'template-parts/section', 'process' );
	get_template_part( 'template-parts/section', 'shop' );
	get_template_part( 'template-parts/section', 'testimonials' );
	get_template_part( 'template-parts/section', 'cta' );
	?>
</main>
<?php
get_footer();
