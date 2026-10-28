<?php
/**
 * Modern Industrial Mini-Cart Template Override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="ashrafco-mini-cart-wrapper font-sans text-right overflow-hidden" dir="rtl">

	<?php if ( ! WC()->cart->is_empty() ) : ?>

		<!-- Mini-Cart Header -->
		<div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
			<div class="flex items-center gap-2">
				<span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
				<span class="font-extrabold text-slate-900 text-sm sm:text-base">سلة المشتريات</span>
				<span class="text-[11px] font-bold bg-brand-500 text-white px-2 py-0.5 rounded-full font-mono">
					<?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?> قطع
				</span>
			</div>
			<button
				type="button"
				class="ashrafco-mini-cart-close text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
				aria-label="<?php esc_attr_e( 'إغلاق السلة', 'ashrafco' ); ?>"
				onclick="document.getElementById('test-theme-cart-dropdown') && document.getElementById('test-theme-cart-dropdown').classList.add('hidden');"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<!-- Free Shipping Progress Bar (Mini-Cart) -->
		<?php
		if ( function_exists( 'ashrafco_render_free_shipping_bar' ) ) {
			ashrafco_render_free_shipping_bar( 'mini-cart' );
		}
		?>

		<!-- Mini-Cart Items List -->
		<ul class="woocommerce-mini-cart cart_list product_list_widget divide-y divide-slate-100 max-h-[60vh] overflow-y-auto pl-1 pr-0 space-y-2 list-none m-0 p-0 <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
			<?php
			do_action( 'woocommerce_before_mini_cart_contents' );

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 52, 52 ), array( 'class' => 'w-12 h-12 rounded-xl object-cover border border-slate-200 bg-white flex-shrink-0' ) ), $cart_item, $cart_item_key );
					$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<li class="woocommerce-mini-cart-item mini_cart_item py-2.5 flex items-center justify-between gap-3 group relative <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">
						
						<!-- Thumbnail -->
						<div class="flex-shrink-0">
							<?php if ( empty( $product_permalink ) ) : ?>
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>">
									<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</a>
							<?php endif; ?>
						</div>

						<!-- Details -->
						<div class="min-w-0 flex-1">
							<?php if ( empty( $product_permalink ) ) : ?>
								<h5 class="text-xs font-bold text-slate-800 line-clamp-1 leading-snug m-0">
									<?php echo wp_kses_post( $product_name ); ?>
								</h5>
							<?php else : ?>
								<h5 class="text-xs font-bold text-slate-800 hover:text-primary transition-colors line-clamp-1 leading-snug m-0">
									<a href="<?php echo esc_url( $product_permalink ); ?>">
										<?php echo wp_kses_post( $product_name ); ?>
									</a>
								</h5>
							<?php endif; ?>

							<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

							<div class="flex items-center gap-2 mt-1 text-xs">
								<span class="text-slate-400 font-medium">الكمية: <strong class="text-slate-700 font-mono"><?php echo esc_html( $cart_item['quantity'] ); ?></strong></span>
								<span class="text-slate-300">•</span>
								<span class="font-bold text-primary font-mono"><?php echo $product_price; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
						</div>

						<!-- Remove Item Button -->
						<div class="flex-shrink-0">
							<?php
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="remove remove_from_cart_button p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors inline-flex items-center justify-center cursor-pointer" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									/* translators: %s is the product name */
									esc_attr( sprintf( __( 'حذف %s من السلة', 'ashrafco' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $cart_item_key ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</div>

					</li>
					<?php
				}
			}

			do_action( 'woocommerce_mini_cart_contents' );
			?>
		</ul>

		<!-- Subtotal Section -->
		<div class="pt-3 border-t border-slate-100 mt-2 space-y-3">
			<div class="flex items-center justify-between text-xs sm:text-sm">
				<span class="font-bold text-slate-600">المجموع الفرعي:</span>
				<span class="font-black text-slate-900 font-mono text-sm sm:text-base">
					<?php echo WC()->cart->get_cart_subtotal(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>

			<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

			<!-- Actions: Stacked CTA Buttons -->
			<div class="space-y-2 pt-1">
				<!-- Primary Button: Checkout -->
				<a
					href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
					class="btn-primary button checkout wc-forward w-full inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-md py-2.5 px-4 text-xs sm:text-sm active:scale-[0.99] transition-all cursor-pointer select-none"
				>
					<span>إتمام الطلب</span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>

				<!-- Secondary Button: View Cart -->
				<a
					href="<?php echo esc_url( wc_get_cart_url() ); ?>"
					class="btn-secondary button wc-forward w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl py-2 px-4 text-xs sm:text-sm transition-colors cursor-pointer select-none"
				>
					<span>عرض السلة</span>
				</a>
			</div>

			<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

			<p class="text-[10px] text-slate-400 text-center m-0 pt-0.5 font-medium">
				🛡️ فحص ومعاينة قبل الاستلام والدفع
			</p>
		</div>

	<?php else : ?>

		<!-- Empty State Card -->
		<div class="woocommerce-mini-cart__empty-message text-center py-7 px-3 space-y-3">
			<div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 shadow-inner">
				<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
			</div>
			<div>
				<h4 class="text-sm font-extrabold text-slate-900 m-0">سلتك فارغة حالياً</h4>
				<p class="text-xs text-slate-400 mt-1 mb-0 leading-relaxed">
					لم تقم بإضافة أي ماكينات أو أدوات بورسلين بعد.
				</p>
			</div>
			<div class="pt-1">
				<a
					href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
					class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-orange-600 transition-colors shadow-sm select-none"
				>
					<span>تصفح ماكينات ومعدات السيراميك</span>
					<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
		</div>

	<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
