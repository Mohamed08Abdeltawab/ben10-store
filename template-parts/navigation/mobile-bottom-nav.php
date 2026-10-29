<?php
/**
 * Noon-Style Mobile Bottom Navigation Bar (Fixed Bottom Shell) & Categories Drawer
 *
 * Scoped strictly for mobile viewports (< 768px).
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

// Prevent rendering inside WP Admin
if ( is_admin() ) {
	return;
}

// 1. Determine active page flags
$is_offers_active     = is_page( 'offers' ) || is_page( 'promotions' ) || is_page_template( 'page-offers.php' ) || ( isset( $_GET['on_sale'] ) && '1' === (string) $_GET['on_sale'] );
$is_home_active       = ! $is_offers_active && ( is_front_page() || ( is_home() && ! is_paged() ) );
$is_categories_active = ! $is_offers_active && (
	( function_exists( 'is_shop' ) && is_shop() )
	|| ( function_exists( 'is_product_category' ) && is_product_category() )
	|| ( function_exists( 'is_product_tag' ) && is_product_tag() )
);
$is_cart_active       = function_exists( 'is_cart' ) && is_cart();
$is_account_active    = function_exists( 'is_account_page' ) && is_account_page();

// 2. URLs
$home_url    = home_url( '/' );
$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$offers_url  = function_exists( 'ashrafco_get_offers_url' ) ? ashrafco_get_offers_url() : home_url( '/offers/' );
$account_url = function_exists( 'wc_get_account_endpoint_url' )
	? wc_get_account_endpoint_url( 'dashboard' )
	: ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() );

// 3. Cart live item count
$cart_count = 0;
if ( function_exists( 'WC' ) && WC()->cart ) {
	$cart_count = WC()->cart->get_cart_contents_count();
}

// 4. Query top product categories for mobile drawer
$product_categories = array();
if ( taxonomy_exists( 'product_cat' ) ) {
	$uncat_id = (int) get_option( 'default_product_cat', 0 );
	$product_categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'exclude'    => array( $uncat_id ),
		'number'     => 8,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
}
?>

<!-- ==========================================================================
     Component 1: Mobile Bottom Navigation Bar (Strict Reference: image_d700c4.png)
     ========================================================================== -->
<nav
	id="test-theme-mobile-bottom-nav"
	class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-100 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] flex items-center justify-around h-16 px-1 md:hidden select-none"
	dir="rtl"
	aria-label="شريط التنقل السفلي"
>
	<!-- Item 1 (Right): الرئيسية (Home - Active State) -->
	<a
		href="<?php echo esc_url( $home_url ); ?>"
		class="flex flex-col items-center justify-center flex-1 h-full py-1 no-underline transition-colors <?php echo $is_home_active ? 'text-[#101211]' : 'text-slate-500 hover:text-slate-800'; ?>"
		aria-label="الرئيسية"
		<?php echo $is_home_active ? 'aria-current="page"' : ''; ?>
	>
		<span class="flex items-center justify-center w-6 h-6">
			<!-- Filled home SVG icon -->
			<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
				<path d="M19 9.3V4h-3v2.7L12 3 2 12h3v8a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1v-8h3l-3-2.7z"/>
			</svg>
		</span>
		<span class="text-[11px] <?php echo $is_home_active ? 'font-black' : 'font-medium'; ?> mt-1 leading-none">الرئيسية</span>
		<span class="w-7 h-0.5 <?php echo $is_home_active ? 'bg-[#A3E635]' : 'bg-transparent'; ?> rounded-full mx-auto mt-0.5"></span>
	</a>

	<!-- Item 2: المتجر (Shop) -->
	<a
		href="<?php echo esc_url( $shop_url ); ?>"
		class="flex flex-col items-center justify-center flex-1 h-full py-1 no-underline transition-colors <?php echo $is_categories_active ? 'text-[#101211]' : 'text-slate-500 hover:text-slate-800'; ?>"
		aria-label="المتجر"
		<?php echo $is_categories_active ? 'aria-current="page"' : ''; ?>
	>
		<span class="flex items-center justify-center w-6 h-6">
			<!-- Modern Storefront / Market SVG icon -->
			<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
				<path d="M3 9l2-5h14l2 5"></path>
				<path d="M21 9v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9"></path>
				<path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path>
				<path d="M10 14h4v7h-4z"></path>
			</svg>
		</span>
		<span class="text-[11px] <?php echo $is_categories_active ? 'font-black' : 'font-medium'; ?> mt-1 leading-none">المتجر</span>
		<span class="w-7 h-0.5 <?php echo $is_categories_active ? 'bg-[#A3E635]' : 'bg-transparent'; ?> rounded-full mx-auto mt-0.5"></span>
	</a>

	<!-- Item 3: العروض (Offers / Deals) -->
	<a
		href="<?php echo esc_url( $offers_url ); ?>"
		class="flex flex-col items-center justify-center flex-1 h-full py-1 no-underline transition-colors <?php echo $is_offers_active ? 'text-[#101211]' : 'text-slate-500 hover:text-slate-800'; ?>"
		aria-label="العروض"
		<?php echo $is_offers_active ? 'aria-current="page"' : ''; ?>
	>
		<span class="flex items-center justify-center w-6 h-6">
			<!-- Angled price tag SVG with center dot -->
			<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
				<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
				<circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/>
			</svg>
		</span>
		<span class="text-[11px] <?php echo $is_offers_active ? 'font-black' : 'font-medium'; ?> mt-1 leading-none">العروض</span>
		<span class="w-7 h-0.5 <?php echo $is_offers_active ? 'bg-[#A3E635]' : 'bg-transparent'; ?> rounded-full mx-auto mt-0.5"></span>
	</a>

	<!-- Item 4: السلة (Cart) -->
	<a
		href="<?php echo esc_url( $cart_url ); ?>"
		data-cart-drawer-trigger="true"
		class="ashrafco-cart-trigger flex flex-col items-center justify-center flex-1 h-full py-1 no-underline transition-colors <?php echo $is_cart_active ? 'text-[#101211]' : 'text-slate-500 hover:text-slate-800'; ?> cursor-pointer"
		aria-label="السلة"
		aria-controls="ashrafco-cart-drawer"
		aria-expanded="false"
		<?php echo $is_cart_active ? 'aria-current="page"' : ''; ?>
	>
		<span class="relative flex items-center justify-center w-6 h-6">
			<!-- Shopping cart SVG -->
			<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
				<circle cx="9" cy="20" r="1.5"/>
				<circle cx="18" cy="20" r="1.5"/>
				<path d="M3 4h2l2.3 9.2a2 2 0 002 1.5h8.9a2 2 0 001.9-1.4l1.8-6.3H6.1"/>
			</svg>
			<!-- Dynamic badge -->
			<span id="mobile-nav-cart-badge"
				class="ashrafco-cart-count-badge absolute -top-1.5 -right-2 bg-[#A3E635] text-[#101211] text-[9px] font-black rounded-full min-w-[17px] h-[17px] px-1 flex items-center justify-center leading-none shadow-xs">
				<?php echo esc_html( $cart_count ); ?>
			</span>
		</span>
		<span class="text-[11px] <?php echo $is_cart_active ? 'font-black' : 'font-medium'; ?> mt-1 leading-none">السلة</span>
		<span class="w-7 h-0.5 <?php echo $is_cart_active ? 'bg-[#A3E635]' : 'bg-transparent'; ?> rounded-full mx-auto mt-0.5"></span>
	</a>

	<!-- Item 5 (Left): حسابي (Account) -->
	<a
		href="<?php echo esc_url( $account_url ); ?>"
		class="flex flex-col items-center justify-center flex-1 h-full py-1 no-underline transition-colors <?php echo $is_account_active ? 'text-[#101211]' : 'text-slate-500 hover:text-slate-800'; ?>"
		aria-label="حسابي"
		<?php echo $is_account_active ? 'aria-current="page"' : ''; ?>
	>
		<span class="flex items-center justify-center w-6 h-6">
			<!-- User / Profile outline SVG icon -->
			<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
				<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
				<circle cx="12" cy="7" r="4"/>
			</svg>
		</span>
		<span class="text-[11px] <?php echo $is_account_active ? 'font-black' : 'font-medium'; ?> mt-1 leading-none">حسابي</span>
		<span class="w-7 h-0.5 <?php echo $is_account_active ? 'bg-[#A3E635]' : 'bg-transparent'; ?> rounded-full mx-auto mt-0.5"></span>
	</a>
</nav>

<!-- ==========================================================================
     Component 2: Mobile Categories Slide-up Drawer Sheet (md:hidden)
     ========================================================================== -->
<div
	id="test-theme-categories-drawer"
	class="fixed inset-0 z-50 transition-all duration-300 pointer-events-none opacity-0 invisible md:hidden"
	aria-hidden="true"
	role="dialog"
	aria-modal="true"
	aria-labelledby="test-theme-drawer-title"
>
	<!-- Backdrop Overlay -->
	<div
		id="test-theme-drawer-backdrop"
		class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"
	></div>

	<!-- Slide-up Content Sheet -->
	<div
		id="test-theme-drawer-sheet"
		class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl shadow-2xl max-h-[82vh] flex flex-col transition-transform duration-300 transform translate-y-full pb-safe font-sans"
		dir="rtl"
	>
		<!-- Drag Handle Indicator -->
		<div class="pt-3 pb-1 flex justify-center">
			<span class="w-12 h-1.5 rounded-full bg-slate-200"></span>
		</div>

		<!-- Drawer Header -->
		<div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
			<div class="flex items-center gap-2.5">
				<div class="w-8 h-8 rounded-xl bg-[#f7fee7] text-[#244A27] flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
					</svg>
				</div>
				<div>
					<h3 id="test-theme-drawer-title" class="font-bold text-base text-slate-900 leading-tight">
						<?php echo esc_html( sprintf( __( 'تشكيلات %s', 'custom-store-boilerplate' ), function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'Ben10' ) ); ?>
					</h3>
					<p class="text-[11px] text-slate-400">
						<?php esc_html_e( 'اختر التشكيلة لتصفح أحدث ساعات ومجموعات Ben10', 'custom-store-boilerplate' ); ?>
					</p>
				</div>
			</div>

			<!-- Close Button -->
			<button
				type="button"
				id="test-theme-drawer-close"
				class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors"
				aria-label="<?php esc_attr_e( 'إغلاق التصنيفات', 'custom-store-boilerplate' ); ?>"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<!-- Categories List / Grid -->
		<div class="p-4 overflow-y-auto max-h-[55vh] space-y-2">
			<?php if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) : ?>
				<div class="grid grid-cols-1 gap-2">
					<?php foreach ( $product_categories as $cat ) :
						$cat_link     = get_term_link( $cat );
						$cat_count    = $cat->count;
						$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
						$image_url    = $thumbnail_id ? wp_get_attachment_thumb_url( $thumbnail_id ) : '';
					?>
						<a
							href="<?php echo esc_url( $cat_link ); ?>"
							class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-[#f7fee7] border border-slate-100 hover:border-[#bef264] transition-all group"
						>
							<div class="flex items-center gap-3">
								<div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 flex items-center justify-center flex-shrink-0 overflow-hidden text-slate-400 group-hover:text-[#244A27] transition-colors">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" width="40" height="40" loading="lazy" decoding="async" class="w-full h-full object-cover aspect-square">
									<?php else : ?>
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
										</svg>
									<?php endif; ?>
								</div>
								<div>
									<div class="font-bold text-sm text-slate-800 group-hover:text-[#244A27] transition-colors">
										<?php echo esc_html( $cat->name ); ?>
									</div>
									<div class="text-[11px] text-slate-400">
										<?php printf( esc_html( _n( '%s منتج', '%s منتجات', $cat_count, 'custom-store-boilerplate' ) ), number_format_i18n( $cat_count ) ); ?>
									</div>
								</div>
							</div>
							<svg class="w-4 h-4 text-slate-300 group-hover:text-[#244A27] transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
							</svg>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<!-- Default / Fallback Category Items -->
				<div class="grid grid-cols-1 gap-2 text-sm">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-[#f7fee7] border border-slate-100 hover:border-[#bef264] transition-all group">
						<span class="font-bold text-slate-800 group-hover:text-[#244A27]">ساعات كلاسيكية فاخرة</span>
						<svg class="w-4 h-4 text-slate-300 group-hover:text-[#244A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
					</a>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-[#f7fee7] border border-slate-100 hover:border-[#bef264] transition-all group">
						<span class="font-bold text-slate-800 group-hover:text-[#244A27]">ساعات رياضية وأوتوماتيك</span>
						<svg class="w-4 h-4 text-slate-300 group-hover:text-[#244A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
					</a>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-[#f7fee7] border border-slate-100 hover:border-[#bef264] transition-all group">
						<span class="font-bold text-slate-800 group-hover:text-[#244A27]">أحدث الإصدارات والتصاميم</span>
						<svg class="w-4 h-4 text-slate-300 group-hover:text-[#244A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
					</a>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 hover:bg-[#f7fee7] border border-slate-100 hover:border-[#bef264] transition-all group">
						<span class="font-bold text-slate-800 group-hover:text-[#244A27]">إكسسوارات وسوارات الساعات</span>
						<svg class="w-4 h-4 text-slate-300 group-hover:text-[#244A27]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<!-- Drawer Footer Actions -->
		<div class="p-4 border-t border-slate-100 bg-slate-50/50">
			<a
				href="<?php echo esc_url( $shop_url ); ?>"
				class="w-full bg-[#101211] hover:bg-[#1f2420] text-[#A3E635] border border-[#244A27] font-bold text-sm py-3 px-4 rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all"
			>
				<span><?php esc_html_e( 'تصفح جميع المنتجات في المتجر', 'custom-store-boilerplate' ); ?></span>
				<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
				</svg>
			</a>
		</div>
	</div>
</div>

<!-- ==========================================================================
     Component 3: Mobile Saved / Bookmarks Slide-up Drawer Sheet
     ========================================================================== -->
<div
	id="test-theme-saved-drawer"
	class="fixed inset-0 z-50 transition-all duration-300 pointer-events-none opacity-0 invisible"
	aria-hidden="true"
	role="dialog"
	aria-modal="true"
	aria-labelledby="test-theme-saved-title"
>
	<!-- Backdrop Overlay -->
	<div
		id="test-theme-saved-backdrop"
		class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"
	></div>

	<!-- Slide-up Content Sheet -->
	<div
		id="test-theme-saved-sheet"
		class="absolute bottom-0 inset-x-0 bg-white rounded-t-3xl shadow-2xl max-h-[80vh] flex flex-col transition-transform duration-300 transform translate-y-full pb-safe font-sans"
		dir="rtl"
	>
		<!-- Drag Handle Indicator -->
		<div class="pt-3 pb-1 flex justify-center">
			<span class="w-12 h-1.5 rounded-full bg-slate-200"></span>
		</div>

		<!-- Drawer Header -->
		<div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
			<div class="flex items-center gap-2.5">
				<div class="w-8 h-8 rounded-xl bg-[#f7fee7] text-[#244A27] flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
					</svg>
				</div>
				<div>
					<h3 id="test-theme-saved-title" class="font-bold text-base text-slate-900 leading-tight">
						<?php esc_html_e( 'المنتجات المحفوظة', 'custom-store-boilerplate' ); ?>
					</h3>
					<p class="text-[11px] text-slate-400">
						<?php esc_html_e( 'قائمتك المفضلة للساعات والمنتجات المختارة للوصول والمقارنة السريعة', 'custom-store-boilerplate' ); ?>
					</p>
				</div>
			</div>

			<!-- Close Button -->
			<button
				type="button"
				id="test-theme-saved-close"
				class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors"
				aria-label="<?php esc_attr_e( 'إغلاق المحفوظات', 'custom-store-boilerplate' ); ?>"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<!-- Empty State / Saved Content Container -->
		<div class="p-6 text-center space-y-4">
			<div class="w-16 h-16 mx-auto rounded-full bg-[#f7fee7] text-[#244A27] flex items-center justify-center shadow-inner">
				<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
				</svg>
			</div>
			<div>
				<h4 class="font-bold text-slate-900 text-base">لا توجد منتجات محفوظة حالياً</h4>
				<p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto leading-relaxed">
					اضغط على أيقونة الحفظ في صفحات المنتجات للوصول السريع إليها ومقارنة أسعارها في أي وقت.
				</p>
			</div>
			<div class="pt-2">
				<a
					href="<?php echo esc_url( $shop_url ); ?>"
					class="inline-flex items-center justify-center gap-2 bg-[#101211] hover:bg-[#1f2420] text-[#A3E635] border border-[#244A27] font-bold text-sm py-2.5 px-6 rounded-xl shadow-md transition-all"
				>
					<span>تصفح أحدث الساعات الآن</span>
				</a>
			</div>
		</div>

	</div>
</div>
