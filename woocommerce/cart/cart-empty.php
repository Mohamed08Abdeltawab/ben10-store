<?php
/**
 * Modern Minimalist Empty Cart Page Template Override for Ashrafco
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 8.0.0
 */

defined( 'ABSPATH' ) || exit;

// Clear any notices (such as "Cart is empty" or "Item removed") so they do not appear
if ( function_exists( 'wc_clear_notices' ) ) {
	wc_clear_notices();
}

// Remove default WooCommerce empty cart message
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
?>

<div class="ashrafco-empty-cart max-w-lg mx-auto px-4 py-16 sm:py-24 text-center font-sans" dir="rtl">

	<?php
	/**
	 * Hook: woocommerce_cart_is_empty.
	 */
	do_action( 'woocommerce_cart_is_empty' );
	?>

	<!-- Minimal Card Container -->
	<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 sm:p-12 flex flex-col items-center">

		<!-- Minimal Cart Icon in soft circle -->
		<div class="w-20 h-20 rounded-full bg-rose-50 text-red-600 flex items-center justify-center mb-6">
			<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
			</svg>
		</div>

		<!-- Title & Minimal Subtitle -->
		<h2 class="text-xl sm:text-2xl font-black text-slate-900 mb-2 tracking-tight">
			سلة التسوق فارغة
		</h2>
		<p class="text-sm text-slate-400 font-medium mb-8">
			لم تقم بإضافة أي منتجات إلى سلتك بعد
		</p>

		<!-- Action Button -->
		<a
			href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
			style="color: #ffffff !important; text-decoration: none !important;"
			class="w-full sm:w-auto min-w-[200px] inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl text-sm font-black !text-white bg-red-600 hover:bg-red-700 hover:!text-white visited:!text-white active:scale-[0.99] transition-all shadow-md shadow-red-600/20 cursor-pointer select-none no-underline"
		>
			<span style="color: #ffffff !important;" class="!text-white">تصفح المتجر</span>
			<svg class="w-4 h-4 rotate-180 text-white" style="color: #ffffff !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
			</svg>
		</a>

	</div>

</div>
