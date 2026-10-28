<?php
/**
 * Checkout Payment Section
 *
 * @package Ashrafco
 * @version 8.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment ashrafco-checkout-payment-wrap">

	<?php if ( WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods ashrafco-payment-methods">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . esc_html( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'للأسف، لا توجد طرق دفع متاحة لمنطقتك.', 'ashrafco' ) : esc_html__( 'يرجى إكمال تفاصيل العنوان أولاً لعرض طرق الدفع.', 'ashrafco' ) ) ) . '</li>';
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order ashrafco-place-order-wrap">
		<noscript>
			<?php
			/* translators: $1 and $2 opening and closing emphasis tags respectively */
			printf( esc_html__( 'بما أن متصفحك لا يدعم JavaScript، يرجى الضغط على زر %1$sتحديث المجاميع%2$s قبل إتمام الطلب.', 'ashrafco' ), '<em>', '</em>' );
			?>
			<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'تحديث المجاميع', 'ashrafco' ); ?>"><?php esc_html_e( 'تحديث المجاميع', 'ashrafco' ); ?></button>
		</noscript>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<button type="submit" 
			class="button alt ashrafco-btn-place-order" 
			name="woocommerce_checkout_place_order" 
			id="place_order" 
			value="تأكيد الطلب الآن" 
			data-value="تأكيد الطلب الآن">
			<span>تأكيد الطلب الآن</span>
			<svg class="ashrafco-btn-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5a2.25 2.25 0 0 1 2.25 2.25v6a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
			</svg>
		</button>

		<div class="ashrafco-payment-step__secure">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 0h10.5a2.25 2.25 0 0 1 2.25 2.25v6a2.25 2.25 0 0 1-2.25 2.25H6.75a2.25 2.25 0 0 1-2.25-2.25v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
			</svg>
			<span>بياناتك محمية ومشفرة. لن يتم خصم أي مبلغ قبل تأكيد الطلب.</span>
		</div>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
?>
