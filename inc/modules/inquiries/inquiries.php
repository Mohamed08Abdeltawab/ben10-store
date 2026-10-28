<?php
/**
 * Ashrafco Inquiries Architecture & FluentSMTP Notification Integration
 *
 * Implements:
 * - Secure Custom Post Type ('ashrafco_inquiry') for persistent and reliable inquiry records.
 * - Form validation, sanitization, nonce protection, rate limiting, and anti-spam honeypot.
 * - Deduplication guard preventing accidental double-submissions.
 * - Execution of 'ashrafco_contact_inquiry_submitted' hook.
 * - FluentSMTP / wp_mail HTML email delivery with Arabic RTL formatting.
 * - Delivery error logging and diagnostics without leaking secrets or sensitive personal data.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ============================================================================
 * 1. Register Custom Post Type for Persistent Inquiries
 * ============================================================================
 */
function ashrafco_register_inquiries_cpt() {
	$labels = array(
		'name'                  => __( 'استفسارات العملاء', 'ashrafco' ),
		'singular_name'         => __( 'استفسار عميل', 'ashrafco' ),
		'menu_name'             => __( 'استفسارات العملاء', 'ashrafco' ),
		'name_admin_bar'        => __( 'استفسار جديد', 'ashrafco' ),
		'add_new'               => __( 'إضافة استفسار', 'ashrafco' ),
		'add_new_item'          => __( 'إضافة استفسار جديد', 'ashrafco' ),
		'new_item'              => __( 'استفسار جديد', 'ashrafco' ),
		'edit_item'             => __( 'عرض / تعديل الاستفسار', 'ashrafco' ),
		'view_item'             => __( 'عرض الاستفسار', 'ashrafco' ),
		'all_items'             => __( 'جميع الاستفسارات والصيانة', 'ashrafco' ),
		'search_items'          => __( 'بحث في الاستفسارات', 'ashrafco' ),
		'not_found'             => __( 'لا توجد استفسارات حالياً.', 'ashrafco' ),
		'not_found_in_trash'    => __( 'سلة المهملات فارغة.', 'ashrafco' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 26,
		'menu_icon'          => 'dashicons-email-alt',
		'supports'           => array( 'title', 'editor' ),
	);

	register_post_type( 'ashrafco_inquiry', $args );
}
add_action( 'init', 'ashrafco_register_inquiries_cpt' );

/**
 * Add custom columns to admin inquiry list.
 */
function ashrafco_inquiry_admin_columns( $columns ) {
	$new_columns = array(
		'cb'             => $columns['cb'],
		'title'          => __( 'عنوان الاستفسار والعميل', 'ashrafco' ),
		'inquiry_phone'  => __( 'رقم الهاتف / واتساب', 'ashrafco' ),
		'inquiry_type'   => __( 'نوع الماكينة / الاستفسار', 'ashrafco' ),
		'inquiry_branch' => __( 'الفرع / المنطقة', 'ashrafco' ),
		'inquiry_email'  => __( 'حالة الإشعار البريدي', 'ashrafco' ),
		'date'           => $columns['date'],
	);
	return $new_columns;
}
add_filter( 'manage_ashrafco_inquiry_posts_columns', 'ashrafco_inquiry_admin_columns' );

/**
 * Render custom columns content in admin inquiry list.
 */
function ashrafco_inquiry_admin_columns_data( $column, $post_id ) {
	switch ( $column ) {
		case 'inquiry_phone':
			$phone = get_post_meta( $post_id, '_inquiry_phone', true );
			if ( $phone ) {
				$clean = preg_replace( '/\D/', '', $phone );
				echo '<a href="tel:' . esc_attr( $clean ) . '" dir="ltr" class="font-mono">' . esc_html( $phone ) . '</a>';
				echo ' | <a href="https://wa.me/' . esc_attr( $clean ) . '" target="_blank" style="color:#25D366;font-weight:bold;">واتساب</a>';
			} else {
				echo '—';
			}
			break;

		case 'inquiry_type':
			$type = get_post_meta( $post_id, '_inquiry_type', true );
			echo esc_html( $type ? $type : '—' );
			break;

		case 'inquiry_branch':
			$branch = get_post_meta( $post_id, '_inquiry_branch', true );
			echo esc_html( $branch ? $branch : '—' );
			break;

		case 'inquiry_email':
			$status = get_post_meta( $post_id, '_email_status', true );
			if ( 'sent' === $status ) {
				echo '<span style="color:#059669;font-weight:bold;">✓ تم الإرسال</span>';
			} elseif ( 'failed' === $status ) {
				echo '<span style="color:#dc2626;font-weight:bold;">✕ فشل الإرسال</span>';
			} else {
				echo '<span style="color:#d97706;font-weight:bold;">قيد المعالجة</span>';
			}
			break;
	}
}
add_action( 'manage_ashrafco_inquiry_posts_custom_column', 'ashrafco_inquiry_admin_columns_data', 10, 2 );

/**
 * ============================================================================
 * 2. Inquiry Processing & Validation Handler
 * ============================================================================
 */

/**
 * Process submitted inquiry form data.
 *
 * @param array $post_data Raw $_POST data.
 * @return array Array with keys: 'success' (bool), 'message' (string), 'inquiry_id' (int|null).
 */
function ashrafco_process_inquiry_submission( $post_data ) {
	// 1. Nonce validation
	if ( empty( $post_data['ashrafco_inquiry_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $post_data['ashrafco_inquiry_nonce'] ) ), 'ashrafco_inquiry_action' ) ) {
		return array(
			'success'    => false,
			'message'    => __( 'انتهت صلاحية جلسة الإرسال، يرجى تحديث الصفحة والمحاولة مرة أخرى.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// 2. Anti-spam Honeypot check
	if ( ! empty( $post_data['inquiry_hp'] ) ) {
		// Bot detected: return generic error without giving clues
		return array(
			'success'    => false,
			'message'    => __( 'حدث خطأ أثناء معالجة الطلب، يرجى المحاولة لاحقاً.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// 3. Client IP Rate Limiting (Anti-abuse: max 4 submissions per 10 minutes)
	$client_ip = function_exists( 'ashrafco_get_client_ip' )
		? ashrafco_get_client_ip()
		: ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1' );
	$rate_key  = 'ashrafco_inq_rate_' . md5( $client_ip );
	$rate_hits = (int) get_transient( $rate_key );

	if ( $rate_hits >= 4 ) {
		return array(
			'success'    => false,
			'message'    => __( 'لقد قمت بإرسال عدة طلبات مؤخراً. يرجى الانتظار بضع دقائق قبل إرسال طلب جديد، أو تواصل معنا مباشرة عبر واتساب.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// 4. Input Sanitization
	$name        = isset( $post_data['inquiry_name'] ) ? sanitize_text_field( wp_unslash( $post_data['inquiry_name'] ) ) : '';
	$raw_phone   = isset( $post_data['inquiry_phone'] ) ? sanitize_text_field( wp_unslash( $post_data['inquiry_phone'] ) ) : '';
	$email       = isset( $post_data['inquiry_email'] ) ? sanitize_email( wp_unslash( $post_data['inquiry_email'] ) ) : '';
	$inquiry_cat = isset( $post_data['inquiry_type'] ) ? sanitize_text_field( wp_unslash( $post_data['inquiry_type'] ) ) : '';
	$branch_gov  = isset( $post_data['inquiry_branch'] ) ? sanitize_text_field( wp_unslash( $post_data['inquiry_branch'] ) ) : '';
	$message     = isset( $post_data['inquiry_message'] ) ? sanitize_textarea_field( wp_unslash( $post_data['inquiry_message'] ) ) : '';

	// Safe string length checking supporting environments without mbstring
	$name_len    = function_exists( 'mb_strlen' ) ? mb_strlen( $name, 'UTF-8' ) : strlen( $name );
	$message_len = function_exists( 'mb_strlen' ) ? mb_strlen( $message, 'UTF-8' ) : strlen( $message );

	// 5. Input Validation
	if ( $name_len < 2 ) {
		return array(
			'success'    => false,
			'message'    => __( 'يرجى إدخال الاسم بالكامل بشكل صحيح.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// Clean phone validation (digits only check)
	$clean_phone = preg_replace( '/[^\d\+]/', '', $raw_phone );
	if ( strlen( $clean_phone ) < 8 || strlen( $clean_phone ) > 18 ) {
		return array(
			'success'    => false,
			'message'    => __( 'يرجى إدخال رقم هاتف محمول / واتساب صحيح للتواصل (مثال: 010XXXXXXXX).', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// Optional email validation on raw input before sanitizing
	$raw_email = isset( $post_data['inquiry_email'] ) ? trim( (string) wp_unslash( $post_data['inquiry_email'] ) ) : '';
	if ( '' !== $raw_email && ! is_email( $raw_email ) ) {
		return array(
			'success'    => false,
			'message'    => __( 'صيغة البريد الإلكتروني غير صحيحة، يرجى تصحيحه أو ترك الحقل فارغاً.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}
	$email = sanitize_email( $raw_email );

	if ( $message_len < 5 ) {
		return array(
			'success'    => false,
			'message'    => __( 'يرجى كتابة تفاصيل الاستفسار أو الموديل المطلوب بوضوح لمساعدتك.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// 6. Deduplication Check (Avoid exact duplicate submission within 3 minutes)
	$dup_hash = md5( $clean_phone . '|' . $inquiry_cat . '|' . $message );
	$dup_key  = 'ashrafco_inq_dup_' . $dup_hash;
	if ( get_transient( $dup_key ) ) {
		return array(
			'success'    => true, // Return success so user is reassured
			'message'    => __( 'تم استلام هذا الاستفسار بالفعل من قبل. جاري مراجعته من قبل فريق الدعم الفني وسنتواصل معك قريباً.', 'ashrafco' ),
			'inquiry_id' => (int) get_transient( $dup_key ),
		);
	}

	// 7. Persistent Record Creation
	$post_title = sprintf(
		'%s - %s (%s)',
		$name,
		$inquiry_cat ? $inquiry_cat : __( 'استفسار عام', 'ashrafco' ),
		current_time( 'Y-m-d H:i' )
	);

	$inquiry_id = wp_insert_post( array(
		'post_type'    => 'ashrafco_inquiry',
		'post_title'   => $post_title,
		'post_content' => $message,
		'post_status'  => 'publish',
	), true );

	if ( is_wp_error( $inquiry_id ) || ! $inquiry_id ) {
		error_log( 'Ashrafco Inquiry Error: Failed to insert inquiry post. ' . ( is_wp_error( $inquiry_id ) ? $inquiry_id->get_error_message() : '' ) );
		return array(
			'success'    => false,
			'message'    => __( 'عذراً، حدث خطأ أثناء حفظ استفسارك. يرجى المحاولة مرة أخرى أو التواصل هاتفياً.', 'ashrafco' ),
			'inquiry_id' => null,
		);
	}

	// Store meta fields
	update_post_meta( $inquiry_id, '_inquiry_name', $name );
	update_post_meta( $inquiry_id, '_inquiry_phone', $clean_phone );
	update_post_meta( $inquiry_id, '_inquiry_email', $email );
	update_post_meta( $inquiry_id, '_inquiry_type', $inquiry_cat );
	update_post_meta( $inquiry_id, '_inquiry_branch', $branch_gov );
	update_post_meta( $inquiry_id, '_inquiry_ip', $client_ip );
	update_post_meta( $inquiry_id, '_email_status', 'pending' );

	// Set deduplication transient (180 seconds)
	set_transient( $dup_key, $inquiry_id, 180 );

	// Increment rate limiting hits (expires in 600 seconds)
	set_transient( $rate_key, $rate_hits + 1, 600 );

	// 8. Execute Project Action Hook
	$inquiry_data = array(
		'inquiry_id' => $inquiry_id,
		'name'       => $name,
		'phone'      => $clean_phone,
		'email'      => $email,
		'type'       => $inquiry_cat,
		'branch'     => $branch_gov,
		'message'    => $message,
		'client_ip'  => $client_ip,
	);

	do_action( 'ashrafco_contact_inquiry_submitted', $inquiry_data, $inquiry_id );

	return array(
		'success'    => true,
		'message'    => __( 'تم إرسال استفسارك الفني بنجاح! سنتواصل معك هاتفياً أو عبر واتساب خلال أقل من ساعة لمساعدتك.', 'ashrafco' ),
		'inquiry_id' => $inquiry_id,
	);
}

/**
 * ============================================================================
 * 4. FluentSMTP & wp_mail Email Notification Handler
 * ============================================================================
 */

/**
 * Handle inquiry notification email delivery via WordPress/FluentSMTP.
 *
 * @param array $inquiry_data Sanitized inquiry information.
 * @param int   $inquiry_id   Persistent post ID of the inquiry.
 */
function ashrafco_handle_inquiry_email_notification( $inquiry_data, $inquiry_id = 0 ) {
	if ( ! is_array( $inquiry_data ) ) {
		return;
	}

	$inquiry_id = absint( $inquiry_id ? $inquiry_id : ( $inquiry_data['inquiry_id'] ?? 0 ) );

	// Determine administrative / support recipient from project settings
	$admin_recipient = apply_filters( 'ashrafco_inquiry_recipient_email', get_option( 'admin_email' ) );
	if ( empty( $admin_recipient ) && function_exists( 'get_option' ) ) {
		$admin_recipient = get_option( 'woocommerce_email_from_address' );
	}

	if ( empty( $admin_recipient ) || ! is_email( $admin_recipient ) ) {
		error_log( sprintf( 'Ashrafco Inquiry #%d: No valid administrator recipient email configured.', $inquiry_id ) );
		if ( $inquiry_id ) {
			update_post_meta( $inquiry_id, '_email_status', 'failed' );
			update_post_meta( $inquiry_id, '_email_error', 'No valid recipient configured' );
		}
		return;
	}

	$name       = $inquiry_data['name'] ?? '';
	$phone      = $inquiry_data['phone'] ?? '';
	$email      = $inquiry_data['email'] ?? '';
	$type       = $inquiry_data['type'] ?? __( 'استفسار عام', 'ashrafco' );
	$branch     = $inquiry_data['branch'] ?? __( 'غير محدد', 'ashrafco' );
	$message    = $inquiry_data['message'] ?? '';
	$clean_phone = preg_replace( '/\D/', '', $phone );

	$store_name  = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو';

	// Email Subject
	$subject = sprintf( '[%s - استفسار جديد #%d] %s: %s', $store_name, $inquiry_id, $type, $name );

	// Headers
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
	);

	if ( ! empty( $email ) && is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . esc_attr( $name ) . ' <' . sanitize_email( $email ) . '>';
	}

	// Styled Arabic RTL HTML Email Template
	ob_start();
	?>
	<!DOCTYPE html>
	<html dir="rtl" lang="ar">
	<head>
		<meta charset="UTF-8">
		<title><?php echo esc_html( $subject ); ?></title>
	</head>
	<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; direction: rtl; text-align: right; color: #1e293b;">
		<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
			
			<!-- Header Banner -->
			<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 24px; text-align: center; color: #ffffff;">
				<h1 style="margin: 0; font-size: 20px; font-weight: 800; color: #f97316;"><?php echo esc_html( $store_name ); ?> للمعدات وماكينات السيراميك</h1>
				<p style="margin: 6px 0 0 0; font-size: 13px; color: #cbd5e1;">إشعار استفسار فني / طلب صيانة جديد</p>
			</div>

			<!-- Content Body -->
			<div style="padding: 24px;">
				<div style="background-color: #fff7ed; border: 1px solid #ffedd5; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px;">
					<span style="font-weight: bold; color: #c2410c; font-size: 14px;">رقم مرجع الاستفسار: #<?php echo esc_html( $inquiry_id ); ?></span>
					<span style="color: #9a3412; font-size: 12px; display: block; margin-top: 4px;"><?php echo esc_html( current_time( 'Y-m-d H:i' ) ); ?></span>
				</div>

				<table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 14px;">
					<tbody>
						<tr style="border-bottom: 1px solid #f1f5f9;">
							<td style="padding: 10px 0; font-weight: bold; color: #64748b; width: 35%;">اسم العميل:</td>
							<td style="padding: 10px 0; color: #0f172a; font-weight: 600;"><?php echo esc_html( $name ); ?></td>
						</tr>
						<tr style="border-bottom: 1px solid #f1f5f9;">
							<td style="padding: 10px 0; font-weight: bold; color: #64748b;">رقم الهاتف:</td>
							<td style="padding: 10px 0; color: #0f172a;" dir="ltr">
								<a href="tel:<?php echo esc_attr( $clean_phone ); ?>" style="color: #ea580c; text-decoration: none; font-weight: bold;"><?php echo esc_html( $phone ); ?></a>
								&nbsp;|&nbsp;
								<a href="https://wa.me/<?php echo esc_attr( $clean_phone ); ?>" style="color: #16a34a; text-decoration: none; font-weight: bold;">محادثة واتساب</a>
							</td>
						</tr>
						<?php if ( ! empty( $email ) ) : ?>
							<tr style="border-bottom: 1px solid #f1f5f9;">
								<td style="padding: 10px 0; font-weight: bold; color: #64748b;">البريد الإلكتروني:</td>
								<td style="padding: 10px 0; color: #0f172a;">
									<a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: #2563eb; text-decoration: none;"><?php echo esc_html( $email ); ?></a>
								</td>
							</tr>
						<?php endif; ?>
						<tr style="border-bottom: 1px solid #f1f5f9;">
							<td style="padding: 10px 0; font-weight: bold; color: #64748b;">نوع الاستفسار / الماكينة:</td>
							<td style="padding: 10px 0; color: #0f172a; font-weight: 600;"><?php echo esc_html( $type ); ?></td>
						</tr>
						<tr style="border-bottom: 1px solid #f1f5f9;">
							<td style="padding: 10px 0; font-weight: bold; color: #64748b;">الفرع الأقرب / المنطقة:</td>
							<td style="padding: 10px 0; color: #0f172a;"><?php echo esc_html( $branch ); ?></td>
						</tr>
					</tbody>
				</table>

				<!-- Message Box -->
				<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
					<h3 style="margin: 0 0 8px 0; font-size: 13px; font-weight: bold; color: #475569;">تفاصيل الرسالة أو المشكلة الفنية:</h3>
					<div style="font-size: 14px; line-height: 1.6; color: #1e293b; white-space: pre-wrap;"><?php echo nl2br( esc_html( $message ) ); ?></div>
				</div>

				<div style="text-align: center; margin-top: 24px;">
					<a href="https://wa.me/<?php echo esc_attr( $clean_phone ); ?>" style="display: inline-block; background-color: #16a34a; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: bold; font-size: 14px;">
						الرد الفوري عبر واتساب
					</a>
				</div>
			</div>

			<!-- Footer -->
			<div style="background-color: #f1f5f9; padding: 14px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0;">
				تم إنشاء هذا الإشعار تلقائياً بواسطة نظام استفسارات شركة <?php echo esc_html( $store_name ); ?>.
			</div>
		</div>
	</body>
	</html>
	<?php
	$html_body = ob_get_clean();

	// Deliver email using standard WordPress mail pipeline (intercepted by FluentSMTP)
	$sent = wp_mail( $admin_recipient, $subject, $html_body, $headers );

	if ( $inquiry_id ) {
		if ( $sent ) {
			update_post_meta( $inquiry_id, '_email_status', 'sent' );
			update_post_meta( $inquiry_id, '_email_sent_at', current_time( 'mysql' ) );
		} else {
			update_post_meta( $inquiry_id, '_email_status', 'failed' );
			update_post_meta( $inquiry_id, '_email_error', 'wp_mail returned false' );
			error_log( sprintf( 'Ashrafco Inquiry #%d: wp_mail failed to deliver to %s', $inquiry_id, $admin_recipient ) );
		}
	}
}
add_action( 'ashrafco_contact_inquiry_submitted', 'ashrafco_handle_inquiry_email_notification', 10, 2 );
