<?php
/**
 * Formulário de busca.
 *
 * @package Espanol
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar videos, actores, categorías y tags', 'espanol' ); ?>">
	<button type="submit" aria-label="<?php esc_attr_e( 'Buscar', 'espanol' ); ?>"><?php espanol_the_icon( 'search' ); ?></button>
</form>
