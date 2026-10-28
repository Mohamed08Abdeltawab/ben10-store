/**
 * Ashrafco / A & S E-Commerce Modern Footer Interactions
 *
 * Mobile-first accordion handler with strict single-open constraint,
 * keyboard accessibility, and responsive state transitions.
 *
 * @package Ashrafco
 * @since 1.0.0
 */
(function () {
	'use strict';

	function initFooterAccordions() {
		var footer = document.getElementById('test-theme-site-footer');
		if (!footer) {
			return;
		}

		var accordionItems = footer.querySelectorAll('.as-accordion-item');
		if (!accordionItems.length) {
			return;
		}

		function isMobile() {
			return window.innerWidth < 768;
		}

		function closeAllAccordions() {
			accordionItems.forEach(function (item) {
				var trigger = item.querySelector('.as-accordion-trigger');
				var panel = item.querySelector('.as-accordion-panel');
				if (trigger) {
					trigger.setAttribute('aria-expanded', 'false');
				}
				if (panel) {
					panel.classList.remove('is-open');
				}
				item.classList.remove('is-open');
			});
		}

		function toggleAccordion(item) {
			if (!isMobile()) {
				return;
			}

			var trigger = item.querySelector('.as-accordion-trigger');
			var panel = item.querySelector('.as-accordion-panel');
			if (!trigger || !panel) {
				return;
			}

			var isCurrentlyOpen = trigger.getAttribute('aria-expanded') === 'true';

			// Always enforce single-accordion open on mobile
			closeAllAccordions();

			if (!isCurrentlyOpen) {
				trigger.setAttribute('aria-expanded', 'true');
				panel.classList.add('is-open');
				item.classList.add('is-open');
			}
		}

		accordionItems.forEach(function (item) {
			var trigger = item.querySelector('.as-accordion-trigger');
			if (!trigger) {
				return;
			}

			trigger.addEventListener('click', function (e) {
				if (!isMobile()) {
					return;
				}
				e.preventDefault();
				toggleAccordion(item);
			});

			trigger.addEventListener('keydown', function (e) {
				if (!isMobile()) {
					return;
				}
				if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
					e.preventDefault();
					toggleAccordion(item);
				}
			});
		});

		// Responsive resize watcher
		var resizeTimeout;
		window.addEventListener('resize', function () {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(function () {
				if (!isMobile()) {
					accordionItems.forEach(function (item) {
						var trigger = item.querySelector('.as-accordion-trigger');
						var panel = item.querySelector('.as-accordion-panel');
						if (trigger) {
							trigger.setAttribute('aria-expanded', 'true');
						}
						if (panel) {
							panel.classList.remove('is-open');
						}
						item.classList.remove('is-open');
					});
				} else {
					closeAllAccordions();
				}
			}, 150);
		}, { passive: true });
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initFooterAccordions);
	} else {
		initFooterAccordions();
	}
})();
