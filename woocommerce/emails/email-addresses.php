<?php
/**
 * Modern Branded Email Customer Addresses Template for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'right';

$billing_address  = $order->get_formatted_billing_address();
$shipping_address = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() ? $order->get_formatted_shipping_address() : '';

$billing_phone = $order->get_billing_phone();
$billing_email = $order->get_billing_email();
$billing_name  = $order->get_formatted_billing_full_name();

$shipping_name  = $order->get_formatted_shipping_full_name();
$shipping_phone = method_exists( $order, 'get_shipping_phone' ) ? $order->get_shipping_phone() : '';
if ( empty( $shipping_phone ) ) {
	$shipping_phone = $billing_phone;
}
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 10px; margin-bottom: 24px; direction: rtl; text-align: right;">
	<tr>
		<?php if ( ! empty( $shipping_address ) ) : ?>
			<!-- 2-Column Responsive Layout for Billing and Shipping -->
			<td class="email-stack" width="48%" style="vertical-align: top; padding-bottom: 12px;">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; direction: rtl; text-align: right;">
					<tr>
						<td style="padding: 12px 16px; background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #0f172a;">
							📍 عنوان وبيانات العميل
						</td>
					</tr>
					<tr>
						<td style="padding: 16px; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.6;">
							<?php if ( $billing_name ) : ?>
								<div style="font-weight: 800; font-size: 13px; color: #0f172a; margin-bottom: 6px;">
									<?php echo esc_html( $billing_name ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $billing_phone ) : ?>
								<div style="margin-bottom: 4px; font-weight: 700; color: #ea580c;">
									📞 هاتف العميل: <span dir="ltr" style="font-family: monospace;"><?php echo esc_html( $billing_phone ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( $billing_email ) : ?>
								<div style="margin-bottom: 8px; color: #64748b; font-size: 11px;">
									✉️ <?php echo esc_html( $billing_email ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $billing_address ) : ?>
								<div style="border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 6px; color: #475569; font-weight: 600;">
									<?php echo wp_kses_post( $billing_address ); ?>
								</div>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</td>

			<td class="email-stack" width="4%" style="font-size: 1px; line-height: 1px;">&nbsp;</td>

			<td class="email-stack" width="48%" style="vertical-align: top; padding-bottom: 12px;">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; direction: rtl; text-align: right;">
					<tr>
						<td style="padding: 12px 16px; background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #0f172a;">
							🚚 عنوان التوصيل والشحن
						</td>
					</tr>
					<tr>
						<td style="padding: 16px; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.6;">
							<?php if ( $shipping_name ) : ?>
								<div style="font-weight: 800; font-size: 13px; color: #0f172a; margin-bottom: 6px;">
									<?php echo esc_html( $shipping_name ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $shipping_phone ) : ?>
								<div style="margin-bottom: 8px; font-weight: 700; color: #16a34a;">
									📞 هاتف الاستلام: <span dir="ltr" style="font-family: monospace;"><?php echo esc_html( $shipping_phone ); ?></span>
								</div>
							<?php endif; ?>

							<div style="border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 6px; color: #475569; font-weight: 600;">
								<?php echo wp_kses_post( $shipping_address ); ?>
							</div>
						</td>
					</tr>
				</table>
			</td>

		<?php else : ?>

			<!-- Full-Width Single Card for Billing Address -->
			<td width="100%" style="vertical-align: top;">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; direction: rtl; text-align: right;">
					<tr>
						<td style="padding: 12px 16px; background-color: #f1f5f9; border-bottom: 1px solid #e2e8f0; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #0f172a;">
							📍 عنوان وبيانات العميل والتوصيل
						</td>
					</tr>
					<tr>
						<td style="padding: 18px 20px; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; color: #334155; line-height: 1.6;">
							<?php if ( $billing_name ) : ?>
								<div style="font-weight: 800; font-size: 14px; color: #0f172a; margin-bottom: 6px;">
									<?php echo esc_html( $billing_name ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $billing_phone ) : ?>
								<div style="margin-bottom: 4px; font-weight: 700; color: #ea580c;">
									📞 رقم الهاتف: <span dir="ltr" style="font-family: monospace;"><?php echo esc_html( $billing_phone ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( $billing_email ) : ?>
								<div style="margin-bottom: 10px; color: #64748b; font-size: 12px;">
									✉️ البريد الإلكتروني: <?php echo esc_html( $billing_email ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $billing_address ) : ?>
								<div style="border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 8px; color: #475569; font-weight: 600;">
									العنوان التفصيلي:<br>
									<?php echo wp_kses_post( $billing_address ); ?>
								</div>
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</td>

		<?php endif; ?>
	</tr>
</table>
