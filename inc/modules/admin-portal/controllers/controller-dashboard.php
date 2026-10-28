<?php
/**
 * Admin Portal Dashboard & Analytics Controller
 *
 * Prepares KPIs, net revenue, order volume, stock alerts, and sales analytics.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Analytics & Dashboard Data Model
 *
 * @return array Dashboard view model data
 */
function ashrafco_portal_get_dashboard_data() {
$selected_period = isset($_GET['period']) ? sanitize_key($_GET['period']) : 'last_30_days';
$custom_start_date = isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : '';
$custom_end_date = isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : '';

$portal_analytics = ashrafco_get_portal_analytics_data($selected_period, $custom_start_date, $custom_end_date);
$analytics_nonce = wp_create_nonce('ashrafco_portal_analytics_nonce');

// Synchronize global metrics with real computed analytics
$total_orders_count = $portal_analytics['kpis']['total_orders']['raw'];
$total_net_sales = $portal_analytics['kpis']['net_revenue']['raw'];
$pending_count = $portal_analytics['kpis']['pending_orders']['raw'];
$total_stock_alerts = $portal_analytics['kpis']['stock_alerts']['raw'];


	return array(
		'selected_period'     => $selected_period,
		'custom_start_date'   => $custom_start_date,
		'custom_end_date'     => $custom_end_date,
		'portal_analytics'    => $portal_analytics,
		'analytics_nonce'     => $analytics_nonce,
		'pending_count'       => $pending_count,
		'total_stock_alerts'  => $total_stock_alerts,
	);
}
