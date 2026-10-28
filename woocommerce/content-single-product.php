<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override of WooCommerce default template for Ashrafco.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

// Load modern, industrial custom single product layout.
get_template_part( 'template-parts/woocommerce/single-product-custom' );

/**
 * Hook: woocommerce_after_single_product.
 */
do_action( 'woocommerce_after_single_product' );
