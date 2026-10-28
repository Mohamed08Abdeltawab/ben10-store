<?php
/**
 * Modular Features Configuration
 *
 * Defines available modular features for easy white-label reusability and toggling.
 * Each module can also be overridden via constants:
 * - CUSTOM_STORE_DISABLE_{MODULE} = true
 * - CUSTOM_STORE_ENABLE_{MODULE} = true
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

return [
	'admin_portal'          => true,  // Full Custom Admin & Analytics Portal
	'quick_product_manager' => true,  // Fast Product & Inventory Editor
	'shipping_manager'      => true,  // Custom Delivery & Shipping Rules
	'offers_engine'         => true,  // Dedicated Promotions & Offers System
	'inquiries'             => true,  // Customer Inquiry & Lead Management System
	'smart_slider'          => true,  // Smart Slider 3 Integration & Responsive Fallback
];
