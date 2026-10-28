<?php
/**
 * Admin Portal Discounts Controller
 *
 * Prepares active discount metrics and security nonces.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Discounts View Model Data
 *
 * @return array Discounts view model data
 */
function ashrafco_portal_get_discounts_data() {
	$discounts_nonce = wp_create_nonce('ashrafco_portal_discounts_nonce');
	$discounts_summary = function_exists('ashrafco_get_discounts_summary') ? ashrafco_get_discounts_summary() : array(
		'total'       => 0,
		'active'      => 0,
		'scheduled'   => 0,
		'ending_soon' => 0,
		'expired'     => 0,
		'no_discount' => 0,
		'catalog_all' => 0,
	);

	return array(
		'discounts_nonce'   => $discounts_nonce,
		'discounts_summary' => $discounts_summary,
	);
}
