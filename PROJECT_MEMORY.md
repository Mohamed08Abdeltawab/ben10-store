# PROJECT MEMORY & MIGRATION BLUEPRINT

> **Document Status**: Authoritative Single Source of Truth  
> **Origin Reference**: `test-theme/` (Astra Child Theme)  
> **Target Theme**: `custom-store-boilerplate/` (Standalone Clean Architecture WooCommerce Theme)  
> **Target Version**: 1.0.0  
> **Author**: Principal Software Architect & Migration Lead  
> **Last Updated**: 2026-10-25  

---

## 1. Architecture Overview & Context

### 1.1 Executive Summary
The objective of this migration is to transition the Ashrafco (أشرفكو / A&S) e-commerce storefront from an **Astra Child Theme (`test-theme`)** to a **Standalone, High-Performance WooCommerce Boilerplate (`custom-store-boilerplate`)** with **100% 1:1 visual parity** and **100% functional parity**, while completely resolving the architectural debt of the legacy implementation.

### 1.2 Legacy (`test-theme`) vs Target (`custom-store-boilerplate`) Architecture

| Architectural Dimension | Legacy Implementation (`test-theme`) | Target Implementation (`custom-store-boilerplate`) |
| :--- | :--- | :--- |
| **Theme Parentage** | Astra Child Theme (`Template: astra`) | **Standalone Custom Theme** (Zero parent dependencies) |
| **CSS Specificity & Bloat** | Over **2,100 `!important` declarations** fighting Astra defaults | **0 `!important` declarations**; pure CSS custom properties (`:root`) & Tailwind utility classes |
| **Inline Styles** | Frequent inline `<style>` tags in PHP templates and `wp_head` hooks | **0 inline `<style>` tags** in PHP templates; 100% centralized styling pipeline |
| **WooCommerce Decoupling** | Overrides battling WooCommerce default CSS (`woocommerce.css`) | `add_filter('woocommerce_enqueue_styles', '__return_empty_array')` suppressing legacy floats |
| **Theme Entry Point** | Monolithic `functions.php` (3,420 lines, 116 KB) mixing setup, AJAX, and markup | **Clean Autoloader `functions.php` (23 lines)** delegating to modular domain handlers in `/inc/` |
| **Admin Portal** | Monolithic `template-admin-portal.php` (8,051 lines, 434 KB) | Modular tab templates in `template-parts/admin-portal/` with compartmentalized AJAX in `inc/` |
| **HPOS Compatibility** | Partial/implicit declarations | Explicit HPOS compatibility declared via `FeaturesUtil::declare_compatibility('custom_order_tables')` |
| **Checkout Architecture** | Multi-step checkout with address manager overridden via Astra hooks | Standalone native WooCommerce multi-step checkout with pure vanilla CSS tokens & modular JS |
| **Design Tokens** | Hardcoded hex values dispersed across CSS rules and inline templates | Standardized CSS variables in `assets/src/css/tokens.css` mapped to Tailwind configuration |

### 1.3 Core Technical Requirements & Environment Constraints
1. **PHP Version**: 8.0+ compatible (strict typing, null-coalescing, `str_ends_with`, `str_starts_with`).
2. **WordPress Core**: 6.4+ compatible.
3. **WooCommerce**: 8.5+ with High-Performance Order Storage (HPOS) active.
4. **Locale / Directionality**: Primary language **Arabic (`ar`)**, layout **Right-to-Left (`rtl`)**, numbers/Latin subtext **LTR**.
5. **Asset Build Tooling**: Tailwind CSS v3 (`3.4.19`), PostCSS (`8.4.47`), Autoprefixer (`10.4.20`).
6. **Zero Regression Guarantee**: All custom meta keys, AJAX action names, nonces, and checkout validation rules must remain strictly identical to guarantee zero database or checkout workflow breakage.

---

## 2. Comprehensive File Inventory & Migration Mapping Table

Total Cataloged Source Files in `test-theme`: **109 files** (excluding `node_modules`).

### 2.1 Root Templates & Core Files

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `functions.php` (3,420 lines) | `functions.php` (autoloader) + `/inc/*.php` | Theme bootstrapping, constants, modular autoloader (16 domain handlers active) | **Migrated (Phase 1 & Phase 4)** |
| `front-page.php` | `front-page.php` | Homepage template: Smart slider, category track, promo banner, best sellers, features | **Pending (Phase 5)** |
| `404.php` | `404.php` | 404 Error page: Friendly search input, return-home button, helpful category links | **Pending (Phase 5)** |
| `page.php` | `page.php` | Default single page container with standard container constraints | **Pending (Phase 5)** |
| `page-offers.php` | `page-offers.php` | Exclusive promotions & countdown deals catalog with category filters | **Pending (Phase 5)** |
| `page-contact.php` | `page-contact.php` | Contact Us & Branches directory with custom inquiry submission form | **Pending (Phase 5)** |
| `page-policy.php` | `page-policy.php` | Terms of service, privacy policy, and shipping/return accordion screens | **Pending (Phase 5)** |
| `page-track-order.php` | `page-track-order.php` | Public guest order status tracker (by Order ID & Phone/Email) | **Pending (Phase 5)** |
| `page-wishlist.php` | `page-wishlist.php` | Standalone wishlist full-page view | **Pending (Phase 5)** |
| `template-admin-portal.php` (8,051 lines) | `template-admin-portal.php` (split into modular parts) | Custom Ashrafco Admin Management Portal (Products, Orders, Discounts, Shipping, Users) | **Pending (Phase 5)** |
| `header_rendered.html` | *Reference only* (Do not deploy) | Static rendered header DOM snapshot for testing & inspection | **Reference Only** |
| `style.css` (child stylesheet) | `style.css` (standalone theme header) | Theme metadata declaration, root token imports | **Migrated (Phase 1)** |
| `package.json` | `package.json` | Tailwind build scripts (`dev`, `build`), devDependencies | **Migrated (Phase 2)** |
| `tailwind.config.js` | `tailwind.config.js` | Tailwind theme extensions, custom color palette, safelist | **Migrated (Phase 2)** |
| `postcss.config.js` | `postcss.config.js` | PostCSS config for Tailwind and Autoprefixer | **Migrated (Phase 2)** |
| `.gitignore` | `.gitignore` | Ignores `node_modules`, `assets/dist`, local IDE artifacts | **Migrated (Phase 1)** |

---

### 2.2 Template Parts (`/template-parts/`)

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `template-parts/header/site-header.php` | `template-parts/header/site-header.php` | Responsive 2-row mobile header (<768px) and compact single-row desktop header (>=768px) | **Migrated (Phase 3)** |
| `template-parts/footer/site-footer.php` | `template-parts/footer/site-footer.php` | Modern dark footer with industrial silhouette background, WhatsApp link, and mobile accordion | **Migrated (Phase 3)** |
| `template-parts/navigation/mobile-bottom-nav.php` | `template-parts/navigation/mobile-bottom-nav.php` | Fixed mobile bottom nav bar (64px) & sliding Categories Drawer bottom sheet (82vh) | **Migrated (Phase 3)** |
| `template-parts/cart/cart-drawer.php` | `template-parts/cart/cart-drawer.php` | Responsive shopping cart drawer: Desktop sliding drawer (460px) & Mobile bottom sheet (90dvh) | **Migrated (Phase 3)** |
| `template-parts/cart/wishlist-drawer.php` | `template-parts/cart/wishlist-drawer.php` | Responsive wishlist drawer: Desktop sliding drawer (460px) & Mobile bottom sheet (90dvh) | **Migrated (Phase 3)** |
| `template-parts/scroll-to-top.php` | `template-parts/scroll-to-top.php` | Floating scroll-to-top round CTA with safe-area bottom offset | **Migrated (Phase 3)** |
| `template-parts/banner/promo-deals-banner.php` | `template-parts/banner/promo-deals-banner.php` | High-impact homepage promotional banner with industrial graphic & CTA button | **Pending (Phase 5)** |
| `template-parts/woocommerce/content-product-card.php` | `template-parts/woocommerce/content-product-card.php` | Unified product card: 1:1 image aspect, sale countdown, rating badge, price, quick-add CTA | **Migrated (Phase 4)** |
| `template-parts/woocommerce/section-best-sellers.php` | `template-parts/woocommerce/section-best-sellers.php` | Dynamic best-sellers product carousel with sales velocity query | **Migrated (Phase 4)** |
| `template-parts/woocommerce/shop-filters.php` | `template-parts/woocommerce/shop-filters.php` | Desktop sticky sidebar filter & Mobile slide-over filters drawer (`max-w-xs sm:max-w-sm`) | **Migrated (Phase 4)** |
| `template-parts/woocommerce/single-product-custom.php` | `template-parts/woocommerce/single-product-custom.php` | Custom single product view: gallery zoom, specs table, WhatsApp order CTA, trust badges | **Migrated (Phase 4)** |
| `template-parts/woocommerce/sticky-add-to-cart.php` | `template-parts/woocommerce/sticky-add-to-cart.php` | Single product sticky bottom bar with quantity, price, and instant buy trigger | **Migrated (Phase 4)** |
| `template-parts/admin-portal/tab-notifications.php` | `template-parts/admin-portal/tab-notifications.php` | Admin portal in-app push notification trigger & broadcast manager | **Pending (Phase 5)** |
| `template-parts/admin-portal/tab-shipping.php` | `template-parts/admin-portal/tab-shipping.php` | Admin portal shipping zones & delivery fee configuration sub-screen | **Pending (Phase 5)** |

---

### 2.3 WooCommerce Template Overrides (`/woocommerce/`)

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `woocommerce/archive-product.php` | `woocommerce/archive-product.php` | Shop & category catalog archive grid layout with filter sidebar integration | **Migrated (Phase 4)** |
| `woocommerce/content-product.php` | `woocommerce/content-product.php` | Loop product wrapper delegating to `template-parts/woocommerce/content-product-card` | **Migrated (Phase 4)** |
| `woocommerce/single-product.php` | `woocommerce/single-product.php` | Single product main template delegating to `content-single-product` | **Migrated (Phase 4)** |
| `woocommerce/content-single-product.php` | `woocommerce/content-single-product.php` | Single product content wrapper delegating to `template-parts/woocommerce/single-product-custom` | **Migrated (Phase 4)** |
| `woocommerce/loop/no-products-found.php` | `woocommerce/loop/no-products-found.php` | Friendly empty state with reset filters button and search input | **Migrated (Phase 4)** |
| `woocommerce/cart/cart.php` | `woocommerce/cart/cart.php` | Clean single-column stacked cart layout with quantity controls and coupon form | **Migrated (Phase 4)** |
| `woocommerce/cart/cart-empty.php` | `woocommerce/cart/cart-empty.php` | Engaging empty cart screen with continue shopping CTA | **Migrated (Phase 4)** |
| `woocommerce/cart/cart-totals.php` | `woocommerce/cart/cart-totals.php` | Order summary totals card with free shipping progress bar | **Migrated (Phase 4)** |
| `woocommerce/cart/mini-cart.php` | `woocommerce/cart/mini-cart.php` | Mini cart items loop rendered inside header popover and cart drawer | **Migrated (Phase 3)** |
| `woocommerce/checkout/form-checkout.php` | `woocommerce/checkout/form-checkout.php` | Multi-step checkout: Step 1 (Customer & Address), Step 2 (Shipping & Payment) | **Migrated (Phase 4)** |
| `woocommerce/checkout/form-coupon.php` | `woocommerce/checkout/form-coupon.php` | Inline collapsible coupon input accordion for checkout | **Migrated (Phase 4)** |
| `woocommerce/checkout/form-login.php` | `woocommerce/checkout/form-login.php` | Seamless login accordion prompt for returning customers | **Migrated (Phase 4)** |
| `woocommerce/checkout/payment.php` | `woocommerce/checkout/payment.php` | Payment gateways container and place-order submit button | **Migrated (Phase 4)** |
| `woocommerce/checkout/payment-method.php` | `woocommerce/checkout/payment-method.php` | Styled radio cards for payment options (COD, Paymob, Fawry) | **Migrated (Phase 4)** |
| `woocommerce/checkout/review-order.php` | `woocommerce/checkout/review-order.php` | Checkout order items table, pricing breakdown, and shipping selector | **Migrated (Phase 4)** |
| `woocommerce/checkout/thankyou.php` | `woocommerce/checkout/thankyou.php` | Order success confirmation screen with order summary, payment status, customer details | **Migrated (Phase 4)** |
| `woocommerce/myaccount/my-account.php` | `woocommerce/myaccount/my-account.php` | Account portal shell rendering navigation sidebar and active endpoint content | **Migrated (Phase 4)** |
| `woocommerce/myaccount/navigation.php` | `woocommerce/myaccount/navigation.php` | Sidebar navigation with left chevron icons, badges, and active state highlights | **Migrated (Phase 4)** |
| `woocommerce/myaccount/dashboard.php` | `woocommerce/myaccount/dashboard.php` | Account dashboard: greeting, order statistics, quick shortcuts, recent orders list | **Migrated (Phase 4)** |
| `woocommerce/myaccount/orders.php` | `woocommerce/myaccount/orders.php` | Customer order history list with status pill badges, item previews, reorder button | **Migrated (Phase 4)** |
| `woocommerce/myaccount/view-order.php` | `woocommerce/myaccount/view-order.php` | Detailed single order timeline tracking, items table, customer address breakdown | **Migrated (Phase 4)** |
| `woocommerce/myaccount/form-login.php` | `woocommerce/myaccount/form-login.php` | Noon-style split auth: Login & Register tabs, Egyptian phone validation, password meter | **Migrated (Phase 4)** |
| `woocommerce/myaccount/form-edit-account.php` | `woocommerce/myaccount/form-edit-account.php` | Profile details form: Name, Egyptian phone, birthdate, password change | **Migrated (Phase 4)** |
| `woocommerce/myaccount/form-edit-address.php` | `woocommerce/myaccount/form-edit-address.php` | Address editor with Egyptian governorates dropdown and phone validation | **Migrated (Phase 4)** |
| `woocommerce/myaccount/my-address.php` | `woocommerce/myaccount/my-address.php` | Address book overview showing billing and shipping cards with edit buttons | **Migrated (Phase 4)** |
| `woocommerce/myaccount/my-notifications.php` | `woocommerce/myaccount/my-notifications.php` | In-app notification feed endpoint with unread filters and mark-all-read action | **Migrated (Phase 4)** |
| `woocommerce/myaccount/admin-hub.php` | `woocommerce/myaccount/admin-hub.php` | Quick Admin Hub dashboard shortcut embedded inside My Account for store managers | **Migrated (Phase 4)** |
| `woocommerce/myaccount/form-lost-password.php` | `woocommerce/myaccount/form-lost-password.php` | Clean password recovery request screen | **Migrated (Phase 4)** |
| `woocommerce/myaccount/form-reset-password.php` | `woocommerce/myaccount/form-reset-password.php` | Clean new password confirmation screen | **Migrated (Phase 4)** |
| `woocommerce/emails/email-header.php` | `woocommerce/emails/email-header.php` | Branded transactional email header with store logo and clean RTL typography | **Migrated (Phase 4)** |
| `woocommerce/emails/email-footer.php` | `woocommerce/emails/email-footer.php` | Branded transactional email footer with copyright and contact links | **Migrated (Phase 4)** |
| `woocommerce/emails/email-addresses.php` | `woocommerce/emails/email-addresses.php` | Transactional email customer billing and shipping address layout | **Migrated (Phase 4)** |
| `woocommerce/emails/email-order-details.php` | `woocommerce/emails/email-order-details.php` | Transactional email order items and totals table | **Migrated (Phase 4)** |
| `woocommerce/notices/error.php` | `woocommerce/notices/error.php` | Modern inline error notice card with red alert styling | **Migrated (Phase 3)** |
| `woocommerce/notices/notice.php` | `woocommerce/notices/notice.php` | Modern inline informational notice card with blue alert styling | **Migrated (Phase 3)** |
| `woocommerce/notices/success.php` | `woocommerce/notices/success.php` | Modern inline success notice card with green alert styling | **Migrated (Phase 3)** |

---

### 2.4 Modular Backend Logic (`/inc/`)

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `functions.php` (Core setup) | `inc/setup.php` | Theme supports (`title-tag`, `post-thumbnails`, `woocommerce`), nav menus, body classes | **Migrated (Phase 1)** |
| `functions.php` (Asset enqueues) | `inc/assets.php` | Cairo & Inter fonts, compiled CSS/JS enqueues with timestamp versioning | **Migrated (Phase 1)** |
| `functions.php` (WC hooks) | `inc/woocommerce-setup.php` | Disable default WC styles, declare HPOS compatibility | **Migrated (Phase 1)** |
| `inc/wishlist.php` | `inc/wishlist.php` | Wishlist engine: user meta & cookie storage, AJAX toggling, drawer HTML renderer | **Migrated (Phase 3)** |
| `inc/cart-drawer.php` | `inc/cart-drawer.php` | AJAX Cart Drawer endpoints: `update_cart_qty`, `remove_cart_item`, `add_to_cart` | **Migrated (Phase 3)** |
| `inc/address-manager.php` | `inc/address-manager.php` | Customer multi-address management logic and user meta synchronization | **Migrated (Phase 4)** |
| `inc/checkout-customer-step.php` | `inc/checkout-customer-step.php` | Multi-step checkout controller, address selection/save/delete AJAX handlers | **Migrated (Phase 4)** |
| `inc/custom-payment-gateways.php` | `inc/custom-payment-gateways.php` | Fawry Sandbox & Paymob Sandbox test payment gateway classes | **Migrated (Phase 4)** |
| `inc/payments.php` | `inc/payments.php` | Payment gateways ordering, COD fallback, order payment meta handling | **Migrated (Phase 4)** |
| `inc/shipping-zones.php` | `inc/shipping-zones.php` | Egyptian governorates shipping rates & free shipping calculation | **Migrated (Phase 4)** |
| `inc/account-details.php` | `inc/account-details.php` | Account profile fields (phone, birthdate), email verification handlers | **Migrated (Phase 4)** |
| `inc/account-navigation.php` | `inc/account-navigation.php` | Custom account endpoints (`my-reviews`, `admin-hub`), navigation menu filtering | **Migrated (Phase 4)** |
| `inc/notifications.php` | `inc/notifications.php` | In-app push notifications engine, endpoint `my-notifications`, polling API | **Migrated (Phase 4)** |
| `inc/product-meta.php` | `inc/product-meta.php` | Product technical specification metaboxes (origin, cut length, warranty, spare parts) | **Migrated (Phase 4)** |
| `functions.php` (Catalog & discount logic) | `inc/catalog.php` | Faceted catalog query filters, discount calculators, on-sale products loop | **Migrated (Phase 4)** |
| `inc/template-functions.php` | `inc/template-functions.php` | Live search, cart fragments, wishlist query helpers, and discount badges | **Migrated (Phase 5)** |
| `inc/checkout.php` | `inc/checkout.php` | Egyptian phone regex validation, checkout fields, order meta & receipt helpers | **Migrated (Phase 5)** |
| `inc/modules-config.php` | `inc/modules-config.php` + `inc/modules/loader.php` | Extensible modular feature toggle configuration & dynamic dispatcher | **Migrated (Phase 5)** |
| `inc/quick-product-manager.php` | `inc/modules/quick-product-manager/quick-product-manager.php` | Front-end quick product editor for store managers (AJAX inline edit/delete) | **Migrated (Phase 5)** |
| `inc/inquiries.php` | `inc/modules/inquiries/inquiries.php` | Contact page custom inquiry CPT/metaboxes and email notification dispatcher | **Migrated (Phase 5)** |
| `inc/seo-schema.php` | `inc/seo-schema.php` | Clean JSON-LD structured data for products, organization, and breadcrumbs | **Pending (Phase 6)** |
| `inc/security.php` | `inc/security.php` | Security hardening: XML-RPC disabling, REST API safeguards, rate limiting | **Pending (Phase 6)** |
| `inc/performance.php` | `inc/performance.php` | Core bloat cleanup, emoji removal, query optimization, asset deferral | **Pending (Phase 6)** |
| `inc/smart-slider.php` | `inc/modules/smart-slider/smart-slider.php` | Smart Slider 3 shortcode integration with graceful responsive fallback | **Migrated (Phase 5)** |
| `inc/portal-analytics.php` | `inc/modules/admin-portal/portal-analytics.php` | Admin portal revenue, orders, and sales performance analytics endpoints | **Migrated (Phase 5)** |
| `inc/portal-orders.php` | `inc/modules/admin-portal/portal-orders.php` | Admin portal order management, status updates, bulk processing | **Migrated (Phase 5)** |
| `inc/portal-categories.php` | `inc/modules/admin-portal/portal-categories.php` | Admin portal category specs generator & brand assignment | **Migrated (Phase 5)** |
| `inc/portal-coupons.php` | `inc/modules/admin-portal/portal-coupons.php` | Admin portal discount coupon generator and manager | **Migrated (Phase 5)** |
| `inc/portal-discounts.php` | `inc/modules/admin-portal/portal-discounts.php` | Admin portal automated percentage/fixed discounts controller | **Migrated (Phase 5)** |
| `inc/portal-promotions.php` | `inc/modules/admin-portal/portal-promotions.php` | Admin portal countdown banners and promotional campaigns | **Migrated (Phase 5)** |
| `inc/portal-users.php` | `inc/modules/admin-portal/portal-users.php` | Admin portal customer directory, search, and user stats | **Migrated (Phase 5)** |

---

### 2.5 Frontend JavaScript Files (`/assets/js/`)

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `assets/js/header.js` | `assets/src/js/header.js` | Live autocomplete search, header wishlist counter, cart dropdown | **Migrated (Phase 3)** |
| `assets/js/main.js` | `assets/src/js/main.js` | Global front-end events, smooth scrolling, generic helpers | **Migrated (Phase 3)** |
| `assets/js/cart-drawer.js` | `assets/src/js/cart-drawer.js` | Cart drawer open/close, AJAX quantity buttons, slide animation | **Migrated (Phase 3)** |
| `assets/js/wishlist-drawer.js` | `assets/src/js/wishlist-drawer.js` | Wishlist drawer open/close, toggle hearts, AJAX remove/add | **Migrated (Phase 3)** |
| `assets/js/mobile-nav.js` | `assets/src/js/mobile-nav.js` | Mobile bottom bar active item sync, Categories Drawer sheet toggle | **Migrated (Phase 3)** |
| `assets/js/notices.js` | `assets/src/js/notices.js` | Removed (Replaced with clean inline alerts) | **Removed** |
| `assets/js/footer.js` | `assets/src/js/footer.js` | Mobile footer collapsible accordions for fast mobile browsing | **Migrated (Phase 3)** |
| `assets/js/shop-filters.js` | `assets/src/js/shop-filters.js` | Catalog filters: mobile drawer toggle, price range, active pills | **Migrated (Phase 4)** |
| `assets/js/mobile-sticky-bar.js` | `assets/src/js/mobile-sticky-bar.js` | Single product sticky buy bar: reveals after scrolling past hero CTA | **Migrated (Phase 4)** |
| `assets/js/checkout-customer-step.js` | `assets/src/js/checkout-customer-step.js` | Multi-step checkout client controller: address cards, modals, transitions | **Migrated (Phase 4)** |
| `assets/js/checkout-validation.js` | `assets/src/js/checkout-validation.js` | Egyptian phone live regex validation on `#billing_phone` | **Migrated (Phase 4)** |
| `assets/js/notifications.js` | `assets/src/js/notifications.js` | In-app notification polling client (35s interval), unread bell badge | **Migrated (Phase 4)** |
| `assets/js/admin-quick-manager.js` | `assets/src/js/admin-quick-manager.js` | Front-end quick product editor modal for logged-in admins | **Pending (Phase 5)** |
| `assets/js/chart.umd.min.js` | `assets/src/js/vendor/chart.umd.min.js` | Vendor Chart.js library for Admin Portal analytics visualizations | **Pending (Phase 5)** |
| `assets/js/portal-analytics.js` | `assets/src/js/portal/portal-analytics.js` | Admin portal analytics rendering: revenue charts, sales breakdown | **Pending (Phase 5)** |
| `assets/js/portal-orders.js` | `assets/src/js/portal/portal-orders.js` | Admin portal live order table filtering, status changes, modals | **Pending (Phase 5)** |
| `assets/js/portal-shipping.js` | `assets/src/js/portal/portal-shipping.js` | Admin portal shipping rates editor by Egyptian governorate | **Pending (Phase 5)** |
| `assets/js/portal-coupons.js` | `assets/src/js/portal/portal-coupons.js` | Admin portal coupon creator, copy coupon code, date picker | **Pending (Phase 5)** |
| `assets/js/portal-discounts.js` | `assets/src/js/portal/portal-discounts.js` | Admin portal sitewide discount rules and scheduling | **Pending (Phase 5)** |
| `assets/js/portal-promotions.js` | `assets/src/js/portal/portal-promotions.js` | Admin portal promotional banners and deals configuration | **Pending (Phase 5)** |
| `assets/js/portal-users.js` | `assets/src/js/portal/portal-users.js` | Admin portal customer list search and spend history modal | **Pending (Phase 5)** |
| `assets/js/portal-offers.js` | `assets/src/js/portal/portal-offers.js` | Admin portal flash sale countdown synchronizer | **Pending (Phase 5)** |

---

### 2.6 Stylesheets & Design Tokens (`/assets/css/`)

| Source File (`test-theme`) | Target File (`custom-store-boilerplate`) | Category / Responsibility | Migration Status |
| :--- | :--- | :--- | :--- |
| `assets/css/variables.css` | `assets/src/css/tokens.css` | Centralized `:root` design tokens (colors, typography, radii, shadows) | **Migrated (Phase 1)** |
| `assets/css/src/input.css` | `assets/src/css/input.css` | Tailwind directives, component layer (`.btn-primary`, `.ui-card`), RTL dropdowns | **Migrated (Phase 2)** |
| `assets/css/checkout-customer-step.css` | `assets/src/css/pages/checkout.css` | Multi-step checkout styling: step indicator, address cards (262 `!important` stripped to 0) | **Migrated (Phase 4)** |
| `style.css` (catalog rules) | `assets/src/css/pages/catalog.css` | Catalog 12-col grid, filter drawer, active facet badges (0 `!important`) | **Migrated (Phase 4)** |
| `style.css` (product rules) | `assets/src/css/pages/single-product.css` | Single product gallery, sticky bar, specs table (0 `!important`) | **Migrated (Phase 4)** |
| `style.css` (cart rules) | `assets/src/css/pages/cart.css` | Stacked cart item cards, totals panel, quantity triggers (0 `!important`) | **Migrated (Phase 4)** |
| `style.css` (account rules) | `assets/src/css/pages/account.css` | Split login tabs, customer portal navigation, address book (0 `!important`) | **Migrated (Phase 4)** |

---

### 2.7 Media & Static Assets (`/assets/images/`)

| Source Asset (`test-theme`) | Target Asset (`custom-store-boilerplate`) | Responsibility |
| :--- | :--- | :--- |
| `assets/images/eagle-logo.webp` | `assets/images/eagle-logo.webp` | Store logo icon (32x32px) used across Header, Footer, and Invoices |
| `assets/images/header-bg.jpg` | `assets/images/header-bg.jpg` | Dark gradient header background texture |
| `assets/images/promo-deals-bg.webp` | `assets/images/promo-deals-bg.webp` | Promotional deals banner background |
| `assets/images/footer-tools-bg.webp` | `assets/images/footer-tools-bg.webp` | Industrial tools silhouette background for footer |
| `assets/images/banners/*` (4 images) | `assets/images/banners/*` | Promotional banners for tile cutters, power tools, toolkits |
| `assets/images/categories/*` (5 images) | `assets/images/categories/*` | Category track icons (fasteners, manual tools, power tools, safety, toolboxes) |
| `assets/images/hero/*` (3 images) | `assets/images/hero/*` | Smart slider fallback hero slides |
| `assets/images/Products/*` (30 images) | `assets/images/Products/*` | High-resolution demonstration product photography |

---

## 3. Design System & Token Specifications

All visual styles are strictly governed by CSS custom properties defined in [`assets/src/css/tokens.css`](file:///c:/Users/ma705/Local%20Sites/ashrafco/app/public/wp-content/themes/custom-store-boilerplate/assets/src/css/tokens.css). No inline styles or hardcoded hex values are permitted in PHP templates.

### 3.1 Color Palette & Token Mapping

```css
:root {
    /* Brand Primary (Industrial Crimson Red) */
    --color-primary: #d90429;
    --color-primary-hover: #b90323;
    --color-primary-light: #fff1f2;
    --color-primary-active: #99001e;
    
    /* Brand Secondary (Industrial Deep Slate) */
    --color-secondary: #0f172a;
    --color-secondary-hover: #1e293b;
    --color-secondary-light: #334155;

    /* Accent & Warning (Amber Gold) */
    --color-accent: #f59e0b;
    --color-accent-hover: #d97706;

    /* Status & Feedback Tokens */
    --color-success: #16a34a;       /* Emerald Green - Order totals & free shipping */
    --color-success-light: #f0fdf4;
    --color-warning: #eab308;
    --color-warning-light: #fefce8;
    --color-error: #dc2626;         /* Crimson Alert - Stock out & error notices */
    --color-error-light: #fef2f2;
    --color-info: #2563eb;
    --color-info-light: #eff6ff;

    /* Neutrals, Surfaces & Backgrounds */
    --color-bg: #f7f7fa;            /* Neutral light background */
    --color-surface: #ffffff;       /* Pure white card surface */
    --color-surface-subtle: #f8fafc;
    --color-surface-hover: #f1f5f9;
    --color-border: #e2e8f0;        /* Subtle slate border */
    --color-border-subtle: #f1f5f9;
    --color-border-strong: #cbd5e1;

    /* Typography Colors */
    --color-text-dark: #1e293b;     /* High contrast body text */
    --color-text-muted: #64748b;    /* Secondary subtitles & hints */
    --color-text-subtle: #94a3b8;   /* Inactive tabs & placeholders */
    --color-text-inverse: #ffffff;
}
```

### 3.2 Typography Hierarchy

- **Primary Font Family (Arabic)**: `'Cairo', system-ui, -apple-system, sans-serif`
  - Font weights enqueued: `400` (Regular), `500` (Medium), `600` (Semi-Bold), `700` (Bold), `800` (Extra-Bold), `900` (Black).
  - Applied to: All Arabic body copy, headings, buttons, form inputs, badges.
- **Secondary Font Family (Latin / Figures)**: `'Inter', system-ui, -apple-system, sans-serif`
  - Applied to: Latin brand symbols ("A & S"), SKU identifiers, English order numbers, currency symbols.
- **Monospace Font Family**: `ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`
  - Applied to: Fawry reference codes, Paymob transaction IDs, shipping tracking numbers.

### 3.3 Strict Layout & UI Component Dimensions

| Component | Desktop Specification (>= 768px) | Mobile Specification (< 768px) |
| :--- | :--- | :--- |
| **Site Header Height** | Exact `62px` (`h-[62px]`) | 2 Rows (~`110px` total): Row 1 (`48px`), Divider (`1px`), Row 2 (`56px`) |
| **Mobile Bottom Bar** | Hidden (`hidden md:flex`) | Fixed bottom, exact `64px` (`h-16`) + `env(safe-area-inset-bottom)` |
| **Container Max-Width** | `1280px` (`max-w-7xl` / `--container-max-w`) | `100%` with `16px` padding (`px-4`) |
| **Checkout Container** | `1340px` (`--container-checkout-max-w`) | `100%` with `4px` padding (`px-1` to `px-2` for maximum touch real estate) |
| **Cart Drawer** | Sliding Left Drawer: width `460px` (`100dvh`) | Bottom Sheet: max height `90dvh`, rounded top `24px` (`rounded-t-3xl`), drag handle |
| **Wishlist Drawer** | Sliding Right Drawer: width `460px` (`100dvh`) | Bottom Sheet: max height `90dvh`, rounded top `24px` (`rounded-t-3xl`), drag handle |
| **Categories Drawer** | Hidden (accessible via header links) | Slide-up Bottom Sheet: max height `82vh`, rounded top `24px`, drag handle |
| **Shop Filters Drawer** | Sticky Sidebar: `col-span-3`, max-height `calc(100vh - 120px)` | Slide-over Right Sheet: `max-w-xs sm:max-w-sm`, backdrop blur |
| **Scroll-To-Top Button** | `48px` circle (`w-12 h-12`), bottom offset `24px` (`bottom-6`) | `44px` circle (`w-11 h-11`), bottom offset `5rem + env(safe-area-inset-bottom)` |
| **Product Card Image** | Aspect ratio `1:1`, object-contain, padding `12px` | Aspect ratio `1:1`, object-contain, padding `8px` |

---

## 4. Backend Logic & Integrations Map

To achieve 100% backend parity, all action names, hook names, nonces, and meta keys must be replicated without modification.

### 4.1 Exhaustive Custom AJAX Endpoints

| AJAX Action Hook (`wp_ajax_*`) | Nopriv? (`wp_ajax_nopriv_*`) | Security Nonce Key | Handler Purpose |
| :--- | :---: | :--- | :--- |
| `test_theme_ajax_search` | Yes | `test_theme_search_nonce` | Autocomplete product live search (returns title, thumb, price, stock) |
| `ashrafco_add_to_cart` | Yes | `ashrafco_cart_drawer_nonce` | AJAX single & variable product add-to-cart with drawer refresh |
| `ashrafco_update_cart_qty` | Yes | `ashrafco_cart_drawer_nonce` | Instant cart drawer quantity increments and decrements |
| `ashrafco_remove_cart_item` | Yes | `ashrafco_cart_drawer_nonce` | Remove cart line item and recalculate subtotal & shipping bar |
| `ashrafco_get_wishlist_items` | Yes | `ashrafco_wishlist_nonce` | Fetch user wishlist product IDs and counts |
| `ashrafco_toggle_wishlist` | Yes | `ashrafco_wishlist_nonce` | Add/remove product from wishlist (stores in cookie or user meta) |
| `ashrafco_remove_from_wishlist` | Yes | `ashrafco_wishlist_nonce` | Remove item specifically from wishlist drawer |
| `ashrafco_get_wishlist_drawer_html` | Yes | `ashrafco_wishlist_nonce` | Fetch dynamic HTML snippet for wishlist drawer contents |
| `ashrafco_checkout_select_address` | No | `ashrafco_checkout_customer` | Select one of customer's saved addresses in multi-step checkout |
| `ashrafco_checkout_save_address` | No | `ashrafco_checkout_customer` | Create or update a customer saved shipping address |
| `ashrafco_checkout_delete_address` | No | `ashrafco_checkout_customer` | Delete a stored customer address |
| `ashrafco_portal_analytics_data` | No | `ashrafco_portal_analytics_nonce` | Provide date-range filtered sales, orders, and average order value data |
| `ashrafco_portal_update_order_status` | No | `ashrafco_portal_order_nonce` | Admin Portal instant order status updater (unified 4 statuses: processing, shipped, completed, cancelled) |
| `ashrafco_portal_bulk_update_orders` | No | `ashrafco_portal_order_nonce` | Admin Portal bulk status batch processor |
| `ashrafco_portal_order_details` | No | `ashrafco_portal_order_nonce` | Admin Portal modal view for order itemization and customer details |
| `ashrafco_portal_get_coupons` | No | `ashrafco_portal_coupons_nonce` | Admin Portal fetch active, expired, and scheduled coupons |
| `ashrafco_portal_get_discounts` | No | `ashrafco_portal_discounts_nonce` | Admin Portal fetch sitewide automatic discount rules |
| `ashrafco_portal_get_promotions` | No | `ashrafco_portal_promotions_nonce` | Admin Portal fetch promotional countdown banner configurations |
| `ashrafco_portal_get_users` | No | `ashrafco_portal_users_nonce` | Admin Portal paginated customer directory with lifetime spend |
| `ashrafco_get_category_specs` | No | `ashrafco_portal_ajax_nonce` | Fetch dynamic technical specifications schema for given category |
| `ashrafco_add_brand` | No | `ashrafco_portal_ajax_nonce` | Create new brand taxonomy term directly from admin portal |
| `test_theme_quick_update_product` | No | `test_theme_quick_manager_nonce` | Front-end quick product editor: update title, price, stock in place |
| `test_theme_quick_delete_product` | No | `test_theme_quick_manager_nonce` | Front-end quick product editor: move product to trash |
| `ashrafco_run_perf_tests` | Yes | `ashrafco_perf_nonce` | Diagnostic performance testing utility |

---

### 4.2 User Meta, Post Meta & Order Meta Schema

#### User Meta Keys (`wp_usermeta`)
- `billing_phone` *(string)*: Primary Egyptian mobile phone number (normalized to `01[0125]XXXXXXXX`). Used for authentication and checkout.
- `shipping_phone` *(string)*: Shipping contact phone number.
- `account_birthdate` *(string)*: Customer date of birth (`YYYY-MM-DD`).
- `ashrafco_email_verified` *(string: '1'|'0')*: Email verification flag.
- `_ashrafco_saved_addresses` *(array)*: Serialized array of saved addresses for the multi-step checkout address manager. Each item contains:
  `['id', 'title', 'first_name', 'last_name', 'phone', 'state', 'city', 'address_1', 'is_default']`.
- `_ashrafco_wishlist` *(array)*: Array of product IDs currently saved by the authenticated user.

#### Product Post Meta Keys (`wp_postmeta`)
- `_test_theme_country_of_origin` *(string)*: Country of manufacture (e.g., إيطاليا، ألمانيا، الصين).
- `_test_theme_max_cut_length` *(string)*: Maximum cutting length for tile cutters (e.g., 1200 مم).
- `_test_theme_max_thickness` *(string)*: Maximum material cutting thickness (e.g., 20 مم).
- `_test_theme_weight` *(string)*: Product physical weight (e.g., 18.5 كجم).
- `_test_theme_warranty` *(string)*: Product warranty description (e.g., ضمان عامين معتمد).
- `_test_theme_spare_parts` *(string)*: Availability of original spare parts (e.g., قطع غيار أصلية متوفرة).
- `_test_theme_brand` *(string)*: Brand name identifier.
- `_test_theme_model` *(string)*: Model number or manufacturer code.
- `_test_theme_tool_type` *(string)*: Specific categorization (e.g., قواطع سيراميك يدوية).
- `_test_theme_material` *(string)*: Primary build material.
- `_test_theme_usage` *(string)*: Recommended operational usage.
- `_test_theme_package_contents` *(string)*: Box contents list.
- `_test_theme_dimensions` *(string)*: Physical dimensions.
- `_ashrafco_category_spec_values` *(array)*: Structured dynamic specifications key-value map.

#### Inquiry Post Meta Keys (`_inquiry_*`)
- `_inquiry_name`, `_inquiry_phone`, `_inquiry_email`, `_inquiry_type`, `_inquiry_branch`, `_inquiry_ip`, `_email_status`, `_email_sent_at`, `_email_error`.

#### Order Meta Keys (`wp_wc_orders_meta` / HPOS)
- `_fawry_reference_number` *(string)*: Fawry Pay electronic reference code for cash collection.
- `_paymob_transaction_id` *(string)*: Paymob payment transaction ID.
- `_paymob_auth_code` *(string)*: Paymob authorization authorization code.
- `_paymob_payment_method` *(string)*: Card / Mobile Wallet indicator.
- `_paymob_paid_at` *(string)*: Timestamp of confirmed payment.
- `_paymob_failed_txn_id`, `_paymob_failed_reason` *(string)*: Transaction diagnostic meta on failed attempts.

---

### 4.3 Custom Validation & Business Logic

#### Egyptian Mobile Phone Normalization & Validation
- **Regex**: `/^01[0125][0-9]{8}$/` (Strictly 11 digits starting with `010`, `011`, `012`, or `015`).
- **Sanitization Pipeline**:
  1. Strip all spaces, dashes, and special characters: `preg_replace('/[\s\-]/', '', $phone)`.
  2. Normalize international prefix `+20` or `0020` to leading `0`.
  3. Normalize prefix `20` (if 12 digits total) to leading `0`.
  4. Validate against regex; throw localized WooCommerce error on failure:
     `'يرجى إدخال رقم هاتف محمول مصري صحيح يبدأ بـ 010 أو 011 أو 012 أو 015 ومكون من 11 رقمًا (مثال: 01012345678).'`

#### Dual Phone / Email Authentication
- Hook: `add_filter('authenticate', 'test_theme_authenticate_by_phone', 25, 3)`.
- If user enters a phone number in the username field, look up user by meta `billing_phone` before fallback to standard username/email authentication.

#### Egyptian Governorates Data Mapping
- Filter: `add_filter('woocommerce_states', 'test_theme_egyptian_governorates')`.
- Returns localized Arabic names for all 27 governorates under country key `'EG'` (e.g., `'EGC' => 'القاهرة'`, `'EGGZ' => 'الجيزة'`, `'EGALX' => 'الإسكندرية'`).

#### Custom Rewrite Endpoints
- `my-reviews` (registered via `add_rewrite_endpoint('my-reviews', EP_ROOT | EP_PAGES)`).
- `my-notifications` (registered via `add_rewrite_endpoint('my-notifications', EP_ROOT | EP_PAGES)`).

---

## 5. Phased Step-by-Step Migration Roadmap

This actionable checklist is the operational roadmap for this project. Every step must be executed in order and verified before marking as complete.

### Phase 1: Architecture Inception & Core Scaffolding
- [x] **Step 1.1**: Initialize clean standalone repository structure in `custom-store-boilerplate/`.
- [x] **Step 1.2**: Create base `style.css` declaring standalone theme metadata without parent theme dependencies.
- [x] **Step 1.3**: Configure modular autoloader `functions.php` (23 lines) pointing to `/inc/`.
- [x] **Step 1.4**: Create `inc/setup.php` declaring HTML5 support, responsive embeds, nav menus, and Arabic body classes.
- [x] **Step 1.5**: Create `inc/woocommerce-setup.php` suppressing legacy styles (`woocommerce_enqueue_styles`) and declaring HPOS compatibility.
- [x] **Step 1.6**: Create `inc/assets.php` configuring Cairo & Inter Google Fonts and asset cache-busting pipeline.
- [x] **Step 1.7**: Create `assets/src/css/tokens.css` with 100% of brand colors, radii, shadows, and container variables.
- [x] **Step 1.8**: Create clean HTML5 shells: `header.php`, `footer.php`, and fallback `index.php`.

---

### Phase 2: Design System, Build Pipeline & Token Consolidation
- [x] **Step 2.1**: Set up local build pipeline in `custom-store-boilerplate/`:
  - Create `package.json` with build scripts:
    `"dev": "tailwindcss -i ./assets/src/css/input.css -o ./assets/dist/css/main.min.css --watch"`
    `"build": "tailwindcss -i ./assets/src/css/input.css -o ./assets/dist/css/main.min.css --minify"`
  - Install dependencies: `tailwindcss@3.4.19`, `postcss@^8.4.47`, `autoprefixer@^10.4.20`.
- [x] **Step 2.2**: Configure `tailwind.config.js` referencing all PHP templates and JS files, with safelist for dynamic classes (password meter, badges, drawer transitions).
- [x] **Step 2.3**: Create `assets/src/css/input.css` importing `tokens.css`, Tailwind layers, and core components (`.btn-primary`, `.btn-secondary`, `.ui-card`, RTL select styling) with **0 `!important` declarations**.
- [x] **Step 2.4**: Run `npm run build` and verify production output in `assets/dist/css/main.min.css`.

---

### Phase 3: Shell & Global UI Components
- [x] **Step 3.1**: Migrate `template-parts/header/site-header.php`:
  - Mobile 2-row layout (<768px): Brand + eagle logo on right, notification bell + hamburger on left, search bar + white wishlist pill below.
  - Desktop single-row layout (>=768px): Exact 62px height, active red nav underline indicator, centered live search, account & cart triggers.
- [x] **Step 3.2**: Migrate `assets/src/js/header.js` and register AJAX live search endpoint (`test_theme_ajax_search` / `test_theme_search_nonce`).
- [x] **Step 3.3**: Migrate `template-parts/footer/site-footer.php` and `assets/src/js/footer.js`:
  - Dark `#080C14` background, ambient red glow, tools silhouette overlay.
  - Desktop multi-column grid, mobile collapsible accordion, WhatsApp direct contact link.
- [x] **Step 3.4**: Migrate `template-parts/navigation/mobile-bottom-nav.php` and `assets/src/js/mobile-nav.js`:
  - Fixed 64px bottom nav with active page indicator and live cart counter badge.
  - Sliding Categories Drawer bottom sheet (82vh max-height, touch-swipe drag handle).
- [x] **Step 3.5**: Migrate Shopping Cart Drawer:
  - Backend: `inc/cart-drawer.php` (`ashrafco_add_to_cart`, `ashrafco_update_cart_qty`, `ashrafco_remove_cart_item`).
  - Template: `template-parts/cart/cart-drawer.php` & `woocommerce/cart/mini-cart.php`.
  - Frontend: `assets/src/js/cart-drawer.js` with smooth sliding animation and free shipping progress calculation.
- [x] **Step 3.6**: Migrate Wishlist Drawer:
  - Backend: `inc/wishlist.php` (`ashrafco_get_wishlist_items`, `ashrafco_toggle_wishlist`, `ashrafco_remove_from_wishlist`).
  - Template: `template-parts/cart/wishlist-drawer.php`.
  - Frontend: `assets/src/js/wishlist-drawer.js` with dynamic heart toggle synchronization.
- [x] **Step 3.7**: Migrate Floating Toast Notices:
  - Templates: `woocommerce/notices/error.php`, `woocommerce/notices/notice.php`, `woocommerce/notices/success.php`.
  - Frontend: `assets/src/js/notices.js` with auto-dismiss and swipe gestures.
- [x] **Step 3.8**: Migrate `template-parts/scroll-to-top.php` and `assets/src/js/scroll-to-top.js`.

---

### Phase 4: WooCommerce Templates & Purchasing Funnel Migration
- [x] **Step 4.1**: Discovery & Catalog Views:
  - Templates: `woocommerce/archive-product.php`, `woocommerce/loop/no-products-found.php`, `template-parts/woocommerce/shop-filters.php`, `template-parts/woocommerce/content-product-card.php`, `woocommerce/content-product.php`, `template-parts/woocommerce/section-best-sellers.php`.
  - Backend: `inc/catalog.php` (orderby, classification, country filters, faceted counts, discount data, on-sale query).
  - Frontend: `assets/src/js/shop-filters.js`.
  - Styling: `assets/src/css/pages/catalog.css` (12-column grid, sticky filter drawer, mobile slide-over, 0 `!important`).
- [x] **Step 4.2**: Single Product Experience:
  - Templates: `woocommerce/single-product.php`, `woocommerce/content-single-product.php`, `template-parts/woocommerce/single-product-custom.php`, `template-parts/woocommerce/sticky-add-to-cart.php`.
  - Backend: `inc/product-meta.php` (technical specifications metaboxes).
  - Frontend: `assets/src/js/mobile-sticky-bar.js`.
  - Styling: `assets/src/css/pages/single-product.css` (gallery layout, pricing blocks, specs table, WhatsApp order CTA, sticky buy bar, 0 `!important`).
- [x] **Step 4.3**: Cart Experience:
  - Templates: `woocommerce/cart/cart.php`, `woocommerce/cart/cart-totals.php`, `woocommerce/cart/cart-empty.php`.
  - Styling: `assets/src/css/pages/cart.css` (stacked item cards, quantity controls, coupon accordion, free shipping progress bar, 0 `!important`).
- [x] **Step 4.4**: Multi-Step Checkout Funnel:
  - Backend: `inc/checkout-customer-step.php`, `inc/address-manager.php`, `inc/shipping-zones.php`, `inc/payments.php`, `inc/custom-payment-gateways.php` (Fawry & Paymob sandboxes).
  - Templates: `woocommerce/checkout/form-checkout.php`, `form-coupon.php`, `form-login.php`, `payment.php`, `payment-method.php`, `review-order.php`, `thankyou.php`.
  - Frontend: `assets/src/js/checkout-customer-step.js` and `assets/src/js/checkout-validation.js` (Egyptian phone live validation).
  - Styling: `assets/src/css/pages/checkout.css` (262 legacy `!important` occurrences purged to **0**, clean Flexbox/Grid scoping, address cards, modal dialogs).
- [x] **Step 4.5**: Customer Portal & Authentication:
  - Backend: `inc/account-details.php`, `inc/account-navigation.php`, `inc/notifications.php`.
  - Templates: `woocommerce/myaccount/form-login.php` (Noon-style split auth), `dashboard.php`, `orders.php`, `view-order.php`, `my-address.php`, `form-edit-account.php`, `form-edit-address.php`, `form-lost-password.php`, `form-reset-password.php`, `my-notifications.php`, `admin-hub.php`.
  - Frontend: `assets/src/js/notifications.js` (polling engine, unread bell badge, mark-as-read AJAX).
  - Styling: `assets/src/css/pages/account.css` (sidebar navigation, order history cards, status badges, notification cards, 0 `!important`).
- [x] **Step 4.6**: Branded WooCommerce Transactional Emails:
  - Templates: `woocommerce/emails/email-header.php`, `email-footer.php`, `email-addresses.php`, `email-order-details.php`, `email-order-items.php`.
- [x] **Step 4.7**: CSS Modularization & Zero `!important` Pipeline:
  - Created modular page stylesheets in `assets/src/css/pages/`: `catalog.css`, `single-product.css`, `cart.css`, `checkout.css`, `account.css`.
  - Imported all stylesheets into `assets/src/css/input.css` and verified zero `!important` declarations across all files.
  - Successfully built production bundle `assets/dist/css/main.min.css` (157.5 KB) via `npm run build`.

---

### Phase 5: Custom Admin Portal & Specialized Business Modules
- [x] **Step 5.1**: Homepage & Standalone Marketing Templates:
  - `front-page.php`: Smart Slider 3 integration (`inc/modules/smart-slider/smart-slider.php`), touch-swipe Category Track, promo banner, Best Sellers carousel.
  - `template-parts/banner/promo-deals-banner.php`: High-impact promotional banner with industrial graphic and CTA.
  - `404.php`: Clean 404 error template with search input and category shortcuts.
  - `page.php`: Default page layout with container constraints.
- [x] **Step 5.2**: Modular Ashrafco Admin Portal Architecture:
  - Standalone admin portal template `template-admin-portal.php` migrated with **0 `<style>` tags** (purged inline `<style>` to `assets/src/css/pages/admin-portal.css`).
  - Modular sub-templates in `template-parts/admin-portal/`:
    - `tab-shipping.php`: Egyptian governorates delivery fee rules editor.
    - `tab-notifications.php`: In-app push notification broadcast center.
  - Modular backend domain controllers in `inc/modules/admin-portal/`:
    - `portal-analytics.php`, `portal-orders.php`, `portal-categories.php`, `portal-coupons.php`, `portal-discounts.php`, `portal-promotions.php`, `portal-users.php`.
  - Feature toggle configuration in `inc/modules-config.php` and dynamic dispatcher `inc/modules/loader.php`.
- [x] **Step 5.3**: Deals & Countdown Offers Page:
  - Template: `page-offers.php` (purged inline `<style>` to `assets/src/css/pages/catalog.css`).
  - Backend: `inc/modules/offers-engine/offers-engine.php` (promotional hero banner, live countdown timer, discount category filters, on-sale products grid).
- [x] **Step 5.4**: Contact Us & Branch Locator:
  - Backend: `inc/modules/inquiries/inquiries.php` (custom inquiry post type & email notifications).
  - Template: `page-contact.php` (branches directory, inquiry submission form, WhatsApp trigger).
- [x] **Step 5.5**: Public Guest Order Tracking:
  - Template: `page-track-order.php`.
  - Backend: `inc/checkout.php` (`ashrafco_lookup_order_for_tracking`, `ashrafco_get_order_tracking_steps`, `ashrafco_generate_order_receipt_data`).
  - Nonce-verified form query by Order ID + Phone/Email; status timeline rendering.
- [x] **Step 5.6**: Standalone Wishlist Full Page:
  - Template: `page-wishlist.php`.
  - Grid of all saved wishlist items with instant add-to-cart and remove actions.
- [x] **Step 5.7**: Policies & Accordion Terms:
  - Template: `page-policy.php`.
  - Terms of service, privacy policy, and return policies organized in clean collapsible tabs.
- [x] **Step 5.8**: Front-End Quick Product Manager:
  - Backend: `inc/modules/quick-product-manager/quick-product-manager.php`.
  - Frontend: `assets/src/js/admin-quick-manager.js`.

- [x] **Step 5.9**: **Architecture Hardening & PHP Fatal Error Resolution (WP-Admin / Customizer)**:
  - **Fatal Error Resolved**: Identified and eliminated fatal collision `Cannot redeclare function ashrafco_filter_free_shipping_label_arabic()` previously declared simultaneously in `inc/template-functions.php` and `inc/checkout-customer-step.php`.
  - **Modular Cleanup**: Removed legacy redundant functions block (lines 1029-1187) from `inc/checkout-customer-step.php`; centrally maintained them in `inc/checkout.php` and `inc/template-functions.php`.
  - **Redeclaration Protection**: Wrapped all custom storefront and checkout functions in `if (!function_exists(...))` across `inc/template-functions.php`, `inc/checkout.php`, `inc/setup.php`, `inc/modules/offers-engine/offers-engine.php`, and `inc/modules/loader.php`.
  - **WP 6.7+ Textdomain Notice Fixed**: Relocated `register_nav_menus()` from `after_setup_theme` hook to standard `init` hook, eliminating `_load_textdomain_just_in_time` notices.
  - **Verification**: Verified 100% PHP syntax linting pass across all 96 PHP files (`0 syntax errors`); confirmed HTTP 200/302 responses for Customizer, Home, and Checkout without fatal errors or warnings.

- [x] **Step 5.10**: **Customizer Frontend Preview Iframe Out-of-Memory Fix**:
  - **Fatal Error Resolved**: Identified `Allowed memory size of 268435456 bytes exhausted` at `inc/template-functions.php:247` triggered by mutual infinite recursion between `ashrafco_get_wishlist_page_url()` and `ashrafco_get_wishlist_url()`.
  - **Modular Consolidation**: Purged conflicting duplicate wishlist definitions from `inc/template-functions.php` and consolidated all wishlist helpers (`ashrafco_get_wishlist_url`, `test_theme_get_wishlist_url`, `ashrafco_wishlist_template_fallback`) into `inc/wishlist.php`.
  - **Astra Residue Audit**: Verified 0 legacy `astra_*` function calls across all templates (`header.php`, `footer.php`, `index.php`, `front-page.php`).
  - **Verification**: Verified Customizer preview request (`/?customize_theme=custom-store-boilerplate`) delivers a complete, clean 343 KB HTML render with HTTP 200 and zero PHP errors in `debug.log`.

- [x] **Step 5.11**: **Storefront Best Sellers UI & Arabic Badge Encoding Architecture Fix**:
  - **Card Collapsing Resolved**: Replaced unconstrained flex items in `template-parts/woocommerce/section-best-sellers.php` and `assets/src/css/components/cards.css`. Set carousel track to `flex overflow-x-auto gap-4 pb-4 snap-x scrollbar-none items-stretch pt-1 snap-mandatory scroll-smooth no-scrollbar` and explicit card wrapper constraints `.best-sellers-card-item` to `flex-shrink-0 w-[220px] sm:w-[240px] snap-start`.
  - **Button & SVG Sizing**: Standardized all 4 button states in `template-parts/woocommerce/content-product-card.php` with `w-full bg-primary hover:bg-primary-hover text-white font-bold py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors`, enforced shopping cart `<svg>` with `ref-cart-icon w-5 h-5 flex-shrink-0 fill-current text-white`, and added defensive CSS in `assets/src/css/components/cards.css` (`.ref-product-card svg { max-width: 20px; max-height: 20px; }`).
  - **Countdown Timer Pill & Strict Height**: Redesigned `.ref-promo-row.is-countdown` into a compact horizontal pill (height: 28px) with horizontal flex alignment between label ("ينتهي خلال" + clock icon) and 4 mini timer digit boxes (`days / hours / mins / secs`). Eliminated duplicated "ينتهي خلال ينتهي خلال" label and prevented cards from stretching vertically.
  - **Mojibake Sale Badges Eliminated**: Corrected multibyte encoding truncation on Arabic badges (`ØªØ®Ù...`). Standardized `ashrafco_format_sale_badge()` in `inc/template-functions.php` and `$discount_label` in `content-product-card.php` to output clean untruncated Arabic strings (`خصم %d%%` / `تخفيض`) with `dir="rtl"`, and added `ashrafco_custom_sale_flash` hook to `woocommerce_sale_flash` to eliminate default WooCommerce byte slicing.
  - **Asset Compilation**: Executed `npm run build` cleanly compiling `assets/dist/css/main.min.css` (197 KB) with 0 `!important` declarations in custom theme rules.

---

### Phase 6: Hardening, SEO, Performance & Final Release
- [ ] **Step 6.1**: **Zero-!important Audit**: Scan all CSS to confirm 100% elimination of `!important` (Audit Passed: 0 declarations).
- [ ] **Step 6.2**: **Zero-Inline-Style Audit**: Scan all migrated PHP templates to confirm zero inline `<style>` blocks or attributes (Audit Passed: 0 inline styles).
- [ ] **Step 6.3**: **Security Hardening**: Migrate `inc/security.php` (XML-RPC disable, REST API protection, rate limiting).
- [ ] **Step 6.4**: **SEO & Schema Parity**: Migrate `inc/seo-schema.php` (JSON-LD structured data for products, organization, breadcrumbs).
- [ ] **Step 6.5**: **Performance & De-bloat**: Migrate `inc/performance.php` (disable emoji scripts, clean query vars, defer non-critical JS).
- [ ] **Step 6.6**: **Production Asset Build**: Run `npm run build`, inspect minified bundle sizes, verify browser caching hashes.
- [ ] **Step 6.7**: **Visual & Functional Walkthrough**: Perform complete side-by-side verification between `test-theme` and `custom-store-boilerplate` across Desktop (1440px), Tablet (768px), and Mobile (375px).

---

## 6. Session Bootstrapping Prompt

Paste the following standardized prompt into any new AI session to immediately resume migration without context loss:

```markdown
Role: Principal Software Architect & Migration Lead.
Context: We are executing the 1:1 migration of `test-theme` into the clean standalone theme `custom-store-boilerplate`.
Action Required:
1. Open and inspect `custom-store-boilerplate/PROJECT_MEMORY.md`.
2. Locate the first unchecked item (`- [ ]`) in "Section 5: Phased Step-by-Step Migration Roadmap".
3. Deep-read the corresponding source file(s) in `test-theme/` (READ-ONLY).
4. Implement the target file(s) in `custom-store-boilerplate/` adhering strictly to:
   - 100% 1:1 visual parity (layout, typography, responsive behavior, colors).
   - 100% backend logic parity (exact hook names, AJAX actions, nonces, meta keys).
   - Clean architecture: 0 `!important` declarations, 0 inline `<style>` tags, design token usage.
5. Update `PROJECT_MEMORY.md` to check off (`- [x]`) the completed step.
6. Provide a concise summary of implemented files and next pending step.
```

---
*End of Authoritative Blueprint.*
