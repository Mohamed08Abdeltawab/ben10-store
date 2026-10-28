<?php
/**
 * Admin Portal View: Coupons Manager (Tab 5)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 5: كوبونات الخصم (Coupon Management)
			 ================================================================= -->
		<div id="portal-tab-coupons"
			class="tab-content <?php echo 'coupons' === $active_tab ? 'active' : ''; ?> space-y-6">

			<!-- 1. Header (Primary action left, Title & Icon right in RTL) -->
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					<div
						class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
						</svg>
					</div>
					<div>
						<h1
							class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight flex items-center gap-2">
							<span><?php esc_html_e('كوبونات الخصم', 'ashrafco'); ?></span>
						</h1>
						<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
							<?php esc_html_e('إدارة كوبونات الخصم وتتبع حالتها واستخدامها', 'ashrafco'); ?>
						</p>
					</div>
				</div>

				<button type="button" id="portal-add-coupon-btn"
					class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
					</svg>
					<span><?php esc_html_e('إضافة كوبون جديد', 'ashrafco'); ?></span>
				</button>
			</div>

			<!-- 2. Four Summary Cards (RTL: إجمالي الكوبونات -> كوبونات نشطة -> كوبونات مجدولة -> كوبونات قاربت على الانتهاء) -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
				<!-- Card 1: إجمالي الكوبونات (Total Coupons) -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs cursor-pointer"
					id="portal-coupons-card-total-box">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
							</svg>
						</div>
						<div id="portal-coupons-count-total" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($coupons_summary['total']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('إجمالي الكوبونات', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-coupons-card-total-link"
							class="mt-2 text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1 transition-colors">
							<span>+ <?php esc_html_e('عرض جميع الكوبونات', 'ashrafco'); ?></span>
							<span class="text-xs">←</span>
						</a>
					</div>
				</div>

				<!-- Card 2: كوبونات نشطة (Active Coupons) -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs cursor-pointer"
					id="portal-coupons-card-active-box">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div id="portal-coupons-count-active" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($coupons_summary['active']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('كوبونات نشطة', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-coupons-card-active-link"
							class="mt-2 text-xs font-bold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1 transition-colors">
							<span><?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span class="text-xs">←</span>
						</a>
					</div>
				</div>

				<!-- Card 3: كوبونات مجدولة (Scheduled Coupons) -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs cursor-pointer"
					id="portal-coupons-card-scheduled-box">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
						<div id="portal-coupons-count-scheduled" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($coupons_summary['scheduled']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('كوبونات مجدولة', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-coupons-card-scheduled-link"
							class="mt-2 text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1 transition-colors">
							<span><?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span class="text-xs">←</span>
						</a>
					</div>
				</div>

				<!-- Card 4: كوبونات قاربت على الانتهاء (Expiring Soon) -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs cursor-pointer"
					id="portal-coupons-card-ending-box">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div id="portal-coupons-count-ending" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($coupons_summary['ending_soon']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('كوبونات قاربت على الانتهاء', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-coupons-card-ending-link"
							class="mt-2 text-xs font-bold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1 transition-colors">
							<span>+ <?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span class="text-xs">←</span>
						</a>
					</div>
				</div>
			</div>

			<!-- 3. Search and Filters Toolbar -->
			<div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs">
				<div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 sm:gap-4">
					<!-- Search Box -->
					<div class="relative flex-1 max-w-md">
						<div
							class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
						</div>
						<input type="text" id="portal-coupons-search"
							placeholder="<?php esc_attr_e('البحث عن كوبون...', 'ashrafco'); ?>"
							class="w-full pr-10 pl-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
					</div>

					<!-- Dropdown Filters & Actions -->
					<div class="flex flex-wrap items-center gap-2 sm:gap-3">
						<!-- Status Filter -->
						<select id="portal-coupons-filter-status"
							class="py-2.5 pr-3 pl-8 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:border-primary transition-colors cursor-pointer">
							<option value="all"><?php esc_html_e('جميع الحالات', 'ashrafco'); ?></option>
							<option value="active"><?php esc_html_e('نشط', 'ashrafco'); ?></option>
							<option value="scheduled"><?php esc_html_e('مجدول', 'ashrafco'); ?></option>
							<option value="ending_soon"><?php esc_html_e('ينتهي قريباً', 'ashrafco'); ?></option>
							<option value="expired"><?php esc_html_e('منتهي', 'ashrafco'); ?></option>
							<option value="disabled"><?php esc_html_e('موقوف', 'ashrafco'); ?></option>
						</select>

						<!-- Discount Type Filter -->
						<select id="portal-coupons-filter-type"
							class="py-2.5 pr-3 pl-8 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:border-primary transition-colors cursor-pointer">
							<option value="all"><?php esc_html_e('جميع أنواع الخصم', 'ashrafco'); ?></option>
							<option value="percent"><?php esc_html_e('نسبة مئوية', 'ashrafco'); ?></option>
							<option value="fixed_cart"><?php esc_html_e('مبلغ ثابت على السلة', 'ashrafco'); ?></option>
							<option value="fixed_product"><?php esc_html_e('مبلغ ثابت على المنتج', 'ashrafco'); ?>
							</option>
						</select>

						<!-- Sort Filter -->
						<select id="portal-coupons-filter-sort"
							class="py-2.5 pr-3 pl-8 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-700 bg-white hover:border-slate-300 focus:outline-none focus:border-primary transition-colors cursor-pointer">
							<option value="date_desc"><?php esc_html_e('الأحدث أولاً', 'ashrafco'); ?></option>
							<option value="date_asc"><?php esc_html_e('الأقدم أولاً', 'ashrafco'); ?></option>
							<option value="usage_desc"><?php esc_html_e('الأكثر استخداماً', 'ashrafco'); ?></option>
							<option value="usage_asc"><?php esc_html_e('الأقل استخداماً', 'ashrafco'); ?></option>
							<option value="expiry_asc"><?php esc_html_e('الأقرب انتهاءً', 'ashrafco'); ?></option>
							<option value="amount_desc"><?php esc_html_e('أعلى قيمة خصم', 'ashrafco'); ?></option>
						</select>

						<!-- Reset Filters Button -->
						<button type="button" id="portal-coupons-reset-filters"
							class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors cursor-pointer select-none"
							title="<?php esc_attr_e('إعادة ضبط كافة الفلاتر والبحث', 'ashrafco'); ?>">
							<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							<span><?php esc_html_e('مسح الفلاتر', 'ashrafco'); ?></span>
						</button>
					</div>
				</div>
			</div>

			<!-- 4. Coupons Table Card -->
			<div class="bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs">
				<!-- Loading Indicator -->
				<div id="portal-coupons-loading"
					class="hidden p-8 text-center text-xs font-bold text-slate-500 flex items-center justify-center gap-2">
					<svg class="w-5 h-5 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
						</circle>
						<path class="opacity-75" fill="currentColor"
							d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
						</path>
					</svg>
					<span><?php esc_html_e('جاري تحميل بيانات الكوبونات...', 'ashrafco'); ?></span>
				</div>

				<div id="portal-coupons-table-wrapper" class="overflow-x-auto min-h-[280px] pb-12">
					<table class="w-full text-right border-collapse min-w-[980px]">
						<thead>
							<tr
								class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider select-none">
								<th class="py-3.5 pr-4 pl-2 w-10 text-center">
									<input type="checkbox" id="portal-coupons-select-all"
										class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer"
										title="<?php esc_attr_e('تحديد الكل', 'ashrafco'); ?>">
								</th>
								<th class="py-3.5 px-3 min-w-[170px]"><?php esc_html_e('الكوبون', 'ashrafco'); ?></th>
								<th class="py-3.5 px-3 min-w-[130px]"><?php esc_html_e('نوع الخصم', 'ashrafco'); ?></th>
								<th class="py-3.5 px-3 min-w-[110px]"><?php esc_html_e('قيمة الخصم', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[120px]">
									<?php esc_html_e('الحد الأدنى للطلب', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[110px]">
									<?php esc_html_e('الحد الأقصى للخصم', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[130px]"><?php esc_html_e('تاريخ البداية', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[130px]"><?php esc_html_e('تاريخ الانتهاء', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[120px]"><?php esc_html_e('الاستخدامات', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 px-3 min-w-[110px] text-center">
									<?php esc_html_e('الحالة', 'ashrafco'); ?>
								</th>
								<th class="py-3.5 pl-4 pr-3 min-w-[100px] text-center">
									<?php esc_html_e('إجراءات', 'ashrafco'); ?>
								</th>
							</tr>
						</thead>
						<tbody id="portal-coupons-tbody" class="divide-y divide-slate-100/90 text-xs sm:text-sm">
							<!-- Populated via AJAX / portal-coupons.js -->
						</tbody>
					</table>
				</div>

				<!-- Empty State -->
				<div id="portal-coupons-empty-state" class="hidden py-16 px-4 text-center">
					<div
						class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
						</svg>
					</div>
					<h3 class="text-sm sm:text-base font-extrabold text-slate-700">
						<?php esc_html_e('لا توجد كوبونات خصم مطابقة', 'ashrafco'); ?>
					</h3>
					<p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
						<?php esc_html_e('لم يتم العثور على أي كوبونات خصم تطابق معايير البحث أو الفلترة المحددة.', 'ashrafco'); ?>
					</p>
					<button type="button" id="portal-coupons-empty-add-btn"
						class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-[#b90323] transition-colors cursor-pointer">
						<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
						</svg>
						<span><?php esc_html_e('إضافة كوبون جديد', 'ashrafco'); ?></span>
					</button>
				</div>

				<!-- Table Footer (Pagination & Counter) -->
				<div id="portal-coupons-footer"
					class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
					<!-- Results Counter (Right) -->
					<div id="portal-coupons-results-count" class="text-xs font-bold text-slate-500 font-sans">
						<!-- e.g. عرض 1 - 10 من 28 كوبون -->
					</div>

					<!-- Pagination Buttons (Left) -->
					<div class="flex items-center gap-1">
						<button type="button" id="portal-coupons-prev-page"
							class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
							title="<?php esc_attr_e('الصفحة السابقة', 'ashrafco'); ?>">
							<span class="text-xs font-bold">›</span>
						</button>
						<div id="portal-coupons-pagination-pages" class="flex items-center gap-1">
							<!-- Dynamic page buttons -->
						</div>
						<button type="button" id="portal-coupons-next-page"
							class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
							title="<?php esc_attr_e('الصفحة التالية', 'ashrafco'); ?>">
							<span class="text-xs font-bold">‹</span>
						</button>
					</div>
				</div>
			</div>

			<!-- 5. Floating Bulk Actions Bar -->
			<div id="portal-coupons-bulk-bar"
				class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white rounded-2xl px-5 py-3 shadow-2xl flex items-center gap-4 border border-slate-700/80">
				<span id="portal-coupons-bulk-count" class="text-xs font-bold font-sans">
					<!-- تم تحديد X كوبون -->
				</span>
				<div class="h-4 w-px bg-slate-700"></div>
				<button type="button" id="portal-coupons-bulk-delete-btn"
					class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors cursor-pointer shadow-xs">
					<?php esc_html_e('حذف الكوبونات المحددة', 'ashrafco'); ?>
				</button>
				<button type="button" id="portal-coupons-bulk-cancel-btn"
					class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors cursor-pointer">
					<?php esc_html_e('إلغاء', 'ashrafco'); ?>
				</button>
			</div>

			<!-- 6. Add / Edit Coupon Modal (Matching Reference Design) -->
			<div id="portal-coupon-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"
					id="portal-coupon-modal-backdrop"></div>
				<div class="min-h-full flex items-center justify-center p-3 sm:p-4">
					<div
						class="relative bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl border border-slate-100 space-y-5 text-right max-h-[92vh] overflow-y-auto">
						<!-- Modal Header -->
						<div class="flex items-center justify-between pb-4 border-b border-slate-100">
							<button type="button" id="portal-coupon-modal-close"
								class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
							<div class="flex items-center gap-2.5">
								<div
									class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
									</svg>
								</div>
								<h3 id="portal-coupon-modal-title"
									class="text-base sm:text-lg font-black text-slate-900">
									<?php esc_html_e('إضافة كوبون جديد', 'ashrafco'); ?>
								</h3>
							</div>
						</div>

						<!-- Modal Error Banner -->
						<div id="portal-coupon-modal-error"
							class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
						</div>

						<!-- Form -->
						<form id="portal-coupon-form" class="space-y-4">
							<input type="hidden" id="portal-coupon-field-id" value="0">

							<!-- Row 1: Code, Type, Amount -->
							<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
								<!-- Coupon Code -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('رمز الكوبون', 'ashrafco'); ?> <span
											class="text-rose-500">*</span>
									</label>
									<input type="text" id="portal-coupon-field-code" required
										placeholder="<?php esc_attr_e('مثال: ASH10', 'ashrafco'); ?>"
										class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 uppercase focus:border-primary focus:outline-none uppercase">
								</div>

								<!-- Discount Type -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('نوع الخصم', 'ashrafco'); ?> <span
											class="text-rose-500">*</span>
									</label>
									<select id="portal-coupon-field-type" required
										class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-primary focus:outline-none cursor-pointer">
										<option value="percent"><?php esc_html_e('نسبة مئوية', 'ashrafco'); ?></option>
										<option value="fixed_cart">
											<?php esc_html_e('مبلغ ثابت على السلة', 'ashrafco'); ?>
										</option>
										<option value="fixed_product">
											<?php esc_html_e('مبلغ ثابت على المنتج', 'ashrafco'); ?>
										</option>
									</select>
								</div>

								<!-- Discount Amount -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('قيمة الخصم', 'ashrafco'); ?> <span
											class="text-rose-500">*</span>
									</label>
									<div class="relative">
										<input type="number" id="portal-coupon-field-amount" required step="any"
											min="0.01" placeholder="10"
											class="w-full px-3 py-2.5 pl-8 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-primary focus:outline-none">
										<span id="portal-coupon-amount-unit"
											class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-black text-slate-400 pointer-events-none">%</span>
									</div>
								</div>
							</div>

							<!-- Row 2: Description -->
							<div>
								<label class="block text-xs font-bold text-slate-700 mb-1">
									<?php esc_html_e('وصف الكوبون', 'ashrafco'); ?>
								</label>
								<input type="text" id="portal-coupon-field-desc"
									placeholder="<?php esc_attr_e('وصف مختصر للكوبون يظهر للعملاء', 'ashrafco'); ?>"
									class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-primary focus:outline-none">
							</div>

							<!-- Row 3: Start Date, Expiry Date -->
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
								<!-- Start Date -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ البداية (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="datetime-local" id="portal-coupon-field-start-date"
										class="portal-date-input w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:border-primary focus:outline-none">
								</div>

								<!-- Expiry Date -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ الانتهاء (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="datetime-local" id="portal-coupon-field-expiry-date"
										class="portal-date-input w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:border-primary focus:outline-none">
								</div>
							</div>

							<!-- Row 4: Minimum Spend, Maximum Discount -->
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
								<!-- Minimum Spend -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('الحد الأدنى للطلب', 'ashrafco'); ?>
									</label>
									<div class="relative">
										<input type="number" id="portal-coupon-field-min-spend" step="any" min="0"
											placeholder="<?php esc_attr_e('مثال: 500', 'ashrafco'); ?>"
											class="w-full px-3 py-2.5 pl-12 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-primary focus:outline-none">
										<span
											class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-bold text-slate-400 pointer-events-none">
											<?php echo esc_html(get_woocommerce_currency_symbol()); ?>
										</span>
									</div>
								</div>

								<!-- Maximum Discount Amount (for percent discount) -->
								<div id="portal-coupon-wrapper-max-discount">
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('الحد الأقصى للخصم (لنسبة الخصم)', 'ashrafco'); ?>
									</label>
									<div class="relative">
										<input type="number" id="portal-coupon-field-max-discount" step="any" min="0"
											placeholder="<?php esc_attr_e('مثال: 100', 'ashrafco'); ?>"
											class="w-full px-3 py-2.5 pl-12 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-primary focus:outline-none">
										<span
											class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-bold text-slate-400 pointer-events-none">
											<?php echo esc_html(get_woocommerce_currency_symbol()); ?>
										</span>
									</div>
								</div>
							</div>

							<!-- Row 5: Usage Limits -->
							<div
								class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/70">
								<!-- Usage Limit Per Coupon -->
								<div>
									<div class="flex items-center justify-between mb-1">
										<label class="text-xs font-bold text-slate-700">
											<?php esc_html_e('حد عدد الاستخدامات', 'ashrafco'); ?>
										</label>
										<label class="inline-flex items-center gap-1.5 cursor-pointer">
											<input type="checkbox" id="portal-coupon-toggle-limit"
												class="w-3.5 h-3.5 rounded border-slate-300 text-primary focus:ring-primary">
											<span
												class="text-[11px] font-bold text-slate-500"><?php esc_html_e('تفعيل الحد', 'ashrafco'); ?></span>
										</label>
									</div>
									<input type="number" id="portal-coupon-field-usage-limit" min="1" step="1" disabled
										placeholder="<?php esc_attr_e('غير محدود', 'ashrafco'); ?>"
										class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white disabled:bg-slate-100 disabled:text-slate-400 focus:border-primary focus:outline-none">
								</div>

								<!-- Usage Limit Per User -->
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('حد الاستخدام لكل عميل', 'ashrafco'); ?>
									</label>
									<input type="number" id="portal-coupon-field-user-limit" min="0" step="1"
										placeholder="<?php esc_attr_e('مثال: 1 (0 = غير محدود)', 'ashrafco'); ?>"
										class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-primary focus:outline-none">
								</div>
							</div>

							<!-- Advanced Rules (Accordion) -->
							<div class="border border-slate-200 rounded-2xl overflow-hidden">
								<button type="button" id="portal-coupon-accordion-btn"
									class="w-full px-4 py-3 bg-slate-50 flex items-center justify-between text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
									<div class="flex items-center gap-2">
										<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
										</svg>
										<span><?php esc_html_e('شروط وتفاصيل متقدمة (منتجات، تصنيفات، قيود)', 'ashrafco'); ?></span>
									</div>
									<span id="portal-coupon-accordion-arrow"
										class="text-xs transition-transform duration-200">▼</span>
								</button>

								<div id="portal-coupon-accordion-body"
									class="hidden p-4 space-y-3.5 bg-white border-t border-slate-100">
									<!-- Target Products -->
									<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
										<div>
											<label class="block text-xs font-bold text-slate-700 mb-1">
												<?php esc_html_e('تطبيق على منتجات محددة (معرفات)', 'ashrafco'); ?>
											</label>
											<input type="text" id="portal-coupon-field-product-ids"
												placeholder="<?php esc_attr_e('معرفات مفصولة بفواصل، مثال: 12, 45, 88', 'ashrafco'); ?>"
												class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-primary focus:outline-none">
										</div>
										<div>
											<label class="block text-xs font-bold text-slate-700 mb-1">
												<?php esc_html_e('استثناء منتجات محددة (معرفات)', 'ashrafco'); ?>
											</label>
											<input type="text" id="portal-coupon-field-excluded-product-ids"
												placeholder="<?php esc_attr_e('معرفات مفصولة بفواصل، مثال: 10, 15', 'ashrafco'); ?>"
												class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-primary focus:outline-none">
										</div>
									</div>

									<!-- Target Categories -->
									<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
										<div>
											<label class="block text-xs font-bold text-slate-700 mb-1">
												<?php esc_html_e('تطبيق على تصنيفات محددة (معرفات)', 'ashrafco'); ?>
											</label>
											<input type="text" id="portal-coupon-field-cat-ids"
												placeholder="<?php esc_attr_e('معرفات التصنيفات، مثال: 3, 7', 'ashrafco'); ?>"
												class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-primary focus:outline-none">
										</div>
										<div>
											<label class="block text-xs font-bold text-slate-700 mb-1">
												<?php esc_html_e('استثناء تصنيفات محددة (معرفات)', 'ashrafco'); ?>
											</label>
											<input type="text" id="portal-coupon-field-excluded-cat-ids"
												placeholder="<?php esc_attr_e('معرفات التصنيفات، مثال: 4', 'ashrafco'); ?>"
												class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-primary focus:outline-none">
										</div>
									</div>

									<!-- Rule Checkboxes -->
									<div class="space-y-2 pt-2 border-t border-slate-100">
										<label class="flex items-center gap-2 cursor-pointer select-none">
											<input type="checkbox" id="portal-coupon-field-individual"
												class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary">
											<span class="text-xs font-bold text-slate-700">
												<?php esc_html_e('استخدام فردي فقط (لا يمكن تطبيقه مع كوبونات أخرى في نفس الطلب)', 'ashrafco'); ?>
											</span>
										</label>

										<label class="flex items-center gap-2 cursor-pointer select-none">
											<input type="checkbox" id="portal-coupon-field-exclude-sale"
												class="w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary">
											<span class="text-xs font-bold text-slate-700">
												<?php esc_html_e('استثناء المنتجات المخفضة (لا يسري الخصم على المنتجات التي لديها سعر مخفض بالفعل)', 'ashrafco'); ?>
											</span>
										</label>

										<label class="flex items-center gap-2 cursor-pointer select-none">
											<input type="checkbox" id="portal-coupon-field-disabled"
												class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
											<span class="text-xs font-bold text-rose-700">
												<?php esc_html_e('إيقاف الكوبون مؤقتاً (موقوف - لن يتمكن العملاء من استخدامه)', 'ashrafco'); ?>
											</span>
										</label>
									</div>
								</div>
							</div>

							<!-- Actions -->
							<div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
								<button type="button" id="portal-coupon-cancel-btn"
									class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
									<?php esc_html_e('إلغاء', 'ashrafco'); ?>
								</button>
								<button type="submit" id="portal-coupon-save-btn"
									class="px-6 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex items-center gap-2">
									<svg id="portal-coupon-save-spinner" class="hidden w-4 h-4 animate-spin" fill="none"
										viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
											stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor"
											d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
										</path>
									</svg>
									<span><?php esc_html_e('حفظ الكوبون', 'ashrafco'); ?></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- 7. Delete Confirmation Modal -->
			<div id="portal-coupon-delete-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"
					id="portal-coupon-delete-backdrop"></div>
				<div class="min-h-full flex items-center justify-center p-4">
					<div
						class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 text-right">
						<div
							class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-2">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
						<h3 class="text-base sm:text-lg font-black text-slate-900 text-center">
							<?php esc_html_e('تأكيد حذف الكوبون', 'ashrafco'); ?>
						</h3>
						<p class="text-xs text-slate-500 text-center leading-relaxed">
							<?php esc_html_e('هل أنت متأكد من رغبتك في حذف الكوبون', 'ashrafco'); ?>
							<span id="portal-coupon-delete-code" class="font-mono font-bold text-slate-900"></span>؟
							<br>
							<?php esc_html_e('هذا الإجراء نهائي ولا يمكن التراجع عنه.', 'ashrafco'); ?>
						</p>
						<div class="flex items-center justify-center gap-3 pt-3">
							<button type="button" id="portal-coupon-cancel-delete-btn"
								class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
								<?php esc_html_e('إلغاء', 'ashrafco'); ?>
							</button>
							<button type="button" id="portal-coupon-confirm-delete-btn"
								class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors cursor-pointer shadow-xs">
								<?php esc_html_e('تأكيد الحذف', 'ashrafco'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- 8. Usage Details Modal -->
			<div id="portal-coupon-usage-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"
					id="portal-coupon-usage-backdrop"></div>
				<div class="min-h-full flex items-center justify-center p-3 sm:p-4">
					<div
						class="relative bg-white rounded-3xl max-w-2xl w-full p-5 sm:p-7 shadow-2xl border border-slate-100 space-y-4 text-right max-h-[85vh] overflow-y-auto">
						<div class="flex items-center justify-between pb-3 border-b border-slate-100">
							<button type="button" id="portal-coupon-usage-close"
								class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
							<div class="flex items-center gap-2">
								<h3 class="text-base font-black text-slate-900">
									<?php esc_html_e('تفاصيل استخدام الكوبون:', 'ashrafco'); ?>
									<span id="portal-coupon-usage-title-code" class="text-primary font-mono"></span>
								</h3>
							</div>
						</div>

						<div id="portal-coupon-usage-stats"
							class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-3.5 bg-slate-50 rounded-2xl">
							<div>
								<div class="text-[11px] font-bold text-slate-400">
									<?php esc_html_e('إجمالي الاستخدامات', 'ashrafco'); ?>
								</div>
								<div id="portal-coupon-usage-stat-count"
									class="text-lg font-black text-slate-900 mt-0.5">0</div>
							</div>
							<div>
								<div class="text-[11px] font-bold text-slate-400">
									<?php esc_html_e('حد الاستخدام', 'ashrafco'); ?>
								</div>
								<div id="portal-coupon-usage-stat-limit"
									class="text-lg font-black text-slate-900 mt-0.5">∞</div>
							</div>
							<div>
								<div class="text-[11px] font-bold text-slate-400">
									<?php esc_html_e('الحالة الحالية', 'ashrafco'); ?>
								</div>
								<div id="portal-coupon-usage-stat-status" class="mt-0.5"></div>
							</div>
						</div>

						<div>
							<h4 class="text-xs font-bold text-slate-700 mb-2">
								<?php esc_html_e('أحدث الطلبات التي استخدمت هذا الكوبون:', 'ashrafco'); ?>
							</h4>
							<div class="border border-slate-100 rounded-xl overflow-hidden">
								<div class="overflow-x-auto max-h-60">
									<table class="w-full text-right text-xs">
										<thead
											class="bg-slate-50 text-slate-500 border-b border-slate-100 text-[11px] font-bold">
											<tr>
												<th class="py-2.5 px-3"><?php esc_html_e('رقم الطلب', 'ashrafco'); ?>
												</th>
												<th class="py-2.5 px-3"><?php esc_html_e('التاريخ', 'ashrafco'); ?></th>
												<th class="py-2.5 px-3"><?php esc_html_e('العميل', 'ashrafco'); ?></th>
												<th class="py-2.5 px-3"><?php esc_html_e('الإجمالي', 'ashrafco'); ?>
												</th>
												<th class="py-2.5 px-3"><?php esc_html_e('الحالة', 'ashrafco'); ?></th>
											</tr>
										</thead>
										<tbody id="portal-coupon-usage-orders-tbody" class="divide-y divide-slate-100">
											<!-- Populated dynamically -->
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>


