<?php
/**
 * Admin Portal View: Discounts Manager (Tab 4)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 4: إدارة الخصومات (Product Discount Management)
			 ================================================================= -->
		<div id="portal-tab-discounts"
			class="tab-content <?php echo 'discounts' === $active_tab ? 'active' : ''; ?> space-y-6">

			<!-- 1. Header (Primary action left, Title & Icon right in RTL) -->
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					<div
						class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
						</svg>
					</div>
					<div>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
							<?php esc_html_e('إدارة الخصومات', 'ashrafco'); ?>
						</h1>
						<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
							<?php esc_html_e('إدارة خصومات المنتجات وتتبع حالتها وموعد انتهائها', 'ashrafco'); ?>
						</p>
					</div>
				</div>

				<button type="button" id="portal-add-discount-btn"
					class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
					</svg>
					<span><?php esc_html_e('إضافة خصم جديد', 'ashrafco'); ?></span>
				</button>
			</div>

			<!-- 2. Four Summary Cards (RTL: إجمالي الخصومات -> خصم نشط حالياً -> خصومات مجدولة -> خصومات ستنتهي قريباً) -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
				<!-- Card 1: إجمالي الخصومات (Total Discounts) -->
				<div
					class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
							</svg>
						</div>
						<div id="portal-discounts-count-total" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($discounts_summary['total']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('إجمالي الخصومات', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-discounts-card-total-link"
							class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700 mt-2 transition-colors no-underline">
							<span>+</span>
							<span><?php esc_html_e('عرض جميع الخصومات', 'ashrafco'); ?></span>
							<span>←</span>
						</a>
					</div>
				</div>

				<!-- Card 2: خصم نشط حالياً (Active Discounts) -->
				<div
					class="bg-emerald-50/50 border border-emerald-100/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div id="portal-discounts-count-active" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($discounts_summary['active']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('خصم نشط حالياً', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-discounts-card-active-link"
							class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 hover:text-emerald-700 mt-2 transition-colors no-underline">
							<span><?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span>←</span>
						</a>
					</div>
				</div>

				<!-- Card 3: خصومات مجدولة (Scheduled Discounts) -->
				<div
					class="bg-sky-50/50 border border-sky-100/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-sky-100/80 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
						<div id="portal-discounts-count-scheduled"
							class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($discounts_summary['scheduled']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('خصومات مجدولة', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-discounts-card-scheduled-link"
							class="inline-flex items-center gap-1 text-xs font-bold text-sky-600 hover:text-sky-700 mt-2 transition-colors no-underline">
							<span><?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span>←</span>
						</a>
					</div>
				</div>

				<!-- Card 4: خصومات ستنتهي قريباً (Ending Soon Discounts) -->
				<div
					class="bg-rose-50/50 border border-rose-100/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-rose-100/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</div>
						<div id="portal-discounts-count-ending" class="text-2xl sm:text-3xl font-black text-slate-900">
							<?php echo esc_html($discounts_summary['ending_soon']); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('خصومات ستنتهي قريباً', 'ashrafco'); ?>
						</div>
						<a href="#" id="portal-discounts-card-ending-link"
							class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 hover:text-rose-700 mt-2 transition-colors no-underline">
							<span>+</span>
							<span><?php esc_html_e('عرض التفاصيل', 'ashrafco'); ?></span>
							<span>←</span>
						</a>
					</div>
				</div>
			</div>

			<!-- 3. Search & Filter Toolbar -->
			<div
				class="bg-white border border-slate-200/90 rounded-2xl p-3 sm:p-4 shadow-2xs flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
				<!-- Right in RTL: Live Search Input -->
				<div class="relative flex-1 max-w-md">
					<input type="text" id="portal-discounts-search"
						placeholder="<?php esc_attr_e('ابحث عن منتج...', 'ashrafco'); ?>"
						class="w-full text-xs sm:text-sm font-semibold bg-slate-50 border border-slate-200/90 rounded-xl pr-10 pl-4 py-2.5 text-slate-800 placeholder-slate-400 focus:bg-white focus:border-rose-500 focus:outline-none transition-all portal-search-input">
					<svg class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</div>

				<!-- Left in RTL: Dropdown Filters, Sorting & Reset -->
				<div class="flex items-center gap-2 sm:gap-3 flex-wrap sm:flex-nowrap">
					<!-- Status Filter Dropdown -->
					<div class="relative w-full sm:w-auto">
						<select id="portal-discounts-filter-status"
							class="w-full sm:w-auto text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200/90 rounded-xl py-2.5 px-3 focus:bg-white focus:border-rose-500 focus:outline-none transition-all cursor-pointer shadow-2xs">
							<option value="all"><?php esc_html_e('جميع الحالات', 'ashrafco'); ?></option>
							<option value="active"><?php esc_html_e('خصم نشط حالياً', 'ashrafco'); ?></option>
							<option value="paused"><?php esc_html_e('خصم غير نشط / معطل', 'ashrafco'); ?></option>
							<option value="scheduled"><?php esc_html_e('خصومات مجدولة', 'ashrafco'); ?></option>
							<option value="ending_soon"><?php esc_html_e('تنتهي قريباً (خلال 7 أيام)', 'ashrafco'); ?>
							</option>
							<option value="expired"><?php esc_html_e('خصومات منتهية', 'ashrafco'); ?></option>
							<option value="none"><?php esc_html_e('بدون خصم', 'ashrafco'); ?></option>
						</select>
					</div>

					<!-- Category Filter Dropdown -->
					<div class="relative w-full sm:w-auto">
						<select id="portal-discounts-filter-category"
							class="w-full sm:w-auto text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200/90 rounded-xl py-2.5 px-3 focus:bg-white focus:border-rose-500 focus:outline-none transition-all cursor-pointer shadow-2xs">
							<option value=""><?php esc_html_e('جميع التصنيفات', 'ashrafco'); ?></option>
						</select>
					</div>

					<!-- Sorting Dropdown -->
					<div class="relative w-full sm:w-auto">
						<select id="portal-discounts-filter-sort"
							class="w-full sm:w-auto text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200/90 rounded-xl py-2.5 px-3 focus:bg-white focus:border-rose-500 focus:outline-none transition-all cursor-pointer shadow-2xs">
							<option value="modified"><?php esc_html_e('ترتيب حسب: الأحدث تعديلاً', 'ashrafco'); ?>
							</option>
							<option value="discount_desc"><?php esc_html_e('أعلى نسبة خصم', 'ashrafco'); ?></option>
							<option value="discount_asc"><?php esc_html_e('أقل نسبة خصم', 'ashrafco'); ?></option>
							<option value="price_asc"><?php esc_html_e('أقل سعر بعد الخصم', 'ashrafco'); ?></option>
							<option value="price_desc"><?php esc_html_e('أعلى سعر بعد الخصم', 'ashrafco'); ?></option>
							<option value="ending_soon"><?php esc_html_e('الأقرب انتهاءً', 'ashrafco'); ?></option>
						</select>
					</div>

					<!-- Reset Filters Button -->
					<button type="button" id="portal-discounts-reset-filters"
						class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/90 transition-colors shadow-2xs cursor-pointer whitespace-nowrap"
						title="<?php esc_attr_e('مسح جميع الفلاتر', 'ashrafco'); ?>">
						<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
						</svg>
						<span><?php esc_html_e('مسح الفلاتر', 'ashrafco'); ?></span>
					</button>
				</div>
			</div>

			<!-- 4. Products Table Card -->
			<div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden relative">
				<!-- Loading Overlay -->
				<div id="portal-discounts-loading"
					class="hidden absolute inset-0 bg-white/80 backdrop-blur-2xs z-30 flex items-center justify-center">
					<div class="flex flex-col items-center gap-2">
						<div class="w-8 h-8 border-3 border-rose-600 border-t-transparent rounded-full animate-spin">
						</div>
						<span
							class="text-xs font-bold text-slate-600"><?php esc_html_e('جاري تحميل الخصومات...', 'ashrafco'); ?></span>
					</div>
				</div>

				<!-- Table Container -->
				<div id="portal-discounts-table-wrapper" class="overflow-x-auto">
					<table class="w-full text-right border-collapse">
						<thead>
							<tr
								class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] sm:text-xs font-black text-slate-500 uppercase tracking-wider select-none">
								<th scope="col" class="py-3 px-3 sm:px-4 text-center w-10">
									<input type="checkbox" id="portal-discounts-select-all"
										class="rounded-md border-slate-300 text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer"
										title="<?php esc_attr_e('تحديد الكل', 'ashrafco'); ?>">
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-right">
									<?php esc_html_e('المنتج', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-right">
									<?php esc_html_e('السعر الأصلي', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-right">
									<?php esc_html_e('السعر بعد الخصم', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-center">
									<?php esc_html_e('نسبة الخصم', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-right">
									<?php esc_html_e('فترة الخصم', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-center">
									<?php esc_html_e('الحالة', 'ashrafco'); ?>
								</th>
								<th scope="col" class="py-3 px-3 sm:px-4 text-left w-24">
									<?php esc_html_e('إجراءات', 'ashrafco'); ?>
								</th>
							</tr>
						</thead>
						<tbody id="portal-discounts-tbody" class="divide-y divide-slate-100/90 text-xs sm:text-sm">
							<!-- Populated via AJAX / portal-discounts.js -->
						</tbody>
					</table>
				</div>

				<!-- Empty State -->
				<div id="portal-discounts-empty-state" class="hidden py-16 px-4 text-center">
					<div
						class="w-16 h-16 rounded-3xl bg-rose-50 border border-rose-200 text-rose-600 mx-auto flex items-center justify-center shadow-xs mb-3">
						<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
						</svg>
					</div>
					<h3 class="text-base font-black text-slate-800">
						<?php esc_html_e('لم يتم العثور على أي خصومات', 'ashrafco'); ?>
					</h3>
					<p class="text-xs text-slate-400 font-medium max-w-sm mx-auto mt-1">
						<?php esc_html_e('لا توجد منتجات تطابق معايير البحث أو الفلترة المحددة حالياً. يمكنك مسح الفلاتر أو إضافة خصم جديد.', 'ashrafco'); ?>
					</p>
				</div>

				<!-- Table Footer: Results Counter & Pagination Controls -->
				<div id="portal-discounts-footer"
					class="px-4 py-3 bg-slate-50/70 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
					<!-- Right: Result Count Info -->
					<div id="portal-discounts-results-count" class="text-xs font-bold text-slate-500 font-sans">
						<!-- e.g. عرض 1 - 7 من 36 خصم -->
					</div>

					<!-- Left: Pagination Numbers -->
					<div class="flex items-center gap-1.5 select-none" dir="ltr">
						<button type="button" id="portal-discounts-prev-page"
							class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center justify-center cursor-pointer shadow-2xs"
							title="<?php esc_attr_e('الصفحة السابقة', 'ashrafco'); ?>">
							&lt;
						</button>
						<div id="portal-discounts-pagination-pages" class="flex items-center gap-1">
							<!-- Page buttons rendered by JS -->
						</div>
						<button type="button" id="portal-discounts-next-page"
							class="w-8 h-8 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 transition-colors flex items-center justify-center cursor-pointer shadow-2xs"
							title="<?php esc_attr_e('الصفحة التالية', 'ashrafco'); ?>">
							&gt;
						</button>
					</div>
				</div>
			</div>

			<!-- 5. Floating Bulk Actions Bar -->
			<div id="portal-discounts-bulk-bar"
				class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white rounded-2xl px-5 py-3 shadow-2xl flex items-center gap-4 border border-slate-700/80">
				<span id="portal-discounts-bulk-count" class="text-xs font-bold font-sans">
					<!-- تم تحديد X منتج -->
				</span>
				<div class="h-4 w-px bg-slate-700"></div>
				<button type="button" id="portal-discounts-bulk-remove-btn"
					class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors cursor-pointer shadow-xs">
					<?php esc_html_e('إزالة الخصومات المحددة', 'ashrafco'); ?>
				</button>
				<button type="button" id="portal-discounts-bulk-cancel-btn"
					class="px-3 py-1.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors cursor-pointer">
					<?php esc_html_e('إلغاء', 'ashrafco'); ?>
				</button>
			</div>

			<!-- 6. Add Discount Modal (Dialog) -->
			<div id="portal-add-discount-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"></div>
				<div class="min-h-full flex items-center justify-center p-4">
					<div
						class="relative bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-right">
						<!-- Modal Header -->
						<div class="flex items-center justify-between pb-4 border-b border-slate-100">
							<button type="button" id="portal-add-discount-close"
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
											d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
									</svg>
								</div>
								<h3 class="text-base font-black text-slate-900">
									<?php esc_html_e('إضافة خصم جديد', 'ashrafco'); ?>
								</h3>
							</div>
						</div>

						<!-- Form -->
						<form id="portal-add-discount-form" class="space-y-4">
							<input type="hidden" id="portal-add-product-id" value="">

							<!-- 1. Product Search & Selection -->
							<div>
								<label class="block text-xs font-bold text-slate-700 mb-1.5">
									<?php esc_html_e('اختر المنتج المراد تخفيضه *', 'ashrafco'); ?>
								</label>
								<div class="relative">
									<input type="text" id="portal-add-product-search"
										placeholder="<?php esc_attr_e('ابحث بالاسم أو رمز SKU...', 'ashrafco'); ?>"
										class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl pr-9 pl-3 py-2.5 text-slate-800 placeholder-slate-400 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
									<svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
										fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
									</svg>
								</div>
								<!-- Live Results Dropdown -->
								<div id="portal-add-product-results"
									class="hidden max-h-48 overflow-y-auto mt-1.5 bg-white border border-slate-200 rounded-xl shadow-lg divide-y divide-slate-100 z-10">
								</div>
							</div>

							<!-- Selected Product Preview Box -->
							<div id="portal-add-product-preview"
								class="hidden p-3 bg-slate-50 border border-slate-200/90 rounded-2xl flex items-center justify-between gap-3 overflow-hidden">
								<div class="flex items-center gap-3 min-w-0 flex-1">
									<img id="portal-add-product-thumb" src="" alt=""
										class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0">
									<div class="min-w-0 flex-1">
										<div id="portal-add-product-name"
											class="text-xs font-black text-slate-900 truncate"></div>
										<div id="portal-add-product-sku"
											class="text-[11px] font-mono text-slate-400 mt-0.5"></div>
									</div>
								</div>
								<div class="text-left flex-shrink-0">
									<div class="text-[10px] text-slate-400 font-bold">
										<?php esc_html_e('السعر الأصلي', 'ashrafco'); ?>
									</div>
									<div id="portal-add-regular-price-display"
										class="text-xs font-black text-slate-800 whitespace-nowrap"></div>
								</div>
							</div>

							<!-- Warning if already discounted -->
							<div id="portal-add-existing-warning"
								class="hidden p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-2 text-xs font-bold text-amber-800">
								<svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
								<span><?php esc_html_e('هذا المنتج يحتوي بالفعل على تخفيض نشط أو مجدول. متابعة الحفظ ستحدث بيانات خصمه الحالية.', 'ashrafco'); ?></span>
							</div>

							<!-- 2. Sale Price & Discount % Preview -->
							<div class="grid grid-cols-2 gap-3">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('السعر بعد الخصم (ج.م) *', 'ashrafco'); ?>
									</label>
									<input type="number" step="any" min="1" id="portal-add-sale-price" required
										placeholder="مثال: 3450"
										class="w-full text-xs font-black bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
								</div>
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('نسبة الخصم المحسوبة', 'ashrafco'); ?>
									</label>
									<div class="h-10 flex items-center">
										<span id="portal-add-discount-percent-badge"
											class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-black bg-slate-100 text-slate-400 border border-slate-200 transition-all">
											%0
										</span>
									</div>
								</div>
							</div>

							<!-- 3. Optional Sale Dates -->
							<div class="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ البدء (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="date" id="portal-add-sale-start"
										class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
									<p class="text-[10px] text-slate-400 mt-0.5">
										<?php esc_html_e('اتركه فارغاً للتفعيل الفوري', 'ashrafco'); ?>
									</p>
								</div>
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ الانتهاء (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="date" id="portal-add-sale-end"
										class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
									<p class="text-[10px] text-slate-400 mt-0.5">
										<?php esc_html_e('اتركه فارغاً لعرض دائم بدون عداد', 'ashrafco'); ?>
									</p>
								</div>
							</div>

							<!-- Modal Footer -->
							<div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
								<button type="button" id="portal-add-discount-cancel"
									class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
									<?php esc_html_e('إلغاء', 'ashrafco'); ?>
								</button>
								<button type="submit" id="portal-add-discount-submit"
									class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-xs cursor-pointer">
									<span><?php esc_html_e('حفظ وتفعيل الخصم', 'ashrafco'); ?></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- 7. Edit Discount Modal (Dialog) -->
			<div id="portal-edit-discount-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"></div>
				<div class="min-h-full flex items-center justify-center p-4">
					<div
						class="relative bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-right">
						<!-- Modal Header -->
						<div class="flex items-center justify-between pb-4 border-b border-slate-100">
							<button type="button" id="portal-edit-discount-close"
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
											d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
									</svg>
								</div>
								<h3 class="text-base font-black text-slate-900">
									<?php esc_html_e('تعديل خصم المنتج', 'ashrafco'); ?>
								</h3>
							</div>
						</div>

						<!-- Form -->
						<form id="portal-edit-discount-form" class="space-y-4">
							<input type="hidden" id="portal-edit-product-id" value="">

							<!-- Selected Product Static Box -->
							<div
								class="p-3 bg-slate-50 border border-slate-200/90 rounded-2xl flex items-center justify-between gap-3 overflow-hidden">
								<div class="flex items-center gap-3 min-w-0 flex-1">
									<img id="portal-edit-product-thumb" src="" alt=""
										class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0">
									<div class="min-w-0 flex-1">
										<div id="portal-edit-product-name"
											class="text-xs font-black text-slate-900 truncate"></div>
										<div id="portal-edit-product-sku"
											class="text-[11px] font-mono text-slate-400 mt-0.5"></div>
									</div>
								</div>
								<div class="text-left flex-shrink-0">
									<div class="text-[10px] text-slate-400 font-bold">
										<?php esc_html_e('السعر الأصلي', 'ashrafco'); ?>
									</div>
									<div id="portal-edit-regular-price-display"
										class="text-xs font-black text-slate-800 whitespace-nowrap"></div>
								</div>
							</div>

							<!-- Sale Price & Discount % Preview -->
							<div class="grid grid-cols-2 gap-3">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('السعر بعد الخصم (ج.م) *', 'ashrafco'); ?>
									</label>
									<input type="number" step="any" min="1" id="portal-edit-sale-price" required
										class="w-full text-xs font-black bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-900 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
								</div>
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('نسبة الخصم المحسوبة', 'ashrafco'); ?>
									</label>
									<div class="h-10 flex items-center">
										<span id="portal-edit-discount-percent-badge"
											class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-black bg-rose-50 text-rose-600 border border-rose-200/80 transition-all">
											%0
										</span>
									</div>
								</div>
							</div>

							<!-- Optional Sale Dates -->
							<div class="grid grid-cols-2 gap-3 pt-1 border-t border-slate-100">
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ البدء (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="date" id="portal-edit-sale-start"
										class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
								</div>
								<div>
									<label class="block text-xs font-bold text-slate-700 mb-1">
										<?php esc_html_e('تاريخ الانتهاء (اختياري)', 'ashrafco'); ?>
									</label>
									<input type="date" id="portal-edit-sale-end"
										class="w-full text-xs font-semibold bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
								</div>
							</div>

							<!-- Modal Footer -->
							<div class="flex items-center justify-between pt-3 border-t border-slate-100">
								<button type="button" id="portal-edit-remove-btn"
									class="px-3.5 py-2.5 rounded-xl text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-rose-200/80 transition-colors cursor-pointer">
									<?php esc_html_e('إزالة الخصم', 'ashrafco'); ?>
								</button>
								<div class="flex items-center gap-2">
									<button type="button" id="portal-edit-discount-cancel"
										class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
										<?php esc_html_e('إلغاء', 'ashrafco'); ?>
									</button>
									<button type="submit" id="portal-edit-discount-submit"
										class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-xs cursor-pointer">
										<span><?php esc_html_e('حفظ التعديلات', 'ashrafco'); ?></span>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- 8. Remove Discount Confirmation Modal -->
			<div id="portal-remove-discount-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity"></div>
				<div class="min-h-full flex items-center justify-center p-4">
					<div
						class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 text-right">
						<div
							class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</div>
						<div class="text-center">
							<h3 class="text-base font-black text-slate-900">
								<?php esc_html_e('تأكيد إزالة الخصم', 'ashrafco'); ?>
							</h3>
							<p class="text-xs text-slate-500 font-medium mt-1">
								<?php esc_html_e('هل أنت متأكد من رغبتك في إزالة الخصم عن المنتج:', 'ashrafco'); ?>
							</p>
							<p id="portal-remove-product-name"
								class="text-xs font-extrabold text-slate-800 mt-1 font-sans"></p>
							<p class="text-[11px] text-slate-400 mt-2">
								<?php esc_html_e('سيتم مسح سعر التخفيض واستعادة السعر الأصلي، مع الحفاظ الكامل على بيانات المخزون والمواصفات وباقي بيانات المنتج.', 'ashrafco'); ?>
							</p>
						</div>
						<div class="flex items-center justify-center gap-3 pt-2">
							<button type="button" id="portal-remove-discount-cancel"
								class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
								<?php esc_html_e('إلغاء', 'ashrafco'); ?>
							</button>
							<button type="button" id="portal-remove-discount-confirm"
								class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
								<span><?php esc_html_e('نعم، إزالة الخصم', 'ashrafco'); ?></span>
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>

