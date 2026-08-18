<?php
/**
 * Template tags e helpers de exibição.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ícones SVG inline.
 *
 * @param string $name Nome do ícone.
 * @return string SVG.
 */
function espanol_icon( $name ) {
	$icons = array(
		'menu'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
		'close'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="22" height="22"><path d="M18 6 6 18M6 6l12 12"/></svg>',
		'search'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="19" height="19"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
		'lock'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="19" height="19"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>',
		'star'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></svg>',
		'grid'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>',
		'shorts'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="7" y="3" width="10" height="18" rx="2"/><path d="M3 7v10M21 7v10"/></svg>',
		'heart'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.5-1.5 2-3.2 2-4.5A4.5 4.5 0 0 0 12 6.5 4.5 4.5 0 0 0 3 9.5c0 1.3.5 3 2 4.5l7 7z"/></svg>',
		'camera'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h3l2-3h6l2 3h3a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z"/><circle cx="12" cy="13" r="4"/></svg>',
		'tag'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12 12 4H4v8l8 8 8-8z"/><circle cx="8.5" cy="8.5" r="1.5"/></svg>',
		'play'      => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>',
		'video'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="15" height="14" rx="2"/><path d="m17 10 5-3v10l-5-3z"/></svg>',
		'eye'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>',
		'folder'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
		'clock'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>',
		'like'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 10v11H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h3zm0 0 4-7a2.4 2.4 0 0 1 2.4 2.4V9H19a2 2 0 0 1 2 2.3l-1.1 7A2 2 0 0 1 17.9 20H7"/></svg>',
		'dislike'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transform:rotate(180deg)"><path d="M7 10v11H4a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h3zm0 0 4-7a2.4 2.4 0 0 1 2.4 2.4V9H19a2 2 0 0 1 2 2.3l-1.1 7A2 2 0 0 1 17.9 20H7"/></svg>',
		'share'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/></svg>',
		'chev-l'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><path d="m15 6-6 6 6 6"/></svg>',
		'chev-r'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="18" height="18"><path d="m9 6 6 6-6 6"/></svg>',
		'telegram'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M21.9 4.6c.2-1-.7-1.8-1.6-1.4L2.7 10.1c-1 .4-1 1.9.1 2.2l4.5 1.4 1.7 5.4c.3.9 1.4 1.1 2 .4l2.5-2.6 4.6 3.4c.8.6 2 .2 2.2-.9zM8.2 13l9.3-6.1c.2-.2.5.1.3.3l-7.7 7.1-.3 3.1z"/></svg>',
		'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-6.9 7.9L23.2 22h-6.4l-5-6.6L6 22H2.9l7.4-8.5L1.7 2h6.6l4.5 6zm-1.1 18h1.7L7.4 3.8H5.5z"/></svg>',
		'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23 7.2a3 3 0 0 0-2.1-2.1C19 4.5 12 4.5 12 4.5s-7 0-8.9.6A3 3 0 0 0 1 7.2 31 31 0 0 0 .5 12 31 31 0 0 0 1 16.8a3 3 0 0 0 2.1 2.1c1.9.6 8.9.6 8.9.6s7 0 8.9-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 23.5 12 31 31 0 0 0 23 7.2zM9.8 15.3V8.7l5.8 3.3z"/></svg>',
		'mail'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6L22 7"/></svg>',
		'gender'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><circle cx="10" cy="14" r="5"/><path d="M19 5l-5.5 5.5M14 5h5v5"/></svg>',
		'dots'      => '<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><circle cx="12" cy="5" r="1.7"/><circle cx="12" cy="12" r="1.7"/><circle cx="12" cy="19" r="1.7"/></svg>',
		'globe'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>',
		'user'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/></svg>',
		'shield'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.4 9.3 8 11 4.6-1.7 8-6 8-11V5z"/><path d="M9.5 12l2 2 3.5-4"/></svg>',
		'info'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8h.01M12 11v5"/></svg>',
		'gear'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.7 1.7 0 0 0 1.87.34h.01a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55h.01a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.7 1.7 0 0 0-.34 1.87v.01a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1z"/></svg>',
		'login'     => '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M16.8 2H14.2C11 2 9 4 9 7.2V11.25H13.44L11.37 9.18C11.22 9.03 11.15 8.84 11.15 8.65C11.15 8.46 11.22 8.27 11.37 8.12C11.66 7.83 12.14 7.83 12.43 8.12L15.78 11.47C16.07 11.76 16.07 12.24 15.78 12.53L12.43 15.88C12.14 16.17 11.66 16.17 11.37 15.88C11.08 15.59 11.08 15.11 11.37 14.82L13.44 12.75H9V16.8C9 20 11 22 14.2 22H16.79C19.99 22 21.99 20 21.99 16.8V7.2C22 4 20 2 16.8 2Z"/><path d="M2.75 11.25C2.34 11.25 2 11.59 2 12C2 12.41 2.34 12.75 2.75 12.75H9V11.25H2.75Z"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Imprime um ícone.
 *
 * @param string $name Nome do ícone.
 */
function espanol_the_icon( $name ) {
	echo espanol_icon( $name ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG interno estático.
}

/**
 * Formata número de views (622K, 1.3M).
 *
 * @param int $num Número.
 * @return string
 */
function espanol_format_views( $num ) {
	$num = (int) $num;
	if ( $num >= 1000000 ) {
		return rtrim( rtrim( number_format( $num / 1000000, 1 ), '0' ), '.' ) . 'M';
	}
	if ( $num >= 1000 ) {
		return rtrim( rtrim( number_format( $num / 1000, 1 ), '0' ), '.' ) . 'K';
	}
	return (string) $num;
}

/**
 * Percentual de likes de um vídeo.
 *
 * @param int $post_id ID do vídeo.
 * @return int|null Percentual ou null se sem votos.
 */
function espanol_like_percent( $post_id ) {
	$likes    = (int) get_post_meta( $post_id, espanol_field( 'likes' ), true );
	$dislikes = (int) get_post_meta( $post_id, espanol_field( 'dislikes' ), true );
	$total    = $likes + $dislikes;
	if ( ! $total ) {
		// Fallback: percentual "votos" gravado pelo tema-a99.
		$percent = get_post_meta( $post_id, espanol_field( 'percent' ), true );
		return ( '' !== $percent && null !== $percent ) ? (int) $percent : null;
	}
	return (int) round( ( $likes / $total ) * 100 );
}

/**
 * Card de vídeo (grid).
 *
 * @param int $post_id ID do vídeo.
 */
function espanol_video_card( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( 'reelix_short' === get_post_type( $post_id ) ) {
		// Shorts do Reelix guardam os dados em metas próprias.
		$views    = (int) get_post_meta( $post_id, 'views', true );
		$duration = get_post_meta( $post_id, 'duracao', true );
		if ( ! $duration ) {
			$duration = get_post_meta( $post_id, 'duration', true );
		}
		$percent = null;
	} else {
		$views    = (int) get_post_meta( $post_id, espanol_field( 'views' ), true );
		$duration = get_post_meta( $post_id, espanol_field( 'duration' ), true );
		$percent  = espanol_like_percent( $post_id );
	}
	$link = get_permalink( $post_id );
	?>
	<article class="video-card">
		<a class="vc-thumb" href="<?php echo esc_url( $link ); ?>">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'espanol-thumb', array( 'loading' => 'lazy' ) );
			}
			?>
			<?php if ( get_post_meta( $post_id, espanol_field( 'is_es' ), true ) ) : ?>
				<span class="vc-lang"><?php espanol_the_icon( 'globe' ); ?> ES</span>
			<?php elseif ( $duration ) : ?>
				<span class="vc-duration"><?php echo esc_html( $duration ); ?></span>
			<?php endif; ?>
		</a>
		<div class="vc-head">
			<h3 class="vc-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<div class="vc-menu-wrap">
				<button class="vc-menu js-card-menu" aria-label="<?php esc_attr_e( 'Opciones', 'espanol' ); ?>"><?php espanol_the_icon( 'dots' ); ?></button>
				<div class="vc-dropdown">
					<button class="js-fav" data-id="<?php echo (int) $post_id; ?>"><?php espanol_the_icon( 'heart' ); ?> <?php esc_html_e( 'Añadir a favoritos', 'espanol' ); ?></button>
					<button class="js-card-share" data-url="<?php echo esc_url( $link ); ?>"><?php espanol_the_icon( 'share' ); ?> <?php esc_html_e( 'Compartir', 'espanol' ); ?></button>
				</div>
			</div>
		</div>
		<div class="vc-meta">
			<span><?php echo esc_html( espanol_format_views( $views ) ); ?> <?php esc_html_e( 'Views', 'espanol' ); ?></span>
			<?php if ( $duration ) : ?>
				<span><?php echo esc_html( $duration ); ?> min</span>
			<?php endif; ?>
			<?php if ( null !== $percent ) : ?>
				<span class="vc-like"><?php espanol_the_icon( 'like' ); ?> <?php echo (int) $percent; ?>%</span>
			<?php endif; ?>
		</div>
	</article>
	<?php
}

/**
 * Card de short (vertical).
 *
 * @param int $post_id ID do vídeo.
 */
function espanol_short_card( $post_id = 0 ) {
	$post_id  = $post_id ? $post_id : get_the_ID();
	$views    = (int) get_post_meta( $post_id, espanol_field( 'views' ), true );
	$duration = get_post_meta( $post_id, espanol_field( 'duration' ), true );
	$link     = get_permalink( $post_id );
	?>
	<article class="short-card">
		<a class="short-thumb" href="<?php echo esc_url( $link ); ?>">
			<?php
			if ( has_post_thumbnail( $post_id ) ) {
				echo get_the_post_thumbnail( $post_id, 'espanol-short', array( 'loading' => 'lazy' ) );
			}
			?>
			<?php if ( $duration ) : ?>
				<span class="short-duration"><?php echo esc_html( $duration ); ?></span>
			<?php endif; ?>
		</a>
		<h3 class="short-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
		<div class="short-views"><?php echo esc_html( espanol_format_views( $views ) ); ?> <?php esc_html_e( 'Views', 'espanol' ); ?></div>
	</article>
	<?php
}

/**
 * Paginação numérica.
 *
 * @param WP_Query|null $query Query (opcional).
 */
function espanol_pagination( $query = null ) {
	global $wp_query;
	$q = $query ? $query : $wp_query;

	$links = paginate_links(
		array(
			'total'     => (int) $q->max_num_pages,
			'current'   => max( 1, (int) get_query_var( 'paged' ) ),
			'type'      => 'plain',
			'mid_size'  => 2,
			'prev_text' => espanol_icon( 'chev-l' ),
			'next_text' => espanol_icon( 'chev-r' ),
		)
	);

	if ( $links ) {
		echo '<nav class="pagination">' . $links . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput -- saída do core.
	}
}

/**
 * Barra de título de seção.
 *
 * @param string $icon  Nome do ícone.
 * @param string $title Título.
 * @param string $link  URL opcional "ver mais".
 */
function espanol_section_bar( $icon, $title, $link = '' ) {
	?>
	<div class="section-bar">
		<?php espanol_the_icon( $icon ); ?>
		<h2 class="bar-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $link ) : ?>
			<a class="bar-link" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'Ver todo', 'espanol' ); ?> ›</a>
		<?php endif; ?>
	</div>
	<?php
}
