/**
 * Client-Side Egyptian Mobile Phone Validation & Anti-Fraud Checkout Hardening
 *
 * Implements:
 * 1. Egyptian mobile phone validation: ^(010|011|012|015)[0-9]{8}$
 * 2. Double-submit and rapid-click prevention on `#place_order`
 * 3. Dynamic loading state with spinner and Arabic feedback text
 * 4. Error recovery via WooCommerce `checkout_error` event
 *
 * @package Ashrafco
 * @since 1.0.0
 */

jQuery(function ($) {
	'use strict';

	var phoneInput   = document.getElementById('billing_phone');
	var $checkoutForm = $('form.checkout');

	// Flag to prevent double submission while request is in-flight
	var isSubmitting = false;

	// Egyptian mobile regex: 11 digits starting with 010, 011, 012, or 015
	var EG_PHONE_REGEX = /^(010|011|012|015)[0-9]{8}$/;

	// Helper to convert Arabic-Indic digits to Latin digits
	function normalizeDigits(str) {
		if (!str) return '';
		var arabicIndic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
		return str.replace(/[٠-٩]/g, function (d) {
			return arabicIndic.indexOf(d);
		}).replace(/\s+/g, '').replace(/[-+]/g, '');
	}

	// Error message container ID
	var ERROR_ID = 'ashrafco-phone-validation-error';

	function getOrCreateErrorEl() {
		if (!phoneInput) return null;
		var errorEl = document.getElementById(ERROR_ID);
		if (!errorEl) {
			errorEl = document.createElement('p');
			errorEl.id = ERROR_ID;
			errorEl.className = 'text-xs text-rose-600 font-bold mt-1.5 flex items-center gap-1.5 transition-all';
			errorEl.setAttribute('role', 'alert');
			errorEl.innerHTML = '<svg class="w-4 h-4 flex-shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg><span>يرجى إدخال رقم محمول مصري صحيح مكون من 11 رقماً يبدأ بـ (010, 011, 012, 015)</span>';

			var parentField = phoneInput.closest('.form-row') || phoneInput.parentElement;
			if (parentField) {
				parentField.appendChild(errorEl);
			} else {
				phoneInput.insertAdjacentElement('afterend', errorEl);
			}
		}
		return errorEl;
	}

	function removeErrorEl() {
		var errorEl = document.getElementById(ERROR_ID);
		if (errorEl && errorEl.parentNode) {
			errorEl.parentNode.removeChild(errorEl);
		}
	}

	/**
	 * Validate phone value
	 * @param {boolean} isInitialCheck - whether to skip error display if empty
	 * @returns {boolean} - true if valid, false otherwise
	 */
	function validatePhone(isInitialCheck) {
		if (!phoneInput) return true;

		var rawVal = phoneInput.value.trim();
		var normalized = normalizeDigits(rawVal);

		// If user entered converted digits, update input value seamlessly
		if (normalized !== rawVal && normalized.length > 0) {
			phoneInput.value = normalized;
		}

		// If empty and initial check, don't show error yet
		if (!normalized && isInitialCheck) {
			phoneInput.classList.remove('border-red-500', 'border-rose-500', 'border-emerald-500');
			removeErrorEl();
			return false;
		}

		var isValid = EG_PHONE_REGEX.test(normalized);

		if (isValid) {
			// Valid State
			phoneInput.classList.remove('border-red-500', 'border-rose-500');
			phoneInput.classList.add('border-emerald-500');
			phoneInput.style.borderColor = '#10b981';
			removeErrorEl();
			return true;
		} else {
			// Invalid State
			phoneInput.classList.remove('border-emerald-500');
			phoneInput.classList.add('border-red-500', 'border-rose-500');
			phoneInput.style.borderColor = '#ef4444';
			getOrCreateErrorEl();
			return false;
		}
	}

	// 1. Real-time phone validation listeners
	if (phoneInput) {
		phoneInput.addEventListener('input', function () {
			var val = normalizeDigits(phoneInput.value.trim());
			if (val.length >= 11) {
				validatePhone(false);
			} else if (val.length === 0) {
				validatePhone(true);
			} else {
				if (val.length >= 3 && !['010', '011', '012', '015'].includes(val.substring(0, 3))) {
					validatePhone(false);
				} else {
					phoneInput.classList.remove('border-emerald-500');
					removeErrorEl();
				}
			}
		});

		phoneInput.addEventListener('blur', function () {
			if (phoneInput.value.trim().length > 0) {
				validatePhone(false);
			}
		});
	}

	/**
	 * ========================================================================
	 * Double-Submit & Rapid Click Prevention
	 * ========================================================================
	 */

	function getPlaceOrderButton() {
		return document.getElementById('place_order') ||
			($checkoutForm.length ? $checkoutForm[0].querySelector('[name="woocommerce_checkout_place_order"]') : null);
	}

	function activateButtonLoading(btn) {
		if (!btn || btn.getAttribute('data-is-loading') === 'true') {
			return;
		}

		// Store original button HTML
		if (!btn.getAttribute('data-original-html')) {
			btn.setAttribute('data-original-html', btn.innerHTML);
		}

		btn.setAttribute('data-is-loading', 'true');
		btn.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');

		// Set visual loading spinner and text in Arabic
		btn.innerHTML = '<span class="inline-flex items-center justify-center gap-2">' +
			'<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
			'<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
			'<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
			'</svg>' +
			'<span>جاري تأكيد الطلب، يرجى الانتظار...</span>' +
			'</span>';
	}

	function restoreButtonState(btn) {
		if (!btn) {
			btn = getPlaceOrderButton();
		}
		if (!btn) return;

		var originalHtml = btn.getAttribute('data-original-html');
		if (originalHtml) {
			btn.innerHTML = originalHtml;
		}

		btn.removeAttribute('data-is-loading');
		btn.classList.remove('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
		isSubmitting = false;
	}

	// 2. Canonical WooCommerce Checkout Place Order Hook
	// Hook into WooCommerce's place_order trigger directly so the native flow proceeds smoothly.
	$checkoutForm.on('checkout_place_order', function () {
		// Prevent double clicks if request is already in-flight
		if (isSubmitting) {
			return false;
		}

		var isPhoneValid = validatePhone(false);
		if (!isPhoneValid) {
			if (phoneInput) {
				phoneInput.focus();
				phoneInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}
			restoreButtonState();
			return false;
		}

		var btn = getPlaceOrderButton();
		if (btn) {
			isSubmitting = true;
			activateButtonLoading(btn);
		}

		return true;
	});

	// Re-enable button on validation failure or submission error from server
	$(document.body).on('checkout_error', function () {
		restoreButtonState();
	});

	// Re-enable button if AJAX fails completely (network timeout, 500 error)
	$(document.ajaxError(function (event, jqxhr, settings) {
		if (settings && settings.url && settings.url.indexOf('wc-ajax=checkout') !== -1) {
			restoreButtonState();
		}
	}));

	// Order Notes Character Counter
	var $notes = $('[data-ashrafco-notes], #order_comments');
	var $counter = $('[data-ashrafco-notes-count]');
	if ($notes.length && $counter.length) {
		$notes.on('input', function () {
			var len = $(this).val().length;
			$counter.text(len + ' / 500');
		});
		$counter.text(($notes.val() ? $notes.val().length : 0) + ' / 500');
	}

	/**
	 * ========================================================================
	 * 2-Way Synchronization Between Shipping Governorate List & Billing State
	 * ========================================================================
	 */
	var isGovSyncing = false;

	function syncShippingToBillingState($radio) {
		if (isGovSyncing) return;
		if (!$radio || !$radio.length) {
			$radio = $('input.shipping_method:checked');
		}
		if (!$radio.length) return;

		var stateCode = $radio.attr('data-state-code') || '';
		var rateVal = $radio.val() || '';

		if (!stateCode && rateVal.indexOf('ashrafco_gov_') === 0) {
			stateCode = rateVal.replace('ashrafco_gov_', '').toUpperCase();
		}

		if (!stateCode) return;

		var $stateSelect = $('#billing_state');
		if ($stateSelect.length) {
			var currentVal = ($stateSelect.val() || '').toUpperCase();
			if (currentVal !== stateCode.toUpperCase()) {
				isGovSyncing = true;
				$stateSelect.val(stateCode);

				if ($stateSelect.hasClass('select2-hidden-accessible')) {
					$stateSelect.trigger('change.select2');
				}

				$stateSelect.trigger('change');

				setTimeout(function () {
					isGovSyncing = false;
				}, 250);
			}
		}
	}

	function syncBillingStateToShipping(stateCode) {
		if (isGovSyncing || !stateCode) return;

		stateCode = $.trim(String(stateCode)).toUpperCase();
		var rateId = 'ashrafco_gov_' + stateCode.toLowerCase();

		var $targetRadio = $('input.shipping_method').filter(function () {
			var code = $(this).attr('data-state-code') || '';
			var val = $(this).val() || '';
			return code.toUpperCase() === stateCode || val.toLowerCase() === rateId;
		});

		if ($targetRadio.length && !$targetRadio.is(':checked')) {
			isGovSyncing = true;
			$targetRadio.prop('checked', true).trigger('change');
			setTimeout(function () {
				isGovSyncing = false;
			}, 250);
		}
	}

	// 1. Listen for clicks and changes on shipping methods (radio, label, or li card)
	$(document).on('change', 'input.shipping_method', function () {
		syncShippingToBillingState($(this));
	});

	$(document).on('click', '.ashrafco-shipping-methods li, .ashrafco-shipping-methods label', function () {
		var $radio = $(this).find('input.shipping_method');
		if (!$radio.length && $(this).is('label')) {
			var forId = $(this).attr('for');
			if (forId) $radio = $('#' + forId);
		}
		if ($radio.length) {
			setTimeout(function () {
				syncShippingToBillingState($radio);
			}, 10);
		}
	});

	// 2. Listen for changes in the Governorate select in customer details form
	$(document).on('change', '#billing_state', function () {
		syncBillingStateToShipping($(this).val());
	});

	// 3. Initial sync on page ready
	setTimeout(function () {
		var $initialChecked = $('input.shipping_method:checked');
		var $state = $('#billing_state');
		if ($initialChecked.length && (!$state.val() || $state.val() === '')) {
			syncShippingToBillingState($initialChecked);
		} else if ($state.length && $state.val()) {
			syncBillingStateToShipping($state.val());
		}
	}, 100);

	// 4. Sync after WooCommerce AJAX checkout update
	$(document.body).on('updated_checkout', function () {
		var $checked = $('input.shipping_method:checked');
		var $state = $('#billing_state');
		if ($checked.length && (!$state.val() || $state.val() === '')) {
			syncShippingToBillingState($checked);
		} else if ($state.length && $state.val()) {
			syncBillingStateToShipping($state.val());
		}
	});

	/**
	 * ========================================================================
	 * Interactive Checkout Coupon Controller (Apply & Remove)
	 * ========================================================================
	 */
	var couponConfig = window.ashrafcoCheckoutData || {};
	var couponAjaxUrl = couponConfig.ajaxUrl || (window.wc_checkout_params ? wc_checkout_params.ajax_url : '/wp-admin/admin-ajax.php');
	var couponNonce = couponConfig.couponNonce || '';

	function decodeHtmlEntities(str) {
		if (!str) return '';
		var txt = document.createElement('textarea');
		txt.innerHTML = str;
		return txt.value;
	}

	function showCouponError(message) {
		message = decodeHtmlEntities(message);
		var $err = $('[data-ashrafco-coupon-error]');
		if ($err.length) {
			$err.text(message).removeAttr('hidden').show();
		}
	}

	function hideCouponError() {
		var $err = $('[data-ashrafco-coupon-error]');
		if ($err.length) {
			$err.text('').attr('hidden', true).hide();
		}
	}

	function setCouponLoading(isLoading) {
		var $btn = $('[data-ashrafco-apply-coupon]');
		if (!$btn.length) return;

		if (isLoading) {
			$btn.prop('disabled', true).attr('data-prev-text', $btn.text()).text('جاري التطبيق...');
		} else {
			var prevText = $btn.attr('data-prev-text') || 'تطبيق';
			$btn.prop('disabled', false).text(prevText);
		}
	}

	function applyCouponCode(code) {
		code = $.trim(code || '');
		if (!code) {
			showCouponError('يرجى إدخال كود الكوبون أولاً.');
			var $input = $('#ashrafco_coupon_code');
			if ($input.length) $input.trigger('focus');
			return;
		}

		hideCouponError();
		setCouponLoading(true);

		$.ajax({
			url: couponAjaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ashrafco_checkout_apply_coupon',
				coupon_code: code,
				security: couponNonce
			},
			success: function (res) {
				setCouponLoading(false);
				if (res && res.success) {
					hideCouponError();
					$('#ashrafco_coupon_code').val('');
					// Trigger WooCommerce checkout recalculation and review refresh
					$(document.body).trigger('update_checkout', { update_shipping_method: false });
				} else {
					var msg = (res && res.data && res.data.message) ? res.data.message : 'كود الخصم غير صحيح أو منتهي الصلاحية.';
					showCouponError(msg);
				}
			},
			error: function () {
				setCouponLoading(false);
				// Fallback to WooCommerce core apply_coupon endpoint if available
				if (window.wc_checkout_params && wc_checkout_params.wc_ajax_url && wc_checkout_params.apply_coupon_nonce) {
					var fallbackUrl = wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'apply_coupon');
					$.ajax({
						url: fallbackUrl,
						type: 'POST',
						dataType: 'html',
						data: {
							coupon_code: code,
							security: wc_checkout_params.apply_coupon_nonce
						},
						success: function () {
							$('#ashrafco_coupon_code').val('');
							$(document.body).trigger('update_checkout', { update_shipping_method: false });
						},
						error: function () {
							showCouponError('تعذر تطبيق الكوبون، يرجى المحاولة مرة أخرى.');
						}
					});
				} else {
					showCouponError('حدث خطأ أثناء الاتصال بالخادم، يرجى المحاولة لاحقاً.');
				}
			}
		});
	}

	function removeCouponCode(code, $btn) {
		code = $.trim(code || '');
		if (!code) return;

		if ($btn && $btn.length) {
			$btn.prop('disabled', true).text('جاري الحذف...');
		}

		$.ajax({
			url: couponAjaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'ashrafco_checkout_remove_coupon',
				coupon_code: code,
				security: couponNonce
			},
			success: function (res) {
				if (res && res.success) {
					$(document.body).trigger('update_checkout', { update_shipping_method: false });
				} else {
					if ($btn && $btn.length) $btn.prop('disabled', false).text('حذف');
				}
			},
			error: function () {
				// Fallback to WooCommerce core remove_coupon
				if (window.wc_checkout_params && wc_checkout_params.wc_ajax_url && wc_checkout_params.remove_coupon_nonce) {
					var fallbackUrl = wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_coupon');
					$.ajax({
						url: fallbackUrl,
						type: 'POST',
						dataType: 'html',
						data: {
							coupon: code,
							security: wc_checkout_params.remove_coupon_nonce
						},
						success: function () {
							$(document.body).trigger('update_checkout', { update_shipping_method: false });
						},
						error: function () {
							if ($btn && $btn.length) $btn.prop('disabled', false).text('حذف');
						}
					});
				} else {
					if ($btn && $btn.length) $btn.prop('disabled', false).text('حذف');
				}
			}
		});
	}

	// 1. Click apply coupon in order review sidebar
	$(document).on('click', '[data-ashrafco-apply-coupon]', function (e) {
		e.preventDefault();
		var code = $('#ashrafco_coupon_code').val();
		applyCouponCode(code);
	});

	// 2. Prevent Enter inside coupon input from submitting checkout, trigger apply instead
	$(document).on('keydown', '#ashrafco_coupon_code', function (e) {
		if (e.key === 'Enter' || e.keyCode === 13) {
			e.preventDefault();
			e.stopPropagation();
			var code = $(this).val();
			applyCouponCode(code);
		}
	});

	// 3. Remove coupon button
	$(document).on('click', '[data-ashrafco-remove-coupon]', function (e) {
		e.preventDefault();
		var $btn = $(this);
		var code = $btn.attr('data-coupon') || '';
		removeCouponCode(code, $btn);
	});

	// 4. Default coupon toggle and form support (if rendered on page)
	$(document).on('click', '.woocommerce-form-coupon-toggle .showcoupon', function (e) {
		e.preventDefault();
		$('.checkout_coupon').slideToggle(200);
	});

	$(document).on('submit', 'form.checkout_coupon', function (e) {
		var $form = $(this);
		var code = $form.find('input[name="coupon_code"]').val();
		if (code) {
			e.preventDefault();
			applyCouponCode(code);
		}
	});

	/**
	 * ========================================================================
	 * Payment Methods Radio & Visual State Synchronization
	 * ========================================================================
	 */
	function syncPaymentMethodSelection() {
		var $cards = $('.ashrafco-pm-card');
		var $checkedRadio = $('input[name="payment_method"]:checked');

		// If no radio is checked yet, default to the first available one
		if (!$checkedRadio.length) {
			var $firstRadio = $('input[name="payment_method"]').first();
			if ($firstRadio.length) {
				$firstRadio.prop('checked', true);
				$checkedRadio = $firstRadio;
			}
		}

		$cards.each(function () {
			var $card = $(this);
			var $radio = $card.find('input[name="payment_method"]');
			var isChecked = $radio.is(':checked');

			// Strict toggle of .is-selected class
			$card.toggleClass('is-selected', isChecked);

			// Synchronize visibility of details box
			var $box = $card.find('.payment_box');
			if ($box.length) {
				if (isChecked) {
					$box.removeClass('ashrafco-hidden-box').stop(true, true).slideDown(200);
				} else {
					$box.addClass('ashrafco-hidden-box').stop(true, true).slideUp(200);
				}
			}
		});
	}

	// 1. Radio change listener
	$(document).on('change', 'input[name="payment_method"]', function () {
		syncPaymentMethodSelection();
	});

	// 2. Card click listener (allow clicking anywhere on the card to select)
	$(document).on('click', '.ashrafco-pm-card', function (e) {
		// Ignore clicks on buttons, links, copy triggers, inputs, or inside the payment instructions box
		if ($(e.target).closest('a, button, input, textarea, select, .ashrafco-copy-btn, .ashrafco-pm-whatsapp-btn, .payment_box').length) {
			return;
		}

		var $radio = $(this).find('input[name="payment_method"]');
		if ($radio.length && !$radio.is(':checked')) {
			$radio.prop('checked', true).trigger('change');
		}
	});

	// 3. Re-sync after WooCommerce AJAX updates
	$(document.body).on('updated_checkout payment_method_selected', function () {
		syncPaymentMethodSelection();
	});

	// Initial payment sync
	syncPaymentMethodSelection();

	/**
	 * ========================================================================
	 * Copy-to-Clipboard for Payment Details (Vodafone Cash & InstaPay)
	 * ========================================================================
	 */
	$(document).on('click', '[data-ashrafco-copy]', function (e) {
		e.preventDefault();
		e.stopPropagation();

		var $btn = $(this);
		var textToCopy = $btn.attr('data-ashrafco-copy') || $btn.text() || '';
		textToCopy = $.trim(textToCopy);

		if (!textToCopy) return;

		var $hint = $btn.closest('.ashrafco-copy-box').find('.ashrafco-copy-box__hint');
		var originalHint = $hint.attr('data-orig-hint') || $hint.text() || 'اضغط لنسخ الرقم';
		if (!$hint.attr('data-orig-hint')) {
			$hint.attr('data-orig-hint', originalHint);
		}

		function showCopySuccess() {
			$hint.addClass('is-copied').text('تم النسخ بنجاح! ✓');
			setTimeout(function () {
				$hint.removeClass('is-copied').text(originalHint);
			}, 2500);
		}

		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(textToCopy).then(showCopySuccess).catch(function () {
				fallbackCopy(textToCopy);
			});
		} else {
			fallbackCopy(textToCopy);
		}

		function fallbackCopy(text) {
			var $temp = $('<textarea>');
			$temp.css({ position: 'absolute', left: '-9999px', top: '0', opacity: '0' });
			$temp.val(text);
			$('body').append($temp);
			$temp[0].select();
			try {
				document.execCommand('copy');
				showCopySuccess();
			} catch (err) {
				// Failed gracefully
			}
			$temp.remove();
		}
	});
});


