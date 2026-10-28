<?php
/**
 * The header template for Custom Store Boilerplate
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site min-h-screen flex flex-col">
	<a class="skip-link screen-reader-text sr-only focus:not-sr-only" href="#primary">
		<?php esc_html_e('تخطي إلى المحتوى الرئيسي', 'custom-store-boilerplate'); ?>
	</a>

	<?php
	/**
	 * Hook: custom_store_before_header.
	 */
	do_action('custom_store_before_header');

	// Render modular header component if present.
	if (file_exists(get_template_directory() . '/template-parts/header/site-header.php')) {
		get_template_part('template-parts/header/site-header');
	} else {
		get_template_part('template-parts/header');
	}

	/**
	 * Hook: custom_store_after_header.
	 */
	do_action('custom_store_after_header');
	?>

	<div id="content" class="site-content flex-1 w-full">