<?php
/**
 * Modern Orders List Template Override for Ashrafco Child Theme
 *
 * Matching Reference Screenshot (Desktop & Mobile Responsive)
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	wc_print_notice( __( 'يرجى تسجيل الدخول لعرض الطلبات الخاصة بك.', 'ashrafco' ), 'notice' );
	return;
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;

// Handle Email Verification Action if clicked
if ( isset( $_GET['action'] ) && 'verify_email' === $_GET['action'] && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ashrafco_verify_email' ) ) {
	update_user_meta( $user_id, 'ashrafco_email_verified', '1' );
	if ( function_exists( 'wc_add_notice' ) ) {
		wc_add_notice( __( 'تم تأكيد بريدك الإلكتروني بنجاح وربطه بحسابك!', 'ashrafco' ), 'success' );
	}
}

$is_email_verified = (bool) get_user_meta( $user_id, 'ashrafco_email_verified', true );

// Retrieve customer order status counts via direct DB query (avoids full order object hydration)
$status_counts    = function_exists( 'ashrafco_get_customer_order_status_counts' ) ? ashrafco_get_customer_order_status_counts( $user_id ) : array( 'all' => 0, 'processing' => 0, 'shipped' => 0, 'completed' => 0, 'cancelled' => 0 );
$count_all        = $status_counts['all'];
$count_processing = $status_counts['processing'];
$count_shipped    = $status_counts['shipped'];
$count_completed  = $status_counts['completed'];
$count_cancelled  = $status_counts['cancelled'];

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<div class="ashrafco-orders-view space-y-6 font-sans select-none" dir="rtl">

	<!-- =========================================================================
	     1. EMAIL VERIFICATION NOTICE BANNER (Strictly Matching Screenshot)
	     ========================================================================= -->
	<?php if ( ! $is_email_verified ) : ?>
		<div id="ashrafco-email-verify-notice" class="bg-sky-50/80 border border-sky-100 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
			<!-- Right Side (in RTL): Warning Icon + Title & Subtitle -->
			<div class="flex items-center gap-3.5 min-w-0">
				<div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 border border-amber-200/90 flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
					</svg>
				</div>
				<div class="min-w-0">
					<h4 class="text-sm sm:text-base font-extrabold text-slate-900 m-0">
						أكّد بريدك الإلكتروني لعرض الطلبات السابقة وربطها بحسابك
					</h4>
					<p class="text-xs text-slate-500 m-0 mt-0.5">
						سيساعدنا ذلك في إرسال تحديثات الطلبات والعروض المهمة.
					</p>
				</div>
			</div>

			<!-- Left Side (in RTL): Action Button + Mailbox Icon -->
			<div class="flex items-center gap-2.5 flex-shrink-0 w-full md:w-auto justify-end">
				<a
					href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'verify_email', wc_get_account_endpoint_url( 'orders' ) ), 'ashrafco_verify_email' ) ); ?>"
					class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#059669] hover:bg-[#047857] transition-all shadow-xs active:scale-95"
				>
					<span>تأكيد البريد الإلكتروني</span>
				</a>
				<div class="w-10 h-10 rounded-xl bg-white border border-sky-200 text-sky-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
					</svg>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- =========================================================================
	     2. PAGE HEADING & REORDER ACTION (Strictly Matching Screenshot)
	     ========================================================================= -->
	<div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200/80 gap-4">
		<!-- Title & Subtitle with Red Shopping Bag Icon -->
		<div class="flex items-center gap-3">
			<span class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
			</span>
			<div>
				<h2 class="text-xl sm:text-2xl font-black text-slate-900 m-0 leading-tight">
					سجل الطلبات والمشتريات
				</h2>
				<p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5">
					تابع جميع طلباتك ومشترياتك مع إمكانية إعادة الطلب بسهولة.
				</p>
			</div>
		</div>

		<!-- New Products CTA Button Linking to Shop -->
		<a
			href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
			class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-primary bg-rose-50 hover:bg-rose-100/80 border border-rose-100 transition-colors flex-shrink-0 select-none shadow-2xs"
		>
			<span>طلب منتجات جديدة</span>
			<svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
			</svg>
		</a>
	</div>

	<!-- =========================================================================
	     3. ORDER STATUS FILTERS ROW (Strictly Matching Screenshot Segmented Bar)
	     ========================================================================= -->
	<div class="w-full overflow-x-auto no-scrollbar py-1">
		<div id="ashrafco-orders-filters" class="inline-flex items-center border border-slate-200/80 bg-white/80 rounded-2xl p-1 gap-1 min-w-full sm:min-w-0 shadow-2xs">

			<!-- Tab 1: All Orders (الكل) -->
			<button
				type="button"
				data-status-filter="all"
				class="filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all duration-150 select-none flex-shrink-0 border border-rose-400 bg-rose-50/50 text-primary shadow-2xs cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h.01M8 6h12M4 12h.01M8 12h12M4 18h.01M8 18h12" />
				</svg>
				<span>الكل</span>
				<span class="count-badge text-xs opacity-90">(<?php echo esc_html( $count_all ); ?>)</span>
			</button>

			<span class="filter-divider h-5 w-[1px] bg-slate-200 flex-shrink-0"></span>

			<!-- Tab 2: Processing (قيد المعالجة) -->
			<button
				type="button"
				data-status-filter="processing"
				class="filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 select-none flex-shrink-0 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
				</svg>
				<span>قيد المعالجة</span>
				<span class="count-badge text-xs text-slate-400">(<?php echo esc_html( $count_processing ); ?>)</span>
			</button>

			<span class="filter-divider h-5 w-[1px] bg-slate-200 flex-shrink-0"></span>

			<!-- Tab 3: Shipped (تم الشحن) -->
			<button
				type="button"
				data-status-filter="shipped"
				class="filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 select-none flex-shrink-0 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 8h4.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16h-3" />
				</svg>
				<span>تم الشحن</span>
				<span class="count-badge text-xs text-slate-400">(<?php echo esc_html( $count_shipped ); ?>)</span>
			</button>

			<span class="filter-divider h-5 w-[1px] bg-slate-200 flex-shrink-0"></span>

			<!-- Tab 4: Completed (تم التسليم) -->
			<button
				type="button"
				data-status-filter="completed"
				class="filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 select-none flex-shrink-0 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
				</svg>
				<span>تم التسليم</span>
				<span class="count-badge text-xs text-slate-400">(<?php echo esc_html( $count_completed ); ?>)</span>
			</button>

			<span class="filter-divider h-5 w-[1px] bg-slate-200 flex-shrink-0"></span>

			<!-- Tab 5: Cancelled (ملغي) -->
			<button
				type="button"
				data-status-filter="cancelled"
				class="filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 select-none flex-shrink-0 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
				<span>ملغي</span>
				<span class="count-badge text-xs text-slate-400">(<?php echo esc_html( $count_cancelled ); ?>)</span>
			</button>

		</div>
	</div>

	<!-- =========================================================================
	     4. ORDER CARDS LIST (Spacious White Cards with Strict Layout Hierarchy)
	     ========================================================================= -->
	<?php if ( $has_orders ) : ?>

		<div id="ashrafco-orders-container" class="space-y-5">
			<?php
			foreach ( $customer_orders->orders as $customer_order ) :
				$order         = wc_get_order( $customer_order );
				$order_id      = $order->get_id();
				$order_num     = $order->get_order_number();
				$status        = $order->get_status();
				$status_name   = wc_get_order_status_name( $status );
				$item_count    = $order->get_item_count();
				$items         = $order->get_items();
				$date_created  = $order->get_date_created();
				$date_placed   = $date_created ? wp_date( 'j F Y', $date_created->getTimestamp() ) : '';
				$pay_title     = $order->get_payment_method_title() ?: 'الدفع عند الاستلام (COD)';
				$customer_note = $order->get_customer_note();

				// Filter category classification
				$filter_cat = 'all';
				if ( in_array( $status, array( 'processing', 'pending', 'on-hold' ), true ) ) {
					$filter_cat = 'processing';
				} elseif ( in_array( $status, array( 'shipped', 'out-for-delivery' ), true ) ) {
					$filter_cat = 'shipped';
				} elseif ( 'completed' === $status ) {
					$filter_cat = 'completed';
				} elseif ( in_array( $status, array( 'cancelled', 'refunded', 'failed' ), true ) ) {
					$filter_cat = 'cancelled';
				}

				// Status Badges (Unified Colors & Dots)
				$badge_data  = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $status ) : array( 'class' => 'bg-slate-50 text-slate-700 border-slate-200', 'dot' => 'bg-slate-400', 'label' => $status_name );
				$badge_class = $badge_data['class'];
				$dot_class   = $badge_data['dot'];
				$status_name = $badge_data['label'];

				// First product details
				$first_item    = reset( $items );
				$first_name    = $first_item ? $first_item->get_name() : sprintf( __( 'معدات وأدوات %s', 'ashrafco' ), function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو' );
				$first_qty     = $first_item ? $first_item->get_quantity() : 1;
				$first_product = $first_item ? $first_item->get_product() : null;
				$first_img     = '';

				if ( $first_product && $first_product->get_image_id() ) {
					$first_img = wp_get_attachment_image_url( $first_product->get_image_id(), 'medium' );
				}
				if ( ! $first_img ) {
					$first_img = get_theme_file_uri( 'assets/images/categories/cat-manual-tools.png' );
				}

				// Reorder link
				$reorder_url = wp_nonce_url( add_query_arg( 'order_again', $order_id, wc_get_cart_url() ), 'woocommerce-order_again' );
				?>
				<div
					data-order-category="<?php echo esc_attr( $filter_cat ); ?>"
					class="order-card-item bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-slate-300 transition-all p-5 sm:p-6 space-y-4"
				>

					<!-- 1. Order Card Header (Order #, Date, Status Badge, Collapse Toggle) -->
					<div class="flex items-center justify-between pb-3.5 border-b border-slate-100 flex-wrap gap-3">
						<div class="flex items-center gap-3 flex-wrap">
							<h3 class="text-base sm:text-lg font-black text-slate-900 m-0">
								طلب رقم #<?php echo esc_html( $order_num ); ?>
							</h3>
							<div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
								<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
								</svg>
								<span><?php echo esc_html( $date_placed ); ?></span>
							</div>
							<span class="text-slate-300 hidden sm:inline">|</span>
							<!-- Status Pill with Dot Indicator -->
							<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo esc_attr( $badge_class ); ?>">
								<span class="w-1.5 h-1.5 rounded-full <?php echo esc_attr( $dot_class ); ?>"></span>
								<span><?php echo esc_html( $status_name ); ?></span>
							</span>
						</div>

						<!-- Expand / Collapse Toggle -->
						<button
							type="button"
							onclick="const body = this.closest('.order-card-item').querySelector('.order-card-body'); body.classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180');"
							class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-500 flex items-center justify-center transition-colors cursor-pointer select-none"
							aria-label="طي أو توسيع تفاصيل الطلب"
						>
							<svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
							</svg>
						</button>
					</div>

					<!-- 2. Order Card Collapsible Body -->
					<div class="order-card-body space-y-4">

						<!-- Inner Information Summary Box -->
						<div class="bg-slate-50/70 border border-slate-100/90 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">

							<!-- Product Preview Details (Right in RTL) -->
							<div class="flex items-center gap-3.5 min-w-0 flex-1">
								<div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white border border-slate-200/70 p-1 flex items-center justify-center flex-shrink-0 shadow-2xs overflow-hidden">
									<img
										src="<?php echo esc_url( $first_img ); ?>"
										alt="<?php echo esc_attr( $first_name ); ?>"
										class="w-full h-full object-contain"
										loading="lazy"
									/>
								</div>
								<div class="min-w-0 space-y-1">
									<h4 class="text-sm sm:text-base font-extrabold text-slate-900 m-0 truncate">
										<?php echo esc_html( $first_name ); ?>
									</h4>
									<div class="flex items-center gap-2 text-xs text-slate-500">
										<span class="inline-flex items-center gap-1 text-slate-500">
											<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
											</svg>
											<span><?php echo esc_html( $item_count ); ?> قطعة</span>
										</span>
										<?php if ( count( $items ) > 1 ) : ?>
											<span class="text-slate-400 text-[11px]">(+ <?php echo esc_html( count( $items ) - 1 ); ?> منتجات أخرى)</span>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Order Total Section (Middle in RTL) -->
							<div class="text-right md:text-center px-0 md:px-6 py-2 md:py-0 border-t md:border-t-0 md:border-r md:border-l border-slate-200/60 flex-shrink-0">
								<span class="text-xs text-slate-400 font-bold block mb-0.5">
									إجمالي الطلب
								</span>
								<span class="text-base sm:text-lg font-black text-[#059669] block [&_.woocommerce-Price-amount]:text-[#059669] [&_.woocommerce-Price-currencySymbol]:text-[#059669]">
									<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
								</span>
							</div>

							<!-- Payment Method Section (Left in RTL) -->
							<div class="text-right px-0 md:pr-4 flex-shrink-0">
								<span class="text-xs text-slate-400 font-bold block mb-0.5">
									طريقة الدفع
								</span>
								<span class="text-xs sm:text-sm font-bold text-slate-600 block">
									<?php echo esc_html( $pay_title ); ?>
								</span>
							</div>

						</div>

						<!-- Metadata, Notes & Action Buttons in a Single Balanced Row -->
						<div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

							<!-- Notes (Right in RTL) -->
							<div class="min-w-0">
								<div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold">
									<svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
									</svg>
									<span>ملاحظات الطلب</span>
								</div>
								<p class="text-xs text-slate-500 m-0 mt-0.5 truncate max-w-xs sm:max-w-md font-medium">
									<?php echo esc_html( $customer_note ?: $first_name ); ?>
								</p>
							</div>

							<!-- Actions (Left in RTL): Reorder + Invoice/Details -->
							<div class="flex items-center gap-2.5 flex-wrap flex-shrink-0 w-full md:w-auto justify-end">

								<!-- Secondary Action: Reorder Button -->
								<a
									href="<?php echo esc_url( $reorder_url ); ?>"
									class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors select-none"
								>
									<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
									</svg>
									<span>إعادة الطلب</span>
								</a>

								<!-- Primary Action: Order Details & Invoice Button -->
								<a
									href="<?php echo esc_url( $order->get_view_order_url() ); ?>"
									class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-[#0f172a] hover:bg-slate-800 transition-colors shadow-xs select-none"
								>
									<svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
									</svg>
									<span>تفاصيل وفاتورة الطلب</span>
								</a>

							</div>

						</div>

					</div>

				</div>
			<?php endforeach; ?>
		</div>

		<!-- Filter Empty State Message (Shown when filter has 0 matching orders) -->
		<div id="ashrafco-filter-empty-state" class="hidden bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 text-center max-w-md mx-auto">
			<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center mx-auto mb-3">
				<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
				</svg>
			</div>
			<h4 class="text-sm font-extrabold text-slate-900 m-0">لا توجد طلبات تطابق هذه الحالة</h4>
			<p class="text-xs text-slate-400 mt-1 m-0">اختر حالة أخرى أو اعرض جميع الطلبات.</p>
			<button
				type="button"
				onclick="document.querySelector('[data-status-filter=\'all\']').click();"
				class="mt-4 px-4 py-2 rounded-xl text-xs font-bold text-white bg-primary hover:bg-primary-hover transition-colors cursor-pointer"
			>
				عرض الكل
			</button>
		</div>

		<!-- Orders Pagination -->
		<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
			<div class="flex items-center justify-between pt-6 border-t border-slate-200/80">
				<?php if ( 1 !== $current_page ) : ?>
					<a
						class="px-5 py-2.5 text-xs font-bold bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors shadow-xs"
						href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"
					>
						&rarr; الصفحة السابقة
					</a>
				<?php else : ?>
					<div></div>
				<?php endif; ?>

				<span class="text-xs text-slate-500 font-bold">
					صفحة <?php echo esc_html( $current_page ); ?> من <?php echo esc_html( $customer_orders->max_num_pages ); ?>
				</span>

				<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
					<a
						class="px-5 py-2.5 text-xs font-bold bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 transition-colors shadow-xs"
						href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"
					>
						الصفحة التالية &larr;
					</a>
				<?php else : ?>
					<div></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<!-- Attractive Empty State Card (No Orders Ever) -->
		<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-8 sm:p-12 text-center max-w-lg mx-auto">
			<div class="w-16 h-16 rounded-2xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center mx-auto mb-4 shadow-2xs">
				<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
			</div>

			<h3 class="text-xl font-black text-slate-900 tracking-tight m-0">
				لا توجد طلبات سابقة حتى الآن
			</h3>

			<p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed m-0">
				لم تقم بتسجيل أي طلب بعد. استكشف تشكيلتنا الاحترافية من ماكينات قص السيراميك، موازين الليزر، وأنظمة التسوية واطلب الآن مع توصيل سريع لباب موقعك.
			</p>

			<div class="mt-6">
				<a
					href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
					class="inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary-hover transition-all shadow-md shadow-rose-500/20 active:scale-95 cursor-pointer"
				>
					<span>ابدأ التسوق الآن</span>
					<svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
					</svg>
				</a>
			</div>
		</div>
	<?php endif; ?>

</div>

<!-- Filter Tab JavaScript Interaction -->
<script>
document.addEventListener('DOMContentLoaded', function() {
	const tabs = document.querySelectorAll('.filter-tab-btn');
	const cards = document.querySelectorAll('.order-card-item');
	const emptyState = document.getElementById('ashrafco-filter-empty-state');

	if (!tabs.length || !cards.length) return;

	tabs.forEach(tab => {
		tab.addEventListener('click', function() {
			const filter = this.getAttribute('data-status-filter');

			// Reset all tabs to inactive styling
			tabs.forEach(t => {
				t.className = 'filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-medium transition-all duration-150 select-none flex-shrink-0 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer';
				const badge = t.querySelector('.count-badge');
				if (badge) badge.className = 'count-badge text-xs text-slate-400';
			});

			// Set active tab styling (Ashrafco Red)
			this.className = 'filter-tab-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all duration-150 select-none flex-shrink-0 border border-rose-400 bg-rose-50/50 text-primary shadow-2xs cursor-pointer';
			const activeBadge = this.querySelector('.count-badge');
			if (activeBadge) activeBadge.className = 'count-badge text-xs opacity-90';

			// Filter cards
			let visibleCount = 0;
			cards.forEach(card => {
				const cat = card.getAttribute('data-order-category');
				if (filter === 'all' || cat === filter) {
					card.style.display = 'block';
					visibleCount++;
				} else {
					card.style.display = 'none';
				}
			});

			// Toggle empty state if no matching orders
			if (emptyState) {
				emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
			}
		});
	});
});
</script>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
