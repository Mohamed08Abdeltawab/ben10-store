<?php
/**
 * Modern My Account Portal Template Override for Ashrafco Child Theme
 *
 * @package Ashrafco
 * @since 1.0.0
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ashrafco-myaccount-portal max-w-7xl mx-auto w-full font-sans" dir="rtl">

	<?php
	/**
	 * Hook: woocommerce_before_account_navigation.
	 */
	do_action( 'woocommerce_before_account_navigation' );
	?>

	<!-- Responsive Grid (Desktop: 12 Cols; Navigation: 3 Cols on Right in RTL, Content: 9 Cols on Left) -->
	<div class="grid grid-cols-1 md:grid-cols-12 gap-6 lg:gap-8 items-start w-full">

		<!-- Navigation Column (md:col-span-5 lg:col-span-4) -->
		<aside class="w-full md:col-span-5 lg:col-span-4">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
		</aside>

		<!-- Content Column (md:col-span-7 lg:col-span-8) -->
		<main class="w-full min-w-0 md:col-span-7 lg:col-span-8 woocommerce-MyAccount-content space-y-6">
			<?php
			/**
			 * Hook: woocommerce_account_content.
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</main>

	</div>

</div>
