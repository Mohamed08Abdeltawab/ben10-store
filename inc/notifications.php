<?php
/**
 * Ashrafco Theme - In-App Notifications Presentation Module
 *
 * Handles customer header bell, dropdown markup, account endpoint ('my-notifications'),
 * Admin Portal notification bell, admin bar integration, and client assets enqueue.
 *
 * @package Ashrafco
 * @since   1.0.0
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * 1. Register WooCommerce Endpoint for Customer Notifications.
 */
function ashrafco_register_notifications_endpoint()
{
	add_rewrite_endpoint('my-notifications', EP_ROOT | EP_PAGES);
}
add_action('init', 'ashrafco_register_notifications_endpoint');

/**
 * 2. Add query var for WooCommerce endpoint routing.
 *
 * @param array $query_vars
 * @return array
 */
function ashrafco_notifications_wc_query_vars($query_vars)
{
	$query_vars['my-notifications'] = 'my-notifications';
	return $query_vars;
}
add_filter('woocommerce_get_query_vars', 'ashrafco_notifications_wc_query_vars');

function ashrafco_notifications_query_vars($vars)
{
	$vars[] = 'my-notifications';
	return $vars;
}
add_filter('query_vars', 'ashrafco_notifications_query_vars');

/**
 * 3. Flush rewrite rules once safely if endpoint is added.
 */
function ashrafco_maybe_flush_notifications_endpoint()
{
	$flushed_version = get_option('ashrafco_notifications_endpoint_flushed_v1');
	if ('1.0' !== $flushed_version) {
		ashrafco_register_notifications_endpoint();
		flush_rewrite_rules(false);
		update_option('ashrafco_notifications_endpoint_flushed_v1', '1.0');
	}
}
add_action('init', 'ashrafco_maybe_flush_notifications_endpoint', 99);

/**
 * 4. Filter the WooCommerce page title on the 'my-notifications' endpoint.
 *
 * @param string $title
 * @param string $endpoint
 * @return string
 */
function ashrafco_notifications_endpoint_title($title, $endpoint = '')
{
	if ('my-notifications' === $endpoint) {
		return __('الإشعارات والتنبيهات', 'ashrafco');
	}
	return $title;
}
add_filter('woocommerce_endpoint_my-notifications_title', 'ashrafco_notifications_endpoint_title', 10, 2);

/**
 * 5. Render Account Notifications Endpoint Content.
 */
function ashrafco_render_account_notifications_content()
{
	if (!is_user_logged_in()) {
		echo '<p class="text-sm text-slate-500">' . esc_html__('يرجى تسجيل الدخول لعرض إشعاراتك.', 'ashrafco') . '</p>';
		return;
	}

	$file = get_stylesheet_directory() . '/woocommerce/myaccount/my-notifications.php';
	if (file_exists($file)) {
		include $file;
	} else {
		echo '<p class="text-sm text-slate-500">' . esc_html__('جاري تحميل نظام الإشعارات...', 'ashrafco') . '</p>';
	}
}
add_action('woocommerce_account_my-notifications_endpoint', 'ashrafco_render_account_notifications_content');

/**
 * 6. Enqueue Notifications Client Script & Styles.
 */
function ashrafco_enqueue_notifications_assets()
{
	$is_logged_in = is_user_logged_in();
	$is_admin_user = current_user_can('manage_woocommerce') || current_user_can('manage_options');

	$user_type = 'guest';
	if ($is_admin_user) {
		$user_type = 'admin';
	} elseif ($is_logged_in) {
		$user_type = 'customer';
	}

	$notifications_js_rel = file_exists(get_template_directory() . '/assets/src/js/notifications.js')
		? '/assets/src/js/notifications.js'
		: '/assets/js/notifications.js';

	wp_enqueue_script(
		'ashrafco-notifications-js',
		get_template_directory_uri() . $notifications_js_rel,
		array(),
		function_exists('custom_store_get_asset_version')
			? custom_store_get_asset_version($notifications_js_rel)
			: (function_exists('ashrafco_get_asset_version') ? ashrafco_get_asset_version($notifications_js_rel) : (defined('CUSTOM_STORE_VERSION') ? CUSTOM_STORE_VERSION : '1.0.0')),
		true
	);

	$unread_count = 0;
	if (function_exists('ashrafco_get_unread_notifications_count')) {
		if ('admin' === $user_type) {
			$unread_count = ashrafco_get_unread_notifications_count('admin', 0);
		} elseif ('customer' === $user_type) {
			$unread_count = ashrafco_get_unread_notifications_count('customer', get_current_user_id());
		}
	}

	wp_localize_script(
		'ashrafco-notifications-js',
		'AshrafcoNotifConfig',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ashrafco_notif_nonce'),
			'isLoggedIn' => $is_logged_in,
			'userType' => $user_type,
			'initialUnreadCount' => $unread_count,
			'pollInterval' => 35000, // 35 seconds
			'myNotificationsUrl' => function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('my-notifications') : home_url('/my-account/my-notifications/'),
			'portalNotificationsUrl' => function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('my-notifications') : home_url('/my-account/my-notifications/'),
			'i18n' => array(
				'loading' => __('جاري التحميل...', 'ashrafco'),
				'noNotifications' => __('لا توجد إشعارات جديدة حالياً.', 'ashrafco'),
				'markAsRead' => __('تحديد كمقروء', 'ashrafco'),
				'markAllAsRead' => __('تحديد الكل كمقروء', 'ashrafco'),
				'viewAll' => __('عرض جميع الإشعارات', 'ashrafco'),
				'markedAllSuccess' => __('تم تحديد جميع الإشعارات كمقروءة.', 'ashrafco'),
				'errorOccurred' => __('حدث خطأ أثناء المعالجة، يرجى المحاولة ثانية.', 'ashrafco'),
			),
		)
	);
}
add_action('wp_enqueue_scripts', 'ashrafco_enqueue_notifications_assets', 20);

/**
 * 7. Render Customer Notification Bell (for Store Header - Desktop & Mobile).
 *
 * @param string $viewport 'desktop' | 'mobile'
 */
/**
 * Render Skeleton Loading Placeholder for Notifications Dropdown.
 */
function test_theme_render_notification_skeleton()
{
	?>
	<div class="ashrafco-notif-skeleton p-2 space-y-2">
		<?php for ($i = 0; $i < 3; $i++): ?>
			<div class="p-3 rounded-xl bg-slate-50/70 border border-slate-100/60 flex items-start gap-3 animate-pulse">
				<div class="w-8 h-8 rounded-xl bg-slate-200 flex-shrink-0"></div>
				<div class="flex-1 space-y-2 py-0.5 text-right">
					<div class="h-3 bg-slate-200 rounded-md w-2/5"></div>
					<div class="h-2.5 bg-slate-200/80 rounded-md w-4/5"></div>
					<div class="h-2 bg-slate-200/50 rounded-md w-1/4"></div>
				</div>
			</div>
		<?php endfor; ?>
	</div>
	<?php
}

function test_theme_render_customer_notification_bell($viewport = 'desktop')
{
	if (!is_user_logged_in()) {
		return;
	}

	$user_id  = get_current_user_id();
	$is_admin = current_user_can('manage_woocommerce') || current_user_can('manage_options');
	$scope    = $is_admin ? 'admin' : 'customer';

	$unread_count = 0;
	if (function_exists('ashrafco_get_unread_notifications_count')) {
		$unread_count = $is_admin
			? ashrafco_get_unread_notifications_count('admin', 0)
			: ashrafco_get_unread_notifications_count('customer', $user_id);
	}

	$notifications_url = function_exists('wc_get_account_endpoint_url')
		? wc_get_account_endpoint_url('my-notifications')
		: home_url('/my-account/my-notifications/');

	$formatted_count = $unread_count > 99 ? '99+' : (string) $unread_count;
	$has_unread      = $unread_count > 0;

	if ('mobile' === $viewport) {
		?>
		<!-- Mobile Notification Bell Button -->
		<div class="relative inline-block ashrafco-notif-wrapper" data-scope="<?php echo esc_attr($scope); ?>" dir="rtl">
			<button type="button"
				class="ashrafco-notif-bell-trigger relative flex items-center justify-center w-10 h-10 bg-[#161616] border border-white/10 rounded-2xl shadow-sm shrink-0 cursor-pointer text-white hover:text-[#ff171f] transition-colors focus:outline-none"
				aria-label="<?php esc_attr_e('الإشعارات', 'ashrafco'); ?>" aria-expanded="false" aria-haspopup="true"
				title="<?php echo $is_admin ? esc_attr__('إشعارات المتجر والنظام', 'ashrafco') : esc_attr__('الإشعارات', 'ashrafco'); ?>">
				<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
					stroke-linecap="round" stroke-linejoin="round">
					<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
					<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
				</svg>
				<span
					class="ashrafco-notif-badge absolute -top-1.5 -right-1.5 bg-[#d90429] text-white text-[10px] font-black rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center leading-none shadow-sm select-none <?php echo $has_unread ? '' : 'hidden'; ?>"
					data-badge-scope="<?php echo esc_attr($scope); ?>">
					<?php echo $has_unread ? esc_html($formatted_count) : ''; ?>
				</span>
			</button>

			<!-- Mobile Notification Dropdown Flyout -->
			<div class="ashrafco-notif-dropdown ashrafco-notif-dropdown-mobile hidden bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden text-slate-800"
				role="region" aria-label="<?php esc_attr_e('قائمة الإشعارات', 'ashrafco'); ?>">
				<?php test_theme_render_notification_dropdown_header($scope, $notifications_url); ?>
				<div class="ashrafco-notif-list-container max-h-80 overflow-y-auto divide-y divide-slate-100 p-1">
					<?php test_theme_render_notification_skeleton(); ?>
				</div>
				<?php test_theme_render_notification_dropdown_footer($scope, $notifications_url); ?>
			</div>
		</div>
		<?php
		return;
	}

	// Desktop Layout
	?>
	<!-- Desktop Customer/Admin Notification Bell Action Item -->
	<div class="relative inline-block ashrafco-notif-wrapper" data-scope="<?php echo esc_attr($scope); ?>" dir="rtl">
		<button type="button"
			class="ashrafco-notif-bell-trigger desktop-header-action-item flex flex-col items-center justify-center text-white group hover:opacity-90 transition-opacity select-none cursor-pointer bg-transparent border-0 p-0 focus:outline-none"
			aria-label="<?php esc_attr_e('الإشعارات', 'ashrafco'); ?>" aria-expanded="false" aria-haspopup="true">
			<div class="relative flex items-center justify-center">
				<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
					stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
					<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
					<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
				</svg>
				<span
					class="ashrafco-notif-badge absolute -top-1.5 -right-2 bg-[#ff171f] text-white text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs <?php echo $has_unread ? '' : 'hidden'; ?>"
					data-badge-scope="<?php echo esc_attr($scope); ?>">
					<?php echo $has_unread ? esc_html($formatted_count) : ''; ?>
				</span>
			</div>
			<span
				class="mt-1 text-[11px] font-semibold text-white leading-none"><?php esc_html_e('الإشعارات', 'ashrafco'); ?></span>
		</button>

		<!-- Desktop Notification Dropdown Menu -->
		<div class="ashrafco-notif-dropdown ashrafco-notif-dropdown-desktop hidden absolute left-0 top-full mt-3 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden text-slate-800"
			role="region" aria-label="<?php esc_attr_e('قائمة الإشعارات', 'ashrafco'); ?>">
			<?php test_theme_render_notification_dropdown_header($scope, $notifications_url); ?>
			<div class="ashrafco-notif-list-container max-h-96 overflow-y-auto divide-y divide-slate-100 p-1">
				<?php test_theme_render_notification_skeleton(); ?>
			</div>
			<?php test_theme_render_notification_dropdown_footer($scope, $notifications_url); ?>
		</div>
	</div>
	<?php
}

/**
 * 8. Render Admin Notification Bell (for Admin Portal Header).
 */
function test_theme_render_admin_portal_notifications_bell()
{
	if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		return;
	}

	$unread_count = function_exists('ashrafco_get_unread_notifications_count')
		? ashrafco_get_unread_notifications_count('admin', 0)
		: 0;

	$notifications_tab_url = function_exists('wc_get_account_endpoint_url')
		? wc_get_account_endpoint_url('my-notifications')
		: home_url('/my-account/my-notifications/');
	$formatted_count = $unread_count > 99 ? '99+' : (string) $unread_count;
	$has_unread = $unread_count > 0;
	?>
	<div class="relative inline-block ashrafco-notif-wrapper" data-scope="admin" dir="rtl">
		<button type="button"
			class="ashrafco-notif-bell-trigger relative inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all border border-slate-200/80 cursor-pointer focus:outline-none"
			aria-label="<?php esc_attr_e('إشعارات الإدارة', 'ashrafco'); ?>" aria-expanded="false" aria-haspopup="true"
			title="<?php esc_attr_e('إشعارات النظام والمتجر', 'ashrafco'); ?>">
			<svg class="w-4.5 h-4.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
				stroke-linecap="round" stroke-linejoin="round">
				<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
				<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
			</svg>
			<span
				class="ashrafco-notif-badge absolute -top-1 -right-1 bg-[#d90429] text-white text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs select-none <?php echo $has_unread ? '' : 'hidden'; ?>"
				data-badge-scope="admin">
				<?php echo $has_unread ? esc_html($formatted_count) : ''; ?>
			</span>
		</button>

		<!-- Admin Dropdown Menu -->
		<div class="ashrafco-notif-dropdown ashrafco-notif-dropdown-desktop hidden absolute left-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200 z-50 overflow-hidden text-slate-800"
			role="region" aria-label="<?php esc_attr_e('إشعارات الإدارة', 'ashrafco'); ?>">
			<?php test_theme_render_notification_dropdown_header('admin', $notifications_tab_url); ?>
			<div class="ashrafco-notif-list-container max-h-96 overflow-y-auto divide-y divide-slate-100 p-1">
				<?php test_theme_render_notification_skeleton(); ?>
			</div>
			<?php test_theme_render_notification_dropdown_footer('admin', $notifications_tab_url); ?>
		</div>
	</div>
	<?php
}

/**
 * Dropdown Header HTML helper.
 *
 * @param string $scope 'admin' | 'customer'
 * @param string $view_all_url
 */
function test_theme_render_notification_dropdown_header($scope, $view_all_url)
{
	?>
	<div class="p-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between gap-2">
		<div class="flex items-center gap-2">
			<span class="text-xs font-black text-slate-900"><?php esc_html_e('الإشعارات', 'ashrafco'); ?></span>
			<!-- Loading state indicator before count arrives -->
			<span
				class="ashrafco-notif-loading-indicator inline-flex items-center gap-1.5 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">
				<span class="w-1.5 h-1.5 rounded-full bg-[#d90429] animate-pulse"></span>
				<span class="ashrafco-notif-loading-text"><?php esc_html_e('جاري الفحص...', 'ashrafco'); ?></span>
			</span>
			<!-- Resolved count pill -->
			<span
				class="ashrafco-notif-unread-pill hidden text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-[#d90429]"></span>
		</div>
		<button type="button"
			class="ashrafco-mark-all-read-btn text-[11px] font-bold text-slate-500 hover:text-[#d90429] transition-colors cursor-pointer bg-transparent border-0 p-0 whitespace-nowrap shrink-0"
			data-scope="<?php echo esc_attr($scope); ?>">
			<?php esc_html_e('تحديد الكل كمقروء', 'ashrafco'); ?>
		</button>
	</div>
	<?php
}

/**
 * Dropdown Footer HTML helper.
 *
 * @param string $scope 'admin' | 'customer'
 * @param string $view_all_url
 */
function test_theme_render_notification_dropdown_footer($scope, $view_all_url)
{
	?>
	<div class="p-2.5 bg-slate-50/50 border-t border-slate-100 text-center">
		<a href="<?php echo esc_url($view_all_url); ?>"
			class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-[#d90429] hover:underline">
			<span><?php esc_html_e('عرض جميع الإشعارات', 'ashrafco'); ?></span>
			<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
				<polyline points="15 18 9 12 15 6"></polyline>
			</svg>
		</a>
	</div>
	<?php
}

/**
 * 9. Hook into standard WP Admin Bar for administrator visibility in /wp-admin/.
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function test_theme_admin_bar_notifications($wp_admin_bar)
{
	if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		return;
	}

	$unread = function_exists('ashrafco_get_unread_notifications_count')
		? ashrafco_get_unread_notifications_count('admin', 0)
		: 0;

	$badge = $unread > 0
		? ' <span class="ashrafco-notif-badge" style="background:#d90429; color:#fff; border-radius:10px; padding:1px 6px; font-size:10px; font-weight:bold; margin-right:4px;">' . ($unread > 99 ? '99+' : $unread) . '</span>'
		: '';

	$wp_admin_bar->add_node(array(
		'id' => 'ashrafco_admin_notifications',
		'title' => '<span class="ab-icon dashicons dashicons-bell"></span> ' . __('إشعارات المتجر', 'ashrafco') . $badge,
		'href' => function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('my-notifications') : home_url('/my-account/my-notifications/'),
		'meta' => array(
			'title' => __('عرض مركز إشعارات متجر أشرفكو', 'ashrafco'),
		),
	));
}
add_action('admin_bar_menu', 'test_theme_admin_bar_notifications', 95);
