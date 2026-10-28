<?php
/**
 * The footer template for Custom Store Boilerplate
 *
 * Closes the #content and #page div wrappers and includes wp_footer()
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;
?>
	</div><!-- #content -->

	<?php
	/**
	 * Hook: custom_store_before_footer.
	 */
	do_action('custom_store_before_footer');

	// Render modular footer component if present.
	if (file_exists(get_template_directory() . '/template-parts/footer/site-footer.php')) {
		get_template_part('template-parts/footer/site-footer');
	} else {
		get_template_part('template-parts/footer');
	}

	/**
	 * Hook: custom_store_after_footer.
	 */
	do_action('custom_store_after_footer');
	?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>