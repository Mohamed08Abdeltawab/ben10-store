<?php
/**
 * Admin Portal View: Orders Manager (Tab 2)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 2: إدارة الطلبات (Orders Manager)
			 ================================================================= -->
		<div id="portal-tab-orders"
			class="tab-content <?php echo 'orders' === $active_tab ? 'active' : ''; ?> space-y-6">

			<!-- 1. Header & Subtitle -->
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div>
					<div class="flex items-center gap-2.5">
						<svg class="w-6 h-6 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
							إدارة الطلبات
						</h1>
					</div>
					<p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
						متابعة الطلبات وتحديث حالتها بكل سهولة وكفاءة.
					</p>
				</div>
			</div>

			<!-- 2. Four Summary Cards -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
				<!-- Card 1: All Orders (كل الطلبات) -->
				<div
					class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div>
						<div class="text-xs font-bold text-slate-500">كل الطلبات</div>
						<div class="text-2xl sm:text-3xl font-black text-slate-900 font-mono mt-1 leading-none">
							<span id="kpi-orders-all"><?php echo esc_html($orders_summary['all']); ?></span>
						</div>
						<div class="text-[11px] font-medium text-slate-400 mt-2">جميع الطلبات في المتجر</div>
					</div>
					<div
						class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
					</div>
				</div>

				<!-- Card 2: Processing (قيد المعالجة - نقطة صفراء) -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-amber-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div>
						<div class="flex items-center gap-1.5 text-xs font-bold text-amber-800">
							<span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
							<span>قيد المعالجة</span>
						</div>
						<div class="text-2xl font-black text-amber-600 font-mono mt-1 leading-none">
							<span id="kpi-orders-processing"><?php echo esc_html($orders_summary['processing']); ?></span>
						</div>
						<div class="text-[11px] font-medium text-amber-700/70 mt-2">تحتاج مراجعة وتجهيز</div>
					</div>
					<div
						class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>

				<!-- Card 3: Shipped (تم الشحن - نقطة زرقاء) -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-blue-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div>
						<div class="flex items-center gap-1.5 text-xs font-bold text-blue-800">
							<span class="w-2 h-2 rounded-full bg-blue-500"></span>
							<span>تم الشحن</span>
						</div>
						<div class="text-2xl font-black text-blue-600 font-mono mt-1 leading-none">
							<span id="kpi-orders-shipped"><?php echo esc_html(isset($orders_summary['shipped']) ? $orders_summary['shipped'] : 0); ?></span>
						</div>
						<div class="text-[11px] font-medium text-blue-700/70 mt-2">مع مندوب الشحن</div>
					</div>
					<div
						class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200/60 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 8h4.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16h-3" />
						</svg>
					</div>
				</div>

				<!-- Card 4: Delivered (تم التسليم - نقطة خضراء) -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-emerald-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div>
						<div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800">
							<span class="w-2 h-2 rounded-full bg-emerald-500"></span>
							<span>تم التسليم</span>
						</div>
						<div class="text-2xl font-black text-emerald-600 font-mono mt-1 leading-none">
							<span id="kpi-orders-completed"><?php echo esc_html($orders_summary['completed']); ?></span>
						</div>
						<div class="text-[11px] font-medium text-emerald-700/70 mt-2">تم التسليم بنجاح</div>
					</div>
					<div
						class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/60 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>

				<!-- Card 5: Cancelled (ملغي - نقطة حمراء) -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-rose-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div>
						<div class="flex items-center gap-1.5 text-xs font-bold text-rose-800">
							<span class="w-2 h-2 rounded-full bg-rose-500"></span>
							<span>ملغي</span>
						</div>
						<div class="text-2xl font-black text-rose-600 font-mono mt-1 leading-none">
							<span id="kpi-orders-cancelled"><?php echo esc_html($orders_summary['cancelled']); ?></span>
						</div>
						<div class="text-[11px] font-medium text-rose-700/70 mt-2">طلبات ملغاة</div>
					</div>
					<div
						class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200/60 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>

			<!-- 3. Search and Filters Toolbar -->
			<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-3.5 sm:p-4">
				<form method="GET" action="<?php echo esc_url(home_url('/admin-portal/')); ?>"
					class="flex flex-col lg:flex-row lg:items-center gap-2 sm:gap-2.5">
					<input type="hidden" name="tab" value="orders">

					<!-- Search component (Standalone red button on right in RTL, flexible input on left) -->
					<div class="flex items-center gap-2 w-full lg:w-auto lg:flex-1 min-w-0">
						<!-- Standalone Search Button (Right side in RTL) -->
						<button type="submit"
							class="w-11 h-11 min-w-[44px] rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white flex items-center justify-center transition-all duration-200 shadow-xs hover:shadow-sm focus:ring-2 focus:ring-rose-400 focus:outline-none flex-shrink-0 cursor-pointer disabled:opacity-50 p-0"
							title="بحث عن الطلبات" aria-label="بحث عن الطلبات">
							<svg class="w-[22px] h-[22px] text-white flex-shrink-0 pointer-events-none"
								style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 22px; max-height: 22px;"
								fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"
								aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
						</button>

						<!-- Search Input (Consumes remaining available width) -->
						<input type="text" name="search" value="<?php echo esc_attr($orders_search); ?>"
							placeholder="ابحث برقم الطلب أو اسم العميل أو اسم المنتج..."
							class="portal-search-input flex-1 min-w-0 w-full h-11 text-xs sm:text-sm font-medium py-2 px-3.5 bg-white hover:bg-slate-50/50 focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-800 placeholder:text-slate-400 transition-colors shadow-2xs">
					</div>

					<!-- Filter Controls Container (Locked to one row on lg: and up) -->
					<div
						class="flex flex-wrap lg:flex-nowrap items-center gap-2 sm:gap-2.5 w-full lg:w-auto flex-shrink-0">
						<!-- Status dropdown -->
						<div class="w-full sm:w-auto flex-shrink-0">
							<select name="status"
								class="w-full sm:w-[130px] h-11 text-xs font-bold py-2 pr-2.5 pl-6 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-700 cursor-pointer shadow-2xs">
								<option value="all">كل الحالات</option>
								<option value="processing" <?php selected($orders_status, 'processing'); ?>>قيد المعالجة</option>
								<option value="shipped" <?php selected($orders_status, 'shipped'); ?>>تم الشحن</option>
								<option value="completed" <?php selected($orders_status, 'completed'); ?>>تم التسليم</option>
								<option value="cancelled" <?php selected($orders_status, 'cancelled'); ?>>ملغي</option>
							</select>
						</div>

						<!-- Payment method dropdown -->
						<div class="w-full sm:w-auto flex-shrink-0">
							<select name="payment_method"
								class="w-full sm:w-[130px] h-11 text-xs font-bold py-2 pr-2.5 pl-6 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-700 cursor-pointer shadow-2xs">
								<option value="all">كل طرق الدفع</option>
								<?php foreach ($available_gateways as $gw_id => $gw_title): ?>
									<option value="<?php echo esc_attr($gw_id); ?>" <?php selected($orders_payment_method, $gw_id); ?>>
										<?php echo esc_html($gw_title); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<!-- Date from -->
						<div class="relative w-full sm:w-auto flex-shrink-0">
							<input type="date" name="date_from" value="<?php echo esc_attr($orders_date_from); ?>"
								class="portal-date-input w-full sm:w-[122px] h-11 text-xs font-bold py-2 px-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-700 cursor-pointer shadow-2xs"
								title="التاريخ من">
						</div>

						<!-- Date to -->
						<div class="relative w-full sm:w-auto flex-shrink-0">
							<input type="date" name="date_to" value="<?php echo esc_attr($orders_date_to); ?>"
								class="portal-date-input w-full sm:w-[122px] h-11 text-xs font-bold py-2 px-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-500 text-slate-700 cursor-pointer shadow-2xs"
								title="التاريخ إلى">
						</div>

						<!-- Action buttons -->
						<div class="flex items-center gap-1.5 w-full sm:w-auto flex-shrink-0">
							<button type="submit"
								class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-3.5 h-11 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 transition-all shadow-xs select-none whitespace-nowrap cursor-pointer">
								<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
								</svg>
								<span>تصفية النتائج</span>
							</button>

							<button type="button" id="orders-reset-filters-btn"
								class="inline-flex items-center justify-center gap-1.5 px-3 h-11 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 active:scale-95 border border-slate-200/90 transition-all shadow-2xs select-none whitespace-nowrap cursor-pointer"
								title="إعادة ضبط الفلاتر">
								<svg class="w-3.5 h-3.5 rotate-45 flex-shrink-0 text-slate-400" fill="none"
									stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
								</svg>
								<span>إعادة ضبط</span>
							</button>
						</div>
					</div>
				</form>
			</div>

			<!-- 4. Table Controls Row & Bulk Action Bar -->
			<div class="space-y-3">
				<!-- Bulk selection notification bar (hidden by default) -->
				<div id="orders-bulk-bar"
					class="hidden bg-slate-900 text-white rounded-2xl p-3 px-5 flex flex-wrap items-center justify-between gap-3 shadow-lg transition-all">
					<div class="flex items-center gap-2 text-xs font-bold">
						<span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
						<span>تم تحديد <strong id="orders-selected-count" class="text-rose-400 font-mono">0</strong>
							طلبات</span>
					</div>
					<div class="flex items-center gap-2">
						<select id="bulk-status-select"
							class="text-xs font-bold py-1.5 pr-3 pl-8 bg-slate-800 border border-slate-700 rounded-lg text-white cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500/50">
							<option value="">تغيير الحالة إلى...</option>
							<option value="processing">قيد المعالجة</option>
							<option value="shipped">تم الشحن</option>
							<option value="completed">تم التسليم</option>
							<option value="cancelled">ملغي</option>
						</select>
						<button type="button" id="bulk-apply-status-btn"
							class="px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-rose-600 hover:bg-rose-500 text-white transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
							تطبيق
						</button>
						<button type="button" id="bulk-cancel-selection-btn"
							class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-400 hover:text-white transition-colors cursor-pointer">
							إلغاء التحديد
						</button>
					</div>
				</div>

				<!-- Table Sub-bar: Per Page, Total, Refresh, CSV Export -->
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
					<!-- Right side: Page size & total count -->
					<div class="flex items-center gap-3">
						<span class="text-slate-500 font-bold">عرض لكل</span>
						<select id="orders-per-page-select"
							class="text-xs font-bold py-1.5 pr-2.5 pl-6 bg-white border border-slate-200 rounded-lg text-slate-700 cursor-pointer">
							<option value="10" <?php selected($orders_per_page, 10); ?>>10</option>
							<option value="25" <?php selected($orders_per_page, 25); ?>>25</option>
							<option value="50" <?php selected($orders_per_page, 50); ?>>50</option>
							<option value="100" <?php selected($orders_per_page, 100); ?>>100</option>
						</select>
						<span class="text-slate-500 font-extrabold mr-2">
							إجمالي <?php echo esc_html($orders_total_count); ?> طلب
						</span>
					</div>

					<!-- Left side: Refresh & CSV Export -->
					<div class="flex items-center gap-2">
						<button type="button" id="orders-refresh-btn"
							class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 shadow-2xs transition-colors">
							<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
							</svg>
							<span>تحديث</span>
						</button>

						<button type="button" id="orders-export-csv-btn"
							class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 shadow-2xs transition-colors">
							<svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
							</svg>
							<span>تصدير CSV</span>
						</button>
					</div>
				</div>
			</div>

			<!-- 5. Orders Table Card -->
			<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
				<?php if (!empty($portal_orders)): ?>
					<div class="overflow-x-auto">
						<table class="w-full text-right border-collapse min-w-[1050px]">
							<thead>
								<tr
									class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
									<th class="py-3.5 px-4 text-center w-10">
										<input type="checkbox" id="orders-select-all"
											class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
									</th>
									<th class="py-3.5 px-4">رقم الطلب</th>
									<th class="py-3.5 px-4">التاريخ والوقت</th>
									<th class="py-3.5 px-4">العميل وبيانات التواصل</th>
									<th class="py-3.5 px-4">المنتجات المطلوبة</th>
									<th class="py-3.5 px-4">الإجمالي</th>
									<th class="py-3.5 px-4">طريقة الدفع</th>
									<th class="py-3.5 px-4">الحالة الحالية</th>
									<th class="py-3.5 px-4 text-center">تحديث الحالة</th>
									<th class="py-3.5 px-4 text-center">إجراءات</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-slate-100 text-xs">
								<?php foreach ($portal_orders as $order):
									$order_id = $order->get_id();
									$status = $order->get_status();
									$state_code = $order->get_shipping_state() ?: $order->get_billing_state();
									$state_name = isset($eg_states[$state_code]) ? $eg_states[$state_code] : $state_code;
									$city = $order->get_shipping_city() ?: $order->get_billing_city();
									$location = trim($state_name . ($city ? ' - ' . $city : ''), ' -');
									$items = $order->get_items();
									$items_count = count($items);
									$phone = $order->get_billing_phone();
									$wa_phone = ashrafco_normalize_whatsapp_phone($phone);
									$wa_url = $wa_phone ? 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode(sprintf('مرحباً %s، بخصوص طلبك #%d من متجر %s...', $order->get_formatted_billing_full_name(), $order_id, $portal_store_name)) : '';
									$badge = ashrafco_get_portal_status_badge($status);

									// Extract first item thumbnail & title
									$first_item = reset($items);
									$first_prod = $first_item ? $first_item->get_product() : null;
									$first_thumb = $first_prod && $first_prod->get_image_id() ? wp_get_attachment_image_url($first_prod->get_image_id(), 'thumbnail') : wc_placeholder_img_src();
									$first_qty = $first_item ? $first_item->get_quantity() : 1;
									$first_name = $first_item ? $first_item->get_name() : 'منتج متجر';

									$date_obj = $order->get_date_created();
									$date_str = $date_obj ? $date_obj->date_i18n('Y-m-d') : '';
									$time_str = $date_obj ? $date_obj->date_i18n('H:i') : '';
									?>
									<tr id="portal-order-row-<?php echo esc_attr($order_id); ?>"
										class="hover:bg-slate-50/70 transition-colors">
										<!-- 1. Selection Checkbox -->
										<td class="py-4 px-4 text-center align-middle">
											<input type="checkbox" value="<?php echo esc_attr($order_id); ?>"
												class="order-row-checkbox w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500 cursor-pointer">
										</td>

										<!-- 2. Order ID -->
										<td class="py-4 px-4 align-middle">
											<div class="font-extrabold text-slate-900 font-mono text-sm">
												#<?php echo esc_html($order_id); ?>
											</div>
										</td>

										<!-- 3. Date & Time -->
										<td class="py-4 px-4 align-middle font-mono text-[11px]">
											<div class="font-bold text-slate-700"><?php echo esc_html($date_str); ?></div>
											<div class="text-slate-400 mt-0.5"><?php echo esc_html($time_str); ?></div>
										</td>

										<!-- 4. Customer & Contact -->
										<td class="py-4 px-4 align-middle">
											<div class="flex items-center gap-1.5">
												<svg class="w-3.5 h-3.5 text-rose-500 flex-shrink-0" fill="none"
													stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
												</svg>
												<span class="font-bold text-slate-900 line-clamp-1">
													<?php echo esc_html($order->get_formatted_billing_full_name() ?: 'عميل مباشر'); ?>
												</span>
											</div>
											<?php if ($location): ?>
												<div class="text-[11px] text-slate-500 mt-0.5">
													<?php echo esc_html($location); ?>
												</div>
											<?php endif; ?>
											<?php if ($phone): ?>
												<div class="text-[11px] font-mono text-rose-600 font-bold mt-0.5" dir="ltr">
													<?php echo esc_html($phone); ?>
												</div>
											<?php endif; ?>
										</td>

										<!-- 5. Products preview -->
										<td class="py-4 px-4 align-middle">
											<div class="flex items-center gap-2.5 max-w-[280px]">
												<img src="<?php echo esc_url($first_thumb); ?>" alt=""
													class="w-10 h-10 rounded-xl object-cover bg-slate-50 border border-slate-100 flex-shrink-0">
												<div class="min-w-0">
													<div class="font-bold text-slate-800 line-clamp-1 text-xs">
														<?php echo esc_html($first_name); ?>
													</div>
													<div class="flex items-center gap-2 mt-0.5">
														<span
															class="font-mono text-slate-400 text-[11px] font-bold">(<?php echo esc_html($first_qty); ?>&times;)</span>
														<?php if ($items_count > 1): ?>
															<span
																class="text-cyan-600 text-[11px] font-extrabold whitespace-nowrap">
																+<?php echo esc_html($items_count - 1); ?> منتجات أخرى
															</span>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</td>

										<!-- 6. Total Amount -->
										<td
											class="py-4 px-4 align-middle font-mono font-black text-slate-900 text-xs whitespace-nowrap">
											EGP <?php echo esc_html(number_format($order->get_total(), 2)); ?>
										</td>

										<!-- 7. Payment Method -->
										<td class="py-4 px-4 align-middle whitespace-nowrap">
											<div class="flex items-center gap-1.5 text-slate-700 text-xs font-bold">
												<?php echo ashrafco_get_payment_icon_html($order->get_payment_method()); ?>
												<span><?php echo esc_html($order->get_payment_method_title() ?: 'الدفع عند الاستلام'); ?></span>
											</div>
										</td>

										<!-- 8. Current Status Badge with Color Dot & Background -->
										<td class="py-4 px-4 align-middle whitespace-nowrap">
											<span
												class="portal-order-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border <?php echo esc_attr($badge['class']); ?>">
												<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 <?php echo esc_attr($badge['dot']); ?>"></span>
												<span><?php echo esc_html($badge['label']); ?></span>
											</span>
										</td>

										<!-- 9. Status Update Form -->
										<td class="py-4 px-4 align-middle text-center whitespace-nowrap">
											<form
												class="portal-order-status-form inline-flex items-center justify-center gap-1.5">
												<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">

												<select name="new_status"
													class="text-[11px] font-bold py-1.5 pr-2.5 pl-6 bg-slate-50 hover:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-rose-500 cursor-pointer appearance-none bg-no-repeat bg-[left_0.4rem_center] text-right"
													style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url(&quot;data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e&quot;); background-position: left 0.4rem center; background-repeat: no-repeat; background-size: 0.9rem 0.9rem; text-align: right; direction: rtl; padding-right: 0.625rem; padding-left: 1.5rem;">
													<option value="processing" <?php selected($status, 'processing'); ?>>قيد المعالجة</option>
													<option value="shipped" <?php selected($status, 'shipped'); ?>>تم الشحن</option>
													<option value="completed" <?php selected($status, 'completed'); ?>>تم التسليم</option>
													<option value="cancelled" <?php selected($status, 'cancelled'); ?>>ملغي</option>
												</select>

												<button type="submit"
													class="px-2.5 py-1.5 rounded-lg text-[11px] font-extrabold text-white bg-slate-900 hover:bg-rose-600 transition-colors flex-shrink-0 shadow-2xs">
													حفظ
												</button>
											</form>
										</td>

										<!-- 10. Actions -->
										<td class="py-4 px-4 align-middle text-center whitespace-nowrap">
											<div class="inline-flex items-center justify-center gap-1.5 relative">
												<!-- View Order Details -->
												<button type="button" data-order-id="<?php echo esc_attr($order_id); ?>"
													class="view-order-details-btn w-8 h-8 rounded-xl text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center transition-colors shadow-2xs flex-shrink-0"
													title="عرض التفاصيل" aria-label="عرض التفاصيل">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
													</svg>
												</button>

												<!-- WhatsApp Contact -->
												<?php if ($wa_url): ?>
													<a href="<?php echo esc_url($wa_url); ?>" target="_blank"
														rel="noopener noreferrer"
														class="w-8 h-8 rounded-xl bg-[#25D366] hover:bg-[#20ba59] active:scale-95 text-white flex items-center justify-center transition-all duration-200 shadow-2xs hover:shadow-xs focus:ring-2 focus:ring-emerald-400 focus:outline-none flex-shrink-0"
														title="التواصل مع العميل عبر واتساب"
														aria-label="التواصل مع العميل عبر واتساب">
														<svg class="w-4 h-4 fill-white" viewBox="0 0 16 16">
															<path
																d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
														</svg>
													</a>
												<?php endif; ?>

												<!-- Three Dots Overflow Menu -->
												<div class="relative">
													<button type="button"
														data-target-menu="overflow-menu-<?php echo esc_attr($order_id); ?>"
														class="portal-overflow-menu-btn w-8 h-8 rounded-xl text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center transition-colors shadow-2xs flex-shrink-0"
														title="خيارات إضافية" aria-label="خيارات إضافية">
														<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
															<path
																d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
														</svg>
													</button>

													<div id="overflow-menu-<?php echo esc_attr($order_id); ?>"
														class="portal-overflow-dropdown hidden absolute left-0 right-auto top-full mt-1.5 w-48 bg-white rounded-xl shadow-xl border border-slate-200/90 py-1.5 z-50 text-right">
														<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
															target="_blank"
															class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-rose-600 transition-colors">
															<svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none"
																stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2"
																	d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
															</svg>
															<span>معاينة الفاتورة للعميل</span>
														</a>
														<button type="button"
															onclick="navigator.clipboard.writeText('<?php echo esc_js($order_id); ?>'); alert('تم نسخ رقم الطلب: <?php echo esc_js($order_id); ?>');"
															class="w-full flex items-center gap-2 text-right px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-rose-600 transition-colors">
															<svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none"
																stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round"
																	stroke-width="2"
																	d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
															</svg>
															<span>نسخ رقم الطلب</span>
														</button>
													</div>
												</div>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<!-- 6. Bottom Pagination Bar -->
					<div
						class="p-4 sm:p-5 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
						<!-- Range Info -->
						<div class="text-xs font-bold text-slate-500">
							<?php
							$start_item = (($orders_current_page - 1) * $orders_per_page) + 1;
							$end_item = min($orders_current_page * $orders_per_page, $orders_total_count);
							if ($orders_total_count > 0) {
								printf(esc_html__('عرض %d إلى %d من إجمالي %d طلب', 'ashrafco'), $start_item, $end_item, $orders_total_count);
							} else {
								esc_html_e('لا توجد طلبات للعرض', 'ashrafco');
							}
							?>
						</div>

						<!-- Page Numbers -->
						<?php if ($orders_total_pages > 1): ?>
							<div class="flex items-center gap-1.5 flex-wrap">
								<?php
								// Previous button
								if ($orders_current_page > 1) {
									$prev_url = add_query_arg(array('tab' => 'orders', 'paged' => $orders_current_page - 1));
									echo '<a href="' . esc_url($prev_url) . '" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition-colors">&lt;</a>';
								}

								// Numeric buttons
								for ($p = 1; $p <= $orders_total_pages; $p++) {
									if ($p === $orders_current_page) {
										echo '<span class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-600 text-white text-xs font-black shadow-xs">' . esc_html($p) . '</span>';
									} elseif ($p <= 3 || $p === $orders_total_pages || abs($p - $orders_current_page) <= 1) {
										$p_url = add_query_arg(array('tab' => 'orders', 'paged' => $p));
										echo '<a href="' . esc_url($p_url) . '" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-colors">' . esc_html($p) . '</a>';
									} elseif ($p === 4 && $orders_total_pages > 5) {
										echo '<span class="px-1 text-slate-400 font-bold">...</span>';
									}
								}

								// Next button
								if ($orders_current_page < $orders_total_pages) {
									$next_url = add_query_arg(array('tab' => 'orders', 'paged' => $orders_current_page + 1));
									echo '<a href="' . esc_url($next_url) . '" class="w-8 h-8 rounded-lg flex items-center justify-center border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition-colors">&gt;</a>';
								}
								?>
							</div>
						<?php endif; ?>
					</div>

				<?php else: ?>
					<div class="text-center py-12 p-4 sm:p-8">
						<div
							class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
							<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
							</svg>
						</div>
						<h3 class="text-base font-extrabold text-slate-800">لا توجد طلبات مطابقة للفلاتر المحددة</h3>
						<p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
							يرجى تجربة تعديل كلمات البحث أو مسح الفلاتر لعرض الطلبات المسجلة.
						</p>
						<button type="button"
							onclick="window.location.href='<?php echo esc_url(home_url('/admin-portal/?tab=orders')); ?>'"
							class="mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-xs">
							إعادة ضبط جميع الفلاتر
						</button>
					</div>
				<?php endif; ?>
			</div>

		</div>

		<!-- =================================================================
			 ORDER DETAILS MODAL (نافذة تفاصيل الطلب الشاملة)
			 ================================================================= -->
		<div id="portal-order-details-modal"
			class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
			<!-- Modal Backdrop -->
			<div id="portal-modal-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity">
			</div>

			<!-- Modal Card Container -->
			<div
				class="relative bg-white rounded-2xl border border-slate-200/90 shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden z-10 animate-scaleIn">
				<!-- Modal Header -->
				<div
					class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4 bg-slate-50/50">
					<div class="flex items-center gap-3">
						<h3 class="text-base sm:text-lg font-black text-slate-900">
							تفاصيل الطلب <span id="modal-order-id" class="font-mono text-rose-600">#</span>
						</h3>
						<span id="modal-order-badge"
							class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border"></span>
					</div>
					<div class="flex items-center gap-2">
						<a id="modal-admin-link" href="#" target="_blank"
							class="p-1.5 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors"
							title="فتح في لوحة التحكم">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
							</svg>
						</a>
						<button type="button"
							class="close-order-modal-btn p-1.5 rounded-xl text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-colors"
							title="إغلاق">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
				</div>

				<!-- Modal Loading Skeleton -->
				<div id="portal-modal-loader" class="p-12 text-center">
					<svg class="animate-spin w-8 h-8 text-rose-600 mx-auto" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
						</circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
					</svg>
					<p class="text-xs font-bold text-slate-400 mt-3">جاري تحميل تفاصيل الطلب...</p>
				</div>

				<!-- Modal Body Content -->
				<div id="portal-modal-content-area" class="hidden overflow-y-auto p-4 sm:p-6 space-y-5 text-xs">
					<!-- Customer & Date Strip -->
					<div
						class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50/70 rounded-xl p-4 border border-slate-100">
						<div>
							<div class="text-[11px] font-bold text-slate-400">العميل</div>
							<div id="modal-cust-name" class="font-extrabold text-slate-900 text-sm mt-0.5"></div>
							<div class="flex items-center gap-2 mt-1">
								<a id="modal-cust-phone" href="#"
									class="font-mono text-rose-600 font-bold hover:underline" dir="ltr"></a>
								<a id="modal-cust-whatsapp" href="#" target="_blank"
									class="hidden inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-200">
									<span>واتساب</span>
								</a>
							</div>
							<div id="modal-cust-location" class="text-slate-500 mt-1"></div>
						</div>

						<div class="sm:text-left">
							<div class="text-[11px] font-bold text-slate-400">تاريخ وتوقيت الطلب</div>
							<div id="modal-order-date" class="font-mono font-bold text-slate-800 text-xs mt-0.5"
								dir="ltr"></div>
							<div class="text-[11px] font-bold text-slate-400 mt-2">طريقة الدفع</div>
							<div id="modal-payment-title" class="font-bold text-slate-800 text-xs mt-0.5"></div>
						</div>
					</div>

					<!-- Ordered Products Table -->
					<div>
						<div class="font-black text-slate-900 text-xs mb-2">المنتجات المطلوبة</div>
						<div class="border border-slate-100 rounded-xl overflow-hidden">
							<table class="w-full text-right">
								<thead
									class="bg-slate-50 text-[11px] font-bold text-slate-500 border-b border-slate-100">
									<tr>
										<th class="py-2.5 px-3">المنتج</th>
										<th class="py-2.5 px-3 text-center">سعر الوحدة</th>
										<th class="py-2.5 px-3 text-center">الكمية</th>
										<th class="py-2.5 px-3 text-left">الإجمالي</th>
									</tr>
								</thead>
								<tbody id="modal-items-tbody" class="divide-y divide-slate-100"></tbody>
							</table>
						</div>
					</div>

					<!-- Financial Summary Table -->
					<div class="border-t border-slate-100 pt-3 space-y-1.5 font-bold text-slate-600">
						<div class="flex items-center justify-between text-xs">
							<span>المجموع الفرعي:</span>
							<span id="modal-subtotal" class="font-mono text-slate-800"></span>
						</div>
						<div class="flex items-center justify-between text-xs">
							<span>الشحن:</span>
							<span id="modal-shipping" class="font-mono text-slate-800"></span>
						</div>
						<div class="flex items-center justify-between text-xs">
							<span>الخصم:</span>
							<span id="modal-discount" class="font-mono text-emerald-600"></span>
						</div>
						<div
							class="flex items-center justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-100">
							<span>الإجمالي النهائي:</span>
							<span id="modal-grand-total" class="font-mono text-rose-600"></span>
						</div>
					</div>

					<!-- Customer Note -->
					<div id="modal-customer-note-container"
						class="hidden bg-amber-50/70 border border-amber-200/70 rounded-xl p-3">
						<div class="font-bold text-amber-800 text-[11px] mb-0.5">ملاحظات العميل:</div>
						<div id="modal-customer-note" class="text-amber-900 text-xs"></div>
					</div>

					<!-- Quick Status Update inside Modal -->
					<div
						class="bg-slate-50 rounded-xl p-3.5 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
						<div class="font-bold text-slate-700 text-xs">تحديث حالة هذا الطلب:</div>
						<form id="modal-status-update-form" class="portal-order-status-form flex items-center gap-2">
							<input type="hidden" id="modal-order-id-input" name="order_id" value="">
							<select id="modal-status-select" name="new_status"
								class="text-xs font-bold py-1.5 pr-2.5 pl-6 bg-white border border-slate-200 rounded-lg text-slate-700">
								<option value="processing">قيد المعالجة</option>
								<option value="shipped">تم الشحن</option>
								<option value="completed">تم التسليم</option>
								<option value="cancelled">ملغي</option>
							</select>
							<button type="submit"
								class="px-4 py-1.5 rounded-lg text-xs font-extrabold text-white bg-slate-900 hover:bg-rose-600 transition-colors">
								حفظ الحالة
							</button>
						</form>
					</div>
				</div>

				<!-- Modal Footer -->
				<div class="p-3.5 sm:p-4 border-t border-slate-100 flex items-center justify-end bg-slate-50/50">
					<button type="button"
						class="close-order-modal-btn px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 transition-colors">
						إغلاق النافذة
					</button>
				</div>
			</div>
		</div>
