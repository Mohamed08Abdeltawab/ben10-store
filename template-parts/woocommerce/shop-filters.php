<?php
/**
 * Modern Advanced Shop Filter Sidebar & Mobile Drawer
 *
 * Implements granular filtering for Ashrafco store:
 * 1. Categories & Sections (الأقسام والفئات)
 * 2. Stock Availability (حالة التوفر بالمخزون)
 * 3. Country of Origin (بلد الصنع والمنشأ)
 * 4. Product Classification (تصنيف المنتج)
 * 5. Price Range ComboBox (نطاق السعر)
 *
 * Recreated with a fixed-height, compact, independently scrollable sidebar
 * matching the visual reference with right-side scrollbar and fixed bottom actions.
 *
 * @package Ashrafco
 * @since 1.0.16
 */

defined( 'ABSPATH' ) || exit;

// 1. Retrieve Current Active GET Parameters
$selected_cats      = isset( $_GET['filter_cat'] ) ? (array) $_GET['filter_cat'] : array();
$in_stock_only      = ! empty( $_GET['in_stock_only'] ) && '1' === (string) $_GET['in_stock_only'];
$selected_brands    = isset( $_GET['filter_brand'] ) ? (array) $_GET['filter_brand'] : array();
$selected_countries = isset( $_GET['filter_country'] ) ? (array) $_GET['filter_country'] : array();
$selected_classes   = isset( $_GET['filter_classification'] ) ? (array) $_GET['filter_classification'] : array();
$min_price          = isset( $_GET['min_price'] ) ? sanitize_text_field( wp_unslash( $_GET['min_price'] ) ) : '';
$max_price          = isset( $_GET['max_price'] ) ? sanitize_text_field( wp_unslash( $_GET['max_price'] ) ) : '';
$search_keyword     = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$orderby            = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';

// 2. Check if any filter is active
$has_active_filters = ! empty( $selected_cats ) ||
	$in_stock_only ||
	! empty( $selected_brands ) ||
	! empty( $selected_countries ) ||
	! empty( $selected_classes ) ||
	'' !== $min_price ||
	'' !== $max_price ||
	'' !== $search_keyword;

// 3. Determine base form action & reset URL
if ( is_product_category() ) {
	$current_term = get_queried_object();
	$form_action  = get_term_link( $current_term );
	$reset_url    = $form_action;
} else {
	$form_action  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	$reset_url    = $form_action;
}

// 4. Query Categories for Filter
$uncat_id   = (int) get_option( 'default_product_cat', 0 );
$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => false,
	'exclude'    => array( $uncat_id ),
	'orderby'    => 'count',
	'order'      => 'DESC',
) );

// 4.5. Query Brands for Filter
$brands_list = function_exists( 'ashrafco_get_brands' ) ? ashrafco_get_brands() : array();
if ( empty( $brands_list ) && taxonomy_exists( 'product_brand' ) ) {
	$brand_terms = get_terms( array(
		'taxonomy'   => 'product_brand',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );
	if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
		foreach ( $brand_terms as $bt ) {
			$brands_list[] = array(
				'id'    => $bt->term_id,
				'name'  => $bt->name,
				'slug'  => $bt->slug,
				'count' => (int) $bt->count,
			);
		}
	}
}

// 5. Predefined Countries in Exact Required Order
$countries_list = array(
	'الصين',
	'ألمانيا',
	'إيطاليا',
	'مصر (صناعة محلية)',
	'تركيا',
	'تايوان',
	'اليابان',
	'إسبانيا',
	'الولايات المتحدة',
	'أخرى',
);

// 6. Predefined Classifications in Required Order
$classifications_list = array(
	'أدوات يدوية',
	'أدوات كهربائية',
	'مستلزمات وملحقات',
	'مواد استهلاكية',
	'أدوات قياس وليزر',
	'معدات وأجهزة',
	'أخرى',
);

// 7. Price Dropdown Choices
$price_options = array(
	'50'   => '50 جنيه',
	'100'  => '100 جنيه',
	'150'  => '150 جنيه',
	'200'  => '200 جنيه',
	'300'  => '300 جنيه',
	'500'  => '500 جنيه',
	'750'  => '750 جنيه',
	'1000' => '1,000 جنيه',
	'1500' => '1,500 جنيه',
	'2000' => '2,000 جنيه',
	'3000' => '3,000 جنيه',
	'4000' => '4,000 جنيه',
	'5000' => '5,000 جنيه',
);

// 8. Compute Real-time Filter Counts
$counts = function_exists( 'test_theme_compute_filter_counts' )
	? test_theme_compute_filter_counts( $_GET )
	: array(
		'instock'         => 0,
		'countries'       => array(),
		'classifications' => array(),
	);

$render_sidebar = ! isset( $args['sidebar'] ) || ! empty( $args['sidebar'] );
$render_drawer  = ! isset( $args['drawer'] )  || ! empty( $args['drawer'] );
?>

<?php if ( $render_sidebar ) : ?>
<!-- ==========================================================================
     1. DESKTOP FILTER SIDEBAR
     Fixed-height, compact, independently scrollable with right-side scrollbar
     ========================================================================== -->
<div
	id="test-theme-shop-filters-desktop"
	class="w-full flex flex-col sticky top-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs font-sans text-right overflow-hidden"
	dir="rtl"
	aria-label="<?php esc_attr_e( 'تصفية المنتجات', 'ashrafco' ); ?>"
>
	<!-- Filter Form takes full height and flex-col -->
	<form method="GET" action="<?php echo esc_url( $form_action ); ?>" class="test-theme-filter-form flex flex-col h-full w-full">

		<!-- Preserve Sorting if Active -->
		<?php if ( ! empty( $orderby ) ) : ?>
			<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>" />
		<?php endif; ?>

		<!-- Non-scrolling Top Area: Header + Search -->
		<div class="p-4 sm:p-5 pb-3.5 flex-shrink-0 space-y-3.5 bg-white border-b border-slate-100/90">
			<!-- Sidebar Header: Red Slider Icon + Title -->
			<div class="flex items-center justify-center gap-2 pb-0.5">
				<div class="w-8 h-8 rounded-full bg-red-50 text-primary flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
					</svg>
				</div>
				<h2 class="font-extrabold text-base text-slate-900"><?php esc_html_e( 'تصفية المنتجات', 'ashrafco' ); ?></h2>
			</div>

			<!-- Search Input inside Category/Filter -->
			<div class="relative">
				<input
					type="search"
					name="s"
					value="<?php echo esc_attr( $search_keyword ); ?>"
					placeholder="<?php esc_attr_e( 'ابحث داخل هذه الفئة ...', 'ashrafco' ); ?>"
					class="w-full pl-9 pr-3.5 py-2 bg-slate-50 border border-slate-200/90 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition"
				/>
				<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
					</svg>
				</div>
			</div>
		</div>

		<!-- Middle Independently Scrollable Filters Area (dir="ltr" so scrollbar is on the RIGHT!) -->
		<div class="test-theme-sidebar-scroll flex-1 overflow-y-auto min-h-0 pl-3.5 pr-2 py-3" dir="ltr">
			<!-- Inner Content Wrapper (dir="rtl" so all content is Arabic RTL!) -->
			<div dir="rtl" class="space-y-4 pr-1">

				<!-- ==================================================================
				     SECTION 1: الأقسام والفئات (Categories)
				     ================================================================== -->
				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<div class="filter-section border-t border-slate-100 pt-3">
						<button
							type="button"
							class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
							aria-expanded="true"
						>
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
								</svg>
								<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'الأقسام والفئات', 'ashrafco' ); ?></span>
							</div>
							<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
							</svg>
						</button>
						<div class="filter-section-content mt-3 max-h-[190px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
							<?php foreach ( $categories as $cat ) :
								$is_checked = in_array( $cat->slug, $selected_cats, true ) || ( is_product_category( $cat->slug ) );
							?>
								<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer group select-none">
									<span class="flex items-center gap-2">
										<input
											type="checkbox"
											name="filter_cat[]"
											value="<?php echo esc_attr( $cat->slug ); ?>"
											<?php checked( $is_checked ); ?>
											class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
										/>
										<span class="group-hover:translate-x-0.5 transition-transform"><?php echo esc_html( $cat->name ); ?></span>
									</span>
									<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
										<?php echo esc_html( $cat->count ); ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- ==================================================================
				     SECTION 2: حالة التوفر بالمخزون (Stock Status)
				     ================================================================== -->
				<div class="filter-section border-t border-slate-100 pt-3">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'حالة التوفر بالمخزون', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3">
						<label class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none group">
							<span class="flex items-center gap-2">
								<input
									type="checkbox"
									name="in_stock_only"
									value="1"
									<?php checked( $in_stock_only ); ?>
									class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
								/>
								<span class="group-hover:translate-x-0.5 transition-transform"><?php esc_html_e( 'متوفر بالمخزون فقط', 'ashrafco' ); ?></span>
							</span>
							<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
								<?php echo esc_html( isset( $counts['instock'] ) ? $counts['instock'] : 0 ); ?>
							</span>
						</label>
					</div>
				</div>

				<!-- ==================================================================
				     SECTION 2.5: العلامة التجارية (Brand Filter)
				     ================================================================== -->
				<?php if ( ! empty( $brands_list ) ) : ?>
					<div class="filter-section border-t border-slate-100 pt-3">
						<button
							type="button"
							class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
							aria-expanded="true"
						>
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
								</svg>
								<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'العلامة التجارية', 'ashrafco' ); ?></span>
							</div>
							<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
							</svg>
						</button>
						<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
							<?php foreach ( $brands_list as $brand_item ) :
								$is_checked = in_array( $brand_item['slug'], $selected_brands, true ) || in_array( $brand_item['name'], $selected_brands, true );
								$b_count = isset( $brand_item['count'] ) ? (int) $brand_item['count'] : 0;
							?>
								<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer group select-none">
									<span class="flex items-center gap-2">
										<input
											type="checkbox"
											name="filter_brand[]"
											value="<?php echo esc_attr( $brand_item['slug'] ); ?>"
											<?php checked( $is_checked ); ?>
											class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
										/>
										<span class="group-hover:translate-x-0.5 transition-transform"><?php echo esc_html( $brand_item['name'] ); ?></span>
									</span>
									<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
										<?php echo esc_html( $b_count ); ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- ==================================================================
				     SECTION 3: بلد الصنع والمنشأ (Country of Origin)
				     ================================================================== -->
				<div class="filter-section border-t border-slate-100 pt-3">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<circle cx="12" cy="12" r="10"></circle>
								<path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'بلد الصنع والمنشأ', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
						<?php foreach ( $countries_list as $country_name ) :
							$is_checked = in_array( $country_name, $selected_countries, true );
							$country_count = isset( $counts['countries'][ $country_name ] ) ? $counts['countries'][ $country_name ] : 0;
						?>
							<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer group select-none">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_country[]"
										value="<?php echo esc_attr( $country_name ); ?>"
										<?php checked( $is_checked ); ?>
										class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
									/>
									<span class="group-hover:translate-x-0.5 transition-transform"><?php echo esc_html( $country_name ); ?></span>
								</span>
								<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
									<?php echo esc_html( $country_count ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- ==================================================================
				     SECTION 4: تصنيف المنتج (Product Classification)
				     ================================================================== -->
				<div class="filter-section border-t border-slate-100 pt-3">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'تصنيف المنتج', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
						<?php foreach ( $classifications_list as $cls_name ) :
							$cls_count = isset( $counts['classifications'][ $cls_name ] ) ? $counts['classifications'][ $cls_name ] : 0;
							$is_checked = in_array( $cls_name, $selected_classes, true );
						?>
							<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer group select-none">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_classification[]"
										value="<?php echo esc_attr( $cls_name ); ?>"
										<?php checked( $is_checked ); ?>
										class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
									/>
									<span class="group-hover:translate-x-0.5 transition-transform"><?php echo esc_html( $cls_name ); ?></span>
								</span>
								<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
									<?php echo esc_html( $cls_count ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- ==================================================================
				     SECTION 5: نطاق السعر (جنيه مصري) (Price Range ComboBox)
				     ================================================================== -->
				<div class="filter-section border-t border-slate-100 pt-3">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<rect x="2" y="6" width="20" height="12" rx="2"></rect>
								<circle cx="12" cy="12" r="3"></circle>
								<path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M18 12h.01"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'نطاق السعر (جنيه مصري)', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3">
						<div class="grid grid-cols-2 gap-2.5">
							<!-- Right side in RTL: من (Min Price) -->
							<div>
								<label class="block text-xs font-semibold text-slate-600 mb-1.5"><?php esc_html_e( 'من', 'ashrafco' ); ?></label>
								<select
									name="min_price"
									class="test-theme-filter-select w-full bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition cursor-pointer appearance-none"
								>
									<option value=""><?php esc_html_e( 'بدون حد أدنى', 'ashrafco' ); ?></option>
									<?php foreach ( $price_options as $val => $label ) : ?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) $min_price, (string) $val ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Left side in RTL: إلى (Max Price) -->
							<div>
								<label class="block text-xs font-semibold text-slate-600 mb-1.5"><?php esc_html_e( 'إلى', 'ashrafco' ); ?></label>
								<select
									name="max_price"
									class="test-theme-filter-select w-full bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition cursor-pointer appearance-none"
								>
									<option value=""><?php esc_html_e( 'بدون حد أقصى', 'ashrafco' ); ?></option>
									<?php foreach ( $price_options as $val => $label ) : ?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) $max_price, (string) $val ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<!-- Error Message Notice -->
						<div class="price-range-error text-[11px] font-bold text-primary mt-2 hidden"></div>
					</div>
				</div>

			</div>
		</div>

		<!-- Non-scrolling Bottom Area: Apply Filters & Clear All Filters Buttons -->
		<div class="p-4 sm:p-5 pt-3 border-t border-slate-100/90 flex-shrink-0 space-y-2 bg-white rounded-b-3xl">
			<!-- Primary Apply Button: Red Solid -->
			<button
				type="submit"
				class="w-full bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-bold text-sm py-2.5 px-4 rounded-xl shadow-md shadow-red-600/20 flex items-center justify-center gap-2 transition-all cursor-pointer"
			>
				<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
				</svg>
				<span><?php esc_html_e( 'تطبيق الفلتر', 'ashrafco' ); ?></span>
			</button>

			<!-- Secondary Reset Button: White with Border -->
			<a
				href="<?php echo esc_url( $reset_url ); ?>"
				class="w-full bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-sm py-2 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer text-center"
			>
				<svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
				</svg>
				<span><?php esc_html_e( 'مسح كل الفلاتر', 'ashrafco' ); ?></span>
			</a>
		</div>

	</form>
</div>
<?php endif; ?>

<?php if ( $render_drawer ) : ?>
<!-- ==========================================================================
     2. MOBILE SLIDE-OVER FILTERS DRAWER (lg:hidden)
     Touch-friendly, adapts to mobile viewport height, non-scrolling action buttons
     ========================================================================== -->
<div
	id="test-theme-filters-drawer"
	class="fixed inset-0 z-[100] transition-all duration-300 pointer-events-none opacity-0 invisible lg:hidden"
	aria-hidden="true"
	role="dialog"
	aria-modal="true"
	aria-labelledby="test-theme-mobile-filters-title"
>
	<!-- Backdrop Blur Overlay -->
	<div
		id="test-theme-filters-backdrop"
		class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity duration-300 opacity-0"
	></div>

	<!-- Slide-over Content Sheet (Sliding from Right in RTL) -->
	<div
		id="test-theme-filters-sheet"
		class="absolute top-0 bottom-0 right-0 w-full max-w-xs sm:max-w-sm bg-white shadow-2xl flex flex-col transition-transform duration-300 transform translate-x-full font-sans pb-safe overflow-hidden"
		dir="rtl"
	>
		<!-- Drawer Header -->
		<div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0 bg-white">
			<div class="flex items-center gap-2.5">
				<div class="w-8 h-8 rounded-full bg-red-50 text-primary flex items-center justify-center flex-shrink-0">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
					</svg>
				</div>
				<h3 id="test-theme-mobile-filters-title" class="font-extrabold text-base text-slate-900 leading-tight">
					<?php esc_html_e( 'تصفية المنتجات', 'ashrafco' ); ?>
				</h3>
			</div>

			<!-- Close Button -->
			<button
				type="button"
				id="test-theme-filters-close"
				class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer"
				aria-label="<?php esc_attr_e( 'إغلاق الفلاتر', 'ashrafco' ); ?>"
			>
				<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<!-- Mobile Form spans scrollable body and sticky action buttons -->
		<form id="test-theme-mobile-filter-form" method="GET" action="<?php echo esc_url( $form_action ); ?>" class="test-theme-filter-form flex flex-col flex-1 min-h-0">

			<!-- Preserve Sorting -->
			<?php if ( ! empty( $orderby ) ) : ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>" />
			<?php endif; ?>

			<!-- Drawer Scrollable Form Body -->
			<div class="p-5 overflow-y-auto flex-1 min-h-0 space-y-5">

				<!-- Search Input inside Category/Filter -->
				<div class="relative">
					<input
						type="search"
						name="s"
						value="<?php echo esc_attr( $search_keyword ); ?>"
						placeholder="<?php esc_attr_e( 'ابحث داخل هذه الفئة ...', 'ashrafco' ); ?>"
						class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200/90 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition"
					/>
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
						</svg>
					</div>
				</div>

				<!-- Section 1: Categories -->
				<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
					<div class="filter-section border-t border-slate-100 pt-4">
						<button
							type="button"
							class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
							aria-expanded="true"
						>
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
								</svg>
								<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'الأقسام والفئات', 'ashrafco' ); ?></span>
							</div>
							<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
							</svg>
						</button>
						<div class="filter-section-content mt-3 max-h-[190px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
							<?php foreach ( $categories as $cat ) :
								$is_checked = in_array( $cat->slug, $selected_cats, true ) || ( is_product_category( $cat->slug ) );
							?>
								<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none">
									<span class="flex items-center gap-2">
										<input
											type="checkbox"
											name="filter_cat[]"
											value="<?php echo esc_attr( $cat->slug ); ?>"
											<?php checked( $is_checked ); ?>
											class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
										/>
										<span><?php echo esc_html( $cat->name ); ?></span>
									</span>
									<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
										<?php echo esc_html( $cat->count ); ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Section 2: Stock Status -->
				<div class="filter-section border-t border-slate-100 pt-4">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'حالة التوفر بالمخزون', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3">
						<label class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none">
							<span class="flex items-center gap-2">
								<input
									type="checkbox"
									name="in_stock_only"
									value="1"
									<?php checked( $in_stock_only ); ?>
									class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
								/>
								<span><?php esc_html_e( 'متوفر بالمخزون فقط', 'ashrafco' ); ?></span>
							</span>
							<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
								<?php echo esc_html( isset( $counts['instock'] ) ? $counts['instock'] : 0 ); ?>
							</span>
						</label>
					</div>
				</div>

				<!-- Section 2.5: Brand -->
				<?php if ( ! empty( $brands_list ) ) : ?>
					<div class="filter-section border-t border-slate-100 pt-4">
						<button
							type="button"
							class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
							aria-expanded="true"
						>
							<div class="flex items-center gap-2">
								<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
								</svg>
								<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'العلامة التجارية', 'ashrafco' ); ?></span>
							</div>
							<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
							</svg>
						</button>
						<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
							<?php foreach ( $brands_list as $brand_item ) :
								$is_checked = in_array( $brand_item['slug'], $selected_brands, true ) || in_array( $brand_item['name'], $selected_brands, true );
								$b_count = isset( $brand_item['count'] ) ? (int) $brand_item['count'] : 0;
							?>
								<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none">
									<span class="flex items-center gap-2">
										<input
											type="checkbox"
											name="filter_brand[]"
											value="<?php echo esc_attr( $brand_item['slug'] ); ?>"
											<?php checked( $is_checked ); ?>
											class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
										/>
										<span><?php echo esc_html( $brand_item['name'] ); ?></span>
									</span>
									<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
										<?php echo esc_html( $b_count ); ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Section 3: Country of Origin -->
				<div class="filter-section border-t border-slate-100 pt-4">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<circle cx="12" cy="12" r="10"></circle>
								<path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'بلد الصنع والمنشأ', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
						<?php foreach ( $countries_list as $country_name ) :
							$is_checked = in_array( $country_name, $selected_countries, true );
							$country_count = isset( $counts['countries'][ $country_name ] ) ? $counts['countries'][ $country_name ] : 0;
						?>
							<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_country[]"
										value="<?php echo esc_attr( $country_name ); ?>"
										<?php checked( $is_checked ); ?>
										class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
									/>
									<span><?php echo esc_html( $country_name ); ?></span>
								</span>
								<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
									<?php echo esc_html( $country_count ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Section 4: Product Classification -->
				<div class="filter-section border-t border-slate-100 pt-4">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'تصنيف المنتج', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3 max-h-[180px] overflow-y-auto space-y-2.5 pr-1.5 pl-0.5" dir="ltr">
						<?php foreach ( $classifications_list as $cls_name ) :
							$cls_count = isset( $counts['classifications'][ $cls_name ] ) ? $counts['classifications'][ $cls_name ] : 0;
							$is_checked = in_array( $cls_name, $selected_classes, true );
						?>
							<label dir="rtl" class="flex items-center justify-between text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 cursor-pointer select-none">
								<span class="flex items-center gap-2">
									<input
										type="checkbox"
										name="filter_classification[]"
										value="<?php echo esc_attr( $cls_name ); ?>"
										<?php checked( $is_checked ); ?>
										class="w-4 h-4 rounded text-red-600 focus:ring-red-500 border-slate-300 transition"
									/>
									<span><?php echo esc_html( $cls_name ); ?></span>
								</span>
								<span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full min-w-[20px] text-center font-mono">
									<?php echo esc_html( $cls_count ); ?>
								</span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Section 5: Price Range ComboBox -->
				<div class="filter-section border-t border-slate-100 pt-4">
					<button
						type="button"
						class="filter-section-toggle w-full flex items-center justify-between py-1 text-right cursor-pointer select-none group"
						aria-expanded="true"
					>
						<div class="flex items-center gap-2">
							<svg class="w-4 h-4 text-primary flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
								<rect x="2" y="6" width="20" height="12" rx="2"></rect>
								<circle cx="12" cy="12" r="3"></circle>
								<path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M18 12h.01"></path>
							</svg>
							<span class="font-bold text-sm text-slate-900"><?php esc_html_e( 'نطاق السعر (جنيه مصري)', 'ashrafco' ); ?></span>
						</div>
						<svg class="filter-section-chevron w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"></path>
						</svg>
					</button>
					<div class="filter-section-content mt-3">
						<div class="grid grid-cols-2 gap-2.5">
							<div>
								<label class="block text-xs font-semibold text-slate-600 mb-1.5"><?php esc_html_e( 'من', 'ashrafco' ); ?></label>
								<select
									name="min_price"
									class="test-theme-filter-select w-full bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition cursor-pointer appearance-none"
								>
									<option value=""><?php esc_html_e( 'بدون حد أدنى', 'ashrafco' ); ?></option>
									<?php foreach ( $price_options as $val => $label ) : ?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) $min_price, (string) $val ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div>
								<label class="block text-xs font-semibold text-slate-600 mb-1.5"><?php esc_html_e( 'إلى', 'ashrafco' ); ?></label>
								<select
									name="max_price"
									class="test-theme-filter-select w-full bg-white border border-slate-200 rounded-xl px-2.5 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-1 focus:ring-red-500 focus:border-red-500 transition cursor-pointer appearance-none"
								>
									<option value=""><?php esc_html_e( 'بدون حد أقصى', 'ashrafco' ); ?></option>
									<?php foreach ( $price_options as $val => $label ) : ?>
										<option value="<?php echo esc_attr( $val ); ?>" <?php selected( (string) $max_price, (string) $val ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="price-range-error text-[11px] font-bold text-primary mt-2 hidden"></div>
					</div>
				</div>

			</div>

			<!-- Fixed Drawer Form Actions Footer -->
			<div class="p-4 pb-5 sm:pb-6 border-t border-slate-100 flex-shrink-0 space-y-2 bg-white">
				<button
					type="submit"
					class="w-full bg-primary hover:bg-primary-hover active:scale-[0.99] text-white font-bold text-sm py-3 px-4 rounded-xl shadow-md shadow-red-600/20 flex items-center justify-center gap-2 transition-all cursor-pointer"
				>
					<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
					</svg>
					<span><?php esc_html_e( 'تطبيق الفلتر', 'ashrafco' ); ?></span>
				</button>

				<a
					href="<?php echo esc_url( $reset_url ); ?>"
					class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer text-center"
				>
					<svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
					</svg>
					<span><?php esc_html_e( 'مسح كل الفلاتر', 'ashrafco' ); ?></span>
				</a>
			</div>

		</form>
	</div>
</div>
<?php endif; ?>
