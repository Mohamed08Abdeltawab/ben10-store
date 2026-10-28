<?php
/**
 * The Template for displaying product archives, including the main shop page
 * which is a post type archive.
 *
 * Enhanced with 12-column grid layout, sticky desktop filters sidebar,
 * and mobile slide-over filter drawer.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header('shop');

// Calculate active filters count for the mobile button badge
$active_filters_count = 0;
if (!empty($_GET['filter_cat'])) {
	$active_filters_count += count((array) $_GET['filter_cat']);
}
if (!empty($_GET['in_stock_only'])) {
	$active_filters_count++;
}
if (!empty($_GET['filter_brand'])) {
	$active_filters_count += count((array) $_GET['filter_brand']);
}
if (!empty($_GET['filter_country'])) {
	$active_filters_count += count((array) $_GET['filter_country']);
}
if (!empty($_GET['filter_classification'])) {
	$active_filters_count += count((array) $_GET['filter_classification']);
}
if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
	$active_filters_count++;
}
if (!empty($_GET['s'])) {
	$active_filters_count++;
}

// Reset URL
$reset_url = is_product_category() ? get_term_link(get_queried_object()) : (function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'));
?>

<div class="w-full min-h-[60vh] bg-slate-50 font-sans clear-both" dir="rtl">

	<!-- Robust full-width clearing container for main content -->
	<div class="container mx-auto px-4 py-6 max-w-7xl">

		<!-- Shop Header & Sorter -->
		<div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-200/80">
			<!-- Right side (RTL): Breadcrumbs & Page title with product count -->
			<div class="space-y-1.5">
				<?php if (function_exists('woocommerce_breadcrumb')): ?>
					<nav class="test-theme-breadcrumbs text-xs text-slate-400 mb-1"
						aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
						<?php
						woocommerce_breadcrumb(array(
							'delimiter' => '<span class="mx-2 text-slate-300">/</span>',
							'wrap_before' => '<nav class="woocommerce-breadcrumb flex items-center flex-wrap text-xs text-slate-400">',
							'wrap_after' => '</nav>',
							'before' => '<span class="hover:text-primary transition-colors">',
							'after' => '</span>',
							'home' => _x('الرئيسية', 'breadcrumb', 'woocommerce'),
						));
						?>
					</nav>
				<?php endif; ?>

				<div class="flex items-center gap-3 flex-wrap">
					<h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
						<?php
						if (is_shop()) {
							echo esc_html__('المتجر', 'ashrafco');
						} else {
							woocommerce_page_title();
						}
						?>
					</h1>
					<?php
					global $wp_query;
					$total_products = $wp_query->found_posts;
					if ($total_products > 0):
						?>
						<span
							class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200/60">
							<?php
							/* translators: %d: number of products */
							printf(esc_html(_n('عرض %d منتج', 'عرض %d منتجات', $total_products, 'ashrafco')), $total_products);
							?>
						</span>
					<?php endif; ?>
				</div>

				<?php if (is_product_category() || is_product_tag()): ?>
					<div class="text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
						<?php do_action('woocommerce_archive_description'); ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Left side (RTL): Mobile Filter Trigger + Sorting Dropdown -->
			<div class="flex items-center gap-3 flex-wrap">
				<!-- Mobile Filter Trigger Button (lg:hidden) -->
				<button type="button" id="test-theme-mobile-filter-trigger"
					class="lg:hidden flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-800 border border-slate-200/90 px-3.5 py-2 rounded-xl font-bold text-xs sm:text-sm shadow-xs transition-all active:scale-95 cursor-pointer"
					aria-label="<?php esc_attr_e('تصفية المنتجات', 'ashrafco'); ?>" aria-expanded="false"
					aria-controls="test-theme-filters-drawer">
					<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
						</path>
					</svg>
					<span><?php esc_html_e('تصفية المنتجات', 'ashrafco'); ?></span>
					<?php if ($active_filters_count > 0): ?>
						<span
							class="bg-primary text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center font-mono">
							<?php echo esc_html($active_filters_count); ?>
						</span>
					<?php endif; ?>
				</button>

				<!-- Styled WooCommerce sorting dropdown (Orderby selector) -->
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>

		<!-- Two Column Split (RTL: Filters on Right, Grid on Left) -->
		<div id="products-catalog" class="flex flex-col lg:flex-row gap-6 items-start">

			<!-- Products Section -->
			<main id="primary" class="site-main w-full lg:flex-1 order-2 lg:order-2 min-w-0">

				<!-- Active Filter Chips Bar -->
				<?php if ($active_filters_count > 0): ?>
					<div
						class="flex items-center gap-2 flex-wrap mb-6 p-3 bg-white rounded-2xl border border-slate-200/70 text-xs shadow-xs">
						<span class="font-bold text-slate-500 flex items-center gap-1.5 ml-2">
							<svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
								</path>
							</svg>
							<span><?php esc_html_e('الفلاتر النشطة:', 'ashrafco'); ?></span>
						</span>

						<?php if (!empty($_GET['s'])): ?>
							<span
								class="inline-flex items-center gap-1.5 bg-red-50 text-[primary] font-bold px-2.5 py-1 rounded-lg border border-red-200">
								<span>بحث: "<?php echo esc_html(wp_unslash($_GET['s'])); ?>"</span>
								<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
									<a href="<?php echo esc_url(test_theme_get_remove_filter_url('s')); ?>" class="hover:text-red-900 transition-colors" aria-label="<?php esc_attr_e('إزالة البحث', 'ashrafco'); ?>">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
									</a>
								<?php endif; ?>
							</span>
						<?php endif; ?>

						<?php if (!empty($_GET['filter_cat'])):
							foreach ((array) $_GET['filter_cat'] as $cat_slug):
								$cat_obj = get_term_by('slug', $cat_slug, 'product_cat');
								$cat_name = $cat_obj ? $cat_obj->name : $cat_slug;
								?>
								<span
									class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-800 font-bold px-2.5 py-1 rounded-lg border border-blue-200">
									<span>القسم: <?php echo esc_html($cat_name); ?></span>
									<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
										<a href="<?php echo esc_url(test_theme_get_remove_filter_url('filter_cat', $cat_slug)); ?>" class="hover:text-blue-950 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر القسم', 'ashrafco'); ?>">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
										</a>
									<?php endif; ?>
								</span>
							<?php endforeach;
						endif; ?>

						<?php if (!empty($_GET['in_stock_only'])): ?>
							<span
								class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-lg border border-emerald-200">
								<span>متوفر بالمخزن</span>
								<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
									<a href="<?php echo esc_url(test_theme_get_remove_filter_url('in_stock_only')); ?>" class="hover:text-emerald-900 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر التوفر', 'ashrafco'); ?>">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
									</a>
								<?php endif; ?>
							</span>
						<?php endif; ?>

						<?php if (!empty($_GET['filter_brand'])):
							foreach ((array) $_GET['filter_brand'] as $b_slug):
								$b_term = get_term_by('slug', $b_slug, 'product_brand');
								if (!$b_term) {
									$b_term = get_term_by('name', $b_slug, 'product_brand');
								}
								$b_label = $b_term ? $b_term->name : $b_slug;
						?>
							<span
								class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-800 font-bold px-2.5 py-1 rounded-lg border border-blue-200">
								<span>العلامة: <?php echo esc_html($b_label); ?></span>
								<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
									<a href="<?php echo esc_url(test_theme_get_remove_filter_url('filter_brand', $b_slug)); ?>" class="hover:text-blue-950 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر العلامة التجارية', 'ashrafco'); ?>">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
									</a>
								<?php endif; ?>
							</span>
						<?php endforeach;
						endif; ?>

						<?php if (!empty($_GET['filter_country'])):
							foreach ((array) $_GET['filter_country'] as $c): ?>
								<span
									class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-800 font-bold px-2.5 py-1 rounded-lg border border-amber-200">
									<span>بلد الصنع: <?php echo esc_html($c); ?></span>
									<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
										<a href="<?php echo esc_url(test_theme_get_remove_filter_url('filter_country', $c)); ?>" class="hover:text-amber-950 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر بلد الصنع', 'ashrafco'); ?>">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
										</a>
									<?php endif; ?>
								</span>
							<?php endforeach;
						endif; ?>

						<?php if (!empty($_GET['filter_classification'])):
							foreach ((array) $_GET['filter_classification'] as $cls): ?>
								<span
									class="inline-flex items-center gap-1.5 bg-rose-50 text-rose-800 font-bold px-2.5 py-1 rounded-lg border border-rose-200">
									<span>تصنيف: <?php echo esc_html($cls); ?></span>
									<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
										<a href="<?php echo esc_url(test_theme_get_remove_filter_url('filter_classification', $cls)); ?>" class="hover:text-rose-950 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر التصنيف', 'ashrafco'); ?>">
											<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
										</a>
									<?php endif; ?>
								</span>
							<?php endforeach;
						endif; ?>

						<?php if (!empty($_GET['min_price']) || !empty($_GET['max_price'])): ?>
							<span
								class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-800 font-bold px-2.5 py-1 rounded-lg border border-slate-200">
								<span>السعر: <?php echo esc_html(!empty($_GET['min_price']) ? $_GET['min_price'] : '0'); ?>
									- <?php echo esc_html(!empty($_GET['max_price']) ? $_GET['max_price'] : '...'); ?>
									ج.م</span>
								<?php if (function_exists('test_theme_get_remove_filter_url')): ?>
									<a href="<?php echo esc_url(test_theme_get_remove_filter_url('min_price')); ?>" class="hover:text-slate-950 transition-colors" aria-label="<?php esc_attr_e('إزالة فلتر السعر', 'ashrafco'); ?>">
										<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
									</a>
								<?php endif; ?>
							</span>
						<?php endif; ?>

						<a href="<?php echo esc_url($reset_url); ?>"
							class="text-[primary] hover:text-[primary-hover] font-bold underline mr-auto transition-colors">
							<?php esc_html_e('مسح الكل', 'ashrafco'); ?>
						</a>
					</div>
				<?php endif; ?>

				<?php if (woocommerce_product_loop()): ?>

					<?php do_action('woocommerce_before_shop_loop'); ?>

					<!-- Responsive Product Grid: 2 cols on mobile, 2 on sm, 3 on md, 4 on xl -->
					<div
						class="test-theme-product-grid grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4 w-full mb-8">
						<?php
						if (wc_get_loop_prop('total')) {
							while (have_posts()) {
								the_post();
								do_action('woocommerce_shop_loop');
								wc_get_template_part('content', 'product');
							}
						}
						?>
					</div>

					<!-- Styled WooCommerce Pagination -->
					<div class="mt-12 flex justify-center">
						<?php woocommerce_pagination(); ?>
					</div>

					<?php do_action('woocommerce_after_shop_loop'); ?>

				<?php else: ?>

					<?php
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action('woocommerce_no_products_found');
					?>

				<?php endif; ?>

			</main>

			<!-- Sticky Filter Sidebar -->
			<aside class="hidden lg:block w-full lg:w-72 flex-shrink-0 order-1 lg:order-1 sticky top-6 self-start z-10">
				<?php get_template_part('template-parts/woocommerce/shop-filters', null, array('sidebar' => true, 'drawer' => false)); ?>
			</aside>

		</div>

		<!-- Mobile Filter Drawer (Mounted outside flex layout) -->
		<?php get_template_part('template-parts/woocommerce/shop-filters', null, array('sidebar' => false, 'drawer' => true)); ?>

	</div>

</div>

<?php
get_footer('shop');
