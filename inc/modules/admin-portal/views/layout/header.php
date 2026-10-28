<?php
/**
 * Admin Portal View Layout: Header & Navigation
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl" class="h-full bg-slate-100">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body
	class="min-h-screen flex flex-col font-sans text-slate-800 antialiased selection:bg-orange-500 selection:text-white">

	<!-- =====================================================================
		 1. Top Application Header
		 ===================================================================== -->
	<header class="w-full sticky top-0 z-40 shadow-xs">
		<!-- Top Application Header Bar (Dark Black) -->
		<div class="w-full bg-[#0b0f19] border-b border-slate-800/80"
			style="background-color: #25272bff; border-color: rgba(30, 41, 59, 0.8);">
			<div class="max-w-[1536px] w-full mx-auto px-3 sm:px-6 lg:px-8">
				<div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4">

					<!-- Brand + Portal Identifier (Right side in RTL) -->
					<div class="flex items-center gap-3 sm:gap-4">
						<a href="<?php echo esc_url(add_query_arg('tab', 'overview', remove_query_arg(array('action', 'paged', 'order_id')))); ?>"
							class="flex items-center gap-2.5 group select-none flex-shrink-0"
							title="<?php echo esc_attr($portal_store_name); ?> - بوابة الإدارة">
							<img src="<?php echo esc_url($portal_logo_url); ?>" alt="أشرفكو"
								class="h-9 w-9 sm:h-10 sm:w-10 object-contain rounded-xl p-1 bg-white/10 border border-white/10 shadow-sm group-hover:scale-105 transition-transform">
							<span
								class="text-base sm:text-lg font-black text-white tracking-wide font-sans group-hover:text-amber-400 transition-colors">
								أشرفكو
							</span>
						</a>

						<div class="h-6 w-px bg-slate-800 hidden sm:block"></div>

						<div class="flex items-center gap-2">
							<span
								class="rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 text-xs font-bold px-3 py-1 whitespace-nowrap shadow-2xs">
								بوابة الإدارة
							</span>
						</div>
					</div>

					<!-- Left Profile Dropdown (Left side in RTL) -->
					<div class="relative" id="portal-user-dropdown-container">
						<button type="button" id="portal-user-dropdown-btn"
							class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-slate-900 border border-transparent hover:border-slate-800 transition-all cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-slate-700"
							aria-expanded="false" aria-haspopup="true">
							<!-- Avatar Initial -->
							<div
								class="w-9 h-9 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 text-white flex items-center justify-center font-extrabold text-sm shadow-sm ring-1 ring-white/15">
								<?php echo esc_html(mb_substr($current_user->display_name ?: 'ع', 0, 1, 'UTF-8')); ?>
							</div>

							<!-- User Details -->
							<div class="hidden sm:block text-right">
								<div class="text-xs font-extrabold text-slate-100 truncate max-w-[130px]">
									<?php echo esc_html($current_user->display_name); ?>
								</div>
								<div class="text-[10px] text-amber-400 font-bold">
									<?php echo current_user_can('manage_options') ? esc_html__('مدير النظام', 'ashrafco') : esc_html__('مدير المتجر', 'ashrafco'); ?>
								</div>
							</div>

							<!-- Dropdown Arrow -->
							<svg id="portal-user-dropdown-arrow"
								class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 9l-7 7-7-7" />
							</svg>
						</button>

						<!-- Dropdown Menu -->
						<div id="portal-user-dropdown-menu"
							class="hidden absolute left-0 top-full mt-2 w-60 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl py-2 z-50 text-right backdrop-blur-xl">
							<!-- User Info in Dropdown -->
							<div class="px-4 py-2.5 border-b border-slate-800/80">
								<div class="text-xs font-bold text-white truncate">
									<?php echo esc_html($current_user->display_name); ?>
								</div>
								<div class="text-[11px] text-slate-400 truncate mt-0.5">
									<?php echo esc_html($current_user->user_email); ?>
								</div>
							</div>

							<!-- Menu Links -->
							<div class="py-1">
								<!-- 1. Return to Store Home Page -->
								<a href="<?php echo esc_url(home_url('/')); ?>"
									class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800/80 transition-colors">
									<svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none"
										stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
									</svg>
									<span>العودة للصفحة الرئيسية</span>
								</a>

								<!-- 2. Control Panel (My Account) -->
								<a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>"
									class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800/80 transition-colors">
									<svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
									</svg>
									<span>لوحة التحكم</span>
								</a>
							</div>

							<div class="h-px bg-slate-800/80 my-1"></div>

							<!-- 3. Logout -->
							<div class="py-1">
								<a href="<?php echo esc_url(wc_logout_url()); ?>"
									class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-400 hover:text-rose-300 hover:bg-rose-950/40 transition-colors">
									<svg class="w-4 h-4 rotate-180 flex-shrink-0 text-rose-500" fill="none"
										stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
									</svg>
									<span>تسجيل الخروج</span>
								</a>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>

		<script>
			(function () {
				function initPortalUserDropdown() {
					var btn = document.getElementById('portal-user-dropdown-btn');
					var menu = document.getElementById('portal-user-dropdown-menu');
					var arrow = document.getElementById('portal-user-dropdown-arrow');
					if (!btn || !menu) return;

					btn.addEventListener('click', function (e) {
						e.preventDefault();
						e.stopPropagation();
						var isHidden = menu.classList.contains('hidden');
						if (isHidden) {
							menu.classList.remove('hidden');
							if (arrow) arrow.classList.add('rotate-180');
							btn.setAttribute('aria-expanded', 'true');
						} else {
							menu.classList.add('hidden');
							if (arrow) arrow.classList.remove('rotate-180');
							btn.setAttribute('aria-expanded', 'false');
						}
					});

					document.addEventListener('click', function (e) {
						if (!menu.contains(e.target) && !btn.contains(e.target)) {
							menu.classList.add('hidden');
							if (arrow) arrow.classList.remove('rotate-180');
							btn.setAttribute('aria-expanded', 'false');
						}
					});

					document.addEventListener('keydown', function (e) {
						if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
							menu.classList.add('hidden');
							if (arrow) arrow.classList.remove('rotate-180');
							btn.setAttribute('aria-expanded', 'false');
						}
					});
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', initPortalUserDropdown);
				} else {
					initPortalUserDropdown();
				}
			})();
		</script>

		<!-- =====================================================================
			 Tab Navigation Bar (Overview, Orders, Products)
			 ===================================================================== -->
		<div class="border-t border-slate-100 bg-white">
			<div class="max-w-[1536px] w-full mx-auto px-3 sm:px-6 lg:px-8">
				<nav class="flex space-x-2 space-x-reverse -mb-px overflow-x-auto no-scrollbar"
					aria-label="أقسام البوابة">
					<button type="button" onclick="switchPortalTab('overview')" id="tab-btn-overview"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'overview' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
						</svg>
						<span>الإحصائيات العامة</span>
					</button>

					<button type="button" onclick="switchPortalTab('orders')" id="tab-btn-orders"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'orders' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
						<span>إدارة الطلبات</span>
						<?php if ($pending_count > 0): ?>
							<span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-amber-500 text-white">
								<?php echo esc_html($pending_count); ?>
							</span>
						<?php endif; ?>
					</button>

					<button type="button" onclick="switchPortalTab('products')" id="tab-btn-products"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'products' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
						<span>إدارة التصنيفات والمنتجات</span>
						<?php if ($total_stock_alerts > 0): ?>
							<span class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-rose-500 text-white">
								<?php echo esc_html($total_stock_alerts); ?>
							</span>
						<?php endif; ?>
					</button>

					<!-- Tab 4: إدارة الخصومات -->
					<button type="button" onclick="switchPortalTab('discounts')" id="tab-btn-discounts"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'discounts' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
						</svg>
						<span><?php esc_html_e('إدارة الخصومات', 'ashrafco'); ?></span>
						<span id="portal-tab-discounts-badge"
							class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-rose-500 text-white <?php echo ($discounts_summary['active'] ?? 0) > 0 ? '' : 'hidden'; ?>">
							<?php echo esc_html($discounts_summary['active'] ?? 0); ?>
						</span>
					</button>

					<!-- Tab 5: كوبونات الخصم -->
					<button type="button" onclick="switchPortalTab('coupons')" id="tab-btn-coupons"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'coupons' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
						</svg>
						<span><?php esc_html_e('كوبونات الخصم', 'ashrafco'); ?></span>
						<span id="portal-tab-coupons-badge"
							class="px-1.5 py-0.2 rounded-full text-[10px] font-black bg-rose-500 text-white <?php echo ($coupons_summary['active'] ?? 0) > 0 ? '' : 'hidden'; ?>">
							<?php echo esc_html($coupons_summary['active'] ?? 0); ?>
						</span>
					</button>

					<!-- Tab: إدارة الشحن والتوصيل -->
					<button type="button" onclick="switchPortalTab('shipping')" id="tab-btn-shipping"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'shipping' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
						</svg>
						<span><?php esc_html_e('إدارة الشحن والتوصيل', 'ashrafco'); ?></span>
					</button>


					<!-- Tab 6: مستخدمو النظام -->
					<button type="button" onclick="switchPortalTab('users')" id="tab-btn-users"
						class="portal-tab-btn flex items-center gap-2 py-3.5 px-4 border-b-2 text-xs font-bold transition-all select-none whitespace-nowrap <?php echo 'users' === $active_tab ? 'border-primary text-primary bg-orange-50/50' : 'border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300'; ?>">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
						<span><?php esc_html_e('مستخدمو النظام', 'ashrafco'); ?></span>
					</button>


				</nav>
			</div>
		</div>
	</header>

	<?php if (function_exists('woocommerce_output_all_notices')): ?>
		<div class="woocommerce-notices-wrapper">
			<?php woocommerce_output_all_notices(); ?>
		</div>
	<?php endif; ?>

	<!-- =====================================================================
		 Main Workspace Container
		 ===================================================================== -->
	<main class="flex-1 max-w-[1536px] w-full mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8 space-y-6 sm:space-y-8">

		<!-- Status Feedback Notice -->
		<?php if (!empty($status_update_notice)): ?>
			<div
				class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center justify-between shadow-xs">
				<div class="flex items-center gap-2">
					<svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					<span><?php echo esc_html($status_update_notice); ?></span>
				</div>
				<button type="button" onclick="this.parentElement.remove()"
					class="text-emerald-500 hover:text-emerald-700 text-xs">إغلاق</button>
			</div>
		<?php endif; ?>

		<!-- Product CRUD Feedback Notice -->
		<?php if (!empty($portal_notice)): ?>
			<div
				class="p-4 rounded-2xl <?php echo ('error' === $portal_notice_type) ? 'bg-rose-50 border border-rose-200 text-rose-800' : 'bg-emerald-50 border border-emerald-200 text-emerald-800'; ?> text-sm font-bold flex items-center justify-between shadow-xs">
				<div class="flex items-center gap-2">
					<?php if ('error' === $portal_notice_type): ?>
						<svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					<?php else: ?>
						<svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
						</svg>
					<?php endif; ?>
					<span><?php echo esc_html($portal_notice); ?></span>
				</div>
				<button type="button" onclick="this.parentElement.remove()"
					class="text-slate-400 hover:text-slate-700 text-xs">إغلاق</button>
			</div>
		<?php endif; ?>