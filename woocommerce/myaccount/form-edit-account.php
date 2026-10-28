<?php
/**
 * Modern Edit Account Form Template Override for Ashrafco Theme
 *
 * Matching Reference Design for My Account -> Account Details
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );

$user_id = $user->ID;

// Values with POST fallback so entered values are preserved on validation errors
$first_name   = isset( $_POST['account_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['account_first_name'] ) ) : $user->first_name;
$last_name    = isset( $_POST['account_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['account_last_name'] ) ) : $user->last_name;
$display_name = isset( $_POST['account_display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['account_display_name'] ) ) : $user->display_name;
$user_email   = isset( $_POST['account_email'] ) ? sanitize_email( wp_unslash( $_POST['account_email'] ) ) : $user->user_email;

// Phone number retrieval
$raw_saved_phone = get_user_meta( $user_id, 'billing_phone', true );
if ( empty( $raw_saved_phone ) && ! empty( $user->billing_phone ) ) {
	$raw_saved_phone = $user->billing_phone;
}

// Determine country code and display phone
$current_country_code = '+20';
$display_phone        = '';

if ( isset( $_POST['billing_phone'] ) ) {
	$display_phone        = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
	$current_country_code = isset( $_POST['phone_country_code'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_country_code'] ) ) : '+20';
} elseif ( ! empty( $raw_saved_phone ) ) {
	$supported_codes = array( '+966', '+971', '+965', '+973', '+968', '+974', '+962' );
	$matched_code    = false;
	foreach ( $supported_codes as $code ) {
		if ( 0 === strpos( $raw_saved_phone, $code ) ) {
			$current_country_code = $code;
			$display_phone        = substr( $raw_saved_phone, strlen( $code ) );
			$matched_code         = true;
			break;
		}
	}
	if ( ! $matched_code ) {
		$current_country_code = '+20';
		$display_phone        = $raw_saved_phone;
		if ( 0 === strpos( $display_phone, '+20' ) ) {
			$display_phone = '0' . substr( $display_phone, 3 );
		}
	}
}

// Birthdate
$birthdate = isset( $_POST['account_birthdate'] ) ? sanitize_text_field( wp_unslash( $_POST['account_birthdate'] ) ) : get_user_meta( $user_id, 'account_birthdate', true );

// Email verification status
$is_email_verified = (bool) get_user_meta( $user_id, 'ashrafco_email_verified', true );
?>

<div class="ashrafco-edit-account w-full font-sans space-y-6" dir="rtl">

	<!-- =========================================================================
	     PAGE HEADER
	     ========================================================================= -->
	<div class="mb-6">
		<div class="flex items-center gap-2.5">
			<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight m-0">بيانات الحساب وكلمة المرور</h1>
			<svg class="w-6 h-6 text-primary flex-shrink-0"  fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
			</svg>
		</div>
		<p class="text-xs sm:text-sm text-slate-500 mt-1.5 mb-0">
			قم بتحديث معلومات الاتصال والاسم وتأمين كلمة المرور الخاصة بحسابك في متجر <?php echo esc_html( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو' ); ?>.
		</p>
	</div>

	<!-- =========================================================================
	     FORM
	     ========================================================================= -->
	<form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<!-- =====================================================================
		     CARD 1: البيانات الأساسية ومعلومات الاتصال
		     ===================================================================== -->
		<div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-7 shadow-xs space-y-6">

			<!-- Section Header -->
			<div class="pb-3 border-b border-slate-100">
				<div class="flex items-center gap-2.5">
					<h2 class="text-base sm:text-lg font-bold text-slate-900 m-0 leading-tight">البيانات الأساسية ومعلومات الاتصال</h2>
					<svg class="w-5 h-5 text-primary flex-shrink-0"  fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
					</svg>
				</div>
				<p class="text-xs text-slate-400 m-0 mt-1 font-normal">نستخدم هذه البيانات للتواصل معك وإدارة طلباتك.</p>
			</div>

			<!-- Fields Grid (2 Columns on Desktop) -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

				<!-- Row 1 Right: First Name -->
				<div>
					<label for="account_first_name" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'الاسم الأول', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="text"
							name="account_first_name"
							id="account_first_name"
							autocomplete="given-name"
							value="<?php echo esc_attr( $first_name ); ?>"
							required
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
							<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
							</svg>
						</div>
					</div>
				</div>

				<!-- Row 1 Left: Last Name -->
				<div>
					<label for="account_last_name" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'اسم العائلة', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="text"
							name="account_last_name"
							id="account_last_name"
							autocomplete="family-name"
							value="<?php echo esc_attr( $last_name ); ?>"
							required
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
							<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
							</svg>
						</div>
					</div>
				</div>

				<!-- Row 2 Right: Display Name -->
				<div>
					<label for="account_display_name" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'اسم العرض (في الموقع)', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="text"
							name="account_display_name"
							id="account_display_name"
							value="<?php echo esc_attr( $display_name ); ?>"
							required
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
							<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
							</svg>
						</div>
					</div>
				</div>

				<!-- Row 2 Left: Email Address -->
				<div>
					<label for="account_email" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'البريد الإلكتروني', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="email"
							name="account_email"
							id="account_email"
							autocomplete="email"
							dir="ltr"
							value="<?php echo esc_attr( $user_email ); ?>"
							required
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans text-right"
						/>
						<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
							<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
				</div>

				<!-- Row 3 Right: Phone Number with Country Code Selector -->
				<div>
					<label for="billing_phone" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'رقم الهاتف', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="flex items-center gap-2">
						<!-- Functional Country Code Selector -->
						<div class="relative w-24 sm:w-28 flex-shrink-0">
							<select
								name="phone_country_code"
								id="phone_country_code"
								class="w-full h-12 pr-3 pl-7 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-700 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition appearance-none cursor-pointer"
							>
								<option value="+20" <?php selected( $current_country_code, '+20' ); ?>>+20</option>
								<option value="+966" <?php selected( $current_country_code, '+966' ); ?>>+966</option>
								<option value="+971" <?php selected( $current_country_code, '+971' ); ?>>+971</option>
								<option value="+965" <?php selected( $current_country_code, '+965' ); ?>>+965</option>
								<option value="+973" <?php selected( $current_country_code, '+973' ); ?>>+973</option>
								<option value="+968" <?php selected( $current_country_code, '+968' ); ?>>+968</option>
								<option value="+974" <?php selected( $current_country_code, '+974' ); ?>>+974</option>
								<option value="+962" <?php selected( $current_country_code, '+962' ); ?>>+962</option>
							</select>
							<span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400">
								<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
								</svg>
							</span>
						</div>

						<!-- Phone Number Input -->
						<div class="relative flex-1">
							<input
								type="tel"
								name="billing_phone"
								id="billing_phone"
								autocomplete="tel"
								dir="ltr"
								value="<?php echo esc_attr( $display_phone ); ?>"
								placeholder="01012345678"
								required
								class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-mono text-right focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition"
							/>
							<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
								<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
								</svg>
							</div>
						</div>
					</div>
				</div>

				<!-- Row 3 Left: Birth Date (Optional) -->
				<div>
					<label for="account_birthdate" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'تاريخ الميلاد (اختياري)', 'ashrafco' ); ?>
					</label>
					<div class="relative">
						<input
							type="date"
							name="account_birthdate"
							id="account_birthdate"
							value="<?php echo esc_attr( $birthdate ); ?>"
							placeholder="اختر تاريخ الميلاد"
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 flex items-center justify-center">
							<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
					</div>
				</div>

			</div>
		</div>

		<!-- =====================================================================
		     CARD 2: تغيير كلمة المرور
		     ===================================================================== -->
		<div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-7 shadow-xs space-y-6">

			<!-- Section Header -->
			<div class="pb-3 border-b border-slate-100">
				<div class="flex items-center gap-2.5">
					<h2 class="text-base sm:text-lg font-bold text-slate-900 m-0 leading-tight">تغيير كلمة المرور</h2>
					<svg class="w-5 h-5 text-primary flex-shrink-0"  fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
					</svg>
				</div>
				<p class="text-xs text-slate-400 m-0 mt-1 font-normal">استخدم كلمة مرور قوية لحماية حسابك.</p>
			</div>

			<!-- Password Fields Grid (3 Columns on Desktop) -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">

				<!-- Current Password -->
				<div>
					<label for="password_current" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'كلمة المرور الحالية', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="password"
							name="password_current"
							id="password_current"
							autocomplete="current-password"
							placeholder="أدخل كلمة المرور الحالية"
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<button
							type="button"
							class="ashrafco-toggle-pw-btn absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1 cursor-pointer transition-colors z-10 flex items-center justify-center"
							data-target="password_current"
							aria-label="تبديل إظهار كلمة المرور"
						>
							<svg class="w-5 h-5 ashrafco-eye-icon"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
							</svg>
						</button>
					</div>
				</div>

				<!-- New Password -->
				<div>
					<label for="password_1" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'كلمة المرور الجديدة', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative password-input">
						<input
							type="password"
							name="password_1"
							id="password_1"
							autocomplete="new-password"
							placeholder="أدخل كلمة المرور الجديدة"
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<button
							type="button"
							class="ashrafco-toggle-pw-btn absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1 cursor-pointer transition-colors z-10 flex items-center justify-center"
							data-target="password_1"
							aria-label="تبديل إظهار كلمة المرور"
						>
							<svg class="w-5 h-5 ashrafco-eye-icon"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
							</svg>
						</button>
					</div>
				</div>

				<!-- Confirm Password -->
				<div>
					<label for="password_2" class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
						<?php esc_html_e( 'تأكيد كلمة المرور الجديدة', 'ashrafco' ); ?> <span class="text-primary">*</span>
					</label>
					<div class="relative">
						<input
							type="password"
							name="password_2"
							id="password_2"
							autocomplete="new-password"
							placeholder="أعد إدخال كلمة المرور الجديدة"
							class="w-full h-12 pr-4 pl-10 bg-slate-50/80 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition font-sans"
						/>
						<button
							type="button"
							class="ashrafco-toggle-pw-btn absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none p-1 cursor-pointer transition-colors z-10 flex items-center justify-center"
							data-target="password_2"
							aria-label="تبديل إظهار كلمة المرور"
						>
							<svg class="w-5 h-5 ashrafco-eye-icon"  fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
							</svg>
						</button>
					</div>
				</div>

			</div>

			<!-- Password Strength Meter & Guidance -->
			<div class="pt-1 space-y-2">
				<!-- WooCommerce Official Password Strength & Hint Container -->
				<div class="woocommerce-password-strength" role="alert" style="display: none;"></div>

				<!-- 5 Dynamic Strength Bars -->
				<div class="flex items-center gap-1.5 w-full">
					<div id="pw-meter-bar-1" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></div>
					<div id="pw-meter-bar-2" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></div>
					<div id="pw-meter-bar-3" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></div>
					<div id="pw-meter-bar-4" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></div>
					<div id="pw-meter-bar-5" class="h-1.5 flex-1 rounded-full bg-slate-200 transition-colors duration-300"></div>
				</div>

				<!-- Strength Label and Rule Explanation -->
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs text-slate-500">
					<div class="flex items-center gap-1.5">
						<span class="font-bold text-slate-700">قوة كلمة المرور:</span>
						<span id="pw-strength-text" class="font-semibold text-slate-400">—</span>
					</div>
					<span class="text-slate-400 text-[11px] sm:text-xs">استخدم 8 أحرف على الأقل مع مزيج من الأحرف والأرقام والرموز</span>
				</div>

				<!-- Live Match Feedback -->
				<div id="pw-match-alert" class="hidden text-xs font-bold pt-1"></div>
			</div>

		</div>

		<!-- =====================================================================
		     CARD 3: أمان الحساب
		     ===================================================================== -->
		<div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-7 shadow-xs space-y-4">

			<!-- Section Header -->
			<div class="pb-3 border-b border-slate-100">
				<div class="flex items-center gap-2.5">
					<h2 class="text-base sm:text-lg font-bold text-slate-900 m-0 leading-tight">أمان الحساب</h2>
					<svg class="w-5 h-5 text-primary flex-shrink-0"  fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
					</svg>
				</div>
				<p class="text-xs text-slate-400 m-0 mt-1 font-normal">معلومات إضافية حول أمان حسابك</p>
			</div>

			<!-- Email Verification Status (Truthful to active state) -->
			<?php if ( $is_email_verified ) : ?>
				<div class="ashrafco-verified-card rounded-2xl p-4 sm:p-5 flex items-center justify-between gap-4">
					<div>
						<h4 class="text-xs sm:text-sm font-bold text-[#166534] m-0">تم تأكيد البريد الإلكتروني</h4>
						<p class="text-[11px] sm:text-xs text-[#15803d] m-0 mt-0.5">عنوان بريدك الإلكتروني مؤكد ويمكنك استلام جميع الإشعارات عليه.</p>
					</div>
					<div class="w-8 h-8 rounded-full bg-[#16a34a] text-white flex items-center justify-center flex-shrink-0 shadow-sm">
						<svg class="w-5 h-5"  fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
						</svg>
					</div>
				</div>
			<?php else : ?>
				<div class="bg-amber-50/80 border border-amber-200/90 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
					<div>
						<h4 class="text-xs sm:text-sm font-bold text-amber-900 m-0">البريد الإلكتروني غير مؤكد</h4>
						<p class="text-[11px] sm:text-xs text-amber-700 m-0 mt-0.5">يرجى تأكيد بريدك الإلكتروني لتأمين حسابك واستلام إشعارات الطلبات وتحديثاتها.</p>
					</div>
					<div class="flex-shrink-0 self-end sm:self-center">
						<a
							href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'verify_email', wc_get_account_endpoint_url( 'edit-account' ) ), 'ashrafco_verify_email' ) ); ?>"
							class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs active:scale-95 no-underline"
							
						>
							<svg class="w-4 h-4"  fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
							<span>تأكيد البريد الإلكتروني الآن</span>
						</a>
					</div>
				</div>
			<?php endif; ?>

		</div>

		<?php do_action( 'woocommerce_edit_account_form' ); ?>

		<!-- =====================================================================
		     FORM ACTIONS: حفظ التغييرات وإلغاء
		     ===================================================================== -->
		<div class="flex flex-row items-center gap-3.5 pt-2">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>

			<button
				type="submit"
				class="woocommerce-Button button ashrafco-save-btn inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary-hover text-white font-bold py-3.5 px-9 rounded-xl text-xs sm:text-sm transition-all shadow-md active:scale-[0.99] cursor-pointer"
				name="save_account_details"
				value="<?php esc_attr_e( 'حفظ التغييرات', 'ashrafco' ); ?>"
				
			>
				<svg class="w-4 h-4 text-white flex-shrink-0"  fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
				</svg>
				<span>حفظ التغييرات</span>
			</button>

			<a
				href="<?php echo esc_url( wc_get_account_endpoint_url( 'dashboard' ) ); ?>"
				class="inline-flex items-center justify-center px-8 py-3.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs sm:text-sm transition-colors cursor-pointer text-center no-underline"
			>
				<span>إلغاء</span>
			</a>

			<input type="hidden" name="action" value="save_account_details" />
		</div>

		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

	</form>

</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

<script>
(function() {
	// 1. Password Visibility Toggle
	var toggleButtons = document.querySelectorAll('.ashrafco-toggle-pw-btn');
	toggleButtons.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			var targetId = this.getAttribute('data-target');
			var input = document.getElementById(targetId);
			if (!input) return;

			var isPassword = input.type === 'password';
			input.type = isPassword ? 'text' : 'password';

			var icon = this.querySelector('.ashrafco-eye-icon');
			if (icon) {
				if (isPassword) {
					// Slashed eye (revealed state toggle)
					icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
				} else {
					// Normal eye
					icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
				}
			}
		});
	});

	// 2. Interactive Password Strength Meter & Realtime Match Validation
	var newPwInput     = document.getElementById('password_1');
	var confirmPwInput = document.getElementById('password_2');
	var meterBars      = [
		document.getElementById('pw-meter-bar-1'),
		document.getElementById('pw-meter-bar-2'),
		document.getElementById('pw-meter-bar-3'),
		document.getElementById('pw-meter-bar-4'),
		document.getElementById('pw-meter-bar-5')
	];
	var strengthText   = document.getElementById('pw-strength-text');
	var matchAlert     = document.getElementById('pw-match-alert');

	function calculateStrength(pwd) {
		if (!pwd) return 0;
		if (window.wp && window.wp.passwordStrength && typeof window.wp.passwordStrength.meter === 'function') {
			try {
				var blacklist = (window.wp.passwordStrength.userInputDisallowedList) ? window.wp.passwordStrength.userInputDisallowedList() : [];
				var wcScore = window.wp.passwordStrength.meter(pwd, blacklist);
				if (wcScore <= 0) return 1;
				if (wcScore === 1) return 2;
				if (wcScore === 2) return 3;
				if (wcScore === 3) return 4;
				return 5;
			} catch (err) {}
		}
		var score = 0;
		if (pwd.length >= 8) score++;
		if (pwd.length >= 12) score++;
		if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) score++;
		if (/[0-9]/.test(pwd)) score++;
		if (/[^a-zA-Z0-9]/.test(pwd)) score++;
		return Math.min(score, 5);
	}

	function updateMeter() {
		var val   = newPwInput ? newPwInput.value : '';
		var score = calculateStrength(val);

		var colors = [
			'bg-slate-200',
			'bg-rose-500',
			'bg-amber-500',
			'bg-yellow-500',
			'bg-emerald-500',
			'bg-emerald-600'
		];
		var textLabels = ['—', 'ضعيفة جداً', 'ضعيفة', 'متوسطة', 'جيدة', 'قوية جداً'];
		var textColors = ['text-slate-400', 'text-rose-500', 'text-amber-500', 'text-yellow-600', 'text-emerald-600', 'text-emerald-700'];

		meterBars.forEach(function(bar, idx) {
			if (!bar) return;
			bar.className = 'h-1.5 flex-1 rounded-full transition-colors duration-300 ' + (idx < score ? colors[score] : 'bg-slate-200');
		});

		if (strengthText) {
			strengthText.textContent = textLabels[score];
			strengthText.className   = 'font-bold transition-colors duration-200 ' + textColors[score];
		}

		checkMatch();
	}

	function checkMatch() {
		if (!confirmPwInput || !matchAlert) return;
		var newPass     = newPwInput ? newPwInput.value : '';
		var confirmPass = confirmPwInput.value;

		if (!confirmPass) {
			matchAlert.className   = 'hidden';
			matchAlert.textContent = '';
			return;
		}

		if (newPass === confirmPass) {
			matchAlert.className = 'block text-xs font-bold text-emerald-600 pt-1';
			matchAlert.innerHTML = '<span class="inline-flex items-center gap-1"><span>كلمتا المرور متطابقتان</span><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg></span>';
		} else {
			matchAlert.className = 'block text-xs font-bold text-rose-500 pt-1';
			matchAlert.innerHTML = '<span class="inline-flex items-center gap-1"><span>كلمتا المرور غير متطابقتين</span><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg></span>';
		}
	}

	if (newPwInput) {
		newPwInput.addEventListener('input', updateMeter);
	}
	if (confirmPwInput) {
		confirmPwInput.addEventListener('input', checkMatch);
	}
})();
</script>
