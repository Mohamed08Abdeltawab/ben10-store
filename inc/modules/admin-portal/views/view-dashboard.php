<?php
/**
 * Admin Portal View: Dashboard & Store Analytics (Tab 1)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB 1: الإحصائيات العامة (Overview & Analytics)
			 ================================================================= -->
		<div id="portal-tab-overview"
			class="tab-content <?php echo 'overview' === $active_tab ? 'active' : ''; ?> space-y-6 sm:space-y-7">

			<!-- 1. Dashboard Heading & Date Range Filter Section -->
			<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-1">
				<!-- Right in RTL: Heading and Subtitle -->
				<div class="flex items-center gap-2.5">
					<svg class="w-7 h-7 sm:w-8 sm:h-8 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
							d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
					</svg>
					<div>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight">
							الإحصائيات العامة
						</h1>
						<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5">
							نظرة سريعة على أداء متجرك والمبيعات والطلبات.
						</p>
					</div>
				</div>

				<!-- Left in RTL: Date Filter Buttons & Custom Date Dropdown -->
				<div class="flex items-center gap-2 flex-wrap">
					<!-- Date Preset Buttons (Separate white rounded pills matching reference screenshot) -->
					<button type="button" data-period="today"
						class="portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs <?php echo 'today' === $selected_period ? 'bg-rose-600 border border-rose-600 text-white shadow-xs font-black' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
						اليوم
					</button>
					<button type="button" data-period="last_7_days"
						class="portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs <?php echo 'last_7_days' === $selected_period ? 'bg-rose-600 border border-rose-600 text-white shadow-xs font-black' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
						آخر 7 أيام
					</button>
					<button type="button" data-period="last_30_days"
						class="portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs <?php echo 'last_30_days' === $selected_period ? 'bg-rose-600 border border-rose-600 text-white shadow-xs font-black' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
						آخر 30 يوم
					</button>
					<button type="button" data-period="this_month"
						class="portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs <?php echo 'this_month' === $selected_period ? 'bg-rose-600 border border-rose-600 text-white shadow-xs font-black' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
						الشهر الحالي
					</button>
					<button type="button" data-period="custom"
						class="portal-period-btn px-4 py-2 rounded-xl text-xs font-bold transition-all select-none whitespace-nowrap shadow-2xs <?php echo 'custom' === $selected_period ? 'bg-rose-600 border border-rose-600 text-white shadow-xs font-black' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50'; ?>">
						تخصيص
					</button>

					<!-- Date Range Display Box with Popover (on far left in RTL) -->
					<div class="relative">
						<button type="button" id="portal-date-range-btn"
							class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition-all shadow-2xs select-none"
							title="تحديد فترة مخصصة">
							<svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 9l-7 7-7-7" />
							</svg>
							<span id="portal-date-range-label" class="font-mono text-xs text-slate-800">
								<?php echo esc_html($portal_analytics['range']['label']); ?>
							</span>
							<svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</button>

						<!-- Custom Date Range Popover -->
						<div id="custom-date-popover"
							class="hidden absolute top-full mt-2 left-0 sm:right-auto sm:left-0 z-50 bg-white rounded-2xl shadow-xl border border-slate-200 p-4 w-72 sm:w-80 space-y-3">
							<div
								class="text-xs font-extrabold text-slate-800 pb-2 border-b border-slate-100 flex items-center justify-between">
								<span>تحديد فترة مخصصة</span>
								<button type="button" id="close-popover-btn"
									class="text-slate-400 hover:text-slate-600 text-sm font-bold">&times;</button>
							</div>
							<div class="space-y-2">
								<div>
									<label class="block text-[11px] font-bold text-slate-500 mb-1">من تاريخ:</label>
									<input type="date" id="portal-start-date"
										value="<?php echo esc_attr($portal_analytics['range']['start_ymd']); ?>"
										class="w-full text-xs font-mono font-bold px-3 py-2 border border-slate-200 rounded-xl focus:border-rose-500 focus:outline-none">
								</div>
								<div>
									<label class="block text-[11px] font-bold text-slate-500 mb-1">إلى تاريخ:</label>
									<input type="date" id="portal-end-date"
										value="<?php echo esc_attr($portal_analytics['range']['end_ymd']); ?>"
										class="w-full text-xs font-mono font-bold px-3 py-2 border border-slate-200 rounded-xl focus:border-rose-500 focus:outline-none">
								</div>
							</div>
							<div class="flex items-center gap-2 pt-2">
								<button type="button" id="apply-custom-date-btn"
									class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs py-2 px-3 rounded-xl transition-all shadow-xs">
									تطبيق التصفية
								</button>
								<button type="button" id="cancel-custom-date-btn"
									class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs py-2 px-3 rounded-xl transition-all">
									إلغاء
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- 2. Four Key KPI Metric Cards -->
			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
				<!-- Metric 1: Net Revenue -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div class="space-y-1">
						<span class="text-xs font-bold text-slate-500 block">صافي الإيرادات المسجلة</span>
						<div class="flex items-baseline gap-2">
							<span id="kpi-net-sales-value"
								class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">
								<?php echo esc_html($portal_analytics['kpis']['net_revenue']['formatted']); ?>
							</span>
							<span id="kpi-net-sales-trend"
								class="text-xs font-bold <?php echo esc_attr($portal_analytics['kpis']['net_revenue']['trend']['badge_css']); ?> inline-flex items-center gap-0.5"
								dir="ltr">
								<?php echo esc_html($portal_analytics['kpis']['net_revenue']['trend']['formatted']); ?>
								<?php echo 'up' === $portal_analytics['kpis']['net_revenue']['trend']['direction'] ? '▲' : ('down' === $portal_analytics['kpis']['net_revenue']['trend']['direction'] ? '▼' : ''); ?>
							</span>
						</div>
						<span class="text-[11px] font-semibold text-slate-400 block pt-0.5">
							إجمالي مبيعات الطلبات المكتملة وقيد المعالجة
						</span>
					</div>
					<div
						class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>

				<!-- Metric 2: Total Orders -->
				<div
					class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm">
					<div class="space-y-1">
						<span class="text-xs font-bold text-slate-500 block">إجمالي عدد الطلبات</span>
						<div class="flex items-baseline gap-2">
							<span id="kpi-total-orders-value"
								class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight">
								<?php echo esc_html($portal_analytics['kpis']['total_orders']['raw']); ?>
							</span>
							<span id="kpi-total-orders-trend"
								class="text-xs font-bold <?php echo esc_attr($portal_analytics['kpis']['total_orders']['trend']['badge_css']); ?> inline-flex items-center gap-0.5"
								dir="ltr">
								<?php echo esc_html($portal_analytics['kpis']['total_orders']['trend']['formatted']); ?>
								<?php echo 'up' === $portal_analytics['kpis']['total_orders']['trend']['direction'] ? '▲' : ('down' === $portal_analytics['kpis']['total_orders']['trend']['direction'] ? '▼' : ''); ?>
							</span>
						</div>
						<span class="text-[11px] font-semibold text-slate-400 block pt-0.5">
							مقارنة بالفترة السابقة
						</span>
					</div>
					<div
						class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
					</div>
				</div>

				<!-- Metric 3: Pending Action -->
				<div onclick="switchPortalTab('orders')"
					class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm cursor-pointer group hover:border-amber-300">
					<div class="space-y-1">
						<span class="text-xs font-bold text-slate-500 block">طلبات قيد المتابعة</span>
						<div class="flex items-baseline gap-2">
							<span id="kpi-pending-orders-value"
								class="text-2xl sm:text-3xl font-black text-amber-600 font-mono tracking-tight">
								<?php echo esc_html($portal_analytics['kpis']['pending_orders']['raw']); ?>
							</span>
							<span id="kpi-pending-orders-trend"
								class="text-xs font-bold text-amber-600 inline-flex items-center gap-0.5" dir="ltr">
								<?php echo esc_html($portal_analytics['kpis']['pending_orders']['trend']['formatted']); ?>
								<?php echo 'up' === $portal_analytics['kpis']['pending_orders']['trend']['direction'] ? '▲' : ('down' === $portal_analytics['kpis']['pending_orders']['trend']['direction'] ? '▼' : ''); ?>
							</span>
						</div>
						<span
							class="text-[11px] font-semibold text-slate-400 block pt-0.5 group-hover:text-amber-600 transition-colors">
							تحتاج للمراجعة وتأكيد الشحن
						</span>
					</div>
					<div
						class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>

				<!-- Metric 4: Inventory Alerts -->
				<div onclick="switchPortalTab('products')"
					class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex items-center justify-between transition-all hover:shadow-sm cursor-pointer group hover:border-rose-300">
					<div class="space-y-1">
						<span class="text-xs font-bold text-slate-500 block">تنبيهات المخزون</span>
						<div class="flex items-baseline gap-2">
							<span id="kpi-stock-alerts-value"
								class="text-2xl sm:text-3xl font-black <?php echo $portal_analytics['kpis']['stock_alerts']['raw'] > 0 ? 'text-rose-600' : 'text-slate-900'; ?> font-mono tracking-tight">
								<?php echo esc_html($portal_analytics['kpis']['stock_alerts']['raw']); ?>
							</span>
							<span id="kpi-stock-alerts-trend"
								class="text-xs font-bold text-rose-600 inline-flex items-center gap-0.5" dir="ltr">
								<?php echo esc_html($portal_analytics['kpis']['stock_alerts']['trend']['formatted']); ?>
								<?php echo 'up' === $portal_analytics['kpis']['stock_alerts']['trend']['direction'] ? '▲' : ''; ?>
							</span>
						</div>
						<span
							class="text-[11px] font-semibold text-slate-400 block pt-0.5 group-hover:text-rose-600 transition-colors">
							منتجات منخفضة المخزون أو نفذت
						</span>
					</div>
					<div
						class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
					</div>
				</div>
			</div>

			<!-- 3. Main Analytics Row: Sales Chart + Order Status Donut -->
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
				<!-- Right Column: Sales & Orders Over Time -->
				<div
					class="lg:col-span-8 bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
					<div>
						<!-- Header & Legends -->
						<div
							class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100/80">
							<div class="flex items-center gap-2.5">
								<svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
								</svg>
								<div>
									<h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
										المبيعات والطلبات خلال الفترة
									</h2>
									<p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
										مقارنة بين إجمالي المبيعات وعدد الطلبات عبر الأيام.
									</p>
								</div>
							</div>

							<!-- Legends matching reference screenshot -->
							<div class="flex items-center gap-4 text-xs font-bold text-slate-600 select-none">
								<span class="inline-flex items-center gap-1.5">
									<span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
									<span>المبيعات (جنيه مصري)</span>
								</span>
								<span class="inline-flex items-center gap-1.5">
									<span class="w-2.5 h-2.5 rounded-full bg-slate-900"></span>
									<span>عدد الطلبات</span>
								</span>
							</div>
						</div>

						<!-- Canvas Container -->
						<div class="relative w-full h-[280px] sm:h-[320px] mt-3">
							<canvas id="ashrafcoSalesOrdersChart"></canvas>
						</div>
					</div>
				</div>

				<!-- Left Column: Order Status Distribution -->
				<div
					class="lg:col-span-4 bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
					<div>
						<!-- Header -->
						<div class="flex items-center gap-2.5 pb-3 border-b border-slate-100/80">
							<svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
							<h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
								توزيع الطلبات حسب الحالة
							</h2>
						</div>

						<!-- Donut Chart & Status Breakdown -->
						<div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 mt-4">
							<!-- Status Breakdown List (Right side in RTL) -->
							<div id="status-distribution-list" class="flex-1 w-full space-y-2">
								<?php foreach ($portal_analytics['chart_status']['breakdown'] as $st_item): ?>
									<div
										class="flex items-center justify-between gap-2 py-1.5 border-b border-slate-50 last:border-0">
										<div class="flex items-center gap-2">
											<span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
												style="background-color: <?php echo esc_attr($st_item['color']); ?>;"></span>
											<span
												class="text-xs font-bold text-slate-700"><?php echo esc_html($st_item['label']); ?></span>
										</div>
										<div class="flex items-center gap-3 font-mono text-xs">
											<span
												class="font-extrabold text-slate-900"><?php echo esc_html($st_item['count']); ?></span>
											<span class="text-slate-400 text-[11px] w-12 text-left"
												dir="ltr"><?php echo esc_html($st_item['percentage']); ?>%</span>
										</div>
									</div>
								<?php endforeach; ?>
							</div>

							<!-- Donut Canvas Container (Left side in RTL) -->
							<div
								class="relative w-36 h-36 sm:w-40 sm:h-40 flex-shrink-0 flex items-center justify-center">
								<canvas id="ashrafcoStatusDonutChart"></canvas>
								<div
									class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
									<span id="donut-total-count"
										class="text-2xl sm:text-3xl font-black text-slate-900 font-mono leading-none">
										<?php echo esc_html($portal_analytics['chart_status']['total_count']); ?>
									</span>
									<span class="text-xs font-bold text-slate-400 mt-1">طلب</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- 4. Recent Orders Table (آخر الطلبات) -->
			<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
				<!-- Header -->
				<div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between gap-4">
					<div class="flex items-center gap-2.5">
						<svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
						<div>
							<h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
								آخر الطلبات
							</h2>
							<p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
								أحدث الطلبات التي تم إنشاؤها في متجرك.
							</p>
						</div>
					</div>

					<button type="button" onclick="switchPortalTab('orders')"
						class="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50/70 hover:bg-rose-100 border border-rose-200/80 transition-all select-none shadow-2xs">
						<span>عرض كل الطلبات</span>
						<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M14 5l7 7m0 0l-7 7m7-7H3" />
						</svg>
					</button>
				</div>

				<!-- Table Body -->
				<div class="overflow-x-auto">
					<table class="w-full text-right border-collapse min-w-[750px]">
						<thead>
							<tr
								class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
								<th class="py-3 px-4">رقم الطلب</th>
								<th class="py-3 px-4">العميل</th>
								<th class="py-3 px-4">التاريخ</th>
								<th class="py-3 px-4">الحالة</th>
								<th class="py-3 px-4">الإجمالي</th>
								<th class="py-3 px-4 text-center">الإجراءات</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100 text-xs font-medium">
							<?php if (!empty($portal_analytics['recent_orders'])): ?>
								<?php foreach ($portal_analytics['recent_orders'] as $r_item): ?>
									<tr class="hover:bg-slate-50/70 transition-colors">
										<!-- Order ID -->
										<td class="py-3.5 px-4 font-black text-slate-900 font-mono">
											<?php echo esc_html($r_item['order_number']); ?>
										</td>

										<!-- Customer Name -->
										<td class="py-3.5 px-4 font-bold text-slate-800">
											<?php echo esc_html($r_item['customer_name']); ?>
										</td>

										<!-- Date -->
										<td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
											<?php echo esc_html($r_item['date']); ?>
										</td>

										<!-- Status Badge -->
										<td class="py-3.5 px-4">
											<span
												class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold border <?php echo esc_attr($r_item['badge_class']); ?>">
												<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 <?php echo esc_attr(!empty($r_item['badge_dot']) ? $r_item['badge_dot'] : 'bg-slate-400'); ?>"></span>
												<span><?php echo esc_html($r_item['status_label']); ?></span>
											</span>
										</td>

										<!-- Total Amount -->
										<td class="py-3.5 px-4 font-black text-slate-900 font-mono">
											<?php echo esc_html($r_item['formatted_total']); ?>
										</td>

										<!-- Actions -->
										<td class="py-3.5 px-4 text-center">
											<div class="inline-flex items-center gap-1.5 justify-center">
												<a href="<?php echo esc_url($r_item['view_url']); ?>" target="_blank"
													class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors"
													title="معاينة تفاصيل الفاتورة">
													عرض التفاصيل
												</a>
												<button type="button" onclick="switchPortalTab('orders')"
													class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
													title="الانتقال لجدول إدارة الطلبات">
													<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
														<path
															d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
													</svg>
												</button>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="6" class="py-8 text-center text-xs font-bold text-slate-400">
										لا توجد طلبات مسجلة حالياً في المتجر.
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 5. Bottom Row: Best-Selling Products & Low-Stock Inventory -->
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
				<!-- Right Card: المنتجات الأكثر مبيعًا -->
				<div
					class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between overflow-hidden p-4 sm:p-5">
					<div>
						<!-- Header -->
						<div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
							<svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
							</svg>
							<div>
								<h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
									المنتجات الأكثر مبيعًا
								</h2>
								<p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
									أكثر 5 منتجات مبيعاً خلال الفترة المحددة
								</p>
							</div>
						</div>

						<!-- Table -->
						<div class="overflow-x-auto mt-2">
							<table class="w-full text-right border-collapse text-xs">
								<thead>
									<tr
										class="text-[11px] font-extrabold uppercase text-slate-400 border-b border-slate-100">
										<th class="py-2.5 px-3 text-center w-8">#</th>
										<th class="py-2.5 px-3">المنتج</th>
										<th class="py-2.5 px-3">SKU</th>
										<th class="py-2.5 px-3 text-left">عدد المبيعات</th>
									</tr>
								</thead>
								<tbody id="top-selling-table-body" class="divide-y divide-slate-100">
									<?php if (!empty($portal_analytics['top_selling'])): ?>
										<?php foreach ($portal_analytics['top_selling'] as $top_p): ?>
											<tr class="hover:bg-slate-50/70 transition-colors">
												<td
													class="py-3 px-3 align-middle font-bold text-slate-400 font-mono text-center w-8">
													<?php echo esc_html($top_p['rank']); ?>
												</td>
												<td class="py-3 px-3 align-middle">
													<div class="flex items-center gap-2.5">
														<img src="<?php echo esc_url($top_p['thumb_url']); ?>"
															alt="<?php echo esc_attr($top_p['name']); ?>"
															class="w-10 h-10 rounded-xl object-cover border border-slate-100 flex-shrink-0 bg-slate-50">
														<a href="<?php echo esc_url($top_p['edit_url']); ?>"
															class="text-xs font-bold text-slate-800 hover:text-rose-600 transition-colors line-clamp-1 max-w-[200px] sm:max-w-xs">
															<?php echo esc_html($top_p['name']); ?>
														</a>
													</div>
												</td>
												<td class="py-3 px-3 align-middle font-mono text-xs text-slate-400">
													<?php echo esc_html($top_p['sku']); ?>
												</td>
												<td
													class="py-3 px-3 align-middle font-black text-slate-900 font-mono text-left">
													<?php echo esc_html($top_p['units_sold']); ?>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="4" class="py-6 text-center text-xs font-bold text-slate-400">
												لا توجد مبيعات مسجلة للمنتجات خلال الفترة المحددة.
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>

					<!-- Card Footer Action -->
					<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-start">
						<button type="button" onclick="switchPortalTab('products')"
							class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50/70 hover:bg-rose-100 border border-rose-200/80 transition-all select-none shadow-2xs">
							<span>عرض كل المنتجات</span>
							<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14 5l7 7m0 0l-7 7m7-7H3" />
							</svg>
						</button>
					</div>
				</div>

				<!-- Left Card: المنتجات منخفضة المخزون -->
				<div
					class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between overflow-hidden p-4 sm:p-5">
					<div>
						<!-- Header -->
						<div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
							<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
							<div>
								<h2 class="text-sm sm:text-base font-extrabold text-slate-900 leading-tight">
									المنتجات منخفضة المخزون
								</h2>
								<p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-0.5">
									المنتجات التي وصلت إلى حد التنبيه أو نفذت مخزونها
								</p>
							</div>
						</div>

						<!-- Table -->
						<div class="overflow-x-auto mt-2">
							<table class="w-full text-right border-collapse text-xs">
								<thead>
									<tr
										class="text-[11px] font-extrabold uppercase text-slate-400 border-b border-slate-100">
										<th class="py-2.5 px-3 text-center w-8">#</th>
										<th class="py-2.5 px-3">المنتج</th>
										<th class="py-2.5 px-3">SKU</th>
										<th class="py-2.5 px-3 text-center">المخزون الحالي</th>
										<th class="py-2.5 px-3 text-left">الحالة</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-slate-100">
									<?php if (!empty($portal_analytics['low_stock'])): ?>
										<?php foreach ($portal_analytics['low_stock'] as $low_p): ?>
											<tr class="hover:bg-slate-50/70 transition-colors">
												<td
													class="py-3 px-3 align-middle font-bold text-slate-400 font-mono text-center w-8">
													<?php echo esc_html($low_p['rank']); ?>
												</td>
												<td class="py-3 px-3 align-middle">
													<div class="flex items-center gap-2.5">
														<img src="<?php echo esc_url($low_p['thumb_url']); ?>"
															alt="<?php echo esc_attr($low_p['name']); ?>"
															class="w-10 h-10 rounded-xl object-cover border border-slate-100 flex-shrink-0 bg-slate-50">
														<a href="<?php echo esc_url($low_p['edit_url']); ?>"
															class="text-xs font-bold text-slate-800 hover:text-rose-600 transition-colors line-clamp-1 max-w-[180px] sm:max-w-xs">
															<?php echo esc_html($low_p['name']); ?>
														</a>
													</div>
												</td>
												<td class="py-3 px-3 align-middle font-mono text-xs text-slate-400">
													<?php echo esc_html($low_p['sku']); ?>
												</td>
												<td
													class="py-3 px-3 align-middle font-black font-mono text-center <?php echo $low_p['is_zero'] ? 'text-rose-600' : 'text-amber-600'; ?>">
													<?php echo esc_html($low_p['stock_qty']); ?>
												</td>
												<td class="py-3 px-3 align-middle text-left">
													<span
														class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-extrabold border <?php echo esc_attr($low_p['badge_class']); ?>">
														<?php echo esc_html($low_p['status_label']); ?>
													</span>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="5" class="py-6 text-center text-xs font-bold text-slate-400">
												جميع المنتجات متوفرة بمخزون كافٍ ولا توجد تنبيهات حالياً.
											</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>

					<!-- Card Footer Action -->
					<div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-start">
						<button type="button" onclick="switchPortalTab('products')"
							class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50/70 hover:bg-rose-100 border border-rose-200/80 transition-all select-none shadow-2xs">
							<span>إدارة المخزون</span>
							<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14 5l7 7m0 0l-7 7m7-7H3" />
							</svg>
						</button>
					</div>
				</div>
			</div>

		</div>

