<?php
/**
 * Checkout billing information form
 *
 * Overrides WooCommerce default form-billing.php.
 * Renders the simplified Egyptian customer address form:
 * 1. First Name (الاسم الأول)
 * 2. Last Name (اسم العائلة)
 * 3. Governorate (المحافظة)
 * 4. Detailed Address (العنوان بالتفصيل)
 * 5. Mobile Phone (رقم الهاتف)
 *
 * @package Ashrafco
 * @version 12.0.0
 */

defined('ABSPATH') || exit;
?>
<div class="woocommerce-billing-fields">
	<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

	<!-- Hidden country fixed to Egypt (EG) -->
	<input type="hidden" name="billing_country" id="billing_country" value="EG" autocomplete="country" />
	<input type="hidden" name="shipping_country" id="shipping_country" value="EG" autocomplete="country" />

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields('billing');

		foreach ($fields as $key => $field) {
			woocommerce_form_field($key, $field, $checkout->get_value($key));
		}
		?>
	</div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>
