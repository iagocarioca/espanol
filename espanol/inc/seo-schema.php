<?php
/**
 * SEO: dados estruturados (JSON-LD) e Open Graph.
 *
 * Emite VideoObject/ImageObject nas páginas de vídeo para elegibilidade
 * nas abas Vídeos e Imagens do Google, mais Open Graph/Twitter Card.
 *
 * @package Espanol
 */

defined( 'ABSPATH' ) || exit;

/**
 * Converte duração livre (12:51, 1:02:30, "12 min", 771) em ISO 8601.
 *
 * O campo `tempo` é digitado à mão no admin, então aceita vários formatos.
 *
 * @param string $raw Duração como cadastrada.
 * @return string Duração ISO 8601 (PT12M51S) ou vazio se não reconhecida.
 */
function espanol_duration_to_iso( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	// Formato com dois pontos: mm:ss ou hh:mm:ss.
	if ( false !== strpos( $raw, ':' ) ) {
		$parts = array_map( 'intval', explode( ':', $raw ) );

		if ( 3 === count( $parts ) ) {
			list( $h, $m, $s ) = $parts;
		} elseif ( 2 === count( $parts ) ) {
			$h = 0;
			list( $m, $s ) = $parts;
		} else {
			return '';
		}

		if ( $h < 0 || $m < 0 || $s < 0 ) {
			return '';
		}

		$iso = 'PT';
		if ( $h ) {
			$iso .= $h . 'H';
		}
		if ( $m ) {
			$iso .= $m . 'M';
		}
		if ( $s ) {
			$iso .= $s . 'S';
		}

		return 'PT' === $iso ? '' : $iso;
	}

	// Só dígitos: trata como minutos (é como o admin costuma preencher).
	if ( preg_match( '/^(\d+)\s*(min|minutos)?$/i', $raw, $m ) ) {
		$minutes = (int) $m[1];
		return $minutes > 0 ? 'PT' . $minutes . 'M' : '';
	}

	return '';
}

/**
 * Dados do vídeo do post atual para uso no schema.
 *
 * @param int $post_id ID do post.
 * @return array Dados normalizados.
 */
function espanol_schema_video_data( $post_id ) {
	$thumb = get_the_post_thumbnail_url( $post_id, 'full' );

	return array(
		'thumb'    => $thumb ? $thumb : '',
		'src'      => espanol_video_src( $post_id ),
		'embed'    => (string) get_post_meta( $post_id, espanol_field( 'embed' ), true ),
		'duration' => espanol_duration_to_iso( get_post_meta( $post_id, espanol_field( 'duration' ), true ) ),
		'views'    => (int) get_post_meta( $post_id, espanol_field( 'views' ), true ),
	);
}

/**
 * JSON-LD VideoObject + ImageObject nas páginas de vídeo.
 *
 * Sem VideoObject o Google não identifica a página como tendo vídeo e ela
 * fica inelegível para a aba Vídeos, independente do ranking.
 */
function espanol_schema_video() {
	if ( ! is_singular( espanol_video_types() ) ) {
		return;
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$data = espanol_schema_video_data( $post_id );

	// Sem thumbnail o VideoObject é inválido: thumbnailUrl é obrigatório.
	if ( ! $data['thumb'] ) {
		return;
	}

	$description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
	if ( ! $description ) {
		$description = wp_strip_all_tags( get_the_title( $post_id ) );
	}

	$schema = array(
		'@context'     => 'https://schema.org',
		'@type'        => 'VideoObject',
		'name'         => wp_strip_all_tags( get_the_title( $post_id ) ),
		'description'  => $description,
		'thumbnailUrl' => array( $data['thumb'] ),
		'uploadDate'   => get_the_date( 'c', $post_id ),
		'url'          => get_permalink( $post_id ),
	);

	if ( $data['duration'] ) {
		$schema['duration'] = $data['duration'];
	}

	if ( $data['src'] ) {
		$schema['contentUrl'] = $data['src'];
	}

	// embedUrl só quando há iframe: extrai o src do código cadastrado.
	if ( ! $data['src'] && $data['embed'] && preg_match( '/src=["\']([^"\']+)["\']/i', $data['embed'], $m ) ) {
		$schema['embedUrl'] = $m[1];
	}

	// Sem contentUrl nem embedUrl o Google descarta o VideoObject.
	if ( empty( $schema['contentUrl'] ) && empty( $schema['embedUrl'] ) ) {
		return;
	}

	if ( $data['views'] > 0 ) {
		$schema['interactionStatistic'] = array(
			'@type'                => 'InteractionCounter',
			'interactionType'      => array( '@type' => 'WatchAction' ),
			'userInteractionCount' => $data['views'],
		);
	}

	// Conteúdo adulto: declarar explicitamente evita classificação errada.
	$schema['isFamilyFriendly'] = false;

	echo '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'espanol_schema_video', 5 );

/**
 * Open Graph e Twitter Card.
 *
 * Alimenta a aba Imagens e o preview em redes sociais.
 */
function espanol_open_graph() {
	$site = get_bloginfo( 'name' );

	if ( is_singular() ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$title = wp_strip_all_tags( get_the_title( $post_id ) );
		$desc  = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		$thumb = get_the_post_thumbnail_url( $post_id, 'full' );
		$is_video = is_singular( espanol_video_types() );

		echo '<meta property="og:type" content="' . ( $is_video ? 'video.other' : 'article' ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( get_permalink( $post_id ) ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $site ) . '">' . "\n";

		if ( $desc ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
		}

		if ( $thumb ) {
			echo '<meta property="og:image" content="' . esc_url( $thumb ) . '">' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $thumb ) . '">' . "\n";
		} else {
			echo '<meta name="twitter:card" content="summary">' . "\n";
		}

		echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";

		if ( $is_video ) {
			$src = espanol_video_src( $post_id );
			if ( $src ) {
				echo '<meta property="og:video" content="' . esc_url( $src ) . '">' . "\n";
				echo '<meta property="og:video:type" content="video/mp4">' . "\n";
			}
		}

		return;
	}

	// Home e arquivos.
	$title = is_front_page() ? $site : wp_get_document_title();

	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( wp_strip_all_tags( $title ) ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary">' . "\n";

	$logo = espanol_get_option( 'logo' );
	if ( $logo ) {
		echo '<meta property="og:image" content="' . esc_url( $logo ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'espanol_open_graph', 6 );

/**
 * Fallback de alt nas thumbnails.
 *
 * Imagens importadas em massa costumam ficar sem alt na biblioteca, o que tira
 * o contexto que o Google usa na aba Imagens. Usa o título do post quando falta.
 *
 * @param array  $attr      Atributos do <img>.
 * @param object $attachment Anexo.
 * @return array Atributos ajustados.
 */
function espanol_thumb_alt_fallback( $attr, $attachment ) {
	if ( ! empty( $attr['alt'] ) ) {
		return $attr;
	}

	$parent = isset( $attachment->post_parent ) ? (int) $attachment->post_parent : 0;
	$title  = $parent ? get_the_title( $parent ) : '';

	if ( ! $title ) {
		$title = isset( $attachment->post_title ) ? $attachment->post_title : '';
	}

	if ( $title ) {
		$attr['alt'] = wp_strip_all_tags( $title );
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'espanol_thumb_alt_fallback', 10, 2 );
