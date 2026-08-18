<?php
/**
 * Painel de opções do tema Espanol.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna uma opção do tema.
 *
 * @param string $key     Chave.
 * @param mixed  $default Valor padrão.
 * @return mixed
 */
function espanol_get_option( $key, $default = '' ) {
	$options = get_option( 'espanol_options', array() );
	return isset( $options[ $key ] ) && '' !== $options[ $key ] ? $options[ $key ] : $default;
}

/**
 * Registra a página no admin.
 */
function espanol_options_menu() {
	add_menu_page(
		__( 'Opções do Tema', 'espanol' ),
		__( 'Opções do Tema', 'espanol' ),
		'manage_options',
		'espanol-options',
		'espanol_options_page_html',
		'dashicons-admin-customizer',
		59
	);
}
add_action( 'admin_menu', 'espanol_options_menu' );

/**
 * Registra a setting.
 */
function espanol_register_settings() {
	register_setting( 'espanol_options_group', 'espanol_options', 'espanol_sanitize_options' );
}
add_action( 'admin_init', 'espanol_register_settings' );

/**
 * Assets do admin.
 *
 * @param string $hook Página atual.
 */
function espanol_options_assets( $hook ) {
	if ( 'toplevel_page_espanol-options' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'espanol-admin', ESPANOL_URI . '/js/admin.js', array( 'jquery', 'wp-color-picker' ), ESPANOL_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'espanol_options_assets' );

/**
 * Sanitização.
 *
 * @param array $input Entrada crua.
 * @return array
 */
function espanol_sanitize_options( $input ) {
	$out = array();

	$out['logo']    = isset( $input['logo'] ) ? esc_url_raw( $input['logo'] ) : '';
	$out['favicon'] = isset( $input['favicon'] ) ? esc_url_raw( $input['favicon'] ) : '';

	$out['site_bg']        = isset( $input['site_bg'] ) ? esc_url_raw( $input['site_bg'] ) : '';
	$out['site_bg_height'] = isset( $input['site_bg_height'] ) ? max( 200, min( 2000, absint( $input['site_bg_height'] ) ) ) : 700;

	$accent               = isset( $input['accent_color'] ) ? sanitize_hex_color( $input['accent_color'] ) : '';
	$out['accent_color']  = $accent ? $accent : '#fdc500';

	$out['color_scheme'] = ( isset( $input['color_scheme'] ) && 'light' === $input['color_scheme'] ) ? 'light' : 'dark';

	$out['google_client_id'] = isset( $input['google_client_id'] ) ? sanitize_text_field( $input['google_client_id'] ) : '';
	$out['aurora5_secret']   = isset( $input['aurora5_secret'] ) ? sanitize_text_field( $input['aurora5_secret'] ) : '';

	$out['stats_text'] = isset( $input['stats_text'] ) ? wp_kses( $input['stats_text'], array( 'span' => array( 'class' => array() ), 'strong' => array(), 'b' => array() ) ) : '';

	$out['cta_show'] = ! empty( $input['cta_show'] ) ? 1 : 0;
	$out['cta_text'] = isset( $input['cta_text'] ) ? sanitize_text_field( $input['cta_text'] ) : '';
	$out['cta_url']  = isset( $input['cta_url'] ) ? esc_url_raw( $input['cta_url'] ) : '';

	// Códigos livres: só administradores com unfiltered_html mantêm scripts.
	foreach ( array( 'header_code', 'footer_code' ) as $code_key ) {
		$code = isset( $input[ $code_key ] ) ? $input[ $code_key ] : '';
		if ( ! current_user_can( 'unfiltered_html' ) ) {
			$code = wp_kses_post( $code );
		}
		$out[ $code_key ] = $code;
	}

	$out['footer_title'] = isset( $input['footer_title'] ) ? sanitize_text_field( $input['footer_title'] ) : '';
	$out['footer_desc']  = isset( $input['footer_desc'] ) ? wp_kses_post( $input['footer_desc'] ) : '';

	$out['social_telegram'] = isset( $input['social_telegram'] ) ? esc_url_raw( $input['social_telegram'] ) : '';
	$out['social_twitter']  = isset( $input['social_twitter'] ) ? esc_url_raw( $input['social_twitter'] ) : '';
	$out['social_youtube']  = isset( $input['social_youtube'] ) ? esc_url_raw( $input['social_youtube'] ) : '';
	$out['contact_url']     = isset( $input['contact_url'] ) ? sanitize_text_field( $input['contact_url'] ) : '';

	$out['top_cats']    = isset( $input['top_cats'] ) && is_array( $input['top_cats'] ) ? array_map( 'absint', $input['top_cats'] ) : array();
	$out['footer_cats'] = isset( $input['footer_cats'] ) && is_array( $input['footer_cats'] ) ? array_map( 'absint', $input['footer_cats'] ) : array();

	// Blocos da home.
	$out['home_latest_count'] = isset( $input['home_latest_count'] ) ? max( 5, min( 60, absint( $input['home_latest_count'] ) ) ) : 20;
	$out['home_viewed_show']  = ! empty( $input['home_viewed_show'] ) ? 1 : 0;
	$out['home_viewed_title'] = isset( $input['home_viewed_title'] ) ? sanitize_text_field( $input['home_viewed_title'] ) : '';
	$out['home_viewed_count'] = isset( $input['home_viewed_count'] ) ? max( 5, min( 40, absint( $input['home_viewed_count'] ) ) ) : 10;
	$out['home_voted_show']   = ! empty( $input['home_voted_show'] ) ? 1 : 0;
	$out['home_voted_title']  = isset( $input['home_voted_title'] ) ? sanitize_text_field( $input['home_voted_title'] ) : '';
	$out['home_voted_count']  = isset( $input['home_voted_count'] ) ? max( 5, min( 40, absint( $input['home_voted_count'] ) ) ) : 10;

	return $out;
}

/**
 * Campo de upload de mídia.
 *
 * @param string $key   Chave da opção.
 * @param string $label Rótulo.
 */
function espanol_media_field( $key, $label ) {
	$value = espanol_get_option( $key );
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<input type="hidden" name="espanol_options[<?php echo esc_attr( $key ); ?>]" class="espanol-media-value" value="<?php echo esc_url( $value ); ?>">
			<img src="<?php echo esc_url( $value ); ?>" class="espanol-media-preview" style="max-height:60px;background:#222;padding:6px;border-radius:6px;<?php echo $value ? '' : 'display:none;'; ?>margin-bottom:8px">
			<p>
				<button type="button" class="button espanol-media-upload"><?php esc_html_e( 'Escolher imagem', 'espanol' ); ?></button>
				<button type="button" class="button espanol-media-remove" <?php echo $value ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Remover', 'espanol' ); ?></button>
			</p>
		</td>
	</tr>
	<?php
}

/**
 * Lista de checkboxes de categorias de vídeo.
 *
 * @param string $key      Chave da opção.
 * @param array  $selected IDs selecionados.
 */
function espanol_cats_checklist( $key, $selected ) {
	$terms = get_terms(
		array(
			'taxonomy'   => espanol_tax( 'category' ),
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		echo '<p>' . esc_html__( 'Nenhuma categoria de vídeo cadastrada ainda.', 'espanol' ) . '</p>';
		return;
	}
	echo '<div style="max-height:220px;overflow:auto;border:1px solid #ccd0d4;padding:10px;background:#fff;columns:3;">';
	foreach ( $terms as $term ) {
		printf(
			'<label style="display:block;margin-bottom:4px"><input type="checkbox" name="espanol_options[%1$s][]" value="%2$d" %3$s> %4$s</label>',
			esc_attr( $key ),
			(int) $term->term_id,
			checked( in_array( $term->term_id, (array) $selected, true ), true, false ),
			esc_html( $term->name )
		);
	}
	echo '</div>';
}

/**
 * HTML da página de opções.
 */
function espanol_options_page_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Espanol — Opções do Tema', 'espanol' ); ?></h1>

		<form method="post" action="options.php">
			<?php settings_fields( 'espanol_options_group' ); ?>

			<h2 class="title"><?php esc_html_e( 'Geral', 'espanol' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php espanol_media_field( 'logo', __( 'Logotipo', 'espanol' ) ); ?>
				<?php espanol_media_field( 'favicon', __( 'Favicon', 'espanol' ) ); ?>
				<?php espanol_media_field( 'site_bg', __( 'Imagem de fundo do topo', 'espanol' ) ); ?>
				<tr>
					<th scope="row"><?php esc_html_e( 'Altura do fundo do topo (px)', 'espanol' ); ?></th>
					<td>
						<input type="number" name="espanol_options[site_bg_height]" value="<?php echo esc_attr( espanol_get_option( 'site_bg_height', 700 ) ); ?>" min="200" max="2000" step="10">
						<p class="description"><?php esc_html_e( 'Até onde a imagem desce na página (do header até o meio). Ela se funde suavemente com o fundo escuro.', 'espanol' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Cor de destaque do site', 'espanol' ); ?></th>
					<td><input type="text" class="espanol-color" name="espanol_options[accent_color]" value="<?php echo esc_attr( espanol_get_option( 'accent_color', '#fdc500' ) ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Esquema de cores', 'espanol' ); ?></th>
					<td>
						<label style="margin-right:18px"><input type="radio" name="espanol_options[color_scheme]" value="dark" <?php checked( espanol_get_option( 'color_scheme', 'dark' ), 'dark' ); ?>> <?php esc_html_e( 'Dark (padrão)', 'espanol' ); ?></label>
						<label><input type="radio" name="espanol_options[color_scheme]" value="light" <?php checked( espanol_get_option( 'color_scheme', 'dark' ), 'light' ); ?>> <?php esc_html_e( 'White (versão clara)', 'espanol' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Linha de estatísticas (abaixo do header)', 'espanol' ); ?></th>
					<td>
						<input type="text" class="large-text" name="espanol_options[stats_text]" value="<?php echo esc_attr( espanol_get_option( 'stats_text' ) ); ?>" placeholder='Más de <span class="num">420,737,550</span> horas de videos de más de <span class="num">15,839</span> Pornstar'>
						<p class="description"><?php esc_html_e( 'Use <span class="num">…</span> para destacar números com a cor de destaque. Deixe vazio para ocultar.', 'espanol' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Aurora5 Secret (player)', 'espanol' ); ?></th>
					<td>
						<input type="text" class="regular-text" name="espanol_options[aurora5_secret]" value="<?php echo esc_attr( espanol_get_option( 'aurora5_secret' ) ); ?>" autocomplete="off">
						<p class="description"><?php esc_html_e( 'Secret usado para assinar a URL do vídeo a partir do campo video_uuid (mesma conversão do tema-a99). Vazio = usa o secret configurado no Reelix, se houver.', 'espanol' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Google Client ID (login com Google)', 'espanol' ); ?></th>
					<td>
						<input type="text" class="large-text" name="espanol_options[google_client_id]" value="<?php echo esc_attr( espanol_get_option( 'google_client_id' ) ); ?>" placeholder="1234567890-xxxxxxxx.apps.googleusercontent.com">
						<p class="description"><?php esc_html_e( 'Crie em console.cloud.google.com → APIs e serviços → Credenciais → ID do cliente OAuth (aplicativo Web). Adicione o domínio do site nas "Origens JavaScript autorizadas". Vazio = botão do Google oculto.', 'espanol' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Botão CTA (canto superior direito)', 'espanol' ); ?></th>
					<td>
						<label><input type="checkbox" name="espanol_options[cta_show]" value="1" <?php checked( espanol_get_option( 'cta_show', 1 ), 1 ); ?>> <?php esc_html_e( 'Exibir botão', 'espanol' ); ?></label><br><br>
						<input type="text" name="espanol_options[cta_text]" value="<?php echo esc_attr( espanol_get_option( 'cta_text' ) ); ?>" placeholder="<?php esc_attr_e( 'Texto do botão', 'espanol' ); ?>">
						<input type="url" class="regular-text" name="espanol_options[cta_url]" value="<?php echo esc_attr( espanol_get_option( 'cta_url' ) ); ?>" placeholder="https://">
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'Códigos Header / Footer', 'espanol' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Código no <head>', 'espanol' ); ?></th>
					<td>
						<textarea name="espanol_options[header_code]" rows="6" class="large-text code" placeholder="&lt;script&gt;...&lt;/script&gt;"><?php echo esc_textarea( espanol_get_option( 'header_code' ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Analytics, meta tags de verificação, scripts de anúncios etc.', 'espanol' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Código antes do </body>', 'espanol' ); ?></th>
					<td><textarea name="espanol_options[footer_code]" rows="6" class="large-text code"><?php echo esc_textarea( espanol_get_option( 'footer_code' ) ); ?></textarea></td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'Rodapé', 'espanol' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Título do rodapé', 'espanol' ); ?></th>
					<td><input type="text" class="large-text" name="espanol_options[footer_title]" value="<?php echo esc_attr( espanol_get_option( 'footer_title' ) ); ?>" placeholder="MeuSite.com - Videos XXX & Clips Porn"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Descrição do rodapé', 'espanol' ); ?></th>
					<td><textarea name="espanol_options[footer_desc]" rows="4" class="large-text"><?php echo esc_textarea( espanol_get_option( 'footer_desc' ) ); ?></textarea></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Link/e-mail de contato', 'espanol' ); ?></th>
					<td><input type="text" class="regular-text" name="espanol_options[contact_url]" value="<?php echo esc_attr( espanol_get_option( 'contact_url' ) ); ?>" placeholder="mailto:contato@site.com"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Telegram', 'espanol' ); ?></th>
					<td><input type="url" class="regular-text" name="espanol_options[social_telegram]" value="<?php echo esc_attr( espanol_get_option( 'social_telegram' ) ); ?>" placeholder="https://t.me/..."></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Twitter / X', 'espanol' ); ?></th>
					<td><input type="url" class="regular-text" name="espanol_options[social_twitter]" value="<?php echo esc_attr( espanol_get_option( 'social_twitter' ) ); ?>" placeholder="https://x.com/..."></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'YouTube', 'espanol' ); ?></th>
					<td><input type="url" class="regular-text" name="espanol_options[social_youtube]" value="<?php echo esc_attr( espanol_get_option( 'social_youtube' ) ); ?>" placeholder="https://youtube.com/..."></td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'Blocos da Home', 'espanol' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Ordem na home: Últimos posts (sem título, paginado) → Shorts (Reelix) → Más Vistos → Más Votados. Na página 2 em diante só a grade de últimos posts continua.', 'espanol' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Últimos posts — quantidade por página', 'espanol' ); ?></th>
					<td><input type="number" name="espanol_options[home_latest_count]" value="<?php echo esc_attr( espanol_get_option( 'home_latest_count', 20 ) ); ?>" min="5" max="60"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Bloco "Más Vistos"', 'espanol' ); ?></th>
					<td>
						<label><input type="checkbox" name="espanol_options[home_viewed_show]" value="1" <?php checked( espanol_get_option( 'home_viewed_show', 1 ), 1 ); ?>> <?php esc_html_e( 'Exibir', 'espanol' ); ?></label><br><br>
						<input type="text" name="espanol_options[home_viewed_title]" value="<?php echo esc_attr( espanol_get_option( 'home_viewed_title' ) ); ?>" placeholder="Videos Más Vistos">
						<input type="number" name="espanol_options[home_viewed_count]" value="<?php echo esc_attr( espanol_get_option( 'home_viewed_count', 10 ) ); ?>" min="5" max="40" style="width:80px"> <?php esc_html_e( 'posts', 'espanol' ); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Bloco "Más Votados"', 'espanol' ); ?></th>
					<td>
						<label><input type="checkbox" name="espanol_options[home_voted_show]" value="1" <?php checked( espanol_get_option( 'home_voted_show', 1 ), 1 ); ?>> <?php esc_html_e( 'Exibir', 'espanol' ); ?></label><br><br>
						<input type="text" name="espanol_options[home_voted_title]" value="<?php echo esc_attr( espanol_get_option( 'home_voted_title' ) ); ?>" placeholder="Videos Más Votados">
						<input type="number" name="espanol_options[home_voted_count]" value="<?php echo esc_attr( espanol_get_option( 'home_voted_count', 10 ) ); ?>" min="5" max="40" style="width:80px"> <?php esc_html_e( 'posts', 'espanol' ); ?>
					</td>
				</tr>
			</table>

			<h2 class="title"><?php esc_html_e( 'Categorias em destaque', 'espanol' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Chips no TOPO (abaixo do menu)', 'espanol' ); ?></th>
					<td><?php espanol_cats_checklist( 'top_cats', espanol_get_option( 'top_cats', array() ) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Chips no RODAPÉ (carrossel)', 'espanol' ); ?></th>
					<td><?php espanol_cats_checklist( 'footer_cats', espanol_get_option( 'footer_cats', array() ) ); ?></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
