<?php
/**
 * Endpoints AJAX: likes, contagem de views e favoritos.
 *
 * @package Espanol
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra like/dislike.
 */
function espanol_ajax_vote() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$vote    = isset( $_POST['vote'] ) ? sanitize_key( $_POST['vote'] ) : '';

	if ( ! $post_id || ! in_array( get_post_type( $post_id ), espanol_video_types(), true ) || ! in_array( $vote, array( 'like', 'dislike' ), true ) ) {
		wp_send_json_error();
	}

	$key   = 'like' === $vote ? espanol_field( 'likes' ) : espanol_field( 'dislikes' );
	$count = (int) get_post_meta( $post_id, $key, true ) + 1;
	update_post_meta( $post_id, $key, $count );

	// Mantém o percentual "votos" sincronizado (compatibilidade com o tema-a99).
	$likes_total    = (int) get_post_meta( $post_id, espanol_field( 'likes' ), true );
	$dislikes_total = (int) get_post_meta( $post_id, espanol_field( 'dislikes' ), true );
	if ( $likes_total + $dislikes_total > 0 ) {
		update_post_meta( $post_id, espanol_field( 'percent' ), (int) round( ( $likes_total / ( $likes_total + $dislikes_total ) ) * 100 ) );
	}

	// Usuário logado: guarda a curtida na conta (lista "Me gusta" da página Mi Cuenta).
	if ( 'like' === $vote && is_user_logged_in() ) {
		$liked = (array) get_user_meta( get_current_user_id(), 'espanol_liked_videos', true );
		if ( ! in_array( $post_id, $liked, true ) ) {
			$liked[] = $post_id;
			update_user_meta( get_current_user_id(), 'espanol_liked_videos', array_values( array_filter( $liked ) ) );
		}
	}

	wp_send_json_success(
		array(
			'likes'   => (int) get_post_meta( $post_id, espanol_field( 'likes' ), true ),
			'percent' => espanol_like_percent( $post_id ),
		)
	);
}
add_action( 'wp_ajax_espanol_vote', 'espanol_ajax_vote' );
add_action( 'wp_ajax_nopriv_espanol_vote', 'espanol_ajax_vote' );

/**
 * Incrementa views (chamado via JS na single, compatível com cache).
 */
function espanol_ajax_view() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $post_id || ! in_array( get_post_type( $post_id ), espanol_video_types(), true ) ) {
		wp_send_json_error();
	}

	$views = (int) get_post_meta( $post_id, espanol_field( 'views' ), true ) + 1;
	update_post_meta( $post_id, espanol_field( 'views' ), $views );
	wp_send_json_success( array( 'views' => $views ) );
}
add_action( 'wp_ajax_espanol_view', 'espanol_ajax_view' );
add_action( 'wp_ajax_nopriv_espanol_view', 'espanol_ajax_view' );

/**
 * Login via modal.
 */
function espanol_ajax_login() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	$creds = array(
		'user_login'    => isset( $_POST['login'] ) ? sanitize_text_field( wp_unslash( $_POST['login'] ) ) : '',
		'user_password' => isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : '', // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- senha não deve ser alterada.
		'remember'      => ! empty( $_POST['remember'] ),
	);

	if ( '' === $creds['user_login'] || '' === $creds['user_password'] ) {
		wp_send_json_error( array( 'message' => __( 'Completa usuario y contraseña.', 'espanol' ) ) );
	}

	$user = wp_signon( $creds, is_ssl() );
	if ( is_wp_error( $user ) ) {
		wp_send_json_error( array( 'message' => __( 'Usuario o contraseña incorrectos.', 'espanol' ) ) );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_nopriv_espanol_login', 'espanol_ajax_login' );

/**
 * Registro via modal.
 */
function espanol_ajax_register() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'message' => __( 'El registro está deshabilitado en este momento.', 'espanol' ) ) );
	}

	$username = isset( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$password = isset( $_POST['password'] ) ? (string) wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- senha não deve ser alterada.

	if ( '' === $username || '' === $email || '' === $password ) {
		wp_send_json_error( array( 'message' => __( 'Completa todos los campos.', 'espanol' ) ) );
	}
	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'El email no es válido.', 'espanol' ) ) );
	}
	if ( strlen( $password ) < 6 ) {
		wp_send_json_error( array( 'message' => __( 'La contraseña debe tener al menos 6 caracteres.', 'espanol' ) ) );
	}
	if ( username_exists( $username ) ) {
		wp_send_json_error( array( 'message' => __( 'Ese nombre de usuario ya existe.', 'espanol' ) ) );
	}
	if ( email_exists( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Ya existe una cuenta con ese email.', 'espanol' ) ) );
	}

	$user_id = wp_create_user( $username, $password, $email );
	if ( is_wp_error( $user_id ) ) {
		wp_send_json_error( array( 'message' => __( 'No se pudo crear la cuenta. Inténtalo de nuevo.', 'espanol' ) ) );
	}

	// Login automático após o registro.
	wp_signon(
		array(
			'user_login'    => $username,
			'user_password' => $password,
			'remember'      => true,
		),
		is_ssl()
	);

	wp_send_json_success();
}
add_action( 'wp_ajax_nopriv_espanol_register', 'espanol_ajax_register' );

/**
 * Login/registro com Google (Google Identity Services).
 * Recebe o credential (JWT) do botão do Google e valida no tokeninfo.
 */
function espanol_ajax_google_auth() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	$client_id  = espanol_get_option( 'google_client_id' );
	$credential = isset( $_POST['credential'] ) ? sanitize_text_field( wp_unslash( $_POST['credential'] ) ) : '';

	if ( ! $client_id || ! $credential ) {
		wp_send_json_error( array( 'message' => __( 'Login con Google no disponible.', 'espanol' ) ) );
	}

	// Validação do token na API do Google.
	$resp = wp_remote_get(
		'https://oauth2.googleapis.com/tokeninfo?id_token=' . rawurlencode( $credential ),
		array( 'timeout' => 15 )
	);
	if ( is_wp_error( $resp ) || 200 !== wp_remote_retrieve_response_code( $resp ) ) {
		wp_send_json_error( array( 'message' => __( 'No se pudo validar con Google. Inténtalo de nuevo.', 'espanol' ) ) );
	}

	$data = json_decode( wp_remote_retrieve_body( $resp ), true );
	if (
		! is_array( $data )
		|| empty( $data['aud'] ) || $data['aud'] !== $client_id
		|| empty( $data['email'] )
		|| empty( $data['email_verified'] ) || 'true' !== $data['email_verified']
		|| empty( $data['iss'] ) || ! in_array( $data['iss'], array( 'accounts.google.com', 'https://accounts.google.com' ), true )
	) {
		wp_send_json_error( array( 'message' => __( 'Token de Google inválido.', 'espanol' ) ) );
	}

	$email = sanitize_email( $data['email'] );
	$sub   = sanitize_text_field( $data['sub'] );
	$name  = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';

	// Procura a conta: primeiro pelo ID do Google, depois pelo email.
	$users = get_users(
		array(
			'meta_key'   => 'espanol_google_sub',
			'meta_value' => $sub,
			'number'     => 1,
		)
	);
	$user  = $users ? $users[0] : get_user_by( 'email', $email );

	if ( ! $user ) {
		if ( ! get_option( 'users_can_register' ) ) {
			wp_send_json_error( array( 'message' => __( 'El registro está deshabilitado en este momento.', 'espanol' ) ) );
		}

		// Cria a conta com nome de usuário único a partir do email.
		$base_login = sanitize_user( strstr( $email, '@', true ), true );
		$base_login = $base_login ? $base_login : 'user';
		$login      = $base_login;
		$suffix     = 1;
		while ( username_exists( $login ) ) {
			$login = $base_login . $suffix;
			$suffix++;
		}

		$user_id = wp_create_user( $login, wp_generate_password( 24 ), $email );
		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error( array( 'message' => __( 'No se pudo crear la cuenta. Inténtalo de nuevo.', 'espanol' ) ) );
		}
		if ( $name ) {
			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => $name,
				)
			);
		}
		$user = get_user_by( 'id', $user_id );
	}

	update_user_meta( $user->ID, 'espanol_google_sub', $sub );

	wp_set_current_user( $user->ID );
	wp_set_auth_cookie( $user->ID, true, is_ssl() );

	wp_send_json_success();
}
add_action( 'wp_ajax_nopriv_espanol_google_auth', 'espanol_ajax_google_auth' );

/**
 * Formulário de contato.
 */
function espanol_ajax_contact() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	// Honeypot: bots preenchem o campo escondido.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success();
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $subject || ! $message ) {
		wp_send_json_error( array( 'message' => __( 'Completa todos los campos con datos válidos.', 'espanol' ) ) );
	}

	// Destino: email extraído do campo "contato" do painel, senão o email do admin.
	$to      = get_option( 'admin_email' );
	$contact = espanol_get_option( 'contact_url' );
	if ( $contact && is_email( str_replace( 'mailto:', '', $contact ) ) ) {
		$to = str_replace( 'mailto:', '', $contact );
	}

	$body = sprintf(
		"%s\n\n---\n%s: %s\n%s: %s\nIP: %s",
		$message,
		__( 'Nombre', 'espanol' ),
		$name,
		'Email',
		$email,
		isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : ''
	);

	$sent = wp_mail(
		$to,
		'[' . get_bloginfo( 'name' ) . '] ' . $subject,
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'No se pudo enviar el mensaje. Inténtalo más tarde.', 'espanol' ) ) );
	}

	wp_send_json_success();
}
add_action( 'wp_ajax_espanol_contact', 'espanol_ajax_contact' );
add_action( 'wp_ajax_nopriv_espanol_contact', 'espanol_ajax_contact' );

/**
 * Retorna os cards dos vídeos favoritados (IDs vêm do localStorage).
 */
function espanol_ajax_favorites() {
	check_ajax_referer( 'espanol_ajax', 'nonce' );

	$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
	$ids = array_slice( array_filter( $ids ), 0, 100 );

	if ( ! $ids ) {
		wp_send_json_success( array( 'html' => '' ) );
	}

	$query = new WP_Query(
		array(
			'post_type'      => espanol_video_types(),
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => 100,
		)
	);

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		espanol_video_card();
	}
	wp_reset_postdata();

	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_espanol_favorites', 'espanol_ajax_favorites' );
add_action( 'wp_ajax_nopriv_espanol_favorites', 'espanol_ajax_favorites' );
