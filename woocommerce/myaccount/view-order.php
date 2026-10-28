<?php
/**
 * Modern Single Order Details View Template Override for Ashrafco Child Theme
 *
 * Matching Reference Screenshot (Desktop & Mobile Responsive)
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $order ) || ! is_a( $order, 'WC_Order' ) ) {
	$order = wc_get_order( $order_id );
}

if ( ! $order ) {
	return;
}

$order_id    = $order->get_id();
$order_num   = $order->get_order_number();
$status      = $order->get_status();
$status_name = wc_get_order_status_name( $status );
$date_created = $order->get_date_created();
$date_placed  = $date_created ? wp_date( 'j F Y', $date_created->getTimestamp() ) : '';
$pay_title    = $order->get_payment_method_title() ?: 'الدفع عند الاستلام (COD)';
$item_count   = $order->get_item_count();
$store_name   = function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو';

// Security check: ensure current user can view this order
$current_user_id = get_current_user_id();
$is_owner        = ( $current_user_id > 0 && $order->get_customer_id() === $current_user_id );
$can_view        = $is_owner || current_user_can( 'view_order', $order_id ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );

if ( ! $can_view ) {
	wc_print_notice( __( 'عفواً، لا تملك صلاحية عرض هذا الطلب.', 'ashrafco' ), 'error' );
	return;
}

// Order status flags
$is_cancelled  = in_array( $status, array( 'cancelled', 'refunded', 'failed' ), true );
$is_completed  = ( 'completed' === $status );
$is_shipped    = in_array( $status, array( 'shipped', 'out-for-delivery' ), true );
$is_processing = in_array( $status, array( 'processing', 'pending', 'on-hold' ), true );

$badge_info = function_exists( 'ashrafco_get_order_status_badge' ) ? ashrafco_get_order_status_badge( $status ) : array();
$status_name = ! empty( $badge_info['label'] ) ? $badge_info['label'] : wc_get_order_status_name( $status );

// Status badge & icon styling
$status_badge_bg  = ! empty( $badge_info['class'] ) ? $badge_info['class'] : 'bg-slate-50 text-slate-700 border-slate-200';
$status_dot_class = ! empty( $badge_info['dot'] ) ? $badge_info['dot'] : 'bg-slate-400';
$status_icon_bg   = 'bg-slate-50 text-slate-500 border-slate-100';

if ( $is_cancelled ) {
	$status_icon_bg = 'bg-rose-50 text-rose-600 border-rose-100';
} elseif ( $is_completed ) {
	$status_icon_bg = 'bg-emerald-50 text-emerald-600 border-emerald-100';
} elseif ( $is_shipped ) {
	$status_icon_bg = 'bg-blue-50 text-blue-600 border-blue-100';
} elseif ( $is_processing ) {
	$status_icon_bg = 'bg-amber-50 text-amber-600 border-amber-100';
}
?>

<div class="ashrafco-view-order space-y-5 font-sans" dir="rtl">

	<!-- =========================================================================
	     1. HEADER: BACK TO ORDERS LINK & PAGE TITLE WITH RED SHOPPING BAG ICON
	     ========================================================================= -->
	<div class="space-y-1.5 pb-2">
		<!-- Back to Orders Link -->
		<div>
			<a
				href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"
				class="text-xs font-bold text-slate-500 hover:text-primary transition-colors inline-flex items-center gap-1 group select-none"
			>
				<svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
				</svg>
				<span>العودة لكل الطلبات</span>
			</a>
		</div>

		<!-- Page Title & Icon -->
		<div class="flex items-center gap-2.5">
			<span class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
				</svg>
			</span>
			<h2 class="text-xl sm:text-2xl font-black text-slate-900 m-0 tracking-tight">
				تفاصيل الطلب رقم #<?php echo esc_html( $order_num ); ?>
			</h2>
		</div>
	</div>

	<!-- =========================================================================
	     2. FOUR SUMMARY CARDS ROW (Matching Screenshot Exact Proportions)
	     ========================================================================= -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">

		<!-- Card 1 (Right): Order Number -->
		<div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
			<div>
				<span class="text-xs text-slate-400 font-bold block mb-0.5">رقم الطلب</span>
				<span class="text-lg font-black text-slate-900 font-mono block">#<?php echo esc_html( $order_num ); ?></span>
			</div>
			<div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 text-slate-400 flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
				</svg>
			</div>
		</div>

		<!-- Card 2: Order Date -->
		<div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
			<div>
				<span class="text-xs text-slate-400 font-bold block mb-0.5">تاريخ الطلب</span>
				<span class="text-xs sm:text-sm font-bold text-slate-800 block"><?php echo esc_html( $date_placed ); ?></span>
			</div>
			<div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-primary flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
				</svg>
			</div>
		</div>

		<!-- Card 3: Payment Method -->
		<div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
			<div class="min-w-0 pr-1">
				<span class="text-xs text-slate-400 font-bold block mb-0.5">طريقة السداد</span>
				<span class="text-xs sm:text-sm font-bold text-slate-800 block truncate" title="<?php echo esc_attr( $pay_title ); ?>">
					<?php echo esc_html( $pay_title ); ?>
				</span>
			</div>
			<div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
				</svg>
			</div>
		</div>

		<!-- Card 4 (Left): Order Status -->
		<div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between shadow-2xs">
			<div>
				<span class="text-xs text-slate-400 font-bold block mb-1">حالة الطلب</span>
				<span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold border <?php echo esc_attr( $status_badge_bg ); ?>">
					<span class="w-1.5 h-1.5 rounded-full <?php echo esc_attr( $status_dot_class ); ?>"></span>
					<span><?php echo esc_html( $status_name ); ?></span>
				</span>
			</div>
			<div class="w-10 h-10 rounded-xl border flex items-center justify-center flex-shrink-0 shadow-2xs <?php echo esc_attr( $status_icon_bg ); ?>">
				<?php if ( $is_cancelled ) : ?>
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
					</svg>
				<?php elseif ( $is_completed ) : ?>
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
					</svg>
				<?php elseif ( $is_shipped ) : ?>
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zM18 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M13 8h4.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16h-3" />
					</svg>
				<?php else : ?>
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				<?php endif; ?>
			</div>
		</div>

	</div>

	<!-- =========================================================================
	     3. ORDER STATUS & SUPPORT MESSAGE (Matching Screenshot)
	     ========================================================================= -->
	<?php if ( $is_cancelled ) : ?>
		<div class="bg-rose-50/70 border border-rose-100 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
			<div class="flex items-center gap-3.5 min-w-0">
				<div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-primary text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</div>
				<div class="min-w-0">
					<h4 class="text-sm sm:text-base font-extrabold text-primary m-0">
						تم إلغاء هذا الطلب (<?php echo esc_html( $status_name ); ?>) رقم #<?php echo esc_html( $order_num ); ?>.
					</h4>
					<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
						إذا كان لديك أي استفسار بخصوص هذا الطلب، يمكنك التواصل مع فريق دعم <?php echo esc_html( $store_name ); ?>.
					</p>
				</div>
			</div>

			<div class="flex-shrink-0 w-full md:w-auto flex justify-end">
				<a
					href="<?php echo esc_url( home_url( '/contact' ) ); ?>"
					class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-primary bg-white border border-rose-200 hover:bg-rose-50 transition-colors shadow-2xs select-none"
				>
					<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
					</svg>
					<span>التواصل مع الدعم</span>
				</a>
			</div>
		</div>
	<?php elseif ( $is_completed ) : ?>
		<div class="bg-emerald-50/70 border border-emerald-100 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
			<div class="flex items-center gap-3.5 min-w-0">
				<div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
					</svg>
				</div>
				<div class="min-w-0">
					<h4 class="text-sm sm:text-base font-extrabold text-emerald-800 m-0">
						تم تسليم هذا الطلب بنجاح رقم #<?php echo esc_html( $order_num ); ?>.
					</h4>
					<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
						شكراً لتسوقك من <?php echo esc_html( $store_name ); ?>. نتمنى أن تنال المنتجات رضاك وتلبي احتياجات عملك.
					</p>
				</div>
			</div>

			<div class="flex-shrink-0 w-full md:w-auto flex justify-end">
				<a
					href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $order_id, wc_get_cart_url() ), 'woocommerce-order_again' ) ); ?>"
					class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-emerald-700 bg-white border border-emerald-200 hover:bg-emerald-50 transition-colors shadow-2xs select-none"
				>
					<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
					</svg>
					<span>إعادة الطلب</span>
				</a>
			</div>
		</div>
	<?php else : ?>
		<div class="bg-amber-50/70 border border-amber-100 rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-2xs">
			<div class="flex items-center gap-3.5 min-w-0">
				<div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
				<div class="min-w-0">
					<h4 class="text-sm sm:text-base font-extrabold text-amber-900 m-0">
						طلبك رقم #<?php echo esc_html( $order_num ); ?> (<?php echo esc_html( $status_name ); ?>) جاري متابعته.
					</h4>
					<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
						يقوم فريق <?php echo esc_html( $store_name ); ?> بالعمل على طلبك لضمان سرعة التجهيز والتسليم بأعلى معايير الجودة.
					</p>
				</div>
			</div>

			<div class="flex-shrink-0 w-full md:w-auto flex justify-end">
				<a
					href="<?php echo esc_url( home_url( '/contact' ) ); ?>"
					class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-amber-800 bg-white border border-amber-200 hover:bg-amber-50 transition-colors shadow-2xs select-none"
				>
					<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
					</svg>
					<span>التواصل مع الدعم</span>
				</a>
			</div>
		</div>
	<?php endif; ?>

	<!-- =========================================================================
	     4. IMPORTANT REMINDER NOTICE (تذكير هام لضمان حقوقك)
	     ========================================================================= -->
	<div class="bg-[#fff8f0] border border-amber-100 rounded-2xl p-4 sm:p-5 flex items-start sm:items-center gap-3.5 shadow-2xs">
		<div class="w-9 h-9 rounded-xl bg-[#ea580c] text-white flex items-center justify-center flex-shrink-0 shadow-2xs">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
			</svg>
		</div>
		<div>
			<h4 class="text-xs sm:text-sm font-extrabold text-slate-900 m-0">
				تذكير هام لضمان حقوقك:
			</h4>
			<p class="text-xs text-slate-600 m-0 mt-0.5 leading-relaxed font-medium">
				يحق لك فحص وتجربة الماكينات ومعدات البورسلين مع مندوب الشحن والتأكد من مطابقتها للمواصفات الفنية قبل سداد المبلغ.
			</p>
		</div>
	</div>

	<!-- =========================================================================
	     5. PRODUCTS AND FINANCIAL BREAKDOWN CARD (المنتجات والمعدات بالطلب)
	     ========================================================================= -->
	<div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-5 sm:p-7 shadow-sm space-y-5">

		<!-- Card Header: Title on Right, Item Count on Left -->
		<div class="flex items-center justify-between pb-4 border-b border-slate-100">
			<div class="flex items-center gap-2">
				<span class="w-6 h-6 rounded-lg bg-rose-50 text-primary flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
				</span>
				<h3 class="text-base sm:text-lg font-black text-slate-900 m-0">
					المنتجات والمعدات بالطلب
				</h3>
			</div>

			<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-50 border border-slate-200 text-slate-600 shadow-2xs select-none">
				<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
				</svg>
				<span><?php echo esc_html( $item_count ); ?> قطعة</span>
			</span>
		</div>

		<!-- Products Desktop Table Header (Hidden on mobile) -->
		<div class="hidden sm:grid grid-cols-12 items-center bg-slate-50/80 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-400">
			<div class="col-span-6 text-right">المنتج</div>
			<div class="col-span-2 text-center">السعر الفردي</div>
			<div class="col-span-2 text-center">الكمية</div>
			<div class="col-span-2 text-left">الإجمالي</div>
		</div>

		<!-- Products List -->
		<div class="divide-y divide-slate-100">
			<?php
			$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
			foreach ( $order_items as $item_id => $item ) :
				$product    = $item->get_product();
				$permalink  = $product ? $product->get_permalink() : '';
				$sku        = $product ? $product->get_sku() : '';
				$qty        = $item->get_quantity();
				$line_total = $order->get_formatted_line_subtotal( $item );

				// Unit price calculation
				$unit_price_val       = $qty > 0 ? ( $order->get_line_subtotal( $item, false, false ) / $qty ) : 0;
				$unit_price_formatted = wc_price( $unit_price_val, array( 'currency' => $order->get_currency() ) );

				// Product Thumbnail
				$img_url = '';
				if ( $product && $product->get_image_id() ) {
					$img_url = wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' );
				}
				if ( ! $img_url ) {
					$img_url = get_theme_file_uri( 'assets/images/categories/cat-manual-tools.png' );
				}
				?>
				<div class="py-4 grid grid-cols-1 sm:grid-cols-12 items-center gap-3 sm:gap-0">

					<!-- Column 1: Product Thumbnail, Title & SKU (Spans 6 on Desktop) -->
					<div class="col-span-1 sm:col-span-6 flex items-center gap-3.5 min-w-0">
						<div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-white border border-slate-200/80 p-1 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-2xs">
							<img
								src="<?php echo esc_url( $img_url ); ?>"
								alt="<?php echo esc_attr( $item->get_name() ); ?>"
								class="w-full h-full object-contain"
								loading="lazy"
							/>
						</div>
						<div class="min-w-0 space-y-1">
							<h4 class="text-xs sm:text-sm font-extrabold text-slate-900 m-0 line-clamp-2">
								<?php if ( $permalink ) : ?>
									<a href="<?php echo esc_url( $permalink ); ?>" class="hover:text-primary transition-colors">
										<?php echo esc_html( $item->get_name() ); ?>
									</a>
								<?php else : ?>
									<?php echo esc_html( $item->get_name() ); ?>
								<?php endif; ?>
							</h4>
							<?php if ( $sku ) : ?>
								<span class="text-[11px] text-slate-400 font-mono block">
									SKU: <?php echo esc_html( $sku ); ?>
								</span>
							<?php endif; ?>

							<!-- Mobile View Only: Quantity and Unit Price -->
							<div class="flex items-center gap-3 sm:hidden text-xs text-slate-500 pt-1 font-medium">
								<span>الكمية: <strong class="text-slate-800 font-bold"><?php echo esc_html( $qty ); ?></strong></span>
								<span>•</span>
								<span>سعر الوحدة: <strong class="text-slate-800 font-bold"><?php echo wp_kses_post( $unit_price_formatted ); ?></strong></span>
							</div>
						</div>
					</div>

					<!-- Column 2: Individual Unit Price (Desktop only) -->
					<div class="hidden sm:block sm:col-span-2 text-center text-xs sm:text-sm font-black text-slate-800 font-mono">
						<?php echo wp_kses_post( $unit_price_formatted ); ?>
					</div>

					<!-- Column 3: Quantity (Desktop only) -->
					<div class="hidden sm:block sm:col-span-2 text-center text-xs sm:text-sm font-bold text-slate-700 font-mono">
						<?php echo esc_html( $qty ); ?>
					</div>

					<!-- Column 4: Line Total -->
					<div class="col-span-1 sm:col-span-2 text-left flex items-center justify-between sm:justify-end">
						<span class="sm:hidden text-xs text-slate-400 font-bold">الإجمالي:</span>
						<span class="text-sm sm:text-base font-black text-slate-900 font-mono">
							<?php echo wp_kses_post( $line_total ); ?>
						</span>
					</div>

				</div>
			<?php endforeach; ?>
		</div>

		<!-- Financial Breakdown & Total -->
		<div class="pt-4 border-t border-slate-100">
			<div class="bg-slate-50/70 border border-slate-100 rounded-2xl p-4 sm:p-5 space-y-3">

				<!-- Subtotal -->
				<div class="flex items-center justify-between text-xs sm:text-sm">
					<span class="font-bold text-slate-500">المجموع الفرعي:</span>
					<span class="font-black text-slate-800 font-mono">
						<?php echo wp_kses_post( $order->get_subtotal_to_display() ); ?>
					</span>
				</div>

				<!-- Shipping Costs -->
				<div class="flex items-center justify-between text-xs sm:text-sm">
					<span class="font-bold text-slate-500">مصاريف الشحن والتوصيل:</span>
					<span class="font-bold text-[#059669] font-mono">
						<?php
						$shipping_cost = (float) $order->get_shipping_total();
						if ( 0.0 === $shipping_cost ) {
							echo '<span class="ashrafco-free-shipping text-[#059669] font-bold text-emerald-600">مجاني</span>';
						} else {
							echo wp_kses_post( wc_price( $shipping_cost, array( 'currency' => $order->get_currency() ) ) );
							$shipping_gov = $order->get_shipping_state() ?: $order->get_billing_state();
							if ( ! $shipping_gov ) {
								$shipping_gov = $order->get_shipping_city() ?: $order->get_billing_city();
							}
							if ( $shipping_gov ) {
								echo ' <span class="text-slate-400 text-xs font-normal font-sans">(' . esc_html( $shipping_gov ) . ')</span>';
							}
						}
						?>
					</span>
				</div>

				<!-- Discounts if available -->
				<?php if ( $order->get_total_discount() > 0 ) : ?>
					<div class="flex items-center justify-between text-xs sm:text-sm text-emerald-600">
						<span class="font-bold">قيمة الخصم:</span>
						<span class="font-black font-mono">-<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); ?></span>
					</div>
				<?php endif; ?>

				<!-- Taxes if available -->
				<?php if ( $order->get_total_tax() > 0 ) : ?>
					<div class="flex items-center justify-between text-xs sm:text-sm text-slate-500">
						<span class="font-bold">الضرائب المشمولة:</span>
						<span class="font-black text-slate-800 font-mono"><?php echo wc_price( $order->get_total_tax(), array( 'currency' => $order->get_currency() ) ); ?></span>
					</div>
				<?php endif; ?>

				<!-- Final Order Total (Green Banner Matching Screenshot) -->
				<div class="bg-emerald-50/70 border border-emerald-100/90 rounded-xl p-3 sm:p-3.5 flex items-center justify-between">
					<span class="text-sm sm:text-base font-black text-[#059669]">
						إجمالي الفاتورة النهائي:
					</span>
					<span class="text-base sm:text-xl font-black text-[#059669] font-mono [&_.woocommerce-Price-amount]:text-[#059669] [&_.woocommerce-Price-currencySymbol]:text-[#059669]">
						<?php echo wp_kses_post( $order->get_formatted_order_total() ); ?>
					</span>
				</div>

			</div>
		</div>

	</div>

	<!-- =========================================================================
	     6. BILLING AND CONTACT INFORMATION
	     ========================================================================= -->
	<div class="w-full">
		<!-- Billing & Contact Details -->
		<div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-5 sm:p-6 shadow-sm space-y-4">
			<div class="flex items-center gap-2 pb-3 border-b border-slate-100">
				<span class="w-6 h-6 rounded-lg bg-rose-50 text-primary flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
					</svg>
				</span>
				<h4 class="text-sm font-extrabold text-slate-900 m-0">
					بيانات الفاتورة والاتصال
				</h4>
			</div>

			<?php
			$billing_name  = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
			$billing_phone = $order->get_billing_phone();
			$billing_email = $order->get_billing_email();
			$billing_addr  = trim( $order->get_billing_address_1() );
			?>

			<div class="space-y-3 text-xs sm:text-sm">
				<!-- Full Name -->
				<div class="flex items-center justify-between gap-4">
					<span class="text-slate-400 font-bold flex-shrink-0">الاسم الكامل:</span>
					<span class="text-slate-800 font-bold text-left truncate">
						<?php echo esc_html( $billing_name ?: ( wp_get_current_user()->display_name ?: '—' ) ); ?>
					</span>
				</div>

				<!-- Phone -->
				<?php if ( $billing_phone ) : ?>
					<div class="flex items-center justify-between gap-4">
						<span class="text-slate-400 font-bold flex-shrink-0">رقم الهاتف:</span>
						<a href="tel:<?php echo esc_attr( $billing_phone ); ?>" class="text-slate-800 font-bold font-mono hover:text-primary transition-colors" dir="ltr">
							<?php echo esc_html( $billing_phone ); ?>
						</a>
					</div>
				<?php endif; ?>

				<!-- Email -->
				<?php if ( $billing_email ) : ?>
					<div class="flex items-center justify-between gap-4">
						<span class="text-slate-400 font-bold flex-shrink-0">البريد الإلكتروني:</span>
						<span class="text-slate-800 font-bold font-mono text-xs text-left truncate" title="<?php echo esc_attr( $billing_email ); ?>" dir="ltr">
							<?php echo esc_html( $billing_email ); ?>
						</span>
					</div>
				<?php endif; ?>

				<!-- Address -->
				<div class="flex items-start justify-between gap-4">
					<span class="text-slate-400 font-bold flex-shrink-0">العنوان:</span>
					<span class="text-slate-800 font-medium text-left">
						<?php
						$full_billing = $billing_addr;
						if ( $order->get_billing_address_2() ) {
							$full_billing .= ( $full_billing ? ' - ' : '' ) . $order->get_billing_address_2();
						}
						if ( $order->get_billing_city() ) {
							$full_billing .= ( $full_billing ? '، ' : '' ) . $order->get_billing_city();
						}
						echo esc_html( $full_billing ?: '—' );
						?>
					</span>
				</div>
			</div>
		</div>

	</div>

</div>
