<?php
/**
 * Modern Inline Success Notice Template for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 8.6.0
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
		class="woocommerce-message ashrafco-inline-alert ashrafco-inline-alert--success mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center gap-3.5 transition-all font-sans"
		<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		role="alert"
		dir="rtl"
	>
		<span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0" aria-hidden="true">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7" />
			</svg>
		</span>

		<div class="ashrafco-alert-content flex-1 leading-relaxed">
			<?php echo wc_kses_notice( $notice['notice'] ); ?>
		</div>
	</div>
<?php endforeach; ?>
