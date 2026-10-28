<?php
/**
 * Modern Reset Password Form Template Override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>

<div class="ashrafco-reset-password py-8 font-sans" dir="rtl">
	<div class="max-w-md mx-auto p-6 sm:p-8 bg-white border border-slate-200 rounded-2xl shadow-sm space-y-6">

		<!-- Header -->
		<div class="text-center space-y-2">
			<div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto border border-emerald-100 shadow-2xs">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
				</svg>
			</div>
			<h2 class="text-xl font-black text-slate-900 tracking-tight m-0">
				تعيين كلمة المرور الجديدة
			</h2>
			<p class="text-xs text-slate-500 leading-relaxed m-0">
				<?php echo apply_filters( 'woocommerce_reset_password_message', esc_html__( 'أدخل كلمة المرور الجديدة الخاصة بحسابك أدناه.', 'ashrafco' ) ); ?>
			</p>
		</div>

		<form method="post" class="woocommerce-ResetPassword lost_reset_password space-y-4">

			<div>
				<label for="password_1" class="block text-xs font-bold text-slate-700 mb-1.5">
					<?php esc_html_e( 'كلمة المرور الجديدة', 'ashrafco' ); ?> <span class="text-rose-500">*</span>
				</label>
				<input
					type="password"
					class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-sans"
					name="password_1"
					id="password_1"
					autocomplete="new-password"
					required
				/>
			</div>

			<div>
				<label for="password_2" class="block text-xs font-bold text-slate-700 mb-1.5">
					<?php esc_html_e( 'تأكيد كلمة المرور الجديدة', 'ashrafco' ); ?> <span class="text-rose-500">*</span>
				</label>
				<input
					type="password"
					class="woocommerce-Input woocommerce-Input--text input-text w-full px-4 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-sans"
					name="password_2"
					id="password_2"
					autocomplete="new-password"
					required
				/>
			</div>

			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

			<?php do_action( 'woocommerce_resetpassword_form' ); ?>

			<div class="pt-2">
				<input type="hidden" name="wc_reset_password" value="true" />
				<button
					type="submit"
					class="woocommerce-Button button w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-6 rounded-xl text-xs sm:text-sm shadow-md transition-all active:scale-[0.99] cursor-pointer"
					value="<?php esc_attr_e( 'حفظ وتحديث كلمة المرور', 'ashrafco' ); ?>"
				>
					<?php esc_html_e( 'حفظ وتحديث كلمة المرور', 'ashrafco' ); ?>
				</button>
			</div>

			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>

		</form>

	</div>
</div>

<?php
do_action( 'woocommerce_after_reset_password_form' );
