<?php
/**
 * Checkout login form override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
	return;
}
?>

<!-- Login Toggle Banner -->
<div class="woocommerce-form-login-toggle mb-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" dir="rtl">
	<div class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-2xs font-sans">
		<svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
		</svg>
		<span><?php echo esc_html( sprintf( __( 'عميل سابق لدى %s؟', 'ashrafco' ), function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو' ) ); ?></span>
		<a href="#" class="showlogin font-bold text-brand-600 hover:text-brand-700 transition-colors underline decoration-brand-600/40 underline-offset-4 cursor-pointer">
			<?php esc_html_e( 'اضغط هنا لتسجيل الدخول', 'ashrafco' ); ?>
		</a>
	</div>
</div>

<!-- Collapsible Login Card -->
<form class="woocommerce-form woocommerce-form-login login max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 font-sans" method="post" style="display:none;" dir="rtl">
	<div class="ui-card bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm max-w-xl">
		<h4 class="text-sm sm:text-base font-extrabold text-slate-900 mb-2">
			<?php esc_html_e( 'تسجيل الدخول السريع', 'ashrafco' ); ?>
		</h4>
		<p class="text-xs text-slate-500 mb-5 leading-relaxed">
			<?php esc_html_e( 'إذا كنت قد اشتريت منا سابقاً، يرجى إدخال تفاصيل الدخول أدناه لمزامنة طلبك مع حسابك المسجل ومتابعة الشحن.', 'ashrafco' ); ?>
		</p>

		<?php do_action( 'woocommerce_login_form_start' ); ?>

		<div class="space-y-4">
			<div>
				<label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">
					<?php esc_html_e( 'اسم المستخدم أو البريد الإلكتروني أو رقم الهاتف', 'ashrafco' ); ?> <span class="required text-rose-500">*</span>
				</label>
				<input
					type="text"
					class="input-text w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition font-sans"
					name="username"
					id="username"
					autocomplete="username"
					required
				/>
			</div>

			<div>
				<label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
					<?php esc_html_e( 'كلمة المرور', 'ashrafco' ); ?> <span class="required text-rose-500">*</span>
				</label>
				<input
					class="input-text w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition font-sans"
					type="password"
					name="password"
					id="password"
					autocomplete="current-password"
					required
				/>
			</div>
		</div>

		<?php do_action( 'woocommerce_login_form' ); ?>

		<div class="flex items-center justify-between flex-wrap gap-3 mt-4 pt-2">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-700 select-none">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox rounded text-brand-600 focus:ring-brand-500" name="rememberme" type="checkbox" id="rememberme" value="forever" />
				<span><?php esc_html_e( 'تذكر بياناتي', 'ashrafco' ); ?></span>
			</label>

			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>" class="text-xs text-slate-500 hover:text-brand-600 transition-colors">
				<?php esc_html_e( 'نسيت كلمة المرور؟', 'ashrafco' ); ?>
			</a>
		</div>

		<div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<input type="hidden" name="redirect" value="<?php echo esc_url( wc_get_checkout_url() ); ?>" />
			<button
				type="submit"
				class="woocommerce-button button woocommerce-form-login__submit bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 px-7 rounded-xl text-xs sm:text-sm transition-all shadow-md active:scale-[0.99] cursor-pointer"
				name="login"
				value="<?php esc_attr_e( 'تسجيل الدخول', 'ashrafco' ); ?>"
			>
				<?php esc_html_e( 'تسجيل الدخول', 'ashrafco' ); ?>
			</button>
		</div>

		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</div>
</form>
