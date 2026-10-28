<?php
/**
 * Custom Store Boilerplate functions and definitions
 *
 * Serves strictly as a clean, modular autoloader requiring compartmentalized logic from /inc/.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

// Define Theme Constants.
define('CUSTOM_STORE_VERSION', '1.0.0');
define('CUSTOM_STORE_DIR', get_template_directory());
define('CUSTOM_STORE_URI', get_template_directory_uri());

/**
 * Autoload core includes.
 */
require_once CUSTOM_STORE_DIR . '/inc/setup.php';
require_once CUSTOM_STORE_DIR . '/inc/woocommerce-setup.php';
require_once CUSTOM_STORE_DIR . '/inc/order-statuses.php';
require_once CUSTOM_STORE_DIR . '/inc/assets.php';
require_once CUSTOM_STORE_DIR . '/inc/template-functions.php';
require_once CUSTOM_STORE_DIR . '/inc/cart-drawer.php';
require_once CUSTOM_STORE_DIR . '/inc/wishlist.php';
require_once CUSTOM_STORE_DIR . '/inc/catalog.php';
require_once CUSTOM_STORE_DIR . '/inc/product-meta.php';
require_once CUSTOM_STORE_DIR . '/inc/shipping-zones.php';
require_once CUSTOM_STORE_DIR . '/inc/payments.php';
require_once CUSTOM_STORE_DIR . '/inc/custom-payment-gateways.php';
require_once CUSTOM_STORE_DIR . '/inc/checkout.php';
require_once CUSTOM_STORE_DIR . '/inc/account-details.php';
require_once CUSTOM_STORE_DIR . '/inc/account-navigation.php';
require_once CUSTOM_STORE_DIR . '/inc/notifications.php';

/**
 * Autoload modular features architecture.
 */
require_once CUSTOM_STORE_DIR . '/inc/modules/loader.php';