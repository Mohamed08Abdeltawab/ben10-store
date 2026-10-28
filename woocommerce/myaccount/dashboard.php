<?php
/**
 * Modern My Account Dashboard Overview Template Override for Ashrafco Child Theme
 *
 * Matching Desktop (Reference Image 1) and Mobile (Reference Image 2)
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 10.0.0
 */

defined('ABSPATH') || exit;

$current_user = wp_get_current_user();
$order_count = wc_get_customer_order_count($current_user->ID);
$billing_city = get_user_meta($current_user->ID, 'billing_city', true);
$billing_gov = get_user_meta($current_user->ID, 'billing_state', true);
$phone = get_user_meta($current_user->ID, 'billing_phone', true);

$states = WC()->countries ? WC()->countries->get_states('EG') : array();
$gov_name = ($billing_gov && isset($states[$billing_gov])) ? $states[$billing_gov] : $billing_gov;
$dash_wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
$dash_store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';

// Role & Permission Check
$is_admin = current_user_can('manage_options') || current_user_can('administrator');
$is_shop_manager = !$is_admin && (current_user_can('manage_woocommerce') || in_array('shop_manager', (array) $current_user->roles, true));
$can_manage_store = $is_admin || $is_shop_manager;
?>

<div class="ashrafco-dashboard w-full font-sans select-none" dir="rtl">

	<!-- =========================================================================
		 1. HERO WELCOME CARD (Desktop: Image 1 | Mobile: Image 2)
		 ========================================================================= -->
	<div
		class="w-full relative overflow-hidden bg-gradient-to-br from-[#0c1017] via-[#141b26] to-[#0c1017] border border-slate-800/80 rounded-2xl sm:rounded-3xl p-5 sm:p-7 text-white shadow-lg">

		<!-- Subtle Red Eagle Artwork Watermark matching Ashrafco branding -->
		<div
			class="absolute inset-y-0 right-0 w-2/3 sm:w-1/2 opacity-20 pointer-events-none overflow-hidden flex items-center justify-end select-none">
			<img src="<?php echo esc_url(get_theme_file_uri('assets/images/eagle-logo.webp')); ?>" alt=""
				class="h-full w-auto object-cover transform translate-x-1/4 scale-125 filter contrast-125 pointer-events-none" />
		</div>

		<!-- Subtle Red Glow Radial Filter -->
		<div class="absolute top-0 right-0 w-64 h-64 bg-primary/15 rounded-full blur-3xl pointer-events-none"></div>

		<div class="relative z-10">

			<!-- DESKTOP HEADER LAYOUT (sm:flex sm:items-center sm:justify-between sm:gap-6) -->
			<div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-6">
				<div class="space-y-1.5 min-w-0 max-w-2xl">
					<div class="flex items-center gap-2.5 flex-wrap">
						<h2 class="text-xl sm:text-2xl font-black tracking-tight text-white m-0 leading-tight">
							مرحباً، <?php echo esc_html($current_user->display_name); ?> 👋
						</h2>
						<?php if ($is_admin): ?>
								<span
									class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-[#d97706]/20 text-[#fbbf24] border border-[#d97706]/40 shadow-xs">
									<?php esc_html_e('مدير النظام', 'ashrafco'); ?>
								</span>
						<?php elseif ($is_shop_manager): ?>
								<span
									class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-extrabold bg-[#d97706]/20 text-[#fbbf24] border border-[#d97706]/40 shadow-xs">
									<?php esc_html_e('مدير المتجر', 'ashrafco'); ?>
								</span>
						<?php endif; ?>
					</div>
					<p class="text-xs sm:text-sm text-slate-300 font-normal leading-relaxed m-0">
						من خلال لوحة التحكم الخاصة بك، يمكنك متابعة طلباتك السابقة والنشطة وتعديل بيانات حسابك بكل
						سهولة.
					</p>
				</div>

				<!-- Desktop Action Buttons -->
				<div class="flex items-center gap-2.5 flex-shrink-0">
					<?php if ($can_manage_store): ?>
							<a href="<?php echo esc_url(home_url('/admin-portal/')); ?>"
								class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-primary hover:bg-primary-hover transition-all shadow-md active:scale-95">
								<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
								</svg>
								<span>لوحة إدارة المتجر</span>
							</a>
					<?php endif; ?>
					<a href="<?php echo esc_url(wc_logout_url()); ?>"
						class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-200 hover:text-white bg-white/10 hover:bg-white/15 border border-white/15 transition-all active:scale-95">
						<svg class="w-4 h-4 rotate-180 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
						</svg>
						<span>تسجيل الخروج</span>
					</a>
				</div>
			</div>

			<!-- MOBILE HEADER LAYOUT (< sm: Strictly Reference Image 2) -->
			<div class="block sm:hidden space-y-4">
				<div class="flex items-center gap-3.5">
					<div
						class="w-13 h-13 rounded-2xl bg-[#d97706]/20 border border-[#d97706]/40 text-[#fbbf24] flex items-center justify-center flex-shrink-0 shadow-inner p-2.5">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
					</div>
					<div class="space-y-1 min-w-0">
						<h2 class="text-lg font-black tracking-tight text-white m-0 truncate">
							مرحباً ، <?php echo esc_html($current_user->display_name); ?> 👋
						</h2>
						<?php if ($is_admin): ?>
								<div class="flex items-center gap-1">
									<span
										class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-[#d97706]/25 text-[#fbbf24] border border-[#d97706]/40">
										<span><?php esc_html_e('مدير النظام', 'ashrafco'); ?></span>
										<span>👑</span>
									</span>
								</div>
						<?php elseif ($is_shop_manager): ?>
								<div class="flex items-center gap-1">
									<span
										class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-[#d97706]/25 text-[#fbbf24] border border-[#d97706]/40">
										<span><?php esc_html_e('مدير المتجر', 'ashrafco'); ?></span>
										<span>💼</span>
									</span>
								</div>
						<?php endif; ?>
						<p class="text-xs text-slate-300 font-medium m-0 truncate">
							تابع طلباتك وبيانات حسابك من هنا.
						</p>
					</div>
				</div>

				<!-- Mobile Action Buttons (Sharing Single Row) -->
				<div class="flex items-center gap-2 pt-1 w-full">
					<?php if ($can_manage_store): ?>
							<a href="<?php echo esc_url(home_url('/admin-portal/')); ?>"
								class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-hover transition-all shadow-md active:scale-95">
								<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
								</svg>
								<span class="truncate">لوحة إدارة المتجر</span>
							</a>
					<?php endif; ?>
					<a href="<?php echo esc_url(wc_logout_url()); ?>"
						class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl text-xs font-bold text-slate-200 hover:text-white bg-white/10 hover:bg-white/15 border border-white/15 transition-all active:scale-95">
						<svg class="w-3.5 h-3.5 rotate-180 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17 16l4-4m0 0l-4-4m4 4H7" />
						</svg>
						<span class="truncate">تسجيل الخروج</span>
					</a>
				</div>
			</div>

		</div>
	</div>

	<!-- =========================================================================
		 2. SUMMARY CARDS
		 - Desktop: Single Row of 3 Equal Cards (Image 1)
		 - Mobile: Row 1 (2 Cards) + Row 2 (1 Full-Width Support Card) (Image 2)
		 ========================================================================= -->

	<!-- DESKTOP SUMMARY CARDS (hidden md:grid md:grid-cols-3 gap-6 mt-6) -->
	<div class="hidden md:grid md:grid-cols-3 gap-6 mt-6">

		<!-- Card 1: Total Orders -->
		<div
			class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-rose-200 transition-all flex flex-col justify-between min-h-[165px] group">
			<div class="flex items-start justify-between gap-4 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 block mb-1">
						إجمالي الطلبات
					</span>
					<div class="text-3xl font-black text-slate-900 group-hover:text-primary transition-colors">
						<?php echo esc_html($order_count); ?>
						<span class="text-xs font-normal text-slate-400 mr-1">طلب</span>
					</div>
				</div>
				<div
					class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 group-hover:bg-primary group-hover:text-white transition-all shadow-2xs">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
				<span class="text-slate-400 flex items-center gap-1.5">
					<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 6h16M4 10h16M4 14h16M4 18h16" />
					</svg>
					<span>الطلبات المسجلة بالحساب</span>
				</span>
				<a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>"
					class="font-bold text-primary hover:text-[#b90323] flex items-center gap-1 transition-colors">
					<span>عرض الطلبات</span>
					<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
		</div>

		<!-- Card 2: Account Details -->
		<div
			class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between min-h-[165px] group">
			<div class="flex items-start justify-between gap-4 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 block mb-1">
						تفاصيل الحساب
					</span>
					<div
						class="text-base font-bold text-slate-900 group-hover:text-blue-700 transition-colors line-clamp-1">
						<?php echo esc_html($current_user->display_name); ?>
					</div>
					<p class="text-xs text-slate-400 mt-1 line-clamp-1 m-0">
						<?php echo esc_html($current_user->user_email); ?>
					</p>
				</div>
				<div
					class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-2xs">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
				<span class="text-slate-400 flex items-center gap-1.5">
					<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
					</svg>
					<span>البيانات الشخصية</span>
				</span>
				<a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>"
					class="font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition-colors">
					<span>تعديل الحساب</span>
					<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
		</div>

		<!-- Card 3: Direct Technical Support -->
		<div
			class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all flex flex-col justify-between min-h-[165px] group">
			<div class="flex items-start justify-between gap-4 mb-3">
				<div>
					<span class="text-xs font-bold text-slate-500 block mb-1">
						الدعم الفني السريع
					</span>
					<div
						class="text-base font-bold text-slate-900 group-hover:text-[#25D366] transition-colors line-clamp-1">
						استشارات المعدات والملحقات
					</div>
					<p class="text-xs text-slate-400 mt-1 m-0">
						فريق <?php echo esc_html($dash_store_name); ?> متاح لمساعدتك دائماً
					</p>
				</div>
				<div
					class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-[#25D366] flex items-center justify-center flex-shrink-0 group-hover:bg-[#25D366] group-hover:text-white transition-all shadow-2xs">
					<svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
						<path
							d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
				<span class="text-slate-400 flex items-center gap-1.5">
					<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
					</svg>
					<span>واتساب مباشر</span>
				</span>
				<a href="https://wa.me/<?php echo esc_attr($dash_wa_phone); ?>?text=<?php echo rawurlencode(sprintf('مرحباً %s، أرغب بالاستفسار عن طلب أو تفاصيل معدة.', $dash_store_name)); ?>"
					target="_blank" rel="noopener noreferrer"
					class="font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
					<span>تواصل معنا</span>
					<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
		</div>

	</div>

	<!-- MOBILE SUMMARY CARDS (< md: Strictly Reference Image 2) -->
	<div class="block md:hidden space-y-3 mt-4">

		<!-- First Row: Two Cards Side by Side -->
		<div class="grid grid-cols-2 gap-3">

			<!-- Mobile Card 1: Total Orders -->
			<div
				class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col justify-between group">
				<div class="flex items-start justify-between gap-2 mb-2">
					<div>
						<span class="text-[11px] font-bold text-slate-500 block mb-0.5">
							إجمالي الطلبات
						</span>
						<div class="text-2xl font-black text-slate-900 group-hover:text-primary transition-colors">
							<?php echo esc_html($order_count); ?>
							<span class="text-[11px] font-normal text-slate-400 mr-0.5">طلب</span>
						</div>
					</div>
					<div
						class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
					</div>
				</div>
				<div class="pt-2.5 border-t border-slate-100 flex items-center justify-start text-[11px]">
					<a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>"
						class="font-bold text-primary flex items-center gap-1">
						<span>عرض الطلبات</span>
						<svg class="w-3 h-3 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
						</svg>
					</a>
				</div>
			</div>

			<!-- Mobile Card 2: Account Details -->
			<div
				class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col justify-between group">
				<div class="flex items-start justify-between gap-2 mb-2">
					<div class="min-w-0 flex-1">
						<span class="text-[11px] font-bold text-slate-500 block mb-0.5">
							تفاصيل الحساب
						</span>
						<div class="text-xs sm:text-sm font-bold text-slate-900 truncate">
							<?php echo esc_html($current_user->display_name); ?>
						</div>
						<p class="text-[10px] text-slate-400 mt-0.5 truncate m-0">
							<?php echo esc_html($current_user->user_email); ?>
						</p>
					</div>
					<div
						class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
					</div>
				</div>
				<div class="pt-2.5 border-t border-slate-100 flex items-center justify-start text-[11px]">
					<a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>"
						class="font-bold text-blue-600 flex items-center gap-1">
						<span>تعديل الحساب</span>
						<svg class="w-3 h-3 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
						</svg>
					</a>
				</div>
			</div>

		</div>

		<!-- Second Row: One Full-Width Customer Support Card -->
		<div
			class="w-full bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between group">
			<div class="flex items-start justify-between gap-3">
				<div class="space-y-1 min-w-0 flex-1">
					<span class="text-[11px] font-bold text-slate-500 block">
						الدعم الفني السريع
					</span>
					<h3 class="text-sm sm:text-base font-extrabold text-slate-900 m-0">
						استشارات المعدات والمقاسات
					</h3>
					<p class="text-xs text-slate-400 m-0">
						فريق <?php echo esc_html($dash_store_name); ?> متاح لمساعدتك دائماً
					</p>
				</div>
				<div
					class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-[#25D366] flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
						<path
							d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
					</svg>
				</div>
			</div>
			<div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-start text-xs">
				<a href="https://wa.me/<?php echo esc_attr($dash_wa_phone); ?>?text=<?php echo rawurlencode(sprintf('مرحباً %s، أرغب بالاستفسار عن طلب أو تفاصيل معدة.', $dash_store_name)); ?>"
					target="_blank" rel="noopener noreferrer"
					class="font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
					<span>تواصل معنا</span>
					<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
		</div>

	</div>

	<!-- =========================================================================
		 3. RECENT ORDERS SECTION
		 - Desktop: Clean Table Layout (Image 1)
		 - Mobile: Compact Responsive Cards (Image 2)
		 ========================================================================= -->
	<?php
	$recent_orders = wc_get_orders(array(
		'customer' => $current_user->ID,
		'limit' => 3,
	));
	?>
	<div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm mt-6">

		<!-- Section Header -->
		<div class="flex items-center justify-between pb-4 border-b border-slate-100">
			<div class="flex items-center gap-2.5">
				<span
					class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
				</span>
				<div>
					<h3 class="text-base sm:text-lg font-black text-slate-900 m-0 leading-tight">
						آخر الطلبات
					</h3>
					<p class="text-xs text-slate-400 m-0 mt-0.5 hidden sm:block">
						نظرة سريعة على أحدث طلباتك من أدوات ومعدات <?php echo esc_html($dash_store_name); ?>
					</p>
				</div>
			</div>

			<a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>"
				class="text-xs sm:text-sm font-bold text-primary hover:text-[#b90323] flex items-center gap-1 transition-colors select-none">
				<span>عرض الكل</span>
				<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg>
			</a>
		</div>

		<?php if (!empty($recent_orders)): ?>

				<!-- DESKTOP TABLE VIEW (hidden md:block: Strictly Reference Image 1) -->
				<div class="hidden md:block mt-4 overflow-x-auto">
					<table class="w-full text-right border-collapse">
						<thead>
							<tr class="bg-slate-50/90 text-slate-500 text-xs font-bold rounded-xl overflow-hidden">
								<th class="py-3 px-4 text-center rounded-r-xl w-16">#</th>
								<th class="py-3 px-4">
									<span class="inline-flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
										</svg>
										<span>التاريخ</span>
									</span>
								</th>
								<th class="py-3 px-4">
									<span class="inline-flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
										</svg>
										<span>المجموع</span>
									</span>
								</th>
								<th class="py-3 px-4">
									<span class="inline-flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
										</svg>
										<span>عدد المنتجات</span>
									</span>
								</th>
								<th class="py-3 px-4">
									<span class="inline-flex items-center gap-1.5">
										<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
										</svg>
										<span>حالة الطلب</span>
									</span>
								</th>
								<th class="py-3 px-4 text-center rounded-l-xl">الإجراءات</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100">
							<?php foreach ($recent_orders as $order):
								$status = $order->get_status();
								$status_name = wc_get_order_status_name($status);
								$date_str = wc_format_datetime($order->get_date_created());
								$pay_title = $order->get_payment_method_title() ?: 'الدفع عند الاستلام (COD)';

								// Unified Status Badge
								$badge_data  = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $status ) : array( 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400', 'label' => $status_name );
								$badge_class = $badge_data['class'];
								$dot_class   = $badge_data['dot'];
								$status_name = $badge_data['label'];
								?>
									<tr class="hover:bg-slate-50/50 transition-colors">
										<!-- Order Number -->
										<td class="py-4 px-4 text-center font-bold text-slate-900 text-sm">
											#<?php echo esc_html($order->get_order_number()); ?>
										</td>

										<!-- Date & Payment Method -->
										<td class="py-4 px-4">
											<div class="text-xs font-bold text-slate-800">
												<?php echo esc_html($date_str); ?>
											</div>
											<div class="text-[11px] text-slate-400 mt-0.5">
												<?php echo esc_html($pay_title); ?>
											</div>
										</td>

										<!-- Total -->
										<td class="py-4 px-4 font-black text-slate-900 text-sm">
											<?php echo wp_kses_post($order->get_formatted_order_total()); ?>
										</td>

										<!-- Product Count -->
										<td class="py-4 px-4 text-xs font-medium text-slate-600">
											<?php echo esc_html($order->get_item_count()); ?> منتج
										</td>

										<!-- Order Status Badge with Dot Indicator -->
										<td class="py-4 px-4">
											<span
												class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo esc_attr($badge_class); ?>">
												<span class="w-1.5 h-1.5 rounded-full <?php echo esc_attr($dot_class); ?>"></span>
												<span><?php echo esc_html($status_name); ?></span>
											</span>
										</td>

										<!-- Action Button -->
										<td class="py-4 px-4 text-center">
											<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
												class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-primary transition-colors shadow-2xs select-none">
												<span>التفاصيل</span>
												<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor"
													viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M9 5l7 7-7 7" />
												</svg>
											</a>
										</td>
									</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- MOBILE CARDS VIEW (< md: Strictly Reference Image 2) -->
				<div class="block md:hidden divide-y divide-slate-100 mt-2">
					<?php foreach ($recent_orders as $order):
						$status = $order->get_status();
						$status_name = wc_get_order_status_name($status);
						$date_str = wc_format_datetime($order->get_date_created());

						// Unified Status Badge
						$badge_data  = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $status ) : array( 'class' => 'bg-slate-100 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400', 'label' => $status_name );
						$badge_class = $badge_data['class'];
						$dot_class   = $badge_data['dot'];
						$status_name = $badge_data['label'];
						?>
							<div class="py-3.5 first:pt-2 last:pb-0">
								<div
									class="bg-slate-50/70 border border-slate-200/70 rounded-2xl p-3.5 flex items-center justify-between gap-2.5">

									<!-- Order Number on Right -->
									<div class="text-base font-black text-slate-900 flex-shrink-0">
										#<?php echo esc_html($order->get_order_number()); ?>
									</div>

									<!-- Date Column -->
									<div class="min-w-0">
										<div class="text-[10px] text-slate-400 font-bold flex items-center gap-1 mb-0.5">
											<svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
											</svg>
											<span>التاريخ</span>
										</div>
										<div class="text-xs font-bold text-slate-800 truncate">
											<?php echo esc_html($date_str); ?>
										</div>
									</div>

									<!-- Total Column -->
									<div class="min-w-0">
										<div class="text-[10px] text-slate-400 font-bold flex items-center gap-1 mb-0.5">
											<svg class="w-3 h-3 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
											</svg>
											<span>المجموع</span>
										</div>
										<div class="text-xs font-black text-slate-900 truncate">
											<?php echo wp_kses_post($order->get_formatted_order_total()); ?>
										</div>
									</div>

									<!-- Status Badge & Details Button on Left -->
									<div class="flex flex-col items-end gap-1.5 flex-shrink-0">
										<span
											class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?php echo esc_attr($badge_class); ?>">
											<span class="w-1.5 h-1.5 rounded-full <?php echo esc_attr($dot_class); ?>"></span>
											<span><?php echo esc_html($status_name); ?></span>
										</span>
										<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
											class="px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-primary transition-colors text-center shadow-2xs select-none">
											التفاصيل
										</a>
									</div>

								</div>
							</div>
					<?php endforeach; ?>
				</div>

		<?php else: ?>
				<!-- Clean Empty Orders State -->
				<div class="text-center py-8">
					<div
						class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center mx-auto mb-3 shadow-2xs">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
						</svg>
					</div>
					<h4 class="text-sm font-bold text-slate-800 m-0">لا توجد طلبات مسجلة بعد</h4>
					<p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto m-0">
						ابدأ بتصفح معدات قص البورسلين والسيراميك ومستلزمات التركيب الاحترافية.
					</p>
					<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
						class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-hover transition-colors shadow-sm select-none">
						<span>تسوق الآن</span>
						<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M14 5l7 7m0 0l-7 7m7-7H3" />
						</svg>
					</a>
				</div>
		<?php endif; ?>

	</div>

	<!-- =========================================================================
		 4. QUICK SHOPPING CATEGORIES
		 - Desktop: Single Row of 4 Horizontal Cards (Image 1)
		 - Mobile: 2-Column Grid of Balanced Vertical Cards (Image 2)
		 ========================================================================= -->
	<?php
	// Curated high-demand Ashrafco categories matching Reference Image 1 & 2
	$theme_uri = get_stylesheet_directory_uri();
	$theme_path = get_stylesheet_directory();
	$shop_link = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

	$categories_catalog = array(
		array(
			'slug' => 'power-tools',
			'name' => 'أدوات كهربائية',
			'subtitle' => 'دريل ومفكات وغيرها',
			'mobile_title' => 'ماكينات قص يدوية',
			'mobile_subtitle' => 'قطع دقيق ومتعدد',
			'image' => $theme_uri . '/assets/images/categories/cat-power-tools.png',
			'search_query' => 'كهربائية',
		),
		array(
			'slug' => 'toolboxes',
			'name' => 'صناديق العدد',
			'subtitle' => 'منظمات وتخزين',
			'mobile_title' => 'موازين ليزر',
			'mobile_subtitle' => 'تحديد دقيق',
			'image' => $theme_uri . '/assets/images/categories/cat-toolboxes.png',
			'search_query' => 'صناديق',
		),
		array(
			'slug' => 'cutting-discs',
			'name' => 'أقراص صلب وصنفرة',
			'subtitle' => 'في وتلميع وتشطيب',
			'mobile_title' => 'أقراص قطع وصنفرة',
			'mobile_subtitle' => 'للحدادة والبناء',
			'image' => file_exists($theme_path . '/assets/images/Products/photo_26_2026-10-06_16-57-21.jpg')
				? $theme_uri . '/assets/images/Products/photo_26_2026-10-06_16-57-21.jpg'
				: $theme_uri . '/assets/images/categories/cat-manual-tools.png',
			'search_query' => 'أقراص',
		),
		array(
			'slug' => 'welding-cutting',
			'name' => 'أدوات لحام وقص',
			'subtitle' => 'في وملحقاتها',
			'mobile_title' => 'أنظمة تسوية البلاط',
			'mobile_subtitle' => 'ضبط المناسيب',
			'image' => file_exists($theme_path . '/assets/images/Products/photo_20_2026-10-06_16-57-21.jpg')
				? $theme_uri . '/assets/images/Products/photo_20_2026-10-06_16-57-21.jpg'
				: $theme_uri . '/assets/images/categories/cat-manual-tools.png',
			'search_query' => 'قص',
		),
	);

	// Resolve actual WooCommerce category links dynamically
	$resolved_categories = array();
	foreach ($categories_catalog as $cat_item) {
		$term = get_term_by('slug', $cat_item['slug'], 'product_cat');
		if (!$term || is_wp_error($term)) {
			$term = get_term_by('name', $cat_item['name'], 'product_cat');
		}

		if ($term && !is_wp_error($term)) {
			$term_link = get_term_link($term);
			$thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
			$img_src = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'medium') : $cat_item['image'];
		} else {
			$term_link = add_query_arg(array('s' => $cat_item['search_query'], 'post_type' => 'product'), home_url('/'));
			$img_src = $cat_item['image'];
		}

		$resolved_categories[] = array(
			'name' => $cat_item['name'],
			'subtitle' => $cat_item['subtitle'],
			'mobile_title' => $cat_item['mobile_title'],
			'mobile_subtitle' => $cat_item['mobile_subtitle'],
			'url' => $term_link,
			'image' => $img_src,
		);
	}
	?>

	<div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-sm mt-6">

		<!-- Section Header -->
		<div class="flex items-center justify-between pb-4 border-b border-slate-100">
			<div class="flex items-center gap-2.5">
				<span
					class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
					</svg>
				</span>
				<h3 class="text-sm sm:text-base font-black text-slate-900 m-0">
					<span class="hidden sm:inline">أقسام سريعة للتسوق الأكثر طلباً في مصر</span>
					<span class="inline sm:hidden">أقسام سريعة للتسوق</span>
				</h3>
			</div>

			<a href="<?php echo esc_url($shop_link); ?>"
				class="text-xs sm:text-sm font-bold text-primary hover:text-[#b90323] flex items-center gap-1 transition-colors select-none">
				<span>عرض الكل</span>
				<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
				</svg>
			</a>
		</div>

		<!-- DESKTOP QUICK CATEGORIES (hidden md:grid md:grid-cols-4 gap-3.5 mt-4: Strictly Reference Image 1) -->
		<div class="hidden md:grid md:grid-cols-4 gap-3.5 mt-4">
			<?php foreach ($resolved_categories as $rcat): ?>
					<a href="<?php echo esc_url($rcat['url']); ?>"
						class="flex items-center justify-between p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:border-[var(--color-primary)]/40 hover:shadow-md transition-all group no-underline">
						<!-- Right: Category Title & Subtitle with Chevron -->
						<div class="min-w-0 flex-1 pl-2">
							<h4
								class="text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors m-0 truncate">
								<?php echo esc_html($rcat['name']); ?>
							</h4>
							<span
								class="text-xs text-slate-400 mt-1 flex items-center gap-1 group-hover:text-slate-600 transition-colors truncate">
								<span><?php echo esc_html($rcat['subtitle']); ?></span>
								<svg class="w-3 h-3 rotate-180 flex-shrink-0 text-slate-400 group-hover:text-primary transition-colors"
									fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
								</svg>
							</span>
						</div>

						<!-- Left: Category Product Image -->
						<div class="w-14 h-14 sm:w-16 sm:h-16 flex items-center justify-center flex-shrink-0 overflow-hidden">
							<img src="<?php echo esc_url($rcat['image']); ?>" alt="<?php echo esc_attr($rcat['name']); ?>"
								class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200"
								loading="lazy" />
						</div>
					</a>
			<?php endforeach; ?>
		</div>

		<!-- MOBILE QUICK CATEGORIES (< md: Dedicated 2-Column Grid: Strictly Reference Image 2) -->
		<div class="grid grid-cols-2 gap-3 md:hidden mt-4">
			<?php foreach ($resolved_categories as $rcat): ?>
					<a href="<?php echo esc_url($rcat['url']); ?>"
						class="flex flex-col items-center justify-center p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:border-[var(--color-primary)]/40 transition-all text-center group no-underline">
						<!-- Top: Image Centered -->
						<div class="w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center mb-2 overflow-hidden">
							<img src="<?php echo esc_url($rcat['image']); ?>"
								alt="<?php echo esc_attr($rcat['mobile_title']); ?>"
								class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200"
								loading="lazy" />
						</div>

						<!-- Bottom: Title and Subtitle -->
						<h4
							class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-primary transition-colors m-0 truncate w-full">
							<?php echo esc_html($rcat['mobile_title']); ?>
						</h4>
						<span class="text-[10px] sm:text-xs text-slate-400 mt-0.5 block truncate w-full">
							<?php echo esc_html($rcat['mobile_subtitle']); ?>
						</span>
					</a>
			<?php endforeach; ?>
		</div>

	</div>

	<?php
	/**
	 * Standard WooCommerce dashboard action hooks.
	 */
	do_action('woocommerce_account_dashboard');
	do_action('woocommerce_before_my_account');
	do_action('woocommerce_after_my_account');
	?>

</div>