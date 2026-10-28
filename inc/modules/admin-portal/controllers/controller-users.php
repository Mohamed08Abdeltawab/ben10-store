<?php
/**
 * Admin Portal Users Controller
 *
 * Prepares user directory queries, role aggregations, and security nonces.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Users View Model Data
 *
 * @return array Users view model data
 */
function ashrafco_portal_get_users_data() {
$users_search = isset($_GET['user_search']) ? sanitize_text_field(wp_unslash($_GET['user_search'])) : '';
$users_role = isset($_GET['user_role']) ? sanitize_key($_GET['user_role']) : '';
$users_status = isset($_GET['user_status']) ? sanitize_key($_GET['user_status']) : '';
$users_per_page = isset($_GET['user_per_page']) ? min(100, max(5, absint($_GET['user_per_page']))) : 10;
$users_paged = isset($_GET['user_paged']) ? max(1, absint($_GET['user_paged'])) : 1;

$users_summary = function_exists('ashrafco_get_users_summary') ? ashrafco_get_users_summary() : array(
	'total' => 0,
	'active' => 0,
	'pending' => 0,
	'suspended' => 0,
);

$users_query_result = function_exists('ashrafco_get_system_users') ? ashrafco_get_system_users(array(
	'search' => $users_search,
	'role' => $users_role,
	'status' => $users_status,
	'per_page' => $users_per_page,
	'page' => $users_paged,
)) : array(
	'users' => array(),
	'total' => 0,
	'total_pages' => 1,
	'current_page' => 1,
	'per_page' => 10,
);

$portal_users = $users_query_result['users'];
$users_total_count = $users_query_result['total'];
$users_total_pages = $users_query_result['total_pages'];
$users_current_page = $users_query_result['current_page'];
$users_nonce = wp_create_nonce('ashrafco_portal_users_nonce');

	return array(
		'users_search'       => $users_search,
		'users_role'         => $users_role,
		'users_status'       => $users_status,
		'users_per_page'     => $users_per_page,
		'users_paged'        => $users_paged,
		'users_summary'      => $users_summary,
		'portal_users'       => $portal_users,
		'users_total_count'  => $users_total_count,
		'users_total_pages'  => $users_total_pages,
		'users_current_page' => $users_current_page,
		'users_nonce'        => $users_nonce,
	);
}
