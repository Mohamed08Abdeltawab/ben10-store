<?php
/**
 * Modern Branded Email Footer for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.4.0
 */

defined('ABSPATH') || exit;

$phone_display = function_exists('ashrafco_get_contact_phone') ? ashrafco_get_contact_phone() : '+201065712366';
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', apply_filters('test_theme_whatsapp_phone', '201065712366'));
$store_name_ar = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$store_name_en = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('en') : 'Ashrafco';
$store_tagline = function_exists('ashrafco_settings') ? ashrafco_settings()->get('tagline', 'التميز في معدات وأدوات تركيب السيراميك') : 'التميز في معدات وأدوات تركيب السيراميك';
$working_hours = function_exists('ashrafco_settings') ? ashrafco_settings()->get('working_hours', 'يومياً من 9:00 صباحاً إلى 10:00 مساءً') : 'يومياً من 9:00 صباحاً إلى 10:00 مساءً';
$address = function_exists('ashrafco_get_store_address') ? ashrafco_get_store_address() : 'جمهورية مصر العربية — خدمة الشحن السريع لكافة محافظات الجمهورية';
$wa_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً خدمة عملاء %s، أود الاستفسار بخصوص طلبي.', $store_name_ar));
$current_year = date_i18n('Y');
?>
</td>
</tr>
<!-- End of Main Email Body Content -->

<!-- Trust & Delivery Notice Card -->
<tr>
	<td style="padding: 0 28px 24px; background-color: #ffffff; direction: rtl; text-align: right;">
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
			style="background-color: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; direction: rtl; text-align: right;">
			<tr>
				<td style="padding: 16px 18px;">
					<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="vertical-align: top; font-family: 'Cairo', Arial, sans-serif;">
								<div style="font-size: 13px; font-weight: 800; color: #92400e; margin-bottom: 4px;">
									🛡️ هام للمشتري: فحص ومعاينة الشحنة
								</div>
								<div style="font-size: 12px; font-weight: 600; color: #78350f; line-height: 1.6;">
									يحق لك معاينة ماكينات ومعدات البورسلين وفحصها مع مندوب الشحن قبل دفع المبلغ (في حالة
									الدفع عند الاستلام). نضمن لك وصول كافة المنتجات بحالتها الأصلية مع بطاقة الضمان
									المعتمد.
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>

<!-- Direct Customer Support Row -->
<tr>
	<td
		style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 28px; direction: rtl; text-align: center;">
		<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="center" style="font-family: 'Cairo', Arial, sans-serif;">
					<h4 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
						هل تحتاج إلى مساعدة أو استفسار فني؟
					</h4>
					<p style="font-size: 12px; font-weight: 500; color: #64748b; margin: 0 0 16px 0;">
						فريق الدعم الفني لمعدات السيراميك متاح للرد على استفساراتك وتتبع شحنتك.
					</p>

					<!-- WhatsApp CTA Button -->
					<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto 12px;">
						<tr>
							<td align="center" style="border-radius: 24px; background-color: #25D366;">
								<a href="<?php echo esc_url($wa_url); ?>" target="_blank"
									style="display: inline-block; padding: 10px 24px; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #ffffff; text-decoration: none; border-radius: 24px;">
									💬 تواصل معنا عبر واتساب (<?php echo esc_html($phone_display); ?>)
								</a>
							</td>
						</tr>
					</table>

					<div style="font-size: 11px; font-weight: 600; color: #94a3b8;">
						⏰ مواعيد العمل وخدمة العملاء: <?php echo esc_html($working_hours); ?>
					</div>
				</td>
			</tr>
		</table>
	</td>
</tr>

<!-- Sub-Footer / Copyright & Location -->
<tr>
	<td style="background-color: #0f172a; padding: 20px 24px; direction: rtl; text-align: center;">
		<div
			style="font-family: 'Cairo', Arial, sans-serif; font-size: 12px; font-weight: 700; color: #cbd5e1; margin-bottom: 4px;">
			متجر <?php echo esc_html($store_name_ar); ?> (<?php echo esc_html($store_name_en); ?>) —
			<?php echo esc_html($store_tagline); ?>
		</div>
		<div style="font-family: 'Cairo', Arial, sans-serif; font-size: 11px; color: #64748b; line-height: 1.5;">
			📍 <?php echo esc_html($address); ?><br>
			جميع الحقوق محفوظة لمتجر <?php echo esc_html($store_name_ar); ?>
			(<?php echo esc_html($store_name_en); ?>) © <?php echo esc_html($current_year); ?>
		</div>
	</td>
</tr>

</table>
<!-- End Main Card Container -->

</td>
</tr>
</table>

</body>

</html>