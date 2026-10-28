<?php
/**
 * Modern Branded Email Header for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

// Retrieve email and order objects from template args or captured global state
if ( ! isset( $email ) || ! is_object( $email ) ) {
	if ( ! empty( $GLOBALS['ashrafco_current_email'] ) && is_object( $GLOBALS['ashrafco_current_email'] ) ) {
		$email = $GLOBALS['ashrafco_current_email'];
	}
}

if ( ! isset( $order ) || ! is_a( $order, 'WC_Order' ) ) {
	if ( isset( $email ) && isset( $email->object ) && is_a( $email->object, 'WC_Order' ) ) {
		$order = $email->object;
	} elseif ( ! empty( $GLOBALS['ashrafco_current_email_order'] ) && is_a( $GLOBALS['ashrafco_current_email_order'], 'WC_Order' ) ) {
		$order = $GLOBALS['ashrafco_current_email_order'];
	} else {
		$order = false;
	}
}

$email_id     = isset( $email ) && isset( $email->id ) ? $email->id : '';
$order_status = $order ? $order->get_status() : '';

$store_name_ar = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو';
$store_name_en = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'en' ) : 'Ashrafco';
$store_tagline = function_exists( 'ashrafco_settings' ) ? ashrafco_settings()->get( 'tagline', 'معدات وأدوات تركيب السيراميك والبورسلين في مصر' ) : 'معدات وأدوات تركيب السيراميك والبورسلين في مصر';

// Determine dynamic greeting banner
$badge_info  = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $order_status ? $order_status : 'processing' ) : null;
$badge_color = $badge_info ? $badge_info['color'] : '#f59e0b';
$badge_bg    = $badge_info ? $badge_info['bg_color'] : '#fffbeb';
$badge_label = $badge_info ? $badge_info['label'] : wc_get_order_status_name( $order_status ? $order_status : 'processing' );

if ( 'shipped' === $order_status ) {
	$status_banner_title = 'شحنتك في الطريق إليك! 🚀';
	$status_banner_desc  = 'تم تسليم شحنتك لمندوب الشحن والتوصيل، وسيتواصل معك قريباً للمعاينة والاستلام.';
} elseif ( 'completed' === $order_status || 'customer_completed_order' === $email_id ) {
	$status_banner_title = 'تم تسليم طلبك بنجاح! 🎉';
	$status_banner_desc  = 'تم استلام وتأكيد تسليم شحنتك بنجاح. شكراً لثقتك في أشرفكو.';
} elseif ( 'cancelled' === $order_status ) {
	$status_banner_title = 'تم إلغاء الطلب';
	$status_banner_desc  = 'تم إلغاء هذا الطلب. إذا كان لديك أي استفسار يسعدنا تواصلك مع خدمة العملاء.';
} else {
	// 'processing' or default
	$status_banner_title = sprintf( 'شكراً لطلبك من %s (%s)! تم استلام طلبك بنجاح 🎉', $store_name_ar, $store_name_en );
	$status_banner_desc  = 'طلبك قيد المعالجة والتجهيز الفوري في مستودعاتنا لضمان الفحص الفني قبل الشحن.';
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	<style type="text/css">
		@import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap');
		body, table, td, p, a, li, blockquote {
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
			font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Tahoma, Arial, sans-serif;
		}
		img {
			-ms-interpolation-mode: bicubic;
			border: 0;
			outline: none;
			text-decoration: none;
		}
		table {
			border-collapse: collapse !important;
		}
		body {
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			background-color: #f1f5f9;
			direction: rtl;
			text-align: right;
		}
		@media screen and (max-width: 600px) {
			.email-container {
				width: 100% !important;
				margin: auto !important;
				border-radius: 0 !important;
			}
			.email-stack {
				display: block !important;
				width: 100% !important;
				box-sizing: border-box !important;
			}
			.email-content-padding {
				padding: 20px 16px !important;
			}
		}
	</style>
</head>
<body style="margin: 0; padding: 30px 10px; background-color: #f1f5f9; direction: rtl; text-align: right; font-family: 'Cairo', Tahoma, Arial, sans-serif;">

	<!-- Top Outer Wrapper -->
	<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; direction: rtl; text-align: right;">
		<tr>
			<td align="center" style="padding: 10px 0;">

				<!-- Main Card Container (Max-Width 640px) -->
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="email-container" style="max-width: 640px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05); direction: rtl; text-align: right;">

					<!-- 1. Full-Width Dark Slate Header (#0f172a) -->
					<tr>
						<td align="center" style="background-color: #0f172a; padding: 34px 24px 28px; text-align: center; border-bottom: 3px solid #ea580c;">
							<!-- Brand Logo Text with Orange Dot -->
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
								<tr>
									<td align="center">
										<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" style="text-decoration: none;">
											<span style="font-family: 'Cairo', Arial, sans-serif; font-size: 28px; font-weight: 900; letter-spacing: -0.5px; color: #ffffff; display: inline-block;">
												<?php echo esc_html( $store_name_ar ); ?> <span style="color: #ea580c; font-size: 22px; font-weight: 700;">(<?php echo esc_html( $store_name_en ); ?>)</span>
											</span>
										</a>
										<div style="font-family: 'Cairo', Arial, sans-serif; font-size: 12px; font-weight: 600; color: #94a3b8; margin-top: 6px; letter-spacing: 0.2px;">
											<?php echo esc_html( $store_tagline ); ?>
										</div>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- 2. Dynamic Order Status Greeting Banner -->
					<tr>
						<td style="background-color: #f8fafc; padding: 22px 28px; border-bottom: 1px solid #e2e8f0; direction: rtl; text-align: right;">
							<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<div style="display: inline-flex; align-items: center; gap: 6px; background-color: <?php echo esc_attr( $badge_bg ); ?>; color: <?php echo esc_attr( $badge_color ); ?>; border: 1px solid <?php echo esc_attr( $badge_color ); ?>33; font-family: 'Cairo', Arial, sans-serif; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; margin-bottom: 8px;">
											<span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background-color: <?php echo esc_attr( $badge_color ); ?>;"></span>
											<span>حالة الطلب: <?php echo esc_html( $badge_label ); ?></span>
										</div>
										<h2 style="font-family: 'Cairo', Arial, sans-serif; font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; line-height: 1.4;">
											<?php echo esc_html( $status_banner_title ); ?>
										</h2>
										<p style="font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 500; color: #64748b; margin: 0; line-height: 1.6;">
											<?php echo esc_html( $status_banner_desc ); ?>
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- 3. Main Email Body Content Starts Here -->
					<tr>
						<td class="email-content-padding" style="padding: 30px 28px; background-color: #ffffff; direction: rtl; text-align: right;">
