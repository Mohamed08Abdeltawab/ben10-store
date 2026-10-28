<?php
/**
 * Admin Portal Modular Domain Controller
 *
 * Bootstraps all Admin Portal sub-systems:
 * - Sales and revenue analytics endpoints
 * - Order workflow and modal inspection endpoints
 * - Category technical specifications and brand taxonomy management
 * - Automated discount and coupon generators
 * - Flash sale promotions and deal countdown sync
 * - Customer lifetime spend directory
 * - Standalone route registration and template loading
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Module Directory Constant
if (!defined('CUSTOM_STORE_PORTAL_DIR')) {
	define('CUSTOM_STORE_PORTAL_DIR', __DIR__);
}

// 1. Require Subsystem Handlers & Controllers
require_once CUSTOM_STORE_PORTAL_DIR . '/portal-ajax.php';
require_once CUSTOM_STORE_PORTAL_DIR . '/controllers/controller-main.php';

/**
 * 2. Programmatically ensure the standalone 'admin-portal' page exists.
 */
function ashrafco_ensure_admin_portal_page() {
	if (get_option('ashrafco_admin_portal_page_created_v1') || get_option('test_theme_admin_portal_page_created_v2')) {
		return;
	}

	$page_slug     = 'admin-portal';
	$existing_page = get_page_by_path($page_slug);

	if (!$existing_page) {
		$page_id = wp_insert_post(array(
			'post_title'     => 'بوابة إدارة المتجر',
			'post_name'      => $page_slug,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_content'   => '',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		));

		if ($page_id && !is_wp_error($page_id)) {
			update_post_meta($page_id, '_wp_page_template', 'template-admin-portal.php');
		}
	} else {
		update_post_meta($existing_page->ID, '_wp_page_template', 'template-admin-portal.php');
	}

	update_option('ashrafco_admin_portal_page_created_v1', 1);
	update_option('test_theme_admin_portal_page_created_v2', 1);
}
add_action('init', 'ashrafco_ensure_admin_portal_page', 20);

if (!function_exists('test_theme_ensure_admin_portal_page')) {
	function test_theme_ensure_admin_portal_page() {
		ashrafco_ensure_admin_portal_page();
	}
}

/**
 * 3. Template router: Guarantee requests to '/admin-portal/' and '/portal/'
 * load 'template-admin-portal.php'.
 *
 * @param string $template Current template file.
 * @return string Filtered template path.
 */
function ashrafco_admin_portal_template_router($template) {
	if (is_page('admin-portal') || is_page('portal') || is_page_template('template-admin-portal.php')) {
		$custom_template = locate_template(array('template-admin-portal.php'));
		if ($custom_template) {
			return $custom_template;
		}
	}

	// URL-based routing fallback for direct requests
	$uri  = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
	$path = trim((string) wp_parse_url($uri, PHP_URL_PATH), '/');

	if ('admin-portal' === $path || 'portal' === $path || 0 === strpos($path, 'admin-portal/')) {
		$custom_template = locate_template(array('template-admin-portal.php'));
		if ($custom_template) {
			return $custom_template;
		}
	}

	return $template;
}
add_filter('template_include', 'ashrafco_admin_portal_template_router', 99);

if (!function_exists('test_theme_admin_portal_template')) {
	function test_theme_admin_portal_template($template) {
		return ashrafco_admin_portal_template_router($template);
	}
}

/**
 * 4. Enqueue dedicated portal styles when viewing Admin Portal page.
 */
function ashrafco_enqueue_admin_portal_assets() {
	if (is_page('admin-portal') || is_page('portal') || is_page_template('template-admin-portal.php')) {
		$theme_uri = get_template_directory_uri();
		$theme_dir = get_template_directory();

		$css_file = '/assets/src/css/pages/admin-portal.css';
		$css_ver  = file_exists($theme_dir . $css_file) ? (string) filemtime($theme_dir . $css_file) : CUSTOM_STORE_VERSION;

		wp_enqueue_style(
			'ashrafco_admin_portal_styles',
			$theme_uri . $css_file,
			array(),
			$css_ver
		);
	}
}
add_action('wp_enqueue_scripts', 'ashrafco_enqueue_admin_portal_assets', 25);
