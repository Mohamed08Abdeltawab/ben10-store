<?php
/**
 * Admin Portal Central AJAX Endpoint Handlers
 *
 * Dispatches and registers all Admin Portal asynchronous endpoints:
 * - Sales and revenue analytics queries (ashrafco_portal_analytics_data)
 * - Order workflow and modal inspection (ashrafco_portal_update_order_status, ashrafco_portal_bulk_update_orders, ashrafco_portal_order_details)
 * - Category technical specifications and brand taxonomy management (ashrafco_get_category_specs, ashrafco_add_brand)
 * - Automated discount and coupon generators (ashrafco_portal_get_discounts, ashrafco_portal_get_coupons)
 * - Flash sale promotions sync (ashrafco_portal_get_promotions)
 * - Customer and system user directory (ashrafco_portal_get_users)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Module Directory Constant
if (!defined('CUSTOM_STORE_PORTAL_DIR')) {
	define('CUSTOM_STORE_PORTAL_DIR', __DIR__);
}

// Ensure all underlying portal subsystems and their handlers are loaded
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-analytics.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-orders.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-categories.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-coupons.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-discounts.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-promotions.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-users.php';

/**
 * Register any additional AJAX endpoint hooks or filters for the admin portal.
 */
function ashrafco_portal_register_ajax_endpoints() {
	// All specific handlers are cleanly defined within their respective domain files
	// and hooked to wp_ajax_*. This central dispatcher ensures they are properly initialized.
}
add_action('admin_init', 'ashrafco_portal_register_ajax_endpoints');
