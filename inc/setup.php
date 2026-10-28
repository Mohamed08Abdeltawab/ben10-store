<?php
/**
 * Core Theme Setup & Feature Registrations
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

if (!function_exists('custom_store_theme_setup')) {
	/**
	 * Sets up theme defaults and registers support for various WordPress and WooCommerce features.
	 */
	function custom_store_theme_setup()
	{
		// Make theme available for translation.
		load_theme_textdomain('custom-store-boilerplate', get_template_directory() . '/languages');

		// Standard WordPress Title Tag Management.
		add_theme_support('title-tag');

		// Enable Featured Images (Post Thumbnails).
		add_theme_support('post-thumbnails');

		// Enable HTML5 semantic markup support.
		add_theme_support('html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		));

		// Responsive embeds support.
		add_theme_support('responsive-embeds');

		// Enable Full WooCommerce Support & Core Gallery Features.
		add_theme_support('woocommerce');
		add_theme_support('wc-product-gallery-zoom');
		add_theme_support('wc-product-gallery-lightbox');
		add_theme_support('wc-product-gallery-slider');
	}
}
add_action('after_setup_theme', 'custom_store_theme_setup');

/**
 * Completely hide the WordPress Admin Bar on the front-end.
 */
add_filter('show_admin_bar', '__return_false');

if (!function_exists('custom_store_register_nav_menus')) {
	/**
	 * Register standard navigation menus on 'init' to prevent WP 6.7+ early textdomain loading notices.
	 */
	function custom_store_register_nav_menus()
	{
		register_nav_menus(array(
			'primary' => esc_html__('القائمة الرئيسية', 'custom-store-boilerplate'),
			'mobile'  => esc_html__('قائمة الهاتف', 'custom-store-boilerplate'),
			'footer'  => esc_html__('قائمة التذييل', 'custom-store-boilerplate'),
		));
	}
	add_action('init', 'custom_store_register_nav_menus');
}

if (!function_exists('custom_store_body_classes')) {
	/**
	 * Add mobile-first base body classes.
	 */
	function custom_store_body_classes($classes)
	{
		$classes[] = 'rtl';
		$classes[] = 'font-sans';
		$classes[] = 'bg-[#f7f7fa]';
		$classes[] = 'text-slate-900';
		$classes[] = 'antialiased';
		return $classes;
	}
	add_filter('body_class', 'custom_store_body_classes', 10);
}

/**
 * ============================================================================
 * Central Store Branding & Contact Information Helpers
 * ============================================================================
 */
if (!function_exists('ashrafco_get_store_name')) {
	function ashrafco_get_store_name($lang = 'ar')
	{
		return ('en' === $lang) ? 'Ashrafco' : 'أشرفكو';
	}
}

if (!function_exists('ashrafco_get_store_logo_url')) {
	function ashrafco_get_store_logo_url()
	{
		return get_template_directory_uri() . '/assets/images/eagle-logo.webp';
	}
}

if (!function_exists('ashrafco_get_offers_url')) {
	function ashrafco_get_offers_url()
	{
		$page = get_page_by_path('offers');
		if ($page && 'publish' === $page->post_status) {
			return get_permalink($page->ID);
		}
		$page_promo = get_page_by_path('promotions');
		if ($page_promo && 'publish' === $page_promo->post_status) {
			return get_permalink($page_promo->ID);
		}
		return home_url('/offers/');
	}
}

if (!function_exists('ashrafco_get_contact_phone')) {
	function ashrafco_get_contact_phone()
	{
		return (string) apply_filters('ashrafco_contact_phone', '+201065712366');
	}
}

if (!function_exists('ashrafco_get_whatsapp_phone')) {
	function ashrafco_get_whatsapp_phone()
	{
		return (string) apply_filters('ashrafco_whatsapp_phone', '201065712366');
	}
}

if (!function_exists('ashrafco_get_contact_email')) {
	function ashrafco_get_contact_email()
	{
		return (string) apply_filters('ashrafco_contact_email', 'info@ashrafco.com');
	}
}

if (!function_exists('ashrafco_get_working_hours')) {
	function ashrafco_get_working_hours()
	{
		return (string) apply_filters('ashrafco_working_hours', 'يومياً من 9:00 ص إلى 11:00 م');
	}
}

if (!function_exists('ashrafco_get_store_address')) {
	function ashrafco_get_store_address()
	{
		return (string) apply_filters('ashrafco_store_address', 'القاهرة، مصر العربية');
	}
}

/**
 * ============================================================================
 * Global Interactive Component Output Hooks
 * ============================================================================
 */
if (!function_exists('custom_store_render_mobile_bottom_nav')) {
	function custom_store_render_mobile_bottom_nav()
	{
		if (is_admin()) {
			return;
		}
		if (file_exists(get_template_directory() . '/template-parts/navigation/mobile-bottom-nav.php')) {
			get_template_part('template-parts/navigation/mobile-bottom-nav');
		}
	}
	add_action('wp_footer', 'custom_store_render_mobile_bottom_nav', 30);
}

if (!function_exists('custom_store_render_scroll_to_top')) {
	function custom_store_render_scroll_to_top()
	{
		if (is_admin()) {
			return;
		}
		if (file_exists(get_template_directory() . '/template-parts/scroll-to-top.php')) {
			get_template_part('template-parts/scroll-to-top');
		}
	}
	add_action('wp_footer', 'custom_store_render_scroll_to_top', 35);
}