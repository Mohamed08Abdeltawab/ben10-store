<?php
/**
 * Admin Portal View Layout: Footer & Client Scripts
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
	</main>

	<!-- Ashrafco Category Specs & Portal Config Initial State -->
	<script>
		window.ASHRAFCO_PORTAL_CONFIG = {
			portalUrl: '<?php echo esc_url(home_url('/admin-portal/')); ?>',
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($portal_ajax_nonce); ?>',
			mainImage: <?php echo ($is_form_mode && !empty($f_thumb_id) && !empty($f_thumb_url)) ? wp_json_encode(array('id' => (int) $f_thumb_id, 'url' => $f_thumb_url)) : 'null'; ?>,
			galleryImages: <?php echo ($is_form_mode && !empty($f_gallery_data)) ? wp_json_encode($f_gallery_data) : '[]'; ?>
		};
		window.ASHRAFCO_CATEGORY_SPECS = <?php echo wp_json_encode($all_portal_categories_specs); ?>;
		window.ASHRAFCO_SAVED_SPEC_VALUES = <?php echo wp_json_encode($f_cat_spec_values); ?>;
	</script>
	<!-- Ashrafco Admin Portal Core Client Script -->
	<script src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/admin-portal.js'), get_stylesheet_directory_uri() . '/assets/js/admin-portal.js')); ?>"></script>

	<!-- Ashrafco Analytics Dashboard Client Scripts -->
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/chart.umd.min.js'), get_stylesheet_directory_uri() . '/assets/js/chart.umd.min.js')); ?>"></script>
	<script>
		window.AshrafcoAnalyticsInitial = <?php echo wp_json_encode($portal_analytics ?? array()); ?>;
		window.AshrafcoAnalyticsConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($analytics_nonce ?? ''); ?>',
			currentPeriod: '<?php echo esc_js($selected_period ?? 'this_month'); ?>',
			startDate: '<?php echo esc_js($custom_start_date ?? ''); ?>',
			endDate: '<?php echo esc_js($custom_end_date ?? ''); ?>'
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-analytics.js'), get_stylesheet_directory_uri() . '/assets/js/portal-analytics.js')); ?>"></script>

	<!-- Ashrafco Orders Management Client Scripts -->
	<script>
		window.AshrafcoOrdersConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($orders_nonce); ?>',
			exportNonce: '<?php echo esc_js($orders_export_nonce); ?>'
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-orders.js'), get_stylesheet_directory_uri() . '/assets/js/portal-orders.js')); ?>"></script>

	<!-- Ashrafco System Users Management Client Scripts -->
	<script>
		window.AshrafcoUsersConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($users_nonce ?? ''); ?>',
			currentUserId: <?php echo (int) get_current_user_id(); ?>,
			canManageOptions: <?php echo current_user_can('manage_options') ? 'true' : 'false'; ?>
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-users.js'), get_stylesheet_directory_uri() . '/assets/js/portal-users.js')); ?>"></script>

	<?php if ('promotions' === $active_tab): ?>
		<!-- Ashrafco Promotions Management Client Scripts (Legacy Fallback) -->
		<script>
			window.AshrafcoPromotionsConfig = {
				ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
				nonce: '<?php echo esc_js($promotions_nonce); ?>',
				canManage: <?php echo current_user_can('manage_woocommerce') || current_user_can('manage_options') ? 'true' : 'false'; ?>
			};
		</script>
		<script
			src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-promotions.js'), get_stylesheet_directory_uri() . '/assets/js/portal-promotions.js')); ?>"></script>
	<?php endif; ?>

	<!-- Ashrafco Discounts Management Client Scripts -->
	<script>
		window.AshrafcoDiscountsConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($discounts_nonce); ?>',
			canManage: <?php echo current_user_can('manage_woocommerce') || current_user_can('edit_products') || current_user_can('manage_options') ? 'true' : 'false'; ?>
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-discounts.js'), get_stylesheet_directory_uri() . '/assets/js/portal-discounts.js')); ?>"></script>

	<!-- Ashrafco Coupons Management Client Scripts -->
	<script>
		window.AshrafcoCouponsConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($coupons_nonce); ?>',
			canManage: <?php echo current_user_can('manage_woocommerce') || current_user_can('manage_options') || current_user_can('edit_shop_coupons') ? 'true' : 'false'; ?>,
			currencySymbol: '<?php echo esc_js(function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'ر.س'); ?>'
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-coupons.js'), get_stylesheet_directory_uri() . '/assets/js/portal-coupons.js')); ?>"></script>

	<!-- Ashrafco Shipping & Delivery Management Client Scripts -->
	<script>
		window.AshrafcoShippingConfig = {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js($shipping_nonce); ?>',
			canManage: <?php echo current_user_can('manage_woocommerce') || current_user_can('manage_options') ? 'true' : 'false'; ?>,
			currencySymbol: '<?php echo esc_js(function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'ج.م'); ?>'
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/portal-shipping.js'), get_stylesheet_directory_uri() . '/assets/js/portal-shipping.js')); ?>"></script>

	<!-- Notification Dropdown & Polling Scripts -->
	<script>
		window.AshrafcoNotifConfig = window.AshrafcoNotifConfig || {
			ajaxUrl: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
			nonce: '<?php echo esc_js(wp_create_nonce('ashrafco_notif_nonce')); ?>',
			isLoggedIn: <?php echo is_user_logged_in() ? 'true' : 'false'; ?>,
			userType: 'admin',
			initialUnreadCount: <?php echo (int) (function_exists('ashrafco_get_unread_notifications_count') ? ashrafco_get_unread_notifications_count('admin', 0) : 0); ?>,
			pollInterval: 35000,
			myNotificationsUrl: '<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('my-notifications') : home_url('/my-account/my-notifications/')); ?>',
			portalNotificationsUrl: '<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('my-notifications') : home_url('/my-account/my-notifications/')); ?>',
			i18n: {
				loading: '<?php echo esc_js(__('جاري التحميل...', 'ashrafco')); ?>',
				noNotifications: '<?php echo esc_js(__('لا توجد إشعارات جديدة حالياً.', 'ashrafco')); ?>',
				markAsRead: '<?php echo esc_js(__('تحديد كمقروء', 'ashrafco')); ?>',
				markAllAsRead: '<?php echo esc_js(__('تحديد الكل كمقروء', 'ashrafco')); ?>',
				viewAll: '<?php echo esc_js(__('عرض جميع الإشعارات', 'ashrafco')); ?>',
				markedAllSuccess: '<?php echo esc_js(__('تم تحديد جميع الإشعارات كمقروءة.', 'ashrafco')); ?>',
				errorOccurred: '<?php echo esc_js(__('حدث خطأ أثناء المعالجة، يرجى المحاولة ثانية.', 'ashrafco')); ?>'
			}
		};
	</script>
	<script
		src="<?php echo esc_url(add_query_arg('ver', ashrafco_get_asset_version('/assets/js/notifications.js'), get_stylesheet_directory_uri() . '/assets/js/notifications.js')); ?>"></script>

	<?php wp_footer(); ?>
</body>

</html>