<?php
/**
 * Checkout Logic, Egyptian Phone Validation & Order Tracking
 *
 * Implements:
 * - Egyptian mobile phone normalization & strict regex validation (/^01[0125][0-9]{8}$/).
 * - Checkout billing field configuration, Egyptian governorates mapping, and email decoupling.
 * - Multi-step checkout validation hooks and order processing safeguards.
 * - Persistent order meta synchronization for phone, notes, address IDs, and gateway references.
 * - Cash On Delivery (COD) programmatic defaults and session handling.
 * - Public guest order tracking lookup and structured receipt generation helpers.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * ============================================================================
 * 1. Egyptian Mobile Phone Normalization & Validation
 * ============================================================================
 */

if (!function_exists('ashrafco_normalize_egyptian_phone')) {
	/**
	 * Normalize Egyptian phone number string into clean 11-digit local format (01XXXXXXXXX).
	 * Converts Arabic-Indic digits, removes spaces/dashes, and strips international prefixes.
	 *
	 * @param string $phone Raw phone input.
	 * @return string Normalized 11-digit phone number or empty string.
	 */
	function ashrafco_normalize_egyptian_phone($phone) {
		if (empty($phone) || !is_string($phone)) {
			return '';
		}

		// 1. Convert Arabic-Indic numerals (٠-٩) to Latin digits (0-9)
		$arabic_indic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
		$latin_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$clean_phone  = str_replace($arabic_indic, $latin_digits, trim($phone));

		// 2. Strip all spaces, hyphens, parentheses, and non-numeric characters
		$clean_phone = preg_replace('/[^0-9]/', '', $clean_phone);

		// 3. Normalize international prefixes (+20, 0020, 20)
		if (strpos($clean_phone, '0020') === 0) {
			$clean_phone = '0' . substr($clean_phone, 4);
		} elseif (strpos($clean_phone, '20') === 0 && strlen($clean_phone) === 12) {
			$clean_phone = '0' . substr($clean_phone, 2);
		}

		// 4. If 10 digits starting with 1, prepend 0
		if (strlen($clean_phone) === 10 && substr($clean_phone, 0, 1) === '1') {
			$clean_phone = '0' . $clean_phone;
		}

		return $clean_phone;
	}
}

if (!function_exists('ashrafco_validate_egyptian_phone')) {
	/**
	 * Validate Egyptian mobile phone number during checkout process.
	 * Hooked to woocommerce_checkout_process.
	 */
	function ashrafco_validate_egyptian_phone() {
		$phone = isset($_POST['billing_phone']) ? sanitize_text_field(wp_unslash($_POST['billing_phone'])) : '';
		$clean_phone = ashrafco_normalize_egyptian_phone($phone);

		if (empty($clean_phone) || !preg_match('/^01[0125][0-9]{8}$/', $clean_phone)) {
			wc_add_notice(
				esc_html__('يرجى إدخال رقم هاتف محمول مصري صحيح يبدأ بـ 010 أو 011 أو 012 أو 015 ومكون من 11 رقماً (مثال: 01012345678).', 'ashrafco'),
				'error'
			);
		}
	}
	add_action('woocommerce_checkout_process', 'ashrafco_validate_egyptian_phone', 15);
}

if (!function_exists('test_theme_validate_egyptian_phone')) {
	function test_theme_validate_egyptian_phone() {
		ashrafco_validate_egyptian_phone();
	}
}

/**
 * ============================================================================
 * 2. Billing Fields Customization & Egyptian Governorates Mapping
 * ============================================================================
 */

if (!function_exists('ashrafco_simplify_checkout_fields')) {
	/**
	 * Configure and simplify WooCommerce checkout fields for the Egyptian storefront.
	 * Strictly retains only 5 required Arabic fields:
	 * 1. first_name (الاسم الأول)
	 * 2. last_name (اسم العائلة)
	 * 3. state (المحافظة)
	 * 4. address_1 (العنوان بالتفصيل)
	 * 5. phone (رقم الهاتف)
	 *
	 * @param array $fields Registered checkout fields.
	 * @return array Filtered checkout fields.
	 */
	function ashrafco_simplify_checkout_fields($fields) {
		// 1. Remove all shipping address fields completely (always ship to billing address)
		$fields['shipping'] = array();

		// 2. Unset all unnecessary billing fields
		unset(
			$fields['billing']['billing_company'],
			$fields['billing']['billing_country'],
			$fields['billing']['billing_address_2'],
			$fields['billing']['billing_city'],
			$fields['billing']['billing_postcode'],
			$fields['billing']['billing_email']
		);

		// 1. First Name
		$fields['billing']['billing_first_name'] = array(
			'label'        => esc_html__('الاسم الأول', 'ashrafco'),
			'placeholder'  => esc_html__('أدخل الاسم الأول', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-first'),
			'priority'     => 10,
			'autocomplete' => 'given-name',
			'type'         => 'text',
		);

		// 2. Last Name
		$fields['billing']['billing_last_name'] = array(
			'label'        => esc_html__('اسم العائلة', 'ashrafco'),
			'placeholder'  => esc_html__('أدخل اسم العائلة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-last'),
			'priority'     => 20,
			'autocomplete' => 'family-name',
			'type'         => 'text',
		);

		// 3. Governorate (State)
		$fields['billing']['billing_state'] = array(
			'label'        => esc_html__('المحافظة', 'ashrafco'),
			'placeholder'  => esc_html__('اختر المحافظة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-wide', 'address-field'),
			'priority'     => 30,
			'autocomplete' => 'address-level1',
			'type'         => 'state',
			'country'      => 'EG',
		);

		// 4. Detailed Address
		$fields['billing']['billing_address_1'] = array(
			'label'        => esc_html__('العنوان بالتفصيل', 'ashrafco'),
			'placeholder'  => esc_html__('اسم الشارع، رقم المبنى، الدور أو علامة مميزة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-wide', 'address-field'),
			'priority'     => 40,
			'autocomplete' => 'street-address',
			'type'         => 'text',
		);

		// 5. Phone Number
		$fields['billing']['billing_phone'] = array(
			'label'        => esc_html__('رقم الهاتف', 'ashrafco'),
			'placeholder'  => '010XXXXXXXX أو 011XXXXXXXX',
			'required'     => true,
			'class'        => array('form-row-wide'),
			'priority'     => 50,
			'autocomplete' => 'tel',
			'type'         => 'tel',
		);

		// Reorder strictly in billing array: first_name, last_name, state, address_1, phone
		$billing_keys = array('billing_first_name', 'billing_last_name', 'billing_state', 'billing_address_1', 'billing_phone');
		$ordered_billing = array();
		foreach ($billing_keys as $key) {
			if (isset($fields['billing'][$key])) {
				$ordered_billing[$key] = $fields['billing'][$key];
			}
		}
		$fields['billing'] = $ordered_billing;

		// Order Notes (order_comments).
		if (isset($fields['order']['order_comments'])) {
			$fields['order']['order_comments']['label']       = esc_html__('ملاحظات خاصة بالتوصيل', 'ashrafco');
			$fields['order']['order_comments']['placeholder'] = esc_html__('أي تعليمات خاصة بالتوصيل أو مواعيد الاستلام المناسبة (اختياري)', 'ashrafco');
			$fields['order']['order_comments']['required']    = false;
			$fields['order']['order_comments']['priority']    = 60;
		}

		return $fields;
	}
	add_filter('woocommerce_checkout_fields', 'ashrafco_simplify_checkout_fields', 9999);
}

if (!function_exists('test_theme_simplify_checkout_fields')) {
	function test_theme_simplify_checkout_fields($fields) {
		return ashrafco_simplify_checkout_fields($fields);
	}
}

if (!function_exists('ashrafco_custom_billing_fields')) {
	/**
	 * Filter raw billing fields collection to strictly retain only 5 fields in Arabic.
	 *
	 * @param array $fields Raw billing fields.
	 * @return array Filtered billing fields.
	 */
	function ashrafco_custom_billing_fields($fields) {
		unset(
			$fields['billing_company'],
			$fields['billing_country'],
			$fields['billing_address_2'],
			$fields['billing_city'],
			$fields['billing_postcode'],
			$fields['billing_email']
		);

		$fields['billing_first_name'] = array(
			'label'        => esc_html__('الاسم الأول', 'ashrafco'),
			'placeholder'  => esc_html__('أدخل الاسم الأول', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-first'),
			'priority'     => 10,
			'autocomplete' => 'given-name',
			'type'         => 'text',
		);

		$fields['billing_last_name'] = array(
			'label'        => esc_html__('اسم العائلة', 'ashrafco'),
			'placeholder'  => esc_html__('أدخل اسم العائلة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-last'),
			'priority'     => 20,
			'autocomplete' => 'family-name',
			'type'         => 'text',
		);

		$fields['billing_state'] = array(
			'label'        => esc_html__('المحافظة', 'ashrafco'),
			'placeholder'  => esc_html__('اختر المحافظة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-wide', 'address-field'),
			'priority'     => 30,
			'autocomplete' => 'address-level1',
			'type'         => 'state',
			'country'      => 'EG',
		);

		$fields['billing_address_1'] = array(
			'label'        => esc_html__('العنوان بالتفصيل', 'ashrafco'),
			'placeholder'  => esc_html__('اسم الشارع، رقم المبنى، الدور أو علامة مميزة', 'ashrafco'),
			'required'     => true,
			'class'        => array('form-row-wide', 'address-field'),
			'priority'     => 40,
			'autocomplete' => 'street-address',
			'type'         => 'text',
		);

		$fields['billing_phone'] = array(
			'label'        => esc_html__('رقم الهاتف', 'ashrafco'),
			'placeholder'  => '010XXXXXXXX أو 011XXXXXXXX',
			'required'     => true,
			'class'        => array('form-row-wide'),
			'priority'     => 50,
			'autocomplete' => 'tel',
			'type'         => 'tel',
		);

		$billing_keys = array('billing_first_name', 'billing_last_name', 'billing_state', 'billing_address_1', 'billing_phone');
		$ordered = array();
		foreach ($billing_keys as $key) {
			if (isset($fields[$key])) {
				$ordered[$key] = $fields[$key];
			}
		}
		return $ordered;
	}
	add_filter('woocommerce_billing_fields', 'ashrafco_custom_billing_fields', 9999);
}

if (!function_exists('ashrafco_custom_default_address_fields')) {
	/**
	 * Localize WooCommerce default address fields to Arabic and disable unneeded fields.
	 *
	 * @param array $fields Address fields.
	 * @return array
	 */
	function ashrafco_custom_default_address_fields($fields) {
		unset($fields['company'], $fields['address_2'], $fields['city'], $fields['postcode'], $fields['country']);

		if (isset($fields['first_name'])) {
			$fields['first_name']['label']       = esc_html__('الاسم الأول', 'ashrafco');
			$fields['first_name']['placeholder'] = esc_html__('أدخل الاسم الأول', 'ashrafco');
			$fields['first_name']['required']    = true;
			$fields['first_name']['priority']    = 10;
		}
		if (isset($fields['last_name'])) {
			$fields['last_name']['label']       = esc_html__('اسم العائلة', 'ashrafco');
			$fields['last_name']['placeholder'] = esc_html__('أدخل اسم العائلة', 'ashrafco');
			$fields['last_name']['required']    = true;
			$fields['last_name']['priority']    = 20;
		}
		if (isset($fields['state'])) {
			$fields['state']['label']       = esc_html__('المحافظة', 'ashrafco');
			$fields['state']['placeholder'] = esc_html__('اختر المحافظة', 'ashrafco');
			$fields['state']['required']    = true;
			$fields['state']['priority']    = 30;
		}
		if (isset($fields['address_1'])) {
			$fields['address_1']['label']       = esc_html__('العنوان بالتفصيل', 'ashrafco');
			$fields['address_1']['placeholder'] = esc_html__('اسم الشارع، رقم المبنى، الدور أو علامة مميزة', 'ashrafco');
			$fields['address_1']['required']    = true;
			$fields['address_1']['priority']    = 40;
		}
		return $fields;
	}
	add_filter('woocommerce_default_address_fields', 'ashrafco_custom_default_address_fields', 9999);
}

if (!function_exists('ashrafco_override_country_locale')) {
	/**
	 * Provide Egypt locale configuration to prevent WooCommerce core from inserting English strings.
	 *
	 * @param array $locales Locales list.
	 * @return array Filtered locales.
	 */
	function ashrafco_override_country_locale($locales) {
		$locales['EG'] = array(
			'state' => array(
				'label'       => esc_html__('المحافظة', 'ashrafco'),
				'placeholder' => esc_html__('اختر المحافظة', 'ashrafco'),
				'required'    => true,
				'priority'    => 30,
			),
			'address_1' => array(
				'label'       => esc_html__('العنوان بالتفصيل', 'ashrafco'),
				'placeholder' => esc_html__('اسم الشارع، رقم المبنى، الدور أو علامة مميزة', 'ashrafco'),
				'required'    => true,
				'priority'    => 40,
			),
			'city' => array(
				'hidden'   => true,
				'required' => false,
			),
			'postcode' => array(
				'hidden'   => true,
				'required' => false,
			),
			'address_2' => array(
				'hidden'   => true,
				'required' => false,
			),
		);
		return $locales;
	}
	add_filter('woocommerce_get_country_locale', 'ashrafco_override_country_locale', 9999);
}

// Enforce Egypt (EG) as absolute default country across WooCommerce checkout and customer session
add_filter('default_checkout_billing_country', function() { return 'EG'; }, 999);
add_filter('default_checkout_shipping_country', function() { return 'EG'; }, 999);
add_filter('woocommerce_customer_get_billing_country', function($country) { return 'EG'; }, 999);
add_filter('woocommerce_customer_get_shipping_country', function($country) { return 'EG'; }, 999);

// Dequeue wc-country-select script on checkout to prevent JS tampering with Arabic labels
add_action('wp_enqueue_scripts', function() {
	if (function_exists('is_checkout') && is_checkout()) {
		wp_dequeue_script('wc-country-select');
	}
}, 100);

if (!function_exists('ashrafco_egyptian_governorates')) {
	/**
	 * Provide localized Arabic names for all 27 Egyptian Governorates in checkout dropdowns.
	 *
	 * @param array $states List of states keyed by country.
	 * @return array Updated states list.
	 */
	function ashrafco_egyptian_governorates($states) {
		$states['EG'] = array(
			'EGC'   => 'القاهرة',
			'EGGZ'  => 'الجيزة',
			'EGALX' => 'الإسكندرية',
			'EGKB'  => 'القليوبية',
			'EGSHR' => 'الشرقية',
			'EGDK'  => 'الدقهلية',
			'EGBH'  => 'البحيرة',
			'EGGH'  => 'الغربية',
			'EGMNF' => 'المنوفية',
			'EGDT'  => 'دمياط',
			'EGKFS' => 'كفر الشيخ',
			'EGIS'  => 'الإسماعيلية',
			'EGSUZ' => 'السويس',
			'EGPTS' => 'بورسعيد',
			'EGBNS' => 'بني سويف',
			'EGFYM' => 'الفيوم',
			'EGMN'  => 'المنيا',
			'EGAST' => 'أسيوط',
			'EGSHG' => 'سوهاج',
			'EGKN'  => 'قنا',
			'EGLX'  => 'الأقصر',
			'EGASN' => 'أسوان',
			'EGBA'  => 'البحر الأحمر',
			'EGWAD' => 'الوادي الجديد',
			'EGMT'  => 'مطروح',
			'EGSIN' => 'شمال سيناء',
			'EGJS'  => 'جنوب سيناء',
		);
		return $states;
	}
	add_filter('woocommerce_states', 'ashrafco_egyptian_governorates', 20);
}

if (!function_exists('test_theme_egyptian_governorates')) {
	function test_theme_egyptian_governorates($states) {
		return ashrafco_egyptian_governorates($states);
	}
}

if (!function_exists('ashrafco_checkout_posted_data')) {
	/**
	 * Ensure posted checkout data enforces Egypt country and syncs address fields.
	 *
	 * @param array $data Posted checkout data.
	 * @return array Filtered data.
	 */
	function ashrafco_checkout_posted_data($data) {
		$data['billing_country']  = 'EG';
		$data['shipping_country'] = 'EG';

		// Sync shipping address fields with billing address
		$data['shipping_first_name'] = $data['billing_first_name'] ?? '';
		$data['shipping_last_name']  = $data['billing_last_name'] ?? '';
		$data['shipping_state']      = $data['billing_state'] ?? '';
		$data['shipping_address_1']  = $data['billing_address_1'] ?? '';
		$data['shipping_phone']      = $data['billing_phone'] ?? '';

		// If billing_city is missing, resolve to state name so internal WC systems have a valid city
		if (empty($data['billing_city']) && !empty($data['billing_state'])) {
			$states = function_exists('ashrafco_egyptian_governorates') ? ashrafco_egyptian_governorates(array()) : array();
			$state_name = $states['EG'][$data['billing_state']] ?? $data['billing_state'];
			$data['billing_city']  = $state_name;
			$data['shipping_city'] = $state_name;
		}

		if (isset($data['billing_email'])) {
			$raw_email = trim((string) $data['billing_email']);
			if ($raw_email !== '') {
				$sanitized = sanitize_email($raw_email);
				$data['billing_email'] = !empty($sanitized) ? $sanitized : $raw_email;
			} else {
				$data['billing_email'] = '';
			}
		} else {
			$data['billing_email'] = '';
		}

		return $data;
	}
	add_filter('woocommerce_checkout_posted_data', 'ashrafco_checkout_posted_data', 20);
}

if (!function_exists('test_theme_checkout_posted_data')) {
	function test_theme_checkout_posted_data($data) {
		return ashrafco_checkout_posted_data($data);
	}
}

if (!function_exists('ashrafco_checkout_order_button_text')) {
	/**
	 * Custom text for place-order checkout submit button.
	 *
	 * @param string $text Default order button text.
	 * @return string Customized Arabic button text.
	 */
	function ashrafco_checkout_order_button_text($text) {
		return esc_html__('تأكيد وإتمام الطلب الآن', 'ashrafco');
	}
	add_filter('woocommerce_order_button_text', 'ashrafco_checkout_order_button_text', 20);
}

if (!function_exists('test_theme_order_button_text')) {
	function test_theme_order_button_text($text) {
		return ashrafco_checkout_order_button_text($text);
	}
}

if (!function_exists('ashrafco_cleanup_thankyou_page')) {
	/**
	 * Remove duplicate order details table on custom thank you page.
	 */
	function ashrafco_cleanup_thankyou_page() {
		remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
	}
	add_action('init', 'ashrafco_cleanup_thankyou_page', 20);
}

if (!function_exists('test_theme_cleanup_thankyou_page')) {
	function test_theme_cleanup_thankyou_page() {
		ashrafco_cleanup_thankyou_page();
	}
}

/**
 * ============================================================================
 * 2.5 Checkout Shipping Methods & Order Notes Helpers
 * ============================================================================
 */

if (!function_exists('ashrafco_checkout_render_shipping_methods')) {
	/**
	 * Render available shipping methods in checkout step.
	 */
	function ashrafco_checkout_render_shipping_methods() {
		echo '<div class="ashrafco-shipping-methods">';

		if (!function_exists('WC') || !WC()->cart || !WC()->cart->needs_shipping() || !WC()->cart->show_shipping()) {
			echo '<div class="ashrafco-shipping-empty"><p>لا تتطلب هذه الطلبية شحناً. يمكنك المتابعة إلى الدفع.</p></div>';
			echo '</div>';
			return;
		}

		$shipping_manager = function_exists('ashrafco_shipping') ? ashrafco_shipping() : null;
		$govs             = $shipping_manager ? $shipping_manager->get_configured_governorates() : array();

		// Fallback to theme defined governorates if empty
		if (empty($govs) && function_exists('ashrafco_get_shipping_zones')) {
			$zones = ashrafco_get_shipping_zones();
			$states_names = function_exists('WC') && WC()->countries ? WC()->countries->get_states('EG') : array();
			foreach ($zones as $zone) {
				$price = $zone['base_rate'] ?? 50;
				foreach ($zone['states'] as $st_code) {
					$govs[$st_code] = array(
						'code'   => $st_code,
						'name'   => $states_names[$st_code] ?? $st_code,
						'price'  => $price,
						'active' => true,
					);
				}
			}
		}

		// Filter active governorates only
		$active_govs = array_filter($govs, function($g) {
			return !empty($g['active']);
		});

		if (empty($active_govs)) {
			echo '<div class="ashrafco-shipping-empty"><p>لا توجد محافظات متاحة للشحن حالياً. يرجى التواصل مع إدارة المتجر.</p></div>';
			echo '</div>';
			return;
		}

		// Determine currently chosen shipping rate
		$chosen_shipping_methods = function_exists('WC') && WC()->session ? WC()->session->get('chosen_shipping_methods', array()) : array();
		$current_chosen = !empty($chosen_shipping_methods[0]) ? $chosen_shipping_methods[0] : '';

		// Also check customer state to pre-select their governorate
		$customer_state = function_exists('WC') && WC()->customer ? WC()->customer->get_billing_state() : '';
		$customer_state = strtoupper(trim((string) $customer_state));

		if (empty($current_chosen) && $customer_state && isset($active_govs[$customer_state])) {
			$current_chosen = 'ashrafco_gov_' . strtolower($customer_state);
		}

		// If still not matched, default to the first active governorate
		$first_key = array_key_first($active_govs);
		if (empty($current_chosen) && $first_key) {
			$current_chosen = 'ashrafco_gov_' . strtolower($first_key);
		}

		// Ensure WooCommerce session holds the chosen shipping method
		if (function_exists('WC') && WC()->session && $current_chosen && (empty($chosen_shipping_methods[0]) || $chosen_shipping_methods[0] !== $current_chosen)) {
			WC()->session->set('chosen_shipping_methods', array($current_chosen));
		}

		// Free Shipping Progress Bar
		if (function_exists('ashrafco_render_free_shipping_bar')) {
			echo '<div class="ashrafco-checkout-free-shipping-wrap mb-4">';
			ashrafco_render_free_shipping_bar('cart');
			echo '</div>';
		}

		do_action('woocommerce_review_order_before_shipping');
		?>
		<table class="ashrafco-shipping-table">
			<tbody>
				<tr class="woocommerce-shipping-totals shipping">
					<td>
						<ul id="shipping_method" class="woocommerce-shipping-methods">
							<?php foreach ($active_govs as $code => $g) :
								$rate_id   = 'ashrafco_gov_' . strtolower($code);
								$price_val = (float) $g['price'];
								$is_checked = ($rate_id === $current_chosen);
								$input_id  = 'shipping_method_0_' . sanitize_title($rate_id);
								?>
								<li>
									<input type="radio" 
										name="shipping_method[0]" 
										data-index="0" 
										data-state-code="<?php echo esc_attr($code); ?>" 
										id="<?php echo esc_attr($input_id); ?>" 
										value="<?php echo esc_attr($rate_id); ?>" 
										class="shipping_method" 
										<?php checked($is_checked, true); ?> />
									<label for="<?php echo esc_attr($input_id); ?>">
										<span class="ashrafco-shipping-method__title"><?php echo esc_html($g['name']); ?></span>
										<span class="woocommerce-Price-amount amount">
											<bdi><?php echo esc_html(number_format_i18n((int) $price_val)); ?>&nbsp;<span class="woocommerce-Price-currencySymbol"><?php echo esc_html(function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : 'ج.م'); ?></span></bdi>
										</span>
									</label>
								</li>
							<?php endforeach; ?>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		do_action('woocommerce_review_order_after_shipping');

		echo '</div>';
	}
}

if (!function_exists('ashrafco_checkout_shipping_fragment')) {
	/**
	 * Refresh shipping methods container during checkout update AJAX.
	 */
	function ashrafco_checkout_shipping_fragment($fragments) {
		if (!function_exists('WC') || !WC()->cart) {
			return $fragments;
		}

		ob_start();
		ashrafco_checkout_render_shipping_methods();
		$fragments['.ashrafco-shipping-methods'] = ob_get_clean();

		return $fragments;
	}
	add_filter('woocommerce_update_order_review_fragments', 'ashrafco_checkout_shipping_fragment', 20);
}

if (!function_exists('ashrafco_sync_chosen_shipping_with_state')) {
	/**
	 * Synchronize customer state in session whenever shipping method changes via AJAX.
	 *
	 * @param string $post_data Serialized checkout post data.
	 */
	function ashrafco_sync_chosen_shipping_with_state($post_data) {
		if (empty($post_data) || !function_exists('WC') || !WC()->customer) {
			return;
		}

		parse_str($post_data, $parsed);
		if (!empty($parsed['shipping_method'][0])) {
			$rate = (string) $parsed['shipping_method'][0];
			if (strpos($rate, 'ashrafco_gov_') === 0) {
				$state_code = strtoupper(str_replace('ashrafco_gov_', '', $rate));
				if ($state_code) {
					WC()->customer->set_billing_state($state_code);
					WC()->customer->set_shipping_state($state_code);
				}
			}
		}
	}
	add_action('woocommerce_checkout_update_order_review', 'ashrafco_sync_chosen_shipping_with_state', 10);
}

if (!function_exists('ashrafco_checkout_validate_order_notes')) {
	/**
	 * Validate order notes maximum length.
	 */
	function ashrafco_checkout_validate_order_notes($data, $errors) {
		$notes = isset($data['order_comments']) ? (string) $data['order_comments'] : '';
		if (mb_strlen($notes) > 500) {
			$errors->add('ashrafco_order_notes_length', 'لا يمكن أن تتجاوز ملاحظات الطلب 500 حرف.');
		}
	}
	add_action('woocommerce_after_checkout_validation', 'ashrafco_checkout_validate_order_notes', 30, 2);
}

if (!function_exists('ashrafco_checkout_disable_default_order_notes')) {
	/**
	 * Disable default order notes field in order fields collection.
	 */
	function ashrafco_checkout_disable_default_order_notes() {
		return false;
	}
	add_filter('woocommerce_enable_order_notes_field', 'ashrafco_checkout_disable_default_order_notes', 20);
}

if (!function_exists('ashrafco_checkout_remove_default_coupon_form')) {
	/**
	 * Remove default coupon form from top of checkout page.
	 */
	function ashrafco_checkout_remove_default_coupon_form() {
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
	}
	add_action('init', 'ashrafco_checkout_remove_default_coupon_form', 20);
}

/**
 * ============================================================================
 * 3. Cash on Delivery (COD) Configuration & Session Handling
 * ============================================================================
 */

if (!function_exists('ashrafco_enable_cod_programmatically')) {
	/**
	 * Programmatically ensure Cash on Delivery (COD) is enabled with appropriate instructions.
	 */
	function ashrafco_enable_cod_programmatically() {
		if (!function_exists('WC')) {
			return;
		}
		$cod_settings = get_option('woocommerce_cod_settings', array());
		if (empty($cod_settings) || !isset($cod_settings['enabled']) || 'yes' !== $cod_settings['enabled']) {
			$cod_settings['enabled']            = 'yes';
			$cod_settings['title']              = !empty($cod_settings['title']) ? $cod_settings['title'] : 'الدفع عند الاستلام (COD)';
			$cod_settings['description']        = !empty($cod_settings['description']) ? $cod_settings['description'] : 'الدفع نقداً عند استلام الشحنة ومعاينة المعدات والأدوات.';
			$cod_settings['instructions']       = !empty($cod_settings['instructions']) ? $cod_settings['instructions'] : 'الدفع عند الاستلام متاح مع إمكانية معاينة المعدات والأدوات قبل دفع المبلغ للمندوب.';
			$cod_settings['enable_for_virtual'] = 'yes';
			update_option('woocommerce_cod_settings', $cod_settings);
		}
	}
	add_action('init', 'ashrafco_enable_cod_programmatically');
}

if (!function_exists('test_theme_enable_cod_programmatically')) {
	function test_theme_enable_cod_programmatically() {
		ashrafco_enable_cod_programmatically();
	}
}

if (!function_exists('ashrafco_default_payment_gateway')) {
	/**
	 * Set COD as default checkout payment method.
	 */
	function ashrafco_default_payment_gateway($default_gateway) {
		if (function_exists('WC') && WC()->payment_gateways) {
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			if (isset($available_gateways['cod'])) {
				return 'cod';
			}
		}
		return $default_gateway;
	}
	add_filter('default_checkout_payment_method', 'ashrafco_default_payment_gateway', 99);
}

if (!function_exists('test_theme_default_payment_gateway')) {
	function test_theme_default_payment_gateway($default_gateway) {
		return ashrafco_default_payment_gateway($default_gateway);
	}
}

if (!function_exists('ashrafco_set_default_payment_session')) {
	/**
	 * Ensure default payment method is saved in session.
	 */
	function ashrafco_set_default_payment_session() {
		if (!is_admin() && function_exists('is_checkout') && is_checkout() && !is_order_received_page()) {
			if (function_exists('WC') && WC()->session) {
				$chosen = WC()->session->get('chosen_payment_method');
				if (empty($chosen)) {
					WC()->session->set('chosen_payment_method', 'cod');
				}
			}
		}
	}
	add_action('template_redirect', 'ashrafco_set_default_payment_session', 20);
}

if (!function_exists('test_theme_set_default_payment_session')) {
	function test_theme_set_default_payment_session() {
		ashrafco_set_default_payment_session();
	}
}

if (!function_exists('ashrafco_reorder_payment_gateways')) {
	/**
	 * Reorder payment gateways so COD appears first.
	 */
	function ashrafco_reorder_payment_gateways($gateways) {
		if (isset($gateways['cod'])) {
			$cod = $gateways['cod'];
			unset($gateways['cod']);
			return array_merge(array('cod' => $cod), $gateways);
		}
		return $gateways;
	}
	add_filter('woocommerce_available_payment_gateways', 'ashrafco_reorder_payment_gateways', 20);
}

if (!function_exists('test_theme_reorder_payment_gateways')) {
	function test_theme_reorder_payment_gateways($gateways) {
		return ashrafco_reorder_payment_gateways($gateways);
	}
}

/**
 * ============================================================================
 * 4. Saving Custom Checkout Fields to Order Meta (HPOS & Post Meta Parity)
 * ============================================================================
 */

if (!function_exists('ashrafco_save_custom_checkout_order_meta')) {
	/**
	 * Synchronize custom checkout fields, normalized phone, notes, and address IDs to order metadata.
	 *
	 * @param int   $order_id Order ID.
	 * @param array $data     Posted checkout data.
	 */
	function ashrafco_save_custom_checkout_order_meta($order_id, $data = array()) {
		$order = wc_get_order($order_id);
		if (!$order) {
			return;
		}

		// 1. Normalize and persist billing phone
		$phone = isset($_POST['billing_phone']) ? sanitize_text_field(wp_unslash($_POST['billing_phone'])) : $order->get_billing_phone();
		$clean_phone = ashrafco_normalize_egyptian_phone($phone);

		if (!empty($clean_phone)) {
			$order->set_billing_phone($clean_phone);
			$order->update_meta_data('_billing_phone', $clean_phone);
			$order->update_meta_data('billing_phone', $clean_phone);
		}

		// 2. Persist customer delivery instructions if submitted
		if (isset($_POST['order_comments'])) {
			$notes = sanitize_textarea_field(wp_unslash($_POST['order_comments']));
			if (!empty($notes)) {
				$order->update_meta_data('_customer_delivery_notes', $notes);
			}
		}

		// 3. Save and commit order changes
		$order->save();
	}
	add_action('woocommerce_checkout_update_order_meta', 'ashrafco_save_custom_checkout_order_meta', 20, 2);
}

if (!function_exists('ashrafco_checkout_create_order_hook')) {
	/**
	 * Ensure custom fields are mapped during initial order creation hook.
	 *
	 * @param WC_Order $order New order object.
	 * @param array    $data  Posted checkout data.
	 */
	function ashrafco_checkout_create_order_hook($order, $data) {
		if (!$order instanceof \WC_Order) {
			return;
		}

		if (isset($data['billing_phone'])) {
			$clean_phone = ashrafco_normalize_egyptian_phone($data['billing_phone']);
			if (!empty($clean_phone)) {
				$order->set_billing_phone($clean_phone);
				$order->update_meta_data('_billing_phone', $clean_phone);
			}
		}
	}
	add_action('woocommerce_checkout_create_order', 'ashrafco_checkout_create_order_hook', 20, 2);
}

/**
 * ============================================================================
 * 5. Custom Order Tracking Logic
 * ============================================================================
 */

if (!function_exists('ashrafco_lookup_order_for_tracking')) {
	/**
	 * Perform a secure order lookup for the guest order tracking service.
	 * Verifies order existence and matches against registered customer phone.
	 *
	 * @param mixed  $raw_order_id Input Order ID.
	 * @param string $raw_phone    Input Mobile Phone.
	 * @return array Result array with status, order object or localized error message.
	 */
	function ashrafco_lookup_order_for_tracking($raw_order_id, $raw_phone) {
		$order_id    = absint(preg_replace('/[^0-9]/', '', (string) $raw_order_id));
		$clean_phone = ashrafco_normalize_egyptian_phone($raw_phone);

		if (empty($order_id) || empty($clean_phone)) {
			return array(
				'success' => false,
				'error'   => esc_html__('يرجى إدخال رقم الطلب ورقم الهاتف المسجل بشكل صحيح.', 'ashrafco'),
				'order'   => null,
			);
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			return array(
				'success' => false,
				'error'   => esc_html__('لم نتمكن من العثور على طلب بهذه البيانات، يرجى التأكد من رقم الطلب ورقم الهاتف.', 'ashrafco'),
				'order'   => null,
			);
		}

		// Normalize order phone
		$order_phone       = $order->get_billing_phone();
		$clean_order_phone = ashrafco_normalize_egyptian_phone($order_phone);

		// Verify phone match: exact match or last 9 digits match
		$matched = false;
		if ($clean_phone === $clean_order_phone) {
			$matched = true;
		} elseif (strlen($clean_phone) >= 9 && strlen($clean_order_phone) >= 9) {
			if (substr($clean_phone, -9) === substr($clean_order_phone, -9)) {
				$matched = true;
			}
		}

		if (!$matched) {
			return array(
				'success' => false,
				'error'   => esc_html__('لم نتمكن من العثور على طلب بهذه البيانات، يرجى التأكد من رقم الطلب ورقم الهاتف.', 'ashrafco'),
				'order'   => null,
			);
		}

		return array(
			'success' => true,
			'error'   => '',
			'order'   => $order,
		);
	}
}

if (!function_exists('ashrafco_get_order_tracking_steps')) {
	/**
	 * Retrieve structured status timeline steps for an order.
	 *
	 * @param WC_Order $order WooCommerce Order object.
	 * @return array Timeline steps array.
	 */
	function ashrafco_get_order_tracking_steps($order) {
		if (!$order instanceof \WC_Order) {
			return array();
		}

		$status = $order->get_status();
		$step   = 1;

		if ('processing' === $status) {
			$step = 2;
		} elseif ('shipped' === $status) {
			$step = 3;
		} elseif ('completed' === $status) {
			$step = 4;
		}

		$is_cancelled = ('cancelled' === $status);
		$is_completed = ('completed' === $status);

		$steps_definition = array(
			1 => array(
				'index'       => 1,
				'title'       => esc_html__('تم استلام الطلب', 'ashrafco'),
				'description' => esc_html__('تم تسجيل طلبك بنجاح بنظام المتجر.', 'ashrafco'),
				'completed'   => $step >= 1 && !$is_cancelled,
				'active'      => 1 === $step && !$is_cancelled,
			),
			2 => array(
				'index'       => 2,
				'title'       => esc_html__('قيد المعالجة', 'ashrafco'),
				'description' => esc_html__('يقوم فريقنا بفحص وتجهيز المعدات للشحن.', 'ashrafco'),
				'completed'   => $step >= 2 && !$is_cancelled,
				'active'      => 2 === $step && !$is_cancelled,
			),
			3 => array(
				'index'       => 3,
				'title'       => esc_html__('تم الشحن', 'ashrafco'),
				'description' => esc_html__('الشحنة في طريقها إليك مع مندوب التوصيل.', 'ashrafco'),
				'completed'   => $step >= 3 && !$is_cancelled,
				'active'      => 3 === $step && !$is_cancelled,
			),
			4 => array(
				'index'       => 4,
				'title'       => esc_html__('تم التسليم', 'ashrafco'),
				'description' => esc_html__('تم تسليم الشحنة للعميل بنجاح.', 'ashrafco'),
				'completed'   => $step >= 4 && !$is_cancelled,
				'active'      => 4 === $step && !$is_cancelled,
			),
		);

		$badge = function_exists('ashrafco_get_order_status_badge') ? ashrafco_get_order_status_badge($status) : null;
		$status_name = $badge ? $badge['label'] : wc_get_order_status_name($status);

		return array(
			'current_step' => $step,
			'status'       => $status,
			'status_name'  => $status_name,
			'badge'        => $badge,
			'is_cancelled' => $is_cancelled,
			'is_completed' => $is_completed,
			'steps'        => $steps_definition,
		);
	}
}

/**
 * ============================================================================
 * 6. Order Receipt Generation & WhatsApp Direct Link Helpers
 * ============================================================================
 */

if (!function_exists('ashrafco_generate_order_receipt_data')) {
	/**
	 * Generate a complete, sanitized data array suitable for generating customer receipts or invoices.
	 *
	 * @param WC_Order $order Order object.
	 * @return array Structured receipt data.
	 */
	function ashrafco_generate_order_receipt_data($order) {
		if (!$order instanceof \WC_Order) {
			return array();
		}

		$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
		$currency   = $order->get_currency();

		$items = array();
		foreach ($order->get_items() as $item_id => $item) {
			$product = $item->get_product();
			$items[] = array(
				'id'         => $item_id,
				'product_id' => $product ? $product->get_id() : 0,
				'name'       => $item->get_name(),
				'sku'        => $product ? $product->get_sku() : '',
				'quantity'   => $item->get_quantity(),
				'unit_price' => $item->get_quantity() > 0 ? ($order->get_line_subtotal($item, false, false) / $item->get_quantity()) : 0,
				'line_total' => (float) $item->get_total(),
				'formatted'  => wc_price($item->get_total(), array('currency' => $currency)),
			);
		}

		$status_slug = $order->get_status();
		$badge       = function_exists('ashrafco_get_order_status_badge') ? ashrafco_get_order_status_badge($status_slug) : null;
		$status_name = $badge ? $badge['label'] : wc_get_order_status_name($status_slug);

		return array(
			'order_id'       => $order->get_id(),
			'order_number'   => $order->get_order_number(),
			'store_name'     => $store_name,
			'status'         => $status_slug,
			'status_name'    => $status_name,
			'date_created'   => $order->get_date_created() ? $order->get_date_created()->date_i18n('Y-m-d H:i') : '',
			'payment_title'  => $order->get_payment_method_title() ?: esc_html__('الدفع عند الاستلام', 'ashrafco'),
			'subtotal'       => (float) $order->get_subtotal(),
			'shipping_total' => (float) $order->get_shipping_total(),
			'discount_total' => (float) $order->get_discount_total(),
			'total'          => (float) $order->get_total(),
			'formatted_total'=> wc_price($order->get_total(), array('currency' => $currency)),
			'customer'       => array(
				'name'    => trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()),
				'phone'   => $order->get_billing_phone(),
				'city'    => $order->get_billing_city(),
				'state'   => $order->get_billing_state(),
				'address' => $order->get_billing_address_1(),
			),
			'items'          => $items,
			'references'     => array(
				'fawry'  => $order->get_meta('_fawry_reference_number'),
				'paymob' => $order->get_meta('_paymob_transaction_id'),
			),
		);
	}
}

if (!function_exists('ashrafco_get_order_whatsapp_url')) {
	/**
	 * Generate a direct WhatsApp inquiry link for a specific order.
	 *
	 * @param WC_Order $order Order object.
	 * @return string WhatsApp URL.
	 */
	function ashrafco_get_order_whatsapp_url($order) {
		$raw_wa_phone   = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
		$clean_wa_phone = preg_replace('/\D/', '', (string) $raw_wa_phone);

		$order_number = $order instanceof \WC_Order ? $order->get_order_number() : '';
		$message      = sprintf(esc_html__('مرحباً، أود الاستفسار والمتابعة بخصوص طلبي رقم #%s', 'ashrafco'), $order_number);

		return 'https://wa.me/' . esc_attr($clean_wa_phone) . '?text=' . rawurlencode($message);
	}
}

/**
 * ============================================================================
 * 7. Checkout AJAX Coupon Handlers (Apply & Remove)
 * ============================================================================
 */

if (!function_exists('ashrafco_ajax_checkout_apply_coupon')) {
	/**
	 * AJAX handler for applying a coupon in the custom checkout review order panel.
	 */
	function ashrafco_ajax_checkout_apply_coupon() {
		check_ajax_referer('ashrafco_checkout_coupon', 'security');

		if (!function_exists('WC') || !WC()->cart) {
			wp_send_json_error(array('message' => esc_html__('سلة المشتريات غير متاحة حالياً.', 'ashrafco')));
		}

		$raw_code = isset($_POST['coupon_code']) ? sanitize_text_field(wp_unslash($_POST['coupon_code'])) : '';
		$code     = function_exists('wc_format_coupon_code') ? wc_format_coupon_code($raw_code) : sanitize_text_field(trim($raw_code));

		if (empty($code)) {
			wp_send_json_error(array('message' => esc_html__('يرجى إدخال كود الكوبون أولاً.', 'ashrafco')));
		}

		if (WC()->cart->has_discount($code)) {
			wp_send_json_error(array('message' => esc_html__('تم تطبيق هذا الكوبون بالفعل على طلبك.', 'ashrafco')));
		}

		// Clear previous notices before applying
		wc_clear_notices();

		$applied = WC()->cart->apply_coupon($code);

		if ($applied && WC()->cart->has_discount($code)) {
			if (class_exists('Ashrafco\Core\Orders\Buy_Now_Context') && \Ashrafco\Core\Orders\Buy_Now_Context::is_buy_now_request()) {
				$context = WC()->session->get(\Ashrafco\Core\Orders\Buy_Now_Context::SESSION_KEY);
				if (is_array($context)) {
					$context['applied_coupons'] = WC()->cart->get_applied_coupons();
					WC()->session->set(\Ashrafco\Core\Orders\Buy_Now_Context::SESSION_KEY, $context);
				}
			}
			wc_clear_notices();
			wp_send_json_success(array(
				'message' => esc_html__('تم تطبيق كود الخصم بنجاح!', 'ashrafco'),
				'coupon'  => $code,
			));
		}

		// Extract error message from WooCommerce notices if available
		$error_notices = wc_get_notices('error');
		$error_message = '';
		if (!empty($error_notices) && is_array($error_notices)) {
			$first_notice = reset($error_notices);
			if (is_array($first_notice) && isset($first_notice['notice'])) {
				$error_message = html_entity_decode(wp_strip_all_tags($first_notice['notice']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
			} elseif (is_string($first_notice)) {
				$error_message = html_entity_decode(wp_strip_all_tags($first_notice), ENT_QUOTES | ENT_HTML5, 'UTF-8');
			}
		}

		wc_clear_notices();

		if (empty($error_message)) {
			$error_message = esc_html__('كود الخصم غير صحيح أو غير متاح لهذا الطلب.', 'ashrafco');
		}

		wp_send_json_error(array('message' => $error_message));
	}
	add_action('wp_ajax_ashrafco_checkout_apply_coupon', 'ashrafco_ajax_checkout_apply_coupon');
	add_action('wp_ajax_nopriv_ashrafco_checkout_apply_coupon', 'ashrafco_ajax_checkout_apply_coupon');
}

if (!function_exists('ashrafco_ajax_checkout_remove_coupon')) {
	/**
	 * AJAX handler for removing an applied coupon in the checkout review order panel.
	 */
	function ashrafco_ajax_checkout_remove_coupon() {
		check_ajax_referer('ashrafco_checkout_coupon', 'security');

		if (!function_exists('WC') || !WC()->cart) {
			wp_send_json_error(array('message' => esc_html__('سلة المشتريات غير متاحة حالياً.', 'ashrafco')));
		}

		$raw_code = isset($_POST['coupon_code']) ? sanitize_text_field(wp_unslash($_POST['coupon_code'])) : '';
		$code     = function_exists('wc_format_coupon_code') ? wc_format_coupon_code($raw_code) : sanitize_text_field(trim($raw_code));

		if (empty($code)) {
			wp_send_json_error(array('message' => esc_html__('كود الكوبون غير محدد.', 'ashrafco')));
		}

		$removed = WC()->cart->remove_coupon($code);
		wc_clear_notices();

		if ($removed) {
			if (class_exists('Ashrafco\Core\Orders\Buy_Now_Context') && \Ashrafco\Core\Orders\Buy_Now_Context::is_buy_now_request()) {
				$context = WC()->session->get(\Ashrafco\Core\Orders\Buy_Now_Context::SESSION_KEY);
				if (is_array($context)) {
					$context['applied_coupons'] = WC()->cart->get_applied_coupons();
					WC()->session->set(\Ashrafco\Core\Orders\Buy_Now_Context::SESSION_KEY, $context);
				}
			}
			wp_send_json_success(array(
				'message' => esc_html__('تم حذف كود الخصم بنجاح.', 'ashrafco'),
				'coupon'  => $code,
			));
		}

		wp_send_json_error(array('message' => esc_html__('تعذر حذف الكوبون أو أنه غير مطبق.', 'ashrafco')));
	}
	add_action('wp_ajax_ashrafco_checkout_remove_coupon', 'ashrafco_ajax_checkout_remove_coupon');
	add_action('wp_ajax_nopriv_ashrafco_checkout_remove_coupon', 'ashrafco_ajax_checkout_remove_coupon');
}
