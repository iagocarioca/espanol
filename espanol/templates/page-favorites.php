<?php
/**
 * Template Name: Página de Favoritos
 *
 * @package Espanol
 */

get_header();
?>

<h1 class="page-heading"><?php espanol_the_icon( 'heart' ); ?> <?php esc_html_e( 'Mis Favoritos', 'espanol' ); ?></h1>

<div class="video-grid js-favorites-grid" style="margin-top:16px"></div>
<p class="notice-empty js-favorites-empty" style="display:none"><?php esc_html_e( 'Aún no has añadido videos a favoritos. Haz clic en el corazón de los videos para guardarlos aquí.', 'espanol' ); ?></p>

<?php get_footer(); ?>
