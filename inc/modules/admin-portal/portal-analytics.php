<?php
/**
 * Ashrafco Admin Portal - General Analytics Engine & REST/AJAX Handlers
 *
 * Provides real WooCommerce analytics, date filtering, timeline calculations,
 * and inventory alert computations for the Ashrafco Store Management Portal.
 *
 * @package Ashrafco
 * @since 1.2.0
 */

defined('ABSPATH') || exit;

/**
 * Format Currency in clean readable Arabic format without trailing zero decimals
 * e.g., "EGP 15,400"
 */
function ashrafco_format_price_clean($amount)
{
	$amount = (float) $amount;
	$currency = get_woocommerce_currency();
	return $currency . ' ' . number_format($amount, 0, '.', ',');
}

/**
 * Arabic Month Names Mapping
 */
function ashrafco_get_arabic_month_name($month_num)
{
	$months = array(
		1 => 'يناير',
		2 => 'فبراير',
		3 => 'مارس',
		4 => 'أبريل',
		5 => 'مايو',
		6 => 'يونيو',
		7 => 'يوليو',
		8 => 'أغسطس',
		9 => 'سبتمبر',
		10 => 'أكتوبر',
		11 => 'نوفمبر',
		12 => 'ديسمبر',
	);
	$num = (int) $month_num;
	return isset($months[$num]) ? $months[$num] : '';
}

/**
 * Format a Date Range in clean Arabic format
 * e.g., "01 أكتوبر 2026 - 13 أكتوبر 2026"
 */
function ashrafco_format_arabic_date_range(DateTimeInterface $start_dt, DateTimeInterface $end_dt)
{
	$start_day = $start_dt->format('d');
	$start_month = ashrafco_get_arabic_month_name($start_dt->format('n'));
	$start_year = $start_dt->format('Y');

	$end_day = $end_dt->format('d');
	$end_month = ashrafco_get_arabic_month_name($end_dt->format('n'));
	$end_year = $end_dt->format('Y');

	if ($start_year === $end_year && $start_month === $end_month) {
		return sprintf('%s %s %s - %s %s %s', $start_day, $start_month, $start_year, $end_day, $end_month, $end_year);
	}

	return sprintf('%s %s %s - %s %s %s', $start_day, $start_month, $start_year, $end_day, $end_month, $end_year);
}

/**
 * Calculate Percentage Change between current and previous value
 */
function ashrafco_calc_percent_change($current, $previous)
{
	$current = (float) $current;
	$previous = (float) $previous;

	if ($previous <= 0.0) {
		if ($current > 0.0) {
			return array(
				'value' => 100,
				'formatted' => '+100%',
				'direction' => 'up',
				'badge_css' => 'text-emerald-600',
			);
		}
		return array(
			'value' => 0,
			'formatted' => '0%',
			'direction' => 'neutral',
			'badge_css' => 'text-slate-400',
		);
	}

	$pct = round((($current - $previous) / $previous) * 100);
	if ($pct > 0) {
		return array(
			'value' => $pct,
			'formatted' => '+' . $pct . '%',
			'direction' => 'up',
			'badge_css' => 'text-emerald-600',
		);
	} elseif ($pct < 0) {
		return array(
			'value' => $pct,
			'formatted' => $pct . '%',
			'direction' => 'down',
			'badge_css' => 'text-rose-600',
		);
	}

	return array(
		'value' => 0,
		'formatted' => '0%',
		'direction' => 'neutral',
		'badge_css' => 'text-slate-400',
	);
}

/**
 * Resolve Date Range Boundaries based on selected period
 *
 * @param string $period 'today' | 'last_7_days' | 'last_30_days' | 'this_month' | 'custom'
 * @param string $custom_start Y-m-d
 * @param string $custom_end Y-m-d
 * @return array
 */
function ashrafco_resolve_portal_date_range($period = 'last_30_days', $custom_start = '', $custom_end = '')
{
	$tz = wp_timezone();
	$now = new DateTimeImmutable('now', $tz);

	$period = in_array($period, array('today', 'last_7_days', 'last_30_days', 'this_month', 'custom'), true) ? $period : 'last_30_days';

	switch ($period) {
		case 'today':
			$start = $now->setTime(0, 0, 0);
			$end = $now->setTime(23, 59, 59);
			$prev_start = $start->modify('-1 day');
			$prev_end = $end->modify('-1 day');
			break;

		case 'last_7_days':
			$start = $now->modify('-6 days')->setTime(0, 0, 0);
			$end = $now->setTime(23, 59, 59);
			$prev_start = $now->modify('-13 days')->setTime(0, 0, 0);
			$prev_end = $now->modify('-7 days')->setTime(23, 59, 59);
			break;

		case 'this_month':
			$start = $now->modify('first day of this month')->setTime(0, 0, 0);
			$end = $now->setTime(23, 59, 59);
			// Previous month full duration
			$prev_start = $now->modify('first day of last month')->setTime(0, 0, 0);
			$prev_end = $now->modify('last day of last month')->setTime(23, 59, 59);
			break;

		case 'custom':
			$custom_start_valid = false;
			$custom_end_valid = false;

			if (!empty($custom_start)) {
				$parsed_start = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $custom_start . ' 00:00:00', $tz);
				if ($parsed_start) {
					$start = $parsed_start;
					$custom_start_valid = true;
				}
			}
			if (!empty($custom_end)) {
				$parsed_end = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $custom_end . ' 23:59:59', $tz);
				if ($parsed_end) {
					$end = $parsed_end;
					$custom_end_valid = true;
				}
			}

			if (!$custom_start_valid || !$custom_end_valid || $start > $end) {
				// Fallback to last 30 days
				$start = $now->modify('-29 days')->setTime(0, 0, 0);
				$end = $now->setTime(23, 59, 59);
			}

			$day_diff = (int) $end->diff($start)->format('%a');
			$prev_end = $start->modify('-1 second');
			$prev_start = $prev_end->modify('-' . $day_diff . ' days')->setTime(0, 0, 0);
			break;

		case 'last_30_days':
		default:
			$start = $now->modify('-29 days')->setTime(0, 0, 0);
			$end = $now->setTime(23, 59, 59);
			$prev_start = $now->modify('-59 days')->setTime(0, 0, 0);
			$prev_end = $now->modify('-30 days')->setTime(23, 59, 59);
			break;
	}

	return array(
		'period' => $period,
		'start_dt' => $start,
		'end_dt' => $end,
		'prev_start' => $prev_start,
		'prev_end' => $prev_end,
		'label' => ashrafco_format_arabic_date_range($start, $end),
		'start_ymd' => $start->format('Y-m-d'),
		'end_ymd' => $end->format('Y-m-d'),
	);
}

/**
 * Fetch and aggregate complete dashboard analytics data
 *
 * @param string $period
 * @param string $custom_start
 * @param string $custom_end
 * @return array
 */
function ashrafco_get_portal_analytics_data($period = 'last_30_days', $custom_start = '', $custom_end = '')
{
	$version = function_exists('ashrafco_get_analytics_cache_version') ? ashrafco_get_analytics_cache_version() : 1;
	$cache_key = 'ashrafco_pa_' . $version . '_' . $period . '_' . md5($custom_start . '_' . $custom_end);
	$cached = get_transient($cache_key);
	if (is_array($cached) && !empty($cached['kpis'])) {
		return $cached;
	}

	$range_info = ashrafco_resolve_portal_date_range($period, $custom_start, $custom_end);

	$start_dt = $range_info['start_dt'];
	$end_dt = $range_info['end_dt'];
	$prev_start = $range_info['prev_start'];
	$prev_end = $range_info['prev_end'];

	$start_ts = $start_dt->getTimestamp();
	$end_ts = $end_dt->getTimestamp();
	$prev_s_ts = $prev_start->getTimestamp();
	$prev_e_ts = $prev_end->getTimestamp();

	// 1. Fetch Orders for Current Period in safe batches (preserves full dataset accuracy without unbounded query)
	$current_orders = array();
	$order_page = 1;
	do {
		$batch = wc_get_orders(array(
			'date_created' => $start_ts . '...' . $end_ts,
			'limit' => 250,
			'page' => $order_page,
			'type' => 'shop_order',
			'paginate' => false,
		));
		if (!empty($batch) && is_array($batch)) {
			$current_orders = array_merge($current_orders, $batch);
			$order_page++;
		}
	} while (!empty($batch) && count($batch) === 250 && $order_page <= 40);

	// 2. Fetch Orders for Previous Period in safe batches (for comparative trend percentages)
	$prev_orders = array();
	$prev_page = 1;
	do {
		$prev_batch = wc_get_orders(array(
			'date_created' => $prev_s_ts . '...' . $prev_e_ts,
			'limit' => 250,
			'page' => $prev_page,
			'type' => 'shop_order',
			'paginate' => false,
		));
		if (!empty($prev_batch) && is_array($prev_batch)) {
			$prev_orders = array_merge($prev_orders, $prev_batch);
			$prev_page++;
		}
	} while (!empty($prev_batch) && count($prev_batch) === 250 && $prev_page <= 40);

	// 3. KPI 1: Net Revenue
	$net_sales = 0.0;
	$prev_net_sales = 0.0;

	// 4. KPI 2: Total Orders
	$total_orders_count = count($current_orders);
	$prev_orders_count = count($prev_orders);

	// 5. KPI 3: Orders Requiring Action / Follow-up
	// We count active processing, on-hold, and pending orders in this period
	$pending_orders_count = 0;
	$prev_pending_orders_count = 0;

	// Status breakdown containers
	$status_counts = array(
		'processing' => 0,
		'shipped'    => 0,
		'completed'  => 0,
		'cancelled'  => 0,
	);

	// Product units sold map: [ product_id => qty ]
	$product_sales_map = array();

	// Timeline aggregation buckets: [ 'Y-m-d' => [ 'sales' => 0.0, 'orders' => 0 ] ]
	$timeline_buckets = array();
	$interval = new DateInterval('P1D');
	$period_iterator = new DatePeriod($start_dt, $interval, $end_dt->modify('+1 second'));

	foreach ($period_iterator as $dt_day) {
		$day_key = $dt_day->format('Y-m-d');
		$timeline_buckets[$day_key] = array(
			'date' => $day_key,
			'day_num' => (int) $dt_day->format('j'),
			'month_ar' => ashrafco_get_arabic_month_name($dt_day->format('n')),
			'label' => $dt_day->format('j') . ' ' . ashrafco_get_arabic_month_name($dt_day->format('n')),
			'sales' => 0.0,
			'orders' => 0,
		);
	}

	// Process Current Orders
	foreach ($current_orders as $order) {
		if (!($order instanceof WC_Order)) {
			continue;
		}

		$status = $order->get_status();
		$total = (float) $order->get_total();

		// Record Status (Normalized to 4 Unified Statuses)
		$mapped_status = $status;
		if (in_array($status, array('pending', 'on-hold'), true)) {
			$mapped_status = 'processing';
		} elseif (in_array($status, array('refunded', 'failed'), true)) {
			$mapped_status = 'cancelled';
		} elseif (in_array($status, array('out-for-delivery'), true)) {
			$mapped_status = 'shipped';
		}

		if (isset($status_counts[$mapped_status])) {
			$status_counts[$mapped_status]++;
		} else {
			$status_counts[$mapped_status] = 1;
		}

		// Calculate Net Sales (Completed, Processing, Shipped, subtracting any refund)
		if (in_array($status, array('completed', 'processing', 'shipped'), true)) {
			$refunded = (float) $order->get_total_refunded();
			$order_net = max(0.0, $total - $refunded);
			$net_sales += $order_net;

			// Add to timeline
			$date_created = $order->get_date_created();
			if ($date_created) {
				$order_day = $date_created->date('Y-m-d');
				if (isset($timeline_buckets[$order_day])) {
					$timeline_buckets[$order_day]['sales'] += $order_net;
				}
			}

			// Aggregate items sold
			foreach ($order->get_items() as $item) {
				$pid = $item->get_product_id();
				if ($pid > 0) {
					$qty = (int) $item->get_quantity();
					if (!isset($product_sales_map[$pid])) {
						$product_sales_map[$pid] = 0;
					}
					$product_sales_map[$pid] += $qty;
				}
			}
		}

		// Count Pending follow-up
		if (in_array($status, array('pending', 'processing', 'on-hold'), true)) {
			$pending_orders_count++;
		}

		// Timeline order count
		$date_created = $order->get_date_created();
		if ($date_created) {
			$order_day = $date_created->date('Y-m-d');
			if (isset($timeline_buckets[$order_day])) {
				$timeline_buckets[$order_day]['orders']++;
			}
		}
	}

	// Process Previous Orders for comparative changes
	foreach ($prev_orders as $p_order) {
		if (!($p_order instanceof WC_Order)) {
			continue;
		}
		$p_status = $p_order->get_status();
		if (in_array($p_status, array('completed', 'processing', 'shipped'), true)) {
			$p_refunded = (float) $p_order->get_total_refunded();
			$prev_net_sales += max(0.0, (float) $p_order->get_total() - $p_refunded);
		}
		if (in_array($p_status, array('pending', 'processing', 'on-hold'), true)) {
			$prev_pending_orders_count++;
		}
	}

	// Calculate KPI Percentage Trends
	$revenue_trend = ashrafco_calc_percent_change($net_sales, $prev_net_sales);
	$orders_trend = ashrafco_calc_percent_change($total_orders_count, $prev_orders_count);
	$pending_trend = ashrafco_calc_percent_change($pending_orders_count, $prev_pending_orders_count);

	// 6. KPI 4: Inventory Alerts (Global Store Stock Health via direct SQL)
	$low_stock_threshold = (int) get_option('woocommerce_notify_low_stock_amount', 3);
	$inventory_counts = function_exists('ashrafco_get_inventory_health_counts') ? ashrafco_get_inventory_health_counts() : array('low_stock' => 0, 'out_of_stock' => 0, 'total_alerts' => 0);
	$low_stock_count = $inventory_counts['low_stock'];
	$total_stock_alerts = $inventory_counts['total_alerts'];

	// Bounded query for the top low stock and out-of-stock products (only 5 displayed in the table)
	global $wpdb;
	$sample_ids = $wpdb->get_col($wpdb->prepare("
		SELECT p.ID
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm_stock ON p.ID = pm_stock.post_id AND pm_stock.meta_key = '_stock'
		WHERE p.post_type = 'product'
		  AND p.post_status = 'publish'
		ORDER BY CAST(pm_stock.meta_value AS SIGNED) ASC
		LIMIT 10
	"));

	$oos_ids = $wpdb->get_col("
		SELECT p.ID
		FROM {$wpdb->posts} p
		INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_stock_status' AND pm.meta_value = 'outofstock'
		WHERE p.post_type = 'product'
		  AND p.post_status = 'publish'
		LIMIT 10
	");

	$candidate_ids = array_unique(array_merge((array) $oos_ids, (array) $sample_ids));
	$low_stock_list = array();

	foreach (array_slice($candidate_ids, 0, 10) as $c_id) {
		$p_item = wc_get_product($c_id);
		if (!$p_item) {
			continue;
		}
		$qty = $p_item->get_stock_quantity();
		$is_zero = (null !== $qty && $qty <= 0) || !$p_item->is_in_stock();
		$priority = $is_zero ? -1 : (null !== $qty ? $qty : 0);

		$low_stock_list[] = array(
			'product' => $p_item,
			'qty' => (null !== $qty) ? $qty : 0,
			'is_zero' => $is_zero,
			'priority' => $priority,
		);
	}

	// Sort low stock list by priority (0 first, then lowest quantity)
	usort($low_stock_list, function ($a, $b) {
		return $a['priority'] - $b['priority'];
	});

	// Build Low Stock Table Items (up to 5)
	$low_stock_table = array();
	$rank_idx = 1;
	foreach (array_slice($low_stock_list, 0, 5) as $low_item) {
		$p_obj = $low_item['product'];
		$p_id = $p_obj->get_id();
		$thumb_id = $p_obj->get_image_id();
		$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : wc_placeholder_img_src();

		$low_stock_table[] = array(
			'rank' => $rank_idx++,
			'id' => $p_id,
			'name' => $p_obj->get_name(),
			'sku' => $p_obj->get_sku() ? '#' . $p_obj->get_sku() : '#' . $p_id,
			'stock_qty' => $low_item['qty'],
			'is_zero' => $low_item['is_zero'],
			'status_label' => $low_item['is_zero'] ? 'نفذ المخزون' : 'مخزون منخفض',
			'badge_class' => $low_item['is_zero'] ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200',
			'thumb_url' => $thumb_url,
			'edit_url' => add_query_arg(array('action' => 'edit-product', 'id' => $p_id), home_url('/admin-portal/')),
		);
	}

	// 7. Status Distribution Data & Percentages (Strictly 4 Unified Statuses)
	$status_palette = array(
		'processing' => array('label' => 'قيد المعالجة', 'color' => '#f59e0b', 'badge' => 'bg-amber-50 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'),
		'shipped'    => array('label' => 'تم الشحن', 'color' => '#3b82f6', 'badge' => 'bg-blue-50 text-blue-800 border-blue-200', 'dot' => 'bg-blue-500'),
		'completed'  => array('label' => 'تم التسليم', 'color' => '#10b981', 'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'),
		'cancelled'  => array('label' => 'ملغي', 'color' => '#ef4444', 'badge' => 'bg-rose-50 text-rose-800 border-rose-200', 'dot' => 'bg-rose-500'),
	);

	$status_distribution_list = array();
	$donut_labels = array();
	$donut_data = array();
	$donut_colors = array();

	// Calculate percentages
	foreach ($status_palette as $st_key => $st_info) {
		$st_count = isset($status_counts[$st_key]) ? (int) $status_counts[$st_key] : 0;
		$pct = $total_orders_count > 0 ? round(($st_count / $total_orders_count) * 100, 1) : 0.0;
		$status_distribution_list[] = array(
			'key' => $st_key,
			'label' => $st_info['label'],
			'color' => $st_info['color'],
			'dot' => $st_info['dot'],
			'badge' => $st_info['badge'],
			'count' => $st_count,
			'percentage' => $pct,
		);
		if ($st_count > 0) {
			$donut_labels[] = $st_info['label'];
			$donut_data[] = $st_count;
			$donut_colors[] = $st_info['color'];
		}
	}

	// 8. Top Selling Products (up to 5)
	arsort($product_sales_map);
	$top_selling_table = array();
	$top_rank = 1;
	foreach (array_slice($product_sales_map, 0, 5, true) as $p_id => $units_sold) {
		$prod_obj = wc_get_product($p_id);
		if (!$prod_obj) {
			continue;
		}
		$thumb_id = $prod_obj->get_image_id();
		$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : wc_placeholder_img_src();

		$top_selling_table[] = array(
			'rank' => $top_rank++,
			'id' => $p_id,
			'name' => $prod_obj->get_name(),
			'sku' => $prod_obj->get_sku() ? '#' . $prod_obj->get_sku() : '#' . $p_id,
			'units_sold' => $units_sold,
			'thumb_url' => $thumb_url,
			'edit_url' => add_query_arg(array('action' => 'edit-product', 'id' => $p_id), home_url('/admin-portal/')),
		);
	}

	// 9. Recent Orders (Latest 5 in store)
	$recent_orders_query = wc_get_orders(array(
		'limit' => 5,
		'orderby' => 'date',
		'order' => 'DESC',
		'type' => 'shop_order',
	));
	$recent_orders_list = array();
	if (!empty($recent_orders_query)) {
		foreach ($recent_orders_query as $r_order) {
			$r_id = $r_order->get_id();
			$r_status = $r_order->get_status();
			$r_date = $r_order->get_date_created() ? $r_order->get_date_created()->date_i18n('Y-m-d H:i') : '';
			$c_name = $r_order->get_formatted_billing_full_name();
			if (empty($c_name)) {
				$c_name = 'عميل زائر مباشر';
			}

			$status_badge_info = function_exists('ashrafco_get_order_status_badge') 
				? ashrafco_get_order_status_badge($r_status) 
				: (isset($status_palette[$r_status]) ? $status_palette[$r_status] : array('label' => wc_get_order_status_name($r_status), 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400'));

			$recent_orders_list[] = array(
				'id' => $r_id,
				'order_number' => '#' . $r_id,
				'customer_name' => $c_name,
				'date' => $r_date,
				'status' => $r_status,
				'status_label' => $status_badge_info['label'],
				'badge_class' => isset($status_badge_info['class']) ? $status_badge_info['class'] : (isset($status_badge_info['badge']) ? $status_badge_info['badge'] : 'bg-slate-100 text-slate-700 border-slate-200'),
				'badge_dot' => isset($status_badge_info['dot']) ? $status_badge_info['dot'] : 'bg-slate-400',
				'formatted_total' => ashrafco_format_price_clean($r_order->get_total()),
				'raw_total' => (float) $r_order->get_total(),
				'view_url' => $r_order->get_view_order_url(),
			);
		}
	}

	// 10. Format Timeline for Chart.js
	$timeline_labels = array();
	$timeline_sales = array();
	$timeline_orders = array();

	foreach ($timeline_buckets as $bucket) {
		$timeline_labels[] = $bucket['label'];
		$timeline_sales[] = round($bucket['sales'], 2);
		$timeline_orders[] = $bucket['orders'];
	}

	$payload = array(
		'range' => array(
			'period' => $range_info['period'],
			'label' => $range_info['label'],
			'start_ymd' => $range_info['start_ymd'],
			'end_ymd' => $range_info['end_ymd'],
		),
		'kpis' => array(
			'net_revenue' => array(
				'raw' => $net_sales,
				'formatted' => ashrafco_format_price_clean($net_sales),
				'trend' => $revenue_trend,
			),
			'total_orders' => array(
				'raw' => $total_orders_count,
				'trend' => $orders_trend,
			),
			'pending_orders' => array(
				'raw' => $pending_orders_count,
				'trend' => $pending_trend,
			),
			'stock_alerts' => array(
				'raw' => $total_stock_alerts,
				'trend' => array(
					'value' => $total_stock_alerts,
					'formatted' => $total_stock_alerts > 0 ? '+' . $total_stock_alerts : '0',
					'direction' => $total_stock_alerts > 0 ? 'up' : 'neutral',
					'badge_css' => $total_stock_alerts > 0 ? 'text-rose-600' : 'text-slate-400',
				),
			),
		),
		'chart_timeline' => array(
			'labels' => $timeline_labels,
			'sales' => $timeline_sales,
			'orders' => $timeline_orders,
		),
		'chart_status' => array(
			'total_count' => $total_orders_count,
			'labels' => $donut_labels,
			'data' => $donut_data,
			'colors' => $donut_colors,
			'breakdown' => $status_distribution_list,
		),
		'recent_orders' => $recent_orders_list,
		'top_selling' => $top_selling_table,
		'low_stock' => $low_stock_table,
		'currency_symbol' => get_woocommerce_currency_symbol(),
		'currency_code' => get_woocommerce_currency(),
	);

	set_transient($cache_key, $payload, HOUR_IN_SECONDS);
	return $payload;
}

/**
 * Handle AJAX requests for General Analytics updates
 */
function ashrafco_ajax_portal_analytics_data()
{
	if (!is_user_logged_in() || (!current_user_can('view_woocommerce_reports') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options'))) {
		wp_send_json_error(array('message' => __('غير مصرح لك بالوصول.', 'ashrafco')), 403);
	}

	check_ajax_referer('ashrafco_portal_analytics_nonce', 'nonce');

	$period = isset($_POST['period']) ? sanitize_key($_POST['period']) : 'last_30_days';
	$custom_start = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
	$custom_end = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

	$data = ashrafco_get_portal_analytics_data($period, $custom_start, $custom_end);

	wp_send_json_success($data);
}
add_action('wp_ajax_ashrafco_portal_analytics_data', 'ashrafco_ajax_portal_analytics_data');
