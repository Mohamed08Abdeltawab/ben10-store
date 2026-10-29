/**
 * Header interactions: Live AJAX Search & Mini-Cart Toggle
 * Ashrafco Child Theme
 */

document.addEventListener('DOMContentLoaded', function () {
	// =========================================================================
	// 1. AJAX Live Search with Debounce & Animated Keyword Seeds
	// =========================================================================
	const searchWrapper = document.getElementById('test-theme-search-wrapper');
	const searchInput   = document.getElementById('noon-search-input') || document.getElementById('test-theme-search-input');
	const searchDropdown= document.getElementById('test-theme-search-dropdown');
	const searchResults = document.getElementById('test-theme-search-results');
	const searchSpinner = document.getElementById('test-theme-search-spinner');
	const seedOverlay   = document.getElementById('search-seed-overlay') || document.getElementById('test-theme-search-seeds');
	const seedTextEl    = document.getElementById('search-seed-text') || document.getElementById('test-theme-seed-text');

	// Rotating seed keywords for search bar (calm, eye-comfortable 3s transitions)
	const seedKeywords = [
		'ساعات رجالية فاخرة...',
		'ساعات أوتوماتيك وميكانيكية...',
		'ساعات رياضية مقاومة للماء...',
		'ساعات نسائية أنيقة...',
		'أحدث إصدارات وتشكيلات Ben10...',
		'إكسسوارات وسوارات أصلية...'
	];

	let seedIndex = 0;
	let seedInterval = null;
	let isInputFocused = false;

	function hideSeedOverlay() {
		if (!seedOverlay) return;
		seedOverlay.classList.add('opacity-0', 'pointer-events-none', 'is-hidden');
		seedOverlay.classList.remove('opacity-100');
		seedOverlay.style.opacity = '0';
		seedOverlay.style.visibility = 'hidden';
	}

	function showSeedOverlay() {
		if (!seedOverlay) return;
		if (isInputFocused || (searchInput && searchInput.value.trim().length > 0)) {
			return;
		}
		seedOverlay.classList.remove('opacity-0', 'pointer-events-none', 'is-hidden');
		seedOverlay.classList.add('opacity-100');
		seedOverlay.style.opacity = '1';
		seedOverlay.style.visibility = 'visible';
	}

	function rotateSeed() {
		if (!seedTextEl || isInputFocused || (searchInput && searchInput.value.trim().length > 0)) {
			return;
		}

		// Phase 1: Smoothly slide up & fade out current keyword
		seedTextEl.style.transition = 'opacity 350ms cubic-bezier(0.4, 0, 0.2, 1), transform 350ms cubic-bezier(0.4, 0, 0.2, 1)';
		seedTextEl.style.opacity = '0';
		seedTextEl.style.transform = 'translateY(-6px)';

		setTimeout(function () {
			if (isInputFocused || (searchInput && searchInput.value.trim().length > 0)) return;

			// Phase 2: Switch keyword while invisible and position below
			seedIndex = (seedIndex + 1) % seedKeywords.length;
			seedTextEl.textContent = seedKeywords[seedIndex];
			seedTextEl.style.transition = 'none';
			seedTextEl.style.transform = 'translateY(6px)';
			seedTextEl.style.opacity = '0';

			// Force reflow
			void seedTextEl.offsetWidth;

			// Phase 3: Glide smoothly into place with a calm ease-out
			seedTextEl.style.transition = 'opacity 400ms cubic-bezier(0.16, 1, 0.3, 1), transform 400ms cubic-bezier(0.16, 1, 0.3, 1)';
			seedTextEl.style.transform = 'translateY(0)';
			seedTextEl.style.opacity = '1';
		}, 350);
	}

	function startSeedRotation() {
		if (seedInterval) clearInterval(seedInterval);
		seedInterval = setInterval(rotateSeed, 3000);
	}

	function stopSeedRotation() {
		if (seedInterval) {
			clearInterval(seedInterval);
			seedInterval = null;
		}
	}

	if (seedTextEl && seedOverlay) {
		startSeedRotation();

		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				stopSeedRotation();
			} else if (!isInputFocused && (!searchInput || searchInput.value.trim().length === 0)) {
				showSeedOverlay();
				startSeedRotation();
			}
		});
	}

	function initSearch(wrapperId, inputId, dropdownId, resultsId) {
		const searchWrapper = document.getElementById(wrapperId);
		const searchInput   = document.getElementById(inputId);
		const searchDropdown= document.getElementById(dropdownId);
		const searchResults = document.getElementById(resultsId);

		if (!searchWrapper || !searchInput || !searchDropdown || !searchResults) return;

		let debounceTimer = null;
		let currentController = null;

		searchInput.addEventListener('input', function () {
			const query = this.value.trim();
			clearTimeout(debounceTimer);

			if (query.length < 2) {
				searchDropdown.classList.add('hidden');
				searchResults.innerHTML = '';
				return;
			}

			debounceTimer = setTimeout(function () {
				if (currentController) {
					currentController.abort();
				}
				currentController = new AbortController();

				if (typeof testThemeData === 'undefined' || !testThemeData.ajaxUrl) return;

				const url = new URL(testThemeData.ajaxUrl);
				url.searchParams.append('action', 'test_theme_ajax_search');
				url.searchParams.append('nonce', testThemeData.searchNonce);
				url.searchParams.append('term', query);

				fetch(url, { signal: currentController.signal })
					.then((response) => response.json())
					.then((data) => {
						if (data && data.success && Array.isArray(data.data)) {
							renderSearchResults(data.data, query);
						} else {
							renderEmptyResults(query);
						}
					})
					.catch((error) => {
						if (error.name !== 'AbortError') {
							console.error('AJAX Search error:', error);
						}
					});
			}, 300);
		});

		function renderSearchResults(products, query) {
			if (products.length === 0) {
				renderEmptyResults(query);
				return;
			}

			let html = '<div class="px-3 py-1.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">المنتجات المقترحة</div>';
			products.forEach((product) => {
				html += `
					<a href="${product.permalink}" class="flex items-center gap-3 p-2.5 hover:bg-slate-50 rounded-xl transition-colors group">
						<img src="${product.thumbnail}" alt="${product.title}" class="w-12 h-12 rounded-lg object-cover bg-slate-100 flex-shrink-0 border border-slate-100">
						<div class="flex-1 min-w-0 text-right">
							<h4 class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors truncate">${product.title}</h4>
							<div class="text-xs font-bold text-primary mt-0.5">${product.price}</div>
						</div>
						<svg class="w-4 h-4 text-slate-300 group-hover:text-primary transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
						</svg>
					</a>
				`;
			});

			searchResults.innerHTML = html;
			searchDropdown.classList.remove('hidden');
		}

		function renderEmptyResults(query) {
			searchResults.innerHTML = `
				<div class="py-6 px-4 text-center">
					<p class="text-sm text-slate-500 font-medium">لا توجد نتائج مطابقة لـ "<span class="font-bold text-slate-800">${escapeHtml(query)}</span>"</p>
					<span class="text-xs text-slate-400 mt-1 block">تأكد من كتابة الكلمات بشكل صحيح أو جرب كلمات أخرى</span>
				</div>
			`;
			searchDropdown.classList.remove('hidden');
		}

		function escapeHtml(str) {
			const div = document.createElement('div');
			div.textContent = str;
			return div.innerHTML;
		}

		// Close search on Escape key press
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				searchDropdown.classList.add('hidden');
			}
		});

		// Close search dropdown on click outside
		document.addEventListener('click', function (e) {
			if (!searchWrapper.contains(e.target)) {
				searchDropdown.classList.add('hidden');
			}
		});
	}

	// Initialize search on mobile and desktop
	initSearch('test-theme-search-wrapper', 'test-theme-search-input', 'test-theme-search-dropdown', 'test-theme-search-results');
	initSearch('desktop-search-wrapper', 'desktop-theme-search-input', 'desktop-search-dropdown', 'desktop-search-results');

	// =========================================================================
	// 2. Interactive AJAX Mini-Cart Toggle & Click Outside
	// =========================================================================
	const cartContainer = document.getElementById('test-theme-mini-cart-container');
	const cartTrigger   = document.getElementById('test-theme-cart-trigger');
	const cartDropdown  = document.getElementById('test-theme-cart-dropdown');

	if (cartContainer && cartTrigger && cartDropdown) {
		function clampCartDropdown() {
			cartDropdown.style.transform = '';
			const rect = cartDropdown.getBoundingClientRect();
			const viewportWidth = window.innerWidth;
			const padding = 16;

			// Clamping right boundary
			if (rect.right > viewportWidth - padding) {
				const overflow = rect.right - (viewportWidth - padding);
				cartDropdown.style.transform = `translateX(-${overflow}px)`;
			}
			// Clamping left boundary
			if (rect.left < padding) {
				const underflow = padding - rect.left;
				cartDropdown.style.transform = `translateX(${underflow}px)`;
			}
		}

		// Toggle dropdown on trigger click
		cartTrigger.addEventListener('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			const isHidden = cartDropdown.classList.contains('hidden');
			if (isHidden) {
				cartDropdown.classList.remove('hidden');
				cartTrigger.setAttribute('aria-expanded', 'true');
				clampCartDropdown();
			} else {
				cartDropdown.classList.add('hidden');
				cartTrigger.setAttribute('aria-expanded', 'false');
				cartDropdown.style.transform = '';
			}
		});

		// Close mini-cart dropdown on click outside
		document.addEventListener('click', function (e) {
			if (!cartContainer.contains(e.target)) {
				cartDropdown.classList.add('hidden');
				cartTrigger.setAttribute('aria-expanded', 'false');
				cartDropdown.style.transform = '';
			}
		});

		// Dynamic clamp adjustment on window resize
		window.addEventListener('resize', function () {
			if (!cartDropdown.classList.contains('hidden')) {
				clampCartDropdown();
			}
		});
	}

	// =========================================================================
	// 3. Dynamic Wishlist Heart Visual State & Real-Time Sync (Header + Cards)
	// =========================================================================
	const wishlistBtn   = document.getElementById('test-theme-wishlist-btn') || document.getElementById('header-wishlist-btn');
	const wishlistIcon  = document.getElementById('test-theme-wishlist-icon') || (wishlistBtn ? wishlistBtn.querySelector('svg') : null);
	const wishlistBadge = document.getElementById('test-theme-wishlist-count');

	function getSavedWishlistIds() {
		const storageKeys = ['ashrafco_wishlist', 'wishlist', 'yith_wcwl_products', 'tinvwl_wishlist'];
		for (let i = 0; i < storageKeys.length; i++) {
			try {
				const raw = localStorage.getItem(storageKeys[i]);
				if (raw) {
					const parsed = JSON.parse(raw);
					if (Array.isArray(parsed)) return parsed.map(String);
					if (typeof parsed === 'object' && parsed !== null) return Object.keys(parsed).map(String);
				}
			} catch (e) {}
		}

		// Also check cookies
		const cookieMatch = document.cookie.match(/(?:^|;\s*)(?:ashrafco_wishlist|yith_wcwl_products)=([^;]+)/);
		if (cookieMatch) {
			try {
				const parsed = JSON.parse(decodeURIComponent(cookieMatch[1]));
				if (Array.isArray(parsed)) return parsed.map(String);
				if (typeof parsed === 'object' && parsed !== null) return Object.keys(parsed).map(String);
			} catch (e) {}
		}

		return [];
	}

	function saveWishlistIds(ids) {
		const uniqueIds = Array.from(new Set(ids.map(String)));
		try {
			localStorage.setItem('ashrafco_wishlist', JSON.stringify(uniqueIds));
		} catch (e) {}
		document.cookie = 'ashrafco_wishlist=' + encodeURIComponent(JSON.stringify(uniqueIds)) + '; path=/; max-age=31536000; SameSite=Lax';
		return uniqueIds;
	}

	function getWishlistCount() {
		try {
			const raw = localStorage.getItem('ashrafco_wishlist');
			if (raw !== null) {
				const parsed = JSON.parse(raw);
				if (Array.isArray(parsed)) return parsed.length;
			}
		} catch (e) {}

		const ids = getSavedWishlistIds();
		if (ids.length > 0) return ids.length;

		// Initial data attribute from server
		const btn = document.getElementById('mobile-header-wishlist-btn') || document.getElementById('desktop-header-wishlist-btn') || document.getElementById('test-theme-wishlist-btn');
		if (btn && btn.dataset.wishlistCount !== undefined) {
			return parseInt(btn.dataset.wishlistCount, 10) || 0;
		}

		return 0;
	}

	function formatWishlistCount(count) {
		const num = (typeof count === 'number') ? count : (parseInt(count, 10) || 0);
		if (num <= 0) return '0';
		if (num > 9) return '+9';
		return String(num);
	}

	function applyHeartIconStyles(svgEl, isFilled) {
		if (!svgEl) return;

		svgEl.classList.remove('stroke-[#f87171]', 'fill-[#d90429]', 'fill-white', 'fill-none', 'fill-black', 'text-black', 'text-slate-400');

		if (isFilled) {
			svgEl.setAttribute('stroke', '#FF171F');
			svgEl.setAttribute('fill', '#FF171F');
			svgEl.style.stroke = '#FF171F';
			svgEl.style.fill = '#FF171F';
			svgEl.classList.add('fill-[#FF171F]', 'stroke-[#FF171F]');
		} else {
			svgEl.setAttribute('stroke', '#374151');
			svgEl.setAttribute('fill', 'none');
			svgEl.style.stroke = '#374151';
			svgEl.style.fill = 'none';
			svgEl.classList.add('stroke-[#374151]', 'fill-none');
		}
		svgEl.setAttribute('stroke-width', '1.8');
	}

	function updateWishlistHeartUI(count) {
		const num = (typeof count === 'number') ? count : (parseInt(count, 10) || 0);
		const formatted = formatWishlistCount(num);

		// Update all wishlist count badges
		const badges = document.querySelectorAll('.header-wishlist-count-badge, #mobile-header-wishlist-badge, #desktop-header-wishlist-badge, #test-theme-wishlist-count');
		badges.forEach(function (badge) {
			badge.textContent = formatted;
			if (num > 0) {
				badge.classList.remove('hidden');
			} else {
				badge.classList.add('hidden');
			}
		});

		// Update dataset count on all buttons
		const wishlistBtns = document.querySelectorAll('#mobile-header-wishlist-btn, #desktop-header-wishlist-btn, #test-theme-wishlist-btn, #header-wishlist-btn');
		wishlistBtns.forEach(function (btn) {
			btn.dataset.wishlistCount = num;
		});

		// Mobile wishlist heart: solid red fill
		const mobileHeart = document.querySelector('#mobile-header-wishlist-btn svg');
		if (mobileHeart) {
			mobileHeart.style.fill = '#ff171f';
			mobileHeart.style.color = '#ff171f';
		}

		// Desktop wishlist heart: clean white outline matching Image 2
		const desktopHeart = document.querySelector('#desktop-header-wishlist-btn svg');
		if (desktopHeart) {
			desktopHeart.style.stroke = '#ffffff';
			desktopHeart.style.color = '#ffffff';
			desktopHeart.style.fill = 'none';
		}
	}

	function syncProductCardsUI() {
		const savedIds = getSavedWishlistIds();
		const toggles = document.querySelectorAll('.test-theme-wishlist-toggle');

		toggles.forEach(function (btn) {
			const pid = String(btn.dataset.productId || '');
			if (!pid) return;

			const isSaved = savedIds.includes(pid);
			btn.classList.toggle('is-active', isSaved);

			const svg = btn.querySelector('svg');
			if (svg) {
				applyHeartIconStyles(svg, isSaved);
			}
		});
	}

	function syncWishlistState(customCount) {
		const count = (typeof customCount === 'number') ? customCount : getWishlistCount();
		updateWishlistHeartUI(count);
		syncProductCardsUI();
	}

	// Initial sync on page load
	syncWishlistState();

	// Handle clicking on product card wishlist toggle hearts
	document.addEventListener('click', function (e) {
		const toggleBtn = e.target.closest('.test-theme-wishlist-toggle');
		if (!toggleBtn) return;

		// If clicked inside the dedicated wishlist page grid, allow page-wishlist.php handler to manage removal & animation
		if (toggleBtn.closest('#wishlist-products-grid')) {
			return;
		}

		e.preventDefault();
		e.stopPropagation();

		const productId = String(toggleBtn.dataset.productId || '');
		if (!productId) return;

		let savedIds = getSavedWishlistIds();
		const index = savedIds.indexOf(productId);
		let isAdded = false;

		if (index > -1) {
			savedIds.splice(index, 1);
			isAdded = false;
		} else {
			savedIds.push(productId);
			isAdded = true;
		}

		saveWishlistIds(savedIds);
		syncWishlistState(savedIds.length);

		// Dispatch custom events for any external listeners
		const eventDetail = { count: savedIds.length, productId: productId, action: isAdded ? 'added' : 'removed' };
		document.dispatchEvent(new CustomEvent('wishlist_updated', { detail: eventDetail }));
		document.dispatchEvent(new CustomEvent(isAdded ? 'added_to_wishlist' : 'removed_from_wishlist', { detail: eventDetail }));

		// Persist change via server AJAX
		if (window.testThemeData && window.testThemeData.ajaxUrl && window.testThemeData.wishlistNonce) {
			const formData = new FormData();
			formData.append('action', 'ashrafco_toggle_wishlist');
			formData.append('nonce', window.testThemeData.wishlistNonce);
			formData.append('product_id', productId);

			fetch(window.testThemeData.ajaxUrl, {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			})
			.then(function (r) { return r.json(); })
			.then(function (res) {
				if (res && res.success && res.data && Array.isArray(res.data.ids)) {
					saveWishlistIds(res.data.ids);
					syncWishlistState(res.data.ids.length);
				}
			})
			.catch(function () {});
		}
	});

	// Listen for native custom events dispatched by external scripts / plugins
	const wishlistEvents = [
		'wishlist_updated',
		'added_to_wishlist',
		'removed_from_wishlist',
		'ashrafco_wishlist_updated',
		'yith_wcwl_reload_fragments',
		'tinvwl_wishlist_mark_products',
		'tinvwl_wishlist_product_added',
		'tinvwl_wishlist_product_removed'
	];

	wishlistEvents.forEach(function (eventName) {
		document.addEventListener(eventName, function (e) {
			const count = (e.detail && typeof e.detail.count === 'number') ? e.detail.count : undefined;
			syncWishlistState(count);
		});
		window.addEventListener(eventName, function (e) {
			const count = (e.detail && typeof e.detail.count === 'number') ? e.detail.count : undefined;
			syncWishlistState(count);
		});
	});

	// Listen for jQuery events if WooCommerce / plugin scripts trigger them
	if (window.jQuery) {
		window.jQuery(document).on(
			'added_to_wishlist removed_from_wishlist wishlist_updated yith_wcwl_fragments_refreshed tinvwl_wishlist_mark_products',
			function (event, response) {
				let count;
				if (response && typeof response.count === 'number') {
					count = response.count;
				} else if (response && typeof response.wishlist_count === 'number') {
					count = response.wishlist_count;
				}
				syncWishlistState(count);
			}
		);
	}

	// Real-time synchronization across browser tabs
	window.addEventListener('storage', function (e) {
		if (e.key && ['ashrafco_wishlist', 'wishlist', 'yith_wcwl_products', 'tinvwl_wishlist'].includes(e.key)) {
			syncWishlistState();
		}
	});

});
