<?php
/**
 * Modern High-Precision Product Card Component
 * Unified, Reusable Product Card matching Ashrafco Store Design Specification
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || !$product->is_visible()) {
	return;
}

$product_id = $product->get_id();
$permalink = get_permalink($product_id);
$in_stock = $product->is_in_stock();
$title = $product->get_name();

// Native WooCommerce Prices & Discounts Single Source of Truth
$discount_data = function_exists('ashrafco_get_product_discount_data')
	? ashrafco_get_product_discount_data($product)
	: array();

$is_on_sale = !empty($discount_data['is_on_sale']);
$regular_price = $discount_data['regular_price'] ?? (float) $product->get_regular_price();
$sale_price = $discount_data['sale_price'] ?? (float) $product->get_sale_price();
$current_price = $discount_data['current_price'] ?? (float) $product->get_price();
$discount_percent = !empty($discount_data['discount_percent']) ? absint($discount_data['discount_percent']) : 0;
if ($is_on_sale && $discount_percent <= 0 && $regular_price > $current_price && $regular_price > 0) {
	$discount_percent = (int) round((($regular_price - $current_price) / $regular_price) * 100);
}
$discount_label = !empty($discount_data['discount_label'])
	? $discount_data['discount_label']
	: ($discount_percent > 0 ? sprintf(__('خصم %d%%', 'ashrafco'), $discount_percent) : __('تخفيض', 'ashrafco'));
$has_countdown = !empty($discount_data['has_countdown']);
$sale_end_ts = $discount_data['sale_end_timestamp'] ?? 0;

// Countdown validation against current time
$has_active_countdown = $is_on_sale && $has_countdown && ($sale_end_ts > time());
// Limited-time promo label if on sale without valid future countdown
$is_limited_promo = $is_on_sale && !$has_active_countdown;

// Ratings & Reviews
$average_rating = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$rating_formatted = $average_rating > 0 ? number_format($average_rating, 1) : '';

// Thumbnail
$thumb_id = get_post_thumbnail_id($product_id);
$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'woocommerce_thumbnail') : '';
if (!$thumb_url) {
	$thumb_url = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
}
if (!$thumb_url && function_exists('wc_placeholder_img_src')) {
	$thumb_url = wc_placeholder_img_src('woocommerce_thumbnail');
}

// Wishlist State
$is_in_wishlist = function_exists('test_theme_is_product_in_wishlist') && test_theme_is_product_in_wishlist($product_id);

$card_classes = 'ref-product-card group' . ($is_on_sale ? ' has-active-discount' : '');
$regular_price_html = wc_price($regular_price);
?>

<div <?php wc_product_class($card_classes, $product); ?> data-product-id="<?php echo esc_attr($product_id); ?>"
	data-regular-price-html="<?php echo esc_attr($regular_price_html); ?>">

	<!-- Top Image Area & Overlays -->
	<div class="ref-image-wrapper">

		<!-- Top-Right: Stock / Discount Badge -->
		<?php if (!$in_stock): ?>
			<span class="ref-stock-badge">
				<?php esc_html_e('نفذ', 'ashrafco'); ?>
			</span>
		<?php elseif ($is_on_sale): ?>
			<span class="ref-discount-badge font-mono" dir="ltr">
				<?php
				if ($discount_percent > 0) {
					echo '-' . esc_html($discount_percent) . '%';
				} else {
					echo '%';
				}
				?>
			</span>
		<?php endif; ?>

		<!-- Top-Left: Floating Circular Wishlist Button -->
		<button type="button"
			class="test-theme-wishlist-toggle ref-wishlist-btn <?php echo $is_in_wishlist ? 'is-active' : ''; ?>"
			data-product-id="<?php echo esc_attr($product_id); ?>"
			aria-label="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>"
			title="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>">
			<svg class="ref-heart-icon" viewBox="0 0 24 24" aria-hidden="true">
				<path stroke-linecap="round" stroke-linejoin="round"
					d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
			</svg>
		</button>

		<!-- Product Image -->
		<a href="<?php echo esc_url($permalink); ?>" class="ref-img-link" title="<?php echo esc_attr($title); ?>">
			<?php if ($thumb_url): ?>
				<img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>" width="300" height="300"
					decoding="async" loading="lazy" class="ref-product-img">
			<?php else: ?>
				<div class="ref-img-placeholder">
					<svg class="w-10 h-10 text-slate-300 stroke-current" fill="none" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
							d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
						</path>
					</svg>
				</div>
			<?php endif; ?>
		</a>
	</div>

	<!-- Middle Body: Title, Promo Row & Rating -->
	<div class="ref-body-wrapper">
		<!-- Product Title (Centered, 2-line clamped) -->
		<h3 class="ref-product-title">
			<a href="<?php echo esc_url($permalink); ?>">
				<?php echo esc_html($title); ?>
			</a>
		</h3>

		<!-- Conditional Promotion Status Row -->
		<?php if ($has_active_countdown): ?>
			<!-- Case 1: Product with Active Discount & Future Sale End Date -->
			<div class="ref-promo-row is-countdown" data-countdown-end="<?php echo esc_attr($sale_end_ts); ?>"
				aria-label="<?php esc_attr_e('ينتهي خلال', 'ashrafco'); ?>">
				<div class="ref-countdown-boxes">
					<div class="ref-countdown-box">
						<span class="timer-days timer-num">00</span>
						<span class="timer-unit"><?php esc_html_e('يوم', 'ashrafco'); ?></span>
					</div>
					<div class="ref-countdown-box">
						<span class="timer-hours timer-num">00</span>
						<span class="timer-unit"><?php esc_html_e('ساعة', 'ashrafco'); ?></span>
					</div>
					<div class="ref-countdown-box">
						<span class="timer-minutes timer-num">00</span>
						<span class="timer-unit"><?php esc_html_e('دقيقة', 'ashrafco'); ?></span>
					</div>
					<div class="ref-countdown-box">
						<span class="timer-seconds timer-num">00</span>
						<span class="timer-unit"><?php esc_html_e('ثانية', 'ashrafco'); ?></span>
					</div>
				</div>
				<div class="ref-countdown-label">
					<span class="ref-countdown-title">
						<span class="ref-countdown-text"><?php esc_html_e('ينتهي خلال', 'ashrafco'); ?></span>
					</span>
					<svg class="ref-countdown-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor"
						stroke-width="2.2" aria-hidden="true">
						<circle cx="12" cy="12" r="10"></circle>
						<polyline points="12 6 12 12 16 14"></polyline>
					</svg>
				</div>
			</div>
		<?php elseif ($is_limited_promo): ?>
			<!-- Case 2: Product with Active Discount but No Sale End Date -->
			<div class="ref-promo-row is-limited-time" aria-label="<?php esc_attr_e('عرض لفترة محدودة', 'ashrafco'); ?>">
				<span class="ref-limited-text"><?php esc_html_e('عرض لفترة محدودة', 'ashrafco'); ?></span>
				<span class="ref-flame-icon" aria-hidden="true">🔥</span>
			</div>
		<?php endif; ?>
		<!-- Case 3: Product has no active discount -> Promotion row is completely omitted -->

		<!-- Rating Row -->
		<div class="ref-rating-row"
			aria-label="<?php echo $review_count > 0 ? esc_attr(sprintf(__('التقييم %s من %d تقييم', 'ashrafco'), $rating_formatted, $review_count)) : esc_attr__('لا توجد تقييمات بعد', 'ashrafco'); ?>">
			<?php if ($review_count > 0 && $average_rating > 0): ?>
				<span class="ref-star-icon" aria-hidden="true">⭐</span>
				<span class="ref-rating-score"><?php echo esc_html($rating_formatted); ?></span>
				<span
					class="ref-review-count">(<?php echo esc_html(sprintf(__('%d تقييم', 'ashrafco'), $review_count)); ?>)</span>
			<?php else: ?>
				<span class="ref-star-icon is-empty" aria-hidden="true">⭐</span>
				<span class="ref-no-reviews"><?php esc_html_e('لا توجد تقييمات', 'ashrafco'); ?></span>
			<?php endif; ?>
		</div>

		<!-- Price Row -->
		<div class="ref-price-row">
			<div class="ref-current-price <?php echo $is_on_sale ? 'is-discounted' : ''; ?>">
				<?php echo wc_price($current_price); ?>
			</div>
			<?php if ($is_on_sale && $regular_price > $current_price): ?>
				<div class="ref-old-price">
					<?php echo wc_price($regular_price); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Bottom Section: Full-Width Add-to-Cart Button -->
	<div class="ref-cart-action-wrapper">
		<?php if (!$in_stock): ?>
			<a href="<?php echo esc_url($permalink); ?>"
				class="ref-add-to-cart-btn ref-out-of-stock-btn button w-full bg-slate-400 hover:bg-slate-500 text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors opacity-80 cursor-not-allowed"
				aria-label="<?php echo esc_attr($title); ?> - <?php esc_attr_e('نفذ من المخزن', 'ashrafco'); ?>">
				<svg class="ref-cart-icon w-5 h-5 flex-shrink-0 fill-current text-white" viewBox="0 0 24 24"
					aria-hidden="true">
					<path
						d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
				</svg>
				<span class="ref-btn-text"><?php esc_html_e('نفذ من المخزن', 'ashrafco'); ?></span>
			</a>
		<?php elseif ($product->is_type('variable')): ?>
			<a href="<?php echo esc_url($permalink); ?>"
				class="ref-add-to-cart-btn ref-variable-btn button w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors"
				aria-label="<?php echo esc_attr($title); ?> - <?php esc_attr_e('اختر الخيارات', 'ashrafco'); ?>">
				<svg class="ref-cart-icon w-5 h-5 flex-shrink-0 fill-current text-white" viewBox="0 0 24 24"
					aria-hidden="true">
					<path
						d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
				</svg>
				<span class="ref-btn-text"><?php esc_html_e('اختر الخيارات', 'ashrafco'); ?></span>
			</a>
		<?php elseif ($product->is_purchasable()): ?>
			<a href="<?php echo esc_url($product->add_to_cart_url()); ?>" data-quantity="1"
				class="ref-add-to-cart-btn button add_to_cart_button ajax_add_to_cart w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors"
				data-product_id="<?php echo esc_attr($product_id); ?>"
				data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
				aria-label="<?php echo esc_attr($product->add_to_cart_description()); ?>" rel="nofollow">
				<svg class="ref-cart-icon w-5 h-5 flex-shrink-0 fill-current text-white" viewBox="0 0 24 24"
					aria-hidden="true">
					<path
						d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
				</svg>
				<span class="ref-btn-text"><?php esc_html_e('أضف إلى السلة', 'ashrafco'); ?></span>
			</a>
		<?php else: ?>
			<a href="<?php echo esc_url($permalink); ?>"
				class="ref-add-to-cart-btn ref-view-btn button w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors"
				aria-label="<?php echo esc_attr($title); ?>">
				<svg class="ref-cart-icon w-5 h-5 flex-shrink-0 fill-current text-white" viewBox="0 0 24 24"
					aria-hidden="true">
					<path
						d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
				</svg>
				<span class="ref-btn-text"><?php esc_html_e('عرض المنتج', 'ashrafco'); ?></span>
			</a>
		<?php endif; ?>
	</div>

</div>