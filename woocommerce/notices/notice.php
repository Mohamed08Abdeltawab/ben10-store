<?php
/**
 * Modern Inline Info/General Notice Template for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 10.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div
		class="woocommerce-info ashrafco-inline-alert ashrafco-inline-alert--info mb-5 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 text-xs sm:text-sm font-semibold flex items-center gap-3.5 transition-all font-sans"
		<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		role="status"
		dir="rtl"
	>
		<span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0" aria-hidden="true">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
			</svg>
		</span>

		<div class="ashrafco-alert-content flex-1 leading-relaxed">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
	</div>
<?php endforeach; ?>
