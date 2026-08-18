<?php
/**
 * Footer do tema.
 *
 * @package Espanol
 */

$espanol_footer_title = espanol_get_option( 'footer_title', get_bloginfo( 'name' ) . ' - Videos XXX & Clips Porn' );
$espanol_footer_desc  = espanol_get_option( 'footer_desc' );
$espanol_footer_cats  = espanol_get_option( 'footer_cats', array() );
$espanol_contact      = espanol_get_option( 'contact_url' );
$espanol_telegram     = espanol_get_option( 'social_telegram' );
$espanol_twitter      = espanol_get_option( 'social_twitter' );
?>
	</div><!-- .container -->
</main>

<img style="display:none" alt="Amung Count" src="https://whos.amung.us/swidget/4mk8logmuu.gif" width="0" height="0" border="0">
<footer class="site-footer">
	<div class="container">
		<p class="footer-title"><?php echo esc_html( $espanol_footer_title ); ?></p>

		<?php if ( $espanol_footer_desc ) : ?>
			<div class="footer-desc"><?php echo wp_kses_post( wpautop( $espanol_footer_desc ) ); ?></div>
		<?php endif; ?>

		<?php if ( $espanol_footer_cats ) : ?>
			<div class="footer-chips-wrap shorts-wrap">
				<button class="carousel-arrow prev js-scroll-prev" aria-label="<?php esc_attr_e( 'Anterior', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-l' ); ?></button>
				<div class="footer-chips js-scroll-track">
					<?php
					foreach ( $espanol_footer_cats as $espanol_cat_id ) :
						$espanol_term = get_term( $espanol_cat_id, espanol_tax( 'category' ) );
						if ( ! $espanol_term || is_wp_error( $espanol_term ) ) {
							continue;
						}
						?>
						<a class="chip" href="<?php echo esc_url( get_term_link( $espanol_term ) ); ?>"><?php echo esc_html( $espanol_term->name ); ?></a>
					<?php endforeach; ?>
				</div>
				<button class="carousel-arrow next js-scroll-next" aria-label="<?php esc_attr_e( 'Siguiente', 'espanol' ); ?>"><?php espanol_the_icon( 'chev-r' ); ?></button>
			</div>
		<?php endif; ?>

		<div class="footer-links">
			<?php
			// Links de páginas por slug (criadas pelo tema).
			$espanol_footer_pages = array(
				array( 'contacto', 'mail', __( 'Contact', 'espanol' ) ),
			);
			foreach ( $espanol_footer_pages as $espanol_fp ) :
				$espanol_fp_page = get_page_by_path( $espanol_fp[0] );
				if ( $espanol_fp_page ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $espanol_fp_page ) ); ?>"><?php espanol_the_icon( $espanol_fp[1] ); ?> <?php echo esc_html( $espanol_fp[2] ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ( $espanol_telegram ) : ?>
				<a href="<?php echo esc_url( $espanol_telegram ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'telegram' ); ?> <?php esc_html_e( 'Telegram Channel', 'espanol' ); ?></a>
			<?php endif; ?>
			<?php if ( $espanol_twitter ) : ?>
				<a href="<?php echo esc_url( $espanol_twitter ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'twitter' ); ?> Twitter</a>
			<?php endif; ?>

			<?php
			$espanol_legal_pages = array(
				array( 'politica-de-privacidad', 'shield', __( 'Policy and Privacy', 'espanol' ) ),
				array( 'politica-de-cookies', 'info', __( 'Cookie Policy', 'espanol' ) ),
				array( 'declaracion-2257', 'folder', '2257' ),
				array( 'dmca', 'gear', 'DMCA' ),
				array( 'rta', 'user', 'RTA' ),
			);
			foreach ( $espanol_legal_pages as $espanol_fp ) :
				$espanol_fp_page = get_page_by_path( $espanol_fp[0] );
				if ( $espanol_fp_page ) :
					?>
					<a href="<?php echo esc_url( get_permalink( $espanol_fp_page ) ); ?>"><?php espanol_the_icon( $espanol_fp[1] ); ?> <?php echo esc_html( $espanol_fp[2] ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'fallback_cb'    => false,
						'depth'          => 1,
					)
				);
			}
			?>
		</div>

		<p class="footer-copy">
			<?php echo esc_html( gmdate( 'Y' ) ); ?> - <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Todos los derechos reservados.', 'espanol' ); ?>
		</p>
	</div>
</footer>

<?php if ( ! is_user_logged_in() ) : ?>
	<div class="auth-modal js-auth-modal" aria-hidden="true">
		<div class="auth-overlay js-auth-close"></div>
		<div class="auth-dialog" role="dialog" aria-modal="true">
			<button class="auth-close js-auth-close" aria-label="<?php esc_attr_e( 'Cerrar', 'espanol' ); ?>"><?php espanol_the_icon( 'close' ); ?></button>

			<div class="auth-logo">
				<?php $espanol_auth_logo = espanol_get_option( 'logo' ); ?>
				<?php if ( $espanol_auth_logo ) : ?>
					<img src="<?php echo esc_url( $espanol_auth_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				<?php else : ?>
					<span class="logo-text"><span class="logo-x"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 1 ) ); ?></span>
				<?php endif; ?>
			</div>
			<p class="auth-subtitle"><?php esc_html_e( 'Accede para guardar favoritos y mucho más', 'espanol' ); ?></p>

			<div class="auth-tabs">
				<button type="button" class="auth-tab is-active" data-tab="login"><?php esc_html_e( 'Iniciar Sesión', 'espanol' ); ?></button>
				<button type="button" class="auth-tab" data-tab="register"><?php esc_html_e( 'Registrarse', 'espanol' ); ?></button>
			</div>

			<form class="auth-form js-auth-form is-active" data-action="espanol_login" data-tab="login">
				<div class="auth-field">
					<?php espanol_the_icon( 'user' ); ?>
					<input type="text" name="login" placeholder="<?php esc_attr_e( 'Usuario o email', 'espanol' ); ?>" autocomplete="username" required>
				</div>
				<div class="auth-field">
					<?php espanol_the_icon( 'lock' ); ?>
					<input type="password" name="password" placeholder="<?php esc_attr_e( 'Contraseña', 'espanol' ); ?>" autocomplete="current-password" required>
				</div>
				<div class="auth-row">
					<label><input type="checkbox" name="remember" value="1" checked> <?php esc_html_e( 'Recordarme', 'espanol' ); ?></label>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '¿Olvidaste tu contraseña?', 'espanol' ); ?></a>
				</div>
				<button type="submit" class="auth-submit"><?php esc_html_e( 'Entrar', 'espanol' ); ?></button>
				<p class="auth-msg"></p>
			</form>

			<form class="auth-form js-auth-form" data-action="espanol_register" data-tab="register">
				<?php if ( get_option( 'users_can_register' ) ) : ?>
					<div class="auth-field">
						<?php espanol_the_icon( 'user' ); ?>
						<input type="text" name="username" placeholder="<?php esc_attr_e( 'Nombre de usuario', 'espanol' ); ?>" autocomplete="username" required>
					</div>
					<div class="auth-field">
						<?php espanol_the_icon( 'mail' ); ?>
						<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email', 'espanol' ); ?>" autocomplete="email" required>
					</div>
					<div class="auth-field">
						<?php espanol_the_icon( 'lock' ); ?>
						<input type="password" name="password" placeholder="<?php esc_attr_e( 'Contraseña (mín. 6 caracteres)', 'espanol' ); ?>" autocomplete="new-password" minlength="6" required>
					</div>
					<button type="submit" class="auth-submit"><?php esc_html_e( 'Crear cuenta gratis', 'espanol' ); ?></button>
					<p class="auth-terms"><?php esc_html_e( 'Al registrarte confirmas que tienes 18 años o más.', 'espanol' ); ?></p>
					<p class="auth-msg"></p>
				<?php else : ?>
					<p class="auth-terms" style="padding:16px 0"><?php esc_html_e( 'El registro está deshabilitado en este momento.', 'espanol' ); ?></p>
				<?php endif; ?>
			</form>

			<?php $espanol_google_id = espanol_get_option( 'google_client_id' ); ?>
			<?php if ( $espanol_google_id ) : ?>
				<div class="auth-divider"><span><?php esc_html_e( 'o continúa con', 'espanol' ); ?></span></div>
				<div class="auth-google">
					<div id="g_id_onload"
						data-client_id="<?php echo esc_attr( $espanol_google_id ); ?>"
						data-callback="espanolGoogleCb"
						data-auto_prompt="false"></div>
					<div class="g_id_signin"
						data-type="standard"
						data-theme="<?php echo 'light' === espanol_get_option( 'color_scheme', 'dark' ) ? 'outline' : 'filled_black'; ?>"
						data-size="large"
						data-text="continue_with"
						data-shape="pill"
						data-locale="es"
						data-width="330"></div>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
