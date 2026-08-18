<?php
/**
 * Template Name: Página de Shorts
 *
 * @package Espanol
 */

get_header();

$espanol_paged  = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$espanol_shorts = new WP_Query(
	array(
		'post_type'      => espanol_video_types(),
		'posts_per_page' => 24,
		'paged'          => $espanol_paged,
		'meta_key'       => espanol_field( 'is_short' ),
		'meta_value'     => '1',
	)
);
?>

<h1 class="page-heading"><?php espanol_the_icon( 'shorts' ); ?> <?php esc_html_e( 'Shorts & Clips', 'espanol' ); ?></h1>

<?php if ( $espanol_shorts->have_posts() ) : ?>
	<div class="video-grid cols-6" style="margin-top:16px">
		<?php
		while ( $espanol_shorts->have_posts() ) :
			$espanol_shorts->the_post();
			espanol_short_card();
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<?php espanol_pagination( $espanol_shorts ); ?>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Aún no hay shorts publicados.', 'espanol' ); ?></p>
<?php endif; ?>

<?php get_footer(); ?>
