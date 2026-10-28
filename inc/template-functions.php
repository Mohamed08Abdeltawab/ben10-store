<?php
/**
 * Core Storefront Functions & Integrations
 *
 * Provides essential storefront backend business logic:
 * - Live AJAX search endpoint supporting legacy and standard hooks.
 * - WooCommerce Cart Fragments filter for instant mini-cart & header updates.
 * - Centralized wishlist retrieval, query, and template resolution helpers.
 * - Dynamic discount percentage calculations, sale badges, and currency formatters.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * ============================================================================
 * 1. Live AJAX Search Handlers
 * ============================================================================
 */

if (!function_exists('ashrafco_ajax_live_search')) {
	/**
	 * AJAX Live Search Handler for WooCommerce Products.
	 * Registered for both logged-in and guest users under both legacy and standard action names:
	 * - test_theme_ajax_search
	 * - live_search
	 */
	function ashrafco_ajax_live_search() {
		// Verify nonce: support test_theme_search_nonce, live_search_nonce, or ashrafco_search_nonce.
		$nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
		$valid_nonce = wp_verify_nonce($nonce, 'test_theme_search_nonce')
			|| wp_verify_nonce($nonce, 'live_search_nonce')
			|| wp_verify_nonce($nonce, 'ashrafco_search_nonce');

		if (!$valid_nonce && !empty($nonce)) {
			wp_send_json_error(array('message' => esc_html__('فشل التحقق الأمني للبحث.', 'ashrafco')), 403);
		}

		$term = isset($_REQUEST['term']) ? sanitize_text_field(wp_unslash($_REQUEST['term'])) : '';
		if (empty($term) && isset($_REQUEST['s'])) {
			$term = sanitize_text_field(wp_unslash($_REQUEST['s']));
		}

		if (empty($term)) {
			wp_send_json_success(array());
		}

		$tax_query = array();
		if (function_exists('wc_get_product_visibility_term_ids')) {
			$visibility_terms = wc_get_product_visibility_term_ids();
			if (!empty($visibility_terms['exclude-from-search'])) {
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array($visibility_terms['exclude-from-search']),
					'operator' => 'NOT IN',
				);
			}
			if ('yes' === get_option('woocommerce_hide_out_of_stock_items') && !empty($visibility_terms['outofstock'])) {
				$tax_query[] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => array($visibility_terms['outofstock']),
					'operator' => 'NOT IN',
				);
			}
		}

		$query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			's'              => $term,
			'posts_per_page' => 8,
			'no_found_rows'  => true,
		);

		if (!empty($tax_query)) {
			$query_args['tax_query'] = $tax_query;
		}

		$query   = new WP_Query($query_args);
		$results = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$product_id = get_the_ID();
				$product    = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
				$thumb_url  = get_the_post_thumbnail_url($product_id, 'thumbnail');

				if (!$thumb_url && function_exists('wc_placeholder_img_src')) {
					$thumb_url = wc_placeholder_img_src('thumbnail');
				}

				$results[] = array(
					'id'        => $product_id,
					'title'     => get_the_title(),
					'permalink' => get_permalink(),
					'thumbnail' => $thumb_url ?: '',
					'price'     => $product ? $product->get_price_html() : '',
				);
			}
			wp_reset_postdata();
		}

		wp_send_json_success($results);
	}
	add_action('wp_ajax_test_theme_ajax_search', 'ashrafco_ajax_live_search');
	add_action('wp_ajax_nopriv_test_theme_ajax_search', 'ashrafco_ajax_live_search');
	add_action('wp_ajax_live_search', 'ashrafco_ajax_live_search');
	add_action('wp_ajax_nopriv_live_search', 'ashrafco_ajax_live_search');
}

if (!function_exists('test_theme_ajax_search')) {
	function test_theme_ajax_search() {
		ashrafco_ajax_live_search();
	}
}

/**
 * ============================================================================
 * 2. WooCommerce Cart Fragments Filter
 * ============================================================================
 */

if (!function_exists('ashrafco_template_cart_fragments')) {
	/**
	 * Ensure Mini-Cart count, subtotal, and content update instantly via WooCommerce AJAX fragments.
	 *
	 * @param array $fragments Existing fragments.
	 * @return array Updated fragments.
	 */
	function ashrafco_template_cart_fragments($fragments) {
		if (!function_exists('WC') || !WC()->cart) {
			return $fragments;
		}

		$cart_count    = WC()->cart->get_cart_contents_count();
		$cart_subtotal = WC()->cart->get_cart_subtotal();

		// 1. Legacy #test-theme-cart-count
		ob_start();
		?>
		<span id="test-theme-cart-count"
			class="absolute -top-2 -right-2.5 bg-brand-500 text-white text-[11px] font-bold w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center shadow-sm">
			<?php echo esc_html($cart_count); ?>
		</span>
		<?php
		$fragments['#test-theme-cart-count'] = ob_get_clean();

		// 2. Legacy #test-theme-cart-subtotal
		ob_start();
		?>
		<span id="test-theme-cart-subtotal" class="text-xs sm:text-sm font-bold text-slate-800 hidden sm:inline">
			<?php echo wp_kses_post($cart_subtotal); ?>
		</span>
		<?php
		$fragments['#test-theme-cart-subtotal'] = ob_get_clean();

		// 3. Mini-cart dropdown content
		ob_start();
		?>
		<div class="test-theme-mini-cart-content">
			<?php
			if (function_exists('woocommerce_mini_cart')) {
				woocommerce_mini_cart();
			}
			?>
		</div>
		<?php
		$fragments['div.test-theme-mini-cart-content'] = ob_get_clean();

		// 4. Header Badge (#header-cart-count-badge)
		$fragments['#header-cart-count-badge'] = sprintf(
			'<span id="header-cart-count-badge" class="ashrafco-cart-count-badge absolute -top-1.5 -right-2 bg-[#ff171f] text-white text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs %s">%s</span>',
			$cart_count > 0 ? '' : 'hidden',
			esc_html($cart_count)
		);

		// 5. Mobile Nav Badge (#mobile-nav-cart-badge)
		$fragments['#mobile-nav-cart-badge'] = sprintf(
			'<span id="mobile-nav-cart-badge" class="ashrafco-cart-count-badge absolute -top-1.5 -right-2 bg-[#d90429] text-white text-[9px] font-black rounded-full w-4 h-4 flex items-center justify-center leading-none shadow-xs %s">%s</span>',
			$cart_count > 0 ? '' : 'hidden',
			esc_html($cart_count)
		);

		// 6. Generic class selector
		$fragments['.ashrafco-cart-items-count'] = '<span class="ashrafco-cart-items-count">' . esc_html($cart_count) . '</span>';

		return $fragments;
	}
	add_filter('woocommerce_add_to_cart_fragments', 'ashrafco_template_cart_fragments', 20);
}

if (!function_exists('test_theme_cart_fragments')) {
	function test_theme_cart_fragments($fragments) {
		return ashrafco_template_cart_fragments($fragments);
	}
}

/**
 * ============================================================================
 * 3. Custom Currency Formatting & Sale Percentage Calculation Badges
 * ============================================================================
 */

if (!function_exists('ashrafco_get_product_discount_data')) {
	/**
	 * Retrieve comprehensive pricing, discount, and sale countdown metadata for a product.
	 *
	 * @param WC_Product|int $product Product object or post ID.
	 * @return array Discount data array.
	 */
	function ashrafco_get_product_discount_data($product) {
		if (is_numeric($product)) {
			$product = wc_get_product($product);
		}
		if (!$product instanceof \WC_Product) {
			return array(
				'is_on_sale'          => false,
				'regular_price'       => 0.0,
				'sale_price'          => null,
				'current_price'       => 0.0,
				'discount_percent'    => 0,
				'discount_label'      => '',
				'has_countdown'       => false,
				'sale_end_timestamp'  => 0,
				'formatted_end_date'  => '',
			);
		}

		$is_on_sale       = $product->is_on_sale();
		$regular_price    = 0.0;
		$sale_price       = null;
		$current_price    = (float) $product->get_price();
		$discount_percent = 0;
		$discount_label   = '';
		$has_countdown    = false;
		$sale_end_ts      = 0;
		$formatted_end    = '';
		$now_ts           = time();

		if ($product->is_type('variable')) {
			$min_reg  = (float) $product->get_variation_regular_price('min', true);
			$max_reg  = (float) $product->get_variation_regular_price('max', true);
			$min_sale = (float) $product->get_variation_sale_price('min', true);
			$max_sale = (float) $product->get_variation_sale_price('max', true);

			$regular_price = $min_reg > 0 ? $min_reg : (float) $product->get_regular_price();
			$sale_price    = $min_sale > 0 ? $min_sale : (float) $product->get_sale_price();

			if ($is_on_sale && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
				$discount_percent = (int) round((($regular_price - $sale_price) / $regular_price) * 100);
				$discount_label   = sprintf(esc_html__('خصم %d%%', 'ashrafco'), $discount_percent);
			}

			if ($is_on_sale) {
				$variations   = $product->get_available_variations('objects');
				$earliest_end = 0;
				foreach ($variations as $variation) {
					if ($variation->is_on_sale()) {
						$var_date_to = $variation->get_date_on_sale_to();
						if ($var_date_to instanceof \WC_DateTime) {
							$v_ts = $var_date_to->getTimestamp();
							if ($v_ts > $now_ts && (0 === $earliest_end || $v_ts < $earliest_end)) {
								$earliest_end  = $v_ts;
								$formatted_end = $var_date_to->date('Y-m-d');
							}
						}
					}
				}
				if ($earliest_end > $now_ts) {
					$has_countdown = true;
					$sale_end_ts   = $earliest_end;
				}
			}
		} else {
			$regular_price = (float) $product->get_regular_price();
			$sale_price    = (float) $product->get_sale_price();

			if ($is_on_sale && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
				$discount_percent = (int) round((($regular_price - $sale_price) / $regular_price) * 100);
				$discount_label   = sprintf(esc_html__('خصم %d%%', 'ashrafco'), $discount_percent);
			}

			if ($is_on_sale) {
				$date_to = $product->get_date_on_sale_to();
				if ($date_to instanceof \WC_DateTime) {
					$ts = $date_to->getTimestamp();
					if ($ts > $now_ts) {
						$has_countdown = true;
						$sale_end_ts   = $ts;
						$formatted_end = $date_to->date('Y-m-d');
					}
				}
			}
		}

		return array(
			'is_on_sale'          => $is_on_sale && $discount_percent > 0,
			'regular_price'       => $regular_price,
			'sale_price'          => $sale_price,
			'current_price'       => $current_price,
			'discount_percent'    => $discount_percent,
			'discount_label'      => $discount_label,
			'has_countdown'       => $has_countdown,
			'sale_end_timestamp'  => $sale_end_ts,
			'formatted_end_date'  => $formatted_end,
		);
	}
}

if (!function_exists('ashrafco_format_sale_badge')) {
	/**
	 * Format and return a stylized sale percentage badge for WooCommerce product cards.
	 *
	 * @param WC_Product|int $product Product object or ID.
	 * @param string         $classes Additional CSS utility classes.
	 * @return string HTML badge markup or empty string.
	 */
	function ashrafco_format_sale_badge($product, $classes = '') {
		$data       = function_exists('ashrafco_get_product_discount_data') ? ashrafco_get_product_discount_data($product) : array();
		$is_on_sale = !empty($data['is_on_sale']);
		$percent    = !empty($data['discount_percent']) ? absint($data['discount_percent']) : 0;

		if (!$is_on_sale) {
			if (is_numeric($product)) {
				$product = wc_get_product($product);
			}
			if ($product instanceof \WC_Product) {
				$is_on_sale = $product->is_on_sale();
				if ($is_on_sale && $percent <= 0) {
					$reg = (float) $product->get_regular_price();
					$cur = (float) $product->get_price();
					if ($reg > $cur && $reg > 0) {
						$percent = (int) round((($reg - $cur) / $reg) * 100);
					}
				}
			}
		}

		if (!$is_on_sale) {
			return '';
		}

		$badge_classes = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-[#d90429] text-white shadow-2xs font-mono ' . esc_attr($classes);
		if ($percent > 0) {
			return sprintf(
				'<span class="%s" dir="rtl"><span dir="ltr">-%d%%</span> %s</span>',
				$badge_classes,
				$percent,
				esc_html__('خصم', 'ashrafco')
			);
		}

		return sprintf(
			'<span class="%s" dir="rtl">%s</span>',
			$badge_classes,
			esc_html__('تخفيض', 'ashrafco')
		);
	}
}

if (!function_exists('ashrafco_custom_sale_flash')) {
	/**
	 * Override standard WooCommerce sale flash to output clean Arabic sale badge without truncation or Mojibake.
	 *
	 * @param string      $html    Default WooCommerce sale flash HTML.
	 * @param WP_Post     $post    Post object.
	 * @param WC_Product  $product WooCommerce product object.
	 * @return string Filtered clean Arabic badge HTML.
	 */
	function ashrafco_custom_sale_flash($html, $post = null, $product = null) {
		if ($product instanceof \WC_Product) {
			$badge = ashrafco_format_sale_badge($product);
			if (!empty($badge)) {
				return $badge;
			}
		}
		return '<span class="onsale" dir="rtl">' . esc_html__('تخفيض', 'ashrafco') . '</span>';
	}
	add_filter('woocommerce_sale_flash', 'ashrafco_custom_sale_flash', 10, 3);
}

if (!function_exists('ashrafco_format_currency')) {
	/**
	 * Format currency amount using standardized WooCommerce formatting with Arabic currency symbols.
	 *
	 * @param float $amount Amount to format.
	 * @param array $args   Optional wc_price arguments.
	 * @return string Formatted price HTML.
	 */
	function ashrafco_format_currency($amount, $args = array()) {
		if (!function_exists('wc_price')) {
			return number_format_i18n((float) $amount, 2) . ' ج.م';
		}
		return wc_price($amount, $args);
	}
}

if (!function_exists('ashrafco_filter_free_shipping_label_arabic')) {
	/**
	 * Filter Free Shipping label to bold green "مجاني" across WooCommerce cart and checkout tables.
	 *
	 * @param string $label Shipping label.
	 * @return string Filtered label with styled Arabic text.
	 */
	function ashrafco_filter_free_shipping_label_arabic($label) {
		if (stripos($label, 'free') !== false || stripos($label, 'مجاني') !== false) {
			return '<span class="ashrafco-free-shipping text-emerald-600 font-extrabold">' . esc_html__('مجاني', 'ashrafco') . '</span>';
		}
		return $label;
	}
	add_filter('woocommerce_cart_shipping_total', 'ashrafco_filter_free_shipping_label_arabic', 999);
	add_filter('woocommerce_order_shipping_to_display', 'ashrafco_filter_free_shipping_label_arabic', 999);
}

if (!function_exists('ashrafco_translate_free_shipping_word')) {
	/**
	 * Translate the standalone word "Free" or "Free shipping" into Arabic in WooCommerce domain.
	 *
	 * @param string $translation Current translation.
	 * @param string $text        Original text.
	 * @param string $domain      Textdomain.
	 * @return string Filtered translation.
	 */
	function ashrafco_translate_free_shipping_word($translation, $text, $domain) {
		if ('woocommerce' === $domain) {
			if ('Free!' === $text || 'Free' === $text || 'Free shipping' === $text) {
				return '<span class="ashrafco-free-shipping text-emerald-600 font-extrabold">' . esc_html__('مجاني', 'ashrafco') . '</span>';
			}
		}
		return $translation;
	}
	add_filter('gettext', 'ashrafco_translate_free_shipping_word', 999, 3);
}

if (!function_exists('ashrafco_get_social_login_url')) {
	/**
	 * Get Social Login / Registration URL with safe fallback and plugin support.
	 *
	 * @param string $provider 'google'|'facebook'
	 * @param string $context  'login'|'register'
	 * @return string Filtered URL or '#' fallback.
	 */
	function ashrafco_get_social_login_url($provider = 'google', $context = 'login') {
		$url = '#';

		// Nextend Social Login integration
		if (class_exists('NextendSocialLogin', false)) {
			$provider_id = ('google' === strtolower($provider)) ? 'google' : 'facebook';
			if (method_exists('NextendSocialLogin', 'isProviderEnabled') && NextendSocialLogin::isProviderEnabled($provider_id)) {
				$provider_instance = NextendSocialLogin::getProvider($provider_id);
				if ($provider_instance && method_exists($provider_instance, 'getLoginUrl')) {
					$url = $provider_instance->getLoginUrl();
				}
			}
		}

		// Super Socializer integration
		if ('#' === $url && function_exists('the_champ_login_button')) {
			$url = home_url('/?loginSocial=' . sanitize_key($provider));
		}

		return apply_filters('ashrafco_social_login_url', $url, $provider, $context);
	}
}

