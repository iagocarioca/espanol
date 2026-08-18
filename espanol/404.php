<?php
/**
 * Página 404.
 *
 * @package Espanol
 */

get_header();
?>
<div class="error-404">
	<h1>404</h1>
	<p><?php esc_html_e( 'Página no encontrada. Usa la búsqueda o vuelve al inicio.', 'espanol' ); ?></p>
	<p style="margin-top:18px"><a class="btn-more" style="display:inline-block;width:auto;padding:12px 30px" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Volver al inicio', 'espanol' ); ?></a></p>
</div>
<?php get_footer(); ?>
