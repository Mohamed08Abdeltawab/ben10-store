<?php
/**
 * Modern Branded Order Details Email Template for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'right';

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );

$payment_method_title = $order->get_payment_method_title();
?>

<!-- Order Number & Info Heading -->
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 18px; direction: rtl; text-align: right;">
	<tr>
		<td style="font-family: 'Cairo', Arial, sans-serif;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="vertical-align: middle;">
						<span style="font-size: 16px; font-weight: 800; color: #0f172a;">تفاصيل الطلب:</span>
						<span style="display: inline-block; background-color: #ffedd5; color: #ea580c; font-size: 14px; font-weight: 800; padding: 2px 10px; border-radius: 6px; font-family: monospace; margin-right: 6px;">
							#<?php echo esc_html( $order->get_order_number() ); ?>
						</span>
					</td>
					<td align="left" style="vertical-align: middle; font-size: 12px; font-weight: 600; color: #64748b;">
						<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!-- Payment Method Highlight Card -->
<?php if ( ! empty( $payment_method_title ) ) : ?>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 20px; direction: rtl; text-align: right;">
	<tr>
		<td style="padding: 12px 16px; font-family: 'Cairo', Arial, sans-serif;">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="font-size: 12px; font-weight: 700; color: #475569;">
						<span style="color: #64748b;">طريقة الدفع:</span>
						<strong style="color: #0f172a; margin-right: 6px; font-size: 13px;">
							💵 <?php echo esc_html( $payment_method_title ); ?>
						</strong>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php endif; ?>

<!-- Products & Order Items Table -->
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="width: 100%; border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 24px; direction: rtl; text-align: right;">
	<thead>
		<tr style="background-color: #f8fafc; border-bottom: 1.5px solid #e2e8f0;">
			<th scope="col" style="padding: 12px 16px; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; font-weight: 800; color: #334155; text-align: right;">
				المنتج والمعدة
			</th>
			<th scope="col" style="padding: 12px 12px; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; font-weight: 800; color: #334155; text-align: center; width: 60px;">
				الكمية
			</th>
			<th scope="col" style="padding: 12px 16px; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; font-weight: 800; color: #334155; text-align: left; width: 110px;">
				السعر
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		echo wc_get_email_order_items( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$order,
			array(
				'show_sku'      => $sent_to_admin,
				'show_image'    => false,
				'image_size'    => array( 32, 32 ),
				'plain_text'    => $plain_text,
				'sent_to_admin' => $sent_to_admin,
			)
		);
		?>
	</tbody>
	<tfoot>
		<?php
		$item_totals = $order->get_order_item_totals();

		if ( $item_totals ) {
			$total_count = count( $item_totals );
			$row_index   = 0;

			foreach ( $item_totals as $total ) {
				$row_index++;
				$is_final_total = ( $row_index === $total_count );

				$row_bg     = $is_final_total ? '#f8fafc' : '#ffffff';
				$label_col  = $is_final_total ? '#0f172a' : '#475569';
				$val_col    = $is_final_total ? '#ea580c' : '#1e293b';
				$font_sz    = $is_final_total ? '15px' : '13px';
				$font_wt    = $is_final_total ? '800' : '600';
				$border_top = $is_final_total ? '2px solid #cbd5e1' : '1px solid #f1f5f9';
				?>
				<tr style="background-color: <?php echo esc_attr( $row_bg ); ?>; border-top: <?php echo esc_attr( $border_top ); ?>;">
					<th scope="row" colspan="2" style="padding: 12px 16px; font-family: 'Cairo', Arial, sans-serif; font-size: <?php echo esc_attr( $font_sz ); ?>; font-weight: <?php echo esc_attr( $font_wt ); ?>; color: <?php echo esc_attr( $label_col ); ?>; text-align: right;">
						<?php echo esc_html( $total['label'] ); ?>
					</th>
					<td style="padding: 12px 16px; font-family: 'Cairo', Arial, sans-serif; font-size: <?php echo esc_attr( $font_sz ); ?>; font-weight: <?php echo esc_attr( $font_wt ); ?>; color: <?php echo esc_attr( $val_col ); ?>; text-align: left;">
						<?php echo wp_kses_post( $total['value'] ); ?>
					</td>
				</tr>
				<?php
			}
		}
		?>
	</tfoot>
</table>

<!-- Customer Notes Box (If present) -->
<?php if ( $order->get_customer_note() ) : ?>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; margin-bottom: 22px; direction: rtl; text-align: right;">
	<tr>
		<td style="padding: 14px 18px; font-family: 'Cairo', Arial, sans-serif;">
			<div style="font-size: 12px; font-weight: 800; color: #475569; margin-bottom: 4px;">
				📝 ملاحظات خاصة مع الطلب:
			</div>
			<div style="font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.6;">
				<?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?>
			</div>
		</td>
	</tr>
</table>
<?php endif; ?>

<?php
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );
