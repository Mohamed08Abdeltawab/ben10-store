<?php
/**
 * Template Name: سياسة الشحن والضمان والمعاينة
 * Description: صفحة سياسة الشحن وضمان الماكينات وحق المعاينة قبل السداد لمتجر أشرفكو.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
	exit;
}

// Centralized Dynamic WhatsApp Configuration
$wa_phone = function_exists('ashrafco_get_whatsapp_phone') ? ashrafco_get_whatsapp_phone() : apply_filters('ashrafco_whatsapp_phone', '201065712366');
$store_name = function_exists('ashrafco_get_store_name') ? ashrafco_get_store_name('ar') : 'أشرفكو';
$wa_policy_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً خدمة عملاء %s، أرغب بالاستفسار بخصوص سياسة الضمان أو تقديم طلب فحص ماكينة / تفعيل الضمان.', $store_name));
$wa_inspect_url = 'https://wa.me/' . esc_attr($wa_phone) . '?text=' . rawurlencode(sprintf('مرحباً %s، أود معرفة تفاصيل شحن طلب وموعد وصول المندوب لمعاينة الماكينة.', $store_name));

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
				<span class="text-white font-medium">سياسة الشحن والضمان والمعاينة</span>
			</nav>

			<div class="max-w-3xl space-y-4">
				<div
					class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-500/10 border border-orange-500/20 text-orange-400 text-xs sm:text-sm font-semibold shadow-inner">
					<span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
					ميثاق الثقة والأمان الأول للفني والمقاول في مصر
				</div>

				<h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white tracking-tight leading-tight">
					سياسة الشحن والضمان وحق المعاينة
				</h1>

				<p class="text-base sm:text-lg md:text-xl text-slate-300 leading-relaxed font-normal">
					نضمن لك راحة البال التامة: عاين وافحص ماكينتك بنفسك مع مندوب الشحن واختبر سلاح الألماظة وزوايا القص
					قبل دفع أي مبالغ، مع ضمان حقيقي وتوافر دائم لقطع الغيار الأصلية.
				</p>
			</div>
		</div>
	</section>


	<!-- =====================================================================
		 2. TOP REASSURANCE PILLARS (3 FEATURE BADGES)
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 sm:-mt-12 relative z-20 mb-16">
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

			<!-- Pillar 1: حق المعاينة الكاملة -->
			<div
				class="ui-card bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:shadow-2xl hover:border-orange-300 transition-all duration-300 group">
				<div class="space-y-4">
					<div
						class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all shadow-sm">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
							</path>
						</svg>
					</div>

					<div
						class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200">
						<span>أمان بنسبة 100%</span>
					</div>

					<h3 class="text-xl font-bold text-slate-900 group-hover:text-orange-600 transition-colors">
						حق المعاينة الكاملة قبل الاستلام وسداد أي مبالغ
					</h3>

					<p class="text-sm text-slate-600 leading-relaxed">
						افتح شحنتك أمام مندوب التوصيل، افحص هيكل الماكينة، سلاسة انزلاق رولمان البلي، وجودة سلاح
						الألماظة. لا تدفع أي جنيه إلا بعد التأكد من مطابقة المعدة لمواصفاتك.
					</p>
				</div>

				<div class="pt-4 border-t border-slate-100 mt-6 flex items-center text-xs font-bold text-orange-600">
					<span>معاينة حية بدون أي رسوم إضافية</span>
				</div>
			</div>

			<!-- Pillar 2: ضمان حقيقي وقطع غيار -->
			<div
				class="ui-card bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:shadow-2xl hover:border-emerald-300 transition-all duration-300 group">
				<div class="space-y-4">
					<div
						class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all shadow-sm">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
							</path>
						</svg>
					</div>

					<div
						class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
						<span>ضمان معتمد حتى 12 شهراً</span>
					</div>

					<h3 class="text-xl font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">
						ضمان حقيقي ضد عيوب الصناعة مع توافر قطع الغيار
					</h3>

					<p class="text-sm text-slate-600 leading-relaxed">
						جميع الماكينات مشمولة بشهادة ضمان رسمية ضد أي عيب مصنعي، مع مركز صيانة متكامل وتوفير دائم لجميع
						قطع الغيار الأصلية والسكاكين ورولمان البلي لجميع الماركات.
					</p>
				</div>

				<div class="pt-4 border-t border-slate-100 mt-6 flex items-center text-xs font-bold text-emerald-600">
					<span>مراكز خدمة بالقاهرة، الجيزة، والدلتا</span>
				</div>
			</div>

			<!-- Pillar 3: شحن سريع لكافة المحافظات -->
			<div
				class="ui-card bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl shadow-slate-200/50 flex flex-col justify-between hover:shadow-2xl hover:border-blue-300 transition-all duration-300 group">
				<div class="space-y-4">
					<div
						class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
						<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0">
							</path>
						</svg>
					</div>

					<div
						class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
						<span>توصيل 24 - 48 ساعة</span>
					</div>

					<h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
						شحن سريع لكافة محافظات مصر بأسعار محددة
					</h3>

					<p class="text-sm text-slate-600 leading-relaxed">
						تغليف صناعي مصفح ضد الصدمات لحماية الماكينات وموازين الليزر أثناء النقل، وشحن سريع حتى باب
						الورشة أو موقع العمل مع إمكانية التتبع المباشر لخط سير الشحنة.
					</p>
				</div>

				<div class="pt-4 border-t border-slate-100 mt-6 flex items-center text-xs font-bold text-blue-600">
					<span>أسعار شحن ثابتة وواضحة بدون مفاجآت</span>
				</div>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 3. SECTION 1: سياسة المعاينة والتجربة مع مندوب الشحن
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-10 lg:p-12">

			<div class="max-w-3xl mb-10 space-y-2">
				<div class="inline-flex items-center gap-2 text-orange-600 text-xs font-bold uppercase tracking-wider">
					<span class="w-2 h-2 rounded-full bg-orange-600"></span>
					خطوات عملية للفحص الذاتي
				</div>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
					سياسة المعاينة والتجربة مع مندوب الشحن
				</h2>
				<p class="text-sm sm:text-base text-slate-600 leading-relaxed">
					اتبع هذه الخطوات البسيطة عند استلامك لماكينة القص أو المعدات بحضور مندوب شركة الشحن:
				</p>
			</div>

			<!-- 5 Sequential Inspection Steps -->
			<div class="grid grid-cols-1 md:grid-cols-5 gap-4 lg:gap-6 relative">

				<!-- Step 1 -->
				<div
					class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between hover:border-orange-300 transition-all group">
					<div class="space-y-3">
						<div
							class="w-10 h-10 rounded-xl bg-orange-600 text-white font-mono font-black text-lg flex items-center justify-center shadow-md shadow-orange-600/30">
							1
						</div>
						<h4 class="font-bold text-slate-900 text-base group-hover:text-orange-600 transition-colors">
							فتح الصندوق وفحص الهيكل
						</h4>
						<p class="text-xs text-slate-600 leading-relaxed">
							افتح كرتونة الشحن وتأكد من سلامة الهيكل الخارجي للماكينة وقاعدة الألومنيوم، وخلوها من أي
							خدوش أو آثار صدمات أثناء النقل.
						</p>
					</div>
					<div class="pt-3 border-t border-slate-200 mt-4 text-[11px] font-semibold text-slate-500">
						فحص بصري خارجي
					</div>
				</div>

				<!-- Step 2 -->
				<div
					class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between hover:border-orange-300 transition-all group">
					<div class="space-y-3">
						<div
							class="w-10 h-10 rounded-xl bg-slate-900 text-white font-mono font-black text-lg flex items-center justify-center shadow-md">
							2
						</div>
						<h4 class="font-bold text-slate-900 text-base group-hover:text-orange-600 transition-colors">
							اختبار حركة المقبض والسكة
						</h4>
						<p class="text-xs text-slate-600 leading-relaxed">
							حرّك ذراع القص للأمام والخلف على المسار الفولاذي للتأكد من انسيابية رولمانات البلي وعدم وجود
							أي ثقل أو خشونة أو احتكاك.
						</p>
					</div>
					<div class="pt-3 border-t border-slate-200 mt-4 text-[11px] font-semibold text-slate-500">
						اختبار السلاسة الميكانيكية
					</div>
				</div>

				<!-- Step 3 -->
				<div
					class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between hover:border-orange-300 transition-all group">
					<div class="space-y-3">
						<div
							class="w-10 h-10 rounded-xl bg-slate-900 text-white font-mono font-black text-lg flex items-center justify-center shadow-md">
							3
						</div>
						<h4 class="font-bold text-slate-900 text-base group-hover:text-orange-600 transition-colors">
							فحص سلاح الألماظة الأصلي
						</h4>
						<p class="text-xs text-slate-600 leading-relaxed">
							تأكد من لقمة/سلاح الألماظة (Wheel Cutter) الأصلي وتثبيته المتقن، وخلو حافة القطع من أي شروخ
							أو تآكل لضمان قص أملس وبدون شظايا.
						</p>
					</div>
					<div class="pt-3 border-t border-slate-200 mt-4 text-[11px] font-semibold text-slate-500">
						فحص حدة القطع
					</div>
				</div>

				<!-- Step 4 -->
				<div
					class="bg-slate-50 rounded-2xl p-5 border border-slate-200/80 flex flex-col justify-between hover:border-orange-300 transition-all group">
					<div class="space-y-3">
						<div
							class="w-10 h-10 rounded-xl bg-slate-900 text-white font-mono font-black text-lg flex items-center justify-center shadow-md">
							4
						</div>
						<h4 class="font-bold text-slate-900 text-base group-hover:text-orange-600 transition-colors">
							تجربة الليزر ومسطرة القياس
						</h4>
						<p class="text-xs text-slate-600 leading-relaxed">
							شغّل شعاع الليزر الأخضر المدمج وتأكد من محاذاته الدقيقة مع مسطرة القياس وزوايا القص (90°
							و45°) دون أي انحراف في الخط.
						</p>
					</div>
					<div class="pt-3 border-t border-slate-200 mt-4 text-[11px] font-semibold text-slate-500">
						فحص دقة التوجيه
					</div>
				</div>

				<!-- Step 5 -->
				<div
					class="bg-emerald-50 rounded-2xl p-5 border border-emerald-200 flex flex-col justify-between hover:border-emerald-300 transition-all group">
					<div class="space-y-3">
						<div
							class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-mono font-black text-lg flex items-center justify-center shadow-md shadow-emerald-600/30">
							5
						</div>
						<h4 class="font-bold text-emerald-900 text-base">
							استلام الضمان وسداد الفاتورة
						</h4>
						<p class="text-xs text-emerald-800 leading-relaxed">
							بعد اطمئنانك التام، استلم الفاتورة الضريبية وبطاقة الضمان وسدد قيمة الطلب للمندوب نقداً.
							وإذا لاحظت أي خلل، يمكنك رفض الاستلام فوراً بدون أي تكلفة!
						</p>
					</div>
					<div class="pt-3 border-t border-emerald-200 mt-4 text-[11px] font-bold text-emerald-700">
						اطمئنان تام وسداد آمن
					</div>
				</div>

			</div>

			<!-- Notice Banner -->
			<div
				class="mt-8 p-4 rounded-2xl bg-orange-50/70 border border-orange-200/80 flex items-center gap-3 text-xs sm:text-sm text-orange-900">
				<svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor"
					viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<span>
					<strong>ملاحظة للمشتري:</strong> مندوب الشحن لديه تعليمات واضحة بالانتظار حتى تنتهي تماماً من فحص
					الماكينة والتأكد من سلامتها. لا تتردد في أخذ كامل وقتك!
				</span>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 4. SECTION 2: شروط الضمان ومراكز الصيانة
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
		<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

			<!-- Warranty Coverage (7 Cols) -->
			<div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-10 space-y-6">
				<div>
					<div
						class="inline-flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase tracking-wider mb-1">
						<span class="w-2 h-2 rounded-full bg-emerald-600"></span>
						الضمان المعتمد
					</div>
					<h3 class="text-2xl font-black text-slate-900 tracking-tight">
						شروط الضمان وتغطية الصيانة
					</h3>
					<p class="text-sm text-slate-500">
						نلتزم بتوفير أعلى معايير الحماية لعملائنا عبر برامج ضمان واضحة وشاملة:
					</p>
				</div>

				<div class="space-y-4 text-xs sm:text-sm text-slate-700">
					<!-- Rule 1 -->
					<div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
						<span
							class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">✦</span>
						<div>
							<strong class="text-slate-900 block font-bold text-sm mb-0.5">مدة الضمان حسب فئة
								المعدة:</strong>
							<span>ضمان لمدة <strong>12 شهراً</strong> على ماكينات القص اليدوية والكهربائية (Rubi,
								Battipav, Shijing, Sigma)، وضمان لمدة <strong>6 أشهر</strong> على أجهزة وموازين وشواخص
								الليزر ضد عيوب الصناعة.</span>
						</div>
					</div>

					<!-- Rule 2 -->
					<div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
						<span
							class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">✦</span>
						<div>
							<strong class="text-slate-900 block font-bold text-sm mb-0.5">ما يشمله الضمان
								المعتمد:</strong>
							<span>عيوب السبك واللحام في قواعد الماكينات، استقامة المسارات الفولاذية، أعطال بوردات
								ومستشعرات الليزر، والتلف المبكر لرولمان البلي غير الناتج عن سوء استخدام.</span>
						</div>
					</div>

					<!-- Rule 3 -->
					<div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
						<span
							class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">✦</span>
						<div>
							<strong class="text-slate-900 block font-bold text-sm mb-0.5">المستهلكات غير
								المشمولة:</strong>
							<span>أسلحة ولقم الألماظة (تعتبر مواد مستهلكة طبيعياً بكثرة القص)، وكذلك الكسور الناتجة عن
								السقوط من مرتفع أو إهمال تنظيف السكك من رواسب الأسمنت والمونة.</span>
						</div>
					</div>

					<!-- Rule 4 -->
					<div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
						<span
							class="w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">✦</span>
						<div>
							<strong class="text-slate-900 block font-bold text-sm mb-0.5">التزام قطع الغيار
								الأصلية:</strong>
							<span>نضمن توافر جميع قطع الغيار الأصلية لجميع الماكينات التي نبيعها لمدة لا تقل عن 5 سنوات
								من تاريخ الشراء.</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Service Centers Quick Card (5 Cols) -->
			<div
				class="lg:col-span-5 bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl space-y-6">
				<div class="space-y-2">
					<div
						class="inline-flex items-center gap-2 text-orange-400 text-xs font-bold uppercase tracking-wider">
						<span class="w-2 h-2 rounded-full bg-orange-400"></span>
						مراكز الصيانة المعتمدة
					</div>
					<h3 class="text-xl sm:text-2xl font-bold text-white">
						كيف تطلب الصيانة أو تفحص ماكينتك؟
					</h3>
					<p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
						إذا واجهت أي مشكلة فنية أو انحراف في الليزر، يمكنك التوجه لأحد مراكزنا المعتمدة أو شحن الماكينة
						إلينا للصيانة السريعة.
					</p>
				</div>

				<div class="space-y-3.5 text-xs sm:text-sm text-slate-300">
					<div class="flex items-start gap-3 pb-3 border-b border-slate-800">
						<span class="font-bold text-orange-400 font-mono text-base">01.</span>
						<div>
							<strong class="text-white block font-bold">مركز صيانة رمسيس (القاهرة):</strong>
							<span>شارع السبتية، رمسيس - هاتف: 01065712366</span>
						</div>
					</div>
					<div class="flex items-start gap-3 pb-3 border-b border-slate-800">
						<span class="font-bold text-orange-400 font-mono text-base">02.</span>
						<div>
							<strong class="text-white block font-bold">مستودع وورشة الجيزة (فيصل):</strong>
							<span>شارع الملك فيصل الرئيسي - هاتف: 01065712366</span>
						</div>
					</div>
					<div class="flex items-start gap-3">
						<span class="font-bold text-orange-400 font-mono text-base">03.</span>
						<div>
							<strong class="text-white block font-bold">مركز صيانة وسط الدلتا (طنطا):</strong>
							<span>شارع الجيش، ميدان المحطة - هاتف: 01065712366</span>
						</div>
					</div>
				</div>

				<div class="pt-4 border-t border-slate-800">
					<a href="<?php echo esc_url($wa_policy_url); ?>" target="_blank" rel="noopener noreferrer"
						class="w-full inline-flex items-center justify-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-lg shadow-emerald-600/30 text-sm">
						<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
							<path
								d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
						</svg>
						<span>تواصل لطلب صيانة أو تفعيل ضمان</span>
					</a>
				</div>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 5. SECTION 3: سياسة الاستبدال والاسترجاع (14 يوماً)
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-10 lg:p-12">

			<div
				class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-4 border-b border-slate-100 gap-4">
				<div>
					<div
						class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/60 text-blue-700 text-xs font-bold mb-2">
						قانون حماية المستهلك المصري رقم 181 لسنة 2018
					</div>
					<h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
						سياسة الاستبدال والاسترجاع خلال 14 يوماً
					</h3>
					<p class="text-sm text-slate-500 mt-1">
						نضمن حقك الكامل في استرجاع أو استبدال مشترياتك بكل سلاسة وفقاً لأحكام القانون:
					</p>
				</div>
				<div
					class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex-shrink-0">
					<span>✓ استرداد نقدي كامل أو فودافون كاش / إنستاباي</span>
				</div>
			</div>

			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<!-- Return Point 1 -->
				<div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
					<div
						class="w-9 h-9 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm">
						1
					</div>
					<h4 class="font-bold text-slate-900 text-base">مهلة 14 يوماً كاملة</h4>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						يحق للمشتري استبدال أو استرجاع المنتج خلال 14 يوماً من تاريخ استلام الشحنة في حالة وجود أي عيب
						مصنعي أو عدم مطابقة للمواصفات المعروضة على المتجر.
					</p>
				</div>

				<!-- Return Point 2 -->
				<div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
					<div
						class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
						2
					</div>
					<h4 class="font-bold text-slate-900 text-base">حالة المنتج عند الإرجاع</h4>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						يشترط أن تكون الماكينة بعبوتها وتغليفها الأصلي مع كافة الملحقات والكتالوجات المرفقة، وألا يكون
						التلف ناتجاً عن إساءة الاستخدام أو الكسر العمدي.
					</p>
				</div>

				<!-- Return Point 3 -->
				<div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2.5">
					<div
						class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm">
						3
					</div>
					<h4 class="font-bold text-slate-900 text-base">سرعة رد المبالغ المالية</h4>
					<p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
						يتم رد قيمة الطلب فور فحص المنتج خلال 24 - 48 ساعة عبر نفس وسيلة الدفع، أو نقداً، أو عبر تحويل
						فوري على محفظة فودافون كاش أو إنستاباي InstaPay.
					</p>
				</div>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 6. SECTION 4: جدول النطاقات الزمنية لتوصيل الطلبات حسب المحافظات
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
		<div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-10 lg:p-12">

			<div class="max-w-3xl mb-8 space-y-2">
				<div class="inline-flex items-center gap-2 text-blue-600 text-xs font-bold uppercase tracking-wider">
					<span class="w-2 h-2 rounded-full bg-blue-600"></span>
					تغطية لوجستية شاملة
				</div>
				<h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
					جدول النطاقات الزمنية لتوصيل الطلبات حسب المحافظات
				</h3>
				<p class="text-sm text-slate-500">
					مواعيد التسليم التقديرية لكافة أنحاء جمهورية مصر العربية:
				</p>
			</div>

			<!-- Delivery Table -->
			<div class="overflow-x-auto rounded-2xl border border-slate-200">
				<table class="w-full text-right text-xs sm:text-sm text-slate-700">
					<thead class="bg-slate-900 text-white font-bold text-xs uppercase">
						<tr>
							<th class="py-4 px-4 sm:px-6">النطاق الجغرافي والمحافظات</th>
							<th class="py-4 px-4 sm:px-6">المدة الزمنية المتوقعة</th>
							<th class="py-4 px-4 sm:px-6">طريقة التسليم والمعاينة</th>
							<th class="py-4 px-4 sm:px-6 text-center">حالة الخدمة</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-200 bg-white">
						<!-- Row 1: Cairo & Giza -->
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="py-4 px-4 sm:px-6 font-bold text-slate-900">
								<span>القاهرة الكبرى والجيزة والقليوبية</span>
								<span class="block text-xs font-normal text-slate-500 mt-0.5">(توصيل لنطاق الورش،
									المواقع، والمنازل)</span>
							</td>
							<td class="py-4 px-4 sm:px-6 font-mono font-bold text-orange-600">
								24 ساعة فقط
								<span class="block text-[11px] font-normal text-slate-500 font-sans mt-0.5">متاح تسليم
									نفس اليوم للطلبات العاجلة</span>
							</td>
							<td class="py-4 px-4 sm:px-6 text-slate-600">
								مندوب شحن خاص + حق المعاينة والتجربة قبل الدفع
							</td>
							<td class="py-4 px-4 sm:px-6 text-center">
								<span
									class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
									شحن سريع ومباشر
								</span>
							</td>
						</tr>

						<!-- Row 2: Delta & Canal -->
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="py-4 px-4 sm:px-6 font-bold text-slate-900">
								<span>محافظات الوجه البحري والدلتا والقناة</span>
								<span class="block text-xs font-normal text-slate-500 mt-0.5">الإسكندرية، البحيرة،
									الغربية، المنوفية، الدقهلية، كفر الشيخ، الشرقية، دمياط، بورسعيد، الإسماعيلية،
									السويس</span>
							</td>
							<td class="py-4 px-4 sm:px-6 font-mono font-bold text-slate-900">
								24 – 48 ساعة
							</td>
							<td class="py-4 px-4 sm:px-6 text-slate-600">
								شحن سريع مؤمن + تتبع مباشر برسائل SMS
							</td>
							<td class="py-4 px-4 sm:px-6 text-center">
								<span
									class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
									متاح يومياً
								</span>
							</td>
						</tr>

						<!-- Row 3: North & Mid Upper Egypt -->
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="py-4 px-4 sm:px-6 font-bold text-slate-900">
								<span>محافظات شمال ووسط الصعيد</span>
								<span class="block text-xs font-normal text-slate-500 mt-0.5">الفيوم، بني سويف، المنيا،
									أسيوط</span>
							</td>
							<td class="py-4 px-4 sm:px-6 font-mono font-bold text-slate-900">
								48 ساعة
							</td>
							<td class="py-4 px-4 sm:px-6 text-slate-600">
								شحن حتى باب الموقع أو الورشة مع المعاينة
							</td>
							<td class="py-4 px-4 sm:px-6 text-center">
								<span
									class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
									متاح بانتظام
								</span>
							</td>
						</tr>

						<!-- Row 4: South Upper Egypt & Remote -->
						<tr class="hover:bg-slate-50 transition-colors">
							<td class="py-4 px-4 sm:px-6 font-bold text-slate-900">
								<span>محافظات جنوب الصعيد والبحر الأحمر والمناطق الساحلية</span>
								<span class="block text-xs font-normal text-slate-500 mt-0.5">سوهاج، قنا، الأقصر، أسوان،
									البحر الأحمر، الغردقة، مطروح، الوادي الجديد</span>
							</td>
							<td class="py-4 px-4 sm:px-6 font-mono font-bold text-slate-900">
								48 – 72 ساعة
							</td>
							<td class="py-4 px-4 sm:px-6 text-slate-600">
								تغليف مصفح ثلاثي الطبقات لحماية المعدات
							</td>
							<td class="py-4 px-4 sm:px-6 text-center">
								<span
									class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
									تغطية شاملة
								</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
	</section>


	<!-- =====================================================================
		 7. CALL TO ACTION FOOTER (WHATSAPP WARRANTY & TECHNICAL SUPPORT)
		 ===================================================================== -->
	<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div
			class="relative bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 rounded-3xl border border-slate-800 p-8 sm:p-12 overflow-hidden shadow-2xl">
			<!-- Background Radial Accents -->
			<div class="absolute -top-20 -left-20 w-80 h-80 bg-orange-600/15 rounded-full blur-3xl pointer-events-none">
			</div>
			<div
				class="absolute -bottom-20 -right-20 w-80 h-80 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none">
			</div>

			<div
				class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8 text-center lg:text-right">
				<div class="max-w-2xl space-y-3">
					<div
						class="inline-flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider">
						<span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
						خدمة عملاء الضمان والدعم الفني
					</div>
					<h3 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-white tracking-tight leading-tight">
						لديك استفسار حول الضمان أو تحتاج مساعدة في فحص ماكينة؟
					</h3>
					<p class="text-slate-300 text-sm sm:text-base leading-relaxed">
						تواصل مباشرة مع فريق الصيانة والدعم الفني عبر واتساب لمتابعة طلبات الضمان، توفير شفرات وأسلحة
						القص، أو فحص الماكينات.
					</p>
				</div>

				<div class="flex-shrink-0 flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
					<!-- Primary WhatsApp Button -->
					<a href="<?php echo esc_url($wa_policy_url); ?>" target="_blank" rel="noopener noreferrer"
						class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-8 rounded-2xl shadow-xl shadow-emerald-600/30 transition-all transform hover:-translate-y-0.5 text-base">
						<svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
							<path
								d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
						</svg>
						<span>محادثة الدعم الفني عبر واتساب</span>
					</a>
				</div>
			</div>
		</div>
	</section>

</main>

<?php
get_footer();
