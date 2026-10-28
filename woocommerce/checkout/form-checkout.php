<?php
/**
 * Checkout form with customer, shipping and payment steps.
 *
 * @package Ashrafco
 * @version 11.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('يجب تسجيل الدخول أولاً لإتمام الطلب.', 'ashrafco')));
	return;
}

$is_logged_in = is_user_logged_in();
$current_user = $is_logged_in ? wp_get_current_user() : null;
$order_notes = (string) $checkout->get_value('order_comments');
$cart_count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout ashrafco-checkout font-sans"
	action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" dir="rtl">

	<div class="ashrafco-checkout__inner">

		<header class="ashrafco-checkout__header">
			<div class="ashrafco-checkout__heading">
				<span class="ashrafco-checkout__heading-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
						<path stroke-linecap="round" stroke-linejoin="round"
							d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.714Z" />
					</svg>
				</span>
				<div>
					<h1>إتمام الطلب</h1>
					<p>بيانات آمنة، خطوات واضحة، وتوصيل سريع لباب منزلك.</p>
				</div>
			</div>
		</header>

		<div class="ashrafco-checkout__layout">

			<div class="ashrafco-checkout__content">

				<section class="ashrafco-panel ashrafco-step-panel" data-ashrafco-panel="customer"
					aria-labelledby="ashrafco-customer-title">
					<div class="ashrafco-panel__header">
						<span class="ashrafco-panel__icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />
							</svg>
						</span>
						<div>
							<h2 id="ashrafco-customer-title">بيانات العميل</h2>
							<p>سنستخدم هذه البيانات لتأكيد الطلب والتواصل معك.</p>
						</div>
					</div>

					<div class="ashrafco-step-error" role="alert" hidden data-ashrafco-step-error="customer"></div>

					<?php if ($checkout->get_checkout_fields()): ?>
						<?php do_action('woocommerce_checkout_before_customer_details'); ?>
						<div id="customer_details" class="ashrafco-checkout-fields">
							<?php do_action('woocommerce_checkout_billing'); ?>
						</div>
						<?php do_action('woocommerce_checkout_after_customer_details'); ?>
					<?php endif; ?>

					<div class="ashrafco-notes">
						<div class="ashrafco-notes__header">
							<div>
								<h3>ملاحظات الطلب <span>(اختياري)</span></h3>
								<p>أي تعليمات خاصة بالتوصيل أو مواعيد مناسبة.</p>
							</div>
							<span class="ashrafco-notes__count" data-ashrafco-notes-count>0 / 500</span>
						</div>
						<textarea name="order_comments" id="order_comments" maxlength="500" rows="4"
							placeholder="اكتب ملاحظاتك هنا..."
							data-ashrafco-notes><?php echo esc_textarea($order_notes); ?></textarea>
					</div>

					<!-- Shipping Methods Section -->
					<div class="ashrafco-checkout-section ashrafco-checkout-section--shipping">
						<div class="ashrafco-checkout-section__header">
							<div>
								<h3>الشحن والتوصيل</h3>
								<p>اختر طريقة الشحن المناسبة لك.</p>
							</div>
						</div>
						<div class="ashrafco-shipping-step" data-ashrafco-shipping-target>
							<?php ashrafco_checkout_render_shipping_methods(); ?>
						</div>
					</div>

					<!-- Payment Methods & Place Order Section -->
					<div class="ashrafco-checkout-section ashrafco-checkout-section--payment">
						<div class="ashrafco-checkout-section__header ashrafco-checkout-section__header--payment">
							<div class="ashrafco-payment-header-flex">
								<div>
									<h3>الدفع</h3>
									<p>اختر طريقة الدفع ثم أكد الطلب.</p>
								</div>
								<span class="ashrafco-payment-header-badge" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
										<rect x="2" y="5" width="20" height="14" rx="3" />
										<path d="M2 10h20M6 15h4" stroke-linecap="round" />
									</svg>
								</span>
							</div>
						</div>
						<div class="ashrafco-payment-step" data-ashrafco-payment-target>
							<?php woocommerce_checkout_payment(); ?>
						</div>
					</div>
				</section>

			</div>

			<aside class="ashrafco-checkout__sidebar">
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action('woocommerce_checkout_before_order_review'); ?>
					<?php woocommerce_order_review(); ?>
					<?php do_action('woocommerce_checkout_after_order_review'); ?>
				</div>
			</aside>
		</div>

	</div>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>