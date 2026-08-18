<?php
/**
 * Home do site.
 *
 * Blocos (configuráveis em Opções do Tema → Blocos da Home):
 *  1. Últimos posts — sem título, é a grade principal paginada.
 *  2. Shorts (vitrine do Reelix, ou fallback do tema).
 *  3. Más Vistos.
 *  4. Más Votados.
 *  5. Canais populares.
 * Da página 2 em diante só a grade de últimos posts continua.
 *
 * @package Espanol
 */

get_header();

$espanol_paged = max( 1, (int) get_query_var( 'paged' ) );
?>

<h1 class="screen-reader-text"><?php echo esc_html( espanol_get_option( 'footer_title', get_bloginfo( 'name' ) . ' - ' . get_bloginfo( 'description' ) ) ); ?></h1>

<?php if ( have_posts() ) : ?>
	<div class="video-grid" style="margin-top:14px">
		<?php
		while ( have_posts() ) :
			the_post();
			espanol_video_card();
		endwhile;
		?>
	</div>
<?php else : ?>
	<p class="notice-empty"><?php esc_html_e( 'Aún no hay videos publicados.', 'espanol' ); ?></p>
<?php endif; ?>

<?php if ( 1 === $espanol_paged ) : ?>

	<?php if ( espanol_is_reelix_active() ) : ?>

		<?php
		// Shorts: vitrine do plugin Reelix.
		do_action( 'reelix_shelf' );
		?>

	<?php else : ?>

		<?php
		// Shorts (fallback do tema quando o Reelix está desativado).
		$espanol_shorts = new WP_Query(
			array(
				'post_type'      => espanol_video_types(),
				'posts_per_page' => 14,
				'meta_key'       => espanol_field( 'is_short' ),
				'meta_value'     => '1',
			)
		);
		if ( $espanol_shorts->have_posts() ) :
			espanol_section_bar( 'shorts', __( 'Shorts & Clips: Recomendados para ti', 'espanol' ), espanol_page_url_by_template( 'templates/page-shorts.php' ) );
			?>
			<div class="shorts-wrap">
				<button class="carousel-arrow prev js-scroll-prev" aria-label="<?php esc_attr_e( 'Anterior', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-l' ); ?></button>
				<div class="shorts-track js-scroll-track">
					<?php
					while ( $espanol_shorts->have_posts() ) :
						$espanol_shorts->the_post();
						espanol_short_card();
					endwhile;
					wp_reset_postdata();
					?>
				</div>
				<button class="carousel-arrow next js-scroll-next" aria-label="<?php esc_attr_e( 'Siguiente', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-r' ); ?></button>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php
	// Bloco "Más Vistos".
	if ( espanol_get_option( 'home_viewed_show', 1 ) ) :
		$espanol_viewed = new WP_Query(
			array(
				'post_type'      => espanol_video_types(),
				'posts_per_page' => (int) espanol_get_option( 'home_viewed_count', 10 ),
				'meta_key'       => espanol_field( 'views' ),
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			)
		);
		if ( $espanol_viewed->have_posts() ) :
			espanol_section_bar( 'eye', espanol_get_option( 'home_viewed_title', __( 'Videos Más Vistos', 'espanol' ) ) );
			?>
			<div class="video-grid">
				<?php
				while ( $espanol_viewed->have_posts() ) :
					$espanol_viewed->the_post();
					espanol_video_card();
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	// Bloco "Más Votados" (ordenado por likes).
	if ( espanol_get_option( 'home_voted_show', 1 ) ) :
		$espanol_voted = new WP_Query(
			array(
				'post_type'      => espanol_video_types(),
				'posts_per_page' => (int) espanol_get_option( 'home_voted_count', 10 ),
				'meta_key'       => espanol_field( 'likes' ),
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			)
		);
		if ( $espanol_voted->have_posts() ) :
			espanol_section_bar( 'like', espanol_get_option( 'home_voted_title', __( 'Videos Más Votados', 'espanol' ) ) );
			?>
			<div class="video-grid">
				<?php
				while ( $espanol_voted->have_posts() ) :
					$espanol_voted->the_post();
					espanol_video_card();
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	// Canais populares.
	$espanol_channels = get_terms(
		array(
			'taxonomy'   => 'channel',
			'hide_empty' => true,
			'number'     => 20,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	if ( ! is_wp_error( $espanol_channels ) && $espanol_channels ) :
		espanol_section_bar( 'folder', __( 'Canales Porno Populares', 'espanol' ) );
		?>
		<div class="shorts-wrap">
			<button class="carousel-arrow prev js-scroll-prev" aria-label="<?php esc_attr_e( 'Anterior', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-l' ); ?></button>
			<div class="channels-track js-scroll-track">
				<?php foreach ( $espanol_channels as $espanol_channel ) : ?>
					<a class="channel-card" href="<?php echo esc_url( get_term_link( $espanol_channel ) ); ?>">
						<span class="channel-avatar">
							<?php $espanol_ch_img = espanol_get_term_image( $espanol_channel ); ?>
							<?php if ( $espanol_ch_img ) : ?>
								<img src="<?php echo esc_url( $espanol_ch_img ); ?>" alt="<?php echo esc_attr( $espanol_channel->name ); ?>" loading="lazy">
							<?php endif; ?>
						</span>
						<span class="channel-name"><?php echo esc_html( $espanol_channel->name ); ?></span>
						<span class="channel-count"><?php echo (int) $espanol_channel->count; ?> <?php esc_html_e( 'videos', 'espanol' ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
			<button class="carousel-arrow next js-scroll-next" aria-label="<?php esc_attr_e( 'Siguiente', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-r' ); ?></button>
		</div>
	<?php endif; ?>

<?php endif; ?>

<?php espanol_pagination(); ?>

<?php get_footer(); ?>
