<?php
/**
 * Modern Branded Email Order Items List for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

$text_align  = is_rtl() ? 'right' : 'right';
$margin_side = is_rtl() ? 'left' : 'right';

foreach ( $items as $item_id => $item ) :
	$product       = $item->get_product();
	$sku           = '';
	$purchase_note = '';
	$image         = '';

	if ( is_object( $product ) ) {
		$sku           = $product->get_sku();
		$purchase_note = $product->get_purchase_note();
	}
	?>
	<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>" style="border-bottom: 1px solid #f1f5f9;">
		<!-- Product Details Cell -->
		<td style="padding: 14px 16px; font-family: 'Cairo', Arial, sans-serif; text-align: right; vertical-align: top; direction: rtl;">
			<div style="font-size: 13px; font-weight: 700; color: #0f172a; line-height: 1.5;">
				<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); ?>
			</div>

			<?php if ( $show_sku && $sku ) : ?>
				<div style="font-size: 11px; font-family: monospace; color: #94a3b8; margin-top: 3px;">
					كود المعدة (SKU): <?php echo esc_html( $sku ); ?>
				</div>
			<?php endif; ?>

			<?php
			// Output Item Metadata (Variations, Specs)
			wc_display_item_meta(
				$item,
				array(
					'before'    => '<div style="font-size: 11px; color: #64748b; margin-top: 4px; line-height: 1.4;">',
					'after'     => '</div>',
					'separator' => '<br>',
					'echo'      => true,
					'autop'     => false,
				)
			);
			?>
		</td>

		<!-- Quantity Cell -->
		<td style="padding: 14px 12px; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 700; color: #334155; text-align: center; vertical-align: top; width: 60px;">
			<?php
			$qty_display = apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item );
			echo esc_html( $qty_display );
			?>
		</td>

		<!-- Price Subtotal Cell -->
		<td style="padding: 14px 16px; font-family: 'Cairo', Arial, sans-serif; font-size: 13px; font-weight: 800; color: #0f172a; text-align: left; vertical-align: top; width: 110px;">
			<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
		</td>
	</tr>

	<?php if ( $show_purchase_note && $purchase_note ) : ?>
		<tr>
			<td colspan="3" style="text-align: right; vertical-align: middle; font-family: 'Cairo', Arial, sans-serif; font-size: 12px; color: #475569; padding: 8px 16px; background-color: #f8fafc; border-bottom: 1px solid #f1f5f9;">
				<?php echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) ); ?>
			</td>
		</tr>
	<?php endif; ?>

<?php endforeach; ?>
