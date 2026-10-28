<?php
/**
 * Product Custom Meta & Technical Specifications Architecture
 *
 * Streamlines adding and managing ceramic and tiling tools in WooCommerce.
 * Separation of presentation & logic enforced: zero inline styles or scripts.
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

/**
 * ============================================================================
 * 1. Enqueue Admin Metabox Assets (CSS & JS Decoupled)
 * ============================================================================
 */

/**
 * Enqueue dedicated stylesheets and scripts on product and category admin screens.
 *
 * @param string $hook_suffix Admin page hook suffix.
 */
function ashrafco_enqueue_product_admin_assets($hook_suffix) {
	$screen = get_current_screen();
	if (!$screen) {
		return;
	}

	$is_product_screen = ('product' === $screen->post_type);
	$is_product_cat    = ('product_cat' === $screen->taxonomy);

	if (!$is_product_screen && !$is_product_cat) {
		return;
	}

	$theme_uri = get_template_directory_uri();
	$theme_dir = get_template_directory();

	// 1. Enqueue Admin Metabox CSS
	$css_file = '/assets/src/css/pages/admin-metabox.css';
	$css_ver  = file_exists($theme_dir . $css_file) ? (string) filemtime($theme_dir . $css_file) : CUSTOM_STORE_VERSION;

	wp_enqueue_style(
		'ashrafco_admin_metabox_styles',
		$theme_uri . $css_file,
		array(),
		$css_ver
	);

	// 2. Enqueue Media uploader on taxonomy screen
	if ($is_product_cat) {
		wp_enqueue_media();
	}

	// 3. Enqueue Admin Helper JS
	$js_file = '/assets/src/js/admin-metabox.js';
	$js_ver  = file_exists($theme_dir . $js_file) ? (string) filemtime($theme_dir . $js_file) : CUSTOM_STORE_VERSION;

	wp_enqueue_script(
		'ashrafco_admin_metabox_scripts',
		$theme_uri . $js_file,
		array('jquery'),
		$js_ver,
		true
	);
}
add_action('admin_enqueue_scripts', 'ashrafco_enqueue_product_admin_assets', 20);

/**
 * Backward compatibility hook for category media.
 */
function test_theme_enqueue_category_media() {
	$screen = get_current_screen();
	if ($screen && 'product_cat' === $screen->taxonomy) {
		wp_enqueue_media();
	}
}

/**
 * Backward compatibility stubs for inline style/script hooks (now handled by asset pipeline).
 */
function test_theme_admin_highlight_product_fields() {
	// Replaced by assets/src/css/pages/admin-metabox.css
}

function test_theme_category_image_preview_script() {
	// Replaced by assets/src/js/admin-metabox.js
}

/**
 * ============================================================================
 * 2. Custom Meta Box: Tiling Technical Specifications
 * ============================================================================
 */

/**
 * Register the Technical Specifications Meta Box for Products.
 */
function test_theme_add_product_specs_metabox() {
	add_meta_box(
		'test_theme_product_specs_box',
		esc_html__('المواصفات الفنية لمعدات وأدوات السيراميك والبورسلين', 'ashrafco'),
		'test_theme_render_product_specs_metabox',
		'product',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'test_theme_add_product_specs_metabox');

/**
 * Render the Technical Specifications Meta Box Form Fields.
 * Clean semantic markup with zero inline styles.
 *
 * @param WP_Post $post Current product post object.
 */
function test_theme_render_product_specs_metabox($post) {
	$product_id = $post->ID;

	// Retrieve saved values with modern standard attribute precedence
	$country_origin = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'country_of_origin') ?: get_post_meta($product_id, '_test_theme_country_of_origin', true)) : get_post_meta($product_id, '_test_theme_country_of_origin', true);
	$max_cut_length = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'max_cut_length') ?: get_post_meta($product_id, '_test_theme_max_cut_length', true)) : get_post_meta($product_id, '_test_theme_max_cut_length', true);
	$weight         = get_post_meta($product_id, '_test_theme_weight', true);
	$warranty       = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'warranty') ?: get_post_meta($product_id, '_test_theme_warranty', true)) : get_post_meta($product_id, '_test_theme_warranty', true);
	$spare_parts    = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'spare_parts') ?: get_post_meta($product_id, '_test_theme_spare_parts', true)) : get_post_meta($product_id, '_test_theme_spare_parts', true);
	$max_thickness  = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'max_thickness') ?: get_post_meta($product_id, '_test_theme_max_thickness', true)) : get_post_meta($product_id, '_test_theme_max_thickness', true);

	// Security nonce
	wp_nonce_field('test_theme_save_specs_action', 'test_theme_specs_nonce');
	?>
	<div class="test-theme-admin-specs-wrap" dir="rtl">

		<div class="test-theme-specs-intro">
			<h4 class="test-theme-specs-intro-title">
				🛠️ <?php esc_html_e('بطاقة المواصفات الفنية السريعة', 'ashrafco'); ?>
			</h4>
			<p class="test-theme-specs-intro-desc">
				<?php esc_html_e('أدخل مواصفات المعدة لتظهر فوراً في جدول المواصفات الفنية بصفحة المنتج في المتجر لتسهيل اتخاذ قرار الشراء للعميل.', 'ashrafco'); ?>
			</p>
		</div>

		<div class="test-theme-specs-grid">

			<!-- 1. بلد الصنع والمنشأ -->
			<div class="test-theme-field-group">
				<label for="_test_theme_country_of_origin">
					🌍 <?php esc_html_e('بلد الصنع والمنشأ:', 'ashrafco'); ?>
				</label>
				<input
					type="text"
					id="_test_theme_country_of_origin"
					name="_test_theme_country_of_origin"
					value="<?php echo esc_attr($country_origin); ?>"
					list="test_theme_countries_list"
					placeholder="<?php esc_attr_e('مثال: إيطاليا، إسبانيا، الصين، محلي', 'ashrafco'); ?>"
				/>
				<datalist id="test_theme_countries_list">
					<option value="إيطاليا">
					<option value="إسبانيا">
					<option value="ألمانيا">
					<option value="الصين (درجة أولى)">
					<option value="محلي (صناعة مصرية)">
					<option value="تايوان">
					<option value="اليابان">
				</datalist>
			</div>

			<!-- 2. أقصى طول للقص / المقاس -->
			<div class="test-theme-field-group">
				<label for="_test_theme_max_cut_length">
					📏 <?php esc_html_e('أقصى طول للقص / المقاس:', 'ashrafco'); ?>
				</label>
				<input
					type="text"
					id="_test_theme_max_cut_length"
					name="_test_theme_max_cut_length"
					value="<?php echo esc_attr($max_cut_length); ?>"
					list="test_theme_cut_lengths_list"
					placeholder="<?php esc_attr_e('مثال: 120 سم، 80 سم، 160 سم', 'ashrafco'); ?>"
				/>
				<datalist id="test_theme_cut_lengths_list">
					<option value="60 سم">
					<option value="80 سم">
					<option value="100 سم">
					<option value="120 سم">
					<option value="140 سم">
					<option value="160 سم">
					<option value="180 سم">
				</datalist>
			</div>

			<!-- 3. الوزن التقريبي -->
			<div class="test-theme-field-group">
				<label for="_test_theme_weight">
					⚖️ <?php esc_html_e('الوزن التقريبي للمعدة:', 'ashrafco'); ?>
				</label>
				<input
					type="text"
					id="_test_theme_weight"
					name="_test_theme_weight"
					value="<?php echo esc_attr($weight); ?>"
					placeholder="<?php esc_attr_e('مثال: 14 كجم، 8.5 كجم', 'ashrafco'); ?>"
				/>
			</div>

			<!-- 4. فترة الضمان -->
			<div class="test-theme-field-group">
				<label for="_test_theme_warranty">
					🛡️ <?php esc_html_e('فترة الضمان:', 'ashrafco'); ?>
				</label>
				<input
					type="text"
					id="_test_theme_warranty"
					name="_test_theme_warranty"
					value="<?php echo esc_attr($warranty); ?>"
					list="test_theme_warranty_list"
					placeholder="<?php esc_attr_e('مثال: سنة ضمان معتمد، 6 أشهر، بدون ضمان', 'ashrafco'); ?>"
				/>
				<datalist id="test_theme_warranty_list">
					<option value="سنة ضمان معتمد">
					<option value="سنتان ضمان شامل">
					<option value="6 أشهر ضد عيوب الصناعة">
					<option value="ضمان الوكيل الرسمي">
					<option value="بدون ضمان (قطع استهلاكية)">
				</datalist>
			</div>

			<!-- 5. توفر قطع الغيار -->
			<div class="test-theme-field-group">
				<label for="_test_theme_spare_parts">
					⚙️ <?php esc_html_e('توفر قطع الغيار والصيانة:', 'ashrafco'); ?>
				</label>
				<select
					id="_test_theme_spare_parts"
					name="_test_theme_spare_parts"
				>
					<option value="نعم - متوفرة أصلية بمركز أشرفكو" <?php selected($spare_parts, 'نعم - متوفرة أصلية بمركز أشرفكو'); ?>>نعم - متوفرة أصلية بمركز أشرفكو</option>
					<option value="نعم - متوفرة بالسوق المحلي" <?php selected($spare_parts, 'نعم - متوفرة بالسوق المحلي'); ?>>نعم - متوفرة بالسوق المحلي</option>
					<option value="متوفرة عند الطلب" <?php selected($spare_parts, 'متوفرة عند الطلب'); ?>>متوفرة عند الطلب</option>
					<option value="غير متوفرة" <?php selected($spare_parts, 'غير متوفرة'); ?>>غير متوفرة</option>
				</select>
			</div>

			<!-- 6. أقصى سمك للبلاطة (ملم) -->
			<div class="test-theme-field-group">
				<label for="_test_theme_max_thickness">
					📐 <?php esc_html_e('أقصى سمك للقص (ملم):', 'ashrafco'); ?>
				</label>
				<input
					type="text"
					id="_test_theme_max_thickness"
					name="_test_theme_max_thickness"
					value="<?php echo esc_attr($max_thickness); ?>"
					placeholder="<?php esc_attr_e('مثال: حتى 20 ملم لجميع أنواع البورسلين', 'ashrafco'); ?>"
				/>
			</div>

		</div>

	</div>
	<?php
}

/**
 * Securely Save Technical Specifications Product Meta.
 *
 * @param int $post_id Product post ID.
 */
function test_theme_save_product_specs($post_id) {
	// Verify user permissions
	if (!current_user_can('manage_woocommerce') && !current_user_can('edit_post', $post_id)) {
		return;
	}

	// Verify nonce
	if (!isset($_POST['test_theme_specs_nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['test_theme_specs_nonce'])), 'test_theme_save_specs_action')) {
		return;
	}

	// Avoid autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$fields = array(
		'_test_theme_country_of_origin' => 'sanitize_text_field',
		'_test_theme_max_cut_length'   => 'sanitize_text_field',
		'_test_theme_weight'           => 'sanitize_text_field',
		'_test_theme_warranty'         => 'sanitize_text_field',
		'_test_theme_spare_parts'      => 'sanitize_text_field',
		'_test_theme_max_thickness'    => 'sanitize_text_field',
		'_test_theme_laser_power'      => 'sanitize_text_field',
	);

	foreach ($fields as $meta_key => $sanitize_callback) {
		if (isset($_POST[$meta_key])) {
			$value = call_user_func($sanitize_callback, wp_unslash($_POST[$meta_key]));
			update_post_meta($post_id, $meta_key, $value);
		}
	}

	// Synchronize standard WooCommerce product attributes if attribute migrator is active
	if (function_exists('ashrafco_attribute_migrator') && ashrafco_attribute_migrator()) {
		ashrafco_attribute_migrator()->migrate_product($post_id, false);
	}
}
add_action('woocommerce_process_product_meta', 'test_theme_save_product_specs', 10, 1);

/**
 * ============================================================================
 * 3. Simplify Default Product Data Tabs & Highlight Price/SKU
 * ============================================================================
 */

/**
 * Reorder and simplify product data tabs for heavy industrial tools.
 *
 * @param array $tabs Registered product data tabs.
 * @return array Filtered product tabs.
 */
function test_theme_simplify_product_data_tabs($tabs) {
	if (isset($tabs['general'])) {
		$tabs['general']['priority'] = 5;
	}
	if (isset($tabs['inventory'])) {
		$tabs['inventory']['priority'] = 10;
	}
	if (isset($tabs['shipping'])) {
		$tabs['shipping']['priority'] = 15;
	}

	if (isset($tabs['linked_product'])) {
		unset($tabs['linked_product']);
	}
	if (isset($tabs['advanced'])) {
		unset($tabs['advanced']);
	}

	return $tabs;
}
add_filter('woocommerce_product_data_tabs', 'test_theme_simplify_product_data_tabs', 99);
