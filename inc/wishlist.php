<?php
/**
 * Ashrafco Wishlist Core Architecture & WooCommerce Integration
 *
 * Implements:
 * - Guest & Logged-in customer wishlist persistence (User Meta & Cookies).
 * - Automatic Guest-to-Customer merge on authentication (wp_login).
 * - Secure AJAX endpoints for fetching product cards, toggling, and removing items.
 * - Server-side validation of product existence, visibility, and trash status.
 * - Cache busting and LiteSpeed Cache control on /wishlist/ routes.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ============================================================================
 * 1. Data Retrieval & Persistence Helpers
 * ============================================================================
 */

/**
 * Retrieve saved wishlist product IDs for a user or current session.
 *
 * @param int|null $user_id Optional user ID. Defaults to current user if logged in.
 * @return array Array of sanitized integer product IDs.
 */
function ashrafco_get_wishlist_ids( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$ids = array();

	// 1. Logged-in customer: read strictly from user meta
	if ( $user_id > 0 ) {
		$meta = get_user_meta( $user_id, '_ashrafco_wishlist', true );
		if ( is_array( $meta ) ) {
			$ids = $meta;
		}
	} else {
		// 2. Guest visitor: read from 'ashrafco_wishlist' cookie
		if ( ! empty( $_COOKIE['ashrafco_wishlist'] ) ) {
			$raw = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['ashrafco_wishlist'] ) ) );
			$parsed = json_decode( $raw, true );
			if ( is_array( $parsed ) ) {
				$ids = $parsed;
			}
		}

		// 3. Compatibility check for legacy/plugin cookies
		if ( empty( $ids ) && ! empty( $_COOKIE['yith_wcwl_products'] ) ) {
			$raw = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['yith_wcwl_products'] ) ) );
			$parsed = json_decode( $raw, true );
			if ( is_array( $parsed ) ) {
				$ids = $parsed;
			}
		}
	}

	// Ensure all values are strictly positive unique integers
	$sanitized = array();
	foreach ( (array) $ids as $val ) {
		if ( is_numeric( $val ) && (int) $val > 0 ) {
			$id = (int) $val;
			if ( ! in_array( $id, $sanitized, true ) ) {
				$sanitized[] = $id;
			}
		}
	}

	return $sanitized;
}

/**
 * Save wishlist product IDs for a user or current session.
 *
 * @param array    $ids     Array of product IDs.
 * @param int|null $user_id Optional user ID. Defaults to current user.
 * @return array Sanitized array of saved IDs.
 */
function ashrafco_set_wishlist_ids( array $ids, $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	$sanitized = array();
	foreach ( $ids as $val ) {
		if ( is_numeric( $val ) && (int) $val > 0 ) {
			$id = (int) $val;
			if ( ! in_array( $id, $sanitized, true ) ) {
				$sanitized[] = $id;
			}
		}
	}

	// If logged in, persist to user meta
	if ( $user_id > 0 ) {
		update_user_meta( $user_id, '_ashrafco_wishlist', $sanitized );
	}

	// Update cookie for immediate client synchronization
	$cookie_val = wp_json_encode( $sanitized );
	if ( ! headers_sent() ) {
		setcookie(
			'ashrafco_wishlist',
			$cookie_val,
			time() + ( 365 * DAY_IN_SECONDS ),
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			false // Accessible via JavaScript for real-time header sync
		);
	}
	$_COOKIE['ashrafco_wishlist'] = $cookie_val;

	return $sanitized;
}

/**
 * Get current wishlist item count.
 *
 * @param int|null $user_id Optional user ID.
 * @return int Total count of saved items.
 */
function ashrafco_get_wishlist_count( $user_id = null ) {
	$ids = ashrafco_get_wishlist_ids( $user_id );
	return count( $ids );
}

/**
 * Check if a specific product ID is in the wishlist.
 *
 * @param int      $product_id Product ID to check.
 * @param int|null $user_id    Optional user ID.
 * @return bool True if in wishlist, false otherwise.
 */
function ashrafco_is_product_in_wishlist( $product_id, $user_id = null ) {
	$product_id = absint( $product_id );
	if ( ! $product_id ) {
		return false;
	}

	$ids = ashrafco_get_wishlist_ids( $user_id );
	return in_array( $product_id, $ids, true );
}

/**
 * ============================================================================
 * 2. Login Synchronization (Merge Guest items into Customer Account)
 * ============================================================================
 */

/**
 * Synchronize guest cookie items with customer account upon authentication.
 *
 * @param string  $user_login User login username.
 * @param WP_User $user       Authenticated WP_User instance.
 */
function ashrafco_sync_wishlist_on_login( $user_login, $user ) {
	if ( ! ( $user instanceof WP_User ) || empty( $user->ID ) ) {
		return;
	}

	// Fetch existing user items
	$user_ids = ashrafco_get_wishlist_ids( $user->ID );

	// Fetch guest cookie items
	$guest_ids = array();
	if ( ! empty( $_COOKIE['ashrafco_wishlist'] ) ) {
		$raw    = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['ashrafco_wishlist'] ) ) );
		$parsed = json_decode( $raw, true );
		if ( is_array( $parsed ) ) {
			foreach ( $parsed as $val ) {
				if ( is_numeric( $val ) && (int) $val > 0 ) {
					$guest_ids[] = (int) $val;
				}
			}
		}
	}

	// Merge guest items into customer account and synchronize cookie
	ashrafco_set_wishlist_ids( array_merge( $user_ids, $guest_ids ), $user->ID );
}
add_action( 'wp_login', 'ashrafco_sync_wishlist_on_login', 10, 2 );

/**
 * Synchronize guest cookie items when a new customer registers.
 *
 * @param int $customer_id Newly created customer/user ID.
 */
function ashrafco_sync_wishlist_on_registration( $customer_id ) {
	$customer_id = absint( $customer_id );
	if ( ! $customer_id ) {
		return;
	}

	$guest_ids = array();
	if ( ! empty( $_COOKIE['ashrafco_wishlist'] ) ) {
		$raw    = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['ashrafco_wishlist'] ) ) );
		$parsed = json_decode( $raw, true );
		if ( is_array( $parsed ) ) {
			foreach ( $parsed as $val ) {
				if ( is_numeric( $val ) && (int) $val > 0 ) {
					$guest_ids[] = (int) $val;
				}
			}
		}
	}

	if ( ! empty( $guest_ids ) ) {
		$user_ids = ashrafco_get_wishlist_ids( $customer_id );
		ashrafco_set_wishlist_ids( array_merge( $user_ids, $guest_ids ), $customer_id );
	}
}
add_action( 'woocommerce_created_customer', 'ashrafco_sync_wishlist_on_registration', 10, 1 );
add_action( 'user_register', 'ashrafco_sync_wishlist_on_registration', 10, 1 );

/**
 * Clear wishlist cookie on user logout to maintain strict account isolation.
 */
function ashrafco_clear_wishlist_on_logout() {
	if ( isset( $_COOKIE['ashrafco_wishlist'] ) ) {
		unset( $_COOKIE['ashrafco_wishlist'] );
	}
	if ( ! headers_sent() ) {
		setcookie(
			'ashrafco_wishlist',
			'',
			time() - YEAR_IN_SECONDS,
			COOKIEPATH ? COOKIEPATH : '/',
			COOKIE_DOMAIN,
			is_ssl(),
			false
		);
	}
}
add_action( 'wp_logout', 'ashrafco_clear_wishlist_on_logout' );

/**
 * ============================================================================
 * 3. Cache Exclusion for Wishlist Route
 * ============================================================================
 */

/**
 * Prevent intermediate and browser caching on the private wishlist view.
 */
function ashrafco_wishlist_prevent_caching() {
	if ( is_admin() ) {
		return;
	}

	$is_wishlist_page = false;
	if ( is_page( 'wishlist' ) ) {
		$is_wishlist_page = true;
	} else {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? untrailingslashit( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';
		if ( str_ends_with( $request_uri, '/wishlist' ) ) {
			$is_wishlist_page = true;
		}
	}

	if ( $is_wishlist_page ) {
		nocache_headers();

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTMINIFY' ) ) {
			define( 'DONOTMINIFY', true );
		}

		// LiteSpeed Cache explicit non-cacheable action
		if ( function_exists( 'do_action' ) ) {
			do_action( 'litespeed_control_set_nocache', 'Ashrafco Wishlist Page' );
		}
	}
}
add_action( 'template_redirect', 'ashrafco_wishlist_prevent_caching', 1 );

/**
 * ============================================================================
 * 4. AJAX Handlers: Fetch Cards, Toggle, Remove & Sync
 * ============================================================================
 */

/**
 * AJAX: Retrieve rendered WooCommerce product cards for wishlist.
 */
function ashrafco_ajax_get_wishlist_items() {
	check_ajax_referer( 'ashrafco_wishlist_nonce', 'nonce' );

	$user_id   = get_current_user_id();
	$client_ids = array();

	// Accept explicit IDs provided by client (e.g. from guest localStorage)
	if ( isset( $_POST['product_ids'] ) ) {
		$raw = wp_unslash( $_POST['product_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $raw ) ) {
			$client_ids = array_map( 'absint', $raw );
		} elseif ( is_string( $raw ) ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$client_ids = array_map( 'absint', $decoded );
			}
		}
	}

	// If logged in, fetch authoritative user meta
	if ( $user_id > 0 ) {
		$target_ids = ashrafco_get_wishlist_ids( $user_id );
	} else {
		// Guest: use client IDs or cookie IDs
		if ( ! empty( $client_ids ) ) {
			$target_ids = array_values( array_unique( array_filter( $client_ids ) ) );
			ashrafco_set_wishlist_ids( $target_ids, 0 );
		} else {
			$target_ids = ashrafco_get_wishlist_ids( 0 );
		}
	}

	$rendered_cards = '';
	$valid_ids      = array();

	if ( ! empty( $target_ids ) ) {
		foreach ( $target_ids as $pid ) {
			$pid = absint( $pid );
			if ( ! $pid ) {
				continue;
			}

			$product = wc_get_product( $pid );

			// Filter out non-existent, trashed, or non-visible products
			if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
				continue;
			}

			if ( 'trash' === $product->get_status() ) {
				continue;
			}

			// Render product card component safely
			ob_start();
			$GLOBALS['product'] = $product;
			get_template_part( 'template-parts/woocommerce/content-product-card' );
			$card_html = ob_get_clean();

			if ( ! empty( $card_html ) ) {
				$rendered_cards .= $card_html;
				$valid_ids[]     = $pid;
			}
		}
	}

	// If any invalid IDs were removed, update storage so client doesn't retry them
	if ( count( $valid_ids ) !== count( $target_ids ) ) {
		ashrafco_set_wishlist_ids( $valid_ids, $user_id );
	}

	wp_send_json_success( array(
		'html'         => $rendered_cards,
		'count'        => count( $valid_ids ),
		'valid_ids'    => $valid_ids,
		'is_logged_in' => $user_id > 0,
	) );
}
add_action( 'wp_ajax_ashrafco_get_wishlist_items', 'ashrafco_ajax_get_wishlist_items' );
add_action( 'wp_ajax_nopriv_ashrafco_get_wishlist_items', 'ashrafco_ajax_get_wishlist_items' );

/**
 * AJAX: Toggle product in wishlist (Add / Remove).
 */
function ashrafco_ajax_toggle_wishlist() {
	check_ajax_referer( 'ashrafco_wishlist_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'معرف المنتج غير صالح.', 'ashrafco' ) ) );
	}

	$product = wc_get_product( $product_id );
	if ( ! $product || ! is_a( $product, 'WC_Product' ) || 'trash' === $product->get_status() ) {
		wp_send_json_error( array( 'message' => __( 'المنتج المطلوب غير متاح أو تم حذفه.', 'ashrafco' ) ) );
	}

	$user_id = get_current_user_id();
	$ids     = ashrafco_get_wishlist_ids( $user_id );

	$is_added = false;
	$key      = array_search( $product_id, $ids, true );

	if ( false !== $key ) {
		// Product currently in list: remove it
		unset( $ids[ $key ] );
		$ids      = array_values( $ids );
		$is_added = false;
	} else {
		// Add product
		$ids[]    = $product_id;
		$is_added = true;
	}

	// Persist updated list
	$updated_ids = ashrafco_set_wishlist_ids( $ids, $user_id );

	wp_send_json_success( array(
		'product_id' => $product_id,
		'is_added'   => $is_added,
		'count'      => count( $updated_ids ),
		'ids'        => $updated_ids,
		'message'    => $is_added
			? __( 'تمت إضافة المنتج إلى المفضلة.', 'ashrafco' )
			: __( 'تمت إزالة المنتج من المفضلة.', 'ashrafco' ),
	) );
}
add_action( 'wp_ajax_ashrafco_toggle_wishlist', 'ashrafco_ajax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_ashrafco_toggle_wishlist', 'ashrafco_ajax_toggle_wishlist' );

/**
 * AJAX: Explicitly remove product from wishlist.
 */
function ashrafco_ajax_remove_from_wishlist() {
	check_ajax_referer( 'ashrafco_wishlist_nonce', 'nonce' );

	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => __( 'معرف المنتج غير صالح.', 'ashrafco' ) ) );
	}

	$user_id = get_current_user_id();
	$ids     = ashrafco_get_wishlist_ids( $user_id );

	$key = array_search( $product_id, $ids, true );
	if ( false !== $key ) {
		unset( $ids[ $key ] );
		$ids = array_values( $ids );
	}

	$updated_ids = ashrafco_set_wishlist_ids( $ids, $user_id );

	wp_send_json_success( array(
		'product_id' => $product_id,
		'count'      => count( $updated_ids ),
		'ids'        => $updated_ids,
		'message'    => __( 'تمت إزالة المنتج من المفضلة.', 'ashrafco' ),
	) );
}
add_action( 'wp_ajax_ashrafco_remove_from_wishlist', 'ashrafco_ajax_remove_from_wishlist' );
add_action( 'wp_ajax_nopriv_ashrafco_remove_from_wishlist', 'ashrafco_ajax_remove_from_wishlist' );

/**
 * ============================================================================
 * 5. Wishlist Drawer Output & Drawer Items Rendering Helpers
 * ============================================================================
 */

/**
 * Enqueue Wishlist Drawer JavaScript and localize necessary settings.
 */
function ashrafco_enqueue_wishlist_drawer_scripts() {
	if ( is_admin() ) {
		return;
	}

	$js_rel = file_exists( get_template_directory() . '/assets/src/js/wishlist-drawer.js' ) ? '/assets/src/js/wishlist-drawer.js' : '/assets/js/wishlist-drawer.js';
	wp_enqueue_script(
		'ashrafco_wishlist_drawer',
		get_template_directory_uri() . $js_rel,
		array( 'jquery' ),
		function_exists( 'custom_store_get_asset_version' ) ? custom_store_get_asset_version( $js_rel ) : ( function_exists( 'ashrafco_get_asset_version' ) ? ashrafco_get_asset_version( $js_rel ) : '1.0.0' ),
		true
	);

	wp_localize_script(
		'ashrafco_wishlist_drawer',
		'ashrafcoWishlistData',
		array(
			'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'ashrafco_wishlist_nonce' ),
			'wishlistUrl' => function_exists( 'test_theme_get_wishlist_url' ) ? test_theme_get_wishlist_url() : home_url( '/wishlist/' ),
			'shopUrl'     => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
			'isRTL'       => is_rtl(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ashrafco_enqueue_wishlist_drawer_scripts', 26 );

/**
 * Output Wishlist Drawer Markup in wp_footer.
 */
function ashrafco_output_wishlist_drawer() {
	if ( is_admin() ) {
		return;
	}

	get_template_part( 'template-parts/cart/wishlist-drawer' );
}
add_action( 'wp_footer', 'ashrafco_output_wishlist_drawer', 20 );

/**
 * Helper to render individual wishlist items HTML for the wishlist drawer.
 * Matches Ashrafco Cart Drawer design closely:
 * - Product thumbnail on the right
 * - Product title, price, and Quick Add to Cart button in middle (no quantity stepper)
 * - Trash icon on left to remove from wishlist (no cart totals)
 *
 * @param array|null $specific_ids Optional list of IDs to render.
 * @return string HTML output of wishlist items.
 */
function ashrafco_get_wishlist_drawer_items_html( $specific_ids = null ) {
	$user_id = get_current_user_id();
	$ids     = is_array( $specific_ids ) ? $specific_ids : ashrafco_get_wishlist_ids( $user_id );

	ob_start();

	if ( empty( $ids ) ) {
		?>
		<div class="ashrafco-wishlist-empty-state text-center py-12 px-6 space-y-4">
			<div class="w-20 h-20 mx-auto rounded-3xl bg-red-50 text-[#d90429] flex items-center justify-center shadow-inner">
				<svg class="w-10 h-10 stroke-[#d90429] fill-none" stroke-width="1.8" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round"
						d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
				</svg>
			</div>
			<div class="space-y-1">
				<h3 class="text-lg font-bold text-slate-900 m-0"><?php esc_html_e( 'قائمة المفضلة فارغة', 'ashrafco' ); ?></h3>
				<p class="text-xs sm:text-sm text-slate-500 max-w-xs mx-auto leading-relaxed m-0">
					<?php esc_html_e( 'لم تقم بحفظ أي منتجات في المفضلة بعد. تصفح أحدث ماكينات ومعدات أشرفكو الآن واضغط على رمز القلب.', 'ashrafco' ); ?>
				</p>
			</div>
			<div class="pt-3">
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"
					class="ashrafco-empty-shop-btn inline-flex items-center justify-center gap-2 bg-[#d90429] hover:bg-[#b90323] text-white font-bold text-sm py-3 px-8 rounded-xl shadow-md transition-all active:scale-95 no-underline">
					<span><?php esc_html_e( 'تصفح المنتجات الآن', 'ashrafco' ); ?></span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
					</svg>
				</a>
			</div>
		</div>
		<?php
	} else {
		?>
		<div class="ashrafco-wishlist-items-list divide-y divide-slate-100">
			<?php
			$rendered_count = 0;
			foreach ( $ids as $product_id ) {
				$product_id = absint( $product_id );
				if ( ! $product_id ) {
					continue;
				}

				$product = wc_get_product( $product_id );
				if ( ! $product || ! is_a( $product, 'WC_Product' ) || 'trash' === $product->get_status() ) {
					continue;
				}

				$product_name      = $product->get_name();
				$product_permalink = $product->get_permalink();
				$price_html        = $product->get_price_html();
				$is_in_stock       = $product->is_in_stock();

				// Product Image Thumbnail
				$thumbnail = $product->get_image(
					array( 76, 76 ),
					array(
						'class' => 'w-[72px] h-[72px] sm:w-[76px] sm:h-[76px] object-contain rounded-xl bg-slate-50 border border-slate-100 p-1 flex-shrink-0 transition-transform group-hover:scale-105',
						'alt'   => esc_attr( $product_name ),
					)
				);

				$rendered_count++;
				?>
				<div class="ashrafco-wishlist-item py-4 flex items-center justify-between gap-3 group transition-colors relative"
					data-product-id="<?php echo esc_attr( $product_id ); ?>">

					<!-- 1. Right: Product Image -->
					<div class="ashrafco-item-img-wrapper flex-shrink-0">
						<?php if ( ! empty( $product_permalink ) ) : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="block" title="<?php echo esc_attr( $product_name ); ?>">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php else : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>

					<!-- 2. Middle: Product Details (Title, Price, Quick Add-to-Cart) -->
					<div class="ashrafco-item-content flex-1 min-w-0 pr-1 text-right">
						<!-- Title -->
						<h4 class="text-xs sm:text-sm font-bold text-slate-800 line-clamp-2 leading-snug m-0">
							<?php if ( ! empty( $product_permalink ) ) : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>" class="hover:text-[#d90429] transition-colors no-underline text-slate-900">
									<?php echo esc_html( $product_name ); ?>
								</a>
							<?php else : ?>
								<?php echo esc_html( $product_name ); ?>
							<?php endif; ?>
						</h4>

						<!-- Price in vibrant green matching cart design (display only active price after discount, no underline) -->
						<div class="ashrafco-item-price text-xs sm:text-sm font-extrabold text-[#059669] font-mono mt-1 no-underline [&_ins]:no-underline [&_ins]:font-normal [&_ins]:bg-transparent">
							<?php
							$current_p = $product->get_price();
							if ( '' !== $current_p ) {
								echo wp_kses_post( wc_price( $current_p ) );
							} else {
								echo wp_kses_post( $product->get_price_html() );
							}
							?>
						</div>

						<!-- Quick Add To Cart Button -->
						<div class="mt-2.5 inline-flex items-center">
							<?php if ( $is_in_stock ) : ?>
								<button type="button"
									class="ashrafco-wishlist-add-to-cart inline-flex items-center gap-1.5 bg-[#d90429] hover:bg-[#b90323] text-white text-xs font-bold py-1.5 px-3 rounded-lg shadow-xs transition-all active:scale-95 cursor-pointer focus:outline-none"
									data-product-id="<?php echo esc_attr( $product_id ); ?>">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
									</svg>
									<span><?php esc_html_e( 'أضف للسلة', 'ashrafco' ); ?></span>
								</button>
							<?php else : ?>
								<span class="text-xs font-medium text-slate-400 bg-slate-100 px-2.5 py-1 rounded-md">
									<?php esc_html_e( 'غير متوفر حالياً', 'ashrafco' ); ?>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<!-- 3. Left: Delete Trash Button to remove from Wishlist -->
					<div class="ashrafco-item-actions flex flex-col items-center justify-center self-stretch flex-shrink-0 pl-1 py-0.5">
						<button type="button"
							class="ashrafco-wishlist-remove-btn w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-[#d90429] hover:bg-red-50/60 transition-all focus:outline-none cursor-pointer"
							data-product-id="<?php echo esc_attr( $product_id ); ?>"
							title="<?php esc_attr_e( 'حذف من المفضلة', 'ashrafco' ); ?>"
							aria-label="<?php esc_attr_e( 'حذف من المفضلة', 'ashrafco' ); ?>">
							<svg class="w-5 h-5 transition-transform active:scale-110" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
						</button>
					</div>

				</div>
				<?php
			}
			?>
		</div>
		<?php
		if ( 0 === $rendered_count ) {
			// If all IDs in list were deleted/trashed products, output empty state
			ob_clean();
			?>
			<div class="ashrafco-wishlist-empty-state text-center py-12 px-6 space-y-4">
				<div class="w-20 h-20 mx-auto rounded-3xl bg-red-50 text-[#d90429] flex items-center justify-center shadow-inner">
					<svg class="w-10 h-10 stroke-[#d90429] fill-none" stroke-width="1.8" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round"
							d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
					</svg>
				</div>
				<div class="space-y-1">
					<h3 class="text-lg font-bold text-slate-900 m-0"><?php esc_html_e( 'قائمة المفضلة فارغة', 'ashrafco' ); ?></h3>
					<p class="text-xs sm:text-sm text-slate-500 max-w-xs mx-auto leading-relaxed m-0">
						<?php esc_html_e( 'لم تقم بحفظ أي منتجات في المفضلة بعد.', 'ashrafco' ); ?>
					</p>
				</div>
			</div>
			<?php
		}
	}

	return ob_get_clean();
}

/**
 * Helper to render wishlist drawer footer HTML.
 * Side-by-side action buttons matching cart drawer styling:
 * - "متابعة التسوق" (Continue shopping / Close drawer)
 * - "عرض كل المفضلة" (View full wishlist page)
 *
 * @return string HTML output of wishlist footer.
 */
function ashrafco_get_wishlist_drawer_footer_html() {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$count    = function_exists( 'test_theme_get_wishlist_count' ) ? test_theme_get_wishlist_count() : 0;

	if ( $count <= 0 ) {
		return '';
	}

	ob_start();
	?>
	<div class="ashrafco-wishlist-footer-inner">
		<!-- Action Buttons: Side-by-side matching Cart design -->
		<div class="ashrafco-wishlist-actions flex items-center gap-3">
			<!-- Secondary Button: Continue Shopping (Dark, on Right in RTL) -->
			<button type="button"
				class="ashrafco-wishlist-continue-btn flex-1 bg-[#1e293b] hover:bg-[#0f172a] text-white font-bold text-sm sm:text-base py-3.5 px-4 rounded-xl text-center shadow-xs transition-all active:scale-[0.98] focus:outline-none select-none cursor-pointer">
				<?php esc_html_e( 'متابعة التسوق', 'ashrafco' ); ?>
			</button>

			<!-- Primary Button: Browse Shop (Red, on Left in RTL) -->
			<a href="<?php echo esc_url( $shop_url ); ?>"
				class="ashrafco-wishlist-view-all flex-1 bg-[#d90429] hover:bg-[#b90323] text-white font-extrabold text-sm sm:text-base py-3.5 px-4 rounded-xl text-center shadow-md hover:shadow-lg transition-all active:scale-[0.98] no-underline select-none">
				<?php esc_html_e( 'تصفح المتجر', 'ashrafco' ); ?>
			</a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * AJAX: Retrieve rendered wishlist drawer HTML.
 */
function ashrafco_ajax_get_wishlist_drawer_html() {
	check_ajax_referer( 'ashrafco_wishlist_nonce', 'nonce' );

	$user_id   = get_current_user_id();
	$client_ids = array();

	if ( isset( $_POST['product_ids'] ) ) {
		$raw = wp_unslash( $_POST['product_ids'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( is_array( $raw ) ) {
			$client_ids = array_map( 'absint', $raw );
		} elseif ( is_string( $raw ) ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$client_ids = array_map( 'absint', $decoded );
			}
		}
	}

	if ( $user_id > 0 ) {
		$ids = ashrafco_get_wishlist_ids( $user_id );
	} else {
		if ( ! empty( $client_ids ) ) {
			$ids = array_values( array_unique( array_filter( $client_ids ) ) );
			ashrafco_set_wishlist_ids( $ids, 0 );
		} else {
			$ids = ashrafco_get_wishlist_ids( 0 );
		}
	}

	wp_send_json_success( array(
		'count'       => count( $ids ),
		'items_html'  => ashrafco_get_wishlist_drawer_items_html( $ids ),
		'footer_html' => ashrafco_get_wishlist_drawer_footer_html(),
		'ids'         => $ids,
	) );
}
add_action( 'wp_ajax_ashrafco_get_wishlist_drawer_html', 'ashrafco_ajax_get_wishlist_drawer_html' );
add_action( 'wp_ajax_nopriv_ashrafco_get_wishlist_drawer_html', 'ashrafco_ajax_get_wishlist_drawer_html' );

/**
 * Retrieve the current wishlist URL.
 *
 * @return string Wishlist URL.
 */
if ( ! function_exists( 'test_theme_get_wishlist_url' ) ) {
	function test_theme_get_wishlist_url() {
		// 1. YITH WooCommerce Wishlist
		if ( function_exists( 'YITH_WCWL' ) && method_exists( YITH_WCWL(), 'get_wishlist_url' ) ) {
			$url = YITH_WCWL()->get_wishlist_url();
			if ( ! empty( $url ) ) {
				return esc_url( $url );
			}
		}

		// 2. TI WooCommerce Wishlist
		if ( function_exists( 'tinv_url_wishlist' ) ) {
			$url = tinv_url_wishlist();
			if ( ! empty( $url ) ) {
				return esc_url( $url );
			}
		}

		// 3. Option-based page lookups
		$yith_page = get_option( 'yith_wcwl_wishlist_page_id' );
		if ( $yith_page && get_post_status( $yith_page ) ) {
			return esc_url( get_permalink( $yith_page ) );
		}

		$tinv_page = get_option( 'tinvwl_wishlist_page_id' );
		if ( $tinv_page && get_post_status( $tinv_page ) ) {
			return esc_url( get_permalink( $tinv_page ) );
		}

		// 4. Default clean route
		return esc_url( home_url( '/wishlist/' ) );
	}
}

if ( ! function_exists( 'ashrafco_get_wishlist_url' ) ) {
	function ashrafco_get_wishlist_url() {
		return test_theme_get_wishlist_url();
	}
}

if ( ! function_exists( 'ashrafco_get_wishlist_page_url' ) ) {
	function ashrafco_get_wishlist_page_url() {
		return test_theme_get_wishlist_url();
	}
}

/**
 * Retrieve the current wishlist item count from active plugins, cookies, or user meta.
 *
 * @return int Number of saved wishlist items.
 */
if ( ! function_exists( 'test_theme_get_wishlist_count' ) ) {
	function test_theme_get_wishlist_count() {
		if ( function_exists( 'ashrafco_get_wishlist_count' ) ) {
			return ashrafco_get_wishlist_count();
		}

		if ( function_exists( 'yith_wcwl_count_all_products' ) ) {
			return (int) yith_wcwl_count_all_products();
		}

		if ( function_exists( 'tinv_get_wishlist_products_count' ) ) {
			return (int) tinv_get_wishlist_products_count();
		}

		if ( ! empty( $_COOKIE['ashrafco_wishlist'] ) ) {
			$raw = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['ashrafco_wishlist'] ) ) );
			$items = json_decode( $raw, true );
			if ( is_array( $items ) ) {
				return count( $items );
			}
			if ( is_numeric( $raw ) ) {
				return (int) $raw;
			}
		}

		return 0;
	}
}

/**
 * Check if a specific product ID is currently saved in the wishlist.
 *
 * @param int $product_id Product ID to check.
 * @return bool True if product is in wishlist, false otherwise.
 */
if ( ! function_exists( 'test_theme_is_product_in_wishlist' ) ) {
	function test_theme_is_product_in_wishlist( $product_id ) {
		$product_id = (int) $product_id;
		if ( ! $product_id ) {
			return false;
		}

		if ( function_exists( 'ashrafco_is_product_in_wishlist' ) ) {
			return ashrafco_is_product_in_wishlist( $product_id );
		}

		if ( function_exists( 'yith_wcwl_is_product_in_wishlist' ) && yith_wcwl_is_product_in_wishlist( $product_id ) ) {
			return true;
		}

		if ( ! empty( $_COOKIE['ashrafco_wishlist'] ) ) {
			$raw = stripslashes( sanitize_text_field( wp_unslash( $_COOKIE['ashrafco_wishlist'] ) ) );
			$items = json_decode( $raw, true );
			if ( is_array( $items ) ) {
				if ( in_array( $product_id, array_map( 'intval', $items ), true ) ) {
					return true;
				}
				if ( isset( $items[ $product_id ] ) || isset( $items[ (string) $product_id ] ) ) {
					return true;
				}
			}
		}

		return false;
	}
}

if ( ! function_exists( 'ashrafco_wishlist_template_fallback' ) ) {
	/**
	 * Template fallback loader for /wishlist/ route.
	 *
	 * @param string $template Path to current template.
	 * @return string Filtered template path.
	 */
	function ashrafco_wishlist_template_fallback( $template ) {
		if ( is_admin() || wp_doing_ajax() ) {
			return $template;
		}

		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$path        = trim( (string) parse_url( $request_uri, PHP_URL_PATH ), '/' );

		if ( 'wishlist' === $path || is_page( 'wishlist' ) || is_page_template( 'page-wishlist.php' ) ) {
			$wishlist_template = get_template_directory() . '/page-wishlist.php';
			if ( file_exists( $wishlist_template ) ) {
				return $wishlist_template;
			}
		}

		return $template;
	}
	add_filter( 'template_include', 'ashrafco_wishlist_template_fallback', 99 );
}


