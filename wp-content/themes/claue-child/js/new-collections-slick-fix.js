/**
 * Home New Collections: .jas-row flex collapses Slick list; also slidesToShow
 * must follow viewport (4 / 3 / 2) and never exceed product count.
 */
(function ($) {
	'use strict';

	function targetShow(count) {
		var w = window.innerWidth;
		var base;
		if (w <= 480) {
			base = 2;
		} else if (w <= 1024) {
			base = 3;
		} else {
			base = 4;
		}
		if (count > 0 && count < base) {
			return count;
		}
		return base;
	}

	function fixOne($slider) {
		if (!$slider.length || !$slider.hasClass('slick-initialized')) {
			return;
		}
		var slick;
		try {
			slick = $slider.slick('getSlick');
		} catch (e) {
			return;
		}
		if (!slick || !slick.options) {
			return;
		}
		var count = slick.slideCount || $slider.find('.slick-slide:not(.slick-cloned)').length;
		var next = targetShow(count);
		var cur = parseInt(slick.options.slidesToShow, 10) || 0;
		if (cur !== next) {
			$slider.slick('slickSetOption', 'slidesToShow', next, true);
			return;
		}
		$slider.slick('setPosition');
	}

	function run() {
		$('.new-available .slick-initialized').each(function () {
			fixOne($(this));
		});
	}

	var resizeTimer;
	$(window).on('resize', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(run, 150);
	});

	$(run);
	$(window).on('load', run);
	setTimeout(run, 400);
	setTimeout(run, 1200);
})(jQuery);
