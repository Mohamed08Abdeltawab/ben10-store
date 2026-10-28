<?php
/**
 * Modular Feature Architecture & Dynamic Loader
 *
 * Dispatches and initializes optional modules based on configuration in /inc/modules-config.php
 * or programmatic overrides via constants and filters.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

if (!function_exists('custom_store_is_module_active')) {
	/**
	 * Check if a specific modular feature is active.
	 *
	 * @param string $module_key Module identifier (e.g., 'admin_portal', 'quick_product_manager').
	 * @return bool True if module is enabled.
	 */
	function custom_store_is_module_active($module_key) {
		$const_disable = 'CUSTOM_STORE_DISABLE_' . strtoupper($module_key);
		if (defined($const_disable) && constant($const_disable)) {
			return false;
		}

		$const_enable = 'CUSTOM_STORE_ENABLE_' . strtoupper($module_key);
		if (defined($const_enable) && constant($const_enable)) {
			return true;
		}

		static $modules_config = null;
		if (null === $modules_config) {
			$config_file = CUSTOM_STORE_DIR . '/inc/modules-config.php';
			if (file_exists($config_file)) {
				$loaded = require $config_file;
				$modules_config = is_array($loaded) ? $loaded : array();
			} else {
				$modules_config = array();
			}
			$modules_config = (array) apply_filters('custom_store_active_modules', $modules_config);
		}

		$is_active = !empty($modules_config[$module_key]);
		return (bool) apply_filters('custom_store_is_module_active', $is_active, $module_key);
	}
}

if (!function_exists('custom_store_bootstrap_modules')) {
	/**
	 * Bootstrap active modular features.
	 */
	function custom_store_bootstrap_modules() {
		$manifest = array(
			'admin_portal' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/admin-portal/admin-portal.php',
			),
			'quick_product_manager' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/quick-product-manager/quick-product-manager.php',
			),
			'shipping_manager' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/shipping-manager/shipping-manager.php',
			),
			'offers_engine' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/offers-engine/offers-engine.php',
			),
			'inquiries' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/inquiries/inquiries.php',
			),
			'smart_slider' => array(
				'file' => CUSTOM_STORE_DIR . '/inc/modules/smart-slider/smart-slider.php',
			),
		);

		$manifest = (array) apply_filters('custom_store_module_manifest', $manifest);

		foreach ($manifest as $key => $module) {
			if (custom_store_is_module_active($key) && !empty($module['file']) && file_exists($module['file'])) {
				require_once $module['file'];
			}
		}
	}
	custom_store_bootstrap_modules();
}
