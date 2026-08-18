<?php
/**
 * Resultados de busca.
 *
 * @package Espanol
 */

get_header();

global $wp_query;
?>

<header class="archive-header">
	<h1 class="archive-title">
		<?php
		/* translators: %s: termo buscado. */
		echo esc_html( sprintf( __( 'Resultados para "%s"', 'espanol' ), get_search_query() ) );
		?>
		<span class="archive-count"><?php espanol_the_icon( 'video' ); ?> <?php echo (int) $wp_query->found_posts; ?> <?php esc_html_e( 'videos', 'espanol' ); ?></span>
	</h1>
</header>

<?php if ( have_posts() ) : ?>
	<div class="video-grid" style="margin-top:16px">
		<?php
		while ( have_posts() ) :
			the_post();
			espanol_video_card();
		endwhile;
		?>
	</div>
	<?php espanol_pagination(); ?>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Nada encontrado. Intenta otra búsqueda.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
