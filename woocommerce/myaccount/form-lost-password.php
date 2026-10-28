<?php
/**
 * Modern Lost Password Form Template Override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>

<div class="ashrafco-lost-password py-8 font-sans" dir="rtl">
	<div class="max-w-md mx-auto p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-sm space-y-6">

		<!-- Header -->
		<div class="text-center space-y-2">
			<div class="w-12 h-12 rounded-2xl bg-orange-50 text-brand-600 flex items-center justify-center mx-auto border border-orange-100 shadow-2xs">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
				</svg>
			</div>
			<h2 class="text-xl font-black text-slate-900 tracking-tight m-0">
				استعادة كلمة المرور
			</h2>
			<p class="text-xs text-slate-500 leading-relaxed m-0">
				<?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'هل نسيت كلمة المرور الخاصة بحسابك؟ يرجى إدخال اسم المستخدم أو البريد الإلكتروني المسجل، وسنرسل إليك رابطاً عبر البريد لتعيين كلمة مرور جديدة.', 'ashrafco' ) ); ?>
			</p>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password space-y-4">

			<div>
				<label for="user_login" class="block text-xs font-bold text-slate-700 mb-1.5">
					<?php esc_html_e( 'اسم المستخدم أو البريد الإلكتروني', 'ashrafco' ); ?> <span class="text-rose-500">*</span>
				</label>
				<input
					class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-sans"
					type="text"
					name="user_login"
					id="user_login"
					autocomplete="username"
					placeholder="name@example.com"
					required
				/>
			</div>

			<?php do_action( 'woocommerce_lostpassword_form' ); ?>

			<div class="pt-2">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button
					type="submit"
					class="woocommerce-Button button w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-6 rounded-xl text-xs sm:text-sm shadow-md transition-all active:scale-[0.99] cursor-pointer"
					value="<?php esc_attr_e( 'إرسال رابط التعيين', 'ashrafco' ); ?>"
				>
					<?php esc_html_e( 'إرسال رابط تعيين كلمة المرور', 'ashrafco' ); ?>
				</button>
			</div>

			<div class="text-center pt-2 border-t border-slate-100">
				<a
					href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
					class="text-xs font-bold text-slate-500 hover:text-brand-600 transition-colors inline-flex items-center gap-1"
				>
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
					<span>العودة لتسجيل الدخول</span>
				</a>
			</div>

			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>

		</form>

	</div>
</div>

<?php
do_action( 'woocommerce_after_lost_password_form' );
