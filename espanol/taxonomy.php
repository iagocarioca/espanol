<?php
/**
 * Arquivo de taxonomias (categorias, tags, pornstars, canais).
 *
 * @package Espanol
 */

get_header();

$espanol_term = get_queried_object();
?>

<header class="archive-header">
	<h1 class="archive-title">
		<?php
		/* translators: %s: nome do termo. */
		echo esc_html( sprintf( __( '%s Porn Videos', 'espanol' ), $espanol_term->name ) );
		?>
		<span class="archive-count"><?php espanol_the_icon( 'video' ); ?> <?php echo (int) $espanol_term->count; ?> <?php esc_html_e( 'videos', 'espanol' ); ?></span>
	</h1>
	<?php if ( term_description() ) : ?>
		<div class="archive-desc"><?php echo wp_kses_post( term_description() ); ?></div>
	<?php endif; ?>
</header>

<?php
// Row de shorts do Reelix no topo do arquivo (renderiza só se o plugin estiver ativo e houver shorts no termo).
do_action( 'reelix_tax_shorts' );
?>

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
	<p class="notice-empty"><?php esc_html_e( 'No se encontraron videos en esta sección.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
