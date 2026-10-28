<?php
/**
 * Modern Industrial 404 Error Page Template for Ashrafco
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="primary" class="site-main w-full min-h-[75vh] flex items-center justify-center py-16 sm:py-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 font-sans" dir="rtl">

	<div class="max-w-2xl mx-auto px-4 sm:px-6 text-center">

		<!-- Visual: Industrial 404 Badge & Broken Tool Icon -->
		<div class="relative inline-flex flex-col items-center justify-center mb-8">
			<!-- Glow backdrop -->
			<div class="absolute -inset-4 bg-orange-500/10 rounded-full blur-2xl pointer-events-none"></div>

			<!-- Large Stylized 404 Badge -->
			<div class="relative bg-white border-2 border-orange-500/20 rounded-3xl p-6 sm:p-8 shadow-sm flex items-center gap-4 sm:gap-6">
				<span class="text-6xl sm:text-7xl md:text-8xl font-black text-slate-900 tracking-tight font-mono select-none">
					4<span class="text-primary">0</span>4
				</span>

				<div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-orange-50 text-primary border border-orange-200/80 flex items-center justify-center flex-shrink-0 shadow-inner">
					<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
					</svg>
				</div>
			</div>
		</div>

		<!-- Headings -->
		<div class="space-y-3 mb-8">
			<h1 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight">
				عذراً، الصفحة التي تبحث عنها غير موجودة!
			</h1>
			<p class="text-sm sm:text-base text-slate-500 max-w-lg mx-auto leading-relaxed">
				ربما تم نقل الصفحة أو أن الرابط غير صحيح. لا تدع عملك يتوقف، تصفح أحدث ماكينات ومعدات السيراميك والبورسلين الآن.
			</p>
		</div>

		<!-- Quick Product Search Bar -->
		<div class="max-w-md mx-auto mb-8">
			<form role="search" method="get" class="relative flex items-center shadow-xs" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input
					type="search"
					class="w-full pr-11 pl-28 py-3.5 bg-white border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition shadow-2xs"
					placeholder="ابحث عن ماكينة، ميزان ليزر، كليبسات..."
					value="<?php echo get_search_query(); ?>"
					name="s"
					autocomplete="off"
				/>
				<input type="hidden" name="post_type" value="product" />

				<div class="absolute right-4 text-slate-400 pointer-events-none">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
					</svg>
				</div>

				<button
					type="submit"
					class="absolute left-2 px-4 py-2 bg-slate-900 hover:bg-orange-600 text-white font-bold text-xs rounded-xl transition-all cursor-pointer select-none"
				>
					بحث
				</button>
			</form>
		</div>

		<!-- Action CTA Buttons -->
		<div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 mb-10">
			<!-- Primary CTA: Shop -->
			<a
				href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
				class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-primary hover:bg-orange-700 active:scale-[0.99] text-white font-bold py-3.5 px-8 rounded-2xl shadow-lg shadow-orange-600/25 transition-all text-sm select-none"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
				<span>العودة للمتجر الرئيسي</span>
			</a>

			<!-- Secondary CTA: Home -->
			<a
				href="<?php echo esc_url( home_url( '/' ) ); ?>"
				class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 active:scale-[0.99] border border-slate-200 text-slate-700 font-bold py-3.5 px-7 rounded-2xl text-sm transition-all shadow-2xs select-none"
			>
				<svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
				</svg>
				<span>الصفحة الرئيسية</span>
			</a>
		</div>

		<!-- Popular Category Chips -->
		<div class="pt-6 border-t border-slate-200/80">
			<span class="text-xs font-bold text-slate-400 block mb-3">أو انتقل مباشرة للأقسام الأكثر طلباً:</span>
			<div class="flex items-center justify-center gap-2 flex-wrap text-xs">
				<a
					href="<?php echo esc_url( home_url( '/?s=ماكينات%20قص&post_type=product' ) ); ?>"
					class="px-3.5 py-1.5 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-600 border border-slate-200 shadow-2xs transition-colors font-medium"
				>
					ماكينات قص يدوي
				</a>
				<a
					href="<?php echo esc_url( home_url( '/?s=ليزر&post_type=product' ) ); ?>"
					class="px-3.5 py-1.5 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-600 border border-slate-200 shadow-2xs transition-colors font-medium"
				>
					شواخص وموازين ليزر
				</a>
				<a
					href="<?php echo esc_url( home_url( '/?s=تسوية&post_type=product' ) ); ?>"
					class="px-3.5 py-1.5 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-600 border border-slate-200 shadow-2xs transition-colors font-medium"
				>
					أنظمة تسوية البلاط
				</a>
			</div>
		</div>

	</div>

</main>

<?php
get_footer();
