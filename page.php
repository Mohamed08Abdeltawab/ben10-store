<?php
/**
 * The template for displaying all individual static pages
 *
 * Ensures standalone WordPress pages do not fallback to parent theme styles
 * and renders with full Cairo typography, RTL support, and Ashrafco branding.
 *
 * @package Ashrafco
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="primary" class="site-main w-full min-h-[70vh] bg-slate-50 font-sans py-4 sm:py-8 lg:py-10" dir="rtl">

	<?php if (function_exists('is_account_page') && is_account_page()): ?>

		<div class="w-full mx-auto px-4 sm:px-6 lg:px-8">
			<?php if (is_user_logged_in()):
				$current_endpoint = (function_exists('WC') && WC()->query) ? WC()->query->get_current_endpoint() : '';
				$endpoint_title = '';
				if ($current_endpoint) {
					switch ($current_endpoint) {
						case 'orders':
							$endpoint_title = __('الطلبات', 'ashrafco');
							break;
						case 'view-order':
							$endpoint_title = __('تفاصيل الطلب', 'ashrafco');
							break;
						case 'edit-account':
							$endpoint_title = __('تفاصيل الحساب', 'ashrafco');
							break;
						case 'downloads':
							$endpoint_title = __('التنزيلات', 'ashrafco');
							break;
						default:
							$endpoint_title = (function_exists('WC') && WC()->query) ? WC()->query->get_endpoint_title($current_endpoint) : '';
							break;
					}
				}
				?>
				<!-- Breadcrumb Trail for Logged In Customer Matching Reference Images -->
				<nav class="mb-4 mt-4 sm:mb-6 max-w-7xl mx-auto text-xs sm:text-sm text-slate-400 flex items-center gap-2 flex-wrap select-none"
					aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
					<a href="<?php echo esc_url(home_url('/')); ?>"
						class="hover:text-primary transition-colors flex items-center gap-1.5 text-slate-500">
						<svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
						</svg>
						<span><?php esc_html_e('الرئيسية', 'ashrafco'); ?></span>
					</a>
					<span class="text-slate-300">/</span>
					<?php if ($current_endpoint && $endpoint_title): ?>
						<a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
							class="hover:text-primary transition-colors text-slate-500">
							<?php esc_html_e('حسابي', 'ashrafco'); ?>
						</a>
						<span class="text-slate-300">/</span>
						<span class="text-slate-700 font-bold truncate">
							<?php echo esc_html($endpoint_title); ?>
						</span>
					<?php else: ?>
						<span class="text-slate-700 font-bold truncate">
							<?php esc_html_e('حسابي', 'ashrafco'); ?>
						</span>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<?php while (have_posts()):
				the_post(); ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>

	<?php else: ?>

		<?php
		$ashrafco_checkout_container = function_exists('is_checkout') && is_checkout() && (!function_exists('is_order_received_page') || !is_order_received_page());
		$checkout_container_classes = $ashrafco_checkout_container ? 'ashrafco-checkout-page-container px-3 sm:px-6 lg:px-8' : 'max-w-5xl px-4 sm:px-6 lg:px-8';
		?>
		<div class="<?php echo esc_attr( $checkout_container_classes ); ?> mx-auto">

		<?php if ( $ashrafco_checkout_container ) : ?>
			<!-- Breadcrumb Trail -->
			<nav class="mb-4 text-xs sm:text-sm text-slate-400 flex items-center gap-2 flex-wrap"
				aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors">
					<?php esc_html_e('الرئيسية', 'ashrafco'); ?>
				</a>
				<span class="text-slate-300">/</span>
				<span class="text-slate-700 font-semibold truncate">
					<?php the_title(); ?>
				</span>
			</nav>

			<?php while (have_posts()): the_post(); ?>
				<div class="ashrafco-checkout-content-wrapper">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>

		<?php else : ?>

			<!-- Breadcrumb Trail -->
			<nav class="mb-6 text-xs sm:text-sm text-slate-400 flex items-center gap-2 flex-wrap"
				aria-label="<?php esc_attr_e('مسار التنقل', 'ashrafco'); ?>">
				<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-primary transition-colors">
					<?php esc_html_e('الرئيسية', 'ashrafco'); ?>
				</a>
				<span class="text-slate-300">/</span>
				<span class="text-slate-700 font-semibold truncate">
					<?php the_title(); ?>
				</span>
			</nav>

			<?php while (have_posts()):
				the_post(); ?>

				<!-- Main Article Card -->
				<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 md:p-12'); ?>>

					<!-- Page Header -->
					<header class="entry-header pb-6 border-b border-slate-100 mb-8">
						<h1
							class="entry-title text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tracking-tight leading-tight m-0">
							<?php the_title(); ?>
						</h1>

						<?php if (has_excerpt()): ?>
							<p class="text-slate-500 text-sm sm:text-base mt-3 leading-relaxed max-w-3xl">
								<?php echo get_the_excerpt(); ?>
							</p>
						<?php endif; ?>
					</header>

					<!-- Featured Image if present -->
					<?php if (has_post_thumbnail()): ?>
						<div class="mb-8 rounded-2xl overflow-hidden border border-slate-200">
							<?php the_post_thumbnail('large', array('class' => 'w-full h-auto object-cover max-h-96')); ?>
						</div>
					<?php endif; ?>

					<!-- Page Content Area with Clean Cairo Typography -->
					<div
						class="entry-content text-slate-700 leading-relaxed text-sm sm:text-base space-y-6 [&_h2]:text-xl [&_h2]:sm:text-2xl [&_h2]:font-extrabold [&_h2]:text-slate-900 [&_h2]:mt-8 [&_h2]:mb-4 [&_h3]:text-lg [&_h3]:sm:text-xl [&_h3]:font-bold [&_h3]:text-slate-800 [&_h3]:mt-6 [&_h3]:mb-3 [&_p]:leading-relaxed [&_ul]:list-disc [&_ul]:list-inside [&_ul]:space-y-2 [&_ol]:list-decimal [&_ol]:list-inside [&_ol]:space-y-2 [&_a]:text-primary [&_a]:underline [&_a]:hover:text-orange-700 [&_blockquote]:border-r-4 [&_blockquote]:border-primary [&_blockquote]:bg-orange-50/50 [&_blockquote]:p-4 [&_blockquote]:rounded-l-xl [&_blockquote]:italic [&_table]:w-full [&_table]:border [&_table]:border-slate-200 [&_table]:rounded-xl [&_th]:bg-slate-50 [&_th]:p-3 [&_th]:text-right [&_td]:p-3 [&_td]:border-t [&_td]:border-slate-100">
						<?php
						the_content();

						wp_link_pages(array(
							'before' => '<div class="page-links pt-6 border-t border-slate-100 flex items-center gap-2"><span class="font-bold text-xs text-slate-500">' . esc_html__('الصفحات:', 'ashrafco') . '</span>',
							'after' => '</div>',
						));
						?>
					</div>

				</article>

				<!-- Optional Comments on Pages -->
				<?php
				if (comments_open() || get_comments_number()):
					?>
					<div class="mt-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10">
						<?php comments_template(); ?>
					</div>
					<?php
				endif;
				?>

			<?php endwhile; ?>
		<?php endif; ?>

		</div>

	<?php endif; ?>

</main>

<?php
get_footer();
