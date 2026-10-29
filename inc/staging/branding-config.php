<?php
/**
 * Staging branding seed values.
 *
 * INTENTIONALLY NOT LOADED.
 *
 * This file is a reviewable data source for an isolated staging clone. It is
 * not required by functions.php and must never be used to write options until
 * the clone has been confirmed and the write has been explicitly approved.
 *
 * Only values verified against the live store on 2026-10-29 are recorded here.
 * Intentionally omitted:
 * - logo_url / logo_alt: dynamic; derive from the active theme at seed time.
 * - brand_symbol, tagline, description, address_details: not verified.
 * - social_facebook, social_instagram, social_youtube, social_tiktok: not verified.
 *
 * @package CustomStoreBoilerplate
 */

defined('ABSPATH') || exit;

return array(
	'store_name_ar' => 'بن 10',
	'store_name_en' => 'ben10',
	'contact_phone' => '+201065712366',
	'whatsapp_phone' => '201065712366',
	'contact_email' => 'mohamedzidan0999@gmail.com',
	'address' => 'القاهرة، مصر العربية',
	'working_hours' => 'يومياً من 9:00 ص إلى 11:00 م',
);
