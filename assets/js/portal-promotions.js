/**
 * Ashrafco Admin Portal - Promotions Management Controller
 *
 * Handles live search, status/type filtering, sorting, pagination,
 * summary metrics updating, Add/Edit Promotion, Product Selector,
 * View Details, Toggle Status, Priority Reordering, and Safe Deletion.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function() {
	'use strict';

	var state = {
		search: '',
		status: '',
		type: '',
		sort: 'newest',
		page: 1,
		perPage: 10,
		total: 0,
		totalPages: 1,
		loading: false,
		selectedProductIds: [],
		allStoreProducts: []
	};

	var activeActionMenu = null;
	var wpMediaFrame = null;

	document.addEventListener('DOMContentLoaded', function() {
		initFilters();
		initPagination();
		initAddEditModal();
		initDetailsModal();
		initDeleteModal();
		initGlobalMenuCloser();
		initPageSizeSelector();

		// Auto load if initial active tab is promotions
		var promoTab = document.getElementById('portal-tab-promotions');
		if (promoTab && promoTab.classList.contains('active')) {
			fetchPromotions();
		}
	});

	// Global hook so switchPortalTab can trigger reload
	window.AshrafcoPromotionsReload = function() {
		fetchPromotions();
	};

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-promos-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-promos-toast';
		var bgClass = type === 'success' ? 'bg-slate-900 border-emerald-500 text-white' : 'bg-rose-900 border-rose-500 text-white';
		toast.className = 'fixed bottom-6 left-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl border shadow-xl text-xs font-bold transition-all transform duration-300 ' + bgClass;

		var icon = type === 'success' ? '✓ ' : '⚠ ';
		toast.innerHTML = '<span>' + icon + '</span><span>' + message + '</span>';
		document.body.appendChild(toast);

		setTimeout(function() {
			toast.classList.add('opacity-0', 'translate-y-2');
			setTimeout(function() { toast.remove(); }, 300);
		}, 4000);
	}

	/**
	 * Setup search and dropdown filter event listeners
	 */
	function initFilters() {
		var searchInput  = document.getElementById('portal-promos-search');
		var statusFilter = document.getElementById('portal-promos-filter-status');
		var typeFilter   = document.getElementById('portal-promos-filter-type');
		var sortFilter   = document.getElementById('portal-promos-filter-sort');

		var debounceTimer = null;
		if (searchInput) {
			searchInput.addEventListener('input', function() {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(function() {
					state.search = searchInput.value.trim();
					state.page = 1;
					fetchPromotions();
				}, 350);
			});
		}

		if (statusFilter) {
			statusFilter.addEventListener('change', function() {
				state.status = statusFilter.value;
				state.page = 1;
				fetchPromotions();
			});
		}

		if (typeFilter) {
			typeFilter.addEventListener('change', function() {
				state.type = typeFilter.value;
				state.page = 1;
				fetchPromotions();
			});
		}

		if (sortFilter) {
			sortFilter.addEventListener('change', function() {
				state.sort = sortFilter.value;
				state.page = 1;
				fetchPromotions();
			});
		}
	}

	/**
	 * Setup page size dropdown listener
	 */
	function initPageSizeSelector() {
		var perPageSelect = document.getElementById('portal-promos-per-page');
		if (perPageSelect) {
			perPageSelect.addEventListener('change', function() {
				state.perPage = parseInt(perPageSelect.value, 10) || 10;
				state.page = 1;
				fetchPromotions();
			});
		}
	}

	/**
	 * Setup pagination controls
	 */
	function initPagination() {
		var prevBtn = document.getElementById('portal-promos-prev-page');
		var nextBtn = document.getElementById('portal-promos-next-page');

		if (prevBtn) {
			prevBtn.addEventListener('click', function() {
				if (state.page > 1) {
					state.page--;
					fetchPromotions();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function() {
				if (state.page < state.totalPages) {
					state.page++;
					fetchPromotions();
				}
			});
		}
	}

	/**
	 * Fetch promotions via AJAX
	 */
	function fetchPromotions() {
		if (state.loading) return;
		state.loading = true;

		var config = window.AshrafcoPromotionsConfig || {};
		var tbody = document.getElementById('portal-promos-tbody');
		var loadingOverlay = document.getElementById('portal-promos-loading');

		if (loadingOverlay) loadingOverlay.classList.remove('hidden');

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_promotions');
		formData.append('nonce', config.nonce || '');
		formData.append('search', state.search);
		formData.append('status', state.status);
		formData.append('type', state.type);
		formData.append('sort', state.sort);
		formData.append('page', state.page);
		formData.append('per_page', state.perPage);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');

			if (data.success && data.data) {
				var res = data.data;
				state.total = res.total;
				state.totalPages = res.total_pages;
				state.page = res.current_page;

				renderPromotions(res.items);
				updateSummaryCards(res.summary);
				renderPaginationUI();
				updateResultCounter(res.items.length, res.total);
			} else {
				showToast(data.data ? data.data.message : 'فشل تحميل العروض الترويجية', 'error');
			}
		})
		.catch(function(err) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');
			showToast('حدث خطأ في الاتصال بالسيرفر.', 'error');
		});
	}

	/**
	 * Update the 4 summary cards
	 */
	function updateSummaryCards(summary) {
		if (!summary) return;
		var totalEl     = document.getElementById('portal-promos-count-total');
		var activeEl    = document.getElementById('portal-promos-count-active');
		var scheduledEl = document.getElementById('portal-promos-count-scheduled');
		var expiredEl   = document.getElementById('portal-promos-count-expired');

		if (totalEl) totalEl.textContent = summary.total || 0;
		if (activeEl) activeEl.textContent = summary.active || 0;
		if (scheduledEl) scheduledEl.textContent = summary.scheduled || 0;
		if (expiredEl) expiredEl.textContent = summary.expired || 0;
	}

	/**
	 * Render result counter (e.g. "عرض 5 من أصل 5 عروض")
	 */
	function updateResultCounter(shownCount, totalCount) {
		var counterEl = document.getElementById('portal-promos-result-counter');
		if (counterEl) {
			if (totalCount === 0) {
				counterEl.textContent = 'لا توجد نتائج';
			} else {
				counterEl.textContent = 'عرض ' + shownCount + ' من أصل ' + totalCount + ' عرضاً';
			}
		}
	}

	/**
	 * Render the promotions table rows
	 */
	function renderPromotions(items) {
		var tbody = document.getElementById('portal-promos-tbody');
		var emptyState = document.getElementById('portal-promos-empty-state');
		var paginationBar = document.getElementById('portal-promos-pagination-bar');

		if (!tbody) return;
		tbody.innerHTML = '';

		if (!items || items.length === 0) {
			if (emptyState) emptyState.classList.remove('hidden');
			if (paginationBar) paginationBar.classList.add('hidden');
			return;
		}

		if (emptyState) emptyState.classList.add('hidden');
		if (paginationBar) paginationBar.classList.remove('hidden');

		items.forEach(function(item) {
			var tr = document.createElement('tr');
			tr.className = 'hover:bg-slate-50/70 border-b border-slate-100 transition-colors text-right';
			tr.id = 'promo-row-' + item.id;

			// 1. Column: Promotion (Image + Title + Description)
			var tdPromo = document.createElement('td');
			tdPromo.className = 'py-4 px-4 sm:px-6';

			var imgHtml = '';
			if (item.thumbnail_url) {
				imgHtml = '<img src="' + escapeHtml(item.thumbnail_url) + '" alt="' + escapeHtml(item.title) + '" class="w-14 h-11 sm:w-16 sm:h-12 rounded-xl object-cover border border-slate-200/80 shadow-2xs flex-shrink-0">';
			} else {
				imgHtml = '<div class="w-14 h-11 sm:w-16 sm:h-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center flex-shrink-0 shadow-2xs">' +
					'<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg></div>';
			}

			tdPromo.innerHTML =
				'<div class="flex items-center gap-3.5">' +
					imgHtml +
					'<div class="min-w-0 flex-1">' +
						'<div class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight truncate">' + escapeHtml(item.title) + '</div>' +
						(item.description ? '<div class="text-[11px] text-slate-400 font-medium truncate max-w-xs mt-0.5">' + escapeHtml(stripTags(item.description)) + '</div>' : '') +
					'</div>' +
				'</div>';
			tr.appendChild(tdPromo);

			// 2. Column: Type badge
			var tdType = document.createElement('td');
			tdType.className = 'py-4 px-3 sm:px-4 whitespace-nowrap';
			tdType.innerHTML = renderTypeBadge(item.type, item.badge_label);
			tr.appendChild(tdType);

			// 3. Column: Discount Value (bold red text)
			var tdDiscount = document.createElement('td');
			tdDiscount.className = 'py-4 px-3 sm:px-4 whitespace-nowrap';
			tdDiscount.innerHTML = '<span class="text-xs sm:text-sm font-black text-rose-600 tracking-tight">' + escapeHtml(item.discount_label) + '</span>';
			tr.appendChild(tdDiscount);

			// 4. Column: Date range (calendar icon + dates)
			var tdDates = document.createElement('td');
			tdDates.className = 'py-4 px-3 sm:px-4 whitespace-nowrap font-mono text-xs text-slate-500';
			tdDates.innerHTML =
				'<div class="inline-flex items-center gap-1.5">' +
					'<svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>' +
					'<span>' + escapeHtml(item.date_range_text) + '</span>' +
				'</div>';
			tr.appendChild(tdDates);

			// 5. Column: Products count
			var tdProducts = document.createElement('td');
			tdProducts.className = 'py-4 px-3 sm:px-4 whitespace-nowrap text-xs font-bold text-slate-700';
			tdProducts.textContent = item.products_label;
			tr.appendChild(tdProducts);

			// 6. Column: Status pill
			var tdStatus = document.createElement('td');
			tdStatus.className = 'py-4 px-3 sm:px-4 whitespace-nowrap';
			tdStatus.innerHTML = renderStatusBadge(item.status, item.status_label);
			tr.appendChild(tdStatus);

			// 7. Column: Priority with up/down arrows
			var tdPriority = document.createElement('td');
			tdPriority.className = 'py-4 px-3 sm:px-4 whitespace-nowrap';
			tdPriority.innerHTML =
				'<div class="inline-flex items-center gap-2 font-mono text-xs font-extrabold text-slate-700">' +
					'<span>' + item.priority + '</span>' +
					'<div class="flex flex-col gap-0.5 text-rose-600">' +
						'<button type="button" class="promo-reorder-btn hover:text-rose-800 transition-colors cursor-pointer leading-none" data-id="' + item.id + '" data-dir="up" title="رفع الأولوية">▲</button>' +
						'<button type="button" class="promo-reorder-btn hover:text-rose-800 transition-colors cursor-pointer leading-none" data-id="' + item.id + '" data-dir="down" title="خفض الأولوية">▼</button>' +
					'</div>' +
				'</div>';
			tr.appendChild(tdPriority);

			// 8. Column: Actions Menu (3 dots)
			var tdActions = document.createElement('td');
			tdActions.className = 'py-4 px-4 sm:px-6 whitespace-nowrap text-left relative';
			tdActions.innerHTML =
				'<div class="relative inline-block text-right">' +
					'<button type="button" class="promo-menu-toggle-btn w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/80 flex items-center justify-center transition-all shadow-2xs" data-id="' + item.id + '">' +
						'<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>' +
					'</button>' +
					'<div id="promo-menu-' + item.id + '" class="promo-action-menu hidden absolute left-0 top-full mt-1.5 w-44 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-30 text-xs font-bold space-y-0.5">' +
						'<button type="button" class="promo-act-view w-full text-right px-3.5 py-2 text-slate-700 hover:bg-slate-50 flex items-center gap-2" data-id="' + item.id + '">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>' +
							'<span>عرض التفاصيل</span>' +
						'</button>' +
						'<button type="button" class="promo-act-edit w-full text-right px-3.5 py-2 text-slate-700 hover:bg-slate-50 flex items-center gap-2" data-id="' + item.id + '">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>' +
							'<span>تعديل العرض</span>' +
						'</button>' +
						'<button type="button" class="promo-act-toggle w-full text-right px-3.5 py-2 ' + (item.is_active ? 'text-amber-700 hover:bg-amber-50' : 'text-emerald-700 hover:bg-emerald-50') + ' flex items-center gap-2" data-id="' + item.id + '">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>' +
							'<span>' + (item.is_active ? 'إيقاف العرض' : 'تفعيل العرض') + '</span>' +
						'</button>' +
						'<div class="h-px bg-slate-100 my-1"></div>' +
						'<button type="button" class="promo-act-del w-full text-right px-3.5 py-2 text-rose-600 hover:bg-rose-50 flex items-center gap-2" data-id="' + item.id + '" data-title="' + escapeHtml(item.title) + '">' +
							'<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
							'<span>حذف العرض</span>' +
						'</button>' +
					'</div>' +
				'</div>';
			tr.appendChild(tdActions);

			tbody.appendChild(tr);
		});

		bindTableEvents();
	}

	/**
	 * Render HTML badge for promotion type
	 */
	function renderTypeBadge(type, label) {
		var config = {
			percentage: {
				cls: 'bg-purple-50 text-purple-700 border-purple-200/80',
				icon: '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m5 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" /></svg>',
				fallback: 'خصم نسبة مئوية %'
			},
			fixed: {
				cls: 'bg-blue-50 text-blue-700 border-blue-200/80',
				icon: '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>',
				fallback: 'خصم مبلغ ثابت'
			},
			featured: {
				cls: 'bg-rose-50 text-rose-700 border-rose-200/80',
				icon: '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>',
				fallback: 'صفقة مميزة'
			}
		};

		var c = config[type] || config.percentage;
		var txt = label || c.fallback;

		return '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border ' + c.cls + ' shadow-2xs whitespace-nowrap">' +
			c.icon + '<span>' + escapeHtml(txt) + '</span></span>';
	}

	/**
	 * Render HTML badge for status
	 */
	function renderStatusBadge(status, label) {
		var config = {
			active:    { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200/80', dot: 'bg-emerald-500', fallback: 'نشط' },
			scheduled: { badge: 'bg-amber-50 text-amber-800 border-amber-200/80', dot: 'bg-amber-500', fallback: 'مجدول' },
			expired:   { badge: 'bg-slate-100 text-slate-600 border-slate-200', dot: 'bg-slate-400', fallback: 'منتهي' },
			inactive:  { badge: 'bg-rose-50 text-rose-700 border-rose-200/80', dot: 'bg-rose-500', fallback: 'معطل' }
		};

		var c = config[status] || config.active;
		var txt = label || c.fallback;

		return '<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border ' + c.badge + ' shadow-2xs whitespace-nowrap">' +
			'<span class="w-1.5 h-1.5 rounded-full ' + c.dot + ' flex-shrink-0"></span>' + escapeHtml(txt) + '</span>';
	}

	/**
	 * Bind events on dynamic table rows
	 */
	function bindTableEvents() {
		// Priority up/down buttons
		document.querySelectorAll('.promo-reorder-btn').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				var id = btn.getAttribute('data-id');
				var dir = btn.getAttribute('data-dir');
				reorderPromotion(id, dir);
			});
		});

		// Three dots action menu toggle
		document.querySelectorAll('.promo-menu-toggle-btn').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				var id = btn.getAttribute('data-id');
				var menu = document.getElementById('promo-menu-' + id);
				if (!menu) return;

				if (activeActionMenu && activeActionMenu !== menu) {
					activeActionMenu.classList.add('hidden');
				}

				menu.classList.toggle('hidden');
				activeActionMenu = menu.classList.contains('hidden') ? null : menu;
			});
		});

		// View Details
		document.querySelectorAll('.promo-act-view').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var id = btn.getAttribute('data-id');
				openDetailsModal(id);
			});
		});

		// Edit Promotion
		document.querySelectorAll('.promo-act-edit').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var id = btn.getAttribute('data-id');
				openEditModal(id);
			});
		});

		// Toggle Status
		document.querySelectorAll('.promo-act-toggle').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var id = btn.getAttribute('data-id');
				togglePromotionStatus(id);
			});
		});

		// Delete Promotion
		document.querySelectorAll('.promo-act-del').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var id = btn.getAttribute('data-id');
				var title = btn.getAttribute('data-title');
				openDeleteModal(id, title);
			});
		});
	}

	/**
	 * Reorder promotion priority via AJAX
	 */
	function reorderPromotion(id, direction) {
		var config = window.AshrafcoPromotionsConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_reorder_promotion');
		formData.append('nonce', config.nonce || '');
		formData.append('id', id);
		formData.append('direction', direction);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			if (data.success) {
				fetchPromotions();
			} else {
				showToast(data.data ? data.data.message : 'فشل تحديث الأولوية', 'error');
			}
		});
	}

	/**
	 * Toggle status via AJAX
	 */
	function togglePromotionStatus(id) {
		var config = window.AshrafcoPromotionsConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_toggle_promotion_status');
		formData.append('nonce', config.nonce || '');
		formData.append('id', id);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			if (data.success) {
				showToast(data.data.message, 'success');
				fetchPromotions();
			} else {
				showToast(data.data ? data.data.message : 'فشل تغيير حالة العرض', 'error');
			}
		});
	}

	/**
	 * Render pagination buttons (< 1 2 >)
	 */
	function renderPaginationUI() {
		var prevBtn = document.getElementById('portal-promos-prev-page');
		var nextBtn = document.getElementById('portal-promos-next-page');
		var container = document.getElementById('portal-promos-pages-container');

		if (prevBtn) prevBtn.disabled = (state.page <= 1);
		if (nextBtn) nextBtn.disabled = (state.page >= state.totalPages);

		if (!container) return;
		container.innerHTML = '';

		var startPage = Math.max(1, state.page - 2);
		var endPage = Math.min(state.totalPages, startPage + 4);
		if (endPage - startPage < 4) {
			startPage = Math.max(1, endPage - 4);
		}

		for (var p = startPage; p <= endPage; p++) {
			(function(pageNum) {
				var btn = document.createElement('button');
				btn.type = 'button';
				btn.className = (pageNum === state.page)
					? 'w-8 h-8 rounded-xl bg-primary text-white font-extrabold text-xs shadow-xs'
					: 'w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-2xs';
				btn.textContent = pageNum;
				btn.addEventListener('click', function() {
					if (state.page !== pageNum) {
						state.page = pageNum;
						fetchPromotions();
					}
				});
				container.appendChild(btn);
			})(p);
		}
	}

	/**
	 * =========================================================================
	 * MODAL: CREATE / EDIT PROMOTION
	 * =========================================================================
	 */
	function initAddEditModal() {
		var addBtn   = document.getElementById('portal-add-promo-btn');
		var emptyAddBtn = document.getElementById('portal-promos-empty-add-btn');
		var modal    = document.getElementById('portal-promo-crud-modal');
		var closeBtn = document.getElementById('portal-promo-crud-close-btn');
		var cancelBtn = document.getElementById('portal-promo-crud-cancel-btn');
		var form     = document.getElementById('portal-promo-crud-form');

		if (addBtn) {
			addBtn.addEventListener('click', function() {
				openCreateModal();
			});
		}
		if (emptyAddBtn) {
			emptyAddBtn.addEventListener('click', function() {
				openCreateModal();
			});
		}
		if (closeBtn) closeBtn.addEventListener('click', closeCrudModal);
		if (cancelBtn) cancelBtn.addEventListener('click', closeCrudModal);

		// Target scope selector toggle (Products vs Category vs All)
		var targetTypeSelect = document.getElementById('portal-promo-target-type');
		var productsSection  = document.getElementById('portal-promo-products-section');
		var categorySection  = document.getElementById('portal-promo-category-section');

		if (targetTypeSelect) {
			targetTypeSelect.addEventListener('change', function() {
				var val = targetTypeSelect.value;
				if (productsSection) {
					productsSection.classList.toggle('hidden', val !== 'products');
				}
				if (categorySection) {
					categorySection.classList.toggle('hidden', val !== 'category');
				}
			});
		}

		// Product search within modal
		var prodSearchInput = document.getElementById('portal-promo-product-search');
		var prodDebounce = null;
		if (prodSearchInput) {
			prodSearchInput.addEventListener('input', function() {
				clearTimeout(prodDebounce);
				prodDebounce = setTimeout(function() {
					searchProductsForSelector(prodSearchInput.value.trim());
				}, 300);
			});
		}

		// Image upload / media selector
		var selectImgBtn = document.getElementById('portal-promo-select-img-btn');
		var removeImgBtn = document.getElementById('portal-promo-remove-img-btn');
		var imgIdInput   = document.getElementById('portal-promo-img-id');
		var previewImg   = document.getElementById('portal-promo-img-preview');
		var imgPlaceholder = document.getElementById('portal-promo-img-placeholder');

		if (selectImgBtn && typeof wp !== 'undefined' && wp.media) {
			selectImgBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (wpMediaFrame) {
					wpMediaFrame.open();
					return;
				}
				wpMediaFrame = wp.media({
					title: 'اختر صورة العرض الترويجي',
					button: { text: 'استخدام هذه الصورة' },
					multiple: false
				});
				wpMediaFrame.on('select', function() {
					var attachment = wpMediaFrame.state().get('selection').first().toJSON();
					if (imgIdInput) imgIdInput.value = attachment.id;
					if (previewImg) {
						previewImg.src = attachment.url;
						previewImg.classList.remove('hidden');
					}
					if (imgPlaceholder) imgPlaceholder.classList.add('hidden');
					if (removeImgBtn) removeImgBtn.classList.remove('hidden');
				});
				wpMediaFrame.open();
			});
		}

		if (removeImgBtn) {
			removeImgBtn.addEventListener('click', function(e) {
				e.preventDefault();
				if (imgIdInput) imgIdInput.value = '0';
				if (previewImg) {
					previewImg.src = '';
					previewImg.classList.add('hidden');
				}
				if (imgPlaceholder) imgPlaceholder.classList.remove('hidden');
				removeImgBtn.classList.add('hidden');
			});
		}

		// Form submission (Create / Update)
		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				submitPromotionForm();
			});
		}
	}

	function openCreateModal() {
		var modal = document.getElementById('portal-promo-crud-modal');
		var titleEl = document.getElementById('portal-promo-crud-title');
		var submitBtn = document.getElementById('portal-promo-submit-btn');

		if (titleEl) titleEl.textContent = 'إضافة عرض ترويجي جديد';
		if (submitBtn) submitBtn.textContent = 'حفظ العرض';

		// Reset form fields
		document.getElementById('portal-promo-crud-id').value = '0';
		document.getElementById('portal-promo-crud-action').value = 'create';
		document.getElementById('portal-promo-title').value = '';
		document.getElementById('portal-promo-description').value = '';
		document.getElementById('portal-promo-type').value = 'percentage';
		document.getElementById('portal-promo-discount').value = '';
		document.getElementById('portal-promo-target-type').value = 'products';
		document.getElementById('portal-promo-category-id').value = '0';
		document.getElementById('portal-promo-priority').value = '1';
		document.getElementById('portal-promo-banner-url').value = '';
		document.getElementById('portal-promo-is-active').checked = true;

		// Today's date as default start date
		var today = new Date().toISOString().split('T')[0];
		document.getElementById('portal-promo-start-date').value = today;
		document.getElementById('portal-promo-end-date').value = '';

		// Reset Image
		document.getElementById('portal-promo-img-id').value = '0';
		var preview = document.getElementById('portal-promo-img-preview');
		var placeholder = document.getElementById('portal-promo-img-placeholder');
		var removeBtn = document.getElementById('portal-promo-remove-img-btn');
		if (preview) { preview.src = ''; preview.classList.add('hidden'); }
		if (placeholder) placeholder.classList.remove('hidden');
		if (removeBtn) removeBtn.classList.add('hidden');

		// Reset products selection
		state.selectedProductIds = [];
		renderSelectedProductChips();
		searchProductsForSelector('');

		// Trigger target type visibility
		var targetTypeSelect = document.getElementById('portal-promo-target-type');
		if (targetTypeSelect) {
			targetTypeSelect.dispatchEvent(new Event('change'));
		}

		if (modal) modal.classList.remove('hidden');
	}

	function openEditModal(promoId) {
		var config = window.AshrafcoPromotionsConfig || {};
		var modal = document.getElementById('portal-promo-crud-modal');
		var titleEl = document.getElementById('portal-promo-crud-title');
		var submitBtn = document.getElementById('portal-promo-submit-btn');

		if (titleEl) titleEl.textContent = 'تعديل العرض الترويجي';
		if (submitBtn) submitBtn.textContent = 'تحديث بيانات العرض';

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_promotion_details');
		formData.append('nonce', config.nonce || '');
		formData.append('id', promoId);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			if (data.success && data.data && data.data.promotion) {
				var promo = data.data.promotion;

				document.getElementById('portal-promo-crud-id').value = promo.id;
				document.getElementById('portal-promo-crud-action').value = 'edit';
				document.getElementById('portal-promo-title').value = promo.title || '';
				document.getElementById('portal-promo-description').value = promo.description || '';
				document.getElementById('portal-promo-type').value = promo.type || 'percentage';
				document.getElementById('portal-promo-discount').value = promo.discount_value || '';
				document.getElementById('portal-promo-target-type').value = promo.target_type || 'products';
				document.getElementById('portal-promo-category-id').value = promo.category_id || '0';
				document.getElementById('portal-promo-start-date').value = promo.start_date || '';
				document.getElementById('portal-promo-end-date').value = promo.end_date || '';
				document.getElementById('portal-promo-priority').value = promo.priority || 1;
				document.getElementById('portal-promo-banner-url').value = promo.banner_url || '';
				document.getElementById('portal-promo-is-active').checked = !!promo.is_active;

				// Image
				document.getElementById('portal-promo-img-id').value = promo.image_id || 0;
				var preview = document.getElementById('portal-promo-img-preview');
				var placeholder = document.getElementById('portal-promo-img-placeholder');
				var removeBtn = document.getElementById('portal-promo-remove-img-btn');

				if (promo.thumbnail_url) {
					if (preview) { preview.src = promo.thumbnail_url; preview.classList.remove('hidden'); }
					if (placeholder) placeholder.classList.add('hidden');
					if (removeBtn) removeBtn.classList.remove('hidden');
				} else {
					if (preview) { preview.src = ''; preview.classList.add('hidden'); }
					if (placeholder) placeholder.classList.remove('hidden');
					if (removeBtn) removeBtn.classList.add('hidden');
				}

				// Product IDs
				state.selectedProductIds = Array.isArray(promo.product_ids) ? promo.product_ids.slice() : [];
				renderSelectedProductChips();
				searchProductsForSelector('');

				var targetTypeSelect = document.getElementById('portal-promo-target-type');
				if (targetTypeSelect) {
					targetTypeSelect.dispatchEvent(new Event('change'));
				}

				if (modal) modal.classList.remove('hidden');
			} else {
				showToast('تعذر تحميل بيانات العرض.', 'error');
			}
		});
	}

	function closeCrudModal() {
		var modal = document.getElementById('portal-promo-crud-modal');
		if (modal) modal.classList.add('hidden');
	}

	/**
	 * Search store products for the modal selector
	 */
	function searchProductsForSelector(query) {
		var config = window.AshrafcoPromotionsConfig || {};
		var listContainer = document.getElementById('portal-promo-products-list');
		if (!listContainer) return;

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_search_products');
		formData.append('nonce', config.nonce || '');
		formData.append('query', query || '');

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			if (data.success && data.data && data.data.items) {
				state.allStoreProducts = data.data.items;
				renderProductSelectorList(data.data.items);
			}
		});
	}

	/**
	 * Render product items in the selector with checkboxes
	 */
	function renderProductSelectorList(products) {
		var listContainer = document.getElementById('portal-promo-products-list');
		if (!listContainer) return;

		listContainer.innerHTML = '';

		if (!products || products.length === 0) {
			listContainer.innerHTML = '<div class="py-4 text-center text-xs text-slate-400 font-medium">لم يتم العثور على منتجات مطابقة.</div>';
			return;
		}

		products.forEach(function(p) {
			var isChecked = state.selectedProductIds.indexOf(p.id) !== -1;
			var row = document.createElement('label');
			row.className = 'flex items-center justify-between p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors cursor-pointer text-right';

			var imgHtml = p.thumbnail_url
				? '<img src="' + escapeHtml(p.thumbnail_url) + '" class="w-9 h-9 rounded-lg object-cover border border-slate-200 flex-shrink-0">'
				: '<div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center flex-shrink-0 text-xs font-bold">📦</div>';

			row.innerHTML =
				'<div class="flex items-center gap-2.5 min-w-0 flex-1">' +
					'<input type="checkbox" class="portal-prod-checkbox w-4 h-4 text-primary rounded border-slate-300 focus:ring-rose-500" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '" ' + (isChecked ? 'checked' : '') + '>' +
					imgHtml +
					'<div class="min-w-0 flex-1">' +
						'<div class="text-xs font-bold text-slate-800 truncate">' + escapeHtml(p.name) + '</div>' +
						(p.regular_price ? '<div class="text-[11px] font-mono text-slate-400 mt-0.5">' + p.regular_price + ' ج.م</div>' : '') +
					'</div>' +
				'</div>';

			row.querySelector('.portal-prod-checkbox').addEventListener('change', function(e) {
				var pid = parseInt(p.id, 10);
				if (this.checked) {
					if (state.selectedProductIds.indexOf(pid) === -1) {
						state.selectedProductIds.push(pid);
					}
				} else {
					var idx = state.selectedProductIds.indexOf(pid);
					if (idx !== -1) {
						state.selectedProductIds.splice(idx, 1);
					}
				}
				renderSelectedProductChips();
			});

			listContainer.appendChild(row);
		});
	}

	/**
	 * Render chosen product chips under selector
	 */
	function renderSelectedProductChips() {
		var chipsContainer = document.getElementById('portal-promo-selected-chips');
		var counterEl = document.getElementById('portal-promo-selected-count');

		if (counterEl) {
			counterEl.textContent = state.selectedProductIds.length;
		}

		if (!chipsContainer) return;
		chipsContainer.innerHTML = '';

		if (state.selectedProductIds.length === 0) {
			chipsContainer.innerHTML = '<span class="text-xs text-slate-400 italic">لم يتم اختيار أي منتج بعد.</span>';
			return;
		}

		state.selectedProductIds.forEach(function(pid) {
			var prod = state.allStoreProducts.find(function(item) { return item.id === pid; });
			var name = prod ? prod.name : ('منتج #' + pid);

			var chip = document.createElement('span');
			chip.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200/80 shadow-2xs';
			chip.innerHTML = '<span class="truncate max-w-[150px]">' + escapeHtml(name) + '</span>' +
				'<button type="button" class="text-rose-500 hover:text-rose-700 font-bold leading-none cursor-pointer" data-id="' + pid + '">&times;</button>';

			chip.querySelector('button').addEventListener('click', function(e) {
				e.preventDefault();
				var idx = state.selectedProductIds.indexOf(pid);
				if (idx !== -1) {
					state.selectedProductIds.splice(idx, 1);
					renderSelectedProductChips();
					// Uncheck in list if visible
					var cb = document.querySelector('.portal-prod-checkbox[data-id="' + pid + '"]');
					if (cb) cb.checked = false;
				}
			});

			chipsContainer.appendChild(chip);
		});
	}

	/**
	 * Submit Create or Edit Promotion Form
	 */
	function submitPromotionForm() {
		var config = window.AshrafcoPromotionsConfig || {};
		var promoId = parseInt(document.getElementById('portal-promo-crud-id').value, 10) || 0;
		var action = document.getElementById('portal-promo-crud-action').value;
		var submitBtn = document.getElementById('portal-promo-submit-btn');

		var title = document.getElementById('portal-promo-title').value.trim();
		if (!title) {
			showToast('يرجى كتابة اسم العرض.', 'error');
			return;
		}

		var targetType = document.getElementById('portal-promo-target-type').value;
		if (targetType === 'products' && state.selectedProductIds.length === 0) {
			showToast('يرجى اختيار منتج واحد على الأقل للعرض.', 'error');
			return;
		}

		if (targetType === 'category') {
			var catId = parseInt(document.getElementById('portal-promo-category-id').value, 10);
			if (!catId || catId <= 0) {
				showToast('يرجى اختيار تصنيف صالح للعرض.', 'error');
				return;
			}
		}

		var startDate = document.getElementById('portal-promo-start-date').value;
		var endDate = document.getElementById('portal-promo-end-date').value;
		if (startDate && endDate && endDate < startDate) {
			showToast('تاريخ الانتهاء لا يمكن أن يسبق تاريخ البدء.', 'error');
			return;
		}

		var originalText = submitBtn.textContent;
		submitBtn.disabled = true;
		submitBtn.textContent = 'جارٍ الحفظ...';

		var formData = new FormData();
		var actionName = (action === 'edit' && promoId > 0) ? 'ashrafco_portal_update_promotion' : 'ashrafco_portal_create_promotion';
		formData.append('action', actionName);
		formData.append('nonce', config.nonce || '');
		if (promoId > 0) formData.append('id', promoId);

		formData.append('title', title);
		formData.append('description', document.getElementById('portal-promo-description').value);
		formData.append('type', document.getElementById('portal-promo-type').value);
		formData.append('discount_value', document.getElementById('portal-promo-discount').value || '0');
		formData.append('target_type', targetType);
		formData.append('product_ids', state.selectedProductIds.join(','));
		formData.append('category_id', document.getElementById('portal-promo-category-id').value || '0');
		formData.append('start_date', startDate);
		formData.append('end_date', endDate);
		formData.append('priority', document.getElementById('portal-promo-priority').value || '1');
		formData.append('banner_url', document.getElementById('portal-promo-banner-url').value);
		formData.append('is_active', document.getElementById('portal-promo-is-active').checked ? '1' : '0');
		formData.append('image_id', document.getElementById('portal-promo-img-id').value || '0');

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			submitBtn.disabled = false;
			submitBtn.textContent = originalText;

			if (data.success) {
				showToast(data.data.message || 'تم حفظ العرض بنجاح.', 'success');
				closeCrudModal();
				fetchPromotions();
			} else {
				showToast(data.data ? data.data.message : 'فشل حفظ العرض.', 'error');
			}
		})
		.catch(function(err) {
			submitBtn.disabled = false;
			submitBtn.textContent = originalText;
			showToast('حدث خطأ في معالجة الطلب.', 'error');
		});
	}

	/**
	 * =========================================================================
	 * MODAL: VIEW PROMOTION DETAILS
	 * =========================================================================
	 */
	function initDetailsModal() {
		var modal = document.getElementById('portal-promo-details-modal');
		var closeBtn = document.getElementById('portal-promo-details-close-btn');
		var okBtn = document.getElementById('portal-promo-details-ok-btn');
		var editBtn = document.getElementById('portal-promo-details-edit-btn');

		if (closeBtn) closeBtn.addEventListener('click', closeDetailsModal);
		if (okBtn) okBtn.addEventListener('click', closeDetailsModal);

		if (editBtn) {
			editBtn.addEventListener('click', function() {
				var promoId = editBtn.getAttribute('data-id');
				closeDetailsModal();
				if (promoId) openEditModal(promoId);
			});
		}
	}

	function openDetailsModal(promoId) {
		var config = window.AshrafcoPromotionsConfig || {};
		var modal = document.getElementById('portal-promo-details-modal');

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_promotion_details');
		formData.append('nonce', config.nonce || '');
		formData.append('id', promoId);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(data) {
			if (data.success && data.data && data.data.promotion) {
				var p = data.data.promotion;

				document.getElementById('portal-details-title').textContent = p.title || '';
				document.getElementById('portal-details-desc').textContent = p.description ? stripTags(p.description) : 'لا يوجد وصف تفصيلي.';
				document.getElementById('portal-details-type-badge').innerHTML = renderTypeBadge(p.type, p.badge_label);
				document.getElementById('portal-details-status-badge').innerHTML = renderStatusBadge(p.status, p.status_label);
				document.getElementById('portal-details-discount').textContent = p.discount_label || '';
				document.getElementById('portal-details-dates').textContent = p.date_range_text || '';
				document.getElementById('portal-details-priority').textContent = p.priority || 1;
				document.getElementById('portal-details-scope').textContent = p.target_type === 'products' ? 'منتجات محددة' : (p.target_type === 'category' ? ('تصنيف: ' + (p.category_name || 'عام')) : 'كل منتجات المتجر');

				var bannerImg = document.getElementById('portal-details-banner-img');
				if (bannerImg) {
					if (p.thumbnail_url) {
						bannerImg.src = p.thumbnail_url;
						bannerImg.classList.remove('hidden');
					} else {
						bannerImg.src = '';
						bannerImg.classList.add('hidden');
					}
				}

				// Edit button hook
				var editBtn = document.getElementById('portal-promo-details-edit-btn');
				if (editBtn) editBtn.setAttribute('data-id', p.id);

				// Render products list
				var prodsContainer = document.getElementById('portal-details-products-grid');
				if (prodsContainer) {
					prodsContainer.innerHTML = '';
					var prods = p.products_details || [];
					if (prods.length === 0) {
						prodsContainer.innerHTML = '<div class="text-xs text-slate-400 py-3">لا توجد منتجات مسجلة بهذا العرض.</div>';
					} else {
						prods.forEach(function(prod) {
							var card = document.createElement('div');
							card.className = 'flex items-center gap-3 p-2.5 rounded-xl border border-slate-100 bg-slate-50/60';
							var thumb = prod.thumbnail_url
								? '<img src="' + escapeHtml(prod.thumbnail_url) + '" class="w-10 h-10 rounded-lg object-cover border border-slate-200 flex-shrink-0">'
								: '<div class="w-10 h-10 rounded-lg bg-slate-200 flex items-center justify-center text-xs">📦</div>';

							card.innerHTML = thumb +
								'<div class="min-w-0 flex-1 text-right">' +
									'<div class="text-xs font-bold text-slate-800 truncate">' + escapeHtml(prod.name) + '</div>' +
									'<div class="text-[11px] font-mono text-slate-500 mt-0.5">' + (prod.price ? prod.price + ' ج.م' : '—') + '</div>' +
								'</div>';
							prodsContainer.appendChild(card);
						});
					}
				}

				if (modal) modal.classList.remove('hidden');
			}
		});
	}

	function closeDetailsModal() {
		var modal = document.getElementById('portal-promo-details-modal');
		if (modal) modal.classList.add('hidden');
	}

	/**
	 * =========================================================================
	 * MODAL: DELETE PROMOTION
	 * =========================================================================
	 */
	function initDeleteModal() {
		var modal = document.getElementById('portal-promo-delete-modal');
		var closeBtn = document.getElementById('portal-promo-del-close-btn');
		var cancelBtn = document.getElementById('portal-promo-del-cancel-btn');
		var confirmBtn = document.getElementById('portal-promo-del-confirm-btn');

		if (closeBtn) closeBtn.addEventListener('click', closeDeleteModal);
		if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);

		if (confirmBtn) {
			confirmBtn.addEventListener('click', function() {
				var promoId = confirmBtn.getAttribute('data-id');
				if (!promoId) return;

				confirmBtn.disabled = true;
				confirmBtn.textContent = 'جارٍ الحذف...';

				var config = window.AshrafcoPromotionsConfig || {};
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_delete_promotion');
				formData.append('nonce', config.nonce || '');
				formData.append('id', promoId);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(data) {
					confirmBtn.disabled = false;
					confirmBtn.textContent = 'نعم، احذف العرض';
					closeDeleteModal();

					if (data.success) {
						showToast(data.data.message || 'تم حذف العرض بنجاح.', 'success');
						fetchPromotions();
					} else {
						showToast(data.data ? data.data.message : 'فشل حذف العرض.', 'error');
					}
				})
				.catch(function() {
					confirmBtn.disabled = false;
					confirmBtn.textContent = 'نعم، احذف العرض';
					closeDeleteModal();
					showToast('حدث خطأ أثناء محاولة الحذف.', 'error');
				});
			});
		}
	}

	function openDeleteModal(promoId, promoTitle) {
		var modal = document.getElementById('portal-promo-delete-modal');
		var nameEl = document.getElementById('portal-promo-del-name');
		var confirmBtn = document.getElementById('portal-promo-del-confirm-btn');

		if (nameEl) nameEl.textContent = promoTitle || ('عرض #' + promoId);
		if (confirmBtn) confirmBtn.setAttribute('data-id', promoId);

		if (modal) modal.classList.remove('hidden');
	}

	function closeDeleteModal() {
		var modal = document.getElementById('portal-promo-delete-modal');
		if (modal) modal.classList.add('hidden');
	}

	/**
	 * Outside click closer for open menus & modals
	 */
	function initGlobalMenuCloser() {
		document.addEventListener('click', function(e) {
			if (activeActionMenu && !activeActionMenu.contains(e.target) && !e.target.closest('.promo-menu-toggle-btn')) {
				closeActiveMenu();
			}

			// Outside click close modals
			var crudModal = document.getElementById('portal-promo-crud-modal');
			if (crudModal && !crudModal.classList.contains('hidden') && e.target === crudModal) {
				closeCrudModal();
			}

			var detailsModal = document.getElementById('portal-promo-details-modal');
			if (detailsModal && !detailsModal.classList.contains('hidden') && e.target === detailsModal) {
				closeDetailsModal();
			}

			var delModal = document.getElementById('portal-promo-delete-modal');
			if (delModal && !delModal.classList.contains('hidden') && e.target === delModal) {
				closeDeleteModal();
			}
		});
	}

	function closeActiveMenu() {
		if (activeActionMenu) {
			activeActionMenu.classList.add('hidden');
			activeActionMenu = null;
		}
	}

	/**
	 * Utility: Escape HTML strings to prevent XSS
	 */
	function escapeHtml(str) {
		if (!str) return '';
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	/**
	 * Utility: Strip HTML tags
	 */
	function stripTags(html) {
		var tmp = document.createElement('DIV');
		tmp.innerHTML = html;
		return tmp.textContent || tmp.innerText || '';
	}

})();
