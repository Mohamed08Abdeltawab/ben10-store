<?php
/**
 * Ashrafco Payment Integrations & Paymob Webhook Handler
 *
 * Implements robust IPN/Webhook and redirect callback listeners for Paymob,
 * HMAC-SHA512 verification, automatic order status transitions, internal
 * audit notes, and failed payment recovery workflows.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Paymob Credentials & Settings Helpers
 */

/**
 * Retrieve Paymob HMAC Secret key.
 *
 * @return string
 */
function ashrafco_paymob_get_hmac_secret() {
	if ( defined( 'ASHRAFCO_PAYMOB_HMAC_SECRET' ) && ! empty( constant( 'ASHRAFCO_PAYMOB_HMAC_SECRET' ) ) ) {
		return (string) apply_filters( 'ashrafco_paymob_hmac_secret', constant( 'ASHRAFCO_PAYMOB_HMAC_SECRET' ) );
	}

	$secret = get_option( 'ashrafco_paymob_hmac_secret', '' );
	if ( empty( $secret ) ) {
		// Check official Paymob plugin settings to avoid duplicated configuration
		$official_paymob_settings = get_option( 'woocommerce_paymob_settings', array() );
		if ( is_array( $official_paymob_settings ) && ! empty( $official_paymob_settings['hmac_hidden'] ) ) {
			$secret = $official_paymob_settings['hmac_hidden'];
		}
	}
	return (string) apply_filters(
		'ashrafco_paymob_hmac_secret',
		$secret
	);
}

/**
 * Retrieve Paymob Callback / IPN URL.
 *
 * @return string
 */
function ashrafco_paymob_get_callback_url() {
	return add_query_arg( 'wc-api', 'ashrafco_paymob_callback', home_url( '/' ) );
}

/**
 * 2. HMAC-SHA512 Verification
 *
 * Concatenates specific transaction fields in the required Paymob canonical order
 * and validates the calculated HMAC against the received HMAC hash using hash_equals.
 *
 * @param array  $txn Transaction data array.
 * @param string $secret Optional HMAC secret override.
 * @return bool
 */
function ashrafco_paymob_verify_hmac( $txn, $secret = '' ) {
	if ( empty( $secret ) ) {
		$secret = ashrafco_paymob_get_hmac_secret();
	}

	// Received HMAC from request
	$received_hmac = '';
	if ( ! empty( $txn['hmac'] ) ) {
		$received_hmac = sanitize_text_field( $txn['hmac'] );
	} elseif ( ! empty( $_GET['hmac'] ) ) {
		$received_hmac = sanitize_text_field( wp_unslash( $_GET['hmac'] ) );
	}

	// Require both a configured secret key and a received HMAC token.
	// Never allow unverified or empty-secret callbacks to pass.
	if ( empty( $secret ) || empty( $received_hmac ) ) {
		return false;
	}

	// Paymob canonical keys in exact required sequence
	$keys = array(
		'amount_cents',
		'created_at',
		'currency',
		'error_occured',
		'has_parent_transaction',
		'id',
		'integration_id',
		'is_3d_secure',
		'is_auth',
		'is_capture',
		'is_refunded',
		'is_standalone_payment',
		'is_voided',
		'order',
		'owner',
		'pending',
		'source_data_pan',
		'source_data_sub_type',
		'source_data_type',
		'success',
	);

	$concatenated = '';

	foreach ( $keys as $key ) {
		$val = '';

		// Handle nested or formatted keys
		if ( 'order' === $key ) {
			if ( isset( $txn['order']['id'] ) ) {
				$val = $txn['order']['id'];
			} elseif ( isset( $txn['order'] ) && ! is_array( $txn['order'] ) ) {
				$val = $txn['order'];
			} elseif ( isset( $txn['order_id'] ) ) {
				$val = $txn['order_id'];
			}
		} elseif ( 'source_data_pan' === $key ) {
			$val = isset( $txn['source_data']['pan'] ) ? $txn['source_data']['pan'] : ( $txn['source_data_pan'] ?? '' );
		} elseif ( 'source_data_sub_type' === $key ) {
			$val = isset( $txn['source_data']['sub_type'] ) ? $txn['source_data']['sub_type'] : ( $txn['source_data_sub_type'] ?? '' );
		} elseif ( 'source_data_type' === $key ) {
			$val = isset( $txn['source_data']['type'] ) ? $txn['source_data']['type'] : ( $txn['source_data_type'] ?? '' );
		} elseif ( isset( $txn[ $key ] ) ) {
			$val = $txn[ $key ];
		}

		// Convert booleans to strings 'true' or 'false' as required by Paymob
		if ( is_bool( $val ) ) {
			$val = $val ? 'true' : 'false';
		} elseif ( 'true' === $val || 'false' === $val ) {
			// Already string representation
		}

		$concatenated .= (string) $val;
	}

	$calculated_hmac = hash_hmac( 'sha512', $concatenated, $secret );

	return hash_equals( strtolower( $calculated_hmac ), strtolower( $received_hmac ) );
}

/**
 * 3. Locate WooCommerce Order from Paymob Payload
 *
 * Checks merchant_order_id, special_reference, query parameters,
 * and order meta _paymob_order_id.
 *
 * @param array $txn Transaction data array.
 * @return WC_Order|false
 */
function ashrafco_paymob_locate_order( $txn ) {
	$order_id = 0;

	// Check merchant_order_id inside order object or top-level
	if ( ! empty( $txn['order']['merchant_order_id'] ) ) {
		$order_id = absint( $txn['order']['merchant_order_id'] );
	} elseif ( ! empty( $txn['merchant_order_id'] ) ) {
		$order_id = absint( $txn['merchant_order_id'] );
	} elseif ( ! empty( $txn['special_reference'] ) ) {
		$order_id = absint( $txn['special_reference'] );
	} elseif ( ! empty( $_GET['merchant_order_id'] ) ) {
		$order_id = absint( wp_unslash( $_GET['merchant_order_id'] ) );
	} elseif ( ! empty( $_GET['order_id'] ) ) {
		$order_id = absint( wp_unslash( $_GET['order_id'] ) );
	}

	if ( $order_id > 0 ) {
		$order = wc_get_order( $order_id );
		if ( $order instanceof WC_Order ) {
			return $order;
		}
	}

	// Try querying order by Paymob internal order ID meta if stored
	$paymob_order_id = '';
	if ( ! empty( $txn['order']['id'] ) ) {
		$paymob_order_id = sanitize_text_field( $txn['order']['id'] );
	} elseif ( ! empty( $txn['order'] ) && is_scalar( $txn['order'] ) ) {
		$paymob_order_id = sanitize_text_field( $txn['order'] );
	}

	if ( ! empty( $paymob_order_id ) && function_exists( 'wc_get_orders' ) ) {
		$orders = wc_get_orders(
			array(
				'limit'      => 1,
				'meta_key'   => '_paymob_order_id',
				'meta_value' => $paymob_order_id,
			)
		);
		if ( ! empty( $orders ) && $orders[0] instanceof WC_Order ) {
			return $orders[0];
		}
	}

	return false;
}

/**
 * 4. Process Paymob Transaction (Status Transition & Notes)
 *
 * Handles successful or failed transaction outcomes, updates order status,
 * adds Arabic internal notes with transaction ID, payment method, and auth code.
 *
 * @param array $txn Parsed transaction payload.
 * @param bool  $is_post True if server-to-server IPN, False if browser GET redirect.
 * @return array Result array [ 'success' => bool, 'order' => WC_Order|false, 'message' => string ]
 */
function ashrafco_paymob_process_transaction( $txn, $is_post = true ) {
	$order = ashrafco_paymob_locate_order( $txn );

	if ( ! $order instanceof WC_Order ) {
		return array(
			'success' => false,
			'order'   => false,
			'message' => 'Order not found for transaction reference.',
		);
	}

	// Verify HMAC authenticity
	$is_authentic = ashrafco_paymob_verify_hmac( $txn );
	if ( ! $is_authentic ) {
		$order->add_order_note( '⚠️ تحذير أمني: تم استلام إشعار Paymob ولكن فشل التحقق من توقيع HMAC الرقمي.' );
		return array(
			'success' => false,
			'order'   => $order,
			'message' => 'HMAC verification failed.',
		);
	}

	// Extract transaction identifiers
	$txn_id    = isset( $txn['id'] ) ? sanitize_text_field( $txn['id'] ) : ( isset( $txn['transaction_id'] ) ? sanitize_text_field( $txn['transaction_id'] ) : 'N/A' );
	$is_success = ( isset( $txn['success'] ) && ( true === $txn['success'] || 'true' === $txn['success'] || 1 === (int) $txn['success'] ) );

	// Source & Method details
	$source_type = isset( $txn['source_data']['type'] ) ? sanitize_text_field( $txn['source_data']['type'] ) : ( isset( $txn['source_data_type'] ) ? sanitize_text_field( $txn['source_data_type'] ) : 'card' );
	$source_sub  = isset( $txn['source_data']['sub_type'] ) ? sanitize_text_field( $txn['source_data']['sub_type'] ) : ( isset( $txn['source_data_sub_type'] ) ? sanitize_text_field( $txn['source_data_sub_type'] ) : '' );
	$pan         = isset( $txn['source_data']['pan'] ) ? sanitize_text_field( $txn['source_data']['pan'] ) : '';

	// Auth code / response code
	$auth_code = 'APPROVED';
	if ( ! empty( $txn['data']['txn_response_code'] ) ) {
		$auth_code = sanitize_text_field( $txn['data']['txn_response_code'] );
	} elseif ( ! empty( $txn['txn_response_code'] ) ) {
		$auth_code = sanitize_text_field( $txn['txn_response_code'] );
	} elseif ( ! empty( $txn['data']['message'] ) ) {
		$auth_code = sanitize_text_field( $txn['data']['message'] );
	}

	// Friendly method label in Arabic
	$method_label = 'بطاقة بنكية (Debit / Credit Card)';
	if ( false !== stripos( $source_type, 'wallet' ) || false !== stripos( $source_sub, 'wallet' ) ) {
		$method_label = 'محفظة إلكترونية كاش (E-Wallet)';
	} elseif ( false !== stripos( $source_sub, 'meeza' ) || false !== stripos( $source_type, 'meeza' ) ) {
		$method_label = 'بطاقة ميزة الوطنية (Meeza)';
	}
	if ( ! empty( $source_sub ) && 'wallet' !== strtolower( $source_sub ) ) {
		$method_label .= ' - ' . esc_html( ucfirst( $source_sub ) );
	}
	if ( ! empty( $pan ) ) {
		$method_label .= ' (•••• ' . esc_html( substr( $pan, -4 ) ) . ')';
	}

	// Amount in EGP
	$amount_egp = isset( $txn['amount_cents'] ) ? ( (float) $txn['amount_cents'] / 100 ) : (float) $order->get_total();

	if ( $is_success ) {
		// Prevent double-processing if order already completed/processing
		if ( ! $order->has_status( array( 'processing', 'completed' ) ) ) {
			// CRITICAL SECURITY ENFORCEMENT:
			// Browser redirects ($_GET) are client-controlled and must NOT transition orders to paid.
			// Only verified server-to-server POST webhooks (IPN) with authentic HMAC can mark an order paid.
			if ( ! $is_post ) {
				$order->add_order_note(
					sprintf(
						'ℹ️ عودة العميل من رابط Paymob (Redirect). المعاملة %s بانتظار استلام تأكيد السداد الموثق عبر الخادم (IPN Webhook).',
						$txn_id
					)
				);
				return array(
					'success' => true,
					'order'   => $order,
					'message' => 'Client return noted. Awaiting server-to-server IPN confirmation for status change.',
				);
			}

			// Transition status to Processing (only via verified POST IPN)
			$order->update_status(
				'processing',
				sprintf( 'تم تأكيد السداد الإلكتروني بنجاح عبر Paymob IPN. معرف المعاملة: %s', $txn_id )
			);

			// Add comprehensive internal note
			$note = sprintf(
				"✅ استلام تأكيد الدفع عبر Paymob IPN:\n• رقم المعاملة (Txn ID): %s\n• وسيلة الدفع: %s\n• كود التفويض (Auth Code): %s\n• إجمالي المبلغ: %s ج.م",
				$txn_id,
				$method_label,
				$auth_code,
				number_format_i18n( $amount_egp, 2 )
			);
			$order->add_order_note( $note, false );

			// Save meta keys for audit and customer account lookup
			$order->update_meta_data( '_paymob_transaction_id', $txn_id );
			$order->update_meta_data( '_paymob_auth_code', $auth_code );
			$order->update_meta_data( '_paymob_payment_method', $method_label );
			$order->update_meta_data( '_paymob_paid_at', current_time( 'mysql' ) );
			$order->save();

			// Empty shopping cart
			if ( function_exists( 'WC' ) && WC()->cart ) {
				WC()->cart->empty_cart();
			}

			do_action( 'ashrafco_paymob_payment_successful', $order, $txn );

			return array(
				'success' => true,
				'order'   => $order,
				'message' => 'Payment processed and order transitioned to processing.',
			);
		}

		return array(
			'success' => true,
			'order'   => $order,
			'message' => 'Payment already processed for this order.',
		);
	} else {
		// Transaction Failed
		if ( ! $order->has_status( array( 'processing', 'completed', 'shipped' ) ) ) {
			$order->update_status(
				'cancelled',
				sprintf( 'فشلت عملية الدفع عبر Paymob. معرف المعاملة: %s | سبب الرفض: %s', $txn_id, $auth_code )
			);

			$fail_note = sprintf(
				"❌ فشل السداد عبر Paymob:\n• رقم المعاملة: %s\n• وسيلة الدفع: %s\n• كود الرد: %s\n• تم تحويل الطلب إلى (ملغي) مع الاحتفاظ بالمنتجات في سلة العميل.",
				$txn_id,
				$method_label,
				$auth_code
			);
			$order->add_order_note( $fail_note, false );

			$order->update_meta_data( '_paymob_failed_txn_id', $txn_id );
			$order->update_meta_data( '_paymob_failed_reason', $auth_code );
			$order->save();

			do_action( 'ashrafco_paymob_payment_failed', $order, $txn );

			return array(
				'success' => false,
				'order'   => $order,
				'message' => 'Payment failed. Order marked as cancelled.',
			);
		}

		return array(
			'success' => false,
			'order'   => $order,
			'message' => 'Order is already processing/shipped/completed. Failed payment notification ignored.',
		);
	}
}

/**
 * 5. Callback Listener Handler
 *
 * Catches both server-to-server POST Webhooks and client GET Redirects.
 */
function ashrafco_paymob_handle_callback() {
	$is_post = ( 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) );

	// Parse payload
	if ( $is_post ) {
		$raw_body = file_get_contents( 'php://input' );
		$payload  = json_decode( $raw_body, true );
		if ( empty( $payload ) || ! is_array( $payload ) ) {
			$payload = $_POST;
		}
	} else {
		$payload = $_GET;
	}

	// Transaction object wrapper (Paymob sends under 'obj' key for processed webhooks)
	$txn = isset( $payload['obj'] ) && is_array( $payload['obj'] ) ? $payload['obj'] : $payload;

	// Verify we have a transaction attempt
	if ( empty( $txn['id'] ) && empty( $txn['transaction_id'] ) && empty( $_GET['id'] ) ) {
		if ( $is_post ) {
			wp_send_json_error( array( 'message' => 'No valid transaction data received.' ), 400 );
		}
		return;
	}

	$result = ashrafco_paymob_process_transaction( $txn, $is_post );

	if ( $is_post ) {
		// Server-to-server webhook response
		if ( $result['success'] ) {
			wp_send_json_success(
				array(
					'message'  => $result['message'],
					'order_id' => $result['order'] ? $result['order']->get_id() : 0,
				),
				200
			);
		} else {
			wp_send_json_error(
				array(
					'message'  => $result['message'],
					'order_id' => $result['order'] ? $result['order']->get_id() : 0,
				),
				200 // Return 200 so Paymob does not endlessly retry failed payments
			);
		}
	} else {
		// Client GET redirect back to store
		$order = $result['order'];

		if ( $result['success'] && $order instanceof WC_Order ) {
			wp_safe_redirect( $order->get_checkout_order_received_url() );
			exit;
		} else {
			// Set Arabic failure notice and redirect to checkout
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice(
					'عفواً، فشلت عملية الدفع الإلكتروني. يرجى التحقق من بيانات البطاقة أو المحفظة والمحاولة مرة أخرى.',
					'error'
				);
			}
			wp_safe_redirect( wc_get_checkout_url() );
			exit;
		}
	}
}

// Hook 1: WooCommerce API standard endpoint (/?wc-api=ashrafco_paymob_callback)
add_action( 'woocommerce_api_ashrafco_paymob_callback', 'ashrafco_paymob_handle_callback' );

// Hook 2: Fallback template_redirect listener for direct parameter handling
function ashrafco_paymob_direct_query_listener() {
	if ( isset( $_GET['ashrafco_paymob_callback'] ) ) {
		ashrafco_paymob_handle_callback();
	}
}
add_action( 'template_redirect', 'ashrafco_paymob_direct_query_listener' );
