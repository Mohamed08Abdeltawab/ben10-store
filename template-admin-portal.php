<?php
/**
 * Template Name: Ashrafco Admin Portal
 * Description: Dedicated standalone full-width administration dashboard.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 * @version 2.0.0
 */

defined('ABSPATH') || exit;

// 1. Access Control & Security Verification
if (!is_user_logged_in() || (!current_user_can('manage_woocommerce') && !current_user_can('manage_options'))) {
	if ('POST' === ($_SERVER['REQUEST_METHOD'] ?? 'GET')) {
		wp_die(esc_html__('غير مصرح لك بالوصول إلى لوحة إدارة المتجر.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}
	wp_safe_redirect(home_url('/my-account/'));
	exit;
}

// 2. Load Modular Feature Architecture Bootstrap
$portal_module = get_theme_file_path('inc/modules/admin-portal/admin-portal.php');
if (file_exists($portal_module)) {
	require_once $portal_module;
}

// 3. Dispatch Controller & Dynamically Include Appropriate View
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'overview';
ashrafco_portal_dispatch($active_tab);