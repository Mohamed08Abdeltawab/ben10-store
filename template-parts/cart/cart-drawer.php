<?php
/**
 * Modern Responsive Shopping Cart Drawer & Bottom Sheet Component
 *
 * Implements Desktop Left Drawer (100dvh, 460px) & Mobile Bottom Sheet (90dvh max, rounded-t, drag handle)
 * Matching reference specification for Ashrafco Store (A&S).
 *
 * @package Ashrafco
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
	return;
}

$cart_count = WC()->cart->get_cart_contents_count();
$is_empty   = WC()->cart->is_empty();
?>

<!-- =========================================================================
     Ashrafco Cart Drawer & Bottom Sheet Component
     ========================================================================= -->
<div id="ashrafco-cart-drawer"
	class="ashrafco-cart-drawer fixed inset-0 z-[99999] pointer-events-none invisible opacity-0 select-none"
	dir="rtl"
	aria-hidden="true"
	role="region"
	aria-label="<?php esc_attr_e( 'سلة المشتريات الجانبية', 'ashrafco' ); ?>">

	<!-- Backdrop Overlay -->
	<div id="ashrafco-cart-backdrop"
		class="ashrafco-cart-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-xs opacity-0 transition-opacity duration-300 cursor-pointer pointer-events-none"
		aria-hidden="true"></div>

	<!-- Drawer Panel (Desktop: Left sliding | Mobile: Bottom Sheet) -->
	<aside id="ashrafco-cart-panel"
		class="ashrafco-cart-panel fixed bg-white shadow-2xl flex flex-col pointer-events-auto transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] focus:outline-none"
		role="dialog"
		aria-modal="true"
		aria-labelledby="ashrafco-cart-title"
		tabindex="-1">

		<!-- Mobile Visual Drag Handle Pill (< 768px only) -->
		<div class="ashrafco-drag-handle-bar md:hidden flex justify-center pt-2.5 pb-1 cursor-grab" aria-hidden="true">
			<span class="w-12 h-1.5 bg-slate-300 rounded-full transition-transform active:scale-95"></span>
		</div>

		<!-- Cart Header (Strictly matching reference screenshot) -->
		<div class="ashrafco-cart-header px-5 py-3.5 sm:py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">

			<!-- Title & Red Bag Icon: Pinned to Right in RTL -->
			<div class="flex items-center gap-2">
				<!-- Red Shopping Bag Icon on the far right matching reference -->
				<div class="w-6 h-6 flex items-center justify-center text-[#d90429] flex-shrink-0" aria-hidden="true">
					<svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
						<path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<path d="M16 10a4 4 0 01-8 0"></path>
					</svg>
				</div>

				<!-- Title with dynamic item count -->
				<h2 id="ashrafco-cart-title" class="text-base sm:text-lg font-black text-slate-900 m-0 tracking-tight flex items-center gap-1">
					<span>عربة التسوق</span>
					<span class="text-slate-800 font-bold">(<span class="ashrafco-cart-items-count"><?php echo esc_html( $cart_count ); ?></span>)</span>
				</h2>
			</div>

			<!-- Close Button: Pinned to Left in RTL (Close X icon) -->
			<button type="button"
				id="ashrafco-cart-close"
				class="ashrafco-cart-close-btn w-9 h-9 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition-colors focus:outline-none cursor-pointer"
				aria-label="<?php esc_attr_e( 'إغلاق عربة التسوق', 'ashrafco' ); ?>">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
					<path d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>

		</div>

		<!-- Scrollable Cart Products Body -->
		<div id="ashrafco-cart-body" class="ashrafco-cart-body flex-1 overflow-y-auto px-4 sm:px-6 py-2 overscroll-contain">
			<?php echo ashrafco_get_cart_drawer_items_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<!-- Sticky Summary & Actions Footer (Remains fixed at bottom while scrolling items) -->
		<div id="ashrafco-cart-footer" class="ashrafco-cart-footer bg-white border-t border-slate-100 p-4 sm:p-5 flex-shrink-0 <?php echo $is_empty ? 'hidden' : ''; ?>">
			<?php echo ashrafco_get_cart_drawer_footer_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

	</aside>
</div>
