<?php
/**
 * Search form.
 *
 * @package GlowUp
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Buscar:', 'temaestetica' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Buscar…', 'temaestetica' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<button type="submit" class="btn btn--small"><?php esc_html_e( 'Buscar', 'temaestetica' ); ?></button>
</form>
