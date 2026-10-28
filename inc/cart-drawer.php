<?php
/**
 * Modern Responsive Shopping Cart Drawer & Bottom Sheet for Ashrafco (A&S)
 *
 * Implements high-conversion side drawer on Desktop (100dvh, left-sliding)
 * and bottom sheet on Mobile (< 768px, 90dvh, drag pill).
 * Provides AJAX add to cart, live quantity adjustments (+/-), item removal,
 * and seamless integration with Ashrafco Wishlist system.
 *
 * @package Ashrafco
 * @since 1.1.0
 */

// Prevent direct script access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Enqueue Cart Drawer JavaScript and localize necessary settings.
 */
function ashrafco_enqueue_cart_drawer_scripts()
{
	if (is_admin() || !function_exists('WC')) {
		return;
	}

	$js_rel = file_exists(get_template_directory() . '/assets/src/js/cart-drawer.js') ? '/assets/src/js/cart-drawer.js' : '/assets/js/cart-drawer.js';
	wp_enqueue_script(
		'ashrafco_cart_drawer',
		get_template_directory_uri() . $js_rel,
		array('jquery'),
		function_exists('custom_store_get_asset_version') ? custom_store_get_asset_version($js_rel) : (function_exists('ashrafco_get_asset_version') ? ashrafco_get_asset_version($js_rel) : '1.0.0'),
		true
	);

	wp_localize_script(
		'ashrafco_cart_drawer',
		'ashrafcoCartData',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'wcAjaxUrl' => WC_AJAX::get_endpoint('%%endpoint%%'),
			'nonce' => wp_create_nonce('ashrafco_cart_drawer_nonce'),
			'cartUrl' => wc_get_cart_url(),
			'checkoutUrl' => wc_get_checkout_url(),
			'shopUrl' => wc_get_page_permalink('shop'),
			'currencySymbol' => get_woocommerce_currency_symbol(),
			'isRTL' => is_rtl(),
			'i18n' => array(
				'adding' => __('جاري الإضافة...', 'ashrafco'),
				'added' => __('تمت الإضافة للسلة ✓', 'ashrafco'),
				'updating' => __('جاري التحديث...', 'ashrafco'),
				'removing' => __('جاري الحذف...', 'ashrafco'),
				'emptyCartTitle' => __('عربة التسوق فارغة', 'ashrafco'),
				'emptyCartDesc' => sprintf(__('لم تقم بإضافة أي منتجات للسلة بعد. تصفح أحدث ماكينات ومعدات %s الآن.', 'ashrafco'), function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو'),
				'continueShop' => __('تصفح المتجر الآن', 'ashrafco'),
			),
		)
	);
}
add_action('wp_enqueue_scripts', 'ashrafco_enqueue_cart_drawer_scripts', 25);

/**
 * Output Cart Drawer Markup in wp_footer.
 */
function ashrafco_output_cart_drawer()
{
	if (is_admin() || !function_exists('WC') || !WC()->cart) {
		return;
	}

	get_template_part('template-parts/cart/cart-drawer');
}
add_action('wp_footer', 'ashrafco_output_cart_drawer', 19);

/**
 * Helper to render individual cart items HTML for the drawer.
 *
 * @return string HTML output of cart items.
 */
function ashrafco_get_cart_drawer_items_html()
{
	if (!function_exists('WC') || !WC()->cart) {
		return '';
	}

	ob_start();

	if (WC()->cart->is_empty()) {
		?>
		<div class="ashrafco-cart-empty-state text-center py-12 px-6 space-y-4">
			<div class="w-20 h-20 mx-auto rounded-3xl bg-red-50 text-[#d90429] flex items-center justify-center shadow-inner">
				<svg class="w-10 h-10 stroke-[#d90429]" fill="none" stroke-width="1.8" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
			</div>
			<div class="space-y-1">
				<h3 class="text-lg font-bold text-slate-900 m-0">عربة التسوق فارغة</h3>
				<p class="text-xs sm:text-sm text-slate-500 max-w-xs mx-auto leading-relaxed m-0">
					لم تقم بإضافة أي معدات أو ماكينات بعد. تصفح منتجاتنا المميزة وأضف ما يناسبك.
				</p>
			</div>
			<div class="pt-3">
				<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
					class="ashrafco-empty-shop-btn inline-flex items-center justify-center gap-2 bg-[#d90429] hover:bg-[#b90323] text-white font-bold text-sm py-3 px-8 rounded-xl shadow-md transition-all active:scale-95 no-underline">
					<span>تصفح المنتجات الآن</span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
		</div>
		<?php
	} else {
		?>
		<div class="ashrafco-cart-items-list divide-y divide-slate-100">
			<?php
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
				$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

				if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0 || !apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
					continue;
				}

				$product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
				$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);

				// Product Image Thumbnail
				$thumbnail = $_product->get_image(
					array(76, 76),
					array(
						'class' => 'w-[72px] h-[72px] sm:w-[76px] sm:h-[76px] object-contain rounded-xl bg-slate-50 border border-slate-100 p-1 flex-shrink-0 transition-transform group-hover:scale-105',
						'alt' => esc_attr($product_name),
					)
				);

				// Line Subtotal (Respecting unit price * qty)
				$line_price = WC()->cart->get_product_price($_product);

				// Check if product is in wishlist
				$is_in_wishlist = function_exists('test_theme_is_product_in_wishlist') && test_theme_is_product_in_wishlist($product_id);
				?>
				<div class="ashrafco-cart-item py-4 flex items-center justify-between gap-3 group transition-colors relative"
					data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
					data-product-id="<?php echo esc_attr($product_id); ?>">

					<!-- 1. Right: Product Image -->
					<div class="ashrafco-item-img-wrapper flex-shrink-0">
						<?php if (!empty($product_permalink)): ?>
							<a href="<?php echo esc_url($product_permalink); ?>" class="block"
								title="<?php echo esc_attr($product_name); ?>">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php else: ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>

					<!-- 2. Middle: Product Details (Title, Price, Quantity) -->
					<div class="ashrafco-item-content flex-1 min-w-0 pr-1 text-right">
						<!-- Title -->
						<h4 class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 leading-snug m-0">
							<?php if (!empty($product_permalink)): ?>
								<a href="<?php echo esc_url($product_permalink); ?>"
									class="hover:text-[#d90429] transition-colors no-underline text-slate-900">
									<?php echo wp_kses_post($product_name); ?>
								</a>
							<?php else: ?>
								<?php echo wp_kses_post($product_name); ?>
							<?php endif; ?>
						</h4>

						<?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

						<!-- Price in vibrant green matching image -->
						<div class="ashrafco-item-price text-xs sm:text-sm font-extrabold text-[#059669] font-mono mt-1">
							<?php echo wp_kses_post($line_price); ?>
						</div>

						<!-- Quantity Controls Pill: [+  Qty  -] -->
						<div class="mt-2.5 inline-flex items-center">
							<div class="ashrafco-qty-picker inline-flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden shadow-xs h-7 select-none"
								dir="ltr">
								<!-- Plus button (+) on left -->
								<button type="button"
									class="ashrafco-qty-btn ashrafco-qty-plus w-7 h-7 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 active:scale-95 transition-all text-base font-bold focus:outline-none"
									data-action="plus" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
									aria-label="<?php esc_attr_e('زيادة الكمية', 'ashrafco'); ?>">
									+
								</button>

								<!-- Quantity Number in Center -->
								<span
									class="ashrafco-qty-val min-w-[28px] text-center font-bold text-xs sm:text-sm text-slate-800 font-mono px-1">
									<?php echo esc_html($cart_item['quantity']); ?>
								</span>

								<!-- Minus button (-) on right -->
								<button type="button"
									class="ashrafco-qty-btn ashrafco-qty-minus w-7 h-7 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 active:scale-95 transition-all text-base font-bold focus:outline-none <?php echo ($cart_item['quantity'] <= 1) ? 'opacity-40 cursor-not-allowed' : ''; ?>"
									data-action="minus" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
									aria-label="<?php esc_attr_e('تقليل الكمية', 'ashrafco'); ?>" <?php echo ($cart_item['quantity'] <= 1) ? 'disabled' : ''; ?>>
									−
								</button>
							</div>
						</div>
					</div>

					<!-- 3. Left: Action Buttons (Wishlist Heart Top, Delete Trash Bottom) -->
					<div
						class="ashrafco-item-actions flex flex-col items-center justify-between self-stretch flex-shrink-0 pl-1 py-0.5">
						<!-- Wishlist Heart Toggle Button -->
						<button type="button"
							class="test-theme-wishlist-toggle ashrafco-drawer-wishlist-btn w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-[#d90429] hover:bg-red-50/60 transition-all focus:outline-none <?php echo $is_in_wishlist ? 'is-active' : ''; ?>"
							data-product-id="<?php echo esc_attr($product_id); ?>"
							title="<?php echo $is_in_wishlist ? esc_attr__('إزالة من المفضلة', 'ashrafco') : esc_attr__('حفظ في المفضلة', 'ashrafco'); ?>"
							aria-label="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>">
							<svg class="w-5 h-5 transition-transform active:scale-125 <?php echo $is_in_wishlist ? 'fill-[#d90429] stroke-[#d90429]' : 'stroke-slate-700 fill-none'; ?>"
								viewBox="0 0 24 24" stroke-width="1.8" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
							</svg>
						</button>

						<!-- Delete Trash Button -->
						<button type="button"
							class="ashrafco-drawer-remove-btn w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-[#d90429] hover:bg-red-50/60 transition-all focus:outline-none cursor-pointer"
							data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>"
							data-product-id="<?php echo esc_attr($product_id); ?>"
							title="<?php esc_attr_e('حذف المنتج من السلة', 'ashrafco'); ?>"
							aria-label="<?php esc_attr_e('حذف المنتج من السلة', 'ashrafco'); ?>">
							<svg class="w-5 h-5 transition-transform active:scale-110" fill="none" stroke="currentColor"
								stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</button>
					</div>

				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	return ob_get_clean();
}

/**
 * Helper to render cart drawer footer HTML.
 *
 * @return string HTML output of cart footer.
 */
function ashrafco_get_cart_drawer_footer_html()
{
	if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
		return '';
	}

	ob_start();
	?>
	<div class="ashrafco-cart-footer-inner space-y-3">
		<!-- Summary Total Box (Soft Red/Pink Tint Matching Reference) -->
		<div
			class="ashrafco-cart-total-box bg-[#fff1f2] border border-[#fecdd3]/40 rounded-2xl p-3.5 sm:p-4 flex items-center justify-between">
			<!-- Total Label (Right in RTL) -->
			<span class="text-sm sm:text-base font-bold text-slate-800">
				مجموع العربة
			</span>
			<!-- Total Amount (Left in RTL, Bold Brand Red) -->
			<span class="ashrafco-cart-subtotal-val text-lg sm:text-xl font-black text-[#d90429] font-mono leading-none">
				<?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>

		<!-- Action Buttons: Side-by-side matching reference image (Continue Shopping on right, Checkout on left) -->
		<div class="ashrafco-cart-actions flex items-center gap-3 pt-1">
			<!-- Secondary Button: Continue Shopping (Dark, on Right in RTL) -->
			<button type="button"
				class="ashrafco-continue-shopping-btn flex-1 bg-[#1e293b] hover:bg-[#0f172a] text-white font-bold text-sm sm:text-base py-3.5 px-4 rounded-xl text-center shadow-xs transition-all active:scale-[0.98] focus:outline-none select-none cursor-pointer">
				متابعة التسوق
			</button>

			<!-- Primary Button: Checkout (Red, on Left in RTL) -->
			<a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
				class="ashrafco-checkout-cta flex-1 bg-[#d90429] hover:bg-[#b90323] text-white font-extrabold text-sm sm:text-base py-3.5 px-4 rounded-xl text-center shadow-md hover:shadow-lg transition-all active:scale-[0.98] no-underline select-none">
				إتمام الشراء
			</a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Register Drawer Fragments in woocommerce_add_to_cart_fragments.
 */
function ashrafco_cart_drawer_fragments($fragments)
{
	if (!function_exists('WC') || !WC()->cart) {
		return $fragments;
	}

	$cart_count = WC()->cart->get_cart_contents_count();

	// 1. Drawer Body HTML
	$fragments['#ashrafco-cart-body'] = '<div id="ashrafco-cart-body" class="ashrafco-cart-body flex-1 overflow-y-auto px-4 sm:px-6 py-2">' . ashrafco_get_cart_drawer_items_html() . '</div>';

	// 2. Drawer Footer HTML
	$fragments['#ashrafco-cart-footer'] = '<div id="ashrafco-cart-footer" class="ashrafco-cart-footer bg-white border-t border-slate-100 p-4 sm:p-5 flex-shrink-0 ' . (WC()->cart->is_empty() ? 'hidden' : '') . '">' . ashrafco_get_cart_drawer_footer_html() . '</div>';

	// 3. Drawer Header Count Span
	$fragments['.ashrafco-cart-items-count'] = '<span class="ashrafco-cart-items-count">' . esc_html($cart_count) . '</span>';

	// 4. Header Badges
	$fragments['#header-cart-count-badge'] = '<span id="header-cart-count-badge" class="ashrafco-cart-count-badge absolute -top-1.5 -right-2 bg-[#ff171f] text-white text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs">' . esc_html($cart_count) . '</span>';

	$fragments['#mobile-nav-cart-badge'] = '<span id="mobile-nav-cart-badge" class="ashrafco-cart-count-badge absolute -top-1 -right-1 bg-[#d90429] text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center leading-none">' . esc_html($cart_count) . '</span>';

	return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'ashrafco_cart_drawer_fragments', 35);

/**
 * AJAX Handler: Update Item Quantity in Cart (+ / -).
 */
function ashrafco_ajax_update_cart_qty()
{
	check_ajax_referer('ashrafco_cart_drawer_nonce', 'nonce');

	if (!function_exists('WC') || !WC()->cart) {
		wp_send_json_error(array('message' => __('WooCommerce غير متوفر.', 'ashrafco')));
	}

	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
	$new_quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

	if (empty($cart_item_key)) {
		wp_send_json_error(array('message' => __('معرف العنصر غير صالح.', 'ashrafco')));
	}

	if ($new_quantity <= 0) {
		WC()->cart->remove_cart_item($cart_item_key);
	} else {
		WC()->cart->set_quantity($cart_item_key, $new_quantity, true);
	}

	WC()->cart->calculate_totals();

	$cart_count = WC()->cart->get_cart_contents_count();

	wp_send_json_success(array(
		'cart_count' => $cart_count,
		'cart_total' => WC()->cart->get_cart_subtotal(),
		'is_empty' => WC()->cart->is_empty(),
		'items_html' => ashrafco_get_cart_drawer_items_html(),
		'footer_html' => ashrafco_get_cart_drawer_footer_html(),
		'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array()),
	));
}
add_action('wp_ajax_ashrafco_update_cart_qty', 'ashrafco_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_ashrafco_update_cart_qty', 'ashrafco_ajax_update_cart_qty');

/**
 * AJAX Handler: Remove Item from Cart.
 */
function ashrafco_ajax_remove_cart_item()
{
	check_ajax_referer('ashrafco_cart_drawer_nonce', 'nonce');

	if (!function_exists('WC') || !WC()->cart) {
		wp_send_json_error(array('message' => __('WooCommerce غير متوفر.', 'ashrafco')));
	}

	$cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';

	if (empty($cart_item_key)) {
		wp_send_json_error(array('message' => __('معرف العنصر غير صالح.', 'ashrafco')));
	}

	$removed = WC()->cart->remove_cart_item($cart_item_key);

	if (!$removed) {
		wp_send_json_error(array('message' => __('تعذر حذف العنصر من السلة.', 'ashrafco')));
	}

	WC()->cart->calculate_totals();

	$cart_count = WC()->cart->get_cart_contents_count();

	wp_send_json_success(array(
		'cart_count' => $cart_count,
		'cart_total' => WC()->cart->get_cart_subtotal(),
		'is_empty' => WC()->cart->is_empty(),
		'items_html' => ashrafco_get_cart_drawer_items_html(),
		'footer_html' => ashrafco_get_cart_drawer_footer_html(),
		'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array()),
	));
}
add_action('wp_ajax_ashrafco_remove_cart_item', 'ashrafco_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_ashrafco_remove_cart_item', 'ashrafco_ajax_remove_cart_item');

/**
 * AJAX Handler: Intercept single product or custom add to cart.
 */
function ashrafco_ajax_add_to_cart()
{
	check_ajax_referer('ashrafco_cart_drawer_nonce', 'nonce');

	if (!function_exists('WC') || !WC()->cart) {
		wp_send_json_error(array('message' => __('WooCommerce غير متوفر.', 'ashrafco')));
	}

	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : (isset($_POST['add-to-cart']) ? absint($_POST['add-to-cart']) : 0);
	$quantity = isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : 1;
	$variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
	$variations = array();

	foreach ($_POST as $key => $value) {
		if ('attribute_' === substr($key, 0, 10)) {
			$variations[sanitize_key($key)] = sanitize_text_field(wp_unslash($value));
		}
	}

	$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations);

	if ($passed_validation && $product_id > 0) {
		$cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations);

		if ($cart_item_key) {
			do_action('woocommerce_ajax_added_to_cart', $product_id);

			WC()->cart->calculate_totals();

			wp_send_json_success(array(
				'cart_hash' => WC()->cart->get_cart_hash(),
				'cart_count' => WC()->cart->get_cart_contents_count(),
				'cart_total' => WC()->cart->get_cart_subtotal(),
				'items_html' => ashrafco_get_cart_drawer_items_html(),
				'footer_html' => ashrafco_get_cart_drawer_footer_html(),
				'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array()),
			));
		}
	}

	// Capture notices if failed
	$notices = wc_get_notices('error');
	$error_msg = !empty($notices) ? wp_strip_all_tags(reset($notices)['notice']) : __('تعذر إضافة المنتج إلى السلة.', 'ashrafco');
	wc_clear_notices();

	wp_send_json_error(array('message' => $error_msg));
}
add_action('wp_ajax_ashrafco_add_to_cart', 'ashrafco_ajax_add_to_cart');
add_action('wp_ajax_nopriv_ashrafco_add_to_cart', 'ashrafco_ajax_add_to_cart');
