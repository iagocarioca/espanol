<?php
/**
 * Meta boxes do vídeo.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra a meta box.
 */
function espanol_add_video_metabox() {
	add_meta_box( 'espanol_video_data', __( 'Dados do vídeo', 'espanol' ), 'espanol_video_metabox_html', espanol_video_types(), 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'espanol_add_video_metabox' );

/**
 * HTML da meta box.
 *
 * @param WP_Post $post Post atual.
 */
function espanol_video_metabox_html( $post ) {
	wp_nonce_field( 'espanol_video_meta', 'espanol_video_meta_nonce' );

	$url      = get_post_meta( $post->ID, espanol_field( 'url' ), true );
	$embed    = get_post_meta( $post->ID, espanol_field( 'embed' ), true );
	$duration = get_post_meta( $post->ID, espanol_field( 'duration' ), true );
	$views    = (int) get_post_meta( $post->ID, espanol_field( 'views' ), true );
	$likes    = (int) get_post_meta( $post->ID, espanol_field( 'likes' ), true );
	$dislikes = (int) get_post_meta( $post->ID, espanol_field( 'dislikes' ), true );
	$is_short = get_post_meta( $post->ID, espanol_field( 'is_short' ), true );
	$is_es    = get_post_meta( $post->ID, espanol_field( 'is_es' ), true );
	?>
	<style>.espanol-mb p{margin:12px 0}.espanol-mb label{display:block;font-weight:600;margin-bottom:4px}.espanol-mb input[type=text],.espanol-mb input[type=number],.espanol-mb textarea{width:100%}</style>
	<?php $video_uuid = get_post_meta( $post->ID, 'video_uuid', true ); ?>
	<div class="espanol-mb">
		<p>
			<label for="espanol_video_uuid"><?php esc_html_e( 'Video UUID (Aurora5 — gera a URL assinada automaticamente)', 'espanol' ); ?></label>
			<input type="text" id="espanol_video_uuid" name="espanol_video_uuid" value="<?php echo esc_attr( $video_uuid ); ?>" placeholder="ex.: 3f9c1a2b-...">
		</p>
		<p>
			<label for="espanol_video_url"><?php esc_html_e( 'OU URL do vídeo (MP4 / M3U8)', 'espanol' ); ?></label>
			<input type="text" id="espanol_video_url" name="espanol_video_url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://cdn.exemplo.com/video.mp4">
		</p>
		<p>
			<label for="espanol_embed"><?php esc_html_e( 'OU código embed (iframe) — tem prioridade sobre a URL', 'espanol' ); ?></label>
			<textarea id="espanol_embed" name="espanol_embed" rows="3" placeholder="&lt;iframe src=&quot;...&quot;&gt;&lt;/iframe&gt;"><?php echo esc_textarea( $embed ); ?></textarea>
		</p>
		<p>
			<label for="espanol_duration"><?php esc_html_e( 'Duração (ex.: 12:51)', 'espanol' ); ?></label>
			<input type="text" id="espanol_duration" name="espanol_duration" value="<?php echo esc_attr( $duration ); ?>" style="max-width:140px">
		</p>
		<p style="display:flex;gap:24px;flex-wrap:wrap">
			<span>
				<label for="espanol_views"><?php esc_html_e( 'Visualizações', 'espanol' ); ?></label>
				<input type="number" id="espanol_views" name="espanol_views" value="<?php echo esc_attr( $views ); ?>" min="0">
			</span>
			<span>
				<label for="espanol_likes"><?php esc_html_e( 'Likes', 'espanol' ); ?></label>
				<input type="number" id="espanol_likes" name="espanol_likes" value="<?php echo esc_attr( $likes ); ?>" min="0">
			</span>
			<span>
				<label for="espanol_dislikes"><?php esc_html_e( 'Dislikes', 'espanol' ); ?></label>
				<input type="number" id="espanol_dislikes" name="espanol_dislikes" value="<?php echo esc_attr( $dislikes ); ?>" min="0">
			</span>
		</p>
		<p>
			<label>
				<input type="checkbox" name="espanol_is_short" value="1" <?php checked( $is_short, '1' ); ?>>
				<?php esc_html_e( 'É um Short (vídeo vertical curto — aparece no carrossel de Shorts)', 'espanol' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input type="checkbox" name="espanol_is_es" value="1" <?php checked( $is_es, '1' ); ?>>
				<?php esc_html_e( 'Vídeo em espanhol (exibe o badge 🌐 ES na thumbnail)', 'espanol' ); ?>
			</label>
		</p>
	</div>
	<?php
}

/**
 * Salva a meta box.
 *
 * @param int $post_id ID do post.
 */
function espanol_save_video_meta( $post_id ) {
	if ( ! isset( $_POST['espanol_video_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['espanol_video_meta_nonce'] ), 'espanol_video_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'video_uuid', isset( $_POST['espanol_video_uuid'] ) ? sanitize_text_field( wp_unslash( $_POST['espanol_video_uuid'] ) ) : '' );
	update_post_meta( $post_id, espanol_field( 'url' ), isset( $_POST['espanol_video_url'] ) ? esc_url_raw( wp_unslash( $_POST['espanol_video_url'] ) ) : '' );

	$embed = isset( $_POST['espanol_embed'] ) ? wp_unslash( $_POST['espanol_embed'] ) : '';
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		$embed = wp_kses_post( $embed );
	}
	update_post_meta( $post_id, espanol_field( 'embed' ), $embed );

	update_post_meta( $post_id, espanol_field( 'duration' ), isset( $_POST['espanol_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['espanol_duration'] ) ) : '' );
	update_post_meta( $post_id, espanol_field( 'views' ), isset( $_POST['espanol_views'] ) ? absint( $_POST['espanol_views'] ) : 0 );
	update_post_meta( $post_id, espanol_field( 'likes' ), isset( $_POST['espanol_likes'] ) ? absint( $_POST['espanol_likes'] ) : 0 );
	update_post_meta( $post_id, espanol_field( 'dislikes' ), isset( $_POST['espanol_dislikes'] ) ? absint( $_POST['espanol_dislikes'] ) : 0 );
	update_post_meta( $post_id, espanol_field( 'is_short' ), isset( $_POST['espanol_is_short'] ) ? '1' : '' );
	update_post_meta( $post_id, espanol_field( 'is_es' ), isset( $_POST['espanol_is_es'] ) ? '1' : '' );
}
foreach ( espanol_video_types() as $espanol_save_type ) {
	add_action( "save_post_{$espanol_save_type}", 'espanol_save_video_meta' );
}
unset( $espanol_save_type );
