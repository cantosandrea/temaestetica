<?php
/**
 * Service marquee.
 *
 * @package GlowUp
 */

$glowup_menu = glowup_get_service_menu();
?>
<div class="service-marquee" aria-hidden="true">
	<div class="service-marquee__track">
		<?php for ( $i = 0; $i < 2; $i++ ) : ?>
			<?php foreach ( $glowup_menu as $group ) : ?>
				<span class="service-marquee__item"><?php echo esc_html( $group['title'] ); ?></span>
				<span class="service-marquee__dot"><?php echo glowup_mark_svg( 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php endforeach; ?>
			<span class="service-marquee__item"><?php esc_html_e( 'belleza sin apuro', 'temaestetica' ); ?></span>
			<span class="service-marquee__dot"><?php echo glowup_mark_svg( 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<?php endfor; ?>
	</div>
</div>
