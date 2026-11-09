<?php
/**
 * WooCommerce wrapper.
 *
 * @package GlowUp
 */

get_header();
?>
<main id="main" class="site-main woocommerce-main">
	<div class="container">
		<?php woocommerce_content(); ?>
	</div>
</main>
<?php
get_footer();
