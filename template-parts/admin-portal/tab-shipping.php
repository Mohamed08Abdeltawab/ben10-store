<!-- =================================================================
			 TAB: إدارة الشحن والتوصيل (Shipping & Regional Delivery Management)
			 ================================================================= -->
		<div id="portal-tab-shipping"
			class="tab-content <?php echo 'shipping' === $active_tab ? 'active' : ''; ?> space-y-6">

			<!-- 1. Header (Primary action left, Title & Icon right in RTL) -->
			<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					<div
						class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
						</svg>
					</div>
					<div>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight flex items-center gap-2">
							<span><?php esc_html_e('إدارة الشحن والتوصيل', 'ashrafco'); ?></span>
						</h1>
						<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
							<?php esc_html_e('إدارة المحافظات وأسعار الشحن ومتابعة الطلبات حسب المنطقة', 'ashrafco'); ?>
						</p>
					</div>
				</div>

				<button type="button" id="portal-add-governorate-btn"
					class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all shadow-xs cursor-pointer flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
					</svg>
					<span><?php esc_html_e('إضافة محافظة', 'ashrafco'); ?></span>
				</button>
			</div>

			<!-- 2. Four Statistics Cards (RTL: إجمالي المحافظات -> إجمالي طلبات الشحن -> متوسط سعر الشحن -> محافظات عليها طلبات) -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
				<!-- Card 1: إجمالي المحافظات -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<div id="portal-shipping-count-total" class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
							<?php echo esc_html( $shipping_summary['total_governorates'] ?? 27 ); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('إجمالي المحافظات', 'ashrafco'); ?>
						</div>
						<div class="text-[11px] font-medium text-slate-400 mt-0.5">
							<?php esc_html_e('محافظة مضافة', 'ashrafco'); ?>
						</div>
					</div>
				</div>

				<!-- Card 2: إجمالي طلبات الشحن -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
							</svg>
						</div>
						<div id="portal-shipping-count-orders" class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
							<?php echo esc_html( number_format_i18n( (int) ( $shipping_summary['total_orders'] ?? 0 ) ) ); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('إجمالي طلبات الشحن', 'ashrafco'); ?>
						</div>
						<div class="text-[11px] font-medium text-slate-400 mt-0.5">
							<?php esc_html_e('طلب مكتمل وقيد المعالجة', 'ashrafco'); ?>
						</div>
					</div>
				</div>

				<!-- Card 3: متوسط سعر الشحن -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</div>
						<div class="flex items-baseline gap-1 text-slate-900">
							<span id="portal-shipping-count-avg-price" class="text-2xl sm:text-3xl font-black font-mono">
								<?php echo esc_html( $shipping_summary['avg_price'] ?? 0 ); ?>
							</span>
							<span class="text-xs sm:text-sm font-bold text-slate-500"><?php esc_html_e('ج.م', 'ashrafco'); ?></span>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('متوسط سعر الشحن', 'ashrafco'); ?>
						</div>
						<div class="text-[11px] font-medium text-slate-400 mt-0.5">
							<?php esc_html_e('لكل طلب', 'ashrafco'); ?>
						</div>
					</div>
				</div>

				<!-- Card 4: محافظات عليها طلبات -->
				<div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex flex-col justify-between shadow-2xs transition-all hover:shadow-xs">
					<div class="flex items-center justify-between">
						<div
							class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
							</svg>
						</div>
						<div id="portal-shipping-count-active-govs" class="text-2xl sm:text-3xl font-black text-slate-900 font-mono">
							<?php echo esc_html( $shipping_summary['govs_with_orders'] ?? 0 ); ?>
						</div>
					</div>
					<div class="mt-3 text-right">
						<div class="text-xs sm:text-sm font-bold text-slate-500">
							<?php esc_html_e('محافظات عليها طلبات', 'ashrafco'); ?>
						</div>
						<div class="text-[11px] font-medium text-slate-400 mt-0.5">
							<?php esc_html_e('من إجمالي المحافظات', 'ashrafco'); ?>
						</div>
					</div>
				</div>
			</div>

			<!-- 3. Filters and Search Bar -->
			<div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-2xs">
				<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
					<!-- Right: Search input -->
					<div class="relative flex-1">
						<input type="text" id="portal-shipping-search"
							placeholder="<?php esc_attr_e('ابحث عن محافظة...', 'ashrafco'); ?>"
							class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all">
						<svg class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</div>

					<!-- Left: Status Filter & Sort Dropdowns -->
					<div class="flex flex-wrap sm:flex-nowrap items-center gap-2">
						<!-- Status Filter -->
						<div class="relative min-w-[140px]">
							<select id="portal-shipping-filter-status"
								class="w-full appearance-none pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white focus:border-primary focus:outline-none transition-all cursor-pointer">
								<option value="all"><?php esc_html_e('كل المحافظات', 'ashrafco'); ?></option>
								<option value="active"><?php esc_html_e('نشطة فقط', 'ashrafco'); ?></option>
								<option value="inactive"><?php esc_html_e('معطّلة فقط', 'ashrafco'); ?></option>
							</select>
							<svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
							</svg>
						</div>

						<!-- Sorting -->
						<div class="relative min-w-[160px]">
							<select id="portal-shipping-filter-sort"
								class="w-full appearance-none pl-8 pr-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white focus:border-primary focus:outline-none transition-all cursor-pointer">
								<option value="orders_desc"><?php esc_html_e('ترتيب حسب الطلبات', 'ashrafco'); ?></option>
								<option value="price_asc"><?php esc_html_e('السعر: من الأقل للأعلى', 'ashrafco'); ?></option>
								<option value="price_desc"><?php esc_html_e('السعر: من الأعلى للأقل', 'ashrafco'); ?></option>
								<option value="name_asc"><?php esc_html_e('اسم المحافظة (أ-ي)', 'ashrafco'); ?></option>
							</select>
							<svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- 4. Governorates Table (RTL clean, plain text, no images) -->
			<div class="bg-white border border-slate-200/90 rounded-2xl shadow-2xs overflow-hidden">
				<div class="overflow-x-auto">
					<table class="w-full text-right text-xs sm:text-sm">
						<thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-200/90">
							<tr>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
										</svg>
										<span><?php esc_html_e('المحافظة', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span><?php esc_html_e('سعر الشحن (ج.م)', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
										</svg>
										<span><?php esc_html_e('عدد الطلبات', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
										</svg>
										<span><?php esc_html_e('عدد العملاء', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span><?php esc_html_e('الحالة', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap">
									<div class="flex items-center gap-1.5">
										<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span><?php esc_html_e('آخر تحديث', 'ashrafco'); ?></span>
									</div>
								</th>
								<th class="py-3.5 px-4 whitespace-nowrap text-center">
									<span><?php esc_html_e('الإجراءات', 'ashrafco'); ?></span>
								</th>
							</tr>
						</thead>
						<tbody id="portal-shipping-tbody" class="divide-y divide-slate-100">
							<!-- Loading Spinner -->
							<tr id="portal-shipping-loading-row">
								<td colspan="7" class="py-12 text-center text-slate-400">
									<div class="inline-flex items-center gap-2">
										<svg class="w-5 h-5 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
											<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
											<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
										</svg>
										<span><?php esc_html_e('جاري تحميل بيانات الشحن...', 'ashrafco'); ?></span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Pagination Bar -->
				<div id="portal-shipping-pagination" class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 bg-slate-50/60 border-t border-slate-200/80 text-xs text-slate-500">
					<div id="portal-shipping-pagination-info">
						<!-- Displayed items count -->
					</div>
					<div class="flex items-center gap-1" id="portal-shipping-pagination-btns">
						<!-- Page buttons dynamically rendered -->
					</div>
				</div>
			</div>

			<!-- 5. Bottom Row: Additional Statistics Chart (Left) + Shipping Notes (Right) -->
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
				<!-- Chart: أعلى المحافظات من حيث عدد الطلبات (7 cols) -->
				<div class="lg:col-span-7 bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs">
					<div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
						<div class="flex items-center gap-2">
							<div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
							<h3 class="text-sm font-extrabold text-slate-900">
								<?php esc_html_e('أعلى المحافظات من حيث عدد الطلبات', 'ashrafco'); ?>
							</h3>
						</div>
						<span class="text-[11px] font-bold text-slate-400"><?php esc_html_e('إحصائيات فعلية', 'ashrafco'); ?></span>
					</div>

					<div class="relative w-full h-56 sm:h-64">
						<canvas id="ashrafcoShippingTopChart"></canvas>
					</div>
				</div>

				<!-- Shipping Notes: ملاحظات الشحن (5 cols) -->
				<div class="lg:col-span-5 bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs flex flex-col justify-between">
					<div>
						<div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
							<div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
								</svg>
							</div>
							<h3 class="text-sm font-extrabold text-slate-900">
								<?php esc_html_e('ملاحظات الشحن', 'ashrafco'); ?>
							</h3>
						</div>

						<ul class="space-y-2.5 text-xs text-slate-600 leading-relaxed list-disc list-inside pr-1">
							<li><?php esc_html_e('أسعار الشحن المخصصة لكل محافظة يتم تطبيقها على مستوى الطلب بالكامل.', 'ashrafco'); ?></li>
							<li><?php esc_html_e('الإحصائيات معتمدة على الطلبات (قيد المعالجة، تم الشحن، وتم التسليم) فقط.', 'ashrafco'); ?></li>
							<li><?php esc_html_e('يمكنك إضافة أو تعديل أسعار الشحن في أي وقت وتطبيقها فوراً.', 'ashrafco'); ?></li>
							<li><?php esc_html_e('في حال تم تعطيل الشحن لمحافظة معينة، لن يتم احتساب سعر شحن تلقائي لها.', 'ashrafco'); ?></li>
							<li><?php esc_html_e('تأكد من مراجعة الأسعار بشكل دوري للتأكد من دقتها وموافقتها لسياسات الشحن.', 'ashrafco'); ?></li>
						</ul>
					</div>

					<div class="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
						<span><?php esc_html_e('نطاق التغطية: 27 محافظة داخل جمهورية مصر العربية', 'ashrafco'); ?></span>
					</div>
				</div>
			</div>

			<!-- 6. Add / Edit Governorate Modal -->
			<div id="portal-shipping-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" id="portal-shipping-modal-backdrop"></div>
				<div class="min-h-full flex items-center justify-center p-3 sm:p-4">
					<div class="relative bg-white rounded-3xl max-w-lg w-full p-5 sm:p-7 shadow-2xl border border-slate-100 space-y-5 text-right">
						<!-- Header -->
						<div class="flex items-center justify-between pb-3 border-b border-slate-100">
							<button type="button" id="portal-shipping-modal-close"
								class="w-8 h-8 rounded-full text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
							<div class="flex items-center gap-2">
								<div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
									</svg>
								</div>
								<h3 class="text-base font-black text-slate-900" id="portal-shipping-modal-title">
									<?php esc_html_e('إضافة محافظة جديدة', 'ashrafco'); ?>
								</h3>
							</div>
						</div>

						<!-- Form Error Notification -->
						<div id="portal-shipping-form-error" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold"></div>

						<!-- Form Fields -->
						<form id="portal-shipping-form" class="space-y-4">
							<input type="hidden" id="portal-shipping-field-code" value="">
							<input type="hidden" id="portal-shipping-field-is-edit" value="0">

							<!-- Governorate Name -->
							<div>
								<label class="block text-xs font-bold text-slate-700 mb-1.5" for="portal-shipping-field-name">
									<?php esc_html_e('اسم المحافظة', 'ashrafco'); ?> <span class="text-rose-500">*</span>
								</label>
								<input type="text" id="portal-shipping-field-name" required
									placeholder="<?php esc_attr_e('مثال: القاهرة، الجيزة، الإسكندرية...', 'ashrafco'); ?>"
									class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-bold text-slate-800 bg-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all">
							</div>

							<!-- Shipping Price (EGP) -->
							<div>
								<label class="block text-xs font-bold text-slate-700 mb-1.5" for="portal-shipping-field-price">
									<?php esc_html_e('سعر الشحن (ج.م)', 'ashrafco'); ?> <span class="text-rose-500">*</span>
								</label>
								<div class="relative">
									<input type="number" id="portal-shipping-field-price" min="0" step="1" required
										placeholder="0"
										class="w-full pl-12 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm font-black text-slate-800 font-mono bg-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all">
									<span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
										<?php esc_html_e('ج.م', 'ashrafco'); ?>
									</span>
								</div>
								<p class="text-[11px] text-slate-400 mt-1">
									<?php esc_html_e('السعر يطبق مباشرة على كافة طلبات هذه المحافظة.', 'ashrafco'); ?>
								</p>
							</div>

							<!-- Status (Active / Inactive) -->
							<div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
								<div>
									<div class="text-xs font-bold text-slate-800"><?php esc_html_e('حالة التفعيل', 'ashrafco'); ?></div>
									<div class="text-[11px] text-slate-400 mt-0.5"><?php esc_html_e('تفعيل أو تعطيل الشحن لهذه المحافظة فوراً', 'ashrafco'); ?></div>
								</div>
								<label class="relative inline-flex items-center cursor-pointer">
									<input type="checkbox" id="portal-shipping-field-active" checked class="sr-only peer">
									<div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
								</label>
							</div>

							<!-- Actions -->
							<div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
								<button type="button" id="portal-shipping-modal-cancel"
									class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
									<?php esc_html_e('إلغاء', 'ashrafco'); ?>
								</button>
								<button type="submit" id="portal-shipping-modal-submit"
									class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-primary hover:bg-[#b90323] transition-all cursor-pointer shadow-xs">
									<svg id="portal-shipping-submit-spinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
										<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
										<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
									</svg>
									<span id="portal-shipping-submit-label"><?php esc_html_e('حفظ المحافظة', 'ashrafco'); ?></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- 7. Deactivate / Delete Confirmation Modal -->
			<div id="portal-shipping-confirm-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
				<div class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs transition-opacity" id="portal-shipping-confirm-backdrop"></div>
				<div class="min-h-full flex items-center justify-center p-4">
					<div class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4 text-right">
						<div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-2">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
						</div>
						<h3 class="text-base sm:text-lg font-black text-slate-900 text-center" id="portal-shipping-confirm-title">
							<?php esc_html_e('تأكيد تغيير حالة الشحن', 'ashrafco'); ?>
						</h3>
						<p class="text-xs text-slate-500 text-center leading-relaxed" id="portal-shipping-confirm-text">
							<!-- Populated dynamically -->
						</p>
						<div class="flex items-center justify-center gap-3 pt-3">
							<button type="button" id="portal-shipping-confirm-cancel"
								class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">
								<?php esc_html_e('إلغاء', 'ashrafco'); ?>
							</button>
							<button type="button" id="portal-shipping-confirm-action"
								class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-primary hover:bg-[#b90323] transition-colors cursor-pointer shadow-xs">
								<?php esc_html_e('تأكيد', 'ashrafco'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>
