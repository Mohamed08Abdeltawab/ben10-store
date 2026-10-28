<?php
/**
 * Asset Management & Enqueue Pipeline
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Output Google Fonts Preconnect tags in <head> for rapid DNS resolution and TLS handshake.
 */
function custom_store_fonts_preconnect()
{
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'custom_store_fonts_preconnect', 2);

/**
 * Retrieve asset version based on file modification timestamp for reliable cache-busting.
 *
 * @param string $relative_path Relative path from the theme root.
 * @return string Version string.
 */
function custom_store_get_asset_version($relative_path)
{
	$file_path = get_template_directory() . '/' . ltrim($relative_path, '/');
	if (file_exists($file_path)) {
		$mtime = filemtime($file_path);
		if (false !== $mtime && $mtime > 0) {
			return CUSTOM_STORE_VERSION . '.' . $mtime;
		}
	}
	return CUSTOM_STORE_VERSION;
}

/**
 * Global asset version helper alias.
 */
if (!function_exists('ashrafco_get_asset_version')) {
	function ashrafco_get_asset_version($relative_path)
	{
		return custom_store_get_asset_version($relative_path);
	}
}

/**
 * Enqueue scripts and styles.
 */
function custom_store_enqueue_assets()
{
	// 1. Google Fonts: Cairo (Arabic primary) and Inter (Latin secondary).
	wp_enqueue_style(
		'custom-store-fonts',
		'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// 2. Production compiled CSS vs. Fallback to style.css.
	$dist_css_rel = '/assets/dist/css/main.min.css';
	$dist_css_path = get_template_directory() . $dist_css_rel;

	if (file_exists($dist_css_path)) {
		wp_enqueue_style(
			'custom-store-main',
			get_template_directory_uri() . $dist_css_rel,
			array(),
			custom_store_get_asset_version($dist_css_rel),
			'all'
		);
	} else {
		// Fallback to style.css until build pipeline compiles dist/css/main.min.css
		wp_enqueue_style(
			'custom-store-style',
			get_stylesheet_uri(),
			array(),
			custom_store_get_asset_version('/style.css'),
			'all'
		);
	}

	// 3. Header Script (Live Autocomplete Search, Wishlist Sync, Mini-Cart Triggers).
	$header_js_rel = file_exists(get_template_directory() . '/assets/src/js/header.js') ? '/assets/src/js/header.js' : '/assets/js/header.js';
	wp_enqueue_script(
		'test_theme_header',
		get_template_directory_uri() . $header_js_rel,
		array(),
		custom_store_get_asset_version($header_js_rel),
		true
	);

	wp_localize_script(
		'test_theme_header',
		'testThemeData',
		array(
			'ajaxUrl'       => admin_url('admin-ajax.php'),
			'searchNonce'   => wp_create_nonce('test_theme_search_nonce'),
			'wishlistNonce' => wp_create_nonce('ashrafco_wishlist_nonce'),
			'wishlistUrl'   => function_exists('test_theme_get_wishlist_url') ? test_theme_get_wishlist_url() : home_url('/wishlist/'),
			'wishlistCount' => function_exists('test_theme_get_wishlist_count') ? test_theme_get_wishlist_count() : 0,
			'isLoggedIn'    => is_user_logged_in(),
		)
	);

	// 4. Main Script (Header Scroll, Bestsellers Carousel, Countdown Engine).
	$main_js_rel = file_exists(get_template_directory() . '/assets/src/js/main.js') ? '/assets/src/js/main.js' : '/assets/js/main.js';
	wp_enqueue_script(
		'test_theme_main_script',
		get_template_directory_uri() . $main_js_rel,
		array(),
		custom_store_get_asset_version($main_js_rel),
		true
	);

	// 6. Mobile Bottom Navigation & Categories Drawer Controller.
	$mobile_nav_js_rel = file_exists(get_template_directory() . '/assets/src/js/mobile-nav.js') ? '/assets/src/js/mobile-nav.js' : '/assets/js/mobile-nav.js';
	wp_enqueue_script(
		'test_theme_mobile_nav',
		get_template_directory_uri() . $mobile_nav_js_rel,
		array(),
		custom_store_get_asset_version($mobile_nav_js_rel),
		true
	);

	// 7. Scroll-To-Top Button Controller.
	$scroll_top_js_rel = file_exists(get_template_directory() . '/assets/src/js/scroll-to-top.js') ? '/assets/src/js/scroll-to-top.js' : '/assets/js/scroll-to-top.js';
	wp_enqueue_script(
		'test_theme_scroll_to_top',
		get_template_directory_uri() . $scroll_top_js_rel,
		array(),
		custom_store_get_asset_version($scroll_top_js_rel),
		true
	);

	// 8. Mobile Footer Accordion Controller.
	$footer_js_rel = file_exists(get_template_directory() . '/assets/src/js/footer.js') ? '/assets/src/js/footer.js' : '/assets/js/footer.js';
	wp_enqueue_script(
		'test_theme_footer',
		get_template_directory_uri() . $footer_js_rel,
		array(),
		custom_store_get_asset_version($footer_js_rel),
		true
	);

	// 9. Advanced Shop Filters Controller on Shop and Category Archives.
	if ((function_exists('is_shop') && is_shop()) || (function_exists('is_product_taxonomy') && is_product_taxonomy())) {
		$filters_js_rel = file_exists(get_template_directory() . '/assets/src/js/shop-filters.js') ? '/assets/src/js/shop-filters.js' : '/assets/js/shop-filters.js';
		wp_enqueue_script(
			'test_theme_shop_filters',
			get_template_directory_uri() . $filters_js_rel,
			array(),
			custom_store_get_asset_version($filters_js_rel),
			true
		);
	}

	// 11. Client-Side Egyptian Phone Validation & Checkout Interactive Controller.
	if (function_exists('is_checkout') && is_checkout()) {
		$val_js_rel = file_exists(get_template_directory() . '/assets/src/js/checkout-validation.js') ? '/assets/src/js/checkout-validation.js' : '/assets/js/checkout-validation.js';
		wp_enqueue_script(
			'test_theme_checkout_validation',
			get_template_directory_uri() . $val_js_rel,
			array('jquery'),
			custom_store_get_asset_version($val_js_rel),
			true
		);

		wp_localize_script(
			'test_theme_checkout_validation',
			'ashrafcoCheckoutData',
			array(
				'ajaxUrl'     => admin_url('admin-ajax.php'),
				'couponNonce' => wp_create_nonce('ashrafco_checkout_coupon'),
			)
		);
	}

	// 12. In-App Notifications Client Controller.
	if (!has_action('wp_enqueue_scripts', 'ashrafco_enqueue_notifications_assets') && function_exists('ashrafco_enqueue_notifications_assets')) {
		ashrafco_enqueue_notifications_assets();
	}
}
add_action('wp_enqueue_scripts', 'custom_store_enqueue_assets', 10);