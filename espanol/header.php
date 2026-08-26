<?php
/**
 * Header do tema.
 *
 * @package Espanol
 */

$espanol_logo  = espanol_get_option( 'logo' );
$espanol_stats = espanol_get_option( 'stats_text', 'Más de <span class="num">420,737,550</span> horas de videos de más de <span class="num">15,839</span> Pornstar' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="rating" content="adult">
<meta name="google-site-verification" content="vhSOpkesDkXDHdN9GcdEjkzkDf82dXw-bH1shyu7g5Q" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="offcanvas-overlay js-menu-close"></div>
<aside class="offcanvas" aria-label="<?php esc_attr_e( 'Menú principal', 'espanol' ); ?>">
	<div class="offcanvas-head">
		<button class="offcanvas-close js-menu-close" aria-label="<?php esc_attr_e( 'Cerrar menú', 'espanol' ); ?>"><?php espanol_the_icon( 'close' ); ?></button>
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( $espanol_logo ) : ?>
				<img src="<?php echo esc_url( $espanol_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php else : ?>
				<span class="logo-text"><span class="logo-x"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 1 ) ); ?></span>
			<?php endif; ?>
		</a>
	</div>

	<a class="offcanvas-lang" href="<?php echo esc_url( home_url( '/' ) ); ?>">🇪🇸 <?php esc_html_e( 'Porno en Español', 'espanol' ); ?></a>

	<?php if ( has_nav_menu( 'mobile' ) ) : ?>
		<nav class="offcanvas-nav">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'mobile',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'walker'         => new Walker_Nav_Menu(),
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
	<?php else : ?>
		<nav class="offcanvas-nav">
			<a href="<?php echo esc_url( get_post_type_archive_link( 'custom_post' ) ); ?>"><?php espanol_the_icon( 'video' ); ?> <?php esc_html_e( 'Mejores Videos', 'espanol' ); ?></a>
			<a href="<?php echo esc_url( espanol_page_url_by_template( 'templates/page-categories.php' ) ); ?>"><?php espanol_the_icon( 'grid' ); ?> <?php esc_html_e( 'Categorías', 'espanol' ); ?></a>
			<a href="<?php echo esc_url( espanol_shorts_url() ); ?>"><?php espanol_the_icon( 'shorts' ); ?> <?php esc_html_e( 'Shorts', 'espanol' ); ?> <span class="badge-beta">Beta</span></a>
			<a href="<?php echo esc_url( espanol_page_url_by_template( 'templates/page-favorites.php' ) ); ?>"><?php espanol_the_icon( 'heart' ); ?> <?php esc_html_e( 'Favoritos', 'espanol' ); ?></a>
			<a href="<?php echo esc_url( espanol_page_url_by_template( 'templates/page-tags.php' ) ); ?>"><?php espanol_the_icon( 'tag' ); ?> <?php esc_html_e( 'Tags', 'espanol' ); ?></a>
			<a href="<?php echo esc_url( add_query_arg( array( 'orderby' => 'views' ), get_post_type_archive_link( 'custom_post' ) ) ); ?>"><?php espanol_the_icon( 'eye' ); ?> <?php esc_html_e( 'Más vistos', 'espanol' ); ?></a>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'custom_post' ) ); ?>"><?php espanol_the_icon( 'folder' ); ?> <?php esc_html_e( 'Recomendados', 'espanol' ); ?></a>
		</nav>
	<?php endif; ?>

	<?php
	$espanol_telegram = espanol_get_option( 'social_telegram' );
	$espanol_youtube  = espanol_get_option( 'social_youtube' );
	$espanol_twitter  = espanol_get_option( 'social_twitter' );
	if ( $espanol_telegram || $espanol_youtube || $espanol_twitter ) :
		?>
		<div class="offcanvas-social">
			<?php if ( $espanol_telegram ) : ?>
				<a href="<?php echo esc_url( $espanol_telegram ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'telegram' ); ?> Telegram</a>
			<?php endif; ?>
			<?php if ( $espanol_youtube ) : ?>
				<a href="<?php echo esc_url( $espanol_youtube ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'youtube' ); ?> Youtube</a>
			<?php endif; ?>
			<?php if ( $espanol_twitter ) : ?>
				<a href="<?php echo esc_url( $espanol_twitter ); ?>" target="_blank" rel="noopener nofollow"><?php espanol_the_icon( 'twitter' ); ?> X (Twitter)</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</aside>

<header class="site-header">
	<div class="header-bar js-header-bar">
		<div class="container">
			<div class="header-inner">
			<div class="header-left">
				<button class="burger js-menu-open" aria-label="<?php esc_attr_e( 'Abrir menú', 'espanol' ); ?>"><?php espanol_the_icon( 'menu' ); ?></button>

				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php if ( $espanol_logo ) : ?>
						<img src="<?php echo esc_url( $espanol_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>">
					<?php else : ?>
						<span class="logo-text"><span class="logo-x"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 1 ) ); ?></span>
					<?php endif; ?>
				</a>
			</div>

			<div class="header-search">
				<?php get_search_form(); ?>
			</div>

			<div class="header-actions">
				<?php if ( is_user_logged_in() ) : ?>
					<a class="header-lock" href="<?php echo esc_url( espanol_page_url_by_template( 'templates/page-account.php' ) ); ?>" aria-label="<?php esc_attr_e( 'Mi cuenta', 'espanol' ); ?>"><?php espanol_the_icon( 'user' ); ?></a>
				<?php else : ?>
					<a class="header-lock js-open-auth" href="<?php echo esc_url( wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Iniciar sesión', 'espanol' ); ?>"><?php espanol_the_icon( 'lock' ); ?></a>
				<?php endif; ?>
				<?php
				// Botão CTA: personalizável nas Opções do Tema; padrão = abre o modal de login/registro.
				$espanol_cta_url_custom = espanol_get_option( 'cta_url' );
				$espanol_cta_text       = espanol_get_option( 'cta_text', __( 'Iniciar Sesión', 'espanol' ) );

				if ( espanol_get_option( 'cta_show', 1 ) && $espanol_cta_text ) :
					if ( is_user_logged_in() && ! $espanol_cta_url_custom ) :
						?>
						<a class="btn-cta" href="<?php echo esc_url( espanol_page_url_by_template( 'templates/page-account.php' ) ); ?>">
							<span class="cta-label"><?php esc_html_e( 'Mi Cuenta', 'espanol' ); ?></span>
						</a>
					<?php elseif ( $espanol_cta_url_custom ) : ?>
						<a class="btn-cta" href="<?php echo esc_url( $espanol_cta_url_custom ); ?>" target="_blank" rel="noopener nofollow">
							<span class="cta-label"><?php echo esc_html( $espanol_cta_text ); ?></span>
						</a>
					<?php else : ?>
						<a class="btn-cta js-open-auth" href="<?php echo esc_url( wp_login_url() ); ?>">
							<?php espanol_the_icon( 'login' ); ?>
							<span class="cta-label"><?php echo esc_html( $espanol_cta_text ); ?></span>
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	</div><!-- .header-bar -->

	<div class="container">
		<?php if ( $espanol_stats ) : ?>
			<div class="header-stats"><?php echo wp_kses( $espanol_stats, array( 'span' => array( 'class' => array() ), 'strong' => array(), 'b' => array() ) ); ?></div>
		<?php endif; ?>

		<div class="nav-pills-wrap">
		<nav class="nav-pills">
			<?php
			$espanol_pills = array(
				array( 'grid', __( 'Categorías', 'espanol' ), espanol_page_url_by_template( 'templates/page-categories.php' ), '' ),
				array( 'shorts', __( 'Shorts', 'espanol' ), espanol_shorts_url(), 'nav-pill--shorts' ),
				array( 'heart', __( 'Favoritos', 'espanol' ), espanol_page_url_by_template( 'templates/page-favorites.php' ), '' ),
				array( 'tag', __( 'Tags', 'espanol' ), espanol_page_url_by_template( 'templates/page-tags.php' ), '' ),
			);
			foreach ( $espanol_pills as $espanol_pill ) :
				?>
				<a class="nav-pill <?php echo esc_attr( $espanol_pill[3] ); ?>" href="<?php echo esc_url( $espanol_pill[2] ); ?>">
					<?php espanol_the_icon( $espanol_pill[0] ); ?> <?php echo esc_html( $espanol_pill[1] ); ?>
					<?php if ( 'nav-pill--shorts' === $espanol_pill[3] ) : ?><span class="pill-badge">Beta</span><?php endif; ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<span class="nav-scroll-hint" aria-hidden="true"><?php espanol_the_icon( 'chev-r' ); ?></span>
		</div>

		<?php
		$espanol_top_cats = espanol_get_option( 'top_cats', array() );
		if ( $espanol_top_cats ) :
			?>
			<div class="chips-row">
				<?php
				foreach ( $espanol_top_cats as $espanol_cat_id ) :
					$espanol_term = get_term( $espanol_cat_id, espanol_tax( 'category' ) );
					if ( ! $espanol_term || is_wp_error( $espanol_term ) ) {
						continue;
					}
					?>
					<a class="chip" href="<?php echo esc_url( get_term_link( $espanol_term ) ); ?>"><?php echo esc_html( $espanol_term->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</header>

<main class="site-main">
	<div class="container">
