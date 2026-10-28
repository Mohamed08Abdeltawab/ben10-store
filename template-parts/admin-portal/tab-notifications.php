<?php
/**
 * Ashrafco Admin Portal - Tab 8: Notifications Management Module
 *
 * Full administration dashboard for viewing, filtering, and managing store alerts:
 * - New Orders, Cancelled Orders, Payment Failures
 * - Low Stock Alerts & Out of Stock Alerts
 * - New Registered Customers
 *
 * @package Ashrafco
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
	return;
}

$current_page = max( 1, isset( $_GET['paged_notif'] ) ? absint( $_GET['paged_notif'] ) : 1 );
$filter_type  = isset( $_GET['notif_type'] ) ? sanitize_key( $_GET['notif_type'] ) : '';
$base_url     = add_query_arg( 'tab', 'notifications', home_url( '/admin-portal/' ) );

$query_args = array(
	'recipient_type' => 'admin',
	'per_page'       => 15,
	'paged'          => $current_page,
);

if ( ! empty( $filter_type ) && 'all' !== $filter_type ) {
	$query_args['type'] = $filter_type;
}

$data         = function_exists( 'ashrafco_get_notifications' ) ? ashrafco_get_notifications( $query_args ) : array(
	'items'        => array(),
	'total_items'  => 0,
	'total_pages'  => 0,
	'current_page' => 1,
	'unread_count' => 0,
);

$items        = $data['items'];
$total_items  = $data['total_items'];
$total_pages  = $data['total_pages'];
$unread_count = $data['unread_count'];
?>

<div class="space-y-6 sm:space-y-7" dir="rtl">

	<!-- 1. Header Section -->
	<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
		<div class="flex items-center gap-3">
			<div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#d90429] border border-rose-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
				</svg>
			</div>
			<div>
				<div class="flex items-center gap-2.5">
					<h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-tight m-0">
						<?php esc_html_e( 'مركز إشعارات المتجر', 'ashrafco' ); ?>
					</h1>
					<span class="ashrafco-page-unread-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#d90429] text-white shadow-2xs <?php echo ( $unread_count > 0 ) ? '' : 'hidden'; ?>">
						<span class="ashrafco-page-unread-count"><?php echo esc_html( $unread_count ); ?></span>
						<span class="ms-1"><?php esc_html_e( 'تنبيه جديد', 'ashrafco' ); ?></span>
					</span>
				</div>
				<p class="text-xs sm:text-sm text-slate-400 font-medium mt-0.5 m-0">
					<?php esc_html_e( 'متابعة العمليات اللحظية، الطلبات الجديدة، تغيرات المخزون، وتنبيهات الدفع.', 'ashrafco' ); ?>
				</p>
			</div>
		</div>

		<?php if ( $unread_count > 0 ) : ?>
			<button
				type="button"
				class="ashrafco-mark-all-read-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition-all cursor-pointer shadow-2xs"
				data-scope="admin"
			>
				<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<polyline points="20 6 9 17 4 12"></polyline>
				</svg>
				<span><?php esc_html_e( 'تحديد الكل كمقروء', 'ashrafco' ); ?></span>
			</button>
		<?php endif; ?>
	</div>

	<!-- 2. Filter Bar -->
	<div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
		<?php
		$filters = array(
			''                    => __( 'جميع التنبيهات', 'ashrafco' ),
			'order_new'           => __( 'طلبات جديدة', 'ashrafco' ),
			'order_cancelled'     => __( 'طلبات مُلغاة', 'ashrafco' ),
			'payment_failed'      => __( 'مشاكل في الدفع', 'ashrafco' ),
			'low_stock'           => __( 'انخفاض المخزون', 'ashrafco' ),
			'customer_registered' => __( 'عملاء جدد', 'ashrafco' ),
		);
		foreach ( $filters as $f_key => $f_title ) :
			$is_active = ( $filter_type === $f_key ) || ( '' === $filter_type && '' === $f_key );
			$f_url     = ! empty( $f_key ) ? add_query_arg( 'notif_type', $f_key, $base_url ) : remove_query_arg( 'notif_type', $base_url );
			?>
			<a
				href="<?php echo esc_url( $f_url ); ?>"
				class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap shadow-2xs no-underline <?php echo $is_active ? 'bg-slate-900 text-white font-black' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200'; ?>"
			>
				<?php echo esc_html( $f_title ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<!-- 3. Notifications Table / Cards -->
	<?php if ( empty( $items ) ) : ?>
		<div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-2xs flex flex-col items-center justify-center gap-3">
			<div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-300 flex items-center justify-center">
				<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
				</svg>
			</div>
			<h3 class="text-base font-bold text-slate-800 m-0">
				<?php esc_html_e( 'لا توجد إشعارات مطابقة حالياً', 'ashrafco' ); ?>
			</h3>
			<p class="text-xs text-slate-400 max-w-sm m-0">
				<?php esc_html_e( 'جميع إشعارات الطلبات وتنبيهات المخزون تظهر هنا لحظياً عند حدوث أي نشاط.', 'ashrafco' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
			<div class="divide-y divide-slate-100">
				<?php foreach ( $items as $item ) :
					$row_bg     = ! $item['is_read'] ? 'bg-rose-50/25' : 'bg-white';
					$type_badge = $item['type_badge'];
					?>
					<div class="ashrafco-notification-row p-4 sm:p-5 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 <?php echo esc_attr( $row_bg ); ?>" data-id="<?php echo esc_attr( $item['id'] ); ?>">
						<!-- Right: Icon and Text -->
						<div class="flex items-start gap-3.5 min-w-0 flex-1">
							<div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 border <?php echo esc_attr( $type_badge['bg'] ); ?>">
								<?php if ( 'order_new' === $item['type'] ) : ?>
									<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
								<?php elseif ( 'order_cancelled' === $item['type'] ) : ?>
									<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
								<?php elseif ( 'payment_failed' === $item['type'] ) : ?>
									<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
								<?php elseif ( 'low_stock' === $item['type'] ) : ?>
									<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
								<?php elseif ( 'customer_registered' === $item['type'] ) : ?>
									<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
								<?php else : ?>
									<svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
								<?php endif; ?>
							</div>

							<div class="min-w-0 flex-1">
								<div class="flex items-center gap-2 flex-wrap">
									<h4 class="text-sm font-black text-slate-900 m-0">
										<?php echo esc_html( $item['title'] ); ?>
									</h4>
									<span class="px-2 py-0.5 rounded-full text-[10px] font-bold border <?php echo esc_attr( $type_badge['bg'] ); ?>">
										<?php echo esc_html( $item['type_label'] ); ?>
									</span>
									<?php if ( ! $item['is_read'] ) : ?>
										<span class="ashrafco-row-unread-badge w-2 h-2 rounded-full bg-[#d90429] inline-block"></span>
									<?php endif; ?>
								</div>
								<p class="text-xs text-slate-600 mt-1 leading-relaxed m-0">
									<?php echo esc_html( $item['raw_message'] ); ?>
								</p>
								<div class="flex items-center gap-3 mt-2 text-[11px] text-slate-400">
									<span><?php echo esc_html( $item['time_ago'] ); ?></span>
									<span>•</span>
									<span><?php echo esc_html( $item['created_at'] ); ?></span>
								</div>
							</div>
						</div>

						<!-- Left: Action Buttons -->
						<div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0">
							<?php if ( ! empty( $item['target_url'] ) ) : ?>
								<a
									href="<?php echo esc_url( $item['target_url'] ); ?>"
									class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-800 bg-slate-100 hover:bg-slate-200 transition-colors no-underline shadow-2xs"
								>
									<span><?php esc_html_e( 'فتح السجل', 'ashrafco' ); ?></span>
									<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<polyline points="15 18 9 12 15 6"></polyline>
									</svg>
								</a>
							<?php endif; ?>

							<?php if ( ! $item['is_read'] ) : ?>
								<button
									type="button"
									class="ashrafco-page-mark-read-btn inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 transition-all cursor-pointer shadow-2xs"
									data-id="<?php echo esc_attr( $item['id'] ); ?>"
									data-scope="admin"
									title="<?php esc_attr_e( 'تحديد كمقروء', 'ashrafco' ); ?>"
								>
									<svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
										<polyline points="20 6 9 17 4 12"></polyline>
									</svg>
									<span class="hidden md:inline"><?php esc_html_e( 'مقروء', 'ashrafco' ); ?></span>
								</button>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- 4. Pagination -->
		<?php if ( $total_pages > 1 ) : ?>
			<div class="pt-4 flex items-center justify-center gap-1.5">
				<?php for ( $p = 1; $p <= $total_pages; $p++ ) :
					$p_url = add_query_arg( 'paged_notif', $p, $base_url );
					if ( ! empty( $filter_type ) ) {
						$p_url = add_query_arg( 'notif_type', $filter_type, $p_url );
					}
					$is_curr = ( $p === $current_page );
					?>
					<a
						href="<?php echo esc_url( $p_url ); ?>"
						class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold no-underline transition-all <?php echo $is_curr ? 'bg-slate-900 text-white shadow-xs font-black' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'; ?>"
					>
						<?php echo esc_html( $p ); ?>
					</a>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div>
