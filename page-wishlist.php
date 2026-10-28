<?php
/**
 * Template Name: المفضلة / قائمة الرغبات
 * Description: صفحة توجيهية للمفضلة السريعة (Side Drawer) لشركة أشرفكو.
 *
 * @package Ashrafco
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url       = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$wishlist_count = function_exists( 'test_theme_get_wishlist_count' ) ? (int) test_theme_get_wishlist_count() : 0;
?>

<main id="primary" class="site-main w-full min-h-[70vh] bg-slate-50 font-sans py-10 sm:py-16" dir="rtl">
	<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

		<!-- Wishlist Fallback Card -->
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 sm:p-12 text-center relative overflow-hidden">

			<!-- Decorative Background Glow -->
			<div class="absolute -top-16 -right-16 w-40 h-40 bg-red-50 rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
			<div class="absolute -bottom-16 -left-16 w-40 h-40 bg-slate-50 rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>

			<!-- Big Heart Icon -->
			<div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-red-50 text-[#d90429] flex items-center justify-center shadow-inner mb-6">
				<svg class="w-10 h-10 sm:w-12 sm:h-12 fill-[#d90429] stroke-[#d90429]" viewBox="0 0 24 24" stroke-width="1.8">
					<path stroke-linecap="round" stroke-linejoin="round"
						d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
				</svg>
				<?php if ( $wishlist_count > 0 ) : ?>
					<span class="header-wishlist-count-badge absolute -top-1 -right-1 bg-[#d90429] text-white text-xs font-black rounded-full min-w-[24px] h-[24px] px-1.5 flex items-center justify-center leading-none shadow-sm">
						<?php echo esc_html( $wishlist_count ); ?>
					</span>
				<?php endif; ?>
			</div>

			<!-- Title & Description -->
			<h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-3">
				<?php esc_html_e( 'قائمة المنتجات المفضلة', 'ashrafco' ); ?>
			</h1>

			<p class="text-sm sm:text-base text-slate-500 max-w-lg mx-auto leading-relaxed mb-6">
				<?php esc_html_e( 'تتم استعراض وإدارة منتجاتك المحفوظة عبر القائمة الجانبية السريعة لتسهيل حفظ ومقارنة ماكينات ومعدات أشرفكو للشراء السريع.', 'ashrafco' ); ?>
			</p>

			<?php if ( $wishlist_count > 0 ) : ?>
				<!-- Wishlist Quick Summary Pill -->
				<div class="inline-flex items-center gap-2 bg-[#fff1f2] border border-[#fecdd3]/60 px-5 py-2.5 rounded-2xl mb-8">
					<span class="text-xs sm:text-sm font-bold text-slate-700">
						<?php printf( esc_html__( 'لديك %s منتج محفوظ في المفضلة', 'ashrafco' ), '<span class="font-black text-[#d90429]">' . esc_html( $wishlist_count ) . '</span>' ); ?>
					</span>
				</div>
			<?php endif; ?>

			<!-- Action Buttons -->
			<div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md mx-auto">
				<!-- Primary Button: Open Wishlist Drawer -->
				<button type="button"
					id="wishlist-page-open-drawer"
					data-wishlist-drawer-trigger="true"
					class="ashrafco-wishlist-trigger w-full sm:flex-1 inline-flex items-center justify-center gap-2.5 bg-[#d90429] hover:bg-[#b90323] text-white font-extrabold text-sm sm:text-base py-3.5 px-6 rounded-2xl shadow-md hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer">
					<svg class="w-5 h-5 fill-white stroke-white" viewBox="0 0 24 24" stroke-width="1.8">
						<path stroke-linecap="round" stroke-linejoin="round"
							d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
					</svg>
					<span><?php esc_html_e( 'فتح قائمة المفضلة', 'ashrafco' ); ?></span>
				</button>

				<!-- Secondary Button: Browse Shop -->
				<a href="<?php echo esc_url( $shop_url ); ?>"
					class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 bg-[#1e293b] hover:bg-[#0f172a] text-white font-bold text-sm sm:text-base py-3.5 px-6 rounded-2xl shadow-xs transition-all active:scale-[0.98] no-underline">
					<span><?php esc_html_e( 'تصفح المنتجات', 'ashrafco' ); ?></span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
					</svg>
				</a>
			</div>

		</div>

	</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
	// Automatically trigger wishlist drawer when visiting /wishlist/ route
	setTimeout(function () {
		var trigger = document.getElementById('wishlist-page-open-drawer');
		if (trigger && !document.body.classList.contains('ashrafco-wishlist-open')) {
			trigger.click();
		}
	}, 150);
});
</script>

<?php
get_footer();
