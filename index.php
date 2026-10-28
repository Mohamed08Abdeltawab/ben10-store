<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package CustomStoreBoilerplate
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="primary" class="site-main w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
	<?php
	if (have_posts()) :

		if (is_home() && !is_front_page()) :
			?>
			<header class="mb-8">
				<h1 class="page-title text-3xl font-extrabold text-slate-900"><?php single_post_title(); ?></h1>
			</header>
			<?php
		endif;

		while (have_posts()) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class('mb-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm'); ?>>
				<header class="entry-header mb-4">
					<?php
					if (is_singular()) :
						the_title('<h1 class="entry-title text-2xl font-bold text-slate-900">', '</h1>');
					else :
						the_title('<h2 class="entry-title text-xl font-bold text-slate-900"><a href="' . esc_url(get_permalink()) . '" class="hover:text-primary transition-colors">', '</a></h2>');
					endif;
					?>
				</header>

				<div class="entry-content text-slate-700 leading-relaxed">
					<?php
					the_content(sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__('المزيد <span class="sr-only">%s</span>', 'custom-store-boilerplate'),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post(get_the_title())
					));
					?>
				</div>
			</article>
			<?php
		endwhile;

		the_posts_navigation();

	else :
		?>
		<section class="no-results not-found bg-white p-8 rounded-2xl border border-slate-200 text-center">
			<h2 class="text-xl font-bold text-slate-800 mb-2"><?php esc_html_e('لا يوجد محتوى لعرضه', 'custom-store-boilerplate'); ?></h2>
			<p class="text-slate-600"><?php esc_html_e('لم يتم العثور على أي نتائج تطابق طلبك.', 'custom-store-boilerplate'); ?></p>
		</section>
		<?php
	endif;
	?>
</main>

<?php
get_footer();