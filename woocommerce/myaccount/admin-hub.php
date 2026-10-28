<?php
/**
 * Custom Frontend Admin Dashboard Template
 * Rendered at /my-account/admin-hub/ for administrators.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

// Security Guard: Restrict strictly to users with manage_woocommerce, manage_options, or administrator capability.
if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options') && !current_user_can('administrator')) {
	?>
	<div class="p-3.5 sm:p-6 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-sm font-bold text-center">
		<?php esc_html_e('غير مصرح لك بالوصول إلى لوحة إدارة المتجر.', 'ashrafco'); ?>
	</div>
	<?php
	return;
}

$current_user = wp_get_current_user();

// -------------------------------------------------------------------------
// 1. Calculate Real-Time Store Metrics via WooCommerce Helper Functions
// -------------------------------------------------------------------------

// A. Total Orders Count (via optimized WooCommerce order count cache)
$total_orders_count = function_exists('wc_orders_count') ? (int) wc_orders_count('all') : 0;

// B. Net Sales (from completed and processing orders via direct SQL and transient)
$total_net_sales = function_exists('ashrafco_get_total_net_sales') ? (float) ashrafco_get_total_net_sales() : 0.0;

// C. Pending / Processing / On-Hold Orders Count
if (function_exists('wc_orders_count')) {
	$pending_count = (int) wc_orders_count('pending') + (int) wc_orders_count('processing') + (int) wc_orders_count('on-hold');
} else {
	$pending_count = 0;
}

// D. Low Stock and Out of Stock Alerts (via direct SQL inventory aggregation and transient)
$inventory_counts = function_exists('ashrafco_get_inventory_health_counts') ? ashrafco_get_inventory_health_counts() : array('low_stock' => 0, 'out_of_stock' => 0, 'total_alerts' => 0);
$low_stock_count = $inventory_counts['low_stock'];
$out_of_stock_count = $inventory_counts['out_of_stock'];
$total_stock_alerts = $inventory_counts['total_alerts'];

// -------------------------------------------------------------------------
// 2. Fetch Recent Orders for Management Table
// -------------------------------------------------------------------------
$recent_orders = wc_get_orders(array(
	'limit' => 8,
	'orderby' => 'date',
	'order' => 'DESC',
));

// -------------------------------------------------------------------------
// 3. Fetch Catalog Products for Quick Inventory Overview
// -------------------------------------------------------------------------
$catalog_products = wc_get_products(array(
	'limit' => 6,
	'orderby' => 'date',
	'order' => 'DESC',
));

$eg_states = WC()->countries ? WC()->countries->get_states('EG') : array();
?>

<div class="ashrafco-admin-hub w-full space-y-8 font-sans" dir="rtl">

	<!-- =====================================================================
		 1. Admin Hub Top Header & Back-End Switcher
		 ===================================================================== -->
	<div
		class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-4 sm:p-8 text-white shadow-lg relative overflow-hidden">
		<!-- Background Grid Glow -->
		<div
			class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#ea580c_1px,transparent_1px)] [background-size:16px_16px]">
		</div>

		<div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
			<div class="flex items-start sm:items-center gap-4">
				<div
					class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-orange-500/20 text-orange-400 border border-orange-500/30 flex items-center justify-center font-black text-2xl flex-shrink-0 shadow-inner">
					<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
					</svg>
				</div>
				<div class="space-y-1">
					<div class="flex items-center gap-2 flex-wrap">
						<h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">
							لوحة إدارة المتجر
						</h1>
						<span
							class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-amber-500/25 text-amber-300 border border-amber-400/40">
							Admin Hub
						</span>
					</div>
					<p class="text-xs sm:text-sm text-slate-300 font-medium max-w-xl leading-relaxed">
						متابعة فورية للمبيعات، إدارة حالات شحن الطلبات، ومراقبة دقيقة لمخزون المعدات والأدوات.
					</p>
				</div>
			</div>

			<!-- Direct Actions -->
			<div class="flex items-center gap-2 flex-wrap">
				<a href="<?php echo esc_url(admin_url()); ?>" target="_blank" rel="noopener noreferrer"
					class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-amber-300 hover:text-white bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/40 transition-all shadow-sm">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
					</svg>
					<span>لوحة تحكم WP Admin</span>
				</a>
				<a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>"
					class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:text-white bg-white/10 hover:bg-white/15 transition-all">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
					</svg>
					<span>حساب العميل</span>
				</a>
			</div>
		</div>
	</div>

	<!-- =====================================================================
		 2. Real-Time Stats Header Cards (4 Columns)
		 ===================================================================== -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

		<!-- Metric 1: Total Orders -->
		<div
			class="bg-white rounded-2xl p-3.5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-orange-200 transition-all flex flex-col justify-between group">
			<div class="flex items-start justify-between gap-3 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">
						إجمالي الطلبات
					</span>
					<div
						class="text-2xl sm:text-3xl font-black text-slate-900 group-hover:text-primary transition-colors">
						<?php echo esc_html(number_format_i18n($total_orders_count)); ?>
						<span class="text-xs font-normal text-slate-400">طلب</span>
					</div>
				</div>
				<div
					class="w-11 h-11 rounded-2xl bg-orange-50 text-primary flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-all">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 text-xs text-slate-400">
				كافة العمليات المسجلة بالمتجر
			</div>
		</div>

		<!-- Metric 2: Net Sales -->
		<div
			class="bg-white rounded-2xl p-3.5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all flex flex-col justify-between group">
			<div class="flex items-start justify-between gap-3 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">
						صافي المبيعات
					</span>
					<div class="text-2xl sm:text-3xl font-black text-emerald-600">
						<?php echo wp_kses_post(wc_price($total_net_sales)); ?>
					</div>
				</div>
				<div
					class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-all">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 text-xs text-slate-400">
				الطلبات المكتملة وقيد المعالجة
			</div>
		</div>

		<!-- Metric 3: Pending & Processing Orders -->
		<div
			class="bg-white rounded-2xl p-3.5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-200 transition-all flex flex-col justify-between group">
			<div class="flex items-start justify-between gap-3 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">
						طلبات قيد المتابعة
					</span>
					<div class="text-2xl sm:text-3xl font-black text-amber-600 flex items-center gap-2">
						<span><?php echo esc_html(number_format_i18n($pending_count)); ?></span>
						<span class="text-xs font-normal text-slate-400">طلب</span>
						<?php if ($pending_count > 0): ?>
							<span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
						<?php endif; ?>
					</div>
				</div>
				<div
					class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-600 group-hover:text-white transition-all">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 text-xs text-slate-400">
				تتطلب المعالجة أو الشحن السريع
			</div>
		</div>

		<!-- Metric 4: Inventory Alerts -->
		<div
			class="bg-white rounded-2xl p-3.5 sm:p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-rose-200 transition-all flex flex-col justify-between group">
			<div class="flex items-start justify-between gap-3 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">
						تنبيهات المخزون
					</span>
					<div
						class="text-2xl sm:text-3xl font-black <?php echo $total_stock_alerts > 0 ? 'text-rose-600' : 'text-slate-700'; ?>">
						<?php echo esc_html(number_format_i18n($total_stock_alerts)); ?>
						<span class="text-xs font-normal text-slate-400">منتج</span>
					</div>
				</div>
				<div
					class="w-11 h-11 rounded-2xl <?php echo $total_stock_alerts > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-500'; ?> flex items-center justify-center flex-shrink-0 transition-all">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 text-xs text-slate-400">
				أصناف نفذت أو قاربت على الانتهاء
			</div>
		</div>

	</div>

	<!-- =====================================================================
		 3. Orders Management Table Section
		 ===================================================================== -->
	<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
		<!-- Section Header -->
		<div
			class="p-3.5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
			<div class="flex items-center gap-3">
				<span
					class="w-10 h-10 rounded-xl bg-orange-500/10 text-primary flex items-center justify-center flex-shrink-0">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
					</svg>
				</span>
				<div>
					<h2 class="text-base sm:text-lg font-bold text-slate-900">
						إدارة أحدث الطلبات
					</h2>
					<p class="text-xs text-slate-400">
						استعراض تفاصيل العملاء وتحديث حالات الشحن والمعالجة مباشرة
					</p>
				</div>
			</div>

			<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_order')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-orange-700 transition-colors">
				<span>كافة الطلبات في WP Admin</span>
				<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg>
			</a>
		</div>

		<?php if (!empty($recent_orders)): ?>
			<div class="overflow-x-auto">
				<table class="w-full text-right border-collapse min-w-[750px]">
					<thead>
						<tr
							class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-extrabold uppercase text-slate-500 tracking-wider">
							<th class="py-3.5 px-4">رقم الطلب والتاريخ</th>
							<th class="py-3.5 px-4">العميل</th>
							<th class="py-3.5 px-4">الهاتف والمحافظة</th>
							<th class="py-3.5 px-4">الإجمالي والدفع</th>
							<th class="py-3.5 px-4">الحالة</th>
							<th class="py-3.5 px-4 text-center">إجراء سريع</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-100 text-xs">
						<?php foreach ($recent_orders as $order):
							$order_id = $order->get_id();
							$status = $order->get_status();
							$status_name = wc_get_order_status_name($status);
							$customer_name = trim($order->get_formatted_billing_full_name());
							if (empty($customer_name)) {
								$customer_name = $order->get_billing_first_name() ?: 'عميل زائر';
							}
							$phone = $order->get_billing_phone();
							$state_code = $order->get_billing_state();
							$gov_label = isset($eg_states[$state_code]) ? $eg_states[$state_code] : ($state_code ?: $order->get_billing_city() ?: '-');

							// Unified Status Badge styling
							$badge_data  = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $status ) : array( 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400', 'label' => $status_name );
							$badge_class = $badge_data['class'];
							$dot_class   = $badge_data['dot'];
							$status_name = $badge_data['label'];
							?>
							<tr class="hover:bg-slate-50/60 transition-colors">
								<!-- Order ID & Date -->
								<td class="py-4 px-4 align-middle">
									<div class="font-extrabold text-slate-900 flex items-center gap-1.5">
										<span
											class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-black">
											#<?php echo esc_html($order->get_order_number()); ?>
										</span>
									</div>
									<div class="text-[11px] text-slate-400 mt-1">
										<?php echo esc_html(wc_format_datetime($order->get_date_created(), 'Y-m-d H:i')); ?>
									</div>
								</td>

								<!-- Customer Name & Email -->
								<td class="py-4 px-4 align-middle">
									<div class="font-bold text-slate-900">
										<?php echo esc_html($customer_name); ?>
									</div>
									<?php if ($order->get_billing_email()): ?>
										<div class="text-[11px] text-slate-400 truncate max-w-[180px]" dir="ltr">
											<?php echo esc_html($order->get_billing_email()); ?>
										</div>
									<?php endif; ?>
								</td>

								<!-- Phone & Governorate -->
								<td class="py-4 px-4 align-middle">
									<?php if ($phone): ?>
										<div class="flex items-center gap-2">
											<a href="tel:<?php echo esc_attr($phone); ?>"
												class="font-bold text-slate-800 hover:text-primary transition-colors" dir="ltr">
												<?php echo esc_html($phone); ?>
											</a>
											<a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone)); ?>"
												target="_blank" rel="noopener noreferrer" title="مراسلة واتساب"
												class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors">
												<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
													<path
														d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
												</svg>
											</a>
										</div>
									<?php else: ?>
										<span class="text-slate-400">-</span>
									<?php endif; ?>
									<div class="text-[11px] text-slate-500 mt-1 font-medium">
										<?php echo esc_html($gov_label); ?>
									</div>
								</td>

								<!-- Total & Payment Method -->
								<td class="py-4 px-4 align-middle">
									<div class="font-extrabold text-slate-900">
										<?php echo wp_kses_post($order->get_formatted_order_total()); ?>
									</div>
									<div class="text-[11px] text-slate-400 mt-0.5">
										<?php echo esc_html($order->get_payment_method_title() ?: 'الدفع عند الاستلام'); ?>
									</div>
								</td>

								<!-- Status Badge with Dot -->
								<td class="py-4 px-4 align-middle">
									<span
										class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border <?php echo esc_attr($badge_class); ?>">
										<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 <?php echo esc_attr($dot_class); ?>"></span>
										<span><?php echo esc_html($status_name); ?></span>
									</span>
								</td>

								<!-- Quick Actions & Status Update Form -->
								<td class="py-4 px-4 align-middle text-center">
									<div class="flex items-center justify-center gap-2 flex-wrap">
										<!-- Quick Status Update Form -->
										<form method="post" action="" class="flex items-center gap-1.5">
											<?php wp_nonce_field('ashrafco_admin_hub_status_nonce'); ?>
											<input type="hidden" name="ashrafco_admin_hub_action" value="update_order_status" />
											<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>" />

											<select name="order_status"
												class="text-[11px] font-bold py-1.5 pr-2.5 pl-6 bg-slate-50 hover:bg-white border border-slate-200 rounded-lg text-slate-700 focus:outline-none focus:border-orange-500 cursor-pointer appearance-none bg-no-repeat bg-[left_0.4rem_center] text-right"
												>
												<option value="processing" <?php selected($status, 'processing'); ?>>قيد المعالجة</option>
												<option value="shipped" <?php selected($status, 'shipped'); ?>>تم الشحن</option>
												<option value="completed" <?php selected($status, 'completed'); ?>>تم التسليم</option>
												<option value="cancelled" <?php selected($status, 'cancelled'); ?>>ملغي</option>
											</select>
											<button type="submit" title="حفظ الحالة"
												class="px-2.5 py-1.5 rounded-lg text-[11px] font-extrabold text-white bg-slate-900 hover:bg-primary transition-colors flex-shrink-0">
												تحديث
											</button>
										</form>

										<!-- Direct View / Edit Link -->
										<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
											class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors"
											title="معاينة تفاصيل الطلب">
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
		<?php else: ?>
			<div class="text-center py-8 sm:py-12 p-3.5 sm:p-6">
				<div
					class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
					</svg>
				</div>
				<h3 class="text-sm font-bold text-slate-800">لا توجد طلبات مسجلة بعد</h3>
				<p class="text-xs text-slate-400 mt-1">ستظهر طلبات الشراء الواردة من العملاء هنا فور تسجيلها.</p>
			</div>
		<?php endif; ?>
	</div>

	<!-- =====================================================================
		 4. Inventory / Products Quick Overview Section
		 ===================================================================== -->
	<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-3.5 sm:p-8 space-y-6">
		<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
			<div class="flex items-center gap-3">
				<span
					class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center flex-shrink-0">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
					</svg>
				</span>
				<div>
					<h2 class="text-base sm:text-lg font-bold text-slate-900">
						مراقبة المخزون وأحدث المنتجات
					</h2>
					<p class="text-xs text-slate-400">
						متابعة فورية للكميات المتوفرة وأسعار البيع والتخفيضات
					</p>
				</div>
			</div>

			<a href="<?php echo esc_url(admin_url('edit.php?post_type=product')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
				<span>إدارة المنتجات في WP Admin</span>
				<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg>
			</a>
		</div>

		<?php if (!empty($catalog_products)): ?>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				<?php foreach ($catalog_products as $product):
					$prod_id = $product->get_id();
					$stock_status = $product->get_stock_status();
					$stock_qty = $product->get_stock_quantity();
					$is_on_sale = $product->is_on_sale();
					$edit_url = get_edit_post_link($prod_id);
					?>
					<div
						class="bg-slate-50/70 hover:bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between gap-3 transition-all group">
						<div class="flex items-center gap-3 min-w-0">
							<!-- Thumbnail -->
							<div
								class="w-14 h-14 rounded-xl bg-white border border-slate-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
								<?php if ($product->get_image_id()): ?>
									<?php echo wp_kses_post($product->get_image(array(56, 56), array('class' => 'w-full h-full object-cover'))); ?>
								<?php else: ?>
									<svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
									</svg>
								<?php endif; ?>
							</div>

							<!-- Details -->
							<div class="min-w-0 flex-1">
								<a href="<?php echo esc_url($product->get_permalink()); ?>"
									class="text-xs font-bold text-slate-900 group-hover:text-primary transition-colors block truncate"
									title="<?php echo esc_attr($product->get_name()); ?>">
									<?php echo esc_html($product->get_name()); ?>
								</a>

								<!-- Price Display -->
								<div class="text-xs font-black text-slate-900 mt-1 flex items-center gap-2">
									<?php if ($is_on_sale && $product->get_regular_price()): ?>
										<span class="text-primary">
											<?php echo wp_kses_post(wc_price($product->get_sale_price())); ?>
										</span>
										<span class="text-[10px] text-slate-400 line-through">
											<?php echo wp_kses_post(wc_price($product->get_regular_price())); ?>
										</span>
									<?php else: ?>
										<span><?php echo wp_kses_post($product->get_price_html()); ?></span>
									<?php endif; ?>
								</div>

								<!-- Stock Status Badge -->
								<div class="mt-1.5">
									<?php if ('outofstock' === $stock_status): ?>
										<span
											class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
											نفذ المخزون
										</span>
									<?php elseif ('onbackorder' === $stock_status): ?>
										<span
											class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
											طلب مسبق
										</span>
									<?php else: ?>
										<span
											class="inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
											<?php echo null !== $stock_qty ? esc_html($stock_qty . ' متوفر') : 'متوفر بالمخزون'; ?>
										</span>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<?php if ($edit_url): ?>
							<a href="<?php echo esc_url($edit_url); ?>" target="_blank" rel="noopener noreferrer"
								title="تعديل في WP Admin"
								class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-primary hover:border-orange-200 flex items-center justify-center flex-shrink-0 transition-colors shadow-xs">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
								</svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<div class="text-center py-8">
				<p class="text-xs text-slate-400">لا توجد منتجات مسجلة في المتجر حالياً.</p>
			</div>
		<?php endif; ?>
	</div>

	<!-- =====================================================================
		 5. Quick Admin Hub Shortcuts Bar
		 ===================================================================== -->
	<div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80">
		<h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
			اختصارات الإدارة المباشرة (WP Admin)
		</h4>
		<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
			<a href="<?php echo esc_url(admin_url('post-new.php?post_type=product')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="p-3 rounded-xl bg-white hover:border-orange-300 border border-slate-200/80 text-center transition-all group shadow-xs">
				<span class="text-xs font-bold text-slate-800 group-hover:text-primary block transition-colors">
					+ إضافة منتج جديد
				</span>
				<span class="text-[10px] text-slate-400">نشر معدة أو أداة</span>
			</a>

			<a href="<?php echo esc_url(admin_url('edit.php?post_type=shop_order')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="p-3 rounded-xl bg-white hover:border-orange-300 border border-slate-200/80 text-center transition-all group shadow-xs">
				<span class="text-xs font-bold text-slate-800 group-hover:text-primary block transition-colors">
					كافة الطلبات
				</span>
				<span class="text-[10px] text-slate-400">طباعة الفواتير والبوالص</span>
			</a>

			<a href="<?php echo esc_url(admin_url('admin.php?page=wc-reports')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="p-3 rounded-xl bg-white hover:border-orange-300 border border-slate-200/80 text-center transition-all group shadow-xs">
				<span class="text-xs font-bold text-slate-800 group-hover:text-primary block transition-colors">
					تقارير المبيعات
				</span>
				<span class="text-[10px] text-slate-400">إحصائيات الإيرادات</span>
			</a>

			<a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings')); ?>" target="_blank"
				rel="noopener noreferrer"
				class="p-3 rounded-xl bg-white hover:border-orange-300 border border-slate-200/80 text-center transition-all group shadow-xs">
				<span class="text-xs font-bold text-slate-800 group-hover:text-primary block transition-colors">
					إعدادات المتجر
				</span>
				<span class="text-[10px] text-slate-400">الشحن والدفع والضرائب</span>
			</a>
		</div>
	</div>

</div>