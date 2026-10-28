<?php
/**
 * Catalog, Filtering & Product Pricing Engine
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Customize catalog ordering options.
 */
function test_theme_customize_catalog_orderby($orderby_options)
{
	return array(
		'date' => __('الترتيب حسب: الأحدث', 'ashrafco'),
		'price' => __('السعر: من الأقل للأعلى', 'ashrafco'),
		'price-desc' => __('السعر: من الأعلى للأقل', 'ashrafco'),
		'popularity' => __('الأكثر مبيعاً', 'ashrafco'),
		'menu_order' => __('الترتيب الافتراضي', 'ashrafco'),
	);
}
add_filter('woocommerce_catalog_orderby', 'test_theme_customize_catalog_orderby', 25);

/**
 * ============================================================================
 * Shop Filter Sidebar & Faceted Search Helpers
 * ============================================================================
 */

/**
 * Classify a product into one of the store's primary product classifications.
 *
 * Checks explicit metadata first (_test_theme_tool_type), followed by category and title heuristics.
 *
 * @param int $post_id
 * @return string
 */
function test_theme_classify_product_id($post_id)
{
	$tool_type = get_post_meta($post_id, '_test_theme_tool_type', true);
	if (!empty($tool_type)) {
		if (mb_stripos($tool_type, 'يدوي') !== false)
			return 'أدوات يدوية';
		if (mb_stripos($tool_type, 'كهربائ') !== false)
			return 'أدوات كهربائية';
		if (mb_stripos($tool_type, 'ملحق') !== false || mb_stripos($tool_type, 'مستلزم') !== false)
			return 'مستلزمات وملحقات';
		if (mb_stripos($tool_type, 'استهلاك') !== false)
			return 'مواد استهلاكية';
		if (mb_stripos($tool_type, 'قياس') !== false || mb_stripos($tool_type, 'ليزر') !== false)
			return 'أدوات قياس وليزر';
		if (mb_stripos($tool_type, 'معد') !== false || mb_stripos($tool_type, 'جهاز') !== false || mb_stripos($tool_type, 'ماكين') !== false)
			return 'معدات وأجهزة';
	}

	$cats = wp_get_post_terms($post_id, 'product_cat', array('fields' => 'names'));
	$cat_name = !empty($cats) ? $cats[0] : '';
	$title = get_the_title($post_id);

	if ($cat_name === 'شواخص وموازين ليزر' || mb_stripos($title, 'ليزر') !== false || mb_stripos($title, 'ميزان') !== false || mb_stripos($title, 'مسطرة') !== false) {
		return 'أدوات قياس وليزر';
	}
	if ($cat_name === 'أدوات كهربائية' || mb_stripos($title, 'كهربائي') !== false || mb_stripos($title, 'صاروخ') !== false || mb_stripos($title, 'خلاط') !== false) {
		return 'أدوات كهربائية';
	}
	if ($cat_name === 'ماكينات قص البورسلين' || $cat_name === 'خلاطات ومعدات ملاط' || mb_stripos($title, 'ماكينة') !== false) {
		return 'معدات وأجهزة';
	}
	if ($cat_name === 'أقراص وسكاكين وألماس القص' || mb_stripos($title, 'قرص') !== false || mb_stripos($title, 'ريشة') !== false || mb_stripos($title, 'ديسك') !== false || mb_stripos($title, 'بنط') !== false) {
		return 'مواد استهلاكية';
	}
	if (mb_stripos($title, 'كليبسات') !== false || mb_stripos($title, 'صلبان') !== false || mb_stripos($title, 'أسافين') !== false || mb_stripos($title, 'شنطة') !== false) {
		return 'مستلزمات وملحقات';
	}
	if ($cat_name === 'أدوات تسوية وتثبيت البلاط' || $cat_name === 'أدوات الشفط والفاكيوم' || mb_stripos($title, 'مطرقة') !== false || mb_stripos($title, 'مالج') !== false || mb_stripos($title, 'كماشة') !== false || mb_stripos($title, 'شفاط') !== false) {
		return 'أدوات يدوية';
	}
	return 'أخرى';
}

/**
 * Normalize raw country string to one of the 10 standard filter options.
 *
 * @param string $val
 * @return string|null
 */
function test_theme_normalize_country($val)
{
	$val = trim((string) $val);
	if (empty($val)) {
		return null;
	}
	if (mb_stripos($val, 'صين') !== false || mb_stripos($val, 'china') !== false) {
		return 'الصين';
	}
	if (mb_stripos($val, 'ألمان') !== false || mb_stripos($val, 'germany') !== false) {
		return 'ألمانيا';
	}
	if (mb_stripos($val, 'إيطال') !== false || mb_stripos($val, 'ايطال') !== false || mb_stripos($val, 'italy') !== false) {
		return 'إيطاليا';
	}
	if (mb_stripos($val, 'مصر') !== false || mb_stripos($val, 'محلي') !== false || mb_stripos($val, 'egypt') !== false) {
		return 'مصر (صناعة محلية)';
	}
	if (mb_stripos($val, 'ترك') !== false || mb_stripos($val, 'turkey') !== false) {
		return 'تركيا';
	}
	if (mb_stripos($val, 'تايوان') !== false || mb_stripos($val, 'taiwan') !== false) {
		return 'تايوان';
	}
	if (mb_stripos($val, 'يابان') !== false || mb_stripos($val, 'japan') !== false) {
		return 'اليابان';
	}
	if (mb_stripos($val, 'إسبان') !== false || mb_stripos($val, 'اسبان') !== false || mb_stripos($val, 'spain') !== false) {
		return 'إسبانيا';
	}
	if (mb_stripos($val, 'أمريك') !== false || mb_stripos($val, 'امريك') !== false || mb_stripos($val, 'ولايات') !== false || mb_stripos($val, 'usa') !== false) {
		return 'الولايات المتحدة';
	}
	return 'أخرى';
}

/**
 * Get normalized country of origin for a product, or null if unassigned.
 *
 * @param int $post_id
 * @return string|null
 */
function test_theme_get_product_country($post_id)
{
	$c = '';
	if (function_exists('ashrafco_get_product_spec')) {
		$c = ashrafco_get_product_spec($post_id, 'country_of_origin');
	}
	if (empty($c)) {
		$c = get_post_meta($post_id, '_test_theme_country_of_origin', true);
	}
	if (empty($c)) {
		$c = get_post_meta($post_id, 'country_of_origin', true);
	}
	if (empty($c)) {
		$c = get_post_meta($post_id, '_tiling_country', true);
	}
	return test_theme_normalize_country($c);
}

/**
 * Get product IDs matching the given countries.
 *
 * @param array $countries
 * @return array
 */
function test_theme_get_product_ids_by_country($countries)
{
	if (empty($countries)) {
		return array();
	}
	$index = ashrafco_get_product_filter_index();
	$matched = array();
	foreach ((array) $countries as $c) {
		if (!empty($index['by_country'][$c])) {
			$matched = array_merge($matched, $index['by_country'][$c]);
		}
	}
	return array_values(array_unique($matched));
}

/**
 * Get product IDs matching the given classifications.
 *
 * @param array $classes
 * @return array
 */
function test_theme_get_product_ids_by_classification($classes)
{
	if (empty($classes)) {
		return array();
	}
	$index = ashrafco_get_product_filter_index();
	$matched = array();
	foreach ((array) $classes as $cls) {
		if (!empty($index['by_class'][$cls])) {
			$matched = array_merge($matched, $index['by_class'][$cls]);
		}
	}
	return array_values(array_unique($matched));
}

/**
 * Compute real-time filter counts for WooCommerce archive sidebar.
 *
 * @param array $args
 * @return array
 */
function test_theme_compute_filter_counts($args = array())
{
	$country_keys = array(
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
	$classification_keys = array(
		'أدوات يدوية',
		'أدوات كهربائية',
		'مستلزمات وملحقات',
		'مواد استهلاكية',
		'أدوات قياس وليزر',
		'معدات وأجهزة',
		'أخرى',
	);

	$version = function_exists('ashrafco_get_catalog_cache_version') ? ashrafco_get_catalog_cache_version() : 1;
	$queried_cat_id = is_product_category() ? get_queried_object_id() : 0;
	$cache_key = 'ashrafco_fc_' . $version . '_' . md5(wp_json_encode($args) . '_' . $queried_cat_id);

	$cached = get_transient($cache_key);
	if (is_array($cached) && isset($cached['instock'], $cached['countries'], $cached['classifications'])) {
		return $cached;
	}

	$index = function_exists('ashrafco_get_product_filter_index') ? ashrafco_get_product_filter_index() : array('products' => array());
	$all_products = isset($index['products']) ? $index['products'] : array();

	// Determine matching category filter
	$target_cats = array();
	if (!empty($args['filter_cat'])) {
		if (is_array($args['filter_cat'])) {
			$raw_cats = $args['filter_cat'];
		} else {
			$raw_str = (string) $args['filter_cat'];
			$raw_cats = strpos($raw_str, ',') !== false ? explode(',', $raw_str) : array($raw_str);
		}
		foreach ($raw_cats as $tc) {
			$tc = (string) $tc;
			$target_cats[] = $tc;
			$target_cats[] = sanitize_title($tc);
			$target_cats[] = rawurlencode($tc);
			$target_cats[] = urldecode($tc);
		}
		$target_cats = array_values(array_filter(array_unique($target_cats)));
	} elseif (is_product_category()) {
		$queried = get_queried_object();
		if ($queried && !empty($queried->slug)) {
			$slug = (string) $queried->slug;
			$target_cats = array_values(array_filter(array_unique(array(
				$slug,
				sanitize_title($slug),
				rawurlencode($slug),
				urldecode($slug),
			))));
		}
	}

	$has_search = !empty($args['s']);
	$search_matched_ids = null;

	if ($has_search) {
		// Bounded query for search terms instead of unbounded -1
		$search_query = new WP_Query(array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => 500,
			'fields' => 'ids',
			'no_found_rows' => true,
			's' => sanitize_text_field($args['s']),
		));
		$search_matched_ids = array_flip($search_query->posts);
	}

	$has_min = isset($args['min_price']) && '' !== trim((string) $args['min_price']);
	$has_max = isset($args['max_price']) && '' !== trim((string) $args['max_price']);
	$min_p = $has_min ? max(0, floatval($args['min_price'])) : 0;
	$max_p = $has_max ? max(0, floatval($args['max_price'])) : 0;

	$country_counts = array_fill_keys($country_keys, 0);
	$classification_counts = array_fill_keys($classification_keys, 0);
	$instock_count = 0;

	$in_stock_only = !empty($args['in_stock_only']);

	foreach ($all_products as $pid => $pinfo) {
		// Respect WooCommerce catalog visibility
		if ($has_search) {
			if (!empty($pinfo['search_hidden'])) {
				continue;
			}
		} else {
			if (!empty($pinfo['catalog_hidden']) || !empty($pinfo['hidden'])) {
				continue;
			}
		}

		// Search keyword filter
		if ($has_search && !isset($search_matched_ids[$pid])) {
			continue;
		}

		// Category filter
		if (!empty($target_cats)) {
			if (empty($pinfo['cats']) || !array_intersect($target_cats, $pinfo['cats'])) {
				continue;
			}
		}

		// Price range filter (with strict min > max boundary validation)
		if ($has_min && $has_max) {
			if ($max_p >= $min_p) {
				if ($pinfo['price'] < $min_p || $pinfo['price'] > $max_p) {
					continue;
				}
			} else {
				// Impossible range (min > max): matches 0 products
				continue;
			}
		} elseif ($has_min) {
			if ($pinfo['price'] < $min_p) {
				continue;
			}
		} elseif ($has_max) {
			if ($pinfo['price'] > $max_p) {
				continue;
			}
		}

		if (!empty($pinfo['is_instock'])) {
			$instock_count++;
		}

		if ($in_stock_only && empty($pinfo['is_instock'])) {
			continue;
		}

		$c = $pinfo['country'];
		if (null !== $c && isset($country_counts[$c])) {
			$country_counts[$c]++;
		}

		$cls = $pinfo['class'];
		if (isset($classification_counts[$cls])) {
			$classification_counts[$cls]++;
		} else {
			$classification_counts['أخرى']++;
		}
	}

	$result = array(
		'instock' => $instock_count,
		'countries' => $country_counts,
		'classifications' => $classification_counts,
	);

	set_transient($cache_key, $result, 6 * HOUR_IN_SECONDS);
	return $result;
}


/**
 * Prevent empty 's' query parameter from breaking product archive queries.
 *
 * @param WP_Query $q
 */
function test_theme_ignore_empty_search($q)
{
	if (!is_admin() && $q->is_main_query() && isset($_GET['s']) && '' === trim((string) wp_unslash($_GET['s']))) {
		$q->is_search = false;
		$q->set('s', false);
	}
}
add_action('pre_get_posts', 'test_theme_ignore_empty_search', 5);

/**
 * Dynamic Product Query Filter for Shop Archive.
 *
 * Reads GET parameters:
 * - filter_cat: Filters by product_cat taxonomy slug
 * - in_stock_only: Filters by _stock_status = 'instock'
 * - filter_country: Filters by normalized country of origin
 * - filter_classification: Filters by product classification
 * - min_price / max_price: Filters by _price numeric range
 *
 * @param WP_Query $q The WooCommerce query object.
 */
function test_theme_filter_products_query($q)
{
	if (is_admin() || !$q->is_main_query()) {
		return;
	}

	$meta_query = (array) $q->get('meta_query');
	$tax_query = (array) $q->get('tax_query');

	// 1. Country & Classification Matching via Post IDs
	$restrict_ids = false;
	$matching_ids = array();

	// Country Filter
	if (!empty($_GET['filter_country'])) {
		if (is_array($_GET['filter_country'])) {
			$raw_countries = wp_unslash($_GET['filter_country']);
		} else {
			$raw_c_str = (string) wp_unslash($_GET['filter_country']);
			$raw_countries = strpos($raw_c_str, ',') !== false ? explode(',', $raw_c_str) : array($raw_c_str);
		}
		$countries = array_filter(array_map('sanitize_text_field', (array) $raw_countries));

		if (!empty($countries)) {
			$country_ids = test_theme_get_product_ids_by_country($countries);
			$matching_ids = $country_ids;
			$restrict_ids = true;
		}
	}

	// Classification Filter
	if (!empty($_GET['filter_classification'])) {
		if (is_array($_GET['filter_classification'])) {
			$raw_classes = wp_unslash($_GET['filter_classification']);
		} else {
			$raw_cls_str = (string) wp_unslash($_GET['filter_classification']);
			$raw_classes = strpos($raw_cls_str, ',') !== false ? explode(',', $raw_cls_str) : array($raw_cls_str);
		}
		$classes = array_filter(array_map('sanitize_text_field', (array) $raw_classes));

		if (!empty($classes)) {
			$class_ids = test_theme_get_product_ids_by_classification($classes);
			if ($restrict_ids) {
				$matching_ids = array_intersect($matching_ids, $class_ids);
			} else {
				$matching_ids = $class_ids;
				$restrict_ids = true;
			}
		}
	}

	if ($restrict_ids) {
		if (empty($matching_ids)) {
			$matching_ids = array(0);
		}
		$existing_post__in = array_filter((array) $q->get('post__in'));
		if (!empty($existing_post__in)) {
			$matching_ids = array_intersect($existing_post__in, $matching_ids);
			if (empty($matching_ids)) {
				$matching_ids = array(0);
			}
		}
		$q->set('post__in', array_values($matching_ids));
	}

	// 2. In Stock Only Filter
	if (!empty($_GET['in_stock_only']) && '1' === (string) $_GET['in_stock_only']) {
		$meta_query[] = array(
			'key' => '_stock_status',
			'value' => 'instock',
			'compare' => '=',
		);
	}

	// 3. Min / Max Price Filter
	$has_min = isset($_GET['min_price']) && '' !== trim((string) $_GET['min_price']);
	$has_max = isset($_GET['max_price']) && '' !== trim((string) $_GET['max_price']);

	if ($has_min || $has_max) {
		$min_val = $has_min ? max(0, floatval(wp_unslash($_GET['min_price']))) : 0;
		$max_val = $has_max ? max(0, floatval(wp_unslash($_GET['max_price']))) : 0;

		$price_meta = array(
			'key' => '_price',
			'type' => 'NUMERIC',
		);

		if ($has_min && $has_max) {
			if ($max_val >= $min_val) {
				$price_meta['value'] = array($min_val, $max_val);
				$price_meta['compare'] = 'BETWEEN';
			} else {
				// Impossible range (min > max): enforce zero results
				$price_meta['value'] = -1;
				$price_meta['compare'] = '<';
			}
		} elseif ($has_min) {
			$price_meta['value'] = $min_val;
			$price_meta['compare'] = '>=';
		} elseif ($has_max) {
			$price_meta['value'] = $max_val;
			$price_meta['compare'] = '<=';
		}

		$meta_query[] = $price_meta;
	}

	// 4. Category Terms Filter
	if (!empty($_GET['filter_cat'])) {
		if (is_array($_GET['filter_cat'])) {
			$raw_cats = wp_unslash($_GET['filter_cat']);
		} else {
			$raw_cat_str = (string) wp_unslash($_GET['filter_cat']);
			$raw_cats = strpos($raw_cat_str, ',') !== false ? explode(',', $raw_cat_str) : array($raw_cat_str);
		}
		$cat_slugs = array_filter(array_map('sanitize_title', (array) $raw_cats));

		if (!empty($cat_slugs)) {
			// If on a taxonomy archive (product_cat), remove the initial single-cat clause so our multi-cat filter takes precedence
			if (!empty($tax_query)) {
				foreach ($tax_query as $t_key => $t_clause) {
					if (is_array($t_clause) && isset($t_clause['taxonomy']) && 'product_cat' === $t_clause['taxonomy']) {
						unset($tax_query[$t_key]);
					}
				}
			}

			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field' => 'slug',
				'terms' => array_values($cat_slugs),
				'operator' => 'IN',
			);
		}
	}

	// 5. Brand Terms Filter
	if (!empty($_GET['filter_brand'])) {
		if (is_array($_GET['filter_brand'])) {
			$raw_brands = wp_unslash($_GET['filter_brand']);
		} else {
			$raw_b_str = (string) wp_unslash($_GET['filter_brand']);
			$raw_brands = strpos($raw_b_str, ',') !== false ? explode(',', $raw_b_str) : array($raw_b_str);
		}
		$brand_slugs = array_filter(array_map('sanitize_title', (array) $raw_brands));

		if (!empty($brand_slugs)) {
			$term_ids = array();
			foreach ($brand_slugs as $bs) {
				$t = get_term_by('slug', $bs, 'product_brand');
				if (!$t) {
					$t = get_term_by('name', $bs, 'product_brand');
				}
				if ($t) {
					$term_ids[] = (int) $t->term_id;
				}
			}

			if (!empty($term_ids)) {
				$tax_query[] = array(
					'taxonomy' => 'product_brand',
					'field'    => 'term_id',
					'terms'    => array_values(array_unique($term_ids)),
					'operator' => 'IN',
				);
			} else {
				$tax_query[] = array(
					'taxonomy' => 'product_brand',
					'field'    => 'slug',
					'terms'    => array_values($brand_slugs),
					'operator' => 'IN',
				);
			}
		}
	}

	$q->set('posts_per_page', 20);
	$q->set('meta_query', $meta_query);
	$q->set('tax_query', $tax_query);
}
add_action('woocommerce_product_query', 'test_theme_filter_products_query', 20);

/**
 * Change WooCommerce catalog archives to display 20 products per page.
 *
 * @param int $per_page
 * @return int
 */
function test_theme_loop_shop_per_page($per_page)
{
	return 20;
}
add_filter('loop_shop_per_page', 'test_theme_loop_shop_per_page', 999);

/**
 * Ensure active store filter parameters are preserved in WooCommerce pagination links.
 *
 * @param array $args Pagination arguments.
 * @return array
 */
function test_theme_woocommerce_pagination_args($args)
{
	$filter_keys = array('filter_cat', 'in_stock_only', 'filter_brand', 'filter_country', 'filter_classification', 'min_price', 'max_price', 's', 'orderby');
	$preserve_args = array();

	foreach ($filter_keys as $key) {
		if (isset($_GET[$key]) && '' !== $_GET[$key]) {
			$preserve_args[$key] = wp_unslash($_GET[$key]);
		}
	}

	if (!empty($preserve_args)) {
		$args['add_args'] = isset($args['add_args']) && is_array($args['add_args'])
			? array_merge($args['add_args'], $preserve_args)
			: $preserve_args;
	}

	return $args;
}
add_filter('woocommerce_pagination_args', 'test_theme_woocommerce_pagination_args');

/**
 * Generate URL to remove a single active filter parameter.
 *
 * @param string $param Parameter key (e.g. 'filter_country', 'in_stock_only', 'filter_cat').
 * @param mixed  $val   Optional specific value to remove from array parameters.
 * @return string
 */
function test_theme_get_remove_filter_url($param, $val = null)
{
	$current_url = is_product_category()
		? get_term_link(get_queried_object())
		: (function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'));

	$params = $_GET;
	unset($params['paged']); // Reset pagination when removing filters

	if ($val !== null && isset($params[$param])) {
		$raw_vals = is_array($params[$param]) ? $params[$param] : array($params[$param]);
		$filtered_vals = array();
		foreach ($raw_vals as $v) {
			if ((string) $v !== (string) $val) {
				$filtered_vals[] = $v;
			}
		}
		if (!empty($filtered_vals)) {
			$params[$param] = $filtered_vals;
		} else {
			unset($params[$param]);
		}
	} else {
		unset($params[$param]);
		// If removing price, remove both min_price and max_price
		if ('min_price' === $param || 'max_price' === $param) {
			unset($params['min_price'], $params['max_price']);
		}
	}

	return !empty($params) ? add_query_arg($params, $current_url) : $current_url;
}

/**
 * Remove duplicate WooCommerce pagination from after_shop_loop
 * so the custom styled pagination in archive-product.php renders once.
 */
function test_theme_remove_duplicate_pagination()
{
	remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
}

if (!function_exists('ashrafco_get_product_discount_data')) {
	function ashrafco_get_product_discount_data($product)
	{
		if (is_numeric($product)) {
			$product = wc_get_product($product);
		}
		if (!$product instanceof \WC_Product) {
			return array(
				'is_on_sale' => false,
				'regular_price' => 0.0,
				'sale_price' => null,
				'current_price' => 0.0,
				'discount_percent' => 0,
				'discount_label' => '',
				'has_countdown' => false,
				'sale_end_timestamp' => 0,
				'formatted_end_date' => '',
			);
		}

		$is_on_sale = $product->is_on_sale();
		$regular_price = 0.0;
		$sale_price = null;
		$current_price = (float) $product->get_price();
		$discount_percent = 0;
		$discount_label = '';
		$has_countdown = false;
		$sale_end_ts = 0;
		$formatted_end = '';
		$now_ts = time();

		if ($product->is_type('variable')) {
			$min_reg = (float) $product->get_variation_regular_price('min', true);
			$max_reg = (float) $product->get_variation_regular_price('max', true);
			$min_sale = (float) $product->get_variation_sale_price('min', true);
			$max_sale = (float) $product->get_variation_sale_price('max', true);

			$regular_price = $min_reg > 0 ? $min_reg : (float) $product->get_regular_price();
			$sale_price = $min_sale > 0 ? $min_sale : (float) $product->get_sale_price();

			if ($is_on_sale && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
				$discount_percent = (int) round((($regular_price - $sale_price) / $regular_price) * 100);
				$discount_label = sprintf(__('خصم %d%%', 'ashrafco'), $discount_percent);
			}

			if ($is_on_sale) {
				$variations = $product->get_available_variations('objects');
				$earliest_end = 0;
				foreach ($variations as $variation) {
					if ($variation->is_on_sale()) {
						$var_date_to = $variation->get_date_on_sale_to();
						if ($var_date_to instanceof \WC_DateTime) {
							$v_ts = $var_date_to->getTimestamp();
							if ($v_ts > $now_ts && (0 === $earliest_end || $v_ts < $earliest_end)) {
								$earliest_end = $v_ts;
								$formatted_end = $var_date_to->date('Y-m-d');
							}
						}
					}
				}
				if ($earliest_end > $now_ts) {
					$has_countdown = true;
					$sale_end_ts = $earliest_end;
				}
			}
		} else {
			$regular_price = (float) $product->get_regular_price();
			$sale_price = (float) $product->get_sale_price();

			if ($is_on_sale && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price) {
				$discount_percent = (int) round((($regular_price - $sale_price) / $regular_price) * 100);
				$discount_label = sprintf(__('خصم %d%%', 'ashrafco'), $discount_percent);
			}

			if ($is_on_sale) {
				$date_to = $product->get_date_on_sale_to();
				if ($date_to instanceof \WC_DateTime) {
					$ts = $date_to->getTimestamp();
					if ($ts > $now_ts) {
						$has_countdown = true;
						$sale_end_ts = $ts;
						$formatted_end = $date_to->date('Y-m-d');
					}
				}
			}
		}

		return array(
			'is_on_sale' => $is_on_sale && $discount_percent > 0,
			'regular_price' => $regular_price,
			'sale_price' => $sale_price,
			'current_price' => $current_price,
			'discount_percent' => $discount_percent,
			'discount_label' => $discount_label,
			'has_countdown' => $has_countdown,
			'sale_end_timestamp' => $sale_end_ts,
			'formatted_end_date' => $formatted_end,
		);
	}
}

if (!function_exists('ashrafco_get_on_sale_catalog')) {
	function ashrafco_get_on_sale_catalog($args = array())
	{
		$on_sale_ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : array();
		if (empty($on_sale_ids)) {
			return array(
				'items' => array(),
				'total' => 0,
				'total_pages' => 0,
				'current_page' => 1,
				'max_discount' => 0,
				'categories' => array(),
			);
		}

		$defaults = array(
			'limit' => 12,
			'page' => 1,
			'category' => '',
			's' => '',
			'orderby' => 'date',
			'order' => 'DESC',
		);
		$parsed = wp_parse_args($args, $defaults);
		$page = max(1, absint($parsed['page']));
		$limit = intval($parsed['limit']);
		if ($limit <= 0 && -1 !== $limit) {
			$limit = 12;
		}

		$query_all = array(
			'status' => 'publish',
			'include' => $on_sale_ids,
			'limit' => -1,
			'visibility' => 'visible',
		);
		if (!empty($parsed['s'])) {
			$query_all['s'] = sanitize_text_field($parsed['s']);
		}

		$all_products = wc_get_products($query_all);
		$items = array();
		$cat_buckets = array();
		$max_discount = 0;

		foreach ($all_products as $p) {
			if (!$p instanceof \WC_Product || !$p->is_visible()) {
				continue;
			}
			$d = ashrafco_get_product_discount_data($p);
			if (!$d['is_on_sale']) {
				continue;
			}
			if ($d['discount_percent'] > $max_discount) {
				$max_discount = $d['discount_percent'];
			}

			$terms = get_the_terms($p->get_id(), 'product_cat');
			$prod_cat_slugs = array();
			if ($terms && !is_wp_error($terms)) {
				foreach ($terms as $term) {
					$prod_cat_slugs[] = $term->slug;
					if (!isset($cat_buckets[$term->slug])) {
						$cat_buckets[$term->slug] = array(
							'id' => $term->term_id,
							'slug' => $term->slug,
							'name' => $term->name,
							'count' => 0,
						);
					}
					$cat_buckets[$term->slug]['count']++;
				}
			}

			if (!empty($parsed['category'])) {
				$req_cat = sanitize_title($parsed['category']);
				if (!in_array($req_cat, $prod_cat_slugs, true)) {
					continue;
				}
			}

			$items[] = array(
				'product' => $p,
				'id' => $p->get_id(),
				'discount_percent' => $d['discount_percent'],
				'price' => (float) $p->get_price(),
				'date' => $p->get_date_created() ? $p->get_date_created()->getTimestamp() : 0,
				'popularity' => (int) $p->get_total_sales(),
				'title' => $p->get_name(),
				'discount_data' => $d,
			);
		}

		$orderby = $parsed['orderby'];
		$order = strtoupper($parsed['order']);
		usort($items, function ($a, $b) use ($orderby, $order) {
			if ('discount' === $orderby) {
				$cmp = $a['discount_percent'] <=> $b['discount_percent'];
			} elseif ('price' === $orderby || 'price-asc' === $orderby) {
				$cmp = $a['price'] <=> $b['price'];
			} elseif ('price-desc' === $orderby) {
				$cmp = $b['price'] <=> $a['price'];
				return $cmp;
			} elseif ('popularity' === $orderby) {
				$cmp = $a['popularity'] <=> $b['popularity'];
			} elseif ('title' === $orderby) {
				$cmp = strcmp($a['title'], $b['title']);
			} else {
				$cmp = $a['date'] <=> $b['date'];
			}
			return 'ASC' === $order ? $cmp : -$cmp;
		});

		$total = count($items);
		$total_pages = $limit > 0 ? (int) ceil($total / $limit) : 1;
		$paged_items = $limit > 0 ? array_slice($items, ($page - 1) * $limit, $limit) : $items;

		return array(
			'items' => $paged_items,
			'total' => $total,
			'total_pages' => $total_pages,
			'current_page' => $page,
			'max_discount' => $max_discount,
			'categories' => array_values($cat_buckets),
		);
	}
}