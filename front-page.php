<?php
/**
 * Modern Ben10 Watch-Store Front Page Template
 *
 * Presentation-layer transformation establishing Ben10 watch brand identity:
 * - High-impact watch hero introduction.
 * - Future watch collections visual category showcase.
 * - Verified watch product query handler (safe from legacy industrial catalog).
 * - Authentic, verified brand pillars and benefits.
 * - Ben10 craftsmanship showcase banner.
 * - Elegant store inquiry & discovery call to action.
 *
 * @package Ben10
 * @since 1.0.0
 * @version 4.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

// Dynamic Store & Contact Configuration
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : '201065712366';
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'Ben10';
$wa_inquiry_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً %s، أرغب بالاستفسار عن تشكيلات الساعات المتوفرة في المتجر.', $store_name));

get_header();
?>

<main id="primary" class="site-main w-full overflow-hidden bg-[#f8faf8] font-sans pb-24 sm:pb-20" dir="rtl">

	<!-- =====================================================================
		 1. BEN10 WATCH HERO SECTION (SMART SLIDER OR THEME PRESENTATION)
		 ===================================================================== -->
	<?php
	test_theme_render_hero_slider();
	?>

	<!-- =====================================================================
		 2. WATCH COLLECTION CATEGORIES
		 Visual category showcase prepared for future watch collections
		 ===================================================================== -->
	<?php
	$db_watch_categories = function_exists('ben10_get_watch_categories') ? ben10_get_watch_categories() : array();

	// Curated watch collection definitions for presentation
	$curated_collections = array(
		array(
			'name'        => 'الساعات الكلاسيكية',
			'tag'         => 'أناقة خالدة',
			'description' => 'تصاميم راقية تليق بالمناسبات الرسمية واليومية',
			'icon'        => 'classic',
			'slug'        => 'classic-watches',
		),
		array(
			'name'        => 'الساعات الرياضية',
			'tag'         => 'أداء ومتانة',
			'description' => 'هيكل قوي مقاوم يناسب نمط الحياة المليء بالحيوية',
			'icon'        => 'sport',
			'slug'        => 'sport-watches',
		),
		array(
			'name'        => 'ساعات الكرونوغراف',
			'tag'         => 'دقة هندسية',
			'description' => 'تعقيدات ميكانيكية دقيقة مع ميناء متعدد الوظائف',
			'icon'        => 'chrono',
			'slug'        => 'chronograph-watches',
		),
		array(
			'name'        => 'الإصدارات الخاصة',
			'tag'         => 'إصدارات محدودة',
			'description' => 'قطع حصرية تعكس التفرد والذوق الرفيع',
			'icon'        => 'limited',
			'slug'        => 'limited-edition',
		),
	);
	?>

	<section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" aria-label="<?php esc_attr_e('تشكيلات الساعات', 'ashrafco'); ?>">
		<!-- Section Header -->
		<div class="flex flex-col sm:flex-row sm:items-end justify-between mb-5 sm:mb-7 gap-3" dir="rtl">
			<div>
				<div class="inline-flex items-center gap-1.5 text-[#244A27] font-bold text-xs uppercase tracking-wider mb-1">
					<span class="w-2 h-2 rounded-full bg-[#A3E635]"></span>
					<span>تصفح المجموعات</span>
				</div>
				<h2 class="text-xl sm:text-2xl md:text-3xl font-black text-slate-900 tracking-tight m-0 leading-tight">
					تشكيلات ساعات Ben10
				</h2>
			</div>

			<a href="<?php echo esc_url($shop_url); ?>"
				class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#101211] hover:text-[#244A27] bg-[#A3E635]/20 hover:bg-[#A3E635]/40 px-3.5 py-1.5 rounded-full transition-all group select-none self-start sm:self-auto"
				title="<?php esc_attr_e('زيارة المتجر لتصفح كافة الساعات', 'ashrafco'); ?>">
				<span>عرض كافة التشكيلات</span>
				<svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1" fill="none"
					stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
				</svg>
			</a>
		</div>

		<!-- Categories Grid -->
		<div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 sm:gap-5" dir="rtl">
			<?php
			foreach ($curated_collections as $col):
				// Determine if this category exists in WooCommerce
				$matching_cat = null;
				foreach ($db_watch_categories as $dbc) {
					if ($dbc['slug'] === $col['slug'] || false !== mb_stripos($dbc['name'], $col['name'])) {
						$matching_cat = $dbc;
						break;
					}
				}
				$is_linked = !empty($matching_cat);
				$card_tag = $is_linked ? 'a' : 'div';
				$card_attrs = $is_linked ? 'href="' . esc_url($matching_cat['link']) . '" title="' . esc_attr($col['name']) . '"' : '';
			?>
				<<?php echo $card_tag; ?> <?php echo $card_attrs; ?>
					class="collection-card relative flex flex-col justify-between bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-[#A3E635] hover:-translate-y-1 transition-all duration-300 group select-none">
					
					<!-- Top Row: Icon & Tag -->
					<div class="flex items-start justify-between mb-4">
						<div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-[#244A27]/10 text-[#244A27] group-hover:bg-[#101211] group-hover:text-[#A3E635] flex items-center justify-center transition-colors duration-300">
							<?php if ('classic' === $col['icon']): ?>
								<!-- Classic Watch Dial Icon -->
								<svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<circle cx="12" cy="12" r="9" />
									<polyline points="12 7 12 12 15 14" />
									<path d="M9 3h6M9 21h6" stroke-linecap="round" />
								</svg>
							<?php elseif ('sport' === $col['icon']): ?>
								<!-- Sport Bezel Watch Icon -->
								<svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<circle cx="12" cy="12" r="9" />
									<circle cx="12" cy="12" r="6" stroke-dasharray="2 2" />
									<path d="M12 9v3l2 1" />
								</svg>
							<?php elseif ('chrono' === $col['icon']): ?>
								<!-- Chronograph Subdials Icon -->
								<svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<circle cx="12" cy="12" r="9" />
									<circle cx="12" cy="9" r="2.5" />
									<circle cx="9" cy="14" r="2" />
									<circle cx="15" cy="14" r="2" />
								</svg>
							<?php else: ?>
								<!-- Limited Edition Diamond/Star Icon -->
								<svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
								</svg>
							<?php endif; ?>
						</div>

						<span class="text-[10px] sm:text-xs font-bold px-2.5 py-1 rounded-full <?php echo $is_linked ? 'bg-[#244A27]/10 text-[#244A27]' : 'bg-slate-100 text-slate-500'; ?>">
							<?php echo $is_linked ? esc_html($col['tag']) : 'قريباً'; ?>
						</span>
					</div>

					<!-- Bottom: Name & Description -->
					<div>
						<h3 class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-[#244A27] transition-colors mb-1 leading-snug">
							<?php echo esc_html($col['name']); ?>
						</h3>
						<p class="text-[11px] sm:text-xs text-slate-500 leading-relaxed m-0 line-clamp-2">
							<?php echo esc_html($col['description']); ?>
						</p>
					</div>
				</<?php echo $card_tag; ?>>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- =====================================================================
		 3. FEATURED WATCHES (CATALOG-SAFE)
		 Displays actual watch products only when verified watch inventory exists.
		 Suppresses legacy industrial products.
		 ===================================================================== -->
	<?php
	$featured_watches_query = function_exists('ben10_get_watch_products')
		? ben10_get_watch_products(array('limit' => 8, 'orderby' => 'date'))
		: new WP_Query(array('post__in' => array(0)));
	?>

	<?php if ($featured_watches_query->have_posts()): ?>
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" aria-label="<?php esc_attr_e('ساعات مختارة', 'ashrafco'); ?>">
			<!-- Section Header -->
			<div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-200">
				<div>
					<div class="flex items-center gap-1.5 text-[#244A27] font-bold text-xs uppercase tracking-wider mb-0.5">
						<span class="w-2 h-2 rounded-full bg-[#A3E635]"></span>
						ساعات مختارة
					</div>
					<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight m-0">
						أحدث تصاميم Ben10
					</h2>
				</div>
				<a href="<?php echo esc_url($shop_url); ?>"
					class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-[#101211] hover:text-[#244A27] transition-colors">
					<span>عرض الكل</span>
					<svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>

			<!-- Products Grid -->
			<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-5">
				<?php while ($featured_watches_query->have_posts()):
					$featured_watches_query->the_post(); ?>
					<?php get_template_part('template-parts/woocommerce/content-product-card'); ?>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</div>
		</section>
	<?php else: ?>
		<!-- Safe Brand Curation Notice (While Watch Inventory Is Being Curated) -->
		<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6" aria-label="<?php esc_attr_e('ترقبوا الإطلاق', 'ashrafco'); ?>">
			<div class="relative bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6" dir="rtl">
				<div class="flex items-center gap-4 text-center sm:text-right">
					<div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-[#244A27]/10 text-[#244A27] flex items-center justify-center shrink-0 mx-auto sm:mx-0">
						<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
							<circle cx="12" cy="12" r="9" />
							<polyline points="12 6 12 12 16 14" />
						</svg>
					</div>
					<div>
						<span class="inline-block text-[11px] font-bold text-[#244A27] bg-[#A3E635]/25 px-2.5 py-0.5 rounded-full mb-1">
							ترقبوا تشكيلاتنا قريباً
						</span>
						<h3 class="text-base sm:text-xl font-black text-slate-900 m-0">
							تشكيلة ساعات Ben10 الحصرية قيد التجهيز
						</h3>
						<p class="text-xs sm:text-sm text-slate-500 m-0 mt-1 leading-relaxed max-w-xl">
							نعمل على إطلاق باقة استثنائية من الساعات التي تجمع بين الأناقة العصرية ودقة الصنع.
						</p>
					</div>
				</div>

				<div class="shrink-0 w-full sm:w-auto">
					<a href="<?php echo esc_url($shop_url); ?>"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#101211] hover:bg-[#244A27] text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-xl transition-colors shadow-xs">
						<span>تصفح المتجر</span>
						<svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
						</svg>
					</a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- =====================================================================
		 4. BRAND BENEFITS (VERIFIED BRAND PILLARS)
		 Honest, verified brand values without invented promises
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8" aria-label="<?php esc_attr_e('مميزات Ben10', 'ashrafco'); ?>">
		<div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 lg:p-10 shadow-xs" dir="rtl">
			<div class="text-center max-w-2xl mx-auto mb-8">
				<span class="text-xs font-bold text-[#244A27] uppercase tracking-wider bg-[#A3E635]/20 px-3 py-1 rounded-full">
					قيم علامتنا
				</span>
				<h2 class="text-xl sm:text-2xl md:text-3xl font-black text-slate-900 tracking-tight mt-2 mb-2">
					لماذا تختار ساعات Ben10؟
				</h2>
				<p class="text-xs sm:text-sm text-slate-500 leading-relaxed m-0">
					نحرص في Ben10 على تقديم ساعات متميزة تلهم ثقتك وترافقك في كافة تفاصيل يومك.
				</p>
			</div>

			<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
				<!-- Benefit 1 -->
				<div class="flex flex-col items-center sm:items-start text-center sm:text-right p-4 rounded-2xl bg-slate-50/60 border border-slate-100 hover:border-[#A3E635] transition-all">
					<div class="w-12 h-12 rounded-xl bg-[#244A27]/10 text-[#244A27] flex items-center justify-center mb-3">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
						</svg>
					</div>
					<h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1">تصميم عصري متفرد</h3>
					<p class="text-xs text-slate-500 leading-relaxed m-0">
						ساعات تجمع بين متانة الصناعة وأناقة المظهر لتناسب إطلالتك اليومية والرسمية.
					</p>
				</div>

				<!-- Benefit 2 -->
				<div class="flex flex-col items-center sm:items-start text-center sm:text-right p-4 rounded-2xl bg-slate-50/60 border border-slate-100 hover:border-[#A3E635] transition-all">
					<div class="w-12 h-12 rounded-xl bg-[#244A27]/10 text-[#244A27] flex items-center justify-center mb-3">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
						</svg>
					</div>
					<h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1">دقة واهتمام بالتفاصيل</h3>
					<p class="text-xs text-slate-500 leading-relaxed m-0">
						عناية فائقة بتفاصيل الميناء والمؤشرات ومكونات الساعة لضمان المتانة والأناقة.
					</p>
				</div>

				<!-- Benefit 3 -->
				<div class="flex flex-col items-center sm:items-start text-center sm:text-right p-4 rounded-2xl bg-slate-50/60 border border-slate-100 hover:border-[#A3E635] transition-all">
					<div class="w-12 h-12 rounded-xl bg-[#244A27]/10 text-[#244A27] flex items-center justify-center mb-3">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
					<h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1">حضور يعكس تميزك</h3>
					<p class="text-xs text-slate-500 leading-relaxed m-0">
						تشكيلات متعددة تمنحك الثقة في كل مناسبة وتبرز ذوقك الشخصي الرفيع.
					</p>
				</div>

				<!-- Benefit 4 -->
				<div class="flex flex-col items-center sm:items-start text-center sm:text-right p-4 rounded-2xl bg-slate-50/60 border border-slate-100 hover:border-[#A3E635] transition-all">
					<div class="w-12 h-12 rounded-xl bg-[#244A27]/10 text-[#244A27] flex items-center justify-center mb-3">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
						</svg>
					</div>
					<h3 class="text-sm sm:text-base font-bold text-slate-900 mb-1">تواصل ودعم مباشر</h3>
					<p class="text-xs text-slate-500 leading-relaxed m-0">
						فريق خدمة عملاء مخصص للإجابة على استفساراتك ومساعدتك في اختيار ساعتك المثالية.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- =====================================================================
		 5. PROMOTIONAL / BRAND CRAFTSMANSHIP SHOWCASE
		 Ben10 watch aesthetics without fake discounts or invented deadlines
		 ===================================================================== -->
	<section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6" aria-label="<?php esc_attr_e('فلسفة Ben10 للساعات', 'ashrafco'); ?>">
		<div class="relative w-full rounded-2xl sm:rounded-3xl bg-[#101211] border border-[#244A27]/40 shadow-xl overflow-hidden">
			<!-- Subtle Accent Glow -->
			<div class="absolute -top-20 -left-20 w-60 h-60 bg-[#A3E635]/10 rounded-full blur-3xl pointer-events-none"></div>

			<div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-6 sm:gap-8 p-6 sm:p-10 lg:p-12 relative z-10" dir="rtl">
				<!-- Text Side -->
				<div class="lg:col-span-7 flex flex-col items-start text-right">
					<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#244A27]/50 border border-[#A3E635]/30 text-[#A3E635] text-xs font-bold mb-3">
						<span>فلسفة التصميم لدى Ben10</span>
					</div>

					<h2 class="text-xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight mb-3 m-0">
						إتقان في التصميم.. وهيبة في الحضور
					</h2>

					<p class="text-xs sm:text-base text-slate-300 font-normal leading-relaxed mb-6 max-w-xl m-0">
						نؤمن بأن الساعة ليست مجرد أداة لمعرفة الوقت، بل هي تعبير عن الذوق والانضباط والأناقة الشخصية. صُممت كل قطعة لتعكس التميز في كل لحظة.
					</p>

					<div class="flex flex-wrap items-center gap-4">
						<a href="<?php echo esc_url($shop_url); ?>"
							class="inline-flex items-center justify-center gap-2 bg-[#A3E635] hover:bg-[#8ce025] text-[#101211] font-extrabold text-xs sm:text-sm px-6 py-3 rounded-xl shadow-md transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
							<span>استكشف المتجر</span>
							<svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
							</svg>
						</a>
					</div>
				</div>

				<!-- Macro Visual Side -->
				<div class="lg:col-span-5 w-full flex items-center justify-center">
					<div class="relative w-full aspect-[16/10] sm:aspect-[16/9] lg:aspect-square max-h-[300px] lg:max-h-[340px] rounded-2xl overflow-hidden border border-white/10 shadow-xl group">
						<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/ben10-promo-watch.jpg'); ?>"
							alt="<?php esc_attr_e('دقة وحرفية ساعات Ben10', 'ashrafco'); ?>"
							width="700" height="400"
							class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
							loading="lazy" decoding="async">
						<div class="absolute inset-0 bg-gradient-to-t from-[#101211]/50 via-transparent to-transparent pointer-events-none"></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- =====================================================================
		 6. FINAL CALL TO ACTION & CONSULTATION
		 Helpful, brand-consistent customer support & exploration
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" aria-label="<?php esc_attr_e('تواصل مع Ben10', 'ashrafco'); ?>">
		<div class="relative bg-gradient-to-r from-[#101211] via-[#141a14] to-[#101211] rounded-2xl sm:rounded-3xl border border-[#244A27]/40 p-6 sm:p-10 overflow-hidden shadow-xl" dir="rtl">
			<!-- Background Radial Accent -->
			<div class="absolute -top-24 -left-24 w-64 h-64 bg-[#244A27]/30 rounded-full blur-3xl pointer-events-none"></div>

			<div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 text-center lg:text-right">
				<div class="max-w-2xl space-y-2">
					<div class="inline-flex items-center gap-1.5 text-[#A3E635] text-xs font-bold uppercase tracking-wider">
						<span class="w-2 h-2 rounded-full bg-[#A3E635] animate-pulse"></span>
						<span>خدمة العملاء والاستفسارات</span>
					</div>
					<h3 class="text-xl sm:text-2xl font-black text-white tracking-tight m-0">
						هل تبحث عن ساعة تناسب إطلالتك الخاصة؟
					</h3>
					<p class="text-slate-300 text-xs sm:text-sm leading-relaxed m-0">
						فريق Ben10 متواجد لمساعدتك في اختيار التشكيلة الأنسب لذوقك وتفضيلاتك الشخصية.
					</p>
				</div>

				<div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto shrink-0">
					<a href="<?php echo esc_url($shop_url); ?>"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#A3E635] hover:bg-[#8ce025] text-[#101211] font-bold py-3 px-6 rounded-xl shadow-md transition-all transform active:scale-95 text-xs sm:text-sm">
						<span>زيارة المتجر</span>
						<svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
						</svg>
					</a>

					<a href="<?php echo esc_url($wa_inquiry_url); ?>" target="_blank" rel="noopener noreferrer"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#244A27] hover:bg-[#1f3f22] border border-[#A3E635]/30 text-white font-bold py-3 px-6 rounded-xl shadow-md transition-all transform active:scale-95 text-xs sm:text-sm">
						<svg class="w-4 h-4 fill-current text-[#A3E635]" viewBox="0 0 24 24" aria-hidden="true">
							<path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
						</svg>
						<span>تواصل عبر واتساب</span>
					</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
