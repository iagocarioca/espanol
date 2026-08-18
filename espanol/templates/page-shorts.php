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

$espanol_reelix = class_exists( '\Reelix\Frontend\Explore' );

// Contagem de shorts publicados, para o subtítulo do cabeçalho.
$espanol_total = 0;
if ( $espanol_reelix ) {
	$espanol_count = wp_count_posts( 'reelix_short' );
	$espanol_total = $espanol_count ? (int) $espanol_count->publish : 0;
}
?>

<header class="page-head">
	<h1 class="page-heading"><?php espanol_the_icon( 'shorts' ); ?> <?php esc_html_e( 'Shorts &amp; Clips', 'espanol' ); ?></h1>

	<?php if ( $espanol_total > 0 ) : ?>
		<p class="page-subheading">
			<?php
			printf(
				/* translators: %s: número de shorts publicados. */
				esc_html( _n( '%s short para ver ahora', '%s shorts para ver ahora', $espanol_total, 'espanol' ) ),
				'<b>' . esc_html( number_format_i18n( $espanol_total ) ) . '</b>'
			);
			?>
		</p>
	<?php endif; ?>
</header>

<?php
// Conteúdo editado na própria página (opcional, acima da grade).
while ( have_posts() ) :
	the_post();
	$espanol_intro = trim( get_the_content() );
	if ( '' !== $espanol_intro ) {
		echo '<div class="page-body page-intro">' . wp_kses_post( apply_filters( 'the_content', $espanol_intro ) ) . '</div>';
	}
endwhile;
wp_reset_postdata();

if ( $espanol_reelix ) :
	// O cabeçalho acima já anuncia a seção, por isso title="no".
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
