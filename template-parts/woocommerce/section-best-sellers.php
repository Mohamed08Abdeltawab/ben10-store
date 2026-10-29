<?php
/**
 * Best Sellers (الأكثر مبيعاً) Product Carousel Section
 *
 * Displays the top 10 best-selling products from WooCommerce in a modern,
 * responsive horizontal carousel with RTL touch-swipe support and popularity order.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// 1. Resolve Dynamic WooCommerce Shop URL with popularity orderby parameter.
$shop_page_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$best_sellers_url = add_query_arg( 'orderby', 'popularity', $shop_page_url );

// 2. Retrieve verified watch products (protecting legacy industrial catalog)
$best_sellers_query = null;
if ( function_exists( 'ben10_get_watch_products' ) ) {
	$best_sellers_query = ben10_get_watch_products( array( 'limit' => 10, 'orderby' => 'popularity' ) );
}

// Fallback query only when explicitly allowing products or when verified
if ( is_null( $best_sellers_query ) && function_exists( 'wc_get_products' ) ) {
	$fallback_ids = wc_get_products(
		array(
			'status'     => 'publish',
			'limit'      => 10,
			'orderby'    => 'popularity',
			'order'      => 'DESC',
			'visibility' => 'catalog',
			'return'     => 'ids',
		)
	);

	// Filter out non-watch legacy products
	if ( ! empty( $fallback_ids ) && function_exists( 'ben10_is_watch_product' ) ) {
		$fallback_ids = array_filter( $fallback_ids, 'ben10_is_watch_product' );
	}

	if ( ! empty( $fallback_ids ) ) {
		$best_sellers_query = new WP_Query(
			array(
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'post__in'            => $fallback_ids,
				'orderby'             => 'post__in',
				'posts_per_page'      => count( $fallback_ids ),
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => true,
			)
		);
	}
}

$has_products = ( $best_sellers_query && $best_sellers_query->have_posts() );

// On the homepage, safely suppress the best sellers section if no verified watch products exist
if ( ! $has_products && ( is_front_page() || is_home() ) ) {
	return;
}
?>

<section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-10 py-5 sm:py-7 relative" aria-label="<?php esc_attr_e( 'الأكثر مبيعاً من ساعات Ben10', 'ashrafco' ); ?>">

	<!-- Section Header -->
	<div class="flex items-center justify-between mb-3.5 sm:mb-5 px-1" dir="rtl">
		
		<!-- Right: Title with Vibrant Green Accent -->
		<div class="flex items-center gap-2">
			<h2 class="text-base sm:text-xl md:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 m-0 leading-tight">
				<span>الساعات الأكثر طلباً</span>
				<span class="inline-flex items-center justify-center shrink-0 w-6 h-6 rounded-full bg-[#244A27]/10" aria-hidden="true">
					<svg class="w-4 h-4 text-[#A3E635]" viewBox="0 0 24 24" fill="currentColor">
						<path d="M12 23c-4.97 0-9-3.8-9-8.5 0-3.6 2.47-7.46 5.6-9.84.45-.34 1.1-.02 1.1.54 0 1.85 1.25 3.4 2.97 3.8.44.1.83-.24.83-.69 0-1.9.95-4.22 2.27-5.56.34-.34.92-.12.96.36.25 3.03 2.14 5.3 4.6 6.37.43.19.67.66.57 1.12C21.4 14.86 17.5 23 12 23z"/>
					</svg>
				</span>
			</h2>
		</div>

		<!-- Left: "عرض الكل" Link with Left Chevron -->
		<a
			href="<?php echo esc_url( $best_sellers_url ); ?>"
			class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-[#101211] hover:text-[#244A27] bg-[#A3E635]/20 hover:bg-[#A3E635]/40 px-3 py-1 rounded-full transition-colors group select-none"
			title="<?php esc_attr_e( 'عرض جميع الساعات في المتجر', 'ashrafco' ); ?>"
		>
			<span>عرض الكل</span>
			<svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
			</svg>
		</a>

	</div>

	<!-- Carousel Content Area (No overflow-hidden on outer wrapper to allow outside arrows) -->
	<div class="relative w-full group/bestsellers">

		<?php if ( $has_products && $best_sellers_query && $best_sellers_query->have_posts() ) : ?>

			<!-- Far Right Arrow (السابق - Outside track on Desktop) -->
			<button
				type="button"
				id="test-theme-bestsellers-prev"
				class="bestsellers-nav-btn bestsellers-nav-prev hidden lg:flex bg-[#101211] hover:bg-[#A3E635] text-white hover:text-[#101211] transition-colors"
				aria-label="<?php esc_attr_e( 'المنتجات السابقة', 'ashrafco' ); ?>"
				title="<?php esc_attr_e( 'السابق', 'ashrafco' ); ?>"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
				</svg>
			</button>

			<!-- Far Left Arrow (التالي - Outside track on Desktop) -->
			<button
				type="button"
				id="test-theme-bestsellers-next"
				class="bestsellers-nav-btn bestsellers-nav-next hidden lg:flex bg-[#101211] hover:bg-[#A3E635] text-white hover:text-[#101211] transition-colors"
				aria-label="<?php esc_attr_e( 'المنتجات التالية', 'ashrafco' ); ?>"
				title="<?php esc_attr_e( 'التالي', 'ashrafco' ); ?>"
			>
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
				</svg>
			</button>

			<!-- Horizontal Scroll Track -->
			<div
				id="test-theme-bestsellers-track"
				class="flex overflow-x-auto gap-4 pb-4 snap-x scrollbar-none items-stretch pt-1 snap-mandatory scroll-smooth no-scrollbar focus:outline-none"
				dir="rtl"
				tabindex="0"
				role="region"
				aria-label="<?php esc_attr_e( 'شريط الساعات الأكثر طلباً', 'ashrafco' ); ?>"
			>
				<?php
				while ( $best_sellers_query->have_posts() ) :
					$best_sellers_query->the_post();
				?>
					<div class="best-sellers-card-item flex-shrink-0 w-[220px] sm:w-[240px] snap-start">
						<?php get_template_part( 'template-parts/woocommerce/content-product-card' ); ?>
					</div>
				<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>

		<?php else : ?>

			<!-- Graceful Neutral Brand Curation State Card -->
			<div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 text-center max-w-lg mx-auto shadow-sm my-4">
				<div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-[#244A27]/10 text-[#A3E635] flex items-center justify-center">
					<svg class="w-7 h-7 text-[#244A27]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<circle cx="12" cy="12" r="10"></circle>
						<polyline points="12 6 12 12 16 14"></polyline>
					</svg>
				</div>
				<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1">
					تشكيلة ساعات Ben10 قريباً
				</h3>
				<p class="text-xs sm:text-sm text-slate-500 mb-4 leading-relaxed">
					نقوم حالياً بتجهيز وإطلاق أحدث مجموعات الساعات العصرية الفاخرة.
				</p>
				<a
					href="<?php echo esc_url( $shop_page_url ); ?>"
					class="inline-flex items-center gap-2 bg-[#101211] hover:bg-[#244A27] text-white text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm transition-colors"
				>
					<span>زيارة المتجر</span>
				</a>
			</div>

		<?php endif; ?>

	</div>

</section>

