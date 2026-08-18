/* Espanol — JS do front. */
(function () {
	'use strict';

	var FAV_KEY = 'espanol_favs';

	/* Callback global do botão "Continuar com Google" (Google Identity Services). */
	window.espanolGoogleCb = function (response) {
		if (!response || !response.credential) { return; }
		var body = new URLSearchParams();
		body.append('action', 'espanol_google_auth');
		body.append('nonce', espanolData.nonce);
		body.append('credential', response.credential);

		fetch(espanolData.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res.success) {
					window.location.reload();
					return;
				}
				var msg = document.querySelector('.auth-form.is-active .auth-msg');
				if (msg) {
					msg.textContent = (res.data && res.data.message) ? res.data.message : 'Error con Google. Inténtalo de nuevo.';
					msg.className = 'auth-msg error';
				}
			})
			.catch(function () {
				var msg = document.querySelector('.auth-form.is-active .auth-msg');
				if (msg) {
					msg.textContent = 'Error de conexión. Inténtalo de nuevo.';
					msg.className = 'auth-msg error';
				}
			});
	};

	function getFavs() {
		try {
			return JSON.parse(localStorage.getItem(FAV_KEY)) || [];
		} catch (e) {
			return [];
		}
	}

	function setFavs(ids) {
		localStorage.setItem(FAV_KEY, JSON.stringify(ids));
	}

	function post(action, data) {
		var body = new URLSearchParams();
		body.append('action', action);
		body.append('nonce', espanolData.nonce);
		Object.keys(data || {}).forEach(function (k) {
			if (Array.isArray(data[k])) {
				data[k].forEach(function (v) { body.append(k + '[]', v); });
			} else {
				body.append(k, data[k]);
			}
		});
		return fetch(espanolData.ajaxUrl, { method: 'POST', body: body }).then(function (r) { return r.json(); });
	}

	document.addEventListener('DOMContentLoaded', function () {

		/* Header fixo ao rolar */
		var headerBar = document.querySelector('.js-header-bar');
		if (headerBar) {
			var headerSpacer = document.createElement('div');
			headerSpacer.style.display = 'none';
			headerBar.parentNode.insertBefore(headerSpacer, headerBar.nextSibling);

			var onHeaderScroll = function () {
				if (window.scrollY > 200) {
					if (!headerBar.classList.contains('is-fixed')) {
						headerSpacer.style.height = headerBar.offsetHeight + 'px';
						headerSpacer.style.display = 'block';
						headerBar.classList.add('is-fixed');
					}
				} else if (headerBar.classList.contains('is-fixed')) {
					headerBar.classList.remove('is-fixed');
					headerSpacer.style.display = 'none';
				}
			};
			window.addEventListener('scroll', onHeaderScroll, { passive: true });
			onHeaderScroll();
		}

		/* Indicador de rolagem das pílulas (mobile) */
		var pillsWrap = document.querySelector('.nav-pills-wrap');
		if (pillsWrap) {
			var pillsTrack = pillsWrap.querySelector('.nav-pills');
			var updatePillsScroll = function () {
				var overflowing = pillsTrack.scrollWidth > pillsTrack.clientWidth + 4;
				pillsWrap.classList.toggle('has-scroll', overflowing);
				var atEnd = pillsTrack.scrollLeft + pillsTrack.clientWidth >= pillsTrack.scrollWidth - 8;
				pillsWrap.classList.toggle('at-end', atEnd);
			};
			pillsTrack.addEventListener('scroll', updatePillsScroll, { passive: true });
			window.addEventListener('resize', updatePillsScroll);
			updatePillsScroll();
		}

		/* Realce deslizante nas pílulas do menu */
		var pillsNav = document.querySelector('.nav-pills');
		if (pillsNav && window.matchMedia('(hover: hover)').matches) {
			var pillInd = document.createElement('span');
			pillInd.className = 'nav-indicator';
			pillsNav.appendChild(pillInd);

			pillsNav.querySelectorAll('.nav-pill').forEach(function (pill) {
				pill.addEventListener('mouseenter', function () {
					pillInd.style.left = pill.offsetLeft + 'px';
					pillInd.style.top = pill.offsetTop + 'px';
					pillInd.style.width = pill.offsetWidth + 'px';
					pillInd.style.height = pill.offsetHeight + 'px';
					pillInd.classList.add('on');
				});
			});
			pillsNav.addEventListener('mouseleave', function () {
				pillInd.classList.remove('on');
			});
		}

		/* Menu off-canvas */
		document.querySelectorAll('.js-menu-open').forEach(function (btn) {
			btn.addEventListener('click', function () { document.body.classList.add('menu-open'); });
		});
		document.querySelectorAll('.js-menu-close').forEach(function (el) {
			el.addEventListener('click', function () { document.body.classList.remove('menu-open'); });
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { document.body.classList.remove('menu-open'); }
		});

		/* Carrosséis */
		document.querySelectorAll('.shorts-wrap, .footer-chips-wrap').forEach(function (wrap) {
			var track = wrap.querySelector('.js-scroll-track');
			if (!track) { return; }
			var prev = wrap.querySelector('.js-scroll-prev');
			var next = wrap.querySelector('.js-scroll-next');
			var step = function () { return Math.max(240, track.clientWidth * 0.7); };
			if (prev) { prev.addEventListener('click', function () { track.scrollBy({ left: -step(), behavior: 'smooth' }); }); }
			if (next) { next.addEventListener('click', function () { track.scrollBy({ left: step(), behavior: 'smooth' }); }); }
		});

		/* Modal de login / registro */
		var authModal = document.querySelector('.js-auth-modal');
		if (authModal) {
			var openAuth = function (e) {
				e.preventDefault();
				authModal.classList.add('open');
				document.body.classList.add('modal-open');
				var firstInput = authModal.querySelector('.auth-form.is-active input');
				if (firstInput) { firstInput.focus(); }
			};
			var closeAuth = function () {
				authModal.classList.remove('open');
				document.body.classList.remove('modal-open');
			};

			document.querySelectorAll('.js-open-auth').forEach(function (el) {
				el.addEventListener('click', openAuth);
			});
			authModal.querySelectorAll('.js-auth-close').forEach(function (el) {
				el.addEventListener('click', closeAuth);
			});
			document.addEventListener('keydown', function (e) {
				if (e.key === 'Escape') { closeAuth(); }
			});

			/* Abas */
			authModal.querySelectorAll('.auth-tab').forEach(function (tab) {
				tab.addEventListener('click', function () {
					var target = tab.getAttribute('data-tab');
					authModal.querySelectorAll('.auth-tab').forEach(function (t) { t.classList.toggle('is-active', t === tab); });
					authModal.querySelectorAll('.js-auth-form').forEach(function (f) {
						f.classList.toggle('is-active', f.getAttribute('data-tab') === target);
					});
					var input = authModal.querySelector('.auth-form.is-active input');
					if (input) { input.focus(); }
				});
			});

			/* Envio */
			authModal.querySelectorAll('.js-auth-form').forEach(function (form) {
				form.addEventListener('submit', function (e) {
					e.preventDefault();
					var msg = form.querySelector('.auth-msg');
					var btn = form.querySelector('.auth-submit');
					if (!msg || !btn) { return; }
					msg.className = 'auth-msg';
					btn.disabled = true;

					var body = new FormData(form);
					body.append('action', form.getAttribute('data-action'));
					body.append('nonce', espanolData.nonce);

					fetch(espanolData.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
						.then(function (r) { return r.json(); })
						.then(function (res) {
							if (res.success) {
								msg.textContent = 'Listo, redirigiendo…';
								msg.className = 'auth-msg success';
								window.location.reload();
							} else {
								msg.textContent = (res.data && res.data.message) ? res.data.message : 'Error. Inténtalo de nuevo.';
								msg.className = 'auth-msg error';
								btn.disabled = false;
							}
						})
						.catch(function () {
							msg.textContent = 'Error de conexión. Inténtalo de nuevo.';
							msg.className = 'auth-msg error';
							btn.disabled = false;
						});
				});
			});
		}

		/* Formulário de contato */
		var contactForm = document.querySelector('.js-contact-form');
		if (contactForm) {
			contactForm.addEventListener('submit', function (e) {
				e.preventDefault();
				var msg = contactForm.querySelector('.auth-msg');
				var btn = contactForm.querySelector('.auth-submit');
				msg.className = 'auth-msg';
				btn.disabled = true;

				var body = new FormData(contactForm);
				body.append('action', 'espanol_contact');
				body.append('nonce', espanolData.nonce);

				fetch(espanolData.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
					.then(function (r) { return r.json(); })
					.then(function (res) {
						if (res.success) {
							msg.textContent = 'Mensaje enviado. ¡Gracias!';
							msg.className = 'auth-msg success';
							contactForm.reset();
						} else {
							msg.textContent = (res.data && res.data.message) ? res.data.message : 'Error al enviar.';
							msg.className = 'auth-msg error';
						}
						btn.disabled = false;
					})
					.catch(function () {
						msg.textContent = 'Error de conexión. Inténtalo de nuevo.';
						msg.className = 'auth-msg error';
						btn.disabled = false;
					});
			});
		}

		/* Menu de três pontos dos cards */
		document.addEventListener('click', function (e) {
			var menuBtn = e.target.closest('.js-card-menu');
			var openDropdowns = document.querySelectorAll('.vc-dropdown.open');

			if (menuBtn) {
				var dropdown = menuBtn.parentElement.querySelector('.vc-dropdown');
				openDropdowns.forEach(function (d) { if (d !== dropdown) { d.classList.remove('open'); } });
				dropdown.classList.toggle('open');
				return;
			}

			var shareBtn = e.target.closest('.js-card-share');
			if (shareBtn) {
				var url = shareBtn.getAttribute('data-url');
				if (navigator.share) {
					navigator.share({ url: url });
				} else {
					navigator.clipboard.writeText(url);
				}
				openDropdowns.forEach(function (d) { d.classList.remove('open'); });
				return;
			}

			if (!e.target.closest('.vc-dropdown')) {
				openDropdowns.forEach(function (d) { d.classList.remove('open'); });
			}
		});

		/* Favoritos (localStorage) */
		var favs = getFavs();
		document.querySelectorAll('.js-fav').forEach(function (btn) {
			var id = parseInt(btn.getAttribute('data-id'), 10);
			if (favs.indexOf(id) !== -1) { btn.classList.add('is-fav'); }
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var list = getFavs();
				var idx = list.indexOf(id);
				if (idx === -1) {
					list.push(id);
					btn.classList.add('is-fav');
				} else {
					list.splice(idx, 1);
					btn.classList.remove('is-fav');
				}
				setFavs(list);
			});
		});

		/* Página Mi Cuenta: abas + contador de favoritos */
		document.querySelectorAll('.js-acc-tab').forEach(function (tab) {
			tab.addEventListener('click', function () {
				var pane = tab.getAttribute('data-pane');
				document.querySelectorAll('.js-acc-tab').forEach(function (t) { t.classList.toggle('is-active', t === tab); });
				document.querySelectorAll('.js-acc-pane').forEach(function (p) {
					p.classList.toggle('is-active', p.getAttribute('data-pane') === pane);
				});
			});
		});
		var favCount = document.querySelector('.js-fav-count');
		if (favCount) { favCount.textContent = getFavs().length; }

		/* Página de favoritos */
		var favGrid = document.querySelector('.js-favorites-grid');
		if (favGrid) {
			var ids = getFavs();
			var emptyMsg = document.querySelector('.js-favorites-empty');
			if (!ids.length) {
				if (emptyMsg) { emptyMsg.style.display = ''; }
			} else {
				post('espanol_favorites', { ids: ids }).then(function (res) {
					if (res.success && res.data.html) {
						favGrid.innerHTML = res.data.html;
					} else if (emptyMsg) {
						emptyMsg.style.display = '';
					}
				});
			}
		}

		/* Single: contagem de view + votos */
		var single = document.querySelector('.single-video-wrap');
		if (single) {
			var videoId = parseInt(single.getAttribute('data-video-id'), 10);

			post('espanol_view', { post_id: videoId });

			var votedKey = 'espanol_voted_' + videoId;
			single.querySelectorAll('.js-vote').forEach(function (btn) {
				btn.addEventListener('click', function () {
					if (localStorage.getItem(votedKey)) { return; }
					post('espanol_vote', { post_id: videoId, vote: btn.getAttribute('data-vote') }).then(function (res) {
						if (!res.success) { return; }
						localStorage.setItem(votedKey, '1');
						btn.classList.add('voted');
						var count = single.querySelector('.js-like-count');
						if (count) { count.textContent = res.data.likes; }
						var pct = single.querySelector('.js-like-pct');
						if (pct && res.data.percent !== null) { pct.textContent = res.data.percent + '% likes'; }
					});
				});
			});

			var share = single.querySelector('.js-share');
			if (share) {
				share.addEventListener('click', function () {
					var url = window.location.href;
					if (navigator.share) {
						navigator.share({ title: document.title, url: url });
					} else {
						navigator.clipboard.writeText(url).then(function () {
							share.querySelector('svg') && (share.style.color = 'var(--accent)');
						});
					}
				});
			}
		}
	});
})();
