/**
 * Ashrafco Admin Portal - Orders Management Controller
 *
 * Handles live status updating, bulk selections, order details modal,
 * CSV export, and filtering interactions for Tab 2.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function() {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
		initOrderStatusUpdates();
		initBulkSelection();
		initOrderDetailsModal();
		initTableControls();
		initOverflowMenus();
	});

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-orders-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-orders-toast';
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
	 * Single Order Status Update Handler (AJAX)
	 */
	function initOrderStatusUpdates() {
		document.addEventListener('submit', function(e) {
			var form = e.target.closest('.portal-order-status-form');
			if (!form) return;

			e.preventDefault();

			var orderIdInput = form.querySelector('input[name="order_id"]');
			var statusSelect = form.querySelector('select[name="new_status"]');
			var submitBtn = form.querySelector('button[type="submit"]');

			if (!orderIdInput || !statusSelect || !submitBtn) return;

			var orderId = orderIdInput.value;
			var newStatus = statusSelect.value;
			var originalBtnHtml = submitBtn.innerHTML;

			submitBtn.disabled = true;
			submitBtn.innerHTML = '<svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>';

			var formData = new FormData();
			formData.append('action', 'ashrafco_portal_update_order_status');
			formData.append('nonce', window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.nonce : '');
			formData.append('order_id', orderId);
			formData.append('new_status', newStatus);

			fetch(window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.ajaxUrl : '/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: formData
			})
			.then(function(res) { return res.json(); })
			.then(function(res) {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalBtnHtml;

				if (res.success && res.data) {
					showToast(res.data.message, 'success');

					// Update status badge on row
					var row = document.getElementById('portal-order-row-' + orderId);
					if (row) {
						var badgeEl = row.querySelector('.portal-order-badge');
						if (badgeEl) {
							if (res.data.badge_html) {
								badgeEl.outerHTML = res.data.badge_html;
							} else {
								var dotClass = res.data.badge_dot || 'bg-slate-400';
								badgeEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ' + dotClass + '"></span><span>' + res.data.badge_label + '</span>';
								badgeEl.className = 'portal-order-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ' + res.data.badge_class;
							}
						}
						var rowSelect = row.querySelector('select[name="new_status"]');
						if (rowSelect) rowSelect.value = res.data.status;
					}

					// Update Summary KPI counts if returned
					if (res.data.summary) {
						updateKpiCounts(res.data.summary);
					}
				} else {
					showToast(res.data && res.data.message ? res.data.message : 'فشل تحديث حالة الطلب.', 'error');
				}
			})
			.catch(function(err) {
				console.error(err);
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalBtnHtml;
				showToast('حدث خطأ بالاتصال أثناء تحديث الحالة.', 'error');
			});
		});
	}

	/**
	 * Update Summary KPI Counts across the page
	 */
	function updateKpiCounts(summary) {
		var allEl  = document.getElementById('kpi-orders-all');
		var procEl = document.getElementById('kpi-orders-processing');
		var shipEl = document.getElementById('kpi-orders-shipped');
		var compEl = document.getElementById('kpi-orders-completed');
		var cancEl = document.getElementById('kpi-orders-cancelled');

		if (allEl && summary.all !== undefined) allEl.textContent = summary.all;
		if (procEl && summary.processing !== undefined) procEl.textContent = summary.processing;
		if (shipEl && summary.shipped !== undefined) shipEl.textContent = summary.shipped;
		if (compEl && summary.completed !== undefined) compEl.textContent = summary.completed;
		if (cancEl && summary.cancelled !== undefined) cancEl.textContent = summary.cancelled;
	}

	/**
	 * Bulk Row Selection & Actions
	 */
	/**
	 * Bulk Row Selection & Actions
	 */
	function initBulkSelection() {
		var selectAll = document.getElementById('orders-select-all');
		var checkboxes = document.querySelectorAll('.order-row-checkbox');
		var bulkBar = document.getElementById('orders-bulk-bar');
		var selectedCountEl = document.getElementById('orders-selected-count');
		var bulkApplyBtn = document.getElementById('bulk-apply-status-btn');
		var bulkStatusSelect = document.getElementById('bulk-status-select');
		var bulkCancelBtn = document.getElementById('bulk-cancel-selection-btn');

		function updateSelectionState() {
			var selectedBoxes = document.querySelectorAll('.order-row-checkbox:checked');
			var count = selectedBoxes.length;

			if (selectedCountEl) {
				selectedCountEl.textContent = count;
			}

			if (count > 0) {
				if (bulkBar) bulkBar.classList.remove('hidden');
			} else {
				if (bulkBar) bulkBar.classList.add('hidden');
			}

			var allBoxes = document.querySelectorAll('.order-row-checkbox');
			if (selectAll) {
				selectAll.checked = (allBoxes.length > 0 && count === allBoxes.length);
			}
		}

		if (selectAll) {
			selectAll.addEventListener('change', function() {
				var isChecked = this.checked;
				var currentCheckboxes = document.querySelectorAll('.order-row-checkbox');
				currentCheckboxes.forEach(function(cb) {
					cb.checked = isChecked;
				});
				updateSelectionState();
			});
		}

		// Use delegation for checkbox changes to ensure dynamic rows are always handled
		document.addEventListener('change', function(e) {
			if (e.target && e.target.classList && e.target.classList.contains('order-row-checkbox')) {
				updateSelectionState();
			}
		});

		if (bulkCancelBtn) {
			bulkCancelBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var currentCheckboxes = document.querySelectorAll('.order-row-checkbox');
				currentCheckboxes.forEach(function(cb) { cb.checked = false; });
				if (selectAll) selectAll.checked = false;
				if (bulkStatusSelect) bulkStatusSelect.value = '';
				updateSelectionState();
			});
		}

		function executeBulkStatusUpdate(newStatus) {
			var selectedBoxes = document.querySelectorAll('.order-row-checkbox:checked');
			if (selectedBoxes.length === 0) {
				showToast('يرجى تحديد طلب واحد على الأقل أولاً.', 'error');
				return;
			}

			if (!newStatus) {
				showToast('يرجى اختيار حالة جديدة للطلبات المحددة.', 'error');
				return;
			}

			var orderIds = [];
			selectedBoxes.forEach(function(cb) { orderIds.push(cb.value); });

			var origHtml = bulkApplyBtn ? bulkApplyBtn.innerHTML : 'تطبيق';
			if (bulkApplyBtn) {
				bulkApplyBtn.disabled = true;
				bulkApplyBtn.innerHTML = '<span class="inline-flex items-center gap-1.5"><svg class="animate-spin w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>جاري التحديث...</span></span>';
			}
			if (bulkStatusSelect) {
				bulkStatusSelect.disabled = true;
			}

			var formData = new FormData();
			formData.append('action', 'ashrafco_portal_bulk_update_orders');
			formData.append('nonce', window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.nonce : '');
			formData.append('new_status', newStatus);
			orderIds.forEach(function(id) { formData.append('order_ids[]', id); });

			var ajaxUrl = window.AshrafcoOrdersConfig && window.AshrafcoOrdersConfig.ajaxUrl
				? window.AshrafcoOrdersConfig.ajaxUrl
				: '/wp-admin/admin-ajax.php';

			fetch(ajaxUrl, {
				method: 'POST',
				body: formData
			})
			.then(function(res) { return res.json(); })
			.then(function(res) {
				if (bulkApplyBtn) {
					bulkApplyBtn.disabled = false;
					bulkApplyBtn.innerHTML = origHtml;
				}
				if (bulkStatusSelect) {
					bulkStatusSelect.disabled = false;
					bulkStatusSelect.value = '';
				}

				if (res.success && res.data) {
					showToast(res.data.message || 'تم تحديث حالة الطلبات المحددة بنجاح.', 'success');

					// Instantly reflect updated status on each selected row in DOM
					orderIds.forEach(function(orderId) {
						var row = document.getElementById('portal-order-row-' + orderId);
						if (row) {
							var badgeEl = row.querySelector('.portal-order-badge');
							if (badgeEl) {
								if (res.data.badge_html) {
									badgeEl.outerHTML = res.data.badge_html;
								} else {
									var dotClass = res.data.badge_dot || 'bg-slate-400';
									badgeEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ' + dotClass + '"></span><span>' + res.data.badge_label + '</span>';
									badgeEl.className = 'portal-order-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ' + res.data.badge_class;
								}
							}
							var rowSelect = row.querySelector('select[name="new_status"]');
							if (rowSelect) rowSelect.value = res.data.status;
						}
					});

					// Update Summary KPI counts if returned
					if (res.data.summary) {
						updateKpiCounts(res.data.summary);
					}

					// Deselect all checkboxes & hide bulk bar
					var currentCheckboxes = document.querySelectorAll('.order-row-checkbox');
					currentCheckboxes.forEach(function(cb) { cb.checked = false; });
					if (selectAll) selectAll.checked = false;
					updateSelectionState();

					// Reload cleanly after short delay to refresh full pagination/state
					setTimeout(function() {
						window.location.reload();
					}, 700);
				} else {
					showToast(res.data && res.data.message ? res.data.message : 'فشل التحديث الجماعي.', 'error');
				}
			})
			.catch(function(err) {
				console.error('Bulk update error:', err);
				if (bulkApplyBtn) {
					bulkApplyBtn.disabled = false;
					bulkApplyBtn.innerHTML = origHtml;
				}
				if (bulkStatusSelect) {
					bulkStatusSelect.disabled = false;
				}
				showToast('حدث خطأ أثناء تنفيذ التحديث الجماعي.', 'error');
			});
		}

		// Trigger via Apply button click
		if (bulkApplyBtn) {
			bulkApplyBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var statusVal = bulkStatusSelect ? bulkStatusSelect.value : '';
				executeBulkStatusUpdate(statusVal);
			});
		}

		// Also trigger immediately when selecting an option from the dropdown
		if (bulkStatusSelect) {
			bulkStatusSelect.addEventListener('change', function() {
				if (this.value) {
					executeBulkStatusUpdate(this.value);
				}
			});
		}
	}

	/**
	 * Order Details Modal
	 */
	function initOrderDetailsModal() {
		var modal = document.getElementById('portal-order-details-modal');
		var backdrop = document.getElementById('portal-modal-backdrop');
		var closeBtns = document.querySelectorAll('.close-order-modal-btn');
		var modalContent = document.getElementById('portal-modal-content-area');
		var modalLoader = document.getElementById('portal-modal-loader');

		function openModal(orderId) {
			if (!modal) return;
			modal.classList.remove('hidden');
			document.body.classList.add('overflow-hidden');

			if (modalContent) modalContent.classList.add('hidden');
			if (modalLoader) modalLoader.classList.remove('hidden');

			var nonce = window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.nonce : '';
			var url = (window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.ajaxUrl : '/wp-admin/admin-ajax.php') +
				'?action=ashrafco_portal_order_details&order_id=' + orderId + '&nonce=' + encodeURIComponent(nonce);

			fetch(url)
			.then(function(res) { return res.json(); })
			.then(function(res) {
				if (modalLoader) modalLoader.classList.add('hidden');

				if (res.success && res.data) {
					renderModalData(res.data);
					if (modalContent) modalContent.classList.remove('hidden');
				} else {
					if (modalContent) {
						modalContent.innerHTML = '<div class="p-8 text-center text-rose-600 font-bold">' + (res.data && res.data.message ? res.data.message : 'تعذر تحميل تفاصيل الطلب.') + '</div>';
						modalContent.classList.remove('hidden');
					}
				}
			})
			.catch(function(err) {
				console.error(err);
				if (modalLoader) modalLoader.classList.add('hidden');
				if (modalContent) {
					modalContent.innerHTML = '<div class="p-8 text-center text-rose-600 font-bold">حدث خطأ أثناء جلب تفاصيل الطلب.</div>';
					modalContent.classList.remove('hidden');
				}
			});
		}

		function closeModal() {
			if (!modal) return;
			modal.classList.add('hidden');
			document.body.classList.remove('overflow-hidden');
		}

		// Trigger buttons
		document.addEventListener('click', function(e) {
			var trigger = e.target.closest('.view-order-details-btn');
			if (trigger) {
				e.preventDefault();
				var id = trigger.getAttribute('data-order-id');
				if (id) openModal(id);
			}
		});

		closeBtns.forEach(function(btn) {
			btn.addEventListener('click', closeModal);
		});

		if (backdrop) {
			backdrop.addEventListener('click', closeModal);
		}

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
				closeModal();
			}
		});
	}

	/**
	 * Populate Order Details into the Modal
	 */
	function renderModalData(data) {
		// Header info
		var idEl = document.getElementById('modal-order-id');
		var dateEl = document.getElementById('modal-order-date');
		var badgeEl = document.getElementById('modal-order-badge');
		var adminLinkEl = document.getElementById('modal-admin-link');

		if (idEl) idEl.textContent = '#' + data.id;
		if (dateEl) dateEl.textContent = data.date;
		if (badgeEl) {
			if (data.badge_dot) {
				badgeEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 ' + data.badge_dot + '"></span><span>' + data.badge_label + '</span>';
			} else {
				badgeEl.textContent = data.badge_label;
			}
			badgeEl.className = 'inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border ' + data.badge_class;
		}
		if (adminLinkEl) adminLinkEl.href = data.admin_edit_url;

		// Customer info
		var nameEl = document.getElementById('modal-cust-name');
		var phoneEl = document.getElementById('modal-cust-phone');
		var emailEl = document.getElementById('modal-cust-email');
		var locEl = document.getElementById('modal-cust-location');
		var waBtn = document.getElementById('modal-cust-whatsapp');

		if (nameEl) nameEl.textContent = data.customer_name;
		if (phoneEl) {
			phoneEl.textContent = data.customer_phone;
			phoneEl.href = 'tel:' + data.customer_phone;
		}
		if (emailEl) {
			emailEl.textContent = data.customer_email;
			emailEl.href = 'mailto:' + data.customer_email;
		}
		if (locEl) locEl.textContent = data.state_name + (data.city && data.city !== '-' ? ' - ' + data.city : '') + (data.address_1 && data.address_1 !== '-' ? ' (' + data.address_1 + ')' : '');

		if (waBtn) {
			if (data.whatsapp_url) {
				waBtn.href = data.whatsapp_url;
				waBtn.classList.remove('hidden');
			} else {
				waBtn.classList.add('hidden');
			}
		}

		// Items table
		var itemsTbody = document.getElementById('modal-items-tbody');
		if (itemsTbody) {
			var html = '';
			data.items.forEach(function(item) {
				html += '<tr class="border-b border-slate-100 last:border-0">' +
					'<td class="py-3 px-3">' +
						'<div class="flex items-center gap-3">' +
							'<img src="' + item.thumb + '" class="w-11 h-11 rounded-lg object-cover bg-slate-50 border border-slate-100">' +
							'<div>' +
								'<div class="font-bold text-slate-900 text-xs">' + item.name + '</div>' +
								'<div class="text-[10px] text-slate-400 font-mono mt-0.5">SKU: ' + item.sku + '</div>' +
							'</div>' +
						'</div>' +
					'</td>' +
					'<td class="py-3 px-3 text-center font-mono font-bold text-slate-700">' + item.unit_price + '</td>' +
					'<td class="py-3 px-3 text-center font-mono font-bold text-slate-900">' + item.quantity + '</td>' +
					'<td class="py-3 px-3 text-left font-mono font-black text-slate-900">' + item.total + '</td>' +
				'</tr>';
			});
			itemsTbody.innerHTML = html;
		}

		// Financial breakdown
		var subEl = document.getElementById('modal-subtotal');
		var shipEl = document.getElementById('modal-shipping');
		var discEl = document.getElementById('modal-discount');
		var grandEl = document.getElementById('modal-grand-total');
		var payEl = document.getElementById('modal-payment-title');

		if (subEl) subEl.innerHTML = data.subtotal;
		if (shipEl) shipEl.innerHTML = data.shipping_total;
		if (discEl) discEl.innerHTML = data.discount_total;
		if (grandEl) grandEl.innerHTML = data.grand_total;
		if (payEl) payEl.innerHTML = data.payment_title;

		// Customer note
		var noteContainer = document.getElementById('modal-customer-note-container');
		var noteEl = document.getElementById('modal-customer-note');
		if (noteContainer && noteEl) {
			if (data.customer_note) {
				noteEl.textContent = data.customer_note;
				noteContainer.classList.remove('hidden');
			} else {
				noteContainer.classList.add('hidden');
			}
		}

		// Modal Quick Status Form
		var modalStatusForm = document.getElementById('modal-status-update-form');
		var modalOrderIdInput = document.getElementById('modal-order-id-input');
		var modalStatusSelect = document.getElementById('modal-status-select');
		if (modalOrderIdInput) modalOrderIdInput.value = data.id;
		if (modalStatusSelect) modalStatusSelect.value = data.status;
	}

	/**
	 * Table Controls (Page Size selector, CSV Export trigger, Reset button)
	 */
	function initTableControls() {
		// Per page selector
		var perPageSelect = document.getElementById('orders-per-page-select');
		if (perPageSelect) {
			perPageSelect.addEventListener('change', function() {
				var url = new URL(window.location.href);
				url.searchParams.set('per_page', this.value);
				url.searchParams.set('paged', '1');
				window.location.href = url.toString();
			});
		}

		// Refresh button
		var refreshBtn = document.getElementById('orders-refresh-btn');
		if (refreshBtn) {
			refreshBtn.addEventListener('click', function(e) {
				e.preventDefault();
				window.location.reload();
			});
		}

		// Reset filters button
		var resetBtn = document.getElementById('orders-reset-filters-btn');
		if (resetBtn) {
			resetBtn.addEventListener('click', function(e) {
				e.preventDefault();
				window.location.href = window.location.pathname + '?tab=orders';
			});
		}

		// CSV Export button
		var exportBtn = document.getElementById('orders-export-csv-btn');
		if (exportBtn) {
			exportBtn.addEventListener('click', function(e) {
				e.preventDefault();
				var exportUrl = new URL(window.location.href);
				exportUrl.searchParams.set('action', 'ashrafco_export_orders_csv');
				exportUrl.searchParams.set('_wpnonce', window.AshrafcoOrdersConfig ? window.AshrafcoOrdersConfig.exportNonce : '');
				window.location.href = exportUrl.toString();
			});
		}
	}

	/**
	 * Compact Overflow Menu (Three Dots)
	 */
	function initOverflowMenus() {
		document.addEventListener('click', function(e) {
			var trigger = e.target.closest('.portal-overflow-menu-btn');
			var allMenus = document.querySelectorAll('.portal-overflow-dropdown');

			if (trigger) {
				e.preventDefault();
				e.stopPropagation();
				var targetMenuId = trigger.getAttribute('data-target-menu');
				var menu = document.getElementById(targetMenuId);

				allMenus.forEach(function(m) {
					if (m !== menu) m.classList.add('hidden');
				});

				if (menu) {
					var isHidden = menu.classList.contains('hidden');
					if (isHidden) {
						menu.classList.remove('hidden');
						var tableWrapper = menu.closest('.overflow-x-auto') || document.body;
						var menuRect = menu.getBoundingClientRect();
						var wrapperRect = tableWrapper.getBoundingClientRect();
						if (menuRect.bottom > wrapperRect.bottom + 10 || menuRect.bottom > window.innerHeight) {
							menu.classList.remove('top-full', 'mt-1.5');
							menu.classList.add('bottom-full', 'mb-1.5');
						} else {
							menu.classList.remove('bottom-full', 'mb-1.5');
							menu.classList.add('top-full', 'mt-1.5');
						}
					} else {
						menu.classList.add('hidden');
					}
				}
			} else {
				// Clicked outside
				allMenus.forEach(function(m) {
					m.classList.add('hidden');
				});
			}
		});

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				document.querySelectorAll('.portal-overflow-dropdown').forEach(function(m) {
					m.classList.add('hidden');
				});
			}
		});
	}

})();
