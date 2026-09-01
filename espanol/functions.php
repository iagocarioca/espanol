<?php
/**
 * Espanol — funções do tema.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ESPANOL_VERSION', '1.3.9' );
define( 'ESPANOL_DIR', get_template_directory() );
define( 'ESPANOL_URI', get_template_directory_uri() );

/**
 * Mapa de campos de vídeo — compatível com o tema-a99.
 * O tema lê/grava nas MESMAS meta keys do tema antigo, então os posts
 * existentes (views, likes, dislike, votos, embed) continuam funcionando.
 *
 * @param string $key Chave lógica.
 * @return string Meta key real.
 */
function espanol_field( $key ) {
	$map = apply_filters(
		'espanol_field_map',
		array(
			'views'    => 'views',            // visualizações (int)
			'likes'    => 'likes',            // likes (int)
			'dislikes' => 'dislike',          // dislikes (int — singular, como no tema-a99)
			'percent'  => 'votos',            // percentual de likes 0–100
			'embed'    => 'embed',            // código embed/iframe
			'url'      => 'mp4',              // URL do arquivo mp4
			'duration' => 'tempo',            // duração (ex.: 12:51)
			'is_short' => '_espanol_is_short',
			'is_es'    => '_espanol_is_es',
		)
	);
	return isset( $map[ $key ] ) ? $map[ $key ] : $key;
}

/**
 * Post types de vídeo — compatível com o tema-a99 (post + custom_post).
 *
 * @return array
 */
function espanol_video_types() {
	return apply_filters( 'espanol_video_types', array( 'post', 'custom_post' ) );
}

/**
 * Mapa de taxonomias — compatível com o tema-a99, que usa as
 * taxonomias nativas category e post_tag nos vídeos.
 *
 * @param string $key Chave lógica (category, tag, pornstar, channel).
 * @return string Taxonomia real.
 */
function espanol_tax( $key ) {
	$map = apply_filters(
		'espanol_tax_map',
		array(
			'category' => 'category',
			'tag'      => 'post_tag',
			'pornstar' => 'pornstar',
			'channel'  => 'channel',
		)
	);
	return isset( $map[ $key ] ) ? $map[ $key ] : $key;
}

require ESPANOL_DIR . '/inc/post-types.php';
require ESPANOL_DIR . '/inc/meta-boxes.php';
require ESPANOL_DIR . '/inc/term-meta.php';
require ESPANOL_DIR . '/inc/theme-options.php';
require ESPANOL_DIR . '/inc/template-tags.php';
require ESPANOL_DIR . '/inc/ajax.php';
require ESPANOL_DIR . '/inc/rest-videos.php';

/**
 * Setup.
 */
function espanol_setup() {
	load_theme_textdomain( 'espanol', ESPANOL_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'automatic-feed-links' );

	add_image_size( 'espanol-thumb', 480, 270, true );
	add_image_size( 'espanol-short', 360, 640, true );

	register_nav_menus(
		array(
			'pills'  => __( 'Menu de pílulas (topo)', 'espanol' ),
			'mobile' => __( 'Menu lateral (off-canvas)', 'espanol' ),
			'footer' => __( 'Links do rodapé', 'espanol' ),
		)
	);
}
add_action( 'after_setup_theme', 'espanol_setup' );

/**
 * Scripts e estilos.
 */
function espanol_scripts() {
	wp_enqueue_style( 'espanol-style', get_stylesheet_uri(), array(), ESPANOL_VERSION );
	wp_enqueue_script( 'espanol-main', ESPANOL_URI . '/js/main.js', array(), ESPANOL_VERSION, true );

	wp_localize_script(
		'espanol-main',
		'espanolData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'espanol_ajax' ),
		)
	);

	// Google Identity Services (login com Google no modal).
	if ( ! is_user_logged_in() && espanol_get_option( 'google_client_id' ) ) {
		wp_enqueue_script( 'espanol-gsi', 'https://accounts.google.com/gsi/client', array(), null, array( 'strategy' => 'async' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
}
add_action( 'wp_enqueue_scripts', 'espanol_scripts' );

/**
 * Cor de destaque + favicon + código do header.
 */
function espanol_head_output() {
	$accent = espanol_get_option( 'accent_color', '#fdc500' );
	if ( $accent ) {
		echo '<style id="espanol-accent">:root{--accent:' . esc_attr( $accent ) . ';}</style>' . "\n";
	}

	$favicon = espanol_get_option( 'favicon' );
	if ( $favicon ) {
		echo '<link rel="icon" href="' . esc_url( $favicon ) . '">' . "\n";
	}

	// Imagem de fundo do topo (header até o meio), fundida no fundo escuro.
	$site_bg = espanol_get_option( 'site_bg' );
	if ( $site_bg ) {
		$bg_height = (int) espanol_get_option( 'site_bg_height', 700 );
		echo '<style id="espanol-site-bg">'
			. 'body{position:relative;}'
			. 'body::before{content:"";position:absolute;top:0;left:0;right:0;height:' . $bg_height . 'px;z-index:-1;pointer-events:none;'
			. 'background:linear-gradient(to bottom,rgba(10,10,10,.55) 0%,rgba(10,10,10,.75) 55%,var(--bg) 100%),'
			. 'url(' . esc_url( $site_bg ) . ') center top/cover no-repeat;}'
			. '</style>' . "\n";
	}

	$header_code = espanol_get_option( 'header_code' );
	if ( $header_code ) {
		echo $header_code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- código livre do administrador.
	}
}
add_action( 'wp_head', 'espanol_head_output', 20 );

/**
 * Código do footer.
 */
function espanol_footer_output() {
	$footer_code = espanol_get_option( 'footer_code' );
	if ( $footer_code ) {
		echo $footer_code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- código livre do administrador.
	}
}
add_action( 'wp_footer', 'espanol_footer_output', 99 );

/**
 * Classe do esquema de cores no body.
 */
function espanol_body_class( $classes ) {
	if ( 'light' === espanol_get_option( 'color_scheme', 'dark' ) ) {
		$classes[] = 'scheme-light';
	}
	return $classes;
}
add_filter( 'body_class', 'espanol_body_class' );

/**
 * Queries principais: home e busca listam vídeos.
 */
function espanol_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() ) {
		$query->set( 'post_type', espanol_video_types() );
		$query->set( 'posts_per_page', (int) espanol_get_option( 'home_latest_count', 20 ) );
	}

	if ( $query->is_search() ) {
		$search_types = espanol_video_types();
		if ( post_type_exists( 'reelix_short' ) ) {
			$search_types[] = 'reelix_short';
		}
		$query->set( 'post_type', $search_types );
		$query->set( 'posts_per_page', 20 );
	}

	if ( $query->is_tax( array( espanol_tax( 'category' ), espanol_tax( 'tag' ), 'pornstar', 'channel' ) ) || $query->is_category() || $query->is_tag() || $query->is_post_type_archive( 'custom_post' ) ) {
		$query->set( 'posts_per_page', 20 );

		// Ordenação por mais vistos via ?orderby=views.
		if ( isset( $_GET['orderby'] ) && 'views' === sanitize_key( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$query->set( 'meta_key', espanol_field( 'views' ) );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
		}
	}
}
add_action( 'pre_get_posts', 'espanol_pre_get_posts' );

/**
 * Cria as páginas utilitárias na ativação do tema.
 */
function espanol_create_pages() {
	$pages = array(
		'categorias' => array( 'title' => 'Categorias', 'template' => 'templates/page-categories.php' ),
		'shorts'     => array( 'title' => 'Shorts', 'template' => 'templates/page-shorts.php' ),
		'favoritos'  => array( 'title' => 'Favoritos', 'template' => 'templates/page-favorites.php' ),
		'pornstars'  => array( 'title' => 'Pornstars', 'template' => 'templates/page-pornstars.php' ),
		'tags'       => array( 'title' => 'Tags', 'template' => 'templates/page-tags.php' ),
		'mi-cuenta'  => array( 'title' => 'Mi Cuenta', 'template' => 'templates/page-account.php' ),
	);

	foreach ( $pages as $slug => $data ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$page_id = wp_insert_post(
			array(
				'post_title'  => $data['title'],
				'post_name'   => $slug,
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $data['template'] );
		}
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'espanol_create_pages' );

if ( ! function_exists( 'aurora5GetVideoUrl' ) ) {
	/**
	 * Converte um UUID do Aurora5 em URL MP4 assinada — mesma conversão do tema-a99.
	 * Se um plugin/mu-plugin já definir a função (caso da produção), a dele prevalece.
	 *
	 * @param string $uuid UUID do vídeo.
	 * @return string URL assinada ou vazio.
	 */
	function aurora5GetVideoUrl( $uuid ) {
		$uuid = trim( (string) $uuid );
		if ( '' === $uuid ) {
			return '';
		}

		// Secret: Opções do Tema -> opção do Reelix -> filtro.
		$secret = espanol_get_option( 'aurora5_secret' );
		if ( ! $secret ) {
			$reelix = get_option( 'reelix_settings' );
			$secret = ( is_array( $reelix ) && ! empty( $reelix['aurora5_secret'] ) ) ? $reelix['aurora5_secret'] : '';
		}
		$secret = apply_filters( 'espanol_aurora5_secret', $secret );
		if ( '' === (string) $secret ) {
			return '';
		}

		// CDN do próprio domínio (cdn.dominio.com), como no plugin aurora5 original.
		$parsed = wp_parse_url( get_site_url() );
		$host   = isset( $parsed['host'] ) ? $parsed['host'] : ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' );
		$host   = preg_replace( '/^www\./i', '', $host );

		$base = apply_filters( 'espanol_aurora5_base', 'https://cdn.' . $host . '/secure-video/' );
		$ttl  = (int) apply_filters( 'espanol_aurora5_ttl', 300 );
		$exp  = time() + max( 60, $ttl );
		$sig  = hash_hmac( 'sha256', $uuid . $exp, $secret );

		return rtrim( $base, '/' ) . '/' . $uuid . '?sig=' . $sig . ':' . $exp;
	}
}

/**
 * Fonte do vídeo de um post, na mesma ordem do tema-a99:
 * video_uuid (URL assinada Aurora5) -> mp4 -> vazio.
 *
 * @param int $post_id ID do post.
 * @return string URL do MP4 ou vazio.
 */
function espanol_video_src( $post_id ) {
	$uuid = get_post_meta( $post_id, 'video_uuid', true );
	$src  = $uuid ? aurora5GetVideoUrl( $uuid ) : '';
	if ( ! $src ) {
		$src = (string) get_post_meta( $post_id, espanol_field( 'url' ), true );
	}
	return apply_filters( 'espanol_video_src', $src, $post_id );
}

/**
 * Integração com o plugin Reelix (shorts).
 * O CPT reelix_short passa a usar as taxonomias do tema.
 */
function espanol_reelix_taxonomies( $taxes ) {
	return array( espanol_tax( 'category' ), espanol_tax( 'tag' ), 'pornstar', 'channel' );
}
add_filter( 'reelix_reused_taxonomies', 'espanol_reelix_taxonomies' );

/**
 * O Reelix está ativo?
 */
function espanol_is_reelix_active() {
	return post_type_exists( 'reelix_short' );
}

/**
 * URL da seção de shorts: arquivo /shorts/ do Reelix quando ativo,
 * senão a página de shorts do próprio tema.
 */
function espanol_shorts_url() {
	if ( espanol_is_reelix_active() ) {
		return get_post_type_archive_link( 'reelix_short' );
	}
	return espanol_page_url_by_template( 'templates/page-shorts.php' );
}

/**
 * URL de uma página pelo template (para os links do menu).
 */
function espanol_page_url_by_template( $template ) {
	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_page_template',
			'meta_value'     => $template,
		)
	);
	return $pages ? get_permalink( $pages[0] ) : '#';
}

/**
 * Traduz para espanhol as strings do plugin Reelix, que vem em portugues.
 *
 * Feito por filtro no tema (e nao editando o plugin) para sobreviver as
 * atualizacoes dele. Só atua no text domain "reelix".
 *
 * @param string $traduzido Texto ja traduzido pelo WordPress.
 * @param string $original  Texto original do plugin.
 * @param string $dominio   Text domain da string.
 * @return string
 */
function espanol_reelix_es( $traduzido, $original, $dominio ) {
	if ( 'reelix' !== $dominio ) {
		return $traduzido;
	}

	$mapa = array(
		'Recentes'                    => 'Recientes',
		'Mais vistos'                 => 'Más vistos',
		'Mais curtidos'               => 'Más gustados',
		'Aleatório'                   => 'Aleatorio',
		'Explorar'                    => 'Explorar',
		'Ordenar'                     => 'Ordenar',
		'Curtir'                      => 'Me gusta',
		'Você chegou ao fim.'         => 'Has llegado al final.',
		'Nenhum short por aqui ainda.' => 'Aún no hay shorts publicados.',
	);

	return isset( $mapa[ $original ] ) ? $mapa[ $original ] : $traduzido;
}
add_filter( 'gettext', 'espanol_reelix_es', 10, 3 );

/**
 * Mesma traducao para strings com contexto (assinatura de 4 argumentos).
 *
 * @param string $traduzido Texto ja traduzido.
 * @param string $original  Texto original.
 * @param string $contexto  Contexto da string.
 * @param string $dominio   Text domain.
 * @return string
 */
function espanol_reelix_es_ctx( $traduzido, $original, $contexto, $dominio ) {
	return espanol_reelix_es( $traduzido, $original, $dominio );
}
add_filter( 'gettext_with_context', 'espanol_reelix_es_ctx', 10, 4 );

/**
 * Forca o navegador a trocar http:// por https:// em qualquer recurso
 * carregado na pagina (imagens, iframes, scripts), mesmo os que vem de
 * fora do controle do tema — como o poster de um iframe de terceiros
 * (ex.: plugins de player que embutem CDN externo em http).
 *
 * upgrade-insecure-requests e suportado por todos os navegadores atuais
 * e nao quebra nada: se o recurso tambem existir em https (caso comum
 * de CDNs), ele so passa a carregar pela versao segura.
 */
function espanol_upgrade_insecure_requests() {
	echo '<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">' . "\n";
}
add_action( 'wp_head', 'espanol_upgrade_insecure_requests', 1 );
