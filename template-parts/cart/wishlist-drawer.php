<?php
/**
 * Modern Responsive Wishlist Drawer & Bottom Sheet Component
 *
 * Desktop Right Drawer (100dvh, 460px on right side) & Mobile Bottom Sheet (90dvh max, rounded-t, drag handle)
 * Matches Ashrafco Cart Drawer design language without quantity & cart totals,
 * displaying products saved in Wishlist with quick add-to-cart and remove actions.
 *
 * @package Ashrafco
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || exit;

$wishlist_count = function_exists( 'test_theme_get_wishlist_count' ) ? (int) test_theme_get_wishlist_count() : 0;
$shop_url       = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<!-- =========================================================================
     Ashrafco Wishlist Drawer & Bottom Sheet Component
     ========================================================================= -->
<div id="ashrafco-wishlist-drawer"
	class="ashrafco-wishlist-drawer fixed inset-0 z-[99999] pointer-events-none invisible opacity-0 select-none"
	dir="rtl"
	aria-hidden="true"
	role="region"
	aria-label="<?php esc_attr_e( 'قائمة المفضلة الجانبية', 'ashrafco' ); ?>">

	<!-- Backdrop Overlay -->
	<div id="ashrafco-wishlist-backdrop"
		class="ashrafco-wishlist-backdrop fixed inset-0 bg-slate-900/60 backdrop-blur-xs opacity-0 transition-opacity duration-300 cursor-pointer pointer-events-none"
		aria-hidden="true"></div>

	<!-- Drawer Panel (Desktop: Right sliding | Mobile: Bottom Sheet) -->
	<aside id="ashrafco-wishlist-panel"
		class="ashrafco-wishlist-panel fixed bg-white shadow-2xl flex flex-col pointer-events-auto transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] focus:outline-none"
		role="dialog"
		aria-modal="true"
		aria-labelledby="ashrafco-wishlist-title"
		tabindex="-1">

		<!-- Mobile Visual Drag Handle Pill (< 768px only) -->
		<div class="ashrafco-drag-handle-bar md:hidden flex justify-center pt-2.5 pb-1 cursor-grab" aria-hidden="true">
			<span class="w-12 h-1.5 bg-slate-300 rounded-full transition-transform active:scale-95"></span>
		</div>

		<!-- Wishlist Header (Matches Cart Header style with Heart Icon) -->
		<div class="ashrafco-wishlist-header px-5 py-3.5 sm:py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">

			<!-- Title & Red Heart Icon: Pinned to Right in RTL -->
			<div class="flex items-center gap-2">
				<!-- Red Heart Icon on the far right matching reference -->
				<div class="w-6 h-6 flex items-center justify-center text-[#d90429] flex-shrink-0" aria-hidden="true">
					<svg class="w-5 h-5 sm:w-6 sm:h-6 fill-[#d90429] stroke-[#d90429]" viewBox="0 0 24 24" stroke-width="1.8">
						<path stroke-linecap="round" stroke-linejoin="round"
							d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
					</svg>
				</div>

				<!-- Title with dynamic item count -->
				<h2 id="ashrafco-wishlist-title" class="text-base sm:text-lg font-black text-slate-900 m-0 tracking-tight flex items-center gap-1">
					<span>المفضلة</span>
					<span class="text-slate-800 font-bold">(<span class="ashrafco-wishlist-items-count"><?php echo esc_html( $wishlist_count ); ?></span>)</span>
				</h2>
			</div>

			<!-- Close Button: Pinned to Left in RTL (Close X icon) -->
			<button type="button"
				id="ashrafco-wishlist-close"
				class="ashrafco-wishlist-close-btn w-9 h-9 rounded-full text-slate-500 hover:text-slate-900 hover:bg-slate-100 flex items-center justify-center transition-colors focus:outline-none cursor-pointer"
				aria-label="<?php esc_attr_e( 'إغلاق المفضلة', 'ashrafco' ); ?>">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
					<path d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>

		</div>

		<!-- Scrollable Wishlist Products Body -->
		<div id="ashrafco-wishlist-body" class="ashrafco-wishlist-body flex-1 overflow-y-auto px-4 sm:px-6 py-2 overscroll-contain">
			<?php
			if ( function_exists( 'ashrafco_get_wishlist_drawer_items_html' ) ) {
				echo ashrafco_get_wishlist_drawer_items_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>

		<!-- Footer: Continue shopping or view full wishlist page -->
		<div id="ashrafco-wishlist-footer" class="ashrafco-wishlist-footer bg-white border-t border-slate-100 p-4 sm:p-5 flex-shrink-0 <?php echo ( 0 === $wishlist_count ) ? 'hidden' : ''; ?>">
			<?php
			if ( function_exists( 'ashrafco_get_wishlist_drawer_footer_html' ) ) {
				echo ashrafco_get_wishlist_drawer_footer_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>

	</aside>
</div>
