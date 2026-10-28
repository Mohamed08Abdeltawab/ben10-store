<?php
/**
 * Account Navigation & Custom Reviews Endpoint for Ashrafco Theme
 *
 * Handles account menu items filter, endpoint URLs, and the dedicated
 * customer product reviews & ratings endpoint ('my-reviews').
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1. Register the custom WooCommerce endpoint for Customer Reviews & Ratings.
 */
function ashrafco_register_reviews_endpoint() {
	add_rewrite_endpoint( 'my-reviews', EP_ROOT | EP_PAGES );
}
add_action( 'init', 'ashrafco_register_reviews_endpoint' );

/**
 * 2. Add query var for WooCommerce query vars.
 * This automatically integrates with WC endpoint routing, is_wc_endpoint_url(),
 * and woocommerce_account_{endpoint}_endpoint action hook.
 *
 * @param array $query_vars WooCommerce query vars.
 * @return array
 */
function ashrafco_reviews_wc_query_vars( $query_vars ) {
	$query_vars['my-reviews'] = 'my-reviews';
	return $query_vars;
}
add_filter( 'woocommerce_get_query_vars', 'ashrafco_reviews_wc_query_vars' );

function ashrafco_reviews_query_vars( $vars ) {
	$vars[] = 'my-reviews';
	return $vars;
}
add_filter( 'query_vars', 'ashrafco_reviews_query_vars' );

/**
 * 3. Flush rewrite rules once safely if endpoint is newly added.
 */
function ashrafco_maybe_flush_account_endpoints() {
	$flushed_version = get_option( 'ashrafco_reviews_endpoint_flushed_version' );
	if ( '2.1' !== $flushed_version ) {
		ashrafco_register_reviews_endpoint();
		flush_rewrite_rules( false );
		update_option( 'ashrafco_reviews_endpoint_flushed_version', '2.1' );
	}
}
add_action( 'init', 'ashrafco_maybe_flush_account_endpoints', 99 );

/**
 * 4. Filter the WooCommerce page title when on the 'my-reviews' endpoint.
 *
 * @param string $title Existing title.
 * @param string $endpoint Current endpoint slug.
 * @return string
 */
function ashrafco_reviews_endpoint_title( $title, $endpoint = '' ) {
	if ( 'my-reviews' === $endpoint ) {
		return __( 'تعليقاتي وتقييماتي', 'ashrafco' );
	}
	return $title;
}
add_filter( 'woocommerce_endpoint_my-reviews_title', 'ashrafco_reviews_endpoint_title', 10, 2 );

/**
 * 5. Define exactly the 9 required Account Menu Items in strict order.
 *
 * Strictly removes «التنزيلات» (downloads) and «لوحة تحكم WP Admin» (admin-dashboard).
 * Server-side checks permissions for the Store Management Portal ('admin-portal').
 *
 * @param array $items Existing account menu items.
 * @return array
 */
function ashrafco_custom_account_menu_items( $items ) {
	$ordered = array();

	// 1. Dashboard
	$ordered['dashboard'] = __( 'لوحة التحكم', 'ashrafco' );

	// 2. Store Management Portal (Authorized users only - strictly server-side verified)
	if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
		$ordered['admin-portal'] = __( 'بوابة إدارة المتجر (شاملة)', 'ashrafco' );
	}

	// 3. Shop
	$ordered['shop'] = __( 'المتجر', 'ashrafco' );

	// 4. Orders
	$ordered['orders'] = __( 'الطلبات', 'ashrafco' );

	// 4.5. Notifications
	$ordered['my-notifications'] = __( 'الإشعارات', 'ashrafco' );

	// 5. Wishlist
	$ordered['wishlist'] = __( 'المفضلة', 'ashrafco' );

	// 6. My Reviews & Ratings
	$ordered['my-reviews'] = __( 'تعليقاتي وتقييماتي', 'ashrafco' );

	// 7. Account Details
	$ordered['edit-account'] = __( 'تفاصيل الحساب', 'ashrafco' );

	// 8. Logout
	$ordered['customer-logout'] = __( 'تسجيل الخروج', 'ashrafco' );

	return $ordered;
}
add_filter( 'woocommerce_account_menu_items', 'ashrafco_custom_account_menu_items', 99 );

/**
 * Redirect and disable edit-address endpoint completely.
 */
function ashrafco_redirect_disabled_address_endpoint() {
	if ( is_account_page() ) {
		if ( is_wc_endpoint_url( 'edit-address' ) || false !== strpos( $_SERVER['REQUEST_URI'] ?? '', '/edit-address' ) ) {
			wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
			exit;
		}
	}
}
add_action( 'template_redirect', 'ashrafco_redirect_disabled_address_endpoint', 1 );

/**
 * One-time cleanup: Clear all saved addresses from database.
 */
function ashrafco_cleanup_all_saved_addresses_once() {
	if ( get_option( 'ashrafco_saved_addresses_cleared_v3' ) ) {
		return;
	}

	global $wpdb;
	// 1. Delete all saved addresses meta array
	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = '_ashrafco_saved_addresses'" );

	// 2. Clear all shipping and billing address meta fields
	$address_meta_keys = array(
		'shipping_first_name',
		'shipping_last_name',
		'shipping_company',
		'shipping_address_1',
		'shipping_address_2',
		'shipping_city',
		'shipping_postcode',
		'shipping_country',
		'shipping_state',
		'shipping_phone',
		'billing_address_1',
		'billing_address_2',
		'billing_city',
		'billing_state',
		'billing_postcode',
	);
	$placeholders = "'" . implode( "','", array_map( 'esc_sql', $address_meta_keys ) ) . "'";
	$wpdb->query( "UPDATE {$wpdb->usermeta} SET meta_value = '' WHERE meta_key IN ($placeholders)" );

	update_option( 'ashrafco_saved_addresses_cleared_v3', 1 );
}
add_action( 'init', 'ashrafco_cleanup_all_saved_addresses_once', 20 );

/**
 * 6. Resolve custom endpoint URLs.
 *
 * @param string $url Default URL.
 * @param string $endpoint Endpoint identifier.
 * @param string $value Value if any.
 * @param string $permalink Page permalink.
 * @return string
 */
function ashrafco_custom_account_endpoint_urls( $url, $endpoint, $value = '', $permalink = '' ) {
	if ( 'admin-portal' === $endpoint ) {
		return home_url( '/admin-portal/' );
	}

	if ( 'shop' === $endpoint ) {
		return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	}

	if ( 'wishlist' === $endpoint ) {
		return function_exists( 'test_theme_get_wishlist_url' ) ? test_theme_get_wishlist_url() : home_url( '/wishlist/' );
	}

	return $url;
}
add_filter( 'woocommerce_get_endpoint_url', 'ashrafco_custom_account_endpoint_urls', 30, 4 );


/**
 * 7. Render Customer Reviews & Ratings content on My Account page.
 */
function ashrafco_render_account_reviews_content() {
	if ( ! is_user_logged_in() ) {
		echo '<p class="text-slate-600">' . esc_html__( 'يرجى تسجيل الدخول لعرض التقييمات.', 'ashrafco' ) . '</p>';
		return;
	}

	$current_user_id = get_current_user_id();

	// Query approved comments on products authored by current user
	$reviews = get_comments( array(
		'user_id'   => $current_user_id,
		'post_type' => 'product',
		'status'    => 'approve',
		'orderby'   => 'comment_date_gmt',
		'order'     => 'DESC',
	) );
	?>

	<div class="ashrafco-reviews-wrapper space-y-6">

		<!-- Header Card -->
		<div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
			<div>
				<div class="flex items-center gap-2.5">
					<div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#d90429] flex-shrink-0">
						<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="4" width="18" height="14" rx="3"></rect>
							<polygon points="12 7 13.5 10 17 10.5 14.5 12.8 15.2 16.2 12 14.4 8.8 16.2 9.5 12.8 7 10.5 10.5 10 12 7" fill="currentColor"></polygon>
							<circle cx="19" cy="18" r="2.5" fill="currentColor"></circle>
						</svg>
					</div>
					<div>
						<h1 class="text-xl sm:text-2xl font-black text-slate-900 m-0">
							<?php esc_html_e( 'تعليقاتي وتقييماتي', 'ashrafco' ); ?>
						</h1>
						<p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5">
							<?php esc_html_e( 'مراجعات وتقييمات المنتجات التي قمت بكتابتها ومشاركتها في المتجر', 'ashrafco' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="flex items-center gap-2">
				<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
					<?php
					/* translators: %d: count */
					printf( esc_html__( '%d تقييم', 'ashrafco' ), count( $reviews ) );
					?>
				</span>
			</div>
		</div>

		<!-- Reviews List or Empty State -->
		<?php if ( empty( $reviews ) ) : ?>
			<!-- Empty State -->
			<div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-sm">
				<div class="w-20 h-20 rounded-full bg-rose-50 border border-rose-100 flex items-center justify-center text-[#d90429] mx-auto mb-4">
					<svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
						<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
					</svg>
				</div>
				<h2 class="text-lg sm:text-xl font-black text-slate-900 mb-2">
					<?php esc_html_e( 'لا توجد لديك أي تقييمات حتى الآن', 'ashrafco' ); ?>
				</h2>
				<p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
					<?php esc_html_e( 'لم تقم بإضافة تقييم لأي من منتجات المتجر بعد. يمكنك تصفح المنتجات ومشاركة رأيك وتجربتك لمساعدة الآخرين.', 'ashrafco' ); ?>
				</p>
				<a
					href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
					class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#d90429] hover:bg-[#b00320] text-white text-sm font-bold shadow-md hover:shadow-lg transition-all"
				>
					<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="9" cy="21" r="1"></circle>
						<circle cx="20" cy="21" r="1"></circle>
						<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
					</svg>
					<span><?php esc_html_e( 'تصفح منتجات المتجر', 'ashrafco' ); ?></span>
				</a>
			</div>
		<?php else : ?>
			<!-- Reviews Grid -->
			<div class="space-y-4">
				<?php foreach ( $reviews as $review ) :
					$product_id = $review->comment_post_ID;
					$product    = wc_get_product( $product_id );
					$rating     = intval( get_comment_meta( $review->comment_ID, 'rating', true ) );
					$date_str   = get_comment_date( get_option( 'date_format', 'Y-m-d' ), $review );
					?>
					<div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
						<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
							<!-- Product Info -->
							<div class="flex items-center gap-3.5 min-w-0">
								<div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden flex-shrink-0">
									<?php if ( $product && has_post_thumbnail( $product_id ) ) : ?>
										<?php echo get_the_post_thumbnail( $product_id, 'thumbnail', array( 'class' => 'w-full h-full object-cover' ) ); ?>
									<?php else : ?>
										<svg class="w-6 h-6 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
											<circle cx="8.5" cy="8.5" r="1.5"></circle>
											<polyline points="21 15 16 10 5 21"></polyline>
										</svg>
									<?php endif; ?>
								</div>
								<div class="min-w-0">
									<h2 class="text-base font-extrabold text-slate-900 truncate m-0">
										<?php if ( $product ) : ?>
											<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="hover:text-[#d90429] transition-colors">
												<?php echo esc_html( $product->get_name() ); ?>
											</a>
										<?php else : ?>
											<?php echo esc_html( get_the_title( $product_id ) ); ?>
										<?php endif; ?>
									</h2>
									<div class="flex items-center gap-2 mt-1 text-xs text-slate-400">
										<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
											<line x1="16" y1="2" x2="16" y2="6"></line>
											<line x1="8" y1="2" x2="8" y2="6"></line>
											<line x1="3" y1="10" x2="21" y2="10"></line>
										</svg>
										<span><?php echo esc_html( $date_str ); ?></span>
									</div>
								</div>
							</div>

							<!-- Star Rating -->
							<div class="flex items-center gap-1.5 flex-shrink-0 bg-amber-50/70 border border-amber-200/60 px-3 py-1.5 rounded-xl">
								<div class="flex items-center gap-0.5 text-amber-400">
									<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
										<svg class="w-4 h-4 <?php echo ( $i <= $rating ) ? 'text-amber-400 fill-amber-400' : 'text-slate-200 fill-slate-100'; ?>" viewBox="0 0 20 20">
											<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
										</svg>
									<?php endfor; ?>
								</div>
								<span class="text-xs font-black text-amber-800 ms-1">
									<?php echo esc_html( $rating . ' / 5' ); ?>
								</span>
							</div>
						</div>

						<!-- Review Body -->
						<div class="pt-4 text-sm leading-relaxed text-slate-700">
							<?php echo wp_kses_post( wpautop( $review->comment_content ) ); ?>
						</div>

						<?php if ( $product ) : ?>
							<div class="pt-4 mt-2 border-t border-slate-50 flex items-center justify-end">
								<a
									href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"
									class="inline-flex items-center gap-1.5 text-xs font-bold text-[#d90429] hover:underline"
								>
									<span><?php esc_html_e( 'عرض صفحة المنتج في المتجر', 'ashrafco' ); ?></span>
									<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<polyline points="15 18 9 12 15 6"></polyline>
									</svg>
								</a>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

	</div>
	<?php
}
add_action( 'woocommerce_account_my-reviews_endpoint', 'ashrafco_render_account_reviews_content' );
