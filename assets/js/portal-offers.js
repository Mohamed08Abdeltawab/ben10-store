/**
 * Ashrafco Store - Dynamic Native WooCommerce Offers Page Controller
 *
 * Handles:
 * 1. Live countdown timers integration with universal countdown engine
 * 2. Smooth scrolling to catalog section
 * 3. Visual feedback on WooCommerce AJAX add-to-cart actions
 *
 * @package Ashrafco
 * @version 3.0.0
 */

(function () {
	'use strict';

	/**
	 * 1. Smooth Scroll to Offers Catalog
	 */
	function initSmoothScroll() {
		var scrollTriggers = document.querySelectorAll('.hero-scroll-btn, a[href="#promotions-catalog"], a[href="#offers-catalog-grid"]');
		scrollTriggers.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				var target = document.getElementById('promotions-catalog') || document.getElementById('offers-catalog-grid');
				if (target) {
					e.preventDefault();
					var topPos = target.getBoundingClientRect().top + window.pageYOffset - 90;
					window.scrollTo({
						top: topPos,
						behavior: 'smooth'
					});
				}
			});
		});
	}

	/**
	 * 2. Add to Cart Button Feedback Integration
	 */
	function initAddToCartFeedback() {
		if (typeof window.jQuery === 'undefined') {
			return;
		}

		window.jQuery(document.body).on('adding_to_cart', function (event, $button) {
			if ($button && $button.hasClass('promo-add-to-cart-btn')) {
				$button.data('original-html', $button.html());
				$button.addClass('loading opacity-80 cursor-wait');
				var textSpan = $button.find('.btn-text');
				if (textSpan.length) {
					textSpan.text('جاري الإضافة...');
				}
			}
		});

		window.jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
			if ($button && $button.hasClass('promo-add-to-cart-btn')) {
				$button.removeClass('loading opacity-80 cursor-wait');
				$button.addClass('bg-emerald-600 border-emerald-600 text-white');
				var textSpan = $button.find('.btn-text');
				if (textSpan.length) {
					textSpan.text('تمت الإضافة للسلة ✓');
				}

				setTimeout(function () {
					$button.removeClass('bg-emerald-600 border-emerald-600');
					var orig = $button.data('original-html');
					if (orig) {
						$button.html(orig);
					} else if (textSpan.length) {
						textSpan.text('أضف إلى السلة');
					}
				}, 2200);
			}
		});
	}

	// Initialize on DOM ready
	function init() {
		initSmoothScroll();
		initAddToCartFeedback();
		if (typeof window.initAshrafcoCountdownTimers === 'function') {
			window.initAshrafcoCountdownTimers();
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

})();
