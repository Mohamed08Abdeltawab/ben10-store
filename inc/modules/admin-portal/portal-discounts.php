<?php
/**
 * Ashrafco Admin Portal - Discount Management Presentation Module
 *
 * Presentation helpers, badge renderers, date formatters, and dual-mode
 * fallback registrations for Tab 4 (إدارة الخصومات).
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render visually distinct status pill/toggle matching the reference design screenshot.
 *
 * Active: Green pill with toggle indicator
 * Scheduled: Blue pill with toggle indicator
 * Expired: Neutral gray pill
 * No discount: Neutral gray pill
 *
 * @param string $status_key 'active'|'scheduled'|'expired'|'none'.
 * @param string $label Optional translated label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_discount_status_badge' ) ) {
	function ashrafco_portal_render_discount_status_badge( $status_key, $label = '' ) {
		switch ( $status_key ) {
			case 'active':
				$display_text = ! empty( $label ) ? $label : __( 'نشط', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs select-none">
						<span>%s</span>
						<span class="w-7 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-end px-0.5 flex-shrink-0">
							<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
						</span>
					</span>',
					esc_html( $display_text )
				);

			case 'paused':
				$display_text = ! empty( $label ) ? $label : __( 'غير نشط', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300 shadow-2xs select-none">
						<span>%s</span>
						<span class="w-7 h-4 bg-slate-300 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0">
							<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
						</span>
					</span>',
					esc_html( $display_text )
				);

			case 'scheduled':
				$display_text = ! empty( $label ) ? $label : __( 'مجدول', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs select-none">
						<span>%s</span>
						<span class="w-7 h-4 bg-blue-500 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0">
							<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
						</span>
					</span>',
					esc_html( $display_text )
				);

			case 'expired':
				$display_text = ! empty( $label ) ? $label : __( 'منتهي', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs select-none">
						<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
						<span>%s</span>
					</span>',
					esc_html( $display_text )
				);

			case 'none':
			default:
				$display_text = ! empty( $label ) ? $label : __( 'بدون خصم', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-400 border border-slate-200 shadow-2xs select-none">
						<span>%s</span>
					</span>',
					esc_html( $display_text )
				);
		}
	}
}

/**
 * Render discount percentage badge matching the reference design.
 *
 * @param int $percent Discount percentage.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_discount_percent_badge' ) ) {
	function ashrafco_portal_render_discount_percent_badge( $percent ) {
		$percent = absint( $percent );
		if ( $percent <= 0 ) {
			return '<span class="text-slate-300 font-mono text-xs">—</span>';
		}

		return sprintf(
			'<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-50 text-rose-600 border border-rose-200/80 shadow-2xs whitespace-nowrap">
				%%%d
			</span>',
			$percent
		);
	}
}

/**
 * AJAX Endpoint: Toggle Product Discount Status (Active <-> Inactive)
 */
add_action( 'wp_ajax_ashrafco_portal_toggle_discount_status', 'ashrafco_portal_handle_ajax_toggle_discount_status' );
if ( ! function_exists( 'ashrafco_portal_handle_ajax_toggle_discount_status' ) ) {
	function ashrafco_portal_handle_ajax_toggle_discount_status() {
		check_ajax_referer( 'ashrafco_portal_discounts_nonce', 'nonce' );

		if ( ! is_user_logged_in() || ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) && ! current_user_can( 'edit_products' ) ) ) {
			wp_send_json_error( array( 'message' => __( 'غير مصرح لك بتعديل خصومات المنتجات.', 'ashrafco' ) ), 403 );
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		if ( ! $product_id ) {
			wp_send_json_error( array( 'message' => __( 'معرف المنتج غير صالح.', 'ashrafco' ) ), 400 );
		}

		// Delegate to Core Discount_Manager if method exists
		if ( class_exists( '\Ashrafco\Core\Discounts\Discount_Manager' ) && method_exists( '\Ashrafco\Core\Discounts\Discount_Manager', 'instance' ) ) {
			$manager = \Ashrafco\Core\Discounts\Discount_Manager::instance();
			if ( method_exists( $manager, 'toggle_product_discount_status' ) ) {
				$result = $manager->toggle_product_discount_status( $product_id );
				if ( is_wp_error( $result ) ) {
					wp_send_json_error( array( 'message' => $result->get_error_message() ), 400 );
				}
				$counts = $manager->get_summary_counts();
				wp_send_json_success( array_merge( $result, array( 'counts' => $counts ) ) );
			}
		}

		$product = wc_get_product( $product_id );
		if ( ! $product instanceof WC_Product ) {
			wp_send_json_error( array( 'message' => __( 'المنتج المحدد غير موجود.', 'ashrafco' ) ), 404 );
		}

		$is_on_sale = $product->is_on_sale();
		$is_paused  = ( 'yes' === $product->get_meta( '_ashrafco_discount_paused' ) );

		if ( $is_on_sale && ! $is_paused ) {
			// DEACTIVATE (Pause)
			$sale_price = $product->get_sale_price();
			$date_from  = $product->get_date_on_sale_from();
			$date_to    = $product->get_date_on_sale_to();

			$product->update_meta_data( '_ashrafco_discount_paused', 'yes' );
			$product->update_meta_data( '_ashrafco_paused_sale_price', $sale_price );
			$product->update_meta_data( '_ashrafco_paused_date_from', $date_from instanceof WC_DateTime ? $date_from->getTimestamp() : '' );
			$product->update_meta_data( '_ashrafco_paused_date_to', $date_to instanceof WC_DateTime ? $date_to->getTimestamp() : '' );

			$product->set_sale_price( '' );
			$product->set_date_on_sale_from( '' );
			$product->set_date_on_sale_to( '' );

			if ( $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $variation_id ) {
					$variation = wc_get_product( $variation_id );
					if ( $variation instanceof WC_Product ) {
						$variation->update_meta_data( '_ashrafco_discount_paused', 'yes' );
						$variation->update_meta_data( '_ashrafco_paused_sale_price', $variation->get_sale_price() );
						$variation->set_sale_price( '' );
						$variation->save();
					}
				}
			}

			$product->save();
			wc_delete_product_transients( $product_id );

			$counts = class_exists( '\Ashrafco\Core\Discounts\Discount_Manager' ) 
				? \Ashrafco\Core\Discounts\Discount_Manager::instance()->get_summary_counts()
				: array();

			wp_send_json_success( array(
				'new_status' => 'paused',
				'message'    => __( 'تم تعطيل الخصم بنجاح.', 'ashrafco' ),
				'counts'     => $counts,
			) );
		} else {
			// ACTIVATE (Unpause)
			$paused_price  = $product->get_meta( '_ashrafco_paused_sale_price' );
			$regular_price = (float) $product->get_regular_price();

			if ( empty( $paused_price ) || (float) $paused_price <= 0 || ( $regular_price > 0 && (float) $paused_price >= $regular_price ) ) {
				wp_send_json_success( array(
					'needs_price' => true,
					'message'     => __( 'يرجى إدخال سعر الخصم لتفعيل التخفيض.', 'ashrafco' ),
				) );
			}

			$now_ts  = time();
			$from_ts = (int) $product->get_meta( '_ashrafco_paused_date_from' );
			$to_ts   = (int) $product->get_meta( '_ashrafco_paused_date_to' );

			$product->set_sale_price( (float) $paused_price );
			$product->set_date_on_sale_from( $from_ts > 0 ? $from_ts : '' );
			if ( $to_ts > 0 && $to_ts < $now_ts ) {
				$product->set_date_on_sale_to( '' );
			} else {
				$product->set_date_on_sale_to( $to_ts > 0 ? $to_ts : '' );
			}

			$product->delete_meta_data( '_ashrafco_discount_paused' );

			if ( $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $variation_id ) {
					$variation = wc_get_product( $variation_id );
					if ( $variation instanceof WC_Product ) {
						$v_paused = $variation->get_meta( '_ashrafco_paused_sale_price' );
						if ( ! empty( $v_paused ) && (float) $v_paused > 0 ) {
							$variation->set_sale_price( (float) $v_paused );
						}
						$variation->delete_meta_data( '_ashrafco_discount_paused' );
						$variation->save();
					}
				}
			}

			$product->save();
			wc_delete_product_transients( $product_id );

			$counts = class_exists( '\Ashrafco\Core\Discounts\Discount_Manager' ) 
				? \Ashrafco\Core\Discounts\Discount_Manager::instance()->get_summary_counts()
				: array();

			wp_send_json_success( array(
				'new_status' => 'active',
				'message'    => __( 'تم تفعيل الخصم بنجاح.', 'ashrafco' ),
				'counts'     => $counts,
			) );
		}
	}
}

/**
 * Legacy Fallback Registrations
 *
 * Only registered when ashrafco-core plugin is NOT active.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	add_action( 'wp_ajax_ashrafco_portal_get_discounts', function() {
		check_ajax_referer( 'ashrafco_portal_discounts_nonce', 'nonce' );
		wp_send_json_error( array( 'message' => 'Ashrafco Core plugin is required for discount operations.' ) );
	} );
}
