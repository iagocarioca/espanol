<?php
/**
 * Arquivo geral de vídeos (suporta ?orderby=views).
 *
 * @package Espanol
 */

get_header();

global $wp_query;

$espanol_orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
?>

<header class="archive-header">
	<h1 class="archive-title">
		<?php echo 'views' === $espanol_orderby ? esc_html__( 'Videos más vistos', 'espanol' ) : esc_html__( 'Todos los videos', 'espanol' ); ?>
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
	<p class="notice-empty"><?php esc_html_e( 'Aún no hay videos publicados.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
