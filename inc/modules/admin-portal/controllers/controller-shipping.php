<?php
/**
 * Admin Portal Shipping Controller
 *
 * Prepares regional shipping metrics, security nonces, and
 * fallback AJAX handlers if ashrafco-core plugin is not active.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Shipping View Model Data
 *
 * @return array Shipping view model data
 */
function ashrafco_portal_get_shipping_data() {
	$shipping_nonce   = wp_create_nonce('ashrafco_portal_shipping_nonce');
	$shipping_summary = function_exists('ashrafco_get_shipping_summary') ? ashrafco_get_shipping_summary() : null;

	if (empty($shipping_summary)) {
		$shipping_summary = array(
			'total_governorates' => 27,
			'total_orders'       => 0,
			'avg_price'          => 75.0,
			'govs_with_orders'   => 0,
		);
	}

	return array(
		'shipping_nonce'   => $shipping_nonce,
		'shipping_summary' => $shipping_summary,
	);
}

/**
 * Fallback shipping data handler when ashrafco-core is inactive.
 */
function ashrafco_portal_ajax_get_shipping_data_fallback() {
	check_ajax_referer('ashrafco_portal_shipping_nonce', 'nonce');

	if (!is_user_logged_in() || (!current_user_can('manage_woocommerce') && !current_user_can('manage_options'))) {
		wp_send_json_error(array('message' => 'غير مصرح لك بعرض بيانات الشحن.'), 403);
	}

	$states_names = function_exists('WC') && WC()->countries ? WC()->countries->get_states('EG') : array();
	$zones        = function_exists('ashrafco_get_shipping_zones') ? ashrafco_get_shipping_zones() : array();

	$items = array();
	$total_price = 0;
	$count = 0;

	foreach ($zones as $zone_id => $zone) {
		$price = isset($zone['base_rate']) ? (float) $zone['base_rate'] : 50.0;
		$states = isset($zone['states']) ? (array) $zone['states'] : array();

		foreach ($states as $st_code) {
			$name = isset($states_names[$st_code]) ? $states_names[$st_code] : $st_code;
			$items[] = array(
				'code'       => $st_code,
				'name'       => $name,
				'zone'       => $zone_id,
				'price'      => $price,
				'active'     => true,
				'orders'     => 0,
				'customers'  => 0,
				'updated_at' => current_time('Y-m-d'),
				'is_custom'  => false,
			);
			$total_price += $price;
			$count++;
		}
	}

	$avg_price = $count > 0 ? round($total_price / $count, 1) : 75.0;

	wp_send_json_success(array(
		'items'   => $items,
		'summary' => array(
			'total_governorates' => $count,
			'total_orders'       => 0,
			'avg_price'          => $avg_price,
			'govs_with_orders'   => 0,
		),
		'top'     => array(),
	));
}

/**
 * Register fallback AJAX endpoints for Shipping management if ashrafco-core is not handling them.
 */
function ashrafco_portal_register_shipping_ajax_fallback() {
	if (!has_action('wp_ajax_ashrafco_portal_get_shipping_data')) {
		add_action('wp_ajax_ashrafco_portal_get_shipping_data', 'ashrafco_portal_ajax_get_shipping_data_fallback');
	}
}
add_action('init', 'ashrafco_portal_register_shipping_ajax_fallback', 25);
