<?php
/**
 * Smart Slider 3 Integration for Ashrafco Homepage Hero
 *
 * Uses Smart Slider 3 as the slider engine for the banner carousel.
 * Features a fixed viewport CTA button overlay ("تسوق الآن") positioned
 * relative to the carousel container so it remains completely stationary
 * while banners transition underneath.
 *
 * @package Ashrafco
 * @since 1.1.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * 1. Auto-activate Smart Slider 3 plugin if installed.
 */
function test_theme_ensure_smart_slider_active()
{
	if (!function_exists('is_plugin_active')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if (!is_plugin_active('smart-slider-3/smart-slider-3.php')) {
		activate_plugin('smart-slider-3/smart-slider-3.php');
	}
}
add_action('after_setup_theme', 'test_theme_ensure_smart_slider_active', 5);

/**
 * 2. Helper to verify and provision the default Home Hero slider in Smart Slider 3.
 *
 * @param bool $force Force re-provisioning regardless of existing slides.
 * @return int|false Slider ID
 */
function test_theme_provision_smart_slider_hero($force = false)
{
	global $wpdb;

	$table_sliders = $wpdb->prefix . 'nextend2_smartslider3_sliders';
	$table_slides = $wpdb->prefix . 'nextend2_smartslider3_slides';
	$table_xref = $wpdb->prefix . 'nextend2_smartslider3_sliders_xref';

	// Verify Smart Slider 3 tables exist
	if ($wpdb->get_var("SHOW TABLES LIKE '{$table_sliders}'") !== $table_sliders) {
		return false;
	}

	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

	// Retrieve decoupled hero banners definitions from core catalog provider
	$banner_definitions = function_exists('ashrafco_get_hero_banners')
		? ashrafco_get_hero_banners()
		: array(
			array(
				'filename' => 'hero-power-tools.webp',
				'title' => 'معدات وأدوات كهربائية متطورة - A & S Power Tools',
				'alt' => 'معدات وأدوات كهربائية متطورة - A & S Power Tools',
				'link' => $shop_url,
			),
			array(
				'filename' => 'hero-tile-cutting.webp',
				'title' => 'ماكينات قص وقص ليزر احترافية للسيراميك والبورسلين',
				'alt' => 'ماكينات قص وقص ليزر احترافية للسيراميك والبورسلين',
				'link' => $shop_url,
			),
			array(
				'filename' => 'hero-toolkits.webp',
				'title' => 'أطقم وحقائب عدد وأدوات متكاملة لكافة الاستخدامات',
				'alt' => 'أطقم وحقائب عدد وأدوات متكاملة لكافة الاستخدامات',
				'link' => $shop_url,
			),
		);

	$existing_slider = $wpdb->get_row($wpdb->prepare("SELECT id, alias FROM {$table_sliders} WHERE alias = %s LIMIT 1", 'home-hero'), ARRAY_A);

	// Check if slides already exist without internal moving layers and match current banner files
	if (!$force && !empty($existing_slider) && !empty($existing_slider['id'])) {
		$slides_with_layers = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_slides} WHERE slider = %d AND slide != '[]' AND slide != ''", $existing_slider['id']));
		if (0 === (int) $slides_with_layers) {
			$existing_thumbnails = $wpdb->get_col($wpdb->prepare("SELECT thumbnail FROM {$table_slides} WHERE slider = %d ORDER BY ordering ASC", $existing_slider['id']));

			$is_up_to_date = (count($existing_thumbnails) === count($banner_definitions));
			if ($is_up_to_date) {
				foreach ($banner_definitions as $i => $banner) {
					$expected_url = get_stylesheet_directory_uri() . '/assets/images/hero/' . $banner['filename'];
					if (!isset($existing_thumbnails[$i]) || $existing_thumbnails[$i] !== $expected_url) {
						$is_up_to_date = false;
						break;
					}
				}
			}

			if ($is_up_to_date) {
				update_option('ashrafco_hero_slider_provisioned', 1);
				return (int) $existing_slider['id'];
			}
		}
	}

	// Fetch base template parameters from Tutorial Slider if present or use comprehensive defaults
	$base_params = array();
	$tutorial_slider = $wpdb->get_row("SELECT params FROM {$table_sliders} WHERE id = 1 LIMIT 1", ARRAY_A);
	if (!empty($tutorial_slider['params'])) {
		$decoded = json_decode($tutorial_slider['params'], true);
		if (is_array($decoded)) {
			$base_params = $decoded;
		}
	}

	// Override specific hero carousel configurations per requirements
	$hero_overrides = array(
		'title' => 'سلايدر العروض الرئيسية',
		'type' => 'simple',
		'width' => 864,
		'height' => 398,
		'responsive-mode' => 'auto',
		'responsive-slide-width-max' => 864,
		'responsive-slide-width-min' => 320,
		'responsive-downscale' => 1,
		'responsive-upscale' => 0,
		'carousel' => 1,
		'animation' => 'horizontal',
		'animation-duration' => 600,
		'animation-easing' => 'easeInOutQuad',
		'autoplay' => 1,
		'autoplayDuration' => 4500,
		'autoplayLoop' => 1,
		'autoplayStopMouse' => 1,
		'autoplayResumeMouse' => 1,
		'autoplayStopClick' => 1,
		'autoplayResumeClick' => 1,
		'autoplayStopMedia' => 1,
		'autoplayResumeMedia' => 1,
		'controlsBlockCarouselInteraction' => 1,
		'widget-bullet-enabled' => 1,
		'widgetbullet' => 'transition',
		'widget-bullet-position-mode' => 'simple',
		'widget-bullet-position-area' => 10, // Bottom center
		'widget-bullet-position-offset' => 12,
		'widget-bullet-action' => 'click',
		'widget-bullet-align' => 'center',
		'widget-bullet-style' => '{"data":[{"backgroundcolor":"ffffffcc","padding":"4|*|4|*|4|*|4|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"50","extra":"margin: 3px;"},{"backgroundcolor":"ff171fff"}]}',
		'widget-bullet-display-hover' => 0,
		'widget-bullet-display-mobileportrait' => 1,
		'widget-bullet-display-mobilelandscape' => 1,
		'widget-bullet-display-tabletportrait' => 1,
		'widget-bullet-display-desktopportrait' => 1,
		'widget-arrow-enabled' => 1,
		'widgetarrow' => 'imageEmpty',
		'widget-arrow-display-hover' => 1,
		'widget-arrow-display-mobileportrait' => 0,
		'widget-arrow-display-mobilelandscape' => 0,
		'widget-arrow-display-tabletportrait' => 1,
		'widget-arrow-display-desktopportrait' => 1,
		'widget-arrow-style' => '{"data":[{"backgroundcolor":"00000040","padding":"14|*|8|*|14|*|8|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"8","extra":""},{"backgroundcolor":"00000080"}]}',
		'widget-arrow-previous-color' => 'ffffffff',
		'widget-arrow-next-color' => 'ffffffff',
	);

	$sliderParams = array_merge($base_params, $hero_overrides);
	$now = current_time('mysql');

	if (!empty($existing_slider) && !empty($existing_slider['id'])) {
		$slider_id = (int) $existing_slider['id'];
		$wpdb->update(
			$table_sliders,
			array(
				'title' => 'سلايدر العروض الرئيسية',
				'type' => 'simple',
				'params' => json_encode($sliderParams, JSON_UNESCAPED_SLASHES),
				'time' => $now,
			),
			array('id' => $slider_id)
		);
		$wpdb->delete($table_slides, array('slider' => $slider_id));
	} else {
		$insert_data = array(
			'title' => 'سلايدر العروض الرئيسية',
			'type' => 'simple',
			'params' => json_encode($sliderParams, JSON_UNESCAPED_SLASHES),
			'time' => $now,
			'thumbnail' => '',
			'ordering' => 0,
			'alias' => 'home-hero',
		);
		$wpdb->insert($table_sliders, $insert_data);
		$slider_id = (int) $wpdb->insert_id;

		if ($wpdb->get_var("SHOW TABLES LIKE '{$table_xref}'") === $table_xref) {
			$wpdb->insert(
				$table_xref,
				array(
					'group_id' => 0,
					'slider_id' => $slider_id,
					'ordering' => 0,
				)
			);
		}
	}

	if (!$slider_id) {
		return false;
	}

	require_once WP_PLUGIN_DIR . '/smart-slider-3/Nextend/SmartSlider3/SlideBuilder/BuilderComponentSlide.php';

	foreach ($banner_definitions as $index => $banner) {
		$image_url = get_stylesheet_directory_uri() . '/assets/images/hero/' . $banner['filename'];

		$slideBuilder = new \Nextend\SmartSlider3\SlideBuilder\BuilderComponentSlide(array(
			'title' => $banner['title'],
			'backgroundImage' => $image_url,
			'background-type' => 'image',
			'backgroundMode' => 'fill',
			'backgroundAlt' => $banner['alt'],
			'href' => $banner['link'],
			'href-target' => '_self',
			'ordering' => $index + 1,
		));

		$slideData = $slideBuilder->getData();

		// Keep slide layers empty so slides contain ONLY the banner image and link.
		// The CTA button is positioned as a fixed viewport overlay on the container.
		$wpdb->insert(
			$table_slides,
			array(
				'title' => $banner['title'],
				'slider' => $slider_id,
				'publish_up' => '1970-01-01 00:00:00',
				'publish_down' => '1970-01-01 00:00:00',
				'published' => 1,
				'first' => (0 === $index) ? 1 : 0,
				'slide' => '[]',
				'description' => '',
				'thumbnail' => $image_url,
				'params' => json_encode($slideData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
				'ordering' => $index + 1,
				'generator_id' => 0,
			)
		);
	}

	// Invalidate Smart Slider 3 section cache & image cache for this slider
	$table_storage = $wpdb->prefix . 'nextend2_section_storage';
	if ($wpdb->get_var("SHOW TABLES LIKE '{$table_storage}'") === $table_storage) {
		$wpdb->query($wpdb->prepare("DELETE FROM {$table_storage} WHERE section LIKE %s OR section = 'sliderChanged'", 'notweb/n2-ss-' . $slider_id . '%'));
	}
	$table_image_storage = $wpdb->prefix . 'nextend2_image_storage';
	if ($wpdb->get_var("SHOW TABLES LIKE '{$table_image_storage}'") === $table_image_storage) {
		$wpdb->query($wpdb->prepare("DELETE FROM {$table_image_storage} WHERE image LIKE %s OR image LIKE %s", '%' . $wpdb->esc_like('test-theme/assets/images/banners') . '%', '%' . $wpdb->esc_like('test-theme/assets/images/hero') . '%'));
	}

	if (class_exists('\Nextend\SmartSlider3\SmartSlider3Info')) {
		\Nextend\SmartSlider3\SmartSlider3Info::sliderChanged();
	}

	return $slider_id;
}

// Automatically provision on init ONLY if not already provisioned
add_action('init', function () {
	if (!is_admin() && !defined('DOING_AJAX')) {
		if (!get_option('ashrafco_hero_slider_provisioned')) {
			$provisioned = test_theme_provision_smart_slider_hero();
			if ($provisioned) {
				update_option('ashrafco_hero_slider_provisioned', 1);
			}
		}
	}
}, 20);

/**
 * 3. Render Hero Slider using Smart Slider 3 with fixed viewport CTA button overlay.
 */
function test_theme_render_hero_slider()
{
	$slider_alias = apply_filters('ashrafco_hero_slider_alias', 'home-hero');
	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

	// Attempt Smart Slider 3 shortcode
	$output = '';
	if (shortcode_exists('smartslider3')) {
		$output = do_shortcode('[smartslider3 slider="' . esc_attr($slider_alias) . '"]');
	}

	if (!empty($output)) {
		?>
		<section
			class="hero-smartslider-section mt-3 sm:mt-4 md:mt-5 max-w-[864px] w-full mx-auto px-3.5 sm:px-4 overflow-hidden bg-transparent"
			aria-label="<?php esc_attr_e('لافتات العروض الرئيسية', 'ashrafco'); ?>">
			<div
				class="hero-smartslider-wrapper relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm bg-transparent select-none">
				<?php echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<!-- Fixed Viewport CTA Overlay Button ("تسوق الآن") -->
				<a href="<?php echo esc_url($shop_url); ?>" id="hero-banner-fixed-cta"
					class="hero-banner-fixed-cta-btn absolute z-30" draggable="false"
					aria-label="<?php esc_attr_e('تسوق الآن', 'ashrafco'); ?>">
					<span><?php esc_html_e('تسوق الآن', 'ashrafco'); ?></span>
					<svg class="hero-cta-chevron w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24" aria-hidden="true">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
					</svg>
				</a>
			</div>
		</section>
		<?php
		return;
	}

	// Graceful Fallback if Smart Slider 3 is disabled or empty
	$fallback_img = get_stylesheet_directory_uri() . '/assets/images/hero/hero-power-tools.webp';
	?>
	<section
		class="hero-smartslider-section mt-3 sm:mt-4 md:mt-5 max-w-[864px] w-full mx-auto px-3.5 sm:px-4 overflow-hidden bg-transparent"
		aria-label="<?php esc_attr_e('لافتات العروض الرئيسية', 'ashrafco'); ?>">
		<div
			class="hero-smartslider-wrapper relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm bg-transparent">
			<a href="<?php echo esc_url($shop_url); ?>" class="block w-full h-full">
				<img src="<?php echo esc_url($fallback_img); ?>"
					alt="<?php esc_attr_e('معدات وأدوات كهربائية متطورة - A & S Power Tools', 'ashrafco'); ?>"
					class="w-full h-auto block rounded-2xl sm:rounded-3xl" loading="eager" decoding="async">
			</a>
			<!-- Fixed Viewport CTA Overlay Button ("تسوق الآن") -->
			<a href="<?php echo esc_url($shop_url); ?>" id="hero-banner-fixed-cta"
				class="hero-banner-fixed-cta-btn absolute z-30" draggable="false"
				aria-label="<?php esc_attr_e('تسوق الآن', 'ashrafco'); ?>">
				<span><?php esc_html_e('تسوق الآن', 'ashrafco'); ?></span>
				<svg class="hero-cta-chevron w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" stroke="currentColor"
					viewBox="0 0 24 24" aria-hidden="true">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
				</svg>
			</a>
		</div>
	</section>
	<?php
}
