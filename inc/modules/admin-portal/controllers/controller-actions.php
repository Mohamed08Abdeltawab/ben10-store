<?php
/**
 * Admin Portal Action Routing & Form Handlers
 *
 * Handles:
 * - Product deletion
 * - Product form submission (Create / Update / Gallery / Attachments / Specs)
 * - Quick order status updates
 * - Flash notices
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Handle Product Deletion
 */
function ashrafco_portal_handle_product_delete() {
$action = isset($_GET['action']) ? sanitize_key($_GET['action']) : '';
$active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'overview';
$portal_store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$portal_logo_url = function_exists('ashrafco_get_store_logo_url') ? ashrafco_get_store_logo_url() : (get_stylesheet_directory_uri() . '/assets/images/logo.png');

// Handle Product Delete Action
if ('delete-product' === $action && isset($_GET['id'])) {
	$del_id = absint($_GET['id']);
	$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

	if (!wp_verify_nonce($nonce, 'ashrafco_delete_product_' . $del_id)) {
		wp_die(esc_html__('فشل التحقق الأمني. غير مسموح بتنفيذ عملية الحذف.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	// Verify post type is specifically a WooCommerce product
	if ('product' !== get_post_type($del_id)) {
		wp_die(esc_html__('المعرف المحدد ليس منتجاً صالحاً.', 'ashrafco'), '400 Bad Request', array('response' => 400));
	}

	// Check deletion capability against the specific product and operation
	if (!current_user_can('delete_post', $del_id) && !current_user_can('delete_product', $del_id)) {
		wp_die(esc_html__('غير مصرح لك بحذف هذا المنتج.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	if ($del_id > 0) {
		wp_trash_post($del_id);
		wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'product_deleted'), home_url('/admin-portal/')));
		exit;
	}
}
}

/**
 * Handle Product Form Submission (Create / Update)
 */
function ashrafco_portal_handle_product_save() {
if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['ashrafco_product_action'])) {
	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'ashrafco_product_form_nonce')) {
		wp_die(esc_html__('فشل التحقق الأمني (Invalid Security Nonce). يرجى المحاولة ثانية.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	$prod_action = sanitize_key($_POST['ashrafco_product_action']); // 'new' or 'edit'
	$prod_name = isset($_POST['product_name']) ? sanitize_text_field(wp_unslash($_POST['product_name'])) : '';

	if (empty($prod_name)) {
		wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'product_error'), home_url('/admin-portal/')));
		exit;
	}

	$sku = isset($_POST['product_sku']) ? sanitize_text_field(wp_unslash($_POST['product_sku'])) : '';
	$regular_price = isset($_POST['regular_price']) ? wc_format_decimal(wp_unslash($_POST['regular_price'])) : '';
	$sale_price = (isset($_POST['sale_price']) && '' !== trim($_POST['sale_price'])) ? wc_format_decimal(wp_unslash($_POST['sale_price'])) : '';

	// Sale End Date Scheduling Behavior
	$sale_expiry_type = isset($_POST['sale_expiry_type']) ? sanitize_key($_POST['sale_expiry_type']) : 'none';
	$sale_end_date = isset($_POST['sale_end_date']) ? sanitize_text_field(wp_unslash($_POST['sale_end_date'])) : '';

	$manage_stock = isset($_POST['manage_stock']) && '1' === $_POST['manage_stock'];
	$stock_qty = (isset($_POST['stock_quantity']) && '' !== trim($_POST['stock_quantity'])) ? intval($_POST['stock_quantity']) : null;
	$stock_status = isset($_POST['stock_status']) ? sanitize_key($_POST['stock_status']) : 'instock';

	// WooCommerce only supports instock/outofstock/onbackorder. The portal's
	// "lowstock" pill is a display convenience derived from the stock quantity.
	if ('lowstock' === $stock_status) {
		$stock_status = 'instock';
	}
	if (!in_array($stock_status, array('instock', 'outofstock', 'onbackorder'), true)) {
		$stock_status = 'instock';
	}
	$desc = isset($_POST['product_description']) ? wp_kses_post(wp_unslash($_POST['product_description'])) : '';
	$short_desc = isset($_POST['product_short_description']) ? wp_kses_post(wp_unslash($_POST['product_short_description'])) : '';
	$selected_cats = isset($_POST['product_categories']) ? array_map('absint', (array) $_POST['product_categories']) : array();

	// Featured Product Setting (Default OFF / disabled)
	$is_featured = isset($_POST['is_featured']) && '1' === (string) $_POST['is_featured'];

	// Product Publishing Status Setting
	$is_published = isset($_POST['is_published']) && '1' === (string) $_POST['is_published'];
	$original_status = isset($_POST['original_product_status']) ? sanitize_key($_POST['original_product_status']) : '';

	// Access control check for editing/publishing products
	if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_die(esc_html__('غير مصرح لك بنشر أو تعديل المنتجات.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	// Validation if product is set to publish: regular price and category are required
	if ($is_published && (empty($regular_price) || empty($selected_cats))) {
		wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'product_publish_error'), home_url('/admin-portal/')));
		exit;
	}

	if ('new' === $prod_action) {
		if (!current_user_can('publish_products') && !current_user_can('edit_products') && !current_user_can('manage_woocommerce')) {
			wp_die(esc_html__('غير مصرح لك بإنشاء منتجات جديدة.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}
		$wc_prod = new WC_Product_Simple();
	} else {
		$target_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
		if ('product' !== get_post_type($target_id)) {
			wp_die(esc_html__('المعرف المحدد ليس منتجاً صالحاً للتعديل.', 'ashrafco'), '400 Bad Request', array('response' => 400));
		}
		if (!current_user_can('edit_post', $target_id) && !current_user_can('edit_product', $target_id)) {
			wp_die(esc_html__('غير مصرح لك بتعديل هذا المنتج.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}
		$wc_prod = wc_get_product($target_id);
		if (!$wc_prod) {
			wp_die(esc_html__('المنتج المطلوب تعديله غير موجود.', 'ashrafco'), '404 Not Found', array('response' => 404));
		}
	}

	// Publication status determination:
	if ($is_published) {
		$target_status = 'publish';
	} else {
		// If toggle is disabled: save as draft (or preserve existing non-publish status like pending/private)
		if (in_array($original_status, array('pending', 'private', 'future'), true)) {
			$target_status = $original_status;
		} else {
			$target_status = 'draft';
		}
	}

	$wc_prod->set_name($prod_name);
	$wc_prod->set_status($target_status);
	$wc_prod->set_description($desc);
	$wc_prod->set_short_description($short_desc);
	$wc_prod->set_regular_price($regular_price);
	$wc_prod->set_sale_price($sale_price);

	// Handle Sale Scheduling strictly based on user selection
	if ('scheduled' === $sale_expiry_type && !empty($sale_end_date)) {
		$timestamp = strtotime($sale_end_date . ' 23:59:59');
		if ($timestamp) {
			$wc_prod->set_date_on_sale_to($timestamp);
		} else {
			$wc_prod->set_date_on_sale_to('');
		}
	} else {
		// «غير محدد»: clear sale end date completely, do not schedule automatic expiration
		$wc_prod->set_date_on_sale_to('');
	}

	if (!empty($sku)) {
		try {
			$wc_prod->set_sku($sku);
		} catch (Exception $e) {
			// Ignore SKU collisions to avoid crash
		}
	}

	$wc_prod->set_manage_stock($manage_stock);
	if ($manage_stock && null !== $stock_qty) {
		$wc_prod->set_stock_quantity($stock_qty);
	}
	$wc_prod->set_stock_status($stock_status);
	$wc_prod->set_category_ids($selected_cats);

	// Enforce Featured status strictly (disabled by default)
	$wc_prod->set_featured($is_featured);

	$saved_id = $wc_prod->save();

	if ($saved_id) {
		// 1. Handle Product Images and Gallery (Strict 15 Images Limit Total)
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$main_image_id = 0;
		if (!empty($_FILES['product_image']['name'])) {
			$attach_id = media_handle_upload('product_image', $saved_id);
			if (!is_wp_error($attach_id)) {
				$main_image_id = $attach_id;
			}
		} elseif (isset($_POST['media_image_id']) && absint($_POST['media_image_id']) > 0) {
			$main_image_id = absint($_POST['media_image_id']);
		}

		if (isset($_POST['remove_image']) && '1' === $_POST['remove_image']) {
			$main_image_id = 0;
			delete_post_thumbnail($saved_id);
		} elseif ($main_image_id > 0) {
			set_post_thumbnail($saved_id, $main_image_id);
		}

		// Gallery Images
		$gallery_ids = array();
		if (!empty($_POST['gallery_image_ids'])) {
			$raw_gallery = is_array($_POST['gallery_image_ids']) ? $_POST['gallery_image_ids'] : explode(',', sanitize_text_field(wp_unslash($_POST['gallery_image_ids'])));
			foreach ($raw_gallery as $gid) {
				$gid = absint(trim($gid));
				if ($gid > 0 && $gid !== $main_image_id && !in_array($gid, $gallery_ids, true)) {
					$gallery_ids[] = $gid;
				}
			}
		}

		// Additional uploaded files in gallery
		if (!empty($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
			$files = $_FILES['gallery_images'];
			foreach ($files['name'] as $key => $value) {
				if ($files['name'][$key]) {
					$file = array(
						'name' => $files['name'][$key],
						'type' => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error' => $files['error'][$key],
						'size' => $files['size'][$key],
					);
					$_FILES['gallery_single_upload'] = $file;
					$attach_id = media_handle_upload('gallery_single_upload', $saved_id);
					if (!is_wp_error($attach_id) && !in_array($attach_id, $gallery_ids, true)) {
						$gallery_ids[] = $attach_id;
					}
				}
			}
		}

		// Enforce server-side 15 total images limit (1 main + 14 gallery max)
		$max_total_images = 15;
		$max_gallery_allowed = $main_image_id > 0 ? ($max_total_images - 1) : $max_total_images;
		if (count($gallery_ids) > $max_gallery_allowed) {
			$gallery_ids = array_slice($gallery_ids, 0, $max_gallery_allowed);
		}

		$wc_prod->set_gallery_image_ids($gallery_ids);
		$wc_prod->save();

		// 2. Optional Product File Upload (PDF, ZIP, DOC, DOCX up to 20MB)
		if (!empty($_FILES['product_file']['name'])) {
			$file_ext = strtolower(pathinfo($_FILES['product_file']['name'], PATHINFO_EXTENSION));
			$allowed_exts = array('pdf', 'zip', 'doc', 'docx');
			$file_size = $_FILES['product_file']['size'];

			if (in_array($file_ext, $allowed_exts, true) && $file_size <= 20 * 1024 * 1024) {
				$file_attach_id = media_handle_upload('product_file', $saved_id);
				if (!is_wp_error($file_attach_id)) {
					update_post_meta($saved_id, '_test_theme_product_file_id', $file_attach_id);
					update_post_meta($saved_id, '_test_theme_product_file_url', wp_get_attachment_url($file_attach_id));
					update_post_meta($saved_id, '_test_theme_product_file_name', sanitize_text_field($_FILES['product_file']['name']));
					update_post_meta($saved_id, '_test_theme_product_file_size', size_format($file_size));
				}
			}
		} elseif (isset($_POST['media_file_id']) && absint($_POST['media_file_id']) > 0) {
			$file_id = absint($_POST['media_file_id']);
			update_post_meta($saved_id, '_test_theme_product_file_id', $file_id);
			update_post_meta($saved_id, '_test_theme_product_file_url', wp_get_attachment_url($file_id));
			update_post_meta($saved_id, '_test_theme_product_file_name', get_the_title($file_id));
		} elseif (isset($_POST['remove_product_file']) && '1' === $_POST['remove_product_file']) {
			delete_post_meta($saved_id, '_test_theme_product_file_id');
			delete_post_meta($saved_id, '_test_theme_product_file_url');
			delete_post_meta($saved_id, '_test_theme_product_file_name');
			delete_post_meta($saved_id, '_test_theme_product_file_size');
		}

		// 3. Basic Specifications & Category Specifications Saving
		// A. Basic Specifications
		$brand_val = isset($_POST['product_brand']) ? sanitize_text_field(wp_unslash($_POST['product_brand'])) : '';
		if ('custom' === $brand_val || empty($brand_val)) {
			if (!empty($_POST['product_brand_custom'])) {
				$brand_val = sanitize_text_field(wp_unslash($_POST['product_brand_custom']));
			}
		}
		if (!empty($brand_val)) {
			update_post_meta($saved_id, '_test_theme_brand', $brand_val);
			update_post_meta($saved_id, '_ashrafco_brand', $brand_val);
			if (taxonomy_exists('product_brand')) {
				$b_term = term_exists($brand_val, 'product_brand');
				if (!$b_term) {
					$b_term = wp_insert_term($brand_val, 'product_brand');
				}
				if (!is_wp_error($b_term) && isset($b_term['term_id'])) {
					wp_set_object_terms($saved_id, (int) $b_term['term_id'], 'product_brand', false);
				}
			}
		} else {
			delete_post_meta($saved_id, '_test_theme_brand');
			delete_post_meta($saved_id, '_ashrafco_brand');
		}

		$model_val = isset($_POST['product_manufacturer_model']) ? sanitize_text_field(wp_unslash($_POST['product_manufacturer_model'])) : '';
		update_post_meta($saved_id, '_test_theme_model', $model_val);
		update_post_meta($saved_id, '_ashrafco_model', $model_val);

		$warranty_val = isset($_POST['product_warranty']) ? sanitize_text_field(wp_unslash($_POST['product_warranty'])) : '';
		if ('ضمان مخصص' === $warranty_val && !empty($_POST['product_warranty_custom'])) {
			$warranty_val = sanitize_text_field(wp_unslash($_POST['product_warranty_custom']));
		}
		update_post_meta($saved_id, '_test_theme_warranty', $warranty_val);

		$pkg_contents_val = isset($_POST['product_package_contents']) ? sanitize_textarea_field(wp_unslash($_POST['product_package_contents'])) : '';
		update_post_meta($saved_id, '_test_theme_package_contents', $pkg_contents_val);

		$origin_val = isset($_POST['product_country_origin']) ? sanitize_text_field(wp_unslash($_POST['product_country_origin'])) : '';
		if ('أخرى' === $origin_val && !empty($_POST['product_country_origin_custom'])) {
			$origin_val = sanitize_text_field(wp_unslash($_POST['product_country_origin_custom']));
		}
		update_post_meta($saved_id, '_test_theme_country_of_origin', $origin_val);

		$weight_val = isset($_POST['product_approx_weight']) ? sanitize_text_field(wp_unslash($_POST['product_approx_weight'])) : '';
		if ('' !== $weight_val) {
			update_post_meta($saved_id, '_test_theme_weight', $weight_val);
			$product_obj = wc_get_product($saved_id);
			if ($product_obj) {
				$product_obj->set_weight($weight_val);
				$product_obj->save();
			}
		} else {
			delete_post_meta($saved_id, '_test_theme_weight');
		}

		// Core Watch Specifications
		$case_mat_val = isset($_POST['product_case_material']) ? sanitize_text_field(wp_unslash($_POST['product_case_material'])) : '';
		if ('custom' === $case_mat_val && !empty($_POST['product_case_material_custom'])) {
			$case_mat_val = sanitize_text_field(wp_unslash($_POST['product_case_material_custom']));
		}
		if (!empty($case_mat_val)) {
			update_post_meta($saved_id, '_test_theme_case_material', $case_mat_val);
			update_post_meta($saved_id, '_watch_case_material', $case_mat_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_case_material');
			delete_post_meta($saved_id, '_watch_case_material');
		}

		$band_mat_val = isset($_POST['product_band_material']) ? sanitize_text_field(wp_unslash($_POST['product_band_material'])) : '';
		if ('custom' === $band_mat_val && !empty($_POST['product_band_material_custom'])) {
			$band_mat_val = sanitize_text_field(wp_unslash($_POST['product_band_material_custom']));
		}
		if (!empty($band_mat_val)) {
			update_post_meta($saved_id, '_test_theme_band_material', $band_mat_val);
			update_post_meta($saved_id, '_watch_band_material', $band_mat_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_band_material');
			delete_post_meta($saved_id, '_watch_band_material');
		}

		$watch_col_val = isset($_POST['product_watch_color']) ? sanitize_text_field(wp_unslash($_POST['product_watch_color'])) : '';
		if ('custom' === $watch_col_val && !empty($_POST['product_watch_color_custom'])) {
			$watch_col_val = sanitize_text_field(wp_unslash($_POST['product_watch_color_custom']));
		}
		if (!empty($watch_col_val)) {
			update_post_meta($saved_id, '_test_theme_watch_color', $watch_col_val);
			update_post_meta($saved_id, '_watch_color', $watch_col_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_watch_color');
			delete_post_meta($saved_id, '_watch_color');
		}

		$dial_col_val = isset($_POST['product_dial_color']) ? sanitize_text_field(wp_unslash($_POST['product_dial_color'])) : '';
		if ('custom' === $dial_col_val && !empty($_POST['product_dial_color_custom'])) {
			$dial_col_val = sanitize_text_field(wp_unslash($_POST['product_dial_color_custom']));
		}
		if (!empty($dial_col_val)) {
			update_post_meta($saved_id, '_test_theme_dial_color', $dial_col_val);
			update_post_meta($saved_id, '_watch_dial_color', $dial_col_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_dial_color');
			delete_post_meta($saved_id, '_watch_dial_color');
		}

		$movement_val = isset($_POST['product_movement_type']) ? sanitize_text_field(wp_unslash($_POST['product_movement_type'])) : '';
		if ('custom' === $movement_val && !empty($_POST['product_movement_type_custom'])) {
			$movement_val = sanitize_text_field(wp_unslash($_POST['product_movement_type_custom']));
		}
		if (!empty($movement_val)) {
			update_post_meta($saved_id, '_test_theme_movement_type', $movement_val);
			update_post_meta($saved_id, '_watch_movement_type', $movement_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_movement_type');
			delete_post_meta($saved_id, '_watch_movement_type');
		}

		$water_res_val = isset($_POST['product_water_resistance']) ? sanitize_text_field(wp_unslash($_POST['product_water_resistance'])) : '';
		if ('custom' === $water_res_val && !empty($_POST['product_water_resistance_custom'])) {
			$water_res_val = sanitize_text_field(wp_unslash($_POST['product_water_resistance_custom']));
		}
		if (!empty($water_res_val)) {
			update_post_meta($saved_id, '_test_theme_water_resistance', $water_res_val);
			update_post_meta($saved_id, '_watch_water_resistance', $water_res_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_water_resistance');
			delete_post_meta($saved_id, '_watch_water_resistance');
		}

		$case_diam_val = isset($_POST['product_case_diameter']) ? sanitize_text_field(wp_unslash($_POST['product_case_diameter'])) : '';
		if (!empty($case_diam_val)) {
			update_post_meta($saved_id, '_test_theme_case_diameter', $case_diam_val);
			update_post_meta($saved_id, '_watch_case_diameter', $case_diam_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_case_diameter');
			delete_post_meta($saved_id, '_watch_case_diameter');
		}

		$glass_val = isset($_POST['product_glass_type']) ? sanitize_text_field(wp_unslash($_POST['product_glass_type'])) : '';
		if ('custom' === $glass_val && !empty($_POST['product_glass_type_custom'])) {
			$glass_val = sanitize_text_field(wp_unslash($_POST['product_glass_type_custom']));
		}
		if (!empty($glass_val)) {
			update_post_meta($saved_id, '_test_theme_glass_type', $glass_val);
			update_post_meta($saved_id, '_watch_glass_type', $glass_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_glass_type');
			delete_post_meta($saved_id, '_watch_glass_type');
		}

		$target_aud_val = isset($_POST['product_target_audience']) ? sanitize_text_field(wp_unslash($_POST['product_target_audience'])) : '';
		if (!empty($target_aud_val)) {
			update_post_meta($saved_id, '_test_theme_target_audience', $target_aud_val);
			update_post_meta($saved_id, '_watch_target_audience', $target_aud_val);
		} else {
			delete_post_meta($saved_id, '_test_theme_target_audience');
			delete_post_meta($saved_id, '_watch_target_audience');
		}

		// B. Category Specifications (Dynamic Key-Value Pairs from Active Category)
		$raw_cat_values = isset($_POST['cat_spec_values']) ? (array) $_POST['cat_spec_values'] : array();
		$raw_cat_meta = isset($_POST['cat_spec_meta']) ? (array) $_POST['cat_spec_meta'] : array();

		$saved_cat_specs = array();
		$specs_table_for_theme = array();

		$mapped_cut_length = '';
		$mapped_thickness = '';
		$mapped_spare_parts = '';
		$mapped_tool_type = '';

		foreach ($raw_cat_values as $s_id => $s_val) {
			$s_val = sanitize_text_field(wp_unslash($s_val));
			$s_name = isset($raw_cat_meta[$s_id]['name']) ? sanitize_text_field(wp_unslash($raw_cat_meta[$s_id]['name'])) : $s_id;
			$s_unit = isset($raw_cat_meta[$s_id]['unit']) ? sanitize_text_field(wp_unslash($raw_cat_meta[$s_id]['unit'])) : '';

			if ('' !== $s_val) {
				$saved_cat_specs[$s_id] = array(
					'id' => $s_id,
					'name' => $s_name,
					'value' => $s_val,
					'unit' => $s_unit,
				);

				$display_val = !empty($s_unit) ? ($s_val . ' ' . $s_unit) : $s_val;
				$specs_table_for_theme[] = array(
					'name' => $s_name,
					'value' => $display_val,
				);

				// Intelligent backward compatibility mapping
				if ('max_cut_length' === $s_id || mb_stripos($s_name, 'طول') !== false || mb_stripos($s_name, 'المقاس') !== false) {
					$mapped_cut_length = $display_val;
				} elseif ('max_thickness' === $s_id || mb_stripos($s_name, 'سمك') !== false) {
					$mapped_thickness = $display_val;
				} elseif ('spare_parts' === $s_id || mb_stripos($s_name, 'غيار') !== false) {
					$mapped_spare_parts = $display_val;
				} elseif ('tool_type' === $s_id || mb_stripos($s_name, 'نوع الأداة') !== false) {
					$mapped_tool_type = $display_val;
				}
			}
		}

		update_post_meta($saved_id, '_ashrafco_category_spec_values', $saved_cat_specs);

		// Append core watch specifications to theme specs table
		$watch_specs_map = array(
			'مادة الهيكل'     => $case_mat_val,
			'مادة السوار'     => $band_mat_val,
			'نوع الحركة'      => $movement_val,
			'مقاومة الماء'     => $water_res_val,
			'قطر الهيكل'      => !empty($case_diam_val) ? ($case_diam_val . ' مم') : '',
			'نوع الزجاج'      => $glass_val,
			'لون الساعة'      => $watch_col_val,
			'لون الميناء'     => $dial_col_val,
			'الفئة المستهدفة' => $target_aud_val,
			'بلد المنشأ'      => $origin_val,
			'فترة الضمان'     => $warranty_val,
		);
		foreach ($watch_specs_map as $ws_name => $ws_val) {
			if (!empty($ws_val)) {
				$specs_table_for_theme[] = array(
					'name'  => $ws_name,
					'value' => $ws_val,
				);
			}
		}

		update_post_meta($saved_id, '_test_theme_specs_table', $specs_table_for_theme);

		if (!empty($mapped_cut_length)) {
			update_post_meta($saved_id, '_test_theme_max_cut_length', $mapped_cut_length);
		}
		if (!empty($mapped_thickness)) {
			update_post_meta($saved_id, '_test_theme_max_thickness', $mapped_thickness);
		}
		if (!empty($mapped_spare_parts)) {
			update_post_meta($saved_id, '_test_theme_spare_parts', $mapped_spare_parts);
		}
		if (!empty($mapped_tool_type)) {
			update_post_meta($saved_id, '_test_theme_tool_type', $mapped_tool_type);
		}

		// Synchronize standard WooCommerce product attributes (TASK-ARCH-04)
		if (function_exists('ashrafco_attribute_migrator') && ashrafco_attribute_migrator()) {
			ashrafco_attribute_migrator()->migrate_product($saved_id, false);
		}

		// Navigation redirect to relevant category products view
		if ('new' === $prod_action) {
			$redirect_notice = $is_published ? 'product_created' : 'product_draft_created';
		} else {
			$redirect_notice = ('publish' === $original_status && !$is_published) ? 'product_unpublished' : 'product_updated';
		}
		$redirect_cat_id = !empty($selected_cats) ? reset($selected_cats) : (isset($_POST['redirect_cat_id']) ? absint($_POST['redirect_cat_id']) : 0);

		if ($redirect_cat_id > 0) {
			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'cat_id' => $redirect_cat_id, 'portal_notice' => $redirect_notice), home_url('/admin-portal/')));
		} else {
			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => $redirect_notice), home_url('/admin-portal/')));
		}
		exit;
	} else {
		wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'product_error'), home_url('/admin-portal/')));
		exit;
	}
}

}

/**
 * Handle Quick Order Status Update
 *
 * @return string Feedback notice string if updated
 */
function ashrafco_portal_handle_order_quick_status() {
	$status_update_notice = '';
	$current_user         = wp_get_current_user();
	if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['ashrafco_portal_action']) && 'update_status' === $_POST['ashrafco_portal_action']) {
	if (isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'ashrafco_portal_order_nonce')) {
		if (!current_user_can('edit_shop_orders') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
			wp_die(esc_html__('غير مصرح لك بتحديث حالة الطلب.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}

		$order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
		$new_status = isset($_POST['new_status']) ? sanitize_key($_POST['new_status']) : '';

		if ($order_id > 0 && !empty($new_status)) {
			$order = wc_get_order($order_id);
			if ($order) {
				$valid_statuses = array_keys(wc_get_order_statuses());
				$target_status = (0 === strpos($new_status, 'wc-')) ? substr($new_status, 3) : $new_status;

				if (in_array('wc-' . $target_status, $valid_statuses, true)) {
					$order->update_status($target_status, sprintf(__('تم تحديث الحالة من بوابة الإدارة بواسطة %s.', 'ashrafco'), $current_user->display_name));
					$status_update_notice = sprintf(__('تم تحديث حالة الطلب #%d إلى "%s" بنجاح.', 'ashrafco'), $order_id, wc_get_order_status_name($target_status));
				}
			}
		}
	}
}
	return $status_update_notice;
}

/**
 * Resolve Flash Notification Message and Type
 *
 * @return array Array with 'notice' and 'type' keys
 */
function ashrafco_portal_get_flash_notice() {
	$portal_notice      = '';
	$portal_notice_type = 'success';
	$portal_store_name  = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
	if (isset($_GET['portal_notice'])) {
	$notice_key = sanitize_key($_GET['portal_notice']);
	switch ($notice_key) {
		case 'product_created':
			$portal_notice = sprintf(__('تمت إضافة ونشر المنتج الجديد بنجاح في متجر %s!', 'ashrafco'), $portal_store_name);
			break;
		case 'product_draft_created':
			$portal_notice = __('تم حفظ المنتج الجديد كمسودة بنجاح ولن يظهر في المتجر حتى يتم نشره.', 'ashrafco');
			break;
		case 'product_updated':
			$portal_notice = __('تم حفظ وتحديث بيانات ومواصفات وأسعار المنتج بنجاح!', 'ashrafco');
			break;
		case 'product_unpublished':
			$portal_notice = __('تم حفظ التعديلات وإلغاء نشر المنتج (تم تحويله إلى مسودة بنجاح).', 'ashrafco');
			break;
		case 'product_publish_error':
			$portal_notice = __('تعذر نشر المنتج. يرجى التأكد من ملء السعر والتصنيف الرئيسي قبل نشر المنتج في المتجر.', 'ashrafco');
			$portal_notice_type = 'error';
			break;
		case 'product_deleted':
			$portal_notice = __('تم نقل المنتج بنجاح إلى سلة المهملات.', 'ashrafco');
			break;
		case 'product_error':
			$portal_notice = __('تعذر حفظ المنتج. يرجى التأكد من ملء الحقول المطلوبة.', 'ashrafco');
			$portal_notice_type = 'error';
			break;
		case 'cat_created':
			$portal_notice = sprintf(__('تمت إضافة التصنيف الجديد بنجاح في متجر %s!', 'ashrafco'), $portal_store_name);
			break;
		case 'cat_updated':
			$portal_notice = __('تم تحديث بيانات ومواصفات التصنيف بنجاح!', 'ashrafco');
			break;
		case 'cat_deleted':
			$portal_notice = __('تم حذف التصنيف بنجاح.', 'ashrafco');
			break;
		case 'cat_empty_name':
			$portal_notice = __('يرجى إدخال اسم التصنيف أولاً.', 'ashrafco');
			$portal_notice_type = 'error';
			break;
		case 'cat_error':
			$portal_notice = __('تعذر حفظ التصنيف، يرجى مراجعة البيانات والمحاولة ثانية.', 'ashrafco');
			$portal_notice_type = 'error';
			break;
		case 'cat_delete_error':
			$portal_notice = __('تعذر حذف التصنيف. يرجى التأكد من صلاحيات النظام.', 'ashrafco');
			$portal_notice_type = 'error';
			break;
	}
}
	return array(
		'notice' => $portal_notice,
		'type'   => $portal_notice_type,
	);
}

/**
 * Main Action Processing Entry Point
 *
 * @return array Action results including status update notice and flash notices
 */
function ashrafco_portal_handle_actions() {
	// 1. Handle Product Deletion
	ashrafco_portal_handle_product_delete();

	// 2. Handle Product Form Submissions
	ashrafco_portal_handle_product_save();

	// 3. Handle Category Actions (if defined in portal-categories.php)
	if (function_exists('ashrafco_handle_category_actions')) {
		ashrafco_handle_category_actions();
	}

	// 4. Handle Quick Order Status Update
	$status_update_notice = ashrafco_portal_handle_order_quick_status();

	// 5. Flash Notifications
	$notice_data = ashrafco_portal_get_flash_notice();

	return array(
		'status_update_notice' => $status_update_notice,
		'portal_notice'        => $notice_data['notice'],
		'portal_notice_type'   => $notice_data['type'],
	);
}
