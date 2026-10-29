<?php
/**
 * Admin Portal Categories & Products Controller
 *
 * Prepares product editing pre-fill data, category specs matrix,
 * category dashboard listings, and product drill-down queries.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Get Products and Categories View Model Data
 *
 * @return array Products view model data
 */
function ashrafco_portal_get_products_data() {
	$action          = isset($_GET['action']) ? sanitize_key($_GET['action']) : '';
	$is_new_mode     = ('new-product' === $action);
	$edit_product_id = ('edit-product' === $action && isset($_GET['id'])) ? absint($_GET['id']) : 0;
	$edit_product    = $edit_product_id ? wc_get_product($edit_product_id) : null;
	$is_edit_mode    = ($edit_product instanceof WC_Product);
	$is_form_mode    = ($is_new_mode || $is_edit_mode);
	$cat_id_param    = isset($_GET['cat_id']) ? absint($_GET['cat_id']) : 0;

	// Prepare Form Initial Values
	if ($is_edit_mode) {
		$f_name          = $edit_product->get_name();
		$f_sku           = $edit_product->get_sku();
		$f_regular_price = $edit_product->get_regular_price();
		$f_sale_price    = $edit_product->get_sale_price();

		// Load real sale scheduling date
		$date_to_obj = $edit_product->get_date_on_sale_to();
		if ($date_to_obj instanceof WC_DateTime) {
			$f_sale_end_date    = $date_to_obj->date('Y-m-d');
			$f_sale_expiry_type = 'scheduled';
		} else {
			$f_sale_end_date    = '';
			$f_sale_expiry_type = 'none';
		}

		$f_manage_stock = $edit_product->get_manage_stock();
		$f_stock_qty    = $edit_product->get_stock_quantity();
		$f_stock_status = $edit_product->get_stock_status();

		// WooCommerce has no native "lowstock" status. Derive it for display
		// from the managed stock quantity so the portal pill stays accurate.
		if ('instock' === $f_stock_status && $f_manage_stock && null !== $f_stock_qty) {
			$low_stock_threshold = absint(get_option('woocommerce_notify_low_stock_amount', 2));
			if ($f_stock_qty > 0 && $f_stock_qty <= $low_stock_threshold) {
				$f_stock_status = 'lowstock';
			}
		}

		$f_is_featured  = $edit_product->is_featured();
		$f_status       = $edit_product->get_status();
		$f_is_published = ('publish' === $f_status);
		$f_desc         = $edit_product->get_description();
		$f_short_desc   = $edit_product->get_short_description();
		$f_categories   = $edit_product->get_category_ids();

		// Resolve primary category for breadcrumb & return actions if not passed in query
		if (empty($cat_id_param) && !empty($f_categories)) {
			$cat_id_param = reset($f_categories);
		}

		// Images and Gallery
		$f_thumb_id     = $edit_product->get_image_id();
		$f_thumb_url    = $f_thumb_id ? wp_get_attachment_url($f_thumb_id) : '';
		$f_gallery_ids  = $edit_product->get_gallery_image_ids();
		$f_gallery_data = array();
		foreach ($f_gallery_ids as $gid) {
			$url = wp_get_attachment_url($gid);
			if ($url) {
				$f_gallery_data[] = array(
					'id'  => $gid,
					'url' => $url,
				);
			}
		}

		// Optional Product File
		$f_file_id   = get_post_meta($edit_product_id, '_test_theme_product_file_id', true);
		$f_file_url  = get_post_meta($edit_product_id, '_test_theme_product_file_url', true);
		$f_file_name = get_post_meta($edit_product_id, '_test_theme_product_file_name', true);
		$f_file_size = get_post_meta($edit_product_id, '_test_theme_product_file_size', true);

		// 1. Basic Specifications
		$f_brand = get_post_meta($edit_product_id, '_test_theme_brand', true);
		if (empty($f_brand) && taxonomy_exists('product_brand')) {
			$f_brand_terms = wp_get_post_terms($edit_product_id, 'product_brand');
			if (!empty($f_brand_terms) && !is_wp_error($f_brand_terms)) {
				$f_brand = $f_brand_terms[0]->name;
			}
		}
		$f_model          = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'model') ?: get_post_meta($edit_product_id, '_test_theme_model', true)) : get_post_meta($edit_product_id, '_test_theme_model', true);
		$f_warranty       = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'warranty') ?: get_post_meta($edit_product_id, '_test_theme_warranty', true)) : get_post_meta($edit_product_id, '_test_theme_warranty', true);
		$f_pkg_contents   = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'package-contents') ?: get_post_meta($edit_product_id, '_test_theme_package_contents', true)) : get_post_meta($edit_product_id, '_test_theme_package_contents', true);
		$f_country_origin = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'country_of_origin') ?: get_post_meta($edit_product_id, '_test_theme_country_of_origin', true)) : get_post_meta($edit_product_id, '_test_theme_country_of_origin', true);
		$f_approx_weight  = get_post_meta($edit_product_id, '_test_theme_weight', true);
		if ('' === $f_approx_weight && $edit_product) {
			$f_approx_weight = $edit_product->get_weight();
		}

		// Core Watch Specifications
		$f_case_material    = get_post_meta($edit_product_id, '_test_theme_case_material', true) ?: get_post_meta($edit_product_id, '_watch_case_material', true);
		$f_band_material    = get_post_meta($edit_product_id, '_test_theme_band_material', true) ?: get_post_meta($edit_product_id, '_watch_band_material', true);
		$f_watch_color      = get_post_meta($edit_product_id, '_test_theme_watch_color', true) ?: get_post_meta($edit_product_id, '_watch_color', true);
		$f_dial_color       = get_post_meta($edit_product_id, '_test_theme_dial_color', true) ?: get_post_meta($edit_product_id, '_watch_dial_color', true);
		$f_movement_type    = get_post_meta($edit_product_id, '_test_theme_movement_type', true) ?: get_post_meta($edit_product_id, '_watch_movement_type', true);
		$f_water_resistance = get_post_meta($edit_product_id, '_test_theme_water_resistance', true) ?: get_post_meta($edit_product_id, '_watch_water_resistance', true);
		$f_case_diameter    = get_post_meta($edit_product_id, '_test_theme_case_diameter', true) ?: get_post_meta($edit_product_id, '_watch_case_diameter', true);
		$f_glass_type       = get_post_meta($edit_product_id, '_test_theme_glass_type', true) ?: get_post_meta($edit_product_id, '_watch_glass_type', true);
		$f_target_audience  = get_post_meta($edit_product_id, '_test_theme_target_audience', true) ?: get_post_meta($edit_product_id, '_watch_target_audience', true);

		// 2. Category Specifications Values
		$raw_saved_cat_specs = get_post_meta($edit_product_id, '_ashrafco_category_spec_values', true);
		$f_cat_spec_values   = array();
		if (is_array($raw_saved_cat_specs) && !empty($raw_saved_cat_specs)) {
			foreach ($raw_saved_cat_specs as $k => $v) {
				if (is_array($v) && isset($v['value'])) {
					$f_cat_spec_values[$k] = $v['value'];
				} else {
					$f_cat_spec_values[$k] = (string) $v;
				}
			}
		} else {
			// Backward compatibility mapping from existing meta:
			$c_length = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'max_cut_length') ?: get_post_meta($edit_product_id, '_test_theme_max_cut_length', true)) : get_post_meta($edit_product_id, '_test_theme_max_cut_length', true);
			if (!empty($c_length)) {
				$f_cat_spec_values['max_cut_length'] = trim(preg_replace('/[^\d\.]/', '', $c_length)) ?: $c_length;
			}
			$c_thick = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'max_thickness') ?: get_post_meta($edit_product_id, '_test_theme_max_thickness', true)) : get_post_meta($edit_product_id, '_test_theme_max_thickness', true);
			if (!empty($c_thick)) {
				$f_cat_spec_values['max_thickness'] = trim(preg_replace('/[^\d\.]/', '', $c_thick)) ?: $c_thick;
			}
			$c_parts = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'spare_parts') ?: get_post_meta($edit_product_id, '_test_theme_spare_parts', true)) : get_post_meta($edit_product_id, '_test_theme_spare_parts', true);
			if (!empty($c_parts)) {
				$f_cat_spec_values['spare_parts'] = $c_parts;
			}
			$c_tool = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($edit_product_id, 'tool_type') ?: get_post_meta($edit_product_id, '_test_theme_tool_type', true)) : get_post_meta($edit_product_id, '_test_theme_tool_type', true);
			if (!empty($c_tool)) {
				$f_cat_spec_values['tool_type'] = $c_tool;
			}
		}
	} else {
		// Newly Created Product: Safe defaults
		$f_name             = '';
		$f_sku              = '';
		$f_regular_price    = '';
		$f_sale_price       = '';
		$f_sale_expiry_type = 'none';
		$f_sale_end_date    = '';
		$f_manage_stock     = true;
		$f_stock_qty        = '';
		$f_stock_status     = 'instock';
		$f_is_featured      = false;
		$f_status           = 'publish';
		$f_is_published     = true;
		$f_desc             = '';
		$f_short_desc       = '';
		$f_categories       = (isset($_GET['cat_id']) && absint($_GET['cat_id']) > 0) ? array(absint($_GET['cat_id'])) : array();
		$f_thumb_id         = 0;
		$f_thumb_url        = '';
		$f_gallery_ids      = array();
		$f_gallery_data     = array();
		$f_file_id          = 0;
		$f_file_url         = '';
		$f_file_name        = '';
		$f_file_size        = '';

		// Basic Specs Defaults for Watches
		$f_brand            = 'Ben10';
		$f_model            = '';
		$f_warranty         = 'سنتان ضمان شامل';
		$f_pkg_contents     = 'علبة الساعة الأصلية الفاخرة + بطاقة الضمان الدولي المعتمد + دليل الاستخدام';
		$f_country_origin   = 'سويسرا';
		$f_approx_weight    = '';
		$f_case_material    = 'ستانلس ستيل 316L';
		$f_band_material    = 'جلد طبيعي فاخر';
		$f_watch_color      = 'فضي';
		$f_dial_color       = 'أسود';
		$f_movement_type    = 'أوتوماتيك ميكانيكية';
		$f_water_resistance = 'مقاومة للماء والسباحة السطحية (5ATM / 50m)';
		$f_case_diameter    = '42';
		$f_glass_type       = 'زجاج ياقوتي مقاوم للخدش (سافاير Sapphire)';
		$f_target_audience  = 'رجالي';
		$f_cat_spec_values  = array();
	}

	// Retrieve All Categories & Brands for Portal Form
	$all_product_cats = get_terms(array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	));
	if (is_wp_error($all_product_cats)) {
		$all_product_cats = array();
	}

	$all_portal_categories_specs = array();
	foreach ($all_product_cats as $c_term) {
		$all_portal_categories_specs[$c_term->term_id] = array(
			'id'    => $c_term->term_id,
			'slug'  => $c_term->slug,
			'name'  => $c_term->name,
			'specs' => function_exists('ashrafco_get_category_specs') ? ashrafco_get_category_specs($c_term->term_id) : array(),
		);
	}

	$all_portal_brands = function_exists('ashrafco_get_brands') ? ashrafco_get_brands() : array();
	$portal_ajax_nonce = wp_create_nonce('ashrafco_portal_ajax_nonce');

	$categories_summary = function_exists('ashrafco_get_categories_summary') ? ashrafco_get_categories_summary() : array(
		'total_products'   => 0,
		'total_categories' => 0,
		'subcategories'    => 0,
		'empty_categories' => 0,
	);

	$cat_search = isset($_GET['cat_search']) ? sanitize_text_field(wp_unslash($_GET['cat_search'])) : '';
	$cat_filter = isset($_GET['cat_filter']) ? sanitize_key($_GET['cat_filter']) : 'all';
	$cat_sort   = isset($_GET['cat_sort']) ? sanitize_key($_GET['cat_sort']) : 'default';

	$portal_category_cards = function_exists('ashrafco_get_portal_categories') ? ashrafco_get_portal_categories(array(
		'search' => $cat_search,
		'filter' => $cat_filter,
		'sort'   => $cat_sort,
	)) : array();

	// Dedicated Category Products View Data
	$selected_category      = $cat_id_param ? get_term($cat_id_param, 'product_cat') : null;
	$category_products_data = array(
		'products'       => array(),
		'total'          => 0,
		'pages'          => 1,
		'current_page'   => 1,
		'in_stock'       => 0,
		'out_of_stock'   => 0,
		'total_category' => 0,
	);

	$cat_prod_search   = isset($_GET['prod_search']) ? sanitize_text_field(wp_unslash($_GET['prod_search'])) : '';
	$cat_prod_stock    = isset($_GET['prod_stock']) ? sanitize_key($_GET['prod_stock']) : 'all';
	$cat_prod_publish  = isset($_GET['prod_publish']) ? sanitize_key($_GET['prod_publish']) : 'all';
	$cat_prod_sort     = isset($_GET['prod_sort']) ? sanitize_key($_GET['prod_sort']) : 'date_desc';
	$cat_prod_per_page = isset($_GET['per_page']) ? min(100, max(5, absint($_GET['per_page']))) : 10;
	$cat_prod_paged    = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;

	if ($selected_category && !is_wp_error($selected_category)) {
		$category_products_data = function_exists('ashrafco_get_category_products') ? ashrafco_get_category_products($cat_id_param, array(
			'search'         => $cat_prod_search,
			'stock_status'   => $cat_prod_stock,
			'publish_status' => $cat_prod_publish,
			'sort'           => $cat_prod_sort,
			'paged'          => $cat_prod_paged,
			'per_page'       => $cat_prod_per_page,
		)) : $category_products_data;
	}

	// All product categories for category picker modals
	$all_product_categories = get_terms(array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	));
	if (is_wp_error($all_product_categories)) {
		$all_product_categories = array();
	}

	return array(
		'action'                      => $action,
		'is_new_mode'                 => $is_new_mode,
		'edit_product_id'             => $edit_product_id,
		'edit_product'                => $edit_product,
		'is_edit_mode'                => $is_edit_mode,
		'is_form_mode'                => $is_form_mode,
		'all_portal_categories_specs' => $all_portal_categories_specs,
		'all_portal_brands'           => $all_portal_brands,
		'portal_ajax_nonce'           => $portal_ajax_nonce,
		'f_name'                      => $f_name,
		'f_sku'                       => $f_sku,
		'f_regular_price'             => $f_regular_price,
		'f_sale_price'                => $f_sale_price,
		'f_sale_expiry_type'          => $f_sale_expiry_type,
		'f_sale_end_date'             => $f_sale_end_date,
		'f_manage_stock'              => $f_manage_stock,
		'f_stock_qty'                 => $f_stock_qty,
		'f_stock_status'              => $f_stock_status,
		'f_is_featured'               => $f_is_featured,
		'f_status'                    => $f_status,
		'f_is_published'              => $f_is_published,
		'f_desc'                      => $f_desc,
		'f_short_desc'                => $f_short_desc,
		'f_categories'                => $f_categories,
		'f_thumb_id'                  => $f_thumb_id,
		'f_thumb_url'                 => $f_thumb_url,
		'f_gallery_ids'               => $f_gallery_ids,
		'f_gallery_data'              => $f_gallery_data,
		'f_file_id'                   => $f_file_id,
		'f_file_url'                  => $f_file_url,
		'f_file_name'                 => $f_file_name,
		'f_file_size'                 => $f_file_size,
		'f_brand'                     => $f_brand,
		'f_model'                     => $f_model,
		'f_warranty'                  => $f_warranty,
		'f_pkg_contents'              => $f_pkg_contents,
		'f_country_origin'            => $f_country_origin,
		'f_approx_weight'             => $f_approx_weight,
		'f_case_material'             => $f_case_material,
		'f_band_material'             => $f_band_material,
		'f_watch_color'               => $f_watch_color,
		'f_dial_color'                => $f_dial_color,
		'f_movement_type'             => $f_movement_type,
		'f_water_resistance'          => $f_water_resistance,
		'f_case_diameter'             => $f_case_diameter,
		'f_glass_type'                => $f_glass_type,
		'f_target_audience'           => $f_target_audience,
		'f_cat_spec_values'           => $f_cat_spec_values,
		'cat_id_param'                => $cat_id_param,
		'categories_summary'          => $categories_summary,
		'cat_search'                  => $cat_search,
		'cat_filter'                  => $cat_filter,
		'cat_sort'                    => $cat_sort,
		'portal_category_cards'       => $portal_category_cards,
		'selected_category'           => $selected_category,
		'category_products_data'      => $category_products_data,
		'cat_prod_search'             => $cat_prod_search,
		'cat_prod_stock'              => $cat_prod_stock,
		'cat_prod_publish'            => $cat_prod_publish,
		'cat_prod_sort'               => $cat_prod_sort,
		'cat_prod_per_page'           => $cat_prod_per_page,
		'cat_prod_paged'              => $cat_prod_paged,
		'all_product_categories'      => $all_product_categories,
		'all_product_cats'            => $all_product_cats,
	);
}
