<?php
/**
 * Aviso de conteúdo adulto (age gate).
 *
 * O markup sai sempre no HTML: a decisão é feita no client pelo cookie
 * `espanol_age_ok`, nunca no PHP, porque o full-page cache guardaria uma única
 * versão da página e serviria o gate (ou a ausência dele) para todo mundo.
 *
 * Fica no FIM do <body> (footer.php), não no topo. O Googlebot renderiza a
 * página sem o cookie, enxerga este painel cobrindo a tela e passava a usar o
 * texto do aviso como descrição do resultado de busca, no lugar da meta
 * description — em toda página do site, sempre o mesmo parágrafo. Quem cobre o
 * primeiro frame é a cortina criada pelo script do header: uma div vazia, sem
 * texto para indexar.
 *
 * Este painel fica dentro de um <template>: ali o markup é inerte, não é
 * renderizado nem lido como texto da página, então some do HTML servido. O JS
 * clona e insere no DOMContentLoaded, e só para quem não tem o cookie.
 *
 * O `data-nosnippet` não é redundância do <template>: o Googlebot não tem cookie,
 * recebe o clone e lê o texto no DOM renderizado. É o atributo que proíbe o
 * Google de usar este texto como descrição do resultado; o <template> só reduz a
 * superfície no HTML servido e nos crawlers que não executam JS.
 *
 * @package Espanol
 */

defined( 'ABSPATH' ) || exit;

$espanol_gate_logo = espanol_get_option( 'logo' );
?>

<template id="ageGateTemplate">
<div class="age-gate" id="age-gate" data-nosnippet>
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
</template>

<script>
	(function () {
		var root = document.documentElement;
		var gate = null;

		// Toda saída passa por aqui. Sem isso, qualquer falha deixaria o visitante
		// preso atrás da cortina preta: página travada e nada para clicar.
		function release() {
			var veil = document.getElementById('age-veil');
			if (gate) gate.remove();
			if (veil) veil.remove();
			document.body.classList.remove('modal-open');
			root.style.overflow = '';
		}

		function start() {
			// O cookie pode ter aparecido depois do script do header — outra aba
			// aceitou enquanto esta carregava.
			if (document.cookie.indexOf('espanol_age_ok=1') !== -1) return release();

			var tpl = document.getElementById('ageGateTemplate');
			if (!tpl || !tpl.content) return release();

			// O painel só entra no DOM aqui. Dentro do <template> o markup é inerte:
			// não é renderizado nem lido como texto da página, então some do HTML
			// servido. O `data-nosnippet` na div continua valendo — depois do clone o
			// texto está no DOM vivo, e é o DOM renderizado que o Googlebot lê.
			var frag = tpl.content.cloneNode(true);
			gate = frag.querySelector('#age-gate');
			var accept = gate && gate.querySelector('[data-age-accept]');
			if (!accept) return release();

			// Abrir antes de inserir: assim o painel nunca chega a pintar fechado.
			gate.classList.add('is-open');
			document.body.appendChild(frag);
			document.body.classList.add('modal-open');
			root.style.overflow = 'hidden';

			// A cortina sai no mesmo frame em que o painel entra: as duas juntas
			// escureceriam em dobro, e o fundo do painel precisa enxergar a página,
			// não uma camada preta por cima dela. A trava da rolagem fica.
			var veil = document.getElementById('age-veil');
			if (veil) veil.remove();

			accept.addEventListener('click', function () {
				document.cookie = 'espanol_age_ok=1;path=/;max-age=604800;samesite=lax';
				release();
			});
		}

		// O `readyState` é a trava de segurança: se o DOMContentLoaded já tiver
		// disparado, o listener nunca chamaria start() e a cortina ficaria para
		// sempre. Aqui no fim do <body> o estado é `loading`, então espera o evento.
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', start);
		} else {
			start();
		}
	})();
</script>
