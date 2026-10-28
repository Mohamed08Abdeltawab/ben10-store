/**
 * Product & Category Admin Helper Scripts
 *
 * Handles category thumbnail preview enhancements without inline script blocks.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

jQuery(document).ready(function($) {
	// Category Image Uploader instant preview enhancement
	var $thumbnailIdField = $('#product_cat_thumbnail_id');
	var $imageWrapper     = $('#product_cat_thumbnail');

	if ($thumbnailIdField.length && $imageWrapper.length) {
		$imageWrapper.addClass('ashrafco-cat-thumbnail-preview');
	}
});
