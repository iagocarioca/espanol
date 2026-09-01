<?php
/**
 * Aviso de conteúdo adulto (age gate).
 *
 * O markup sai sempre no HTML: a decisão é feita no client pelo cookie
 * `espanol_age_ok`, nunca no PHP, porque o full-page cache guardaria uma única
 * versão da página e serviria o gate (ou a ausência dele) para todo mundo.
 *
 * @package Espanol
 */

defined( 'ABSPATH' ) || exit;

$espanol_gate_logo = espanol_get_option( 'logo' );
?>

<div class="age-gate" id="age-gate">
	<div class="age-gate-overlay"></div>

	<div class="age-gate-dialog" role="dialog" aria-modal="true" aria-labelledby="age-gate-title">
		<div class="age-gate-logo">
			<?php if ( $espanol_gate_logo ) : ?>
				<img src="<?php echo esc_url( $espanol_gate_logo ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php else : ?>
				<span class="logo-text"><span class="logo-x"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 1 ) ); ?></span>
			<?php endif; ?>
		</div>

		<h2 class="age-gate-title" id="age-gate-title"><?php esc_html_e( 'Este es un sitio para adultos', 'espanol' ); ?></h2>

		<p class="age-gate-text">
			<?php
			printf(
				/* translators: %s: destaque "18 años o más". */
				esc_html__( 'Este sitio contiene material con restricción de edad, incluyendo desnudos y representaciones explícitas de actividad sexual. Al entrar, confirmas que tienes %s (o la mayoría de edad en tu jurisdicción) y que aceptas ver contenido para adultos.', 'espanol' ),
				'<strong>' . esc_html__( '18 años o más', 'espanol' ) . '</strong>'
			);
			?>
		</p>

		<div class="age-gate-actions">
			<button type="button" class="age-gate-btn is-accept" data-age-accept>
				<?php esc_html_e( 'Tengo 18 años o más · Entrar', 'espanol' ); ?>
			</button>

			<a href="https://www.google.com" rel="nofollow noopener" class="age-gate-btn is-exit">
				<?php esc_html_e( 'Soy menor de 18 · Salir', 'espanol' ); ?>
			</a>
		</div>

		<div class="age-gate-legal">
			<span>18 U.S.C. 2257</span>
			<?php include ESPANOL_DIR . '/svg/rta.svg'; ?>
		</div>
	</div>
</div>

<script>
	(function () {
		var gate = document.getElementById('age-gate');
		if (!gate) return;
		if (document.cookie.indexOf('espanol_age_ok=1') !== -1) return;

		gate.classList.add('is-open');
		document.body.classList.add('modal-open');

		gate.querySelector('[data-age-accept]').addEventListener('click', function () {
			document.cookie = 'espanol_age_ok=1;path=/;max-age=604800;samesite=lax';
			gate.remove();
			document.body.classList.remove('modal-open');
		});
	})();
</script>
