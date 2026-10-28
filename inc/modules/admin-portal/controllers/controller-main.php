<?php
/**
 * Admin Portal Main Dispatcher & Controller Orchestrator
 *
 * Coordinates request processing, action handling, data loading,
 * and view presentation in a clean modular MVC architecture.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Module Directory Constant
if (!defined('CUSTOM_STORE_PORTAL_DIR')) {
	define('CUSTOM_STORE_PORTAL_DIR', dirname(__DIR__));
}

// 1. Require Subsystem Controllers
require_once __DIR__ . '/controller-actions.php';
require_once __DIR__ . '/controller-dashboard.php';
require_once __DIR__ . '/controller-orders.php';
require_once __DIR__ . '/controller-products.php';
require_once __DIR__ . '/controller-discounts.php';
require_once __DIR__ . '/controller-coupons.php';
require_once __DIR__ . '/controller-shipping.php';
require_once __DIR__ . '/controller-users.php';

/**
 * Dispatch Admin Portal Request & Render Corresponding View
 *
 * @param string $active_tab Current tab parameter from $_GET['tab']
 */
function ashrafco_portal_dispatch($active_tab = 'overview') {
	// Auto-detect products sub-actions or category drill-down
	$action = isset($_GET['action']) ? sanitize_key($_GET['action']) : '';
	if (in_array($action, array('new-product', 'edit-product', 'products'), true) || isset($_GET['cat_id']) || isset($_GET['cat_search']) || isset($_GET['cat_filter'])) {
		$active_tab = 'products';
	}

	// Normalize active tab
	if (!in_array($active_tab, array('overview', 'dashboard', 'orders', 'products', 'discounts', 'coupons', 'shipping', 'users'), true)) {
		$active_tab = 'overview';
	}

	// 1. Process Actions (Product Deletion, Form CRUD, Order Status)
	$actions_result       = ashrafco_portal_handle_actions();
	$status_update_notice = $actions_result['status_update_notice'];
	$portal_notice        = $actions_result['portal_notice'];
	$portal_notice_type   = $actions_result['portal_notice_type'];

	// 2. Application Context & Environment Setup
	$current_user      = wp_get_current_user();
	$portal_store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
	$portal_logo_url   = function_exists('ashrafco_get_store_logo_url') ? ashrafco_get_store_logo_url() : (get_stylesheet_directory_uri() . '/assets/images/logo.png');
	$promotions_nonce  = wp_create_nonce('ashrafco_portal_promotions_nonce');
	$all_product_categories = get_terms(array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	));
	if (is_wp_error($all_product_categories)) {
		$all_product_categories = array();
	}

	// WP Media Library and Admin Bar Controls
	wp_enqueue_media();
	show_admin_bar(false);

	// 3. Global Metrics & Navigation Badges
	$dashboard_data     = ashrafco_portal_get_dashboard_data();
	$pending_count      = $dashboard_data['pending_count'] ?? 0;
	$total_stock_alerts = $dashboard_data['total_stock_alerts'] ?? 0;

	$discounts_data     = ashrafco_portal_get_discounts_data();
	$discounts_summary  = $discounts_data['discounts_summary'] ?? array('active' => 0, 'scheduled' => 0, 'expired' => 0, 'total' => 0);
	$discounts_nonce    = $discounts_data['discounts_nonce'] ?? '';

	$coupons_data       = ashrafco_portal_get_coupons_data();
	$coupons_summary    = $coupons_data['coupons_summary'] ?? array('active' => 0, 'scheduled' => 0, 'expired' => 0, 'total' => 0);
	$coupons_nonce      = $coupons_data['coupons_nonce'] ?? '';

	$shipping_data      = ashrafco_portal_get_shipping_data();
	$shipping_summary   = $shipping_data['shipping_summary'] ?? array('total_governorates' => 27, 'total_orders' => 0, 'avg_price' => 75.0, 'govs_with_orders' => 0);
	$shipping_nonce     = $shipping_data['shipping_nonce'] ?? '';

	// Base View Model shared across layout and views
	$view_data = array_merge(
		$dashboard_data,
		$discounts_data,
		$coupons_data,
		$shipping_data,
		array(
			'active_tab'                  => $active_tab,
			'current_user'                => $current_user,
			'portal_store_name'           => $portal_store_name,
			'portal_logo_url'             => $portal_logo_url,
			'status_update_notice'        => $status_update_notice,
			'portal_notice'               => $portal_notice,
			'portal_notice_type'          => $portal_notice_type,
			'promotions_nonce'            => $promotions_nonce,
			'orders_nonce'                => wp_create_nonce('ashrafco_portal_order_nonce'),
			'orders_export_nonce'         => wp_create_nonce('ashrafco_export_orders_nonce'),
			'all_product_categories'      => $all_product_categories,
			'is_form_mode'                => false,
			'f_thumb_id'                  => 0,
			'f_thumb_url'                 => '',
			'f_gallery_data'              => array(),
			'portal_ajax_nonce'           => wp_create_nonce('ashrafco_portal_ajax_nonce'),
			'all_portal_categories_specs' => function_exists('ashrafco_get_portal_categories_specs_matrix') ? ashrafco_get_portal_categories_specs_matrix() : array(),
			'f_cat_spec_values'           => array(),
		)
	);

	// 4. Tab Specific Data Population
	if ('orders' === $active_tab) {
		$orders_data = ashrafco_portal_get_orders_data($pending_count);
		$view_data   = array_merge($view_data, $orders_data);
	} elseif ('products' === $active_tab) {
		$products_data = ashrafco_portal_get_products_data();
		$view_data     = array_merge($view_data, $products_data);
	} elseif ('users' === $active_tab) {
		$users_data = ashrafco_portal_get_users_data();
		$view_data  = array_merge($view_data, $users_data);
	}

	// 5. Render Layout & Requested Screen View
	ashrafco_portal_render_view($active_tab, $view_data);
}

/**
 * Render Admin Portal View Layout & Content
 *
 * @param string $tab Active tab name
 * @param array  $data View model parameters
 */
function ashrafco_portal_render_view($tab, $data = array()) {
	// Extract variables to local view scope
	extract($data, EXTR_SKIP);

	$views_dir = CUSTOM_STORE_PORTAL_DIR . '/views';

	// 1. Layout Header & Top Navigation Bar
	include $views_dir . '/layout/header.php';

	// 2. Render Screen View Dynamically Based on $tab
	switch ($tab) {
		case 'orders':
			include $views_dir . '/view-orders.php';
			break;
		case 'products':
			include $views_dir . '/view-products.php';
			break;
		case 'discounts':
			include $views_dir . '/view-discounts.php';
			break;
		case 'coupons':
			include $views_dir . '/view-coupons.php';
			break;
		case 'shipping':
			include $views_dir . '/view-shipping.php';
			break;
		case 'users':
			include $views_dir . '/view-users.php';
			break;
		case 'overview':
		case 'dashboard':
		default:
			include $views_dir . '/view-dashboard.php';
			break;
	}

	// 3. Layout Footer & Client Scripts
	include $views_dir . '/layout/footer.php';
}
