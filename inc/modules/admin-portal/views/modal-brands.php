<?php
/**
 * Admin Portal View: Brand Management Modal & Sub-Modals
 *
 * Provides:
 * - Brand Listing with live counts
 * - Adding new brands
 * - Assigning / Unassigning products to a brand
 * - Safe or Full Deletion of brands (Option to unlink products or delete products)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>

<!-- =============================================================
     1. Brand Management Master Modal
     ============================================================= -->
<div id="brand-management-modal"
	class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
	aria-labelledby="brand-modal-title" role="dialog" aria-modal="true">
	<div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl border border-slate-200 overflow-hidden my-6 max-h-[92vh] flex flex-col transition-all">

		<!-- Modal Header -->
		<div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
			<div class="flex items-center gap-3">
				<div class="w-10 h-10 rounded-2xl bg-rose-50 text-primary border border-rose-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
					</svg>
				</div>
				<div>
					<h3 id="brand-modal-title" class="text-base font-black text-slate-900 leading-tight">
						إدارة العلامات التجارية (الماركات)
					</h3>
					<p class="text-xs text-slate-500 font-medium mt-0.5">
						إضافة وحذف الماركات التجارية وإدارة المنتجات المرتبطة بها في المتجر.
					</p>
				</div>
			</div>
			<button type="button" onclick="closeBrandsModal()"
				class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer"
				aria-label="إغلاق">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<!-- Modal Body -->
		<div class="overflow-y-auto flex-1 p-4 sm:p-6 space-y-5">

			<!-- View 1: Main Brands Management List -->
			<div id="brands-view-list" class="space-y-5">

				<!-- Quick Add Brand Box -->
				<div class="bg-slate-50/80 rounded-2xl border border-slate-200/80 p-4 sm:p-5">
					<h4 class="text-xs font-bold text-slate-800 mb-2.5 flex items-center gap-2">
						<span class="w-2 h-2 rounded-full bg-primary inline-block"></span>
						<span>إضافة علامة تجارية جديدة</span>
					</h4>
					<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
						<div class="relative flex-1">
							<input type="text" id="brand-new-name-input"
								placeholder="اكتب اسم العلامة التجارية (مثال: Bosch أو Ingco)..."
								onkeydown="if(event.key==='Enter'){event.preventDefault();handleAddNewBrand();}"
								class="w-full text-xs font-medium py-2.5 px-3.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 transition-colors">
						</div>
						<button type="button" id="brand-new-submit-btn" onclick="handleAddNewBrand()"
							class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-2xs cursor-pointer flex-shrink-0">
							<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
							</svg>
							<span>إضافة العلامة</span>
						</button>
					</div>
					<div id="brand-add-feedback" class="hidden mt-2 text-xs font-medium"></div>
				</div>

				<!-- Search & Count Bar -->
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
					<div class="relative flex-1 max-w-sm">
						<input type="text" id="brand-filter-search" oninput="filterBrandsList()"
							placeholder="ابحث في العلامات التجارية..."
							class="w-full text-xs font-medium py-2 pr-9 pl-3.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 transition-colors">
						<span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
						</span>
					</div>
					<div class="text-xs text-slate-500 font-bold flex items-center gap-1.5 self-end sm:self-auto">
						<span>إجمالي العلامات:</span>
						<span id="brands-total-counter" class="text-slate-900 font-mono font-bold bg-slate-100 px-2 py-0.5 rounded-md text-[11px]">0</span>
					</div>
				</div>

				<!-- Brands Table Container -->
				<div class="border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs bg-white">
					<div class="overflow-x-auto max-h-[380px] overflow-y-auto">
						<table class="w-full text-right border-collapse">
							<thead class="bg-slate-50/80 sticky top-0 z-10 border-b border-slate-200 text-slate-600 text-[11px] font-extrabold uppercase">
								<tr>
									<th class="py-3 px-4">العلامة التجارية</th>
									<th class="py-3 px-4 text-center">المنتجات المرتبطة</th>
									<th class="py-3 px-4 text-left">الإجراءات</th>
								</tr>
							</thead>
							<tbody id="brands-table-tbody" class="divide-y divide-slate-100 text-xs">
								<!-- Populated dynamically via JavaScript -->
							</tbody>
						</table>
					</div>

					<!-- Empty State -->
					<div id="brands-empty-state" class="hidden py-10 px-4 text-center">
						<div class="w-12 h-12 mx-auto rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mb-2.5">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
									d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
							</svg>
						</div>
						<p class="text-xs font-bold text-slate-700">لا توجد أي علامات تجارية مطابقة</p>
						<p class="text-[11px] text-slate-400 mt-0.5">يمكنك إضافة علامة تجارية جديدة من الحقل أعلاه.</p>
					</div>
				</div>

			</div>

			<!-- View 2: Brand Products Assignment Sub-View (Hidden initially) -->
			<div id="brands-view-products" class="hidden space-y-4">

				<!-- Back & Title Header -->
				<div class="flex items-center justify-between pb-3 border-b border-slate-100">
					<div class="flex items-center gap-2">
						<button type="button" onclick="backToBrandsList()"
							class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-xl transition-colors cursor-pointer">
							<svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
							</svg>
							<span>رجوع للماركات</span>
						</button>
						<span class="text-slate-300">|</span>
						<h4 class="text-xs sm:text-sm font-bold text-slate-800">
							المنتجات التابعة لعلامة:
							<span id="active-brand-name-display" class="text-primary font-black"></span>
							<span id="active-brand-count-badge" class="font-mono text-[11px] bg-rose-50 text-primary border border-rose-100 px-2 py-0.5 rounded-lg mr-1.5 font-bold">0</span>
						</h4>
					</div>
				</div>

				<!-- Product Search & Assign Box -->
				<div class="bg-slate-50/80 rounded-2xl border border-slate-200/80 p-4 space-y-2">
					<label class="block text-xs font-bold text-slate-700">
						إضافة منتجات لهذه العلامة التجارية:
					</label>
					<div class="relative">
						<input type="text" id="brand-product-search-input"
							placeholder="ابحث عن منتج بالاسم أو رمز SKU لتعيينه لهذه الماركة..."
							oninput="debounceBrandProductSearch()"
							class="w-full text-xs font-medium py-2.5 pr-9 pl-3.5 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 transition-colors">
						<span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
						</span>
					</div>

					<!-- Search Results Dropdown/Box -->
					<div id="brand-product-search-results"
						class="hidden max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-xl divide-y divide-slate-100 shadow-sm text-xs">
						<!-- Populated via AJAX search -->
					</div>
				</div>

				<!-- Assigned Products List -->
				<div>
					<h5 class="text-xs font-bold text-slate-700 mb-2">
						المنتجات المرتبطة حالياً (<span id="assigned-products-counter">0</span>):
					</h5>
					<div class="border border-slate-200/90 rounded-2xl overflow-hidden bg-white">
						<div id="assigned-products-container" class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto">
							<!-- Populated via AJAX -->
						</div>
						<div id="assigned-products-empty" class="hidden py-8 px-4 text-center">
							<p class="text-xs font-medium text-slate-400">لا توجد منتجات مرتبطة بهذه الماركة حالياً.</p>
							<p class="text-[11px] text-slate-400 mt-1">استخدم حقل البحث أعلاه لربط المنتجات بهذه العلامة التجارية.</p>
						</div>
					</div>
				</div>

			</div>

		</div>

		<!-- Modal Footer -->
		<div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between flex-shrink-0">
			<span class="text-[11px] text-slate-400">تُحفظ كافة التغييرات على الماركات والمنتجات مباشرة في المتجر.</span>
			<button type="button" onclick="closeBrandsModal()"
				class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors shadow-2xs cursor-pointer">
				إغلاق
			</button>
		</div>

	</div>
</div>

<!-- =============================================================
     2. Brand Deletion Confirmation Dialog (High Z-Index)
     ============================================================= -->
<div id="brand-delete-confirm-modal"
	class="fixed inset-0 z-[60] hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
	role="dialog" aria-modal="true">
	<div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden my-6 flex flex-col transition-all">

		<!-- Dialog Header -->
		<div class="p-5 border-b border-slate-100 flex items-center gap-3 bg-rose-50/40">
			<div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
				</svg>
			</div>
			<div>
				<h4 class="text-sm font-black text-slate-900">
					تأكيد حذف العلامة التجارية
				</h4>
				<p class="text-xs text-slate-500 font-medium">
					العلامة: <strong id="delete-brand-title" class="text-slate-800"></strong>
				</p>
			</div>
		</div>

		<!-- Dialog Body -->
		<div class="p-5 space-y-4">
			<input type="hidden" id="delete-target-brand-id" value="0">
			<input type="hidden" id="delete-target-brand-count" value="0">

			<!-- Case 1: Brand has products attached -->
			<div id="delete-mode-with-products" class="space-y-3">
				<div class="p-3 bg-amber-50 border border-amber-200/70 rounded-xl text-xs text-amber-800 font-medium">
					تحتوي هذه العلامة على <strong id="delete-brand-products-count" class="font-mono font-bold text-rose-600">0</strong> منتج مرتبط بها.
					يرجى تحديد خيار الحذف المناسب:
				</div>

				<div class="space-y-2.5">
					<!-- Option 1: Unlink Only (Safe) -->
					<label class="flex items-start gap-3 p-3.5 border border-slate-200 rounded-xl hover:border-slate-300 hover:bg-slate-50/50 cursor-pointer transition-colors">
						<input type="radio" name="brand_delete_option" value="unlink" checked
							class="mt-1 text-primary focus:ring-primary h-4 w-4 border-slate-300">
						<div class="min-w-0">
							<span class="block text-xs font-bold text-slate-900">
								حذف العلامة التجارية فقط (فك ارتباط المنتجات)
							</span>
							<span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed">
								تُحذف العلامة التجارية فقط، وتظل جميع المنتجات (<span class="delete-brand-num-placeholder font-mono font-bold">0</span> منتج) موجودة في المتجر كما هي دون ماركة.
							</span>
						</div>
					</label>

					<!-- Option 2: Delete Brand & Products (Destructive) -->
					<label class="flex items-start gap-3 p-3.5 border border-rose-200 bg-rose-50/20 rounded-xl hover:border-rose-300 hover:bg-rose-50/40 cursor-pointer transition-colors">
						<input type="radio" name="brand_delete_option" value="delete_all"
							class="mt-1 text-rose-600 focus:ring-rose-500 h-4 w-4 border-slate-300">
						<div class="min-w-0">
							<span class="block text-xs font-black text-rose-700">
								حذف العلامة التجارية مع جميع منتجاتها نهائياً
							</span>
							<span class="block text-[11px] text-rose-600/90 mt-0.5 leading-relaxed">
								تحذير: سيتم حذف جميع الـ (<span class="delete-brand-num-placeholder font-mono font-bold">0</span> منتج) نهائياً من المتجر وقاعدة البيانات ولا يمكن التراجع.
							</span>
						</div>
					</label>
				</div>
			</div>

			<!-- Case 2: Brand has 0 products -->
			<div id="delete-mode-zero-products" class="hidden text-xs text-slate-600 font-medium">
				هل أنت متأكد من رغبتك في حذف هذه العلامة التجارية نهائياً؟
				لا توجد أي منتجات مرتبطة بها حالياً.
			</div>

			<div id="delete-brand-feedback" class="hidden text-xs font-medium p-2.5 rounded-xl"></div>
		</div>

		<!-- Dialog Footer -->
		<div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-2.5">
			<button type="button" onclick="closeDeleteBrandModal()"
				class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
				إلغاء
			</button>
			<button type="button" id="confirm-delete-brand-btn" onclick="executeBrandDeletion()"
				class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs cursor-pointer">
				<span id="delete-btn-text">تأكيد الحذف</span>
			</button>
		</div>

	</div>
</div>
