<?php
/**
 * Ashrafco Admin Portal - Coupon Management Presentation Module
 *
 * Presentation helpers, badge renderers, date formatters, and fallback registrations
 * for Tab 5 (كوبونات الخصم).
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render visually distinct coupon status pill/toggle matching the reference design screenshot.
 *
 * Active: Green pill with toggle indicator (switch to the right in RTL)
 * Scheduled: Blue pill with toggle indicator (switch to the left in RTL)
 * Ending Soon: Orange pill with clock icon
 * Expired: Neutral gray pill with check/cross icon
 * Disabled/Paused: Rose/Red pill with toggle indicator
 *
 * @param string $status_key 'active'|'scheduled'|'ending_soon'|'expired'|'disabled'.
 * @param string $label Optional translated label.
 * @param int $coupon_id Optional coupon ID for interactive toggle.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_coupon_status_badge' ) ) {
	function ashrafco_portal_render_coupon_status_badge( $status_key, $label = '', $coupon_id = 0 ) {
		$coupon_id_attr = $coupon_id ? sprintf( ' data-id="%d"', absint( $coupon_id ) ) : '';

		switch ( $status_key ) {
			case 'active':
				$display_text = ! empty( $label ) ? $label : __( 'نشط', 'ashrafco' );
				return sprintf(
					'<button type="button" class="portal-coupon-toggle-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs select-none hover:bg-emerald-100 transition-colors cursor-pointer"%s data-status="active" title="%s">
						<span>%s</span>
						<span class="w-7 h-4 bg-emerald-500 rounded-full inline-flex items-center justify-end px-0.5 flex-shrink-0">
							<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
						</span>
					</button>',
					$coupon_id_attr,
					esc_attr__( 'انقر لإيقاف الكوبون', 'ashrafco' ),
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

			case 'ending_soon':
				$display_text = ! empty( $label ) ? $label : __( 'ينتهي قريباً', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs select-none">
						<span>%s</span>
						<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</span>',
					esc_html( $display_text )
				);

			case 'expired':
				$display_text = ! empty( $label ) ? $label : __( 'منتهي', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-2xs select-none">
						<span>%s</span>
						<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</span>',
					esc_html( $display_text )
				);

			case 'disabled':
			default:
				$display_text = ! empty( $label ) ? $label : __( 'موقوف', 'ashrafco' );
				return sprintf(
					'<button type="button" class="portal-coupon-toggle-status inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs select-none hover:bg-rose-100 transition-colors cursor-pointer"%s data-status="disabled" title="%s">
						<span>%s</span>
						<span class="w-7 h-4 bg-rose-400 rounded-full inline-flex items-center justify-start px-0.5 flex-shrink-0">
							<span class="w-3 h-3 bg-white rounded-full shadow-xs"></span>
						</span>
					</button>',
					$coupon_id_attr,
					esc_attr__( 'انقر لتفعيل الكوبون', 'ashrafco' ),
					esc_html( $display_text )
				);
		}
	}
}

/**
 * Render discount type badge with icon matching reference design.
 *
 * @param string $type 'percent'|'fixed_cart'|'fixed_product'.
 * @param string $label Optional translated label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_coupon_type_badge' ) ) {
	function ashrafco_portal_render_coupon_type_badge( $type, $label = '' ) {
		switch ( $type ) {
			case 'percent':
				$display_text = ! empty( $label ) ? $label : __( 'نسبة مئوية', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
						<svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zm11 11a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
						</svg>
						<span>%s</span>
					</span>',
					esc_html( $display_text )
				);

			case 'fixed_cart':
				$display_text = ! empty( $label ) ? $label : __( 'مبلغ ثابت', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
						<svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
						</svg>
						<span>%s</span>
					</span>',
					esc_html( $display_text )
				);

			case 'fixed_product':
			default:
				$display_text = ! empty( $label ) ? $label : __( 'مبلغ ثابت على المنتج', 'ashrafco' );
				return sprintf(
					'<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-slate-50 text-slate-700 border border-slate-200/80">
						<svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
						</svg>
						<span>%s</span>
					</span>',
					esc_html( $display_text )
				);
		}
	}
}

/**
 * Fallback Registrations if ashrafco-core is inactive.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	add_action( 'wp_ajax_ashrafco_portal_get_coupons', function() {
		check_ajax_referer( 'ashrafco_portal_coupons_nonce', 'nonce' );
		wp_send_json_error( array( 'message' => __( 'يتطلب نظام الكوبونات تفعيل إضافة Ashrafco Core.', 'ashrafco' ) ) );
	} );
}
