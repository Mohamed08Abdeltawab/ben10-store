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
 * Canonical country filter values and the request aliases accepted for each.
 *
 * Only exact (case-insensitive) matches are accepted so unknown request values
 * can never be interpreted as the broad "أخرى" bucket.
 *
 * @return array<string, string[]>
 */
function test_theme_get_country_filter_aliases()
{
	return array(
		'الصين' => array('الصين', 'صين', 'china'),
		'ألمانيا' => array('ألمانيا', 'ألمان', 'germany'),
		'إيطاليا' => array('إيطاليا', 'ايطاليا', 'إيطال', 'ايطال', 'italy'),
		'مصر (صناعة محلية)' => array('مصر (صناعة محلية)', 'مصر', 'محلي', 'egypt'),
		'تركيا' => array('تركيا', 'ترك', 'turkey'),
		'تايوان' => array('تايوان', 'taiwan'),
		'اليابان' => array('اليابان', 'يابان', 'japan'),
		'إسبانيا' => array('إسبانيا', 'اسبانيا', 'إسبان', 'اسبان', 'spain'),
		'الولايات المتحدة' => array('الولايات المتحدة', 'ولايات', 'أمريك', 'امريك', 'usa', 'united states'),
		'أخرى' => array('أخرى', 'اخرى', 'other'),
	);
}

/**
 * Parse and validate country filter values from a request.
 *
 * Unknown values are rejected instead of being normalized to "أخرى". If every
 * supplied value is unknown the result is an empty array, which callers must
 * treat as an impossible filter (zero results), never as "no filter".
 *
 * @param mixed $values
 * @return string[] Canonical country names.
 */
function test_theme_parse_country_filter($values)
{
	$alias_map = array();
	foreach (test_theme_get_country_filter_aliases() as $canonical => $aliases) {
		foreach ($aliases as $alias) {
			$alias_map[mb_strtolower(trim((string) $alias))] = $canonical;
		}
	}

	$matched = array();
	foreach ((array) $values as $value) {
		if (!is_scalar($value)) {
			continue;
		}
		$key = mb_strtolower(trim(wp_unslash((string) $value)));
		if ('' !== $key && isset($alias_map[$key])) {
			$matched[] = $alias_map[$key];
		}
	}

	return array_values(array_unique($matched));
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
 * Retrieve the product filter index used by the shop filters.
 *
 * Uses Ashrafco Core's index when available. When the plugin is inactive,
 * build an equivalent theme-local index so filtering remains functional.
 *
 * @return array
 */
function test_theme_get_product_filter_index()
{
	if (function_exists('ashrafco_get_product_filter_index')) {
		$index = ashrafco_get_product_filter_index();
		if (is_array($index) && isset($index['products']) && is_array($index['products'])) {
			return $index;
		}
	}

	$cached = wp_cache_get('product_filter_index', 'test_theme_catalog');
	if (is_array($cached) && isset($cached['products'])) {
		return $cached;
	}

	$local_index = test_theme_build_product_filter_index();
	wp_cache_set('product_filter_index', $local_index, 'test_theme_catalog', HOUR_IN_SECONDS);

	return $local_index;
}

/**
 * Get the brand tokens used to match the filter_brand request values.
 *
 * @param int $product_id
 * @return string[]
 */
function test_theme_get_product_brand_tokens($product_id)
{
	$brands = array();
	if (!taxonomy_exists('product_brand')) {
		return $brands;
	}

	$brand_terms = wp_get_post_terms($product_id, 'product_brand');
	if (!is_wp_error($brand_terms)) {
		foreach ($brand_terms as $brand_term) {
			$brands[] = $brand_term->slug;
			$brands[] = urldecode($brand_term->slug);
			$brands[] = sanitize_title($brand_term->name);
			$brands[] = $brand_term->name;
		}
	}

	return array_values(array_unique(array_filter($brands)));
}

/**
 * Build a theme-local product filter index.
 *
 * Matches the shape expected by the shop filters: products, by_country,
 * and by_class.
 *
 * @return array
 */
function test_theme_build_product_filter_index()
{
	$index = array(
		'products' => array(),
		'by_country' => array(),
		'by_class' => array(),
	);

	$product_ids = get_posts(array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'no_found_rows' => true,
		'suppress_filters' => true,
		'orderby' => 'ID',
		'order' => 'ASC',
	));

	foreach ($product_ids as $product_id) {
		$product_id = (int) $product_id;
		$product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;

		$country = test_theme_get_product_country($product_id);
		$classification = test_theme_classify_product_id($product_id);

		if ($product instanceof \WC_Product) {
			$price = (float) $product->get_price();
			$stock_status = (string) $product->get_stock_status();
		} else {
			$price = (float) get_post_meta($product_id, '_price', true);
			$stock_status = (string) get_post_meta($product_id, '_stock_status', true);
		}
		// Match the in_stock_only query exactly. WooCommerce::is_in_stock()
		// also returns true for onbackorder, so it cannot be used here.
		$is_instock = ('instock' === $stock_status);

		$visibility = wp_get_post_terms($product_id, 'product_visibility', array('fields' => 'slugs'));
		$visibility = is_array($visibility) ? $visibility : array();
		$exclude_catalog = in_array('exclude-from-catalog', $visibility, true);
		$exclude_search = in_array('exclude-from-search', $visibility, true);

		$cats = array();
		$cat_slugs = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'slugs'));
		$cat_slugs = is_array($cat_slugs) ? $cat_slugs : array();
		foreach ($cat_slugs as $cat_slug) {
			$decoded_slug = urldecode($cat_slug);
			$cats[] = $cat_slug;
			$cats[] = $decoded_slug;
			$cats[] = rawurlencode($decoded_slug);
			$cats[] = sanitize_title($decoded_slug);
		}
		$cats = array_values(array_unique(array_filter($cats)));

		$brands = test_theme_get_product_brand_tokens($product_id);

		$index['products'][$product_id] = array(
			'country' => $country,
			'class' => $classification,
			'price' => $price,
			'stock_status' => $stock_status,
			'is_instock' => (bool) $is_instock,
			'cats' => $cats,
			'brands' => $brands,
			'catalog_hidden' => $exclude_catalog,
			'hidden' => ($exclude_catalog && $exclude_search),
			'search_hidden' => $exclude_search,
		);

		if (!empty($country)) {
			$index['by_country'][$country][] = $product_id;
		}
		$index['by_class'][$classification][] = $product_id;
	}

	return $index;
}

/**
 * Read the version used to namespace theme-local catalog counts transients.
 *
 * The theme owns a non-autoloaded option and bumps it on every relevant
 * catalog change. Core's transient version is also mixed in so changes made
 * through Ashrafco Core invalidate the theme counts as well, without relying
 * on a getter function that may not exist.
 *
 * @return string
 */
function test_theme_get_catalog_counts_version()
{
	$theme_version = (int) get_option('test_theme_catalog_counts_version', 0);
	$core_version = (int) get_transient('ashrafco_catalog_cache_version');

	return $theme_version . '-' . $core_version;
}

/**
 * Bump the theme-owned catalog counts version and clear the index in-request cache.
 */
function test_theme_bump_catalog_counts_version()
{
	$current = (int) get_option('test_theme_catalog_counts_version', 0);
	$next = max(time(), $current + 1);

	update_option('test_theme_catalog_counts_version', $next, false);
	wp_cache_delete('product_filter_index', 'test_theme_catalog');
}

/**
 * Clear the theme-local filter index and counts after catalog changes.
 */
function test_theme_invalidate_product_filter_index()
{
	test_theme_bump_catalog_counts_version();
}

/**
 * Invalidate counts when a product/variation is published, unpublished, trashed, or restored.
 *
 * @param string $new_status
 * @param string $old_status
 * @param WP_Post $post
 */
function test_theme_invalidate_on_product_status_change($new_status, $old_status, $post)
{
	if ($new_status === $old_status) {
		return;
	}
	if ($post instanceof WP_Post && in_array($post->post_type, array('product', 'product_variation'), true)) {
		test_theme_bump_catalog_counts_version();
	}
}

/**
 * Invalidate counts when product taxonomy assignments change.
 *
 * @param int $object_id
 * @param array $terms
 * @param array $tt_ids
 * @param string $taxonomy
 */
function test_theme_invalidate_on_object_terms($object_id, $terms, $tt_ids, $taxonomy)
{
	if (in_array($taxonomy, array('product_cat', 'product_tag', 'product_brand'), true)) {
		test_theme_bump_catalog_counts_version();
	}
}

/**
 * Invalidate counts when a product taxonomy term is created, edited, or deleted.
 *
 * @param int $term_id
 * @param int $tt_id
 * @param string $taxonomy
 */
function test_theme_invalidate_on_term_change($term_id, $tt_id = 0, $taxonomy = '')
{
	if (in_array($taxonomy, array('product_cat', 'product_tag', 'product_brand'), true)) {
		test_theme_bump_catalog_counts_version();
	}
}

/**
 * Invalidate counts when relevant product meta is written directly.
 *
 * WooCommerce saves normally fire the product/variation hooks above, but
 * imports and integrations may update price, stock, country, or classification
 * meta directly. One bump per request is enough because the version namespaces
 * every subsequent counts lookup.
 *
 * @param int $meta_id
 * @param int $object_id
 * @param string $meta_key
 * @param mixed $meta_value
 */
function test_theme_invalidate_on_product_meta_change($meta_id, $object_id, $meta_key, $meta_value)
{
	static $bumped = false;
	if ($bumped) {
		return;
	}

	$keys = array(
		'_price',
		'_regular_price',
		'_sale_price',
		'_stock',
		'_stock_status',
		'_manage_stock',
		'_backorders',
		'_test_theme_tool_type',
		'_test_theme_country_of_origin',
		'country_of_origin',
		'_tiling_country',
	);
	if (!in_array($meta_key, $keys, true)) {
		return;
	}
	if (!in_array(get_post_type($object_id), array('product', 'product_variation'), true)) {
		return;
	}

	$bumped = true;
	test_theme_bump_catalog_counts_version();
}

add_action('woocommerce_new_product', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_update_product', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_delete_product', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_product_set_stock', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_variation_set_stock', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_product_set_stock_status', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_variation_set_stock_status', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_new_product_variation', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_update_product_variation', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_save_product_variation', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_before_delete_product_variation', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_delete_product_variation', 'test_theme_invalidate_product_filter_index');
add_action('woocommerce_scheduled_sales', 'test_theme_invalidate_product_filter_index', 20);
add_action('update_option_woocommerce_hide_out_of_stock_items', 'test_theme_invalidate_product_filter_index');
add_action('transition_post_status', 'test_theme_invalidate_on_product_status_change', 10, 3);
add_action('set_object_terms', 'test_theme_invalidate_on_object_terms', 10, 4);
add_action('created_term', 'test_theme_invalidate_on_term_change', 10, 3);
add_action('edited_term', 'test_theme_invalidate_on_term_change', 10, 3);
add_action('delete_term', 'test_theme_invalidate_on_term_change', 10, 3);
add_action('added_post_meta', 'test_theme_invalidate_on_product_meta_change', 10, 4);
add_action('updated_post_meta', 'test_theme_invalidate_on_product_meta_change', 10, 4);
add_action('deleted_post_meta', 'test_theme_invalidate_on_product_meta_change', 10, 4);

/**
 * Get product IDs matching the given countries.
 *
 * @param array $countries
 * @return array
 */
function test_theme_get_product_ids_by_country($countries)
{
	$countries = test_theme_parse_country_filter($countries);
	if (empty($countries)) {
		return array();
	}
	$index = test_theme_get_product_filter_index();
	$matched = array();
	foreach ($countries as $country) {
		if (!empty($index['by_country'][$country])) {
			$matched = array_merge($matched, $index['by_country'][$country]);
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
	$index = test_theme_get_product_filter_index();
	$matched = array();
	foreach ((array) $classes as $cls) {
		if (!empty($index['by_class'][$cls])) {
			$matched = array_merge($matched, $index['by_class'][$cls]);
		}
	}
	return array_values(array_unique($matched));
}

/**
 * Split a filter request value into a list of scalar strings.
 *
 * @param mixed $value
 * @return string[]
 */
function test_theme_get_request_filter_values($value)
{
	$values = is_array($value) ? $value : array($value);
	$list = array();

	foreach ($values as $item) {
		if (!is_scalar($item)) {
			continue;
		}
		$item = trim((string) $item);
		if ('' === $item) {
			continue;
		}
		if (!is_array($value) && false !== strpos($item, ',')) {
			foreach (explode(',', $item) as $part) {
				$part = trim((string) $part);
				if ('' !== $part) {
					$list[] = $part;
				}
			}
			continue;
		}
		$list[] = $item;
	}

	return $list;
}

/**
 * Compute real-time filter counts for WooCommerce archive sidebar.
 *
 * Count semantics:
 * - Every facet ignores its own active selection and applies all other active
 *   filters, so each count shows what selecting that option would return.
 * - "In stock" is a strict _stock_status === 'instock' count, matching the
 *   in_stock_only query. onbackorder products are intentionally excluded.
 * - When woocommerce_hide_out_of_stock_items is "yes", outofstock products are
 *   excluded from every count, matching WooCommerce's catalog query.
 * - Unknown country request values are rejected as an impossible filter and
 *   produce zero counts instead of falling back to the broad "أخرى" bucket.
 * - Search requests are never persisted to transients, and cache keys only
 *   include the filter arguments this function actually reads.
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
	$zero_result = array(
		'instock' => 0,
		'countries' => array_fill_keys($country_keys, 0),
		'classifications' => array_fill_keys($classification_keys, 0),
	);

	$args = is_array($args) ? $args : array();

	// Category: match the sanitized slugs the main query applies.
	$target_cats = array();
	$category_key = array();
	if (isset($args['filter_cat'])) {
		foreach (test_theme_get_request_filter_values($args['filter_cat']) as $raw_cat) {
			$target_cats[] = $raw_cat;
			$target_cats[] = sanitize_title($raw_cat);
			$target_cats[] = rawurlencode($raw_cat);
			$target_cats[] = urldecode($raw_cat);
			$category_key[] = sanitize_title($raw_cat);
		}
		$target_cats = array_values(array_filter(array_unique($target_cats), 'strlen'));
		$category_key = array_values(array_filter(array_unique($category_key), 'strlen'));
		sort($category_key);
	} elseif (is_product_category()) {
		$queried = get_queried_object();
		if ($queried && !empty($queried->slug)) {
			$slug = (string) $queried->slug;
			$target_cats = array_values(array_filter(array_unique(array(
				$slug,
				sanitize_title($slug),
				rawurlencode($slug),
				urldecode($slug),
			)), 'strlen'));
			$category_key = $target_cats;
			sort($category_key);
		}
	}

	// Countries: only known aliases are accepted. An unknown-only selection is
	// an impossible filter, not an inactive one.
	$selected_countries = array();
	$country_filter_invalid = false;
	if (isset($args['filter_country'])) {
		$raw_countries = array_filter(array_map('sanitize_text_field', test_theme_get_request_filter_values($args['filter_country'])), 'strlen');
		if (!empty($raw_countries)) {
			$selected_countries = test_theme_parse_country_filter($raw_countries);
			if (empty($selected_countries)) {
				$country_filter_invalid = true;
			}
			sort($selected_countries);
		}
	}

	// Classifications: unknown values naturally match no product, like the query.
	$selected_classes = array();
	if (isset($args['filter_classification'])) {
		$selected_classes = array_values(array_unique(array_filter(array_map('sanitize_text_field', test_theme_get_request_filter_values($args['filter_classification'])), 'strlen')));
		sort($selected_classes);
	}

	// Brands: the query uses sanitize_title(), so counts do too.
	$selected_brands = array();
	if (isset($args['filter_brand'])) {
		$selected_brands = array_values(array_unique(array_filter(array_map('sanitize_title', test_theme_get_request_filter_values($args['filter_brand'])), 'strlen')));
		sort($selected_brands);
	}

	$has_search = isset($args['s']) && is_scalar($args['s']) && '' !== trim((string) $args['s']);
	$search_term = $has_search ? sanitize_text_field((string) $args['s']) : '';

	$has_min = isset($args['min_price']) && is_scalar($args['min_price']) && '' !== trim((string) $args['min_price']);
	$has_max = isset($args['max_price']) && is_scalar($args['max_price']) && '' !== trim((string) $args['max_price']);
	$min_p = $has_min ? max(0, floatval($args['min_price'])) : 0;
	$max_p = $has_max ? max(0, floatval($args['max_price'])) : 0;
	$price_impossible = ($has_min && $has_max && $max_p < $min_p);

	$in_stock_only = isset($args['in_stock_only']) && is_scalar($args['in_stock_only']) && '1' === (string) $args['in_stock_only'];
	$hide_out_of_stock = ('yes' === get_option('woocommerce_hide_out_of_stock_items'));

	$index_source = function_exists('ashrafco_get_product_filter_index') ? 'core' : 'theme';
	$version = preg_replace('/[^0-9A-Za-z_-]/', '', (string) test_theme_get_catalog_counts_version());
	$cache_args = array(
		'cats' => $category_key,
		'archive' => is_product_category() ? (int) get_queried_object_id() : 0,
		'countries' => $country_filter_invalid ? array('__invalid__') : $selected_countries,
		'classes' => $selected_classes,
		'brands' => $selected_brands,
		'min' => $has_min ? number_format($min_p, 6, '.', '') : '',
		'max' => $has_max ? number_format($max_p, 6, '.', '') : '',
		'stock_only' => $in_stock_only ? 1 : 0,
		'hide_out_of_stock' => $hide_out_of_stock ? 1 : 0,
		'impossible_price' => $price_impossible ? 1 : 0,
	);
	$cache_key = 'ashrafco_fc_' . $version . '_' . $index_source . '_' . md5((string) wp_json_encode($cache_args));

	// Search results are user-specific and can generate unbounded keys.
	if (!$has_search) {
		$cached = get_transient($cache_key);
		if (is_array($cached) && isset($cached['instock'], $cached['countries'], $cached['classifications'])) {
			return $cached;
		}
	}

	if ($country_filter_invalid || $price_impossible) {
		if (!$has_search) {
			set_transient($cache_key, $zero_result, 6 * HOUR_IN_SECONDS);
		}
		return $zero_result;
	}

	$search_matched_ids = null;
	if ($has_search) {
		// Bounded query for search terms instead of unbounded -1.
		$search_query = new WP_Query(array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'posts_per_page' => 500,
			'fields' => 'ids',
			'no_found_rows' => true,
			's' => $search_term,
		));
		$search_matched_ids = array_flip(array_map('intval', $search_query->posts));
	}

	$index = test_theme_get_product_filter_index();
	$all_products = isset($index['products']) ? $index['products'] : array();

	$country_counts = array_fill_keys($country_keys, 0);
	$classification_counts = array_fill_keys($classification_keys, 0);
	$instock_count = 0;

	foreach ($all_products as $pid => $pinfo) {
		$pinfo = is_array($pinfo) ? $pinfo : array();

		// Respect WooCommerce catalog visibility.
		if ($has_search) {
			if (!empty($pinfo['search_hidden'])) {
				continue;
			}
		} elseif (!empty($pinfo['catalog_hidden']) || !empty($pinfo['hidden'])) {
			continue;
		}

		// Strict stock status, matching the _stock_status query clause.
		if (isset($pinfo['stock_status'])) {
			$stock_status = (string) $pinfo['stock_status'];
		} else {
			$product = function_exists('wc_get_product') ? wc_get_product($pid) : null;
			$stock_status = $product instanceof \WC_Product
				? (string) $product->get_stock_status()
				: (!empty($pinfo['is_instock']) ? 'instock' : 'outofstock');
		}
		if ($hide_out_of_stock && 'outofstock' === $stock_status) {
			continue;
		}
		$product_is_instock = ('instock' === $stock_status);

		// Search keyword filter.
		if ($has_search && !isset($search_matched_ids[$pid])) {
			continue;
		}

		// Category filter.
		if (!empty($target_cats) && (empty($pinfo['cats']) || !array_intersect($target_cats, (array) $pinfo['cats']))) {
			continue;
		}

		// Price range filter (with strict min > max boundary validation).
		$price = isset($pinfo['price']) ? (float) $pinfo['price'] : 0.0;
		if ($has_min && $has_max) {
			if ($price < $min_p || $price > $max_p) {
				continue;
			}
		} elseif ($has_min) {
			if ($price < $min_p) {
				continue;
			}
		} elseif ($has_max) {
			if ($price > $max_p) {
				continue;
			}
		}

		// Brand filter.
		if (!empty($selected_brands)) {
			$product_brands = isset($pinfo['brands']) && is_array($pinfo['brands'])
				? $pinfo['brands']
				: test_theme_get_product_brand_tokens($pid);
			if (!array_intersect($selected_brands, $product_brands)) {
				continue;
			}
		}

		$product_country = isset($pinfo['country']) ? $pinfo['country'] : null;
		$product_class = isset($pinfo['class']) ? (string) $pinfo['class'] : 'أخرى';
		if (!isset($classification_counts[$product_class])) {
			$product_class = 'أخرى';
		}

		$matches_stock = !$in_stock_only || $product_is_instock;
		$matches_country = (empty($selected_countries) || in_array($product_country, $selected_countries, true));
		$matches_class = (empty($selected_classes) || in_array($product_class, $selected_classes, true));

		// In-stock facet: ignore its own filter, apply country + classification.
		if ($matches_country && $matches_class && $product_is_instock) {
			$instock_count++;
		}

		// Country facet: ignore country, apply stock + classification.
		if ($matches_stock && $matches_class && null !== $product_country && isset($country_counts[$product_country])) {
			$country_counts[$product_country]++;
		}

		// Classification facet: ignore classification, apply stock + country.
		if ($matches_stock && $matches_country) {
			$classification_counts[$product_class]++;
		}
	}

	$result = array(
		'instock' => $instock_count,
		'countries' => $country_counts,
		'classifications' => $classification_counts,
	);

	if (!$has_search) {
		set_transient($cache_key, $result, 6 * HOUR_IN_SECONDS);
	}

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
	if (isset($_GET['filter_country'])) {
		$countries = array_filter(array_map('sanitize_text_field', test_theme_get_request_filter_values(wp_unslash($_GET['filter_country']))), 'strlen');

		if (!empty($countries)) {
			// Unknown values return an empty ID list, which becomes post__in [0].
			$country_ids = test_theme_get_product_ids_by_country($countries);
			$matching_ids = $country_ids;
			$restrict_ids = true;
		}
	}

	// Classification Filter
	if (isset($_GET['filter_classification'])) {
		$classes = array_filter(array_map('sanitize_text_field', test_theme_get_request_filter_values(wp_unslash($_GET['filter_classification']))), 'strlen');

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
	if (isset($_GET['filter_cat'])) {
		$cat_slugs = array_filter(array_map('sanitize_title', test_theme_get_request_filter_values(wp_unslash($_GET['filter_cat']))), 'strlen');

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
	if (isset($_GET['filter_brand'])) {
		$brand_slugs = array_filter(array_map('sanitize_title', test_theme_get_request_filter_values(wp_unslash($_GET['filter_brand']))), 'strlen');

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

/**
 * ============================================================================
 * Ben10 Watch Catalog Safety & Query Helpers
 * ============================================================================
 */

/**
 * Determine if a product is an authentic watch product rather than legacy industrial inventory.
 *
 * @param int|WC_Product $product
 * @return bool
 */
function ben10_is_watch_product($product)
{
	if (is_numeric($product)) {
		$product = function_exists('wc_get_product') ? wc_get_product($product) : null;
	}
	if (!$product || !is_a($product, 'WC_Product')) {
		return false;
	}

	$product_id = $product->get_id();

	// Check SKU prefix for legacy products (e.g. ASH-*)
	$sku = (string) $product->get_sku();
	if (0 === strpos($sku, 'ASH-')) {
		return false;
	}

	// Check if the product has any legacy tool type meta
	$tool_type = get_post_meta($product_id, '_test_theme_tool_type', true);
	if (!empty($tool_type)) {
		return false;
	}

	// Check product categories
	$term_names = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
	if (!is_wp_error($term_names) && !empty($term_names)) {
		$legacy_cat_indicators = array('سيراميك', 'بورسلين', 'بلاط', 'ليزر', 'قص', 'شواخص', 'موازين', 'أدوات يدوية', 'أدوات كهربائية', 'معدات');
		foreach ($term_names as $name) {
			foreach ($legacy_cat_indicators as $indicator) {
				if (false !== mb_stripos($name, $indicator)) {
					return false;
				}
			}
		}
	}

	// Check title for industrial keywords
	$title = $product->get_name();
	$industrial_keywords = array('ماكينة', 'كليبسات', 'ميزان', 'قص', 'بورسلين', 'سيراميك', 'صاروخ', 'بنطة', 'ديسك');
	foreach ($industrial_keywords as $kw) {
		if (false !== mb_stripos($title, $kw)) {
			return false;
		}
	}

	// Check for watch category or watch indicator
	$watch_indicators = array('ساعة', 'ساعات', 'watch', 'watches', 'timepiece', 'chronograph');
	if (!is_wp_error($term_names) && !empty($term_names)) {
		foreach ($term_names as $name) {
			foreach ($watch_indicators as $wi) {
				if (false !== mb_stripos($name, $wi)) {
					return true;
				}
			}
		}
	}
	foreach ($watch_indicators as $wi) {
		if (false !== mb_stripos($title, $wi)) {
			return true;
		}
	}

	return false;
}

/**
 * Query authentic Ben10 watch products.
 *
 * @param array $args Query arguments
 * @return WP_Query
 */
function ben10_get_watch_products($args = array())
{
	$limit = isset($args['limit']) ? (int) $args['limit'] : 8;
	$orderby = isset($args['orderby']) ? $args['orderby'] : 'date';
	$order = isset($args['order']) ? $args['order'] : 'DESC';

	// Query candidates
	$query_args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'orderby'        => $orderby,
		'order'          => $order,
		'no_found_rows'  => true,
	);

	$candidates = new WP_Query($query_args);
	$watch_ids = array();

	if ($candidates->have_posts()) {
		foreach ($candidates->posts as $post) {
			if (ben10_is_watch_product($post->ID)) {
				$watch_ids[] = $post->ID;
				if (count($watch_ids) >= $limit) {
					break;
				}
			}
		}
	}

	if (empty($watch_ids)) {
		// Return an empty WP_Query
		return new WP_Query(array('post__in' => array(0)));
	}

	return new WP_Query(array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'post__in'       => $watch_ids,
		'orderby'        => 'post__in',
		'posts_per_page' => count($watch_ids),
		'no_found_rows'  => true,
	));
}

/**
 * Retrieve authentic Ben10 watch categories (excluding legacy industrial categories).
 *
 * @return array
 */
function ben10_get_watch_categories()
{
	$terms = get_terms(array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
	));

	if (is_wp_error($terms) || empty($terms)) {
		return array();
	}

	$legacy_slugs = array(
		'uncategorized',
		'tile-leveling-clips',
		'laser-levels',
		'tile-cutters',
	);

	$legacy_name_indicators = array('سيراميك', 'بورسلين', 'بلاط', 'ليزر', 'قص', 'شواخص', 'موازين', 'أدوات', 'معدات');

	$watch_categories = array();
	foreach ($terms as $term) {
		if (in_array($term->slug, $legacy_slugs, true)) {
			continue;
		}

		$is_legacy = false;
		foreach ($legacy_name_indicators as $indicator) {
			if (false !== mb_stripos($term->name, $indicator)) {
				$is_legacy = true;
				break;
			}
		}

		if ($is_legacy) {
			continue;
		}

		// Must match watch indicators
		$watch_indicators = array('ساعة', 'ساعات', 'watch', 'timepiece', 'chronograph', 'كلاسيك', 'رياض');
		$is_watch = false;
		foreach ($watch_indicators as $wi) {
			if (false !== mb_stripos($term->name, $wi) || false !== mb_stripos($term->slug, $wi)) {
				$is_watch = true;
				break;
			}
		}

		if ($is_watch) {
			$watch_categories[] = array(
				'id'    => $term->term_id,
				'name'  => $term->name,
				'slug'  => $term->slug,
				'count' => $term->count,
				'link'  => get_term_link($term),
			);
		}
	}

	return $watch_categories;
}