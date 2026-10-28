<?php
/**
 * Offers & Promotions Engine Module
 *
 * Implements:
 * - Customer-facing Offers / Promotions page canonical routing & template resolution
 * - 301 redirection from legacy /shop/?on_sale=1 queries to /offers/
 * - On-sale products query builder and faceted offers catalog provider
 * - Flash sale countdown timestamp synchronization
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Retrieve canonical URL for customer-facing Offers / Promotions catalog page.
 *
 * @return string Canonical offers page URL.
 */
if (!function_exists('ashrafco_get_offers_url')) {
	function ashrafco_get_offers_url() {
		$page = get_page_by_path('offers');
		if ($page && 'publish' === $page->post_status) {
			return get_permalink($page->ID);
		}

		$page_promo = get_page_by_path('promotions');
		if ($page_promo && 'publish' === $page_promo->post_status) {
			return get_permalink($page_promo->ID);
		}

		return home_url('/offers/');
	}
}

/**
 * Ensure template loader resolves page-offers.php for the offers and promotions routes.
 */
add_filter('template_include', function ($template) {
	if (is_admin() || wp_doing_ajax()) {
		return $template;
	}

	$is_offers_route = is_page('offers') || is_page('promotions') || is_page_template('page-offers.php');

	if (!$is_offers_route && !empty($_SERVER['REQUEST_URI'])) {
		$request_path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
		if ('offers' === $request_path || 'promotions' === $request_path) {
			$is_offers_route = true;
		}
	}

	if ($is_offers_route) {
		$offers_template = get_template_directory() . '/page-offers.php';
		if (file_exists($offers_template)) {
			return $offers_template;
		}
	}

	return $template;
}, 99);

/**
 * Redirect legacy /shop/?on_sale=1 queries directly to designated /offers/ page.
 */
add_action('template_redirect', function () {
	if (is_admin() || wp_doing_ajax()) {
		return;
	}

	if (function_exists('is_shop') && is_shop() && isset($_GET['on_sale']) && '1' === (string) $_GET['on_sale']) {
		wp_safe_redirect(ashrafco_get_offers_url(), 301);
		exit;
	}
});

/**
 * Provide on-sale catalog products data with category buckets, discounts, and sorting.
 */
if (!function_exists('ashrafco_get_on_sale_catalog')) {
	function ashrafco_get_on_sale_catalog($args = array()) {
		$on_sale_ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : array();
		if (empty($on_sale_ids)) {
			return array(
				'items'        => array(),
				'total'        => 0,
				'total_pages'  => 0,
				'current_page' => 1,
				'max_discount' => 0,
				'categories'   => array(),
			);
		}

		$defaults = array(
			'limit'    => 12,
			'page'     => 1,
			'category' => '',
			's'        => '',
			'orderby'  => 'date',
			'order'    => 'DESC',
		);
		$parsed = wp_parse_args($args, $defaults);
		$page   = max(1, absint($parsed['page']));
		$limit  = intval($parsed['limit']);
		if ($limit <= 0 && -1 !== $limit) {
			$limit = 12;
		}

		$query_all = array(
			'status'     => 'publish',
			'include'    => $on_sale_ids,
			'limit'      => -1,
			'visibility' => 'visible',
		);
		if (!empty($parsed['s'])) {
			$query_all['s'] = sanitize_text_field($parsed['s']);
		}

		$all_products = wc_get_products($query_all);
		$items        = array();
		$cat_buckets  = array();
		$max_discount = 0;

		foreach ($all_products as $p) {
			if (!$p instanceof \WC_Product || !$p->is_visible()) {
				continue;
			}
			$d = function_exists('ashrafco_get_product_discount_data') ? ashrafco_get_product_discount_data($p) : array('is_on_sale' => false, 'discount_percent' => 0);
			if (empty($d['is_on_sale'])) {
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
							'id'    => $term->term_id,
							'slug'  => $term->slug,
							'name'  => $term->name,
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
				'product'          => $p,
				'id'               => $p->get_id(),
				'discount_percent' => $d['discount_percent'],
				'price'            => (float) $p->get_price(),
				'date'             => $p->get_date_created() ? $p->get_date_created()->getTimestamp() : 0,
				'popularity'       => (int) $p->get_total_sales(),
				'title'            => $p->get_name(),
				'discount_data'    => $d,
			);
		}

		$orderby = $parsed['orderby'];
		$order   = strtoupper($parsed['order']);
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

		$total       = count($items);
		$total_pages = $limit > 0 ? (int) ceil($total / $limit) : 1;
		$paged_items = $limit > 0 ? array_slice($items, ($page - 1) * $limit, $limit) : $items;

		return array(
			'items'        => $paged_items,
			'total'        => $total,
			'total_pages'  => $total_pages,
			'current_page' => $page,
			'max_discount' => $max_discount,
			'categories'   => array_values($cat_buckets),
		);
	}
}
