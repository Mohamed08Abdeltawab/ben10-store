<?php
/**
 * Modern Sticky Cart Totals Template Override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?> w-full bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6" dir="rtl">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<!-- Header: Order Summary -->
	<div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
		<h2 class="text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2 m-0 p-0">
			<svg class="w-6 h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
			</svg>
			<span>ملخص الطلب</span>
		</h2>
		<span class="text-xs font-bold text-primary bg-primary/10 px-3 py-1 rounded-xl">
			<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?> منتجات
		</span>
	</div>

	<!-- Totals Breakdown Table / List -->
	<div class="space-y-4 text-sm w-full">

		<!-- 1. Subtotal -->
		<div class="flex items-center justify-between text-slate-700 font-bold text-sm sm:text-base">
			<span class="whitespace-nowrap">المجموع الفرعي:</span>
			<span class="font-bold text-slate-900 font-mono text-base sm:text-lg whitespace-nowrap">
				<?php wc_cart_totals_subtotal_html(); ?>
			</span>
		</div>

		<!-- 2. Applied Coupons (Discounts) -->
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex items-center justify-between text-emerald-600 font-medium bg-emerald-50/70 p-3 rounded-xl border border-emerald-100">
				<span class="flex items-center gap-1.5 text-xs sm:text-sm font-bold">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
					</svg>
					<span>كوبون (<?php wc_cart_totals_coupon_label( $coupon ); ?>):</span>
				</span>
				<span class="font-bold font-mono text-sm sm:text-base whitespace-nowrap">
					<?php wc_cart_totals_coupon_html( $coupon ); ?>
				</span>
			</div>
		<?php endforeach; ?>

		<!-- 3. Shipping -->
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
			<div class="pt-3 border-t border-slate-200/60 w-full">
				<div class="text-sm font-bold text-slate-800 mb-2 flex items-center justify-between">
					<span class="whitespace-nowrap">الشحن المقدر:</span>
					<span class="text-xs text-slate-500 font-normal">يُحسب حسب المحافظة</span>
				</div>
				<div class="ashrafco-shipping-box text-xs sm:text-sm text-slate-700 w-full">
					<?php wc_cart_totals_shipping_html(); ?>
				</div>
			</div>
			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
		<?php elseif ( WC()->cart->needs_shipping() ) : ?>
			<div class="flex items-center justify-between text-slate-700 text-sm font-bold pt-3 border-t border-slate-200/60">
				<span class="whitespace-nowrap">الشحن والتوصيل:</span>
				<span class="font-semibold text-slate-800 text-xs sm:text-sm">
					يتم حسابه عند إتمام الطلب
				</span>
			</div>
		<?php endif; ?>

		<!-- 4. Fees -->
		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="flex items-center justify-between text-slate-700 font-medium">
				<span class="whitespace-nowrap"><?php echo esc_html( $fee->name ); ?>:</span>
				<span class="font-bold text-slate-900 font-mono whitespace-nowrap">
					<?php wc_cart_totals_fee_html( $fee ); ?>
				</span>
			</div>
		<?php endforeach; ?>

		<!-- 5. Taxes -->
		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				/* translators: %s location. */
				$estimated_text = sprintf( ' <small>' . esc_html__( '(تقديري لـ %s)', 'ashrafco' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
					?>
					<div class="flex items-center justify-between text-slate-500 text-xs">
						<span><?php echo esc_html( $tax->label ) . $estimated_text; // PHPCS: XSS ok. ?>:</span>
						<span class="font-bold text-slate-800 font-mono whitespace-nowrap"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div class="flex items-center justify-between text-slate-500 text-xs">
					<span><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // PHPCS: XSS ok. ?>:</span>
					<span class="font-bold text-slate-800 font-mono whitespace-nowrap"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>

		<!-- 6. Final Order Total -->
		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
		<div class="pt-4 border-t-2 border-slate-200 flex items-baseline justify-between">
			<span class="text-base sm:text-lg font-extrabold text-slate-900 whitespace-nowrap">الإجمالي النهائي:</span>
			<span class="text-xl sm:text-2xl font-black text-emerald-600 font-mono whitespace-nowrap">
				<?php wc_cart_totals_order_total_html(); ?>
			</span>
		</div>
		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</div>

	<!-- High-Contrast Proceed to Checkout CTA Button -->
	<div class="wc-proceed-to-checkout pt-2">
		<a
			href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
			class="checkout-button button alt wc-forward w-full bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-black text-base py-4 px-6 rounded-2xl text-center block shadow-lg shadow-primary/25 transition-all duration-200 cursor-pointer select-none"
		>
			<span class="inline-flex items-center justify-center gap-2">
				<span>التقدم لإتمام الطلب</span>
				<svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
				</svg>
			</span>
		</a>
	</div>

	<!-- Trust Reassurance Note -->
	<div class="text-center pt-1">
		<p class="text-[11px] font-semibold text-slate-500 leading-normal">
			🛡️ شحن سريع لكافة محافظات مصر • إمكانية المعاينة قبل الاستلام والدفع
		</p>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
