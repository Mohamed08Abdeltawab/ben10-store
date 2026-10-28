/**
 * Ashrafco Modern Responsive Wishlist Drawer & Bottom Sheet Controller
 *
 * Implements Desktop Right Drawer (100dvh, slides from right side)
 * and Mobile Bottom Sheet (< 768px, 90dvh max, drag handle).
 * Allows quick browsing of saved wishlist items, adding items directly
 * to the cart (which opens the Cart Drawer), and instant item removal.
 *
 * @package Ashrafco
 * @since 1.2.0
 */

(function () {
	'use strict';

	var drawerEl    = null;
	var backdropEl  = null;
	var panelEl     = null;
	var bodyEl      = null;
	var footerEl    = null;
	var closeBtn    = null;
	var isOpen      = false;

	/**
	 * Retrieve saved Wishlist product IDs from localStorage and Cookies
	 */
	function getSavedWishlistIds() {
		var storageKeys = ['ashrafco_wishlist', 'wishlist', 'yith_wcwl_products', 'tinvwl_wishlist'];
		for (var i = 0; i < storageKeys.length; i++) {
			try {
				var raw = localStorage.getItem(storageKeys[i]);
				if (raw) {
					var parsed = JSON.parse(raw);
					if (Array.isArray(parsed)) return parsed.map(String);
					if (typeof parsed === 'object' && parsed !== null) return Object.keys(parsed).map(String);
				}
			} catch (e) {}
		}

		var cookieMatch = document.cookie.match(/(?:^|;\s*)(?:ashrafco_wishlist|yith_wcwl_products)=([^;]+)/);
		if (cookieMatch) {
			try {
				var parsedCookie = JSON.parse(decodeURIComponent(cookieMatch[1]));
				if (Array.isArray(parsedCookie)) return parsedCookie.map(String);
				if (typeof parsedCookie === 'object' && parsedCookie !== null) return Object.keys(parsedCookie).map(String);
			} catch (e) {}
		}
		return [];
	}

	function saveWishlistIds(ids) {
		var unique = Array.from(new Set(ids.map(String)));
		try {
			localStorage.setItem('ashrafco_wishlist', JSON.stringify(unique));
		} catch (e) {}
		document.cookie = 'ashrafco_wishlist=' + encodeURIComponent(JSON.stringify(unique)) + '; path=/; max-age=31536000; SameSite=Lax';
		return unique;
	}

	/**
	 * Update Wishlist counter badges across header and drawer
	 */
	function updateAllWishlistCounts(count) {
		var num = parseInt(count, 10);
		if (isNaN(num)) num = 0;

		var drawerCountEls = document.querySelectorAll('.ashrafco-wishlist-items-count');
		drawerCountEls.forEach(function (el) {
			el.textContent = String(num);
		});

		var badgeEls = document.querySelectorAll('.header-wishlist-count-badge, #mobile-header-wishlist-badge, #desktop-header-wishlist-badge');
		badgeEls.forEach(function (el) {
			el.textContent = String(num);
			if (num > 0) {
				el.classList.remove('hidden');
			} else {
				el.classList.add('hidden');
			}
		});

		var btns = document.querySelectorAll('#mobile-header-wishlist-btn, #desktop-header-wishlist-btn');
		btns.forEach(function (b) {
			b.dataset.wishlistCount = num;
		});

		if (footerEl) {
			if (num <= 0) {
				footerEl.classList.add('hidden');
			} else {
				footerEl.classList.remove('hidden');
			}
		}
	}

	/**
	 * Open Wishlist Drawer / Bottom Sheet
	 */
	function openWishlistDrawer() {
		if (!drawerEl || !panelEl || !backdropEl) return;

		// If cart drawer is open, close it first
		if (window.ashrafcoCartDrawer && typeof window.ashrafcoCartDrawer.close === 'function') {
			window.ashrafcoCartDrawer.close();
		}

		isOpen = true;
		drawerEl.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
		drawerEl.classList.add('opacity-100', 'pointer-events-auto');
		drawerEl.setAttribute('aria-hidden', 'false');

		backdropEl.classList.remove('pointer-events-none');
		backdropEl.classList.add('pointer-events-auto');

		// Lock body scroll
		document.body.classList.add('ashrafco-wishlist-open');
		document.body.style.overflow = 'hidden';

		// Animation frame: Desktop slides in from RIGHT, Mobile slides up from BOTTOM
		requestAnimationFrame(function () {
			backdropEl.classList.remove('opacity-0');
			backdropEl.classList.add('opacity-100');

			if (window.innerWidth >= 768) {
				panelEl.classList.remove('translate-x-full');
				panelEl.classList.add('translate-x-0');
			} else {
				panelEl.classList.remove('translate-y-full');
				panelEl.classList.add('translate-y-0');
			}
		});

		// Refresh drawer contents via AJAX to ensure accurate sync
		fetchWishlistDrawerContent();
	}

	/**
	 * Close Wishlist Drawer / Bottom Sheet
	 */
	function closeWishlistDrawer() {
		if (!drawerEl || !panelEl || !backdropEl) return;

		isOpen = false;
		drawerEl.setAttribute('aria-hidden', 'true');

		backdropEl.classList.remove('opacity-100', 'pointer-events-auto');
		backdropEl.classList.add('opacity-0', 'pointer-events-none');

		if (window.innerWidth >= 768) {
			panelEl.classList.remove('translate-x-0');
			panelEl.classList.add('translate-x-full');
		} else {
			panelEl.classList.remove('translate-y-0');
			panelEl.classList.add('translate-y-full');
		}

		document.body.classList.remove('ashrafco-wishlist-open');
		document.body.style.overflow = '';

		setTimeout(function () {
			if (!isOpen) {
				drawerEl.classList.add('invisible', 'opacity-0', 'pointer-events-none');
				drawerEl.classList.remove('opacity-100', 'pointer-events-auto');
			}
		}, 300);
	}

	/**
	 * Handle window resize between Desktop and Mobile
	 */
	function handleResize() {
		if (!panelEl) return;
		if (window.innerWidth >= 768) {
			panelEl.classList.remove('translate-y-full', 'translate-y-0');
			if (isOpen) {
				panelEl.classList.add('translate-x-0');
				panelEl.classList.remove('translate-x-full');
			} else {
				panelEl.classList.add('translate-x-full');
				panelEl.classList.remove('translate-x-0');
			}
		} else {
			panelEl.classList.remove('translate-x-full', 'translate-x-0');
			if (isOpen) {
				panelEl.classList.add('translate-y-0');
				panelEl.classList.remove('translate-y-full');
			} else {
				panelEl.classList.add('translate-y-full');
				panelEl.classList.remove('translate-y-0');
			}
		}
	}

	/**
	 * Fetch Wishlist Drawer Content via AJAX
	 */
	function fetchWishlistDrawerContent() {
		if (!bodyEl) return;

		var ajaxUrl = (window.ashrafcoWishlistData && window.ashrafcoWishlistData.ajaxUrl)
			? window.ashrafcoWishlistData.ajaxUrl
			: (window.testThemeData && window.testThemeData.ajaxUrl ? window.testThemeData.ajaxUrl : '/wp-admin/admin-ajax.php');

		var nonce = (window.ashrafcoWishlistData && window.ashrafcoWishlistData.nonce)
			? window.ashrafcoWishlistData.nonce
			: (window.testThemeData && window.testThemeData.wishlistNonce ? window.testThemeData.wishlistNonce : '');

		var clientIds = getSavedWishlistIds();

		var formData = new FormData();
		formData.append('action', 'ashrafco_get_wishlist_drawer_html');
		formData.append('nonce', nonce);
		formData.append('product_ids', JSON.stringify(clientIds));

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
		.then(function (res) {
			if (!res.ok) throw new Error('HTTP ' + res.status);
			return res.json();
		})
		.then(function (res) {
			if (res && res.success && res.data) {
				if (bodyEl) {
					bodyEl.innerHTML = res.data.items_html;
				}
				if (footerEl) {
					footerEl.innerHTML = res.data.footer_html;
					if (res.data.count <= 0) {
						footerEl.classList.add('hidden');
					} else {
						footerEl.classList.remove('hidden');
					}
				}
				updateAllWishlistCounts(res.data.count);
			}
		})
		.catch(function () {});
	}

	/**
	 * Remove Item from Wishlist Drawer
	 */
	function removeItem(productId, itemEl) {
		if (!productId) return;

		if (itemEl) {
			itemEl.style.opacity = '0.5';
			itemEl.style.pointerEvents = 'none';
		}

		var ajaxUrl = (window.ashrafcoWishlistData && window.ashrafcoWishlistData.ajaxUrl)
			? window.ashrafcoWishlistData.ajaxUrl
			: (window.testThemeData && window.testThemeData.ajaxUrl ? window.testThemeData.ajaxUrl : '/wp-admin/admin-ajax.php');

		var nonce = (window.ashrafcoWishlistData && window.ashrafcoWishlistData.nonce)
			? window.ashrafcoWishlistData.nonce
			: (window.testThemeData && window.testThemeData.wishlistNonce ? window.testThemeData.wishlistNonce : '');

		var savedIds = getSavedWishlistIds();
		var idx = savedIds.indexOf(String(productId));
		if (idx !== -1) {
			savedIds.splice(idx, 1);
			saveWishlistIds(savedIds);
		}

		var formData = new FormData();
		formData.append('action', 'ashrafco_remove_from_wishlist');
		formData.append('nonce', nonce);
		formData.append('product_id', productId);

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
		.then(function (res) {
			return res.json();
		})
		.then(function (res) {
			var newCount = (res && res.success && typeof res.data.count !== 'undefined') ? res.data.count : savedIds.length;
			updateAllWishlistCounts(newCount);

			// Synchronize any heart buttons on page
			document.dispatchEvent(new CustomEvent('wishlist_updated', {
				detail: {
					count: newCount,
					productId: productId,
					isAdded: false
				}
			}));

			// Refresh drawer view
			fetchWishlistDrawerContent();
		})
		.catch(function () {
			if (itemEl) {
				itemEl.style.opacity = '1';
				itemEl.style.pointerEvents = 'auto';
			}
		});
	}

	/**
	 * Quick Add to Cart from Wishlist Drawer
	 */
	function addToCart(productId, btn) {
		if (!productId || !btn) return;

		var originalText = btn.innerHTML;
		btn.disabled = true;
		btn.innerHTML = '<svg class="animate-spin h-3.5 w-3.5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>جاري الإضافة...</span>';

		var ajaxUrl = (window.ashrafcoWishlistData && window.ashrafcoWishlistData.ajaxUrl)
			? window.ashrafcoWishlistData.ajaxUrl
			: (window.testThemeData && window.testThemeData.ajaxUrl ? window.testThemeData.ajaxUrl : '/wp-admin/admin-ajax.php');

		var cartNonce = (window.ashrafcoCartData && window.ashrafcoCartData.nonce) ? window.ashrafcoCartData.nonce : '';

		var formData = new FormData();
		formData.append('action', 'ashrafco_add_to_cart');
		formData.append('nonce', cartNonce);
		formData.append('product_id', productId);
		formData.append('quantity', 1);

		fetch(ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin'
		})
		.then(function (res) {
			return res.json();
		})
		.then(function (res) {
			btn.disabled = false;
			btn.innerHTML = originalText;

			if (res && res.success) {
				// Close wishlist drawer and open cart drawer
				closeWishlistDrawer();

				// Trigger cart drawer open and update cart fragments
				if (window.ashrafcoCartDrawer && typeof window.ashrafcoCartDrawer.open === 'function') {
					window.ashrafcoCartDrawer.open();
				} else {
					var cartTrigger = document.querySelector('[data-cart-drawer-trigger]');
					if (cartTrigger) {
						cartTrigger.click();
					}
				}

				if (window.jQuery) {
					window.jQuery(document.body).trigger('added_to_cart', [res.data.fragments, res.data.cart_hash, btn]);
				}
			} else {
				var msg = (res && res.data && res.data.message) ? res.data.message : 'تعذر إضافة المنتج للسلة.';
				alert(msg);
			}
		})
		.catch(function () {
			btn.disabled = false;
			btn.innerHTML = originalText;
		});
	}

	/**
	 * Setup Mobile Touch Drag Handle to pull-down dismiss
	 */
	function initDragHandle() {
		var dragHandle = drawerEl.querySelector('.ashrafco-drag-handle-bar');
		if (!dragHandle) return;

		var startY = 0;
		var currentY = 0;
		var isDragging = false;

		dragHandle.addEventListener('touchstart', function (e) {
			if (window.innerWidth >= 768) return;
			startY = e.touches[0].clientY;
			isDragging = true;
		}, { passive: true });

		dragHandle.addEventListener('touchmove', function (e) {
			if (!isDragging || window.innerWidth >= 768) return;
			currentY = e.touches[0].clientY;
			var deltaY = currentY - startY;
			if (deltaY > 0) {
				panelEl.style.transform = 'translateY(' + deltaY + 'px)';
			}
		}, { passive: true });

		dragHandle.addEventListener('touchend', function () {
			if (!isDragging || window.innerWidth >= 768) return;
			isDragging = false;
			var deltaY = currentY - startY;
			panelEl.style.transform = '';
			if (deltaY > 70) {
				closeWishlistDrawer();
			}
		});
	}

	/**
	 * Initialize Wishlist Drawer on DOM Ready
	 */
	document.addEventListener('DOMContentLoaded', function () {
		drawerEl   = document.getElementById('ashrafco-wishlist-drawer');
		backdropEl = document.getElementById('ashrafco-wishlist-backdrop');
		panelEl    = document.getElementById('ashrafco-wishlist-panel');
		bodyEl     = document.getElementById('ashrafco-wishlist-body');
		footerEl   = document.getElementById('ashrafco-wishlist-footer');
		closeBtn   = document.getElementById('ashrafco-wishlist-close');

		if (!drawerEl || !panelEl) return;

		handleResize();
		window.addEventListener('resize', handleResize, { passive: true });

		// Close button listener
		if (closeBtn) {
			closeBtn.addEventListener('click', function (e) {
				e.preventDefault();
				closeWishlistDrawer();
			});
		}

		// Backdrop click listener
		if (backdropEl) {
			backdropEl.addEventListener('click', function (e) {
				e.preventDefault();
				closeWishlistDrawer();
			});
		}

		// ESC Key dismiss
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen) {
				closeWishlistDrawer();
			}
		});

		// Listen for click on header wishlist trigger buttons (Desktop & Mobile header)
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('#mobile-header-wishlist-btn, #desktop-header-wishlist-btn, .ashrafco-wishlist-trigger');
			if (trigger) {
				e.preventDefault();
				e.stopPropagation();
				if (isOpen) {
					closeWishlistDrawer();
				} else {
					openWishlistDrawer();
				}
				return;
			}

			// Continue Shopping button in wishlist footer
			var continueBtn = e.target.closest('.ashrafco-wishlist-continue-btn');
			if (continueBtn) {
				e.preventDefault();
				closeWishlistDrawer();
				return;
			}

			// Remove item from wishlist inside drawer
			var removeBtn = e.target.closest('.ashrafco-wishlist-remove-btn');
			if (removeBtn && drawerEl.contains(removeBtn)) {
				e.preventDefault();
				e.stopPropagation();
				var pid = removeBtn.dataset.productId;
				var item = removeBtn.closest('.ashrafco-wishlist-item');
				removeItem(pid, item);
				return;
			}

			// Quick Add to Cart button inside wishlist drawer
			var addCartBtn = e.target.closest('.ashrafco-wishlist-add-to-cart');
			if (addCartBtn && drawerEl.contains(addCartBtn)) {
				e.preventDefault();
				e.stopPropagation();
				var pidToAdd = addCartBtn.dataset.productId;
				addToCart(pidToAdd, addCartBtn);
				return;
			}
		});

		// Listen for custom wishlist_updated events dispatched elsewhere
		document.addEventListener('wishlist_updated', function (e) {
			if (e.detail && typeof e.detail.count !== 'undefined') {
				updateAllWishlistCounts(e.detail.count);
			}
			if (isOpen) {
				fetchWishlistDrawerContent();
			}
		});

		initDragHandle();

		// Export global API for external triggers
		window.ashrafcoWishlistDrawer = {
			open: openWishlistDrawer,
			close: closeWishlistDrawer,
			refresh: fetchWishlistDrawerContent
		};
	});

})();
