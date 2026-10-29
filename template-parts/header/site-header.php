<?php
/**
 * Header Template Part - Ben10 Timepieces
 *
 * Scoped strictly to reference images:
 * - Mobile (< 768px): Two rows with brand on right, hamburger on left, search + white wishlist pill below.
 * - Desktop (>= 768px): Single compact horizontal row with brand on far right, nav links, centered search, and utility icons (cart, wishlist, account) on left.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

$cart_count = (function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
$account_url = function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url('dashboard') : (function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url());
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
$offers_url = function_exists('ashrafco_get_offers_url') ? ashrafco_get_offers_url() : home_url('/offers/');

// Wishlist URL & Count
$wishlist_url = function_exists('test_theme_get_wishlist_url') ? test_theme_get_wishlist_url() : home_url('/wishlist/');
$wishlist_count = function_exists('test_theme_get_wishlist_count') ? test_theme_get_wishlist_count() : 0;
$display_cart_count = (!empty($cart_count)) ? (int) $cart_count : 0;
$display_wishlist_count = (int) $wishlist_count;

// Active page detection for desktop nav
$is_offers_active = is_page('offers') || is_page('promotions') || is_page_template('page-offers.php') || (isset($_GET['on_sale']) && '1' === (string) $_GET['on_sale']);
$is_shop_active   = ! $is_offers_active && function_exists('is_shop') && (is_shop() || is_product_taxonomy() || is_product());
$is_home_active   = ! $is_offers_active && ! $is_shop_active && (is_front_page() || (is_home() && !is_paged()));
?>

<header id="test-theme-site-header" class="w-full text-white shadow-sm select-none" dir="rtl">

	<!-- =================================================================
	     1. MOBILE HEADER: 2-Row Layout (< 768px only) - Matching Image 1
	     ================================================================= -->
	<div class="block md:hidden w-full">

		<!-- First Row: Brand on Right, Hamburger Menu on Far Left -->
		<div class="flex items-center justify-between w-full px-4 pt-3 pb-2.5" dir="rtl">
			<!-- Mobile Brand Logo: Ben10 Brand Identity -->
			<a href="<?php echo esc_url(home_url('/')); ?>"
				class="header-brand-link flex items-center text-white no-underline shrink-0 select-none py-1"
				aria-label="<?php echo esc_attr( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'en' ) : 'Ben10' ); ?>">
				<img
					src="<?php echo esc_url( function_exists( 'ashrafco_get_store_logo_url' ) ? ashrafco_get_store_logo_url() : ( get_stylesheet_directory_uri() . '/assets/images/ben10-logo.svg' ) ); ?>"
					alt="<?php echo esc_attr( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'en' ) : 'Ben10' ); ?>"
					width="140"
					height="32"
					class="ben10-header-logo h-8 w-auto max-h-[32px] object-contain shrink-0"
				/>
			</a>

			<!-- Left Side Actions: Notification Bell + Mobile Hamburger Button -->
			<div class="flex items-center gap-2.5 shrink-0">
				<?php
				if ( function_exists( 'test_theme_render_customer_notification_bell' ) ) {
					test_theme_render_customer_notification_bell( 'mobile' );
				}
				?>

				<!-- Mobile Hamburger Button on Far Left -->
				<button
					id="open-categories-drawer"
					type="button"
					class="mobile-hamburger-btn w-10 h-10 bg-[#161917] border border-white/10 rounded-2xl flex items-center justify-center text-white focus:outline-none focus:border-[#A3E635]/50 shrink-0 cursor-pointer shadow-sm hover:border-[#A3E635]/40 transition-colors"
					aria-label="تشكيلات المتجر">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
						<line x1="4" y1="7" x2="20" y2="7"></line>
						<line x1="4" y1="12" x2="20" y2="12"></line>
						<line x1="4" y1="17" x2="20" y2="17"></line>
					</svg>
				</button>
			</div>
		</div>

		<!-- Subtle Dark Green Horizontal Accent Divider -->
		<div class="w-full h-[1px] bg-[#244A27]/30"></div>

		<!-- Second Row: Wishlist Pill on Left & Full-Width Search on Right -->
		<div class="flex items-center gap-2.5 px-4 pt-2.5 pb-3 w-full" dir="rtl">
			<!-- Search Bar occupying remaining width -->
			<div class="relative flex-1" id="test-theme-search-wrapper">
				<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative w-full m-0" dir="rtl">
					<input type="hidden" name="post_type" value="product">

					<input type="search" id="test-theme-search-input" name="s"
						placeholder="ابحث عن ساعة، ماركة، أو تشكيلة ..."
						class="w-full h-11 bg-white text-slate-800 placeholder-slate-400 text-xs sm:text-sm rounded-2xl pr-10 pl-4 border-0 focus:outline-none focus:ring-2 focus:ring-[#A3E635]/50 shadow-sm"
						autocomplete="off"
						value="<?php echo get_search_query() ? esc_attr(get_search_query()) : ''; ?>" />

					<!-- Search Icon: Positioned on the RIGHT inside the input -->
					<span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none flex items-center justify-center">
						<svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</span>
				</form>

				<!-- Autocomplete Dropdown Results -->
				<div id="test-theme-search-dropdown"
					class="absolute top-full mt-2 w-full bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-100 overflow-hidden hidden z-50 max-h-96 overflow-y-auto">
					<div id="test-theme-search-results" class="p-2 divide-y divide-slate-100"></div>
				</div>
			</div>

			<!-- Mobile Wishlist Button (White Background, Clean Heart, Green Count Badge on Top-Right) -->
			<a href="<?php echo esc_url($wishlist_url); ?>" id="mobile-header-wishlist-btn"
				data-wishlist-drawer-trigger="true"
				class="mobile-header-wishlist-btn ashrafco-wishlist-trigger relative flex items-center justify-center w-11 h-11 bg-white rounded-2xl shadow-sm shrink-0 no-underline cursor-pointer"
				dir="ltr" aria-label="المفضلة" title="المفضلة"
				aria-controls="ashrafco-wishlist-drawer"
				aria-expanded="false"
				data-wishlist-count="<?php echo esc_attr($display_wishlist_count); ?>">
				<svg class="w-5 h-5 text-[#101211] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
				</svg>
				<span id="mobile-header-wishlist-badge"
					class="header-wishlist-count-badge absolute -top-1.5 -right-1.5 bg-[#A3E635] text-[#101211] text-[10px] font-black rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center leading-none shadow-sm select-none <?php echo $display_wishlist_count <= 0 ? 'hidden' : ''; ?>">
					<?php echo esc_html($display_wishlist_count); ?>
				</span>
			</a>
		</div>

	</div>


	<!-- =================================================================
	     2. DESKTOP HEADER: Single Horizontal Row (>= 768px)
	     ================================================================= -->
	<div class="hidden md:flex items-center justify-between w-full h-[62px] px-4 lg:px-8" dir="rtl">

		<!-- Right Side: Ben10 Brand Logo & Navigation Links -->
		<div class="flex items-center gap-5 lg:gap-8 shrink-0">
			<!-- Brand Lockup: Ben10 SVG Logo -->
			<a href="<?php echo esc_url(home_url('/')); ?>"
				class="header-brand-link flex items-center text-white no-underline shrink-0 select-none py-1"
				aria-label="<?php echo esc_attr( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'en' ) : 'Ben10' ); ?>">
				<img
					src="<?php echo esc_url( function_exists( 'ashrafco_get_store_logo_url' ) ? ashrafco_get_store_logo_url() : ( get_stylesheet_directory_uri() . '/assets/images/ben10-logo.svg' ) ); ?>"
					alt="<?php echo esc_attr( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'en' ) : 'Ben10' ); ?>"
					width="160"
					height="36"
					class="ben10-header-logo h-9 w-auto max-h-[36px] object-contain shrink-0"
				/>
			</a>

			<!-- Main Navigation Links: الرئيسية (active with green underline), المتجر, عروضنا -->
			<nav class="flex items-center gap-5 lg:gap-7 font-bold text-sm lg:text-base text-white" aria-label="التنقل الرئيسي">
				<a href="<?php echo esc_url(home_url('/')); ?>"
					class="relative py-1 text-white no-underline font-bold transition-colors <?php echo $is_home_active ? 'after:content-[\'\'] after:absolute after:-bottom-2.5 after:right-0 after:w-full after:h-[3px] after:bg-[#A3E635] after:rounded-full' : 'hover:text-[#A3E635]'; ?>">
					الرئيسية
				</a>

				<a href="<?php echo esc_url($shop_url); ?>"
					class="relative py-1 text-white no-underline font-bold transition-colors <?php echo $is_shop_active ? 'after:content-[\'\'] after:absolute after:-bottom-2.5 after:right-0 after:w-full after:h-[3px] after:bg-[#A3E635] after:rounded-full' : 'hover:text-[#A3E635]'; ?>">
					المتجر
				</a>

				<a href="<?php echo esc_url($offers_url); ?>"
					class="relative py-1 text-white no-underline font-bold transition-colors <?php echo $is_offers_active ? 'after:content-[\'\'] after:absolute after:-bottom-2.5 after:right-0 after:w-full after:h-[3px] after:bg-[#A3E635] after:rounded-full' : 'hover:text-[#A3E635]'; ?>">
					عروضنا
				</a>
			</nav>
		</div>

		<!-- Center Area: Wide Search Input with Search Icon on the Right -->
		<div class="relative flex-1 max-w-xl xl:max-w-2xl mx-4 lg:mx-8" id="desktop-search-wrapper">
			<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative w-full m-0" dir="rtl">
				<input type="hidden" name="post_type" value="product">

				<input type="search" id="desktop-theme-search-input" name="s"
					placeholder="ابحث عن ساعة، ماركة، أو تشكيلة ..."
					class="w-full h-11 bg-white text-slate-800 placeholder-slate-400 text-xs sm:text-sm rounded-2xl pr-10 pl-4 border-0 focus:outline-none focus:ring-2 focus:ring-[#A3E635]/50 shadow-sm transition-all"
					autocomplete="off"
					value="<?php echo get_search_query() ? esc_attr(get_search_query()) : ''; ?>" />

				<!-- Search Icon on the RIGHT inside the input -->
				<span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none flex items-center justify-center">
					<svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</span>
			</form>

			<!-- Autocomplete Dropdown Results -->
			<div id="desktop-search-dropdown"
				class="absolute top-full mt-2 w-full bg-white text-slate-800 rounded-xl shadow-2xl border border-slate-100 overflow-hidden hidden z-50 max-h-96 overflow-y-auto">
				<div id="desktop-search-results" class="p-2 divide-y divide-slate-100"></div>
			</div>
		</div>

		<!-- Left Area: Utility Action Icons (Cart, Wishlist, Account) -->
		<div class="flex items-center gap-5 lg:gap-7 shrink-0">

			<!-- 1. السلة (Cart Outline Icon + Green Count Badge + "السلة" Label) -->
			<a href="<?php echo esc_url($cart_url); ?>"
				id="header-cart-drawer-trigger"
				data-cart-drawer-trigger="true"
				class="ashrafco-cart-trigger desktop-header-action-item flex flex-col items-center justify-center text-white group hover:text-[#A3E635] transition-colors no-underline cursor-pointer select-none"
				aria-label="السلة"
				aria-controls="ashrafco-cart-drawer"
				aria-expanded="false">
				<div class="relative flex items-center justify-center">
					<svg class="w-6 h-6 text-white group-hover:text-[#A3E635] transition-colors" fill="none" stroke="currentColor" stroke-width="1.8"
						stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<circle cx="9" cy="20" r="1.5" />
						<circle cx="18" cy="20" r="1.5" />
						<path d="M3 4h2l2.3 9.2a2 2 0 002 1.5h8.9a2 2 0 001.9-1.4l1.8-6.3H6.1" />
					</svg>
					<span
						id="header-cart-count-badge"
						class="ashrafco-cart-count-badge absolute -top-1.5 -right-2 bg-[#A3E635] text-[#101211] text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs">
						<?php echo esc_html($display_cart_count); ?>
					</span>
				</div>
				<span class="mt-1 text-[11px] font-semibold text-white group-hover:text-[#A3E635] transition-colors leading-none">السلة</span>
			</a>

			<!-- 2. المفضلة (Desktop Wishlist: Standalone Heart Outline + Green Count Badge + "المفضلة" Label) -->
			<a href="<?php echo esc_url($wishlist_url); ?>"
				id="desktop-header-wishlist-btn"
				data-wishlist-drawer-trigger="true"
				class="ashrafco-wishlist-trigger desktop-header-action-item flex flex-col items-center justify-center text-white group hover:text-[#A3E635] transition-colors no-underline select-none cursor-pointer"
				aria-label="المفضلة"
				aria-controls="ashrafco-wishlist-drawer"
				aria-expanded="false"
				data-wishlist-count="<?php echo esc_attr($display_wishlist_count); ?>">
				<div class="relative flex items-center justify-center">
					<svg id="desktop-wishlist-icon" class="w-6 h-6 text-white group-hover:text-[#A3E635] transition-colors" fill="none" stroke="currentColor" stroke-width="1.8"
						stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<path
							d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
					</svg>
					<span
						id="desktop-header-wishlist-badge"
						class="header-wishlist-count-badge absolute -top-1.5 -right-2 bg-[#A3E635] text-[#101211] text-[10px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs <?php echo $display_wishlist_count <= 0 ? 'hidden' : ''; ?>">
						<?php echo esc_html($display_wishlist_count); ?>
					</span>
				</div>
				<span class="mt-1 text-[11px] font-semibold text-white group-hover:text-[#A3E635] transition-colors leading-none">المفضلة</span>
			</a>

			<!-- 2.5 الإشعارات (Customer Notifications Bell) -->
			<?php
			if ( function_exists( 'test_theme_render_customer_notification_bell' ) ) {
				test_theme_render_customer_notification_bell( 'desktop' );
			}
			?>

			<!-- 3. حسابي (User Icon + "حسابي" Label) -->
			<a href="<?php echo esc_url($account_url); ?>"
				class="desktop-header-action-item flex flex-col items-center justify-center text-white group hover:opacity-90 transition-opacity no-underline select-none cursor-pointer"
				aria-label="حسابي">
				<div class="relative flex items-center justify-center">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8"
						stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
						<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
						<circle cx="12" cy="7" r="4" />
					</svg>
				</div>
				<span class="mt-1 text-[11px] font-semibold text-white leading-none">حسابي</span>
			</a>

		</div>
	</div>

</header>