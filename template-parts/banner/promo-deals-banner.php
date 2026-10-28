<?php
/**
 * Dynamic Promo Deals Banner with dynamic text overlay
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="w-full px-4 py-3 sm:py-5">
	<div
		class="relative w-full aspect-[16/5] sm:aspect-[21/6] max-h-[160px] sm:max-h-[220px] rounded-3xl overflow-hidden shadow-sm bg-[#fee2e2]">
		<!-- The Raw Background Image -->
		<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/promo-deals-bg.webp'); ?>"
			alt="عروض خاصة" class="absolute inset-0 w-full h-full object-cover z-0" loading="lazy" decoding="async" />

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
				<a href="<?php echo esc_url(home_url('/deals')); ?>"
					class="mt-2.5 sm:mt-4 bg-[#d90429] hover:bg-red-700 text-white text-[11px] sm:text-xs font-bold px-4 py-2.5 rounded-full shadow-sm flex items-center gap-1 transition-transform active:scale-95">
					<span>عرض العروض</span>
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
			</div>

			<!-- Left Side (Floating badge over the Tools) -->
			<div
				class="absolute top-4 sm:top-6 left-12 sm:left-24 bg-[#d90429] text-white rounded-xl px-2.5 py-1.5 sm:py-2 flex flex-col items-center justify-center text-center shadow-md select-none transform -rotate-3 hover:rotate-0 transition-transform duration-200">
				<span class="text-[9px] sm:text-[10px] uppercase font-bold tracking-wider opacity-90">خصم حتى</span>
				<span class="text-base sm:text-2xl font-black leading-none">50%</span>
			</div>
		</div>
	</div>
</div>