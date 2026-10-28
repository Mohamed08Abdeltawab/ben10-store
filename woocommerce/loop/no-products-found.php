<?php
/**
 * Modern Empty State for Product Archives & Search Results
 *
 * Override of WooCommerce loop/no-products-found.php template for Ashrafco Child Theme.
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ashrafco-no-products bg-slate-50 border border-slate-200 rounded-3xl p-8 sm:p-10 max-w-xl mx-auto my-12 text-center shadow-xs font-sans" dir="rtl">

	<!-- Visual: Sleek Search / Package SVG Icon -->
	<div class="w-20 h-20 rounded-3xl bg-orange-50 text-primary border border-orange-200/80 flex items-center justify-center mx-auto mb-6 shadow-inner">
		<svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
		</svg>
	</div>

	<!-- Primary Title -->
	<h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-2">
		<?php if ( is_search() ) : ?>
			لم يتم العثور على أي منتجات تطابق بحثك
		<?php else : ?>
			لا توجد معدات متوفرة في هذا القسم حالياً
		<?php endif; ?>
	</h2>

	<?php if ( is_search() && get_search_query() ) : ?>
		<p class="text-xs font-bold text-primary mb-3">
			نتائج البحث عن: «<?php echo esc_html( get_search_query() ); ?>»
		</p>
	<?php endif; ?>

	<!-- Helper Text -->
	<p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto leading-relaxed mb-6">
		تأكد من كتابة الكلمات بشكل صحيح، أو تصفح الأقسام الرئيسية للعثور على ما تحتاجه لعملك:
	</p>

	<!-- Category Quick Chips -->
	<div class="flex items-center justify-center gap-2 flex-wrap text-xs mb-8">
		<?php
		$cats = get_terms( array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 4,
			'exclude'    => get_option( 'default_product_cat' ),
		) );

		if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
			foreach ( $cats as $cat ) :
				?>
				<a
					href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
					class="px-4 py-2 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-700 border border-slate-200 shadow-2xs transition-colors font-bold"
				>
					<?php echo esc_html( $cat->name ); ?>
				</a>
				<?php
			endforeach;
		else :
			?>
			<a
				href="<?php echo esc_url( home_url( '/?s=ماكينات%20قص&post_type=product' ) ); ?>"
				class="px-4 py-2 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-700 border border-slate-200 shadow-2xs transition-colors font-bold"
			>
				ماكينات قص
			</a>
			<a
				href="<?php echo esc_url( home_url( '/?s=ليزر&post_type=product' ) ); ?>"
				class="px-4 py-2 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-700 border border-slate-200 shadow-2xs transition-colors font-bold"
			>
				شواخص ليزر
			</a>
			<a
				href="<?php echo esc_url( home_url( '/?s=تسوية&post_type=product' ) ); ?>"
				class="px-4 py-2 rounded-full bg-white hover:bg-orange-50 hover:text-primary text-slate-700 border border-slate-200 shadow-2xs transition-colors font-bold"
			>
				أدوات تسوية
			</a>
		<?php endif; ?>
	</div>

	<!-- Primary Action Button -->
	<div>
		<a
			href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
			class="inline-flex items-center justify-center gap-2.5 bg-primary hover:bg-orange-700 active:scale-[0.99] text-white font-bold py-3.5 px-8 rounded-2xl shadow-lg shadow-orange-600/20 text-sm transition-all select-none"
		>
			<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
			</svg>
			<span>عرض كل المعدات المتوفرة</span>
		</a>
	</div>

</div>
