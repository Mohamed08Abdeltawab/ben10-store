<?php
/**
 * Customer & Admin My Account Notifications Template
 *
 * Dedicated responsive view for notifications in the user account.
 * Automatically adapts between Admin Store Notifications and Personal Customer Orders based on role.
 *
 * @package Ashrafco
 * @since   1.0.0
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	wc_print_notice( __( 'يرجى تسجيل الدخول لعرض الإشعارات الخاصة بك.', 'ashrafco' ), 'notice' );
	return;
}

$current_user_id = get_current_user_id();
$is_admin        = current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
$active_scope    = isset( $_GET['notif_scope'] ) ? sanitize_key( $_GET['notif_scope'] ) : ( $is_admin ? 'admin' : 'customer' );

if ( ! $is_admin ) {
	$active_scope = 'customer';
}

$current_page = max( 1, get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : ( isset( $_GET['notif_page'] ) ? absint( $_GET['notif_page'] ) : 1 ) );
$filter_type  = isset( $_GET['notif_type'] ) ? sanitize_key( $_GET['notif_type'] ) : '';

$query_args = array(
	'recipient_type' => $active_scope,
	'per_page'       => 12,
	'paged'          => $current_page,
);

if ( 'customer' === $active_scope ) {
	$query_args['recipient_user_id'] = $current_user_id;
}

if ( ! empty( $filter_type ) && 'all' !== $filter_type ) {
	$query_args['type'] = $filter_type;
}

$notif_data   = function_exists( 'ashrafco_get_notifications' ) ? ashrafco_get_notifications( $query_args ) : array(
	'items'        => array(),
	'total_items'  => 0,
	'total_pages'  => 0,
	'current_page' => 1,
	'unread_count' => 0,
);

$items        = $notif_data['items'];
$total_items  = $notif_data['total_items'];
$total_pages  = $notif_data['total_pages'];
$unread_count = $notif_data['unread_count'];

$endpoint_base_url = wc_get_account_endpoint_url( 'my-notifications' );
$base_url          = $is_admin ? add_query_arg( 'notif_scope', $active_scope, $endpoint_base_url ) : $endpoint_base_url;
?>

<div class="ashrafco-customer-notifications space-y-6 font-sans select-none" dir="rtl">

	<!-- 1. Header Banner: Title, Description, and Actions -->
	<div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-100 shadow-2xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
		<div class="flex items-center gap-3.5">
			<div class="w-12 h-12 rounded-2xl bg-rose-50 text-primary border border-rose-100 flex items-center justify-center flex-shrink-0 shadow-2xs">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
					<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
					<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
				</svg>
			</div>
			<div>
				<div class="flex items-center gap-2.5">
					<h2 class="text-lg sm:text-xl font-black text-slate-900 m-0">
						<?php esc_html_e( 'مركز الإشعارات والتنبيهات', 'ashrafco' ); ?>
					</h2>
					<span class="ashrafco-page-unread-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary text-white shadow-2xs <?php echo ( $unread_count > 0 ) ? '' : 'hidden'; ?>">
						<span class="ashrafco-page-unread-count"><?php echo esc_html( $unread_count ); ?></span>
						<span class="ms-1"><?php esc_html_e( 'غير مقروء', 'ashrafco' ); ?></span>
					</span>
				</div>
				<p class="text-xs sm:text-sm text-slate-400 font-medium mt-1 m-0">
					<?php
					if ( 'admin' === $active_scope ) {
						esc_html_e( 'متابعة تنبيهات المتجر الفورية، الطلبات الجديدة، وحالات الدفع والمخزون.', 'ashrafco' );
					} else {
						esc_html_e( 'تابع حالة طلباتك، مواعيد الشحن، والتحديثات الخاصة بحسابك فور صدورها.', 'ashrafco' );
					}
					?>
				</p>
			</div>
		</div>

		<div class="flex items-center gap-2 self-stretch md:self-auto justify-between md:justify-end flex-wrap">
			<?php if ( $unread_count > 0 ) : ?>
				<button
					type="button"
					class="ashrafco-mark-all-read-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all cursor-pointer shadow-2xs"
					data-scope="<?php echo esc_attr( $active_scope ); ?>"
				>
					<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<polyline points="20 6 9 17 4 12"></polyline>
					</svg>
					<span><?php esc_html_e( 'تحديد الكل كمقروء', 'ashrafco' ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $is_admin ) : ?>
		<!-- Admin Scope Segmented Switch: Store vs Personal Orders -->
		<div class="flex items-center justify-between gap-3 p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/60 flex-wrap">
			<div class="flex items-center gap-1.5 flex-1 sm:flex-initial">
				<a
					href="<?php echo esc_url( add_query_arg( 'notif_scope', 'admin', $endpoint_base_url ) ); ?>"
					class="flex-1 sm:flex-initial text-center px-4 py-2 rounded-xl text-xs font-bold transition-all no-underline <?php echo 'admin' === $active_scope ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900'; ?>"
				>
					<span><?php esc_html_e( 'إشعارات المتجر (الإدارة)', 'ashrafco' ); ?></span>
				</a>
				<a
					href="<?php echo esc_url( add_query_arg( 'notif_scope', 'customer', $endpoint_base_url ) ); ?>"
					class="flex-1 sm:flex-initial text-center px-4 py-2 rounded-xl text-xs font-bold transition-all no-underline <?php echo 'customer' === $active_scope ? 'bg-white text-slate-900 shadow-xs font-black' : 'text-slate-600 hover:text-slate-900'; ?>"
				>
					<span><?php esc_html_e( 'طلباتي الشخصية (كعميل)', 'ashrafco' ); ?></span>
				</a>
			</div>
		</div>
	<?php endif; ?>

	<!-- 2. Filter Tabs -->
	<div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
		<?php
		if ( 'admin' === $active_scope ) {
			$tabs = array(
				''                    => __( 'الكل', 'ashrafco' ),
				'order_new'           => __( 'الطلبات الجديدة', 'ashrafco' ),
				'order_cancelled'     => __( 'الطلبات الملغاة', 'ashrafco' ),
				'low_stock'           => __( 'تنبيهات المخزون', 'ashrafco' ),
				'payment_failed'      => __( 'فشل الدفع', 'ashrafco' ),
				'customer_registered' => __( 'عملاء جدد', 'ashrafco' ),
			);
		} else {
			$tabs = array(
				''                         => __( 'الكل', 'ashrafco' ),
				'order_processing'         => __( 'قيد المعالجة', 'ashrafco' ),
				'order_shipped'            => __( 'تم الشحن', 'ashrafco' ),
				'order_completed'          => __( 'تم التسليم', 'ashrafco' ),
				'order_cancelled_customer' => __( 'ملغي', 'ashrafco' ),
			);
		}

		foreach ( $tabs as $tab_key => $tab_title ) :
			$is_active = ( $filter_type === $tab_key ) || ( '' === $filter_type && '' === $tab_key );
			$tab_url   = ! empty( $tab_key ) ? add_query_arg( 'notif_type', $tab_key, $base_url ) : remove_query_arg( 'notif_type', $base_url );
			?>
			<a
				href="<?php echo esc_url( $tab_url ); ?>"
				class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap shadow-2xs no-underline <?php echo $is_active ? 'bg-primary text-white font-black' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'; ?>"
			>
				<?php echo esc_html( $tab_title ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<!-- 3. Notifications List -->
	<?php if ( empty( $items ) ) : ?>
		<div class="bg-white rounded-2xl p-12 text-center border border-slate-100 shadow-2xs flex flex-col items-center justify-center gap-3">
			<div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-300 flex items-center justify-center">
				<svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
				</svg>
			</div>
			<h3 class="text-base font-bold text-slate-800 m-0">
				<?php esc_html_e( 'لا توجد إشعارات في هذا القسم حالياً', 'ashrafco' ); ?>
			</h3>
			<p class="text-xs text-slate-400 max-w-sm m-0">
				<?php
				if ( 'admin' === $active_scope ) {
					esc_html_e( 'عند حدوث أي أحداث في المتجر كالطلبات الجديدة أو تنبيهات المخزون، ستظهر الإشعارات هنا.', 'ashrafco' );
				} else {
					esc_html_e( 'عندما يتم تحديث حالة طلباتك أو إصدار عروض خاصة بك، ستظهر جميع التنبيهات هنا.', 'ashrafco' );
				}
				?>
			</p>
		</div>
	<?php else : ?>
		<div class="space-y-3">
			<?php foreach ( $items as $item ) :
				$row_bg     = ! $item['is_read'] ? 'bg-rose-50/30 border-rose-100 font-semibold' : 'bg-white border-slate-100';
				$type_badge = $item['type_badge'];
				?>
				<div class="ashrafco-notification-row p-4 sm:p-5 rounded-2xl border transition-all shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 <?php echo esc_attr( $row_bg ); ?>" data-id="<?php echo esc_attr( $item['id'] ); ?>">
					<!-- Right in RTL: Icon + Details -->
					<div class="flex items-start gap-3.5 min-w-0 flex-1">
						<div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 border <?php echo esc_attr( $type_badge['bg'] ); ?>">
							<?php if ( in_array( $item['type'], array( 'order_completed' ), true ) ) : ?>
								<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
							<?php elseif ( 'order_processing' === $item['type'] ) : ?>
								<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php elseif ( 'order_shipped' === $item['type'] ) : ?>
								<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
							<?php elseif ( in_array( $item['type'], array( 'order_cancelled', 'order_cancelled_customer', 'payment_failed' ), true ) ) : ?>
								<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
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
									<span class="ashrafco-row-unread-badge w-2 h-2 rounded-full bg-primary inline-block"></span>
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

					<!-- Left in RTL: Action Buttons -->
					<div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0">
						<?php if ( ! empty( $item['target_url'] ) ) : ?>
							<a
								href="<?php echo esc_url( $item['target_url'] ); ?>"
								class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-primary bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors no-underline shadow-2xs"
							>
								<span><?php esc_html_e( 'عرض التفاصيل', 'ashrafco' ); ?></span>
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
								data-scope="<?php echo esc_attr( $active_scope ); ?>"
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

		<!-- 4. Pagination -->
		<?php if ( $total_pages > 1 ) : ?>
			<div class="pt-4 flex items-center justify-center gap-1.5">
				<?php for ( $p = 1; $p <= $total_pages; $p++ ) :
					$p_url = add_query_arg( 'notif_page', $p, $base_url );
					if ( ! empty( $filter_type ) ) {
						$p_url = add_query_arg( 'notif_type', $filter_type, $p_url );
					}
					$is_curr = ( $p === $current_page );
					?>
					<a
						href="<?php echo esc_url( $p_url ); ?>"
						class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold no-underline transition-all <?php echo $is_curr ? 'bg-primary text-white shadow-xs font-black' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'; ?>"
					>
						<?php echo esc_html( $p ); ?>
					</a>
				<?php endfor; ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

</div>
