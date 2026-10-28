<?php
/**
 * Modern Industrial Cart Fallback Page Template
 *
 * Redirects user attention to the modern interactive Ashrafco Cart Drawer / Bottom Sheet.
 * Provides a fallback interface with quick action buttons:
 * - "فتح سلة المشتريات" (Opens Ashrafco Cart Drawer directly)
 * - "متابعة التسوق" (Returns to shop)
 *
 * @package Ashrafco
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || exit;

$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$cart_count   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$cart_total   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_subtotal() : '';
$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
?>

<div class="test-theme-cart-page max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16 font-sans text-center" dir="rtl">

	<!-- Cart Fallback Card -->
	<div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-8 sm:p-12 relative overflow-hidden">

		<!-- Decorative Background Glow -->
		<div class="absolute -top-16 -right-16 w-40 h-40 bg-red-50 rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>
		<div class="absolute -bottom-16 -left-16 w-40 h-40 bg-slate-50 rounded-full blur-2xl pointer-events-none" aria-hidden="true"></div>

		<!-- Big Cart Icon -->
		<div class="relative w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-red-50 text-primary flex items-center justify-center shadow-inner mb-6">
			<svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
				<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
				<line x1="3" y1="6" x2="21" y2="6"></line>
				<path d="M16 10a4 4 0 01-8 0"></path>
			</svg>
			<?php if ( $cart_count > 0 ) : ?>
				<span class="ashrafco-cart-count-badge absolute -top-1 -right-1 bg-primary text-white text-xs font-black rounded-full min-w-[24px] h-[24px] px-1.5 flex items-center justify-center leading-none shadow-sm">
					<?php echo esc_html( $cart_count ); ?>
				</span>
			<?php endif; ?>
		</div>

		<!-- Title & Description -->
		<h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-3">
			<?php esc_html_e( 'سلة المشتريات السريعة', 'ashrafco' ); ?>
		</h1>

		<p class="text-sm sm:text-base text-slate-500 max-w-lg mx-auto leading-relaxed mb-6">
			<?php esc_html_e( 'تتم إدارة ومراجعة سلة مشترياتك عبر القائمة الجانبية السريعة لتجربة تسوق سلسة وسريعة دون مغادرة الصفحة.', 'ashrafco' ); ?>
		</p>

		<?php if ( $cart_count > 0 && ! empty( $cart_total ) ) : ?>
			<!-- Cart Quick Summary Pill -->
			<div class="inline-flex items-center gap-3 bg-primary-light border border-rose-200/60 px-5 py-2.5 rounded-2xl mb-8">
				<span class="text-xs sm:text-sm font-bold text-slate-700">
					<?php printf( esc_html__( 'عدد المنتجات: %s منتج', 'ashrafco' ), '<span class="font-black text-primary">' . esc_html( $cart_count ) . '</span>' ); ?>
				</span>
				<span class="w-1 h-1 bg-slate-300 rounded-full"></span>
				<span class="text-xs sm:text-sm font-bold text-slate-700">
					<?php printf( esc_html__( 'المجموع: %s', 'ashrafco' ), '<span class="font-black text-primary font-mono">' . wp_kses_post( $cart_total ) . '</span>' ); ?>
				</span>
			</div>
		<?php endif; ?>

		<!-- Action Buttons -->
		<div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4 max-w-md mx-auto">
			<!-- Primary Button: Open Cart Drawer -->
			<button type="button"
				id="cart-page-open-drawer"
				data-cart-drawer-trigger="true"
				class="ashrafco-cart-trigger w-full sm:flex-1 inline-flex items-center justify-center gap-2.5 bg-primary hover:bg-primary-hover text-white font-extrabold text-sm sm:text-base py-3.5 px-6 rounded-2xl shadow-md hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
					<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<path d="M16 10a4 4 0 01-8 0"></path>
				</svg>
				<span><?php esc_html_e( 'فتح سلة المشتريات', 'ashrafco' ); ?></span>
			</button>

			<!-- Secondary Button: Continue Shopping -->
			<a href="<?php echo esc_url( $shop_url ); ?>"
				class="w-full sm:flex-1 inline-flex items-center justify-center gap-2 bg-secondary-light hover:bg-secondary text-white font-bold text-sm sm:text-base py-3.5 px-6 rounded-2xl shadow-xs transition-all active:scale-[0.98] no-underline">
				<span><?php esc_html_e( 'تصفح المتجر', 'ashrafco' ); ?></span>
				<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
				</svg>
			</a>
		</div>

	</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	// Automatically trigger cart drawer when visiting /cart/ route
	setTimeout(function () {
		var trigger = document.getElementById('cart-page-open-drawer');
		if (trigger && !document.body.classList.contains('ashrafco-cart-open')) {
			trigger.click();
		}
	}, 150);
});
</script>
