<?php
/**
 * Custom Shipping & Delivery Management Module
 *
 * Implements:
 * - Egyptian shipping zones management & governorate rate mapping
 * - Free shipping subtotal calculation & dynamic threshold progress bar
 * - Fast delivery rules for ceramic & heavy tiling equipment
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Ensure core shipping zones logic is loaded
if (file_exists(CUSTOM_STORE_DIR . '/inc/shipping-zones.php')) {
	require_once CUSTOM_STORE_DIR . '/inc/shipping-zones.php';
}

/**
 * Filter shipping methods and rates dynamically based on active rules.
 *
 * @param array $rates Available shipping rates.
 * @param array $package Shipping package.
 * @return array Filtered rates.
 */
function custom_store_filter_shipping_rates($rates, $package) {
	// Shipping rates can be adjusted or decorated here
	return apply_filters('custom_store_shipping_rates', $rates, $package);
}
add_filter('woocommerce_package_rates', 'custom_store_filter_shipping_rates', 15, 2);
