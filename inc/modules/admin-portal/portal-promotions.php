<?php
/**
 * Ashrafco Admin Portal - Promotions Management Module
 *
 * Presentation helpers, badge renderers, date formatters, and dual-mode
 * fallback registrations for Tab 4 (إدارة العروض).
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render visually distinct promotion type badge matching the reference design.
 *
 * @param string $type_key Type slug: 'percentage'|'fixed'|'featured'.
 * @param string $label Optional translated label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_promotion_badge' ) ) {
	function ashrafco_portal_render_promotion_badge( $type_key, $label = '' ) {
		if ( empty( $label ) && function_exists( 'ashrafco_get_promotion_type_label' ) ) {
			$label = ashrafco_get_promotion_type_label( $type_key );
		}

		$config = array(
			'percentage' => array(
				'class' => 'bg-purple-50 text-purple-700 border-purple-200/80',
				'svg'   => '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m5 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" /></svg>',
				'text'  => 'خصم نسبة مئوية %',
			),
			'fixed'      => array(
				'class' => 'bg-blue-50 text-blue-700 border-blue-200/80',
				'svg'   => '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>',
				'text'  => 'خصم مبلغ ثابت',
			),
			'featured'   => array(
				'class' => 'bg-rose-50 text-rose-700 border-rose-200/80',
				'svg'   => '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>',
				'text'  => 'صفقة مميزة',
			),
			'campaign'   => array(
				'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
				'svg'   => '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>',
				'text'  => 'حملة ترويجية',
			),
		);

		$c = $config[ $type_key ] ?? $config['percentage'];
		$display_text = ! empty( $label ) ? $label : $c['text'];

		return sprintf(
			'<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border %s shadow-2xs whitespace-nowrap">%s<span>%s</span></span>',
			esc_attr( $c['class'] ),
			$c['svg'],
			esc_html( $display_text )
		);
	}
}

/**
 * Render promotion status badge with leading colored dot.
 *
 * @param string $status_key 'active'|'scheduled'|'expired'|'inactive'.
 * @param string $label Optional label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_promotion_status_badge' ) ) {
	function ashrafco_portal_render_promotion_status_badge( $status_key, $label = '' ) {
		if ( empty( $label ) && function_exists( 'ashrafco_get_promotion_status_label' ) ) {
			$label = ashrafco_get_promotion_status_label( $status_key );
		}
		if ( empty( $label ) ) {
			$labels = array(
				'active'    => 'نشط',
				'scheduled' => 'مجدول',
				'expired'   => 'منتهي',
				'inactive'  => 'معطل',
			);
			$label = $labels[ $status_key ] ?? $status_key;
		}

		$config = array(
			'active'    => array(
				'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
				'dot'   => 'bg-emerald-500',
			),
			'scheduled' => array(
				'badge' => 'bg-amber-50 text-amber-800 border-amber-200/80',
				'dot'   => 'bg-amber-500',
			),
			'expired'   => array(
				'badge' => 'bg-slate-100 text-slate-600 border-slate-200',
				'dot'   => 'bg-slate-400',
			),
			'inactive'  => array(
				'badge' => 'bg-rose-50 text-rose-700 border-rose-200/80',
				'dot'   => 'bg-rose-500',
			),
		);

		$c = $config[ $status_key ] ?? $config['active'];

		return sprintf(
			'<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border %s shadow-2xs whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full %s flex-shrink-0"></span>%s</span>',
			esc_attr( $c['badge'] ),
			esc_attr( $c['dot'] ),
			esc_html( $label )
		);
	}
}

/**
 * Render bold discount value label (e.g. "خصم 20%" or "خصم 500 ج.م").
 *
 * @param array $promo Formatted promotion array.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_promotion_discount_label' ) ) {
	function ashrafco_portal_render_promotion_discount_label( $promo ) {
		$label = $promo['discount_label'] ?? '';
		return sprintf(
			'<span class="text-xs sm:text-sm font-black text-rose-600 tracking-tight whitespace-nowrap">%s</span>',
			esc_html( $label )
		);
	}
}

/**
 * Render promotion date range with calendar icon.
 *
 * @param string $start_date YYYY-MM-DD
 * @param string $end_date   YYYY-MM-DD
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_promotion_dates' ) ) {
	function ashrafco_portal_render_promotion_dates( $start_date, $end_date = '' ) {
		$start_formatted = ! empty( $start_date ) ? str_replace( '-', '/', $start_date ) : '—';

		if ( empty( $end_date ) ) {
			$range_text = sprintf( 'يبدأ من %s', $start_formatted );
		} else {
			$end_formatted = str_replace( '-', '/', $end_date );
			$range_text    = sprintf( '%s — %s', $start_formatted, $end_formatted );
		}

		return sprintf(
			'<span class="inline-flex items-center gap-1.5 text-xs font-mono font-medium text-slate-500 whitespace-nowrap"><svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg><span>%s</span></span>',
			esc_html( $range_text )
		);
	}
}

/**
 * Legacy Fallback Registrations
 *
 * Only registered when ashrafco-core plugin is NOT active.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	add_action( 'wp_ajax_ashrafco_portal_get_promotions', function() {
		check_ajax_referer( 'ashrafco_portal_promotions_nonce', 'nonce' );
		wp_send_json_error( array( 'message' => 'Ashrafco Core plugin is required for promotion operations.' ) );
	} );
}
