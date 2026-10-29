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
		esc_html__('المواصفات الفنية للساعات والمنتجات - Ben10', 'ashrafco'),
		'test_theme_render_product_specs_metabox',
		'product',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'test_theme_add_product_specs_metabox');

/**
 * Render the Technical Specifications Meta Box Form Fields.
 *
 * @param WP_Post $post Current product post object.
 */
function test_theme_render_product_specs_metabox($post) {
	$product_id = $post->ID;

	// Retrieve core watch specifications
	$country_origin    = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'country_of_origin') ?: get_post_meta($product_id, '_test_theme_country_of_origin', true)) : get_post_meta($product_id, '_test_theme_country_of_origin', true);
	$warranty          = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'warranty') ?: get_post_meta($product_id, '_test_theme_warranty', true)) : get_post_meta($product_id, '_test_theme_warranty', true);
	$weight            = get_post_meta($product_id, '_test_theme_weight', true);
	$case_material     = get_post_meta($product_id, '_test_theme_case_material', true) ?: get_post_meta($product_id, '_watch_case_material', true);
	$band_material     = get_post_meta($product_id, '_test_theme_band_material', true) ?: get_post_meta($product_id, '_watch_band_material', true);
	$movement_type     = get_post_meta($product_id, '_test_theme_movement_type', true) ?: get_post_meta($product_id, '_watch_movement_type', true);
	$water_resistance  = get_post_meta($product_id, '_test_theme_water_resistance', true) ?: get_post_meta($product_id, '_watch_water_resistance', true);
	$case_diameter     = get_post_meta($product_id, '_test_theme_case_diameter', true) ?: get_post_meta($product_id, '_watch_case_diameter', true);
	$glass_type        = get_post_meta($product_id, '_test_theme_glass_type', true) ?: get_post_meta($product_id, '_watch_glass_type', true);
	$dial_color        = get_post_meta($product_id, '_test_theme_dial_color', true) ?: get_post_meta($product_id, '_watch_dial_color', true);
	$watch_color       = get_post_meta($product_id, '_test_theme_watch_color', true) ?: get_post_meta($product_id, '_watch_color', true);
	$target_audience   = get_post_meta($product_id, '_test_theme_target_audience', true) ?: get_post_meta($product_id, '_watch_target_audience', true);
	$pkg_contents      = function_exists('ashrafco_get_product_spec') ? (ashrafco_get_product_spec($product_id, 'package-contents') ?: get_post_meta($product_id, '_test_theme_package_contents', true)) : get_post_meta($product_id, '_test_theme_package_contents', true);

	// Legacy industrial fields check
	$max_cut_length    = get_post_meta($product_id, '_test_theme_max_cut_length', true);
	$spare_parts       = get_post_meta($product_id, '_test_theme_spare_parts', true);
	$max_thickness     = get_post_meta($product_id, '_test_theme_max_thickness', true);

	wp_nonce_field('test_theme_save_specs_action', 'test_theme_specs_nonce');
	?>
	<div class="test-theme-admin-specs-wrap" dir="rtl">
		<div class="test-theme-specs-intro">
			<h4 class="test-theme-specs-intro-title">
				⌚ <?php esc_html_e('بطاقة المواصفات الفنية للساعات', 'ashrafco'); ?>
			</h4>
			<p class="test-theme-specs-intro-desc">
				<?php esc_html_e('أدخل المواصفات الأساسية للساعة لتظهر في جدول المواصفات الفنية بمتجر Ben10.', 'ashrafco'); ?>
			</p>
		</div>
		<div class="test-theme-specs-grid">
			<!-- 1. بلد الصنع والمنشأ -->
			<div class="test-theme-field-group">
				<label for="_test_theme_country_of_origin">🌍 <?php esc_html_e('بلد الصنع والمنشأ:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_country_of_origin" name="_test_theme_country_of_origin" value="<?php echo esc_attr($country_origin); ?>" list="test_theme_countries_list" placeholder="سويسرا، اليابان، ألمانيا..." />
				<datalist id="test_theme_countries_list">
					<option value="سويسرا (Switzerland)">
					<option value="اليابان (Japan)">
					<option value="ألمانيا (Germany)">
					<option value="إيطاليا (Italy)">
					<option value="فرنسا (France)">
					<option value="المملكة المتحدة (UK)">
					<option value="الولايات المتحدة (USA)">
					<option value="الصين (درجة أولى)">
				</datalist>
			</div>
			<!-- 2. فترة الضمان -->
			<div class="test-theme-field-group">
				<label for="_test_theme_warranty">🛡️ <?php esc_html_e('فترة الضمان:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_warranty" name="_test_theme_warranty" value="<?php echo esc_attr($warranty); ?>" list="test_theme_warranty_list" placeholder="سنتان ضمان شامل، سنة واحدة..." />
				<datalist id="test_theme_warranty_list">
					<option value="سنتان ضمان شامل">
					<option value="سنة واحدة ضمان معتمد">
					<option value="3 سنوات ضمان دولي">
					<option value="5 سنوات ضمان الصانع">
					<option value="6 أشهر ضد عيوب الصناعة">
					<option value="بدون ضمان">
				</datalist>
			</div>
			<!-- 3. مادة الهيكل -->
			<div class="test-theme-field-group">
				<label for="_test_theme_case_material">🛡️ <?php esc_html_e('مادة الهيكل:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_case_material" name="_test_theme_case_material" value="<?php echo esc_attr($case_material); ?>" list="test_theme_case_mat_list" placeholder="ستانلس ستيل 316L، تيتانيوم..." />
				<datalist id="test_theme_case_mat_list">
					<option value="ستانلس ستيل 316L">
					<option value="تيتانيوم فائق الخفة">
					<option value="سيراميك عالي الصلابة">
					<option value="كربون فايبر">
					<option value="ذهب أصفر عيار 18 قيراط">
					<option value="ذهب وردي عيار 18 قيراط">
				</datalist>
			</div>
			<!-- 4. مادة السوار -->
			<div class="test-theme-field-group">
				<label for="_test_theme_band_material">🎗️ <?php esc_html_e('مادة السوار:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_band_material" name="_test_theme_band_material" value="<?php echo esc_attr($band_material); ?>" list="test_theme_band_mat_list" placeholder="جلد طبيعي فاخر، ستانلس ستيل..." />
				<datalist id="test_theme_band_mat_list">
					<option value="جلد طبيعي فاخر">
					<option value="ستانلس ستيل مصمت">
					<option value="سيليكون رياضي مرن">
					<option value="نايلون عسكري الناتو (NATO)">
					<option value="تيتانيوم فائق المتانة">
					<option value="مطاط فائق التحمل">
				</datalist>
			</div>
			<!-- 5. نوع الحركة -->
			<div class="test-theme-field-group">
				<label for="_test_theme_movement_type">⚙️ <?php esc_html_e('نوع الحركة:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_movement_type" name="_test_theme_movement_type" value="<?php echo esc_attr($movement_type); ?>" list="test_theme_movement_list" placeholder="أوتوماتيك ميكانيكية، كوارتز ياباني..." />
				<datalist id="test_theme_movement_list">
					<option value="أوتوماتيك ميكانيكية ذاتية التعبئة">
					<option value="كوارتز ياباني عالي الدقة">
					<option value="كوارتز سويسري فائق الدقة">
					<option value="ميكانيكية تعبئة يدوية">
					<option value="شحن شمسي / ضوئي (Solar)">
					<option value="رقمي ذكي (Smart Digital)">
				</datalist>
			</div>
			<!-- 6. مقاومة الماء -->
			<div class="test-theme-field-group">
				<label for="_test_theme_water_resistance">💧 <?php esc_html_e('مقاومة الماء:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_water_resistance" name="_test_theme_water_resistance" value="<?php echo esc_attr($water_resistance); ?>" list="test_theme_wr_list" placeholder="5ATM / 50m، 10ATM / 100m..." />
				<datalist id="test_theme_wr_list">
					<option value="مقاومة للرذاذ والغسيل (3ATM / 30m)">
					<option value="مقاومة للسباحة السطحية (5ATM / 50m)">
					<option value="مقاومة للغوص والرياضات المائية (10ATM / 100m)">
					<option value="ساعة غوص احترافية (20ATM / 200m)">
					<option value="غير مقاومة للماء">
				</datalist>
			</div>
			<!-- 7. قطر الهيكل -->
			<div class="test-theme-field-group">
				<label for="_test_theme_case_diameter">📐 <?php esc_html_e('قطر الهيكل (مم):', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_case_diameter" name="_test_theme_case_diameter" value="<?php echo esc_attr($case_diameter); ?>" placeholder="42 مم" />
			</div>
			<!-- 8. نوع الزجاج -->
			<div class="test-theme-field-group">
				<label for="_test_theme_glass_type">🔍 <?php esc_html_e('نوع الزجاج:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_glass_type" name="_test_theme_glass_type" value="<?php echo esc_attr($glass_type); ?>" list="test_theme_glass_list" placeholder="زجاج ياقوتي (سافاير)..." />
				<datalist id="test_theme_glass_list">
					<option value="زجاج ياقوتي مقاوم للخدش (سافاير Sapphire)">
					<option value="زجاج كريستال معدني مقوى (Mineral Crystal)">
					<option value="زجاج هاردليكس (Hardlex)">
					<option value="أكريليك / بليكسي جلاس كلاسيكي">
				</datalist>
			</div>
			<!-- 9. لون الميناء ولون الساعة -->
			<div class="test-theme-field-group">
				<label for="_test_theme_dial_color">🎨 <?php esc_html_e('لون الميناء:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_dial_color" name="_test_theme_dial_color" value="<?php echo esc_attr($dial_color); ?>" placeholder="أسود، أزرق سنبراش، أبيض لؤلؤي..." />
			</div>
			<div class="test-theme-field-group">
				<label for="_test_theme_watch_color">✨ <?php esc_html_e('لون الساعة / الهيكل:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_watch_color" name="_test_theme_watch_color" value="<?php echo esc_attr($watch_color); ?>" placeholder="فضي، أسود مطفي، ذهبي وردي..." />
			</div>
			<!-- 10. الفئة المستهدفة والوزن -->
			<div class="test-theme-field-group">
				<label for="_test_theme_target_audience">👥 <?php esc_html_e('الفئة المستهدفة:', 'ashrafco'); ?></label>
				<select id="_test_theme_target_audience" name="_test_theme_target_audience">
					<option value="رجالي" <?php selected($target_audience, 'رجالي'); ?>>رجالي</option>
					<option value="نسائي" <?php selected($target_audience, 'نسائي'); ?>>نسائي</option>
					<option value="للجنسين (Unisex)" <?php selected($target_audience, 'للجنسين (Unisex)'); ?>>للجنسين (Unisex)</option>
					<option value="شبابي / أطفال" <?php selected($target_audience, 'شبابي / أطفال'); ?>>شبابي / أطفال</option>
				</select>
			</div>
			<div class="test-theme-field-group">
				<label for="_test_theme_weight">⚖️ <?php esc_html_e('الوزن التقريبي (جرام):', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_weight" name="_test_theme_weight" value="<?php echo esc_attr($weight); ?>" placeholder="مثال: 140 جرام" />
			</div>
			<!-- 11. محتويات العبوة -->
			<div class="test-theme-field-group" style="grid-column: 1 / -1;">
				<label for="_test_theme_package_contents">📦 <?php esc_html_e('محتويات العبوة:', 'ashrafco'); ?></label>
				<input type="text" id="_test_theme_package_contents" name="_test_theme_package_contents" value="<?php echo esc_attr($pkg_contents); ?>" placeholder="علبة الساعة الأصلية الفاخرة + بطاقة الضمان والشهادة + كتيب التعليمات" />
			</div>
		</div>

		<?php if (!empty($max_cut_length) || !empty($spare_parts) || !empty($max_thickness)): ?>
		<!-- Legacy Industrial Specifications (Preserved for existing products) -->
		<details style="margin-top: 15px; border-top: 1px dashed #cbd5e1; padding-top: 10px;">
			<summary style="cursor: pointer; font-size: 12px; color: #64748b;">مواصفات سابقة إضافية (معدات صناعية)</summary>
			<div class="test-theme-specs-grid" style="margin-top: 10px;">
				<?php if (!empty($max_cut_length)): ?>
				<div class="test-theme-field-group">
					<label>أقصى طول للقص:</label>
					<input type="text" name="_test_theme_max_cut_length" value="<?php echo esc_attr($max_cut_length); ?>" />
				</div>
				<?php endif; ?>
				<?php if (!empty($max_thickness)): ?>
				<div class="test-theme-field-group">
					<label>أقصى سمك للقص:</label>
					<input type="text" name="_test_theme_max_thickness" value="<?php echo esc_attr($max_thickness); ?>" />
				</div>
				<?php endif; ?>
				<?php if (!empty($spare_parts)): ?>
				<div class="test-theme-field-group">
					<label>توفر قطع الغيار:</label>
					<input type="text" name="_test_theme_spare_parts" value="<?php echo esc_attr($spare_parts); ?>" />
				</div>
				<?php endif; ?>
			</div>
		</details>
		<?php endif; ?>

	</div>
	<?php
}

/**
 * Securely Save Technical Specifications Product Meta.
 *
 * @param int $post_id Product post ID.
 */
function test_theme_save_product_specs($post_id) {
	if (!current_user_can('manage_woocommerce') && !current_user_can('edit_post', $post_id)) {
		return;
	}
	if (!isset($_POST['test_theme_specs_nonce']) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST['test_theme_specs_nonce'])), 'test_theme_save_specs_action')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$fields = array(
		'_test_theme_country_of_origin' => 'sanitize_text_field',
		'_test_theme_warranty'         => 'sanitize_text_field',
		'_test_theme_weight'           => 'sanitize_text_field',
		'_test_theme_case_material'    => 'sanitize_text_field',
		'_test_theme_band_material'    => 'sanitize_text_field',
		'_test_theme_movement_type'    => 'sanitize_text_field',
		'_test_theme_water_resistance' => 'sanitize_text_field',
		'_test_theme_case_diameter'    => 'sanitize_text_field',
		'_test_theme_glass_type'       => 'sanitize_text_field',
		'_test_theme_dial_color'       => 'sanitize_text_field',
		'_test_theme_watch_color'      => 'sanitize_text_field',
		'_test_theme_target_audience'  => 'sanitize_text_field',
		'_test_theme_package_contents' => 'sanitize_textarea_field',
		// Legacy fields preserved
		'_test_theme_max_cut_length'   => 'sanitize_text_field',
		'_test_theme_spare_parts'      => 'sanitize_text_field',
		'_test_theme_max_thickness'    => 'sanitize_text_field',
		'_test_theme_laser_power'      => 'sanitize_text_field',
	);

	foreach ($fields as $meta_key => $sanitize_callback) {
		if (isset($_POST[$meta_key])) {
			$value = call_user_func($sanitize_callback, wp_unslash($_POST[$meta_key]));
			update_post_meta($post_id, $meta_key, $value);
			// Mirror clean watch keys
			$clean_key = str_replace('_test_theme_', '_watch_', $meta_key);
			if ($clean_key !== $meta_key) {
				update_post_meta($post_id, $clean_key, $value);
			}
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
