<?php
/**
 * The Template for displaying all single products
 *
 * Override of WooCommerce default template for Ashrafco.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
	the_post();
	wc_get_template_part( 'content', 'single-product' );
endwhile;

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
