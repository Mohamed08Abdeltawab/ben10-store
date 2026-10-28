<?php
/**
 * Checkout coupon form override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) {
	return;
}
?>

<!-- Coupon Toggle Banner -->
<div class="woocommerce-form-coupon-toggle mb-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" dir="rtl">
	<div class="inline-flex items-center gap-2 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-xl px-4 py-2.5 shadow-2xs font-sans">
		<svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
		</svg>
		<span><?php esc_html_e( 'هل لديك كوبون خصم؟', 'ashrafco' ); ?></span>
		<a href="#" class="showcoupon font-bold text-brand-600 hover:text-brand-700 transition-colors underline decoration-brand-600/40 underline-offset-4 cursor-pointer">
			<?php esc_html_e( 'اضغط هنا للإدخال', 'ashrafco' ); ?>
		</a>
	</div>
</div>

<!-- Collapsible Coupon Card -->
<form class="checkout_coupon woocommerce-form-coupon max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 font-sans" method="post" style="display:none;" dir="rtl">
	<div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mt-2 max-w-xl shadow-xs">
		<p class="text-xs text-slate-600 mb-3 font-medium leading-relaxed">
			<?php echo esc_html( sprintf( __( 'إذا كان لديك كود خصم ترويجي خاص بـ %s، يرجى كتابته في الحقل أدناه لتطبيق الخصم فوراً.', 'ashrafco' ), function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو' ) ); ?>
		</p>
		<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
			<input
				type="text"
				name="coupon_code"
				class="input-text flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition font-sans"
				placeholder="<?php esc_attr_e( 'أدخل كود الكوبون هنا...', 'ashrafco' ); ?>"
				id="coupon_code"
				value=""
			/>
			<button
				type="submit"
				class="btn-secondary button bg-slate-900 hover:bg-brand-600 text-white font-bold py-2.5 px-6 rounded-xl text-xs sm:text-sm transition-all shadow-sm active:scale-[0.99] whitespace-nowrap cursor-pointer select-none"
				name="apply_coupon"
				value="<?php esc_attr_e( 'تطبيق الكوبون', 'ashrafco' ); ?>"
			>
				<?php esc_html_e( 'تطبيق الكوبون', 'ashrafco' ); ?>
			</button>
		</div>
	</div>
</form>
