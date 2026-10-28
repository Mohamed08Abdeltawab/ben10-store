<?php
/**
 * Custom Payment Gateways for Ashrafco Checkout:
 * - Cash on Delivery (Customized for inspection before payment)
 * - Vodafone Cash (with copyable number, WhatsApp receipt confirmation)
 * - InstaPay (with copyable IPA & phone number, WhatsApp confirmation)
 *
 * Enforces ONLY these three payment methods on checkout.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * 1. Register Custom Payment Gateways (Vodafone Cash & InstaPay)
 */
function ashrafco_register_custom_payment_gateways($gateways)
{
	if (!class_exists('WC_Payment_Gateway')) {
		return $gateways;
	}

	/**
	 * Vodafone Cash Gateway
	 */
	if (!class_exists('WC_Gateway_Ashrafco_Vodafone_Cash')) {
		class WC_Gateway_Ashrafco_Vodafone_Cash extends WC_Payment_Gateway
		{

			public function __construct()
			{
				$this->id = 'vodafone_cash';
				$this->icon = '';
				$this->has_fields = true;
				$this->method_title = __('فودافون كاش (Vodafone Cash)', 'ashrafco');
				$this->method_description = __('الدفع عبر تحويل فودافون كاش مع إرسال إيصال التحويل عبر واتساب.', 'ashrafco');
				$this->title = __('الدفع عبر فودافون كاش', 'ashrafco');
				$this->description = __('حوّل المبلغ على رقم فودافون كاش التالي ثم اضغط على تأكيد الطلب.', 'ashrafco');

				$this->init_form_fields();
				$this->init_settings();

				$this->enabled = $this->get_option('enabled', 'yes');

				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			}

			public function init_form_fields()
			{
				$this->form_fields = array(
					'enabled' => array(
						'title' => __('تفعيل/تعطيل', 'ashrafco'),
						'type' => 'checkbox',
						'label' => __('تفعيل الدفع عبر فودافون كاش', 'ashrafco'),
						'default' => 'yes',
					),
					'phone_number' => array(
						'title' => __('رقم فودافون كاش', 'ashrafco'),
						'type' => 'text',
						'description' => __('رقم المحفظة الذي سيقوم العميل بالتحويل إليه.', 'ashrafco'),
						'default' => '201065712366',
					),
				);
			}

			public function process_payment($order_id)
			{
				$order = wc_get_order($order_id);
				if (!$order) {
					return array(
						'result' => 'failure',
						'redirect' => '',
					);
				}

				// Mark as processing (awaiting receipt verification)
				$vf_phone = ashrafco_get_vodafone_cash_number();
				$order->update_status(
					'processing',
					sprintf(__('طلب عبر فودافون كاش: بانتظار تحويل المبلغ على الرقم (%s) وإرسال إيصال التحويل عبر واتساب.', 'ashrafco'), $vf_phone)
				);

				$order->add_order_note(__('طلب قيد المراجعة: بانتظار استلام إيصال فودافون كاش عبر واتساب.', 'ashrafco'));

				// Reduce stock levels
				wc_reduce_stock_levels($order_id);

				// Clear cart
				WC()->cart->empty_cart();

				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url($order),
				);
			}
		}
	}

	/**
	 * InstaPay Gateway
	 */
	if (!class_exists('WC_Gateway_Ashrafco_InstaPay')) {
		class WC_Gateway_Ashrafco_InstaPay extends WC_Payment_Gateway
		{

			public function __construct()
			{
				$this->id = 'instapay';
				$this->icon = '';
				$this->has_fields = true;
				$this->method_title = __('إنستا باي (InstaPay)', 'ashrafco');
				$this->method_description = __('الدفع عبر تحويل إنستا باي المباشر مع إرسال إيصال التحويل عبر واتساب.', 'ashrafco');
				$this->title = __('الدفع عبر إنستا باي', 'ashrafco');
				$this->description = __('حوّل المبلغ على حساب إنستا باي التالي ثم اضغط على تأكيد الطلب.', 'ashrafco');

				$this->init_form_fields();
				$this->init_settings();

				$this->enabled = $this->get_option('enabled', 'yes');

				add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
			}

			public function init_form_fields()
			{
				$this->form_fields = array(
					'enabled' => array(
						'title' => __('تفعيل/تعطيل', 'ashrafco'),
						'type' => 'checkbox',
						'label' => __('تفعيل الدفع عبر إنستا باي', 'ashrafco'),
						'default' => 'yes',
					),
					'account_address' => array(
						'title' => __('عنوان الحساب (IPA)', 'ashrafco'),
						'type' => 'text',
						'description' => __('معرف إنستا باي للتحويل (مثال: Ashrafco@instapay).', 'ashrafco'),
						'default' => 'Ashrafco@instapay',
					),
					'phone_number' => array(
						'title' => __('رقم الهاتف المسجل', 'ashrafco'),
						'type' => 'text',
						'description' => __('رقم الهاتف المسجل على إنستا باي.', 'ashrafco'),
						'default' => '201065712366',
					),
				);
			}

			public function process_payment($order_id)
			{
				$order = wc_get_order($order_id);
				if (!$order) {
					return array(
						'result' => 'failure',
						'redirect' => '',
					);
				}

				// Mark as processing (awaiting receipt verification)
				$ipa_address = ashrafco_get_instapay_address();
				$order->update_status(
					'processing',
					sprintf(__('طلب عبر إنستا باي: بانتظار تحويل المبلغ إلى (%s) وإرسال إيصال التحويل عبر واتساب.', 'ashrafco'), $ipa_address)
				);

				$order->add_order_note(__('طلب قيد المراجعة: بانتظار استلام إيصال إنستا باي عبر واتساب.', 'ashrafco'));

				// Reduce stock levels
				wc_reduce_stock_levels($order_id);

				// Clear cart
				WC()->cart->empty_cart();

				return array(
					'result' => 'success',
					'redirect' => $this->get_return_url($order),
				);
			}
		}
	}

	$gateways[] = 'WC_Gateway_Ashrafco_Vodafone_Cash';
	$gateways[] = 'WC_Gateway_Ashrafco_InstaPay';

	return $gateways;
}
add_filter('woocommerce_payment_gateways', 'ashrafco_register_custom_payment_gateways', 10);

/**
 * 2. Helper functions to get payment details
 */
function ashrafco_get_vodafone_cash_number()
{
	return (string) apply_filters('ashrafco_vodafone_cash_number', '201065712366');
}

function ashrafco_get_instapay_address()
{
	return (string) apply_filters('ashrafco_instapay_address', 'Ashrafco@instapay');
}

function ashrafco_get_instapay_phone()
{
	return (string) apply_filters('ashrafco_instapay_phone', '201065712366');
}

function ashrafco_get_payment_whatsapp_url($method = 'vodafone_cash')
{
	$phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : (string) apply_filters('ashrafco_whatsapp_phone', '201065712366');
	$phone = preg_replace('/[^0-9]/', '', $phone);
	if (strpos($phone, '01') === 0) {
		$phone = '2' . $phone;
	}

	if ('instapay' === $method) {
		$message = 'مرحباً أشرفكو، أود إرسال صورة إيصال الدفع عبر إنستا باي للطلب.';
	} else {
		$message = 'مرحباً أشرفكو، أود إرسال صورة إيصال الدفع عبر فودافون كاش للطلب.';
	}

	return 'https://wa.me/' . esc_attr($phone) . '?text=' . rawurlencode($message);
}

/**
 * 3. Enforce ONLY these 3 payment gateways on Checkout in the exact order:
 * 1. cod (الدفع عند الاستلام)
 * 2. vodafone_cash (الدفع عبر فودافون كاش)
 * 3. instapay (الدفع عبر إنستا باي)
 */
function ashrafco_strictly_limit_available_payment_gateways($gateways)
{
	// If in admin or not checkout, don't restrict admin settings
	if (is_admin() && !wp_doing_ajax()) {
		return $gateways;
	}

	$all_gateways = function_exists('WC') && WC()->payment_gateways() ? WC()->payment_gateways()->payment_gateways() : array();
	$allowed_order = array('cod', 'vodafone_cash', 'instapay');
	$filtered = array();

	foreach ($allowed_order as $gateway_id) {
		$target_gateway = null;
		if (isset($gateways[$gateway_id])) {
			$target_gateway = $gateways[$gateway_id];
		} elseif (isset($all_gateways[$gateway_id])) {
			$target_gateway = $all_gateways[$gateway_id];
			$target_gateway->enabled = 'yes';
		}

		if ($target_gateway) {
			if ('cod' === $gateway_id) {
				$target_gateway->title = __('الدفع عند الاستلام (معاينة قبل الدفع)', 'ashrafco');
				$target_gateway->description = __('ادفع نقداً عند استلام الطلب من مندوب التوصيل بعد معاينة المنتجات.', 'ashrafco');
				$target_gateway->enabled = 'yes';
			}
			$filtered[$gateway_id] = $target_gateway;
		}
	}

	return $filtered;
}
add_filter('woocommerce_available_payment_gateways', 'ashrafco_strictly_limit_available_payment_gateways', 999);

/**
 * Remove standard English privacy policy text from checkout.
 */
add_filter('woocommerce_get_privacy_policy_text', '__return_empty_string', 999);
remove_action('woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20);
