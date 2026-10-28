<?php
/**
 * Ashrafco Admin Portal - Orders Management Module
 *
 * Handles order queries, filters, status transitions, summary KPI calculation,
 * CSV export, and AJAX interactions for Tab 2 (إدارة الطلبات).
 *
 * When ashrafco-core is active, business operations are delegated to
 * Ashrafco\Core\Orders\Order_Manager. This file provides presentation helpers
 * and backward-compatible fallback implementations.
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get Summary KPI Counts for Orders
 *
 * @return array
 */
if ( ! function_exists( 'ashrafco_get_orders_summary' ) ) {
	function ashrafco_get_orders_summary() {
		if ( ! function_exists( 'wc_orders_count' ) ) {
			return array(
				'all'        => 0,
				'processing' => 0,
				'shipped'    => 0,
				'completed'  => 0,
				'cancelled'  => 0,
			);
		}

		$processing_count = (int) wc_orders_count( 'processing' ) + (int) wc_orders_count( 'pending' ) + (int) wc_orders_count( 'on-hold' );
		$shipped_count    = (int) wc_orders_count( 'shipped' );
		$completed_count  = (int) wc_orders_count( 'completed' );
		$cancelled_count  = (int) wc_orders_count( 'cancelled' ) + (int) wc_orders_count( 'refunded' ) + (int) wc_orders_count( 'failed' );

		$total_all = $processing_count + $shipped_count + $completed_count + $cancelled_count;

		return array(
			'all'        => $total_all,
			'processing' => $processing_count,
			'shipped'    => $shipped_count,
			'completed'  => $completed_count,
			'cancelled'  => $cancelled_count,
		);
	}
}

/**
 * Search WooCommerce Order IDs across Number, Items, Customer Name, Phone, and Email.
 *
 * @param string $search_term The search keyword.
 * @param int    $limit       Safety limit for matched order IDs.
 * @return array Array of matching order IDs.
 */
if ( ! function_exists( 'ashrafco_search_order_ids' ) ) {
	function ashrafco_search_order_ids( $search_term, $limit = 250 ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->search_order_ids( $search_term, $limit );
		}

		global $wpdb;

		$search_term = trim( (string) $search_term );
		if ( '' === $search_term ) {
			return array();
		}

		$matched_ids = array();

		// 1. Order Number / ID
		$clean_id = ltrim( $search_term, '#' );
		if ( is_numeric( $clean_id ) && absint( $clean_id ) > 0 ) {
			$direct_id = absint( $clean_id );
			if ( function_exists( 'wc_get_order' ) && wc_get_order( $direct_id ) ) {
				$matched_ids[] = $direct_id;
			}
		}

		$like = '%' . $wpdb->esc_like( $search_term ) . '%';

		// 2. Line Item Product Name Search
		$product_order_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT order_id 
			 FROM {$wpdb->prefix}woocommerce_order_items 
			 WHERE order_item_type = 'line_item' 
			   AND order_item_name LIKE %s 
			 LIMIT %d",
			$like,
			$limit
		) );
		if ( ! empty( $product_order_ids ) ) {
			foreach ( $product_order_ids as $p_oid ) {
				$matched_ids[] = absint( $p_oid );
			}
		}

		// Catalog product title match
		$catalog_item_order_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT oi.order_id 
			 FROM {$wpdb->prefix}woocommerce_order_items oi
			 INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oi.order_item_id = oim.order_item_id
			 INNER JOIN {$wpdb->posts} p ON CAST(oim.meta_value AS UNSIGNED) = p.ID
			 WHERE oi.order_item_type = 'line_item' 
			   AND oim.meta_key IN ('_product_id', '_variation_id')
			   AND (p.post_title LIKE %s OR p.post_excerpt LIKE %s)
			 LIMIT %d",
			$like,
			$like,
			$limit
		) );
		if ( ! empty( $catalog_item_order_ids ) ) {
			foreach ( $catalog_item_order_ids as $cp_oid ) {
				$matched_ids[] = absint( $cp_oid );
			}
		}

		// 3. Customer Details & Order Meta
		$is_hpos = class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();

		if ( $is_hpos ) {
			$hpos_ids = $wpdb->get_col( $wpdb->prepare(
				"SELECT DISTINCT o.id 
				 FROM {$wpdb->prefix}wc_orders o
				 LEFT JOIN {$wpdb->prefix}wc_order_addresses a ON o.id = a.order_id
				 WHERE o.billing_email LIKE %s
				    OR a.email LIKE %s
				    OR a.phone LIKE %s
				    OR a.first_name LIKE %s
				    OR a.last_name LIKE %s
				    OR CONCAT(a.first_name, ' ', a.last_name) LIKE %s
				 LIMIT %d",
				$like, $like, $like, $like, $like, $like,
				$limit
			) );
			if ( ! empty( $hpos_ids ) ) {
				foreach ( $hpos_ids as $h_oid ) {
					$matched_ids[] = absint( $h_oid );
				}
			}
		}

		// PostMeta search
		$meta_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT post_id 
			 FROM {$wpdb->postmeta} 
			 WHERE meta_key IN (
				'_billing_first_name', 
				'_billing_last_name', 
				'_billing_phone', 
				'_billing_email', 
				'_shipping_first_name', 
				'_shipping_last_name',
				'_shipping_phone',
				'_order_number'
			 )
			 AND meta_value LIKE %s
			 LIMIT %d",
			$like,
			$limit
		) );
		if ( ! empty( $meta_ids ) ) {
			foreach ( $meta_ids as $m_oid ) {
				$matched_ids[] = absint( $m_oid );
			}
		}

		// Registered customer user account search
		$user_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->users} 
			 WHERE display_name LIKE %s 
			    OR user_login LIKE %s 
			    OR user_email LIKE %s
			 LIMIT 25",
			$like, $like, $like
		) );
		if ( ! empty( $user_ids ) ) {
			$user_placeholders = implode( ',', array_map( 'absint', $user_ids ) );
			if ( $is_hpos ) {
				$user_order_ids = $wpdb->get_col( "SELECT DISTINCT id FROM {$wpdb->prefix}wc_orders WHERE customer_id IN ($user_placeholders) LIMIT " . absint( $limit ) );
			} else {
				$user_order_ids = $wpdb->get_col( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_customer_user' AND meta_value IN ($user_placeholders) LIMIT " . absint( $limit ) );
			}
			if ( ! empty( $user_order_ids ) ) {
				foreach ( $user_order_ids as $u_oid ) {
					$matched_ids[] = absint( $u_oid );
				}
			}
		}

		return array_values( array_unique( array_filter( array_map( 'absint', $matched_ids ) ) ) );
	}
}

/**
 * Query portal orders with comprehensive filters, search, and pagination.
 *
 * @param array $args Filter parameters.
 * @return array Array containing 'orders', 'total', 'pages', 'current_page', 'per_page'
 */
if ( ! function_exists( 'ashrafco_get_portal_filtered_orders' ) ) {
	function ashrafco_get_portal_filtered_orders( $args = array() ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->get_filtered_orders( $args );
		}

		$defaults = array(
			'search'         => '',
			'status'         => 'all',
			'payment_method' => 'all',
			'date_from'      => '',
			'date_to'        => '',
			'per_page'       => 10,
			'paged'          => 1,
		);

		$params = wp_parse_args( $args, $defaults );

		$paged    = max( 1, absint( $params['paged'] ) );
		$per_page = min( 100, max( 5, absint( $params['per_page'] ) ) );

		$query_args = array(
			'limit'    => $per_page,
			'page'     => $paged,
			'paginate' => true,
			'orderby'  => 'date',
			'order'    => 'DESC',
		);

		// Status Filter
		if ( ! empty( $params['status'] ) && 'all' !== $params['status'] ) {
			$st = sanitize_key( $params['status'] );
			if ( 0 === strpos( $st, 'wc-' ) ) {
				$st = substr( $st, 3 );
			}
			$query_args['status'] = array( $st );
		}

		// Payment Method Filter
		if ( ! empty( $params['payment_method'] ) && 'all' !== $params['payment_method'] ) {
			$query_args['payment_method'] = sanitize_text_field( $params['payment_method'] );
		}

		// Date Range Filter
		if ( ! empty( $params['date_from'] ) && ! empty( $params['date_to'] ) ) {
			$from = sanitize_text_field( $params['date_from'] );
			$to   = sanitize_text_field( $params['date_to'] );
			$query_args['date_created'] = $from . '...' . $to;
		} elseif ( ! empty( $params['date_from'] ) ) {
			$from = sanitize_text_field( $params['date_from'] );
			$query_args['date_created'] = '>=' . $from;
		} elseif ( ! empty( $params['date_to'] ) ) {
			$to = sanitize_text_field( $params['date_to'] );
			$query_args['date_created'] = '<=' . $to;
		}

		// Search by Order ID, Customer Name, Phone, or Product Name
		$search_term = trim( $params['search'] );
		if ( ! empty( $search_term ) ) {
			$found_order_ids = ashrafco_search_order_ids( $search_term );

			if ( ! empty( $found_order_ids ) ) {
				$query_args['post__in'] = $found_order_ids;
				$query_args['include']  = $found_order_ids;
			} else {
				return array(
					'orders'       => array(),
					'total'        => 0,
					'pages'        => 0,
					'current_page' => $paged,
					'per_page'     => $per_page,
				);
			}
		}

		if ( ! function_exists( 'wc_get_orders' ) ) {
			return array(
				'orders'       => array(),
				'total'        => 0,
				'pages'        => 0,
				'current_page' => $paged,
				'per_page'     => $per_page,
			);
		}

		$results = wc_get_orders( $query_args );

		if ( is_object( $results ) && isset( $results->orders ) ) {
			return array(
				'orders'       => $results->orders,
				'total'        => $results->total,
				'pages'        => $results->max_num_pages,
				'current_page' => $paged,
				'per_page'     => $per_page,
			);
		}

		$orders_list = is_array( $results ) ? $results : array();
		return array(
			'orders'       => $orders_list,
			'total'        => count( $orders_list ),
			'pages'        => 1,
			'current_page' => 1,
			'per_page'     => $per_page,
		);
	}
}

/**
 * Get all payment gateways configured in the store for filtering.
 *
 * @return array
 */
if ( ! function_exists( 'ashrafco_get_portal_payment_gateways' ) ) {
	function ashrafco_get_portal_payment_gateways() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->get_payment_gateways();
		}

		$gateways = array();
		if ( function_exists( 'WC' ) && WC()->payment_gateways ) {
			$all = WC()->payment_gateways->payment_gateways();
			foreach ( $all as $id => $gateway ) {
				if ( 'yes' === $gateway->enabled ) {
					$gateways[ $id ] = $gateway->get_title();
				}
			}
		}

		if ( empty( $gateways ) ) {
			$gateways = array(
				'cod'    => 'الدفع عند الاستلام',
				'bacs'   => 'تحويل بنكي',
				'card'   => 'الدفع بالبطاقة',
				'wallet' => 'محفظة إلكترونية',
			);
		}

		return $gateways;
	}
}

/**
 * Return payment method icon SVG based on method ID.
 * (Presentation Helper)
 *
 * @param string $method_id Gateway ID.
 * @return string HTML SVG.
 */
function ashrafco_get_payment_icon_html( $method_id ) {
	$method_id = strtolower( (string) $method_id );

	if ( false !== strpos( $method_id, 'cod' ) || false !== strpos( $method_id, 'delivery' ) || false !== strpos( $method_id, 'cash' ) ) {
		return '<svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>';
	} elseif ( false !== strpos( $method_id, 'bacs' ) || false !== strpos( $method_id, 'bank' ) ) {
		return '<svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>';
	} elseif ( false !== strpos( $method_id, 'wallet' ) || false !== strpos( $method_id, 'vodafone' ) || false !== strpos( $method_id, 'fawry' ) ) {
		return '<svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 4h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
	} else {
		return '<svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>';
	}
}

/**
 * Return status badge CSS and Arabic text.
 *
 * @param string $status WooCommerce order status slug.
 * @return array
 */
if ( ! function_exists( 'ashrafco_get_portal_status_badge' ) ) {
	function ashrafco_get_portal_status_badge( $status ) {
		if ( function_exists( 'ashrafco_get_order_status_badge' ) ) {
			return ashrafco_get_order_status_badge( $status );
		}

		$clean_status = ( 0 === strpos( $status, 'wc-' ) ) ? substr( $status, 3 ) : $status;

		switch ( $clean_status ) {
			case 'processing':
			case 'pending':
			case 'on-hold':
				return array(
					'status' => 'processing',
					'label'  => 'قيد المعالجة',
					'class'  => 'bg-amber-50 text-amber-800 border-amber-200/90 font-bold',
					'dot'    => 'bg-amber-500',
				);
			case 'shipped':
			case 'out-for-delivery':
				return array(
					'status' => 'shipped',
					'label'  => 'تم الشحن',
					'class'  => 'bg-blue-50 text-blue-800 border-blue-200/90 font-bold',
					'dot'    => 'bg-blue-500',
				);
			case 'completed':
				return array(
					'status' => 'completed',
					'label'  => 'تم التسليم',
					'class'  => 'bg-emerald-50 text-emerald-800 border-emerald-200/90 font-bold',
					'dot'    => 'bg-emerald-500',
				);
			case 'cancelled':
			case 'refunded':
			case 'failed':
				return array(
					'status' => 'cancelled',
					'label'  => 'ملغي',
					'class'  => 'bg-rose-50 text-rose-800 border-rose-200/90 font-bold',
					'dot'    => 'bg-rose-500',
				);
			default:
				return array(
					'status' => $clean_status,
					'label'  => function_exists( 'wc_get_order_status_name' ) ? wc_get_order_status_name( $clean_status ) : $clean_status,
					'class'  => 'bg-slate-50 text-slate-700 border-slate-200/90 font-bold',
					'dot'    => 'bg-slate-400',
				);
		}
	}
}

/**
 * Normalize phone number for WhatsApp URL.
 *
 * @param string $phone
 * @return string|false
 */
if ( ! function_exists( 'ashrafco_normalize_whatsapp_phone' ) ) {
	function ashrafco_normalize_whatsapp_phone( $phone ) {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->normalize_whatsapp_phone( $phone );
		}

		if ( empty( $phone ) ) {
			return false;
		}

		$clean = preg_replace( '/\D+/', '', $phone );
		if ( empty( $clean ) ) {
			return false;
		}

		if ( 11 === strlen( $clean ) && '01' === substr( $clean, 0, 2 ) ) {
			$clean = '2' . $clean;
		} elseif ( 10 === strlen( $clean ) && '1' === substr( $clean, 0, 1 ) ) {
			$clean = '20' . $clean;
		}

		return $clean;
	}
}

/**
 * Handle CSV Export of Orders (Compatibility Wrapper)
 */
if ( ! function_exists( 'ashrafco_handle_orders_csv_export' ) ) {
	function ashrafco_handle_orders_csv_export() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->handle_csv_export();
		}

		if ( ! isset( $_GET['action'] ) || 'ashrafco_export_orders_csv' !== $_GET['action'] ) {
			return;
		}

		if ( ! is_user_logged_in() || ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) ) {
			wp_die( esc_html__( 'غير مسموح لك بتصدير بيانات الطلبات.', 'ashrafco' ), 403 );
		}

		check_admin_referer( 'ashrafco_export_orders_nonce' );

		$search         = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
		$status         = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : 'all';
		$payment_method = isset( $_GET['payment_method'] ) ? sanitize_text_field( wp_unslash( $_GET['payment_method'] ) ) : 'all';
		$date_from      = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '';
		$date_to        = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '';

		$query_result = ashrafco_get_portal_filtered_orders( array(
			'search'         => $search,
			'status'         => $status,
			'payment_method' => $payment_method,
			'date_from'      => $date_from,
			'date_to'        => $date_to,
			'per_page'       => 500,
			'paged'          => 1,
		) );

		$orders   = $query_result['orders'];
		$filename = 'ashrafco-orders-' . gmdate( 'Y-m-d-His' ) . '.csv';

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$output = fopen( 'php://output', 'w' );
		fputs( $output, "\xEF\xBB\xBF" );

		fputcsv( $output, array(
			'رقم الطلب',
			'تاريخ الطلب',
			'اسم العميل',
			'رقم الهاتف',
			'المحافظة / المدينة',
			'المنتجات',
			'الإجمالي',
			'طريقة الدفع',
			'حالة الطلب',
		) );

		$eg_states = ( function_exists( 'WC' ) && WC()->countries ) ? WC()->countries->get_states( 'EG' ) : array();

		foreach ( $orders as $order ) {
			$items_names = array();
			foreach ( $order->get_items() as $item ) {
				$items_names[] = $item->get_name() . ' (' . $item->get_quantity() . ')';
			}
			$items_str = implode( ' | ', $items_names );

			$state_code = $order->get_shipping_state() ?: $order->get_billing_state();
			$state_name = isset( $eg_states[ $state_code ] ) ? $eg_states[ $state_code ] : $state_code;
			$city       = $order->get_shipping_city() ?: $order->get_billing_city();
			$location   = trim( $state_name . ' - ' . $city, ' -' );

			$badge = ashrafco_get_portal_status_badge( $order->get_status() );

			fputcsv( $output, array(
				'#' . $order->get_id(),
				$order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d H:i' ) : '',
				$order->get_formatted_billing_full_name() ?: 'عميل مباشر',
				$order->get_billing_phone(),
				$location,
				$items_str,
				$order->get_total() . ' EGP',
				$order->get_payment_method_title(),
				$badge['label'],
			) );
		}

		fclose( $output );
		exit;
	}
}

/**
 * AJAX: Quick Status Update for Single Order (Compatibility Wrapper)
 */
if ( ! function_exists( 'ashrafco_ajax_portal_update_order_status' ) ) {
	function ashrafco_ajax_portal_update_order_status() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->handle_ajax_update_status();
		}

		check_ajax_referer( 'ashrafco_portal_order_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'غير مصرح لك بتحديث حالة الطلب.' ), 403 );
		}

		$order_id   = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
		$new_status = isset( $_POST['new_status'] ) ? sanitize_key( $_POST['new_status'] ) : '';

		if ( ! $order_id || empty( $new_status ) ) {
			wp_send_json_error( array( 'message' => 'بيانات الطلب غير صالحة.' ) );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => 'الطلب غير موجود.' ) );
		}

		$target_status  = ( 0 === strpos( $new_status, 'wc-' ) ) ? substr( $new_status, 3 ) : $new_status;
		$valid_statuses = array_keys( wc_get_order_statuses() );

		if ( ! in_array( 'wc-' . $target_status, $valid_statuses, true ) ) {
			wp_send_json_error( array( 'message' => 'حالة الطلب المحددة غير مدعومة.' ) );
		}

		$current_user = wp_get_current_user();
		$order->update_status( $target_status, sprintf( __( 'تم تحديث الحالة من بوابة الإدارة بواسطة %s.', 'ashrafco' ), $current_user->display_name ) );

		$badge   = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $target_status ) : ashrafco_get_portal_status_badge( $target_status );
		$summary = ashrafco_get_orders_summary();

		wp_send_json_success( array(
			'message'     => sprintf( __( 'تم تحديث حالة الطلب #%d إلى "%s" بنجاح.', 'ashrafco' ), $order_id, $badge['label'] ),
			'order_id'    => $order_id,
			'status'      => $target_status,
			'badge_label' => $badge['label'],
			'badge_class' => $badge['class'],
			'badge_dot'   => isset( $badge['dot'] ) ? $badge['dot'] : '',
			'badge_html'  => isset( $badge['html'] ) ? $badge['html'] : '',
			'summary'     => $summary,
		) );
	}
}

/**
 * AJAX: Bulk Status Update for Multiple Orders (Compatibility Wrapper)
 */
if ( ! function_exists( 'ashrafco_ajax_portal_bulk_update_orders' ) ) {
	function ashrafco_ajax_portal_bulk_update_orders() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->handle_ajax_bulk_update();
		}

		check_ajax_referer( 'ashrafco_portal_order_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'غير مصرح لك بتحديث حالة الطلبات.' ), 403 );
		}

		$order_ids  = isset( $_POST['order_ids'] ) ? array_map( 'absint', (array) $_POST['order_ids'] ) : array();
		$new_status = isset( $_POST['new_status'] ) ? sanitize_key( $_POST['new_status'] ) : '';

		if ( empty( $order_ids ) || empty( $new_status ) ) {
			wp_send_json_error( array( 'message' => 'يرجى تحديد طلبات واختيار حالة جديدة.' ) );
		}

		$target_status  = ( 0 === strpos( $new_status, 'wc-' ) ) ? substr( $new_status, 3 ) : $new_status;
		$valid_statuses = array_keys( wc_get_order_statuses() );

		if ( ! in_array( 'wc-' . $target_status, $valid_statuses, true ) ) {
			wp_send_json_error( array( 'message' => 'الحالة المحددة غير مدعومة.' ) );
		}

		$current_user  = wp_get_current_user();
		$updated_count = 0;

		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$order->update_status( $target_status, sprintf( __( 'تحديث جماعي للحالة من بوابة الإدارة بواسطة %s.', 'ashrafco' ), $current_user->display_name ) );
				$updated_count++;
			}
		}

		$badge   = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $target_status ) : ashrafco_get_portal_status_badge( $target_status );
		$summary = ashrafco_get_orders_summary();

		wp_send_json_success( array(
			'message'       => sprintf( __( 'تم تحديث %d طلبات بنجاح إلى "%s".', 'ashrafco' ), $updated_count, $badge['label'] ),
			'updated_count' => $updated_count,
			'status'        => $target_status,
			'badge_label'   => $badge['label'],
			'badge_class'   => $badge['class'],
			'badge_dot'     => isset( $badge['dot'] ) ? $badge['dot'] : '',
			'badge_html'    => isset( $badge['html'] ) ? $badge['html'] : '',
			'summary'       => $summary,
		) );
	}
}

/**
 * AJAX: Fetch Full Order Details for Modal (Compatibility Wrapper)
 */
if ( ! function_exists( 'ashrafco_ajax_portal_order_details' ) ) {
	function ashrafco_ajax_portal_order_details() {
		if ( function_exists( 'ashrafco_core' ) && ashrafco_core()->get_orders() ) {
			return ashrafco_core()->get_orders()->handle_ajax_order_details();
		}

		check_ajax_referer( 'ashrafco_portal_order_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'غير مصرح لك بعرض تفاصيل الطلب.' ), 403 );
		}

		$order_id = isset( $_REQUEST['order_id'] ) ? absint( $_REQUEST['order_id'] ) : 0;
		if ( ! $order_id ) {
			wp_send_json_error( array( 'message' => 'رقم الطلب غير صالح.' ) );
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			wp_send_json_error( array( 'message' => 'الطلب غير موجود.' ) );
		}

		$badge      = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $order->get_status() ) : ashrafco_get_portal_status_badge( $order->get_status() );
		$eg_states  = WC()->countries ? WC()->countries->get_states( 'EG' ) : array();
		$state_code = $order->get_shipping_state() ?: $order->get_billing_state();
		$state_name = isset( $eg_states[ $state_code ] ) ? $eg_states[ $state_code ] : $state_code;

		$items_data = array();
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product();
			$thumb   = $product && $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : wc_placeholder_img_src();
			$sku     = $product ? ( $product->get_sku() ?: '#' . $item->get_product_id() ) : '-';

			$items_data[] = array(
				'name'       => $item->get_name(),
				'sku'        => $sku,
				'thumb'      => $thumb,
				'quantity'   => $item->get_quantity(),
				'total'      => wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) ),
				'unit_price' => wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) ),
			);
		}

		$phone      = $order->get_billing_phone();
		$wa_phone   = ashrafco_normalize_whatsapp_phone( $phone );
		$store_name = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو';
		$wa_url     = $wa_phone ? 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode( sprintf( 'مرحباً %s، بخصوص طلبك #%d من متجر %s...', $order->get_formatted_billing_full_name(), $order_id, $store_name ) ) : '';

		$payload = array(
			'id'             => $order_id,
			'date'           => $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d H:i' ) : '',
			'status'         => $order->get_status(),
			'badge_label'    => $badge['label'],
			'badge_class'    => $badge['class'],
			'badge_dot'      => isset( $badge['dot'] ) ? $badge['dot'] : '',
			'badge_html'     => isset( $badge['html'] ) ? $badge['html'] : '',
			'customer_name'  => $order->get_formatted_billing_full_name() ?: 'عميل مباشر',
			'customer_email' => $order->get_billing_email() ?: '-',
			'customer_phone' => $phone ?: '-',
			'whatsapp_url'   => $wa_url,
			'state_name'     => $state_name ?: '-',
			'city'           => $order->get_billing_city() ?: '-',
			'address_1'      => $order->get_billing_address_1() ?: '-',
			'payment_title'  => $order->get_payment_method_title() ?: 'الدفع عند الاستلام',
			'payment_icon'   => ashrafco_get_payment_icon_html( $order->get_payment_method() ),
			'items'          => $items_data,
			'subtotal'       => wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ),
			'shipping_total' => wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ),
			'discount_total' => wc_price( $order->get_discount_total(), array( 'currency' => $order->get_currency() ) ),
			'grand_total'    => wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ),
			'customer_note'  => $order->get_customer_note() ?: '',
			'admin_edit_url' => admin_url( 'post.php?post=' . $order_id . '&action=edit' ),
		);

		wp_send_json_success( $payload );
	}
}

/**
 * Hook Registration for Orders Management
 *
 * If ashrafco-core is active, CSV export and AJAX actions are hooked
 * canonically by the plugin (Ashrafco\Core\Orders\Order_Manager).
 * The theme only registers these hooks as a legacy fallback if ashrafco-core is absent.
 */
if ( ! function_exists( 'ashrafco_is_core_active' ) || ! ashrafco_is_core_active() ) {
	add_action( 'template_redirect', 'ashrafco_handle_orders_csv_export' );
	add_action( 'wp_ajax_ashrafco_portal_update_order_status', 'ashrafco_ajax_portal_update_order_status' );
	add_action( 'wp_ajax_ashrafco_portal_bulk_update_orders', 'ashrafco_ajax_portal_bulk_update_orders' );
	add_action( 'wp_ajax_ashrafco_portal_order_details', 'ashrafco_ajax_portal_order_details' );
}