<?php
/**
 * Checkout order review / summary.
 *
 * @package Ashrafco
 * @version 11.0.0
 */

defined('ABSPATH') || exit;

$cart = WC()->cart;
?>

<div class="woocommerce-checkout-review-order-table ashrafco-summary-card">
	<div class="ashrafco-summary-card__header">
		<div class="ashrafco-summary-card__title">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round"
					d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1.2 12H3.8L5 9Z" />
			</svg>
			<h2>ملخص الطلب</h2>
		</div>
		<span class="ashrafco-summary-card__count">
			<?php echo esc_html(sprintf('%d منتجات', $cart ? $cart->get_cart_contents_count() : 0)); ?>
		</span>
	</div>

	<?php if (class_exists('Ashrafco\Core\Orders\Buy_Now_Context') && \Ashrafco\Core\Orders\Buy_Now_Context::is_buy_now_request()): ?>
		<div class="ashrafco-buy-now-banner p-3 mb-3 bg-amber-50/90 border border-amber-200/80 rounded-xl text-xs font-medium text-amber-900 flex items-center justify-between gap-2">
			<div class="flex items-center gap-1.5">
				<svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
					<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
				</svg>
				<span>طلب فوري مباشر (سلتك السابقة محفوظة بأمان)</span>
			</div>
			<a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="text-amber-800 underline font-bold hover:text-amber-950 flex-shrink-0">عرض السلة</a>
		</div>
	<?php endif; ?>

	<div class="ashrafco-summary-items">
		<?php
		do_action('woocommerce_review_order_before_cart_contents');

		if ($cart) {
			foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

				if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
					$thumbnail = $_product->get_image(
						array(72, 72),
						array('class' => 'ashrafco-summary-item__image', 'loading' => 'lazy')
					);
					?>
					<div
						class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?> ashrafco-summary-item">
						<div class="ashrafco-summary-item__thumb">
							<?php echo wp_kses_post($thumbnail); ?>
							<span class="ashrafco-summary-item__qty" aria-label="<?php esc_attr_e('الكمية', 'ashrafco'); ?>">
								<?php echo esc_html($cart_item['quantity']); ?>
							</span>
						</div>
						<div class="ashrafco-summary-item__info">
							<p class="ashrafco-summary-item__name">
								<?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
							</p>
							<?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<div class="ashrafco-summary-item__price">
							<?php echo apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
					<?php
				}
			}
		}

		do_action('woocommerce_review_order_after_cart_contents');
		?>
	</div>

	<?php if (wc_coupons_enabled()): ?>
		<div class="ashrafco-coupon" data-ashrafco-coupon>
			<div class="ashrafco-coupon__field">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round"
						d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 0 1 0 2.828l-7 7a2 2 0 0 1-2.828 0l-7-7A1.994 1.994 0 0 1 3 12V7a4 4 0 0 1 4-4Z" />
				</svg>
				<input type="text" id="ashrafco_coupon_code" name="ashrafco_coupon_code" placeholder="أدخل كود الخصم"
					autocomplete="off" aria-label="كود الخصم" />
			</div>
			<button type="button" class="ashrafco-coupon__apply" data-ashrafco-apply-coupon>تطبيق</button>
			<p class="ashrafco-coupon__error" role="alert" hidden data-ashrafco-coupon-error></p>
		</div>
	<?php endif; ?>

	<table class="ashrafco-summary-totals">
		<tbody>
			<tr class="cart-subtotal">
				<th>المجموع الفرعي</th>
				<td><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php foreach ($cart ? $cart->get_coupons() : array() as $code => $coupon): ?>
				<tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
					<th>
						<span class="ashrafco-summary-coupon-label">
							كوبون: <?php echo esc_html($coupon->get_code()); ?>
						</span>
					</th>
					<td>
						<span class="ashrafco-summary-coupon-value">
							<?php
							$discount_amount = $cart->get_coupon_discount_amount($coupon->get_code(), $cart->display_cart_ex_tax);
							if ($coupon->get_free_shipping() && empty($discount_amount)) {
								echo esc_html__('شحن مجاني', 'ashrafco');
							} else {
								echo wp_kses_post('-' . wc_price($discount_amount));
							}
							?>
						</span>
						<button type="button" class="ashrafco-summary-coupon-remove" data-ashrafco-remove-coupon
							data-coupon="<?php echo esc_attr($coupon->get_code()); ?>"
							aria-label="<?php echo esc_attr(sprintf('حذف كوبون %s', $coupon->get_code())); ?>">حذف</button>
					</td>
				</tr>
			<?php endforeach; ?>

			<?php if ($cart && $cart->needs_shipping() && $cart->show_shipping()): ?>
				<?php do_action('woocommerce_review_order_before_shipping'); ?>
				<tr class="woocommerce-shipping-totals shipping">
					<th>مصاريف الشحن</th>
					<td>
						<?php
						$shipping_html = $cart->get_cart_shipping_total();
						if ( stripos( $shipping_html, 'free' ) !== false || 0.0 === (float) $cart->get_shipping_total() ) {
							echo '<span class="ashrafco-free-shipping text-emerald-600 font-bold" >مجاني</span>';
						} else {
							echo wp_kses_post( $shipping_html );
						}
						?>
					</td>
				</tr>
				<?php do_action('woocommerce_review_order_after_shipping'); ?>
			<?php endif; ?>

			<?php foreach ($cart ? $cart->get_fees() : array() as $fee): ?>
				<tr class="fee">
					<th><?php echo esc_html($fee->name); ?></th>
					<td><?php wc_cart_totals_fee_html($fee); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php if (wc_tax_enabled() && $cart && !$cart->display_prices_including_tax()): ?>
				<?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
					<?php foreach ($cart->get_tax_totals() as $code => $tax): ?>
						<tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
							<th><?php echo esc_html($tax->label); ?></th>
							<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr class="tax-total">
						<th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
						<td><?php wc_cart_totals_taxes_total_html(); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>

			<?php do_action('woocommerce_review_order_before_order_total'); ?>

			<tr class="order-total">
				<th>الإجمالي النهائي</th>
				<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action('woocommerce_review_order_after_order_total'); ?>
		</tbody>
	</table>

	<div class="ashrafco-summary-trust">
		<span>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
			</svg>
			معاينة قبل الاستلام
		</span>
		<span>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
			</svg>
			استبدال خلال 14 يوماً
		</span>
		<span>
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5" />
			</svg>
			توصيل لكل المحافظات
		</span>
	</div>
</div>