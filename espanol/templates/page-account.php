<?php
/**
 * Template Name: Página Mi Cuenta
 *
 * @package Espanol
 */

get_header();

if ( ! is_user_logged_in() ) :
	?>
	<div class="account-guest">
		<span class="account-guest-icon"><?php espanol_the_icon( 'user' ); ?></span>
		<h1><?php esc_html_e( 'Mi Cuenta', 'espanol' ); ?></h1>
		<p><?php esc_html_e( 'Inicia sesión o crea una cuenta para ver tus favoritos y tus me gusta.', 'espanol' ); ?></p>
		<a class="btn-more js-open-auth" style="display:inline-block;width:auto;padding:12px 34px" href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Iniciar Sesión', 'espanol' ); ?></a>
	</div>
	<?php
	get_footer();
	return;
endif;

$espanol_user  = wp_get_current_user();
$espanol_liked = array_filter( array_map( 'intval', (array) get_user_meta( $espanol_user->ID, 'espanol_liked_videos', true ) ) );
?>

<div class="account-header">
	<div class="account-avatar"><?php echo get_avatar( $espanol_user->ID, 84 ); ?></div>
	<div class="account-info">
		<h1 class="account-name"><?php echo esc_html( $espanol_user->display_name ); ?></h1>
		<p class="account-email"><?php echo esc_html( $espanol_user->user_email ); ?></p>
		<p class="account-since">
			<?php
			/* translators: %s: data de registro. */
			echo esc_html( sprintf( __( 'Miembro desde %s', 'espanol' ), date_i18n( 'M Y', strtotime( $espanol_user->user_registered ) ) ) );
			?>
		</p>
	</div>
	<div class="account-actions">
		<a class="action-btn" href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php espanol_the_icon( 'user' ); ?> <?php esc_html_e( 'Editar perfil', 'espanol' ); ?></a>
		<a class="action-btn" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Cerrar sesión', 'espanol' ); ?></a>
	</div>
</div>

<div class="account-stats">
	<div class="account-stat">
		<strong class="js-fav-count">0</strong>
		<span><?php esc_html_e( 'Favoritos', 'espanol' ); ?></span>
	</div>
	<div class="account-stat">
		<strong><?php echo count( $espanol_liked ); ?></strong>
		<span><?php esc_html_e( 'Me gusta', 'espanol' ); ?></span>
	</div>
</div>

<div class="account-tabs">
	<button type="button" class="account-tab is-active js-acc-tab" data-pane="favs"><?php espanol_the_icon( 'heart' ); ?> <?php esc_html_e( 'Favoritos', 'espanol' ); ?></button>
	<button type="button" class="account-tab js-acc-tab" data-pane="likes"><?php espanol_the_icon( 'like' ); ?> <?php esc_html_e( 'Me gusta', 'espanol' ); ?></button>
</div>

<div class="account-pane is-active js-acc-pane" data-pane="favs">
	<div class="video-grid js-favorites-grid"></div>
	<p class="notice-empty js-favorites-empty" style="display:none"><?php esc_html_e( 'Aún no has añadido videos a favoritos. Haz clic en el corazón de los videos para guardarlos aquí.', 'espanol' ); ?></p>
</div>

<div class="account-pane js-acc-pane" data-pane="likes">
	<?php if ( $espanol_liked ) : ?>
		<?php
		$espanol_liked_q = new WP_Query(
			array(
				'post_type'      => espanol_video_types(),
				'post__in'       => array_reverse( $espanol_liked ),
				'orderby'        => 'post__in',
				'posts_per_page' => 100,
			)
		);
		?>
		<div class="video-grid">
			<?php
			while ( $espanol_liked_q->have_posts() ) :
				$espanol_liked_q->the_post();
				espanol_video_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	<?php else : ?>
		<p class="notice-empty"><?php esc_html_e( 'Aún no le has dado me gusta a ningún video.', 'espanol' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
