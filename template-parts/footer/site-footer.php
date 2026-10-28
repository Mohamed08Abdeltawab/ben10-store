<?php
/**
 * Mobile-First Modern E-Commerce Footer Component for A & S
 *
 * RTL Arabic layout for premium tools & hardware store.
 * Reproduces the visual language, hierarchy, spacing, colors, and interactions.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart');
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account');
$tracking_url = function_exists('wc_get_endpoint_url') ? wc_get_endpoint_url('orders', '', $account_url) : home_url('/track-order');
$wishlist_url = function_exists('test_theme_get_wishlist_url') ? test_theme_get_wishlist_url() : home_url('/wishlist');
$contact_url = home_url('/contact');
$policy_url = home_url('/policy');
$current_year = date('Y');
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$brand_symbol = function_exists('ashrafco_settings') ? ashrafco_settings('brand_symbol', 'A & S') : 'A & S';
$tagline = function_exists('ashrafco_settings') ? ashrafco_settings('tagline', 'أدوات أصلية .. لأداء أقوى') : 'أدوات أصلية .. لأداء أقوى';
$description = function_exists('ashrafco_settings') ? ashrafco_settings('description', 'نوفر لك مجموعة مختارة من أفضل الأدوات والمعدات لجودة عالية ومتينة تدوم.') : 'نوفر لك مجموعة مختارة من أفضل الأدوات والمعدات لجودة عالية ومتينة تدوم.';
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : (string) apply_filters('ashrafco_whatsapp_phone', '201065712366');
$wa_footer_url = function_exists('ashrafco_get_whatsapp_url') ? ashrafco_get_whatsapp_url(sprintf('مرحباً متجر %s، أود الاستفسار عن أحدث الأدوات والمعدات.', $brand_symbol)) : ('https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode('مرحباً متجر A & S، أود الاستفسار عن أحدث الأدوات والمعدات.'));
$eagle_logo_url = function_exists('ashrafco_get_store_logo_url') ? ashrafco_get_store_logo_url() : (get_stylesheet_directory_uri() . '/assets/images/eagle-logo.webp');
$tools_bg_url = get_stylesheet_directory_uri() . '/assets/images/footer-tools-bg.webp';
$contact_phone = function_exists('ashrafco_get_contact_phone') ? ashrafco_get_contact_phone() : '+201065712366';
$store_address = function_exists('ashrafco_get_store_address') ? ashrafco_get_store_address() : 'القاهرة، مصر العربية';
$address_details = function_exists('ashrafco_settings') ? ashrafco_settings('address_details', 'المركز الرئيسي والمعرض') : 'المركز الرئيسي والمعرض';
$working_hours = function_exists('ashrafco_settings') ? ashrafco_settings('working_hours', 'يومياً من 9 ص إلى 10 م') : 'يومياً من 9 ص إلى 10 م';
$social_facebook = function_exists('ashrafco_settings') ? ashrafco_settings('social_facebook', 'https://facebook.com') : 'https://facebook.com';
$social_instagram = function_exists('ashrafco_settings') ? ashrafco_settings('social_instagram', 'https://instagram.com') : 'https://instagram.com';
$social_youtube = function_exists('ashrafco_settings') ? ashrafco_settings('social_youtube', 'https://youtube.com') : 'https://youtube.com';
?>

<footer id="test-theme-site-footer"
	class="as-site-footer relative w-full overflow-hidden text-[#F5F7FA] font-sans select-none sm:select-auto" dir="rtl"
	role="contentinfo">

	<!-- Decorative Background (Bottom-Left Tools Silhouette + Red Ambient Glow) -->
	<div class="as-footer-bg-decor pointer-events-none absolute inset-0 overflow-hidden z-0" aria-hidden="true">
		<!-- Red ambient radial glow at bottom left -->
		<div class="as-footer-glow absolute bottom-0 left-0 w-80 h-80 rounded-full"></div>

		<!-- Industrial decorative image: power drill, toolbox, hand tools -->
		<img src="<?php echo esc_url($tools_bg_url); ?>" alt="" width="480" height="270" loading="lazy" decoding="async"
			class="as-footer-tools-img absolute bottom-0 left-0" />

		<!-- Seamless gradient overlay into #080C14 -->
		<div class="as-footer-overlay absolute inset-0"></div>
	</div>

	<!-- Main Footer Content Wrapper -->
	<div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-24 md:pb-12">

		<!-- ==================================================================
			 SECTION 1 — BRAND (Top Center)
			 ================================================================== -->
		<div class="as-brand-section text-center max-w-md mx-auto mb-6">
			<!-- A&S Eagle Logo -->
			<a href="<?php echo esc_url(home_url('/')); ?>"
				class="inline-block transition-transform duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-[#FF171F] rounded-2xl p-1 mb-3.5"
				aria-label="الصفحة الرئيسية لمتجر A & S">
				<img src="<?php echo esc_url($eagle_logo_url); ?>" alt="A & S Logo" width="64" height="64"
					class="w-14 h-14 sm:w-16 sm:h-16 mx-auto object-contain filter drop-shadow-[0_2px_12px_rgba(255,23,31,0.22)]" />
			</a>

			<!-- Brand Tagline -->
			<h2 class="as-brand-tagline text-lg sm:text-2xl font-extrabold text-[#F5F7FA] tracking-tight mb-2">
				<?php echo esc_html($tagline); ?>
			</h2>

			<!-- Brand Description -->
			<p class="as-brand-desc text-[13px] sm:text-sm text-[#AAB3C2] leading-[1.8] px-2 max-w-sm mx-auto">
				<?php echo esc_html($description); ?>
			</p>
		</div>

		<!-- ==================================================================
			 SECTION 2 — SOCIAL MEDIA (4 Circular Buttons)
			 ================================================================== -->
		<div class="as-social-section flex items-center justify-center gap-3 mb-8" aria-label="روابط التواصل الاجتماعي">
			<!-- Facebook -->
			<a href="<?php echo esc_url($social_facebook); ?>" target="_blank" rel="noopener noreferrer"
				class="as-social-btn" aria-label="تابعنا على فيسبوك">
				<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
					<path
						d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
				</svg>
			</a>

			<!-- WhatsApp -->
			<a href="<?php echo esc_url($wa_footer_url); ?>" target="_blank" rel="noopener noreferrer"
				class="as-social-btn" aria-label="تواصل معنا عبر واتساب">
				<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
					<path
						d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
				</svg>
			</a>

			<!-- Instagram -->
			<a href="<?php echo esc_url($social_instagram); ?>" target="_blank" rel="noopener noreferrer"
				class="as-social-btn" aria-label="تابعنا على انستغرام">
				<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
					<path
						d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
				</svg>
			</a>

			<!-- YouTube -->
			<a href="<?php echo esc_url($social_youtube); ?>" target="_blank" rel="noopener noreferrer"
				class="as-social-btn" aria-label="قناتنا على يوتيوب">
				<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
					<path
						d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
				</svg>
			</a>
		</div>

		<!-- ==================================================================
			 SECTION 3 — ACCORDION LINKS (4 Rows)
			 ================================================================== -->
		<div class="as-accordion-container mb-8" id="as-footer-accordions">

			<!-- Accordion Row 1: روابط سريعة -->
			<div class="as-accordion-item" data-accordion="1">
				<button type="button" class="as-accordion-trigger" id="as-acc-trigger-1" aria-expanded="false"
					aria-controls="as-acc-panel-1">
					<span class="as-acc-heading-wrap">
						<!-- Section Icon (Right) -->
						<span class="as-acc-icon" aria-hidden="true">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
							</svg>
						</span>
						<span class="as-acc-title">روابط سريعة</span>
					</span>
					<!-- Chevron (Left) -->
					<span class="as-acc-chevron" aria-hidden="true">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
						</svg>
					</span>
				</button>
				<div class="as-accordion-panel" id="as-acc-panel-1" role="region" aria-labelledby="as-acc-trigger-1">
					<div class="as-accordion-content">
						<ul class="as-link-list">
							<li><a href="<?php echo esc_url($shop_url); ?>" class="as-footer-link">المتجر</a></li>
							<li><a href="<?php echo esc_url(function_exists('ashrafco_get_offers_url') ? ashrafco_get_offers_url() : home_url('/offers/')); ?>"
									class="as-footer-link">العروض والتخفيضات</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('orderby' => 'date'), $shop_url)); ?>"
									class="as-footer-link">أحدث المنتجات</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('orderby' => 'popularity'), $shop_url)); ?>"
									class="as-footer-link">المنتجات الأكثر طلباً</a></li>
							<li><a href="<?php echo esc_url($wishlist_url); ?>" class="as-footer-link">قائمة الرغبات
									والمحفوظات</a></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Accordion Row 2: الأقسام الرئيسية -->
			<div class="as-accordion-item" data-accordion="2">
				<button type="button" class="as-accordion-trigger" id="as-acc-trigger-2" aria-expanded="false"
					aria-controls="as-acc-panel-2">
					<span class="as-acc-heading-wrap">
						<!-- Section Icon (Right) -->
						<span class="as-acc-icon" aria-hidden="true">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
							</svg>
						</span>
						<span class="as-acc-title">الأقسام الرئيسية</span>
					</span>
					<!-- Chevron (Left) -->
					<span class="as-acc-chevron" aria-hidden="true">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
						</svg>
					</span>
				</button>
				<div class="as-accordion-panel" id="as-acc-panel-2" role="region" aria-labelledby="as-acc-trigger-2">
					<div class="as-accordion-content">
						<ul class="as-link-list">
							<li><a href="<?php echo esc_url(add_query_arg(array('s' => 'ماكينات قص', 'post_type' => 'product'), home_url('/'))); ?>"
									class="as-footer-link">ماكينات قص السيراميك والبورسلين</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('s' => 'ليزر', 'post_type' => 'product'), home_url('/'))); ?>"
									class="as-footer-link">شواخص وموازين ليزر 3D</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('s' => 'تسوية', 'post_type' => 'product'), home_url('/'))); ?>"
									class="as-footer-link">أدوات الشفط وتثبيت البلاط</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('s' => 'أقراص', 'post_type' => 'product'), home_url('/'))); ?>"
									class="as-footer-link">أقراص وصلب وألماس القص</a></li>
							<li><a href="<?php echo esc_url(add_query_arg(array('s' => 'شنط', 'post_type' => 'product'), home_url('/'))); ?>"
									class="as-footer-link">شنط وحقائب العدد المتكاملة</a></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Accordion Row 3: خدمة العملاء -->
			<div class="as-accordion-item" data-accordion="3">
				<button type="button" class="as-accordion-trigger" id="as-acc-trigger-3" aria-expanded="false"
					aria-controls="as-acc-panel-3">
					<span class="as-acc-heading-wrap">
						<!-- Section Icon (Right) -->
						<span class="as-acc-icon" aria-hidden="true">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
							</svg>
						</span>
						<span class="as-acc-title">خدمة العملاء</span>
					</span>
					<!-- Chevron (Left) -->
					<span class="as-acc-chevron" aria-hidden="true">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
						</svg>
					</span>
				</button>
				<div class="as-accordion-panel" id="as-acc-panel-3" role="region" aria-labelledby="as-acc-trigger-3">
					<div class="as-accordion-content">
						<ul class="as-link-list">
							<li><a href="<?php echo esc_url($tracking_url); ?>" class="as-footer-link">تتبع الطلب
									والشحنة</a></li>
							<li><a href="<?php echo esc_url($policy_url); ?>" class="as-footer-link">سياسة الاسترجاع
									والاستبدال</a></li>
							<li><a href="<?php echo esc_url($policy_url); ?>" class="as-footer-link">الشحن ومناطق
									التوصيل</a></li>
							<li><a href="<?php echo esc_url($contact_url); ?>" class="as-footer-link">الضمان والدعم
									الفني</a></li>
							<li><a href="<?php echo esc_url($policy_url); ?>" class="as-footer-link">الأسئلة الشائعة
									والمساعدة</a></li>
						</ul>
					</div>
				</div>
			</div>

			<!-- Accordion Row 4: معلومات المتجر -->
			<div class="as-accordion-item" data-accordion="4">
				<button type="button" class="as-accordion-trigger" id="as-acc-trigger-4" aria-expanded="false"
					aria-controls="as-acc-panel-4">
					<span class="as-acc-heading-wrap">
						<!-- Section Icon (Right) -->
						<span class="as-acc-icon" aria-hidden="true">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
							</svg>
						</span>
						<span class="as-acc-title">معلومات المتجر</span>
					</span>
					<!-- Chevron (Left) -->
					<span class="as-acc-chevron" aria-hidden="true">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
						</svg>
					</span>
				</button>
				<div class="as-accordion-panel" id="as-acc-panel-4" role="region" aria-labelledby="as-acc-trigger-4">
					<div class="as-accordion-content">
						<ul class="as-link-list">
							<li><a href="<?php echo esc_url($contact_url); ?>" class="as-footer-link">عن متجر A &
									S</a></li>
							<li><a href="<?php echo esc_url($contact_url); ?>" class="as-footer-link">معارضنا
									وفروعنا</a></li>
							<li><a href="<?php echo esc_url($contact_url); ?>" class="as-footer-link">تواصل معنا
									مباشرة</a></li>
							<li><a href="<?php echo esc_url($wa_footer_url); ?>" class="as-footer-link">طلب عروض أسعار
									للشركات</a></li>
							<li><a href="<?php echo esc_url($contact_url); ?>" class="as-footer-link">شهادات الجودة
									والاعتمادات</a></li>
						</ul>
					</div>
				</div>
			</div>

		</div>

		<!-- ==================================================================
			 SECTION 4 — STORE CONTACT (3 Compact Cards)
			 ================================================================== -->
		<div class="as-contact-section mb-8">
			<div class="as-contact-grid">
				<!-- Card 1: Location -->
				<div class="as-contact-card">
					<div class="as-contact-icon" aria-hidden="true">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
					</div>
					<div class="as-contact-text"><?php echo esc_html($store_address); ?></div>
					<div class="as-contact-subtext"><?php echo esc_html($address_details); ?></div>
				</div>

				<!-- Card 2: Phone / WhatsApp -->
				<a href="tel:<?php echo esc_attr(preg_replace('/[^0-9\+]/', '', $contact_phone)); ?>"
					class="as-contact-card as-contact-card-link" aria-label="اتصل بنا أو راسلنا واتساب">
					<div class="as-contact-icon" aria-hidden="true">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
						</svg>
					</div>
					<div class="as-contact-text" dir="ltr"><?php echo esc_html($contact_phone); ?></div>
					<div class="as-contact-subtext">هاتف وواتساب مباشر</div>
				</a>

				<!-- Card 3: Clock -->
				<div class="as-contact-card">
					<div class="as-contact-icon" aria-hidden="true">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
					<div class="as-contact-text"><?php echo esc_html($working_hours); ?></div>
					<div class="as-contact-subtext">خدمة متواصلة طوال الأسبوع</div>
				</div>
			</div>
		</div>

		<!-- ==================================================================
			 SECTION 5 — STORE BENEFITS (3 Columns, Large Line Icons)
			 ================================================================== -->
		<div class="as-benefits-section mb-8">
			<div class="as-benefits-grid">
				<!-- Benefit 1: جودة عالية -->
				<div class="as-benefit-item">
					<div class="as-benefit-icon" aria-hidden="true">
						<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="1.75"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
						</svg>
					</div>
					<div class="as-benefit-title">جودة عالية</div>
				</div>

				<!-- Benefit 2: توصيل سريع -->
				<div class="as-benefit-item">
					<div class="as-benefit-icon" aria-hidden="true">
						<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="1.75"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
						</svg>
					</div>
					<div class="as-benefit-title">توصيل سريع</div>
				</div>

				<!-- Benefit 3: أسعار منافسة -->
				<div class="as-benefit-item">
					<div class="as-benefit-icon" aria-hidden="true">
						<svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" stroke-width="1.75"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
					<div class="as-benefit-title">أسعار منافسة</div>
				</div>
			</div>
		</div>

		<!-- ==================================================================
			 SECTION 6 — PAYMENT METHODS (Pills)
			 ================================================================== -->
		<div class="as-payment-section text-center mb-6">
			<h3 class="as-payment-heading text-xs sm:text-sm font-bold text-[#F5F7FA] mb-3.5">
				طرق الدفع المتاحة
			</h3>
			<div class="as-payment-badges flex items-center justify-center flex-wrap gap-2.5">
				<!-- Visa -->
				<div class="as-payment-pill as-pill-visa" title="Visa">
					<span class="as-visa-logo">VISA</span>
				</div>

				<!-- Mastercard -->
				<div class="as-payment-pill as-pill-mastercard" title="Mastercard">
					<span class="as-mc-circles" aria-hidden="true">
						<span class="as-mc-circle as-mc-red"></span>
						<span class="as-mc-circle as-mc-yellow"></span>
					</span>
					<span class="text-[11px] font-bold text-white tracking-tight">Mastercard</span>
				</div>

				<!-- Fawry -->
				<div class="as-payment-pill as-pill-fawry" title="فوري Fawry">
					<span class="as-fawry-badge" aria-hidden="true">F</span>
					<span class="text-[11px] font-bold text-[#FBBF24]">فوري</span>
				</div>

				<!-- Cash on Delivery -->
				<div class="as-payment-pill as-pill-cod" title="الدفع عند الاستلام">
					<span class="as-cod-icon" aria-hidden="true">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
						</svg>
					</span>
					<span class="text-[11px] font-bold text-[#F5F7FA]">الدفع عند الاستلام</span>
				</div>
			</div>
		</div>

		<!-- ==================================================================
			 SECTION 7 — COPYRIGHT
			 ================================================================== -->
		<div class="as-copyright-section border-t border-[#1D2633] pt-6 text-center">
			<p class="text-xs sm:text-[13px] text-[#8A94A5] font-medium leading-relaxed m-0">
				جميع الحقوق محفوظة لمتجر A & S © <?php echo esc_html($current_year); ?>
			</p>
		</div>

	</div>
</footer>