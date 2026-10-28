<?php
/**
 * WooCommerce Customization & Decoupling
 *
 * Suppresses legacy WooCommerce styles to eliminate specificity conflicts,
 * leaving full design control to our custom tokenized design system.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Completely disable default WooCommerce styles.
 *
 * By returning an empty array, we suppress:
 * - woocommerce-layout.css
 * - woocommerce-smallscreen.css
 * - woocommerce-general.css
 *
 * This eliminates over 1,500 legacy float rules and specificity conflicts,
 * allowing modern Tailwind Flex/Grid layouts without !important overrides.
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Declare High-Performance Order Storage (HPOS) compatibility.
 */
add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, false);
	}
});

/**
 * Remove duplicate catalog orderby, result count, and pagination from default hooks.
 * These are already rendered in our custom shop header toolbar and centered pagination in archive-product.php.
 */
add_action('init', function () {
	remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
	remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
	remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
});

/**
 * Arabic label for single add to cart button
 */
add_filter('woocommerce_product_single_add_to_cart_text', function () {
	return __('إضافة إلى السلة', 'ashrafco');
});

/**
 * Suppress default WooCommerce stock HTML inside add to cart form (already displayed in row 1 badge)
 */
add_filter('woocommerce_get_stock_html', function ($html, $product) {
	if (is_product()) {
		return '';
	}
	return $html;
}, 99, 2);

/**
 * Handle Direct Checkout (Instant Buy) for single product without affecting general cart.
 * Relegated to Ashrafco\Core\Orders\Buy_Now_Context for complete session & persistent cart isolation.
 */
add_action('template_redirect', function () {
	if (!isset($_REQUEST['direct_checkout']) || empty($_REQUEST['direct_checkout'])) {
		return;
	}

	// If ashrafco-core Buy_Now_Context is active, it handles safe fallback redirect at priority 5
	if (class_exists('Ashrafco\Core\Orders\Buy_Now_Context')) {
		return;
	}
}, 4);

/**
 * Suppress notices on empty cart page
 */
add_action('template_redirect', function () {
	if (function_exists('is_cart') && is_cart() && function_exists('WC') && WC()->cart && WC()->cart->is_empty()) {
		if (function_exists('wc_clear_notices')) {
			wc_clear_notices();
		}
	}
});