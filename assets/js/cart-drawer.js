/**
 * Ashrafco Modern Responsive Shopping Cart Drawer & Bottom Sheet Controller
 *
 * Implements Desktop Left Drawer (100dvh) and Mobile Bottom Sheet (< 768px, 90dvh),
 * AJAX live quantity adjustment (+/-), item removal, wishlist heart sync,
 * and automatic drawer open on WooCommerce add-to-cart events.
 *
 * @package Ashrafco
 * @since 1.1.0
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
	 * Safe Wishlist helpers matching Ashrafco theme implementation
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

	function syncDrawerWishlistHearts() {
		if (!drawerEl) return;
		var savedIds = getSavedWishlistIds();
		var heartBtns = drawerEl.querySelectorAll('.test-theme-wishlist-toggle');

		heartBtns.forEach(function (btn) {
			var pid = String(btn.dataset.productId || '');
			if (!pid) return;

			var isSaved = savedIds.includes(pid);
			btn.classList.toggle('is-active', isSaved);

			var svg = btn.querySelector('svg');
			if (svg) {
				if (isSaved) {
					svg.setAttribute('stroke', '#d90429');
					svg.setAttribute('fill', '#d90429');
					svg.style.stroke = '#d90429';
					svg.style.fill = '#d90429';
				} else {
					svg.setAttribute('stroke', '#374151');
					svg.setAttribute('fill', 'none');
					svg.style.stroke = '#374151';
					svg.style.fill = 'none';
				}
			}
		});
	}

	/**
	 * Update all cart badge counters in header, drawer, and bottom navigation
	 */
	function updateAllCartCounts(count) {
		var num = parseInt(count, 10);
		if (isNaN(num)) num = 0;

		var drawerCountEls = document.querySelectorAll('.ashrafco-cart-items-count');
		drawerCountEls.forEach(function (el) {
			el.textContent = String(num);
		});

		var badgeEls = document.querySelectorAll('.ashrafco-cart-count-badge, #header-cart-count-badge, #test-theme-cart-count, #mobile-nav-cart-badge');
		badgeEls.forEach(function (el) {
			el.textContent = String(num);
			if (num > 0) {
				el.classList.remove('hidden');
			}
		});
	}

	/**
	 * Open Cart Drawer / Bottom Sheet
	 */
	function openCartDrawer() {
		if (!drawerEl || !panelEl || !backdropEl) return;

		isOpen = true;
		drawerEl.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
		drawerEl.classList.add('opacity-100', 'pointer-events-auto');
		drawerEl.setAttribute('aria-hidden', 'false');

		// Enable backdrop pointer events so clicks are captured
		backdropEl.classList.remove('pointer-events-none');
		backdropEl.classList.add('pointer-events-auto');

		// Lock body scroll
		document.body.classList.add('ashrafco-cart-open');
		document.body.style.overflow = 'hidden';

		// Trigger smooth animation frame
		requestAnimationFrame(function () {
			backdropEl.classList.remove('opacity-0');
			backdropEl.classList.add('opacity-100');

			// Desktop: slide in from left | Mobile: slide up from bottom
			if (window.innerWidth >= 768) {
				panelEl.classList.remove('-translate-x-full');
				panelEl.classList.add('translate-x-0');
			} else {
				panelEl.classList.remove('translate-y-full');
				panelEl.classList.add('translate-y-0');
			}
		});

		syncDrawerWishlistHearts();
	}

	/**
	 * Close Cart Drawer / Bottom Sheet
	 */
	function closeCartDrawer() {
		if (!drawerEl || !panelEl || !backdropEl) return;

		isOpen = false;
		drawerEl.setAttribute('aria-hidden', 'true');

		// Backdrop animation & disable pointer events
		backdropEl.classList.remove('opacity-100', 'pointer-events-auto');
		backdropEl.classList.add('opacity-0', 'pointer-events-none');

		// Panel exit animation
		if (window.innerWidth >= 768) {
			panelEl.classList.remove('translate-x-0');
			panelEl.classList.add('-translate-x-full');
		} else {
			panelEl.classList.remove('translate-y-0');
			panelEl.classList.add('translate-y-full');
		}

		// Unlock body scroll
		document.body.classList.remove('ashrafco-cart-open');
		document.body.style.overflow = '';

		// After animation ends (300ms) hide container
		setTimeout(function () {
			if (!isOpen) {
				drawerEl.classList.add('invisible', 'opacity-0', 'pointer-events-none');
				drawerEl.classList.remove('opacity-100', 'pointer-events-auto');
			}
		}, 300);
	}

	/**
	 * Handle window resize to adapt translation classes between Desktop and Mobile
	 */
	function handleResize() {
		if (!panelEl) return;
		if (isOpen) {
			panelEl.classList.remove('-translate-x-full', 'translate-y-full');
			if (window.innerWidth >= 768) {
				panelEl.classList.add('translate-x-0');
				panelEl.classList.remove('translate-y-0');
			} else {
				panelEl.classList.add('translate-y-0');
				panelEl.classList.remove('translate-x-0');
			}
		} else {
			panelEl.classList.remove('translate-x-0', 'translate-y-0');
			if (window.innerWidth >= 768) {
				panelEl.classList.add('-translate-x-full');
				panelEl.classList.remove('translate-y-full');
			} else {
				panelEl.classList.add('translate-y-full');
				panelEl.classList.remove('-translate-x-full');
			}
		}
	}

	/**
	 * Send AJAX Request to update cart item quantity (+ / -)
	 */
	function updateItemQuantity(cartItemKey, newQty, itemRow) {
		if (!cartItemKey || newQty < 1 || !window.ashrafcoCartData) return;

		if (itemRow) {
			itemRow.classList.add('ashrafco-item-loading');
		}

		var formData = new FormData();
		formData.append('action', 'ashrafco_update_cart_qty');
		formData.append('nonce', window.ashrafcoCartData.nonce);
		formData.append('cart_item_key', cartItemKey);
		formData.append('quantity', newQty);

		fetch(window.ashrafcoCartData.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (res) {
				if (itemRow) {
					itemRow.classList.remove('ashrafco-item-loading');
				}
				if (res.success && res.data) {
					// Update drawer body
					if (bodyEl && res.data.items_html) {
						bodyEl.innerHTML = res.data.items_html;
					}
					// Update drawer footer
					if (footerEl) {
						footerEl.innerHTML = res.data.footer_html;
						if (res.data.is_empty) {
							footerEl.classList.add('hidden');
						} else {
							footerEl.classList.remove('hidden');
						}
					}
					// Update cart count
					updateAllCartCounts(res.data.cart_count);
					syncDrawerWishlistHearts();

					// Trigger jQuery cart fragments if available
					if (window.jQuery && res.data.fragments) {
						window.jQuery(document.body).trigger('wc_fragments_refreshed', [res.data.fragments]);
					}
				}
			})
			.catch(function () {
				if (itemRow) {
					itemRow.classList.remove('ashrafco-item-loading');
				}
			});
	}

	/**
	 * Send AJAX Request to remove cart item (Trash icon)
	 */
	function removeCartItem(cartItemKey, itemRow) {
		if (!cartItemKey || !window.ashrafcoCartData) return;

		if (itemRow) {
			itemRow.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
			itemRow.style.opacity = '0';
			itemRow.style.transform = 'scale(0.95) translateX(-20px)';
		}

		var formData = new FormData();
		formData.append('action', 'ashrafco_remove_cart_item');
		formData.append('nonce', window.ashrafcoCartData.nonce);
		formData.append('cart_item_key', cartItemKey);

		fetch(window.ashrafcoCartData.ajaxUrl, {
			method: 'POST',
			body: formData,
			credentials: 'same-origin',
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (res) {
				if (res.success && res.data) {
					setTimeout(function () {
						if (bodyEl && res.data.items_html) {
							bodyEl.innerHTML = res.data.items_html;
						}
						if (footerEl) {
							footerEl.innerHTML = res.data.footer_html;
							if (res.data.is_empty) {
								footerEl.classList.add('hidden');
							} else {
								footerEl.classList.remove('hidden');
							}
						}
						updateAllCartCounts(res.data.cart_count);
						syncDrawerWishlistHearts();

						if (window.jQuery && res.data.fragments) {
							window.jQuery(document.body).trigger('wc_fragments_refreshed', [res.data.fragments]);
						}
					}, 200);
				}
			})
			.catch(function () {
				if (itemRow) {
					itemRow.style.opacity = '';
					itemRow.style.transform = '';
				}
			});
	}

	/**
	 * Intercept Single Product form add-to-cart submissions via AJAX
	 */
	function setupSingleProductAjaxAdd() {
		var cartForm = document.querySelector('form.cart');
		if (!cartForm) return;

		cartForm.addEventListener('submit', function (e) {
			var submitBtn = cartForm.querySelector('.single_add_to_cart_button');
			if (!submitBtn) return;

			// Check if variable product has variation selected
			var isVariable = cartForm.classList.contains('variations_form');
			if (isVariable) {
				var variationIdInput = cartForm.querySelector('input[name="variation_id"]');
				var varId = variationIdInput ? parseInt(variationIdInput.value, 10) : 0;
				if (!varId || varId <= 0) {
					// Let native WooCommerce validation notify user
					return;
				}
			}

			// Prevent full page reload
			e.preventDefault();

			var origText = submitBtn.textContent;
			submitBtn.disabled = true;
			submitBtn.classList.add('ashrafco-btn-loading');

			var formData = new FormData(cartForm);
			formData.append('action', 'ashrafco_add_to_cart');
			if (submitBtn.name && submitBtn.value) {
				formData.append(submitBtn.name, submitBtn.value);
			}
			if (!formData.get('product_id')) {
				var prodIdInput = cartForm.querySelector('input[name="product_id"]');
				if (prodIdInput && prodIdInput.value) {
					formData.append('product_id', prodIdInput.value);
				} else if (submitBtn.value) {
					formData.append('product_id', submitBtn.value);
				}
			}
			if (window.ashrafcoCartData) {
				formData.append('nonce', window.ashrafcoCartData.nonce);
			}

			fetch(window.ashrafcoCartData ? window.ashrafcoCartData.ajaxUrl : '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin',
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (res) {
					submitBtn.disabled = false;
					submitBtn.classList.remove('ashrafco-btn-loading');

					if (res.success && res.data) {
						if (bodyEl && res.data.items_html) {
							bodyEl.innerHTML = res.data.items_html;
						}
						if (footerEl) {
							footerEl.innerHTML = res.data.footer_html;
							footerEl.classList.remove('hidden');
						}
						updateAllCartCounts(res.data.cart_count);
						openCartDrawer();

						if (window.jQuery) {
							window.jQuery(document.body).trigger('added_to_cart', [res.data.fragments, res.data.cart_hash, submitBtn]);
						}
					} else {
						// Fallback: regular submit if custom error
						cartForm.submit();
					}
				})
				.catch(function () {
					submitBtn.disabled = false;
					submitBtn.classList.remove('ashrafco-btn-loading');
					cartForm.submit();
				});
		});
	}

	/**
	 * Setup Mobile Touch Drag Handle to dismiss bottom sheet
	 */
	function setupTouchDismiss() {
		var dragHandle = drawerEl ? drawerEl.querySelector('.ashrafco-drag-handle-bar') : null;
		if (!dragHandle || !panelEl) return;

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
				closeCartDrawer();
			}
		});
	}

	/**
	 * Initialize Cart Drawer on DOM Ready
	 */
	document.addEventListener('DOMContentLoaded', function () {
		drawerEl   = document.getElementById('ashrafco-cart-drawer');
		backdropEl = document.getElementById('ashrafco-cart-backdrop');
		panelEl    = document.getElementById('ashrafco-cart-panel');
		bodyEl     = document.getElementById('ashrafco-cart-body');
		footerEl   = document.getElementById('ashrafco-cart-footer');
		closeBtn   = document.getElementById('ashrafco-cart-close');

		if (!drawerEl || !panelEl) return;

		// Initial resize class setup
		handleResize();
		window.addEventListener('resize', handleResize, { passive: true });

		// Close button listener
		if (closeBtn) {
			closeBtn.addEventListener('click', function (e) {
				e.preventDefault();
				closeCartDrawer();
			});
		}

		// Backdrop click listener
		if (backdropEl) {
			backdropEl.addEventListener('click', function (e) {
				e.preventDefault();
				closeCartDrawer();
			});
		}

		// ESC Key dismiss
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen) {
				closeCartDrawer();
			}
		});

		// Listen for click on all Cart Trigger elements across header and bottom nav
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('[data-cart-drawer-trigger], .ashrafco-cart-trigger, #header-cart-drawer-trigger, #test-theme-cart-trigger');
			if (trigger) {
				e.preventDefault();
				e.stopPropagation();
				if (isOpen) {
					closeCartDrawer();
				} else {
					openCartDrawer();
				}
				return;
			}

			// Close button click (X icon)
			var closeBtnEl = e.target.closest('#ashrafco-cart-close, .ashrafco-cart-close-btn');
			if (closeBtnEl) {
				e.preventDefault();
				closeCartDrawer();
				return;
			}

			// Continue shopping button click
			var continueBtn = e.target.closest('.ashrafco-continue-shopping-btn, #ashrafco-cart-continue-btn');
			if (continueBtn) {
				e.preventDefault();
				closeCartDrawer();
				return;
			}

			// Click outside the cart panel while open (clicking backdrop or anywhere outside panel)
			if (isOpen && panelEl && !panelEl.contains(e.target)) {
				e.preventDefault();
				closeCartDrawer();
				return;
			}

			// Delegated Quantity Buttons (+ / -)
			var qtyBtn = e.target.closest('.ashrafco-qty-btn');
			if (qtyBtn) {
				e.preventDefault();
				var action      = qtyBtn.dataset.action;
				var cartItemKey = qtyBtn.dataset.cartItemKey;
				var itemRow     = qtyBtn.closest('.ashrafco-cart-item');
				var qtyValEl    = itemRow ? itemRow.querySelector('.ashrafco-qty-val') : null;

				if (!qtyValEl || !cartItemKey) return;

				var currentQty = parseInt(qtyValEl.textContent, 10) || 1;
				var newQty = (action === 'plus') ? currentQty + 1 : currentQty - 1;

				if (newQty < 1) return;

				// Optimistic counter display
				qtyValEl.textContent = String(newQty);
				updateItemQuantity(cartItemKey, newQty, itemRow);
				return;
			}

			// Delegated Remove Button (Trash)
			var removeBtn = e.target.closest('.ashrafco-drawer-remove-btn');
			if (removeBtn) {
				e.preventDefault();
				var key = removeBtn.dataset.cartItemKey;
				var row = removeBtn.closest('.ashrafco-cart-item');
				removeCartItem(key, row);
				return;
			}
		});

		// Listen for WooCommerce AJAX added_to_cart events
		if (window.jQuery) {
			window.jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
				// Re-sync wishlist hearts and open the drawer immediately!
				setTimeout(function () {
					openCartDrawer();
					syncDrawerWishlistHearts();
				}, 60);
			});

			// When WooCommerce updates fragments automatically
			window.jQuery(document.body).on('wc_fragments_refreshed', function () {
				syncDrawerWishlistHearts();
			});
		}

		// Listen for custom wishlist events from header.js to keep drawer in sync
		document.addEventListener('wishlist_updated', function () {
			syncDrawerWishlistHearts();
		});

		// Intercept single product page add-to-cart form
		setupSingleProductAjaxAdd();

		// Setup mobile drag handle
		setupTouchDismiss();
	});

	// Expose globally for programmatic control if needed
	window.AshrafcoCartDrawer = {
		open: openCartDrawer,
		close: closeCartDrawer,
		syncWishlist: syncDrawerWishlistHearts,
	};
})();
