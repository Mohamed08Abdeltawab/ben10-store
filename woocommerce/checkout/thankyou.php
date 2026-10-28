<?php
/**
 * Clean & Simple "Order Received / Thank You" Page Template for Ashrafco
 *
 * Override of WooCommerce default checkout/thankyou.php template.
 * 100% Arabic labels with crisp black text on action buttons.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 9.1.0
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order max-w-2xl mx-auto my-8 md:my-12 px-4 font-sans" dir="rtl">

	<?php if ($order): ?>

			<?php do_action('woocommerce_before_thankyou', $order->get_id()); ?>

			<?php if ($order->has_status('failed')): ?>

					<!-- Failed Payment State -->
					<div class="bg-white border border-rose-200 rounded-2xl p-6 sm:p-8 text-center shadow-xs mb-6">
						<div
							class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center mx-auto mb-3 border border-rose-100">
							<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path>
							</svg>
						</div>
						<h1 class="text-xl font-bold text-slate-900 mb-2">للأسف، تعذر إتمام عملية الدفع</h1>
						<p class="text-sm text-slate-600 max-w-md mx-auto mb-5 leading-relaxed">
							تعذر إتمام الطلب نظراً لرفض المعاملة من جهة الدفع. يمكنك إعادة المحاولة أو اختيار وسيلة دفع أخرى.
						</p>
						<div class="flex flex-col sm:flex-row items-center justify-center gap-3">
							<a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
								class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 font-bold py-2.5 px-6 rounded-xl text-sm transition"
								>
								<span >إعادة محاولة الدفع</span>
							</a>
							<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
								class="w-full sm:w-auto bg-slate-100 hover:bg-slate-200 font-bold py-2.5 px-5 rounded-xl text-sm transition"
								>
								<span >العودة للمتجر</span>
							</a>
						</div>
					</div>

			<?php else: ?>

					<!-- 1. Simple Top Confirmation Card -->
					<div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xs mb-5 text-center">
						<div
							class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3.5 border border-emerald-100">
							<svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
							</svg>
						</div>

						<h1 class="text-2xl font-black text-slate-900 mb-1.5">
							تم استلام طلبك بنجاح!
						</h1>

						<p class="text-sm text-slate-600 max-w-md mx-auto leading-relaxed">
							شكراً لتسوقك معنا. رقم طلبك هو <strong class="text-slate-900 font-mono font-bold"
								dir="ltr">#<?php echo esc_html($order->get_order_number()); ?></strong>، وسيقوم فريقنا بتجهيز الشحنة
							والتواصل معك لتأكيد موعد التسليم.
						</p>

						<!-- Quick Highlights 4-box Row (All Arabic) -->
						<div
							class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 bg-slate-50 border border-slate-100 rounded-xl p-3.5 mt-6 text-center">
							<div class="p-2 bg-white rounded-lg border border-slate-100">
								<span class="text-[11px] text-slate-400 block mb-0.5">رقم الطلب</span>
								<span class="text-sm font-bold text-slate-800 font-mono"
									dir="ltr">#<?php echo esc_html($order->get_order_number()); ?></span>
							</div>
							<div class="p-2 bg-white rounded-lg border border-slate-100">
								<span class="text-[11px] text-slate-400 block mb-0.5">التاريخ</span>
								<span class="text-xs font-semibold text-slate-800">
									<?php
									$created_date = $order->get_date_created();
									echo esc_html($created_date ? date_i18n('j F Y', $created_date->getTimestamp()) : '');
									?>
								</span>
							</div>
							<div class="p-2 bg-white rounded-lg border border-slate-100">
								<span class="text-[11px] text-slate-400 block mb-0.5">طريقة الدفع</span>
								<span class="text-xs font-semibold text-slate-800 block line-clamp-1"
									title="<?php echo esc_attr($order->get_payment_method_title()); ?>">
									<?php echo esc_html($order->get_payment_method_title()); ?>
								</span>
							</div>
							<div class="p-2 bg-emerald-50/60 rounded-lg border border-emerald-100">
								<span class="text-[11px] text-emerald-700 block mb-0.5">الإجمالي</span>
								<span
									class="text-sm font-black text-emerald-600 font-mono"><?php echo wp_kses_post(wc_price($order->get_total(), array('currency' => $order->get_currency()))); ?></span>
							</div>
						</div>

						<!-- "View Order Details" Button at the end of this section (Wide, Reddish-White Background, Black Text) -->
						<div class="mt-5 pt-1 flex justify-center">
							<a href="<?php echo esc_url($order->get_view_order_url()); ?>"
								class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 sm:px-12 py-3.5 rounded-xl border border-rose-200/90 bg-[#fff5f5] hover:bg-rose-100/70 transition-all shadow-xs text-sm font-extrabold"
								>
								<svg class="w-4 h-4"  fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
								</svg>
								<span >عرض تفاصيل الطلب</span>
							</a>
						</div>
					</div>

					<!-- 2. Clean Order Details & Items Card -->
					<div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs mb-5">
						<h2
							class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-3 flex items-center justify-between">
							<span>المنتجات المطلوبة</span>
							<span class="text-xs font-normal text-slate-500">(<?php echo esc_html($order->get_item_count()); ?>
								منتج)</span>
						</h2>

						<!-- Items List -->
						<div class="divide-y divide-slate-100 text-sm">
							<?php
							$order_items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
							foreach ($order_items as $item_id => $item):
								$qty = $item->get_quantity();
								?>
									<div class="py-3 flex items-center justify-between gap-3">
										<div class="min-w-0 flex-1">
											<p class="font-bold text-slate-800 text-sm truncate"><?php echo esc_html($item->get_name()); ?>
											</p>
											<span class="text-xs text-slate-400">الكمية: <strong
													class="text-slate-600"><?php echo esc_html($qty); ?></strong></span>
											<?php wc_display_item_meta($item); ?>
										</div>
										<div class="font-mono font-bold text-slate-900 text-sm whitespace-nowrap">
											<?php echo wp_kses_post(wc_price($item->get_total(), array('currency' => $order->get_currency()))); ?>
										</div>
									</div>
							<?php endforeach; ?>
						</div>

						<!-- Totals Breakdown (Pure Arabic) -->
						<?php
						$country_code = $order->get_shipping_country() ?: ($order->get_billing_country() ?: 'EG');
						$state_code = $order->get_shipping_state() ?: $order->get_billing_state();
						$states = (function_exists('WC') && WC()->countries) ? WC()->countries->get_states($country_code) : array();
						$governorate = ($states && isset($states[$state_code])) ? $states[$state_code] : $state_code;
						$city = $order->get_shipping_city() ?: $order->get_billing_city();
						$address_1 = $order->get_shipping_address_1() ?: $order->get_billing_address_1();
						$billing_phone = $order->get_billing_phone();
						$customer_note = $order->get_customer_note();
						$full_location = implode('، ', array_filter(array($address_1, $city, $governorate)));
						?>
						<div class="pt-3 mt-2 border-t border-slate-100 space-y-2 text-xs">
							<!-- المجموع الفرعي -->
							<div class="flex items-center justify-between text-slate-600">
								<span class="font-medium text-slate-500">المجموع الفرعي:</span>
								<span class="font-mono font-bold text-slate-800 text-sm">
									<?php echo wp_kses_post(wc_price($order->get_subtotal(), array('currency' => $order->get_currency()))); ?>
								</span>
							</div>

							<!-- مصاريف الشحن -->
							<div class="flex items-center justify-between text-slate-600">
								<span class="font-medium text-slate-500">الشحن والتوصيل:</span>
								<span class="font-mono font-bold text-slate-800 text-sm">
									<?php
									$shipping_total = (float) $order->get_shipping_total();
									if (0.0 === $shipping_total) {
										echo '<span class="text-emerald-600 font-bold">شحن مجاني</span>';
									} else {
										echo wp_kses_post(wc_price($shipping_total, array('currency' => $order->get_currency())));
										if ($governorate) {
											echo ' <span class="text-slate-400 text-[11px] font-normal">(' . esc_html($governorate) . ')</span>';
										}
									}
									?>
								</span>
							</div>

							<!-- الخصم إن وجد -->
							<?php if ((float) $order->get_discount_total() > 0): ?>
									<div class="flex items-center justify-between text-emerald-600">
										<span class="font-medium">الخصم:</span>
										<span class="font-mono font-bold">
											-<?php echo wp_kses_post(wc_price($order->get_discount_total(), array('currency' => $order->get_currency()))); ?>
										</span>
									</div>
							<?php endif; ?>

							<!-- وسيلة الدفع -->
							<div class="flex items-center justify-between text-slate-600">
								<span class="font-medium text-slate-500">طريقة الدفع:</span>
								<span class="font-bold text-slate-800 text-xs">
									<?php echo esc_html($order->get_payment_method_title()); ?>
								</span>
							</div>

							<!-- الإجمالي النهائي -->
							<div class="flex items-center justify-between pt-2.5 border-t border-slate-200 text-slate-900">
								<span class="font-extrabold text-sm text-slate-900">الإجمالي النهائي:</span>
								<span class="font-mono text-xl font-black text-emerald-600">
									<?php echo wp_kses_post(wc_price($order->get_total(), array('currency' => $order->get_currency()))); ?>
								</span>
							</div>
						</div>

						<!-- Delivery & Customer Details -->
						<div class="mt-4 pt-3.5 border-t border-slate-100 text-xs text-slate-600">
							<div class="flex flex-wrap items-center justify-between gap-2">
								<?php if ($full_location): ?>
										<div>
											<span class="text-slate-400">العنوان: </span>
											<span class="font-semibold text-slate-800"><?php echo esc_html($full_location); ?></span>
										</div>
								<?php endif; ?>
								<?php if ($billing_phone): ?>
										<div>
											<span class="text-slate-400">الهاتف: </span>
											<span class="font-mono font-bold text-slate-800"
												dir="ltr"><?php echo esc_html($billing_phone); ?></span>
										</div>
								<?php endif; ?>
							</div>
							<?php if (!empty($customer_note)): ?>
									<p class="mt-2 text-slate-500 bg-slate-50 p-2 rounded-lg border border-slate-100">
										<strong class="text-slate-700">ملاحظات:</strong> <?php echo esc_html($customer_note); ?>
									</p>
							<?php endif; ?>
						</div>
					</div>

					<!-- 3. Action Buttons with explicit BLACK font color -->
					<?php
					$raw_wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
					$clean_wa_phone = preg_replace('/\D/', '', $raw_wa_phone);
					$order_number = $order->get_order_number();
					$wa_msg = 'مرحباً، أود المتابعة بخصوص طلبي رقم #' . $order_number;
					$wa_url = 'https://api.whatsapp.com/send?phone=' . $clean_wa_phone . '&text=' . rawurlencode($wa_msg);
					?>
					<div class="flex flex-col sm:flex-row items-center justify-center gap-3">
						<!-- زر واتساب بنص أسود صريح -->
						<a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer"
							class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#20ba59] font-bold py-3 px-7 rounded-xl text-sm transition shadow-xs text-slate-900 no-underline"
							>
							<svg class="w-4 h-4"  viewBox="0 0 24 24">
								<path
									d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
							</svg>
							<span >تواصل عبر واتساب</span>
						</a>

						<!-- زر العودة للمتجر بنص أسود صريح -->
						<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
							class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 font-bold py-3 px-7 rounded-xl border border-slate-300 text-sm transition shadow-xs text-slate-900 no-underline"
							>
							<span >العودة إلى المتجر</span>
						</a>
					</div>

			<?php endif; ?>

			<?php do_action('woocommerce_thankyou', $order->get_id()); ?>

	<?php else: ?>

			<div class="bg-white border border-slate-200 rounded-2xl p-8 text-center shadow-xs">
				<div
					class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
					</svg>
				</div>
				<h2 class="text-lg font-bold text-slate-900 mb-2">تم تسجيل طلبك بنجاح</h2>
				<p class="text-slate-600 text-sm mb-5">شكراً لك. تم استلام طلبك وسنقوم بالتواصل معك قريباً.</p>
				<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
					class="inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 font-bold py-2.5 px-6 rounded-xl text-sm transition text-white no-underline"
					>
					<span >تصفح المتجر</span>
				</a>
			</div>

	<?php endif; ?>

</div>