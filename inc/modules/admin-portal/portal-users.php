<?php
/**
 * Ashrafco Admin Portal - Users Management Module
 *
 * Presentation helpers, badge renderers, avatar builders, and dual-mode
 * fallback registrations for Tab 6 (مستخدمو النظام).
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render user avatar with initials and styled background matching the reference design.
 *
 * @param array|object $user User array or WP_User object.
 * @param string       $size Size classes (default 'w-9 h-9 text-sm').
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_user_avatar' ) ) {
	function ashrafco_portal_render_user_avatar( $user, $size = 'w-9 h-9 text-sm' ) {
		$name    = is_array( $user ) ? ( $user['display_name'] ?? $user['user_login'] ?? 'U' ) : ( $user->display_name ?: $user->user_login );
		$initial = is_array( $user ) ? ( $user['avatar_initial'] ?? mb_substr( $name, 0, 1, 'UTF-8' ) ) : mb_substr( $name, 0, 1, 'UTF-8' );
		$initial = mb_strtoupper( $initial, 'UTF-8' );

		$color_class = is_array( $user ) && ! empty( $user['avatar_color_class'] )
			? $user['avatar_color_class']
			: ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_users()
				? ashrafco_core()->get_users()->get_avatar_color_class( is_array( $user ) ? $user['id'] : $user->ID, is_array( $user ) ? $user['user_login'] : $user->user_login )
				: 'bg-slate-900' );

		return sprintf(
			'<div class="%s rounded-xl %s text-white font-black flex items-center justify-center flex-shrink-0 shadow-2xs select-none">%s</div>',
			esc_attr( $size ),
			esc_attr( $color_class ),
			esc_html( $initial )
		);
	}
}

/**
 * Render visually distinct role badge matching the reference design.
 *
 * Distinct palettes:
 * - مدير المتجر (shop_manager): soft pink/rose
 * - دعم العملاء (customer_support): soft blue/indigo
 * - محرر المنتجات (product_editor / editor): soft purple
 * - مدير النظام (administrator): soft sky/blue
 * - Other roles: soft slate
 *
 * @param string $role_key Role slug.
 * @param string $role_label Optional translated label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_role_badge' ) ) {
	function ashrafco_portal_render_role_badge( $role_key, $role_label = '' ) {
		if ( empty( $role_label ) && function_exists( 'ashrafco_get_role_label' ) ) {
			$role_label = ashrafco_get_role_label( $role_key );
		}
		if ( empty( $role_label ) ) {
			$role_label = ucfirst( str_replace( '_', ' ', $role_key ) );
		}

		$badge_styles = array(
			'shop_manager'     => 'bg-rose-50 text-rose-700 border-rose-200/70',
			'customer_support' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
			'product_editor'   => 'bg-purple-50 text-purple-700 border-purple-200/70',
			'editor'           => 'bg-purple-50 text-purple-700 border-purple-200/70',
			'administrator'    => 'bg-sky-50 text-sky-700 border-sky-200/70',
			'customer'         => 'bg-slate-100 text-slate-700 border-slate-200',
			'author'           => 'bg-amber-50 text-amber-700 border-amber-200/70',
			'contributor'      => 'bg-teal-50 text-teal-700 border-teal-200/70',
			'subscriber'       => 'bg-slate-50 text-slate-600 border-slate-200/60',
		);

		$style = $badge_styles[ $role_key ] ?? 'bg-slate-100 text-slate-700 border-slate-200';

		return sprintf(
			'<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold border %s shadow-2xs whitespace-nowrap">%s</span>',
			esc_attr( $style ),
			esc_html( $role_label )
		);
	}
}

/**
 * Render status badge with leading indicator dot matching the reference design.
 *
 * @param string $status_key 'active'|'pending'|'suspended'.
 * @param string $status_label Optional label.
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_status_badge' ) ) {
	function ashrafco_portal_render_status_badge( $status_key, $status_label = '' ) {
		if ( empty( $status_label ) && function_exists( 'ashrafco_get_status_label' ) ) {
			$status_label = ashrafco_get_status_label( $status_key );
		}
		if ( empty( $status_label ) ) {
			$labels = array(
				'active'    => 'نشط',
				'pending'   => 'بانتظار التفعيل',
				'suspended' => 'موقوف',
			);
			$status_label = $labels[ $status_key ] ?? $status_key;
		}

		$config = array(
			'active'    => array(
				'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
				'dot'   => 'bg-emerald-500',
			),
			'pending'   => array(
				'badge' => 'bg-amber-50 text-amber-800 border-amber-200/70',
				'dot'   => 'bg-amber-500',
			),
			'suspended' => array(
				'badge' => 'bg-rose-50 text-rose-700 border-rose-200/70',
				'dot'   => 'bg-rose-500',
			),
		);

		$c = $config[ $status_key ] ?? $config['active'];

		return sprintf(
			'<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border %s shadow-2xs whitespace-nowrap"><span class="w-1.5 h-1.5 rounded-full %s flex-shrink-0"></span>%s</span>',
			esc_attr( $c['badge'] ),
			esc_attr( $c['dot'] ),
			esc_html( $status_label )
		);
	}
}

/**
 * Render last activity indicator with dot matching the reference design.
 *
 * @param string $formatted_activity e.g. "اليوم، 12:40 ص" or "أمس" or "—".
 * @return string HTML string.
 */
if ( ! function_exists( 'ashrafco_portal_render_activity_indicator' ) ) {
	function ashrafco_portal_render_activity_indicator( $formatted_activity ) {
		$is_empty = empty( $formatted_activity ) || '—' === trim( $formatted_activity );

		$dot_class  = $is_empty ? 'bg-slate-300' : 'bg-emerald-500';
		$text_class = $is_empty ? 'text-slate-400' : 'text-slate-700';

		return sprintf(
			'<span class="inline-flex items-center gap-1.5 text-xs font-semibold %s"><span class="w-1.5 h-1.5 rounded-full %s flex-shrink-0"></span>%s</span>',
			esc_attr( $text_class ),
			esc_attr( $dot_class ),
			esc_html( $formatted_activity ?: '—' )
		);
	}
}

/**
 * Legacy Fallback Registrations
 *
 * Only registered when ashrafco-core plugin is NOT active.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	// Fallback AJAX stubs if core plugin is disabled
	add_action( 'wp_ajax_ashrafco_portal_get_users', function() {
		check_ajax_referer( 'ashrafco_portal_users_nonce', 'nonce' );
		wp_send_json_error( array( 'message' => 'Ashrafco Core plugin is required for user management operations.' ) );
	} );
}
