/**
 * Quick Product Manager Admin Interactions
 *
 * Handles inline AJAX updates, stock toggling, and fast product trashing.
 *
 * @package Ashrafco
 */

jQuery(document).ready(function($) {
	'use strict';

	// Toast Helper
	function showToast(message, type) {
		var $toast = $('#test-theme-toast');
		var $icon  = $('#test-theme-toast-icon');
		var $msg   = $('#test-theme-toast-msg');

		if (!$toast.length) return;

		if (type === 'error') {
			$toast.css('border-right-color', '#ef4444');
			$icon.text('✕').css('color', '#f87171');
		} else {
			$toast.css('border-right-color', '#10b981');
			$icon.text('✓').css('color', '#34d399');
		}

		$msg.text(message);
		$toast.css('display', 'inline-flex').hide().stop(true, true).fadeIn(200);

		setTimeout(function() {
			$toast.fadeOut(300);
		}, 3200);
	}

	// 1. Stock Status Toggle Button
	$(document).on('click', '.test-theme-stock-toggle-btn', function(e) {
		e.preventDefault();
		var $btn = $(this);
		var currentStatus = $btn.attr('data-status');

		if (currentStatus === 'instock') {
			$btn.attr('data-status', 'outofstock');
			$btn.text('✕ غير متوفر');
			$btn.css({
				'background': '#fef2f2',
				'color': '#991b1b',
				'border-color': '#fecaca'
			});
		} else {
			$btn.attr('data-status', 'instock');
			$btn.text('✓ متوفر');
			$btn.css({
				'background': '#f0fdf4',
				'color': '#166534',
				'border-color': '#86efac'
			});
		}

		// Highlight row as modified
		$btn.closest('tr').addClass('test-theme-row-dirty');
	});

	// Mark row dirty when any input changes
	$(document).on('input change', '.test-theme-product-row input', function() {
		$(this).closest('tr').addClass('test-theme-row-dirty');
	});

	// 2. Inline AJAX Save Product
	$(document).on('click', '.test-theme-quick-save-btn', function(e) {
		e.preventDefault();
		var $btn       = $(this);
		var $row       = $btn.closest('tr');
		var productId  = $row.data('product-id');
		var regPrice   = $row.find('.test-theme-reg-price-input').val();
		var salePrice  = $row.find('.test-theme-sale-price-input').val();
		var sku        = $row.find('.test-theme-sku-input').val();
		var stockQty   = $row.find('.test-theme-stock-qty-input').val();
		var stockStatus= $row.find('.test-theme-stock-toggle-btn').attr('data-status');

		// UI Loading State
		$btn.prop('disabled', true);
		$btn.find('.btn-text').hide();
		$btn.find('.btn-spinner').show();
		$row.css('opacity', '0.75');

		$.ajax({
			url: testThemeQuickManager.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'test_theme_quick_update_product',
				nonce: testThemeQuickManager.nonce,
				product_id: productId,
				regular_price: regPrice,
				sale_price: salePrice,
				sku: sku,
				stock_qty: stockQty,
				stock_status: stockStatus
			},
			success: function(response) {
				if (response && response.success) {
					// Success green flash
					$row.removeClass('test-theme-row-dirty');
					$row.css({
						'background-color': '#f0fdf4',
						'box-shadow': 'inset 0 0 0 2px #86efac'
					});

					setTimeout(function() {
						$row.css({
							'background-color': '',
							'box-shadow': ''
						});
					}, 1200);

					showToast(response.data.message || 'تم حفظ التعديلات بنجاح!', 'success');
				} else {
					var err = (response && response.data && response.data.message) ? response.data.message : 'حدث خطأ أثناء حفظ التعديلات.';
					showToast(err, 'error');
				}
			},
			error: function() {
				showToast('تعذر الاتصال بالخادم، يرجى المحاولة مرة أخرى.', 'error');
			},
			complete: function() {
				$btn.prop('disabled', false);
				$btn.find('.btn-text').show();
				$btn.find('.btn-spinner').hide();
				$row.css('opacity', '1');
			}
		});
	});

	// 3. Quick Product Trashing
	$(document).on('click', '.test-theme-quick-trash-btn', function(e) {
		e.preventDefault();
		var $btn      = $(this);
		var $row      = $btn.closest('tr');
		var productId = $row.data('product-id');

		if (!confirm('هل أنت متأكد من رغبتك في نقل هذه المعدة إلى سلة المهملات؟')) {
			return;
		}

		$btn.prop('disabled', true);
		$row.css('opacity', '0.5');

		$.ajax({
			url: testThemeQuickManager.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'test_theme_quick_delete_product',
				nonce: testThemeQuickManager.nonce,
				product_id: productId
			},
			success: function(response) {
				if (response && response.success) {
					$row.css('background-color', '#fee2e2');
					$row.fadeOut(400, function() {
						$(this).remove();
					});
					showToast(response.data.message || 'تم حذف المعدة بنجاح.', 'success');
				} else {
					var err = (response && response.data && response.data.message) ? response.data.message : 'فشل حذف المعدة.';
					showToast(err, 'error');
					$btn.prop('disabled', false);
					$row.css('opacity', '1');
				}
			},
			error: function() {
				showToast('تعذر الاتصال بالخادم.', 'error');
				$btn.prop('disabled', false);
				$row.css('opacity', '1');
			}
		});
	});
});
