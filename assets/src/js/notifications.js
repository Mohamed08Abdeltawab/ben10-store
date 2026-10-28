/**
 * Ashrafco Store - In-App Notifications Client Interaction Script
 *
 * Provides:
 * - Dropdown toggle and outside click dismiss
 * - Dynamic notification loading and rendering in dropdown
 * - Instant Mark-as-Read & Mark-All-as-Read with optimistic UI update
 * - Lightweight polling with Page Visibility API (pauses when inactive)
 * - Counter badges synchronization across desktop, mobile, and portal
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function () {
	'use strict';

	var config = window.AshrafcoNotifConfig || {
		ajaxUrl: '/wp-admin/admin-ajax.php',
		nonce: '',
		isLoggedIn: false,
		userType: 'guest',
		initialUnreadCount: 0,
		pollInterval: 35000,
		myNotificationsUrl: '/my-account/my-notifications/',
		portalNotificationsUrl: '/my-account/my-notifications/',
		i18n: {
			loading: 'جاري التحميل...',
			noNotifications: 'لا توجد إشعارات جديدة حالياً.',
			markAsRead: 'تحديد كمقروء',
			markAllAsRead: 'تحديد الكل كمقروء',
			viewAll: 'عرض جميع الإشعارات',
			markedAllSuccess: 'تم تحديد جميع الإشعارات كمقروءة.',
			errorOccurred: 'حدث خطأ أثناء المعالجة، يرجى المحاولة ثانية.'
		}
	};

	var currentCount = parseInt(config.initialUnreadCount, 10) || 0;
	var pollTimer = null;
	var isPollingActive = false;
	var dropdownsData = {}; // Cache per scope

	/**
	 * Helper: Return SVG icon markup for notification type.
	 */
	function getIconSvg(type) {
		switch (type) {
			case 'order_new':
			case 'order_received':
				return '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>';
			case 'order_processing':
				return '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
			case 'order_shipped':
				return '<svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>';
			case 'order_cancelled':
			case 'order_cancelled_customer':
				return '<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
			case 'payment_failed':
				return '<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
			case 'low_stock':
				return '<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>';
			case 'customer_registered':
				return '<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
			default:
				return '<svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>';
		}
	}

	/**
	 * Helper: Return Animated Skeleton HTML for dropdown loading state.
	 */
	function getSkeletonHtml() {
		var itemHtml = '<div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100/60 flex items-start gap-3 animate-pulse">' +
			'<div class="w-8 h-8 rounded-xl bg-slate-200 flex-shrink-0"></div>' +
			'<div class="flex-1 space-y-2 py-0.5 text-right">' +
			'<div class="h-3 bg-slate-200 rounded-md w-2/5"></div>' +
			'<div class="h-2.5 bg-slate-200/80 rounded-md w-4/5"></div>' +
			'<div class="h-2 bg-slate-200/50 rounded-md w-1/4"></div>' +
			'</div>' +
			'</div>';
		return '<div class="ashrafco-notif-skeleton p-2 space-y-2" aria-hidden="true">' + itemHtml + itemHtml + itemHtml + '</div>';
	}

	/**
	 * Update UI Badges for all notification bells and menu links.
	 */
	function updateBadges(count, scope) {
		currentCount = parseInt(count, 10);
		if (isNaN(currentCount) || currentCount < 0) {
			currentCount = 0;
		}

		var formatted = currentCount > 99 ? '99+' : String(currentCount);

		// Hide loading indicators once count is resolved
		var loadingIndicators = document.querySelectorAll('.ashrafco-notif-loading-indicator');
		loadingIndicators.forEach(function (ind) {
			ind.classList.add('hidden');
		});

		// 1. Header and Portal Badges (Never show "0")
		var selector = '.ashrafco-notif-badge';
		if (scope) {
			selector += '[data-badge-scope="' + scope + '"]';
		}
		var badges = document.querySelectorAll(selector);
		badges.forEach(function (badge) {
			if (currentCount > 0) {
				badge.textContent = formatted;
				badge.classList.remove('hidden');
			} else {
				badge.textContent = '';
				badge.classList.add('hidden');
			}
		});

		// 2. Unread Pill in Dropdown Header (Never show "0 جديدة")
		var pills = document.querySelectorAll('.ashrafco-notif-unread-pill');
		pills.forEach(function (pill) {
			if (currentCount > 0) {
				pill.textContent = currentCount + ' جديدة';
				pill.classList.remove('hidden');
			} else {
				pill.textContent = '';
				pill.classList.add('hidden');
			}
		});

		// 3. Account Navigation Menu Item Badge (if present)
		var menuBadges = document.querySelectorAll('.ashrafco-menu-notif-badge');
		menuBadges.forEach(function (mb) {
			if (currentCount > 0) {
				mb.textContent = formatted;
				mb.classList.remove('hidden');
			} else {
				mb.textContent = '';
				mb.classList.add('hidden');
			}
		});

		// 4. Admin Portal Tab Badge (if present)
		var portalTabBadge = document.getElementById('portal-tab-notifications-badge');
		if (portalTabBadge) {
			if (currentCount > 0) {
				portalTabBadge.textContent = formatted;
				portalTabBadge.classList.remove('hidden');
			} else {
				portalTabBadge.textContent = '';
				portalTabBadge.classList.add('hidden');
			}
		}

		// 5. Full Page Header Unread Badges (Account Page & Portal Tab)
		var pageUnreadBadges = document.querySelectorAll('.ashrafco-page-unread-badge');
		pageUnreadBadges.forEach(function (pub) {
			var countSpan = pub.querySelector('.ashrafco-page-unread-count');
			if (currentCount > 0) {
				if (countSpan) {
					countSpan.textContent = formatted;
				}
				pub.classList.remove('hidden');
			} else {
				pub.classList.add('hidden');
			}
		});

		// Hide "Mark all as read" button if unread count is 0
		var markAllBtns = document.querySelectorAll('.ashrafco-mark-all-read-btn');
		markAllBtns.forEach(function (btn) {
			if (currentCount === 0) {
				btn.classList.add('hidden');
			} else {
				btn.classList.remove('hidden');
			}
		});
	}

	/**
	 * Fetch notifications for a wrapper dropdown.
	 */
	function loadDropdownNotifications(wrapper, forceRefresh) {
		var scope = wrapper.getAttribute('data-scope') || 'customer';
		var container = wrapper.querySelector('.ashrafco-notif-list-container');
		if (!container) return;

		// Show loading indicator in dropdown header
		var loadingInd = wrapper.querySelector('.ashrafco-notif-loading-indicator');
		if (loadingInd) loadingInd.classList.remove('hidden');
		var unreadPill = wrapper.querySelector('.ashrafco-notif-unread-pill');
		if (unreadPill) unreadPill.classList.add('hidden');

		if (!forceRefresh && dropdownsData[scope]) {
			if (loadingInd) loadingInd.classList.add('hidden');
			if (unreadPill && currentCount > 0) unreadPill.classList.remove('hidden');
			renderDropdownItems(container, dropdownsData[scope]);
			return;
		}

		// Use rich skeleton animation instead of plain text loading
		container.innerHTML = getSkeletonHtml();

		var url = config.ajaxUrl + '?action=ashrafco_get_notifications&nonce=' + encodeURIComponent(config.nonce) + '&scope=' + encodeURIComponent(scope) + '&per_page=6&paged=1';

		fetch(url, {
			method: 'GET',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data && data.success && data.data) {
					dropdownsData[scope] = data.data.items || [];
					updateBadges(data.data.unread_count, scope);
					renderDropdownItems(container, dropdownsData[scope]);
				} else {
					if (loadingInd) loadingInd.classList.add('hidden');
					container.innerHTML = '<div class="py-8 text-center text-xs text-rose-500 font-medium">' + config.i18n.errorOccurred + '</div>';
				}
			})
			.catch(function () {
				if (loadingInd) loadingInd.classList.add('hidden');
				container.innerHTML = '<div class="py-8 text-center text-xs text-rose-500 font-medium">' + config.i18n.errorOccurred + '</div>';
			});
	}

	/**
	 * Render HTML items in dropdown container.
	 */
	function renderDropdownItems(container, items) {
		if (!items || items.length === 0) {
			container.innerHTML = '<div class="py-10 px-4 text-center text-xs text-slate-400 font-medium flex flex-col items-center justify-center gap-2">' +
				'<svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>' +
				'<span>' + config.i18n.noNotifications + '</span>' +
				'</div>';
			return;
		}

		var html = '';
		items.forEach(function (item) {
			var unreadClass = !item.is_read ? 'bg-rose-50/40 hover:bg-rose-50/70 font-semibold' : 'bg-white hover:bg-slate-50';
			var dotHtml = !item.is_read ? '<span class="w-2 h-2 rounded-full bg-[#d90429] flex-shrink-0 animate-pulse"></span>' : '';
			var iconSvg = getIconSvg(item.type);

			html += '<div class="ashrafco-notif-item p-3 transition-colors flex items-start justify-between gap-3 cursor-pointer group ' + unreadClass + '" data-id="' + item.id + '" data-read="' + (item.is_read ? '1' : '0') + '" data-url="' + (item.target_url || '') + '">';

			// Right side: Icon + Content
			html += '<div class="flex items-start gap-2.5 min-w-0 flex-1">';
			html += '<div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">' + iconSvg + '</div>';
			html += '<div class="min-w-0 flex-1 text-right">';
			html += '<div class="flex items-center gap-1.5">';
			html += dotHtml;
			html += '<h5 class="text-xs font-black text-slate-900 truncate leading-snug m-0">' + escapeHtml(item.title) + '</h5>';
			html += '</div>';
			html += '<p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed mt-0.5 m-0">' + escapeHtml(item.raw_message) + '</p>';
			html += '<span class="text-[10px] text-slate-400 font-medium mt-1 inline-block">' + escapeHtml(item.time_ago) + '</span>';
			html += '</div>';
			html += '</div>';

			// Left side: Mark as read button (if unread)
			if (!item.is_read) {
				html += '<button type="button" class="ashrafco-mark-single-read-btn flex-shrink-0 p-1 text-slate-400 hover:text-[#d90429] rounded-lg hover:bg-white transition-all text-[10px] font-bold focus:outline-none" title="' + config.i18n.markAsRead + '" data-id="' + item.id + '">';
				html += '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
				html += '</button>';
			}

			html += '</div>';
		});

		container.innerHTML = html;

		// Bind Click Events
		bindDropdownItemClicks(container);
	}

	/**
	 * Bind click listeners on individual items.
	 */
	function bindDropdownItemClicks(container) {
		// Item click -> mark as read and navigate
		var items = container.querySelectorAll('.ashrafco-notif-item');
		items.forEach(function (item) {
			item.addEventListener('click', function (e) {
				if (e.target.closest('.ashrafco-mark-single-read-btn')) {
					return; // handled by button listener
				}

				var id = item.getAttribute('data-id');
				var url = item.getAttribute('data-url');
				var isRead = item.getAttribute('data-read') === '1';

				if (!isRead && id) {
					markSingleAsRead(id, function () {
						if (url) window.location.href = url;
					});
				} else if (url) {
					window.location.href = url;
				}
			});
		});

		// Mark as read checkmark button click
		var checkBtns = container.querySelectorAll('.ashrafco-mark-single-read-btn');
		checkBtns.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var id = btn.getAttribute('data-id');
				if (id) {
					markSingleAsRead(id);
				}
			});
		});
	}

	/**
	 * Mark single notification as read via AJAX.
	 */
	function markSingleAsRead(id, callback, scope) {
		var activeScope = scope || (config.userType === 'admin' ? 'admin' : 'customer');
		var formData = new FormData();
		formData.append('action', 'ashrafco_mark_notification_read');
		formData.append('nonce', config.nonce);
		formData.append('notification_id', id);
		formData.append('scope', activeScope);

		// Optimistic UI update
		var targetItems = document.querySelectorAll('.ashrafco-notif-item[data-id="' + id + '"]');
		targetItems.forEach(function (el) {
			el.setAttribute('data-read', '1');
			el.classList.remove('bg-rose-50/40', 'font-semibold');
			el.classList.add('bg-white');
			var dot = el.querySelector('.w-2.h-2.rounded-full');
			if (dot) dot.remove();
			var btn = el.querySelector('.ashrafco-mark-single-read-btn');
			if (btn) btn.remove();
		});

		// Optimistic decrement for instant snappy feedback
		if (currentCount > 0) {
			updateBadges(currentCount - 1, activeScope);
		}

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: formData,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data && data.success && typeof data.data.unread_count !== 'undefined') {
					updateBadges(data.data.unread_count, activeScope);
				}
				if (typeof callback === 'function') callback();
			})
			.catch(function () {
				if (typeof callback === 'function') callback();
			});
	}

	/**
	 * Mark all notifications as read via AJAX.
	 */
	function markAllAsRead(scope, button) {
		if (button) {
			button.disabled = true;
			button.classList.add('opacity-50');
		}

		var formData = new FormData();
		formData.append('action', 'ashrafco_mark_all_notifications_read');
		formData.append('nonce', config.nonce);
		formData.append('scope', scope);

		fetch(config.ajaxUrl, {
			method: 'POST',
			body: formData,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data && data.success) {
					updateBadges(0, scope);
					dropdownsData[scope] = null; // force reload next time

					// Update open dropdown items visually
					document.querySelectorAll('.ashrafco-notif-item').forEach(function (el) {
						el.setAttribute('data-read', '1');
						el.classList.remove('bg-rose-50/40', 'font-semibold');
						el.classList.add('bg-white');
						var dot = el.querySelector('.w-2.h-2.rounded-full');
						if (dot) dot.remove();
						var btn = el.querySelector('.ashrafco-mark-single-read-btn');
						if (btn) btn.remove();
					});

					// Also update full page notification rows
					document.querySelectorAll('.ashrafco-notification-row').forEach(function (row) {
						row.classList.remove('bg-rose-50/30', 'border-rose-100', 'font-semibold');
						row.classList.add('bg-white', 'border-slate-100');
						var badge = row.querySelector('.ashrafco-row-unread-badge');
						if (badge) badge.remove();
						var pageBtn = row.querySelector('.ashrafco-page-mark-read-btn');
						if (pageBtn) pageBtn.remove();
					});
				}
			})
			.finally(function () {
				if (button) {
					button.disabled = false;
					button.classList.remove('opacity-50');
				}
			});
	}

	/**
	 * Lightweight Polling: Fetch Unread Count periodically.
	 */
	function pollUnreadCount() {
		if (document.hidden || !config.isLoggedIn) {
			return;
		}

		var scope = config.userType === 'admin' ? 'admin' : 'customer';
		var url = config.ajaxUrl + '?action=ashrafco_get_unread_count&nonce=' + encodeURIComponent(config.nonce) + '&scope=' + encodeURIComponent(scope);

		fetch(url, {
			method: 'GET',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data && data.success && typeof data.data.count !== 'undefined') {
					var newCount = parseInt(data.data.count, 10);
					if (newCount !== currentCount) {
						dropdownsData = {}; // Clear cached dropdown items
						updateBadges(newCount, scope);
					}
				}
			})
			.catch(function () {});
	}

	/**
	 * Start or Restart Polling Timer.
	 */
	function startPolling() {
		if (isPollingActive) return;
		if (!config.isLoggedIn || config.userType === 'guest') return;

		isPollingActive = true;
		var interval = parseInt(config.pollInterval, 10) || 35000;
		pollTimer = setInterval(pollUnreadCount, interval);
	}

	function stopPolling() {
		if (pollTimer) {
			clearInterval(pollTimer);
			pollTimer = null;
		}
		isPollingActive = false;
	}

	/**
	 * Setup Page Visibility Listener.
	 */
	function initVisibilityListener() {
		document.addEventListener('visibilitychange', function () {
			if (document.hidden) {
				stopPolling();
			} else {
				pollUnreadCount();
				startPolling();
			}
		});
	}

	/**
	 * Setup Dropdown Toggling and Dismissal.
	 */
	function initDropdowns() {
		// Toggle click on bell triggers
		document.addEventListener('click', function (e) {
			var trigger = e.target.closest('.ashrafco-notif-bell-trigger');
			if (trigger) {
				e.preventDefault();
				e.stopPropagation();

				var wrapper = trigger.closest('.ashrafco-notif-wrapper');
				if (!wrapper) return;

				var dropdown = wrapper.querySelector('.ashrafco-notif-dropdown');
				if (!dropdown) return;

				var isOpening = dropdown.classList.contains('hidden');

				// Close all other open dropdowns first
				document.querySelectorAll('.ashrafco-notif-dropdown').forEach(function (d) {
					d.classList.add('hidden');
					var parentTrigger = d.closest('.ashrafco-notif-wrapper')?.querySelector('.ashrafco-notif-bell-trigger');
					if (parentTrigger) parentTrigger.setAttribute('aria-expanded', 'false');
				});

				if (isOpening) {
					dropdown.classList.remove('hidden');
					trigger.setAttribute('aria-expanded', 'true');
					loadDropdownNotifications(wrapper);
				} else {
					dropdown.classList.add('hidden');
					trigger.setAttribute('aria-expanded', 'false');
				}
				return;
			}

			// Mark all read button click
			var markAllBtn = e.target.closest('.ashrafco-mark-all-read-btn');
			if (markAllBtn) {
				e.preventDefault();
				e.stopPropagation();
				var scope = markAllBtn.getAttribute('data-scope') || (config.userType === 'admin' ? 'admin' : 'customer');
				markAllAsRead(scope, markAllBtn);
				return;
			}

			// Click outside open dropdowns -> close them
			if (!e.target.closest('.ashrafco-notif-dropdown')) {
				document.querySelectorAll('.ashrafco-notif-dropdown').forEach(function (d) {
					d.classList.add('hidden');
					var parentTrigger = d.closest('.ashrafco-notif-wrapper')?.querySelector('.ashrafco-notif-bell-trigger');
					if (parentTrigger) parentTrigger.setAttribute('aria-expanded', 'false');
				});
			}
		});

		// Close on Escape key
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				document.querySelectorAll('.ashrafco-notif-dropdown').forEach(function (d) {
					d.classList.add('hidden');
					var parentTrigger = d.closest('.ashrafco-notif-wrapper')?.querySelector('.ashrafco-notif-bell-trigger');
					if (parentTrigger) parentTrigger.setAttribute('aria-expanded', 'false');
				});
			}
		});
	}

	/**
	 * Setup standalone notifications pages (Account page & Portal Tab).
	 */
	function initPageActions() {
		// Individual mark read buttons on full page table/cards
		document.addEventListener('click', function (e) {
			var pageMarkBtn = e.target.closest('.ashrafco-page-mark-read-btn');
			if (pageMarkBtn) {
				e.preventDefault();
				var id = pageMarkBtn.getAttribute('data-id');
				var scope = pageMarkBtn.getAttribute('data-scope');
				if (!scope) {
					var container = pageMarkBtn.closest('.ashrafco-customer-notifications, [data-scope]');
					if (container) {
						var activeTab = container.querySelector('[data-scope]');
						if (activeTab) scope = activeTab.getAttribute('data-scope');
					}
				}
				if (id) {
					markSingleAsRead(id, function () {
						var row = pageMarkBtn.closest('.ashrafco-notification-row');
						if (row) {
							row.classList.remove('bg-rose-50/30', 'border-rose-100', 'font-semibold');
							row.classList.add('bg-white', 'border-slate-100');
							var badge = row.querySelector('.ashrafco-row-unread-badge');
							if (badge) badge.remove();
							pageMarkBtn.remove();
						}
					}, scope);
				}
			}
		});
	}

	/**
	 * Escape HTML helper.
	 */
	function escapeHtml(text) {
		if (!text) return '';
		var div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

	/**
	 * Initialize everything on DOM ready.
	 */
	function init() {
		initDropdowns();
		initPageActions();
		initVisibilityListener();
		startPolling();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

})();
