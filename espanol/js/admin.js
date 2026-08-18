/* Espanol — JS do admin (uploader de mídia e color picker). */
(function ($) {
	'use strict';

	$(function () {
		if ($.fn.wpColorPicker) {
			$('.espanol-color').wpColorPicker();
		}

		$(document).on('click', '.espanol-media-upload', function (e) {
			e.preventDefault();
			var wrap = $(this).closest('td, .form-field');
			var frame = wp.media({ title: 'Escolher imagem', multiple: false, library: { type: 'image' } });

			frame.on('select', function () {
				var url = frame.state().get('selection').first().toJSON().url;
				wrap.find('.espanol-media-value').val(url);
				wrap.find('.espanol-media-preview').attr('src', url).show();
				wrap.find('.espanol-media-remove').show();
			});

			frame.open();
		});

		$(document).on('click', '.espanol-media-remove', function (e) {
			e.preventDefault();
			var wrap = $(this).closest('td, .form-field');
			wrap.find('.espanol-media-value').val('');
			wrap.find('.espanol-media-preview').hide();
			$(this).hide();
		});
	});
})(jQuery);
