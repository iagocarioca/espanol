<?php
/**
 * Single de vídeo.
 *
 * @package Espanol
 */

get_header();

while ( have_posts() ) :
	the_post();

	$espanol_id       = get_the_ID();
	$espanol_url      = get_post_meta( $espanol_id, espanol_field( 'url' ), true );
	$espanol_embed    = get_post_meta( $espanol_id, espanol_field( 'embed' ), true );
	$espanol_duration = get_post_meta( $espanol_id, espanol_field( 'duration' ), true );
	$espanol_views    = (int) get_post_meta( $espanol_id, espanol_field( 'views' ), true );
	$espanol_likes    = (int) get_post_meta( $espanol_id, espanol_field( 'likes' ), true );
	$espanol_percent  = espanol_like_percent( $espanol_id );
	?>

	<article class="single-video-wrap" data-video-id="<?php echo (int) $espanol_id; ?>">
		<?php
		// Fonte na ordem do tema-a99: video_uuid (Aurora5 assinado) -> mp4 -> embed -> poster.
		$espanol_src = espanol_video_src( $espanol_id );
		if ( $espanol_src ) :
			?>
			<div id="preroll" class="preroll video-player">
				<video
					id="my-video"
					class="video-js"
					controls
					preload="auto"
					<?php echo has_post_thumbnail() ? 'poster="' . esc_url( get_the_post_thumbnail_url( $espanol_id, 'full' ) ) . '"' : ''; ?>
					data-setup="{}"
				>
					<source src="<?php echo esc_url( $espanol_src ); ?>" type="video/mp4" />
				</video>
			</div>
			<script>var siteLocalAppend = '#preroll';</script>
		<?php elseif ( $espanol_embed ) : ?>
			<div class="video-player">
				<?php echo $espanol_embed; // phpcs:ignore WordPress.Security.EscapeOutput -- embed cadastrado pelo admin. ?>
			</div>
		<?php elseif ( has_post_thumbnail() ) : ?>
			<div class="video-player">
				<div class="player-poster">
					<?php the_post_thumbnail( 'large' ); ?>
					<span class="play-icon"><?php espanol_the_icon( 'play' ); ?></span>
				</div>
			</div>
		<?php endif; ?>

		<h1 class="single-title"><?php the_title(); ?></h1>

		<div class="single-meta">
			<span class="js-views"><?php echo esc_html( espanol_format_views( $espanol_views ) ); ?> views</span>
			<span>•</span>
			<span><?php esc_html_e( 'hace', 'espanol' ); ?> <?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span>
			<?php if ( null !== $espanol_percent ) : ?>
				<span>•</span>
				<span class="likes-pct js-like-pct"><?php echo (int) $espanol_percent; ?>% likes</span>
			<?php endif; ?>
		</div>

		<div class="single-actions">
			<span class="action-group">
				<button class="action-btn js-vote" data-vote="like"><?php espanol_the_icon( 'like' ); ?> <span class="js-like-count"><?php echo (int) $espanol_likes; ?></span></button>
				<span class="sep"></span>
				<button class="action-btn js-vote" data-vote="dislike"><?php espanol_the_icon( 'dislike' ); ?></button>
			</span>
			<?php if ( $espanol_duration ) : ?>
				<span class="action-btn"><?php espanol_the_icon( 'clock' ); ?> <?php echo esc_html( $espanol_duration ); ?> mins</span>
			<?php endif; ?>
			<button class="action-btn js-fav" data-id="<?php echo (int) $espanol_id; ?>"><?php espanol_the_icon( 'heart' ); ?> <?php esc_html_e( 'Añadir a favoritos', 'espanol' ); ?></button>
			<button class="action-btn js-share"><?php espanol_the_icon( 'share' ); ?> <?php esc_html_e( 'Compartir', 'espanol' ); ?></button>
		</div>

		<?php
		$espanol_terms = array();
		foreach ( array( espanol_tax( 'category' ), espanol_tax( 'tag' ), 'pornstar' ) as $espanol_tax ) {
			$espanol_tax_terms = get_the_terms( $espanol_id, $espanol_tax );
			if ( $espanol_tax_terms && ! is_wp_error( $espanol_tax_terms ) ) {
				$espanol_terms = array_merge( $espanol_terms, $espanol_tax_terms );
			}
		}
		$espanol_tg = espanol_get_option( 'social_telegram' );
		if ( $espanol_terms || $espanol_tg ) :
			?>
			<div class="tag-chips">
				<?php if ( $espanol_tg ) : ?>
					<a class="chip chip-telegram" href="<?php echo esc_url( $espanol_tg ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'telegram' ); ?> Telegram</a>
				<?php endif; ?>
				<?php foreach ( $espanol_terms as $espanol_term ) : ?>
					<a class="chip" href="<?php echo esc_url( get_term_link( $espanol_term ) ); ?>"><?php echo esc_html( $espanol_term->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>
			<div class="archive-desc"><?php the_content(); ?></div>
		<?php endif; ?>
	</article>

	<?php
	// Relacionados por categoria.
	$espanol_cats    = wp_get_post_terms( $espanol_id, espanol_tax( 'category' ), array( 'fields' => 'ids' ) );
	$espanol_related = new WP_Query(
		array(
			'post_type'      => espanol_video_types(),
			'posts_per_page' => 12,
			'post__not_in'   => array( $espanol_id ),
			'orderby'        => 'rand',
			'tax_query'      => $espanol_cats && ! is_wp_error( $espanol_cats ) ? array(
				array(
					'taxonomy' => espanol_tax( 'category' ),
					'terms'    => $espanol_cats,
				),
			) : array(),
		)
	);
	if ( $espanol_related->have_posts() ) :
		?>
		<h2 class="screen-reader-text"><?php esc_html_e( 'Videos relacionados', 'espanol' ); ?></h2>
		<div class="video-grid cols-6" style="margin-top:22px">
			<?php
			while ( $espanol_related->have_posts() ) :
				$espanol_related->the_post();
				espanol_video_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<?php
		$espanol_first_cat = $espanol_cats && ! is_wp_error( $espanol_cats ) ? get_term( $espanol_cats[0], espanol_tax( 'category' ) ) : null;
		if ( $espanol_first_cat && ! is_wp_error( $espanol_first_cat ) ) :
			?>
			<a class="btn-more" href="<?php echo esc_url( get_term_link( $espanol_first_cat ) ); ?>"><?php esc_html_e( 'Mostrar más videos relacionados', 'espanol' ); ?></a>
		<?php endif; ?>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
