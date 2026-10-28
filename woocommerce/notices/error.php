<?php
/**
 * Modern Inline Error Notice Template for Ashrafco Child Theme
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

<div
	class="woocommerce-error ashrafco-inline-alert ashrafco-inline-alert--error mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-semibold flex items-start gap-3.5 transition-all font-sans"
	role="alert"
	dir="rtl"
>
	<span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mt-0.5" aria-hidden="true">
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
		</svg>
	</span>

	<div class="ashrafco-alert-content flex-1 leading-relaxed space-y-1">
		<?php if ( count( $notices ) > 1 ) : ?>
			<ul class="list-disc list-inside space-y-1">
				<?php foreach ( $notices as $notice ) : ?>
					<li <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo wc_kses_notice( $notice['notice'] ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<?php foreach ( $notices as $notice ) : ?>
				<div <?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo wc_kses_notice( $notice['notice'] ); ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
