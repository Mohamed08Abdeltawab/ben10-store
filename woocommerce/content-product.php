<?php
/**
 * The template for displaying product content within loops
 *
 * This overrides WooCommerce default content-product.php
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

get_template_part( 'template-parts/woocommerce/content-product-card' );
