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
 * Get the legacy theme's hero asset directory as it appears in slider markup.
 *
 * @return string
 */
function test_theme_get_stale_hero_asset_path()
{
	return '/wp-content/themes/test-theme/assets/images/hero/';
}

/**
 * Check whether a URL is a same-site or relative URL under the stale hero directory.
 *
 * External URLs are deliberately left alone even when their path matches.
 *
 * @param string $url
 * @return bool
 */
function test_theme_is_stale_hero_url($url)
{
	$url = trim((string) $url);
	if ('' === $url) {
		return false;
	}

	$stale_path = test_theme_get_stale_hero_asset_path();
	if (0 === strpos($url, $stale_path)) {
		return true;
	}

	$protocol_relative = (0 === strpos($url, '//'));
	if (!$protocol_relative && 0 !== stripos($url, 'http://') && 0 !== stripos($url, 'https://')) {
		return false;
	}

	$parsed = wp_parse_url($protocol_relative ? 'http:' . $url : $url);
	if (!is_array($parsed) || empty($parsed['host']) || empty($parsed['path'])) {
		return false;
	}

	$home_host = wp_parse_url(home_url('/'), PHP_URL_HOST);
	if (empty($home_host) || 0 !== strcasecmp($home_host, $parsed['host'])) {
		return false;
	}

	return 0 === strpos($parsed['path'], $stale_path);
}

/**
 * Rewrite one stale hero URL to the active theme, preserving query and fragment.
 *
 * Only same-site URLs (or root-relative URLs) are changed; external URLs and
 * unrelated links/text are returned untouched.
 *
 * @param string $url
 * @return string
 */
function test_theme_rewrite_stale_hero_url($url)
{
	$original = (string) $url;
	$url = trim($original);
	if ('' === $url) {
		return $original;
	}

	$stale_path = test_theme_get_stale_hero_asset_path();
	$current_base = get_stylesheet_directory_uri() . '/assets/images/hero/';
	$current_relative = wp_make_link_relative($current_base);

	if (0 === strpos($url, $stale_path)) {
		return $current_relative . substr($url, strlen($stale_path));
	}

	$protocol_relative = (0 === strpos($url, '//'));
	if (!$protocol_relative && 0 !== stripos($url, 'http://') && 0 !== stripos($url, 'https://')) {
		return $original;
	}

	$parsed = wp_parse_url($protocol_relative ? 'http:' . $url : $url);
	if (!is_array($parsed) || empty($parsed['host']) || empty($parsed['path'])) {
		return $original;
	}

	$home_host = wp_parse_url(home_url('/'), PHP_URL_HOST);
	if (empty($home_host) || 0 !== strcasecmp($home_host, $parsed['host'])) {
		return $original;
	}
	if (0 !== strpos($parsed['path'], $stale_path)) {
		return $original;
	}

	$suffix = substr($parsed['path'], strlen($stale_path));
	$query = !empty($parsed['query']) ? '?' . $parsed['query'] : '';
	$fragment = !empty($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

	if ($protocol_relative) {
		$host = $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '');
		return '//' . $host . $current_relative . $suffix . $query . $fragment;
	}

	return $current_base . $suffix . $query . $fragment;
}

/**
 * Rewrite CSS url() values inside a style attribute or style block.
 *
 * @param string $css
 * @return string
 */
function test_theme_rewrite_css_hero_urls($css)
{
	if (!is_string($css) || '' === $css) {
		return $css;
	}

	$rewritten = preg_replace_callback(
		'#(url\(\s*)(["\']?)(.*?)(\2\s*\))#is',
		function ($matches) {
			return $matches[1] . $matches[2] . test_theme_rewrite_stale_hero_url($matches[3]) . $matches[4];
		},
		$css
	);

	return null === $rewritten ? $css : $rewritten;
}

/**
 * Rewrite the URL(s) held by one image-related HTML attribute.
 *
 * @param string $value
 * @param string $attribute
 * @return string
 */
function test_theme_rewrite_hero_image_attribute($value, $attribute)
{
	$value = (string) $value;
	$attribute = strtolower((string) $attribute);

	if (false !== strpos($attribute, 'srcset')) {
		$parts = explode(',', $value);
		foreach ($parts as $index => $part) {
			if (preg_match('#^(\s*)(\S+)(.*)$#s', $part, $matches)) {
				$parts[$index] = $matches[1] . test_theme_rewrite_stale_hero_url($matches[2]) . $matches[3];
			}
		}
		return implode(',', $parts);
	}

	if (false !== stripos($value, 'url(')) {
		return test_theme_rewrite_css_hero_urls($value);
	}

	return test_theme_rewrite_stale_hero_url($value);
}

/**
 * Detect a residual same-site stale hero URL in image/CSS contexts.
 *
 * External URLs containing the same path are not residual failures and are
 * intentionally preserved.
 *
 * @param string $output
 * @return bool
 */
function test_theme_output_has_residual_stale_hero_url($output)
{
	if (!is_string($output) || '' === $output) {
		return false;
	}

	$image_attributes = 'src|srcset|data-src|data-srcset|data-lazy-src|data-lazy-srcset|data-bg|data-background|data-background-image|data-image|poster';
	if (preg_match_all('#(?<![\w-])((?:' . $image_attributes . '|style))\s*=\s*(["\'])(.*?)\2#is', $output, $attributes)) {
		foreach ($attributes[3] as $index => $value) {
			$attribute = strtolower($attributes[1][$index]);

			if ('style' === $attribute || false !== stripos($value, 'url(')) {
				if (preg_match_all('#url\(\s*(["\']?)(.*?)\1\s*\)#is', $value, $urls)) {
					foreach ($urls[2] as $url) {
						if (test_theme_is_stale_hero_url($url)) {
							return true;
						}
					}
				}
				continue;
			}

			if (false !== strpos($attribute, 'srcset')) {
				foreach (explode(',', $value) as $candidate) {
					if (preg_match('#^\s*(\S+)#', $candidate, $matches) && test_theme_is_stale_hero_url($matches[1])) {
						return true;
					}
				}
				continue;
			}

			if (test_theme_is_stale_hero_url($value)) {
				return true;
			}
		}
	}

	if (preg_match_all('#<style\b[^>]*>(.*?)</style>#is', $output, $styles)) {
		foreach ($styles[1] as $css) {
			if (preg_match_all('#url\(\s*(["\']?)(.*?)\1\s*\)#is', $css, $urls)) {
				foreach ($urls[2] as $url) {
					if (test_theme_is_stale_hero_url($url)) {
						return true;
					}
				}
			}
		}
	}

	return false;
}

/**
 * Repoint stale test-theme hero image URLs at the active theme's copies.
 *
 * The existing slider records were provisioned while the legacy test-theme
 * was active. The same hero files exist in this theme, so the rendered URLs
 * are corrected without touching slider records or plugin internals.
 *
 * Only image attributes and CSS url() values are rewritten, and only when the
 * URL belongs to this site. External URLs, query strings, links, text, and
 * markup outside those URL values are preserved.
 *
 * @param string $output Smart Slider shortcode output.
 * @return string Corrected output, or an empty string when a same-site stale URL remains.
 */
function test_theme_repair_stale_hero_asset_urls($output)
{
	if (!is_string($output) || '' === $output) {
		return $output;
	}

	if ('test-theme' === get_stylesheet() || 'test-theme' === get_template()) {
		return $output;
	}

	$stale_path = test_theme_get_stale_hero_asset_path();
	if (false === strpos($output, $stale_path)) {
		return $output;
	}

	$image_attributes = 'src|srcset|data-src|data-srcset|data-lazy-src|data-lazy-srcset|data-bg|data-background|data-background-image|data-image|poster';

	$rewritten = preg_replace_callback(
		'#((?<![\w-])(?:' . $image_attributes . ')\s*=\s*)(["\'])(.*?)\2#is',
		function ($matches) {
			return $matches[1] . $matches[2] . test_theme_rewrite_hero_image_attribute($matches[3], $matches[1]) . $matches[2];
		},
		$output
	);
	if (null !== $rewritten) {
		$output = $rewritten;
	}

	$rewritten = preg_replace_callback(
		'#((?<![\w-])style\s*=\s*)(["\'])(.*?)\2#is',
		function ($matches) {
			return $matches[1] . $matches[2] . test_theme_rewrite_css_hero_urls($matches[3]) . $matches[2];
		},
		$output
	);
	if (null !== $rewritten) {
		$output = $rewritten;
	}

	$rewritten = preg_replace_callback(
		'#(<style\b[^>]*>)(.*?)(</style>)#is',
		function ($matches) {
			return $matches[1] . test_theme_rewrite_css_hero_urls($matches[2]) . $matches[3];
		},
		$output
	);
	if (null !== $rewritten) {
		$output = $rewritten;
	}

	return test_theme_output_has_residual_stale_hero_url($output) ? '' : $output;
}

/**
 * Check if slider markup contains legacy industrial content.
 *
 * @param string $output
 * @return bool
 */
function test_theme_slider_has_legacy_content($output)
{
	if (!is_string($output) || empty($output)) {
		return false;
	}

	$legacy_indicators = array(
		'hero-power-tools',
		'hero-tile-cutting',
		'hero-toolkits',
		'A & S',
		'ماكينات قص',
		'سيراميك',
		'بورسلين',
		'عدد وأدوات',
	);

	foreach ($legacy_indicators as $indicator) {
		if (false !== stripos($output, $indicator)) {
			return true;
		}
	}

	return false;
}

/**
 * 3. Render Hero Slider using Smart Slider 3 with fixed viewport CTA button overlay,
 * or theme-controlled Ben10 Watch-Store Hero presentation when legacy slides are present.
 */
function test_theme_render_hero_slider()
{
	$slider_alias = apply_filters('ashrafco_hero_slider_alias', 'home-hero');
	$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');

	// Attempt Smart Slider 3 shortcode
	$output = '';
	if (shortcode_exists('smartslider3')) {
		$output = do_shortcode('[smartslider3 slider="' . esc_attr($slider_alias) . '"]');
		$output = test_theme_repair_stale_hero_asset_urls($output);

		// Suppress slider output if it contains unverified legacy industrial content
		if (test_theme_slider_has_legacy_content($output)) {
			$output = '';
		}
	}

	if (!empty($output)) {
		?>
		<section
			class="hero-smartslider-section mt-3 sm:mt-4 md:mt-5 max-w-[864px] w-full mx-auto px-3.5 sm:px-4 overflow-hidden bg-transparent"
			aria-label="<?php esc_attr_e('لافتات العروض الرئيسية', 'ashrafco'); ?>">
			<div
				class="hero-smartslider-wrapper relative w-full rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm bg-transparent select-none">
				<?php echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<!-- Fixed Viewport CTA Overlay Button ("اكتشف التشكيلات") -->
				<a href="<?php echo esc_url($shop_url); ?>" id="hero-banner-fixed-cta"
					class="hero-banner-fixed-cta-btn absolute z-30" draggable="false"
					aria-label="<?php esc_attr_e('اكتشف تشكيلات Ben10', 'ashrafco'); ?>">
					<span><?php esc_html_e('اكتشف تشكيلات Ben10', 'ashrafco'); ?></span>
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

	// High-Impact Theme-Controlled Ben10 Watch-Store Hero Presentation
	$watch_hero_img = get_template_directory_uri() . '/assets/images/hero/ben10-hero-watch.jpg';
	?>
	<section class="w-full max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 pt-2 sm:pt-4 pb-4 sm:pb-6" aria-label="<?php esc_attr_e('مقدمة Ben10 للساعات', 'ashrafco'); ?>">
		<div class="relative w-full rounded-2xl sm:rounded-3xl bg-[#101211] border border-[#244A27]/40 shadow-2xl overflow-hidden">
			<!-- Subtle Brand Ambient Gradients -->
			<div class="absolute -top-24 -right-24 w-72 sm:w-96 h-72 sm:h-96 bg-[#A3E635]/10 rounded-full blur-3xl pointer-events-none"></div>
			<div class="absolute -bottom-24 -left-24 w-72 sm:w-96 h-72 sm:h-96 bg-[#244A27]/25 rounded-full blur-3xl pointer-events-none"></div>

			<div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 items-center gap-6 sm:gap-8 lg:gap-10 p-5 sm:p-8 lg:p-12" dir="rtl">
				<!-- Content Column -->
				<div class="lg:col-span-7 flex flex-col items-start text-right">
					<!-- Brand Badge -->
					<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#244A27]/50 border border-[#A3E635]/30 text-[#A3E635] text-xs font-bold mb-3.5 sm:mb-4 select-none">
						<span class="w-2 h-2 rounded-full bg-[#A3E635] animate-pulse"></span>
						<span>تشكيلة Ben10 الحصرية للساعات</span>
					</div>

					<!-- Hero Headline -->
					<h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-[1.25] sm:leading-[1.2] mb-3 sm:mb-4 m-0">
						دقة متناهية.. وتصميم يعكس تميزك
					</h1>

					<!-- Supporting Copy -->
					<p class="text-xs sm:text-base lg:text-lg text-slate-300 font-normal leading-relaxed mb-5 sm:mb-6 max-w-xl m-0">
						اكتشف الفخامة والأناقة العصرية مع تشكيلة ساعات Ben10 الحصرية، المصممة بحرفية دقيقة وتفاصيل تعكس حضورك الواثق في كل لحظة.
					</p>

					<!-- Primary Call to Action -->
					<div class="flex flex-wrap items-center gap-3.5 mb-5 sm:mb-6 w-full sm:w-auto">
						<a href="<?php echo esc_url($shop_url); ?>"
							class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#A3E635] hover:bg-[#8ce025] text-[#101211] font-black text-xs sm:text-sm md:text-base px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl shadow-lg shadow-[#A3E635]/20 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-[#A3E635] focus:ring-offset-2 focus:ring-offset-[#101211] select-none">
							<span>اكتشف تشكيلات Ben10</span>
							<svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
								<path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
							</svg>
						</a>
					</div>

					<!-- Brand Values Micro Highlights -->
					<div class="flex flex-wrap items-center gap-3 sm:gap-5 pt-3.5 sm:pt-4 border-t border-white/10 text-[11px] sm:text-xs text-slate-300 font-medium">
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 text-[#A3E635]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							تصاميم أصلية
						</span>
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 text-[#A3E635]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							حرفية متقنة
						</span>
						<span class="inline-flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 text-[#A3E635]" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
								<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
							</svg>
							دقة استثنائية
						</span>
					</div>
				</div>

				<!-- Visual Column -->
				<div class="lg:col-span-5 w-full flex items-center justify-center">
					<div class="relative w-full aspect-[4/3] sm:aspect-[16/10] lg:aspect-square max-h-[380px] lg:max-h-[440px] rounded-2xl overflow-hidden border border-white/10 bg-[#161917] shadow-2xl group">
						<img src="<?php echo esc_url($watch_hero_img); ?>"
							alt="<?php esc_attr_e('ساعة Ben10 الفاخرة', 'ashrafco'); ?>"
							width="800" height="600"
							class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700 ease-out"
							loading="eager" decoding="async">
						<div class="absolute inset-0 bg-gradient-to-t from-[#101211]/60 via-transparent to-transparent pointer-events-none"></div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}

