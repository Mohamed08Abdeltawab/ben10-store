<?php
/**
 * Template Name: عروضنا / العروض والتخفيضات الحصرية
 * Description: صفحة عروض وتخفيضات متجر أشرفكو الديناميكية المعتمدة كلياً على أسعار التخفيض وجدولة ووكومرس الأصلية.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

// Ensure WooCommerce add-to-cart scripts and styles are available for AJAX cart operations
if (function_exists('wp_enqueue_script')) {
	wp_enqueue_script('wc-add-to-cart');
}

get_header();

// -----------------------------------------------------------------------------
// 1. Parse Request Parameters: Filtering, Sorting, Pagination & Search
// -----------------------------------------------------------------------------
$paged = max(1, get_query_var('paged') ? absint(get_query_var('paged')) : (isset($_GET['paged']) ? absint($_GET['paged']) : 1));
$category_slug = isset($_GET['category']) ? sanitize_title(wp_unslash($_GET['category'])) : '';
$search_query = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
$orderby = isset($_GET['orderby']) ? sanitize_key(wp_unslash($_GET['orderby'])) : 'discount'; // default to highest discount
$order = isset($_GET['order']) ? ('ASC' === strtoupper(sanitize_text_field(wp_unslash($_GET['order']))) ? 'ASC' : 'DESC') : 'DESC';
$timed_only = isset($_GET['timed']) && '1' === (string) $_GET['timed'];

// -----------------------------------------------------------------------------
// 2. Query Native On-Sale Products Catalog
// -----------------------------------------------------------------------------
$catalog_args = array(
	'limit' => 12,
	'page' => $paged,
	'category' => $category_slug,
	's' => $search_query,
	'orderby' => $orderby,
	'order' => $order,
);

$catalog_data = function_exists('ashrafco_get_on_sale_catalog')
	? ashrafco_get_on_sale_catalog($catalog_args)
	: array('items' => array(), 'total' => 0, 'total_pages' => 0, 'current_page' => 1, 'max_discount' => 0, 'categories' => array());

$qualifying_items = $catalog_data['items'] ?? array();
$total_on_sale = $catalog_data['total'] ?? 0;
$total_pages = $catalog_data['total_pages'] ?? 0;
$current_page = $catalog_data['current_page'] ?? 1;
$max_discount = $catalog_data['max_discount'] ?? 0;
$cat_filters = $catalog_data['categories'] ?? array();

// If user specifically filtered for "timed only" (scheduled countdowns), filter items in memory
if ($timed_only) {
	$qualifying_items = array_values(array_filter($qualifying_items, function ($item) {
		return !empty($item['discount_data']['has_countdown']);
	}));
	$total_on_sale = count($qualifying_items);
	$total_pages = 1;
}

// Global discount cap for hero highlight (default fallback to 20% if none)
$display_max_discount = $max_discount > 0 ? $max_discount : 20;

// Resolve Hero Visual from first available product with valid image
$hero_img = '';
if (!empty($qualifying_items)) {
	foreach ($qualifying_items as $item) {
		$prod = $item['product'];
		$thumb_id = $prod->get_image_id();
		if ($thumb_id) {
			$hero_img = wp_get_attachment_image_url($thumb_id, 'large');
			if ($hero_img) {
				break;
			}
		}
	}
}
if (empty($hero_img)) {
	$hero_img = content_url('/uploads/products/photo_28_2026-10-06_16-57-21.jpg');
}
?>

<main id="primary"
	class="site-main w-full min-h-screen bg-slate-50 font-sans pb-16 selection:bg-rose-500 selection:text-white"
	dir="rtl">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6">

		<!-- =====================================================================
			 1. BREADCRUMBS TRAIL
			 ===================================================================== -->
		<nav class="mb-4 sm:mb-6 text-xs text-slate-400 flex items-center gap-2 flex-wrap"
			aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors no-underline">
				<?php esc_html_e('الرئيسية', 'ashrafco'); ?>
			</a>
			<span class="text-slate-300">/</span>
			<a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"
				class="hover:text-primary transition-colors no-underline">
				<?php esc_html_e('المتجر', 'ashrafco'); ?>
			</a>
			<span class="text-slate-300">/</span>
			<span class="text-slate-800 font-bold truncate">
				<?php esc_html_e('عروض وتخفيضات المتجر', 'ashrafco'); ?>
			</span>
		</nav>

		<!-- =====================================================================
			 2. PROMOTIONAL HERO BANNER (Dynamic & Connected to Real Discounts)
			 ===================================================================== -->
		<section id="promotions-hero-banner"
			class="relative rounded-3xl overflow-hidden shadow-xl border border-slate-900/60 bg-gradient-to-br from-slate-950 via-[#10141e] to-red-950/70 text-white p-6 sm:p-10 lg:p-12 mb-8 select-none">

			<!-- Atmospheric Background Glow Accents -->
			<div class="absolute -top-24 -right-24 w-96 h-96 bg-red-600/25 rounded-full blur-3xl pointer-events-none">
			</div>
			<div
				class="absolute -bottom-24 -left-24 w-96 h-96 bg-orange-600/15 rounded-full blur-3xl pointer-events-none">
			</div>
			<div
				class="absolute inset-0 bg-[radial-gradient(#ffffff0d_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none opacity-40">
			</div>

			<!-- Hero Content Grid -->
			<div
				class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-8 items-center min-h-[260px] sm:min-h-[280px]">

				<!-- Right Column (RTL): Promotional Text & CTA Button -->
				<div class="md:col-span-7 space-y-4 sm:space-y-5 text-right z-10">

					<!-- Badge Tag -->
					<div
						class="inline-flex items-center gap-2 bg-[#ff171f] text-white text-xs sm:text-sm font-bold px-3.5 py-1.5 rounded-full shadow-sm">
						<svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<span><?php esc_html_e('تخفيضات حصرية مباشرة', 'ashrafco'); ?></span>
					</div>

					<!-- Main Heading -->
					<h1
						class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight font-cairo tracking-tight">
						<?php esc_html_e('عروض وتخفيضات أشرفكو', 'ashrafco'); ?>
					</h1>

					<!-- Subtitle -->
					<p class="text-sm sm:text-base lg:text-lg text-slate-300 font-medium leading-relaxed max-w-xl">
						<?php esc_html_e('أفضل ماكينات قص السيراميك والبورسلين، موازين الليزر، ومعدات التركيب بأسعار مخفضة وخصومات حقيقية معتمدة.', 'ashrafco'); ?>
					</p>

					<!-- Action CTA Button & Stats -->
					<div class="pt-2 flex items-center gap-4 flex-wrap">
						<a href="#promotions-catalog"
							class="hero-scroll-btn inline-flex items-center gap-3 bg-white hover:bg-slate-100 text-[#d90429] font-bold text-sm sm:text-base px-6 py-3.5 rounded-2xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 cursor-pointer no-underline group">
							<span><?php esc_html_e('تسوق العروض الآن', 'ashrafco'); ?></span>
							<svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M14 5l7 7m0 0l-7 7m7-7H3" />
							</svg>
						</a>

						<?php if ($total_on_sale > 0): ?>
							<span
								class="text-xs sm:text-sm text-slate-300 font-semibold bg-white/10 px-3.5 py-2 rounded-xl backdrop-blur-xs">
								<?php printf(esc_html__('%d منتج مخفض حالياً', 'ashrafco'), $total_on_sale); ?>
							</span>
						<?php endif; ?>
					</div>
				</div>

				<!-- Left Column (RTL): Image & Prominent Circular Discount Badge -->
				<div class="md:col-span-5 relative flex items-center justify-center">

					<!-- Floating Circular Red Discount Badge -->
					<div
						class="absolute -bottom-2 right-2 sm:bottom-2 sm:right-2 md:-right-4 z-20 w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-gradient-to-tr from-[#b00320] via-[#ff171f] to-rose-500 shadow-2xl flex flex-col items-center justify-center text-center p-2.5 border-2 border-white/20 transform -rotate-3 hover:rotate-0 transition-transform select-none">
						<span class="text-[11px] sm:text-xs font-bold text-white/95 leading-tight">
							<?php esc_html_e('خصومات تصل إلى', 'ashrafco'); ?>
						</span>
						<span
							class="text-3xl sm:text-4xl font-black text-white font-mono leading-none tracking-tight mt-1">
							<?php echo esc_html($display_max_discount); ?>%
						</span>
					</div>

					<!-- Image Wrapper -->
					<div
						class="relative w-full max-w-sm sm:max-w-md h-56 sm:h-64 lg:h-72 flex items-center justify-center p-2">
						<img src="<?php echo esc_url($hero_img); ?>"
							alt="<?php esc_attr_e('عروض أشرفكو', 'ashrafco'); ?>"
							class="max-w-full max-h-full object-contain filter drop-shadow-[0_15px_25px_rgba(0,0,0,0.65)] hover:scale-105 transition-transform duration-500"
							loading="eager" />
					</div>

				</div>

			</div>
		</section>

		<!-- =====================================================================
			 3. DYNAMIC CATEGORY & OFFERS FILTER BAR + SORTING TOOLBAR
			 ===================================================================== -->
		<section id="promotions-catalog" class="mb-8 space-y-4"
			aria-label="<?php esc_attr_e('تصفية وترتيب العروض', 'ashrafco'); ?>">

			<!-- Category Filter Pills (Dynamically populated from genuine on-sale products) -->
			<div class="flex items-center gap-2 sm:gap-2.5 flex-wrap">

				<!-- Pill 1: جميع العروض (All Offers) -->
				<?php
				$all_url = remove_query_arg(array('category', 'paged', 'timed'));
				$is_all_active = empty($category_slug) && !$timed_only;
				?>
				<a href="<?php echo esc_url($all_url); ?>"
					class="promo-filter-pill flex items-center gap-2 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold border transition-all duration-200 no-underline shadow-xs <?php echo $is_all_active ? 'is-active bg-[#ff171f] text-white border-[#ff171f]' : 'bg-white text-slate-700 border-slate-200/90 hover:bg-slate-50'; ?>">
					<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
					</svg>
					<span><?php esc_html_e('جميع العروض', 'ashrafco'); ?></span>
					<span class="text-[11px] opacity-80 font-mono">(<?php echo esc_html($total_on_sale); ?>)</span>
				</a>

				<!-- Dynamic Category Pills -->
				<?php foreach ($cat_filters as $c_item):
					$c_url = add_query_arg(array('category' => $c_item['slug'], 'paged' => 1), remove_query_arg('timed'));
					$c_active = ($category_slug === $c_item['slug']);
					?>
					<a href="<?php echo esc_url($c_url); ?>"
						class="promo-filter-pill flex items-center gap-2 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold border transition-all duration-200 no-underline shadow-xs <?php echo $c_active ? 'is-active bg-[#ff171f] text-white border-[#ff171f]' : 'bg-white text-slate-700 border-slate-200/90 hover:bg-slate-50'; ?>">
						<span><?php echo esc_html($c_item['name']); ?></span>
						<span
							class="text-[11px] <?php echo $c_active ? 'text-white/90' : 'text-slate-400'; ?> font-mono">(<?php echo esc_html($c_item['count']); ?>)</span>
					</a>
				<?php endforeach; ?>

				<!-- Timed Offers Pill (Only shown if timed products exist) -->
				<?php
				$timed_url = add_query_arg(array('timed' => '1', 'paged' => 1), remove_query_arg('category'));
				?>
				<a href="<?php echo esc_url($timed_only ? remove_query_arg('timed') : $timed_url); ?>"
					class="promo-filter-pill flex items-center gap-1.5 px-4 py-2 rounded-2xl text-xs sm:text-sm font-bold border transition-all duration-200 no-underline shadow-xs <?php echo $timed_only ? 'is-active bg-[#ff171f] text-white border-[#ff171f]' : 'bg-white text-slate-700 border-slate-200/90 hover:bg-slate-50'; ?>">
					<svg class="w-3.5 h-3.5 text-rose-500 animate-spin-slow" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					<span><?php esc_html_e('عروض تنتهي قريباً', 'ashrafco'); ?></span>
				</a>

			</div>

			<!-- Secondary Controls: Search & Orderby Toolbar -->
			<div
				class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2 border-t border-slate-200/80">

				<!-- Results Counter -->
				<div class="text-xs sm:text-sm font-semibold text-slate-500">
					<?php if ($total_on_sale > 0): ?>
						<?php printf(esc_html__('عرض %d منتج مخفض حالياً', 'ashrafco'), $total_on_sale); ?>
					<?php else: ?>
						<?php esc_html_e('لا توجد منتجات مطابقة للبحث أو الفلتر', 'ashrafco'); ?>
					<?php endif; ?>
				</div>

				<!-- Sorting Form -->
				<form method="get" action="<?php echo esc_url(ashrafco_get_offers_url()); ?>"
					class="flex items-center gap-2">
					<?php if (!empty($category_slug)): ?>
						<input type="hidden" name="category" value="<?php echo esc_attr($category_slug); ?>" />
					<?php endif; ?>
					<?php if ($timed_only): ?>
						<input type="hidden" name="timed" value="1" />
					<?php endif; ?>

					<label for="offers-orderby-select" class="text-xs font-bold text-slate-600 whitespace-nowrap">
						<?php esc_html_e('ترتيب حسب:', 'ashrafco'); ?>
					</label>

					<select id="offers-orderby-select" name="orderby" onchange="this.form.submit()"
						class="text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl px-3 py-1.5 focus:outline-none focus:border-red-500 cursor-pointer shadow-2xs">
						<option value="discount" <?php selected($orderby, 'discount'); ?>>
							<?php esc_html_e('الأعلى خصماً (%)', 'ashrafco'); ?>
						</option>
						<option value="date" <?php selected($orderby, 'date'); ?>>
							<?php esc_html_e('الأحدث', 'ashrafco'); ?>
						</option>
						<option value="price-asc" <?php selected($orderby, 'price-asc'); ?>>
							<?php esc_html_e('السعر: من الأقل للأعلى', 'ashrafco'); ?>
						</option>
						<option value="price-desc" <?php selected($orderby, 'price-desc'); ?>>
							<?php esc_html_e('السعر: من الأعلى للأقل', 'ashrafco'); ?>
						</option>
						<option value="popularity" <?php selected($orderby, 'popularity'); ?>>
							<?php esc_html_e('الأكثر طلباً', 'ashrafco'); ?>
						</option>
					</select>
				</form>

			</div>

		</section>

		<!-- =====================================================================
			 4. PROMOTIONAL PRODUCT CARDS GRID
			 ===================================================================== -->
		<section id="offers-catalog-grid" class="mb-12">

			<?php if (empty($qualifying_items)): ?>
				<!-- Global / Filter Empty State -->
				<div class="py-16 px-4 bg-white border border-slate-200/90 rounded-3xl text-center shadow-xs">
					<div
						class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-red-50 text-[#ff171f] flex items-center justify-center">
						<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
								d="M12 8v13m0-13V4.5a2.5 2.5 0 115 0V8h-5zm0 0H7m5 0v13m-5-13a2.5 2.5 0 100-5c-1.5 0-2.5 1-2.5 2.5V8h2.5zm0 0H2v13a2 2 0 002 2h16a2 2 0 002-2V8h-5" />
						</svg>
					</div>
					<h3 class="text-xl font-black text-slate-900 mb-2">
						<?php esc_html_e('لا توجد منتجات مخفضة مطابقة حالياً', 'ashrafco'); ?>
					</h3>
					<p class="text-sm text-slate-500 font-medium max-w-md mx-auto mb-6">
						<?php esc_html_e('تابعنا باستمرار للاستفادة من أقوى العروض والتخفيضات على ماكينات ومعدات السيراميك والبورسلين.', 'ashrafco'); ?>
					</p>
					<a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"
						class="inline-flex items-center gap-2 bg-[#ff171f] hover:bg-[#d90429] text-white font-bold text-sm px-6 py-3 rounded-2xl shadow-sm transition-all no-underline">
						<span><?php esc_html_e('تصفح جميع المنتجات في المتجر', 'ashrafco'); ?></span>
						<svg class="w-4 h-4 transform -scale-x-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M14 5l7 7m0 0l-7 7m7-7H3" />
						</svg>
					</a>
				</div>
			<?php else: ?>

				<!-- Grid: 4 columns on Desktop, 3 on Tablet, 2 on Mobile -->
				<div id="promotions-grid"
					class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
					<?php
					foreach ($qualifying_items as $item):
						$GLOBALS['product'] = $item['product'];
						get_template_part('template-parts/woocommerce/content-product-card');
					endforeach;
					wp_reset_postdata();
					?>
				</div>

				<!-- Pagination Row -->
				<?php if ($total_pages > 1): ?>
					<div class="Pagination-Row flex items-center justify-center gap-2"
						aria-label="<?php esc_attr_e('صفحات العروض', 'ashrafco'); ?>">
						<?php
						$pagination_links = paginate_links(array(
							'base' => add_query_arg('paged', '%#%'),
							'format' => '',
							'current' => $current_page,
							'total' => $total_pages,
							'prev_text' => '&rarr; ' . __('السابق', 'ashrafco'),
							'next_text' => __('التالي', 'ashrafco') . ' &larr;',
							'type' => 'array',
						));

						if (!empty($pagination_links)):
							foreach ($pagination_links as $link):
								$is_curr = strpos($link, 'current') !== false;
								$link_clean = str_replace('page-numbers', 'px-4 py-2 rounded-xl text-xs sm:text-sm font-bold border transition-all no-underline ' . ($is_curr ? 'bg-[#ff171f] text-white border-[#ff171f] shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'), $link);
								echo $link_clean; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							endforeach;
						endif;
						?>
					</div>
				<?php endif; ?>

			<?php endif; ?>

		</section>


	</div>
</main>

<!-- Enqueue Portal Offers Client Controller -->
<script
	src="<?php echo esc_url(add_query_arg('ver', function_exists('ashrafco_get_asset_version') ? ashrafco_get_asset_version('/assets/js/portal-offers.js') : '3.0.0', get_stylesheet_directory_uri() . '/assets/js/portal-offers.js')); ?>"></script>

<?php
get_footer();
