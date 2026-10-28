<?php
/**
 * Output a single payment method for Ashrafco checkout.
 * Customized to precisely match the modern Egyptian high-trust checkout design:
 * - COD with Cash Inspection guarantee & badges
 * - Vodafone Cash with copyable wallet number & direct WhatsApp receipt confirmation
 * - InstaPay with copyable IPA address, phone number & WhatsApp confirmation
 *
 * @package Ashrafco
 * @version 8.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$gateway_id = $gateway->id;
$is_chosen = (bool) $gateway->chosen;
$input_id = 'payment_method_' . esc_attr($gateway_id);
$vf_number = function_exists('ashrafco_get_vodafone_cash_number') ? ashrafco_get_vodafone_cash_number() : '201065712366';
$ipa_address = function_exists('ashrafco_get_instapay_address') ? ashrafco_get_instapay_address() : 'Ashrafco@instapay';
$ipa_phone = function_exists('ashrafco_get_instapay_phone') ? ashrafco_get_instapay_phone() : '201065712366';
$wa_url_vf = function_exists('ashrafco_get_payment_whatsapp_url') ? ashrafco_get_payment_whatsapp_url('vodafone_cash') : 'https://wa.me/201065712366';
$wa_url_insta = function_exists('ashrafco_get_payment_whatsapp_url') ? ashrafco_get_payment_whatsapp_url('instapay') : 'https://wa.me/201065712366';
?>

<li class="wc_payment_method payment_method_<?php echo esc_attr($gateway_id); ?> ashrafco-pm-card <?php echo $is_chosen ? 'is-selected' : ''; ?>"
	data-gateway="<?php echo esc_attr($gateway_id); ?>">

	<!-- Radio Input (Hidden visually, accessible for WC) -->
	<input id="<?php echo esc_attr($input_id); ?>" type="radio" class="input-radio ashrafco-pm-input"
		name="payment_method" value="<?php echo esc_attr($gateway_id); ?>" <?php checked($is_chosen, true); ?>
		data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>" />

	<label for="<?php echo esc_attr($input_id); ?>" class="ashrafco-pm-label">

		<?php if ('cod' === $gateway_id): ?>
				<!-- ========================================== -->
				<!-- METHOD 1: CASH ON DELIVERY (معاينة قبل الدفع) -->
				<!-- ========================================== -->
				<div class="ashrafco-pm-row ashrafco-pm-row--cod">

					<!-- Right Area (Radio + Title + Cash Illustration) -->
					<div class="ashrafco-pm-main-group">
						<span class="ashrafco-pm-radio" aria-hidden="true">
							<span class="ashrafco-pm-radio__inner"></span>
						</span>

						<div class="ashrafco-pm-text-block">
							<h4 class="ashrafco-pm-title">الدفع عند الاستلام (معاينة قبل الدفع)</h4>
							<p class="ashrafco-pm-subtitle">ادفع نقداً عند استلام الطلب من مندوب التوصيل بعد معاينة المنتجات.
							</p>
						</div>

						<!-- Cash Banknotes & Hand Illustration -->
						<div class="ashrafco-pm-illustration" aria-hidden="true">
							<svg viewBox="0 0 56 46" fill="none" class="ashrafco-svg-cash-hand">
								<!-- Banknotes -->
								<rect x="6" y="8" width="36" height="22" rx="3" fill="#65a30d" />
								<rect x="8" y="10" width="32" height="18" rx="2" fill="#84cc16" />
								<circle cx="24" cy="19" r="5" fill="#4d7c0f" opacity="0.35" />
								<circle cx="24" cy="19" r="3" fill="#ecfccb" />
								<!-- Second note peeking -->
								<rect x="10" y="4" width="34" height="20" rx="3" fill="#4d7c0f" opacity="0.25" />
								<!-- Hand delivering / holding -->
								<path
									d="M48 38c0 0-6-3-11-2-4 .8-8 3-12 1-3-1.5-6 1-6 1l2 4c0 0 3.5-1.5 6-.5s6 2.5 10 0l11-3.5V38z"
									fill="#fbcfe8" opacity="0.4" />
								<path d="M46 28c-3-1-8 0-11 3-3 3-8 4-12 3l-4 3c3 2 7 2 11 0 4-2 9-2 13 0l7-5c1-1 0-3-4-4z"
									fill="#fed7aa" />
								<!-- Sleeve in Red -->
								<path d="M46 26l7-2.5c1.2-.4 2.5.3 2.8 1.5l1.5 4.5c.4 1.2-.3 2.5-1.5 2.8L49 35"
									fill="#dc2626" />
								<path d="M45.5 25.5l1.5 8" stroke="#b91c1c" stroke-width="1.5" stroke-linecap="round" />
							</svg>
						</div>
					</div>

					<!-- Left Area (3 Trust Guarantee Bullets) -->
					<div class="ashrafco-pm-features-list">
						<div class="ashrafco-pm-feature">
							<span class="ashrafco-pm-feature__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.714Z" />
								</svg>
							</span>
							<span class="ashrafco-pm-feature__label">معاينة المنتجات قبل الدفع</span>
						</div>

						<div class="ashrafco-pm-feature">
							<span class="ashrafco-pm-feature__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
								</svg>
							</span>
							<span class="ashrafco-pm-feature__label">إمكانية إرجاع أو استبدال حسب سياسة المتجر</span>
						</div>

						<div class="ashrafco-pm-feature">
							<span class="ashrafco-pm-feature__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.375c0-.621-.504-1.125-1.125-1.125h-9.75c-.621 0-1.125.504-1.125 1.125v10.875" />
								</svg>
							</span>
							<span class="ashrafco-pm-feature__label">متاح لجميع المحافظات</span>
						</div>
					</div>
				</div>

		<?php elseif ('vodafone_cash' === $gateway_id): ?>
				<!-- ========================================== -->
				<!-- METHOD 2: VODAFONE CASH (فودافون كاش)      -->
				<!-- ========================================== -->
				<div class="ashrafco-pm-header-row">
					<!-- Right Group -->
					<div class="ashrafco-pm-main-group">
						<span class="ashrafco-pm-radio" aria-hidden="true">
							<span class="ashrafco-pm-radio__inner"></span>
						</span>

						<div class="ashrafco-pm-text-block">
							<h4 class="ashrafco-pm-title">الدفع عبر فودافون كاش</h4>
							<p class="ashrafco-pm-subtitle">حوّل المبلغ على رقم فودافون كاش التالي ثم اضغط على تأكيد الطلب.</p>
						</div>

						<!-- Vodafone Cash Phone Transfer Illustration -->
						<div class="ashrafco-pm-illustration" aria-hidden="true">
							<svg viewBox="0 0 54 54" fill="none" class="ashrafco-svg-vf-phone">
								<!-- Smartphone Base -->
								<rect x="14" y="5" width="28" height="44" rx="6" fill="#fdf2f2" stroke="#f87171"
									stroke-width="1.8" />
								<rect x="18" y="11" width="20" height="30" rx="3" fill="#fff" />
								<circle cx="28" cy="8" r="1.2" fill="#ef4444" />
								<!-- Red circular arrow / cash transfer symbol -->
								<circle cx="28" cy="26" r="8" fill="#e60000" />
								<path d="M25 26h6m0 0l-2.5-2.5M31 26l-2.5 2.5" stroke="#fff" stroke-width="1.8"
									stroke-linecap="round" stroke-linejoin="round" />
								<!-- Phone bottom bar -->
								<rect x="25" y="44" width="6" height="1.8" rx="0.9" fill="#f87171" />
								<!-- Arrow pointing in -->
								<path d="M6 26h6m0 0l-2.5-2.5M12 26l-2.5 2.5" stroke="#e60000" stroke-width="2.2"
									stroke-linecap="round" stroke-linejoin="round" />
							</svg>
						</div>
					</div>

					<!-- Left Group: Red Vodafone Cash Branded Card Badge -->
					<div class="ashrafco-pm-brand-card ashrafco-pm-brand-card--vodafone">
						<div class="ashrafco-pm-vf-logo-wrap">
							<!-- Vodafone Logo circle with teardrop -->
							<svg class="ashrafco-vf-logo" viewBox="0 0 36 36" fill="none">
								<circle cx="18" cy="18" r="16" fill="#fff" />
								<path
									d="M18 7c-6.075 0-11 4.925-11 11 0 5.48 4.02 9.98 9.25 10.85v-4.52c-2.82-.72-4.9-3.26-4.9-6.33 0-3.6 2.92-6.52 6.52-6.52 3.6 0 6.53 2.92 6.53 6.52 0 1.94-.85 3.68-2.2 4.88l3.18 3.18c2.4-2.12 3.89-5.2 3.89-8.59 0-6.075-4.925-11-11-11z"
									fill="#e60000" />
								<circle cx="18" cy="18" r="3.2" fill="#e60000" />
							</svg>
							<!-- Phone cash icon -->
							<svg class="ashrafco-vf-phone-icon" viewBox="0 0 24 24" fill="none" stroke="#fff"
								stroke-width="2.2">
								<rect x="5" y="2" width="14" height="20" rx="3" />
								<path stroke-linecap="round" d="M12 18h.01M9 6h6" />
								<path stroke-linecap="round" stroke-linejoin="round" d="M10 11l2 2 2-2" />
							</svg>
						</div>
						<div class="ashrafco-pm-vf-name">
							<span>vodafone</span>
							<strong>cash</strong>
						</div>
					</div>
				</div>

		<?php elseif ('instapay' === $gateway_id): ?>
				<!-- ========================================== -->
				<!-- METHOD 3: INSTAPAY (إنستا باي)              -->
				<!-- ========================================== -->
				<div class="ashrafco-pm-header-row">
					<!-- Right Group -->
					<div class="ashrafco-pm-main-group">
						<span class="ashrafco-pm-radio" aria-hidden="true">
							<span class="ashrafco-pm-radio__inner"></span>
						</span>

						<div class="ashrafco-pm-text-block">
							<h4 class="ashrafco-pm-title">الدفع عبر إنستا باي</h4>
							<p class="ashrafco-pm-subtitle">حوّل المبلغ على حساب إنستا باي التالي ثم اضغط على تأكيد الطلب.</p>
						</div>

						<!-- InstaPay Phone / Chevrons Illustration -->
						<div class="ashrafco-pm-illustration" aria-hidden="true">
							<svg viewBox="0 0 54 54" fill="none" class="ashrafco-svg-insta-phone">
								<!-- Smartphone Base -->
								<rect x="14" y="5" width="28" height="44" rx="6" fill="#fdf4ff" stroke="#c084fc"
									stroke-width="1.8" />
								<rect x="18" y="11" width="20" height="30" rx="3" fill="#fff" />
								<circle cx="28" cy="8" r="1.2" fill="#9333ea" />
								<!-- InstaPay double chevrons in center -->
								<g transform="translate(20, 20)">
									<path d="M4 3l4 4-4 4" stroke="#f97316" stroke-width="2.4" stroke-linecap="round"
										stroke-linejoin="round" />
									<path d="M9 3l4 4-4 4" stroke="#ea580c" stroke-width="2.4" stroke-linecap="round"
										stroke-linejoin="round" />
								</g>
								<text x="28" y="38" font-size="5" font-weight="900" text-anchor="middle" fill="#581c87"
									font-family="sans-serif">INSTAPAY</text>
							</svg>
						</div>
					</div>

					<!-- Left Group: Gradient InstaPay Branded Card Badge -->
					<div class="ashrafco-pm-brand-card ashrafco-pm-brand-card--instapay">
						<div class="ashrafco-instapay-badge">
							<span class="ashrafco-instapay-badge__text">INSTA</span>
							<span class="ashrafco-instapay-badge__arrows">
								<svg viewBox="0 0 24 24" fill="none" class="w-4 h-4">
									<path d="M6 5l6 7-6 7" stroke="#f97316" stroke-width="3.5" stroke-linecap="round"
										stroke-linejoin="round" />
									<path d="M13 5l6 7-6 7" stroke="#fbbf24" stroke-width="3.5" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
							</span>
							<span class="ashrafco-instapay-badge__text">PAY</span>
						</div>
					</div>
				</div>

		<?php else: ?>
				<!-- Fallback Generic Payment Gateway -->
				<div class="ashrafco-pm-header-row">
					<div class="ashrafco-pm-main-group">
						<span class="ashrafco-pm-radio" aria-hidden="true">
							<span class="ashrafco-pm-radio__inner"></span>
						</span>
						<div class="ashrafco-pm-text-block">
							<h4 class="ashrafco-pm-title"><?php echo esc_html($gateway->get_title()); ?></h4>
							<?php if ($gateway->get_description()): ?>
									<p class="ashrafco-pm-subtitle"><?php echo esc_html($gateway->get_description()); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
		<?php endif; ?>

	</label>

	<!-- Payment Method Details Box (Displayed when selected or always active for clarity) -->
	<?php if ('vodafone_cash' === $gateway_id): ?>

			<div
				class="payment_box payment_method_vodafone_cash ashrafco-pm-body <?php echo !$is_chosen ? 'ashrafco-hidden-box' : ''; ?>">

				<!-- Interactive Middle Row: Copy Number & WhatsApp Button -->
				<div class="ashrafco-pm-action-row ashrafco-pm-action-row--vf">

					<!-- Copy Phone Number Pill -->
					<div class="ashrafco-copy-box">
						<button type="button" class="ashrafco-copy-btn" data-ashrafco-copy="<?php echo esc_attr($vf_number); ?>"
							title="اضغط لنسخ الرقم">
							<span class="ashrafco-copy-btn__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
									<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
								</svg>
							</span>
							<span class="ashrafco-copy-btn__val" dir="ltr"><?php echo esc_html($vf_number); ?></span>
						</button>
						<span class="ashrafco-copy-box__hint">اضغط لنسخ الرقم</span>
					</div>

					<!-- WhatsApp Direct Contact Button -->
					<div class="ashrafco-copy-box ashrafco-copy-box--wa">
						<a href="<?php echo esc_url($wa_url_vf); ?>" target="_blank" rel="noopener noreferrer"
							class="ashrafco-pm-whatsapp-btn">
							<svg class="ashrafco-pm-whatsapp-icon" viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.888 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
							</svg>
							<span>تواصل عبر واتساب</span>
						</a>
					</div>
				</div>

				<!-- Instructions Alert Box -->
				<div class="ashrafco-pm-instructions">
					<div class="ashrafco-pm-instructions__header">
						<span class="ashrafco-pm-instructions__icon ashrafco-pm-instructions__icon--red" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path fill-rule="evenodd"
									d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
									clip-rule="evenodd" />
							</svg>
						</span>
						<strong class="ashrafco-pm-instructions__title ashrafco-pm-instructions__title--red">تعليمات
							مهمة:</strong>
					</div>
					<ul class="ashrafco-pm-instructions__list">
						<li>قم بإرسال صورة إيصال الدفع عبر واتساب بعد التحويل.</li>
						<li>سيتم تأكيد طلبك بعد التحقق من عملية الدفع.</li>
					</ul>
				</div>

			</div>

	<?php elseif ('instapay' === $gateway_id): ?>

			<div
				class="payment_box payment_method_instapay ashrafco-pm-body <?php echo !$is_chosen ? 'ashrafco-hidden-box' : ''; ?>">

				<!-- Interactive Middle Row: Registered Phone + Account IPA + WhatsApp -->
				<div class="ashrafco-pm-action-row ashrafco-pm-action-row--instapay">

					<!-- Registered Phone Number Copy Box -->
					<div class="ashrafco-copy-box">
						<span class="ashrafco-copy-box__label">رقم الهاتف المسجل</span>
						<button type="button" class="ashrafco-copy-btn ashrafco-copy-btn--purple"
							data-ashrafco-copy="<?php echo esc_attr($ipa_phone); ?>" title="اضغط لنسخ الرقم">
							<span class="ashrafco-copy-btn__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
									<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
								</svg>
							</span>
							<span class="ashrafco-copy-btn__val" dir="ltr"><?php echo esc_html($ipa_phone); ?></span>
						</button>
						<span class="ashrafco-copy-box__hint">اضغط لنسخ الرقم</span>
					</div>

					<!-- Account IPA Copy Box -->
					<div class="ashrafco-copy-box">
						<span class="ashrafco-copy-box__label">عنوان الحساب</span>
						<button type="button" class="ashrafco-copy-btn ashrafco-copy-btn--purple"
							data-ashrafco-copy="<?php echo esc_attr($ipa_address); ?>" title="اضغط لنسخ عنوان الحساب">
							<span class="ashrafco-copy-btn__icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
									<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
								</svg>
							</span>
							<span class="ashrafco-copy-btn__val" dir="ltr"><?php echo esc_html($ipa_address); ?></span>
						</button>
						<span class="ashrafco-copy-box__hint">اضغط لنسخ عنوان الحساب</span>
					</div>

					<!-- WhatsApp Direct Contact Button -->
					<div class="ashrafco-copy-box ashrafco-copy-box--wa">
						<span class="ashrafco-copy-box__label ashrafco-copy-box__label--spacer" aria-hidden="true">&nbsp;</span>
						<a href="<?php echo esc_url($wa_url_insta); ?>" target="_blank" rel="noopener noreferrer"
							class="ashrafco-pm-whatsapp-btn">
							<svg class="ashrafco-pm-whatsapp-icon" viewBox="0 0 24 24" fill="currentColor">
								<path
									d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.888 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
							</svg>
							<span>تواصل عبر واتساب</span>
						</a>
					</div>
				</div>

				<!-- Instructions Alert Box -->
				<div class="ashrafco-pm-instructions">
					<div class="ashrafco-pm-instructions__header">
						<span class="ashrafco-pm-instructions__icon ashrafco-pm-instructions__icon--purple" aria-hidden="true">
							<svg viewBox="0 0 24 24" fill="currentColor">
								<path fill-rule="evenodd"
									d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
									clip-rule="evenodd" />
							</svg>
						</span>
						<strong class="ashrafco-pm-instructions__title ashrafco-pm-instructions__title--purple">تعليمات
							مهمة:</strong>
					</div>
					<ul class="ashrafco-pm-instructions__list">
						<li>تأكد من إرسال صورة إيصال الدفع عبر واتساب بعد التحويل.</li>
						<li>سيتم تأكيد طلبك بعد التحقق من عملية الدفع.</li>
					</ul>
				</div>

			</div>

	<?php elseif ($gateway->has_fields() || $gateway->get_description()): ?>

			<div
				class="payment_box payment_method_<?php echo esc_attr($gateway_id); ?> ashrafco-pm-body <?php echo !$is_chosen ? 'ashrafco-hidden-box' : ''; ?>">
				<?php $gateway->payment_fields(); ?>
			</div>

	<?php endif; ?>

</li>