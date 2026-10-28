<?php
/**
 * Modern Responsive Navigation Template Override for Ashrafco Child Theme
 *
 * Strictly reproduces the reference design:
 * - Profile header with orange circle avatar, real user name, role badge, email, and divider.
 * - Exact 9 navigation items in strict sequence with Arabic labels and subtitles.
 * - Squircle icons on the right, left chevrons, rounded cards, pink active and logout treatments.
 * - Server-side authorization for Store Management Portal.
 * - Complete removal of Downloads and WP Admin items.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 12.0.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$raw_items    = wc_get_account_menu_items();

// Subtitles mapping as requested
$item_subtitles = array(
	'dashboard'       => __( 'ملخص الحساب والطلبات', 'ashrafco' ),
	'admin-portal'    => __( 'إدارة المتجر وأدواته', 'ashrafco' ),
	'shop'            => __( 'عرض أحدث المنتجات', 'ashrafco' ),
	'orders'          => __( 'عرض الطلبات وتفاصيلها', 'ashrafco' ),
	'my-notifications' => __( 'تنبيهات الطلبات وتحديثات الحساب', 'ashrafco' ),
	'wishlist'        => __( 'المنتجات التي حفظها العميل', 'ashrafco' ),
	'my-reviews'      => __( 'مراجعات المنتجات التي أضافها العميل', 'ashrafco' ),
	'edit-account'    => __( 'البيانات الشخصية وكلمة المرور', 'ashrafco' ),
	'customer-logout' => __( 'الخروج من الحساب', 'ashrafco' ),
);

// Mobile short labels for compact horizontal pill navigation
$mobile_pill_labels = array(
	'dashboard'       => __( 'لوحة التحكم', 'ashrafco' ),
	'admin-portal'    => __( 'إدارة المتجر', 'ashrafco' ),
	'shop'            => __( 'المتجر', 'ashrafco' ),
	'orders'          => __( 'الطلبات', 'ashrafco' ),
	'my-notifications' => __( 'الإشعارات', 'ashrafco' ),
	'wishlist'        => __( 'المفضلة', 'ashrafco' ),
	'my-reviews'      => __( 'تقييماتي', 'ashrafco' ),
	'edit-account'    => __( 'تفاصيل الحساب', 'ashrafco' ),
	'customer-logout' => __( 'تسجيل الخروج', 'ashrafco' ),
);

// Helper function to resolve active state accurately
$is_endpoint_active = function( $endpoint ) {
	if ( 'dashboard' === $endpoint ) {
		return is_account_page() && ( ! WC()->query->get_current_endpoint() || 'dashboard' === WC()->query->get_current_endpoint() );
	}
	if ( 'my-notifications' === $endpoint ) {
		return is_wc_endpoint_url( 'my-notifications' );
	}
	if ( 'orders' === $endpoint ) {
		return is_wc_endpoint_url( 'orders' ) || is_wc_endpoint_url( 'view-order' );
	}
	if ( 'edit-account' === $endpoint ) {
		return is_wc_endpoint_url( 'edit-account' );
	}
	if ( 'my-reviews' === $endpoint ) {
		return is_wc_endpoint_url( 'my-reviews' );
	}
	if ( 'shop' === $endpoint ) {
		return function_exists( 'is_shop' ) && is_shop();
	}
	if ( 'wishlist' === $endpoint ) {
		return is_page( 'wishlist' ) || ( false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/wishlist' ) );
	}
	if ( 'admin-portal' === $endpoint ) {
		return ( false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/admin-portal' ) );
	}
	return false;
};

// User Profile Details
$display_name = $current_user->display_name ?: $current_user->user_login;
$user_email   = $current_user->user_email ?: '';
$first_char   = mb_substr( $display_name, 0, 1, 'UTF-8' );
$initial      = ( preg_match( '/[a-zA-Z]/', $first_char ) ) ? mb_strtolower( $first_char, 'UTF-8' ) : $first_char;

// Role check
$is_admin        = current_user_can( 'manage_options' ) || current_user_can( 'administrator' );
$is_shop_manager = ! $is_admin && ( current_user_can( 'manage_woocommerce' ) || in_array( 'shop_manager', (array) $current_user->roles, true ) );

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation w-full" aria-label="<?php esc_attr_e( 'تنقل حساب العميل', 'ashrafco' ); ?>">

	<!-- =========================================================================
	     1. MOBILE HORIZONTAL NAVIGATION (< md) — Fast Scrollable Pill Bar
	     ========================================================================= -->
	<div class="block md:hidden w-full mb-4">
		<!-- Quick Switcher Track -->
		<div class="relative flex items-center w-full">
			<div id="ashrafco-mobile-nav-track" class="flex items-center gap-2 overflow-x-auto no-scrollbar scroll-smooth w-full py-1.5 px-0.5">
				<?php foreach ( $raw_items as $endpoint => $label ) :
					$active     = $is_endpoint_active( $endpoint );
					$is_logout  = ( 'customer-logout' === $endpoint );
					$pill_label = isset( $mobile_pill_labels[ $endpoint ] ) ? $mobile_pill_labels[ $endpoint ] : $label;

					if ( 'customer-logout' === $endpoint ) {
						$url = wc_logout_url();
					} elseif ( 'admin-portal' === $endpoint ) {
						$url = home_url( '/admin-portal/' );
					} elseif ( 'shop' === $endpoint ) {
						$url = wc_get_page_permalink( 'shop' );
					} elseif ( 'wishlist' === $endpoint ) {
						$url = function_exists( 'test_theme_get_wishlist_url' ) ? test_theme_get_wishlist_url() : home_url( '/wishlist/' );
					} else {
						$url = wc_get_account_endpoint_url( $endpoint );
					}
					?>
					<a
						href="<?php echo esc_url( $url ); ?>"
						class="flex items-center gap-2 whitespace-nowrap px-4 py-2.5 rounded-2xl text-xs font-bold transition-all select-none flex-shrink-0 <?php
						if ( $active ) {
							echo 'bg-rose-50 text-primary border border-rose-200 shadow-xs font-black';
						} elseif ( $is_logout ) {
							echo 'text-rose-600 bg-white border border-rose-200 hover:bg-rose-50';
						} else {
							echo 'text-slate-700 bg-white border border-slate-200/80 hover:bg-slate-50 shadow-2xs';
						}
						?>"
					>
						<?php switch ( $endpoint ) :
							case 'dashboard': ?>
								<svg class="w-4 h-4 flex-shrink-0 <?php echo $active ? 'text-primary' : 'text-slate-600'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
									<rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
									<rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
									<rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
								</svg>
								<?php break;
							case 'admin-portal': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M3 9l2-5h14l2 5v2a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-2H8v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
									<path d="M4 11v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9"></path>
									<path d="M9 21v-7a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7"></path>
								</svg>
								<?php break;
							case 'shop': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<circle cx="9" cy="21" r="1"></circle>
									<circle cx="20" cy="21" r="1"></circle>
									<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
								</svg>
								<?php break;
							case 'orders': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
									<line x1="3" y1="6" x2="21" y2="6"></line>
									<path d="M16 10a4 4 0 0 1-8 0"></path>
								</svg>
								<?php break;

							case 'my-notifications': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
									<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
								</svg>
								<?php break;

							case 'wishlist': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
								</svg>
								<?php break;
							case 'my-reviews': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="14" rx="3"></rect>
									<polygon points="12 7 13.5 10 17 10.5 14.5 12.8 15.2 16.2 12 14.4 8.8 16.2 9.5 12.8 7 10.5 10.5 10 12 7" fill="currentColor"></polygon>
									<circle cx="19" cy="18" r="2.5" fill="currentColor"></circle>
								</svg>
								<?php break;
							case 'edit-account': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
									<circle cx="9" cy="7" r="4"></circle>
									<circle cx="19" cy="11" r="2"></circle>
								</svg>
								<?php break;
							case 'customer-logout': ?>
								<svg class="w-4 h-4 flex-shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
									<polyline points="16 17 21 12 16 7"></polyline>
									<line x1="21" y1="12" x2="9" y2="12"></line>
								</svg>
								<?php break;
						endswitch; ?>
						<span><?php echo esc_html( $pill_label ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>

			<!-- Visual Scroll Indicator -->
			<button
				type="button"
				onclick="document.getElementById('ashrafco-mobile-nav-track').scrollBy({left: -150, behavior: 'smooth'});"
				class="w-8 h-8 rounded-full bg-white/95 border border-slate-200/90 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-800 flex-shrink-0 mr-1.5 focus:outline-none"
				aria-label="<?php esc_attr_e( 'تمرير القائمة', 'ashrafco' ); ?>"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
				</svg>
			</button>
		</div>

		<!-- Accessible Collapsible Menu View on mobile -->
		<details class="group mt-2 bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
			<summary class="flex items-center justify-between px-4 py-2.5 cursor-pointer text-xs font-bold text-slate-700 hover:bg-slate-50 select-none">
				<span class="flex items-center gap-2">
					<span class="w-2 h-2 rounded-full bg-primary"></span>
					<span><?php esc_html_e( 'عرض كافة أقسام وقائمة الحساب', 'ashrafco' ); ?></span>
				</span>
				<svg class="w-4 h-4 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<polyline points="6 9 12 15 18 9"></polyline>
				</svg>
			</summary>
			<div class="p-3 border-t border-slate-100">
				<!-- Embedded full cards list on mobile -->
				<ul class="space-y-2 list-none p-0 m-0 w-full">
					<?php foreach ( $raw_items as $endpoint => $label ) :
						$active    = $is_endpoint_active( $endpoint );
						$is_logout = ( 'customer-logout' === $endpoint );
						$subtitle  = isset( $item_subtitles[ $endpoint ] ) ? $item_subtitles[ $endpoint ] : '';

						if ( 'customer-logout' === $endpoint ) {
							$url = wc_logout_url();
						} elseif ( 'admin-portal' === $endpoint ) {
							$url = home_url( '/admin-portal/' );
						} elseif ( 'shop' === $endpoint ) {
							$url = wc_get_page_permalink( 'shop' );
						} elseif ( 'wishlist' === $endpoint ) {
							$url = function_exists( 'test_theme_get_wishlist_url' ) ? test_theme_get_wishlist_url() : home_url( '/wishlist/' );
						} else {
							$url = wc_get_account_endpoint_url( $endpoint );
						}
						?>
						<li class="m-0 p-0 w-full">
							<a
								href="<?php echo esc_url( $url ); ?>"
								class="relative flex items-center justify-between p-2.5 rounded-xl text-xs transition-all w-full <?php
								if ( $active ) {
									echo 'bg-[#fff1f2] border border-rose-200 text-slate-900 font-bold overflow-hidden';
								} elseif ( $is_logout ) {
									echo 'bg-[#fff1f2] border border-rose-100 text-primary font-bold';
								} else {
									echo 'bg-white border border-slate-100 text-slate-800 hover:bg-slate-50';
								}
								?>"
							>
								<?php if ( $active ) : ?>
									<span class="absolute right-0 top-1.5 bottom-1.5 w-1 bg-primary rounded-l-full" aria-hidden="true"></span>
								<?php endif; ?>
								<div class="flex items-center gap-2.5 min-w-0 flex-1">
									<div class="min-w-0 pr-1">
										<span class="block font-bold text-xs truncate <?php echo ( $is_logout || $active ) ? 'text-primary' : 'text-slate-800'; ?>">
											<?php echo esc_html( $label ); ?>
										</span>
										<?php if ( $subtitle ) : ?>
											<span class="block text-[10px] text-slate-400 font-normal truncate mt-0.5">
												<?php echo esc_html( $subtitle ); ?>
											</span>
										<?php endif; ?>
									</div>
								</div>
								<svg class="w-4 h-4 flex-shrink-0 <?php echo ( $active || $is_logout ) ? 'text-primary' : 'text-slate-400'; ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
								</svg>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</details>
	</div>

	<!-- =========================================================================
	     2. DESKTOP VERTICAL SIDEBAR (md:block) — STRICT 1:1 REFERENCE DESIGN
	     ========================================================================= -->
	<div class="hidden md:block bg-white rounded-3xl border border-slate-100/90 shadow-[0_2px_14px_rgba(0,0,0,0.03)] p-4 sm:p-5 w-full">

		<!-- Profile Header Card (Top) -->
		<div class="flex items-center justify-between gap-3.5 pb-4 mb-4 border-b border-slate-100 px-1">
			<!-- User Details (Name + Role Badge on Row 1, Email on Row 2) -->
			<div class="min-w-0 flex-1 text-right">
				<!-- Top Row: Display Name & Role Badge -->
				<div class="flex items-center gap-2 flex-wrap justify-start">
					<h2 class="text-base font-extrabold text-slate-900 truncate m-0 leading-tight">
						<?php echo esc_html( $display_name ); ?>
					</h2>
					<?php if ( $is_admin ) : ?>
						<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#fef3c7] text-[#92400e] border border-[#fde68a] select-none shadow-2xs">
							<svg class="w-3.5 h-3.5 text-[#d97706] fill-[#d97706]" viewBox="0 0 24 24">
								<path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
							</svg>
							<span><?php esc_html_e( 'مدير النظام', 'ashrafco' ); ?></span>
						</span>
					<?php elseif ( $is_shop_manager ) : ?>
						<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 select-none shadow-2xs">
							<span><?php esc_html_e( 'مدير المتجر', 'ashrafco' ); ?></span>
						</span>
					<?php endif; ?>
				</div>

				<!-- Bottom Row: User Email -->
				<?php if ( $user_email ) : ?>
					<p class="text-xs text-slate-400 truncate m-0 mt-1 font-normal tracking-wide" dir="ltr">
						<?php echo esc_html( $user_email ); ?>
					</p>
				<?php endif; ?>
			</div>

			<!-- Vibrant Orange Circle Avatar with Initial (Reference Design) -->
			<div class="w-14 h-14 rounded-full bg-[#ea580c] text-white flex items-center justify-center font-black text-2xl flex-shrink-0 shadow-sm leading-none select-none">
				<?php echo esc_html( $initial ); ?>
			</div>
		</div>

		<!-- Navigation Items List (9 Items Exactly in Strict Sequence) -->
		<ul class="space-y-2.5 list-none p-0 m-0 w-full">
			<?php foreach ( $raw_items as $endpoint => $label ) :
				$active    = $is_endpoint_active( $endpoint );
				$is_logout = ( 'customer-logout' === $endpoint );
				$is_portal = ( 'admin-portal' === $endpoint );
				$subtitle  = isset( $item_subtitles[ $endpoint ] ) ? $item_subtitles[ $endpoint ] : '';

				if ( 'customer-logout' === $endpoint ) {
					$url = wc_logout_url();
				} elseif ( 'admin-portal' === $endpoint ) {
					$url = home_url( '/admin-portal/' );
				} elseif ( 'shop' === $endpoint ) {
					$url = wc_get_page_permalink( 'shop' );
				} elseif ( 'wishlist' === $endpoint ) {
					$url = function_exists( 'test_theme_get_wishlist_url' ) ? test_theme_get_wishlist_url() : home_url( '/wishlist/' );
				} else {
					$url = wc_get_account_endpoint_url( $endpoint );
				}
				?>
				<li class="m-0 p-0 w-full">
					<a
						href="<?php echo esc_url( $url ); ?>"
						class="group relative flex items-center justify-between p-3 sm:px-3.5 sm:py-3.5 rounded-2xl transition-all duration-150 select-none w-full overflow-hidden focus:outline-none focus:ring-2 focus:ring-primary/20 <?php
						if ( $active ) {
							echo 'bg-[#fff1f2] border border-rose-100 shadow-2xs';
						} elseif ( $is_logout ) {
							echo 'bg-[#fff1f2] border border-rose-100 hover:bg-rose-100/60 text-primary shadow-2xs';
						} else {
							echo 'bg-white border border-slate-100 hover:border-slate-200/90 hover:bg-slate-50/70 shadow-2xs hover:shadow-xs';
						}
						?>"
					>
						<!-- Active Red Accent Vertical Bar on Right Edge (Strictly Reference Design) -->
						<?php if ( $active ) : ?>
							<span class="absolute right-0 top-2 bottom-2 w-1.5 bg-primary rounded-l-full" aria-hidden="true"></span>
						<?php endif; ?>

						<!-- Right Block: Squircle Icon + Titles Block -->
						<div class="flex items-center gap-3 min-w-0 flex-1">
							<!-- Squircle Icon Box -->
							<div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-105 <?php
							if ( $active ) {
								echo 'bg-white/90 border border-rose-100 text-primary shadow-2xs';
							} elseif ( $is_logout ) {
								echo 'bg-white/90 border border-rose-100 text-primary shadow-2xs';
							} elseif ( $is_portal ) {
								echo 'bg-slate-50/90 border border-slate-100 text-slate-700';
							} else {
								echo 'bg-slate-50/90 border border-slate-100 text-primary';
							}
							?>">
								<?php switch ( $endpoint ) :
									case 'dashboard': ?>
										<!-- 1. Grid (4 squares) -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
											<rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
											<rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
											<rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
										</svg>
										<?php break;
									case 'admin-portal': ?>
										<!-- 2. Storefront -->
										<svg class="w-5 h-5 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M3 9l2-5h14l2 5v2a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-2H8v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
											<path d="M4 11v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9"></path>
											<path d="M9 21v-7a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v7"></path>
										</svg>
										<?php break;
									case 'shop': ?>
										<!-- 3. Shopping Cart -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<circle cx="9" cy="21" r="1"></circle>
											<circle cx="20" cy="21" r="1"></circle>
											<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
										</svg>
										<?php break;
									case 'orders': ?>
										<!-- 4. Shopping Bag -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
											<line x1="3" y1="6" x2="21" y2="6"></line>
											<path d="M16 10a4 4 0 0 1-8 0"></path>
										</svg>
										<?php break;

									case 'my-notifications': ?>
										<!-- Bell Icon -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
											<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
										</svg>
										<?php break;

									case 'wishlist': ?>
										<!-- 6. Heart -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
										</svg>
										<?php break;
									case 'my-reviews': ?>
										<!-- 7. Review Star Badge -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<rect x="3" y="4" width="18" height="14" rx="3"></rect>
											<polygon points="12 7 13.5 10 17 10.5 14.5 12.8 15.2 16.2 12 14.4 8.8 16.2 9.5 12.8 7 10.5 10.5 10 12 7" fill="currentColor"></polygon>
											<circle cx="19" cy="18" r="2.5" fill="currentColor"></circle>
										</svg>
										<?php break;
									case 'edit-account': ?>
										<!-- 8. User Details + Cog -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
											<circle cx="9" cy="7" r="4"></circle>
											<circle cx="19" cy="11" r="2"></circle>
											<path d="M19 8v1m0 4v1m-2.5-3.5l.8.8m3.4 3.4l.8.8m-4.2 0l.8-.8m3.4-3.4l.8-.8"></path>
										</svg>
										<?php break;
									case 'customer-logout': ?>
										<!-- 9. Logout Door + Arrow -->
										<svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
											<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
											<polyline points="16 17 21 12 16 7"></polyline>
											<line x1="21" y1="12" x2="9" y2="12"></line>
										</svg>
										<?php break;
								endswitch; ?>
							</div>

							<!-- Title & Subtitle Text (Wrapped naturally without truncating) -->
							<div class="min-w-0 flex-1 text-right">
								<span class="block text-[14.5px] font-bold leading-tight <?php
								if ( $is_logout ) {
									echo 'text-primary';
								} elseif ( $active ) {
									echo 'text-slate-900 font-extrabold';
								} else {
									echo 'text-slate-800 group-hover:text-slate-900';
								}
								?>">
									<?php echo esc_html( $label ); ?>
								</span>
								<?php if ( $subtitle ) : ?>
									<span class="block text-[11.5px] font-normal leading-snug mt-1 <?php
									if ( $active ) {
										echo 'text-slate-500';
									} elseif ( $is_logout ) {
										echo 'text-slate-500';
									} else {
										echo 'text-slate-400 group-hover:text-slate-500';
									}
									?>">
										<?php echo esc_html( $subtitle ); ?>
									</span>
								<?php endif; ?>
							</div>
						</div>

						<!-- Left Chevron Arrow -->
						<div class="w-5 h-5 flex items-center justify-center flex-shrink-0 ms-2">
							<svg class="w-4 h-4 transition-transform group-hover:-translate-x-1 <?php
							if ( $active || $is_logout ) {
								echo 'text-primary';
							} else {
								echo 'text-slate-400 group-hover:text-slate-600';
							}
							?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
							</svg>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>

</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
