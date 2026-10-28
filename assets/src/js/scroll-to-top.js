/**
 * Ashrafco Modern Scroll To Top Interaction Handler
 *
 * Provides smooth scrolling and fluid visibility transitions when scrolling past 300px.
 *
 * @package Ashrafco
 * @since 1.0.0
 */
(function () {
	'use strict';

	function initScrollToTop() {
		var btn = document.getElementById('ast-scroll-top') || document.querySelector('.test-theme-scroll-top');
		if (!btn) {
			return;
		}

		var isVisible = false;
		var scrollThreshold = 300;

		function updateVisibility() {
			var currentScroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
			if (currentScroll > scrollThreshold) {
				if (!isVisible) {
					btn.classList.add('is-visible', 'ast-scroll-to-top-show', 'scale-100');
					btn.classList.remove('opacity-0', 'pointer-events-none', 'scale-90', 'translate-y-4');
					isVisible = true;
				}
			} else {
				if (isVisible) {
					btn.classList.remove('is-visible', 'ast-scroll-to-top-show', 'scale-100');
					btn.classList.add('opacity-0', 'pointer-events-none', 'scale-90');
					isVisible = false;
				}
			}
		}

		// Smooth scroll click handler
		btn.addEventListener('click', function (e) {
			e.preventDefault();
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});

		// Check on scroll with passive listener for maximum scroll performance
		window.addEventListener('scroll', updateVisibility, { passive: true });

		// Initial check on load
		updateVisibility();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initScrollToTop);
	} else {
		initScrollToTop();
	}
})();
