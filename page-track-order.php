<?php
/**
 * Template Name: تتبع الطلب (Order Tracking)
 * Description: Public Guest Order Tracking Template for Ashrafco Online Store
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header();

$searched = false;
$order_found = null;
$error_message = '';

if (isset($_POST['ashrafco_track_order_submit']) || (isset($_GET['order_id']) && isset($_GET['billing_phone']))) {
	$searched = true;

	$nonce = $_POST['ashrafco_track_order_nonce'] ?? '';
	if (!empty($_POST) && !wp_verify_nonce($nonce, 'ashrafco_track_order')) {
		$error_message = 'انتهت صلاحية الجلسة، يرجى إعادة المحاولة.';
	} else {
		$raw_order_id = isset($_POST['order_id']) ? sanitize_text_field(wp_unslash($_POST['order_id'])) : sanitize_text_field(wp_unslash($_GET['order_id'] ?? ''));
		$raw_phone = isset($_POST['billing_phone']) ? sanitize_text_field(wp_unslash($_POST['billing_phone'])) : sanitize_text_field(wp_unslash($_GET['billing_phone'] ?? ''));

		// Extract clean numeric digits
		$order_id = absint(preg_replace('/[^0-9]/', '', $raw_order_id));

		// Normalize phone number (handle Arabic-Indic numbers ٠-٩ and strip spaces, dashes, +2, etc.)
		$arabic_indic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
		$latin_digits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		$clean_phone = str_replace($arabic_indic, $latin_digits, $raw_phone);
		$clean_phone = preg_replace('/[^0-9]/', '', $clean_phone);

		// Remove Egyptian country code if provided (e.g. 2010... -> 010...)
		if (substr($clean_phone, 0, 2) === '20' && strlen($clean_phone) === 12) {
			$clean_phone = substr($clean_phone, 2);
		}
		if (strlen($clean_phone) === 10 && substr($clean_phone, 0, 1) === '1') {
			$clean_phone = '0' . $clean_phone;
		}

		if (empty($order_id) || empty($clean_phone)) {
			$error_message = 'يرجى إدخال رقم الطلب ورقم الهاتف المسجل بشكل صحيح.';
		} else {
			$order = wc_get_order($order_id);

			if ($order) {
				$order_phone = $order->get_billing_phone();
				$clean_order_phone = str_replace($arabic_indic, $latin_digits, $order_phone);
				$clean_order_phone = preg_replace('/[^0-9]/', '', $clean_order_phone);
				if (substr($clean_order_phone, 0, 2) === '20' && strlen($clean_order_phone) === 12) {
					$clean_order_phone = substr($clean_order_phone, 2);
				}
				if (strlen($clean_order_phone) === 10 && substr($clean_order_phone, 0, 1) === '1') {
					$clean_order_phone = '0' . $clean_order_phone;
				}

				// Match order phone: check exact match or if last 9 digits match
				$matched = false;
				if ($clean_phone === $clean_order_phone) {
					$matched = true;
				} elseif (strlen($clean_phone) >= 9 && strlen($clean_order_phone) >= 9) {
					if (substr($clean_phone, -9) === substr($clean_order_phone, -9)) {
						$matched = true;
					}
				}

				if ($matched) {
					$order_found = $order;
				} else {
					$error_message = 'لم نتمكن من العثور على طلب بهذه البيانات، يرجى التأكد من رقم الطلب ورقم الهاتف.';
				}
			} else {
				$error_message = 'لم نتمكن من العثور على طلب بهذه البيانات، يرجى التأكد من رقم الطلب ورقم الهاتف.';
			}
		}
	}
}

// WhatsApp support link
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$wa_message = sprintf('مرحباً %s، أود الاستفسار والمساعدة بخصوص تتبع الشحنة', $store_name) . ($order_found ? ' رقم #' . $order_found->get_order_number() : '');
$wa_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode($wa_message);
?>

<main id="primary" class="site-main w-full min-h-[70vh] bg-slate-50 font-sans py-8 sm:py-12" dir="rtl">
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

		<!-- Breadcrumb -->
		<nav class="text-xs sm:text-sm text-slate-400 flex items-center gap-2 flex-wrap"
			aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-brand-600 transition-colors">
				<?php esc_html_e('الرئيسية', 'ashrafco'); ?>
			</a>
			<span class="text-slate-300">/</span>
			<span class="text-slate-700 font-semibold">تتبع مسار الشحنة</span>
		</nav>

		<!-- Page Header -->
		<div class="text-center space-y-2">
			<div
				class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 border border-orange-200/80 text-brand-600 text-xs font-bold mb-1">
				<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
				</svg>
				<span>خدمة التتبع المباشر لعملاء <?php echo esc_html($store_name); ?></span>
			</div>
			<h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight m-0">
				تتبع حالة شحنتك ومعداتك
			</h1>
			<p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto leading-relaxed">
				أدخل رقم الطلب المسجل في رسالة التأكيد ورقم هاتفك المحمول لمتابعة خروج الشحنة مع المندوب وموعد التسليم.
			</p>
		</div>

		<?php if (!$order_found): ?>
			<!-- Lookup Form Card -->
			<div
				class="ui-card bg-white border border-slate-200 rounded-2xl p-6 sm:p-10 shadow-sm max-w-xl mx-auto space-y-6">

				<?php if (!empty($error_message)): ?>
					<div
						class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm font-bold flex items-center gap-2.5">
						<svg class="w-5 h-5 flex-shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
								clip-rule="evenodd" />
						</svg>
						<span><?php echo esc_html($error_message); ?></span>
					</div>
				<?php endif; ?>

				<form method="post" action="" class="space-y-4">
					<?php wp_nonce_field('ashrafco_track_order', 'ashrafco_track_order_nonce'); ?>

					<!-- Order ID -->
					<div>
						<label for="order_id" class="block text-xs font-bold text-slate-700 mb-1.5">
							رقم الطلب (Order ID) <span class="text-rose-500">*</span>
						</label>
						<input type="text" name="order_id" id="order_id" required placeholder="مثال: 12450"
							value="<?php echo esc_attr($_POST['order_id'] ?? ($_GET['order_id'] ?? '')); ?>"
							class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-sans" />
						<span class="text-[11px] text-slate-400 mt-1 block">الرقم المذكور في رسالة تأكيد الطلب أو
							الفاتورة</span>
					</div>

					<!-- Billing Phone -->
					<div>
						<label for="billing_phone" class="block text-xs font-bold text-slate-700 mb-1.5">
							رقم الهاتف المحمول المسجل <span class="text-rose-500">*</span>
						</label>
						<input type="tel" name="billing_phone" id="billing_phone" required
							placeholder="010XXXXXXXX أو 011XXXXXXXX"
							value="<?php echo esc_attr($_POST['billing_phone'] ?? ($_GET['billing_phone'] ?? '')); ?>"
							class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition font-sans font-mono" />
					</div>

					<div class="pt-2">
						<button type="submit" name="ashrafco_track_order_submit" value="1"
							class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs sm:text-sm transition-all shadow-md active:scale-[0.99] cursor-pointer flex items-center justify-center gap-2">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
							</svg>
							<span>تتبع حالة الشحنة فوراً</span>
						</button>
					</div>
				</form>

			</div>
		<?php else: ?>

			<!-- Order Details View for Found Order -->
			<?php
			$order_id = $order_found->get_id();
			$order_num = $order_found->get_order_number();
			$status = $order_found->get_status();
			$badge = function_exists('ashrafco_get_order_status_badge') ? ashrafco_get_order_status_badge($status) : null;
			$status_name = $badge ? $badge['label'] : wc_get_order_status_name($status);
			$badge_class = $badge ? $badge['class'] : 'bg-slate-100 text-slate-700 border-slate-200';
			$badge_dot = $badge ? $badge['dot'] : 'bg-slate-400';
			$date_placed = wc_format_datetime($order_found->get_date_created());
			$pay_method = $order_found->get_payment_method_title();
			$item_count = $order_found->get_item_count();

			$step = 1;
			if ('processing' === $status) {
				$step = 2;
			} elseif ('shipped' === $status) {
				$step = 3;
			} elseif ('completed' === $status) {
				$step = 4;
			}
			?>

			<div class="space-y-6">

				<!-- Back / Another Lookup CTA -->
				<div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
					<a href="<?php echo esc_url(get_permalink()); ?>"
						class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition shadow-2xs">
						<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M10 19l-7-7m0 0l7-7m-7 7h18" />
						</svg>
						<span>تتبع طلب آخر</span>
					</a>

					<span
						class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full flex items-center gap-1.5">
						<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
						<span>تم التحقق من بيانات الشحنة</span>
					</span>
				</div>

				<!-- 1. Status Progress Tracker Card -->
				<div class="ui-card bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">

					<!-- Order Metadata Grid -->
					<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-slate-100">
						<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
							<span class="text-[11px] font-bold text-slate-400 block mb-1">رقم الطلب</span>
							<span
								class="font-mono font-black text-slate-900 text-sm sm:text-base">#<?php echo esc_html($order_num); ?></span>
						</div>
						<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
							<span class="text-[11px] font-bold text-slate-400 block mb-1">تاريخ الطلب</span>
							<span
								class="font-bold text-slate-800 text-xs sm:text-sm font-mono"><?php echo esc_html($date_placed); ?></span>
						</div>
						<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
							<span class="text-[11px] font-bold text-slate-400 block mb-1">طريقة السداد</span>
							<span class="font-bold text-slate-800 text-xs sm:text-sm truncate block">
								<?php echo esc_html($pay_method ? $pay_method : 'الدفع عند الاستلام'); ?>
							</span>
						</div>
						<div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
							<span class="text-[11px] font-bold text-slate-400 block mb-1">حالة الشحنة</span>
							<span
								class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border <?php echo esc_attr($badge_class); ?>">
								<span class="w-1.5 h-1.5 rounded-full flex-shrink-0 <?php echo esc_attr($badge_dot); ?>"></span>
								<span><?php echo esc_html($status_name); ?></span>
							</span>
						</div>
					</div>

					<!-- Visual 4-Step Progress Bar -->
					<div>
						<h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">
							مراحل شحن وتوصيل الطلب
						</h4>

						<div class="relative">
							<div class="absolute top-5 inset-x-8 h-1 bg-slate-100 -z-0"></div>
							<div class="absolute top-5 right-8 h-1 bg-emerald-500 transition-all duration-500 -z-0"
								style="width: <?php echo esc_attr((($step - 1) / 3) * 100); ?>%;"></div>

							<div class="grid grid-cols-4 relative z-10 text-center">
								<!-- Step 1 -->
								<div class="flex flex-col items-center">
									<div
										class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm <?php echo $step >= 1 ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500'; ?>">
										✓
									</div>
									<span
										class="text-xs font-bold mt-2 <?php echo $step >= 1 ? 'text-slate-900' : 'text-slate-400'; ?>">تم
										استلام الطلب</span>
								</div>

								<!-- Step 2 -->
								<div class="flex flex-col items-center">
									<div
										class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm <?php echo $step >= 2 ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : ($step === 1 ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500'); ?>">
										<?php echo $step > 2 ? '✓' : '2'; ?>
									</div>
									<span
										class="text-xs font-bold mt-2 <?php echo $step >= 2 ? 'text-slate-900' : 'text-slate-400'; ?>">قيد
										المعالجة</span>
								</div>

								<!-- Step 3 -->
								<div class="flex flex-col items-center">
									<div
										class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm <?php echo $step >= 3 ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : ($step === 2 ? 'bg-amber-500 text-white animate-pulse' : 'bg-slate-200 text-slate-500'); ?>">
										<?php echo $step > 3 ? '✓' : '3'; ?>
									</div>
									<span
										class="text-xs font-bold mt-2 <?php echo $step >= 3 ? 'text-slate-900' : 'text-slate-400'; ?>">تم
										الشحن</span>
								</div>

								<!-- Step 4 -->
								<div class="flex flex-col items-center">
									<div
										class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-sm <?php echo $step >= 4 ? 'bg-emerald-600 text-white ring-4 ring-emerald-50' : 'bg-slate-200 text-slate-500'; ?>">
										<?php echo $step >= 4 ? '✓' : '4'; ?>
									</div>
									<span
										class="text-xs font-bold mt-2 <?php echo $step >= 4 ? 'text-slate-900 font-extrabold' : 'text-slate-400'; ?>">تم
										التسليم</span>
								</div>
							</div>
						</div>
					</div>

				</div>

				<!-- Inspection Notice -->
				<div
					class="bg-gradient-to-r from-amber-500/10 via-orange-500/10 to-amber-500/10 border border-orange-200 rounded-2xl p-4 sm:p-5 flex items-center gap-3.5 shadow-2xs">
					<div
						class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
						</svg>
					</div>
					<div>
						<h4 class="text-xs sm:text-sm font-extrabold text-slate-900 m-0">تذكير فحص ومعاينة الشحنة:</h4>
						<p class="text-xs text-slate-600 mt-0.5 mb-0 leading-relaxed font-medium">
							يحق لك فحص وتجربة الماكينات ومعدات البورسلين مع مندوب الشحن والتأكد من مطابقتها للمواصفات الفنية
							قبل سداد المبلغ.
						</p>
					</div>
				</div>

				<!-- Items List Card -->
				<div class="ui-card bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-5">
					<div class="flex items-center justify-between pb-3 border-b border-slate-100">
						<h3 class="text-base sm:text-lg font-black text-slate-900 m-0">المنتجات المطلوبة</h3>
						<span class="text-xs font-bold text-slate-500 font-mono"><?php echo esc_html($item_count); ?>
							قطع</span>
					</div>

					<div class="divide-y divide-slate-100">
						<?php
						$order_items = $order_found->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
						foreach ($order_items as $item_id => $item):
							$product = $item->get_product();
							$permalink = $product ? $product->get_permalink() : '';
							$thumb = $product ? $product->get_image(array(60, 60), array('class' => 'w-full h-full object-cover')) : '';
							$qty = $item->get_quantity();
							$line_total = $order_found->get_formatted_line_subtotal($item);
							?>
							<div class="py-3.5 flex items-center justify-between gap-4">
								<div class="flex items-center gap-3 min-w-0 flex-1">
									<div
										class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl overflow-hidden border border-slate-200 bg-slate-50 flex-shrink-0 flex items-center justify-center">
										<?php if ($thumb): ?>
											<?php echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php else: ?>
											<svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor"
												viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
											</svg>
										<?php endif; ?>
									</div>
									<div class="min-w-0 flex-1">
										<h4 class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-1 leading-snug m-0">
											<?php echo esc_html($item->get_name()); ?>
										</h4>
										<span class="text-xs text-slate-400 mt-0.5 block">الكمية: <strong
												class="text-slate-700 font-mono"><?php echo esc_html($qty); ?></strong></span>
									</div>
								</div>
								<div class="text-left flex-shrink-0 font-mono font-black text-slate-900 text-xs sm:text-sm">
									<?php echo wp_kses_post($line_total); ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<div class="pt-4 border-t border-slate-100 flex items-center justify-between">
						<span class="text-sm font-extrabold text-slate-900">إجمالي الفاتورة مع الشحن:</span>
						<span class="text-base sm:text-lg font-black text-slate-900 font-mono">
							<?php echo wp_kses_post($order_found->get_formatted_order_total()); ?>
						</span>
					</div>
				</div>

				<!-- Address Card -->
				<div class="ui-card bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
					<h4
						class="text-sm font-extrabold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100 m-0 mb-3">
						<svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
						<span>عنوان تسليم الشحنة</span>
					</h4>
					<div class="text-xs sm:text-sm text-slate-600 leading-relaxed space-y-1">
						<p class="font-bold text-slate-800 m-0">
							<?php echo esc_html($order_found->get_formatted_shipping_full_name() ? $order_found->get_formatted_shipping_full_name() : $order_found->get_formatted_billing_full_name()); ?>
						</p>
						<p class="m-0">
							<?php
							$addr = $order_found->get_shipping_address_1() ? $order_found->get_shipping_address_1() : $order_found->get_billing_address_1();
							echo esc_html($addr);
							?>
						</p>
						<p class="text-slate-500 m-0">
							<?php
							$city = $order_found->get_shipping_city() ? $order_found->get_shipping_city() : $order_found->get_billing_city();
							$state = $order_found->get_shipping_state() ? $order_found->get_shipping_state() : $order_found->get_billing_state();
							echo esc_html($city . ($state ? '، ' . $state : ''));
							?>
						</p>
					</div>
				</div>

			</div>

		<?php endif; ?>

		<!-- Direct WhatsApp Support Banner -->
		<div class="bg-emerald-50 border border-emerald-200/90 rounded-2xl p-5 sm:p-6 text-center space-y-3 shadow-xs">
			<div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto">
				<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
					<path
						d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
				</svg>
			</div>
			<div>
				<h4 class="text-sm font-extrabold text-slate-900 m-0">تحتاج مساعدة في تتبع شحنتك؟</h4>
				<p class="text-xs text-slate-600 mt-1 mb-0">فريق الدعم الفني وخدمة عملاء
					<?php echo esc_html($store_name); ?> جاهز لمساعدتك عبر واتساب فوراً
				</p>
			</div>
			<div class="pt-1">
				<a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer"
					class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold py-2.5 px-6 rounded-xl text-xs sm:text-sm transition-all shadow-sm">
					<span>تواصل معنا عبر واتساب الآن</span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
		</div>

	</div>
</main>

<?php
get_footer();
