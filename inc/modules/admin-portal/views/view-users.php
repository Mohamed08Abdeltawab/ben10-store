<?php
/**
 * Admin Portal View: Users Management & Modals (Tab 7)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 6: مستخدمو النظام (System Users & Team Management)
			 ================================================================= -->
		<div id="portal-tab-users" class="tab-content <?php echo 'users' === $active_tab ? 'active' : ''; ?> space-y-6">

			<!-- 1. Heading & Primary Action (إضافة مستخدم) -->
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					<div
						class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
						</svg>
					</div>
					<div>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
							<?php esc_html_e('مستخدمو النظام', 'ashrafco'); ?>
						</h1>
						<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
							<?php esc_html_e('إدارة حسابات وصلاحيات فريق المتجر', 'ashrafco'); ?>
						</p>
					</div>
				</div>

				<button type="button" id="portal-add-user-btn"
					class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
					</svg>
					<span><?php esc_html_e('إضافة مستخدم', 'ashrafco'); ?></span>
				</button>
			</div>

			<!-- 2. Four Summary Cards (RTL: إجمالي المستخدمين -> نشطون -> بانتظار التفعيل -> موقوفون) -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
				<!-- Card 1: إجمالي المستخدمين (Total Users) -->
				<div
					class="bg-blue-50/40 border border-blue-100/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-2xs transition-all hover:shadow-xs">
					<div>
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('إجمالي المستخدمين', 'ashrafco'); ?>
						</div>
						<div id="portal-users-count-total" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
							<?php echo esc_html($users_summary['total']); ?>
						</div>
					</div>
					<div
						class="w-11 h-11 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
						</svg>
					</div>
				</div>

				<!-- Card 2: نشطون (Active Users) -->
				<div
					class="bg-emerald-50/40 border border-emerald-100/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-2xs transition-all hover:shadow-xs">
					<div>
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('نشطون', 'ashrafco'); ?>
						</div>
						<div id="portal-users-count-active" class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
							<?php echo esc_html($users_summary['active']); ?>
						</div>
					</div>
					<div
						class="w-11 h-11 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
					</div>
				</div>

				<!-- Card 3: بانتظار التفعيل (Pending Activation) -->
				<div
					class="bg-amber-50/40 border border-amber-100/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-2xs transition-all hover:shadow-xs">
					<div>
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('بانتظار التفعيل', 'ashrafco'); ?>
						</div>
						<div id="portal-users-count-pending"
							class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">
							<?php echo esc_html($users_summary['pending']); ?>
						</div>
					</div>
					<div
						class="w-11 h-11 rounded-xl bg-amber-100/80 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>

				<!-- Card 4: موقوفون (Suspended Users) -->
				<div
					class="bg-rose-50/40 border border-rose-100/90 rounded-2xl p-4 sm:p-5 flex items-center justify-between shadow-2xs transition-all hover:shadow-xs">
					<div>
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('موقوفون', 'ashrafco'); ?>
						</div>
						<div id="portal-users-count-suspended"
							class="text-2xl sm:text-3xl font-black text-rose-600 mt-1">
							<?php echo esc_html($users_summary['suspended']); ?>
						</div>
					</div>
					<div
						class="w-11 h-11 rounded-xl bg-rose-100/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
						</svg>
					</div>
				</div>
			</div>

			<!-- 3. Search and Filtering Toolbar -->
			<div
				class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
				<!-- Search & Select Filters (Right in RTL) -->
				<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-3xl">
					<!-- Search Box -->
					<div class="relative flex-1">
						<input type="text" id="portal-users-search"
							placeholder="<?php esc_attr_e('ابحث بالاسم أو البريد الإلكتروني...', 'ashrafco'); ?>"
							value="<?php echo esc_attr($users_search); ?>"
							class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:bg-white transition-all shadow-2xs">
						<svg class="w-4 h-4 text-slate-400 absolute right-3.5 top-3.5 pointer-events-none" fill="none"
							stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</div>

					<!-- Role Filter Dropdown -->
					<div class="relative sm:w-44 flex-shrink-0">
						<select id="portal-users-filter-role"
							class="w-full appearance-none py-2.5 pr-4 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer shadow-2xs">
							<option value=""><?php esc_html_e('كل الأدوار', 'ashrafco'); ?></option>
							<option value="administrator" <?php selected($users_role, 'administrator'); ?>>
								<?php esc_html_e('مدير النظام', 'ashrafco'); ?>
							</option>
							<option value="shop_manager" <?php selected($users_role, 'shop_manager'); ?>>
								<?php esc_html_e('مدير المتجر', 'ashrafco'); ?>
							</option>
							<option value="product_editor" <?php selected($users_role, 'product_editor'); ?>>
								<?php esc_html_e('محرر المنتجات', 'ashrafco'); ?>
							</option>
							<option value="customer_support" <?php selected($users_role, 'customer_support'); ?>>
								<?php esc_html_e('دعم العملاء', 'ashrafco'); ?>
							</option>
							<option value="customer" <?php selected($users_role, 'customer'); ?>>
								<?php esc_html_e('عميل', 'ashrafco'); ?>
							</option>
						</select>
						<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none"
							stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
						</svg>
					</div>

					<!-- Status Filter Dropdown -->
					<div class="relative sm:w-40 flex-shrink-0">
						<select id="portal-users-filter-status"
							class="w-full appearance-none py-2.5 pr-4 pl-9 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer shadow-2xs">
							<option value=""><?php esc_html_e('كل الحالات', 'ashrafco'); ?></option>
							<option value="active" <?php selected($users_status, 'active'); ?>>
								<?php esc_html_e('نشط', 'ashrafco'); ?>
							</option>
							<option value="pending" <?php selected($users_status, 'pending'); ?>>
								<?php esc_html_e('بانتظار التفعيل', 'ashrafco'); ?>
							</option>
							<option value="suspended" <?php selected($users_status, 'suspended'); ?>>
								<?php esc_html_e('موقوف', 'ashrafco'); ?>
							</option>
						</select>
						<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none"
							stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
						</svg>
					</div>
				</div>

				<!-- Result Count (Left in RTL) -->
				<div
					class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 flex-shrink-0 self-end sm:self-center pr-2">
					<svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					<span id="portal-users-count-badge"><?php echo esc_html($users_total_count); ?>
						<?php esc_html_e('مستخدماً', 'ashrafco'); ?></span>
				</div>
			</div>

			<!-- 4. System Users Table -->
			<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden relative">

				<!-- Loading Overlay -->
				<div id="portal-users-loading"
					class="hidden absolute inset-0 bg-white/70 backdrop-blur-2xs z-30 flex items-center justify-center">
					<div
						class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-lg">
						<svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
							</circle>
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
						</svg>
						<span><?php esc_html_e('جاري التحديث...', 'ashrafco'); ?></span>
					</div>
				</div>

				<div class="overflow-x-auto no-scrollbar">
					<table class="w-full text-right border-collapse">
						<thead>
							<tr
								class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] sm:text-xs font-bold text-slate-500 uppercase tracking-wider select-none">
								<th class="py-3.5 px-4 sm:px-6"><?php esc_html_e('المستخدم', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4"><?php esc_html_e('البريد الإلكتروني', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4"><?php esc_html_e('الدور', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4"><?php esc_html_e('الحالة', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4"><?php esc_html_e('آخر نشاط', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4"><?php esc_html_e('تاريخ الإنشاء', 'ashrafco'); ?></th>
								<th class="py-3.5 px-4 sm:px-6 text-left">
									<?php esc_html_e('الإجراءات', 'ashrafco'); ?>
								</th>
							</tr>
						</thead>
						<tbody id="portal-users-table-body" class="divide-y divide-slate-100">
							<?php if (!empty($portal_users)): ?>
								<?php foreach ($portal_users as $u): ?>
									<tr id="portal-user-row-<?php echo esc_attr($u['id']); ?>"
										class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors group">
										<!-- User cell (Avatar, Username, Role Subtitle) -->
										<td class="py-4 px-4 sm:px-6">
											<div class="flex items-center gap-3">
												<div
													class="w-10 h-10 rounded-xl <?php echo esc_attr($u['avatar_color_class']); ?> text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs select-none">
													<?php echo esc_html($u['avatar_initial']); ?>
												</div>
												<div class="text-right truncate">
													<div
														class="text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors flex items-center gap-1.5">
														<?php echo esc_html($u['display_name'] ?: $u['user_login']); ?>
														<?php if ($u['is_current_user']): ?>
															<span
																class="text-[10px] px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded-md font-bold">حسابك</span>
														<?php endif; ?>
													</div>
													<div class="text-xs text-slate-400 font-medium mt-0.5">
														<?php echo esc_html($u['role_label']); ?>
													</div>
												</div>
											</div>
										</td>

										<!-- Email cell -->
										<td
											class="py-4 px-4 text-xs font-semibold text-slate-600 text-right font-mono truncate max-w-[200px]">
											<?php echo esc_html($u['user_email'] ?: '—'); ?>
										</td>

										<!-- Role cell -->
										<td class="py-4 px-4 text-right">
											<?php echo ashrafco_portal_render_role_badge($u['primary_role'], $u['role_label']); ?>
										</td>

										<!-- Status cell -->
										<td class="py-4 px-4 text-right">
											<?php echo ashrafco_portal_render_status_badge($u['status'], $u['status_label']); ?>
										</td>

										<!-- Last activity cell -->
										<td class="py-4 px-4 text-right">
											<?php echo ashrafco_portal_render_activity_indicator($u['last_activity_formatted']); ?>
										</td>

										<!-- Creation date cell -->
										<td class="py-4 px-4 text-xs font-semibold text-slate-500 text-right whitespace-nowrap">
											<?php echo esc_html($u['registered_formatted'] ?: '—'); ?>
										</td>

										<!-- Actions cell -->
										<td class="py-4 px-4 sm:px-6 text-left whitespace-nowrap relative">
											<div class="relative inline-block text-right">
												<button type="button"
													class="portal-user-menu-btn w-8 h-8 rounded-lg border border-slate-200/80 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors shadow-2xs"
													data-user-id="<?php echo esc_attr($u['id']); ?>"
													title="<?php esc_attr_e('خيارات', 'ashrafco'); ?>">
													<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
														<path
															d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
													</svg>
												</button>
												<div id="user-menu-dropdown-<?php echo esc_attr($u['id']); ?>"
													class="portal-user-dropdown hidden absolute left-0 mt-1 w-52 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold text-slate-700 divide-y divide-slate-100"
													data-user='<?php echo esc_attr(wp_json_encode($u)); ?>'>
													<div class="py-1">
														<button type="button"
															class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-view-details">
															<svg class="w-4 h-4 text-slate-400" fill="none"
																stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2"
																	d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
															</svg>
															<span><?php esc_html_e('عرض تفاصيل المستخدم', 'ashrafco'); ?></span>
														</button>
														<?php if ($u['can_edit']): ?>
															<button type="button"
																class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-edit-user">
																<svg class="w-4 h-4 text-slate-400" fill="none"
																	stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
																</svg>
																<span><?php esc_html_e('تعديل المستخدم', 'ashrafco'); ?></span>
															</button>
															<button type="button"
																class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-change-role">
																<svg class="w-4 h-4 text-slate-400" fill="none"
																	stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
																</svg>
																<span><?php esc_html_e('تغيير الدور أو الصلاحيات', 'ashrafco'); ?></span>
															</button>
														<?php endif; ?>
													</div>
													<div class="py-1">
														<?php if ($u['can_edit'] && !$u['is_current_user']): ?>
															<?php
															$is_susp = ('suspended' === $u['status']);
															$t_label = $is_susp ? __('تفعيل الحساب', 'ashrafco') : __('إيقاف الحساب', 'ashrafco');
															$t_next = $is_susp ? 'active' : 'suspended';
															?>
															<button type="button"
																class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-toggle-status"
																data-next-status="<?php echo esc_attr($t_next); ?>">
																<?php if ($is_susp): ?>
																	<svg class="w-4 h-4 text-emerald-600" fill="none"
																		stroke="currentColor" viewBox="0 0 24 24">
																		<path stroke-linecap="round" stroke-linejoin="round"
																			stroke-width="2"
																			d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
																	</svg>
																<?php else: ?>
																	<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor"
																		viewBox="0 0 24 24">
																		<path stroke-linecap="round" stroke-linejoin="round"
																			stroke-width="2"
																			d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
																	</svg>
																<?php endif; ?>
																<span><?php echo esc_html($t_label); ?></span>
															</button>
														<?php endif; ?>

														<?php if ($u['can_edit']): ?>
															<button type="button"
																class="w-full text-right px-3 py-2 hover:bg-slate-50 flex items-center gap-2.5 action-reset-password">
																<svg class="w-4 h-4 text-amber-500" fill="none"
																	stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
																</svg>
																<span><?php esc_html_e('إعادة تعيين كلمة المرور', 'ashrafco'); ?></span>
															</button>
														<?php endif; ?>
													</div>
													<?php if ($u['can_delete']): ?>
														<div class="py-1">
															<button type="button"
																class="w-full text-right px-3 py-2 hover:bg-rose-50 text-rose-600 flex items-center gap-2.5 action-delete-user">
																<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor"
																	viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
																</svg>
																<span><?php esc_html_e('حذف المستخدم', 'ashrafco'); ?></span>
															</button>
														</div>
													<?php endif; ?>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Empty State Container -->
				<div id="portal-users-empty-state"
					class="<?php echo empty($portal_users) ? '' : 'hidden'; ?> py-12 px-4 text-center">
					<div
						class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
						<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
								d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
						</svg>
					</div>
					<h3 class="text-sm font-bold text-slate-800">
						<?php esc_html_e('لم يتم العثور على أية مستخدمين', 'ashrafco'); ?>
					</h3>
					<p class="text-xs text-slate-400 mt-1">
						<?php esc_html_e('جرب تغيير معايير البحث أو تصفية الأدوار والحالات.', 'ashrafco'); ?>
					</p>
				</div>

				<!-- 5. Pagination Bar matching screenshot -->
				<div
					class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 border-t border-slate-100 bg-white">
					<!-- Next / Prev + Page Buttons (Left in RTL) -->
					<div class="flex items-center gap-1.5 order-2 sm:order-1">
						<button type="button" id="portal-users-prev-page" <?php disabled($users_current_page <= 1); ?>
							class="w-8 h-8 rounded-lg border text-xs font-bold flex items-center justify-center transition-colors <?php echo $users_current_page <= 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900'; ?>"
							title="السابق">
							<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 5l7 7-7 7" />
							</svg>
						</button>

						<div id="portal-users-pages-container" class="flex items-center gap-1.5">
							<?php for ($pi = 1; $pi <= max(1, $users_total_pages); $pi++): ?>
								<button type="button"
									class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-colors <?php echo $pi === $users_current_page ? 'bg-primary text-white shadow-xs' : 'border border-slate-200 text-slate-600 hover:bg-slate-100'; ?>"
									onclick="window.AshrafcoUsersGoPage && window.AshrafcoUsersGoPage(<?php echo $pi; ?>)">
									<?php echo $pi; ?>
								</button>
							<?php endfor; ?>
						</div>

						<button type="button" id="portal-users-next-page" <?php disabled($users_current_page >= $users_total_pages); ?>
							class="w-8 h-8 rounded-lg border text-xs font-bold flex items-center justify-center transition-colors <?php echo $users_current_page >= $users_total_pages ? 'border-slate-200 text-slate-300 cursor-not-allowed bg-slate-50' : 'border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900'; ?>"
							title="التالي">
							<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M15 19l-7-7 7-7" />
							</svg>
						</button>
					</div>

					<!-- Page Size Dropdown (Right in RTL) -->
					<div class="flex items-center gap-2 text-xs font-bold text-slate-600 order-1 sm:order-2">
						<span><?php esc_html_e('عرض', 'ashrafco'); ?></span>
						<div class="relative">
							<select id="portal-users-per-page"
								class="appearance-none py-1.5 pr-3 pl-7 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 focus:outline-none focus:border-primary cursor-pointer shadow-2xs">
								<option value="10" <?php selected($users_per_page, 10); ?>>10</option>
								<option value="25" <?php selected($users_per_page, 25); ?>>25</option>
								<option value="50" <?php selected($users_per_page, 50); ?>>50</option>
							</select>
							<svg class="w-3.5 h-3.5 text-slate-400 absolute left-2 top-2 pointer-events-none"
								fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 9l-7 7-7-7" />
							</svg>
						</div>
						<span><?php esc_html_e('من كل صفحة', 'ashrafco'); ?></span>
					</div>
				</div>

			</div>

		</div>

		<!-- =====================================================================
			 MODAL: إضافة مستخدم جديد (Add User Modal)
			 ===================================================================== -->
		<div id="portal-add-user-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<div class="flex items-center justify-between pb-4 border-b border-slate-100">
					<div class="flex items-center gap-2.5">
						<div class="w-9 h-9 rounded-xl bg-rose-50 text-primary flex items-center justify-center">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
									d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
							</svg>
						</div>
						<h3 class="text-base sm:text-lg font-black text-slate-900">
							<?php esc_html_e('إضافة مستخدم جديد', 'ashrafco'); ?>
						</h3>
					</div>
					<button type="button" id="portal-add-user-cancel"
						class="text-slate-400 hover:text-slate-700 transition-colors">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				<div id="portal-add-user-error"
					class="hidden mt-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold">
				</div>

				<form id="portal-add-user-form" class="mt-5 space-y-4">
					<div>
						<label class="block text-xs font-bold text-slate-700 mb-1">
							<?php esc_html_e('اسم المستخدم', 'ashrafco'); ?> <span class="text-rose-500">*</span>
						</label>
						<input type="text" name="username" required placeholder="مثال: ahmed_tools"
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-primary focus:bg-white transition-all text-right">
						<p class="text-[10px] text-slate-400 mt-1 font-medium">
							<?php esc_html_e('يستخدم لتسجيل الدخول، أحرف إنجليزية وأرقام وبدون مسافات.', 'ashrafco'); ?>
						</p>
					</div>

					<div>
						<label class="block text-xs font-bold text-slate-700 mb-1">
							<?php esc_html_e('الاسم الظاهر', 'ashrafco'); ?>
						</label>
						<input type="text" name="display_name" placeholder="مثال: أحمد محمود"
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-primary focus:bg-white transition-all text-right">
					</div>

					<div>
						<label class="block text-xs font-bold text-slate-700 mb-1">
							<?php esc_html_e('البريد الإلكتروني', 'ashrafco'); ?> <span class="text-rose-500">*</span>
						</label>
						<input type="email" name="email" required placeholder="ahmed@ashrafco.com"
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-primary focus:bg-white transition-all text-right font-mono">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
						<div>
							<label class="block text-xs font-bold text-slate-700 mb-1">
								<?php esc_html_e('الدور والصلاحية', 'ashrafco'); ?>
							</label>
							<div class="relative">
								<select name="role"
									class="w-full appearance-none px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer">
									<?php if (current_user_can('manage_options')): ?>
										<option value="administrator">
											<?php esc_html_e('مدير النظام (Administrator)', 'ashrafco'); ?>
										</option>
									<?php endif; ?>
									<option value="shop_manager" selected>
										<?php esc_html_e('مدير المتجر (Shop Manager)', 'ashrafco'); ?>
									</option>
									<option value="product_editor">
										<?php esc_html_e('محرر المنتجات (Product Editor)', 'ashrafco'); ?>
									</option>
									<option value="customer_support">
										<?php esc_html_e('دعم العملاء (Customer Support)', 'ashrafco'); ?>
									</option>
									<option value="customer"><?php esc_html_e('عميل (Customer)', 'ashrafco'); ?>
									</option>
								</select>
								<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
									fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 9l-7 7-7-7" />
								</svg>
							</div>
						</div>

						<div>
							<label class="block text-xs font-bold text-slate-700 mb-1">
								<?php esc_html_e('حالة الحساب', 'ashrafco'); ?>
							</label>
							<div class="relative">
								<select name="status"
									class="w-full appearance-none px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer">
									<option value="active" selected>
										<?php esc_html_e('نشط (مفعل فوراً)', 'ashrafco'); ?>
									</option>
									<option value="pending"><?php esc_html_e('بانتظار التفعيل', 'ashrafco'); ?>
									</option>
									<option value="suspended"><?php esc_html_e('موقوف', 'ashrafco'); ?></option>
								</select>
								<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
									fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 9l-7 7-7-7" />
								</svg>
							</div>
						</div>
					</div>

					<div>
						<label class="block text-xs font-bold text-slate-700 mb-1">
							<?php esc_html_e('كلمة المرور', 'ashrafco'); ?>
						</label>
						<input type="text" name="password"
							placeholder="<?php esc_attr_e('اترك الحقل فارغاً لتوليد كلمة مرور قوية تلقائياً', 'ashrafco'); ?>"
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:outline-none focus:border-primary focus:bg-white transition-all text-right">
					</div>

					<div class="flex items-center gap-2 pt-1">
						<input type="checkbox" id="add_user_send_notification" name="send_notification"
							class="w-4 h-4 rounded text-primary focus:ring-primary border-slate-300" checked>
						<label for="add_user_send_notification"
							class="text-xs font-bold text-slate-600 select-none cursor-pointer">
							<?php esc_html_e('إرسال بيانات الحساب للمستخدم عبر البريد الإلكتروني', 'ashrafco'); ?>
						</label>
					</div>

					<div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
						<button type="button"
							onclick="document.getElementById('portal-add-user-modal').classList.add('hidden')"
							class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
							<?php esc_html_e('إلغاء', 'ashrafco'); ?>
						</button>
						<button type="submit"
							class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
							<span><?php esc_html_e('حفظ المستخدم', 'ashrafco'); ?></span>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- =====================================================================
			 MODAL: تعديل المستخدم (Edit User Modal)
			 ===================================================================== -->
		<div id="portal-edit-user-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<div class="flex items-center justify-between pb-4 border-b border-slate-100">
					<div class="flex items-center gap-2.5">
						<div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
							</svg>
						</div>
						<h3 class="text-base sm:text-lg font-black text-slate-900">
							<?php esc_html_e('تعديل بيانات المستخدم', 'ashrafco'); ?>
						</h3>
					</div>
					<button type="button" id="portal-edit-user-cancel"
						class="text-slate-400 hover:text-slate-700 transition-colors">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				<div id="portal-edit-user-error"
					class="hidden mt-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold">
				</div>

				<form id="portal-edit-user-form" class="mt-5 space-y-4">
					<input type="hidden" name="edit_user_id" value="0">

					<div>
						<label
							class="block text-xs font-bold text-slate-400 mb-1"><?php esc_html_e('اسم المستخدم (غير قابل للتعديل)', 'ashrafco'); ?></label>
						<input type="text" name="edit_username" readonly
							class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-slate-500 cursor-not-allowed text-right font-mono">
					</div>

					<div>
						<label
							class="block text-xs font-bold text-slate-700 mb-1"><?php esc_html_e('الاسم الظاهر', 'ashrafco'); ?>
							<span class="text-rose-500">*</span></label>
						<input type="text" name="edit_display_name" required
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-primary focus:bg-white transition-all text-right">
					</div>

					<div>
						<label
							class="block text-xs font-bold text-slate-700 mb-1"><?php esc_html_e('البريد الإلكتروني', 'ashrafco'); ?>
							<span class="text-rose-500">*</span></label>
						<input type="email" name="edit_email" required
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:outline-none focus:border-primary focus:bg-white transition-all text-right font-mono">
					</div>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
						<div>
							<label
								class="block text-xs font-bold text-slate-700 mb-1"><?php esc_html_e('الدور والصلاحية', 'ashrafco'); ?></label>
							<div class="relative">
								<select name="edit_role"
									class="w-full appearance-none px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer">
									<?php if (current_user_can('manage_options')): ?>
										<option value="administrator">
											<?php esc_html_e('مدير النظام (Administrator)', 'ashrafco'); ?>
										</option>
									<?php endif; ?>
									<option value="shop_manager">
										<?php esc_html_e('مدير المتجر (Shop Manager)', 'ashrafco'); ?>
									</option>
									<option value="product_editor">
										<?php esc_html_e('محرر المنتجات (Product Editor)', 'ashrafco'); ?>
									</option>
									<option value="customer_support">
										<?php esc_html_e('دعم العملاء (Customer Support)', 'ashrafco'); ?>
									</option>
									<option value="customer"><?php esc_html_e('عميل (Customer)', 'ashrafco'); ?>
									</option>
								</select>
								<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
									fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 9l-7 7-7-7" />
								</svg>
							</div>
						</div>

						<div>
							<label
								class="block text-xs font-bold text-slate-700 mb-1"><?php esc_html_e('حالة الحساب', 'ashrafco'); ?></label>
							<div class="relative">
								<select name="edit_status"
									class="w-full appearance-none px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer">
									<option value="active"><?php esc_html_e('نشط', 'ashrafco'); ?></option>
									<option value="pending"><?php esc_html_e('بانتظار التفعيل', 'ashrafco'); ?>
									</option>
									<option value="suspended"><?php esc_html_e('موقوف', 'ashrafco'); ?></option>
								</select>
								<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
									fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M19 9l-7 7-7-7" />
								</svg>
							</div>
						</div>
					</div>

					<div>
						<label class="block text-xs font-bold text-slate-700 mb-1">
							<?php esc_html_e('تحديث كلمة المرور (اختياري)', 'ashrafco'); ?>
						</label>
						<input type="password" name="edit_password"
							placeholder="<?php esc_attr_e('اترك الحقل فارغاً للاحتفاظ بكلمة المرور الحالية', 'ashrafco'); ?>"
							class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:outline-none focus:border-primary focus:bg-white transition-all text-right">
					</div>

					<div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
						<button type="button"
							onclick="document.getElementById('portal-edit-user-modal').classList.add('hidden')"
							class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
							<?php esc_html_e('إلغاء', 'ashrafco'); ?>
						</button>
						<button type="submit"
							class="px-6 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex items-center gap-1.5">
							<span><?php esc_html_e('حفظ التعديلات', 'ashrafco'); ?></span>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- =====================================================================
			 MODAL: تغيير الدور أو الصلاحيات (Quick Role Modal)
			 ===================================================================== -->
		<div id="portal-role-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<div class="pb-3 border-b border-slate-100">
					<h3 class="text-base font-black text-slate-900">
						<?php esc_html_e('تغيير دور المستخدم', 'ashrafco'); ?>
					</h3>
					<p id="portal-role-modal-username" class="text-xs text-slate-400 font-semibold mt-0.5 truncate"></p>
				</div>

				<form id="portal-role-form" class="mt-4 space-y-4">
					<input type="hidden" name="role_modal_user_id" value="0">
					<div>
						<label
							class="block text-xs font-bold text-slate-700 mb-1.5"><?php esc_html_e('اختر الدور الجديد', 'ashrafco'); ?></label>
						<div class="relative">
							<select name="role_modal_role"
								class="w-full appearance-none px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-primary focus:bg-white transition-all cursor-pointer">
								<?php if (current_user_can('manage_options')): ?>
									<option value="administrator">
										<?php esc_html_e('مدير النظام (Administrator)', 'ashrafco'); ?>
									</option>
								<?php endif; ?>
								<option value="shop_manager">
									<?php esc_html_e('مدير المتجر (Shop Manager)', 'ashrafco'); ?>
								</option>
								<option value="product_editor">
									<?php esc_html_e('محرر المنتجات (Product Editor)', 'ashrafco'); ?>
								</option>
								<option value="customer_support">
									<?php esc_html_e('دعم العملاء (Customer Support)', 'ashrafco'); ?>
								</option>
								<option value="customer"><?php esc_html_e('عميل (Customer)', 'ashrafco'); ?></option>
							</select>
							<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 9l-7 7-7-7" />
							</svg>
						</div>
					</div>

					<div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
						<button type="button" id="portal-role-cancel"
							class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
							<?php esc_html_e('إلغاء', 'ashrafco'); ?>
						</button>
						<button type="submit"
							class="px-5 py-2 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs">
							<?php esc_html_e('تطبيق الدور', 'ashrafco'); ?>
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- =====================================================================
			 MODAL: عرض تفاصيل المستخدم (View User Details Modal)
			 ===================================================================== -->
		<div id="portal-view-user-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<!-- Header with Avatar and Basic Info -->
				<div class="flex items-center justify-between pb-4 border-b border-slate-100">
					<div class="flex items-center gap-3.5">
						<div id="view-modal-avatar"
							class="w-14 h-14 rounded-2xl bg-slate-900 text-white font-black text-xl flex items-center justify-center shadow-xs select-none">
							U</div>
						<div>
							<h3 id="view-modal-display-name"
								class="text-base sm:text-lg font-black text-slate-900 leading-tight"></h3>
							<div class="flex items-center gap-2 mt-1">
								<span id="view-modal-username"
									class="text-xs text-slate-400 font-mono font-semibold"></span>
								<span id="view-modal-role"></span>
								<span id="view-modal-status"></span>
							</div>
						</div>
					</div>
					<button type="button"
						onclick="document.getElementById('portal-view-user-modal').classList.add('hidden')"
						class="text-slate-400 hover:text-slate-700 transition-colors">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				<!-- Details Grid -->
				<div class="mt-5 grid grid-cols-2 gap-3.5 text-xs">
					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('البريد الإلكتروني', 'ashrafco'); ?>
						</div>
						<div id="view-modal-email" class="font-bold text-slate-800 font-mono truncate"></div>
					</div>

					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('رقم الهاتف', 'ashrafco'); ?>
						</div>
						<div id="view-modal-phone" class="font-bold text-slate-800 font-mono"></div>
					</div>

					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('تاريخ الانضمام', 'ashrafco'); ?>
						</div>
						<div id="view-modal-registered" class="font-bold text-slate-800"></div>
					</div>

					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('آخر نشاط', 'ashrafco'); ?>
						</div>
						<div id="view-modal-activity" class="font-bold text-slate-800"></div>
					</div>

					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('المدينة / المنطقة', 'ashrafco'); ?>
						</div>
						<div id="view-modal-city" class="font-bold text-slate-800"></div>
					</div>

					<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
						<div class="text-[11px] font-bold text-slate-400 mb-1">
							<?php esc_html_e('إجمالي الطلبات / الإنفاق', 'ashrafco'); ?>
						</div>
						<div class="font-bold text-slate-800 flex items-center gap-1.5">
							<span id="view-modal-orders">0</span> <?php esc_html_e('طلب', 'ashrafco'); ?>
							<span>&bull;</span>
							<span id="view-modal-spent">0.00 ج.م</span>
						</div>
					</div>
				</div>

				<div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end">
					<button type="button"
						onclick="document.getElementById('portal-view-user-modal').classList.add('hidden')"
						class="px-5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
						<?php esc_html_e('إغلاق', 'ashrafco'); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- =====================================================================
			 MODAL: إعادة تعيين كلمة المرور (Reset Password Modal)
			 ===================================================================== -->
		<div id="portal-reset-pass-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<div class="flex items-center gap-3 pb-3 border-b border-slate-100">
					<div
						class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
						</svg>
					</div>
					<div>
						<h3 class="text-base font-black text-slate-900">
							<?php esc_html_e('إعادة تعيين كلمة المرور', 'ashrafco'); ?>
						</h3>
						<p class="text-xs text-slate-400 font-semibold mt-0.5">
							<span id="portal-reset-pass-name"></span> &bull; <span id="portal-reset-pass-email"
								class="font-mono"></span>
						</p>
					</div>
				</div>

				<div class="py-4 space-y-3">
					<p class="text-xs text-slate-600 leading-relaxed font-medium">
						<?php esc_html_e('يمكنك إرسال رابط آمن للمستخدم عبر بريده الإلكتروني لتعيين كلمة مرور جديدة بنفسه، أو توليد كلمة مرور جديدة فوراً وعرضها لك.', 'ashrafco'); ?>
					</p>

					<!-- Generated password display area -->
					<div id="portal-reset-pass-result"
						class="hidden p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
						<div class="text-[11px] font-bold text-emerald-800">
							<?php esc_html_e('تم توليد كلمة المرور الجديدة بنجاح:', 'ashrafco'); ?>
						</div>
						<div class="flex items-center gap-2">
							<input type="text" id="portal-reset-pass-new-value" readonly
								class="w-full bg-white border border-emerald-300 rounded-lg px-3 py-1.5 text-xs font-mono font-bold text-slate-900 select-all text-left">
							<button type="button"
								onclick="navigator.clipboard.writeText(document.getElementById('portal-reset-pass-new-value').value); showToast('تم نسخ كلمة المرور', 'success');"
								class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors whitespace-nowrap">
								<?php esc_html_e('نسخ', 'ashrafco'); ?>
							</button>
						</div>
					</div>
				</div>

				<div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-end gap-2">
					<button type="button" id="portal-reset-pass-cancel"
						class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
						<?php esc_html_e('إلغاء', 'ashrafco'); ?>
					</button>
					<button type="button" id="portal-reset-pass-generate"
						class="px-4 py-2 rounded-xl text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 transition-colors">
						<?php esc_html_e('توليد كلمة مرور فورية', 'ashrafco'); ?>
					</button>
					<button type="button" id="portal-reset-pass-send-email"
						class="px-5 py-2 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs">
						<?php esc_html_e('إرسال رابط عبر البريد', 'ashrafco'); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- =====================================================================
			 MODAL: تأكيد حذف المستخدم (Delete User Confirmation Modal)
			 ===================================================================== -->
		<div id="portal-delete-modal"
			class="portal-modal hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
			<div
				class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative transform transition-all text-right">
				<div class="flex items-center gap-3 pb-3 border-b border-slate-100">
					<div
						class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
					</div>
					<div>
						<h3 class="text-base font-black text-slate-900">
							<?php esc_html_e('تأكيد حذف المستخدم', 'ashrafco'); ?>
						</h3>
						<p id="portal-delete-user-name" class="text-xs text-rose-600 font-bold mt-0.5"></p>
					</div>
				</div>

				<div class="py-4 space-y-2 text-xs text-slate-600 leading-relaxed font-medium">
					<p><?php esc_html_e('هل أنت متأكد من رغبتك في حذف هذا الحساب نهائياً؟', 'ashrafco'); ?></p>
					<p class="text-slate-400 text-[11px]">
						<?php esc_html_e('سيتم نقل أية مقالات أو طلبات مسجلة للمستخدم تلقائياً إلى حسابك الإداري لحماية تاريخ المعاملات.', 'ashrafco'); ?>
					</p>
				</div>

				<div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
					<button type="button" id="portal-delete-cancel"
						class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors">
						<?php esc_html_e('إلغاء', 'ashrafco'); ?>
					</button>
					<button type="button" id="portal-delete-confirm"
						class="px-5 py-2 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs">
						<?php esc_html_e('نعم، حذف الحساب', 'ashrafco'); ?>
					</button>
				</div>
			</div>
		</div>


