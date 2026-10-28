<?php
/**
 * Admin Portal Orders Management Controller
 *
 * Prepares filtered orders list, search results, summaries, and nonces.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Orders View Model Data
 *
 * @param int $pending_count Optional pending count from analytics
 * @return array Orders view model data
 */
function ashrafco_portal_get_orders_data($pending_count = 0) {
	$orders_search         = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
	$orders_status         = isset($_GET['status']) ? sanitize_key($_GET['status']) : 'all';
	$orders_payment_method = isset($_GET['payment_method']) ? sanitize_text_field(wp_unslash($_GET['payment_method'])) : 'all';
	$orders_date_from      = isset($_GET['date_from']) ? sanitize_text_field(wp_unslash($_GET['date_from'])) : '';
	$orders_date_to        = isset($_GET['date_to']) ? sanitize_text_field(wp_unslash($_GET['date_to'])) : '';
	$orders_per_page       = isset($_GET['per_page']) ? min(100, max(5, absint($_GET['per_page']))) : 10;
	$orders_paged          = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;

	$orders_summary = function_exists('ashrafco_get_orders_summary') ? ashrafco_get_orders_summary() : array(
		'all'        => 0,
		'processing' => $pending_count,
		'shipped'    => 0,
		'completed'  => 0,
		'cancelled'  => 0,
	);

	$orders_query_result = function_exists('ashrafco_get_portal_filtered_orders') ? ashrafco_get_portal_filtered_orders(array(
		'search'         => $orders_search,
		'status'         => $orders_status,
		'payment_method' => $orders_payment_method,
		'date_from'      => $orders_date_from,
		'date_to'        => $orders_date_to,
		'per_page'       => $orders_per_page,
		'paged'          => $orders_paged,
	)) : array(
		'orders'       => array(),
		'total'        => 0,
		'pages'        => 1,
		'current_page' => 1,
		'per_page'     => 10,
	);

	$portal_orders       = $orders_query_result['orders'];
	$orders_total_count  = $orders_query_result['total'];
	$orders_total_pages  = $orders_query_result['pages'];
	$orders_current_page = $orders_query_result['current_page'];

	$orders_nonce        = wp_create_nonce('ashrafco_portal_order_nonce');
	$orders_export_nonce = wp_create_nonce('ashrafco_export_orders_nonce');
	$available_gateways  = function_exists('ashrafco_get_portal_payment_gateways') ? ashrafco_get_portal_payment_gateways() : array();
	$eg_states           = (function_exists('WC') && WC()->countries) ? WC()->countries->get_states('EG') : array();

	return array(
		'orders_search'         => $orders_search,
		'orders_status'         => $orders_status,
		'orders_payment_method' => $orders_payment_method,
		'orders_date_from'      => $orders_date_from,
		'orders_date_to'        => $orders_date_to,
		'orders_per_page'       => $orders_per_page,
		'orders_paged'          => $orders_paged,
		'orders_summary'        => $orders_summary,
		'portal_orders'         => $portal_orders,
		'orders_total_count'    => $orders_total_count,
		'orders_total_pages'    => $orders_total_pages,
		'orders_current_page'   => $orders_current_page,
		'available_gateways'    => $available_gateways,
		'eg_states'             => $eg_states,
		'orders_nonce'          => $orders_nonce,
		'orders_export_nonce'   => $orders_export_nonce,
	);
}
