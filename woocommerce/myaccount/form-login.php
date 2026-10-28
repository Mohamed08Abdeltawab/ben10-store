<?php
/**
 * Modern Login & Registration Form Override for Ashrafco Child Theme
 * Matches exact Ashrafco Design System for Desktop (2-panel) & Mobile (Compact banner)
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 9.2.0
 */

defined('ABSPATH') || exit;

$registration_enabled = 'yes' === get_option('woocommerce_enable_myaccount_registration');
$is_register_active = isset($_POST['register']) || (isset($_GET['action']) && 'register' === $_GET['action']);

$eagle_logo_url = function_exists( 'ashrafco_get_store_logo_url' ) ? ashrafco_get_store_logo_url() : ( get_stylesheet_directory_uri() . '/assets/images/eagle-logo.webp' );
$store_name_ar  = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو';
$header_bg_url = get_stylesheet_directory_uri() . '/assets/images/header-bg.jpg';
$mobile_banner_url = get_stylesheet_directory_uri() . '/assets/images/hero/hero-toolkits.webp';
?>

<style>
/* Guaranteed styling for login & registration buttons against WooCommerce overrides */
.ashrafco-auth-wrapper .woocommerce-form-login__submit,
.ashrafco-auth-wrapper .woocommerce-form-register__submit {
	background-color: var(--color-primary, #d90429) !important;
	color: #ffffff !important;
	border: none !important;
	box-shadow: 0 4px 14px rgba(217, 4, 41, 0.25) !important;
}
.ashrafco-auth-wrapper .woocommerce-form-login__submit:hover,
.ashrafco-auth-wrapper .woocommerce-form-register__submit:hover {
	background-color: var(--color-primary-hover, #b90323) !important;
	color: #ffffff !important;
}
</style>

<div class="ashrafco-auth-wrapper w-full max-w-[1040px] mx-auto px-3 sm:px-6 font-sans py-2 sm:py-6 pb-20 lg:pb-8"
	dir="rtl">

	<!-- Breadcrumb Trail (Reference 1 & 2) -->
	<nav class="mb-3.5 sm:mb-6 text-xs sm:text-sm text-slate-400 flex items-center gap-2 select-none"
		aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
		<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-[var(--color-primary)] transition-colors">
			<?php esc_html_e('الرئيسية', 'ashrafco'); ?>
		</a>
		<span class="text-slate-300">/</span>
		<span id="ashrafco-breadcrumb-current" class="text-slate-700 font-semibold truncate">
			<?php echo $is_register_active ? esc_html__('إنشاء حساب جديد', 'ashrafco') : esc_html__('تسجيل الدخول', 'ashrafco'); ?>
		</span>
	</nav>

	<!-- Mobile Compact Branding Banner (Hidden on mobile per specification) -->
	<div class="ashrafco-mobile-banner hidden w-full rounded-2xl overflow-hidden shadow-sm relative mb-5 border border-slate-900/10 h-[115px] sm:h-[130px] select-none"
		style="background: #000000 url('<?php echo esc_url($mobile_banner_url); ?>') right center / cover no-repeat;">
		<!-- Solid black overlay covering the left half to cleanly mask the banner's background text -->
		<div class="absolute inset-0 pointer-events-none"
			style="background: linear-gradient(to right, #050505 0%, #050505 48%, rgba(5,5,5,0.92) 58%, rgba(5,5,5,0) 78%);">
		</div>

		<!-- Banner Content (Left side in LTR / End in RTL, matching Reference 1 layout) -->
		<div
			class="absolute left-0 top-0 bottom-0 z-10 flex flex-col justify-center items-center text-center px-4 sm:px-6 w-[52%]">
			<img src="<?php echo esc_url($eagle_logo_url); ?>" alt="<?php echo esc_attr($store_name_ar); ?>"
				class="ashrafco-mobile-logo drop-shadow mb-1 select-none pointer-events-none w-[38px] h-[32px] max-w-[38px] max-h-[32px] object-contain" />
			<h2 class="text-lg sm:text-xl font-black text-white leading-tight tracking-wide m-0">
				<?php echo esc_html($store_name_ar); ?>
			</h2>
			<div class="w-7 h-0.5 bg-primary rounded-full mx-auto my-1"></div>
			<p class="text-[10px] sm:text-xs text-slate-300 font-medium m-0 leading-tight truncate">
				<?php esc_html_e('أدوات ومعدات في متناول يدك', 'ashrafco'); ?>
			</p>
		</div>
	</div>

	<!-- Main Auth Card Container (Reference 2: Centered 2-panel on desktop, clean single-column on mobile) -->
	<div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100/90 overflow-hidden">

		<div class="grid grid-cols-1 lg:grid-cols-12 w-full">

			<!-- ======================================================== -->
			<!-- RIGHT PANEL: Desktop Dark Branding Panel (RTL Col 1: 5 cols) -->
			<!-- ======================================================== -->
			<div class="lg:col-span-5 hidden lg:flex flex-col items-center justify-center p-8 xl:p-12 text-center relative overflow-hidden select-none min-h-[580px]"
				style="background: #000000 url('<?php echo esc_url($header_bg_url); ?>') center center / cover no-repeat;">
				<!-- Dark vignette overlay for optimal typography contrast -->
				<div
					class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-black/80 pointer-events-none">
				</div>

				<div class="relative z-10 w-full flex flex-col items-center">
					<!-- Existing Store Brand Logo -->
					<img src="<?php echo esc_url($eagle_logo_url); ?>" alt="<?php echo esc_attr($store_name_ar); ?>"
						class="w-28 xl:w-32 max-w-[130px] h-auto object-contain drop-shadow-2xl mx-auto mb-2 select-none pointer-events-none" />

					<!-- Arabic Brand Typography -->
					<h2 class="text-3xl xl:text-4xl font-black text-white tracking-wide m-0 mb-1">
						<?php echo esc_html($store_name_ar); ?>
					</h2>

					<!-- Desktop Branding: Login View -->
					<div id="desktop-brand-login"
						class="w-full transition-opacity duration-300 <?php echo $is_register_active ? 'hidden' : 'block'; ?>">
						<h3 class="text-xl xl:text-2xl font-bold text-white mt-6 mb-2">
							<?php esc_html_e('أهلاً بعودتك', 'ashrafco'); ?>
						</h3>
						<div class="w-12 h-[3px] bg-primary rounded-full mx-auto mb-3.5"></div>
						<p class="text-xs xl:text-sm text-slate-300 font-medium max-w-[220px] mx-auto leading-relaxed">
							<?php esc_html_e('سجّل دخولك وتابع تسوّق أدواتك المفضلة', 'ashrafco'); ?>
						</p>
					</div>

					<!-- Desktop Branding: Registration View -->
					<div id="desktop-brand-register"
						class="w-full transition-opacity duration-300 <?php echo $is_register_active ? 'block' : 'hidden'; ?>">
						<h3 class="text-xl xl:text-2xl font-bold text-white mt-6 mb-2">
							<?php esc_html_e('انضم إلينا اليوم', 'ashrafco'); ?>
						</h3>
						<div class="w-12 h-[3px] bg-primary rounded-full mx-auto mb-3.5"></div>
						<p class="text-xs xl:text-sm text-slate-300 font-medium max-w-[240px] mx-auto leading-relaxed">
							<?php esc_html_e('أنشئ حسابك وتسوّق أفضل معدات وأدوات المحترفين', 'ashrafco'); ?>
						</p>
					</div>
				</div>
			</div>

			<!-- ======================================================== -->
			<!-- LEFT PANEL: Auth Form Panel (RTL Col 2: 7 cols)         -->
			<!-- ======================================================== -->
			<div class="lg:col-span-7 p-6 sm:p-10 xl:p-12 flex flex-col justify-center bg-white min-w-0">

				<!-- Form Header -->
				<div class="mb-6 text-center sm:text-right">
					<h1 id="auth-title"
						class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-snug m-0">
						<?php echo $is_register_active ? esc_html__('إنشاء حساب جديد', 'ashrafco') : esc_html__('تسجيل الدخول إلى حسابك', 'ashrafco'); ?>
					</h1>
					<p id="auth-subtitle" class="text-xs sm:text-sm text-slate-500 font-medium mt-1.5 m-0">
						<?php echo $is_register_active ? esc_html__('سجّل بياناتك للاستمتاع بتجربة تسوق متكاملة وسريعة', 'ashrafco') : esc_html__('مرحباً بك مجدداً، سجّل دخولك للمتابعة في التسوق', 'ashrafco'); ?>
					</p>
				</div>

				<?php if ($registration_enabled): ?>
					<!-- Dynamic Segmented Tab Switcher (Reference 2: Pill button switcher) -->
					<div class="bg-slate-100 p-1.5 rounded-2xl flex items-center mb-6 relative select-none" role="tablist">
						<!-- Login Tab -->
						<button type="button" id="tab-btn-login" role="tab" aria-controls="panel-login"
							aria-selected="<?php echo $is_register_active ? 'false' : 'true'; ?>"
							class="ashrafco-tab-btn flex-1 py-2.5 px-3 sm:px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 text-center flex items-center justify-center gap-2 cursor-pointer <?php echo $is_register_active ? 'text-slate-600 hover:text-slate-900 bg-transparent' : 'bg-slate-900 text-white shadow-sm'; ?>">
							<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
							</svg>
							<span><?php esc_html_e('تسجيل الدخول', 'ashrafco'); ?></span>
						</button>

						<!-- Register Tab -->
						<button type="button" id="tab-btn-register" role="tab" aria-controls="panel-register"
							aria-selected="<?php echo $is_register_active ? 'true' : 'false'; ?>"
							class="ashrafco-tab-btn flex-1 py-2.5 px-3 sm:px-4 rounded-xl text-xs sm:text-sm font-bold transition-all duration-200 text-center flex items-center justify-center gap-2 cursor-pointer <?php echo $is_register_active ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 bg-transparent'; ?>">
							<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
							</svg>
							<span><?php esc_html_e('إنشاء حساب جديد', 'ashrafco'); ?></span>
						</button>
					</div>
				<?php endif; ?>

				<!-- WooCommerce Messages & Validation Notices Hook -->
				<?php
				/**
				 * Hook: woocommerce_before_customer_login_form.
				 *
				 * @hooked woocommerce_output_all_notices - 10
				 */
				do_action('woocommerce_before_customer_login_form');
				?>

				<!-- ========================================== -->
				<!-- 1. LOGIN FORM PANEL                        -->
				<!-- ========================================== -->
				<div id="panel-login" role="tabpanel" aria-labelledby="tab-btn-login"
					class="ashrafco-auth-panel transition-opacity duration-200 <?php echo $is_register_active ? 'hidden' : 'block'; ?>">
					<form class="woocommerce-form woocommerce-form-login login space-y-4" method="post">

						<?php do_action('woocommerce_login_form_start'); ?>

						<!-- Username / Email / Phone Input -->
						<div>
							<label for="username"
								class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
								<?php esc_html_e('البريد الإلكتروني أو رقم الهاتف', 'ashrafco'); ?>
								<span class="text-rose-500 font-black">*</span>
							</label>
							<div class="relative">
								<input type="text"
									class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
									name="username" id="username" autocomplete="username"
									placeholder="<?php esc_attr_e('البريد الإلكتروني أو رقم الهاتف', 'ashrafco'); ?>"
									value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
									required />
								<span
									class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
											d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
									</svg>
								</span>
							</div>
						</div>

						<!-- Password Input with Reveal Toggle -->
						<div>
							<label for="password"
								class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
								<?php esc_html_e('كلمة المرور', 'ashrafco'); ?>
								<span class="text-rose-500 font-black">*</span>
							</label>
							<div class="relative">
								<input type="password"
									class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
									name="password" id="password" autocomplete="current-password"
									placeholder="<?php esc_attr_e('كلمة المرور', 'ashrafco'); ?>" required />
								<button type="button"
									class="ashrafco-pwd-toggle absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none transition-colors cursor-pointer"
									data-target="password"
									aria-label="<?php esc_attr_e('إظهار أو إخفاء كلمة المرور', 'ashrafco'); ?>">
									<svg class="icon-eye w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
											d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
											d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
									</svg>
									<svg class="icon-eye-off w-5 h-5 hidden" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
											d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
									</svg>
								</button>
							</div>
						</div>

						<?php do_action('woocommerce_login_form'); ?>

						<!-- Remember Me Checkbox -->
						<div class="flex items-center justify-start pt-1">
							<label class="inline-flex items-center gap-2 cursor-pointer select-none">
								<input type="checkbox" name="rememberme" id="rememberme" value="forever"
									class="w-4 h-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)] accent-[var(--color-primary)] cursor-pointer" />
								<span
									class="text-xs sm:text-sm font-semibold text-slate-700"><?php esc_html_e('تذكرني', 'ashrafco'); ?></span>
							</label>
						</div>

						<!-- Login Submit Button (Brand Red with Left Arrow) -->
						<div class="pt-1">
							<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
							<button type="submit"
								class="woocommerce-button woocommerce-form-login__submit btn-primary w-full py-3.5 px-6 rounded-xl font-bold text-white bg-primary hover:bg-[#b90323] active:scale-[0.99] transition duration-200 shadow-md shadow-red-600/20 flex items-center justify-center gap-2.5 cursor-pointer text-sm sm:text-base border-0"
								name="login" 
								value="<?php esc_attr_e('تسجيل الدخول', 'ashrafco'); ?>">
								<span class="text-base sm:text-lg leading-none font-bold">←</span>
								<span><?php esc_html_e('تسجيل الدخول', 'ashrafco'); ?></span>
							</button>
						</div>

						<!-- Social Login Section (Google & Facebook) -->
						<div class="ashrafco-social-login-section my-4">
							<!-- Visual Divider -->
							<div class="relative flex items-center justify-center mb-3">
								<div class="border-t border-slate-200 w-full absolute inset-0 my-auto"></div>
								<span class="relative bg-white px-3 text-xs sm:text-sm font-semibold text-slate-500 select-none">
									<?php esc_html_e('أو المتابعة باستخدام', 'ashrafco'); ?>
								</span>
							</div>

							<!-- Social Buttons (Stacked, full-width responsive) -->
							<div class="flex flex-col gap-2.5">
								<!-- Google Login Button -->
								<a href="<?php echo esc_url(function_exists('ashrafco_get_social_login_url') ? ashrafco_get_social_login_url('google', 'login') : '#'); ?>"
									class="ashrafco-social-btn ashrafco-btn-google w-full h-12 px-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:scale-[0.99] transition-all duration-200 shadow-sm flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 group select-none cursor-pointer"
									aria-label="<?php esc_attr_e('المتابعة باستخدام Google', 'ashrafco'); ?>">
									<!-- Official Multicolor Google SVG Icon -->
									<svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105" viewBox="0 0 24 24" aria-hidden="true">
										<path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
										<path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
										<path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
										<path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
									</svg>
									<span><?php esc_html_e('المتابعة باستخدام Google', 'ashrafco'); ?></span>
								</a>

								<!-- Facebook Login Button -->
								<a href="<?php echo esc_url(function_exists('ashrafco_get_social_login_url') ? ashrafco_get_social_login_url('facebook', 'login') : '#'); ?>"
									class="ashrafco-social-btn ashrafco-btn-facebook w-full h-12 px-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:scale-[0.99] transition-all duration-200 shadow-sm flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 group select-none cursor-pointer"
									aria-label="<?php esc_attr_e('المتابعة باستخدام Facebook', 'ashrafco'); ?>">
									<!-- Official Facebook Blue SVG Icon -->
									<svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="#1877F2" aria-hidden="true">
										<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
									</svg>
									<span><?php esc_html_e('المتابعة باستخدام Facebook', 'ashrafco'); ?></span>
								</a>
							</div>
						</div>

						<!-- Centered Password Recovery Link -->
						<div class="text-center pt-1">
							<a href="<?php echo esc_url(wp_lostpassword_url()); ?>"
								class="text-xs sm:text-sm font-bold text-slate-500 hover:text-primary hover:underline transition-colors">
								<?php esc_html_e('نسيت كلمة المرور؟', 'ashrafco'); ?>
							</a>
						</div>

						<?php if ($registration_enabled): ?>
							<!-- Subtle Divider & Switch to Register -->
							<div class="relative flex items-center justify-center my-5">
								<div class="border-t border-slate-200 w-full absolute inset-0 my-auto"></div>
								<div
									class="relative bg-white px-3 text-xs sm:text-sm text-slate-500 flex items-center gap-1.5 select-none">
									<span><?php esc_html_e('ليس لديك حساب؟', 'ashrafco'); ?></span>
									<button type="button"
										class="ashrafco-switch-to-register hover:underline font-bold text-primary cursor-pointer">
										<?php esc_html_e('إنشاء حساب جديد', 'ashrafco'); ?>
									</button>
								</div>
							</div>
						<?php endif; ?>

						<!-- Bottom Security & Shipping Micro-Badges (Reference 1 & 2) -->
						<div
							class="pt-2 flex items-center justify-center gap-4 sm:gap-6 text-xs text-slate-500 font-medium select-none">
							<span class="flex items-center gap-1.5">
								<svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
								</svg>
								<span><?php esc_html_e('اتصال آمن ومشفر', 'ashrafco'); ?></span>
							</span>
							<span class="h-4 w-px bg-slate-200"></span>
							<span class="flex items-center gap-1.5">
								<svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
								</svg>
								<span><?php esc_html_e('شحن ومتابعة فورية', 'ashrafco'); ?></span>
							</span>
						</div>

						<?php do_action('woocommerce_login_form_end'); ?>

					</form>
				</div>

				<?php if ($registration_enabled): ?>
					<!-- ========================================== -->
					<!-- 2. REGISTER FORM PANEL                     -->
					<!-- ========================================== -->
					<div id="panel-register" role="tabpanel" aria-labelledby="tab-btn-register"
						class="ashrafco-auth-panel transition-opacity duration-200 <?php echo $is_register_active ? 'block' : 'hidden'; ?>">
						<form method="post" class="woocommerce-form woocommerce-form-register register space-y-4" <?php do_action('woocommerce_register_form_tag'); ?>>

							<?php do_action('woocommerce_register_form_start'); ?>

							<?php if ('no' === get_option('woocommerce_registration_generate_username')): ?>
								<!-- Username Input -->
								<div>
									<label for="reg_username"
										class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
										<?php esc_html_e('اسم المستخدم', 'ashrafco'); ?>
										<span class="text-rose-500 font-black">*</span>
									</label>
									<div class="relative">
										<input type="text"
											class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
											name="username" id="reg_username" autocomplete="username"
											placeholder="<?php esc_attr_e('أدخل اسم المستخدم المفضل', 'ashrafco'); ?>"
											value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>"
											required />
										<span
											class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
											<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
											</svg>
										</span>
									</div>
								</div>
							<?php endif; ?>

							<!-- Email Input -->
							<div>
								<label for="reg_email"
									class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
									<?php esc_html_e('البريد الإلكتروني', 'ashrafco'); ?>
									<span class="text-rose-500 font-black">*</span>
								</label>
								<div class="relative">
									<input type="email"
										class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
										name="email" id="reg_email" autocomplete="email"
										placeholder="<?php esc_attr_e('yourname@example.com', 'ashrafco'); ?>"
										value="<?php echo (!empty($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>"
										required />
									<span
										class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
												d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
										</svg>
									</span>
								</div>
							</div>

							<!-- Egyptian Mobile Phone Number -->
							<div>
								<label for="reg_billing_phone"
									class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
									<?php esc_html_e('رقم الهاتف المحمول (مصر)', 'ashrafco'); ?>
									<span class="text-rose-500 font-black">*</span>
								</label>
								<div class="relative">
									<input type="tel"
										class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white text-right"
										name="billing_phone" id="reg_billing_phone" autocomplete="tel"
										placeholder="01012345678"
										value="<?php echo (!empty($_POST['billing_phone'])) ? esc_attr(wp_unslash($_POST['billing_phone'])) : ''; ?>"
										pattern="^(01)[0125][0-9]{8}$"
										title="<?php esc_attr_e('يرجى إدخال رقم محمول مصري مكون من 11 رقماً يبدأ بـ 010 أو 011 أو 012 أو 015', 'ashrafco'); ?>"
										required />
									<span
										class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
												d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
										</svg>
									</span>
								</div>
								<p class="text-[11px] text-slate-400 mt-1 text-right">
									<?php esc_html_e('سنرسل تحديثات الشحن ومتابعة الطلب السريع على هذا الرقم عبر واتساب والرسائل.', 'ashrafco'); ?>
								</p>
							</div>

							<!-- Password Input -->
							<?php if ('no' === get_option('woocommerce_registration_generate_password')): ?>
								<div>
									<label for="reg_password"
										class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
										<?php esc_html_e('كلمة المرور', 'ashrafco'); ?>
										<span class="text-rose-500 font-black">*</span>
									</label>
									<div class="relative password-input">
										<input type="password"
											class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
											name="password" id="reg_password" autocomplete="new-password"
											placeholder="<?php esc_attr_e('اختر كلمة مرور قوية', 'ashrafco'); ?>" required />
										<button type="button"
											class="ashrafco-pwd-toggle absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none transition-colors cursor-pointer"
											data-target="reg_password"
											aria-label="<?php esc_attr_e('إظهار أو إخفاء كلمة المرور', 'ashrafco'); ?>">
											<svg class="icon-eye w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
											</svg>
											<svg class="icon-eye-off w-5 h-5 hidden" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
											</svg>
										</button>

									</div>

									<!-- WooCommerce Standard Password Strength Indicator -->
									<div class="woocommerce-password-strength" role="alert" style="display: none;"></div>
								</div>

								<!-- Confirm Password Input -->
								<div>
									<label for="reg_password_confirm"
										class="block text-xs sm:text-sm font-bold text-slate-800 mb-1.5 text-right">
										<?php esc_html_e('تأكيد كلمة المرور', 'ashrafco'); ?>
										<span class="text-rose-500 font-black">*</span>
									</label>
									<div class="relative">
										<input type="password"
											class="w-full px-4 py-3 pl-11 rounded-xl border border-slate-200 focus:outline-none focus:border-slate-400 focus:ring-2 focus:ring-[var(--color-primary)]/15 text-slate-900 placeholder-slate-400 text-sm transition-all duration-200 bg-slate-50/50 hover:bg-slate-50 focus:bg-white"
											name="password_confirm" id="reg_password_confirm" autocomplete="new-password"
											placeholder="<?php esc_attr_e('أعد إدخال كلمة المرور', 'ashrafco'); ?>" required />
										<button type="button"
											class="ashrafco-pwd-toggle absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 focus:outline-none transition-colors cursor-pointer"
											data-target="reg_password_confirm"
											aria-label="<?php esc_attr_e('إظهار أو إخفاء تأكيد كلمة المرور', 'ashrafco'); ?>">
											<svg class="icon-eye w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
											</svg>
											<svg class="icon-eye-off w-5 h-5 hidden" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
													d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
											</svg>
										</button>
									</div>
									<div id="password-match-error"
										class="hidden text-xs text-rose-600 font-bold mt-1.5 text-right flex items-center gap-1.5">
										<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
										</svg>
										<span><?php esc_html_e('كلمتا المرور غير متطابقتين. يرجى التأكد من تطابق كلمة المرور وتأكيدها.', 'ashrafco'); ?></span>
									</div>
								</div>
							<?php else: ?>
								<p class="text-xs text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100">
									<?php esc_html_e('سيتم إرسال رابط تعيين كلمة المرور إلى بريدك الإلكتروني المدخل.', 'ashrafco'); ?>
								</p>
							<?php endif; ?>

							<?php do_action('woocommerce_register_form'); ?>

							<!-- Register Submit Button (Brand Red with Left Arrow) -->
							<div class="pt-1">
								<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
								<button type="submit"
									class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit btn-primary w-full py-3.5 px-6 rounded-xl font-bold text-white bg-primary hover:bg-[#b90323] active:scale-[0.99] transition duration-200 shadow-md shadow-red-600/20 flex items-center justify-center gap-2.5 cursor-pointer text-sm sm:text-base border-0"
									name="register" 
									value="<?php esc_attr_e('إنشاء الحساب', 'ashrafco'); ?>">
									<span class="text-base sm:text-lg leading-none font-bold">←</span>
									<span><?php esc_html_e('إنشاء حساب جديد', 'ashrafco'); ?></span>
								</button>
							</div>

							<!-- Social Registration Section (Google & Facebook) -->
							<div class="ashrafco-social-register-section my-4">
								<!-- Visual Divider -->
								<div class="relative flex items-center justify-center mb-3">
									<div class="border-t border-slate-200 w-full absolute inset-0 my-auto"></div>
									<span class="relative bg-white px-3 text-xs sm:text-sm font-semibold text-slate-500 select-none">
										<?php esc_html_e('أو إنشاء حساب باستخدام', 'ashrafco'); ?>
									</span>
								</div>

								<!-- Social Buttons (Stacked, full-width responsive) -->
								<div class="flex flex-col gap-2.5">
									<!-- Google Register Button -->
									<a href="<?php echo esc_url(function_exists('ashrafco_get_social_login_url') ? ashrafco_get_social_login_url('google', 'register') : '#'); ?>"
										class="ashrafco-social-btn ashrafco-btn-google w-full h-12 px-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:scale-[0.99] transition-all duration-200 shadow-sm flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 group select-none cursor-pointer"
										aria-label="<?php esc_attr_e('إنشاء حساب باستخدام Google', 'ashrafco'); ?>">
										<!-- Official Multicolor Google SVG Icon -->
										<svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105" viewBox="0 0 24 24" aria-hidden="true">
											<path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
											<path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
											<path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
											<path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
										</svg>
										<span><?php esc_html_e('إنشاء حساب باستخدام Google', 'ashrafco'); ?></span>
									</a>

									<!-- Facebook Register Button -->
									<a href="<?php echo esc_url(function_exists('ashrafco_get_social_login_url') ? ashrafco_get_social_login_url('facebook', 'register') : '#'); ?>"
										class="ashrafco-social-btn ashrafco-btn-facebook w-full h-12 px-4 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 active:scale-[0.99] transition-all duration-200 shadow-sm flex items-center justify-center gap-3 text-xs sm:text-sm font-bold text-slate-700 hover:text-slate-900 group select-none cursor-pointer"
										aria-label="<?php esc_attr_e('إنشاء حساب باستخدام Facebook', 'ashrafco'); ?>">
										<!-- Official Facebook Blue SVG Icon -->
										<svg class="w-5 h-5 flex-shrink-0 transition-transform duration-200 group-hover:scale-105" viewBox="0 0 24 24" fill="#1877F2" aria-hidden="true">
											<path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
										</svg>
										<span><?php esc_html_e('إنشاء حساب باستخدام Facebook', 'ashrafco'); ?></span>
									</a>
								</div>
							</div>

							<!-- Subtle Divider & Switch back to Login -->
							<div class="relative flex items-center justify-center my-5">
								<div class="border-t border-slate-200 w-full absolute inset-0 my-auto"></div>
								<div
									class="relative bg-white px-3 text-xs sm:text-sm text-slate-500 flex items-center gap-1.5 select-none">
									<span><?php esc_html_e('لديك حساب بالفعل؟', 'ashrafco'); ?></span>
									<button type="button"
										class="ashrafco-switch-to-login hover:underline font-bold text-primary cursor-pointer">
										<?php esc_html_e('تسجيل الدخول هنا', 'ashrafco'); ?>
									</button>
								</div>
							</div>

							<!-- Bottom Security & Shipping Micro-Badges -->
							<div
								class="pt-2 flex items-center justify-center gap-4 sm:gap-6 text-xs text-slate-500 font-medium select-none">
								<span class="flex items-center gap-1.5">
									<svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
									</svg>
									<span><?php esc_html_e('اتصال آمن ومشفر', 'ashrafco'); ?></span>
								</span>
								<span class="h-4 w-px bg-slate-200"></span>
								<span class="flex items-center gap-1.5">
									<svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor"
										viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
									</svg>
									<span><?php esc_html_e('شحن ومتابعة فورية', 'ashrafco'); ?></span>
								</span>
							</div>

							<?php do_action('woocommerce_register_form_end'); ?>

						</form>
					</div>
				<?php endif; ?>

			</div>

		</div>

	</div>

</div>

<!-- Vanilla JS for Responsive Interactions, Dynamic Breadcrumbs, and Password Visibility -->
<script>
	(function () {
		function initAuthInteractions() {
			var tabLogin = document.getElementById('tab-btn-login');
			var tabRegister = document.getElementById('tab-btn-register');
			var panelLogin = document.getElementById('panel-login');
			var panelRegister = document.getElementById('panel-register');
			var desktopBrandLogin = document.getElementById('desktop-brand-login');
			var desktopBrandRegister = document.getElementById('desktop-brand-register');
			var authTitle = document.getElementById('auth-title');
			var authSubtitle = document.getElementById('auth-subtitle');
			var breadcrumbCurrent = document.getElementById('ashrafco-breadcrumb-current');
			var switchToRegisterBtns = document.querySelectorAll('.ashrafco-switch-to-register');
			var switchToLoginBtns = document.querySelectorAll('.ashrafco-switch-to-login');

			function showTab(tabName) {
				if (!panelRegister) return;

				if (tabName === 'register') {
					if (tabRegister) {
						tabRegister.classList.remove('text-slate-600', 'hover:text-slate-900', 'bg-transparent');
						tabRegister.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
						tabRegister.setAttribute('aria-selected', 'true');
					}
					if (tabLogin) {
						tabLogin.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
						tabLogin.classList.add('text-slate-600', 'hover:text-slate-900', 'bg-transparent');
						tabLogin.setAttribute('aria-selected', 'false');
					}
					panelLogin.classList.add('hidden');
					panelRegister.classList.remove('hidden');

					if (desktopBrandLogin) desktopBrandLogin.classList.add('hidden');
					if (desktopBrandRegister) desktopBrandRegister.classList.remove('hidden');

					if (authTitle) authTitle.textContent = '<?php echo esc_js(__('إنشاء حساب جديد', 'ashrafco')); ?>';
					if (authSubtitle) authSubtitle.textContent = '<?php echo esc_js(__('سجّل بياناتك للاستمتاع بتجربة تسوق متكاملة وسريعة', 'ashrafco')); ?>';
					if (breadcrumbCurrent) breadcrumbCurrent.textContent = '<?php echo esc_js(__('إنشاء حساب جديد', 'ashrafco')); ?>';

					if (window.location.hash !== '#register') {
						window.location.hash = 'register';
					}
				} else {
					if (tabLogin) {
						tabLogin.classList.remove('text-slate-600', 'hover:text-slate-900', 'bg-transparent');
						tabLogin.classList.add('bg-slate-900', 'text-white', 'shadow-sm');
						tabLogin.setAttribute('aria-selected', 'true');
					}
					if (tabRegister) {
						tabRegister.classList.remove('bg-slate-900', 'text-white', 'shadow-sm');
						tabRegister.classList.add('text-slate-600', 'hover:text-slate-900', 'bg-transparent');
						tabRegister.setAttribute('aria-selected', 'false');
					}
					panelRegister.classList.add('hidden');
					panelLogin.classList.remove('hidden');

					if (desktopBrandRegister) desktopBrandRegister.classList.add('hidden');
					if (desktopBrandLogin) desktopBrandLogin.classList.remove('hidden');

					if (authTitle) authTitle.textContent = '<?php echo esc_js(__('تسجيل الدخول إلى حسابك', 'ashrafco')); ?>';
					if (authSubtitle) authSubtitle.textContent = '<?php echo esc_js(__('مرحباً بك مجدداً، سجّل دخولك للمتابعة في التسوق', 'ashrafco')); ?>';
					if (breadcrumbCurrent) breadcrumbCurrent.textContent = '<?php echo esc_js(__('تسجيل الدخول', 'ashrafco')); ?>';

					if (window.location.hash === '#register') {
						history.pushState(null, null, window.location.pathname + window.location.search);
					}
				}
			}

			if (tabLogin) {
				tabLogin.addEventListener('click', function (e) {
					e.preventDefault();
					showTab('login');
				});
			}

			if (tabRegister) {
				tabRegister.addEventListener('click', function (e) {
					e.preventDefault();
					showTab('register');
				});
			}

			switchToRegisterBtns.forEach(function (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					showTab('register');
				});
			});

			switchToLoginBtns.forEach(function (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					showTab('login');
				});
			});

			// Check initial hash
			if (window.location.hash === '#register') {
				showTab('register');
			}

			// Password reveal toggle
			var pwdToggles = document.querySelectorAll('.ashrafco-pwd-toggle');
			pwdToggles.forEach(function (toggle) {
				toggle.addEventListener('click', function (e) {
					e.preventDefault();
					var targetId = this.getAttribute('data-target');
					var input = document.getElementById(targetId);
					if (!input) return;

					var iconEye = this.querySelector('.icon-eye');
					var iconEyeOff = this.querySelector('.icon-eye-off');

					if (input.type === 'password') {
						input.type = 'text';
						if (iconEye) iconEye.classList.add('hidden');
						if (iconEyeOff) iconEyeOff.classList.remove('hidden');
					} else {
						input.type = 'password';
						if (iconEye) iconEye.classList.remove('hidden');
						if (iconEyeOff) iconEyeOff.classList.add('hidden');
					}
				});
			});

			// Client-side confirm password validation
			var regForm = document.querySelector('form.woocommerce-form-register');
			var pwdInput = document.getElementById('reg_password');
			var pwdConfirmInput = document.getElementById('reg_password_confirm');
			var pwdMatchError = document.getElementById('password-match-error');

			function validatePasswordMatch() {
				if (!pwdInput || !pwdConfirmInput) return true;
				if (pwdConfirmInput.value && pwdInput.value !== pwdConfirmInput.value) {
					if (pwdMatchError) pwdMatchError.classList.remove('hidden');
					pwdConfirmInput.classList.add('border-rose-500');
					return false;
				} else {
					if (pwdMatchError) pwdMatchError.classList.add('hidden');
					pwdConfirmInput.classList.remove('border-rose-500');
					return true;
				}
			}

			if (pwdConfirmInput && pwdInput) {
				pwdConfirmInput.addEventListener('input', validatePasswordMatch);
				pwdInput.addEventListener('input', function () {
					if (pwdConfirmInput.value) {
						validatePasswordMatch();
					}
				});
			}

			if (regForm) {
				regForm.addEventListener('submit', function (e) {
					if (pwdInput && pwdConfirmInput) {
						if (pwdInput.value !== pwdConfirmInput.value) {
							e.preventDefault();
							if (pwdMatchError) pwdMatchError.classList.remove('hidden');
							pwdConfirmInput.classList.add('border-rose-500');
							pwdConfirmInput.focus();
							return false;
						}
					}
				});
			}

		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', initAuthInteractions);
		} else {
			initAuthInteractions();
		}
	})();
</script>

<?php do_action('woocommerce_after_customer_login_form'); ?>