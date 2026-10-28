<?php
/**
 * Fast & Lightweight Quick Product Manager for Ashrafco Admins
 *
 * Provides a streamlined inline dashboard to quickly manage prices,
 * inventory stock, and availability without navigating away.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Quick Product Manager Submenu under WooCommerce Products.
 */
function test_theme_register_quick_manager_menu() {
	$hook = add_submenu_page(
		'edit.php?post_type=product',
		'الإدارة السريعة للمنتجات | Ashrafco',
		'⚡ الإدارة السريعة',
		'manage_woocommerce',
		'test-theme-quick-manager',
		'test_theme_render_quick_manager_page'
	);

	// Load dedicated scripts and styles on this page only
	add_action( 'load-' . $hook, 'test_theme_load_quick_manager_assets' );
}
add_action( 'admin_menu', 'test_theme_register_quick_manager_menu', 20 );

/**
 * Enqueue dedicated assets for the Quick Product Manager page.
 */
function test_theme_load_quick_manager_assets() {
	add_action( 'admin_enqueue_scripts', function() {
		// Enqueue Cairo Font for Admin UI
		wp_enqueue_style(
			'test_theme_cairo_admin',
			'https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap',
			array(),
			null
		);

		$js_path = file_exists( get_template_directory() . '/assets/src/js/admin-quick-manager.js' )
			? '/assets/src/js/admin-quick-manager.js'
			: '/assets/js/admin-quick-manager.js';

		wp_enqueue_script(
			'test_theme_admin_quick_manager',
			get_template_directory_uri() . $js_path,
			array( 'jquery' ),
			defined( 'CUSTOM_STORE_VERSION' ) ? CUSTOM_STORE_VERSION : ( defined( 'TEST_THEME_VERSION' ) ? TEST_THEME_VERSION : '1.0.0' ),
			true
		);

		wp_localize_script(
			'test_theme_admin_quick_manager',
			'testThemeQuickManager',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'test_theme_quick_manager_nonce' ),
			)
		);
	} );
}

/**
 * Render the Quick Product Manager Screen.
 */
function test_theme_render_quick_manager_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'عذراً، لا تمتلك الصلاحيات الكافية للوصول إلى هذه الصفحة.' );
	}

	$search_query = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$cat_filter   = isset( $_GET['product_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['product_cat'] ) ) : '';
	$stock_filter = isset( $_GET['stock_status'] ) ? sanitize_text_field( wp_unslash( $_GET['stock_status'] ) ) : '';
	$paged        = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;

	// Build Query Arguments
	$query_args = array(
		'status'   => 'publish',
		'limit'    => 20,
		'page'     => $paged,
		'paginate' => true,
		'orderby'  => 'date',
		'order'    => 'DESC',
	);

	if ( ! empty( $search_query ) ) {
		$query_args['s'] = $search_query;
	}
	if ( ! empty( $cat_filter ) ) {
		$query_args['category'] = array( $cat_filter );
	}
	if ( ! empty( $stock_filter ) ) {
		$query_args['stock_status'] = $stock_filter;
	}

	$results      = wc_get_products( $query_args );
	$products     = $results->products;
	$total_pages  = $results->max_num_pages;
	$total_count  = $results->total;

	// General Stats
	$counts = wp_count_posts( 'product' );
	$total_published = isset( $counts->publish ) ? $counts->publish : 0;
	$categories = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	) );
	?>
	<div class="wrap test-theme-quick-manager-wrap" dir="rtl" style="font-family: 'Cairo', system-ui, sans-serif; max-width: 1400px; margin: 20px auto 40px auto; padding: 0 15px;">

		<!-- Header Banner -->
		<div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 24px 30px; border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15); margin-bottom: 24px; position: relative; overflow: hidden;">
			<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: gap; gap: 20px; position: relative; z-index: 2;">
				<div>
					<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
						<span style="background: rgba(234, 88, 12, 0.2); color: #ea580c; border: 1px solid rgba(234, 88, 12, 0.4); width: 42px; height: 42px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 20px;">
							⚡
						</span>
						<h1 style="color: #ffffff; font-size: 24px; font-weight: 900; margin: 0; padding: 0; letter-spacing: -0.5px;">
							الإدارة السريعة لمنتجات <?php echo esc_html( function_exists( 'ashrafco_get_store_name' ) ? ashrafco_get_store_name( 'ar' ) : 'أشرفكو' ); ?>
						</h1>
					</div>
					<p style="color: #94a3b8; font-size: 13px; margin: 0;">
						تحكم فوري بأسعار ماكينات ومعدات السيراميك، تعديل كميات المخزون، وتبديل حالة التوفر بضغطة زر دون الحاجة لفتح كل منتج على حدة.
					</p>
				</div>

				<div style="display: flex; align-items: center; gap: 10px;">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ); ?>" class="button button-primary" style="background: #ea580c; border-color: #ea580c; font-weight: 700; border-radius: 10px; padding: 6px 18px; height: auto; font-size: 13px;">
						+ إضافة معدة جديدة
					</a>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" target="_blank" class="button" style="background: rgba(255,255,255,0.1); color: #fff; border-color: rgba(255,255,255,0.2); border-radius: 10px; padding: 6px 16px; height: auto; font-size: 13px;">
						👁️ زيارة المتجر
					</a>
				</div>
			</div>
		</div>

		<!-- Filters & Search Toolbar -->
		<div style="background: #ffffff; padding: 18px 20px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 20px;">
			<form method="get" action="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0;">
				<input type="hidden" name="post_type" value="product" />
				<input type="hidden" name="page" value="test-theme-quick-manager" />

				<!-- Search Input -->
				<div style="flex: 1; min-width: 240px; position: relative;">
					<input
						type="search"
						name="s"
						value="<?php echo esc_attr( $search_query ); ?>"
						placeholder="ابحث بالاسم أو الباركود SKU..."
						style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 14px; font-size: 13px; font-family: inherit;"
					/>
				</div>

				<!-- Category Dropdown -->
				<div style="min-width: 200px;">
					<select name="product_cat" style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px; font-family: inherit;">
						<option value="">جميع الأقسام والتصنيفات</option>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat->slug ); ?>" <?php selected( $cat_filter, $cat->slug ); ?>>
								<?php echo esc_html( $cat->name ); ?> (<?php echo esc_html( $cat->count ); ?>)
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<!-- Stock Status Filter -->
				<div style="min-width: 160px;">
					<select name="stock_status" style="width: 100%; height: 42px; border-radius: 10px; border: 1px solid #cbd5e1; padding: 0 10px; font-size: 13px; font-family: inherit;">
						<option value="">حالة التوفر (الكل)</option>
						<option value="instock" <?php selected( $stock_filter, 'instock' ); ?>>متوفر فقط</option>
						<option value="outofstock" <?php selected( $stock_filter, 'outofstock' ); ?>>نفذت الكمية فقط</option>
					</select>
				</div>

				<!-- Submit & Reset Buttons -->
				<button type="submit" class="button button-primary" style="background: #0f172a; border-color: #0f172a; height: 42px; padding: 0 20px; border-radius: 10px; font-weight: 700; font-size: 13px; font-family: inherit;">
					تصفية النتائج
				</button>

				<?php if ( ! empty( $search_query ) || ! empty( $cat_filter ) || ! empty( $stock_filter ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product&page=test-theme-quick-manager' ) ); ?>" class="button" style="height: 42px; line-height: 40px; border-radius: 10px; padding: 0 16px; font-size: 13px; font-family: inherit; color: #64748b;">
						إلغاء الفلترة
					</a>
				<?php endif; ?>

				<div style="margin-right: auto; font-size: 12px; color: #64748b; font-weight: 600;">
					عرض <strong style="color: #0f172a;"><?php echo esc_html( count( $products ) ); ?></strong> من إجمالي <strong style="color: #0f172a;"><?php echo esc_html( $total_count ); ?></strong> معدة
				</div>
			</form>
		</div>

		<!-- Interactive Table Container -->
		<div style="background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px -3px rgba(0,0,0,0.05); overflow: hidden;">
			<table class="wp-list-table widefat fixed striped" style="border: none; margin: 0; font-family: inherit;">
				<thead>
					<tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
						<th style="width: 70px; text-align: center; padding: 14px 10px; font-weight: 800; font-size: 13px; color: #475569;">الصورة</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 280px;">اسم المعدة والباركود</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 140px;">السعر الأساسي (ج.م)</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 140px;">سعر التخفيض (ج.م)</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 130px;">الكمية بالمخزن</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 140px; text-align: center;">حالة التوفر</th>
						<th style="padding: 14px 12px; font-weight: 800; font-size: 13px; color: #475569; width: 160px; text-align: center;">إجراءات سريعة</th>
					</tr>
				</thead>

				<tbody id="test-theme-products-tbody">
					<?php if ( ! empty( $products ) ) : ?>
						<?php foreach ( $products as $product ) :
							$p_id       = $product->get_id();
							$p_title    = $product->get_name();
							$p_thumb    = get_the_post_thumbnail_url( $p_id, 'thumbnail' ) ?: wc_placeholder_img_src();
							$p_sku      = $product->get_sku();
							$p_reg_price= $product->get_regular_price();
							$p_sale_price= $product->get_sale_price();
							$p_stock_qty= $product->get_stock_quantity();
							$p_status   = $product->get_stock_status();
							$p_view_url = get_permalink( $p_id );
							$p_edit_url = get_edit_post_link( $p_id );
							?>
							<tr id="product-row-<?php echo esc_attr( $p_id ); ?>" class="test-theme-product-row" data-product-id="<?php echo esc_attr( $p_id ); ?>" style="transition: all 0.3s ease;">

								<!-- 1. Thumbnail -->
								<td style="text-align: center; vertical-align: middle; padding: 12px 10px;">
									<img
										src="<?php echo esc_url( $p_thumb ); ?>"
										alt="<?php echo esc_attr( $p_title ); ?>"
										style="width: 52px; height: 52px; object-fit: cover; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-block;"
									/>
								</td>

								<!-- 2. Product Name & SKU -->
								<td style="vertical-align: middle; padding: 12px;">
									<div style="font-weight: 700; font-size: 13px; color: #0f172a; margin-bottom: 4px; line-height: 1.4;">
										<a href="<?php echo esc_url( $p_view_url ); ?>" target="_blank" style="color: #0f172a; text-decoration: none;" title="معاينة في المتجر">
											<?php echo esc_html( $p_title ); ?>
										</a>
									</div>
									<div style="display: flex; align-items: center; gap: 8px; font-size: 11px;">
										<span style="color: #64748b; font-weight: 600;">SKU:</span>
										<input
											type="text"
											class="test-theme-sku-input"
											value="<?php echo esc_attr( $p_sku ); ?>"
											placeholder="كود المعدة"
											style="height: 26px; padding: 2px 6px; font-size: 11px; width: 110px; border-radius: 6px; border: 1px solid #cbd5e1; font-family: monospace;"
										/>
										<a href="<?php echo esc_url( $p_edit_url ); ?>" target="_blank" style="color: #ea580c; text-decoration: none; font-size: 11px; font-weight: 700; margin-right: 6px;">
											[تعديل كامل]
										</a>
									</div>
								</td>

								<!-- 3. Regular Price -->
								<td style="vertical-align: middle; padding: 12px;">
									<div style="position: relative;">
										<input
											type="number"
											step="0.01"
											min="0"
											class="test-theme-reg-price-input"
											value="<?php echo esc_attr( $p_reg_price ); ?>"
											placeholder="0.00"
											style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px 10px; font-size: 13px; font-weight: 700; color: #0f172a; font-family: monospace;"
										/>
									</div>
								</td>

								<!-- 4. Sale Price -->
								<td style="vertical-align: middle; padding: 12px;">
									<div style="position: relative;">
										<input
											type="number"
											step="0.01"
											min="0"
											class="test-theme-sale-price-input"
											value="<?php echo esc_attr( $p_sale_price ); ?>"
											placeholder="بدون تخفيض"
											style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px 10px; font-size: 13px; font-weight: 700; color: #dc2626; font-family: monospace;"
										/>
									</div>
								</td>

								<!-- 5. Stock Quantity -->
								<td style="vertical-align: middle; padding: 12px;">
									<input
										type="number"
										step="1"
										min="0"
										class="test-theme-stock-qty-input"
										value="<?php echo esc_attr( null !== $p_stock_qty ? $p_stock_qty : '' ); ?>"
										placeholder="غير محدد"
										style="width: 100%; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px 10px; font-size: 13px; font-weight: 700; color: #0f172a; font-family: monospace;"
									/>
								</td>

								<!-- 6. Stock Status Toggle -->
								<td style="vertical-align: middle; padding: 12px; text-align: center;">
									<button
										type="button"
										class="test-theme-stock-toggle-btn"
										data-status="<?php echo esc_attr( $p_status ); ?>"
										style="cursor: pointer; border-radius: 10px; font-size: 12px; font-weight: 700; padding: 6px 14px; border: 1px solid transparent; width: 100%; transition: all 0.2s ease; <?php
										if ( 'instock' === $p_status ) {
											echo 'background: #f0fdf4; color: #166534; border-color: #86efac;';
										} else {
											echo 'background: #fef2f2; color: #991b1b; border-color: #fecaca;';
										}
										?>"
									>
										<?php echo 'instock' === $p_status ? '✓ متوفر' : '✕ غير متوفر'; ?>
									</button>
								</td>

								<!-- 7. Actions -->
								<td style="vertical-align: middle; padding: 12px; text-align: center;">
									<div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
										<button
											type="button"
											class="button button-primary test-theme-quick-save-btn"
											title="حفظ التعديلات السريعة"
											style="background: #0f172a; border-color: #0f172a; border-radius: 8px; height: 34px; padding: 0 12px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;"
										>
											<span class="btn-text">حفظ</span>
											<span class="btn-spinner" style="display: none;">⏳</span>
										</button>

										<button
											type="button"
											class="button test-theme-quick-trash-btn"
											title="نقل لسلة المهملات"
											style="color: #dc2626; border-color: #fecaca; background: #fff; border-radius: 8px; height: 34px; padding: 0 10px; font-size: 12px;"
										>
											🗑️
										</button>
									</div>
								</td>

							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="7" style="text-align: center; padding: 40px 20px; color: #64748b; font-size: 14px; font-weight: 600;">
								لم يتم العثور على أي منتجات مطابقة لخيارات البحث أو التصفية الحالية.
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<!-- Pagination Footer -->
		<?php if ( $total_pages > 1 ) : ?>
			<div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding: 10px 4px;">
				<div style="font-size: 13px; color: #64748b; font-weight: 600;">
					الصفحة <?php echo esc_html( $paged ); ?> من <?php echo esc_html( $total_pages ); ?>
				</div>

				<div style="display: flex; gap: 6px;">
					<?php if ( $paged > 1 ) : ?>
						<a
							href="<?php echo esc_url( add_query_arg( 'paged', $paged - 1 ) ); ?>"
							class="button"
							style="border-radius: 8px; font-size: 12px; font-weight: 700;"
						>
							&rarr; الصفحة السابقة
						</a>
					<?php endif; ?>

					<?php if ( $paged < $total_pages ) : ?>
						<a
							href="<?php echo esc_url( add_query_arg( 'paged', $paged + 1 ) ); ?>"
							class="button"
							style="border-radius: 8px; font-size: 12px; font-weight: 700;"
						>
							الصفحة التالية &larr;
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Toast Notification Container -->
		<div id="test-theme-toast" style="display: none; position: fixed; bottom: 25px; left: 25px; z-index: 99999; background: #0f172a; color: #fff; padding: 14px 22px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); font-size: 13px; font-weight: 700; align-items: center; gap: 10px; border-right: 4px solid #10b981; direction: rtl;">
			<span id="test-theme-toast-icon">✓</span>
			<span id="test-theme-toast-msg">تم حفظ التعديلات بنجاح!</span>
		</div>

	</div>
	<?php
}

/**
 * ============================================================================
 * AJAX Backend Endpoints: Quick Update & Quick Trash Product
 * ============================================================================
 */

/**
 * AJAX Handler for Quick Product Updating.
 */
function test_theme_ajax_quick_update_product() {
	check_ajax_referer( 'test_theme_quick_manager_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( array( 'message' => 'عذراً، لا تمتلك الصلاحيات الكافية.' ), 403 );
	}

	$product_id   = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
	if ( 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error( array( 'message' => 'المعرف المحدد ليس منتجاً صالحاً.' ), 400 );
	}

	if ( ! current_user_can( 'edit_post', $product_id ) && ! current_user_can( 'edit_product', $product_id ) ) {
		wp_send_json_error( array( 'message' => 'عذراً، لا تمتلك صلاحية تعديل هذا المنتج.' ), 403 );
	}

	$regular_price= isset( $_POST['regular_price'] ) ? sanitize_text_field( wp_unslash( $_POST['regular_price'] ) ) : '';
	$sale_price   = isset( $_POST['sale_price'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_price'] ) ) : '';
	$sku          = isset( $_POST['sku'] ) ? sanitize_text_field( wp_unslash( $_POST['sku'] ) ) : '';
	$stock_qty    = isset( $_POST['stock_qty'] ) ? sanitize_text_field( wp_unslash( $_POST['stock_qty'] ) ) : '';
	$stock_status = isset( $_POST['stock_status'] ) ? sanitize_text_field( wp_unslash( $_POST['stock_status'] ) ) : 'instock';

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'المنتج غير موجود أو تم حذفه.' ) );
	}

	// 1. Update Regular Price
	$product->set_regular_price( wc_format_decimal( $regular_price ) );

	// 2. Update Sale Price
	if ( '' !== $sale_price && is_numeric( $sale_price ) ) {
		$product->set_sale_price( wc_format_decimal( $sale_price ) );
	} else {
		$product->set_sale_price( '' );
	}

	// 3. Update SKU
	try {
		$product->set_sku( $sku );
	} catch ( Exception $e ) {
		// Ignore duplicate SKU warnings or handle gracefully
	}

	// 4. Update Stock Quantity & Status
	if ( '' !== $stock_qty && is_numeric( $stock_qty ) ) {
		$product->set_manage_stock( true );
		$product->set_stock_quantity( intval( $stock_qty ) );
	} else {
		$product->set_manage_stock( false );
	}

	if ( in_array( $stock_status, array( 'instock', 'outofstock' ), true ) ) {
		$product->set_stock_status( $stock_status );
	}

	$product->save();

	wp_send_json_success( array(
		'product_id'      => $product_id,
		'price_html'      => $product->get_price_html(),
		'stock_status'    => $product->get_stock_status(),
		'formatted_total' => wc_price( $product->get_price() ),
		'message'         => 'تم تحديث بيانات المعدة بنجاح.',
	) );
}
add_action( 'wp_ajax_test_theme_quick_update_product', 'test_theme_ajax_quick_update_product' );

/**
 * AJAX Handler for Quick Product Trashing.
 */
function test_theme_ajax_quick_delete_product() {
	check_ajax_referer( 'test_theme_quick_manager_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_send_json_error( array( 'message' => 'عذراً، لا تمتلك الصلاحيات الكافية.' ), 403 );
	}

	$product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
	if ( 'product' !== get_post_type( $product_id ) ) {
		wp_send_json_error( array( 'message' => 'المعرف المحدد ليس منتجاً صالحاً.' ), 400 );
	}

	if ( ! current_user_can( 'delete_post', $product_id ) && ! current_user_can( 'delete_product', $product_id ) ) {
		wp_send_json_error( array( 'message' => 'عذراً، لا تمتلك صلاحية حذف هذا المنتج.' ), 403 );
	}

	$product    = wc_get_product( $product_id );

	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'المنتج غير موجود.' ) );
	}

	// Move to trash
	$product->delete();

	wp_send_json_success( array(
		'product_id' => $product_id,
		'message'    => 'تم نقل المعدة إلى سلة المهملات بنجاح.',
	) );
}
add_action( 'wp_ajax_test_theme_quick_delete_product', 'test_theme_ajax_quick_delete_product' );
