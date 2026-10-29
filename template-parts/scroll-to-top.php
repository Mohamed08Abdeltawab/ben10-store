<?php
/**
 * Custom Scroll To Top Component for Ashrafco Theme
 *
 * Replaces and overrides the default Astra scroll-to-top element.
 * Perfectly centered arrow icon, Ashrafco brand orange, rounded-full,
 * and positioned above the mobile bottom navigation bar.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent duplicate rendering if called by multiple hooks.
static $ashrafco_scroll_rendered = false;
if ( $ashrafco_scroll_rendered ) {
	return;
}
$ashrafco_scroll_rendered = true;
?>

<!-- Ben10 Premium Floating Back To Top Button (52px diameter, #A3E635, Green Lume Glow) -->
<button
	id="ast-scroll-top"
	type="button"
	tabindex="0"
	aria-label="<?php esc_attr_e( 'العودة إلى أعلى الصفحة', 'custom-store-boilerplate' ); ?>"
	class="test-theme-scroll-top as-scroll-top-btn ast-scroll-top-icon ast-scroll-to-top-right fixed z-40 right-4 sm:right-6 bottom-[calc(4.75rem+env(safe-area-inset-bottom,0px))] md:bottom-6 w-[52px] h-[52px] rounded-full bg-[#A3E635] hover:bg-[#8ce025] text-[#101211] shadow-[0_4px_20px_rgba(163,230,53,0.35)] hover:shadow-[0_6px_24px_rgba(163,230,53,0.55)] flex items-center justify-center p-0 transition-all duration-300 transform scale-90 opacity-0 pointer-events-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#A3E635] focus:ring-offset-2 focus:ring-offset-[#101211] select-none group"
>
	<!-- Clean Upward Arrow SVG (24px) -->
	<svg
		class="w-6 h-6 text-[#101211] flex-shrink-0 transition-transform duration-200 group-hover:-translate-y-0.5"
		fill="none"
		stroke="currentColor"
		viewBox="0 0 24 24"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
	>
		<path
			stroke-linecap="round"
			stroke-linejoin="round"
			stroke-width="2.5"
			d="M5 15l7-7 7 7"
		/>
	</svg>
	<span class="screen-reader-text"><?php esc_html_e( 'العودة إلى أعلى الصفحة', 'custom-store-boilerplate' ); ?></span>
</button>
