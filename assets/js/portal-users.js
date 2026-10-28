/**
 * Ashrafco Admin Portal - System Users Management Controller
 *
 * Handles live search, role/status filtering, pagination, summary metrics updating,
 * Add User, Edit User, View Details, Toggle Status, Password Reset, and Safe Delete.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

(function() {
	'use strict';

	var state = {
		search: '',
		role: '',
		status: '',
		page: 1,
		perPage: 10,
		total: 0,
		totalPages: 1,
		loading: false
	};

	var activeMenu = null;

	document.addEventListener('DOMContentLoaded', function() {
		initFilters();
		initPagination();
		initAddUserModal();
		initEditUserModal();
		initQuickRoleModal();
		initPasswordResetModal();
		initDeleteUserModal();
		initGlobalMenuCloser();
		initPageSizeSelector();

		// If initial active tab is users, fetch users or initialize from DOM
		var usersTab = document.getElementById('portal-tab-users');
		if (usersTab && usersTab.classList.contains('active')) {
			fetchUsers();
		}
	});

	/**
	 * Toast notification helper
	 */
	function showToast(message, type) {
		type = type || 'success';
		var existing = document.getElementById('portal-users-toast');
		if (existing) existing.remove();

		var toast = document.createElement('div');
		toast.id = 'portal-users-toast';
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
		var searchInput = document.getElementById('portal-users-search');
		var roleFilter   = document.getElementById('portal-users-filter-role');
		var statusFilter = document.getElementById('portal-users-filter-status');

		var debounceTimer = null;
		if (searchInput) {
			searchInput.addEventListener('input', function() {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(function() {
					state.search = searchInput.value.trim();
					state.page = 1;
					fetchUsers();
				}, 350);
			});
		}

		if (roleFilter) {
			roleFilter.addEventListener('change', function() {
				state.role = roleFilter.value;
				state.page = 1;
				fetchUsers();
			});
		}

		if (statusFilter) {
			statusFilter.addEventListener('change', function() {
				state.status = statusFilter.value;
				state.page = 1;
				fetchUsers();
			});
		}
	}

	/**
	 * Setup page size dropdown listener
	 */
	function initPageSizeSelector() {
		var perPageSelect = document.getElementById('portal-users-per-page');
		if (perPageSelect) {
			perPageSelect.addEventListener('change', function() {
				state.perPage = parseInt(perPageSelect.value, 10) || 10;
				state.page = 1;
				fetchUsers();
			});
		}
	}

	/**
	 * Setup pagination controls
	 */
	function initPagination() {
		var prevBtn = document.getElementById('portal-users-prev-page');
		var nextBtn = document.getElementById('portal-users-next-page');

		if (prevBtn) {
			prevBtn.addEventListener('click', function() {
				if (state.page > 1) {
					state.page--;
					fetchUsers();
				}
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function() {
				if (state.page < state.totalPages) {
					state.page++;
					fetchUsers();
				}
			});
		}
	}

	/**
	 * Fetch users via AJAX with current state parameters
	 */
	function fetchUsers() {
		if (state.loading) return;
		state.loading = true;

		var tableBody = document.getElementById('portal-users-table-body');
		var loadingOverlay = document.getElementById('portal-users-loading');
		if (loadingOverlay) loadingOverlay.classList.remove('hidden');

		var config = window.AshrafcoUsersConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_get_users');
		formData.append('nonce', config.nonce || '');
		formData.append('search', state.search);
		formData.append('role', state.role);
		formData.append('status', state.status);
		formData.append('page', state.page);
		formData.append('per_page', state.perPage);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(res) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');

			if (res.success && res.data) {
				state.total = res.data.total;
				state.totalPages = res.data.total_pages;
				state.page = res.data.current_page;

				renderUsersTable(res.data.users);
				updatePaginationControls();
				if (res.data.summary) {
					updateSummaryCards(res.data.summary);
				}
				updateResultCountBadge(res.data.total);
			} else {
				showToast(res.data && res.data.message ? res.data.message : 'فشل تحميل بيانات المستخدمين', 'error');
			}
		})
		.catch(function(err) {
			state.loading = false;
			if (loadingOverlay) loadingOverlay.classList.add('hidden');
			showToast('تعذر الاتصال بالخادم', 'error');
		});
	}

	/**
	 * Render HTML table rows
	 */
	function renderUsersTable(users) {
		var tbody = document.getElementById('portal-users-table-body');
		var emptyState = document.getElementById('portal-users-empty-state');
		if (!tbody) return;

		tbody.innerHTML = '';

		if (!users || users.length === 0) {
			if (emptyState) emptyState.classList.remove('hidden');
			return;
		}

		if (emptyState) emptyState.classList.add('hidden');

		users.forEach(function(u) {
			var tr = document.createElement('tr');
			tr.id = 'portal-user-row-' + u.id;
			tr.className = 'border-b border-slate-100 hover:bg-slate-50/60 transition-colors group';

			// User cell (Avatar, Username, Role Subtitle)
			var userCell = document.createElement('td');
			userCell.className = 'py-4 px-4 sm:px-6';
			userCell.innerHTML =
				'<div class="flex items-center gap-3">' +
					'<div class="w-10 h-10 rounded-xl ' + escapeHtml(u.avatar_color_class) + ' text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs select-none">' +
						escapeHtml(u.avatar_initial) +
					'</div>' +
					'<div class="text-right truncate">' +
						'<div class="text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors flex items-center gap-1.5">' +
							escapeHtml(u.display_name || u.user_login) +
							(u.is_current_user ? '<span class="text-[10px] px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded-md font-bold">حسابك</span>' : '') +
						'</div>' +
						'<div class="text-xs text-slate-400 font-medium mt-0.5">' +
							escapeHtml(u.role_label) +
						'</div>' +
					'</div>' +
				'</div>';

			// Email cell
			var emailCell = document.createElement('td');
			emailCell.className = 'py-4 px-4 text-xs font-semibold text-slate-600 text-right font-mono truncate max-w-[200px]';
			emailCell.textContent = u.user_email || '—';

			// Role cell
			var roleCell = document.createElement('td');
			roleCell.className = 'py-4 px-4 text-right';
			roleCell.innerHTML = renderRoleBadge(u.primary_role, u.role_label);

			// Status cell
			var statusCell = document.createElement('td');
			statusCell.className = 'py-4 px-4 text-right';
			statusCell.innerHTML = renderStatusBadge(u.status, u.status_label);

			// Last activity cell
			var activityCell = document.createElement('td');
			activityCell.className = 'py-4 px-4 text-right';
			activityCell.innerHTML = renderActivityIndicator(u.last_activity_formatted);

			// Registered date cell
			var dateCell = document.createElement('td');
			dateCell.className = 'py-4 px-4 text-xs font-semibold text-slate-500 text-right whitespace-nowrap';
			dateCell.textContent = u.registered_formatted || '—';

			// Actions cell (3-dots button and menu)
			var actionsCell = document.createElement('td');
			actionsCell.className = 'py-4 px-4 sm:px-6 text-left whitespace-nowrap relative';
			actionsCell.innerHTML = renderActionsMenu(u);

			tr.appendChild(userCell);
			tr.appendChild(emailCell);
			tr.appendChild(roleCell);
			tr.appendChild(statusCell);
			tr.appendChild(activityCell);
			tr.appendChild(dateCell);
			tr.appendChild(actionsCell);

			tbody.appendChild(tr);
		});

		// Attach listeners to newly created action buttons
		attachRowActionListeners();
	}

	/**
	 * Role badge HTML helper matching screenshot
	 */
	function renderRoleBadge(roleKey, roleLabel) {
		var styles = {
			'shop_manager': 'bg-rose-50 text-rose-700 border-rose-200/70',
			'customer_support': 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
			'product_editor': 'bg-purple-50 text-purple-700 border-purple-200/70',
			'editor': 'bg-purple-50 text-purple-700 border-purple-200/70',
			'administrator': 'bg-sky-50 text-sky-700 border-sky-200/70',
			'customer': 'bg-slate-100 text-slate-700 border-slate-200',
			'author': 'bg-amber-50 text-amber-700 border-amber-200/70',
			'contributor': 'bg-teal-50 text-teal-700 border-teal-200/70',
			'subscriber': 'bg-slate-50 text-slate-600 border-slate-200/60'
		};
		var style = styles[roleKey] || 'bg-slate-100 text-slate-700 border-slate-200';
		return '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold border ' + style + ' shadow-2xs whitespace-nowrap">' + escapeHtml(roleLabel) + '</span>';
	}

	/**
	 * Status badge HTML helper matching screenshot
	 */
	function renderStatusBadge(statusKey, statusLabel) {
		var config = {
			'active': { badge: 'bg-emerald-50 text-emerald-700 border-emerald-200/70', dot: 'bg-emerald-500' },
			'pending': { badge: 'bg-amber-50 text-amber-800 border-amber-200/70', dot: 'bg-amber-500' },
			'suspended': { badge: 'bg-rose-50 text-rose-700 border-rose-200/70', dot: 'bg-rose-500' }
		};
		var c = config[statusKey] || config['active'];
		return '<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border ' + c.badge + ' shadow-2xs whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full ' + c.dot + ' flex-shrink-0"></span>' + escapeHtml(statusLabel) + '</span>';
	}

	/**
	 * Activity indicator HTML helper matching screenshot
	 */
	function renderActivityIndicator(formatted) {
		var isEmpty = !formatted || formatted.trim() === '—';
		var dotClass = isEmpty ? 'bg-slate-300' : 'bg-emerald-500';
		var textClass = isEmpty ? 'text-slate-400' : 'text-slate-700';
		return '<span class="inline-flex items-center gap-1.5 text-xs font-semibold ' + textClass + '"><span class="w-1.5 h-1.5 rounded-full ' + dotClass + ' flex-shrink-0"></span>' + escapeHtml(formatted || '—') + '</span>';
	}

	/**
	 * Actions dropdown menu HTML helper
	 */
	function renderActionsMenu(u) {
		var jsonUserData = escapeHtml(JSON.stringify(u));
		var toggleStatusLabel = u.status === 'suspended' ? 'تفعيل الحساب' : 'إيقاف الحساب';
		var toggleStatusNext = u.status === 'suspended' ? 'active' : 'suspended';
		var toggleStatusIcon = u.status === 'suspended'
			? '<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
			: '<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>';

		var html =
			'<div class="relative inline-block text-right">' +
				'<button type="button" class="portal-user-menu-btn w-8 h-8 rounded-lg border border-slate-200/80 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors shadow-2xs" data-user-id="' + u.id + '" title="خيارات">' +
					'<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>' +
				'</button>' +
				'<div id="user-menu-dropdown-' + u.id + '" class="portal-user-dropdown hidden absolute left-0 mt-1 w-52 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold text-slate-700 divide-y divide-slate-100" data-user=\'' + jsonUserData + '\'>' +
					'<div class="py-1">' +
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-view-details">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>' +
							'<span>عرض تفاصيل المستخدم</span>' +
						'</button>' +
						(u.can_edit ? (
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-edit-user">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>' +
							'<span>تعديل المستخدم</span>' +
						'</button>' +
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-change-role">' +
							'<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>' +
							'<span>تغيير الدور أو الصلاحيات</span>' +
						'</button>'
						) : '') +
					'</div>' +
					'<div class="py-1">' +
						(u.can_edit && !u.is_current_user ? (
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-toggle-status" data-next-status="' + toggleStatusNext + '">' +
							toggleStatusIcon +
							'<span>' + toggleStatusLabel + '</span>' +
						'</button>'
						) : '') +
						(u.can_edit ? (
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-reset-password">' +
							'<svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>' +
							'<span>إعادة تعيين كلمة المرور</span>' +
						'</button>'
						) : '') +
					'</div>' +
					(u.can_delete ? (
					'<div class="py-1">' +
						'<button type="button" class="w-full text-right px-3 py-2 hover:bg-rose-50 text-rose-600 flex items-center gap-2.5 action-delete-user">' +
							'<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>' +
							'<span>حذف المستخدم</span>' +
						'</button>' +
					'</div>'
					) : '') +
				'</div>' +
			'</div>';

		return html;
	}

	/**
	 * Attach event listeners to action buttons on rendered rows
	 */
	function attachRowActionListeners() {
		document.querySelectorAll('.portal-user-menu-btn').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				var userId = btn.getAttribute('data-user-id');
				var dropdown = document.getElementById('user-menu-dropdown-' + userId);
				if (!dropdown) return;

				if (activeMenu && activeMenu !== dropdown) {
					activeMenu.classList.add('hidden');
				}

				var isHidden = dropdown.classList.contains('hidden');
				if (isHidden) {
					dropdown.classList.remove('hidden');
					activeMenu = dropdown;
				} else {
					dropdown.classList.add('hidden');
					activeMenu = null;
				}
			});
		});

		// View Details
		document.querySelectorAll('.action-view-details').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				openViewDetailsModal(user);
			});
		});

		// Edit User
		document.querySelectorAll('.action-edit-user').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				openEditUserModal(user);
			});
		});

		// Change Role
		document.querySelectorAll('.action-change-role').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				openQuickRoleModal(user);
			});
		});

		// Toggle Status
		document.querySelectorAll('.action-toggle-status').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				var nextStatus = btn.getAttribute('data-next-status');
				handleToggleStatus(user.id, nextStatus);
			});
		});

		// Reset Password
		document.querySelectorAll('.action-reset-password').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				openPasswordResetModal(user);
			});
		});

		// Delete User
		document.querySelectorAll('.action-delete-user').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				closeActiveMenu();
				var dropdown = btn.closest('.portal-user-dropdown');
				if (!dropdown) return;
				var user = JSON.parse(dropdown.getAttribute('data-user'));
				openDeleteUserModal(user);
			});
		});
	}

	function closeActiveMenu() {
		if (activeMenu) {
			activeMenu.classList.add('hidden');
			activeMenu = null;
		}
	}

	function initGlobalMenuCloser() {
		document.addEventListener('click', function() {
			closeActiveMenu();
		});
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				closeActiveMenu();
				closeAllModals();
			}
		});
	}

	/**
	 * Update Pagination Controls UI
	 */
	function updatePaginationControls() {
		var prevBtn = document.getElementById('portal-users-prev-page');
		var nextBtn = document.getElementById('portal-users-next-page');
		var pagesContainer = document.getElementById('portal-users-pages-container');

		if (prevBtn) {
			prevBtn.disabled = state.page <= 1;
			prevBtn.className = 'w-8 h-8 rounded-lg border text-xs font-bold flex items-center justify-center transition-colors ' +
				(state.page <= 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900');
		}

		if (nextBtn) {
			nextBtn.disabled = state.page >= state.totalPages;
			nextBtn.className = 'w-8 h-8 rounded-lg border text-xs font-bold flex items-center justify-center transition-colors ' +
				(state.page >= state.totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900');
		}

		if (pagesContainer) {
			pagesContainer.innerHTML = '';
			for (var i = 1; i <= state.totalPages; i++) {
				var pBtn = document.createElement('button');
				pBtn.type = 'button';
				pBtn.className = 'w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-colors ' +
					(i === state.page ? 'bg-primary text-white shadow-xs' : 'border border-slate-200 text-slate-600 hover:bg-slate-100');
				pBtn.textContent = i;
				(function(pageIndex) {
					pBtn.addEventListener('click', function() {
						state.page = pageIndex;
						fetchUsers();
					});
				})(i);
				pagesContainer.appendChild(pBtn);
			}
		}
	}

	/**
	 * Update Summary KPI Counts
	 */
	function updateSummaryCards(summary) {
		var totalEl     = document.getElementById('portal-users-count-total');
		var activeEl    = document.getElementById('portal-users-count-active');
		var pendingEl   = document.getElementById('portal-users-count-pending');
		var suspendedEl = document.getElementById('portal-users-count-suspended');

		if (totalEl) totalEl.textContent = summary.total;
		if (activeEl) activeEl.textContent = summary.active;
		if (pendingEl) pendingEl.textContent = summary.pending;
		if (suspendedEl) suspendedEl.textContent = summary.suspended;
	}

	/**
	 * Update result count badge in toolbar
	 */
	function updateResultCountBadge(total) {
		var badge = document.getElementById('portal-users-count-badge');
		if (badge) {
			badge.textContent = total + ' مستخدماً';
		}
	}

	/**
	 * Add User Modal Handling
	 */
	function initAddUserModal() {
		var openBtn = document.getElementById('portal-add-user-btn');
		var modal   = document.getElementById('portal-add-user-modal');
		var form    = document.getElementById('portal-add-user-form');
		var cancelBtn = document.getElementById('portal-add-user-cancel');

		if (openBtn && modal) {
			openBtn.addEventListener('click', function() {
				if (form) form.reset();
				var errEl = document.getElementById('portal-add-user-error');
				if (errEl) errEl.classList.add('hidden');
				modal.classList.remove('hidden');
			});
		}

		if (cancelBtn && modal) {
			cancelBtn.addEventListener('click', function() {
				modal.classList.add('hidden');
			});
		}

		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var submitBtn = form.querySelector('button[type="submit"]');
				var errEl = document.getElementById('portal-add-user-error');
				if (errEl) errEl.classList.add('hidden');

				var username = form.querySelector('[name="username"]').value.trim();
				var email    = form.querySelector('[name="email"]').value.trim();
				var dispName = form.querySelector('[name="display_name"]').value.trim();
				var role     = form.querySelector('[name="role"]').value;
				var status   = form.querySelector('[name="status"]').value;
				var password = form.querySelector('[name="password"]').value;
				var notify   = form.querySelector('[name="send_notification"]').checked ? '1' : '0';

				if (!username || !email) {
					if (errEl) {
						errEl.textContent = 'يرجى ملء كافة الحقول الإلزامية (اسم المستخدم والبريد الإلكتروني).';
						errEl.classList.remove('hidden');
					}
					return;
				}

				var originalBtnHtml = submitBtn.innerHTML;
				submitBtn.disabled = true;
				submitBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full ml-2"></span> جاري الحفظ...';

				var config = window.AshrafcoUsersConfig || {};
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_create_user');
				formData.append('nonce', config.nonce || '');
				formData.append('username', username);
				formData.append('display_name', dispName || username);
				formData.append('email', email);
				formData.append('role', role);
				formData.append('status', status);
				formData.append('password', password);
				formData.append('send_notification', notify);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;

					if (res.success && res.data) {
						showToast(res.data.message || 'تم إنشاء المستخدم بنجاح', 'success');
						modal.classList.add('hidden');
						form.reset();
						if (res.data.summary) {
							updateSummaryCards(res.data.summary);
						}
						fetchUsers();
					} else {
						var msg = res.data && res.data.message ? res.data.message : 'حدث خطأ أثناء إنشاء المستخدم.';
						if (errEl) {
							errEl.textContent = msg;
							errEl.classList.remove('hidden');
						} else {
							showToast(msg, 'error');
						}
					}
				})
				.catch(function(err) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;
					showToast('تعذر الاتصال بالخادم.', 'error');
				});
			});
		}
	}

	/**
	 * Edit User Modal Handling
	 */
	function initEditUserModal() {
		var modal = document.getElementById('portal-edit-user-modal');
		var form  = document.getElementById('portal-edit-user-form');
		var cancelBtn = document.getElementById('portal-edit-user-cancel');

		if (cancelBtn && modal) {
			cancelBtn.addEventListener('click', function() {
				modal.classList.add('hidden');
			});
		}

		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var submitBtn = form.querySelector('button[type="submit"]');
				var errEl = document.getElementById('portal-edit-user-error');
				if (errEl) errEl.classList.add('hidden');

				var userId   = form.querySelector('[name="edit_user_id"]').value;
				var dispName = form.querySelector('[name="edit_display_name"]').value.trim();
				var email    = form.querySelector('[name="edit_email"]').value.trim();
				var role     = form.querySelector('[name="edit_role"]').value;
				var status   = form.querySelector('[name="edit_status"]').value;
				var password = form.querySelector('[name="edit_password"]').value;

				var originalBtnHtml = submitBtn.innerHTML;
				submitBtn.disabled = true;
				submitBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full ml-2"></span> جاري الحفظ...';

				var config = window.AshrafcoUsersConfig || {};
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_update_user');
				formData.append('nonce', config.nonce || '');
				formData.append('user_id', userId);
				formData.append('display_name', dispName);
				formData.append('email', email);
				formData.append('role', role);
				formData.append('status', status);
				formData.append('password', password);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;

					if (res.success && res.data) {
						showToast(res.data.message || 'تم تحديث بيانات المستخدم بنجاح', 'success');
						modal.classList.add('hidden');
						if (res.data.summary) {
							updateSummaryCards(res.data.summary);
						}
						fetchUsers();
					} else {
						var msg = res.data && res.data.message ? res.data.message : 'حدث خطأ أثناء تعديل بيانات المستخدم.';
						if (errEl) {
							errEl.textContent = msg;
							errEl.classList.remove('hidden');
						} else {
							showToast(msg, 'error');
						}
					}
				})
				.catch(function(err) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;
					showToast('تعذر الاتصال بالخادم.', 'error');
				});
			});
		}
	}

	function openEditUserModal(user) {
		var modal = document.getElementById('portal-edit-user-modal');
		var form  = document.getElementById('portal-edit-user-form');
		if (!modal || !form) return;

		var errEl = document.getElementById('portal-edit-user-error');
		if (errEl) errEl.classList.add('hidden');

		form.querySelector('[name="edit_user_id"]').value = user.id;
		form.querySelector('[name="edit_username"]').value = user.user_login;
		form.querySelector('[name="edit_display_name"]').value = user.display_name;
		form.querySelector('[name="edit_email"]').value = user.user_email;
		form.querySelector('[name="edit_password"]').value = '';

		var roleSelect = form.querySelector('[name="edit_role"]');
		if (roleSelect) roleSelect.value = user.primary_role;

		var statusSelect = form.querySelector('[name="edit_status"]');
		if (statusSelect) statusSelect.value = user.status;

		modal.classList.remove('hidden');
	}

	/**
	 * Quick Role Change Modal Handling
	 */
	function initQuickRoleModal() {
		var modal = document.getElementById('portal-role-modal');
		var form  = document.getElementById('portal-role-form');
		var cancelBtn = document.getElementById('portal-role-cancel');

		if (cancelBtn && modal) {
			cancelBtn.addEventListener('click', function() {
				modal.classList.add('hidden');
			});
		}

		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var submitBtn = form.querySelector('button[type="submit"]');
				var userId = form.querySelector('[name="role_modal_user_id"]').value;
				var role   = form.querySelector('[name="role_modal_role"]').value;

				var originalBtnHtml = submitBtn.innerHTML;
				submitBtn.disabled = true;
				submitBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full ml-2"></span> جاري الحفظ...';

				var config = window.AshrafcoUsersConfig || {};
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_update_user');
				formData.append('nonce', config.nonce || '');
				formData.append('user_id', userId);
				formData.append('role', role);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;

					if (res.success) {
						showToast(res.data.message || 'تم تحديث دور المستخدم بنجاح', 'success');
						modal.classList.add('hidden');
						fetchUsers();
					} else {
						showToast(res.data && res.data.message ? res.data.message : 'فشل تغيير الدور.', 'error');
					}
				})
				.catch(function(err) {
					submitBtn.disabled = false;
					submitBtn.innerHTML = originalBtnHtml;
					showToast('تعذر الاتصال بالخادم.', 'error');
				});
			});
		}
	}

	function openQuickRoleModal(user) {
		var modal = document.getElementById('portal-role-modal');
		var form  = document.getElementById('portal-role-form');
		if (!modal || !form) return;

		form.querySelector('[name="role_modal_user_id"]').value = user.id;
		var titleEl = document.getElementById('portal-role-modal-username');
		if (titleEl) titleEl.textContent = user.display_name || user.user_login;

		var roleSelect = form.querySelector('[name="role_modal_role"]');
		if (roleSelect) roleSelect.value = user.primary_role;

		modal.classList.remove('hidden');
	}

	/**
	 * View User Details Modal
	 */
	function openViewDetailsModal(user) {
		var modal = document.getElementById('portal-view-user-modal');
		if (!modal) return;

		var config = window.AshrafcoUsersConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_user_details');
		formData.append('nonce', config.nonce || '');
		formData.append('user_id', user.id);

		// Set initial pre-filled fields
		document.getElementById('view-modal-avatar').textContent = user.avatar_initial;
		document.getElementById('view-modal-avatar').className = 'w-14 h-14 rounded-2xl ' + escapeHtml(user.avatar_color_class) + ' text-white font-black text-xl flex items-center justify-center shadow-sm select-none';
		document.getElementById('view-modal-display-name').textContent = user.display_name || user.user_login;
		document.getElementById('view-modal-username').textContent = '@' + user.user_login;
		document.getElementById('view-modal-email').textContent = user.user_email;
		document.getElementById('view-modal-role').innerHTML = renderRoleBadge(user.primary_role, user.role_label);
		document.getElementById('view-modal-status').innerHTML = renderStatusBadge(user.status, user.status_label);
		document.getElementById('view-modal-registered').textContent = user.registered_formatted || '—';
		document.getElementById('view-modal-activity').textContent = user.last_activity_formatted || '—';

		modal.classList.remove('hidden');

		// Fetch rich data (orders, spent, phone, city)
		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(res) {
			if (res.success && res.data && res.data.user) {
				var u = res.data.user;
				document.getElementById('view-modal-phone').textContent = u.phone || '—';
				document.getElementById('view-modal-city').textContent = u.city || '—';
				document.getElementById('view-modal-orders').textContent = u.order_count || '0';
				document.getElementById('view-modal-spent').innerHTML = u.total_spent || '0 ج.م';
			}
		});
	}

	/**
	 * Toggle Status (Active / Suspended)
	 */
	function handleToggleStatus(userId, nextStatus) {
		var config = window.AshrafcoUsersConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_toggle_user_status');
		formData.append('nonce', config.nonce || '');
		formData.append('user_id', userId);
		formData.append('new_status', nextStatus);

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(res) {
			if (res.success && res.data) {
				showToast(res.data.message || 'تم تحديث حالة الحساب بنجاح', 'success');
				if (res.data.summary) {
					updateSummaryCards(res.data.summary);
				}
				fetchUsers();
			} else {
				showToast(res.data && res.data.message ? res.data.message : 'فشل تغيير حالة الحساب.', 'error');
			}
		})
		.catch(function(err) {
			showToast('تعذر الاتصال بالخادم.', 'error');
		});
	}

	/**
	 * Password Reset Modal Handling
	 */
	function initPasswordResetModal() {
		var modal = document.getElementById('portal-reset-pass-modal');
		var cancelBtn = document.getElementById('portal-reset-pass-cancel');
		var sendEmailBtn = document.getElementById('portal-reset-pass-send-email');
		var generateBtn  = document.getElementById('portal-reset-pass-generate');

		if (cancelBtn && modal) {
			cancelBtn.addEventListener('click', function() {
				modal.classList.add('hidden');
			});
		}

		if (sendEmailBtn) {
			sendEmailBtn.addEventListener('click', function() {
				var userId = modal.getAttribute('data-user-id');
				executePasswordReset(userId, 'email');
			});
		}

		if (generateBtn) {
			generateBtn.addEventListener('click', function() {
				var userId = modal.getAttribute('data-user-id');
				executePasswordReset(userId, 'generate');
			});
		}
	}

	function openPasswordResetModal(user) {
		var modal = document.getElementById('portal-reset-pass-modal');
		if (!modal) return;

		modal.setAttribute('data-user-id', user.id);
		document.getElementById('portal-reset-pass-name').textContent = user.display_name || user.user_login;
		document.getElementById('portal-reset-pass-email').textContent = user.user_email;

		var resultBox = document.getElementById('portal-reset-pass-result');
		if (resultBox) resultBox.classList.add('hidden');

		modal.classList.remove('hidden');
	}

	function executePasswordReset(userId, mode) {
		var config = window.AshrafcoUsersConfig || {};
		var formData = new FormData();
		formData.append('action', 'ashrafco_portal_reset_password');
		formData.append('nonce', config.nonce || '');
		formData.append('user_id', userId);
		formData.append('mode', mode);

		var modal = document.getElementById('portal-reset-pass-modal');

		fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
			method: 'POST',
			body: formData
		})
		.then(function(res) { return res.json(); })
		.then(function(res) {
			if (res.success && res.data) {
				showToast(res.data.message, 'success');
				if (mode === 'generate' && res.data.new_password) {
					var resultBox = document.getElementById('portal-reset-pass-result');
					var passInput = document.getElementById('portal-reset-pass-new-value');
					if (resultBox && passInput) {
						passInput.value = res.data.new_password;
						resultBox.classList.remove('hidden');
					}
				} else {
					if (modal) modal.classList.add('hidden');
				}
			} else {
				showToast(res.data && res.data.message ? res.data.message : 'فشلت عملية إعادة التعيين.', 'error');
			}
		})
		.catch(function(err) {
			showToast('تعذر الاتصال بالخادم.', 'error');
		});
	}

	/**
	 * Delete User Modal Handling
	 */
	function initDeleteUserModal() {
		var modal = document.getElementById('portal-delete-modal');
		var cancelBtn = document.getElementById('portal-delete-cancel');
		var confirmBtn = document.getElementById('portal-delete-confirm');

		if (cancelBtn && modal) {
			cancelBtn.addEventListener('click', function() {
				modal.classList.add('hidden');
			});
		}

		if (confirmBtn) {
			confirmBtn.addEventListener('click', function() {
				var userId = modal.getAttribute('data-user-id');
				if (!userId) return;

				var originalBtnHtml = confirmBtn.innerHTML;
				confirmBtn.disabled = true;
				confirmBtn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full ml-2"></span> جاري الحذف...';

				var config = window.AshrafcoUsersConfig || {};
				var formData = new FormData();
				formData.append('action', 'ashrafco_portal_delete_user');
				formData.append('nonce', config.nonce || '');
				formData.append('user_id', userId);

				fetch(config.ajaxUrl || '/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					confirmBtn.disabled = false;
					confirmBtn.innerHTML = originalBtnHtml;

					if (res.success && res.data) {
						showToast(res.data.message || 'تم حذف المستخدم بنجاح', 'success');
						modal.classList.add('hidden');
						if (res.data.summary) {
							updateSummaryCards(res.data.summary);
						}
						fetchUsers();
					} else {
						showToast(res.data && res.data.message ? res.data.message : 'فشلت عملية الحذف.', 'error');
					}
				})
				.catch(function(err) {
					confirmBtn.disabled = false;
					confirmBtn.innerHTML = originalBtnHtml;
					showToast('تعذر الاتصال بالخادم.', 'error');
				});
			});
		}
	}

	function openDeleteUserModal(user) {
		var modal = document.getElementById('portal-delete-modal');
		if (!modal) return;

		modal.setAttribute('data-user-id', user.id);
		document.getElementById('portal-delete-user-name').textContent = user.display_name || user.user_login;

		modal.classList.remove('hidden');
	}

	function closeAllModals() {
		document.querySelectorAll('.portal-modal').forEach(function(m) {
			m.classList.add('hidden');
		});
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

	// Expose for external tab switcher
	window.AshrafcoUsersReload = fetchUsers;

})();
