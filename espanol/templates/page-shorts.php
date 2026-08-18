<?php
/**
 * Template Name: Página de Shorts
 *
 * Renderiza a grade do plugin Reelix (CPT reelix_short), com scroll
 * infinito e abas de ordenação. Se o plugin estiver inativo, cai no
 * grid do próprio tema (posts marcados com a meta is_short).
 *
 * @package Espanol
 */

get_header();
?>

<h1 class="page-heading"><?php espanol_the_icon( 'shorts' ); ?> <?php esc_html_e( 'Shorts & Clips', 'espanol' ); ?></h1>

<?php
if ( class_exists( '\Reelix\Frontend\Explore' ) ) :
	// O título já vem do cabeçalho da página, por isso title="no".
	echo do_shortcode( '[reelix_explore title="no"]' );
else :
	// Fallback: shorts publicados como posts do próprio tema.
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

	if ( $espanol_shorts->have_posts() ) :
		?>
		<div class="video-grid cols-6" style="margin-top:16px">
			<?php
			while ( $espanol_shorts->have_posts() ) :
				$espanol_shorts->the_post();
				espanol_short_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php
		espanol_pagination( $espanol_shorts );
	else :
		?>
		<p class="notice-empty"><?php esc_html_e( 'Aún no hay shorts publicados.', 'espanol' ); ?></p>
		<?php
	endif;
endif;
?>

<?php get_footer(); ?>
