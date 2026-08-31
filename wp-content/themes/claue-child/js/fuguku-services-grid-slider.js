/**
 * Home .fuguku-services-grid: keep Elementor grid on desktop;
 * on mobile, native swipe + dots (scroll-snap, no Slick).
 */
(function () {
	'use strict';

	var MQ = 767;
	var ROOT = '.fuguku-services-grid';

	function innerOf(root) {
		return root.querySelector(':scope > .e-con-inner') || root;
	}

	function slidesOf(inner) {
		return inner.querySelectorAll(':scope > .e-con');
	}

	function removeDots(root) {
		var dots = root.querySelector('.fuguku-services-dots');
		if (dots) {
			dots.remove();
		}
	}

	function currentIndex(inner) {
		var w = inner.clientWidth || 1;
		return Math.round(inner.scrollLeft / w);
	}

	function addDots(root, inner, slides) {
		if (root.querySelector('.fuguku-services-dots')) {
			return;
		}
		var wrap = document.createElement('div');
		wrap.className = 'fuguku-services-dots';
		wrap.setAttribute('role', 'tablist');

		slides.forEach(function (slide, i) {
			var btn = document.createElement('button');
			btn.type = 'button';
			btn.setAttribute('aria-label', 'Slide ' + (i + 1));
			btn.addEventListener('click', function () {
				slide.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
			});
			wrap.appendChild(btn);
		});

		root.appendChild(wrap);

		function paint() {
			var idx = currentIndex(inner);
			[].forEach.call(wrap.children, function (el, i) {
				el.classList.toggle('is-active', i === idx);
			});
		}

		inner.addEventListener('scroll', paint, { passive: true });
		paint();
	}

	function run() {
		var root = document.querySelector(ROOT);
		if (!root) {
			return;
		}
		if (window.innerWidth > MQ) {
			removeDots(root);
			return;
		}
		var inner = innerOf(root);
		var slides = slidesOf(inner);
		if (slides.length < 2) {
			return;
		}
		addDots(root, inner, slides);
	}

	if (document.readyState === 'complete') {
		run();
	} else {
		window.addEventListener('load', run);
	}
	window.addEventListener('resize', run);
})();
