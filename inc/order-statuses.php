<?php
/**
 * Unified Ashrafco Order Statuses Architecture
 *
 * Enforces strictly 4 unified order statuses across the entire system and WooCommerce:
 * 1. processing -> 'قيد المعالجة' (Yellow dot + Yellow/Amber background)
 * 2. shipped    -> 'تم الشحن'     (Blue dot + Blue background)
 * 3. completed  -> 'تم التسليم'   (Green dot + Green/Emerald background)
 * 4. cancelled  -> 'ملغي'         (Red dot + Red/Rose background)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * 1. Register 'wc-shipped' (تم الشحن) Custom Order Post Status in WooCommerce.
 */
function ashrafco_register_custom_order_statuses() {
	register_post_status('wc-shipped', array(
		'label'                     => _x('تم الشحن', 'Order status', 'ashrafco'),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop('تم الشحن <span class="count">(%s)</span>', 'تم الشحن <span class="count">(%s)</span>', 'ashrafco'),
	));
}
add_action('init', 'ashrafco_register_custom_order_statuses', 9);

/**
 * 2. Filter WooCommerce Order Statuses to Strictly Use the 4 Required Statuses with Arabic Names.
 *
 * @param array $order_statuses Existing registered WooCommerce order statuses.
 * @return array Strictly mapped unified order statuses.
 */
function ashrafco_filter_wc_order_statuses($order_statuses) {
	return array(
		'wc-processing' => _x('قيد المعالجة', 'Order status', 'ashrafco'),
		'wc-shipped'    => _x('تم الشحن', 'Order status', 'ashrafco'),
		'wc-completed'  => _x('تم التسليم', 'Order status', 'ashrafco'),
		'wc-cancelled'  => _x('ملغي', 'Order status', 'ashrafco'),
	);
}
add_filter('wc_order_statuses', 'ashrafco_filter_wc_order_statuses', 99);

/**
 * 3. Default Status for Newly Created Orders.
 */
add_filter('woocommerce_default_order_status', function () {
	return 'processing';
});

/**
 * 4. Configure Valid / Paid / Report Order Statuses in WooCommerce.
 */
add_filter('woocommerce_reports_order_statuses', function () {
	return array('processing', 'shipped', 'completed');
});

add_filter('woocommerce_valid_order_statuses_for_payment_complete', function () {
	return array('processing', 'shipped', 'completed');
});

add_filter('woocommerce_order_is_paid_statuses', function () {
	return array('processing', 'shipped', 'completed');
});

/**
 * When payment is completed via any gateway, set status to 'processing' (قيد المعالجة).
 */
add_filter('woocommerce_payment_complete_order_status', function ($status, $order_id, $order) {
	return 'processing';
}, 99, 3);

/**
 * 5. Central Order Status Metadata & Visual Badge Generator.
 *
 * Returns visual styles including dots and backgrounds:
 * - قيد المعالجة: نقطة صفراء + خلفية صفراء
 * - تم الشحن: نقطة زرقاء + خلفية زرقاء
 * - تم التسليم: نقطة خضراء + خلفية خضراء
 * - ملغي: نقطة حمراء + خلفية حمراء
 *
 * @param string $status WooCommerce order status slug.
 * @return array Badge details (status, label, class, dot, color, bg_color, html).
 */
function ashrafco_get_order_status_badge($status) {
	$clean = (0 === strpos($status, 'wc-')) ? substr($status, 3) : $status;

	switch ($clean) {
		case 'processing':
		case 'pending':
		case 'on-hold':
			$label = 'قيد المعالجة';
			$class = 'bg-amber-50 text-amber-800 border-amber-200/90 font-bold';
			$dot   = 'bg-amber-500';
			$color = '#f59e0b';
			$bg    = '#fffbeb';
			break;

		case 'shipped':
		case 'out-for-delivery':
			$label = 'تم الشحن';
			$class = 'bg-blue-50 text-blue-800 border-blue-200/90 font-bold';
			$dot   = 'bg-blue-500';
			$color = '#3b82f6';
			$bg    = '#eff6ff';
			break;

		case 'completed':
			$label = 'تم التسليم';
			$class = 'bg-emerald-50 text-emerald-800 border-emerald-200/90 font-bold';
			$dot   = 'bg-emerald-500';
			$color = '#10b981';
			$bg    = '#ecfdf5';
			break;

		case 'cancelled':
		case 'refunded':
		case 'failed':
			$label = 'ملغي';
			$class = 'bg-rose-50 text-rose-800 border-rose-200/90 font-bold';
			$dot   = 'bg-rose-500';
			$color = '#ef4444';
			$bg    = '#fff1f2';
			break;

		default:
			$name  = function_exists('wc_get_order_status_name') ? wc_get_order_status_name($clean) : $clean;
			$label = $name ?: $clean;
			$class = 'bg-slate-50 text-slate-700 border-slate-200/90 font-bold';
			$dot   = 'bg-slate-400';
			$color = '#64748b';
			$bg    = '#f8fafc';
			break;
	}

	$html = sprintf(
		'<span class="portal-order-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border %s"><span class="w-1.5 h-1.5 rounded-full flex-shrink-0 %s"></span><span>%s</span></span>',
		esc_attr($class),
		esc_attr($dot),
		esc_html($label)
	);

	return array(
		'status'   => $clean,
		'label'    => $label,
		'class'    => $class,
		'dot'      => $dot,
		'color'    => $color,
		'bg_color' => $bg,
		'html'     => $html,
	);
}

/**
 * Backward-compatibility wrapper for existing portal status badge calls.
 *
 * @param string $status
 * @return array
 */
if (!function_exists('ashrafco_get_portal_status_badge')) {
	function ashrafco_get_portal_status_badge($status) {
		return ashrafco_get_order_status_badge($status);
	}
}

/**
 * 6. Customer Order Status Counts Helper.
 *
 * Returns live customer counts for account tabs:
 * - all
 * - processing (قيد المعالجة)
 * - shipped    (تم الشحن)
 * - completed  (تم التسليم)
 * - cancelled  (ملغي)
 *
 * @param int $user_id Customer WP user ID.
 * @return array
 */
function ashrafco_get_customer_order_status_counts($user_id) {
	$user_id = absint($user_id);
	$counts  = array(
		'all'        => 0,
		'processing' => 0,
		'shipped'    => 0,
		'completed'  => 0,
		'cancelled'  => 0,
	);

	if (!$user_id) {
		return $counts;
	}

	global $wpdb;

	if (class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
		$table   = $wpdb->prefix . 'wc_orders';
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT status, COUNT(*) as count FROM {$table} WHERE customer_id = %d GROUP BY status",
			$user_id
		));
	} else {
		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT p.post_status as status, COUNT(*) as count 
			 FROM {$wpdb->posts} p
			 INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
			 WHERE p.post_type = 'shop_order' 
			   AND pm.meta_key = '_customer_user' 
			   AND pm.meta_value = %d
			 GROUP BY p.post_status",
			$user_id
		));
	}

	$total = 0;
	if (!empty($results)) {
		foreach ($results as $row) {
			$raw_st = (0 === strpos($row->status, 'wc-')) ? substr($row->status, 3) : $row->status;
			$cnt    = (int) $row->count;
			$total += $cnt;

			if ('processing' === $raw_st) {
				$counts['processing'] += $cnt;
			} elseif ('shipped' === $raw_st) {
				$counts['shipped'] += $cnt;
			} elseif ('completed' === $raw_st) {
				$counts['completed'] += $cnt;
			} elseif ('cancelled' === $raw_st) {
				$counts['cancelled'] += $cnt;
			}
		}
	}
	$counts['all'] = $total;

	return $counts;
}

/**
 * 7. Storewide Admin Orders Summary Counts Helper.
 *
 * @return array
 */
if (!function_exists('ashrafco_get_orders_summary')) {
	function ashrafco_get_orders_summary() {
		if (function_exists('ashrafco_core') && ashrafco_core()->get_orders()) {
			return ashrafco_core()->get_orders()->get_orders_summary();
		}

		if (!function_exists('wc_orders_count')) {
			return array(
				'all'        => 0,
				'processing' => 0,
				'shipped'    => 0,
				'completed'  => 0,
				'cancelled'  => 0,
			);
		}

		$processing_count = (int) wc_orders_count('processing');
		$shipped_count    = (int) wc_orders_count('shipped');
		$completed_count  = (int) wc_orders_count('completed');
		$cancelled_count  = (int) wc_orders_count('cancelled');

		$total_all = $processing_count + $shipped_count + $completed_count + $cancelled_count;

		return array(
			'all'        => $total_all,
			'processing' => $processing_count,
			'shipped'    => $shipped_count,
			'completed'  => $completed_count,
			'cancelled'  => $cancelled_count,
		);
	}
}

/**
 * 8. Automatic One-time Legacy Order Status Clean-up & Migration.
 *
 * Seamlessly normalizes any historic orders with deprecated statuses:
 * - pending, on-hold -> processing (قيد المعالجة)
 * - refunded, failed -> cancelled  (ملغي)
 */
function ashrafco_migrate_legacy_order_statuses() {
	if (get_option('ashrafco_order_statuses_migrated_v1')) {
		return;
	}

	global $wpdb;

	// HPOS Custom Orders Table
	if (class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
		$table = $wpdb->prefix . 'wc_orders';
		$wpdb->query("UPDATE {$table} SET status = 'wc-processing' WHERE status IN ('wc-pending', 'wc-on-hold', 'pending', 'on-hold')");
		$wpdb->query("UPDATE {$table} SET status = 'wc-cancelled' WHERE status IN ('wc-refunded', 'wc-failed', 'refunded', 'failed')");
	}

	// WordPress Posts Table (Legacy CPT)
	$wpdb->query("UPDATE {$wpdb->posts} SET post_status = 'wc-processing' WHERE post_type = 'shop_order' AND post_status IN ('wc-pending', 'wc-on-hold')");
	$wpdb->query("UPDATE {$wpdb->posts} SET post_status = 'wc-cancelled' WHERE post_type = 'shop_order' AND post_status IN ('wc-refunded', 'wc-failed')");

	update_option('ashrafco_order_statuses_migrated_v1', 1);
}
add_action('init', 'ashrafco_migrate_legacy_order_statuses', 20);

/**
 * 9. Custom Admin Styles for Native WooCommerce Order List.
 */
add_action('admin_head', function () {
	?>
	<style>
		.order-status.status-processing,
		mark.order-status.status-processing {
			background: #fffbeb !important;
			color: #b45309 !important;
			border: 1px solid #fde68a !important;
			font-weight: 700 !important;
		}
		.order-status.status-shipped,
		mark.order-status.status-shipped {
			background: #eff6ff !important;
			color: #1d4ed8 !important;
			border: 1px solid #bfdbfe !important;
			font-weight: 700 !important;
		}
		.order-status.status-completed,
		mark.order-status.status-completed {
			background: #ecfdf5 !important;
			color: #047857 !important;
			border: 1px solid #a7f3d0 !important;
			font-weight: 700 !important;
		}
		.order-status.status-cancelled,
		mark.order-status.status-cancelled {
			background: #fff1f2 !important;
			color: #be123c !important;
			border: 1px solid #fecdd3 !important;
			font-weight: 700 !important;
		}
	</style>
	<?php
});
