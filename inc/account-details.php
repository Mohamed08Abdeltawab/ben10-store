<?php
/**
 * Ashrafco WooCommerce Account Details Customizations & Security
 *
 * Implements:
 * - Phone number validation & persistence (billing_phone) with Egyptian & international support.
 * - Date of birth (account_birthdate) persistence.
 * - Email verification action handler.
 * - Safe password validation hooks.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean and normalize phone numbers supporting Egyptian format and international codes.
 *
 * @param string $phone Raw phone string.
 * @param string $country_code Country dial code, e.g. '+20'.
 * @return string Normalized phone number.
 */
function ashrafco_normalize_phone( $phone, $country_code = '+20' ) {
	$clean_phone = preg_replace( '/[\s\-]/', '', $phone );

	if ( '+20' === $country_code || empty( $country_code ) ) {
		// Normalize Egyptian formats to canonical 11-digit local format: 01XXXXXXXXX
		if ( 0 === strpos( $clean_phone, '+20' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 3 );
		} elseif ( 0 === strpos( $clean_phone, '0020' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 4 );
		} elseif ( 0 === strpos( $clean_phone, '20' ) && 12 === strlen( $clean_phone ) ) {
			$clean_phone = '0' . substr( $clean_phone, 2 );
		} elseif ( preg_match( '/^1[0125][0-9]{8}$/', $clean_phone ) ) {
			$clean_phone = '0' . $clean_phone;
		}
	} else {
		// International country code selected: ensure prefix matches
		if ( 0 !== strpos( $clean_phone, '+' ) ) {
			$clean_phone = ltrim( $clean_phone, '0' );
			$clean_phone = $country_code . $clean_phone;
		}
	}

	return $clean_phone;
}

/**
 * Validate customer profile fields on WooCommerce edit-account submission.
 *
 * @param WP_Error $errors WooCommerce validation errors.
 * @param WP_User  $user Current WP_User object.
 * @return WP_Error
 */
function ashrafco_validate_account_details( $errors, $user ) {
	// Only run on save_account_details post
	if ( ! isset( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] ) {
		return $errors;
	}

	$country_code = isset( $_POST['phone_country_code'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_country_code'] ) ) : '+20';
	$raw_phone    = isset( $_POST['billing_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) ) : '';
	$clean_phone  = ashrafco_normalize_phone( $raw_phone, $country_code );

	if ( empty( $clean_phone ) ) {
		$errors->add( 'billing_phone_empty', __( 'يرجى إدخال رقم الهاتف.', 'ashrafco' ) );
	} elseif ( '+20' === $country_code ) {
		if ( ! preg_match( '/^01[0125][0-9]{8}$/', $clean_phone ) ) {
			$errors->add( 'billing_phone_invalid', __( 'يرجى إدخال رقم هاتف محمول مصري صحيح (مثال: 01012345678).', 'ashrafco' ) );
		}
	} else {
		// General international regex
		if ( ! preg_match( '/^\+[0-9]{8,15}$/', $clean_phone ) ) {
			$errors->add( 'billing_phone_invalid_intl', __( 'يرجى إدخال رقم هاتف دولي صحيح يتضمن رمز الدولة.', 'ashrafco' ) );
		}
	}

	return $errors;
}
add_filter( 'woocommerce_save_account_details_errors', 'ashrafco_validate_account_details', 10, 2 );

/**
 * Save custom account details (phone number & birth date) upon successful WooCommerce account update.
 *
 * @param int $user_id Updated customer user ID.
 */
function ashrafco_save_account_details( $user_id ) {
	// Verify user permissions
	if ( ! $user_id || ( get_current_user_id() !== $user_id && ! current_user_can( 'edit_user', $user_id ) ) ) {
		return;
	}

	// 1. Phone number persistence
	if ( isset( $_POST['billing_phone'] ) ) {
		$country_code = isset( $_POST['phone_country_code'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_country_code'] ) ) : '+20';
		$raw_phone    = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
		$clean_phone  = ashrafco_normalize_phone( $raw_phone, $country_code );

		if ( ! empty( $clean_phone ) ) {
			update_user_meta( $user_id, 'billing_phone', $clean_phone );

			if ( function_exists( 'wc_get_customer' ) ) {
				$customer = wc_get_customer( $user_id );
				if ( $customer ) {
					$customer->set_billing_phone( $clean_phone );
					$customer->save();
				}
			}
		}
	}

	// 2. Birth date persistence (optional)
	if ( isset( $_POST['account_birthdate'] ) ) {
		$birthdate = sanitize_text_field( wp_unslash( $_POST['account_birthdate'] ) );
		update_user_meta( $user_id, 'account_birthdate', $birthdate );
	}
}
add_action( 'woocommerce_save_account_details', 'ashrafco_save_account_details', 10, 1 );

/**
 * Handle direct email verification action on account pages.
 */
function ashrafco_handle_email_verification() {
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}

	if ( isset( $_GET['action'] ) && 'verify_email' === $_GET['action'] && isset( $_GET['_wpnonce'] ) ) {
		if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ashrafco_verify_email' ) ) {
			$user_id = get_current_user_id();
			update_user_meta( $user_id, 'ashrafco_email_verified', '1' );

			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'تم تأكيد بريدك الإلكتروني بنجاح وربطه بحسابك!', 'ashrafco' ), 'success' );
			}

			// Clean redirect to remove query arguments
			$redirect_url = wc_get_endpoint_url( 'edit-account', '', wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}
}
add_action( 'template_redirect', 'ashrafco_handle_email_verification', 5 );

/**
 * ============================================================================
 * Dual Phone / Email Authentication & Registration Validation
 * ============================================================================
 */

/**
 * Allow customers to log in using their Egyptian mobile number in addition to username/email.
 */
function test_theme_authenticate_by_phone( $user, $username, $password ) {
	if ( ! empty( $username ) && is_string( $username ) ) {
		$clean_phone = preg_replace( '/[\s\-]/', '', $username );
		if ( 0 === strpos( $clean_phone, '+20' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 3 );
		} elseif ( 0 === strpos( $clean_phone, '0020' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 4 );
		} elseif ( 0 === strpos( $clean_phone, '20' ) && 12 === strlen( $clean_phone ) ) {
			$clean_phone = '0' . substr( $clean_phone, 2 );
		}

		if ( preg_match( '/^01[0125][0-9]{8}$/', $clean_phone ) ) {
			$user_query = new WP_User_Query( array(
				'meta_key'   => 'billing_phone',
				'meta_value' => $clean_phone,
				'number'     => 1,
			) );
			$matched = $user_query->get_results();
			if ( ! empty( $matched ) ) {
				$found_user = $matched[0];
				if ( wp_check_password( $password, $found_user->user_pass, $found_user->ID ) ) {
					return $found_user;
				}
			}
		}
	}
	return $user;
}
add_filter( 'authenticate', 'test_theme_authenticate_by_phone', 25, 3 );

/**
 * Validate Egyptian mobile phone number on customer registration.
 */
function test_theme_validate_registration_phone( $errors, $username, $email ) {
	if ( isset( $_POST['billing_phone'] ) ) {
		$phone = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
		$clean_phone = preg_replace( '/[\s\-]/', '', $phone );
		if ( 0 === strpos( $clean_phone, '+20' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 3 );
		} elseif ( 0 === strpos( $clean_phone, '0020' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 4 );
		} elseif ( 0 === strpos( $clean_phone, '20' ) && 12 === strlen( $clean_phone ) ) {
			$clean_phone = '0' . substr( $clean_phone, 2 );
		}

		if ( empty( $clean_phone ) ) {
			$errors->add( 'billing_phone_empty', 'يرجى إدخال رقم الهاتف المحمول.' );
		} elseif ( ! preg_match( '/^01[0125][0-9]{8}$/', $clean_phone ) ) {
			$errors->add( 'billing_phone_invalid', 'يرجى إدخال رقم هاتف محمول مصري صحيح (مثال: 01012345678).' );
		}
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'test_theme_validate_registration_phone', 10, 3 );

/**
 * Save Egyptian mobile phone number to customer user meta upon registration.
 */
function test_theme_save_registration_phone( $customer_id ) {
	if ( isset( $_POST['billing_phone'] ) ) {
		$phone = sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
		$clean_phone = preg_replace( '/[\s\-]/', '', $phone );
		if ( 0 === strpos( $clean_phone, '+20' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 3 );
		} elseif ( 0 === strpos( $clean_phone, '0020' ) ) {
			$clean_phone = '0' . substr( $clean_phone, 4 );
		} elseif ( 0 === strpos( $clean_phone, '20' ) && 12 === strlen( $clean_phone ) ) {
			$clean_phone = '0' . substr( $clean_phone, 2 );
		}
		update_user_meta( $customer_id, 'billing_phone', $clean_phone );
	}
}
add_action( 'woocommerce_created_customer', 'test_theme_save_registration_phone', 10, 1 );

/**
 * Validate password confirmation on customer registration.
 */
function ashrafco_validate_registration_password_confirm( $errors, $username, $email ) {
	if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
		$password = isset( $_POST['password'] ) ? $_POST['password'] : '';
		$password_confirm = isset( $_POST['password_confirm'] ) ? $_POST['password_confirm'] : '';

		if ( empty( $password_confirm ) ) {
			$errors->add( 'password_confirm_empty', __( 'يرجى تأكيد كلمة المرور.', 'ashrafco' ) );
		} elseif ( $password !== $password_confirm ) {
			$errors->add( 'password_mismatch', __( 'كلمتا المرور غير متطابقتين. يرجى التأكد من تطابق كلمة المرور وتأكيدها.', 'ashrafco' ) );
		}
	}
	return $errors;
}
add_filter( 'woocommerce_registration_errors', 'ashrafco_validate_registration_password_confirm', 10, 3 );

/**
 * Translate WooCommerce password strength meter script parameters at source.
 */
function ashrafco_filter_pws_meter_script_data( $params, $handle ) {
	if ( 'wc-password-strength-meter' === $handle && is_array( $params ) ) {
		$params['i18n_password_error'] = __( 'يرجى إدخال كلمة مرور أكثر أماناً.', 'ashrafco' );
		$params['i18n_password_hint']  = __( 'استخدم 12 حرفًا على الأقل، وامزج بين الحروف الكبيرة والصغيرة والأرقام والرموز لزيادة الأمان.', 'ashrafco' );
	}
	return $params;
}
add_filter( 'woocommerce_get_script_data', 'ashrafco_filter_pws_meter_script_data', 20, 2 );

/**
 * Translate password hint string in WooCommerce.
 */
function ashrafco_filter_password_hint() {
	return __( 'استخدم 12 حرفًا على الأقل، وامزج بين الحروف الكبيرة والصغيرة والأرقام والرموز لزيادة الأمان.', 'ashrafco' );
}
add_filter( 'woocommerce_password_hint', 'ashrafco_filter_password_hint', 20 );

/**
 * Localize WordPress core password strength meter (pwsL10n) with clear Arabic translations.
 */
function ashrafco_localize_pws_strings() {
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$arabic_pws = array(
			'unknown'  => __( 'قوة كلمة المرور غير معروفة', 'ashrafco' ),
			'short'    => __( 'ضعيفة جداً', 'ashrafco' ),
			'bad'      => __( 'ضعيفة', 'ashrafco' ),
			'good'     => __( 'متوسطة', 'ashrafco' ),
			'strong'   => __( 'قوية', 'ashrafco' ),
			'mismatch' => __( 'غير متطابقة', 'ashrafco' ),
		);
		wp_localize_script( 'password-strength-meter', 'pwsL10n', $arabic_pws );
	}
}
add_action( 'wp_enqueue_scripts', 'ashrafco_localize_pws_strings', 100 );

/**
 * Filter gettext translations for password strength terms.
 */
function ashrafco_translate_pws_gettext( $translation, $text, $domain ) {
	if ( 'woocommerce' === $domain || 'default' === $domain ) {
		switch ( $text ) {
			case 'Very weak':
				return 'ضعيفة جداً';
			case 'Weak':
				return 'ضعيفة';
			case 'Medium':
				return 'متوسطة';
			case 'Strong':
				return 'قوية';
		}
	}
	return $translation;
}
add_filter( 'gettext', 'ashrafco_translate_pws_gettext', 20, 3 );