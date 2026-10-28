<?php
/**
 * Admin Portal Coupons Controller
 *
 * Prepares coupon metrics and security nonces.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Coupons View Model Data
 *
 * @return array Coupons view model data
 */
function ashrafco_portal_get_coupons_data() {
	$coupons_nonce = wp_create_nonce('ashrafco_portal_coupons_nonce');
	$coupons_summary = function_exists('ashrafco_get_coupons_summary') ? ashrafco_get_coupons_summary() : array(
		'total'       => 0,
		'active'      => 0,
		'scheduled'   => 0,
		'ending_soon' => 0,
		'expired'     => 0,
		'disabled'    => 0,
	);

	return array(
		'coupons_nonce'   => $coupons_nonce,
		'coupons_summary' => $coupons_summary,
	);
}
