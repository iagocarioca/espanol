<?php
/**
 * Template Name: Página de Contato
 *
 * @package Espanol
 */

get_header();
?>

<article class="page-content">
	<h1 class="page-heading"><?php espanol_the_icon( 'mail' ); ?> <?php the_title(); ?></h1>

	<?php if ( get_the_content() ) : ?>
		<div class="page-body"><?php the_content(); ?></div>
	<?php endif; ?>

	<form class="contact-form js-contact-form">
		<div class="cf-row">
			<div class="cf-field">
				<label for="cf-name"><?php esc_html_e( 'Nombre', 'espanol' ); ?></label>
				<input type="text" id="cf-name" name="name" required>
			</div>
			<div class="cf-field">
				<label for="cf-email"><?php esc_html_e( 'Email', 'espanol' ); ?></label>
				<input type="email" id="cf-email" name="email" required>
			</div>
		</div>
		<div class="cf-field">
			<label for="cf-subject"><?php esc_html_e( 'Asunto', 'espanol' ); ?></label>
			<input type="text" id="cf-subject" name="subject" required>
		</div>
		<div class="cf-field">
			<label for="cf-message"><?php esc_html_e( 'Mensaje', 'espanol' ); ?></label>
			<textarea id="cf-message" name="message" rows="6" required></textarea>
		</div>
		<input type="text" name="website" class="cf-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
		<button type="submit" class="auth-submit" style="max-width:240px"><?php esc_html_e( 'Enviar mensaje', 'espanol' ); ?></button>
		<p class="auth-msg"></p>
	</form>
</article>

<?php get_footer(); ?>
