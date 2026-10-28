/**
 * Ashrafco Admin Portal - Shipping & Delivery Management Controller
 *
 * Implements real-time governorate search, status filtering, sorting, pagination,
 * Add / Edit governorate modals, active/deactive status toggles, deletion/reset,
 * KPI statistics updating, and Chart.js visualization for top governorates.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function () {
	'use strict';

	var state = {
		search: '',
		status: 'all',
		sort: 'orders_desc',
		page: 1,
		perPage: 10,
		total: 0,
		totalPages: 1,
		loading: false,
		items: [],
		filteredItems: [],
		summary: null,
		topGovs: [],
		chartInstance: null,
		activeActionMenu: null,
		editingGovernorate: null,
		confirmAction: null
	};

	function getConfig() {
		return window.AshrafcoShippingConfig || {};
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
		return cfg.currencySymbol || 'ج.م';
	}

	document.addEventListener('DOMContentLoaded', function () {
		initFilters();
		initModal();
		initConfirmModal();
		initGlobalMenuCloser();

		// Auto load if initial active tab is shipping
		var shippingTab = document.getElementById('portal-tab-shipping');
		if (shippingTab && shippingTab.classList.contains('active')) {
			fetchShippingData();
		}
	});

	// Global hook called by switchPortalTab('shipping')
	window.AshrafcoShippingReload = function () {
		fetchShippingData();
	};

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-shipping-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-shipping-toast';
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
	 * Fetch shipping data from server
	 */
	function fetchShippingData() {
		state.loading = true;
		renderLoading();

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_shipping_data');
		formData.append('nonce', getNonce());
		formData.append('search', state.search);
		formData.append('status', state.status);
		formData.append('sort', state.sort);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				state.loading = false;
				if (data.success && data.data) {
					state.items = data.data.items || [];
					state.filteredItems = state.items;
					state.summary = data.data.summary || null;
					state.topGovs = data.data.top || [];

					updateKPIs(state.summary);
					renderTable();
					renderChart(state.topGovs);
				} else {
					var msg = (data.data && data.data.message) ? data.data.message : 'تعذر تحميل بيانات الشحن.';
					showToast(msg, 'error');
					renderEmpty(msg);
				}
			})
			.catch(function (err) {
				state.loading = false;
				showToast('حدث خطأ أثناء الاتصال بالخادم.', 'error');
				renderEmpty('حدث خطأ في الشبكة.');
			});
	}

	/**
	 * Update KPI summary cards
	 */
	function updateKPIs(summary) {
		if (!summary) return;

		var elTotal = document.getElementById('portal-shipping-count-total');
		var elOrders = document.getElementById('portal-shipping-count-orders');
		var elAvg = document.getElementById('portal-shipping-count-avg-price');
		var elActive = document.getElementById('portal-shipping-count-active-govs');

		if (elTotal) elTotal.textContent = summary.total_governorates || 0;
		if (elOrders) elOrders.textContent = (summary.total_orders || 0).toLocaleString('ar-EG');
		if (elAvg) elAvg.textContent = summary.avg_price || 0;
		if (elActive) elActive.textContent = summary.govs_with_orders || 0;
	}

	/**
	 * Render Loading skeleton
	 */
	function renderLoading() {
		var tbody = document.getElementById('portal-shipping-tbody');
		if (!tbody) return;

		tbody.innerHTML = '<tr id="portal-shipping-loading-row">' +
			'<td colspan="7" class="py-12 text-center text-slate-400">' +
			'<div class="inline-flex items-center gap-2">' +
			'<svg class="w-5 h-5 animate-spin text-primary" fill="none" viewBox="0 0 24 24">' +
			'<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
			'<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>' +
			'</svg>' +
			'<span>جاري تحميل بيانات الشحن...</span>' +
			'</div></td></tr>';
	}

	/**
	 * Render Empty state
	 */
	function renderEmpty(message) {
		var tbody = document.getElementById('portal-shipping-tbody');
		if (!tbody) return;

		tbody.innerHTML = '<tr>' +
			'<td colspan="7" class="py-12 text-center text-slate-400">' +
			'<div class="flex flex-col items-center justify-center gap-2">' +
			'<svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
			'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />' +
			'</svg>' +
			'<span class="text-xs font-bold text-slate-500">' + (message || 'لا توجد محافظات مطابقة للبحث') + '</span>' +
			'</div></td></tr>';

		var infoEl = document.getElementById('portal-shipping-pagination-info');
		if (infoEl) infoEl.textContent = 'إجمالي النتائج: 0';
		var btnsEl = document.getElementById('portal-shipping-pagination-btns');
		if (btnsEl) btnsEl.innerHTML = '';
	}

	/**
	 * Render Table with Pagination
	 */
	function renderTable() {
		var tbody = document.getElementById('portal-shipping-tbody');
		if (!tbody) return;

		var items = state.filteredItems;
		state.total = items.length;
		state.totalPages = Math.max(1, Math.ceil(state.total / state.perPage));

		if (state.page > state.totalPages) {
			state.page = state.totalPages;
		}

		if (items.length === 0) {
			renderEmpty();
			return;
		}

		var startIdx = (state.page - 1) * state.perPage;
		var pageItems = items.slice(startIdx, startIdx + state.perPage);

		var html = '';
		var currency = getCurrencySymbol();

		pageItems.forEach(function (item) {
			var statusBadge = item.active
				? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>نشطة</span>'
				: '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>معطّلة</span>';

			var customBadge = item.is_custom
				? '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 mr-1.5">مخصصة</span>'
				: '';

			html += '<tr class="hover:bg-slate-50/70 transition-colors" data-code="' + escapeHtml(item.code) + '">' +
				// Governorate name (plain text, NO images)
				'<td class="py-3.5 px-4 font-bold text-slate-900">' +
				'<div class="flex items-center gap-2">' +
				'<span>' + escapeHtml(item.name) + '</span>' +
				customBadge +
				'</div>' +
				'</td>' +

				// Price
				'<td class="py-3.5 px-4 font-mono font-black text-slate-800">' +
				escapeHtml(item.price.toLocaleString('ar-EG')) + ' ' + currency +
				'</td>' +

				// Orders count
				'<td class="py-3.5 px-4 font-mono font-bold text-slate-700">' +
				(item.orders > 0
					? '<span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 font-black">' + item.orders.toLocaleString('ar-EG') + '</span>'
					: '<span class="text-slate-400">0</span>') +
				'</td>' +

				// Customers count (distinct)
				'<td class="py-3.5 px-4 font-mono font-bold text-slate-700">' +
				(item.customers > 0
					? '<span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700">' + item.customers.toLocaleString('ar-EG') + '</span>'
					: '<span class="text-slate-400">0</span>') +
				'</td>' +

				// Status Badge
				'<td class="py-3.5 px-4">' + statusBadge + '</td>' +

				// Updated At
				'<td class="py-3.5 px-4 text-xs text-slate-400 font-mono" dir="ltr" style="text-align: right;">' +
				escapeHtml(item.updated_at) +
				'</td>' +

				// Actions
				'<td class="py-3.5 px-4 text-center">' +
				'<div class="relative inline-flex items-center gap-1.5">' +
				// Quick Edit Price Button
				'<button type="button" class="portal-shipping-edit-btn p-1.5 rounded-lg text-slate-400 hover:text-primary hover:bg-rose-50 transition-colors cursor-pointer" title="تعديل السعر" data-code="' + escapeHtml(item.code) + '">' +
				'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>' +
				'</button>' +

				// Toggle Status Button
				'<button type="button" class="portal-shipping-toggle-btn p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" title="' + (item.active ? 'تعطيل الشحن' : 'تفعيل الشحن') + '" data-code="' + escapeHtml(item.code) + '" data-active="' + (item.active ? '1' : '0') + '">' +
				(item.active
					? '<svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>'
					: '<svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>') +
				'</button>' +

				(item.is_custom
					? '<button type="button" class="portal-shipping-delete-btn p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="حذف المحافظة" data-code="' + escapeHtml(item.code) + '">' +
					'<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
					'</button>'
					: '') +
				'</div>' +
				'</td>' +
				'</tr>';
		});

		tbody.innerHTML = html;
		attachRowActionListeners();
		renderPagination();
	}

	/**
	 * Render pagination controls
	 */
	function renderPagination() {
		var infoEl = document.getElementById('portal-shipping-pagination-info');
		var btnsEl = document.getElementById('portal-shipping-pagination-btns');
		if (!infoEl || !btnsEl) return;

		var startIdx = state.total === 0 ? 0 : (state.page - 1) * state.perPage + 1;
		var endIdx = Math.min(state.page * state.perPage, state.total);
		infoEl.textContent = 'عرض ' + startIdx + ' - ' + endIdx + ' من إجمالي ' + state.total + ' محافظة';

		var btnsHtml = '';
		if (state.totalPages > 1) {
			// Previous button
			btnsHtml += '<button type="button" class="px-2.5 py-1 rounded-lg border text-xs font-bold transition-colors cursor-pointer ' +
				(state.page > 1 ? 'border-slate-200 text-slate-600 hover:bg-slate-100' : 'border-slate-100 text-slate-300 pointer-events-none') +
				'" data-page="' + (state.page - 1) + '">السابق</button>';

			for (var i = 1; i <= state.totalPages; i++) {
				var activeClass = i === state.page
					? 'bg-primary text-white border-primary'
					: 'border-slate-200 text-slate-600 hover:bg-slate-100';
				btnsHtml += '<button type="button" class="w-7 h-7 rounded-lg border text-xs font-bold flex items-center justify-center transition-colors cursor-pointer ' + activeClass + '" data-page="' + i + '">' + i + '</button>';
			}

			// Next button
			btnsHtml += '<button type="button" class="px-2.5 py-1 rounded-lg border text-xs font-bold transition-colors cursor-pointer ' +
				(state.page < state.totalPages ? 'border-slate-200 text-slate-600 hover:bg-slate-100' : 'border-slate-100 text-slate-300 pointer-events-none') +
				'" data-page="' + (state.page + 1) + '">التالي</button>';
		}

		btnsEl.innerHTML = btnsHtml;

		// Attach pagination click
		btnsEl.querySelectorAll('button[data-page]').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var targetPage = parseInt(this.getAttribute('data-page'), 10);
				if (targetPage >= 1 && targetPage <= state.totalPages && targetPage !== state.page) {
					state.page = targetPage;
					renderTable();
				}
			});
		});
	}

	/**
	 * Attach event listeners to table rows
	 */
	function attachRowActionListeners() {
		// Edit
		document.querySelectorAll('.portal-shipping-edit-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var code = this.getAttribute('data-code');
				openEditModal(code);
			});
		});

		// Toggle Active / Inactive
		document.querySelectorAll('.portal-shipping-toggle-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var code = this.getAttribute('data-code');
				var isActive = this.getAttribute('data-active') === '1';
				promptToggleStatus(code, isActive);
			});
		});

		// Delete Custom
		document.querySelectorAll('.portal-shipping-delete-btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				var code = this.getAttribute('data-code');
				promptDelete(code);
			});
		});
	}

	/**
	 * Render Chart.js Top Governorates
	 */
	function renderChart(topGovs) {
		var canvas = document.getElementById('ashrafcoShippingTopChart');
		if (!canvas || typeof Chart === 'undefined') return;

		// Destroy previous chart if exists
		if (state.chartInstance) {
			state.chartInstance.destroy();
			state.chartInstance = null;
		}

		// Prepare labels & data (default top 5)
		var labels = [];
		var dataPoints = [];

		if (Array.isArray(topGovs) && topGovs.length > 0) {
			topGovs.forEach(function (item) {
				labels.push(item.name);
				dataPoints.push(item.orders);
			});
		} else {
			labels = ['لا توجد طلبات بعد'];
			dataPoints = [0];
		}

		var ctx = canvas.getContext('2d');
		state.chartInstance = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'عدد الطلبات',
					data: dataPoints,
					backgroundColor: '#d90429',
					borderRadius: 8,
					borderSkipped: false,
					maxBarThickness: 36
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				plugins: {
					legend: { display: false },
					tooltip: {
						rtl: true,
						titleFont: { family: 'Cairo', weight: 'bold' },
						bodyFont: { family: 'Cairo' },
						callbacks: {
							label: function (context) {
								return ' ' + context.parsed.y + ' طلب';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							precision: 0,
							font: { family: 'Cairo', size: 10 },
							color: '#64748b'
						},
						grid: {
							color: '#f1f5f9'
						}
					},
					x: {
						ticks: {
							font: { family: 'Cairo', weight: 'bold', size: 11 },
							color: '#334155'
						},
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	/**
	 * Initialize Filters & Search
	 */
	function initFilters() {
		var searchInput = document.getElementById('portal-shipping-search');
		var filterStatus = document.getElementById('portal-shipping-filter-status');
		var filterSort = document.getElementById('portal-shipping-filter-sort');

		if (searchInput) {
			var searchDebounceTimer = null;
			searchInput.addEventListener('input', function () {
				var val = this.value.trim();
				clearTimeout(searchDebounceTimer);
				searchDebounceTimer = setTimeout(function () {
					state.search = val;
					state.page = 1;
					fetchShippingData();
				}, 250);
			});
		}

		if (filterStatus) {
			filterStatus.addEventListener('change', function () {
				state.status = this.value;
				state.page = 1;
				fetchShippingData();
			});
		}

		if (filterSort) {
			filterSort.addEventListener('change', function () {
				state.sort = this.value;
				state.page = 1;
				fetchShippingData();
			});
		}
	}

	/**
	 * Initialize Add / Edit Modal
	 */
	function initModal() {
		var addBtn = document.getElementById('portal-add-governorate-btn');
		var modal = document.getElementById('portal-shipping-modal');
		var closeBtn = document.getElementById('portal-shipping-modal-close');
		var cancelBtn = document.getElementById('portal-shipping-modal-cancel');
		var backdrop = document.getElementById('portal-shipping-modal-backdrop');
		var form = document.getElementById('portal-shipping-form');

		if (addBtn) {
			addBtn.addEventListener('click', function () {
				openAddModal();
			});
		}

		function closeModal() {
			if (modal) modal.classList.add('hidden');
			state.editingGovernorate = null;
		}

		if (closeBtn) closeBtn.addEventListener('click', closeModal);
		if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
		if (backdrop) backdrop.addEventListener('click', closeModal);

		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				saveGovernorateForm();
			});
		}
	}

	function openAddModal() {
		var modal = document.getElementById('portal-shipping-modal');
		var titleEl = document.getElementById('portal-shipping-modal-title');
		var errorEl = document.getElementById('portal-shipping-form-error');
		var fieldCode = document.getElementById('portal-shipping-field-code');
		var fieldIsEdit = document.getElementById('portal-shipping-field-is-edit');
		var fieldName = document.getElementById('portal-shipping-field-name');
		var fieldPrice = document.getElementById('portal-shipping-field-price');
		var fieldActive = document.getElementById('portal-shipping-field-active');

		if (!modal) return;

		state.editingGovernorate = null;
		if (titleEl) titleEl.textContent = 'إضافة محافظة جديدة';
		if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }

		if (fieldCode) fieldCode.value = '';
		if (fieldIsEdit) fieldIsEdit.value = '0';
		if (fieldName) { fieldName.value = ''; fieldName.disabled = false; }
		if (fieldPrice) fieldPrice.value = '';
		if (fieldActive) fieldActive.checked = true;

		modal.classList.remove('hidden');
		if (fieldName) fieldName.focus();
	}

	function openEditModal(code) {
		var item = state.items.find(function (g) { return g.code === code; });
		if (!item) return;

		var modal = document.getElementById('portal-shipping-modal');
		var titleEl = document.getElementById('portal-shipping-modal-title');
		var errorEl = document.getElementById('portal-shipping-form-error');
		var fieldCode = document.getElementById('portal-shipping-field-code');
		var fieldIsEdit = document.getElementById('portal-shipping-field-is-edit');
		var fieldName = document.getElementById('portal-shipping-field-name');
		var fieldPrice = document.getElementById('portal-shipping-field-price');
		var fieldActive = document.getElementById('portal-shipping-field-active');

		if (!modal) return;

		state.editingGovernorate = item;
		if (titleEl) titleEl.textContent = 'تعديل محافظة: ' + item.name;
		if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }

		if (fieldCode) fieldCode.value = item.code;
		if (fieldIsEdit) fieldIsEdit.value = '1';
		if (fieldName) {
			fieldName.value = item.name;
			fieldName.disabled = !item.is_custom; // Standard governorate names shouldn't be altered arbitrarily
		}
		if (fieldPrice) fieldPrice.value = item.price;
		if (fieldActive) fieldActive.checked = !!item.active;

		modal.classList.remove('hidden');
		if (fieldPrice) fieldPrice.focus();
	}

	function saveGovernorateForm() {
		var fieldCode = document.getElementById('portal-shipping-field-code');
		var fieldIsEdit = document.getElementById('portal-shipping-field-is-edit');
		var fieldName = document.getElementById('portal-shipping-field-name');
		var fieldPrice = document.getElementById('portal-shipping-field-price');
		var fieldActive = document.getElementById('portal-shipping-field-active');
		var errorEl = document.getElementById('portal-shipping-form-error');
		var spinner = document.getElementById('portal-shipping-submit-spinner');
		var submitBtn = document.getElementById('portal-shipping-modal-submit');

		var name = fieldName ? fieldName.value.trim() : '';
		var price = fieldPrice ? parseFloat(fieldPrice.value) : 0;
		var active = fieldActive ? (fieldActive.checked ? '1' : '0') : '1';
		var isEdit = fieldIsEdit ? fieldIsEdit.value : '0';
		var code = fieldCode ? fieldCode.value.trim() : '';

		if (!name) {
			if (errorEl) { errorEl.textContent = 'يرجى إدخال اسم المحافظة.'; errorEl.classList.remove('hidden'); }
			return;
		}

		if (isNaN(price) || price < 0) {
			if (errorEl) { errorEl.textContent = 'سعر الشحن لا يمكن أن يكون سالباً.'; errorEl.classList.remove('hidden'); }
			return;
		}

		if (spinner) spinner.classList.remove('hidden');
		if (submitBtn) submitBtn.disabled = true;

		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_save_governorate');
		formData.append('nonce', getNonce());
		formData.append('code', code);
		formData.append('name', name);
		formData.append('price', price);
		formData.append('active', active);
		formData.append('is_edit', isEdit);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (spinner) spinner.classList.add('hidden');
				if (submitBtn) submitBtn.disabled = false;

				if (data.success) {
					var modal = document.getElementById('portal-shipping-modal');
					if (modal) modal.classList.add('hidden');
					showToast(data.data && data.data.message ? data.data.message : 'تم حفظ بيانات المحافظة.');
					fetchShippingData();
				} else {
					var msg = data.data && data.data.message ? data.data.message : 'تعذر حفظ المحافظة.';
					if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('hidden'); }
				}
			})
			.catch(function () {
				if (spinner) spinner.classList.add('hidden');
				if (submitBtn) submitBtn.disabled = false;
				if (errorEl) { errorEl.textContent = 'حدث خطأ في الاتصال بالخادم.'; errorEl.classList.remove('hidden'); }
			});
	}

	/**
	 * Confirmation Modal (Toggle Deactivate & Delete)
	 */
	function initConfirmModal() {
		var modal = document.getElementById('portal-shipping-confirm-modal');
		var cancelBtn = document.getElementById('portal-shipping-confirm-cancel');
		var backdrop = document.getElementById('portal-shipping-confirm-backdrop');
		var actionBtn = document.getElementById('portal-shipping-confirm-action');

		function closeConfirm() {
			if (modal) modal.classList.add('hidden');
			state.confirmAction = null;
		}

		if (cancelBtn) cancelBtn.addEventListener('click', closeConfirm);
		if (backdrop) backdrop.addEventListener('click', closeConfirm);

		if (actionBtn) {
			actionBtn.addEventListener('click', function () {
				if (typeof state.confirmAction === 'function') {
					state.confirmAction();
				}
				closeConfirm();
			});
		}
	}

	function promptToggleStatus(code, currentIsActive) {
		var item = state.items.find(function (g) { return g.code === code; });
		if (!item) return;

		// If activating, do it directly
		if (!currentIsActive) {
			executeToggleStatus(code);
			return;
		}

		// If deactivating, show confirmation
		var modal = document.getElementById('portal-shipping-confirm-modal');
		var titleEl = document.getElementById('portal-shipping-confirm-title');
		var textEl = document.getElementById('portal-shipping-confirm-text');
		var actionBtn = document.getElementById('portal-shipping-confirm-action');

		if (!modal) {
			if (confirm('هل أنت متأكد من رغبتك في تعطيل الشحن لمحافظة ' + item.name + '؟')) {
				executeToggleStatus(code);
			}
			return;
		}

		if (titleEl) titleEl.textContent = 'تأكيد تعطيل الشحن';
		if (textEl) {
			textEl.innerHTML = 'هل أنت متأكد من رغبتك في تعطيل الشحن لمحافظة <strong class="text-slate-900">' +
				escapeHtml(item.name) + '</strong>؟<br>لن يتمكن العملاء من طلب هذه المحافظة حتى يتم إعادة تفعيلها.';
		}
		if (actionBtn) {
			actionBtn.textContent = 'تعطيل الشحن';
			actionBtn.className = 'px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-amber-600 hover:bg-amber-700 transition-colors cursor-pointer shadow-xs';
		}

		state.confirmAction = function () {
			executeToggleStatus(code);
		};

		modal.classList.remove('hidden');
	}

	function executeToggleStatus(code) {
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_toggle_governorate_status');
		formData.append('nonce', getNonce());
		formData.append('code', code);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data.success) {
					showToast(data.data && data.data.message ? data.data.message : 'تم تعديل حالة الشحن.');
					fetchShippingData();
				} else {
					showToast(data.data && data.data.message ? data.data.message : 'تعذر تغيير الحالة.', 'error');
				}
			})
			.catch(function () {
				showToast('حدث خطأ أثناء تعديل حالة المحافظة.', 'error');
			});
	}

	function promptDelete(code) {
		var item = state.items.find(function (g) { return g.code === code; });
		if (!item) return;

		var modal = document.getElementById('portal-shipping-confirm-modal');
		var titleEl = document.getElementById('portal-shipping-confirm-title');
		var textEl = document.getElementById('portal-shipping-confirm-text');
		var actionBtn = document.getElementById('portal-shipping-confirm-action');

		if (!modal) {
			if (confirm('هل أنت متأكد من رغبتك في حذف محافظة ' + item.name + '؟')) {
				executeDelete(code);
			}
			return;
		}

		if (titleEl) titleEl.textContent = 'تأكيد حذف المحافظة';
		if (textEl) {
			textEl.innerHTML = 'هل تريد حذف المحافظة المخصصة <strong class="text-slate-900">' +
				escapeHtml(item.name) + '</strong>؟<br>هذا الإجراء نهائي ولا يمكن التراجع عنه.';
		}
		if (actionBtn) {
			actionBtn.textContent = 'حذف المحافظة';
			actionBtn.className = 'px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors cursor-pointer shadow-xs';
		}

		state.confirmAction = function () {
			executeDelete(code);
		};

		modal.classList.remove('hidden');
	}

	function executeDelete(code) {
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_delete_governorate');
		formData.append('nonce', getNonce());
		formData.append('code', code);

		fetch(getAjaxUrl(), {
			method: 'POST',
			body: formData
		})
			.then(function (res) { return res.json(); })
			.then(function (data) {
				if (data.success) {
					showToast(data.data && data.data.message ? data.data.message : 'تم حذف المحافظة بنجاح.');
					fetchShippingData();
				} else {
					showToast(data.data && data.data.message ? data.data.message : 'تعذر حذف المحافظة.', 'error');
				}
			})
			.catch(function () {
				showToast('حدث خطأ في الاتصال بالخادم.', 'error');
			});
	}

	/**
	 * Document click listener for menus
	 */
	function initGlobalMenuCloser() {
		document.addEventListener('click', function (e) {
			if (state.activeActionMenu && !state.activeActionMenu.contains(e.target)) {
				state.activeActionMenu.classList.add('hidden');
				state.activeActionMenu = null;
			}
		});
	}

	/**
	 * HTML sanitizer helper
	 */
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
