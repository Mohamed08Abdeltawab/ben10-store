<?php
/**
 * Modern High-Precision Single Product Template Part
 * Recreated accurately from reference design specification for Ashrafco.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (empty($product) || !is_a($product, 'WC_Product')) {
	return;
}

$product_id = $product->get_id();
$permalink = get_permalink($product_id);
$title = $product->get_name();
$short_desc = $product->get_short_description();
$full_desc = $product->get_description();
$in_stock = $product->is_in_stock();
$is_on_sale = $product->is_on_sale();
$is_featured = $product->is_featured();
$product_type = $product->get_type();

// Pricing & Calculations
$regular_price = (float) $product->get_regular_price();
$sale_price = (float) $product->get_sale_price();
$current_price = (float) $product->get_price();

$discount_percent = 0;
if ($is_on_sale && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
	$discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
}

// Ratings & Reviews
$average_rating = (float) $product->get_average_rating();
$review_count = (int) $product->get_review_count();
$rating_display = $average_rating > 0 ? number_format($average_rating, 1) : '';

// Product Categories Hierarchy
$terms = get_the_terms($product_id, 'product_cat');
$primary_cat = ($terms && !is_wp_error($terms)) ? $terms[0] : null;

// Wishlist Status
$is_in_wishlist = function_exists('test_theme_is_product_in_wishlist') && test_theme_is_product_in_wishlist($product_id);

// WhatsApp Direct URL
$phone_number = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', apply_filters('test_theme_whatsapp_phone', '201065712366'));
$store_name_ar = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$store_name_en = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('en') : 'Ashrafco';
$wa_message = rawurlencode(sprintf("مرحباً متجر %s (%s)، أود الاستفسار وطلب منتج:\n%s\nالرابط: %s", $store_name_ar, $store_name_en, $title, $permalink));
$wa_url = 'https://wa.me/' . $phone_number . '?text=' . $wa_message;

// Build Gallery Image Dataset
$main_image_id = $product->get_image_id();
$gallery_img_ids = $product->get_gallery_image_ids();
$all_image_ids = array();

if ($main_image_id) {
	$all_image_ids[] = $main_image_id;
}
foreach ($gallery_img_ids as $gid) {
	if ($gid && !in_array($gid, $all_image_ids, true)) {
		$all_image_ids[] = $gid;
	}
}

$gallery_images_data = array();
if (!empty($all_image_ids)) {
	foreach ($all_image_ids as $img_id) {
		$single_url = wp_get_attachment_image_url($img_id, 'woocommerce_single');
		$thumb_url = wp_get_attachment_image_url($img_id, 'woocommerce_gallery_thumbnail');
		$full_url = wp_get_attachment_image_url($img_id, 'full');
		$alt = get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: $title;

		if ($single_url) {
			$gallery_images_data[] = array(
				'id' => $img_id,
				'single' => $single_url,
				'thumb' => $thumb_url ?: $single_url,
				'full' => $full_url ?: $single_url,
				'alt' => $alt,
			);
		}
	}
}

// Fallback image if product has no media
if (empty($gallery_images_data)) {
	$placeholder = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_single') : '';
	$gallery_images_data[] = array(
		'id' => 0,
		'single' => $placeholder,
		'thumb' => $placeholder,
		'full' => $placeholder,
		'alt' => $title,
	);
}

$active_main_single = $gallery_images_data[0]['single'];
$active_main_full = $gallery_images_data[0]['full'];
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 font-sans', $product); ?> dir="rtl">

	<!-- 1. Breadcrumbs Navigation -->
	<nav class="mb-6 text-xs sm:text-sm text-slate-400 flex items-center gap-2 flex-wrap select-none"
		aria-label="Breadcrumbs">
		<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors">الرئيسية</a>
		<span class="text-slate-300">&gt;</span>
		<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
			class="hover:text-primary transition-colors">المتجر</a>
		<?php if ($primary_cat): ?>
				<?php
				if ($primary_cat->parent > 0) {
					$parent_cat = get_term($primary_cat->parent, 'product_cat');
					if ($parent_cat && !is_wp_error($parent_cat)) {
						echo '<span class="text-slate-300">&gt;</span>';
						echo '<a href="' . esc_url(get_term_link($parent_cat)) . '" class="hover:text-primary transition-colors">' . esc_html($parent_cat->name) . '</a>';
					}
				}
				?>
				<span class="text-slate-300">&gt;</span>
				<a href="<?php echo esc_url(get_term_link($primary_cat)); ?>" class="hover:text-primary transition-colors">
					<?php echo esc_html($primary_cat->name); ?>
				</a>
		<?php endif; ?>
		<span class="text-slate-300">&gt;</span>
		<span
			class="text-slate-700 font-semibold truncate max-w-xs sm:max-w-md"><?php echo esc_html($title); ?></span>
	</nav>

	<!-- 2. Main Product Section (2-Column Desktop Grid) -->
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start mb-12">

		<!-- Right Column in RTL: Gallery Showcase (Cols 6) -->
		<div class="lg:col-span-6 order-1 lg:order-1 relative">
			<div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/90 shadow-2xs relative overflow-hidden">

				<!-- Top-Right: Discount Badge -->
				<?php if ($is_on_sale && $discount_percent > 0): ?>
						<span
							class="absolute top-5 right-5 z-20 bg-red-600 text-white text-xs font-black px-3.5 py-1.5 rounded-full shadow-sm select-none">
							خصم <?php echo esc_html($discount_percent); ?>%
						</span>
				<?php endif; ?>

				<!-- Top-Left: Floating Wishlist Button -->
				<button type="button"
					class="test-theme-wishlist-toggle absolute top-5 left-5 z-20 w-11 h-11 rounded-2xl bg-white hover:bg-rose-50 shadow-sm border border-slate-200/80 flex items-center justify-center transition-all duration-200 active:scale-95 cursor-pointer select-none <?php echo $is_in_wishlist ? 'is-active' : ''; ?>"
					data-product-id="<?php echo esc_attr($product_id); ?>"
					aria-label="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>"
					title="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>">
					<svg class="w-5 h-5 <?php echo $is_in_wishlist ? 'fill-red-600 stroke-red-600' : 'fill-none stroke-slate-400 hover:stroke-red-600'; ?> transition-colors"
						stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round"
							d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
					</svg>
				</button>

				<!-- Main Display Image Container with Arrows -->
				<div id="ashrafco-gallery-container"
					class="relative w-full h-[320px] sm:h-[400px] flex items-center justify-center p-2 group select-none overflow-hidden">

					<?php if (count($gallery_images_data) > 1): ?>
							<!-- Next Arrow (Left side in RTL: Advances forward to next photo) -->
							<button type="button" id="ashrafco-gallery-next"
								class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 hover:bg-white shadow-md border border-slate-200 text-slate-700 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer focus:outline-none"
								aria-label="الصورة التالية">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
										d="M15 19l-7-7 7-7" />
								</svg>
							</button>

							<!-- Prev Arrow (Right side in RTL: Goes back to previous photo) -->
							<button type="button" id="ashrafco-gallery-prev"
								class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-white/95 hover:bg-white shadow-md border border-slate-200 text-slate-700 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer focus:outline-none"
								aria-label="الصورة السابقة">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
								</svg>
							</button>
					<?php endif; ?>

					<!-- Main Product Image -->
					<img id="ashrafco-gallery-main-img" src="<?php echo esc_url($active_main_single); ?>"
						data-full-src="<?php echo esc_url($active_main_full); ?>"
						alt="<?php echo esc_attr($title); ?>"
						class="max-h-full max-w-full object-contain cursor-zoom-in will-change-transform select-none"
						draggable="false">

					<!-- Lightbox Zoom Icon Button (Bottom-Left in RTL) -->
					<button type="button" id="ashrafco-gallery-zoom-btn"
						class="absolute bottom-2 left-2 z-20 w-9 h-9 rounded-xl bg-white/90 hover:bg-white shadow-md border border-slate-200 text-slate-600 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer"
						title="تكبير الصورة" aria-label="تكبير الصورة">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
						</svg>
					</button>
				</div>

				<!-- Gallery Thumbnails Row -->
				<?php if (count($gallery_images_data) > 1): ?>
						<div class="flex items-center gap-2.5 sm:gap-3 mt-5 overflow-x-auto no-scrollbar py-2 px-1 scroll-smooth"
							id="ashrafco-gallery-thumbs"
							style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
							<?php foreach ($gallery_images_data as $idx => $img_item): ?>
									<button type="button" data-index="<?php echo esc_attr($idx); ?>"
										data-single="<?php echo esc_url($img_item['single']); ?>"
										data-full="<?php echo esc_url($img_item['full']); ?>"
										class="ashrafco-gallery-thumb w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 p-1 bg-white flex items-center justify-center overflow-hidden transition-all duration-200 cursor-pointer flex-shrink-0 <?php echo 0 === $idx ? 'border-red-600 shadow-sm scale-105' : 'border-slate-200/80 hover:border-slate-300 opacity-70 hover:opacity-100'; ?>"
										aria-label="عرض صورة رقم <?php echo ($idx + 1); ?>">
										<img src="<?php echo esc_url($img_item['thumb']); ?>" alt=""
											class="max-h-full max-w-full object-contain pointer-events-none select-none">
									</button>
							<?php endforeach; ?>
						</div>
				<?php endif; ?>

			</div>
		</div>

		<!-- Left Column in RTL: Product Info & Purchase Controls (Cols 6) -->
		<div class="lg:col-span-6 order-2 lg:order-2 space-y-4">

			<!-- Top Badges Row -->
			<div class="flex items-center gap-2 flex-wrap">
				<?php if ($is_featured): ?>
						<span class="text-xs font-bold text-slate-700 bg-slate-100 px-3.5 py-1 rounded-full">
							الأكثر مبيعاً
						</span>
				<?php elseif ($primary_cat): ?>
						<span class="text-xs font-bold text-slate-700 bg-slate-100 px-3.5 py-1 rounded-full">
							<?php echo esc_html($primary_cat->name); ?>
						</span>
				<?php endif; ?>

				<?php if ($is_on_sale && $discount_percent > 0): ?>
						<span class="text-xs font-bold text-white bg-red-600 px-3 py-1 rounded-full shadow-2xs">
							خصم <?php echo esc_html($discount_percent); ?>%
						</span>
				<?php endif; ?>

				<?php if ($in_stock): ?>
						<span
							class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-300/80 px-3.5 py-1 rounded-full">
							<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
							متوفر في المخزون
						</span>
				<?php else: ?>
						<span
							class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3.5 py-1 rounded-full">
							غير متوفر حالياً
						</span>
				<?php endif; ?>
			</div>

			<!-- Product Title -->
			<h1 class="text-2xl sm:text-3xl font-black text-slate-900 leading-snug tracking-tight font-sans">
				<?php echo esc_html($title); ?>
			</h1>

			<!-- Short Description -->
			<?php if ($short_desc): ?>
					<p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">
						<?php echo wp_kses_post($short_desc); ?>
					</p>
			<?php endif; ?>

			<!-- Rating and Reviews Count -->
			<div class="flex items-center gap-2 text-xs sm:text-sm">
				<a href="#section-reviews"
					class="ashrafco-jump-reviews flex items-center gap-1.5 text-slate-600 hover:text-red-600 transition-colors cursor-pointer select-none">
					<?php if ($review_count > 0 && $average_rating > 0): ?>
							<span
								class="text-slate-400 font-bold">(<?php echo esc_html(sprintf(_n('%s تقييم', '%s تقييمات', $review_count, 'ashrafco'), number_format_i18n($review_count))); ?>)</span>
							<span class="font-black text-slate-800 font-mono"><?php echo esc_html($rating_display); ?></span>
							<div class="flex items-center gap-0.5">
								<?php for ($i = 1; $i <= 5; $i++): ?>
										<svg class="w-4 h-4 <?php echo ($i <= round($average_rating)) ? 'fill-amber-400 text-amber-400' : 'fill-slate-200 text-slate-200'; ?>"
											viewBox="0 0 24 24">
											<path
												d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
										</svg>
								<?php endfor; ?>
							</div>
					<?php else: ?>
							<div class="flex items-center gap-0.5 text-slate-200">
								<?php for ($i = 1; $i <= 5; $i++): ?>
										<svg class="w-4 h-4 fill-slate-200 text-slate-200" viewBox="0 0 24 24">
											<path
												d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
										</svg>
								<?php endfor; ?>
							</div>
							<span class="text-slate-400 font-bold text-xs">لا توجد تقييمات بعد (كن أول من يقيّم)</span>
					<?php endif; ?>
				</a>
			</div>

			<!-- Price Area -->
			<div class="pt-1 pb-1">
				<div class="flex items-baseline gap-2.5 flex-wrap">
					<span class="text-3xl sm:text-4xl font-black text-red-600 font-sans">
						<?php echo wc_price($current_price); ?>
					</span>
					<?php if ($is_on_sale && $regular_price > $current_price): ?>
							<span class="line-through text-slate-400 text-base sm:text-lg font-bold font-sans">
								<?php echo wc_price($regular_price); ?>
							</span>
							<span class="bg-red-600 text-white text-[11px] font-black px-2 py-0.5 rounded-md shadow-2xs">
								خصم <?php echo esc_html($discount_percent); ?>%
							</span>
					<?php endif; ?>
				</div>
				<p class="text-[11px] sm:text-xs text-slate-400 font-medium mt-1">
					السعر شامل ضريبة القيمة المضافة
				</p>
			</div>

			<!-- Purchase Controls Row: Stock + Wishlist (Row 1), Checkout + Add to Cart (Row 2) -->
			<div class="pt-2">
				<div class="test-theme-add-to-cart-wrapper">
					<!-- Row 1: متوفر بالمخزون + المفضلة -->
					<div class="flex items-center justify-between gap-3 mb-3 w-full">
						<div class="flex items-center gap-2">
							<?php if ($in_stock): ?>
								<span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-300/80 px-3.5 py-1.5 rounded-xl">
									<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
									<?php
									$stock_qty = $product->get_stock_quantity();
									if (null !== $stock_qty && $stock_qty > 0) {
										printf(esc_html__('متوفر بالمخزون: %d قطعة', 'ashrafco'), (int) $stock_qty);
									} else {
										esc_html_e('متوفر بالمخزون', 'ashrafco');
									}
									?>
								</span>
							<?php else: ?>
								<span class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3.5 py-1.5 rounded-xl">
									<?php esc_html_e('غير متوفر حالياً', 'ashrafco'); ?>
								</span>
							<?php endif; ?>
						</div>

						<button type="button"
							class="test-theme-wishlist-toggle ashrafco-purchase-wishlist-btn w-11 h-11 rounded-2xl bg-white hover:bg-rose-50 border border-slate-200/90 text-slate-400 hover:text-red-600 flex items-center justify-center transition-all cursor-pointer flex-shrink-0 select-none shadow-xs <?php echo $is_in_wishlist ? 'is-active' : ''; ?>"
							data-product-id="<?php echo esc_attr($product_id); ?>"
							aria-label="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>"
							title="<?php esc_attr_e('إضافة للمفضلة', 'ashrafco'); ?>">
							<svg class="w-5 h-5 <?php echo $is_in_wishlist ? 'fill-red-600 stroke-red-600' : 'fill-none stroke-slate-400 hover:stroke-red-600'; ?>"
								stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
							</svg>
						</button>
					</div>

					<!-- Row 2: Native WooCommerce Add to Cart Form + Direct Checkout Button -->
					<?php
					$direct_checkout_btn_hook = function () use ($product_id) {
						?>
						<button type="button"
							id="ashrafco-direct-checkout-btn"
							data-product-id="<?php echo esc_attr($product_id); ?>"
							class="ashrafco-direct-checkout-btn flex-1 min-w-[160px] h-[3.25rem] bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-black text-sm rounded-2xl flex items-center justify-center gap-2 transition-all shadow-md cursor-pointer select-none">
							<svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="2">
								<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
							</svg>
							<span>إتمام الطلب والدفع</span>
						</button>
						<?php
					};
					add_action('woocommerce_after_add_to_cart_button', $direct_checkout_btn_hook, 15);

					woocommerce_template_single_add_to_cart();

					remove_action('woocommerce_after_add_to_cart_button', $direct_checkout_btn_hook, 15);
					?>
				</div>

				<!-- Direct WhatsApp CTA Button -->
				<a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer"
					class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-black py-3.5 px-6 rounded-2xl flex items-center justify-center gap-2.5 transition shadow-sm hover:shadow-md text-sm sm:text-base mt-3 cursor-pointer select-none">
					<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
						<path
							d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
					</svg>
					<span>اطلب الآن عبر واتساب</span>
				</a>
			</div>

			<!-- 4. Trust Highlights Card (4 in 1 row) -->
			<div
				class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-3.5 sm:p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-center mt-4">
				<!-- Return Guarantee -->
				<div class="flex flex-col items-center justify-center p-1">
					<svg class="w-5 h-5 text-slate-700 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
					</svg>
					<span class="text-[11px] font-black text-slate-800 leading-tight">ضمان استبدال واسترجاع</span>
					<span class="text-[10px] text-slate-400 font-medium mt-0.5">خلال 14 يوم</span>
				</div>
				<!-- Fast Delivery -->
				<div class="flex flex-col items-center justify-center p-1">
					<svg class="w-5 h-5 text-slate-700 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
					</svg>
					<span class="text-[11px] font-black text-slate-800 leading-tight">توصيل سريع</span>
					<span class="text-[10px] text-slate-400 font-medium mt-0.5">في جميع المحافظات</span>
				</div>
				<!-- Original Quality -->
				<div class="flex flex-col items-center justify-center p-1">
					<svg class="w-5 h-5 text-slate-700 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
					</svg>
					<span class="text-[11px] font-black text-slate-800 leading-tight">منتج أصلي</span>
					<span class="text-[10px] text-slate-400 font-medium mt-0.5">100%</span>
				</div>
				<!-- 24/7 Support -->
				<div class="flex flex-col items-center justify-center p-1">
					<svg class="w-5 h-5 text-slate-700 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3" />
					</svg>
					<span class="text-[11px] font-black text-slate-800 leading-tight">دعم فني</span>
					<span class="text-[10px] text-slate-400 font-medium mt-0.5">مساعدة 24/7</span>
				</div>
			</div>

			<!-- Stock Counter Line -->
			<?php if ($in_stock): ?>
					<div class="flex items-center gap-2 text-xs sm:text-sm font-bold text-emerald-700 pt-2">
						<svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
						</svg>
						<span>
							<?php
							$stock_qty = $product->get_stock_quantity();
							if (null !== $stock_qty && $stock_qty > 0) {
								printf(esc_html__('متوفر في المخزون: %d قطعة', 'ashrafco'), (int) $stock_qty);
							} else {
								esc_html_e('متوفر في المخزون وجاهز للشحن الفوري', 'ashrafco');
							}
							?>
						</span>
					</div>
			<?php endif; ?>

		</div>

	</div>

	<!-- 3. In-Page Section Navigation Bar (No Separate Routes) -->
	<nav id="ashrafco-product-nav" class="ashrafco-product-section-nav ashrafco-product-nav-sticky sticky top-0 z-40 rounded-b-2xl mb-8">
		<div id="ashrafco-product-nav-scroll-container"
			class="flex items-center gap-4 sm:gap-8 px-4 sm:px-6 overflow-x-auto no-scrollbar whitespace-nowrap text-xs sm:text-sm font-bold text-slate-600 scroll-smooth">

			<a href="#section-description"
				class="ashrafco-nav-link active py-3.5 border-b-2 border-red-600 text-red-600 flex items-center gap-2 transition-colors cursor-pointer focus:outline-none">
				<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
				</svg>
				<span>الوصف التفصيلي</span>
			</a>

			<a href="#section-specs"
				class="ashrafco-nav-link py-3.5 border-b-2 border-transparent hover:text-slate-900 flex items-center gap-2 transition-colors cursor-pointer focus:outline-none">
				<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
				</svg>
				<span>المواصفات الفنية</span>
			</a>

			<a href="#section-reviews"
				class="ashrafco-nav-link py-3.5 border-b-2 border-transparent hover:text-slate-900 flex items-center gap-2 transition-colors cursor-pointer focus:outline-none">
				<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
				</svg>
				<span>التقييمات (<?php echo esc_html($review_count); ?>)</span>
			</a>

			<a href="#section-similar"
				class="ashrafco-nav-link py-3.5 border-b-2 border-transparent hover:text-slate-900 flex items-center gap-2 transition-colors cursor-pointer focus:outline-none">
				<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
				</svg>
				<span>منتجات مشابهة</span>
			</a>

			<a href="#section-complementary"
				class="ashrafco-nav-link py-3.5 border-b-2 border-transparent hover:text-slate-900 flex items-center gap-2 transition-colors cursor-pointer focus:outline-none">
				<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
				</svg>
				<span>منتجات مكملة</span>
			</a>

		</div>
	</nav>

	<!-- 4. Section: Full Description («الوصف التفصيلي») -->
	<section id="section-description" class="scroll-mt-24 mb-12">
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
			<!-- Header -->
			<div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-6">
				<span
					class="w-7 h-7 rounded-lg bg-rose-50 text-rose-500 border border-rose-100/60 flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
					</svg>
				</span>
				<h2 class="text-lg sm:text-xl font-black text-slate-900">الوصف التفصيلي</h2>
			</div>

			<!-- Body: Two-column layout if secondary photo exists, matching screenshot -->
			<?php
			$secondary_img_url = (count($gallery_images_data) > 1) ? $gallery_images_data[1]['single'] : '';
			?>
			<div
				class="grid grid-cols-1 <?php echo $secondary_img_url ? 'lg:grid-cols-12' : ''; ?> gap-6 sm:gap-8 items-start">

				<?php if ($secondary_img_url): ?>
						<!-- Framed Product Feature Image on Left in RTL (Cols 4) -->
						<div class="lg:col-span-4 order-2 lg:order-1">
							<div
								class="rounded-2xl overflow-hidden border border-slate-200/60 bg-white aspect-4/3 flex items-center justify-center p-2">
								<img src="<?php echo esc_url($secondary_img_url); ?>" alt="<?php echo esc_attr($title); ?>"
									class="max-h-full max-w-full object-contain rounded-xl">
							</div>
						</div>
				<?php endif; ?>

				<!-- Description Content (Cols 8 or Full) -->
				<div class="<?php echo $secondary_img_url ? 'lg:col-span-8' : 'w-full'; ?> order-1 lg:order-2">
					<?php if (!empty($full_desc)): ?>
							<div class="text-slate-600 text-xs sm:text-sm font-medium leading-relaxed space-y-4">
								<?php echo apply_filters('the_content', $full_desc); ?>
							</div>
					<?php else: ?>
							<p class="text-slate-600 text-xs sm:text-sm font-medium leading-relaxed">
								<?php echo wp_kses_post($short_desc ?: 'معدة وأداة احترافية عالية الجودة تم تصنيعها واختبارها بدقة لضمان أعلى مستويات الأداء والتحمل في المشروعات وورش العمل.'); ?>
							</p>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</section>

	<!-- 5. Section: Specifications («المواصفات الفنية» & «معلومات إضافية») -->
	<?php
	// Retrieve product metadata
	$f_brand = get_post_meta($product_id, '_test_theme_brand', true);
	if (empty($f_brand) && $primary_cat) {
		$f_brand = $primary_cat->name;
	}
	$f_model = (function_exists('ashrafco_get_product_spec') ? ashrafco_get_product_spec($product, 'model') : '') ?: (get_post_meta($product_id, '_test_theme_model', true) ?: '—');
	$tool_type = (function_exists('ashrafco_get_product_spec') ? ashrafco_get_product_spec($product, 'tool_type') : '') ?: (get_post_meta($product_id, '_test_theme_tool_type', true) ?: ($primary_cat ? $primary_cat->name : 'أداة تركيب'));
	$material = get_post_meta($product_id, '_test_theme_material', true) ?: 'كاوتشوك عالي الجودة';
	$usage = get_post_meta($product_id, '_test_theme_usage', true) ?: 'تركيب البورسلين والسيراميك';
	$warranty = (function_exists('ashrafco_get_product_spec') ? ashrafco_get_product_spec($product, 'warranty') : '') ?: (get_post_meta($product_id, '_test_theme_warranty', true) ?: 'بدون ضمان');
	$country_origin = (function_exists('ashrafco_get_product_spec') ? ashrafco_get_product_spec($product, 'country_of_origin') : '') ?: (get_post_meta($product_id, '_test_theme_country_of_origin', true) ?: 'الصين');
	$pkg_contents = (function_exists('ashrafco_get_product_spec') ? ashrafco_get_product_spec($product, 'package-contents') : '') ?: (get_post_meta($product_id, '_test_theme_package_contents', true) ?: $title);
	$weight = get_post_meta($product_id, '_test_theme_weight', true);
	if (empty($weight) && $product->has_weight()) {
		$weight = $product->get_weight() . ' كجم';
	}
	if (empty($weight)) {
		$weight = '0.5 كجم';
	}
	$dimensions = get_post_meta($product_id, '_test_theme_dimensions', true);
	if (empty($dimensions) && $product->has_dimensions()) {
		$dimensions = $product->get_dimensions(false);
	}
	if (empty($dimensions)) {
		$dimensions = '500 × 300 مم';
	}

	// 1. Basic specifications list (Right card)
	$basic_specs = array(
		'الماركة' => $f_brand,
		'رقم الموديل' => $f_model,
		'نوع المنتج' => $tool_type,
		'الخامة' => $material,
		'الاستخدام' => $usage,
		'الوزن' => $weight,
	);

	// 2. Technical / Category specifications list (Left card)
	$tech_specs = array(
		'فئة الضمان' => $warranty,
		'بلد الصنع والمنشأ' => $country_origin,
		'الماركة' => $f_brand,
		'وزن تقريبي' => $weight,
		'محتويات العبوة' => $pkg_contents,
		'الأبعاد' => $dimensions,
	);

	// Read dynamic category specifications from '_ashrafco_category_spec_values'
	$raw_cat_specs = get_post_meta($product_id, '_ashrafco_category_spec_values', true);
	if (is_array($raw_cat_specs) && !empty($raw_cat_specs)) {
		foreach ($raw_cat_specs as $spec_item) {
			if (is_array($spec_item) && !empty($spec_item['name']) && isset($spec_item['value']) && '' !== $spec_item['value']) {
				$val_text = $spec_item['value'];
				if (!empty($spec_item['unit']) && mb_stripos($val_text, $spec_item['unit']) === false) {
					$val_text .= ' ' . $spec_item['unit'];
				}
				$tech_specs[$spec_item['name']] = $val_text;
			}
		}
	}

	// Read WooCommerce visible attributes
	$wc_attrs = $product->get_attributes();
	if (!empty($wc_attrs)) {
		foreach ($wc_attrs as $attr) {
			if ($attr->get_visible()) {
				$attr_lbl = wc_attribute_label($attr->get_name());
				$attr_val = $product->get_attribute($attr->get_name());
				if (!empty($attr_val) && !isset($tech_specs[$attr_lbl])) {
					$tech_specs[$attr_lbl] = $attr_val;
				}
			}
		}
	}
	?>

	<section id="section-specs" class="scroll-mt-24 mb-12">
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

			<!-- Card 1 (Right in RTL): معلومات إضافية -->
			<div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
				<div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-4">
					<span
						class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center flex-shrink-0">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</span>
					<h3 class="text-base sm:text-lg font-black text-slate-900">معلومات إضافية</h3>
				</div>
				<div class="divide-y divide-slate-100 text-xs sm:text-sm">
					<?php foreach ($basic_specs as $lbl => $val): ?>
							<div class="flex items-center justify-between py-3">
								<span class="text-slate-500 font-bold"><?php echo esc_html($lbl); ?></span>
								<span class="text-slate-800 font-extrabold text-left"><?php echo esc_html($val); ?></span>
							</div>
					<?php endforeach; ?>
				</div>
			</div>

			<!-- Card 2 (Left in RTL): المواصفات الفنية -->
			<div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8">
				<div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-4">
					<span
						class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center flex-shrink-0">
						<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
					</span>
					<h3 class="text-base sm:text-lg font-black text-slate-900">المواصفات الفنية</h3>
				</div>
				<div id="ashrafco-tech-specs-list" class="divide-y divide-slate-100 text-xs sm:text-sm">
					<?php
					$count = 0;
					foreach ($tech_specs as $lbl => $val):
						$count++;
						$hidden_class = ($count > 6) ? 'ashrafco-extra-spec hidden' : '';
						?>
							<div class="flex items-center justify-between py-3 <?php echo esc_attr($hidden_class); ?>">
								<span class="text-slate-500 font-bold"><?php echo esc_html($lbl); ?></span>
								<span class="text-slate-800 font-extrabold text-left"><?php echo esc_html($val); ?></span>
							</div>
					<?php endforeach; ?>
				</div>

				<?php if (count($tech_specs) > 6): ?>
						<button type="button" id="ashrafco-toggle-all-specs-btn"
							class="w-full mt-4 pt-3 border-t border-slate-100 text-center text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center justify-center gap-1.5 transition-colors cursor-pointer select-none">
							<span id="ashrafco-specs-btn-text">عرض جميع المواصفات</span>
							<svg id="ashrafco-specs-btn-icon" class="w-3.5 h-3.5 transition-transform" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
							</svg>
						</button>
				<?php endif; ?>
			</div>

		</div>
	</section>

	<!-- 6. Section: Reviews («آراء وتجارب المقاولين والحرفيين») -->
	<?php
	$reviews = get_comments(array(
		'post_id' => $product_id,
		'status' => 'approve',
		'type' => 'review',
	));

	// Calculate star distribution
	$star_counts = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
	foreach ($reviews as $rev) {
		$r = intval(get_comment_meta($rev->comment_ID, 'rating', true)) ?: 5;
		if (isset($star_counts[$r])) {
			$star_counts[$r]++;
		}
	}
	$total_reviews_count = count($reviews);
	?>

	<section id="section-reviews" class="scroll-mt-24 mb-12">
		<!-- Section Title Header -->
		<div class="flex items-center justify-between mb-6">
			<div class="flex items-center gap-2">
				<span class="text-amber-400 text-xl font-bold">★</span>
				<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">آراء وتجارب المقاولين والحرفيين
				</h2>
			</div>
			<button type="button" id="ashrafco-open-review-form-btn"
				class="text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 flex items-center gap-1.5 transition-colors cursor-pointer select-none">
				<span>أضف تقييمك وتجربتك</span>
				<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
						d="M14 5l7 7m0 0l-7 7m7-7H3" />
				</svg>
			</button>
		</div>

		<!-- Ratings Overview & Reviews Showcase Box -->
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 mb-6">
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

				<!-- Rating Summary & 5 Progress Bars (Cols 5) -->
				<div
					class="lg:col-span-5 flex items-center gap-5 sm:gap-7 border-b lg:border-b-0 lg:border-l border-slate-100 pb-6 lg:pb-0 pl-0 lg:pl-8">
					<!-- Score Box -->
					<div class="text-center min-w-[90px] sm:min-w-[110px] flex-shrink-0">
						<div class="text-4xl sm:text-5xl font-black text-slate-900 font-sans leading-none mb-2">
							<?php echo esc_html($total_reviews_count > 0 ? number_format($average_rating, 1) : '0.0'); ?>
						</div>
						<div class="flex items-center justify-center gap-0.5 mb-1">
							<?php for ($i = 1; $i <= 5; $i++): ?>
									<svg class="w-4 h-4 <?php echo ($total_reviews_count > 0 && $i <= round($average_rating)) ? 'fill-amber-400 text-amber-400' : 'fill-slate-200 text-slate-200'; ?>"
										viewBox="0 0 24 24">
										<path
											d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
									</svg>
							<?php endfor; ?>
						</div>
						<span class="text-xs text-slate-400 font-bold block">
							<?php echo esc_html(sprintf(_n('من %s تقييم', 'من %s تقييمات', $total_reviews_count, 'ashrafco'), number_format_i18n($total_reviews_count))); ?>
						</span>
					</div>

					<!-- 5 Star Bars -->
					<div class="flex-1 space-y-1.5 text-xs text-slate-500 font-bold">
						<?php
						foreach (array(5, 4, 3, 2, 1) as $star_num):
							$pct = $total_reviews_count > 0 ? round(($star_counts[$star_num] / $total_reviews_count) * 100) : 0;
							?>
								<div class="flex items-center gap-2">
									<span class="w-4 text-left font-mono"><?php echo $star_num; ?>★</span>
									<div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
										<div class="h-full bg-amber-400 rounded-full"
											style="width: <?php echo esc_attr($pct); ?>%;"></div>
									</div>
									<span
										class="w-8 text-right font-mono text-[11px] text-slate-400"><?php echo esc_html($pct); ?>%</span>
								</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Customer Review Cards Row (Cols 7) -->
				<div class="lg:col-span-7">
					<?php if (!empty($reviews)): ?>
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
								<?php
								$shown_reviews = array_slice($reviews, 0, 2);
								foreach ($shown_reviews as $rev):
									$r_val = intval(get_comment_meta($rev->comment_ID, 'rating', true));
									$is_verified = wc_review_is_from_verified_owner($rev->comment_ID);
									?>
										<div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4 space-y-2">
											<div class="flex items-center justify-between">
												<div class="flex items-center gap-2">
													<div
														class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">
														<?php echo esc_html(mb_substr($rev->comment_author, 0, 1)); ?>
													</div>
													<div>
														<h4 class="text-xs font-black text-slate-900 m-0">
															<?php echo esc_html($rev->comment_author); ?></h4>
														<span
															class="text-[10px] text-slate-400 font-mono"><?php echo esc_html(get_comment_date('j F Y', $rev)); ?></span>
													</div>
												</div>
												<?php if ($is_verified): ?>
														<span
															class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200/60">
															✓ مشتري معتمد
														</span>
												<?php endif; ?>
											</div>
											<div class="flex items-center gap-0.5 text-xs">
												<?php for ($s = 1; $s <= 5; $s++): ?>
														<span
															class="<?php echo ($s <= $r_val) ? 'text-amber-400' : 'text-slate-200'; ?>">★</span>
												<?php endfor; ?>
											</div>
											<p class="text-xs text-slate-600 leading-relaxed m-0">
												<?php echo esc_html(wp_trim_words($rev->comment_content, 22)); ?>
											</p>
										</div>
								<?php endforeach; ?>
							</div>
					<?php else: ?>
							<!-- Empty State for Reviews -->
							<div
								class="flex flex-col items-center justify-center text-center p-6 bg-slate-50/70 border border-slate-200/80 rounded-2xl space-y-3">
								<div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center">
									<svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
										<path
											d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
									</svg>
								</div>
								<div class="space-y-1">
									<h4 class="text-sm font-bold text-slate-800 m-0">لا توجد تقييمات لهذا المنتج حتى الآن</h4>
									<p class="text-xs text-slate-500 max-w-sm m-0">كن أول من يشارك تجربته مع هذا المنتج لمساعدة
										زملائك من الحرفيين والمقاولين.</p>
								</div>
								<?php if (comments_open($product_id)): ?>
										<button type="button"
											onclick="document.getElementById('ashrafco-open-review-form-btn')?.click();"
											class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 transition-colors shadow-xs cursor-pointer">
											<span>أضف أول تقييم</span>
											<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
													d="M14 5l7 7m0 0l-7 7m7-7H3" />
											</svg>
										</button>
								<?php endif; ?>
							</div>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<!-- Review Submission Form (Expandable) -->
		<?php if (comments_open($product_id)): ?>
				<div id="ashrafco-review-form-box"
					class="hidden bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-2xs mb-8 transition-all">
					<h3 class="text-base sm:text-lg font-black text-slate-900 mb-1">شاركنا تقييمك وتجربتك للمنتج</h3>
					<p class="text-xs text-slate-500 mb-5">رأيك يساعد الحرفيين والعملاء في اختيار المعدات المناسبة لأعمالهم.</p>

					<form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post"
						class="space-y-4">
						<div>
							<label class="block text-xs font-bold text-slate-700 mb-2">تقييمك بالنجوم:</label>
							<div class="flex items-center gap-3 flex-wrap">
								<?php for ($r = 5; $r >= 1; $r--): ?>
										<label
											class="inline-flex items-center gap-1.5 cursor-pointer bg-slate-50 hover:bg-amber-50 px-3 py-1.5 rounded-xl border border-slate-200 transition-colors">
											<input type="radio" name="rating" value="<?php echo esc_attr($r); ?>" <?php checked($r, 5); ?> class="text-red-600 focus:ring-red-500 accent-red-600">
											<span class="text-amber-400 font-bold"><?php echo str_repeat('★', $r); ?></span>
											<span class="text-[11px] text-slate-500 font-bold">(<?php echo esc_html($r); ?>)</span>
										</label>
								<?php endfor; ?>
							</div>
						</div>

						<div>
							<label for="comment" class="block text-xs font-bold text-slate-700 mb-1.5">نص التقييم
								والملاحظات:</label>
							<textarea id="comment" name="comment" rows="3" required
								placeholder="اكتب تفاصيل تجربتك، متانة المنتج، وسرعة الاستلام..."
								class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-red-600 focus:bg-white transition leading-relaxed"></textarea>
						</div>

						<?php if (!is_user_logged_in()): ?>
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
									<div>
										<label for="author" class="block text-xs font-bold text-slate-700 mb-1.5">الاسم الكريم:</label>
										<input type="text" id="author" name="author" required placeholder="مثال: م. أحمد عبد الفتاح"
											class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-red-600 focus:bg-white transition">
									</div>
									<div>
										<label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">البريد
											الإلكتروني:</label>
										<input type="email" id="email" name="email" required placeholder="name@example.com"
											class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:border-red-600 focus:bg-white transition">
									</div>
								</div>
						<?php endif; ?>

						<input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($product_id); ?>">
						<input type="hidden" name="comment_type" value="review">
						<?php wp_nonce_field('comment_post_' . $product_id, '_wpnonce'); ?>

						<button type="submit"
							class="bg-red-600 hover:bg-red-700 text-white font-black py-3 px-6 rounded-xl text-xs sm:text-sm transition-all shadow-sm cursor-pointer">
							إرسال التقييم
						</button>
					</form>
				</div>
		<?php endif; ?>
	</section>

	<!-- 7. Section: Similar Products («منتجات مشابهة» / «منتجات ذات صلة قد تحتاجها لعملك») -->
	<?php
	$similar_ids = wc_get_related_products($product_id, 4);
	if (empty($similar_ids) && $primary_cat) {
		$similar_query = new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 4,
			'post__not_in' => array($product_id),
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'term_id',
					'terms' => $primary_cat->term_id,
				),
			),
		));
	} elseif (!empty($similar_ids)) {
		$similar_query = new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 4,
			'post__in' => $similar_ids,
			'post_status' => 'publish',
		));
	} else {
		$similar_query = new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 4,
			'post__not_in' => array($product_id),
			'post_status' => 'publish',
		));
	}
	?>

	<?php if ($similar_query->have_posts()): ?>
			<section id="section-similar" class="scroll-mt-24 mb-12">
				<!-- Section Header -->
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center gap-2.5">
						<span
							class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
							<svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
							</svg>
						</span>
						<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">منتجات ذات صلة قد تحتاجها لعملك
						</h2>
					</div>
					<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
						class="text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 flex items-center gap-1.5 transition-colors select-none">
						<span>تصفح كل المنتجات المشابهة</span>
						<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
								d="M14 5l7 7m0 0l-7 7m7-7H3" />
						</svg>
					</a>
				</div>

				<!-- 4-Column Product Cards Grid -->
				<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
					<?php
					while ($similar_query->have_posts()):
						$similar_query->the_post();
						get_template_part('template-parts/woocommerce/content-product-card');
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
	<?php endif; ?>

	<!-- 8. Section: Complementary Products («منتجات مكملة قد تحتاجها») -->
	<?php
	$upsell_ids = $product->get_upsell_ids();
	$crosssell_ids = $product->get_cross_sell_ids();
	$comp_ids = array_unique(array_merge((array) $upsell_ids, (array) $crosssell_ids));
	$comp_ids = array_diff($comp_ids, (array) $similar_ids, array($product_id));

	if (empty($comp_ids)) {
		// Fallback to complementary accessories from store
		$comp_query = new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 4,
			'post__not_in' => array_merge(array($product_id), (array) $similar_ids),
			'post_status' => 'publish',
		));
	} else {
		$comp_query = new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 4,
			'post__in' => $comp_ids,
			'post_status' => 'publish',
		));
	}
	?>

	<?php if ($comp_query->have_posts()): ?>
			<section id="section-complementary" class="scroll-mt-24 mb-12">
				<!-- Section Header -->
				<div class="flex items-center justify-between mb-6">
					<div class="flex items-center gap-2.5">
						<span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0">
							<svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
							</svg>
						</span>
						<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">منتجات مكملة قد تحتاجها</h2>
					</div>
				</div>

				<!-- 4-Column Product Cards Grid -->
				<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
					<?php
					while ($comp_query->have_posts()):
						$comp_query->the_post();
						get_template_part('template-parts/woocommerce/content-product-card');
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
	<?php endif; ?>

</div>

<style>
/* Scoped Add-to-Cart & Purchase Area Styling */
.test-theme-add-to-cart-wrapper form.cart {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	flex-wrap: wrap;
	margin: 0;
	padding: 0;
	width: 100%;
}

/* Hide any duplicate native WooCommerce stock notifications */
.test-theme-add-to-cart-wrapper .stock,
.test-theme-add-to-cart-wrapper p.stock,
.test-theme-add-to-cart-wrapper form.cart .stock,
.test-theme-add-to-cart-wrapper form.cart p.stock,
.test-theme-add-to-cart-wrapper .woocommerce-variation-availability {
	display: none;
}

.test-theme-add-to-cart-wrapper form.cart .quantity {
	display: inline-flex;
	align-items: center;
	background: #f8fafc;
	border: 1.5px solid #e2e8f0;
	border-radius: 1rem;
	padding: 0.25rem;
	gap: 0.35rem;
	margin: 0;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
	transition: all 0.2s ease;
	flex-shrink: 0;
}

.test-theme-add-to-cart-wrapper form.cart .quantity:hover {
	border-color: #cbd5e1;
}

.test-theme-add-to-cart-wrapper form.cart .quantity input.qty {
	width: 2.75rem;
	height: 2.5rem;
	text-align: center;
	font-weight: 800;
	font-size: 0.95rem;
	color: #0f172a;
	background: transparent;
	border: none;
	outline: none;
	padding: 0;
	margin: 0;
	-moz-appearance: textfield;
}

.test-theme-add-to-cart-wrapper form.cart .quantity input.qty::-webkit-outer-spin-button,
.test-theme-add-to-cart-wrapper form.cart .quantity input.qty::-webkit-inner-spin-button {
	-webkit-appearance: none;
	margin: 0;
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button {
	flex: 1 1 auto;
	min-width: 150px;
	height: 3.25rem;
	background: linear-gradient(135deg, #e00325 0%, #ba021d 100%);
	color: #ffffff;
	font-weight: 900;
	font-size: 0.95rem;
	letter-spacing: -0.01em;
	border-radius: 1rem;
	border: none;
	padding: 0 1.25rem;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.65rem;
	cursor: pointer;
	box-shadow: 0 4px 14px -2px rgba(224, 3, 37, 0.38);
	transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
	user-select: none;
	text-decoration: none;
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button:hover {
	background: linear-gradient(135deg, #f0052a 0%, #c90220 100%);
	box-shadow: 0 6px 20px -2px rgba(224, 3, 37, 0.48);
	transform: translateY(-1px);
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button:active {
	transform: translateY(1px) scale(0.99);
	box-shadow: 0 2px 8px -1px rgba(224, 3, 37, 0.3);
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button .ashrafco-cart-icon {
	width: 1.25rem;
	height: 1.25rem;
	stroke: currentColor;
	stroke-width: 2.2;
	transition: transform 0.2s ease;
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button.ashrafco-btn-loading {
	position: relative;
	color: transparent;
	pointer-events: none;
	opacity: 0.85;
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button.ashrafco-btn-loading .ashrafco-cart-icon {
	visibility: hidden;
}

.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button.ashrafco-btn-loading::after {
	content: "";
	position: absolute;
	width: 1.35rem;
	height: 1.35rem;
	top: 50%;
	left: 50%;
	margin: -0.675rem 0 0 -0.675rem;
	border: 2.5px solid rgba(255, 255, 255, 0.35);
	border-top-color: #ffffff;
	border-radius: 50%;
	animation: ashrafco-btn-spin 0.65s linear infinite;
}

/* Direct Checkout Instant Buy Button */
.ashrafco-direct-checkout-btn {
	flex: 1 1 180px;
	min-width: 170px;
	height: 3.25rem;
	background: #0f172a;
	color: #ffffff;
	font-weight: 900;
	font-size: 0.95rem;
	border: none;
	border-radius: 1rem;
	padding: 0 1.25rem;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
	box-shadow: 0 4px 14px -2px rgba(15, 23, 42, 0.35);
	cursor: pointer;
	user-select: none;
	text-decoration: none;
	white-space: nowrap;
}

.ashrafco-direct-checkout-btn:hover {
	background: #1e293b;
	box-shadow: 0 6px 20px -2px rgba(15, 23, 42, 0.48);
	transform: translateY(-1px);
}

.ashrafco-direct-checkout-btn:active {
	transform: translateY(1px) scale(0.99);
}

.ashrafco-direct-checkout-btn svg {
	width: 1.25rem;
	height: 1.25rem;
	stroke: currentColor;
	stroke-width: 2.2;
	flex-shrink: 0;
}

.ashrafco-direct-checkout-btn.ashrafco-btn-loading {
	position: relative;
	color: transparent;
	pointer-events: none;
	opacity: 0.85;
}

.ashrafco-direct-checkout-btn.ashrafco-btn-loading svg {
	visibility: hidden;
}

.ashrafco-direct-checkout-btn.ashrafco-btn-loading::after {
	content: "";
	position: absolute;
	width: 1.35rem;
	height: 1.35rem;
	top: 50%;
	left: 50%;
	margin: -0.675rem 0 0 -0.675rem;
	border: 2.5px solid rgba(255, 255, 255, 0.35);
	border-top-color: #ffffff;
	border-radius: 50%;
	animation: ashrafco-btn-spin 0.65s linear infinite;
}

.ashrafco-purchase-wishlist-btn {
	height: 2.75rem;
	width: 2.75rem;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
</style>

<!-- 9. Interactive Gallery Lightbox Modal -->
<div id="ashrafco-lightbox-modal"
	class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden items-center justify-center p-4 select-none">
	<button type="button" id="ashrafco-lightbox-close"
		class="absolute top-5 right-5 z-50 text-white/80 hover:text-white p-2.5 rounded-full hover:bg-white/10 text-2xl font-bold cursor-pointer transition-colors"
		aria-label="إغلاق المعاينة">✕</button>

	<?php if (count($gallery_images_data) > 1): ?>
			<!-- Next Arrow in RTL (Left side: ❮ moves forward) -->
			<button type="button" id="ashrafco-lightbox-next"
				class="absolute left-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-2xl cursor-pointer transition-colors"
				aria-label="التالي">❮</button>
			<!-- Prev Arrow in RTL (Right side: ❯ goes back) -->
			<button type="button" id="ashrafco-lightbox-prev"
				class="absolute right-4 top-1/2 -translate-y-1/2 z-50 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-2xl cursor-pointer transition-colors"
				aria-label="السابق">❯</button>
	<?php endif; ?>

	<div class="max-w-4xl max-h-[85vh] flex items-center justify-center">
		<img id="ashrafco-lightbox-img" src="" alt="<?php echo esc_attr($title); ?>"
			class="max-h-[85vh] max-w-full object-contain rounded-2xl shadow-2xl transition-all duration-200">
	</div>
</div>

<!-- 10. Front-End Interactions Script -->
<script>
	document.addEventListener('DOMContentLoaded', function () {
		'use strict';

		// =========================================================================
		// A. GALLERY THUMBNAILS, SLIDER & LIGHTBOX (RTL SYNCHRONIZED)
		// =========================================================================
		var galleryImages = <?php echo wp_json_encode($gallery_images_data); ?>;
		var currentIndex = 0;
		var isAnimating = false;

		var mainImg = document.getElementById('ashrafco-gallery-main-img');
		var galleryContainer = document.getElementById('ashrafco-gallery-container');
		var thumbsContainer = document.getElementById('ashrafco-gallery-thumbs');
		var thumbs = document.querySelectorAll('.ashrafco-gallery-thumb');
		var prevBtn = document.getElementById('ashrafco-gallery-prev');
		var nextBtn = document.getElementById('ashrafco-gallery-next');
		var zoomBtn = document.getElementById('ashrafco-gallery-zoom-btn');
		var lightbox = document.getElementById('ashrafco-lightbox-modal');
		var lightboxImg = document.getElementById('ashrafco-lightbox-img');
		var lightboxClose = document.getElementById('ashrafco-lightbox-close');
		var lightboxPrev = document.getElementById('ashrafco-lightbox-prev');
		var lightboxNext = document.getElementById('ashrafco-lightbox-next');

		/**
		 * Update Gallery Image with smooth RTL slide transition and thumbnail auto-scrolling
		 *
		 * @param {number} index Target image index
		 * @param {string} direction 'next' (moves right to left in RTL) or 'prev' (moves left to right)
		 */
		function updateGallery(index, direction) {
			if (!galleryImages || !galleryImages.length) return;
			if (isAnimating) return;

			if (index < 0) index = galleryImages.length - 1;
			if (index >= galleryImages.length) index = 0;

			if (!direction) {
				direction = (index >= currentIndex) ? 'next' : 'prev';
			}

			if (index === currentIndex && !direction) return;

			currentIndex = index;
			var item = galleryImages[currentIndex];

			// 1. Smooth RTL Slide Transition for Main Image
			if (mainImg) {
				isAnimating = true;

				// In RTL:
				// 'next' (التقليب للأمام من اليمين للشمال):
				// الصورة الحالية تخرج لليسار (-30%)، والصورة الجديدة تدخل من اليمين (+30% -> 0)
				// 'prev' (الرجوع للخلف من الشمال لليمين):
				// الصورة الحالية تخرج لليمين (+30%)، والصورة الجديدة تدخل من اليسار (-30% -> 0)
				var exitX = (direction === 'next') ? '-30%' : '30%';
				var enterX = (direction === 'next') ? '30%' : '-30%';

				mainImg.style.transition = 'transform 0.18s cubic-bezier(0.4, 0, 1, 1), opacity 0.18s ease-in';
				mainImg.style.transform = 'translateX(' + exitX + ') scale(0.96)';
				mainImg.style.opacity = '0';

				setTimeout(function () {
					mainImg.src = item.single;
					mainImg.setAttribute('data-full-src', item.full);
					mainImg.style.transition = 'none';
					mainImg.style.transform = 'translateX(' + enterX + ') scale(0.96)';

					// Force DOM reflow
					void mainImg.offsetWidth;

					mainImg.style.transition = 'transform 0.24s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease-out';
					mainImg.style.transform = 'translateX(0) scale(1)';
					mainImg.style.opacity = '1';

					setTimeout(function () {
						isAnimating = false;
					}, 240);
				}, 180);
			}

			// 2. Highlight Active Thumbnail
			thumbs.forEach(function (t, i) {
				if (i === currentIndex) {
					t.classList.add('border-red-600', 'shadow-sm', 'scale-105');
					t.classList.remove('border-slate-200/80', 'opacity-70');
				} else {
					t.classList.remove('border-red-600', 'shadow-sm', 'scale-105');
					t.classList.add('border-slate-200/80', 'opacity-70');
				}
			});

			// 3. Auto-scroll Thumbnails Strip so active thumbnail moves smoothly into view/center
			var activeThumb = thumbs[currentIndex];
			if (thumbsContainer && activeThumb) {
				try {
					activeThumb.scrollIntoView({
						behavior: 'smooth',
						inline: 'center',
						block: 'nearest'
					});
				} catch (e) {}

				// Secondary scroll centering fallback for cross-browser reliability
				var cRect = thumbsContainer.getBoundingClientRect();
				var tRect = activeThumb.getBoundingClientRect();
				var diff = (tRect.left + tRect.width / 2) - (cRect.left + cRect.width / 2);
				if (Math.abs(diff) > 4) {
					thumbsContainer.scrollBy({ left: diff, behavior: 'smooth' });
				}
			}

			// 4. Sync Lightbox Image if open
			if (lightboxImg && !lightbox.classList.contains('hidden')) {
				lightboxImg.src = item.full;
			}
		}

		// Thumbnail Click Listeners
		thumbs.forEach(function (t) {
			t.addEventListener('click', function () {
				var idx = parseInt(this.getAttribute('data-index'), 10);
				if (idx !== currentIndex) {
					var dir = (idx > currentIndex) ? 'next' : 'prev';
					updateGallery(idx, dir);
				}
			});
		});

		// Arrows Click Listeners (In RTL: Left arrow is Next, Right arrow is Prev)
		if (nextBtn) {
			nextBtn.addEventListener('click', function (e) {
				e.preventDefault();
				updateGallery(currentIndex + 1, 'next');
			});
		}
		if (prevBtn) {
			prevBtn.addEventListener('click', function (e) {
				e.preventDefault();
				updateGallery(currentIndex - 1, 'prev');
			});
		}

		// Touch Swipe Support on Main Gallery Container
		if (galleryContainer) {
			var touchStartX = 0;
			var touchStartY = 0;

			galleryContainer.addEventListener('touchstart', function (e) {
				if (e.touches && e.touches.length === 1) {
					touchStartX = e.touches[0].clientX;
					touchStartY = e.touches[0].clientY;
				}
			}, { passive: true });

			galleryContainer.addEventListener('touchend', function (e) {
				if (e.changedTouches && e.changedTouches.length === 1) {
					var touchEndX = e.changedTouches[0].clientX;
					var touchEndY = e.changedTouches[0].clientY;
					var diffX = touchStartX - touchEndX;
					var diffY = touchStartY - touchEndY;

					// Only trigger on horizontal swipe (ignore vertical scrolling)
					if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
						// In RTL:
						// Swiping Left (diffX > 0) -> Next image (move right to left)
						// Swiping Right (diffX < 0) -> Prev image (move left to right)
						if (diffX > 0) {
							updateGallery(currentIndex + 1, 'next');
						} else {
							updateGallery(currentIndex - 1, 'prev');
						}
					}
				}
			}, { passive: true });
		}

		// Lightbox Interactions
		function openLightbox() {
			if (!lightbox || !galleryImages.length) return;
			lightbox.classList.remove('hidden');
			lightbox.classList.add('flex');
			document.body.style.overflow = 'hidden';
			if (lightboxImg) {
				lightboxImg.src = galleryImages[currentIndex].full;
			}
		}

		function closeLightbox() {
			if (!lightbox) return;
			lightbox.classList.add('hidden');
			lightbox.classList.remove('flex');
			document.body.style.overflow = '';
		}

		if (zoomBtn) zoomBtn.addEventListener('click', openLightbox);
		if (mainImg) mainImg.addEventListener('click', openLightbox);
		if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
		if (lightboxNext) lightboxNext.addEventListener('click', function () { updateGallery(currentIndex + 1, 'next'); });
		if (lightboxPrev) lightboxPrev.addEventListener('click', function () { updateGallery(currentIndex - 1, 'prev'); });

		if (lightbox) {
			lightbox.addEventListener('click', function (e) {
				if (e.target === lightbox) closeLightbox();
			});
		}

		document.addEventListener('keydown', function (e) {
			if (lightbox && !lightbox.classList.contains('hidden')) {
				if (e.key === 'Escape') {
					closeLightbox();
				} else if (e.key === 'ArrowLeft') {
					updateGallery(currentIndex + 1, 'next');
				} else if (e.key === 'ArrowRight') {
					updateGallery(currentIndex - 1, 'prev');
				}
			}
		});

		// =========================================================================
		// B. QUANTITY STEPPER (MINUS / PLUS) IN FORM.CART
		// =========================================================================
		var qtyContainer = document.querySelector('.test-theme-add-to-cart-wrapper form.cart .quantity');
		if (qtyContainer && !qtyContainer.querySelector('.ashrafco-qty-btn-minus')) {
			var qtyInput = qtyContainer.querySelector('input.qty');
			if (qtyInput) {
				qtyContainer.classList.add('inline-flex', 'items-center', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-2xl', 'p-1', 'gap-1');

				var minusBtn = document.createElement('button');
				minusBtn.type = 'button';
				minusBtn.className = 'ashrafco-qty-btn-minus w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-black text-sm flex items-center justify-center transition-colors cursor-pointer select-none';
				minusBtn.textContent = '-';

				var plusBtn = document.createElement('button');
				plusBtn.type = 'button';
				plusBtn.className = 'ashrafco-qty-btn-plus w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-black text-sm flex items-center justify-center transition-colors cursor-pointer select-none';
				plusBtn.textContent = '+';

				qtyInput.classList.add('w-10', 'text-center', 'font-black', 'text-sm', 'bg-transparent', 'border-0', 'focus:outline-none');

				qtyContainer.insertBefore(minusBtn, qtyInput);
				qtyContainer.appendChild(plusBtn);

				minusBtn.addEventListener('click', function () {
					var val = parseInt(qtyInput.value, 10) || 1;
					var min = parseInt(qtyInput.getAttribute('min'), 10) || 1;
					if (val > min) {
						qtyInput.value = val - 1;
						qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
					}
				});

				plusBtn.addEventListener('click', function () {
					var val = parseInt(qtyInput.value, 10) || 1;
					var max = parseInt(qtyInput.getAttribute('max'), 10) || 9999;
					if (val < max) {
						qtyInput.value = val + 1;
						qtyInput.dispatchEvent(new Event('change', { bubbles: true }));
					}
				});
			}
		}

		// Ensure Shopping Cart Icon and Arabic Label on Native Add-to-Cart Button
		var submitBtn = document.querySelector('.test-theme-add-to-cart-wrapper form.cart .single_add_to_cart_button');
		if (submitBtn) {
			var currentText = submitBtn.textContent.trim();
			if (!currentText || currentText === 'Add to cart' || currentText === 'Add to Cart') {
				submitBtn.textContent = 'إضافة إلى السلة';
			}
			if (!submitBtn.querySelector('.ashrafco-cart-icon')) {
				var cartSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
				cartSvg.setAttribute('class', 'ashrafco-cart-icon w-5 h-5 fill-none stroke-current flex-shrink-0');
				cartSvg.setAttribute('viewBox', '0 0 24 24');
				cartSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>';
				submitBtn.appendChild(cartSvg);
			}
		}

		// Direct Checkout Instant Buy Action (Isolated Buy Now Flow)
		var directCheckoutBtn = document.getElementById('ashrafco-direct-checkout-btn');
		if (directCheckoutBtn) {
			directCheckoutBtn.addEventListener('click', function (e) {
				e.preventDefault();
				var cartForm = directCheckoutBtn.closest('form.cart') || document.querySelector('.test-theme-add-to-cart-wrapper form.cart') || document.querySelector('form.cart');
				var pid = this.getAttribute('data-product-id');
				if (!pid) return;

				// Check variable product variation validation
				if (cartForm && cartForm.classList.contains('variations_form')) {
					var variationIdInput = cartForm.querySelector('input[name="variation_id"]');
					var varId = variationIdInput ? (parseInt(variationIdInput.value, 10) || 0) : 0;
					if (!varId || varId <= 0) {
						var existingNotice = document.getElementById('ashrafco-buy-now-inline-notice');
						if (!existingNotice) {
							existingNotice = document.createElement('div');
							existingNotice.id = 'ashrafco-buy-now-inline-notice';
							existingNotice.className = 'w-full mt-2 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-2.5 text-center transition-all';
							cartForm.appendChild(existingNotice);
						}
						existingNotice.textContent = 'يرجى اختيار مواصفات وخيارات المنتج المطلوبة أولاً قبل إتمام الطلب الفوري.';
						existingNotice.style.display = 'block';
						existingNotice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
						return;
					}
				}

				var qtyInput = cartForm ? cartForm.querySelector('input.qty') : null;
				var qty = qtyInput ? (parseInt(qtyInput.value, 10) || 1) : 1;

				directCheckoutBtn.disabled = true;
				directCheckoutBtn.classList.add('ashrafco-btn-loading');

				// Clear any previous inline notice
				var prevNotice = document.getElementById('ashrafco-buy-now-inline-notice');
				if (prevNotice) {
					prevNotice.style.display = 'none';
				}

				var formData = cartForm ? new FormData(cartForm) : new FormData();
				formData.append('action', 'ashrafco_init_buy_now');
				formData.append('security', '<?php echo esc_js(wp_create_nonce("ashrafco_buy_now_nonce")); ?>');
				if (!formData.get('product_id')) {
					formData.append('product_id', pid);
				}
				if (!formData.get('quantity')) {
					formData.append('quantity', qty);
				}

				var ajaxUrl = (window.ashrafcoCartData && window.ashrafcoCartData.ajaxUrl) ? window.ashrafcoCartData.ajaxUrl : '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';

				fetch(ajaxUrl, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin'
				})
				.then(function (res) {
					return res.json();
				})
				.then(function (res) {
					if (res.success && res.data && res.data.redirect_url) {
						window.location.href = res.data.redirect_url;
					} else {
						directCheckoutBtn.disabled = false;
						directCheckoutBtn.classList.remove('ashrafco-btn-loading');
						var errMsg = (res.data && res.data.message) ? res.data.message : 'تعذر إتمام الطلب الفوري حالياً. يرجى المحاولة مرة أخرى.';
						alert(errMsg);
					}
				})
				.catch(function () {
					// Fallback to safe direct redirect if network fails
					var targetUrl = '<?php echo esc_url(wc_get_checkout_url()); ?>';
					var sep = targetUrl.indexOf('?') !== -1 ? '&' : '?';
					window.location.href = targetUrl + sep + 'direct_checkout=' + encodeURIComponent(pid) + '&quantity=' + encodeURIComponent(qty);
				});
			});
		}

		// Translate or suppress any default WooCommerce in stock text rendered inside form
		var wcStockEls = document.querySelectorAll('.test-theme-add-to-cart-wrapper .stock, .test-theme-add-to-cart-wrapper p.stock, .test-theme-add-to-cart-wrapper form.cart .stock, .test-theme-add-to-cart-wrapper form.cart p.stock, .test-theme-add-to-cart-wrapper .woocommerce-variation-availability');
		wcStockEls.forEach(function (el) {
			el.remove();
		});

		// =========================================================================
		// C. IN-PAGE SECTION NAVIGATION SMOOTH SCROLL & SCROLLSPY
		// =========================================================================
		var navLinks = document.querySelectorAll('#ashrafco-product-nav a');
		var sections = [];

		navLinks.forEach(function (link) {
			var href = link.getAttribute('href');
			if (href && href.startsWith('#')) {
				var sec = document.querySelector(href);
				if (sec) sections.push({ link: link, el: sec });
			}

			link.addEventListener('click', function (e) {
				e.preventDefault();
				var targetSec = document.querySelector(this.getAttribute('href'));
				if (targetSec) {
					var headerOffset = 85;
					var elementPosition = targetSec.getBoundingClientRect().top;
					var offsetPosition = elementPosition + window.pageYOffset - headerOffset;
					window.scrollTo({
						top: offsetPosition,
						behavior: 'smooth'
					});
				}
			});
		});

		// Review jump link in rating bar
		var jumpReview = document.querySelector('.ashrafco-jump-reviews');
		if (jumpReview) {
			jumpReview.addEventListener('click', function (e) {
				e.preventDefault();
				var revSec = document.getElementById('section-reviews');
				if (revSec) {
					var offset = revSec.getBoundingClientRect().top + window.pageYOffset - 85;
					window.scrollTo({ top: offset, behavior: 'smooth' });
				}
			});
		}

		// Scrollspy with requestAnimationFrame / scroll
		var navScrollContainer = document.getElementById('ashrafco-product-nav-scroll-container');
		var lastActiveIndex = -1;

		function updateActiveNavLink() {
			var scrollPos = window.pageYOffset + 120;
			var currentActiveIndex = -1;

			sections.forEach(function (item, idx) {
				var top = item.el.offsetTop;
				var height = item.el.offsetHeight;
				if (scrollPos >= top && scrollPos < top + height) {
					currentActiveIndex = idx;
				}
			});

			if (currentActiveIndex !== -1 && currentActiveIndex !== lastActiveIndex) {
				lastActiveIndex = currentActiveIndex;
				var activeItem = sections[currentActiveIndex];

				navLinks.forEach(function (l) {
					l.classList.remove('border-red-600', 'text-red-600');
					l.classList.add('border-transparent', 'text-slate-600');
					var svg = l.querySelector('svg');
					if (svg) svg.classList.remove('text-red-600');
				});

				activeItem.link.classList.remove('border-transparent', 'text-slate-600');
				activeItem.link.classList.add('border-red-600', 'text-red-600');
				var itemSvg = activeItem.link.querySelector('svg');
				if (itemSvg) itemSvg.classList.add('text-red-600');

				// Automatically scroll the horizontal nav bar so the active tab is visible and centered on mobile
				if (navScrollContainer && window.innerWidth < 1024) {
					var containerWidth = navScrollContainer.offsetWidth;
					var linkLeft = activeItem.link.offsetLeft;
					var linkWidth = activeItem.link.offsetWidth;
					var scrollTarget = linkLeft - (containerWidth / 2) + (linkWidth / 2);
					navScrollContainer.scrollTo({
						left: scrollTarget,
						behavior: 'smooth'
					});
				}
			}
		}
		window.addEventListener('scroll', updateActiveNavLink, { passive: true });

		// =========================================================================
		// D. COLLAPSIBLE SPECIFICATIONS TOGGLE («عرض جميع المواصفات»)
		// =========================================================================
		var toggleSpecsBtn = document.getElementById('ashrafco-toggle-all-specs-btn');
		var extraSpecs = document.querySelectorAll('.ashrafco-extra-spec');
		var specsBtnText = document.getElementById('ashrafco-specs-btn-text');
		var specsBtnIcon = document.getElementById('ashrafco-specs-btn-icon');
		var isExpanded = false;

		if (toggleSpecsBtn && extraSpecs.length) {
			toggleSpecsBtn.addEventListener('click', function () {
				isExpanded = !isExpanded;
				extraSpecs.forEach(function (row) {
					if (isExpanded) {
						row.classList.remove('hidden');
					} else {
						row.classList.add('hidden');
					}
				});
				if (specsBtnText) {
					specsBtnText.textContent = isExpanded ? 'إخفاء المواصفات الإضافية' : 'عرض جميع المواصفات';
				}
				if (specsBtnIcon) {
					specsBtnIcon.style.transform = isExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
				}
			});
		}

		// =========================================================================
		// E. REVIEW FORM TOGGLE
		// =========================================================================
		var openReviewBtn = document.getElementById('ashrafco-open-review-form-btn');
		var reviewFormBox = document.getElementById('ashrafco-review-form-box');
		if (openReviewBtn && reviewFormBox) {
			openReviewBtn.addEventListener('click', function (e) {
				e.preventDefault();
				reviewFormBox.classList.toggle('hidden');
				if (!reviewFormBox.classList.contains('hidden')) {
					reviewFormBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
			});
		}
	});
</script>