<?php
/**
 * SEO: canonical, meta description e breadcrumbs.
 *
 * Complementa inc/seo-schema.php (VideoObject/Open Graph) cobrindo os itens
 * P0 do PRD de otimização que faltavam no tema.
 *
 * @package Espanol
 */

defined( 'ABSPATH' ) || exit;

/**
 * Atributo sizes das thumbnails da grade.
 *
 * Sem sizes o navegador assume 100vw e baixa a maior variante do srcset, o que
 * anula o ganho de ter variantes. Os valores espelham os breakpoints da grade
 * em style.css: 5-6 colunas por padrão, 4 até 1200px, 3 até 900px, 2 até 600px.
 *
 * @param string $sizes Valor calculado pelo core.
 * @param array  $size  Dimensões da imagem.
 * @return string Valor ajustado.
 */
function espanol_thumb_sizes( $sizes, $size ) {
	// Só as thumbs da grade (480x270 e variantes 16:9 menores).
	if ( ! is_array( $size ) || empty( $size[0] ) || (int) $size[0] > 480 ) {
		return $sizes;
	}

	return '(max-width: 600px) 50vw, (max-width: 900px) 33vw, (max-width: 1200px) 25vw, 260px';
}
add_filter( 'wp_calculate_image_sizes', 'espanol_thumb_sizes', 10, 2 );

/**
 * Atributos width/height de uma imagem da biblioteca, a partir da URL.
 *
 * Sem dimensões o navegador não reserva espaço antes do download e o layout
 * salta quando a imagem chega (CLS). O logo é o caso mais visível, porque fica
 * no topo e é uma das primeiras coisas a carregar.
 *
 * @param string $url URL da imagem.
 * @return string ' width="X" height="Y"' ou vazio se não for possível descobrir.
 */
function espanol_img_dimensions( $url ) {
	if ( ! $url ) {
		return '';
	}

	$id = attachment_url_to_postid( $url );
	if ( ! $id ) {
		return '';
	}

	$meta = wp_get_attachment_metadata( $id );
	if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) {
		return '';
	}

	return ' width="' . (int) $meta['width'] . '" height="' . (int) $meta['height'] . '"';
}

/**
 * Idioma declarado no <html>.
 *
 * O WordPress deriva o atributo lang do locale do site, que está em pt-BR
 * enquanto todo o conteúdo é espanhol. Declarar o idioma errado faz o Google
 * associar as páginas ao público errado, então o tema sobrescreve apenas o
 * atributo — sem tocar no locale do admin nem na tradução da interface.
 *
 * @param string $output Atributos já montados pelo core (lang="…" etc).
 * @return string Atributos com o lang corrigido.
 */
function espanol_html_lang( $output ) {
	$lang = apply_filters( 'espanol_html_lang_value', 'es' );

	if ( preg_match( '/lang="[^"]*"/', $output ) ) {
		return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $lang ) . '"', $output, 1 );
	}

	// Sem atributo lang (locale vazio): acrescenta.
	return trim( $output . ' lang="' . esc_attr( $lang ) . '"' );
}
add_filter( 'language_attributes', 'espanol_html_lang' );

/**
 * Canonical em arquivos e busca.
 *
 * O core do WordPress só emite canonical em is_singular(), então arquivos,
 * taxonomias e paginação ficavam sem. A canonical de cada página aponta para
 * ela mesma (inclusive /page/N/), nunca para a home: apontar tudo para a raiz
 * faria o Google descartar as páginas internas.
 */
function espanol_canonical_archives() {
	if ( is_singular() || is_404() ) {
		return; // Singular já é coberto por rel_canonical() do core.
	}

	$url = '';

	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_home() ) {
		$url = get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$url = get_term_link( $term );
		}
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_post_type() );
	} elseif ( is_author() ) {
		$url = get_author_posts_url( (int) get_query_var( 'author' ) );
	} elseif ( is_search() ) {
		// Busca é noindex; a canonical evita variações de ?s= competindo entre si.
		$url = get_search_link();
	}

	if ( ! $url || is_wp_error( $url ) ) {
		return;
	}

	// Preserva a página atual da paginação: /page/2/ não é duplicata da /page/1/.
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$url = user_trailingslashit( trailingslashit( $url ) . 'page/' . $paged );
	}

	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
}
add_action( 'wp_head', 'espanol_canonical_archives', 4 );

/**
 * Busca interna não deve ser indexada.
 *
 * Cada consulta gera uma URL distinta com conteúdo montado a partir de outras
 * páginas — é o padrão de URL infinita que consome crawl budget sem retorno.
 */
function espanol_noindex_search() {
	if ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
	}
}
add_action( 'wp_head', 'espanol_noindex_search', 3 );

/**
 * Texto base para a meta description da página atual.
 *
 * @return string Descrição sem HTML, ou vazio quando não houver fonte boa.
 */
function espanol_meta_description_text() {
	if ( is_singular() ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		$text = get_the_excerpt( $post_id );

		if ( ! $text ) {
			$post = get_post( $post_id );
			$text = $post ? $post->post_content : '';
		}

		return wp_strip_all_tags( strip_shortcodes( (string) $text ) );
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$desc = term_description( $term );
			if ( $desc ) {
				return wp_strip_all_tags( $desc );
			}

			/* translators: %s: nome da categoria. */
			return sprintf( __( 'Videos de %s en español. Los mejores videos porno gratis en HD.', 'espanol' ), $term->name );
		}
	}

	if ( is_front_page() || is_home() ) {
		$tagline = get_bloginfo( 'description' );
		return $tagline ? wp_strip_all_tags( $tagline ) : '';
	}

	return '';
}

/**
 * Meta description gerada a partir do conteúdo real.
 *
 * O PRD pede descrição única por página, sem HTML e sem string fixa.
 */
function espanol_meta_description() {
	if ( is_search() || is_404() ) {
		return; // Páginas noindex não precisam.
	}

	$text = espanol_meta_description_text();
	if ( ! $text ) {
		return;
	}

	// Normaliza espaços antes de cortar, senão a contagem sai errada.
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
	if ( '' === $text ) {
		return;
	}

	// ~160 caracteres, cortando na palavra para não truncar no meio.
	if ( mb_strlen( $text ) > 160 ) {
		$text = mb_substr( $text, 0, 160 );
		$cut  = mb_strrpos( $text, ' ' );
		if ( $cut && $cut > 100 ) {
			$text = mb_substr( $text, 0, $cut );
		}
		$text = rtrim( $text, " ,.;:-" ) . '…';
	}

	echo '<meta name="description" content="' . esc_attr( $text ) . '">' . "\n";
}
add_action( 'wp_head', 'espanol_meta_description', 4 );

/**
 * Itens do breadcrumb da página atual.
 *
 * @return array Lista de array( 'name' => string, 'url' => string ).
 */
function espanol_breadcrumb_items() {
	$items = array(
		array(
			'name' => __( 'Inicio', 'espanol' ),
			'url'  => home_url( '/' ),
		),
	);

	if ( is_singular( espanol_video_types() ) ) {
		$post_id = get_the_ID();
		$terms   = get_the_terms( $post_id, espanol_tax( 'category' ) );

		if ( $terms && ! is_wp_error( $terms ) ) {
			$term = $terms[0];
			$link = get_term_link( $term );

			if ( ! is_wp_error( $link ) ) {
				$items[] = array(
					'name' => $term->name,
					'url'  => $link,
				);
			}
		}

		$items[] = array(
			'name' => wp_strip_all_tags( get_the_title( $post_id ) ),
			'url'  => get_permalink( $post_id ),
		);

		return $items;
	}

	if ( is_singular() ) {
		$post_id = get_the_ID();
		$items[] = array(
			'name' => wp_strip_all_tags( get_the_title( $post_id ) ),
			'url'  => get_permalink( $post_id ),
		);

		return $items;
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$items[] = array(
					'name' => $term->name,
					'url'  => $link,
				);
			}
		}

		return $items;
	}

	return $items;
}

/**
 * Breadcrumbs visíveis, em links HTML reais.
 *
 * Rastreáveis sem JS, como pede o PRD. O JSON-LD correspondente é emitido por
 * espanol_breadcrumb_schema() a partir da mesma fonte, garantindo consistência
 * entre a navegação visível e o structured data.
 */
function espanol_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = espanol_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Migas de pan', 'espanol' ) . '">';

	$last = count( $items ) - 1;
	foreach ( $items as $i => $item ) {
		if ( $i === $last ) {
			// O item atual não vira link: apontaria para a própria página.
			echo '<span class="breadcrumb-current">' . esc_html( $item['name'] ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>';
			echo '<span class="breadcrumb-sep" aria-hidden="true">/</span>';
		}
	}

	echo '</nav>' . "\n";
}

/**
 * JSON-LD BreadcrumbList, espelhando o breadcrumb visível.
 */
function espanol_breadcrumb_schema() {
	if ( is_front_page() ) {
		return;
	}

	$items = espanol_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}

	$list = array();
	foreach ( $items as $i => $item ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		);
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);

	echo '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>' . "\n";
}
add_action( 'wp_head', 'espanol_breadcrumb_schema', 7 );

/**
 * decoding="async" nas thumbnails.
 *
 * Complementa o loading="lazy" que os templates já aplicam, tirando a
 * decodificação da imagem do caminho de renderização.
 *
 * @param array $attr Atributos do <img>.
 * @return array Atributos ajustados.
 */
function espanol_img_decoding_async( $attr ) {
	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'espanol_img_decoding_async', 11 );
