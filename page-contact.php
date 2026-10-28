<?php
/**
 * Template Name: الفروع ومراكز الصيانة
 * Description: صفحة الفروع ومراكز الصيانة المعتمدة ونموذج الاستفسار الفني لشركة أشرفكو.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Centralized Dynamic Configuration
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$contact_phone = function_exists('ashrafco_get_contact_phone') ? ashrafco_get_contact_phone() : '+201065712366';
$working_hours = function_exists('ashrafco_settings') ? ashrafco_settings()->get('working_hours', 'يومياً من الساعة 9:00 صباحاً حتى الساعة 10:00 مساءً') : 'يومياً من الساعة 9:00 صباحاً حتى الساعة 10:00 مساءً';

// Handle Inquiry Form Submission
$form_submitted = false;
$form_error = '';
$inquiry_id = null;

if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['ashrafco_inquiry_nonce'])) {
	if (function_exists('ashrafco_process_inquiry_submission')) {
		$result = ashrafco_process_inquiry_submission($_POST);
		if ($result['success']) {
			$form_submitted = true;
			$inquiry_id = $result['inquiry_id'];
		} else {
			$form_error = $result['message'];
		}
	} else {
		$form_error = __('عذراً، تعذر معالجة الطلب في الوقت الحالي. يرجى المحاولة لاحقاً.', 'ashrafco');
	}
}

get_header();
?>

<main id="primary" class="site-main w-full bg-slate-50 font-sans min-h-screen pb-20" dir="rtl">

	<!-- =====================================================================
		 1. HERO HEADER
		 ===================================================================== -->
	<section
		class="relative bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white pt-14 pb-20 md:pt-20 md:pb-28 overflow-hidden">
		<!-- Subtle Radial Glow Accents -->
		<div class="absolute -top-24 -right-24 w-96 h-96 bg-orange-600/20 rounded-full blur-3xl pointer-events-none">
		</div>
		<div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none">
		</div>
		<div
			class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none opacity-40">
		</div>

		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<!-- Breadcrumb -->
			<nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-400 mb-6" aria-label="Breadcrumb">
				<a href="<?php echo esc_url(home_url('/')); ?>"
					class="hover:text-orange-400 transition-colors">الرئيسية</a>
				<span class="text-slate-600">/</span>
				<span class="text-white font-medium">الفروع ومراكز الصيانة</span>
			</nav>

			<div class="max-w-3xl space-y-4">
				<div
					class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs sm:text-sm font-semibold shadow-inner">
					<span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
					شبكة الفروع المعتمدة وخدمات الدعم الفني
				</div>

				<h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
					فروعنا ومراكز الصيانة المعتمدة
				</h1>

				<p class="text-base sm:text-lg md:text-xl text-slate-300 leading-relaxed font-normal">
					معاينة المعدات، استلام فوري، وقطع غيار أصلية لجميع الماكينات في القاهرة والجيزة وعموم محافظات مصر.
				</p>
			</div>

			<!-- Reassurance Mini Badges -->
			<div
				class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mt-8 pt-6 border-t border-slate-800/80 text-xs sm:text-sm text-slate-300">
				<div class="flex items-center gap-2">
					<span class="text-orange-400 font-bold text-base">✓</span>
					<span>معاينة وتجربة قبل الشراء</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-emerald-400 font-bold text-base">✓</span>
					<span>استلام فوري من الفروع</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-orange-400 font-bold text-base">✓</span>
					<span>قطع غيار أصلية بالكامل</span>
				</div>
				<div class="flex items-center gap-2">
					<span class="text-emerald-400 font-bold text-base">✓</span>
					<span>فحص ومعايرة أجهزة الليزر</span>
				</div>
			</div>
		</div>
	</section>


	<!-- =====================================================================
		 2. BRANCH CARDS SECTION
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 sm:-mt-12 relative z-20 mb-16">
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">

			<?php
			$branches = array(
				array(
					'id' => 'cairo_ramses',
					'city' => 'القاهرة',
					'badge' => 'المقر الرئيسي والمعرض المركزي',
					'badge_style' => 'bg-orange-50 text-orange-700 border-orange-200',
					'title' => 'فرع ومعرض رمسيس المركزي',
					'address' => '14 شارع السبتية، متفرع من ميدان رمسيس، القاهرة',
					'landmark' => 'بجوار محطة مترو الشهداء والبنك الأهلي المصري',
					'work_hours' => 'السبت إلى الخميس: 9:00 ص – 10:00 م | الجمعة: 1:00 م – 10:00 م',
					'tech_hours' => 'فحص الماكينات واستقبال الصيانة: يومياً من 10:00 ص – 8:00 م',
					'phones' => array('+20 2 2578 9012', '+20 106 571 2366'),
					'call_phone' => '+201065712366',
					'maps_url' => 'https://maps.google.com/?q=30.0626,31.2497',
					'features' => array('معرض متكامل لجميع الماكينات', 'تجربة القص الحي على البورسلين', 'استلام فوري لطلبات الموقع'),
					'wa_msg' => sprintf('مرحباً %s، أرغب بالتواصل مع فرع رمسيس للاستفسار عن توفر ماكينة وتحديد موعد للمعاينة.', $store_name),
				),
				array(
					'id' => 'giza_faisal',
					'city' => 'الجيزة',
					'badge' => 'معرض ومستودع المعدات الثقيلة',
					'badge_style' => 'bg-blue-50 text-blue-700 border-blue-200',
					'title' => 'فرع ومستودع الجيزة والبورسلين الكبير',
					'address' => 'شارع الملك فيصل الرئيسي، بالقرب من محطة مترو فيصل ومحور 26 يوليو، الجيزة',
					'landmark' => 'أمام بنك الإسكندرية وبجوار مجمع كبرى شركات السيراميك',
					'work_hours' => 'يومياً من 9:30 ص حتى 10:30 م بدون انقطاع',
					'tech_hours' => 'تسليم ومطابقة المعدات الثقيلة: يومياً من 10:30 ص – 9:00 م',
					'phones' => array('+20 2 3745 6789', '+20 106 571 2366'),
					'call_phone' => '+201065712366',
					'maps_url' => 'https://maps.google.com/?q=30.0131,31.1873',
					'features' => array('طاولات قص حتى 360 سم', 'شفاطات كهربائية وأنظمة مناولة', 'تحميل سيارات للمشاريع الكبرى'),
					'wa_msg' => sprintf('مرحباً %s، أود الاستفسار من فرع الجيزة عن معدات البورسلين الكبيرة وطاولات القص.', $store_name),
				),
				array(
					'id' => 'delta_tanta',
					'city' => 'الغربية / وسط الدلتا',
					'badge' => 'مركز صيانة ودعم فني معتمد',
					'badge_style' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
					'title' => 'مركز صيانة وخدمة وسط الدلتا (طنطا)',
					'address' => 'شارع الجيش الرئيسي، ميدان المحطة، طنطا، محافظة الغربية',
					'landmark' => 'أمام محطة قطار طنطا وبجوار مبنى نقابة المهندسين',
					'work_hours' => 'السبت إلى الخميس: 9:00 ص – 9:00 م (الجمعة: عطلة أسبوعية)',
					'tech_hours' => 'قسم صيانة الماكينات ومعايرة الليزر: 10:00 ص – 6:00 م',
					'phones' => array('+20 40 334 5678', '+20 106 571 2366'),
					'call_phone' => '+201065712366',
					'maps_url' => 'https://maps.google.com/?q=30.7865,31.0004',
					'features' => array('فحص فوري مجاني للأعطال', 'معايرة دقة أشعة الليزر الخضراء', 'توفير لقم وسكاكين أصلية فوراً'),
					'wa_msg' => sprintf('مرحباً مركز صيانة %s بطنطا، أحتاج فحص ماكينة قص وتوفير قطع غيار أصلية.', $store_name),
				),
				array(
					'id' => 'alex_smouha',
					'city' => 'الإسكندرية والساحل',
					'badge' => 'معرض وموزع معتمد',
					'badge_style' => 'bg-purple-50 text-purple-700 border-purple-200',
					'title' => 'فرع الإسكندرية والساحل الشمالي (سموحة)',
					'address' => 'ميدان فيكتور عمانويل، سموحة، الإسكندرية',
					'landmark' => 'بالقرب من نادي سموحة ومحور 14 مايو وطريق المطار',
					'work_hours' => 'السبت إلى الخميس: 10:00 ص – 10:00 م | الجمعة: 2:00 م – 10:00 م',
					'tech_hours' => 'استقبال الفنيين والمقاولين: 11:00 ص – 8:00 م',
					'phones' => array('+20 3 423 4567', '+20 106 571 2366'),
					'call_phone' => '+201065712366',
					'maps_url' => 'https://maps.google.com/?q=31.2156,29.9553',
					'features' => array('شحن فوري لمواقع الساحل الشمالي', 'أطقم شفاطات وموازين متطورة', 'دعم فني واستشارات مشاريع'),
					'wa_msg' => sprintf('مرحباً فرع %s بالإسكندرية، أرغب بالاستفسار عن أسعار المعدات ومواعيد الاستلام والتوصيل.', $store_name),
				),
			);

			foreach ($branches as $branch):
				$branch_wa_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode($branch['wa_msg']);
				?>
				<div
					class="ui-card bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:shadow-2xl hover:border-orange-300 transition-all duration-300 relative group">

					<div class="space-y-6">
						<!-- Top Header Badge & City -->
						<div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-100">
							<span
								class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border <?php echo esc_attr($branch['badge_style']); ?>">
								<span class="w-2 h-2 rounded-full bg-current"></span>
								<?php echo esc_html($branch['badge']); ?>
							</span>
							<span class="text-xs font-extrabold text-slate-400 font-mono tracking-wider">
								<?php echo esc_html($branch['city']); ?>
							</span>
						</div>

						<!-- Branch Title -->
						<div>
							<h3
								class="text-xl sm:text-2xl font-black text-slate-900 group-hover:text-orange-600 transition-colors">
								<?php echo esc_html($branch['title']); ?>
							</h3>
						</div>

						<!-- Details List -->
						<div class="space-y-3.5 text-xs sm:text-sm text-slate-600">

							<!-- Address & Landmark -->
							<div class="flex items-start gap-3">
								<div
									class="w-8 h-8 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
										</path>
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
									</svg>
								</div>
								<div>
									<strong class="text-slate-900 block font-bold">العنوان التفصيلي:</strong>
									<span><?php echo esc_html($branch['address']); ?></span>
									<span class="block text-slate-500 text-xs mt-0.5 font-medium">علامة مميزة:
										<?php echo esc_html($branch['landmark']); ?></span>
								</div>
							</div>

							<!-- Working Hours -->
							<div class="flex items-start gap-3">
								<div
									class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
									</svg>
								</div>
								<div>
									<strong class="text-slate-900 block font-bold">مواعيد المعرض والبيع:</strong>
									<span><?php echo esc_html($branch['work_hours']); ?></span>
									<span class="block text-slate-500 text-xs mt-0.5">ساعات الصيانة:
										<?php echo esc_html($branch['tech_hours']); ?></span>
								</div>
							</div>

							<!-- Phone Numbers -->
							<div class="flex items-start gap-3">
								<div
									class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
										</path>
									</svg>
								</div>
								<div>
									<strong class="text-slate-900 block font-bold">الهواتف المباشرة:</strong>
									<div class="flex flex-wrap items-center gap-3 mt-0.5">
										<?php foreach ($branch['phones'] as $phone_num): ?>
											<a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $phone_num)); ?>"
												class="font-mono font-bold text-slate-900 hover:text-orange-600 transition-colors"
												dir="ltr">
												<?php echo esc_html($phone_num); ?>
											</a>
										<?php endforeach; ?>
									</div>
								</div>
							</div>

							<!-- Key Feature Tags -->
							<div class="pt-2 flex flex-wrap gap-1.5">
								<?php foreach ($branch['features'] as $feat): ?>
									<span
										class="inline-flex items-center gap-1 text-[11px] font-semibold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-md">
										<span>✦</span>
										<span><?php echo esc_html($feat); ?></span>
									</span>
								<?php endforeach; ?>
							</div>

						</div>
					</div>

					<!-- Branch Action Buttons -->
					<div class="pt-6 border-t border-slate-100 mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
						<!-- WhatsApp Branch Button -->
						<a href="<?php echo esc_url($branch_wa_url); ?>" target="_blank" rel="noopener noreferrer"
							class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md shadow-emerald-600/20 text-center">
							<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
								<path
									d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
							</svg>
							<span>محادثة الفرع واتساب</span>
						</a>

						<!-- Google Maps Button -->
						<a href="<?php echo esc_url($branch['maps_url']); ?>" target="_blank" rel="noopener noreferrer"
							class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md text-center">
							<svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
							</svg>
							<span>موقع الفرع على الخريطة</span>
						</a>
					</div>

				</div>
			<?php endforeach; ?>

		</div>
	</section>


	<!-- =====================================================================
		 3. TECHNICAL INQUIRY FORM & ASSISTANCE SECTION
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div
			class="bg-white rounded-3xl border border-slate-200/90 shadow-2xl shadow-slate-200/60 p-6 sm:p-10 lg:p-12 overflow-hidden">
			<div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">

				<!-- Form Column (7 Cols) -->
				<div class="lg:col-span-7 space-y-6">

					<div>
						<div
							class="inline-flex items-center gap-2 text-orange-600 text-xs font-bold uppercase tracking-wider mb-2">
							<span class="w-2 h-2 rounded-full bg-orange-600"></span>
							استشارة فنية وتواصل مباشر
						</div>
						<h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
							نموذج الاستفسار وطلب الصيانة الفنية
						</h2>
						<p class="text-sm sm:text-base text-slate-500 mt-1 leading-relaxed">
							هل تحتاج مساعدة في اختيار ماكينة القص، طلب قطعة غيار نادرة، أو صيانة ميزان ليزر؟ املأ
							البيانات وسيقوم مهندسنا الفني بالتواصل معك.
						</p>
					</div>

					<!-- Form Feedback Messages -->
					<?php if ($form_submitted): ?>
						<div
							class="p-5 sm:p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-4 animate-fadeIn shadow-sm">
							<div
								class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
								<svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round"
										d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
								</svg>
							</div>
							<div>
								<div class="flex items-center gap-2 flex-wrap">
									<h4 class="font-bold text-base sm:text-lg text-emerald-900 m-0">تم إرسال استفسارك الفني
										بنجاح!</h4>
									<?php if ($inquiry_id): ?>
										<span
											class="text-xs font-mono font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-md">
											رقم المرجع: #<?php echo esc_html($inquiry_id); ?>
										</span>
									<?php endif; ?>
								</div>
								<p class="text-xs sm:text-sm text-emerald-700 mt-1.5 leading-relaxed">
									شكراً لتواصلك مع <?php echo esc_html($store_name); ?>. تم تسجيل بيانات طلبك وإرسال
									إشعار للمهندس المختص. سيتواصل معك أحد ممثلي الدعم الفني هاتفياً أو عبر واتساب خلال أقل
									من ساعة لمساعدتك.
								</p>
							</div>
						</div>
					<?php elseif (!empty($form_error)): ?>
						<div
							class="p-4 sm:p-5 rounded-2xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3 shadow-xs">
							<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
							<span class="text-xs sm:text-sm font-semibold"><?php echo esc_html($form_error); ?></span>
						</div>
					<?php endif; ?>

					<!-- Contact Form -->
					<form method="POST" action="#primary" id="ashrafco-inquiry-form" class="space-y-4">
						<?php wp_nonce_field('ashrafco_inquiry_action', 'ashrafco_inquiry_nonce'); ?>

						<!-- Anti-spam Honeypot -->
						<div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;"
							aria-hidden="true">
							<label for="inquiry_hp">اترك هذا الحقل فارغاً</label>
							<input type="text" id="inquiry_hp" name="inquiry_hp" tabindex="-1" autocomplete="off">
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
							<!-- Full Name -->
							<div>
								<label for="inquiry_name" class="block text-xs font-bold text-slate-700 mb-1.5">
									الاسم بالكامل <span class="text-red-500">*</span>
								</label>
								<input type="text" id="inquiry_name" name="inquiry_name" required
									placeholder="مثال: م. أحمد عبد الله"
									value="<?php echo isset($_POST['inquiry_name']) && !$form_submitted ? esc_attr($_POST['inquiry_name']) : ''; ?>"
									class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
							</div>

							<!-- Phone / WhatsApp -->
							<div>
								<label for="inquiry_phone" class="block text-xs font-bold text-slate-700 mb-1.5">
									رقم الموبايل / واتساب <span class="text-red-500">*</span>
								</label>
								<input type="tel" id="inquiry_phone" name="inquiry_phone" required
									placeholder="010XXXXXXXX" dir="ltr"
									value="<?php echo isset($_POST['inquiry_phone']) && !$form_submitted ? esc_attr($_POST['inquiry_phone']) : ''; ?>"
									class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm text-right focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all font-mono">
							</div>

							<!-- Email (Optional) -->
							<div class="sm:col-span-2 lg:col-span-1">
								<label for="inquiry_email" class="block text-xs font-bold text-slate-700 mb-1.5">
									البريد الإلكتروني <span class="text-slate-400 font-normal">(اختياري)</span>
								</label>
								<input type="email" id="inquiry_email" name="inquiry_email"
									placeholder="name@example.com" dir="ltr"
									value="<?php echo isset($_POST['inquiry_email']) && !$form_submitted ? esc_attr($_POST['inquiry_email']) : ''; ?>"
									class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm text-right focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all">
							</div>
						</div>

						<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
							<!-- Type of Inquiry -->
							<div>
								<label for="inquiry_type" class="block text-xs font-bold text-slate-700 mb-1.5">
									نوع الماكينة أو موضوع الاستفسار <span class="text-red-500">*</span>
								</label>
								<?php
								$selected_type = isset($_POST['inquiry_type']) && !$form_submitted ? sanitize_text_field(wp_unslash($_POST['inquiry_type'])) : '';
								$inquiry_types = array(
									'ماكينات قص يدوية' => 'ماكينات قص يدوية (Rubi, Shijing, Sigma)',
									'ماكينات قص كهربائية ومائية' => 'ماكينات قص كهربائية ومائية (Battipav)',
									'شواخص وموازين ليزر' => 'شواخص وموازين ليزر خضراء',
									'أنظمة مناولة وشفاطات' => 'أنظمة مناولة البورسلين وشفاطات (Bihui)',
									'طلب صيانة وفحص معدة' => 'طلب صيانة وفحص فني لمعدة',
									'استفسار عن قطع غيار وسكاكين' => 'استفسار عن قطع غيار وسكاكين ألماظة',
									'استشارة فنية قبل الشراء' => 'استشارة هندسية قبل الشراء لمشروع',
								);
								?>
								<select id="inquiry_type" name="inquiry_type"
									class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all bg-white">
									<?php foreach ($inquiry_types as $val => $label): ?>
										<option value="<?php echo esc_attr($val); ?>" <?php selected($selected_type, $val); ?>>
											<?php echo esc_html($label); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Preferred Branch / Region -->
							<div>
								<label for="inquiry_branch" class="block text-xs font-bold text-slate-700 mb-1.5">
									الفرع الأقرب لك أو المحافظة
								</label>
								<?php
								$selected_branch = isset($_POST['inquiry_branch']) && !$form_submitted ? sanitize_text_field(wp_unslash($_POST['inquiry_branch'])) : '';
								$inquiry_branches = array(
									'القاهرة - فرع رمسيس' => 'القاهرة - فرع رمسيس والمعرض الرئيسي',
									'الجيزة - مستودع فيصل' => 'الجيزة - فرع ومستودع فيصل',
									'الغربية - مركز صيانة طنطا' => 'الغربية والدلتا - مركز صيانة طنطا',
									'الإسكندرية - فرع سموحة' => 'الإسكندرية والساحل - فرع سموحة',
									'محافظة أخرى - شحن وتوصيل' => 'محافظة أخرى (شحن وتوصيل للموقع)',
								);
								?>
								<select id="inquiry_branch" name="inquiry_branch"
									class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all bg-white">
									<?php foreach ($inquiry_branches as $val => $label): ?>
										<option value="<?php echo esc_attr($val); ?>" <?php selected($selected_branch, $val); ?>>
											<?php echo esc_html($label); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<!-- Message -->
						<div>
							<label for="inquiry_message" class="block text-xs font-bold text-slate-700 mb-1.5">
								تفاصيل الاستفسار أو المشكلة الفنية <span class="text-red-500">*</span>
							</label>
							<textarea id="inquiry_message" name="inquiry_message" rows="4" required
								placeholder="يرجى توضيح مقاس البورسلين، موديل الماكينة، أو الاستفسار المطلوب بالتفصيل..."
								class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 text-sm focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all"><?php echo isset($_POST['inquiry_message']) && !$form_submitted ? esc_textarea($_POST['inquiry_message']) : ''; ?></textarea>
						</div>

						<!-- Submit Button -->
						<div class="pt-2">
							<button type="submit" id="inquiry-submit-btn"
								class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg shadow-orange-600/30 hover:shadow-orange-600/50 transition-all transform hover:-translate-y-0.5 active:translate-y-0 text-base cursor-pointer">
								<span id="submit-btn-text">إرسال الاستفسار الفني</span>
								<svg id="submit-btn-icon" class="w-5 h-5 rtl:rotate-180" fill="none"
									stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
								</svg>
							</button>
						</div>
					</form>

					<script>
						document.addEventListener('DOMContentLoaded', function () {
							var inqForm = document.getElementById('ashrafco-inquiry-form');
							if (inqForm) {
								inqForm.addEventListener('submit', function () {
									var btn = document.getElementById('inquiry-submit-btn');
									var txt = document.getElementById('submit-btn-text');
									if (btn && txt) {
										btn.disabled = true;
										btn.classList.add('opacity-75', 'cursor-not-allowed');
										txt.textContent = 'جاري الإرسال والتسجيل...';
									}
								});
							}
						});
					</script>

				</div>

				<!-- Quick Reassurance & Technical Support Sidebar (5 Cols) -->
				<div class="lg:col-span-5 space-y-6 lg:border-r lg:border-slate-100 lg:pr-10">

					<!-- Instant WhatsApp Consultation Box -->
					<div
						class="bg-gradient-to-br from-emerald-900 to-slate-900 text-white rounded-3xl p-6 sm:p-7 border border-emerald-700/40 shadow-xl relative overflow-hidden">
						<div
							class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none">
						</div>

						<div class="relative z-10 space-y-4">
							<div
								class="inline-flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider">
								<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
								استجابة فورية خلال 5 دقائق
							</div>

							<h3 class="text-xl sm:text-2xl font-bold text-white">
								مستعجل ومحتاج إجابة فورية في موقعك؟
							</h3>

							<p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
								تواصل مباشرة مع المهندس الفني عبر واتساب لمشاركة صور العطل أو مواصفات البورسلين والحصول
								على رد فوري.
							</p>

							<?php
							$quick_wa_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً %s، أحتاج مساعدة واستشارة فنية عاجلة بخصوص ماكينة قص / معدة.', $store_name));
							?>
							<a href="<?php echo esc_url($quick_wa_url); ?>" target="_blank" rel="noopener noreferrer"
								class="w-full inline-flex items-center justify-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-emerald-600/30 text-sm">
								<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
									<path
										d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
								</svg>
								<span>محادثة المهندس عبر واتساب</span>
							</a>
						</div>
					</div>

					<!-- Hotline & Customer Service Details -->
					<div class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 space-y-3">
						<h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
							<svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z">
								</path>
							</svg>
							<span>خدمة العملاء والخط الساخن</span>
						</h4>
						<p class="text-xs text-slate-600 leading-relaxed">
							فريق المبيعات والصيانة متاح للرد على استفساراتكم الهاتفية
							<?php echo esc_html($working_hours); ?>.
						</p>
						<div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
							<span class="text-slate-500 font-medium">الخط المباشر الموحد:</span>
							<a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $contact_phone)); ?>"
								class="font-mono font-bold text-orange-600 hover:text-orange-700" dir="ltr">
								<?php echo esc_html($contact_phone); ?>
							</a>
						</div>
					</div>

				</div>

			</div>
		</div>
	</section>

</main>

<?php
get_footer();
