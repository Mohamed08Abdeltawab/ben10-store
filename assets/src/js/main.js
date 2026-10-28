/**
 * Main Theme Interactions & Frontpage Header Scroll Listener
 * Ashrafco Child Theme
 */
document.addEventListener('DOMContentLoaded', function () {
	const header = document.getElementById('test-theme-site-header');
	if (header && header.classList.contains('frontpage-header')) {
		const scrollThreshold = 50;

		function checkScroll() {
			const scrollY = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
			if (scrollY > scrollThreshold) {
				header.classList.add('is-scrolled', 'header-scrolled');
				header.style.backgroundColor = 'rgba(2, 6, 23, 0.8)';
				header.style.backdropFilter = 'blur(16px)';
				header.style.webkitBackdropFilter = 'blur(16px)';
			} else {
				header.classList.remove('is-scrolled', 'header-scrolled');
				header.style.backgroundColor = 'rgba(2, 6, 23, 0.4)';
				header.style.backdropFilter = 'blur(12px)';
				header.style.webkitBackdropFilter = 'blur(12px)';
			}
		}

		window.addEventListener('scroll', checkScroll, { passive: true });
		checkScroll();
	}

	// =========================================================================
	// Best Sellers Horizontal Carousel Controller (RTL & Touch Optimized)
	// =========================================================================
	const bsTrack = document.getElementById('test-theme-bestsellers-track');
	const bsPrev = document.getElementById('test-theme-bestsellers-prev');
	const bsNext = document.getElementById('test-theme-bestsellers-next');

	if (bsTrack) {
		const getScrollStep = () => {
			const firstCard = bsTrack.querySelector('.best-sellers-card-item') || bsTrack.querySelector('.ref-product-card');
			const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 220;
			const isDesktop = window.innerWidth >= 768;
			return isDesktop ? (cardWidth * 2) + 16 : cardWidth + 12;
		};

		// In RTL layout (dir="rtl"):
		// Scrolling left (towards negative values in modern browser CSSOM) reveals NEXT cards.
		// Scrolling right (towards 0 / positive) reveals PREVIOUS cards.
		if (bsNext) {
			bsNext.addEventListener('click', function () {
				const step = getScrollStep();
				bsTrack.scrollBy({ left: -step, behavior: 'smooth' });
			});
		}

		if (bsPrev) {
			bsPrev.addEventListener('click', function () {
				const step = getScrollStep();
				bsTrack.scrollBy({ left: step, behavior: 'smooth' });
			});
		}

		// Optional Keyboard navigation when carousel track is focused
		bsTrack.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') {
				e.preventDefault();
				const step = getScrollStep();
				bsTrack.scrollBy({ left: -step, behavior: 'smooth' });
			} else if (e.key === 'ArrowRight') {
				e.preventDefault();
				const step = getScrollStep();
				bsTrack.scrollBy({ left: step, behavior: 'smooth' });
			}
		});
	}

	// =========================================================================
	// Native WooCommerce Product Card & Offers Countdown Timer Engine
	// =========================================================================
	function initAshrafcoCountdownTimers() {
		const timerElements = document.querySelectorAll('[data-countdown-end]');
		if (!timerElements || timerElements.length === 0) {
			if (window._ashrafcoCountdownInterval) {
				clearInterval(window._ashrafcoCountdownInterval);
				window._ashrafcoCountdownInterval = null;
			}
			return;
		}

		function pad(num) {
			return String(num).padStart(2, '0');
		}

		function tick() {
			const now = Math.floor(Date.now() / 1000);
			let activeCount = 0;

			timerElements.forEach(function (el) {
				const endRaw = el.getAttribute('data-countdown-end');
				if (!endRaw) {
					el.style.display = 'none';
					return;
				}

				const endTimestamp = parseInt(endRaw, 10);
				if (!endTimestamp || isNaN(endTimestamp)) {
					el.style.display = 'none';
					return;
				}

				// Normalize seconds / milliseconds
				const endSec = endTimestamp > 1e11 ? Math.floor(endTimestamp / 1000) : endTimestamp;
				const diff = endSec - now;

				if (diff <= 0) {
					// Sale has expired: stop presenting product as actively discounted
					el.style.display = 'none';
					const card = el.closest('.ref-product-card, .promo-product-card');
					if (card) {
						card.classList.remove('has-active-discount');
						const discountBadge = card.querySelector('.ref-discount-badge');
						if (discountBadge) {
							discountBadge.style.display = 'none';
						}
						const oldPrice = card.querySelector('.ref-old-price');
						if (oldPrice) {
							oldPrice.style.display = 'none';
						}
						const promoRow = card.querySelector('.ref-promo-row');
						if (promoRow) {
							promoRow.style.display = 'none';
						}
						const currentPrice = card.querySelector('.ref-current-price');
						const regPriceHtml = card.getAttribute('data-regular-price-html');
						if (currentPrice && regPriceHtml) {
							currentPrice.innerHTML = regPriceHtml;
							currentPrice.classList.remove('text-red-600', 'font-black', 'is-discounted');
						}
					}
					return;
				}

				activeCount++;
				const days = Math.floor(diff / 86400);
				const hours = Math.floor((diff % 86400) / 3600);
				const minutes = Math.floor((diff % 3600) / 60);
				const seconds = diff % 60;

				const dEl = el.querySelector('.timer-days');
				const hEl = el.querySelector('.timer-hours');
				const mEl = el.querySelector('.timer-minutes');
				const sEl = el.querySelector('.timer-seconds');

				if (dEl) dEl.textContent = pad(days);
				if (hEl) hEl.textContent = pad(hours);
				if (mEl) mEl.textContent = pad(minutes);
				if (sEl) sEl.textContent = pad(seconds);
			});

			if (activeCount === 0 && window._ashrafcoCountdownInterval) {
				clearInterval(window._ashrafcoCountdownInterval);
				window._ashrafcoCountdownInterval = null;
			}
		}

		// Initial tick immediately
		tick();

		// Avoid duplicate intervals
		if (window._ashrafcoCountdownInterval) {
			clearInterval(window._ashrafcoCountdownInterval);
		}
		window._ashrafcoCountdownInterval = setInterval(tick, 1000);
	}

	window.initAshrafcoCountdownTimers = initAshrafcoCountdownTimers;
	initAshrafcoCountdownTimers();
});

