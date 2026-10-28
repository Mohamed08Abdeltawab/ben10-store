<?php
/**
 * Admin Portal View: Shipping & Delivery Manager (Tab 6)
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
		<!-- =================================================================
			 TAB: إدارة الشحن والتوصيل (Shipping & Regional Delivery Management)
			 ================================================================= -->
		<?php
		$shipping_partial = get_theme_file_path('template-parts/admin-portal/tab-shipping.php');
		if (file_exists($shipping_partial)) {
			include $shipping_partial;
		}
		?>
