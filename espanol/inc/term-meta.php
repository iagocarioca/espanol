<?php
/**
 * Imagem para termos (categorias e canais).
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$espanol_image_taxonomies = array( 'category', 'channel', 'pornstar' );

foreach ( $espanol_image_taxonomies as $tax ) {
	add_action( "{$tax}_add_form_fields", 'espanol_term_image_add_field' );
	add_action( "{$tax}_edit_form_fields", 'espanol_term_image_edit_field' );
	add_action( "created_{$tax}", 'espanol_term_image_save' );
	add_action( "edited_{$tax}", 'espanol_term_image_save' );
}

/**
 * Campo na tela de criação de termo.
 */
function espanol_term_image_add_field() {
	wp_enqueue_media();
	wp_enqueue_script( 'espanol-admin', ESPANOL_URI . '/js/admin.js', array( 'jquery' ), ESPANOL_VERSION, true );
	?>
	<div class="form-field">
		<label><?php esc_html_e( 'Imagem', 'espanol' ); ?></label>
		<input type="hidden" name="espanol_term_image" class="espanol-media-value" value="">
		<img src="" class="espanol-media-preview" style="max-width:120px;display:none;margin-bottom:6px">
		<button type="button" class="button espanol-media-upload"><?php esc_html_e( 'Escolher imagem', 'espanol' ); ?></button>
		<button type="button" class="button espanol-media-remove" style="display:none"><?php esc_html_e( 'Remover', 'espanol' ); ?></button>
	</div>
	<?php
}

/**
 * Campo na tela de edição de termo.
 *
 * @param WP_Term $term Termo.
 */
function espanol_term_image_edit_field( $term ) {
	wp_enqueue_media();
	wp_enqueue_script( 'espanol-admin', ESPANOL_URI . '/js/admin.js', array( 'jquery' ), ESPANOL_VERSION, true );
	$image = get_term_meta( $term->term_id, 'espanol_term_image', true );
	?>
	<tr class="form-field">
		<th scope="row"><label><?php esc_html_e( 'Imagem', 'espanol' ); ?></label></th>
		<td>
			<input type="hidden" name="espanol_term_image" class="espanol-media-value" value="<?php echo esc_url( $image ); ?>">
			<img src="<?php echo esc_url( $image ); ?>" class="espanol-media-preview" style="max-width:120px;<?php echo $image ? '' : 'display:none;'; ?>margin-bottom:6px">
			<p>
				<button type="button" class="button espanol-media-upload"><?php esc_html_e( 'Escolher imagem', 'espanol' ); ?></button>
				<button type="button" class="button espanol-media-remove" <?php echo $image ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remover', 'espanol' ); ?></button>
			</p>
		</td>
	</tr>
	<?php
}

/**
 * Salva a imagem do termo.
 *
 * @param int $term_id ID do termo.
 */
function espanol_term_image_save( $term_id ) {
	if ( isset( $_POST['espanol_term_image'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- salvo junto ao nonce nativo da tela de termos.
		update_term_meta( $term_id, 'espanol_term_image', esc_url_raw( wp_unslash( $_POST['espanol_term_image'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}
}

/**
 * Retorna a URL da imagem do termo (ou de um vídeo do termo como fallback).
 *
 * @param WP_Term $term Termo.
 * @return string
 */
function espanol_get_term_image( $term ) {
	$image = get_term_meta( $term->term_id, 'espanol_term_image', true );
	if ( $image ) {
		return $image;
	}

	// Fallback: thumbnail do vídeo mais recente do termo.
	$videos = get_posts(
		array(
			'post_type'      => espanol_video_types(),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => $term->taxonomy,
					'terms'    => $term->term_id,
				),
			),
		)
	);
	if ( $videos ) {
		$thumb = get_the_post_thumbnail_url( $videos[0], 'espanol-thumb' );
		if ( $thumb ) {
			return $thumb;
		}
	}
	return '';
}
