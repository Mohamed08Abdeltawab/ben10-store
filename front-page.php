<?php
/**
 * Modern Mobile-First Noon-Style Storefront Homepage
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

// Centralized Dynamic Configuration
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$wa_hero_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً %s، أرغب بالاستفسار عن ماكينات ومعدات السيراميك والبورسلين المعروضة على المتجر.', $store_name));
$wa_consult_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً %s، أحتاج استشارة هندسية وفنية لاختيار الماكينة الأنسب لطبيعة شغلي ومشاريعي.', $store_name));

get_header();
?>

<main id="primary" class="site-main w-full overflow-hidden bg-[#f7f7fa] font-sans pb-20" dir="rtl">

	<!-- =====================================================================
		 1. MODERN LANDSCAPE HERO BANNER SLIDER (SMART SLIDER 3)
		 ===================================================================== -->
	<?php
	test_theme_render_hero_slider();
	?>



	<!-- =====================================================================
		 4. SINGLE-ROW CATEGORY SLIDER (STRICTLY MATCHING image_37d807.jpg)
		 Real WooCommerce product categories containing sellable eligible products
		 ===================================================================== -->
	<?php
	$categories_data = function_exists('ashrafco_get_sellable_categories')
		? ashrafco_get_sellable_categories()
		: (function_exists('ashrafco_get_sellable_category_slider_data') ? ashrafco_get_sellable_category_slider_data() : array());
	?>

	<?php if (!empty($categories_data)): ?>
		<section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-7 relative" aria-label="تصفح حسب الفئة">
			<!-- Section Header -->
			<div class="flex items-center justify-between mb-4 sm:mb-6 px-1" dir="rtl">
				<h2
					class="text-lg sm:text-xl md:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2 m-0 leading-tight">
					تصفح حسب الفئة
				</h2>
				<a href="<?php echo esc_url($shop_url); ?>"
					class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-[#d90429] hover:text-[#b90323] transition-colors group select-none py-1"
					title="عرض جميع التصنيفات في المتجر">
					<span>عرض الكل</span>
					<svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none"
						stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
			</div>

			<!-- Categories Track: Touch-Swipe on Mobile (<md) & Balanced 5-Column Grid on Tablet/Desktop (md+) -->
			<div class="categories-track flex md:grid md:grid-cols-5 items-stretch gap-3 sm:gap-3.5 md:gap-4 lg:gap-5 overflow-x-auto md:overflow-visible no-scrollbar pt-1 pb-3 px-1 scroll-smooth snap-x snap-mandatory"
				dir="rtl">
				<?php foreach ($categories_data as $cat): ?>
					<a href="<?php echo esc_url($cat['link']); ?>" title="<?php echo esc_attr($cat['name']); ?>"
						class="category-card flex flex-col items-center justify-between shrink-0 md:shrink w-[114px] sm:w-[130px] md:w-full min-h-[126px] sm:min-h-[140px] md:min-h-[160px] lg:min-h-[185px] bg-white rounded-2xl md:rounded-3xl p-3 sm:p-4 lg:p-5 shadow-sm border border-slate-100 hover:shadow-md hover:border-red-200 hover:-translate-y-1 transition-all duration-300 group cursor-pointer select-none snap-start">
						<div
							class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 lg:w-24 lg:h-24 flex items-center justify-center mb-2 sm:mb-2.5 shrink-0 aspect-square">
							<img src="<?php echo esc_url($cat['image']); ?>" alt="<?php echo esc_attr($cat['name']); ?>"
								width="96" height="96" loading="lazy" decoding="async"
								class="max-w-full max-h-full w-auto h-auto object-contain group-hover:scale-105 transition-transform duration-300">
						</div>
						<p
							class="text-xs sm:text-[13px] md:text-sm lg:text-[15px] font-bold text-slate-800 group-hover:text-[#d90429] transition-colors text-center leading-snug whitespace-normal break-words m-0 mt-auto">
							<?php echo esc_html($cat['name']); ?>
						</p>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- =====================================================================
		 BEST SELLERS (الأكثر مبيعاً) HORIZONTAL PRODUCT CAROUSEL SECTION
		 Strictly matching reference specification
		 ===================================================================== -->
	<?php get_template_part('template-parts/woocommerce/section-best-sellers'); ?>

	<!-- =====================================================================
		 PROMOTIONAL DEALS BANNER (DYNAMIC OVERLAY)
		 ===================================================================== -->
	<section class="w-full max-w-7xl mx-auto" aria-label="عروض خاصة">
		<div class="w-full px-4 py-3 sm:py-5">
			<div
				class="relative w-full aspect-[16/5] sm:aspect-[21/6] max-h-[160px] sm:max-h-[220px] rounded-3xl overflow-hidden shadow-sm bg-[#fee2e2]">
				<!-- The Raw Background Image -->
				<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/promo-deals-bg.webp'); ?>"
					alt="عروض خاصة" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy"
					decoding="async" />

				<!-- Overlay Content (From Right to Left in RTL order) -->
				<div class="absolute inset-0 z-10 flex items-center justify-between px-6 sm:px-12" dir="rtl">
					<!-- Right Side (Visual text & CTA) -->
					<div class="flex flex-col justify-center items-start text-right gap-0.5 sm:gap-1 max-w-[45%]">
						<!-- Title 1 (Dynamic Badge style) -->
						<div class="flex items-center gap-1.5 text-[#d90429] font-bold text-sm sm:text-lg">
							<span class="w-2.5 h-2.5 rounded-full bg-[#d90429] shrink-0 animate-pulse"></span>
							<span>عروض خاصة</span>
						</div>
						<!-- Subtitle 2 (Offer Details) -->
						<p class="text-[#1e293b] text-xs sm:text-sm font-semibold mt-0.5">خصومات تصل حتى 50%</p>
						<!-- Action Button ("عرض العروض") -->
						<a href="<?php echo esc_url(home_url('/offers')); ?>"
							class="mt-2.5 sm:mt-4 bg-[#d90429] hover:bg-red-700 text-white text-[11px] sm:text-xs font-bold px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1 transition-transform active:scale-95">
							<span>عرض العروض</span>
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
									d="M15 19l-7-7 7-7" />
							</svg>
						</a>
					</div>

					<!-- Left Side (Floating badge over the Tools) -->
					<div
						class="absolute top-4 sm:top-6 left-12 sm:left-24 bg-[#d90429] text-white rounded-xl px-2.5 py-1.5 sm:py-2 flex flex-col items-center justify-center text-center shadow-md select-none transform -rotate-3 hover:rotate-0 transition-transform duration-200">
						<span class="text-[9px] sm:text-[10px] uppercase font-bold tracking-wider opacity-90">خصم
							حتى</span>
						<span class="text-base sm:text-2xl font-black leading-none">50%</span>
					</div>
				</div>
			</div>
		</div>
	</section>


	<!-- =====================================================================
		 5. FEATURED / LATEST PRODUCTS SECTION
		 ===================================================================== -->
	<?php
	$products_query = function_exists('ashrafco_get_featured_products')
		? ashrafco_get_featured_products(array('limit' => 8))
		: new WP_Query(array(
			'post_type' => 'product',
			'posts_per_page' => 8,
			'post_status' => 'publish',
		));
	?>

	<section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6">
		<!-- Section Header with Shop Link -->
		<div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200">
			<div>
				<div class="flex items-center gap-1.5 text-red-600 font-bold text-xs uppercase tracking-wider mb-0.5">
					<span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
					تشكيلة مختارة
				</div>
				<h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">
					أحدث وأقوى المعدات
				</h2>
			</div>
			<a href="<?php echo esc_url($shop_url); ?>"
				class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-red-600 hover:text-red-700 transition-colors">
				<span>عرض الكل</span>
				<svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
				</svg>
			</a>
		</div>

		<!-- Products Grid -->
		<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">

			<?php if ($products_query->have_posts()): ?>
				<?php while ($products_query->have_posts()):
					$products_query->the_post(); ?>
					<?php get_template_part('template-parts/woocommerce/content-product-card'); ?>
				<?php endwhile;
				wp_reset_postdata(); ?>

			<?php else: ?>
				<!-- High-End Fallback Mock Cards -->

				<!-- Mock 1: ماكينة قص ليزر -->
				<div
					class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-3 sm:p-4 flex flex-col justify-between group relative">
					<div>
						<div
							class="relative block overflow-hidden rounded-xl bg-slate-50 aspect-square mb-2.5 flex items-center justify-center">
							<span
								class="absolute top-2 right-2 z-10 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
								خصم 8%
							</span>
							<div
								class="w-16 h-16 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242L10.758 10.758m0 0L7.879 7.879a3 3 0 00-4.242 4.242L6.515 15m4.243-4.242l4.242 4.242">
									</path>
								</svg>
							</div>
						</div>
						<span class="text-[10px] font-semibold text-slate-400 block mb-0.5">ماكينات قص</span>
						<h3
							class="font-bold text-slate-900 group-hover:text-red-600 transition-colors text-xs sm:text-sm line-clamp-2 mb-1.5 leading-snug">
							ماكينة قص سيراميك وبورسلين ليزر 120 سم سكة مزدوجة
						</h3>
					</div>
					<div class="pt-2.5 border-t border-slate-100 mt-2">
						<div class="flex items-center gap-1.5 mb-2.5">
							<span class="text-red-600 font-bold font-mono text-base sm:text-lg">8,450 ج.م</span>
							<span class="text-[10px] sm:text-xs text-slate-400 line-through font-mono">9,200 ج.م</span>
						</div>
						<a href="<?php echo esc_url($shop_url); ?>"
							class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
							</svg>
							<span>أضف للسلة</span>
						</a>
					</div>
				</div>

				<!-- Mock 2: ميزان ليزر -->
				<div
					class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-3 sm:p-4 flex flex-col justify-between group relative">
					<div>
						<div
							class="relative block overflow-hidden rounded-xl bg-slate-50 aspect-square mb-2.5 flex items-center justify-center">
							<span
								class="absolute top-2 right-2 z-10 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
								خصم 11%
							</span>
							<div
								class="w-16 h-16 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M12 4v1m0 14v1m8-8h-1M5 12H4m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z">
									</path>
								</svg>
							</div>
						</div>
						<span class="text-[10px] font-semibold text-slate-400 block mb-0.5">موازين ليزر</span>
						<h3
							class="font-bold text-slate-900 group-hover:text-red-600 transition-colors text-xs sm:text-sm line-clamp-2 mb-1.5 leading-snug">
							ميزان ليزر 16 خط 4D أخضر 360 درجة مع بطاريتين ليثيوم
						</h3>
					</div>
					<div class="pt-2.5 border-t border-slate-100 mt-2">
						<div class="flex items-center gap-1.5 mb-2.5">
							<span class="text-red-600 font-bold font-mono text-base sm:text-lg">3,200 ج.م</span>
							<span class="text-[10px] sm:text-xs text-slate-400 line-through font-mono">3,600 ج.م</span>
						</div>
						<a href="<?php echo esc_url($shop_url); ?>"
							class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
							</svg>
							<span>أضف للسلة</span>
						</a>
					</div>
				</div>

				<!-- Mock 3: كليبسات تسوية -->
				<div
					class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-3 sm:p-4 flex flex-col justify-between group relative">
					<div>
						<div
							class="relative block overflow-hidden rounded-xl bg-slate-50 aspect-square mb-2.5 flex items-center justify-center">
							<div
								class="w-16 h-16 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
								</svg>
							</div>
						</div>
						<span class="text-[10px] font-semibold text-slate-400 block mb-0.5">أدوات تسوية</span>
						<h3
							class="font-bold text-slate-900 group-hover:text-red-600 transition-colors text-xs sm:text-sm line-clamp-2 mb-1.5 leading-snug">
							طقم كليبسات تسوية البورسلين والسيراميك 500 قطعة عالي الجودة
						</h3>
					</div>
					<div class="pt-2.5 border-t border-slate-100 mt-2">
						<div class="flex items-center gap-1.5 mb-2.5">
							<span class="text-red-600 font-bold font-mono text-base sm:text-lg">450 ج.م</span>
						</div>
						<a href="<?php echo esc_url($shop_url); ?>"
							class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
							</svg>
							<span>أضف للسلة</span>
						</a>
					</div>
				</div>

				<!-- Mock 4: قرص قص -->
				<div
					class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all p-3 sm:p-4 flex flex-col justify-between group relative">
					<div>
						<div
							class="relative block overflow-hidden rounded-xl bg-slate-50 aspect-square mb-2.5 flex items-center justify-center">
							<span
								class="absolute top-2 right-2 z-10 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
								خصم 10%
							</span>
							<div
								class="w-16 h-16 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:scale-105 transition-transform">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
										d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
								</svg>
							</div>
						</div>
						<span class="text-[10px] font-semibold text-slate-400 block mb-0.5">أقراص وسكاكين</span>
						<h3
							class="font-bold text-slate-900 group-hover:text-red-600 transition-colors text-xs sm:text-sm line-clamp-2 mb-1.5 leading-snug">
							قرص قص بورسلين رفيع توربو 115 ملم فائق الدقة
						</h3>
					</div>
					<div class="pt-2.5 border-t border-slate-100 mt-2">
						<div class="flex items-center gap-1.5 mb-2.5">
							<span class="text-red-600 font-bold font-mono text-base sm:text-lg">380 ج.م</span>
							<span class="text-[10px] sm:text-xs text-slate-400 line-through font-mono">420 ج.م</span>
						</div>
						<a href="<?php echo esc_url($shop_url); ?>"
							class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-900 hover:bg-red-600 text-white py-2 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all shadow-sm">
							<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
							</svg>
							<span>أضف للسلة</span>
						</a>
					</div>
				</div>

			<?php endif; ?>

		</div>
	</section>


	<!-- =====================================================================
		 6. CRAFTSMEN & CONTRACTORS TESTIMONIALS (SOCIAL PROOF)
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-8">
		<div class="flex flex-col md:flex-row md:items-end justify-between mb-6 pb-3 border-b border-slate-200 gap-3">
			<div>
				<div
					class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 border border-red-100 text-red-600 text-xs font-bold mb-1.5">
					<svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
						<path
							d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
					</svg>
					<span>ثقة أكثر من 5,000 فني ومقاول في مصر</span>
				</div>
				<h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">
					آراء فنيي ومقاولي السيراميك والبورسلين
				</h2>
			</div>

			<div
				class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 bg-white px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-sm">
				<span class="flex text-amber-500">
					<?php for ($i = 0; $i < 5; $i++): ?>
						<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
							<path
								d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
					<?php endfor; ?>
				</span>
				<span class="font-bold text-slate-900 font-mono">4.9 / 5</span>
				<span class="text-slate-300">|</span>
				<span>تقييمات موثقة 100%</span>
			</div>
		</div>

		<!-- Testimonials Cards Grid -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-6">

			<!-- Review 1 -->
			<div
				class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-red-300 transition-all">
				<div class="space-y-3">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-1 text-amber-500">
							<?php for ($i = 0; $i < 5; $i++): ?>
								<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							<?php endfor; ?>
						</div>
						<span
							class="text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded-full border border-red-100">
							ماكينة قص شيجينج 120
						</span>
					</div>
					<p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
						"أمانة الشغل عندهم رقم واحد. طلبت ماكينة شيجينج 120 ليزر، المندوب قالي: <strong
							class="text-slate-900 font-bold">«افحص وقيس واختبر سلاح الألماظة قبل ما تدفع أي
							جنيه»</strong>. جربت السكة ولقيت القص مسطرة بدون أي كسر أو شرشرة."
					</p>
				</div>
				<div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between">
					<div class="flex items-center gap-2.5">
						<div
							class="w-8 h-8 rounded-xl bg-red-100 text-red-700 flex items-center justify-center font-bold text-xs">
							م
						</div>
						<div>
							<h4 class="font-bold text-slate-900 text-xs sm:text-sm">الأسطى محمد النجار</h4>
							<p class="text-[10px] text-slate-500">فني تركيب بورسلين - القاهرة</p>
						</div>
					</div>
					<span
						class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">مشتري
						موثق</span>
				</div>
			</div>

			<!-- Review 2 -->
			<div
				class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-red-300 transition-all">
				<div class="space-y-3">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-1 text-amber-500">
							<?php for ($i = 0; $i < 5; $i++): ?>
								<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							<?php endfor; ?>
						</div>
						<span
							class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
							طقم فاكيوم وميزان ليزر
						</span>
					</div>
					<p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
						"كشركة مقاولات بنشتغل في تشطيب فيلات ومحتاجين معدات تعيش وتتحمل ضغط الشغل المتواصل. طقم شفاطات
						الفاكيوم وميزان الليزر الـ 16 خط دقيق جداً بدون أي انحراف."
					</p>
				</div>
				<div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between">
					<div class="flex items-center gap-2.5">
						<div
							class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
							إ
						</div>
						<div>
							<h4 class="font-bold text-slate-900 text-xs sm:text-sm">م. إبراهيم عبد الحميد</h4>
							<p class="text-[10px] text-slate-500">مقاول تشطيبات - الإسكندرية</p>
						</div>
					</div>
					<span
						class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">مقاول
						معتمد</span>
				</div>
			</div>

			<!-- Review 3 -->
			<div
				class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200 shadow-sm flex flex-col justify-between hover:border-red-300 transition-all">
				<div class="space-y-3">
					<div class="flex items-center justify-between">
						<div class="flex items-center gap-1 text-amber-500">
							<?php for ($i = 0; $i < 5; $i++): ?>
								<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
									<path
										d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
								</svg>
							<?php endfor; ?>
						</div>
						<span
							class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
							قطع غيار Rubi أصلية
						</span>
					</div>
					<p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
						"أهم حاجة لأي صنايعي توفر قطع الغيار الأصلية. في أشرفكو لقيت كل لقم وأسلحة الروبي وسيجما الأصلية
						بضمان ومراكز صيانة واضحة ودعم سريع عبر الواتساب."
					</p>
				</div>
				<div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between">
					<div class="flex items-center gap-2.5">
						<div
							class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
							ح
						</div>
						<div>
							<h4 class="font-bold text-slate-900 text-xs sm:text-sm">الأسطى حسام الصعيدي</h4>
							<p class="text-[10px] text-slate-500">معلم سيراميك - الجيزة</p>
						</div>
					</div>
					<span
						class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">مشتري
						موثق</span>
				</div>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 7. WHATSAPP INSTANT CONSULTATION BANNER
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 mb-6">
		<div
			class="relative bg-gradient-to-r from-slate-900 via-slate-950 to-red-950 rounded-2xl border border-slate-800 p-5 sm:p-8 overflow-hidden shadow-xl">
			<!-- Background Radial Accents -->
			<div class="absolute -top-20 -left-20 w-60 h-60 bg-red-600/15 rounded-full blur-3xl pointer-events-none">
			</div>

			<div
				class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-5 text-center lg:text-right">
				<div class="max-w-2xl space-y-1.5">
					<div
						class="inline-flex items-center gap-1.5 text-emerald-400 text-xs font-bold uppercase tracking-wider">
						<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
						<span>استشارة فنية مجانية فورية</span>
					</div>
					<h3 class="text-lg sm:text-2xl font-extrabold text-white tracking-tight">
						محتاج استشارة لاختيار الماكينة المناسبة لمشروعك؟
					</h3>
					<p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
						فريقنا الهندسي المتخصص جاهز لمساعدتك في تحديد أنسب مقاسات ماكينات القص، دقة الليزر، ومعدات
						التركيب المناسبة لنوعية بلاطك.
					</p>
				</div>

				<div class="flex-shrink-0 w-full sm:w-auto">
					<a href="<?php echo esc_url($wa_consult_url); ?>" target="_blank" rel="noopener noreferrer"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-emerald-600/30 transition-all transform active:scale-95 text-xs sm:text-sm">
						<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
							<path
								d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
						</svg>
						<span>تواصل واتساب الآن</span>
					</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
