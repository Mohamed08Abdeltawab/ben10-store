/**
 * Ashrafco Admin Portal - Coupon Management Controller
 *
 * Single source of truth: Native WooCommerce coupons (WC_Coupon).
 * Handles summary metrics, live search, status and discount type filtering, sorting,
 * server-side pagination, Add/Edit coupon modals, interactive status toggling,
 * coupon duplication, single & bulk coupon deletion, and order usage tracking.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function () {
	'use strict';

	var state = {
		search: '',
		status: 'all',
		discountType: 'all',
		sort: 'date_desc',
		page: 1,
		perPage: 10,
		total: 0,
		totalPages: 1,
		loading: false,
		selectedCouponIds: [],
		activeEditCoupon: null,
		deletingCouponId: null,
		deletingCouponCode: ''
	};

	var activeActionMenu = null;

	function getConfig() {
		return window.AshrafcoCouponsConfig || {};
	}

	function getAjaxUrl() {
		var cfg = getConfig();
		return cfg.ajaxUrl || (window.ajaxurl || '/wp-admin/admin-ajax.php');
	}

	function getNonce() {
		var cfg = getConfig();
		return cfg.nonce || '';
	}

	function getCurrencySymbol() {
		var cfg = getConfig();
		return cfg.currencySymbol || 'ر.س';
	}

	document.addEventListener('DOMContentLoaded', function () {
		initFilters();
		initPagination();
		initModal();
		initDeleteModal();
		initUsageModal();
		initBulkActions();
		initCardQuickFilters();
		initGlobalMenuCloser();

		// Auto load if initial active tab is coupons
		var couponsTab = document.getElementById('portal-tab-coupons');
		if (couponsTab && couponsTab.classList.contains('active')) {
			fetchCoupons();
		}
	});

	// Global hook so switchPortalTab can trigger reload
	window.AshrafcoCouponsReload = function () {
		fetchCoupons();
	};

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-coupons-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-coupons-toast';
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
		var searchInput = document.getElementById('portal-coupons-search');
		var statusFilter = document.getElementById('portal-coupons-filter-status');
		var typeFilter = document.getElementById('portal-coupons-filter-type');
		var sortFilter = document.getElementById('portal-coupons-filter-sort');
		var resetBtn = document.getElementById('portal-coupons-reset-filters');

		var debounceTimer = null;
		if (searchInput) {
			searchInput.addEventListener('input', function () {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(function () {
					state.search = searchInput.value.trim();
					state.page = 1;
					fetchCoupons();
				}, 350);
			});
		}

		if (statusFilter) {
			statusFilter.addEventListener('change', function () {
				state.status = statusFilter.value;
				state.page = 1;
				fetchCoupons();
			});
		}

		if (typeFilter) {
			typeFilter.addEventListener('change', function () {
				state.discountType = typeFilter.value;
				state.page = 1;
				fetchCoupons();
			});
		}

		if (sortFilter) {
			sortFilter.addEventListener('change', function () {
				state.sort = sortFilter.value;
				state.page = 1;
				fetchCoupons();
			});
		}

		if (resetBtn) {
			resetBtn.addEventListener('click', function () {
				if (searchInput) searchInput.value = '';
				if (statusFilter) statusFilter.value = 'all';
				if (typeFilter) typeFilter.value = 'all';
				if (sortFilter) sortFilter.value = 'date_desc';

				state.search = '';
				state.status = 'all';
				state.discountType = 'all';
				state.sort = 'date_desc';
				state.page = 1;
				fetchCoupons();
			});
		}
	}

	/**
	 * Summary card quick filter clicks
	 */
	function initCardQuickFilters() {
		var map = [
			{ id: 'portal-coupons-card-total-box', link: 'portal-coupons-card-total-link', status: 'all' },
			{ id: 'portal-coupons-card-active-box', link: 'portal-coupons-card-active-link', status: 'active' },
			{ id: 'portal-coupons-card-scheduled-box', link: 'portal-coupons-card-scheduled-link', status: 'scheduled' },
			{ id: 'portal-coupons-card-ending-box', link: 'portal-coupons-card-ending-link', status: 'ending_soon' }
		];

		map.forEach(function (item) {
			var box = document.getElementById(item.id);
			var link = document.getElementById(item.link);
			var handler = function (e) {
				if (e) e.preventDefault();
				state.status = item.status;
				state.page = 1;
				var statusSelect = document.getElementById('portal-coupons-filter-status');
				if (statusSelect) statusSelect.value = item.status;
				fetchCoupons();
			};

			if (box) box.addEventListener('click', handler);
			if (link) link.addEventListener('click', function (e) {
				e.stopPropagation();
				handler(e);
			});
		});
	}

	/**
	 * Setup pagination prev/next listeners
	 */
	function initPagination() {
		var prevBtn = document.getElementById('portal-coupons-prev-page');
		var nextBtn = document.getElementById('portal-coupons-next-page');

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				if (state.page > 1) {
					state.page--;
					fetchCoupons();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				if (state.page < state.totalPages) {
					state.page++;
					fetchCoupons();
				}
			});
		}
	}

	/**
	 * Global click listener to close open action menus
	 */
	function initGlobalMenuCloser() {
		document.addEventListener('click', function (e) {
			if (activeActionMenu && !activeActionMenu.contains(e.target)) {
				activeActionMenu.classList.add('hidden');
				activeActionMenu = null;
			}
		});
	}

	/**
	 * Fetch coupons list via AJAX
	 */
	function fetchCoupons() {
		if (state.loading) return;
		state.loading = true;

		var loadingEl = document.getElementById('portal-coupons-loading');
		var tbody = document.getElementById('portal-coupons-tbody');
		var emptyState = document.getElementById('portal-coupons-empty-state');
		var tableWrapper = document.getElementById('portal-coupons-table-wrapper');
		var footer = document.getElementById('portal-coupons-footer');

		if (loadingEl) loadingEl.classList.remove('hidden');

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_coupons');
		formData.append('nonce', getNonce());
		formData.append('search', state.search);
		formData.append('status', state.status);
		formData.append('discount_type', state.discountType);
		formData.append('sort', state.sort);
		formData.append('page', state.page);
		formData.append('per_page', state.perPage);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (res) {
				state.loading = false;
				if (loadingEl) loadingEl.classList.add('hidden');

				if (!res.success) {
					showToast(res.data && res.data.message ? res.data.message : 'فشل في تحميل الكوبونات', 'error');
					return;
				}

				var data = res.data;
				state.total = data.total;
				state.totalPages = data.total_pages;

				updateSummaryCounts(data.summary);
				renderTableRows(data.items);
				renderPagination(data);

				if (data.items.length === 0) {
					if (emptyState) emptyState.classList.remove('hidden');
					if (tableWrapper) tableWrapper.classList.add('hidden');
					if (footer) footer.classList.add('hidden');
				} else {
					if (emptyState) emptyState.classList.add('hidden');
					if (tableWrapper) tableWrapper.classList.remove('hidden');
					if (footer) footer.classList.remove('hidden');
				}

				updateBulkBar();
			})
			.catch(function (err) {
				state.loading = false;
				if (loadingEl) loadingEl.classList.add('hidden');
				showToast('حدث خطأ أثناء الاتصال بالخادم.', 'error');
			});
	}

	/**
	 * Update summary statistics counters
	 */
	function updateSummaryCounts(summary) {
		if (!summary) return;
		var totalEl = document.getElementById('portal-coupons-count-total');
		var activeEl = document.getElementById('portal-coupons-count-active');
		var scheduledEl = document.getElementById('portal-coupons-count-scheduled');
		var endingEl = document.getElementById('portal-coupons-count-ending');
		var badgeEl = document.getElementById('portal-tab-coupons-badge');

		if (totalEl) totalEl.textContent = summary.total;
		if (activeEl) activeEl.textContent = summary.active;
		if (scheduledEl) scheduledEl.textContent = summary.scheduled;
		if (endingEl) endingEl.textContent = summary.ending_soon;

		if (badgeEl) {
			if (summary.active > 0) {
				badgeEl.textContent = summary.active;
				badgeEl.classList.remove('hidden');
			} else {
				badgeEl.classList.add('hidden');
			}
		}
	}

	/**
	 * Render table rows
	 */
	function renderTableRows(items) {
		var tbody = document.getElementById('portal-coupons-tbody');
		if (!tbody) return;
		tbody.innerHTML = '';

		items.forEach(function (item) {
			var tr = document.createElement('tr');
			tr.className = 'hover:bg-slate-50/70 transition-colors border-b border-slate-100/90 text-right';
			tr.id = 'portal-coupon-row-' + item.id;

			var isChecked = state.selectedCouponIds.indexOf(item.id) !== -1;

			// Checkbox
			var tdCheck = document.createElement('td');
			tdCheck.className = 'py-3.5 pr-4 pl-2 text-center';
			tdCheck.innerHTML = '<input type="checkbox" class="portal-coupon-row-cb w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer" data-id="' + item.id + '"' + (isChecked ? ' checked' : '') + '>';
			tr.appendChild(tdCheck);

			// Coupon Code & Description
			var tdCoupon = document.createElement('td');
			tdCoupon.className = 'py-3.5 px-3';
			var descHtml = item.description ? '<div class="text-[11px] text-slate-400 mt-0.5 truncate max-w-xs">' + escapeHtml(item.description) + '</div>' : '';
			tdCoupon.innerHTML = '<div class="font-mono font-black text-slate-900 tracking-wider text-sm hover:text-primary cursor-pointer portal-coupon-title-click" data-id="' + item.id + '">' + escapeHtml(item.code) + '</div>' + descHtml;
			tr.appendChild(tdCoupon);

			// Discount Type
			var tdType = document.createElement('td');
			tdType.className = 'py-3.5 px-3';
			tdType.innerHTML = renderTypeBadge(item.discount_type, item.discount_type_label);
			tr.appendChild(tdType);

			// Discount Value
			var tdAmount = document.createElement('td');
			tdAmount.className = 'py-3.5 px-3 font-bold text-rose-600 font-sans';
			tdAmount.innerHTML = '<span class="text-xs sm:text-sm font-black">' + item.amount_formatted + '</span>';
			tr.appendChild(tdAmount);

			// Minimum Spend
			var tdMinSpend = document.createElement('td');
			tdMinSpend.className = 'py-3.5 px-3 text-slate-600 font-sans text-xs';
			tdMinSpend.innerHTML = item.minimum_amount_formatted;
			tr.appendChild(tdMinSpend);

			// Maximum Discount
			var tdMaxDisc = document.createElement('td');
			tdMaxDisc.className = 'py-3.5 px-3 text-slate-600 font-sans text-xs';
			tdMaxDisc.innerHTML = item.max_discount_formatted;
			tr.appendChild(tdMaxDisc);

			// Start Date
			var tdStart = document.createElement('td');
			tdStart.className = 'py-3.5 px-3 text-slate-500 font-sans text-xs whitespace-nowrap';
			tdStart.textContent = item.start_date;
			tr.appendChild(tdStart);

			// Expiry Date
			var tdExpiry = document.createElement('td');
			tdExpiry.className = 'py-3.5 px-3 text-slate-500 font-sans text-xs whitespace-nowrap';
			tdExpiry.textContent = item.expiry_date;
			tr.appendChild(tdExpiry);

			// Usages
			var tdUsage = document.createElement('td');
			tdUsage.className = 'py-3.5 px-3 font-sans';
			var barColor = item.is_exhausted ? 'bg-rose-500' : 'bg-primary';
			var barHtml = item.has_limit
				? '<div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden mt-1"><div class="' + barColor + ' h-1.5 rounded-full" style="width: ' + item.usage_percent + '%"></div></div>'
				: '';
			tdUsage.innerHTML = '<div class="text-xs font-bold text-slate-700">' + escapeHtml(item.usage_display) + '</div>' + barHtml;
			tr.appendChild(tdUsage);

			// Status
			var tdStatus = document.createElement('td');
			tdStatus.className = 'py-3.5 px-3 text-center whitespace-nowrap';
			tdStatus.innerHTML = renderStatusBadge(item.status, item.status_label, item.id);
			tr.appendChild(tdStatus);

			// Actions
			var tdActions = document.createElement('td');
			tdActions.className = 'py-3.5 pl-4 pr-3 text-center whitespace-nowrap';
			tdActions.innerHTML = `
				<div class="flex items-center justify-center gap-1.5">
					<button type="button" class="portal-coupon-edit-btn w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/80 flex items-center justify-center transition-colors cursor-pointer" title="تعديل الكوبون" data-id="${item.id}">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
						</svg>
					</button>
					<div class="relative">
						<button type="button" class="portal-coupon-menu-btn w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/80 flex items-center justify-center transition-colors cursor-pointer" title="المزيد من الإجراءات" data-id="${item.id}">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
							</svg>
						</button>
						<!-- Dropdown Menu -->
						<div id="portal-coupon-menu-${item.id}" class="hidden absolute left-0 top-full mt-1.5 z-50 w-48 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-1.5 text-right font-medium text-xs">
							<button type="button" class="portal-menu-action-edit w-full px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 cursor-pointer" data-id="${item.id}">
								<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
								<span>تعديل الكوبون</span>
							</button>
							<button type="button" class="portal-menu-action-duplicate w-full px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 cursor-pointer" data-id="${item.id}">
								<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
								<span>نسخ الكوبون</span>
							</button>
							<button type="button" class="portal-menu-action-toggle w-full px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 cursor-pointer" data-id="${item.id}">
								<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
								<span>${item.is_disabled ? 'تفعيل الكوبون' : 'إيقاف الكوبون'}</span>
							</button>
							<button type="button" class="portal-menu-action-usage w-full px-3.5 py-2 hover:bg-slate-50 flex items-center gap-2 text-slate-700 cursor-pointer" data-id="${item.id}">
								<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
								<span>عرض الاستخدامات</span>
							</button>
							<div class="h-px bg-slate-100 my-1"></div>
							<button type="button" class="portal-menu-action-delete w-full px-3.5 py-2 hover:bg-rose-50 flex items-center gap-2 text-rose-600 cursor-pointer" data-id="${item.id}" data-code="${escapeHtml(item.code)}">
								<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
								<span>حذف الكوبون</span>
							</button>
						</div>
					</div>
				</div>
			`;
			tr.appendChild(tdActions);

			tbody.appendChild(tr);

			// Attach row item event listeners
			attachRowEventListeners(tr, item);
		});

		// Attach select all listener
		var selectAllCb = document.getElementById('portal-coupons-select-all');
		if (selectAllCb) {
			selectAllCb.checked = items.length > 0 && items.every(function (it) {
				return state.selectedCouponIds.indexOf(it.id) !== -1;
			});
			selectAllCb.onchange = function () {
				var checked = selectAllCb.checked;
				items.forEach(function (it) {
					var idx = state.selectedCouponIds.indexOf(it.id);
					if (checked && idx === -1) {
						state.selectedCouponIds.push(it.id);
					} else if (!checked && idx !== -1) {
						state.selectedCouponIds.splice(idx, 1);
					}
				});
				tbody.querySelectorAll('.portal-coupon-row-cb').forEach(function (cb) {
					cb.checked = checked;
				});
				updateBulkBar();
			};
		}
	}

	/**
	 * Attach event listeners to single row elements
	 */
	function attachRowEventListeners(tr, item) {
		// Row checkbox
		var cb = tr.querySelector('.portal-coupon-row-cb');
		if (cb) {
			cb.addEventListener('change', function () {
				var id = parseInt(cb.getAttribute('data-id'), 10);
				var idx = state.selectedCouponIds.indexOf(id);
				if (cb.checked && idx === -1) {
					state.selectedCouponIds.push(id);
				} else if (!cb.checked && idx !== -1) {
					state.selectedCouponIds.splice(idx, 1);
				}
				updateBulkBar();
			});
		}

		// Title click opens edit
		var titleClick = tr.querySelector('.portal-coupon-title-click');
		if (titleClick) {
			titleClick.addEventListener('click', function () {
				openEditModal(item);
			});
		}

		// Edit button
		var editBtn = tr.querySelector('.portal-coupon-edit-btn');
		if (editBtn) {
			editBtn.addEventListener('click', function () {
				openEditModal(item);
			});
		}

		// Status toggle
		var statusToggle = tr.querySelector('.portal-coupon-toggle-status');
		if (statusToggle) {
			statusToggle.addEventListener('click', function (e) {
				e.stopPropagation();
				toggleCouponStatus(item.id);
			});
		}

		// Menu toggle
		var menuBtn = tr.querySelector('.portal-coupon-menu-btn');
		var menu = tr.querySelector('#portal-coupon-menu-' + item.id);
		if (menuBtn && menu) {
			menuBtn.addEventListener('click', function (e) {
				e.stopPropagation();
				if (activeActionMenu && activeActionMenu !== menu) {
					activeActionMenu.classList.add('hidden');
				}
				menu.classList.toggle('hidden');
				activeActionMenu = menu.classList.contains('hidden') ? null : menu;
			});
		}

		// Menu item: edit
		var menuEdit = tr.querySelector('.portal-menu-action-edit');
		if (menuEdit) {
			menuEdit.addEventListener('click', function () {
				if (menu) menu.classList.add('hidden');
				openEditModal(item);
			});
		}

		// Menu item: duplicate
		var menuDuplicate = tr.querySelector('.portal-menu-action-duplicate');
		if (menuDuplicate) {
			menuDuplicate.addEventListener('click', function () {
				if (menu) menu.classList.add('hidden');
				duplicateCoupon(item.id);
			});
		}

		// Menu item: toggle
		var menuToggle = tr.querySelector('.portal-menu-action-toggle');
		if (menuToggle) {
			menuToggle.addEventListener('click', function () {
				if (menu) menu.classList.add('hidden');
				toggleCouponStatus(item.id);
			});
		}

		// Menu item: usage
		var menuUsage = tr.querySelector('.portal-menu-action-usage');
		if (menuUsage) {
			menuUsage.addEventListener('click', function () {
				if (menu) menu.classList.add('hidden');
				openUsageModal(item.id);
			});
		}

		// Menu item: delete
		var menuDelete = tr.querySelector('.portal-menu-action-delete');
		if (menuDelete) {
			menuDelete.addEventListener('click', function () {
				if (menu) menu.classList.add('hidden');
				openDeleteModal(item.id, item.code);
			});
		}
	}

	/**
	 * Render Discount Type Badge
	 */
	function renderTypeBadge(type, label) {
		label = label || type;
		switch (type) {
			case 'percent':
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
					<svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm11 11a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/></svg>
					<span>${escapeHtml(label)}</span>
				</span>`;
			case 'fixed_cart':
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
					<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
					<span>${escapeHtml(label)}</span>
				</span>`;
			case 'fixed_product':
			default:
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
					<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
					<span>${escapeHtml(label)}</span>
				</span>`;
		}
	}

	/**
	 * Render Status Badge
	 */
	function renderStatusBadge(status, label, id) {
		label = label || status;
		switch (status) {
			case 'active':
				return `<button type="button" class="portal-coupon-toggle-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs select-none hover:bg-emerald-100 transition-colors cursor-pointer" data-id="${id}" title="انقر لإيقاف الكوبون">
					<span>${escapeHtml(label)}</span>
					<span class="w-7 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-end px-0.5 flex-shrink-0">
						<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
					</span>
				</button>`;
			case 'scheduled':
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs select-none">
					<span>${escapeHtml(label)}</span>
					<span class="w-7 h-4 bg-blue-500 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0">
						<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
					</span>
				</span>`;
			case 'ending_soon':
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs select-none">
					<span>${escapeHtml(label)}</span>
					<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				</span>`;
			case 'expired':
				return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs select-none">
					<span>${escapeHtml(label)}</span>
					<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				</span>`;
			case 'disabled':
			default:
				return `<button type="button" class="portal-coupon-toggle-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs select-none hover:bg-rose-100 transition-colors cursor-pointer" data-id="${id}" title="انقر لتفعيل الكوبون">
					<span>${escapeHtml(label)}</span>
					<span class="w-7 h-4 bg-rose-400 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0">
						<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
					</span>
				</button>`;
		}
	}

	/**
	 * Render Pagination and Results Counter
	 */
	function renderPagination(data) {
		var resultsCount = document.getElementById('portal-coupons-results-count');
		var pagesContainer = document.getElementById('portal-coupons-pagination-pages');
		var prevBtn = document.getElementById('portal-coupons-prev-page');
		var nextBtn = document.getElementById('portal-coupons-next-page');

		var from = data.total > 0 ? (data.page - 1) * data.per_page + 1 : 0;
		var to = Math.min(data.page * data.per_page, data.total);

		if (resultsCount) {
			resultsCount.textContent = 'عرض ' + from + ' - ' + to + ' من ' + data.total + ' كوبون';
		}

		if (prevBtn) prevBtn.disabled = data.page <= 1;
		if (nextBtn) nextBtn.disabled = data.page >= data.total_pages;

		if (pagesContainer) {
			pagesContainer.innerHTML = '';
			var totalP = data.total_pages;
			var curP = data.page;

			// Generate page numbers
			var pages = [];
			if (totalP <= 7) {
				for (var i = 1; i <= totalP; i++) pages.push(i);
			} else {
				pages.push(1);
				if (curP > 3) pages.push('...');
				var start = Math.max(2, curP - 1);
				var end = Math.min(totalP - 1, curP + 1);
				for (var j = start; j <= end; j++) pages.push(j);
				if (curP < totalP - 2) pages.push('...');
				pages.push(totalP);
			}

			pages.forEach(function (p) {
				if (p === '...') {
					var span = document.createElement('span');
					span.className = 'w-8 h-8 flex items-center justify-center text-xs text-slate-400 font-bold';
					span.textContent = '...';
					pagesContainer.appendChild(span);
				} else {
					var btn = document.createElement('button');
					btn.type = 'button';
					var isCur = p === curP;
					btn.className = isCur
						? 'w-8 h-8 rounded-lg text-xs font-bold text-white bg-primary shadow-xs cursor-default'
						: 'w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-xs font-bold transition-colors cursor-pointer';
					btn.textContent = p;
					if (!isCur) {
						btn.addEventListener('click', function () {
							state.page = p;
							fetchCoupons();
						});
					}
					pagesContainer.appendChild(btn);
				}
			});
		}
	}

	/**
	 * Toggle coupon status (active <-> disabled)
	 */
	function toggleCouponStatus(couponId) {
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_toggle_coupon_status');
		formData.append('nonce', getNonce());
		formData.append('id', couponId);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (res) {
				if (res.success) {
					showToast(res.data.message || 'تم تحديث حالة الكوبون بنجاح');
					fetchCoupons();
				} else {
					showToast(res.data && res.data.message ? res.data.message : 'فشل تحديث الحالة', 'error');
				}
			})
			.catch(function () {
				showToast('حدث خطأ في الاتصال بالخادم.', 'error');
			});
	}

	/**
	 * Duplicate coupon
	 */
	function duplicateCoupon(couponId) {
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_duplicate_coupon');
		formData.append('nonce', getNonce());
		formData.append('id', couponId);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (res) {
				if (res.success) {
					showToast(res.data.message || 'تم نسخ الكوبون بنجاح');
					fetchCoupons();
				} else {
					showToast(res.data && res.data.message ? res.data.message : 'فشل نسخ الكوبون', 'error');
				}
			})
			.catch(function () {
				showToast('حدث خطأ في الاتصال بالخادم.', 'error');
			});
	}

	/**
	 * Setup Add / Edit Modal
	 */
	function initModal() {
		var addBtn = document.getElementById('portal-add-coupon-btn');
		var emptyAddBtn = document.getElementById('portal-coupons-empty-add-btn');
		var modal = document.getElementById('portal-coupon-modal');
		var closeBtn = document.getElementById('portal-coupon-modal-close');
		var cancelBtn = document.getElementById('portal-coupon-cancel-btn');
		var backdrop = document.getElementById('portal-coupon-modal-backdrop');
		var form = document.getElementById('portal-coupon-form');
		var typeSelect = document.getElementById('portal-coupon-field-type');
		var unitLabel = document.getElementById('portal-coupon-amount-unit');
		var maxDiscWrapper = document.getElementById('portal-coupon-wrapper-max-discount');
		var limitToggle = document.getElementById('portal-coupon-toggle-limit');
		var usageLimitInput = document.getElementById('portal-coupon-field-usage-limit');
		var accordionBtn = document.getElementById('portal-coupon-accordion-btn');
		var accordionBody = document.getElementById('portal-coupon-accordion-body');
		var accordionArrow = document.getElementById('portal-coupon-accordion-arrow');

		if (addBtn) addBtn.addEventListener('click', openAddModal);
		if (emptyAddBtn) emptyAddBtn.addEventListener('click', openAddModal);

		var closeModal = function () {
			if (modal) modal.classList.add('hidden');
			state.activeEditCoupon = null;
		};

		if (closeBtn) closeBtn.addEventListener('click', closeModal);
		if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
		if (backdrop) backdrop.addEventListener('click', closeModal);

		// Dynamic type adjustment
		if (typeSelect) {
			typeSelect.addEventListener('change', function () {
				var isPercent = typeSelect.value === 'percent';
				if (unitLabel) unitLabel.textContent = isPercent ? '%' : getCurrencySymbol();
				if (maxDiscWrapper) {
					maxDiscWrapper.style.display = isPercent ? 'block' : 'none';
				}
			});
		}

		// Usage limit toggle
		if (limitToggle && usageLimitInput) {
			limitToggle.addEventListener('change', function () {
				usageLimitInput.disabled = !limitToggle.checked;
				if (limitToggle.checked && !usageLimitInput.value) {
					usageLimitInput.value = '100';
				}
			});
		}

		// Accordion
		if (accordionBtn && accordionBody) {
			accordionBtn.addEventListener('click', function () {
				accordionBody.classList.toggle('hidden');
				if (accordionArrow) {
					accordionArrow.style.transform = accordionBody.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
				}
			});
		}

		// Form Submission
		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				saveCoupon();
			});
		}
	}

	/**
	 * Open Modal in Add Mode
	 */
	function openAddModal() {
		var modal = document.getElementById('portal-coupon-modal');
		var title = document.getElementById('portal-coupon-modal-title');
		var form = document.getElementById('portal-coupon-form');
		var errorEl = document.getElementById('portal-coupon-modal-error');
		var idField = document.getElementById('portal-coupon-field-id');
		var codeField = document.getElementById('portal-coupon-field-code');
		var typeField = document.getElementById('portal-coupon-field-type');
		var amountField = document.getElementById('portal-coupon-field-amount');
		var descField = document.getElementById('portal-coupon-field-desc');
		var startField = document.getElementById('portal-coupon-field-start-date');
		var expiryField = document.getElementById('portal-coupon-field-expiry-date');
		var minSpendField = document.getElementById('portal-coupon-field-min-spend');
		var maxDiscField = document.getElementById('portal-coupon-field-max-discount');
		var limitToggle = document.getElementById('portal-coupon-toggle-limit');
		var usageLimitField = document.getElementById('portal-coupon-field-usage-limit');
		var userLimitField = document.getElementById('portal-coupon-field-user-limit');
		var prodIdsField = document.getElementById('portal-coupon-field-product-ids');
		var exclProdIdsField = document.getElementById('portal-coupon-field-excluded-product-ids');
		var catIdsField = document.getElementById('portal-coupon-field-cat-ids');
		var exclCatIdsField = document.getElementById('portal-coupon-field-excluded-cat-ids');
		var indivCb = document.getElementById('portal-coupon-field-individual');
		var exclSaleCb = document.getElementById('portal-coupon-field-exclude-sale');
		var disabledCb = document.getElementById('portal-coupon-field-disabled');

		state.activeEditCoupon = null;
		if (form) form.reset();
		if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }
		if (title) title.textContent = 'إضافة كوبون جديد';
		if (idField) idField.value = '0';

		if (limitToggle) limitToggle.checked = false;
		if (usageLimitField) usageLimitField.disabled = true;

		var typeSelect = document.getElementById('portal-coupon-field-type');
		if (typeSelect) {
			typeSelect.value = 'percent';
			typeSelect.dispatchEvent(new Event('change'));
		}

		if (modal) modal.classList.remove('hidden');
		if (codeField) codeField.focus();
	}

	/**
	 * Open Modal in Edit Mode
	 */
	function openEditModal(coupon) {
		var modal = document.getElementById('portal-coupon-modal');
		var title = document.getElementById('portal-coupon-modal-title');
		var errorEl = document.getElementById('portal-coupon-modal-error');
		var idField = document.getElementById('portal-coupon-field-id');
		var codeField = document.getElementById('portal-coupon-field-code');
		var typeField = document.getElementById('portal-coupon-field-type');
		var amountField = document.getElementById('portal-coupon-field-amount');
		var descField = document.getElementById('portal-coupon-field-desc');
		var startField = document.getElementById('portal-coupon-field-start-date');
		var expiryField = document.getElementById('portal-coupon-field-expiry-date');
		var minSpendField = document.getElementById('portal-coupon-field-min-spend');
		var maxDiscField = document.getElementById('portal-coupon-field-max-discount');
		var limitToggle = document.getElementById('portal-coupon-toggle-limit');
		var usageLimitField = document.getElementById('portal-coupon-field-usage-limit');
		var userLimitField = document.getElementById('portal-coupon-field-user-limit');
		var prodIdsField = document.getElementById('portal-coupon-field-product-ids');
		var exclProdIdsField = document.getElementById('portal-coupon-field-excluded-product-ids');
		var catIdsField = document.getElementById('portal-coupon-field-cat-ids');
		var exclCatIdsField = document.getElementById('portal-coupon-field-excluded-cat-ids');
		var indivCb = document.getElementById('portal-coupon-field-individual');
		var exclSaleCb = document.getElementById('portal-coupon-field-exclude-sale');
		var disabledCb = document.getElementById('portal-coupon-field-disabled');

		state.activeEditCoupon = coupon;
		if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }
		if (title) title.textContent = 'تعديل الكوبون: ' + coupon.code;
		if (idField) idField.value = coupon.id;
		if (codeField) codeField.value = coupon.code;
		if (typeField) {
			typeField.value = coupon.discount_type;
			typeField.dispatchEvent(new Event('change'));
		}
		if (amountField) amountField.value = coupon.amount;
		if (descField) descField.value = coupon.description || '';
		if (startField) startField.value = coupon.start_date_raw || '';
		if (expiryField) expiryField.value = coupon.expiry_date_raw || '';
		if (minSpendField) minSpendField.value = coupon.minimum_amount > 0 ? coupon.minimum_amount : '';
		if (maxDiscField) maxDiscField.value = coupon.max_discount > 0 ? coupon.max_discount : '';

		if (limitToggle && usageLimitField) {
			limitToggle.checked = coupon.has_limit;
			usageLimitField.disabled = !coupon.has_limit;
			usageLimitField.value = coupon.has_limit ? coupon.usage_limit : '';
		}

		if (userLimitField) userLimitField.value = coupon.usage_limit_per_user > 0 ? coupon.usage_limit_per_user : '';
		if (prodIdsField) prodIdsField.value = (coupon.product_ids || []).join(', ');
		if (exclProdIdsField) exclProdIdsField.value = (coupon.excluded_product_ids || []).join(', ');
		if (catIdsField) catIdsField.value = (coupon.product_categories || []).join(', ');
		if (exclCatIdsField) exclCatIdsField.value = (coupon.excluded_product_categories || []).join(', ');

		if (indivCb) indivCb.checked = !!coupon.individual_use;
		if (exclSaleCb) exclSaleCb.checked = !!coupon.exclude_sale_items;
		if (disabledCb) disabledCb.checked = !!coupon.is_disabled;

		if (modal) modal.classList.remove('hidden');
	}

	/**
	 * Save (Create or Update) Coupon via AJAX
	 */
	function saveCoupon() {
		var errorEl = document.getElementById('portal-coupon-modal-error');
		var spinner = document.getElementById('portal-coupon-save-spinner');
		var saveBtn = document.getElementById('portal-coupon-save-btn');

		var id = parseInt(document.getElementById('portal-coupon-field-id').value, 10) || 0;
		var code = (document.getElementById('portal-coupon-field-code').value || '').trim();
		var type = document.getElementById('portal-coupon-field-type').value;
		var amount = parseFloat(document.getElementById('portal-coupon-field-amount').value);
		var desc = (document.getElementById('portal-coupon-field-desc').value || '').trim();
		var startDate = document.getElementById('portal-coupon-field-start-date').value;
		var expiryDate = document.getElementById('portal-coupon-field-expiry-date').value;
		var minSpend = parseFloat(document.getElementById('portal-coupon-field-min-spend').value) || 0;
		var maxDiscount = parseFloat(document.getElementById('portal-coupon-field-max-discount').value) || 0;

		var limitToggle = document.getElementById('portal-coupon-toggle-limit');
		var usageLimit = (limitToggle && limitToggle.checked)
			? parseInt(document.getElementById('portal-coupon-field-usage-limit').value, 10) || 0
			: 0;

		var userLimit = parseInt(document.getElementById('portal-coupon-field-user-limit').value, 10) || 0;

		var prodIdsRaw = document.getElementById('portal-coupon-field-product-ids').value || '';
		var exclProdIdsRaw = document.getElementById('portal-coupon-field-excluded-product-ids').value || '';
		var catIdsRaw = document.getElementById('portal-coupon-field-cat-ids').value || '';
		var exclCatIdsRaw = document.getElementById('portal-coupon-field-excluded-cat-ids').value || '';

		var indiv = document.getElementById('portal-coupon-field-individual').checked ? 1 : 0;
		var exclSale = document.getElementById('portal-coupon-field-exclude-sale').checked ? 1 : 0;
		var isDisabled = document.getElementById('portal-coupon-field-disabled').checked ? 1 : 0;

		// Client validation
		if (!code) {
			showModalError('يرجى إدخال رمز الكوبون.');
			return;
		}
		if (isNaN(amount) || amount <= 0) {
			showModalError('يرجى إدخال قيمة خصم صالحة أكبر من الصفر.');
			return;
		}
		if (type === 'percent' && amount > 100) {
			showModalError('نسبة الخصم لا يمكن أن تتجاوز 100%.');
			return;
		}
		if (startDate && expiryDate && new Date(startDate) >= new Date(expiryDate)) {
			showModalError('تاريخ البداية يجب أن يكون قبل تاريخ الانتهاء.');
			return;
		}

		if (errorEl) errorEl.classList.add('hidden');
		if (spinner) spinner.classList.remove('hidden');
		if (saveBtn) saveBtn.disabled = true;

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_save_coupon');
		formData.append('nonce', getNonce());
		formData.append('id', id);
		formData.append('code', code);
		formData.append('discount_type', type);
		formData.append('amount', amount);
		formData.append('description', desc);
		formData.append('start_date', startDate);
		formData.append('expiry_date', expiryDate);
		formData.append('minimum_amount', minSpend);
		formData.append('max_discount', maxDiscount);
		formData.append('usage_limit', usageLimit);
		formData.append('usage_limit_per_user', userLimit);
		formData.append('individual_use', indiv);
		formData.append('exclude_sale_items', exclSale);
		formData.append('is_disabled', isDisabled);

		// Parse comma separated IDs
		var parseIds = function (str) {
			return str.split(',').map(function (s) { return parseInt(s.trim(), 10); }).filter(function (n) { return !isNaN(n) && n > 0; });
		};

		parseIds(prodIdsRaw).forEach(function (pid) { formData.append('product_ids[]', pid); });
		parseIds(exclProdIdsRaw).forEach(function (pid) { formData.append('excluded_product_ids[]', pid); });
		parseIds(catIdsRaw).forEach(function (cid) { formData.append('product_categories[]', cid); });
		parseIds(exclCatIdsRaw).forEach(function (cid) { formData.append('excluded_product_categories[]', cid); });

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (res) {
				if (spinner) spinner.classList.add('hidden');
				if (saveBtn) saveBtn.disabled = false;

				if (res.success) {
					var modal = document.getElementById('portal-coupon-modal');
					if (modal) modal.classList.add('hidden');
					showToast(res.data.message || 'تم حفظ الكوبون بنجاح');
					fetchCoupons();
				} else {
					showModalError(res.data && res.data.message ? res.data.message : 'فشل حفظ الكوبون');
				}
			})
			.catch(function () {
				if (spinner) spinner.classList.add('hidden');
				if (saveBtn) saveBtn.disabled = false;
				showModalError('حدث خطأ في الاتصال بالخادم.');
			});
	}

	function showModalError(msg) {
		var errorEl = document.getElementById('portal-coupon-modal-error');
		if (errorEl) {
			errorEl.textContent = msg;
			errorEl.classList.remove('hidden');
		} else {
			alert(msg);
		}
	}

	/**
	 * Setup Delete Confirmation Modal
	 */
	function initDeleteModal() {
		var modal = document.getElementById('portal-coupon-delete-modal');
		var cancelBtn = document.getElementById('portal-coupon-cancel-delete-btn');
		var confirmBtn = document.getElementById('portal-coupon-confirm-delete-btn');
		var backdrop = document.getElementById('portal-coupon-delete-backdrop');

		var close = function () {
			if (modal) modal.classList.add('hidden');
			state.deletingCouponId = null;
		};

		if (cancelBtn) cancelBtn.addEventListener('click', close);
		if (backdrop) backdrop.addEventListener('click', close);

		if (confirmBtn) {
			confirmBtn.addEventListener('click', function () {
				if (!state.deletingCouponId) return;

				confirmBtn.disabled = true;
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_delete_coupon');
				formData.append('nonce', getNonce());
				formData.append('id', state.deletingCouponId);

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData
				})
					.then(function (res) { return res.json(); })
					.then(function (res) {
						confirmBtn.disabled = false;
						close();
						if (res.success) {
							showToast(res.data.message || 'تم حذف الكوبون بنجاح');
							fetchCoupons();
						} else {
							showToast(res.data && res.data.message ? res.data.message : 'فشل حذف الكوبون', 'error');
						}
					})
					.catch(function () {
						confirmBtn.disabled = false;
						close();
						showToast('حدث خطأ أثناء حذف الكوبون.', 'error');
					});
			});
		}
	}

	function openDeleteModal(couponId, code) {
		state.deletingCouponId = couponId;
		var modal = document.getElementById('portal-coupon-delete-modal');
		var codeSpan = document.getElementById('portal-coupon-delete-code');
		if (codeSpan) codeSpan.textContent = '"' + code + '"';
		if (modal) modal.classList.remove('hidden');
	}

	/**
	 * Setup Usage Details Modal
	 */
	function initUsageModal() {
		var modal = document.getElementById('portal-coupon-usage-modal');
		var closeBtn = document.getElementById('portal-coupon-usage-close');
		var backdrop = document.getElementById('portal-coupon-usage-backdrop');

		var close = function () {
			if (modal) modal.classList.add('hidden');
		};

		if (closeBtn) closeBtn.addEventListener('click', close);
		if (backdrop) backdrop.addEventListener('click', close);
	}

	function openUsageModal(couponId) {
		var modal = document.getElementById('portal-coupon-usage-modal');
		var titleCode = document.getElementById('portal-coupon-usage-title-code');
		var statCount = document.getElementById('portal-coupon-usage-stat-count');
		var statLimit = document.getElementById('portal-coupon-usage-stat-limit');
		var statStatus = document.getElementById('portal-coupon-usage-stat-status');
		var ordersTbody = document.getElementById('portal-coupon-usage-orders-tbody');

		if (titleCode) titleCode.textContent = '...';
		if (ordersTbody) ordersTbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-slate-400">جاري تحميل الاستخدامات...</td></tr>';
		if (modal) modal.classList.remove('hidden');

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_coupon_usage');
		formData.append('nonce', getNonce());
		formData.append('id', couponId);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (res) {
				if (!res.success) {
					if (ordersTbody) ordersTbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-rose-500">فشل في تحميل تفاصيل الاستخدام.</td></tr>';
					return;
				}

				var data = res.data;
				var coupon = data.coupon;
				if (titleCode) titleCode.textContent = coupon.code;
				if (statCount) statCount.textContent = coupon.usage_count;
				if (statLimit) statLimit.textContent = coupon.has_limit ? coupon.usage_limit : '∞ غير محدود';
				if (statStatus) statStatus.innerHTML = renderStatusBadge(coupon.status, coupon.status_label, coupon.id);

				if (!data.orders || data.orders.length === 0) {
					if (ordersTbody) ordersTbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-slate-400">لم يتم استخدام هذا الكوبون في أي طلبات بعد.</td></tr>';
					return;
				}

				if (ordersTbody) {
					ordersTbody.innerHTML = '';
					data.orders.forEach(function (order) {
						var tr = document.createElement('tr');
						tr.className = 'hover:bg-slate-50 transition-colors border-b border-slate-100';
						tr.innerHTML = `
							<td class="py-2.5 px-3 font-bold text-slate-900">
								<a href="${order.view_url}" target="_blank" class="hover:text-primary transition-colors">#${order.order_number}</a>
							</td>
							<td class="py-2.5 px-3 text-slate-500">${order.date}</td>
							<td class="py-2.5 px-3 font-medium text-slate-700">${escapeHtml(order.customer_name)}</td>
							<td class="py-2.5 px-3 font-bold font-sans text-slate-900">${order.total_formatted}</td>
							<td class="py-2.5 px-3">
								<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">${escapeHtml(order.status)}</span>
							</td>
						`;
						ordersTbody.appendChild(tr);
					});
				}
			})
			.catch(function () {
				if (ordersTbody) ordersTbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-rose-500">حدث خطأ في الاتصال بالخادم.</td></tr>';
			});
	}

	/**
	 * Setup Floating Bulk Actions
	 */
	function initBulkActions() {
		var bulkDeleteBtn = document.getElementById('portal-coupons-bulk-delete-btn');
		var bulkCancelBtn = document.getElementById('portal-coupons-bulk-cancel-btn');

		if (bulkCancelBtn) {
			bulkCancelBtn.addEventListener('click', function () {
				state.selectedCouponIds = [];
				var selectAllCb = document.getElementById('portal-coupons-select-all');
				if (selectAllCb) selectAllCb.checked = false;
				document.querySelectorAll('.portal-coupon-row-cb').forEach(function (cb) {
					cb.checked = false;
				});
				updateBulkBar();
			});
		}

		if (bulkDeleteBtn) {
			bulkDeleteBtn.addEventListener('click', function () {
				if (state.selectedCouponIds.length === 0) return;

				if (!confirm('هل أنت متأكد من رغبتك في حذف ' + state.selectedCouponIds.length + ' كوبونات نهائياً؟')) {
					return;
				}

				bulkDeleteBtn.disabled = true;
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_bulk_delete_coupons');
				formData.append('nonce', getNonce());
				state.selectedCouponIds.forEach(function (id) {
					formData.append('ids[]', id);
				});

				fetch(getAjaxUrl(), {
					method: 'POST',
					body: formData
				})
					.then(function (res) { return res.json(); })
					.then(function (res) {
						bulkDeleteBtn.disabled = false;
						if (res.success) {
							showToast(res.data.message || 'تم حذف الكوبونات بنجاح');
							state.selectedCouponIds = [];
							fetchCoupons();
						} else {
							showToast(res.data && res.data.message ? res.data.message : 'فشل حذف الكوبونات', 'error');
						}
					})
					.catch(function () {
						bulkDeleteBtn.disabled = false;
						showToast('حدث خطأ أثناء الحذف الجماعي.', 'error');
					});
			});
		}
	}

	function updateBulkBar() {
		var bar = document.getElementById('portal-coupons-bulk-bar');
		var countEl = document.getElementById('portal-coupons-bulk-count');
		if (!bar || !countEl) return;

		var count = state.selectedCouponIds.length;
		if (count > 0) {
			countEl.textContent = 'تم تحديد ' + count + ' كوبون';
			bar.classList.remove('hidden');
		} else {
			bar.classList.add('hidden');
		}
	}

	/**
	 * HTML Escape Helper
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

})();
