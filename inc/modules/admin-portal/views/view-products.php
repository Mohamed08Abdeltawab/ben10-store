<?php
/**
 * Admin Portal View: Categories & Products Manager (Tab 3)
 *
 * Includes Product CRUD Form, Category Products Drill-Down,
 * Category Management Dashboard, and Category Modals.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 3: إدارة المنتجات والمخزون (Products Manager)
			 ================================================================= -->
		<div id="portal-tab-products"
			class="tab-content <?php echo 'products' === $active_tab ? 'active' : ''; ?> space-y-6">

			<?php if ($is_form_mode): ?>
				<!-- =============================================================
					 SUB-VIEW: نموذج إضافة / تعديل المنتج (Product Form)
					 ============================================================= -->
				<!-- =============================================================
					 SUB-VIEW: نموذج إضافة / تعديل المنتج (Product Form - Reference Match)
					 ============================================================= -->
				<div class="w-full space-y-6">

					<!-- Breadcrumbs, Heading and Back Navigation Action -->
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
						<div>
							<!-- Breadcrumbs -->
							<div class="flex items-center gap-2 text-xs text-slate-400 mb-1.5 flex-wrap font-medium">
								<a href="<?php echo esc_url(add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
									class="hover:text-primary transition-colors">
									إدارة التصنيفات
								</a>
								<?php if ($selected_category && !is_wp_error($selected_category)): ?>
									<span>/</span>
									<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param), home_url('/admin-portal/'))); ?>"
										class="hover:text-primary transition-colors font-medium">
										<?php echo esc_html($selected_category->name); ?>
									</a>
								<?php endif; ?>
								<span>/</span>
								<span class="text-slate-700 font-bold">
									<?php echo $is_edit_mode ? 'تعديل المنتج' : 'إضافة منتج جديد'; ?>
								</span>
							</div>

							<!-- Page Heading -->
							<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
								<?php echo $is_edit_mode ? sprintf(esc_html__('تعديل المنتج: %s', 'ashrafco'), esc_html($f_name)) : esc_html__('إضافة منتج جديد للمتجر', 'ashrafco'); ?>
							</h1>
						</div>

						<!-- Back to Category Products Action -->
						<a href="<?php echo esc_url(($cat_id_param && $selected_category) ? add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param), home_url('/admin-portal/')) : add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
							class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-2xs select-none self-start sm:self-auto flex-shrink-0">
							<svg class="w-4 h-4 rotate-180 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M10 19l-7-7m0 0l7-7m-7 7h18" />
							</svg>
							<span><?php echo ($cat_id_param && $selected_category) ? sprintf(esc_html__('العودة إلى منتجات %s', 'ashrafco'), esc_html($selected_category->name)) : esc_html__('العودة إلى منتجات التصنيف', 'ashrafco'); ?></span>
						</a>
					</div>

					<!-- Main Product Form -->
					<form id="ashrafco-product-form" method="POST"
						action="<?php echo esc_url(home_url('/admin-portal/')); ?>" enctype="multipart/form-data"
						class="w-full">
						<?php wp_nonce_field('ashrafco_product_form_nonce', '_wpnonce'); ?>
						<input type="hidden" name="ashrafco_product_action"
							value="<?php echo $is_edit_mode ? 'edit' : 'new'; ?>">
						<input type="hidden" name="product_id" value="<?php echo esc_attr($edit_product_id); ?>">
						<input type="hidden" name="original_product_status" id="original_product_status"
							value="<?php echo esc_attr($f_status); ?>">
						<input type="hidden" name="redirect_cat_id" value="<?php echo esc_attr($cat_id_param); ?>">
						<input type="hidden" name="media_image_id" id="portal_media_image_id"
							value="<?php echo esc_attr($f_thumb_id); ?>">
						<input type="hidden" name="gallery_image_ids" id="portal_gallery_image_ids"
							value="<?php echo esc_attr(implode(',', $f_gallery_ids)); ?>">
						<input type="hidden" name="remove_image" id="portal_remove_image_flag" value="0">
						<input type="hidden" name="media_file_id" id="portal_media_file_id"
							value="<?php echo esc_attr($f_file_id); ?>">
						<input type="hidden" name="remove_product_file" id="portal_remove_file_flag" value="0">
						<input type="hidden" name="sale_expiry_type" id="sale_expiry_type_input"
							value="<?php echo esc_attr($f_sale_expiry_type); ?>">
						<input type="hidden" name="stock_status" id="stock_status_input"
							value="<?php echo esc_attr($f_stock_status); ?>">

						<!-- Hidden direct upload inputs -->
						<input type="file" id="portal_direct_image_input" name="product_image" accept="image/*"
							class="hidden">
						<input type="file" id="portal_direct_file_input" name="product_file" accept=".pdf,.zip,.doc,.docx"
							class="hidden">

						<!-- Two-Column Responsive Grid (Main col 8 on right in RTL, Sidebar col 4 on left in RTL) -->
						<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

							<!-- =========================================================
								 MAIN COLUMN: Basic Info, Pricing, Specs, Description (col-span-8)
								 ========================================================= -->
							<div class="lg:col-span-8 space-y-6 w-full min-w-0 order-2 lg:order-1">

								<!-- 1. البيانات الأساسية للمنتج -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-5">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
												</svg>
											</span>
											<h3 class="text-sm sm:text-base font-extrabold text-slate-900">البيانات الأساسية
												للمنتج</h3>
										</div>
										<span
											class="w-5 h-5 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help"
											title="البيانات التعريفية والتصنيفية الأساسية للمنتج">?</span>
									</div>

									<div class="space-y-4">
										<div>
											<label for="product_name" class="block text-xs font-bold text-slate-700 mb-1.5">
												اسم المنتج <span class="text-rose-500">*</span>
											</label>
											<input type="text" id="product_name" name="product_name"
												value="<?php echo esc_attr($f_name); ?>" required
												placeholder="مثال: شنطة عدد وتركيب بلاط متكاملة مع كافة ملحقات تسوية وتثبيت"
												class="w-full text-sm font-bold py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-rose-500/20 transition-all text-slate-900">
										</div>

										<div>
											<label for="product_short_description"
												class="block text-xs font-bold text-slate-700 mb-1.5">
												وصف مختصر للمنتج
											</label>
											<textarea id="product_short_description" name="product_short_description"
												rows="3"
												placeholder="مجموعة كاملة أدوات تسوية وتثبيت البلاط مناسبة للاستخدام المنزلي والاحترافي."
												class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-rose-500/20 transition-all text-slate-800 leading-relaxed"><?php echo esc_textarea($f_short_desc); ?></textarea>
										</div>

										<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
											<!-- التصنيف الرئيسي (Right in RTL) -->
											<div class="order-1 sm:order-2">
												<label for="product_category_main"
													class="block text-xs font-bold text-slate-700 mb-1.5">
													التصنيف الرئيسي <span class="text-rose-500">*</span>
												</label>
												<select id="product_category_main" name="product_categories[]" required
													class="w-full text-xs font-bold py-2.5 pr-4 pl-10 bg-slate-50/60 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 cursor-pointer">
													<option value="">اختر التصنيف الرئيسي للمنتج...</option>
													<?php foreach ($all_product_cats as $cat_term): ?>
														<option value="<?php echo esc_attr($cat_term->term_id); ?>" <?php echo in_array($cat_term->term_id, $f_categories, true) || ($cat_term->term_id === $cat_id_param) ? 'selected' : ''; ?>>
															<?php echo esc_html($cat_term->name); ?>
															(<?php echo esc_html($cat_term->count); ?>)
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<!-- رمز المنتج SKU (Left in RTL) -->
											<div class="order-2 sm:order-1">
												<label for="product_sku"
													class="block text-xs font-bold text-slate-700 mb-1.5">
													رمز المنتج (SKU)
												</label>
												<input type="text" id="product_sku" name="product_sku"
													value="<?php echo esc_attr($f_sku); ?>"
													placeholder="مثال: ASH-KIT-TILE-PRO"
													class="w-full text-xs font-mono py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
											</div>
										</div>
									</div>
								</div>

								<!-- 2. التسعير وإدارة المخزون -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-5">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
												</svg>
											</span>
											<h3 class="text-sm sm:text-base font-extrabold text-slate-900">التسعير وإدارة
												المخزون</h3>
										</div>
										<span
											class="w-5 h-5 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help"
											title="إعدادات التسعير وسعر الخصم وجدولته وتتبع المستودع">?</span>
									</div>

									<!-- 3 Pricing Fields in Row -->
									<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-start">
										<!-- السعر الأساسي -->
										<div>
											<label for="regular_price"
												class="block text-xs font-bold text-slate-700 mb-1.5">
												السعر الأساسي (ج.م) <span class="text-rose-500">*</span>
											</label>
											<div class="relative w-full">
												<input type="text" id="regular_price" name="regular_price"
													value="<?php echo esc_attr($f_regular_price); ?>" required
													placeholder="1700"
													class="w-full text-sm font-bold font-mono py-2.5 pr-3.5 pl-12 bg-slate-50/60 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-900">
												<span
													class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">ج.م</span>
											</div>
										</div>

										<!-- سعر التخفيض -->
										<div>
											<label for="sale_price" class="block text-xs font-bold text-slate-700 mb-1.5">
												سعر التخفيض (ج.م)
											</label>
											<div class="relative w-full">
												<input type="text" id="sale_price" name="sale_price"
													value="<?php echo esc_attr($f_sale_price); ?>" placeholder="1450"
													class="w-full text-sm font-bold font-mono py-2.5 pr-3.5 pl-12 bg-slate-50/60 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-primary">
												<span
													class="absolute left-3 top-2.5 text-xs text-slate-400 font-bold pointer-events-none">ج.م</span>
											</div>
										</div>

										<!-- تاريخ انتهاء التخفيض + خيار غير محدد الصريح -->
										<div>
											<div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
												<label for="sale_end_date" class="block text-xs font-bold text-slate-700">
													تاريخ نهاية التخفيض
												</label>
												<!-- Explicit None / Scheduled Switcher -->
												<div
													class="inline-flex items-center p-0.5 bg-slate-100 rounded-lg text-[10px] font-bold">
													<button type="button" id="sale_expiry_btn_none"
														onclick="portalSetSaleExpiryMode('none')"
														class="px-2 py-0.5 rounded-md transition-all <?php echo 'none' === $f_sale_expiry_type ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-700'; ?>">
														غير محدد
													</button>
													<button type="button" id="sale_expiry_btn_scheduled"
														onclick="portalSetSaleExpiryMode('scheduled')"
														class="px-2 py-0.5 rounded-md transition-all <?php echo 'scheduled' === $f_sale_expiry_type ? 'bg-white text-slate-900 shadow-2xs font-extrabold' : 'text-slate-500 hover:text-slate-700'; ?>">
														تحديد تاريخ
													</button>
												</div>
											</div>
											<div class="relative w-full">
												<input type="date" id="sale_end_date" name="sale_end_date"
													value="<?php echo esc_attr($f_sale_end_date); ?>" <?php echo 'none' === $f_sale_expiry_type ? 'disabled' : ''; ?>
													class="w-full text-xs font-bold py-2.5 pr-3.5 pl-9 bg-slate-50/60 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed">
												<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
													fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
														d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
												</svg>
											</div>
										</div>
									</div>

									<!-- Inventory Stock Controls -->
									<div class="pt-4 border-t border-slate-100 space-y-4">
										<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
											<div>
												<label class="inline-flex items-center gap-2 cursor-pointer select-none">
													<input type="checkbox" id="manage_stock" name="manage_stock" value="1"
														<?php checked($f_manage_stock, true); ?>
														onchange="portalToggleStockQuantity(this.checked)"
														class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-slate-300">
													<span class="text-xs font-bold text-slate-800">تفعيل تتبع المخزون</span>
												</label>
											</div>

											<div id="stock_quantity_wrapper"
												class="<?php echo $f_manage_stock ? '' : 'hidden'; ?>">
												<label for="stock_quantity"
													class="block text-xs font-bold text-slate-700 mb-1">
													الكمية المتوفرة بالمستودع
												</label>
												<div class="relative w-full max-w-xs">
													<input type="number" id="stock_quantity" name="stock_quantity"
														value="<?php echo esc_attr($f_stock_qty); ?>" min="0"
														placeholder="0"
														class="w-full text-xs font-bold font-mono py-2.5 pr-3.5 pl-9 bg-slate-50/60 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
													<svg class="w-4 h-4 text-slate-400 absolute left-3 top-3 pointer-events-none"
														fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
															stroke-width="1.8"
															d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
													</svg>
												</div>
											</div>
										</div>

										<!-- حالة التوفر بالمخزون: Segmented Control Matching Reference -->
										<div class="pt-2">
											<label class="block text-xs font-bold text-slate-700 mb-2">
												حالة التوفر بالمخزون
											</label>
											<div class="flex items-center gap-2 flex-wrap" id="portal-stock-status-group"
												role="radiogroup">
												<!-- 1. متوفر بالمخزون -->
												<button type="button" onclick="portalSetStockStatus('instock')"
													data-status="instock"
													class="stock-status-pill rounded-xl px-4 py-2 font-bold text-xs sm:text-sm transition-all select-none flex items-center gap-2 cursor-pointer border <?php echo 'instock' === $f_stock_status ? 'bg-emerald-50 text-emerald-800 border-emerald-300 shadow-2xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'; ?>">
													<span
														class="w-2.5 h-2.5 rounded-full border border-current flex items-center justify-center flex-shrink-0">
														<span
															class="w-1.5 h-1.5 rounded-full <?php echo 'instock' === $f_stock_status ? 'bg-emerald-600' : 'bg-transparent'; ?>"></span>
													</span>
													<span>متوفر بالمخزون</span>
												</button>

												<!-- 2. مخزون منخفض -->
												<button type="button" onclick="portalSetStockStatus('lowstock')"
													data-status="lowstock"
													class="stock-status-pill rounded-xl px-4 py-2 font-bold text-xs sm:text-sm transition-all select-none flex items-center gap-2 cursor-pointer border <?php echo 'lowstock' === $f_stock_status ? 'bg-amber-50 text-amber-800 border-amber-300 shadow-2xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'; ?>">
													<span
														class="w-2.5 h-2.5 rounded-full border border-current flex items-center justify-center flex-shrink-0">
														<span
															class="w-1.5 h-1.5 rounded-full <?php echo 'lowstock' === $f_stock_status ? 'bg-amber-600' : 'bg-transparent'; ?>"></span>
													</span>
													<span>مخزون منخفض</span>
												</button>

												<!-- 3. نفذ المخزون -->
												<button type="button" onclick="portalSetStockStatus('outofstock')"
													data-status="outofstock"
													class="stock-status-pill rounded-xl px-4 py-2 font-bold text-xs sm:text-sm transition-all select-none flex items-center gap-2 cursor-pointer border <?php echo 'outofstock' === $f_stock_status ? 'bg-rose-50 text-rose-800 border-rose-300 shadow-2xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'; ?>">
													<span
														class="w-2.5 h-2.5 rounded-full border border-current flex items-center justify-center flex-shrink-0">
														<span
															class="w-1.5 h-1.5 rounded-full <?php echo 'outofstock' === $f_stock_status ? 'bg-rose-600' : 'bg-transparent'; ?>"></span>
													</span>
													<span>نفذ المخزون</span>
												</button>

												<!-- 4. طلب مسبق -->
												<button type="button" onclick="portalSetStockStatus('onbackorder')"
													data-status="onbackorder"
													class="stock-status-pill rounded-xl px-4 py-2 font-bold text-xs sm:text-sm transition-all select-none flex items-center gap-2 cursor-pointer border <?php echo 'onbackorder' === $f_stock_status ? 'bg-slate-100 text-slate-900 border-slate-300 shadow-2xs font-extrabold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'; ?>">
													<span
														class="w-2.5 h-2.5 rounded-full border border-current flex items-center justify-center flex-shrink-0">
														<span
															class="w-1.5 h-1.5 rounded-full <?php echo 'onbackorder' === $f_stock_status ? 'bg-slate-700' : 'bg-transparent'; ?>"></span>
													</span>
													<span>طلب مسبق</span>
												</button>
											</div>
											<p class="text-xs text-slate-500 mt-2 leading-relaxed">
												ملاحظة: «مخزون منخفض» حالة عرض تُحتسب تلقائياً من الكمية المتوفرة وحد التنبيه في WooCommerce، ويُحفظ المنتج كـ«متوفر بالمخزون».
											</p>
										</div>
									</div>
								</div>

								<!-- 3. المواصفات الأساسية (Basic Specifications) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-5">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M4 6h16M4 10h16M4 14h16M4 18h16" />
												</svg>
											</span>
											<h3 class="text-sm sm:text-base font-extrabold text-slate-900">المواصفات
												الأساسية</h3>
										</div>
										<span
											class="w-5 h-5 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help"
											title="المواصفات العامة الثابتة للمنتج مثل العلامة والموديل والضمان وبلد الصنع">?</span>
									</div>

									<!-- 3 Rows, 2 Columns (RTL) Matching Reference Screenshot -->
									<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

										<!-- Row 1: العلامة التجارية (Right) & موديل الشركة المصنّعة (Left) -->
										<div class="space-y-1.5">
											<div class="flex items-center justify-between">
												<label for="product_brand_select"
													class="block text-xs font-bold text-slate-700">
													العلامة التجارية
												</label>
												<button type="button" onclick="toggleNewBrandInput()"
													id="toggle-new-brand-btn"
													class="text-[11px] font-bold text-primary hover:underline cursor-pointer">
													+ إضافة علامة جديدة
												</button>
											</div>
											<div class="relative" id="product_brand_select_wrapper">
												<select id="product_brand_select" name="product_brand"
													onchange="handleBrandSelectChange(this)"
													class="w-full text-xs font-bold py-2.5 pr-4 pl-9 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 appearance-none cursor-pointer">
													<option value="">اختر العلامة التجارية...</option>
													<?php foreach ($all_portal_brands as $brand_item): ?>
														<option value="<?php echo esc_attr($brand_item['name']); ?>" <?php selected($f_brand, $brand_item['name']); ?>>
															<?php echo esc_html($brand_item['name']); ?>
														</option>
													<?php endforeach; ?>
													<?php if (!empty($f_brand) && !in_array($f_brand, array_column($all_portal_brands, 'name'), true)): ?>
														<option value="<?php echo esc_attr($f_brand); ?>" selected>
															<?php echo esc_html($f_brand); ?>
														</option>
													<?php endif; ?>
													<option value="custom">+ علامة تجارية أخرى...</option>
												</select>
												<span
													class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
													<svg class="w-4 h-4" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
															stroke-width="2" d="M19 9l-7 7-7-7" />
													</svg>
												</span>
											</div>
											<!-- Inline input for adding custom brand -->
											<div id="product_brand_custom_wrapper" class="hidden mt-2">
												<div class="flex items-center gap-2">
													<input type="text" id="product_brand_custom" name="product_brand_custom"
														placeholder="اكتب اسم العلامة التجارية..."
														class="w-full text-xs font-medium py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
													<button type="button" onclick="saveQuickNewBrand()"
														class="px-3 py-2 text-xs font-bold text-white bg-primary rounded-xl hover:bg-[#b90323] whitespace-nowrap cursor-pointer">
														تأكيد
													</button>
													<button type="button" onclick="cancelNewBrandInput()"
														class="px-2.5 py-2 text-xs font-bold text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 whitespace-nowrap cursor-pointer">
														إلغاء
													</button>
												</div>
											</div>
										</div>

										<div class="space-y-1.5">
											<label for="product_manufacturer_model"
												class="block text-xs font-bold text-slate-700">
												موديل الشركة المصنّعة
											</label>
											<input type="text" id="product_manufacturer_model"
												name="product_manufacturer_model" value="<?php echo esc_attr($f_model); ?>"
												placeholder="ASH-KIT-TILE-PRO"
												class="w-full text-xs font-mono py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
										</div>

										<!-- Row 2: فترة الضمان (Right) & محتويات العبوة (Left) -->
										<div class="space-y-1.5">
											<label for="product_warranty" class="block text-xs font-bold text-slate-700">
												فترة الضمان
											</label>
											<div class="relative">
												<select id="product_warranty" name="product_warranty"
													onchange="handleWarrantyChange(this)"
													class="w-full text-xs font-bold py-2.5 pr-4 pl-9 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 appearance-none cursor-pointer">
													<?php
													$warranty_options = array(
														'بدون ضمان',
														'3 أشهر',
														'6 أشهر',
														'سنة واحدة',
														'سنتان',
														'3 سنوات',
														'ضمان مخصص',
													);
													$is_custom_warranty = !empty($f_warranty) && !in_array($f_warranty, $warranty_options, true);
													foreach ($warranty_options as $w_opt):
														$is_sel = ($f_warranty === $w_opt) || ('ضمان مخصص' === $w_opt && $is_custom_warranty);
														?>
														<option value="<?php echo esc_attr($w_opt); ?>" <?php echo $is_sel ? 'selected' : ''; ?>>
															<?php echo esc_html($w_opt); ?>
														</option>
													<?php endforeach; ?>
												</select>
												<span
													class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
													<svg class="w-4 h-4" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
															stroke-width="2" d="M19 9l-7 7-7-7" />
													</svg>
												</span>
											</div>
											<div id="product_warranty_custom_wrap"
												class="<?php echo $is_custom_warranty ? '' : 'hidden'; ?> mt-2">
												<input type="text" id="product_warranty_custom"
													name="product_warranty_custom"
													value="<?php echo esc_attr($is_custom_warranty ? $f_warranty : ''); ?>"
													placeholder="اكتب فترة أو شروط الضمان المخصص..."
													class="w-full text-xs font-medium py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
											</div>
										</div>

										<div class="space-y-1.5">
											<label for="product_package_contents"
												class="block text-xs font-bold text-slate-700">
												محتويات العبوة
											</label>
											<input type="text" id="product_package_contents" name="product_package_contents"
												value="<?php echo esc_attr($f_pkg_contents); ?>"
												placeholder="شنطة + أدوات تسوية وتثبيت + كلابش شفط + قواعد تثبيت + ملحقات إضافية"
												class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
										</div>

										<!-- Row 3: بلد المنشأ (Right) & الوزن التقريبي (Left) -->
										<div class="space-y-1.5">
											<label for="product_country_origin"
												class="block text-xs font-bold text-slate-700">
												بلد المنشأ
											</label>
											<div class="relative">
												<select id="product_country_origin" name="product_country_origin"
													onchange="handleCountryChange(this)"
													class="w-full text-xs font-bold py-2.5 pr-4 pl-9 bg-slate-50/60 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 appearance-none cursor-pointer">
													<?php
													$country_options = array(
														'الصين',
														'ألمانيا',
														'إيطاليا',
														'مصر (صناعة محلية)',
														'تركيا',
														'تايوان',
														'اليابان',
														'إسبانيا',
														'الولايات المتحدة',
														'أخرى',
													);
													$is_custom_country = !empty($f_country_origin) && !in_array($f_country_origin, $country_options, true);
													foreach ($country_options as $c_opt):
														$is_c_sel = ($f_country_origin === $c_opt) || ('أخرى' === $c_opt && $is_custom_country);
														?>
														<option value="<?php echo esc_attr($c_opt); ?>" <?php echo $is_c_sel ? 'selected' : ''; ?>>
															<?php echo esc_html($c_opt); ?>
														</option>
													<?php endforeach; ?>
												</select>
												<span
													class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
													<svg class="w-4 h-4" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round"
															stroke-width="2" d="M19 9l-7 7-7-7" />
													</svg>
												</span>
											</div>
											<div id="product_country_custom_wrap"
												class="<?php echo $is_custom_country ? '' : 'hidden'; ?> mt-2">
												<input type="text" id="product_country_origin_custom"
													name="product_country_origin_custom"
													value="<?php echo esc_attr($is_custom_country ? $f_country_origin : ''); ?>"
													placeholder="اكتب اسم بلد المنشأ..."
													class="w-full text-xs font-medium py-2 px-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800">
											</div>
										</div>

										<div class="space-y-1.5">
											<label for="product_approx_weight"
												class="block text-xs font-bold text-slate-700">
												الوزن التقريبي
											</label>
											<div
												class="flex items-stretch rounded-xl border border-slate-200 focus-within:border-primary overflow-hidden bg-white shadow-2xs">
												<input type="number" step="any" min="0" id="product_approx_weight"
													name="product_approx_weight"
													value="<?php echo esc_attr($f_approx_weight); ?>" placeholder="14"
													class="w-full text-xs font-bold py-2.5 px-3.5 bg-slate-50/60 hover:bg-white focus:bg-white focus:outline-none text-slate-800 text-right">
												<span
													class="bg-slate-100/80 border-r border-slate-200 text-slate-600 text-xs font-bold px-3.5 py-2.5 flex items-center justify-center select-none min-w-[52px]">
													كجم
												</span>
											</div>
										</div>

									</div>
								</div>

								<!-- 4. مواصفات التصنيف (Category Specifications) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-5">
									<div
										class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
												</svg>
											</span>
											<h3 class="text-sm sm:text-base font-extrabold text-slate-900">مواصفات التصنيف
											</h3>
											<span
												class="w-5 h-5 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help"
												title="المواصفات الفنية المحددة لهذا التصنيف وتُدار من شاشة إدارة التصنيفات">?</span>
										</div>
										<div id="portal-cat-specs-badge" class="text-xs text-slate-500 font-medium">
											اخترنا لك في هذا القالب مواصفات التصنيف (<strong id="portal-cat-name-display"
												class="text-slate-800 font-bold">---</strong>)
										</div>
									</div>

									<!-- Dynamic Category Specifications Container -->
									<div id="portal-category-specs-container">
										<!-- Populated dynamically via portalRenderCategorySpecs() -->
									</div>
								</div>

								<!-- 5. الوصف التفصيلي الكامل (Spacious Textarea matching design reference) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-4">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 border border-rose-100/60 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor"
													viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
												</svg>
											</span>
											<h3 class="text-sm sm:text-base font-extrabold text-slate-900">الوصف التفصيلي
												الكامل</h3>
										</div>
										<span
											class="w-6 h-6 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help shadow-2xs"
											title="الوصف التفصيلي الكامل للمنتج الذي يظهر في صفحة تفاصيل المنتج">?</span>
									</div>

									<!-- Spacious Single Textarea matching reference -->
									<textarea id="product_description" name="product_description" rows="8" dir="rtl"
										placeholder="أدخل الوصف التفصيلي الكامل للمنتج..."
										class="w-full min-h-[220px] p-4 sm:p-5 text-xs sm:text-sm font-medium text-slate-800 leading-relaxed bg-white border border-slate-200/90 rounded-2xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-rose-500/10 transition-all resize-y text-right"><?php echo esc_textarea($f_desc); ?></textarea>
								</div>

							</div>

							<!-- =========================================================
								 SIDEBAR COLUMN: Images, File, Features, Actions (col-span-4)
								 ========================================================= -->
							<div class="lg:col-span-4 space-y-6 w-full order-1 lg:order-2">

								<!-- 1. صور المنتج (Up to 15 images limit strictly enforced) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-4">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
												</svg>
											</span>
											<h3 class="text-sm font-extrabold text-slate-900">صور المنتج</h3>
										</div>
										<span
											class="w-5 h-5 rounded-full bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xs font-bold cursor-help"
											title="الحد الأقصى المسموح به هو 15 صورة للمنتج (شاملة الصورة الرئيسية ومعرض الصور)">?</span>
									</div>

									<!-- Large Main Image Preview Box with Badge -->
									<div
										class="aspect-square bg-slate-50 border border-slate-200 rounded-2xl relative overflow-hidden flex items-center justify-center p-3 group shadow-2xs">
										<span id="portal-main-image-badge"
											class="absolute top-3 right-3 bg-white/95 backdrop-blur-xs px-2.5 py-1 rounded-lg text-[10px] font-black text-slate-800 shadow-2xs border border-slate-200 flex items-center gap-1 z-10 <?php echo empty($f_thumb_url) ? 'hidden' : ''; ?>">
											<span>الصورة الرئيسية</span>
										</span>

										<img id="portal-main-image-preview" src="<?php echo esc_url($f_thumb_url); ?>"
											alt="الصورة الرئيسية للمنتج"
											class="max-h-full max-w-full object-contain <?php echo empty($f_thumb_url) ? 'hidden' : ''; ?>">

										<div id="portal-main-placeholder"
											class="text-center p-4 <?php echo !empty($f_thumb_url) ? 'hidden' : ''; ?>">
											<svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
													d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
											</svg>
											<span class="text-xs text-slate-400 font-bold block">لم يتم تحديد صورة رئيسية
												بعد</span>
										</div>
									</div>

									<!-- Thumbnails Row (Main Thumbnail + Gallery + Add Images Button) -->
									<div class="flex items-center gap-2 overflow-x-auto pb-1 mt-3.5 no-scrollbar"
										id="portal-thumbnails-container">
										<!-- Rendered dynamically by JavaScript -->
									</div>

									<!-- Caption & Image Count Badge -->
									<div
										class="flex items-center justify-between text-[11px] text-slate-400 font-medium pt-1">
										<span>يمكنك رفع عدة صور للمنتج (حتى 15 صورة)</span>
										<span id="portal-images-counter-badge" class="font-bold text-slate-700">0 /
											15</span>
									</div>

									<!-- 15 Images Limit Notice Alert -->
									<div id="portal-images-limit-alert"
										class="hidden p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold flex items-center gap-1.5">
										<svg class="w-4 h-4 flex-shrink-0 text-amber-600" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
										</svg>
										<span>تم بلوغ الحد الأقصى للصور (15 صورة شاملة الصورة الرئيسية).</span>
									</div>
								</div>

								<!-- 2. ملف المنتج (اختياري) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-4">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
												</svg>
											</span>
											<h3 class="text-sm font-extrabold text-slate-900">ملف المنتج (اختياري)</h3>
										</div>
									</div>

									<!-- Dashed Box Matching Reference -->
									<div
										class="p-4 sm:p-5 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 text-center space-y-2">
										<div id="portal-file-preview-box"
											class="<?php echo empty($f_file_url) ? 'hidden' : ''; ?> space-y-2">
											<div
												class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
												</svg>
											</div>
											<div class="text-xs font-bold text-slate-800 break-all"
												id="portal-file-name-text">
												<?php echo esc_html($f_file_name ? $f_file_name : basename($f_file_url)); ?>
											</div>
											<?php if (!empty($f_file_size)): ?>
												<div class="text-[10px] text-slate-400 font-mono" id="portal-file-size-text">
													<?php echo esc_html($f_file_size); ?>
												</div>
											<?php endif; ?>
											<button type="button" onclick="portalRemoveProductFile()"
												class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline pt-1 block mx-auto cursor-pointer">
												إزالة الملف المرفق
											</button>
										</div>

										<div id="portal-file-placeholder"
											class="<?php echo !empty($f_file_url) ? 'hidden' : ''; ?> space-y-1">
											<div
												class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
												<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
														d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
												</svg>
											</div>
											<div class="text-xs font-bold text-slate-700">أضف كتالوج أو دليل الاستخدام</div>
											<div class="text-[10px] text-slate-400 font-medium">بحد أقصى 20 ميجابايت - PDF,
												ZIP, DOC</div>
										</div>
									</div>

									<button type="button" id="portal-select-file-btn" onclick="portalChooseProductFile()"
										class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-2xs text-center cursor-pointer">
										اختيار ملف
									</button>
								</div>

								<!-- 3. مميزات إضافية (إبراز المنتج ونشر المنتج) -->
								<div
									class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 sm:p-6 space-y-4">
									<div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
										<div class="flex items-center gap-2.5">
											<span
												class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
												</svg>
											</span>
											<h3 class="text-sm font-extrabold text-slate-900">مميزات إضافية</h3>
										</div>
									</div>

									<!-- 1. إبراز المنتج كمنتج مميز -->
									<div class="flex items-center justify-between gap-3">
										<div>
											<label for="is_featured"
												class="text-xs font-black text-slate-800 block cursor-pointer">
												إبراز المنتج كمنتج مميز
											</label>
											<p class="text-[11px] text-slate-400 font-medium mt-0.5">
												سيظهر المنتج ضمن المنتجات المميزة في المتجر.
											</p>
										</div>

										<!-- Accessible Modern Toggle Switch (Default: Disabled for new products) -->
										<label
											class="relative inline-flex items-center cursor-pointer select-none flex-shrink-0">
											<input type="checkbox" name="is_featured" id="is_featured" value="1" <?php checked($f_is_featured, true); ?> class="sr-only peer">
											<div
												class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600">
											</div>
										</label>
									</div>

									<!-- Subtle Divider -->
									<div class="border-t border-slate-100"></div>

									<!-- 2. نشر المنتج -->
									<div>
										<div class="flex items-center justify-between gap-3">
											<div>
												<label for="is_published"
													class="text-xs font-black text-slate-800 block cursor-pointer">
													نشر المنتج
												</label>
												<p class="text-[11px] text-slate-400 font-medium mt-0.5">
													عند التفعيل، يصبح المنتج منشورًا في المتجر وفقًا لإعدادات WooCommerce.
												</p>
											</div>

											<!-- Accessible Modern Toggle Switch (Consistent with featured product toggle) -->
											<label
												class="relative inline-flex items-center cursor-pointer select-none flex-shrink-0">
												<input type="checkbox" name="is_published" id="is_published" value="1" <?php checked($f_is_published, true); ?> class="sr-only peer">
												<div
													class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600">
												</div>
											</label>
										</div>

										<!-- Dynamic Communication Warning when unpublishing an existing published product -->
										<div id="unpublish-warning"
											class="<?php echo ($is_edit_mode && 'publish' === $f_status && !$f_is_published) ? 'flex' : 'hidden'; ?> mt-3 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] font-medium items-start gap-2">
											<svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
											</svg>
											<span>
												<strong>تنبيه:</strong> سيؤدي حفظ التعديلات مع تعطيل هذا الخيار إلى إلغاء
												نشر المنتج وتحويله إلى مسودة (Draft) وحجبه عن العملاء في المتجر.
											</span>
										</div>
									</div>
								</div>

								<!-- 4. Form Actions (Primary Red Save, Preview, Delete) -->
								<div class="space-y-3 pt-1">
									<!-- Primary Red Save Action -->
									<button type="submit" id="portal-product-submit-btn"
										class="w-full py-3.5 px-4 rounded-xl text-sm font-black text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs flex items-center justify-center gap-2 cursor-pointer select-none">
										<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
												d="M5 13l4 4L19 7" />
										</svg>
										<span id="portal-submit-btn-text">حفظ التغييرات</span>
									</button>

									<!-- Product Preview Action -->
									<a href="<?php echo esc_url($is_edit_mode ? $edit_product->get_permalink() : home_url('/shop/')); ?>"
										target="_blank"
										class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors flex items-center justify-center gap-2 select-none shadow-2xs">
										<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
										</svg>
										<span>معاينة المنتج</span>
									</a>

									<!-- Delete Action (Existing Product Only) -->
									<?php if ($is_edit_mode): ?>
										<a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete-product', 'id' => $edit_product_id, 'cat_id' => $cat_id_param), home_url('/admin-portal/')), 'ashrafco_delete_product_' . $edit_product_id)); ?>"
											onclick="return confirm('هل أنت متأكد تماماً من رغبتك في نقل هذا المنتج إلى سلة المهملات؟');"
											class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-rose-600 bg-white border border-rose-200 hover:bg-rose-50 transition-colors flex items-center justify-center gap-2 select-none shadow-2xs">
											<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
											</svg>
											<span>حذف المنتج</span>
										</a>
									<?php endif; ?>
								</div>

							</div>

						</div>
					</form>
				</div>

			<?php elseif ($selected_category && !is_wp_error($selected_category)): ?>

				<!-- =============================================================
					 SUB-VIEW 2: عرض منتجات التصنيف المختار (Category Products View)
					 ============================================================= -->
				<div class="space-y-5">

					<!-- Category Header & Navigation Bar -->
					<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
						<div>
							<!-- Breadcrumbs -->
							<div class="flex items-center gap-2 text-xs text-slate-400 mb-1 flex-wrap font-medium">
								<a href="<?php echo esc_url(add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
									class="hover:text-primary transition-colors">
									إدارة التصنيفات
								</a>
								<span>/</span>
								<span class="text-slate-800 font-bold">
									<?php echo esc_html($selected_category->name); ?>
								</span>
							</div>

							<div class="flex items-center gap-3">
								<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
									منتجات تصنيف: <?php echo esc_html($selected_category->name); ?>
								</h2>
								<span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
									<?php echo esc_html($category_products_data['total_category']); ?> منتجات
								</span>
							</div>
							<?php if (!empty($selected_category->description)): ?>
								<p class="text-xs text-slate-500 mt-1 max-w-2xl">
									<?php echo esc_html($selected_category->description); ?>
								</p>
							<?php endif; ?>
						</div>

						<!-- Actions -->
						<div class="flex items-center gap-2.5 flex-wrap">
							<a href="<?php echo esc_url(add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
								class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-2xs select-none">
								<svg class="w-4 h-4 rotate-180 flex-shrink-0" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M10 19l-7-7m0 0l7-7m-7 7h18" />
								</svg>
								<span>العودة إلى التصنيفات</span>
							</a>

							<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'action' => 'new-product', 'cat_id' => $cat_id_param), home_url('/admin-portal/'))); ?>"
								class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-xs select-none">
								<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
										d="M12 4v16m8-8H4" />
								</svg>
								<span>إضافة منتج لهذا التصنيف</span>
							</a>
						</div>
					</div>

					<!-- Three Summary Cards -->
					<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
						<!-- Card A: إجمالي المنتجات (Right in RTL) -->
						<div
							class="bg-white rounded-2xl border border-slate-100 shadow-xs p-5 flex items-center justify-between">
							<div class="text-right">
								<span class="text-xs font-medium text-slate-500 block mb-1">إجمالي المنتجات</span>
								<span
									class="text-3xl font-black text-slate-900 block leading-tight"><?php echo esc_html($category_products_data['total_category']); ?></span>
								<span class="text-[11px] text-slate-400 mt-1 block">منتج في هذا التصنيف</span>
							</div>
							<div
								class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
										d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
								</svg>
							</div>
						</div>

						<!-- Card B: متوفر بالمخزون (Middle) -->
						<div
							class="bg-white rounded-2xl border border-slate-100 shadow-xs p-5 flex items-center justify-between">
							<div class="text-right">
								<span class="text-xs font-medium text-emerald-700 block mb-1">متوفر بالمخزون</span>
								<span
									class="text-3xl font-black text-slate-900 block leading-tight"><?php echo esc_html($category_products_data['in_stock']); ?></span>
								<span class="text-[11px] text-slate-400 mt-1 block">من إجمالي
									<?php echo esc_html($category_products_data['total_category']); ?> منتجات</span>
							</div>
							<div
								class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
						</div>

						<!-- Card C: نفذ من المخزون (Left in RTL) -->
						<div
							class="bg-white rounded-2xl border border-slate-100 shadow-xs p-5 flex items-center justify-between">
							<div class="text-right">
								<span class="text-xs font-medium text-rose-600 block mb-1">نفذ من المخزون</span>
								<span
									class="text-3xl font-black text-slate-900 block leading-tight"><?php echo esc_html($category_products_data['out_of_stock']); ?></span>
								<span class="text-[11px] text-slate-400 mt-1 block">من إجمالي
									<?php echo esc_html($category_products_data['total_category']); ?> منتجات</span>
							</div>
							<div
								class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							</div>
						</div>
					</div>

					<!-- Category Product Search & Filter Toolbar -->
					<div class="bg-white rounded-2xl border border-slate-100 shadow-xs p-3.5 sm:p-4">
						<form method="GET" action="<?php echo esc_url(home_url('/admin-portal/')); ?>"
							class="flex flex-wrap lg:flex-nowrap items-center gap-3 justify-between">
							<input type="hidden" name="tab" value="products">
							<input type="hidden" name="cat_id" value="<?php echo esc_attr($cat_id_param); ?>">
							<input type="hidden" name="per_page" value="<?php echo esc_attr($cat_prod_per_page); ?>">

							<!-- Product Search Input & Search Button -->
							<div class="flex items-center gap-2 flex-1 min-w-[260px] max-w-md">
								<button type="submit"
									class="w-10 h-10 bg-primary hover:bg-[#b90323] text-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs transition-colors cursor-pointer"
									title="بحث">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
											d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
									</svg>
								</button>
								<input type="text" name="prod_search" value="<?php echo esc_attr($cat_prod_search); ?>"
									placeholder="ابحث باسم المنتج أو SKU..."
									class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-right focus:outline-none focus:border-primary placeholder-slate-400">
							</div>

							<!-- Filters & Reset Controls -->
							<div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
								<!-- Stock Status Filter -->
								<select name="prod_stock" onchange="this.form.submit()"
									class="text-xs font-semibold py-2.5 pr-3 pl-8 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-primary cursor-pointer whitespace-nowrap">
									<option value="all" <?php selected($cat_prod_stock, 'all'); ?>>كل حالات المخزون
									</option>
									<option value="instock" <?php selected($cat_prod_stock, 'instock'); ?>>متوفر بالمخزون
									</option>
									<option value="lowstock" <?php selected($cat_prod_stock, 'lowstock'); ?>>مخزون منخفض
									</option>
									<option value="outofstock" <?php selected($cat_prod_stock, 'outofstock'); ?>>نفذ
										المخزون</option>
								</select>

								<!-- Publication Status Filter -->
								<select name="prod_publish" onchange="this.form.submit()"
									class="text-xs font-semibold py-2.5 pr-3 pl-8 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-primary cursor-pointer whitespace-nowrap">
									<option value="all" <?php selected($cat_prod_publish, 'all'); ?>>كل حالات النشر
									</option>
									<option value="publish" <?php selected($cat_prod_publish, 'publish'); ?>>منشور
									</option>
									<option value="draft" <?php selected($cat_prod_publish, 'draft'); ?>>مسودة</option>
								</select>

								<!-- Sorting Filter -->
								<select name="prod_sort" onchange="this.form.submit()"
									class="text-xs font-semibold py-2.5 pr-3 pl-8 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-primary cursor-pointer whitespace-nowrap">
									<option value="date_desc" <?php selected($cat_prod_sort, 'date_desc'); ?>>الأحدث أولاً
									</option>
									<option value="date_asc" <?php selected($cat_prod_sort, 'date_asc'); ?>>الأقدم أولاً
									</option>
									<option value="title_asc" <?php selected($cat_prod_sort, 'title_asc'); ?>>الاسم: أ إلى
										ي</option>
									<option value="price_asc" <?php selected($cat_prod_sort, 'price_asc'); ?>>السعر: الأقل
										إلى الأعلى</option>
									<option value="price_desc" <?php selected($cat_prod_sort, 'price_desc'); ?>>السعر:
										الأعلى إلى الأقل</option>
									<option value="stock_asc" <?php selected($cat_prod_sort, 'stock_asc'); ?>>أقل كمية
										بالمخزون</option>
								</select>

								<!-- Reset Action -->
								<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param), home_url('/admin-portal/'))); ?>"
									class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors whitespace-nowrap flex-shrink-0">
									<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
									</svg>
									<span>إعادة ضبط</span>
								</a>
							</div>
						</form>
					</div>

					<!-- Table Controls Bar (Items per page & Actions) -->
					<div class="flex items-center justify-between flex-wrap gap-3 px-1">
						<div class="flex items-center gap-2 text-xs font-bold text-slate-600">
							<span>عرض</span>
							<select onchange="location.href=this.value"
								class="text-xs font-bold py-1.5 pr-2.5 pl-6 bg-white border border-slate-200 rounded-lg text-slate-700 cursor-pointer shadow-2xs">
								<option
									value="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => 10), home_url('/admin-portal/'))); ?>"
									<?php selected($cat_prod_per_page, 10); ?>>10</option>
								<option
									value="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => 25), home_url('/admin-portal/'))); ?>"
									<?php selected($cat_prod_per_page, 25); ?>>25</option>
								<option
									value="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => 50), home_url('/admin-portal/'))); ?>"
									<?php selected($cat_prod_per_page, 50); ?>>50</option>
							</select>
							<span>من <?php echo esc_html($category_products_data['total']); ?> منتجات</span>
						</div>

						<div class="flex items-center gap-2">
							<!-- Refresh -->
							<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => $cat_prod_per_page), home_url('/admin-portal/'))); ?>"
								class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-2xs"
								title="تحديث البيانات">
								<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
								</svg>
								<span>تحديث</span>
							</a>

							<!-- CSV Export -->
							<a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('tab' => 'products', 'action' => 'export_cat_products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort), home_url('/admin-portal/')), 'ashrafco_export_cat_products_nonce')); ?>"
								class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors shadow-2xs"
								title="تصدير بيانات المنتجات إلى ملف CSV">
								<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
								</svg>
								<span>تصدير CSV</span>
							</a>
						</div>
					</div>

					<!-- Category Products Table -->
					<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
						<?php if (!empty($category_products_data['products'])): ?>
							<div class="overflow-x-auto">
								<table class="w-full text-right border-collapse min-w-[950px]">
									<thead>
										<tr
											class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
											<th class="py-3 px-4 text-right">الصورة والمنتج</th>
											<th class="py-3 px-4 text-right">كود المنتج (SKU)</th>
											<th class="py-3 px-4 text-right">السعر والخصم</th>
											<th class="py-3 px-4 text-right">المخزون والتوفر</th>
											<th class="py-3 px-4 text-right">التصنيف</th>
											<th class="py-3 px-4 text-right">حالة النشر</th>
											<th class="py-3 px-4 text-center">الإجراءات</th>
										</tr>
									</thead>
									<tbody class="divide-y divide-slate-100 text-xs">
										<?php foreach ($category_products_data['products'] as $product):
											$prod_id = $product->get_id();
											$stock_status = $product->get_stock_status();
											$stock_qty = $product->get_stock_quantity();
											$is_on_sale = $product->is_on_sale();
											$edit_link = add_query_arg(array('action' => 'edit-product', 'id' => $prod_id, 'cat_id' => $cat_id_param), home_url('/admin-portal/'));
											$del_link = wp_nonce_url(add_query_arg(array('action' => 'delete-product', 'id' => $prod_id, 'cat_id' => $cat_id_param), home_url('/admin-portal/')), 'ashrafco_delete_product_' . $prod_id);

											// Low stock detection
											$low_stock_amount = $product->get_low_stock_amount();
											if ('' === $low_stock_amount || null === $low_stock_amount) {
												$low_stock_amount = get_option('woocommerce_notify_low_stock_amount', 2);
											}
											$is_low_stock = $product->managing_stock() && null !== $stock_qty && $stock_qty > 0 && $stock_qty <= (int) $low_stock_amount;

											// Subtitle extraction
											$prod_subtitle = !empty($product->get_short_description()) ? wp_trim_words(wp_strip_all_tags($product->get_short_description()), 6, '...') : (!empty($product->get_description()) ? wp_trim_words(wp_strip_all_tags($product->get_description()), 6, '...') : '');
											?>
											<tr class="hover:bg-slate-50/70 transition-colors">
												<!-- Thumbnail & Title -->
												<td class="py-3.5 px-4 align-middle">
													<div class="flex items-center gap-3">
														<div
															class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200/90 overflow-hidden flex-shrink-0 flex items-center justify-center p-0.5">
															<?php if ($product->get_image_id()): ?>
																<?php echo wp_kses_post($product->get_image(array(48, 48), array('class' => 'w-full h-full object-cover rounded-lg'))); ?>
															<?php else: ?>
																<svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor"
																	viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
																</svg>
															<?php endif; ?>
														</div>
														<div class="min-w-0">
															<a href="<?php echo esc_url($edit_link); ?>"
																class="font-bold text-slate-900 hover:text-primary transition-colors block text-xs sm:text-sm truncate max-w-[280px]"
																title="<?php echo esc_attr($product->get_name()); ?>">
																<?php echo esc_html($product->get_name()); ?>
															</a>
															<?php if (!empty($prod_subtitle)): ?>
																<span
																	class="text-[11px] text-slate-400 block truncate max-w-[280px] mt-0.5 font-normal">
																	<?php echo esc_html($prod_subtitle); ?>
																</span>
															<?php endif; ?>
														</div>
													</div>
												</td>

												<!-- SKU -->
												<td class="py-3.5 px-4 align-middle whitespace-nowrap">
													<span class="font-mono text-xs font-semibold text-slate-600">
														<?php echo esc_html($product->get_sku() ?: 'غير محدد'); ?>
													</span>
												</td>

												<!-- Prices & Discount -->
												<td class="py-3.5 px-4 align-middle whitespace-nowrap">
													<div class="font-bold text-xs sm:text-sm flex items-center gap-1.5">
														<?php if ($is_on_sale && $product->get_regular_price()): ?>
															<span class="text-xs text-slate-400 line-through">
																<?php echo wp_kses_post(wc_price($product->get_regular_price())); ?>
															</span>
															<span class="text-primary font-bold">
																<?php echo wp_kses_post(wc_price($product->get_sale_price())); ?>
															</span>
														<?php else: ?>
															<span class="text-slate-900 font-bold">
																<?php echo wp_kses_post($product->get_price_html()); ?>
															</span>
														<?php endif; ?>
													</div>
												</td>

												<!-- Stock & Status -->
												<td class="py-3.5 px-4 align-middle whitespace-nowrap">
													<span class="text-xs font-semibold text-slate-700 block mb-1">
														<?php echo null !== $stock_qty ? esc_html($stock_qty) . ' قطعة' : ($product->is_in_stock() ? 'متوفر' : 'غير متوفر'); ?>
													</span>
													<?php if ('outofstock' === $stock_status || (null !== $stock_qty && $stock_qty <= 0)): ?>
														<span
															class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200/60">
															نفذ المخزون
														</span>
													<?php elseif ($is_low_stock): ?>
														<span
															class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60">
															مخزون منخفض
														</span>
													<?php else: ?>
														<span
															class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
															متوفر بالمخزون
														</span>
													<?php endif; ?>
												</td>

												<!-- Category -->
												<td class="py-3.5 px-4 align-middle whitespace-nowrap">
													<span class="text-xs text-slate-600 font-medium">
														<?php echo esc_html($selected_category->name); ?>
													</span>
												</td>

												<!-- Publication Status -->
												<td class="py-3.5 px-4 align-middle whitespace-nowrap">
													<?php if ('publish' === $product->get_status()): ?>
														<span
															class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
															منشور
														</span>
													<?php else: ?>
														<span
															class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
															مسودة
														</span>
													<?php endif; ?>
												</td>

												<!-- Actions Column -->
												<td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
													<div class="flex items-center justify-center gap-1.5 relative">
														<!-- 3-dots overflow button with popover -->
														<div class="relative inline-block text-right">
															<button type="button"
																onclick="toggleCategoryMenu(event, 'prod-menu-<?php echo esc_attr($prod_id); ?>')"
																class="w-8 h-8 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-50 flex items-center justify-center transition-colors cursor-pointer"
																title="خيارات إضافية">
																<svg class="w-4 h-4" fill="none" stroke="currentColor"
																	viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		stroke-width="2"
																		d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
																</svg>
															</button>
															<div id="prod-menu-<?php echo esc_attr($prod_id); ?>"
																class="cat-overflow-menu hidden absolute left-0 top-9 w-36 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-30 text-right">
																<a href="<?php echo esc_url($edit_link); ?>"
																	class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-bold">تعديل
																	كامل</a>
																<a href="<?php echo esc_url($product->get_permalink()); ?>"
																	target="_blank"
																	class="block px-3.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-bold">معاينة
																	بالمتجر</a>
																<a href="<?php echo esc_url($del_link); ?>"
																	onclick="return confirm('هل أنت متأكد من رغبتك في نقل المنتج إلى سلة المهملات؟');"
																	class="block px-3.5 py-1.5 text-xs text-rose-600 hover:bg-rose-50 font-bold">حذف
																	المنتج</a>
															</div>
														</div>

														<!-- Edit Action -->
														<a href="<?php echo esc_url($edit_link); ?>"
															class="px-2.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 text-xs font-semibold inline-flex items-center gap-1.5 transition-colors">
															<svg class="w-3.5 h-3.5 text-slate-500" fill="none"
																stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2"
																	d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
															</svg>
															<span>تعديل</span>
														</a>

														<!-- Delete Action -->
														<a href="<?php echo esc_url($del_link); ?>"
															onclick="return confirm('هل أنت متأكد من نقل المنتج \'<?php echo esc_js($product->get_name()); ?>\' إلى سلة المهملات؟');"
															class="px-2.5 py-1.5 rounded-lg border border-rose-200 text-rose-600 bg-rose-50/60 hover:bg-rose-100 text-xs font-semibold transition-colors">
															حذف
														</a>

														<!-- View Action -->
														<a href="<?php echo esc_url($product->get_permalink()); ?>" target="_blank"
															class="w-8 h-8 rounded-lg border border-slate-200 text-slate-400 hover:text-slate-800 hover:bg-slate-50 flex items-center justify-center transition-colors"
															title="معاينة في المتجر">
															<svg class="w-4 h-4" fill="none" stroke="currentColor"
																viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2"
																	d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
															</svg>
														</a>
													</div>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>

							<!-- Pagination -->
							<?php
							$from_item = $category_products_data['total'] > 0 ? (($cat_prod_paged - 1) * $category_products_data['per_page']) + 1 : 0;
							$to_item = min($category_products_data['total'], $cat_prod_paged * $category_products_data['per_page']);
							?>
							<div class="p-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
								<div class="text-xs text-slate-500 font-bold">
									عرض <?php echo esc_html($from_item); ?> إلى <?php echo esc_html($to_item); ?> من إجمالي
									<?php echo esc_html($category_products_data['total']); ?> منتجات
								</div>
								<div class="flex items-center gap-1.5">
									<?php if ($cat_prod_paged > 1): ?>
										<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => $cat_prod_per_page, 'paged' => $cat_prod_paged - 1), home_url('/admin-portal/'))); ?>"
											class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center text-xs font-bold transition-colors"
											title="الصفحة السابقة">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M9 5l7 7-7 7" />
											</svg>
										</a>
									<?php else: ?>
										<span
											class="w-8 h-8 rounded-lg border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center text-xs font-bold cursor-not-allowed">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M9 5l7 7-7 7" />
											</svg>
										</span>
									<?php endif; ?>

									<?php for ($i = 1; $i <= $category_products_data['pages']; $i++):
										$is_active_page = ($i === $cat_prod_paged);
										$page_url = add_query_arg(array(
											'tab' => 'products',
											'cat_id' => $cat_id_param,
											'prod_search' => $cat_prod_search,
											'prod_stock' => $cat_prod_stock,
											'prod_publish' => $cat_prod_publish,
											'prod_sort' => $cat_prod_sort,
											'per_page' => $cat_prod_per_page,
											'paged' => $i,
										), home_url('/admin-portal/'));
										?>
										<a href="<?php echo esc_url($page_url); ?>"
											class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-colors <?php echo $is_active_page ? 'bg-primary text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
											<?php echo esc_html($i); ?>
										</a>
									<?php endfor; ?>

									<?php if ($cat_prod_paged < $category_products_data['pages']): ?>
										<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param, 'prod_search' => $cat_prod_search, 'prod_stock' => $cat_prod_stock, 'prod_publish' => $cat_prod_publish, 'prod_sort' => $cat_prod_sort, 'per_page' => $cat_prod_per_page, 'paged' => $cat_prod_paged + 1), home_url('/admin-portal/'))); ?>"
											class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center text-xs font-bold transition-colors"
											title="الصفحة التالية">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15 19l-7-7 7-7" />
											</svg>
										</a>
									<?php else: ?>
										<span
											class="w-8 h-8 rounded-lg border border-slate-100 bg-slate-50 text-slate-300 flex items-center justify-center text-xs font-bold cursor-not-allowed">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15 19l-7-7 7-7" />
											</svg>
										</span>
									<?php endif; ?>
								</div>
							</div>

						<?php else: ?>
							<div class="text-center py-12 p-4">
								<div
									class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300">
									<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
											d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
									</svg>
								</div>
								<p class="text-sm font-bold text-slate-700">لا توجد منتجات مسجلة في هذا التصنيف تطابق معايير
									البحث.</p>
								<p class="text-xs text-slate-400 mt-1">يمكنك إعادة ضبط الفلاتر أو إضافة منتج جديد في هذا
									التصنيف.</p>
								<div class="flex items-center justify-center gap-2 mt-4">
									<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'cat_id' => $cat_id_param), home_url('/admin-portal/'))); ?>"
										class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-2xs">
										إعادة ضبط الفلاتر
									</a>
									<a href="<?php echo esc_url(add_query_arg(array('tab' => 'products', 'action' => 'new-product', 'cat_id' => $cat_id_param), home_url('/admin-portal/'))); ?>"
										class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-xs">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 4v16m8-8H4" />
										</svg>
										<span>إضافة منتج في هذا التصنيف</span>
									</a>
								</div>
							</div>
						<?php endif; ?>
					</div>

				</div>

			<?php else: ?>

				<!-- =============================================================
					 SUB-VIEW 1 (DEFAULT): لوحة إدارة التصنيفات (Category Management Dashboard)
					 Matching Reference Screenshot media_1791896564883.png
					 ============================================================= -->
				<div class="space-y-5 sm:space-y-6">

					<!-- 1. Header Section: Title + Subtitle on Right, Add Category Button on Left -->
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
						<div class="flex items-center gap-3">
							<div
								class="w-10 h-10 rounded-2xl bg-rose-50 text-primary border border-rose-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
								</svg>
							</div>
							<div>
								<h2 class="text-lg sm:text-xl font-black text-slate-900 leading-tight">
									إدارة التصنيفات
								</h2>
								<p class="text-xs text-slate-500 font-medium mt-0.5">
									نظّم منتجات المتجر وادخل لكل تصنيف لإدارة محتوياته.
								</p>
							</div>
						</div>

						<div class="flex items-center gap-2.5 flex-wrap self-start sm:self-auto">
							<button type="button" onclick="openBrandsModal()"
								class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 transition-all shadow-2xs hover:shadow-xs select-none cursor-pointer">
								<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
								</svg>
								<span>إدارة العلامات التجارية</span>
								<span id="portal-brands-header-badge"
									class="bg-rose-50 text-primary border border-rose-100 text-[11px] px-2 py-0.5 rounded-lg font-bold font-mono">
									<?php echo esc_html(count($all_portal_brands)); ?>
								</span>
							</button>

							<button type="button" onclick="openCreateCategoryModal()"
							class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-sm select-none self-start sm:self-auto cursor-pointer">
							<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
									d="M12 4v16m8-8H4" />
							</svg>
							<span>إضافة تصنيف جديد</span>
						</button>
						</div>
					</div>

					<!-- 2. Category Summary KPI Cards (4 Cards Grid) -->
					<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">

						<!-- Card 1 (Far Right in RTL): إجمالي المنتجات -->
						<div
							class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between transition-all hover:shadow-sm">
							<div class="min-w-0">
								<span class="text-xs font-bold text-slate-500 block">إجمالي المنتجات</span>
								<span
									class="text-2xl sm:text-3xl font-black text-slate-900 my-1 block leading-none font-mono">
									<?php echo esc_html(number_format_i18n($categories_summary['total_products'])); ?>
								</span>
								<span class="text-[11px] text-slate-400 font-medium block">منتج في جميع التصنيفات</span>
							</div>
							<div
								class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
								</svg>
							</div>
						</div>

						<!-- Card 2: إجمالي التصنيفات -->
						<div
							class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between transition-all hover:shadow-sm">
							<div class="min-w-0">
								<span class="text-xs font-bold text-slate-500 block">إجمالي التصنيفات</span>
								<span
									class="text-2xl sm:text-3xl font-black text-slate-900 my-1 block leading-none font-mono">
									<?php echo esc_html(number_format_i18n($categories_summary['total_categories'])); ?>
								</span>
								<span class="text-[11px] text-slate-400 font-medium block">كل التصنيفات في المتجر</span>
							</div>
							<div
								class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
								</svg>
							</div>
						</div>

						<!-- Card 3: التصنيفات الفرعية -->
						<div
							class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between transition-all hover:shadow-sm">
							<div class="min-w-0">
								<span class="text-xs font-bold text-slate-500 block">التصنيفات الفرعية</span>
								<span
									class="text-2xl sm:text-3xl font-black text-slate-900 my-1 block leading-none font-mono">
									<?php echo esc_html(number_format_i18n($categories_summary['subcategories'])); ?>
								</span>
								<span class="text-[11px] text-slate-400 font-medium block">تصنيف فرعي داخل التصنيفات
									الرئيسية</span>
							</div>
							<div
								class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0 shadow-2xs">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
								</svg>
							</div>
						</div>

						<!-- Card 4 (Far Left in RTL): تصنيفات بدون منتجات -->
						<div
							class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex items-center justify-between transition-all hover:shadow-sm">
							<div class="min-w-0">
								<span class="text-xs font-bold text-slate-500 block">تصنيفات بدون منتجات</span>
								<span
									class="text-2xl sm:text-3xl font-black text-slate-900 my-1 block leading-none font-mono">
									<?php echo esc_html(number_format_i18n($categories_summary['empty_categories'])); ?>
								</span>
								<span class="text-[11px] text-slate-400 font-medium block">تحتاج لمراجعة أو إضافة
									منتجات</span>
							</div>
							<div
								class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0 shadow-2xs">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
								</svg>
							</div>
						</div>

					</div>

					<!-- 3. Toolbar: Search, Filters, Sorting, and Reset -->
					<div class="bg-white rounded-2xl border border-slate-200/80 p-3 sm:p-4 shadow-xs">
						<form method="GET" action="<?php echo esc_url(home_url('/admin-portal/')); ?>"
							class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
							<input type="hidden" name="tab" value="products">

							<!-- Right Controls in RTL: Reset, Category filter, Sorting -->
							<div class="flex items-center gap-2.5 flex-wrap justify-start">
								<!-- Reset Button -->
								<a href="<?php echo esc_url(add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
									class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-200/90 text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors whitespace-nowrap bg-white shadow-2xs select-none"
									title="إعادة ضبط الفلاتر والبحث">
									<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
									</svg>
									<span>إعادة ضبط</span>
								</a>

								<!-- Category Filter Dropdown -->
								<div class="relative min-w-[130px]">
									<select name="cat_filter" onchange="this.form.submit()"
										class="w-full text-xs font-bold py-2.5 pr-8 pl-4 bg-slate-50/70 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-primary cursor-pointer text-right appearance-none">
										<option value="all" <?php selected($cat_filter, 'all'); ?>>كل التصنيفات</option>
										<option value="main" <?php selected($cat_filter, 'main'); ?>>التصنيفات الرئيسية
										</option>
										<option value="sub" <?php selected($cat_filter, 'sub'); ?>>التصنيفات الفرعية
										</option>
										<option value="empty" <?php selected($cat_filter, 'empty'); ?>>تصنيفات بدون منتجات
										</option>
									</select>
								</div>

								<!-- Sort Dropdown -->
								<div class="relative min-w-[130px]">
									<select name="cat_sort" onchange="this.form.submit()"
										class="w-full text-xs font-bold py-2.5 pr-8 pl-4 bg-slate-50/70 border border-slate-200 rounded-xl text-slate-700 focus:outline-none focus:border-primary cursor-pointer text-right appearance-none">
										<option value="default" <?php selected($cat_sort, 'default'); ?>>الترتيب أولاً
										</option>
										<option value="name_asc" <?php selected($cat_sort, 'name_asc'); ?>>الترتيب الأبجدي
											(أ - ي)</option>
										<option value="name_desc" <?php selected($cat_sort, 'name_desc'); ?>>الترتيب
											الأبجدي (ي - أ)</option>
										<option value="count_desc" <?php selected($cat_sort, 'count_desc'); ?>>الأكثر
											منتجات</option>
										<option value="count_asc" <?php selected($cat_sort, 'count_asc'); ?>>الأقل منتجات
										</option>
										<option value="newest" <?php selected($cat_sort, 'newest'); ?>>الأحدث</option>
									</select>
								</div>
							</div>

							<!-- Left Controls in RTL: Search Input + Red Search Button -->
							<div class="flex items-center gap-2 flex-1 max-w-lg justify-end">
								<div class="relative flex-1">
									<input type="text" name="cat_search" value="<?php echo esc_attr($cat_search); ?>"
										placeholder="ابحث باسم التصنيف..."
										class="w-full text-xs font-medium py-2.5 px-4 bg-slate-50/70 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 text-right">
								</div>
								<button type="submit"
									class="w-10 h-10 rounded-xl bg-primary hover:bg-[#b90323] text-white flex items-center justify-center flex-shrink-0 transition-colors shadow-xs cursor-pointer"
									title="بحث">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
											d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
									</svg>
								</button>
							</div>
						</form>
					</div>

					<!-- 4. Category Cards Grid (4 per row on Desktop) -->
					<?php if (!empty($portal_category_cards)): ?>
						<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
							<?php foreach ($portal_category_cards as $cat_item): ?>
								<div
									class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all p-4 flex flex-col justify-between relative group">

									<!-- Card Top: Image & Titles on Right, 3-Dot Menu on Left -->
									<div>
										<div class="flex items-start justify-between gap-3">

											<!-- Right side in RTL: Thumbnail + Category Info -->
											<div class="flex items-center gap-3 min-w-0 flex-1">
												<!-- Thumbnail Box -->
												<div
													class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-slate-50 border border-slate-100 p-1 flex items-center justify-center flex-shrink-0 overflow-hidden">
													<?php if (!empty($cat_item['thumbnail_url'])): ?>
														<img src="<?php echo esc_url($cat_item['thumbnail_url']); ?>"
															alt="<?php echo esc_attr($cat_item['name']); ?>"
															class="w-full h-full object-contain" loading="lazy">
													<?php else: ?>
														<div class="w-full h-full flex items-center justify-center text-slate-300">
															<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
																	d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
															</svg>
														</div>
													<?php endif; ?>
												</div>

												<!-- Title & Counts -->
												<div class="min-w-0 text-right flex-1">
													<h3 class="text-sm sm:text-base font-black text-slate-900 truncate leading-snug"
														title="<?php echo esc_attr($cat_item['name']); ?>">
														<?php echo esc_html($cat_item['name']); ?>
													</h3>
													<p class="text-xs font-bold text-slate-500 mt-0.5">
														<?php echo esc_html($cat_item['count']); ?> منتج
													</p>
													<p class="text-[11px] text-slate-400 mt-0.5 truncate">
														<?php
														if ($cat_item['child_count'] > 0) {
															printf(esc_html__('%d تصنيفات فرعية', 'ashrafco'), $cat_item['child_count']);
														} elseif (!empty($cat_item['parent_name'])) {
															printf(esc_html__('فرعي من: %s', 'ashrafco'), esc_html($cat_item['parent_name']));
														} else {
															esc_html_e('0 تصنيف فرعي', 'ashrafco');
														}
														?>
													</p>
												</div>
											</div>

											<!-- Left side in RTL: 3-Dot Overflow Menu Button -->
											<div class="relative flex-shrink-0">
												<button type="button"
													onclick="toggleCategoryMenu(event, 'cat-menu-<?php echo esc_attr($cat_item['term_id']); ?>')"
													class="w-7 h-7 rounded-lg border border-slate-200/80 text-slate-400 hover:text-slate-700 hover:bg-slate-50 flex items-center justify-center transition-colors cursor-pointer"
													title="خيارات إضافية">
													<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
														<path
															d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
													</svg>
												</button>

												<!-- Popover Menu -->
												<div id="cat-menu-<?php echo esc_attr($cat_item['term_id']); ?>"
													class="cat-overflow-menu hidden absolute left-0 top-8 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-30 text-right">
													<a href="<?php echo esc_url($cat_item['frontend_url']); ?>" target="_blank"
														rel="noopener noreferrer"
														class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
														<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
															viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
														</svg>
														<span>معاينة في المتجر</span>
													</a>
													<a href="<?php echo esc_url(wp_nonce_url(add_query_arg(array('action' => 'delete-category', 'cat_id' => $cat_item['term_id']), home_url('/admin-portal/')), 'ashrafco_delete_cat_' . $cat_item['term_id'])); ?>"
														onclick="return confirm('هل أنت متأكد من رغبتك في حذف التصنيف \'<?php echo esc_js($cat_item['name']); ?>\' نهائياً؟');"
														class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors">
														<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
															viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
														</svg>
														<span>حذف التصنيف</span>
													</a>
												</div>
											</div>

										</div>
									</div>

									<!-- Card Bottom Actions: تعديل on right, عرض المنتجات on left -->
									<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
										<!-- تعديل Button -->
										<button type="button"
											onclick='openEditCategoryModal(<?php echo wp_json_encode($cat_item); ?>)'
											class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 py-1.5 px-2.5 rounded-lg hover:bg-slate-100 transition-colors select-none cursor-pointer">
											<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
											</svg>
											<span>تعديل</span>
										</button>

										<!-- عرض المنتجات Button with Left Arrow in Red -->
										<a href="<?php echo esc_url($cat_item['view_url']); ?>"
											class="inline-flex items-center gap-1 text-xs font-extrabold text-primary hover:text-[#b90323] py-1.5 px-2.5 rounded-lg hover:bg-rose-50 transition-colors select-none">
											<span>عرض المنتجات</span>
											<svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M14 5l7 7m0 0l-7 7m7-7H3" />
											</svg>
										</a>
									</div>

								</div>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<!-- Empty Search or Categories State -->
						<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-8 sm:p-12 text-center">
							<div
								class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-300">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
								</svg>
							</div>
							<h3 class="text-base font-extrabold text-slate-800">لم يتم العثور على أي تصنيفات مطابقة</h3>
							<p class="text-xs text-slate-400 mt-1">جرّب تغيير كلمات البحث أو إعادة ضبط خيارات التصفية.</p>
							<div class="flex items-center justify-center gap-2 mt-4">
								<a href="<?php echo esc_url(add_query_arg('tab', 'products', home_url('/admin-portal/'))); ?>"
									class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
									إعادة ضبط البحث
								</a>
								<button type="button" onclick="openCreateCategoryModal()"
									class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-[#b90323] transition-colors">
									إضافة تصنيف جديد
								</button>
							</div>
						</div>
					<?php endif; ?>

				</div>

				<!-- =============================================================
					 Category Create / Edit Modal (Integrated with WP Media)
					 ============================================================= -->
				<div id="category-crud-modal"
					class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
					<div
						class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden my-8 max-h-[92vh] flex flex-col">
						<!-- Modal Header -->
						<div
							class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50 flex-shrink-0">
							<h3 id="category-modal-title" class="text-base font-black text-slate-900">
								إضافة تصنيف جديد
							</h3>
							<button type="button" onclick="closeCategoryModal()"
								class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>

						<!-- Modal Form -->
						<form method="POST" action="<?php echo esc_url(home_url('/admin-portal/')); ?>"
							class="p-4 sm:p-6 space-y-4 overflow-y-auto flex-1">
							<?php wp_nonce_field('ashrafco_category_form_nonce', '_wpnonce'); ?>
							<input type="hidden" name="ashrafco_cat_action" id="modal-cat-action" value="create">
							<input type="hidden" name="cat_id" id="modal-cat-id" value="0">
							<input type="hidden" name="cat_thumbnail_id" id="modal-cat-thumbnail-id" value="0">
							<input type="hidden" name="cat_remove_thumbnail" id="modal-cat-remove-thumb" value="0">

							<!-- Category Name -->
							<div>
								<label for="modal-cat-name"
									class="block text-xs font-bold text-slate-700 mb-1.5 text-right">
									اسم التصنيف <span class="text-rose-500">*</span>
								</label>
								<input type="text" id="modal-cat-name" name="cat_name" required
									placeholder="مثال: معدات السلامة أو أدوات يدوية"
									class="w-full text-xs font-medium py-2.5 px-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 text-right">
							</div>

							<!-- Parent Category Dropdown -->
							<div>
								<label for="modal-cat-parent"
									class="block text-xs font-bold text-slate-700 mb-1.5 text-right">
									التصنيف الأب <span class="text-slate-400 font-normal">(اختر إذا كان هذا تصنيفاً
										فرعياً)</span>
								</label>
								<select id="modal-cat-parent" name="cat_parent"
									class="w-full text-xs font-bold py-2.5 pr-4 pl-10 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary text-slate-800 text-right appearance-none">
									<option value="0">بدون تصنيف أب (تصنيف رئيسي)</option>
									<?php foreach ($all_product_cats as $p_cat): ?>
										<option value="<?php echo esc_attr($p_cat->term_id); ?>">
											<?php echo esc_html($p_cat->name); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Category Description -->
							<div>
								<label for="modal-cat-desc"
									class="block text-xs font-bold text-slate-700 mb-1.5 text-right">
									وصف التصنيف <span class="text-slate-400 font-normal">(اختياري)</span>
								</label>
								<textarea id="modal-cat-desc" name="cat_desc" rows="2"
									placeholder="نبذة مختصرة عن منتجات هذا التصنيف..."
									class="w-full text-xs py-2.5 px-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary leading-relaxed text-slate-800 text-right"></textarea>
							</div>

							<!-- Category Image Selection -->
							<div>
								<label class="block text-xs font-bold text-slate-700 mb-1.5 text-right">
									صورة التصنيف
								</label>
								<div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
									<div
										class="w-14 h-14 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
										<img id="modal-cat-preview" src="" alt="معاينة"
											class="w-full h-full object-contain hidden">
										<div id="modal-cat-preview-placeholder" class="text-slate-300">
											<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
													d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
											</svg>
										</div>
									</div>
									<div class="flex items-center gap-2">
										<button type="button" id="modal-cat-select-image-btn"
											class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
											اختر صورة
										</button>
										<button type="button" id="modal-cat-remove-image-btn"
											class="px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors hidden cursor-pointer">
											إزالة
										</button>
									</div>
								</div>
							</div>

							<!-- Category Specifications Management Section -->
							<div class="border-t border-slate-200/90 pt-4 mt-2 space-y-3">
								<div class="flex items-center justify-between">
									<div>
										<label
											class="block text-xs font-bold text-slate-800 text-right flex items-center gap-1.5">
											<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
											</svg>
											<span>مواصفات التصنيف الفنية (Category Specifications)</span>
										</label>
										<p class="text-[11px] text-slate-500 mt-0.5 text-right">
											المواصفات المحددة هنا تظهر ديناميكياً في شاشة إضافة وتعديل منتجات هذا التصنيف.
										</p>
									</div>
									<button type="button" onclick="categoryModalAddSpecRow()"
										class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-primary bg-primary/10 hover:bg-primary/20 transition-colors cursor-pointer">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
												d="M12 4v16m8-8H4" />
										</svg>
										<span>إضافة مواصفة</span>
									</button>
								</div>

								<input type="hidden" name="cat_specs_json" id="modal-cat-specs-json" value="[]">

								<!-- Dynamic Specifications List -->
								<div id="modal-cat-specs-list" class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
									<!-- Populated by categoryModalAddSpecRow() -->
								</div>

								<!-- Empty state banner -->
								<div id="modal-cat-specs-empty"
									class="py-5 px-4 rounded-xl border border-dashed border-slate-200 text-center text-xs text-slate-400 bg-slate-50/60">
									لم يتم تعريف أي مواصفات لهذا التصنيف بعد. انقر على «إضافة مواصفة» لتعريف مواصفة جديدة.
								</div>
							</div>

							<!-- Modal Footer Buttons -->
							<div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5 flex-shrink-0">
								<button type="button" onclick="closeCategoryModal()"
									class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
									إلغاء
								</button>
								<button type="submit" id="modal-cat-submit-btn"
									class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors shadow-xs cursor-pointer">
									حفظ التصنيف
								</button>
							</div>
						</form>
					</div>
				</div>

				<?php include __DIR__ . '/modal-brands.php'; ?>


			<?php endif; ?>

		</div>
