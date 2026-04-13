/**
 * New Collections (.new-available): unslick → remove OOS slides from DOM → slick( opts ) again.
 * Avoids slickFilter + wrong slide width on first paint.
 */
(function ($) {
	'use strict';

	var ROOT = '.new-available';
	var SLIDER = '.slick-slider';
	var CARD = '.product';
	var OOS = 'outofstock';

	function getSlickOptions($s) {
		var inst = $s.data('slick');
		if (inst && inst.options) {
			return $.extend(true, {}, inst.options);
		}
		return null;
	}

	function stripOOSMarkup($slider) {
		// Before unslick (should not happen here) or unusual markup
		if ($slider.find('.slick-slide').length) {
			$slider.find('.slick-slide:not(.slick-cloned)').each(function () {
				var $slide = $(this);
				var $p = $slide.find(CARD).first();
				if (!$p.length && $slide.is(CARD)) {
					$p = $slide;
				}
				if ($p.length && $p.hasClass(OOS)) {
					$slide.remove();
				}
			});
			return;
		}
		// After unslick: slides are usually direct children of the slider root
		$slider.children().each(function () {
			var $wrap = $(this);
			var $p = $wrap.find(CARD).first();
			if (!$p.length && $wrap.is(CARD)) {
				$p = $wrap;
			}
			if ($p.length && $p.hasClass(OOS)) {
				$wrap.remove();
			}
		});
	}

	function rebuildOne($slider, attempt) {
		attempt = attempt || 0;
		if (!$slider.length) {
			return;
		}
		if (!$slider.hasClass('slick-initialized')) {
			if (attempt < 50) {
				setTimeout(function () {
					rebuildOne($slider, attempt + 1);
				}, 80);
			}
			return;
		}

		var w = $slider.width();
		if (w < 40 && attempt < 50) {
			setTimeout(function () {
				rebuildOne($slider, attempt + 1);
			}, 80);
			return;
		}

		var opts = getSlickOptions($slider);
		if (!opts) {
			return;
		}

		try {
			$slider.slick('unslick');
		} catch (e) {
			return;
		}

		stripOOSMarkup($slider);

		try {
			$slider.slick(opts);
		} catch (e2) {
			return;
		}
	}

	function run() {
		$(ROOT).each(function () {
			var $root = $(this);
			var $sliders = $root.is(SLIDER) ? $root : $root.find(SLIDER);
			$sliders.each(function () {
				rebuildOne($(this), 0);
			});
		});
	}

	function scheduleRun() {
		setTimeout(run, 0);
	}

	if (document.readyState === 'complete') {
		scheduleRun();
	} else {
		$(window).on('load', scheduleRun);
	}
})(jQuery);
