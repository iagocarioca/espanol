<?php
/**
 * REST API — auditoria das fontes de vídeo dos posts.
 *
 * Expõe os metafields de vídeo (embed/iframe, mp4, video_uuid) para uma
 * aplicação externa auditar quais posts estão com a fonte problemática.
 *
 * Rota: /wp-json/espanol/v1/videos
 *
 * @package Espanol
 */

defined( 'ABSPATH' ) || exit;

/**
 * Namespace e versão da API do tema.
 */
const ESPANOL_REST_NS = 'espanol/v1';

/**
 * Registra as rotas.
 */
function espanol_rest_register() {
	register_rest_route(
		ESPANOL_REST_NS,
		'/videos',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'espanol_rest_videos',
			'permission_callback' => 'espanol_rest_permission',
			'args'                => array(
				'per_page' => array(
					'default'           => 50,
					'sanitize_callback' => 'absint',
					'validate_callback' => static function ( $v ) {
						return $v >= 1 && $v <= 200;
					},
				),
				'page'     => array(
					'default'           => 1,
					'sanitize_callback' => 'absint',
				),
				'status'   => array(
					'default'           => 'all',
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => static function ( $v ) {
						return in_array( $v, array( 'all', 'ok', 'broken', 'iframe', 'empty' ), true );
					},
				),
				'type'     => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
			),
		)
	);

	register_rest_route(
		ESPANOL_REST_NS,
		'/videos/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'espanol_rest_video',
			'permission_callback' => 'espanol_rest_permission',
			'args'                => array(
				'id' => array(
					'sanitize_callback' => 'absint',
				),
			),
		)
	);

	register_rest_route(
		ESPANOL_REST_NS,
		'/videos/stats',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'espanol_rest_stats',
			'permission_callback' => 'espanol_rest_permission',
		)
	);
}
add_action( 'rest_api_init', 'espanol_rest_register' );

/**
 * Quem pode consultar.
 *
 * Os metafields expõem URLs de origem dos vídeos, então a rota exige
 * um usuário com permissão de edição (Application Password serve).
 *
 * @return bool|WP_Error
 */
function espanol_rest_permission() {
	if ( current_user_can( 'edit_posts' ) ) {
		return true;
	}
	return new WP_Error(
		'espanol_rest_forbidden',
		__( 'Necesitas autenticarte para consultar esta información.', 'espanol' ),
		array( 'status' => rest_authorization_required_code() )
	);
}

/**
 * Analisa a fonte de vídeo de um post e classifica o estado.
 *
 * Reproduz a ordem de prioridade do tema (video_uuid -> mp4 -> embed),
 * a mesma usada em single.php, para o diagnóstico bater com o que o
 * visitante realmente vê.
 *
 * @param int $post_id ID do post.
 * @return array
 */
function espanol_video_report( $post_id ) {
	$post_id = (int) $post_id;

	$uuid     = (string) get_post_meta( $post_id, 'video_uuid', true );
	$mp4      = (string) get_post_meta( $post_id, espanol_field( 'url' ), true );
	$embed    = (string) get_post_meta( $post_id, espanol_field( 'embed' ), true );
	$duration = (string) get_post_meta( $post_id, espanol_field( 'duration' ), true );

	// Fonte efetiva, na ordem que o tema usa para reproduzir.
	$source = 'none';
	if ( '' !== $uuid ) {
		$source = 'uuid';
	} elseif ( '' !== $mp4 ) {
		$source = 'mp4';
	} elseif ( '' !== $embed ) {
		$source = 'embed';
	}

	// Host do iframe, útil para achar um provedor inteiro fora do ar.
	$iframe_src  = '';
	$iframe_host = '';
	if ( '' !== $embed && preg_match( '/src\s*=\s*["\']([^"\']+)["\']/i', $embed, $m ) ) {
		$iframe_src  = $m[1];
		$host        = wp_parse_url( $iframe_src, PHP_URL_HOST );
		$iframe_host = $host ? strtolower( $host ) : '';
	}

	// Motivos pelos quais a fonte é considerada problemática.
	$issues = array();

	if ( 'none' === $source ) {
		$issues[] = 'sin_fuente';
	}

	if ( '' !== $embed ) {
		if ( '' === $iframe_src ) {
			$issues[] = 'iframe_sin_src';
		} elseif ( 0 === strpos( $iframe_src, 'http://' ) ) {
			// http puro quebra em site https (mixed content).
			$issues[] = 'iframe_inseguro';
		}

		if ( false === stripos( $embed, '<iframe' ) ) {
			$issues[] = 'embed_sin_iframe';
		}
	}

	if ( '' !== $mp4 && 0 === strpos( $mp4, 'http://' ) ) {
		$issues[] = 'mp4_inseguro';
	}

	if ( ! has_post_thumbnail( $post_id ) ) {
		$issues[] = 'sin_miniatura';
	}

	return array(
		'id'          => $post_id,
		'title'       => get_the_title( $post_id ),
		'permalink'   => get_permalink( $post_id ),
		'post_type'   => get_post_type( $post_id ),
		'status'      => get_post_status( $post_id ),
		'date'        => get_post_time( 'c', true, $post_id ),
		'source'      => $source,
		'has_embed'   => '' !== $embed,
		'has_mp4'     => '' !== $mp4,
		'has_uuid'    => '' !== $uuid,
		'iframe_src'  => $iframe_src,
		'iframe_host' => $iframe_host,
		'embed_raw'   => $embed,
		'mp4_url'     => $mp4,
		'duration'    => $duration,
		'thumbnail'   => get_the_post_thumbnail_url( $post_id, 'medium' ) ?: '',
		'issues'      => $issues,
		'is_broken'   => ! empty( $issues ),
		'edit_link'   => get_edit_post_link( $post_id, 'raw' ),
	);
}

/**
 * GET /espanol/v1/videos
 *
 * @param WP_REST_Request $request Requisição.
 * @return WP_REST_Response
 */
function espanol_rest_videos( $request ) {
	$per_page = (int) $request->get_param( 'per_page' );
	$page     = max( 1, (int) $request->get_param( 'page' ) );
	$status   = (string) $request->get_param( 'status' );
	$type     = (string) $request->get_param( 'type' );

	$post_types = $type ? array( $type ) : espanol_video_types();

	$query = new WP_Query(
		array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$items = array();
	foreach ( $query->posts as $post ) {
		$report = espanol_video_report( $post->ID );

		// Filtro por estado, aplicado depois da análise.
		if ( 'broken' === $status && ! $report['is_broken'] ) {
			continue;
		}
		if ( 'ok' === $status && $report['is_broken'] ) {
			continue;
		}
		if ( 'iframe' === $status && ! $report['has_embed'] ) {
			continue;
		}
		if ( 'empty' === $status && 'none' !== $report['source'] ) {
			continue;
		}

		$items[] = $report;
	}

	return new WP_REST_Response(
		array(
			'items' => $items,
			'meta'  => array(
				'page'        => $page,
				'per_page'    => $per_page,
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
				'returned'    => count( $items ),
				'filter'      => $status,
			),
		),
		200
	);
}

/**
 * GET /espanol/v1/videos/{id}
 *
 * @param WP_REST_Request $request Requisição.
 * @return WP_REST_Response|WP_Error
 */
function espanol_rest_video( $request ) {
	$id = (int) $request->get_param( 'id' );

	if ( ! get_post( $id ) ) {
		return new WP_Error(
			'espanol_rest_not_found',
			__( 'Vídeo no encontrado.', 'espanol' ),
			array( 'status' => 404 )
		);
	}

	return new WP_REST_Response( espanol_video_report( $id ), 200 );
}

/**
 * GET /espanol/v1/videos/stats
 *
 * Resumo por estado e por host de iframe, para a aplicação priorizar
 * o que corrigir primeiro.
 *
 * @return WP_REST_Response
 */
function espanol_rest_stats() {
	$ids = get_posts(
		array(
			'post_type'      => espanol_video_types(),
			'post_status'    => 'publish',
			'posts_per_page' => (int) apply_filters( 'espanol_rest_stats_limit', 2000 ),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	$stats = array(
		'total'      => 0,
		'ok'         => 0,
		'broken'     => 0,
		'by_source'  => array(
			'uuid'  => 0,
			'mp4'   => 0,
			'embed' => 0,
			'none'  => 0,
		),
		'by_issue'   => array(),
		'by_host'    => array(),
	);

	foreach ( $ids as $id ) {
		$report = espanol_video_report( $id );
		++$stats['total'];

		if ( $report['is_broken'] ) {
			++$stats['broken'];
		} else {
			++$stats['ok'];
		}

		++$stats['by_source'][ $report['source'] ];

		foreach ( $report['issues'] as $issue ) {
			if ( ! isset( $stats['by_issue'][ $issue ] ) ) {
				$stats['by_issue'][ $issue ] = 0;
			}
			++$stats['by_issue'][ $issue ];
		}

		if ( '' !== $report['iframe_host'] ) {
			if ( ! isset( $stats['by_host'][ $report['iframe_host'] ] ) ) {
				$stats['by_host'][ $report['iframe_host'] ] = 0;
			}
			++$stats['by_host'][ $report['iframe_host'] ];
		}
	}

	arsort( $stats['by_issue'] );
	arsort( $stats['by_host'] );

	return new WP_REST_Response( $stats, 200 );
}
