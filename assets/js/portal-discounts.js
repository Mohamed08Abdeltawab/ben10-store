/**
 * Ashrafco Admin Portal - Discount Management Controller
 *
 * Single source of truth: Native WooCommerce product sale prices and schedules.
 * Handles summary metrics, live search, status and category filtering, sorting,
 * server-side pagination, live countdown ticker, Add/Edit discount modals,
 * and single & bulk safe discount removal.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function () {
	'use strict';

	var state = {
		search: '',
		status: 'all',
		category: '',
		sort: 'modified',
		page: 1,
		perPage: 10,
		total: 0,
		totalPages: 1,
		loading: false,
		selectedProductIds: [],
		activeEditProduct: null,
		countdownInterval: null
	};

	var activeActionMenu = null;

	document.addEventListener('DOMContentLoaded', function () {
		initFilters();
		initPagination();
		initAddModal();
		initEditModal();
		initRemoveModal();
		initBulkActions();
		initCardQuickFilters();
		initGlobalMenuCloser();
		startCountdownLoop();

		// Auto load if initial active tab is discounts
		var discountsTab = document.getElementById('portal-tab-discounts');
		if (discountsTab && discountsTab.classList.contains('active')) {
			fetchDiscounts();
		}
	});

	// Global hook so switchPortalTab can trigger reload
	window.AshrafcoDiscountsReload = function () {
		fetchDiscounts();
	};

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-discounts-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-discounts-toast';
		var bgClass = type === 'success'
			? 'bg-slate-900 border-emerald-500 text-white'
			: 'bg-rose-900 border-rose-500 text-white';

		toast.className = 'fixed bottom-6 left-6 z-[9999] flex items-center gap-3 px-4 py-3 rounded-xl border shadow-xl text-xs font-bold transition-all transform duration-300 ' + bgClass;

		var icon = type === 'success' ? '✓ ' : '⚠ ';
		toast.innerHTML = '<span>' + icon + '</span><span>' + message + '</span>';
		document.body.appendChild(toast);

		setTimeout(function () {
			toast.classList.add('opacity-0', 'translate-y-2');
			setTimeout(function () { toast.remove(); }, 300);
		}, 4000);
	}

	/**
	 * Setup search and dropdown filter event listeners
	 */
	function initFilters() {
		var searchInput  = document.getElementById('portal-discounts-search');
		var statusFilter = document.getElementById('portal-discounts-filter-status');
		var catFilter    = document.getElementById('portal-discounts-filter-category');
		var sortFilter   = document.getElementById('portal-discounts-filter-sort');
		var resetBtn     = document.getElementById('portal-discounts-reset-filters');

		var debounceTimer = null;
		if (searchInput) {
			searchInput.addEventListener('input', function () {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(function () {
					state.search = searchInput.value.trim();
					state.page = 1;
					fetchDiscounts();
				}, 350);
			});
		}

		if (statusFilter) {
			statusFilter.addEventListener('change', function () {
				state.status = statusFilter.value;
				state.page = 1;
				fetchDiscounts();
			});
		}

		if (catFilter) {
			catFilter.addEventListener('change', function () {
				state.category = catFilter.value;
				state.page = 1;
				fetchDiscounts();
			});
		}

		if (sortFilter) {
			sortFilter.addEventListener('change', function () {
				state.sort = sortFilter.value;
				state.page = 1;
				fetchDiscounts();
			});
		}

		if (resetBtn) {
			resetBtn.addEventListener('click', function () {
				if (searchInput) searchInput.value = '';
				if (statusFilter) statusFilter.value = 'all';
				if (catFilter) catFilter.value = '';
				if (sortFilter) sortFilter.value = 'modified';

				state.search = '';
				state.status = 'all';
				state.category = '';
				state.sort = 'modified';
				state.page = 1;
				fetchDiscounts();
			});
		}
	}

	/**
	 * Summary card contextual quick filter links
	 */
	function initCardQuickFilters() {
		var cardTotal = document.getElementById('portal-discounts-card-total-link');
		var cardActive = document.getElementById('portal-discounts-card-active-link');
		var cardScheduled = document.getElementById('portal-discounts-card-scheduled-link');
		var cardEnding = document.getElementById('portal-discounts-card-ending-link');

		var statusSelect = document.getElementById('portal-discounts-filter-status');

		function applyStatusFilter(statusVal) {
			state.status = statusVal;
			state.page = 1;
			if (statusSelect) {
				statusSelect.value = statusVal;
			}
			fetchDiscounts();
		}

		if (cardTotal) {
			cardTotal.addEventListener('click', function (e) {
				e.preventDefault();
				applyStatusFilter('all');
			});
		}
		if (cardActive) {
			cardActive.addEventListener('click', function (e) {
				e.preventDefault();
				applyStatusFilter('active');
			});
		}
		if (cardScheduled) {
			cardScheduled.addEventListener('click', function (e) {
				e.preventDefault();
				applyStatusFilter('scheduled');
			});
		}
		if (cardEnding) {
			cardEnding.addEventListener('click', function (e) {
				e.preventDefault();
				applyStatusFilter('ending_soon');
			});
		}
	}

	/**
	 * Setup pagination controls
	 */
	function initPagination() {
		var prevBtn = document.getElementById('portal-discounts-prev-page');
		var nextBtn = document.getElementById('portal-discounts-next-page');

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				if (state.page > 1) {
					state.page--;
					fetchDiscounts();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				if (state.page < state.totalPages) {
					state.page++;
					fetchDiscounts();
				}
			});
		}
	}

	/**
	 * Fetch discounts catalog via AJAX
	 */
	function fetchDiscounts() {
		if (state.loading) return;
		state.loading = true;

		var config = window.AshrafcoDiscountsConfig || {};
		var loadingOverlay = document.getElementById('portal-discounts-loading');
		if (loadingOverlay) loadingOverlay.classList.remove('hidden');

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_discounts');
		formData.append('nonce', config.nonce || '');
		formData.append('search', state.search);
		formData.append('status', state.status);
		formData.append('category', state.category);
		formData.append('orderby', state.sort);
		formData.append('page', state.page);
		formData.append('per_page', state.perPage);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function (res) { return res.json(); })
		.then(function (data) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');

			if (data.success && data.data) {
				var res = data.data;
				state.total = res.pagination.total;
				state.totalPages = res.pagination.total_pages;
				state.page = res.pagination.current_page;

				updateSummaryCards(res.counts);
				populateCategoryFilter(res.categories);
				renderTable(res.items, res.pagination);
				renderPaginationControls(res.pagination);
			} else {
				showToast(data.data ? data.data.message : 'فشل تحميل قائمة الخصومات', 'error');
			}
		})
		.catch(function (err) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');
			showToast('حدث خطأ في الاتصال بالسيرفر', 'error');
		});
	}

	/**
	 * Update the 4 summary cards and navigation badge with real numbers
	 */
	function updateSummaryCards(counts) {
		if (!counts) return;

		var elTotal     = document.getElementById('portal-discounts-count-total');
		var elActive    = document.getElementById('portal-discounts-count-active');
		var elScheduled = document.getElementById('portal-discounts-count-scheduled');
		var elEnding    = document.getElementById('portal-discounts-count-ending');
		var navBadge    = document.getElementById('portal-tab-discounts-badge');

		if (elTotal)     elTotal.textContent     = counts.total || 0;
		if (elActive)    elActive.textContent    = counts.active || 0;
		if (elScheduled) elScheduled.textContent = counts.scheduled || 0;
		if (elEnding)    elEnding.textContent    = counts.ending_soon || 0;

		if (navBadge) {
			navBadge.textContent = counts.active || 0;
			if ((counts.active || 0) > 0) {
				navBadge.classList.remove('hidden');
			} else {
				navBadge.classList.add('hidden');
			}
		}
	}

	/**
	 * Populate Category Filter Dropdown if empty
	 */
	function populateCategoryFilter(categories) {
		var catSelect = document.getElementById('portal-discounts-filter-category');
		if (!catSelect || !categories || catSelect.dataset.populated === 'true') return;

		var currentVal = catSelect.value;
		while (catSelect.options.length > 1) {
			catSelect.remove(1);
		}

		categories.forEach(function (c) {
			var opt = document.createElement('option');
			opt.value = c.slug;
			opt.textContent = c.name + ' (' + c.count + ')';
			catSelect.appendChild(opt);
		});

		catSelect.dataset.populated = 'true';
		catSelect.value = currentVal;
	}

	/**
	 * Render the products discounts table matching the reference design
	 */
	function renderTable(items, pagination) {
		var tbody = document.getElementById('portal-discounts-tbody');
		var emptyState = document.getElementById('portal-discounts-empty-state');
		var tableWrapper = document.getElementById('portal-discounts-table-wrapper');
		var footer = document.getElementById('portal-discounts-footer');
		var resultsText = document.getElementById('portal-discounts-results-count');

		if (!tbody) return;

		// Clear selected rows
		state.selectedProductIds = [];
		updateBulkBar();
		var selectAll = document.getElementById('portal-discounts-select-all');
		if (selectAll) selectAll.checked = false;

		if (!items || items.length === 0) {
			tbody.innerHTML = '';
			if (tableWrapper) tableWrapper.classList.add('hidden');
			if (emptyState) emptyState.classList.remove('hidden');
			if (footer) footer.classList.add('hidden');
			return;
		}

		if (tableWrapper) tableWrapper.classList.remove('hidden');
		if (emptyState) emptyState.classList.add('hidden');
		if (footer) footer.classList.remove('hidden');

		if (resultsText && pagination) {
			resultsText.textContent = 'عرض ' + pagination.from + ' - ' + pagination.to + ' من ' + pagination.total + ' خصم';
		}

		var html = '';
		items.forEach(function (item) {
			html += renderTableRow(item);
		});

		tbody.innerHTML = html;
		attachRowEventListeners();
	}

	/**
	 * Render a single table row matching screenshot column structure
	 */
	function renderTableRow(item) {
		// 1. Sale Period Cell
		var periodHtml = '';
		if (item.status === 'active') {
			if (item.countdown_seconds > 0) {
				periodHtml = '<div class="flex items-center gap-1.5 text-xs font-bold text-rose-600">' +
					'<svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' +
					'<span class="portal-countdown-label font-sans" data-end="' + item.sale_to_ts + '" data-product-id="' + item.id + '">' +
						escapeHtml(item.period_formatted) +
					'</span>' +
				'</div>';
			} else {
				periodHtml = '<div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">' +
					'<span class="text-sm font-bold leading-none select-none">∞</span>' +
					'<span>بدون تاريخ انتهاء</span>' +
				'</div>';
			}
		} else if (item.status === 'scheduled') {
			periodHtml = '<div class="flex items-center gap-1.5 text-xs font-medium text-blue-600">' +
				'<svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>' +
				'<span>' + escapeHtml(item.period_formatted) + '</span>' +
			'</div>';
		} else if (item.status === 'expired') {
			periodHtml = '<div class="text-xs font-medium text-slate-400">' +
				escapeHtml(item.period_formatted) +
			'</div>';
		} else {
			periodHtml = '<span class="text-slate-300 font-mono">—</span>';
		}

		// 2. Status Badge / Toggle Pill matching visual reference
		var statusBadgeHtml = '';
		if (item.status === 'active') {
			statusBadgeHtml = '<button type="button" class="portal-discount-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-2xs transition-all cursor-pointer select-none" data-product-id="' + item.id + '" data-status="active" title="انقر لتعطيل الخصم">' +
				'<span>نشط</span>' +
				'<span class="w-7 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-end px-0.5 flex-shrink-0 transition-colors">' +
					'<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>' +
				'</span>' +
			'</button>';
		} else if (item.status === 'paused') {
			statusBadgeHtml = '<button type="button" class="portal-discount-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-slate-100 hover:bg-slate-200 text-slate-500 border border-slate-300 shadow-2xs transition-all cursor-pointer select-none" data-product-id="' + item.id + '" data-status="paused" title="انقر لتفعيل الخصم">' +
				'<span>غير نشط</span>' +
				'<span class="w-7 h-4 bg-slate-300 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0 transition-colors">' +
					'<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>' +
				'</span>' +
			'</button>';
		} else if (item.status === 'scheduled') {
			statusBadgeHtml = '<button type="button" class="portal-discount-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 shadow-2xs transition-all cursor-pointer select-none" data-product-id="' + item.id + '" data-status="scheduled" title="انقر لتعطيل الخصم">' +
				'<span>مجدول</span>' +
				'<span class="w-7 h-4 bg-blue-500 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0 transition-colors">' +
					'<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>' +
				'</span>' +
			'</button>';
		} else if (item.status === 'expired') {
			statusBadgeHtml = '<button type="button" class="portal-discount-toggle-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 border border-slate-200 shadow-2xs transition-all cursor-pointer select-none" data-product-id="' + item.id + '" data-status="expired" title="انقر لتفعيل أو تعديل الخصم">' +
				'<span class="w-7 h-4 bg-slate-300 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0 transition-colors">' +
					'<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>' +
				'</span>' +
				'<span>منتهي</span>' +
			'</button>';
		} else {
			statusBadgeHtml = '<button type="button" class="portal-discount-toggle-btn inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-400 border border-slate-200 shadow-2xs transition-all cursor-pointer select-none" data-product-id="' + item.id + '" data-status="none" title="انقر لإضافة خصم">' +
				'<span class="w-7 h-4 bg-slate-300 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0 transition-colors">' +
					'<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>' +
				'</span>' +
				'<span>بدون خصم</span>' +
			'</button>';
		}

		// 3. Discount Percentage Badge
		var discountBadge = '';
		if (item.discount_percent > 0) {
			discountBadge = '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-600 border border-rose-200/80 shadow-2xs whitespace-nowrap">' +
				'%' + item.discount_percent +
			'</span>';
		} else {
			discountBadge = '<span class="text-slate-300 font-mono text-xs">—</span>';
		}

		// 4. Regular Price strike-through
		var regPriceHtml = '<del class="line-through text-slate-400 text-xs font-semibold">' + item.regular_price_html + '</del>';

		// 5. Sale Price bold red
		var salePriceHtml = item.sale_price > 0
			? '<span class="text-xs sm:text-sm font-black text-rose-600 tracking-tight whitespace-nowrap">' + item.sale_price_html + '</span>'
			: '<span class="text-slate-400 text-xs font-medium">—</span>';

		// Encode full item payload into data attribute for modal access
		var itemJson = encodeURIComponent(JSON.stringify(item));

		return '<tr class="border-b border-slate-100/90 hover:bg-slate-50/70 transition-colors group" data-product-id="' + item.id + '">' +
			// Checkbox
			'<td class="py-3.5 px-3 sm:px-4 text-center w-10">' +
				'<input type="checkbox" class="portal-discount-checkbox rounded-md border-slate-300 text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer" value="' + item.id + '">' +
			'</td>' +

			// Product (Image + Name + SKU)
			'<td class="py-3.5 px-3 sm:px-4 text-right">' +
				'<div class="flex items-center gap-3">' +
					'<div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200/80 p-0.5 overflow-hidden flex-shrink-0 flex items-center justify-center">' +
						'<img src="' + escapeHtml(item.image_url) + '" alt="' + escapeHtml(item.name) + '" class="w-full h-full object-cover rounded-lg" loading="lazy">' +
					'</div>' +
					'<div class="min-w-0 max-w-xs sm:max-w-md">' +
						'<a href="' + escapeHtml(item.permalink) + '" target="_blank" class="text-xs sm:text-sm font-extrabold text-slate-900 hover:text-rose-600 transition-colors line-clamp-1 block text-right no-underline">' +
							escapeHtml(item.name) +
						'</a>' +
						'<div class="text-[11px] font-mono text-slate-400 mt-0.5 text-right">' +
							'SKU: ' + escapeHtml(item.sku) +
						'</div>' +
					'</div>' +
				'</div>' +
			'</td>' +

			// Regular Price
			'<td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">' +
				regPriceHtml +
			'</td>' +

			// Sale Price
			'<td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">' +
				salePriceHtml +
			'</td>' +

			// Discount %
			'<td class="py-3.5 px-3 sm:px-4 text-center whitespace-nowrap">' +
				discountBadge +
			'</td>' +

			// Sale Period
			'<td class="py-3.5 px-3 sm:px-4 text-right whitespace-nowrap">' +
				periodHtml +
			'</td>' +

			// Status
			'<td class="py-3.5 px-3 sm:px-4 text-center whitespace-nowrap">' +
				statusBadgeHtml +
			'</td>' +

			// Actions
			'<td class="py-3.5 px-3 sm:px-4 text-left whitespace-nowrap w-24">' +
				'<div class="flex items-center justify-end gap-1 relative">' +
					// Pencil Edit Button
					'<button type="button" class="portal-discount-edit-btn w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer shadow-2xs" data-item="' + itemJson + '" title="تعديل الخصم">' +
						'<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>' +
					'</button>' +

					// Overflow Menu Button (...)
					'<button type="button" class="portal-discount-menu-btn w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-900 border border-slate-200/90 flex items-center justify-center transition-all cursor-pointer shadow-2xs" data-product-id="' + item.id + '" title="خيارات إضافية">' +
						'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h.01M12 12h.01M19 12h.01" /></svg>' +
					'</button>' +

					// Dropdown Menu Popover
					'<div class="portal-discount-dropdown hidden absolute left-0 right-auto top-full mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 z-50 text-right">' +
						'<a href="' + escapeHtml(item.permalink) + '" target="_blank" class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 no-underline transition-colors">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>' +
							'<span>معاينة في المتجر</span>' +
						'</a>' +
						'<a href="' + escapeHtml(item.edit_url) + '" target="_blank" class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-slate-900 no-underline transition-colors">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>' +
							'<span>تعديل في ووكومرس</span>' +
						'</a>' +
						(item.sale_price > 0 ?
							'<button type="button" class="portal-discount-remove-btn w-full flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors text-right cursor-pointer" data-product-id="' + item.id + '" data-product-name="' + escapeHtml(item.name) + '">' +
								'<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>' +
								'<span>إزالة الخصم</span>' +
							'</button>' : '') +
					'</div>' +
				'</div>' +
			'</td>' +
		'</tr>';
	}

	/**
	 * Attach event listeners to newly rendered rows
	 */
	function attachRowEventListeners() {
		// Row checkboxes
		var checkboxes = document.querySelectorAll('.portal-discount-checkbox');
		checkboxes.forEach(function (cb) {
			cb.addEventListener('change', function () {
				var id = parseInt(cb.value, 10);
				if (cb.checked) {
					if (state.selectedProductIds.indexOf(id) === -1) {
						state.selectedProductIds.push(id);
					}
				} else {
					state.selectedProductIds = state.selectedProductIds.filter(function (x) { return x !== id; });
				}
				updateBulkBar();
			});
		});

		// Select all checkbox
		var selectAll = document.getElementById('portal-discounts-select-all');
		if (selectAll) {
			selectAll.onchange = function () {
				var isChecked = selectAll.checked;
				checkboxes.forEach(function (cb) {
					cb.checked = isChecked;
					var id = parseInt(cb.value, 10);
					if (isChecked) {
						if (state.selectedProductIds.indexOf(id) === -1) {
							state.selectedProductIds.push(id);
						}
					}
				});
				if (!isChecked) {
					state.selectedProductIds = [];
				}
				updateBulkBar();
			};
		}

		// Edit button
		var editBtns = document.querySelectorAll('.portal-discount-edit-btn');
		editBtns.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				closeActiveActionMenu();
				var raw = btn.getAttribute('data-item');
				if (raw) {
					try {
						var item = JSON.parse(decodeURIComponent(raw));
						openEditModal(item);
					} catch (err) {}
				}
			});
		});

		// Overflow menu dropdown toggle
		var menuBtns = document.querySelectorAll('.portal-discount-menu-btn');
		menuBtns.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var dropdown = btn.nextElementSibling;
				if (activeActionMenu && activeActionMenu !== dropdown) {
					activeActionMenu.classList.add('hidden');
				}
				if (dropdown) {
					dropdown.classList.toggle('hidden');
					activeActionMenu = dropdown.classList.contains('hidden') ? null : dropdown;
				}
			});
		});

		// Quick Remove button from menu
		var removeBtns = document.querySelectorAll('.portal-discount-remove-btn');
		removeBtns.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				closeActiveActionMenu();
				var pid = parseInt(btn.getAttribute('data-product-id'), 10);
				var pname = btn.getAttribute('data-product-name') || '';
				openRemoveModal(pid, pname);
			});
		});

		// Status toggle buttons (نشط / غير نشط switch)
		var toggleBtns = document.querySelectorAll('.portal-discount-toggle-btn');
		toggleBtns.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.stopPropagation();
				var pid = parseInt(btn.getAttribute('data-product-id'), 10);
				var curStatus = btn.getAttribute('data-status');
				if (!pid) return;

				// If product currently has no discount, clicking toggle opens the discount editor
				if (curStatus === 'none') {
					var row = btn.closest('tr');
					var editBtn = row ? row.querySelector('.portal-discount-edit-btn') : null;
					if (editBtn) {
						editBtn.click();
						return;
					}
				}

				btn.disabled = true;
				btn.classList.add('opacity-50', 'pointer-events-none');

				var config = window.AshrafcoDiscountsConfig || {};
				var fd = new FormData();
				fd.append('action', 'ashrafco_portal_toggle_discount_status');
				fd.append('nonce', config.nonce || '');
				fd.append('product_id', pid);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					btn.disabled = false;
					btn.classList.remove('opacity-50', 'pointer-events-none');

					if (data.success) {
						if (data.data.needs_price) {
							showToast(data.data.message || 'يرجى إدخال سعر الخصم أولاً', 'error');
							var row = btn.closest('tr');
							var editBtn = row ? row.querySelector('.portal-discount-edit-btn') : null;
							if (editBtn) editBtn.click();
						} else {
							showToast(data.data.message || 'تم تحديث حالة الخصم بنجاح', 'success');
							if (data.data.counts) {
								updateSummaryCards(data.data.counts);
							}
							fetchDiscounts();
						}
					} else {
						showToast(data.data ? data.data.message : 'فشل تغيير حالة الخصم', 'error');
					}
				})
				.catch(function () {
					btn.disabled = false;
					btn.classList.remove('opacity-50', 'pointer-events-none');
					showToast('حدث خطأ في الاتصال بالسيرفر', 'error');
				});
			});
		});
	}

	/**
	 * Update floating Bulk Actions bar
	 */
	function updateBulkBar() {
		var bar = document.getElementById('portal-discounts-bulk-bar');
		var countEl = document.getElementById('portal-discounts-bulk-count');
		if (!bar) return;

		var count = state.selectedProductIds.length;
		if (count > 0) {
			bar.classList.remove('hidden');
			if (countEl) countEl.textContent = 'تم تحديد ' + count + ' منتج';
		} else {
			bar.classList.add('hidden');
		}
	}

	/**
	 * Close any open overflow dropdown menu
	 */
	function closeActiveActionMenu() {
		if (activeActionMenu) {
			activeActionMenu.classList.add('hidden');
			activeActionMenu = null;
		}
	}

	function initGlobalMenuCloser() {
		document.addEventListener('click', function () {
			closeActiveActionMenu();
		});
	}

	/**
	 * Render dynamic pagination numbers (1, 2, 3, ..., N)
	 */
	function renderPaginationControls(pagination) {
		var container = document.getElementById('portal-discounts-pagination-pages');
		var prevBtn = document.getElementById('portal-discounts-prev-page');
		var nextBtn = document.getElementById('portal-discounts-next-page');

		if (!container) return;

		if (prevBtn) {
			if (pagination.current_page <= 1) {
				prevBtn.disabled = true;
				prevBtn.classList.add('opacity-40', 'cursor-not-allowed');
			} else {
				prevBtn.disabled = false;
				prevBtn.classList.remove('opacity-40', 'cursor-not-allowed');
			}
		}

		if (nextBtn) {
			if (pagination.current_page >= pagination.total_pages) {
				nextBtn.disabled = true;
				nextBtn.classList.add('opacity-40', 'cursor-not-allowed');
			} else {
				nextBtn.disabled = false;
				nextBtn.classList.remove('opacity-40', 'cursor-not-allowed');
			}
		}

		var total = pagination.total_pages;
		var cur   = pagination.current_page;
		var html  = '';

		// Generate compact pagination window
		var pages = [];
		if (total <= 7) {
			for (var i = 1; i <= total; i++) pages.push(i);
		} else {
			pages.push(1);
			if (cur > 3) pages.push('...');
			var start = Math.max(2, cur - 1);
			var end   = Math.min(total - 1, cur + 1);
			for (var j = start; j <= end; j++) pages.push(j);
			if (cur < total - 2) pages.push('...');
			pages.push(total);
		}

		pages.forEach(function (p) {
			if (p === '...') {
				html += '<span class="px-2 py-1 text-slate-400 text-xs font-mono">...</span>';
			} else if (p === cur) {
				html += '<button type="button" class="w-8 h-8 rounded-xl bg-primary text-white text-xs font-black shadow-xs flex items-center justify-center cursor-default">' + p + '</button>';
			} else {
				html += '<button type="button" class="portal-pagination-page-btn w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center justify-center cursor-pointer shadow-2xs" data-page="' + p + '">' + p + '</button>';
			}
		});

		container.innerHTML = html;

		container.querySelectorAll('.portal-pagination-page-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var p = parseInt(btn.getAttribute('data-page'), 10);
				if (p && p !== state.page) {
					state.page = p;
					fetchDiscounts();
				}
			});
		});
	}

	/**
	 * Real-time Countdown Timer Loop (updates seconds in-browser without network requests)
	 */
	function startCountdownLoop() {
		if (state.countdownInterval) {
			clearInterval(state.countdownInterval);
		}

		state.countdownInterval = setInterval(function () {
			var now = Math.floor(Date.now() / 1000);
			var timerEls = document.querySelectorAll('.portal-countdown-label');

			timerEls.forEach(function (el) {
				var endTs = parseInt(el.getAttribute('data-end'), 10);
				if (!endTs) return;

				var remaining = endTs - now;
				if (remaining <= 0) {
					el.textContent = 'انتهى الخصم';
					el.classList.add('text-slate-400');
					el.classList.remove('text-rose-600');
					return;
				}

				var days  = Math.floor(remaining / 86400);
				var hours = Math.floor((remaining % 86400) / 3600);
				var mins  = Math.floor((remaining % 3600) / 60);
				var secs  = remaining % 60;

				var formatted = '';
				if (days > 0) {
					formatted = 'ينتهي خلال ' + pad(days) + ' يوم ' + pad(hours) + ' ساعة';
				} else {
					formatted = 'ينتهي خلال ' + pad(hours) + ':' + pad(mins) + ':' + pad(secs);
				}
				el.textContent = formatted;
			});
		}, 1000);
	}

	function pad(num) {
		return num < 10 ? '0' + num : num;
	}

	/**
	 * Add Discount Modal Initialization
	 */
	function initAddModal() {
		var modal       = document.getElementById('portal-add-discount-modal');
		var openBtn     = document.getElementById('portal-add-discount-btn');
		var closeBtn    = document.getElementById('portal-add-discount-close');
		var cancelBtn   = document.getElementById('portal-add-discount-cancel');
		var form        = document.getElementById('portal-add-discount-form');

		var prodSearch  = document.getElementById('portal-add-product-search');
		var prodResults = document.getElementById('portal-add-product-results');
		var prodPreview = document.getElementById('portal-add-product-preview');
		var selectedPid = document.getElementById('portal-add-product-id');

		var saleInput   = document.getElementById('portal-add-sale-price');
		var regDisplay  = document.getElementById('portal-add-regular-price-display');
		var percentBadge= document.getElementById('portal-add-discount-percent-badge');
		var warnBox     = document.getElementById('portal-add-existing-warning');

		var searchDebounce = null;
		var currentRegPrice = 0;

		function resetAddForm() {
			form.reset();
			selectedPid.value = '';
			currentRegPrice = 0;
			prodPreview.classList.add('hidden');
			prodSearch.value = '';
			prodResults.classList.add('hidden');
			prodResults.innerHTML = '';
			if (warnBox) warnBox.classList.add('hidden');
			if (percentBadge) percentBadge.textContent = '%0';
		}

		if (openBtn) {
			openBtn.addEventListener('click', function () {
				resetAddForm();
				modal.classList.remove('hidden');
				prodSearch.focus();
			});
		}

		if (closeBtn) closeBtn.addEventListener('click', function () { modal.classList.add('hidden'); });
		if (cancelBtn) cancelBtn.addEventListener('click', function () { modal.classList.add('hidden'); });

		// Live product search
		if (prodSearch) {
			prodSearch.addEventListener('input', function () {
				clearTimeout(searchDebounce);
				var term = prodSearch.value.trim();
				if (term.length < 1) {
					prodResults.classList.add('hidden');
					return;
				}

				searchDebounce = setTimeout(function () {
					var config = window.AshrafcoDiscountsConfig || {};
					var fd = new FormData();
					fd.append('action', 'ashrafco_portal_search_discount_products');
					fd.append('nonce', config.nonce || '');
					fd.append('term', term);

					fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
					.then(function (r) { return r.json(); })
					.then(function (data) {
						if (data.success && data.data && data.data.products) {
							renderProductSearchResults(data.data.products);
						}
					});
				}, 300);
			});
		}

		function renderProductSearchResults(products) {
			if (!products || products.length === 0) {
				prodResults.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">لم يتم العثور على منتجات مطابقة</div>';
				prodResults.classList.remove('hidden');
				return;
			}

			var html = '';
			products.forEach(function (p) {
				var pJson = encodeURIComponent(JSON.stringify(p));
				html += '<div class="portal-search-result-item flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl cursor-pointer transition-colors border-b border-slate-100 last:border-b-0" data-product="' + pJson + '">' +
					'<div class="flex items-center gap-3">' +
						'<img src="' + escapeHtml(p.image_url) + '" class="w-9 h-9 rounded-lg object-cover border border-slate-200" alt="">' +
						'<div>' +
							'<div class="text-xs font-bold text-slate-900 line-clamp-1">' + escapeHtml(p.name) + '</div>' +
							'<div class="text-[11px] text-slate-400 font-mono">SKU: ' + escapeHtml(p.sku || '—') + '</div>' +
						'</div>' +
					'</div>' +
					'<div class="text-left">' +
						'<div class="text-xs font-bold text-slate-700">' + p.regular_price_html + '</div>' +
						(p.has_existing_discount ? '<span class="text-[10px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded">يحتوي على خصم</span>' : '') +
					'</div>' +
				'</div>';
			});

			prodResults.innerHTML = html;
			prodResults.classList.remove('hidden');

			prodResults.querySelectorAll('.portal-search-result-item').forEach(function (itemEl) {
				itemEl.addEventListener('click', function () {
					var raw = itemEl.getAttribute('data-product');
					if (raw) {
						var p = JSON.parse(decodeURIComponent(raw));
						selectProductForDiscount(p);
					}
				});
			});
		}

		function selectProductForDiscount(p) {
			selectedPid.value = p.id;
			currentRegPrice = p.regular_price;

			// Update visual card
			document.getElementById('portal-add-product-name').textContent = p.name;
			document.getElementById('portal-add-product-sku').textContent = 'SKU: ' + (p.sku || '—');
			document.getElementById('portal-add-product-thumb').src = p.image_url;
			regDisplay.innerHTML = p.regular_price_html || (p.regular_price + ' ج.م');

			prodPreview.classList.remove('hidden');
			prodResults.classList.add('hidden');
			prodSearch.value = '';

			// Warn if existing discount
			if (p.has_existing_discount && warnBox) {
				warnBox.classList.remove('hidden');
			} else if (warnBox) {
				warnBox.classList.add('hidden');
			}

			// Pre-fill existing sale price if any
			if (p.sale_price) {
				saleInput.value = p.sale_price;
				updatePercentPreview();
			} else {
				saleInput.value = '';
				percentBadge.textContent = '%0';
			}

			saleInput.focus();
		}

		// Live discount % preview calculation
		function updatePercentPreview() {
			var saleVal = parseFloat(saleInput.value);
			if (currentRegPrice > 0 && saleVal > 0 && saleVal < currentRegPrice) {
				var pct = Math.round(((currentRegPrice - saleVal) / currentRegPrice) * 100);
				percentBadge.textContent = '%' + pct;
				percentBadge.classList.remove('bg-slate-100', 'text-slate-400');
				percentBadge.classList.add('bg-rose-50', 'text-rose-600');
			} else {
				percentBadge.textContent = '%0';
				percentBadge.classList.remove('bg-rose-50', 'text-rose-600');
				percentBadge.classList.add('bg-slate-100', 'text-slate-400');
			}
		}

		if (saleInput) {
			saleInput.addEventListener('input', updatePercentPreview);
		}

		// Submit Add Discount Form
		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();

				var pid = parseInt(selectedPid.value, 10);
				if (!pid) {
					showToast('يرجى اختيار منتج أولاً', 'error');
					return;
				}

				var salePrice = parseFloat(saleInput.value);
				if (!salePrice || salePrice <= 0) {
					showToast('يرجى إدخال سعر تخفيض صالح', 'error');
					saleInput.focus();
					return;
				}

				if (currentRegPrice > 0 && salePrice >= currentRegPrice) {
					showToast('يجب أن يكون سعر الخصم أقل من السعر الأصلي', 'error');
					saleInput.focus();
					return;
				}

				var startDate = document.getElementById('portal-add-sale-start').value;
				var endDate   = document.getElementById('portal-add-sale-end').value;

				if (startDate && endDate && endDate < startDate) {
					showToast('تاريخ الانتهاء لا يمكن أن يسبق تاريخ البدء', 'error');
					return;
				}

				var submitBtn = document.getElementById('portal-add-discount-submit');
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> جاري الحفظ...';
				}

				var config = window.AshrafcoDiscountsConfig || {};
				var fd = new FormData();
				fd.append('action', 'ashrafco_portal_save_product_discount');
				fd.append('nonce', config.nonce || '');
				fd.append('product_id', pid);
				fd.append('sale_price', salePrice);
				fd.append('sale_start_date', startDate);
				fd.append('sale_end_date', endDate);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.innerHTML = '<span>حفظ وتفعيل الخصم</span>';
					}

					if (data.success) {
						modal.classList.add('hidden');
						showToast(data.data.message || 'تم حفظ الخصم بنجاح', 'success');
						fetchDiscounts();
					} else {
						showToast(data.data ? data.data.message : 'فشل حفظ الخصم', 'error');
					}
				})
				.catch(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.innerHTML = '<span>حفظ وتفعيل الخصم</span>';
					}
					showToast('حدث خطأ في الشبكة', 'error');
				});
			});
		}
	}

	/**
	 * Edit Discount Modal
	 */
	function initEditModal() {
		var modal     = document.getElementById('portal-edit-discount-modal');
		var closeBtn  = document.getElementById('portal-edit-discount-close');
		var cancelBtn = document.getElementById('portal-edit-discount-cancel');
		var form      = document.getElementById('portal-edit-discount-form');
		var removeBtn = document.getElementById('portal-edit-remove-btn');

		var saleInput = document.getElementById('portal-edit-sale-price');
		var pctBadge  = document.getElementById('portal-edit-discount-percent-badge');

		if (closeBtn) closeBtn.addEventListener('click', function () { modal.classList.add('hidden'); });
		if (cancelBtn) cancelBtn.addEventListener('click', function () { modal.classList.add('hidden'); });

		function updateEditPercent() {
			if (!state.activeEditProduct) return;
			var reg = state.activeEditProduct.regular_price;
			var val = parseFloat(saleInput.value);
			if (reg > 0 && val > 0 && val < reg) {
				var pct = Math.round(((reg - val) / reg) * 100);
				pctBadge.textContent = '%' + pct;
				pctBadge.classList.remove('bg-slate-100', 'text-slate-400');
				pctBadge.classList.add('bg-rose-50', 'text-rose-600');
			} else {
				pctBadge.textContent = '%0';
				pctBadge.classList.remove('bg-rose-50', 'text-rose-600');
				pctBadge.classList.add('bg-slate-100', 'text-slate-400');
			}
		}

		if (saleInput) {
			saleInput.addEventListener('input', updateEditPercent);
		}

		if (removeBtn) {
			removeBtn.addEventListener('click', function () {
				if (!state.activeEditProduct) return;
				modal.classList.add('hidden');
				openRemoveModal(state.activeEditProduct.id, state.activeEditProduct.name);
			});
		}

		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				if (!state.activeEditProduct) return;

				var pid = state.activeEditProduct.id;
				var salePrice = parseFloat(saleInput.value);
				var regPrice = state.activeEditProduct.regular_price;

				if (!salePrice || salePrice <= 0) {
					showToast('يرجى إدخال سعر تخفيض صالح', 'error');
					return;
				}

				if (regPrice > 0 && salePrice >= regPrice) {
					showToast('يجب أن يكون سعر الخصم أقل من السعر الأصلي', 'error');
					return;
				}

				var startDate = document.getElementById('portal-edit-sale-start').value;
				var endDate   = document.getElementById('portal-edit-sale-end').value;

				if (startDate && endDate && endDate < startDate) {
					showToast('تاريخ الانتهاء لا يمكن أن يسبق تاريخ البدء', 'error');
					return;
				}

				var submitBtn = document.getElementById('portal-edit-discount-submit');
				if (submitBtn) {
					submitBtn.disabled = true;
					submitBtn.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> جاري الحفظ...';
				}

				var config = window.AshrafcoDiscountsConfig || {};
				var fd = new FormData();
				fd.append('action', 'ashrafco_portal_save_product_discount');
				fd.append('nonce', config.nonce || '');
				fd.append('product_id', pid);
				fd.append('sale_price', salePrice);
				fd.append('sale_start_date', startDate);
				fd.append('sale_end_date', endDate);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.innerHTML = '<span>حفظ التعديلات</span>';
					}

					if (data.success) {
						modal.classList.add('hidden');
						showToast(data.data.message || 'تم تحديث الخصم بنجاح', 'success');
						fetchDiscounts();
					} else {
						showToast(data.data ? data.data.message : 'فشل تحديث الخصم', 'error');
					}
				})
				.catch(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
						submitBtn.innerHTML = '<span>حفظ التعديلات</span>';
					}
					showToast('حدث خطأ في الشبكة', 'error');
				});
			});
		}
	}

	function openEditModal(item) {
		state.activeEditProduct = item;
		var modal = document.getElementById('portal-edit-discount-modal');
		if (!modal) return;

		document.getElementById('portal-edit-product-id').value = item.id;
		document.getElementById('portal-edit-product-name').textContent = item.name;
		document.getElementById('portal-edit-product-sku').textContent = 'SKU: ' + (item.sku || '—');
		document.getElementById('portal-edit-product-thumb').src = item.image_url;
		document.getElementById('portal-edit-regular-price-display').innerHTML = item.regular_price_html || (item.regular_price + ' ج.م');

		var saleInput = document.getElementById('portal-edit-sale-price');
		saleInput.value = item.sale_price > 0 ? item.sale_price : '';

		document.getElementById('portal-edit-sale-start').value = item.sale_from_raw || '';
		document.getElementById('portal-edit-sale-end').value = item.sale_to_raw || '';

		var pctBadge = document.getElementById('portal-edit-discount-percent-badge');
		if (item.discount_percent > 0) {
			pctBadge.textContent = '%' + item.discount_percent;
			pctBadge.classList.remove('bg-slate-100', 'text-slate-400');
			pctBadge.classList.add('bg-rose-50', 'text-rose-600');
		} else {
			pctBadge.textContent = '%0';
			pctBadge.classList.remove('bg-rose-50', 'text-rose-600');
			pctBadge.classList.add('bg-slate-100', 'text-slate-400');
		}

		modal.classList.remove('hidden');
	}

	/**
	 * Remove Discount Confirmation Modal
	 */
	function initRemoveModal() {
		var modal     = document.getElementById('portal-remove-discount-modal');
		var cancelBtn = document.getElementById('portal-remove-discount-cancel');
		var confirmBtn= document.getElementById('portal-remove-discount-confirm');
		var targetPid = 0;

		if (cancelBtn) cancelBtn.addEventListener('click', function () { modal.classList.add('hidden'); });

		window.openRemoveModal = function (pid, pname) {
			targetPid = pid;
			var nameEl = document.getElementById('portal-remove-product-name');
			if (nameEl) nameEl.textContent = pname || 'هذا المنتج';
			modal.classList.remove('hidden');
		};

		if (confirmBtn) {
			confirmBtn.addEventListener('click', function () {
				if (!targetPid) return;

				confirmBtn.disabled = true;
				confirmBtn.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> جاري الإزالة...';

				var config = window.AshrafcoDiscountsConfig || {};
				var fd = new FormData();
				fd.append('action', 'ashrafco_portal_remove_product_discount');
				fd.append('nonce', config.nonce || '');
				fd.append('product_id', targetPid);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					confirmBtn.disabled = false;
					confirmBtn.innerHTML = '<span>نعم، إزالة الخصم</span>';
					modal.classList.add('hidden');

					if (data.success) {
						showToast(data.data.message || 'تمت إزالة الخصم بنجاح', 'success');
						fetchDiscounts();
					} else {
						showToast(data.data ? data.data.message : 'فشل إزالة الخصم', 'error');
					}
				})
				.catch(function () {
					confirmBtn.disabled = false;
					confirmBtn.innerHTML = '<span>نعم، إزالة الخصم</span>';
					showToast('حدث خطأ في الشبكة', 'error');
				});
			});
		}
	}

	/**
	 * Bulk Actions Initialization
	 */
	function initBulkActions() {
		var removeSelectedBtn = document.getElementById('portal-discounts-bulk-remove-btn');
		var cancelSelectedBtn = document.getElementById('portal-discounts-bulk-cancel-btn');

		if (cancelSelectedBtn) {
			cancelSelectedBtn.addEventListener('click', function () {
				state.selectedProductIds = [];
				var checkboxes = document.querySelectorAll('.portal-discount-checkbox');
				checkboxes.forEach(function (cb) { cb.checked = false; });
				var selectAll = document.getElementById('portal-discounts-select-all');
				if (selectAll) selectAll.checked = false;
				updateBulkBar();
			});
		}

		if (removeSelectedBtn) {
			removeSelectedBtn.addEventListener('click', function () {
				var count = state.selectedProductIds.length;
				if (count === 0) return;

				if (!confirm('هل أنت متأكد من رغبتك في إزالة الخصم عن ' + count + ' منتج محدد واستعادة أسعارها الأصلية؟')) {
					return;
				}

				removeSelectedBtn.disabled = true;
				removeSelectedBtn.innerHTML = '<span class="inline-block animate-spin mr-1">↻</span> جاري المعالجة...';

				var config = window.AshrafcoDiscountsConfig || {};
				var fd = new FormData();
				fd.append('action', 'ashrafco_portal_bulk_remove_discounts');
				fd.append('nonce', config.nonce || '');
				state.selectedProductIds.forEach(function (id) {
					fd.append('product_ids[]', id);
				});

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
				.then(function (r) { return r.json(); })
				.then(function (data) {
					removeSelectedBtn.disabled = false;
					removeSelectedBtn.innerHTML = '<span>إزالة الخصومات المحددة</span>';

					if (data.success) {
						showToast(data.data.message || 'تمت إزالة الخصومات بنجاح', 'success');
						fetchDiscounts();
					} else {
						showToast(data.data ? data.data.message : 'فشلت عملية الإزالة المجمعة', 'error');
					}
				})
				.catch(function () {
					removeSelectedBtn.disabled = false;
					removeSelectedBtn.innerHTML = '<span>إزالة الخصومات المحددة</span>';
					showToast('حدث خطأ في الشبكة', 'error');
				});
			});
		}
	}

	function escapeHtml(str) {
		if (str === null || str === undefined) return '';
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

})();
