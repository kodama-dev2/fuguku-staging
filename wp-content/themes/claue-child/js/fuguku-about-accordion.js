/**
 * Footer columns About / Services / Others: mobile heading + underline,
 * tap to expand the rest (nav menu and/or icon lists).
 */
(function () {
	'use strict';

	var MQ = 767;
	var TITLES = { About: 1, Services: 1, Others: 1 };
	var CLS = 'fuguku-footer-accordion';

	function headingText(el) {
		return (el.textContent || '').replace(/\s+/g, ' ').trim();
	}

	function kidsOf(con) {
		var inner = con.querySelector(':scope > .e-con-inner');
		return inner ? [].slice.call(inner.children) : [].slice.call(con.children);
	}

	function firstHeading(kids) {
		return kids.find(function (k) {
			return k.classList.contains('elementor-widget-heading');
		});
	}

	function hasExpandable(con, kids) {
		if (con.closest('.fuguku-services-grid')) {
			return false;
		}
		return kids.some(function (k) {
			if (k.classList.contains('elementor-widget-heading')) {
				return false;
			}
			return (
				k.classList.contains('elementor-widget-nav-menu') ||
				k.classList.contains('elementor-widget-icon-list') ||
				k.classList.contains('e-con') ||
				!!k.querySelector('.elementor-widget-nav-menu, .elementor-widget-icon-list')
			);
		});
	}

	function mark() {
		[].forEach.call(document.querySelectorAll('.e-con'), function (con) {
			if (con.classList.contains(CLS) || con.classList.contains('fuguku-about-accordion')) {
				con.classList.add(CLS);
				return;
			}
			var kids = kidsOf(con);
			if (kids.length < 2) {
				return;
			}
			var head = firstHeading(kids);
			if (!head || !TITLES[headingText(head)]) {
				return;
			}
			if (!hasExpandable(con, kids)) {
				return;
			}
			con.classList.add(CLS);
			con.classList.add('fuguku-about-accordion');
		});
	}

	function bind(con) {
		if (con.getAttribute('data-fuguku-footer-bound') === '1') {
			return;
		}
		var head = firstHeading(kidsOf(con));
		if (!head) {
			return;
		}
		con.setAttribute('data-fuguku-footer-bound', '1');
		head.setAttribute('role', 'button');
		head.setAttribute('tabindex', '0');
		head.setAttribute('aria-expanded', 'false');

		function toggle(e) {
			if (window.innerWidth > MQ) {
				return;
			}
			e.preventDefault();
			var open = con.classList.toggle('is-open');
			head.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		head.addEventListener('click', toggle);
		head.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				toggle(e);
			}
		});
	}

	function run() {
		mark();
		[].forEach.call(document.querySelectorAll('.' + CLS + ', .fuguku-about-accordion'), bind);
		if (window.innerWidth > MQ) {
			[].forEach.call(document.querySelectorAll('.' + CLS + ', .fuguku-about-accordion'), function (con) {
				con.classList.remove('is-open');
				var head = firstHeading(kidsOf(con));
				if (head) {
					head.setAttribute('aria-expanded', 'false');
				}
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', run);
	} else {
		run();
	}
	window.addEventListener('load', run);
	window.addEventListener('resize', run);
})();
