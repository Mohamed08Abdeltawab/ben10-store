<?php
/**
 * Ashrafco Shipping & Fulfillment Presentation & Legacy Compatibility
 *
 * Presentation layer for Free Shipping Progress Bar and fallback bridges
 * delegating to ashrafco-core (Ashrafco\Core\Shipping\Shipping_Manager).
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Free Shipping Threshold (Compatibility Wrapper)
 *
 * Returns the minimum order subtotal (in EGP) required to qualify for free shipping.
 * Filterable via 'ashrafco_free_shipping_threshold'. Default: 5000 EGP.
 *
 * @return float
 */
if ( ! function_exists( 'ashrafco_get_free_shipping_threshold' ) ) {
	function ashrafco_get_free_shipping_threshold() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->get_free_shipping_threshold();
		}
		return (float) apply_filters( 'ashrafco_free_shipping_threshold', 5000 );
	}
}

/**
 * 2. Egyptian Shipping Zones Configuration (Compatibility Wrapper)
 *
 * Configures the 3 programmatic shipping zones for Egypt:
 * - Zone 1: Greater Cairo, Giza & Qalyubia (Standard local rate).
 * - Zone 2: Delta Governorates & Canal Cities (+ Alexandria).
 * - Zone 3: Upper Egypt (Sa'id) & Frontier Governorates.
 *
 * @return array
 */
if ( ! function_exists( 'ashrafco_get_shipping_zones' ) ) {
	function ashrafco_get_shipping_zones() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->get_shipping_zones();
		}

		$zones = array(
			'zone_1' => array(
				'id'            => 'zone_1',
				'name'          => 'القاهرة الكبرى والجيزة',
				'short_name'    => 'القاهرة والجيزة',
				'label'         => 'القاهرة الكبرى، الجيزة، والقليوبية',
				'desc'          => 'توصيل محلي سريع لباب الورشة أو موقع العمل خلال 24 - 48 ساعة',
				'delivery_time' => 'خلال 24 إلى 48 ساعة',
				'base_rate'     => (float) apply_filters( 'ashrafco_shipping_zone_1_rate', 50 ),
				'states'        => apply_filters(
					'ashrafco_shipping_zone_1_states',
					array( 'EGC', 'EGGZ', 'EGKB' )
				),
			),
			'zone_2' => array(
				'id'            => 'zone_2',
				'name'          => 'محافظات الدلتا ومدن القناة',
				'short_name'    => 'الدلتا ومدن القناة',
				'label'         => 'محافظات الدلتا، مدن القناة، والإسكندرية',
				'desc'          => 'شحن منتظم ومؤمّن لكافة محافظات الوجه البحري ومدن القناة',
				'delivery_time' => 'خلال 2 إلى 3 أيام عمل',
				'base_rate'     => (float) apply_filters( 'ashrafco_shipping_zone_2_rate', 75 ),
				'states'        => apply_filters(
					'ashrafco_shipping_zone_2_states',
					array(
						'EGALX', // الإسكندرية
						'EGSHR', // الشرقية
						'EGDK',  // الدقهلية
						'EGBH',  // البحيرة
						'EGGH',  // الغربية
						'EGMNF', // المنوفية
						'EGDT',  // دمياط
						'EGKFS', // كفر الشيخ
						'EGIS',  // الإسماعيلية
						'EGSUZ', // السويس
						'EGPTS', // بورسعيد
					)
				),
			),
			'zone_3' => array(
				'id'            => 'zone_3',
				'name'          => 'الصعيد والمحافظات الحدودية',
				'short_name'    => 'الصعيد والحدود',
				'label'         => 'محافظات الصعيد والوجه القبلي والمحافظات الحدودية',
				'desc'          => 'شحن معزز ومحمي للأدوات الثقيلة ومعدات البورسلين لمحافظات الصعيد وسيناء والبحر الأحمر',
				'delivery_time' => 'خلال 3 إلى 5 أيام عمل',
				'base_rate'     => (float) apply_filters( 'ashrafco_shipping_zone_3_rate', 100 ),
				'states'        => apply_filters(
					'ashrafco_shipping_zone_3_states',
					array(
						'EGBNS', // بني سويف
						'EGFYM', // الفيوم
						'EGMN',  // المنيا
						'EGAST', // أسيوط
						'EGSHG', // سوهاج
						'EGKN',  // قنا
						'EGLX',  // الأقصر
						'EGASN', // أسوان
						'EGBA',  // البحر الأحمر
						'EGWAD', // الوادي الجديد
						'EGMT',  // مطروح
						'EGSIN', // شمال سيناء
						'EGJS',  // جنوب سيناء
					)
				),
			),
		);

		return apply_filters( 'ashrafco_shipping_zones', $zones );
	}
}

/**
 * 3. Match Customer Shipping Zone (Compatibility Wrapper)
 *
 * Resolves customer's zone according to their governorate code.
 *
 * @param string $state_code Optional Egyptian state code (e.g. 'EGC').
 * @return array Zone configuration array.
 */
if ( ! function_exists( 'ashrafco_get_customer_shipping_zone' ) ) {
	function ashrafco_get_customer_shipping_zone( $state_code = '' ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->get_customer_shipping_zone( $state_code );
		}

		if ( empty( $state_code ) && function_exists( 'WC' ) && WC()->customer ) {
			$state_code = WC()->customer->get_shipping_state() ?: WC()->customer->get_billing_state();
		}

		if ( empty( $state_code ) ) {
			$state_code = 'EGC'; // Default to Cairo
		}

		$zones = ashrafco_get_shipping_zones();
		foreach ( $zones as $zone_id => $zone ) {
			if ( in_array( $state_code, $zone['states'], true ) ) {
				return $zone;
			}
		}

		// Default fallback to Zone 1
		return $zones['zone_1'];
	}
}

/**
 * 4. Free Shipping Progress Calculations (Compatibility Wrapper)
 *
 * Computes remaining amount, percentage, and qualification status.
 *
 * @param float|null $subtotal Optional subtotal value to calculate against.
 * @return array Detailed progress calculation array.
 */
if ( ! function_exists( 'ashrafco_get_free_shipping_progress' ) ) {
	function ashrafco_get_free_shipping_progress( $subtotal = null ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->get_free_shipping_progress( $subtotal );
		}

		$threshold = ashrafco_get_free_shipping_threshold();

		if ( null === $subtotal ) {
			if ( function_exists( 'WC' ) && WC()->cart ) {
				$subtotal = (float) WC()->cart->get_displayed_subtotal();
				if ( $subtotal <= 0 ) {
					$subtotal = (float) WC()->cart->get_subtotal();
				}
			} else {
				$subtotal = 0.0;
			}
		}

		$remaining = max( 0.0, $threshold - $subtotal );
		$percent   = $threshold > 0 ? min( 100, max( 0, (int) round( ( $subtotal / $threshold ) * 100 ) ) ) : 100;
		$is_free   = ( $subtotal >= $threshold );

		return array(
			'threshold'           => $threshold,
			'current'             => $subtotal,
			'remaining'           => $remaining,
			'percent'             => $percent,
			'is_free'             => $is_free,
			'formatted_remaining' => function_exists( 'number_format_i18n' ) ? number_format_i18n( (int) ceil( $remaining ) ) : number_format( ceil( $remaining ) ),
			'formatted_threshold' => function_exists( 'number_format_i18n' ) ? number_format_i18n( (int) $threshold ) : number_format( $threshold ),
			'formatted_current'   => function_exists( 'number_format_i18n' ) ? number_format_i18n( (int) $subtotal ) : number_format( $subtotal ),
		);
	}
}

/**
 * 5. Free Shipping Progress Bar Component (Theme Presentation Layer)
 *
 * Renders high-conversion Cairo RTL progress bar in Cart and Mini-Cart.
 *
 * @param string $context 'cart' or 'mini-cart'.
 * @return void
 */
function ashrafco_render_free_shipping_bar( $context = 'cart' ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}

	$progress = ashrafco_get_free_shipping_progress();
	$is_mini  = ( 'mini-cart' === $context );

	if ( $is_mini ) :
		// Mini-Cart Compact Layout
		?>
		<div class="ashrafco-free-shipping-bar ashrafco-free-shipping-mini mb-3.5 <?php echo $progress['is_free'] ? 'bg-emerald-50/80 border-emerald-200' : 'bg-slate-50 border-slate-200/90'; ?> border rounded-2xl p-3 shadow-2xs font-sans transition-all" dir="rtl">
			<div class="flex items-center justify-between gap-2 mb-2">
				<div class="flex items-center gap-2 min-w-0">
					<span class="w-6 h-6 rounded-lg <?php echo $progress['is_free'] ? 'bg-emerald-100 text-emerald-600' : 'bg-orange-100 text-primary'; ?> flex items-center justify-center flex-shrink-0">
						<?php if ( $progress['is_free'] ) : ?>
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
							</svg>
						<?php else : ?>
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
							</svg>
						<?php endif; ?>
					</span>
					<div class="text-[11px] leading-tight">
						<?php if ( $progress['is_free'] ) : ?>
							<span class="font-extrabold text-emerald-800">
								تهانينا! لقد حصلت على شحن مجاني بالكامل
							</span>
						<?php else : ?>
							<span class="font-bold text-slate-700">
								أضف بـ <strong class="text-primary font-black font-mono"><?php echo esc_html( $progress['formatted_remaining'] ); ?> ج.م</strong> للشحن المجاني
							</span>
						<?php endif; ?>
					</div>
				</div>
				<span class="text-[10px] font-black <?php echo $progress['is_free'] ? 'text-emerald-700 bg-emerald-100/80' : 'text-slate-600 bg-slate-200/70'; ?> px-2 py-0.5 rounded-full font-mono flex-shrink-0">
					<?php echo esc_html( $progress['percent'] ); ?>%
				</span>
			</div>

			<!-- Visual Progress Track -->
			<div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden relative shadow-inner">
				<div
					class="h-full rounded-full transition-all duration-500 ease-out <?php echo $progress['is_free'] ? 'bg-gradient-to-l from-emerald-500 to-teal-500' : 'bg-gradient-to-l from-orange-500 to-amber-500'; ?>"
					style="width: <?php echo esc_attr( $progress['percent'] ); ?>%;"
					role="progressbar"
					aria-valuenow="<?php echo esc_attr( $progress['percent'] ); ?>"
					aria-valuemin="0"
					aria-valuemax="100"
				></div>
			</div>
		</div>
		<?php
	else :
		// Cart Page Full-Featured Banner
		?>
		<div class="ashrafco-free-shipping-bar ashrafco-cart-shipping-bar <?php echo $progress['is_free'] ? 'bg-gradient-to-r from-emerald-50/90 via-teal-50/50 to-emerald-50/90 border-2 border-emerald-300/80 shadow-emerald-500/5' : 'bg-gradient-to-r from-orange-50/80 via-amber-50/50 to-orange-50/80 border border-orange-200/90 shadow-orange-500/5'; ?> rounded-3xl p-5 sm:p-6 mb-6 shadow-sm font-sans transition-all relative overflow-hidden" dir="rtl">
			
			<!-- Decorative Background Glow -->
			<div class="absolute -top-10 -left-10 w-36 h-36 <?php echo $progress['is_free'] ? 'bg-emerald-400/10' : 'bg-orange-400/10'; ?> rounded-full blur-2xl pointer-events-none"></div>

			<div class="relative z-10">
				<!-- Header Row -->
				<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
					<div class="flex items-center gap-3.5">
						<span class="w-12 h-12 rounded-2xl <?php echo $progress['is_free'] ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-orange-500 text-white shadow-md shadow-orange-500/30'; ?> flex items-center justify-center flex-shrink-0">
							<?php if ( $progress['is_free'] ) : ?>
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
								</svg>
							<?php else : ?>
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
								</svg>
							<?php endif; ?>
						</span>
						<div>
							<?php if ( $progress['is_free'] ) : ?>
								<h3 class="text-base sm:text-lg font-black text-emerald-950 flex items-center gap-2 m-0">
									<span>تهانينا! لقد حصلت على شحن مجاني بالكامل</span>
									<span class="inline-block animate-bounce">🚚</span>
								</h3>
								<p class="text-xs sm:text-sm text-emerald-700/90 mt-1 m-0">
									طلبك مؤهل للاستفادة من ميزة التوصيل المجاني إلى باب الورشة أو موقع العمل بكافة محافظات مصر.
								</p>
							<?php else : ?>
								<h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-snug m-0">
									أضف بـ <span class="text-primary font-black font-mono text-lg sm:text-xl px-1"><?php echo esc_html( $progress['formatted_remaining'] ); ?> ج.م</span> إضافية للحصول على شحن مجاني لكافة المحافظات
								</h3>
								<p class="text-xs sm:text-sm text-slate-500 mt-1 m-0">
									شحن مجاني فوري عند وصول مشترياتك إلى <strong class="text-slate-700 font-mono"><?php echo esc_html( $progress['formatted_threshold'] ); ?> ج.م</strong>.
								</p>
							<?php endif; ?>
						</div>
					</div>

					<!-- Percent Badge -->
					<div class="self-end sm:self-center">
						<span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black font-mono <?php echo $progress['is_free'] ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-orange-100 text-orange-800 border border-orange-200'; ?> shadow-xs">
							<span><?php echo esc_html( $progress['percent'] ); ?>%</span>
							<span class="text-[10px] font-sans font-bold"><?php echo $progress['is_free'] ? 'مكتمل' : 'من الحد'; ?></span>
						</span>
					</div>
				</div>

				<!-- Visual Progress Bar Track with Smooth Animation -->
				<div class="relative w-full h-3 bg-slate-200/90 rounded-full overflow-hidden shadow-inner p-0.5">
					<div
						class="h-full rounded-full transition-all duration-700 ease-out <?php echo $progress['is_free'] ? 'bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 shadow-sm shadow-emerald-500/40' : 'bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600 shadow-sm shadow-orange-500/30'; ?>"
						style="width: <?php echo esc_attr( $progress['percent'] ); ?>%;"
						role="progressbar"
						aria-valuenow="<?php echo esc_attr( $progress['percent'] ); ?>"
						aria-valuemin="0"
						aria-valuemax="100"
					></div>
				</div>

				<!-- Zones Trust Footer Notice -->
				<div class="flex items-center justify-between flex-wrap gap-2 text-[11px] text-slate-500 font-medium pt-3 mt-1">
					<span class="flex items-center gap-1">
						<span>⚡ القاهرة الكبرى والجيزة (24-48 ساعة)</span>
					</span>
					<span class="hidden md:inline text-slate-300">•</span>
					<span class="flex items-center gap-1">
						<span>🌊 الدلتا ومدن القناة (2-3 أيام)</span>
					</span>
					<span class="hidden md:inline text-slate-300">•</span>
					<span class="flex items-center gap-1">
						<span>🏛️ محافظات الصعيد والحدود (3-5 أيام)</span>
					</span>
				</div>
			</div>

		</div>
		<?php
	endif;
}

/**
 * 6. Dynamic WooCommerce Shipping Rates & Threshold Logic Hook (Compatibility Wrapper)
 *
 * @param array $rates Existing calculated shipping rates.
 * @param array $package Package details.
 * @return array Modified shipping rates.
 */
if ( ! function_exists( 'ashrafco_apply_shipping_zone_rates' ) ) {
	function ashrafco_apply_shipping_zone_rates( $rates, $package ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->apply_package_rates( $rates, $package );
		}

		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return $rates;
		}

		$threshold = ashrafco_get_free_shipping_threshold();
		$subtotal  = (float) WC()->cart->get_displayed_subtotal();
		if ( $subtotal <= 0 ) {
			$subtotal = (float) WC()->cart->get_subtotal();
		}

		$is_free_eligible = ( $subtotal >= $threshold );

		if ( $is_free_eligible ) {
			$free_rate_key = '';
			foreach ( $rates as $rate_id => $rate ) {
				if ( 'free_shipping' === $rate->method_id || 0.0 === (float) $rate->cost ) {
					$free_rate_key = $rate_id;
					break;
				}
			}

			if ( ! empty( $free_rate_key ) ) {
				if ( apply_filters( 'ashrafco_hide_shipping_when_free', true ) ) {
					return array( $free_rate_key => $rates[ $free_rate_key ] );
				}
			} else {
				$free_shipping_title = apply_filters(
					'ashrafco_free_shipping_label',
					'شحن مجاني لكافة المحافظات (طلب أكثر من ' . number_format_i18n( (int) $threshold ) . ' ج.م)'
				);

				$free_rate = new WC_Shipping_Rate(
					'ashrafco_free_shipping',
					$free_shipping_title,
					0,
					array(),
					'free_shipping'
				);

				if ( apply_filters( 'ashrafco_hide_shipping_when_free', true ) ) {
					return array( 'ashrafco_free_shipping' => $free_rate );
				} else {
					$rates['ashrafco_free_shipping'] = $free_rate;
				}
			}
		} else {
			if ( class_exists( 'WC_Shipping_Rate' ) ) {
				$destination_state = isset( $package['destination']['state'] ) ? strtoupper( trim( $package['destination']['state'] ) ) : '';
				$all_govs          = function_exists( 'ashrafco_shipping' ) && ashrafco_shipping() ? ashrafco_shipping()->get_configured_governorates() : array();

				if ( empty( $all_govs ) ) {
					$zones = ashrafco_get_shipping_zones();
					$states_names = function_exists( 'WC' ) && WC()->countries ? WC()->countries->get_states( 'EG' ) : array();
					foreach ( $zones as $zone ) {
						$price = $zone['base_rate'] ?? 50;
						foreach ( $zone['states'] as $st_code ) {
							$all_govs[ $st_code ] = array(
								'code'   => $st_code,
								'name'   => $states_names[ $st_code ] ?? $st_code,
								'price'  => $price,
								'active' => true,
							);
						}
					}
				}

				$gov_rates = array();
				foreach ( $all_govs as $code => $g ) {
					if ( empty( $g['active'] ) ) {
						continue;
					}
					$rate_id    = 'ashrafco_gov_' . strtolower( $code );
					$rate_cost  = (float) $g['price'];
					$rate_title = sprintf( 'شحن إلى %s', $g['name'] );

					$gov_rates[ $rate_id ] = new WC_Shipping_Rate(
						$rate_id,
						$rate_title,
						$rate_cost,
						array(),
						'flat_rate'
					);
				}

				if ( ! empty( $gov_rates ) ) {
					return $gov_rates;
				}
			}
		}

		return $rates;
	}
}

/**
 * 7. Live AJAX Fragment Support for Cart Progress Bar (Compatibility Wrapper)
 *
 * @param array $fragments Cart fragments array.
 * @return array
 */
if ( ! function_exists( 'ashrafco_free_shipping_cart_fragments' ) ) {
	function ashrafco_free_shipping_cart_fragments( $fragments ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_shipping() ) {
			return ashrafco_core()->get_shipping()->cart_fragments( $fragments );
		}

		if ( function_exists( 'ashrafco_render_free_shipping_bar' ) ) {
			ob_start();
			ashrafco_render_free_shipping_bar( 'cart' );
			$fragments['div.ashrafco-cart-shipping-bar'] = ob_get_clean();
		}
		return $fragments;
	}
}

/**
 * 8. Hook Registration for Regional Shipping
 *
 * If ashrafco-core is active, shipping package rates and cart fragments are
 * hooked canonically by the plugin (Ashrafco\Core\Shipping\Shipping_Manager).
 * The theme only registers these filters as a legacy fallback if ashrafco-core is absent.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	add_filter( 'woocommerce_package_rates', 'ashrafco_apply_shipping_zone_rates', 100, 2 );
	add_filter( 'woocommerce_add_to_cart_fragments', 'ashrafco_free_shipping_cart_fragments', 25 );
}
