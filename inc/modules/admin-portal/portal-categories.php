<?php
/**
 * Ashrafco Admin Portal - Category Management Module
 *
 * Handles category queries, KPI statistics, category CRUD (add, update, delete),
 * hierarchy calculations, and category-filtered product listings for Tab 3 (إدارة التصنيفات والمنتجات).
 *
 * @package Ashrafco
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Calculate Summary Statistics for Categories
 *
 * Rules:
 * A. إجمالي المنتجات: Real count of published products in the store.
 * B. إجمالي التصنيفات: Total number of WooCommerce product_cat terms.
 * C. التصنيفات الفرعية: Categories that have a parent category ($cat->parent > 0).
 * D. تصنيفات بدون منتجات: Categories with zero products ($cat->count == 0).
 *
 * @return array
 */
function ashrafco_get_categories_summary()
{
	// A. Total published products
	$products_count_obj = wp_count_posts('product');
	$total_products = isset($products_count_obj->publish) ? (int) $products_count_obj->publish : 0;

	// B, C, D. Query all product categories
	$all_terms = get_terms(array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	));

	if (is_wp_error($all_terms) || empty($all_terms)) {
		return array(
			'total_products' => $total_products,
			'total_categories' => 0,
			'subcategories' => 0,
			'empty_categories' => 0,
		);
	}

	$total_categories = count($all_terms);
	$subcategories = 0;
	$empty_categories = 0;

	foreach ($all_terms as $term) {
		if ($term->parent > 0) {
			$subcategories++;
		}
		if (0 === (int) $term->count) {
			$empty_categories++;
		}
	}

	return array(
		'total_products' => $total_products,
		'total_categories' => $total_categories,
		'subcategories' => $subcategories,
		'empty_categories' => $empty_categories,
	);
}

/**
 * Get Category Thumbnail URL with intelligent fallback
 *
 * Priority:
 * 1. WooCommerce Category thumbnail (term_meta: thumbnail_id)
 * 2. First product with thumbnail inside this category
 * 3. Default fallback SVG placeholder
 *
 * @param int $term_id Product category term ID.
 * @return array Array with 'url' and 'id'.
 */
function ashrafco_get_category_thumbnail($term_id)
{
	$term_id = absint($term_id);
	if (!$term_id) {
		return array('url' => '', 'id' => 0);
	}

	// 1. Term Meta thumbnail_id
	$thumb_id = get_term_meta($term_id, 'thumbnail_id', true);
	if ($thumb_id) {
		$img_url = wp_get_attachment_image_url($thumb_id, 'medium');
		if ($img_url) {
			return array('url' => $img_url, 'id' => (int) $thumb_id);
		}
	}

	// 2. Fallback to first published product's thumbnail in this category
	$term = get_term($term_id, 'product_cat');
	if ($term && !is_wp_error($term)) {
		$product_ids = wc_get_products(array(
			'category' => array($term->slug),
			'limit' => 1,
			'status' => 'publish',
			'return' => 'ids',
		));

		if (!empty($product_ids)) {
			$first_prod_thumb_id = get_post_thumbnail_id($product_ids[0]);
			if ($first_prod_thumb_id) {
				$img_url = wp_get_attachment_image_url($first_prod_thumb_id, 'medium');
				if ($img_url) {
					return array('url' => $img_url, 'id' => (int) $first_prod_thumb_id);
				}
			}
		}
	}

	return array('url' => '', 'id' => 0);
}

/**
 * Query & Filter Product Categories for the Category Dashboard
 *
 * @param array $args Filter and search criteria.
 * @return array List of processed category items.
 */
function ashrafco_get_portal_categories($args = array())
{
	$search = isset($args['search']) ? trim(sanitize_text_field($args['search'])) : '';
	$filter = isset($args['filter']) ? sanitize_key($args['filter']) : 'all';
	$sort = isset($args['sort']) ? sanitize_key($args['sort']) : 'default';

	$term_args = array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	);

	if (!empty($search)) {
		$term_args['name__like'] = $search;
	}

	$all_terms = get_terms($term_args);
	if (is_wp_error($all_terms) || empty($all_terms)) {
		return array();
	}

	// Preload all category parents & children for quick lookup
	$all_terms_by_id = array();
	$children_map = array();

	// Full query to build accurate hierarchy maps
	$full_terms = get_terms(array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	));
	if (!is_wp_error($full_terms)) {
		foreach ($full_terms as $t) {
			$all_terms_by_id[$t->term_id] = $t;
			if ($t->parent > 0) {
				if (!isset($children_map[$t->parent])) {
					$children_map[$t->parent] = array();
				}
				$children_map[$t->parent][] = $t->term_id;
			}
		}
	}

	$filtered_terms = array();

	foreach ($all_terms as $term) {
		// Filter logic
		if ('main' === $filter && $term->parent > 0) {
			continue;
		}
		if ('sub' === $filter && 0 === (int) $term->parent) {
			continue;
		}
		if ('empty' === $filter && (int) $term->count > 0) {
			continue;
		}

		$thumb_data = ashrafco_get_category_thumbnail($term->term_id);
		$child_count = isset($children_map[$term->term_id]) ? count($children_map[$term->term_id]) : 0;
		$parent_name = '';
		if ($term->parent > 0 && isset($all_terms_by_id[$term->parent])) {
			$parent_name = $all_terms_by_id[$term->parent]->name;
		}

		$filtered_terms[] = array(
			'term_id' => $term->term_id,
			'name' => $term->name,
			'slug' => $term->slug,
			'description' => $term->description,
			'parent' => (int) $term->parent,
			'parent_name' => $parent_name,
			'count' => (int) $term->count,
			'child_count' => $child_count,
			'thumbnail_url' => $thumb_data['url'],
			'thumbnail_id' => $thumb_data['id'],
			'frontend_url' => get_term_link($term),
			'view_url' => add_query_arg(array('tab' => 'products', 'cat_id' => $term->term_id), home_url('/admin-portal/')),
			'edit_url' => add_query_arg(array('tab' => 'products', 'edit_cat' => $term->term_id), home_url('/admin-portal/')),
			'specs' => ashrafco_get_category_specs($term->term_id),
		);
	}

	// Sorting
	switch ($sort) {
		case 'name_asc':
			usort($filtered_terms, function ($a, $b) {
				return strcmp($a['name'], $b['name']);
			});
			break;

		case 'name_desc':
			usort($filtered_terms, function ($a, $b) {
				return strcmp($b['name'], $a['name']);
			});
			break;

		case 'count_desc':
			usort($filtered_terms, function ($a, $b) {
				if ($a['count'] === $b['count']) {
					return strcmp($a['name'], $b['name']);
				}
				return ($a['count'] < $b['count']) ? 1 : -1;
			});
			break;

		case 'count_asc':
			usort($filtered_terms, function ($a, $b) {
				if ($a['count'] === $b['count']) {
					return strcmp($a['name'], $b['name']);
				}
				return ($a['count'] > $b['count']) ? 1 : -1;
			});
			break;

		case 'newest':
			usort($filtered_terms, function ($a, $b) {
				return ($a['term_id'] < $b['term_id']) ? 1 : -1;
			});
			break;

		default:
			// Default: primary categories first, then alphabetical
			usort($filtered_terms, function ($a, $b) {
				if ((0 === $a['parent'] && 0 !== $b['parent'])) {
					return -1;
				}
				if ((0 !== $a['parent'] && 0 === $b['parent'])) {
					return 1;
				}
				return ($a['count'] < $b['count']) ? 1 : -1;
			});
			break;
	}

	return $filtered_terms;
}

/**
 * Handle Category CRUD Submissions (Create, Edit, Delete)
 */
function ashrafco_handle_category_actions()
{
	$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
	$has_action = (isset($_GET['action']) && 'delete-category' === $_GET['action']) || ('POST' === $method && isset($_POST['ashrafco_cat_action']));
	if (!$has_action) {
		return;
	}

	if (!is_user_logged_in() || (!current_user_can('manage_product_terms') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options'))) {
		wp_die(esc_html__('غير مصرح لك بإدارة التصنيفات.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	// 1. Handle Category Delete Action (GET)
	if (isset($_GET['action']) && 'delete-category' === $_GET['action'] && isset($_GET['cat_id'])) {
		$cat_id = absint($_GET['cat_id']);
		$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

		if (!wp_verify_nonce($nonce, 'ashrafco_delete_cat_' . $cat_id)) {
			wp_die(esc_html__('فشل التحقق الأمني. غير مسموح بحذف التصنيف.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}

		if (!current_user_can('delete_product_terms') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
			wp_die(esc_html__('غير مصرح لك بحذف هذا التصنيف.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}

		if ($cat_id > 0) {
			$result = wp_delete_term($cat_id, 'product_cat');
			if (is_wp_error($result)) {
				wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_delete_error'), home_url('/admin-portal/')));
				exit;
			}
			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_deleted'), home_url('/admin-portal/')));
			exit;
		}
	}

	// 2. Handle Category Form Submission (POST: create or edit)
	if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['ashrafco_cat_action'])) {
		if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'ashrafco_category_form_nonce')) {
			wp_die(esc_html__('فشل التحقق الأمني (Invalid Security Nonce). يرجى المحاولة ثانية.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}

		if (!current_user_can('edit_product_terms') && !current_user_can('manage_product_terms') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
			wp_die(esc_html__('غير مصرح لك بإدارة التصنيفات.', 'ashrafco'), '403 Forbidden', array('response' => 403));
		}

		$cat_action = sanitize_key($_POST['ashrafco_cat_action']); // 'create' or 'edit'
		$cat_name = isset($_POST['cat_name']) ? sanitize_text_field(wp_unslash($_POST['cat_name'])) : '';
		$cat_slug = isset($_POST['cat_slug']) ? sanitize_title(wp_unslash($_POST['cat_slug'])) : '';
		$cat_parent = isset($_POST['cat_parent']) ? absint($_POST['cat_parent']) : 0;
		$cat_desc = isset($_POST['cat_desc']) ? wp_kses_post(wp_unslash($_POST['cat_desc'])) : '';
		$thumb_id = isset($_POST['cat_thumbnail_id']) ? absint($_POST['cat_thumbnail_id']) : 0;

		if (empty($cat_name)) {
			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_empty_name'), home_url('/admin-portal/')));
			exit;
		}

		$term_args = array(
			'description' => $cat_desc,
			'parent' => $cat_parent,
		);
		// Slug handling:
		// The slug field is hidden/removed from the category UI.
		// - On 'create': When 'slug' is omitted from $term_args, WordPress natively generates
		//   an appropriate, unique slug from $cat_name via sanitize_title().
		// - On 'edit': When 'slug' is omitted from $term_args, wp_update_term() preserves
		//   the existing category slug intact without overwriting it.
		if (!empty($cat_slug)) {
			$term_args['slug'] = $cat_slug;
		}

		if ('create' === $cat_action) {
			$inserted = wp_insert_term($cat_name, 'product_cat', $term_args);

			if (is_wp_error($inserted)) {
				wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_error'), home_url('/admin-portal/')));
				exit;
			}

			$new_term_id = $inserted['term_id'];
			if ($thumb_id > 0) {
				update_term_meta($new_term_id, 'thumbnail_id', $thumb_id);
			}

			if (isset($_POST['cat_specs_json'])) {
				ashrafco_process_and_save_category_specs($new_term_id, $_POST['cat_specs_json']);
			}

			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_created'), home_url('/admin-portal/')));
			exit;
		} elseif ('edit' === $cat_action) {
			$cat_id = isset($_POST['cat_id']) ? absint($_POST['cat_id']) : 0;
			if (!$cat_id) {
				wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_error'), home_url('/admin-portal/')));
				exit;
			}

			$term_args['name'] = $cat_name;
			$updated = wp_update_term($cat_id, 'product_cat', $term_args);

			if (is_wp_error($updated)) {
				wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_error'), home_url('/admin-portal/')));
				exit;
			}

			if ($thumb_id > 0) {
				update_term_meta($cat_id, 'thumbnail_id', $thumb_id);
			} elseif (isset($_POST['cat_remove_thumbnail']) && '1' === $_POST['cat_remove_thumbnail']) {
				delete_term_meta($cat_id, 'thumbnail_id');
			}

			if (isset($_POST['cat_specs_json'])) {
				ashrafco_process_and_save_category_specs($cat_id, $_POST['cat_specs_json']);
			}

			wp_safe_redirect(add_query_arg(array('tab' => 'products', 'portal_notice' => 'cat_updated'), home_url('/admin-portal/')));
			exit;
		}
	}
}
add_action('init', 'ashrafco_handle_category_actions');

/**
 * Get Products belonging to a Specific Category with search, stock filters, publication filters, sorting and pagination
 *
 * @param int   $category_id Term ID.
 * @param array $args Filter and pagination parameters.
 * @return array
 */
function ashrafco_get_category_products($category_id, $args = array())
{
	$category_id = absint($category_id);
	if (!$category_id) {
		return array(
			'products' => array(),
			'total' => 0,
			'pages' => 1,
			'current_page' => 1,
			'per_page' => 10,
			'in_stock' => 0,
			'out_of_stock' => 0,
			'total_category' => 0,
		);
	}

	$term = get_term($category_id, 'product_cat');
	if (!$term || is_wp_error($term)) {
		return array(
			'products' => array(),
			'total' => 0,
			'pages' => 1,
			'current_page' => 1,
			'per_page' => 10,
			'in_stock' => 0,
			'out_of_stock' => 0,
			'total_category' => 0,
		);
	}

	$search = isset($args['search']) ? trim(sanitize_text_field($args['search'])) : '';
	$stock_status = isset($args['stock_status']) ? sanitize_key($args['stock_status']) : 'all';
	$publish_status = isset($args['publish_status']) ? sanitize_key($args['publish_status']) : 'all';
	$sort = isset($args['sort']) ? sanitize_key($args['sort']) : 'date_desc';
	$paged = isset($args['paged']) ? max(1, absint($args['paged'])) : 1;
	$per_page = isset($args['per_page']) ? min(100, max(5, absint($args['per_page']))) : 10;

	// Base Query arguments for WooCommerce products
	$query_args = array(
		'limit'    => $per_page,
		'page'     => $paged,
		'paginate' => true,
		'category' => array($term->slug),
	);

	// Publication status
	if ('publish' === $publish_status) {
		$query_args['status'] = array('publish');
	} elseif ('draft' === $publish_status) {
		$query_args['status'] = array('draft');
	} else {
		$query_args['status'] = array('publish', 'draft', 'pending', 'private');
	}

	// Sorting rules
	switch ($sort) {
		case 'date_asc':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'ASC';
			break;
		case 'title_asc':
			$query_args['orderby'] = 'title';
			$query_args['order'] = 'ASC';
			break;
		case 'price_asc':
			$query_args['orderby'] = 'meta_value_num';
			$query_args['meta_key'] = '_price';
			$query_args['order'] = 'ASC';
			break;
		case 'price_desc':
			$query_args['orderby'] = 'meta_value_num';
			$query_args['meta_key'] = '_price';
			$query_args['order'] = 'DESC';
			break;
		case 'stock_asc':
			$query_args['orderby'] = 'meta_value_num';
			$query_args['meta_key'] = '_stock';
			$query_args['order'] = 'ASC';
			break;
		case 'date_desc':
		default:
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
	}

	// Enhanced Product Search: Title or SKU
	if (!empty($search)) {
		global $wpdb;
		$escaped_like = '%' . $wpdb->esc_like($search) . '%';

		// Matching by SKU in postmeta
		$sku_ids = $wpdb->get_col($wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_sku' AND meta_value LIKE %s",
			$escaped_like
		));

		// Matching by Title in posts
		$title_ids = $wpdb->get_col($wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_title LIKE %s",
			$escaped_like
		));

		$matching_ids = array_unique(array_merge((array) $sku_ids, (array) $title_ids));
		if (!empty($matching_ids)) {
			$query_args['include'] = $matching_ids;
		} else {
			$query_args['include'] = array(0); // Force empty results
		}
	}

	// Stock Status Filtering
	if ('instock' === $stock_status) {
		$query_args['stock_status'] = 'instock';
	} elseif ('outofstock' === $stock_status) {
		$query_args['stock_status'] = 'outofstock';
	} elseif ('lowstock' === $stock_status) {
		global $wpdb;
		$low_stock_threshold = absint(get_option('woocommerce_notify_low_stock_amount', 2));
		$low_stock_ids = $wpdb->get_col($wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} p
			 INNER JOIN {$wpdb->postmeta} pm_manage ON p.ID = pm_manage.post_id AND pm_manage.meta_key = '_manage_stock' AND pm_manage.meta_value = 'yes'
			 INNER JOIN {$wpdb->postmeta} pm_stock ON p.ID = pm_stock.post_id AND pm_stock.meta_key = '_stock'
			 WHERE p.post_type = 'product' AND CAST(pm_stock.meta_value AS SIGNED) > 0 AND CAST(pm_stock.meta_value AS SIGNED) <= %d",
			$low_stock_threshold
		));

		if (!empty($low_stock_ids)) {
			if (isset($query_args['include'])) {
				$query_args['include'] = array_intersect($query_args['include'], $low_stock_ids);
				if (empty($query_args['include'])) {
					$query_args['include'] = array(0);
				}
			} else {
				$query_args['include'] = $low_stock_ids;
			}
		} else {
			$query_args['include'] = array(0);
		}
	}

	$results = wc_get_products($query_args);
	$products = isset($results->products) ? $results->products : array();
	$total = isset($results->total) ? $results->total : 0;
	$pages = isset($results->max_num_pages) ? $results->max_num_pages : 1;

	// Calculate overall in-stock and out-of-stock counts for this category
	$cat_stock_data = ashrafco_get_category_stock_counts($category_id);
	$total_category_products = $cat_stock_data['total'];
	$in_stock_count = $cat_stock_data['in_stock'];
	$out_of_stock_count = $cat_stock_data['out_of_stock'];

	return array(
		'products' => $products,
		'total' => $total,
		'pages' => $pages,
		'current_page' => $paged,
		'per_page' => $per_page,
		'in_stock' => $in_stock_count,
		'out_of_stock' => $out_of_stock_count,
		'total_category' => $total_category_products,
	);
}

/**
 * Get category stock counts (total, in_stock, out_of_stock)
 *
 * @param int $category_id Term ID.
 * @return array
 */
function ashrafco_get_category_stock_counts($category_id)
{
	global $wpdb;
	$category_id = absint($category_id);

	if (!$category_id) {
		return array('in_stock' => 0, 'out_of_stock' => 0, 'total' => 0);
	}

	// Get all term IDs (including children)
	$term_ids = get_term_children($category_id, 'product_cat');
	if (is_wp_error($term_ids) || empty($term_ids)) {
		$term_ids = array($category_id);
	} else {
		$term_ids[] = $category_id;
	}
	$term_ids = array_map('absint', array_unique($term_ids));
	$term_ids_in = implode(',', $term_ids);

	$counts = $wpdb->get_results(
		"SELECT pm_stock.meta_value AS stock_status, COUNT(DISTINCT p.ID) as total_count
		 FROM {$wpdb->posts} p
		 INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
		 INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'product_cat'
		 LEFT JOIN {$wpdb->postmeta} pm_stock ON p.ID = pm_stock.post_id AND pm_stock.meta_key = '_stock_status'
		 WHERE p.post_type = 'product'
		   AND p.post_status IN ('publish', 'draft', 'pending', 'private')
		   AND tt.term_id IN ({$term_ids_in})
		 GROUP BY pm_stock.meta_value"
	);

	$in_stock = 0;
	$out_of_stock = 0;
	$total = 0;

	if (!empty($counts)) {
		foreach ($counts as $row) {
			$cnt = (int) $row->total_count;
			$total += $cnt;
			if ('outofstock' === $row->stock_status) {
				$out_of_stock += $cnt;
			} else {
				$in_stock += $cnt;
			}
		}
	}

	return array(
		'in_stock'     => $in_stock,
		'out_of_stock' => $out_of_stock,
		'total'        => $total,
	);
}

/**
 * Handle CSV Export for Category Products
 */
function ashrafco_handle_category_products_csv_export()
{
	if (!isset($_GET['action']) || 'export_cat_products' !== $_GET['action']) {
		return;
	}

	if (!current_user_can('manage_woocommerce') && !current_user_can('edit_products') && !current_user_can('manage_options')) {
		wp_die(esc_html__('ليس لديك الصلاحيات الكافية لتصدير هذه البيانات.', 'ashrafco'), '403 Forbidden', array('response' => 403));
	}

	check_admin_referer('ashrafco_export_cat_products_nonce', '_wpnonce');

	$cat_id = isset($_GET['cat_id']) ? absint($_GET['cat_id']) : 0;
	if (!$cat_id) {
		wp_die(esc_html__('معرّف التصنيف غير صالح.', 'ashrafco'));
	}

	$term = get_term($cat_id, 'product_cat');
	$cat_name = ($term && !is_wp_error($term)) ? $term->name : 'category-' . $cat_id;

	$search = isset($_GET['prod_search']) ? trim(sanitize_text_field($_GET['prod_search'])) : '';
	$stock_status = isset($_GET['prod_stock']) ? sanitize_key($_GET['prod_stock']) : 'all';
	$publish_status = isset($_GET['prod_publish']) ? sanitize_key($_GET['prod_publish']) : 'all';
	$sort = isset($_GET['prod_sort']) ? sanitize_key($_GET['prod_sort']) : 'date_desc';

	$export_data = ashrafco_get_category_products($cat_id, array(
		'search' => $search,
		'stock_status' => $stock_status,
		'publish_status' => $publish_status,
		'sort' => $sort,
		'per_page' => 500,
		'paged' => 1,
	));

	$filename = 'ashrafco-' . sanitize_title($cat_name) . '-products-' . gmdate('Y-m-d') . '.csv';

	header('Content-Type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header('Pragma: no-cache');
	header('Expires: 0');

	$output = fopen('php://output', 'w');
	fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM for Arabic support in Excel

	fputcsv($output, array(
		'كود المنتج (SKU)',
		'اسم المنتج',
		'السعر الحالي',
		'السعر الأصلي',
		'الكمية بالمخزون',
		'حالة المخزون',
		'التصنيف',
		'حالة النشر',
	));

	foreach ($export_data['products'] as $product) {
		$sku = $product->get_sku() ?: ('#' . $product->get_id());
		$name = $product->get_name();
		$price = $product->get_price();
		$reg_price = $product->get_regular_price() ?: $price;
		$stock_qty = $product->get_stock_quantity() !== null ? $product->get_stock_quantity() : 'غير محدد';
		$stock_label = $product->is_in_stock() ? 'متوفر بالمخزون' : 'نفذ المخزون';
		$status_label = 'publish' === $product->get_status() ? 'منشور' : 'مسودة';

		fputcsv($output, array(
			$sku,
			$name,
			$price,
			$reg_price,
			$stock_qty,
			$stock_label,
			$cat_name,
			$status_label,
		));
	}

	fclose($output);
	exit;
}
add_action('init', 'ashrafco_handle_category_products_csv_export');

/**
 * ============================================================================
 * Category Specifications & Brand Management Infrastructure
 * ============================================================================
 */

/**
 * Register Product Brand Taxonomy
 */
function ashrafco_register_brand_taxonomy()
{
	if (taxonomy_exists('product_brand')) {
		return;
	}

	register_taxonomy('product_brand', 'product', array(
		'labels' => array(
			'name' => 'العلامات التجارية',
			'singular_name' => 'علامة تجارية',
			'search_items' => 'البحث في العلامات التجارية',
			'all_items' => 'جميع العلامات التجارية',
			'parent_item' => 'العلامة الأب',
			'parent_item_colon' => 'العلامة الأب:',
			'edit_item' => 'تعديل العلامة التجارية',
			'update_item' => 'تحديث العلامة التجارية',
			'add_new_item' => 'إضافة علامة تجارية جديدة',
			'new_item_name' => 'اسم العلامة التجارية الجديدة',
			'menu_name' => 'العلامات التجارية',
		),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'show_admin_column' => true,
		'show_in_rest' => true,
		'rewrite' => array('slug' => 'brand'),
	));
}
add_action('init', 'ashrafco_register_brand_taxonomy', 5);

/**
 * Pre-populate Default Brands if empty
 */
function ashrafco_seed_default_brands_if_empty()
{
	if (!taxonomy_exists('product_brand')) {
		return;
	}

	$existing = get_terms(array(
		'taxonomy' => 'product_brand',
		'hide_empty' => false,
		'number' => 1,
	));

	if (empty($existing) || is_wp_error($existing)) {
		$default_brands = array(
			function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو',
			'بوش (Bosch)',
			'ماكيتا (Makita)',
			'ديوالت (DeWalt)',
			'توتال (Total)',
			'إنجكو (Ingco)',
			'كراون (Crown)',
			'روبي (Rubi)',
			'سيجما (Sigma)',
			'باتل (Battipav)',
			'مونوليت (Montolit)',
		);
		foreach ($default_brands as $brand_name) {
			wp_insert_term($brand_name, 'product_brand');
		}
	}
}
add_action('init', 'ashrafco_seed_default_brands_if_empty', 10);

/**
 * Retrieve All Registered Brands
 *
 * @return array
 */
function ashrafco_get_brands()
{
	$terms = get_terms(array(
		'taxonomy' => 'product_brand',
		'hide_empty' => false,
		'orderby' => 'name',
		'order' => 'ASC',
	));

	if (is_wp_error($terms) || empty($terms)) {
		return array();
	}

	$brands = array();
	foreach ($terms as $t) {
		$brands[] = array(
			'id'    => $t->term_id,
			'name'  => $t->name,
			'slug'  => $t->slug,
			'count' => (int) $t->count,
		);
	}
	return $brands;
}

/**
 * Get Defined Specifications for a Product Category
 *
 * @param int $term_id Category term ID.
 * @return array List of specification objects.
 */
function ashrafco_get_category_specs($term_id)
{
	$term_id = absint($term_id);
	if (!$term_id) {
		return array();
	}

	$specs = get_term_meta($term_id, '_ashrafco_category_specs', true);

	if (is_array($specs) && !empty($specs)) {
		// Sort by order
		usort($specs, function ($a, $b) {
			$ord_a = isset($a['order']) ? (int) $a['order'] : 0;
			$ord_b = isset($b['order']) ? (int) $b['order'] : 0;
			return $ord_a <=> $ord_b;
		});
		return $specs;
	}

	// Pre-configured default specs for watch categories (fallback if not saved in term meta)
	$term = get_term($term_id, 'product_cat');
	if ($term && !is_wp_error($term)) {
		$slug = $term->slug;

		if ('classic-watches' === $slug || mb_stripos($term->name, 'كلاسيكية') !== false) {
			return array(
				array(
					'id'       => 'dial_shape',
					'name'     => 'شكل الميناء',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('دائري', 'مربع', 'مستطيل', 'برميلي (Tonneau)', 'بيضاوي'),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'clasp_type',
					'name'     => 'نوع الإغلاق',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('إبزيم مشبك دبوسي (Pin Buckle)', 'قفل فراشة قابل للطي (Butterfly Clasp)', 'مشبك قابل للطي بزر أمان', 'مشبك مغناطيسي'),
					'required' => 0,
					'order'    => 2,
				),
			);
		}

		if ('sports-watches' === $slug || mb_stripos($term->name, 'رياضية') !== false) {
			return array(
				array(
					'id'       => 'sport_functions',
					'name'     => 'الوظائف الرياضية',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'illumination_type',
					'name'     => 'نوع الإضاءة',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('عقارب ومؤشرات مضيئة بتقنية لومينوس (Super-LumiNova)', 'إضاءة خلفية LED كهربائية', 'أنابيب غاز التريتيوم ذاتية الإضاءة', 'بدون إضاءة'),
					'required' => 0,
					'order'    => 2,
				),
			);
		}

		if ('chronograph-watches' === $slug || mb_stripos($term->name, 'كرونوغراف') !== false) {
			return array(
				array(
					'id'       => 'subdials_count',
					'name'     => 'عدد العدادات الفرعية',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('2 عدادات فرعية', '3 عدادات فرعية', '4 عدادات فرعية'),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'chronograph_function',
					'name'     => 'وظيفة الكرونوغراف',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('قياس أجزاء الثانية (1/10 و 1/100)', 'تاكيميتر لقياس السرعة (Tachymeter)', 'كرونوغراف فلاي باك (Flyback)', 'قياس تراكمي 60 دقيقة و 12 ساعة'),
					'required' => 0,
					'order'    => 2,
				),
			);
		}

		if ('smart-watches' === $slug || mb_stripos($term->name, 'ذكية') !== false) {
			return array(
				array(
					'id'       => 'os_compatibility',
					'name'     => 'نظام التشغيل أو التوافق',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('متوافق مع Android و iOS', 'WatchOS (Apple)', 'Wear OS (Google)', 'نظام مخصص فائق التوافق'),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'screen_size',
					'name'     => 'حجم الشاشة',
					'type'     => 'text',
					'unit'     => 'بوصة',
					'options'  => array(),
					'required' => 0,
					'order'    => 2,
				),
				array(
					'id'       => 'battery_life',
					'name'     => 'عمر البطارية',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 3,
				),
				array(
					'id'       => 'connectivity',
					'name'     => 'الاتصال',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('بلوتوث 5.3 + Wi-Fi', 'بلوتوث + Wi-Fi + شريحة اتصال eSIM', 'بلوتوث 5.0 منخفض الطاقة'),
					'required' => 0,
					'order'    => 4,
				),
				array(
					'id'       => 'sensors',
					'name'     => 'المستشعرات',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 5,
				),
			);
		}

		if ('special-editions' === $slug || mb_stripos($term->name, 'إصدارات خاصة') !== false || mb_stripos($term->name, 'اصدارات خاصة') !== false) {
			return array(
				array(
					'id'       => 'collection_name',
					'name'     => 'اسم المجموعة',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'edition_number',
					'name'     => 'رقم الإصدار',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 2,
				),
			);
		}

		if ('watch-accessories' === $slug || mb_stripos($term->name, 'إكسسوار') !== false || mb_stripos($term->name, 'اكسسوار') !== false) {
			return array(
				array(
					'id'       => 'accessory_type',
					'name'     => 'نوع الإكسسوار',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('سوار ساعة بديل', 'صندوق وحافظة ساعات فاخرة', 'جهاز تدوير الساعات الأوتوماتيكية (Watch Winder)', 'أداة ضبط وإزالة وصلات السوار', 'واقي شاشة وحماية', 'شاحن مغناطيسي'),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'accessory_size',
					'name'     => 'المقاس',
					'type'     => 'text',
					'unit'     => 'مم',
					'options'  => array(),
					'required' => 0,
					'order'    => 2,
				),
				array(
					'id'       => 'watch_compatibility',
					'name'     => 'التوافق مع الساعات',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 3,
				),
			);
		}

		// Backward compatibility: Seed default specs for legacy industrial category if requested
		if (19 === $term_id || mb_stripos($term->name, 'تسوية') !== false || mb_stripos($term->name, 'تثبيت') !== false) {
			return array(
				array(
					'id'       => 'max_cut_length',
					'name'     => 'أقصى طول للقص / المقاس',
					'type'     => 'number',
					'unit'     => 'سم',
					'options'  => array(),
					'required' => 0,
					'order'    => 1,
				),
				array(
					'id'       => 'max_thickness',
					'name'     => 'أقصى سمك للقص / سمك البلاط',
					'type'     => 'number',
					'unit'     => 'ملم',
					'options'  => array(),
					'required' => 0,
					'order'    => 2,
				),
				array(
					'id'       => 'tool_type',
					'name'     => 'نوع الأداة',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('أداة يدوية', 'أداة كهربائية', 'أداة هوائية / ضغط', 'ملحقات ومستهلكات'),
					'required' => 0,
					'order'    => 3,
				),
				array(
					'id'       => 'spare_parts',
					'name'     => 'توفر قطع الغيار والصيانة',
					'type'     => 'select',
					'unit'     => '',
					'options'  => array('نعم - متوفرة أصلية بمركز أشرفكو', 'نعم - متوفرة تجارية', 'متوفرة بالطلب', 'غير متوفرة حالياً'),
					'required' => 0,
					'order'    => 4,
				),
				array(
					'id'       => 'cut_accuracy',
					'name'     => 'دقة القص',
					'type'     => 'text',
					'unit'     => '',
					'options'  => array(),
					'required' => 0,
					'order'    => 5,
				),
			);
		}
	}

	return array();
}

/**
 * Process and Save Category Specifications
 *
 * @param int          $term_id Category term ID.
 * @param string|array $raw_specs JSON string or array of specs.
 * @return bool
 */
function ashrafco_process_and_save_category_specs($term_id, $raw_specs)
{
	$term_id = absint($term_id);
	if (!$term_id) {
		return false;
	}

	if (is_string($raw_specs)) {
		$raw_specs = json_decode(wp_unslash($raw_specs), true);
	}

	if (!is_array($raw_specs)) {
		$raw_specs = array();
	}

	$cleaned_specs = array();
	$order = 1;

	foreach ($raw_specs as $s) {
		$name = isset($s['name']) ? sanitize_text_field($s['name']) : '';
		if (empty($name)) {
			continue;
		}

		$type = isset($s['type']) ? sanitize_key($s['type']) : 'text';
		if (!in_array($type, array('text', 'number', 'select', 'boolean'), true)) {
			$type = 'text';
		}

		$unit = isset($s['unit']) ? sanitize_text_field($s['unit']) : '';
		$opts = array();
		if (!empty($s['options'])) {
			if (is_array($s['options'])) {
				$opts = array_map('sanitize_text_field', $s['options']);
			} else {
				$exploded = explode(',', (string) $s['options']);
				$opts = array_values(array_filter(array_map('trim', array_map('sanitize_text_field', $exploded))));
			}
		}

		$required = !empty($s['required']) ? 1 : 0;
		$id = !empty($s['id']) ? sanitize_key($s['id']) : sanitize_title($name);
		if (empty($id)) {
			$id = 'spec_' . $order;
		}

		$cleaned_specs[] = array(
			'id' => $id,
			'name' => $name,
			'type' => $type,
			'unit' => $unit,
			'options' => $opts,
			'required' => $required,
			'order' => $order++,
		);
	}

	return (bool) update_term_meta($term_id, '_ashrafco_category_specs', $cleaned_specs);
}

/**
 * AJAX: Fetch Category Specifications for Dynamic Product Form Rendering
 */
function ashrafco_ajax_get_category_specs()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بالوصول.'), 403);
	}

	$cat_id = isset($_GET['cat_id']) ? absint($_GET['cat_id']) : 0;
	if (!$cat_id) {
		wp_send_json_error(array('message' => 'معرّف التصنيف غير صالح'));
	}

	$term = get_term($cat_id, 'product_cat');
	if (!$term || is_wp_error($term)) {
		wp_send_json_error(array('message' => 'التصنيف غير موجود'));
	}

	$specs = ashrafco_get_category_specs($cat_id);

	wp_send_json_success(array(
		'cat_id' => $cat_id,
		'cat_name' => $term->name,
		'specs' => $specs,
	));
}
add_action('wp_ajax_ashrafco_get_category_specs', 'ashrafco_ajax_get_category_specs');

/**
/**
 * AJAX: Retrieve all Brands with product counts
 */
function ashrafco_ajax_get_brands()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بعرض العلامات التجارية'), 403);
	}

	$brands = ashrafco_get_brands();
	wp_send_json_success(array('brands' => $brands));
}
add_action('wp_ajax_ashrafco_get_brands', 'ashrafco_ajax_get_brands');

/**
 * AJAX: Add Brand (with optional initial products assignment)
 */
function ashrafco_ajax_add_brand()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بإضافة علامة تجارية'), 403);
	}

	$brand_name = isset($_POST['brand_name']) ? sanitize_text_field(wp_unslash($_POST['brand_name'])) : '';
	if (empty($brand_name)) {
		wp_send_json_error(array('message' => 'اسم العلامة التجارية مطلوب'));
	}

	$term_id = 0;
	$inserted = wp_insert_term($brand_name, 'product_brand');
	if (is_wp_error($inserted)) {
		if (isset($inserted->error_data['term_exists'])) {
			$term_id = (int) $inserted->error_data['term_exists'];
		} else {
			wp_send_json_error(array('message' => $inserted->get_error_message()));
		}
	} else {
		$term_id = (int) $inserted['term_id'];
	}

	$term = get_term($term_id, 'product_brand');
	$term_name = $term ? $term->name : $brand_name;

	// Optionally assign products
	if (!empty($_POST['product_ids']) && is_array($_POST['product_ids'])) {
		foreach ($_POST['product_ids'] as $pid) {
			$pid = absint($pid);
			if ($pid > 0) {
				wp_set_object_terms($pid, $term_id, 'product_brand', false);
				update_post_meta($pid, '_test_theme_brand', $term_name);
				update_post_meta($pid, '_ashrafco_brand', $term_name);
			}
		}
		wp_update_term_count_now(array($term_id), 'product_brand');
	}

	$refreshed_term = get_term($term_id, 'product_brand');

	wp_send_json_success(array(
		'term_id' => $term_id,
		'id'      => $term_id,
		'name'    => $term_name,
		'count'   => $refreshed_term ? (int) $refreshed_term->count : 0,
		'brands'  => ashrafco_get_brands(),
	));
}
add_action('wp_ajax_ashrafco_add_brand', 'ashrafco_ajax_add_brand');

/**
 * AJAX: Retrieve Products Assigned to a Specific Brand
 */
function ashrafco_ajax_get_brand_products()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بعرض المنتجات'), 403);
	}

	$brand_id = isset($_POST['brand_id']) ? absint($_POST['brand_id']) : 0;
	if (!$brand_id) {
		wp_send_json_error(array('message' => 'معرف العلامة التجارية غير صالح'));
	}

	$term = get_term($brand_id, 'product_brand');
	if (!$term || is_wp_error($term)) {
		wp_send_json_error(array('message' => 'العلامة التجارية غير موجودة'));
	}

	$query_args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => array('publish', 'draft', 'pending'),
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_brand',
				'field'    => 'term_id',
				'terms'    => $brand_id,
			),
		),
	);
	$posts = get_posts($query_args);

	$products_data = array();
	foreach ($posts as $post) {
		$product = wc_get_product($post->ID);
		if (!$product) continue;

		$thumb_id = $product->get_image_id();
		$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : wc_placeholder_img_src();

		$products_data[] = array(
			'id'        => $product->get_id(),
			'name'      => $product->get_name(),
			'sku'       => $product->get_sku() ?: '—',
			'price'     => $product->get_price_html() ?: '',
			'thumb_url' => $thumb_url ?: '',
		);
	}

	wp_send_json_success(array(
		'brand_id'   => $brand_id,
		'brand_name' => $term->name,
		'count'      => count($products_data),
		'products'   => $products_data,
	));
}
add_action('wp_ajax_ashrafco_get_brand_products', 'ashrafco_ajax_get_brand_products');

/**
 * AJAX: Search Products for Brand Assignment
 */
function ashrafco_ajax_search_products_for_brand()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بالبحث في المنتجات'), 403);
	}

	$search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
	$brand_id = isset($_POST['brand_id']) ? absint($_POST['brand_id']) : 0;

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => 20,
		'post_status'    => array('publish', 'draft'),
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if (!empty($search)) {
		$args['s'] = $search;
	}

	$posts = get_posts($args);
	$results = array();

	foreach ($posts as $post) {
		$product = wc_get_product($post->ID);
		if (!$product) continue;

		$is_assigned = false;
		if ($brand_id > 0) {
			$terms = wp_get_post_terms($post->ID, 'product_brand', array('fields' => 'ids'));
			if (!is_wp_error($terms) && in_array($brand_id, $terms, true)) {
				$is_assigned = true;
			}
		}

		$thumb_id = $product->get_image_id();
		$thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : wc_placeholder_img_src();

		$results[] = array(
			'id'          => $product->get_id(),
			'name'        => $product->get_name(),
			'sku'         => $product->get_sku() ?: '—',
			'thumb_url'   => $thumb_url ?: '',
			'is_assigned' => $is_assigned,
		);
	}

	wp_send_json_success(array('results' => $results));
}
add_action('wp_ajax_ashrafco_search_products_for_brand', 'ashrafco_ajax_search_products_for_brand');

/**
 * AJAX: Assign or Unassign Products to a Brand
 */
function ashrafco_ajax_assign_brand_products()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('edit_products') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بتعيين المنتجات'), 403);
	}

	$brand_id = isset($_POST['brand_id']) ? absint($_POST['brand_id']) : 0;
	$product_ids = isset($_POST['product_ids']) ? array_map('absint', (array) $_POST['product_ids']) : array();
	$assign_action = isset($_POST['assign_action']) ? sanitize_key($_POST['assign_action']) : 'add'; // 'add' or 'remove'

	if (!$brand_id) {
		wp_send_json_error(array('message' => 'العلامة التجارية غير محددة'));
	}

	$term = get_term($brand_id, 'product_brand');
	if (!$term || is_wp_error($term)) {
		wp_send_json_error(array('message' => 'العلامة التجارية غير موجودة'));
	}

	foreach ($product_ids as $pid) {
		if ($pid <= 0) continue;

		if ('add' === $assign_action) {
			wp_set_object_terms($pid, $brand_id, 'product_brand', false);
			update_post_meta($pid, '_test_theme_brand', $term->name);
			update_post_meta($pid, '_ashrafco_brand', $term->name);
		} elseif ('remove' === $assign_action) {
			wp_remove_object_terms($pid, $brand_id, 'product_brand');
			delete_post_meta($pid, '_test_theme_brand');
			delete_post_meta($pid, '_ashrafco_brand');
		}
	}

	wp_update_term_count_now(array($brand_id), 'product_brand');
	$refreshed_term = get_term($brand_id, 'product_brand');

	wp_send_json_success(array(
		'brand_id' => $brand_id,
		'count'    => $refreshed_term ? (int) $refreshed_term->count : 0,
		'brands'   => ashrafco_get_brands(),
	));
}
add_action('wp_ajax_ashrafco_assign_brand_products', 'ashrafco_ajax_assign_brand_products');

/**
 * AJAX: Delete Brand (with option to delete all products or unlink them)
 */
function ashrafco_ajax_delete_brand()
{
	check_ajax_referer('ashrafco_portal_ajax_nonce', 'security');

	if (!current_user_can('delete_product_terms') && !current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
		wp_send_json_error(array('message' => 'غير مصرح لك بحذف العلامات التجارية'), 403);
	}

	$brand_id = isset($_POST['brand_id']) ? absint($_POST['brand_id']) : 0;
	$delete_products = !empty($_POST['delete_products']) && ('1' === (string)$_POST['delete_products'] || 'true' === (string)$_POST['delete_products']);

	if (!$brand_id) {
		wp_send_json_error(array('message' => 'العلامة التجارية غير صالحة'));
	}

	$term = get_term($brand_id, 'product_brand');
	if (!$term || is_wp_error($term)) {
		wp_send_json_error(array('message' => 'العلامة التجارية غير موجودة'));
	}

	// Fetch all products linked to this brand
	$product_ids = get_posts(array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'post_status'    => 'any',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_brand',
				'field'    => 'term_id',
				'terms'    => $brand_id,
			),
		),
	));

	if ($delete_products) {
		// Permanently delete all products associated with this brand
		foreach ($product_ids as $pid) {
			$product = wc_get_product($pid);
			if ($product) {
				$product->delete(true);
			} else {
				wp_delete_post($pid, true);
			}
		}
	} else {
		// Unlink brand from all products, leave products intact
		foreach ($product_ids as $pid) {
			wp_remove_object_terms($pid, $brand_id, 'product_brand');
			delete_post_meta($pid, '_test_theme_brand');
			delete_post_meta($pid, '_ashrafco_brand');
		}
	}

	// Delete the term itself
	$result = wp_delete_term($brand_id, 'product_brand');
	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message()));
	}

	wp_send_json_success(array(
		'message'          => $delete_products ? 'تم حذف الماركة وجميع منتجاتها بنجاح' : 'تم حذف الماركة وفك ارتباط منتجاتها بنجاح',
		'deleted_products' => $delete_products,
		'deleted_count'    => count($product_ids),
		'brands'           => ashrafco_get_brands(),
	));
}
add_action('wp_ajax_ashrafco_delete_brand', 'ashrafco_ajax_delete_brand');


