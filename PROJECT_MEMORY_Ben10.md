# Ben10 Store --- Project Memory

**Purpose:** This file is the single source of truth for the current
Ben10 watch-store customization. Keep it focused on the active store
project. Do not resume or discuss the previous store's migration history
unless explicitly requested.

## 1. Current Project

-   **Store name:** Ben10
-   **Platform:** WordPress + WooCommerce
-   **Local site:** `http://ben10.local/`
-   **Active theme:** `wp-content/themes/custom-store-boilerplate/`
-   **Store Core plugin:** `wp-content/plugins/ashrafco-core/`
-   **Primary language:** Arabic
-   **Layout:** RTL
-   **Goal:** Turn the current store into a polished, modern watch
    e-commerce brand with a coherent identity, real watch categories,
    accurate product information, and a reliable shopping experience.

## 2. Brand Identity

Use this palette consistently:

-   Ben10 Green: `#A3E635`
-   Deep Black: `#101211`
-   White: `#FFFFFF`
-   Dark Forest Green: `#244A27`

Design direction: - Premium, modern, clean watch retail. - White and
neutral surfaces should keep product photography clear. - Black creates
contrast; green is a deliberate accent for CTAs, active states, and
small highlights. - Avoid making the entire site green or using green in
a way that reduces readability. - Use original brand assets. Do not use
official Ben 10 franchise logos, characters, Omnitrix artwork, or other
copyrighted franchise assets. - Keep Arabic typography and RTL behavior
consistent.

## 3. Work Completed

### Branding foundation

-   Design tokens and Tailwind brand colors were updated for Ben10.
-   New Ben10 logo variants, icon/favicon, and footer background assets
    were reportedly created.
-   Header, mobile navigation, footer, scroll-to-top, and search
    placeholders were updated to Ben10/watch-store language.
-   The production CSS bundle was rebuilt using the existing build
    pipeline.

### Watch image pilot

Three watch concept images were reportedly generated through Google Flow
and registered in the WordPress Media Library:

  Concept       File                                   Reported attachment ID
  ------------- ------------------------------------ ------------------------
  Classic       `ben10-classic-concept-01.png`                             36
  Sport         `ben10-sport-concept-01.png`                               37
  Chronograph   `ben10-chronograph-concept-01.png`                         38

Reported dimensions: 512 × 512 PNG. Verify the actual files, media
attachments, alt text, and visual quality before using them publicly. Do
not create duplicate media attachments if they already exist.

## 4. Catalog Status and Data Safety

-   The WooCommerce catalog still contains legacy industrial-tool
    products; the latest report said the total was 5 products.
-   These products are **not Ben10 watches** and must not be presented
    as watch inventory.
-   The current watch concepts are images only; do not assume they are
    real products available for sale.
-   Do not invent product names, prices, SKUs, stock quantities,
    specifications, warranty, country of origin, customer reviews,
    discounts, delivery promises, or authenticity claims.
-   Do not delete or alter existing products, orders, customers, stock,
    prices, or categories without explicit approval.
-   Before removing old products, confirm the user wants the catalog
    cleared and take a recoverable backup/export. Prefer a controlled,
    reversible cleanup over permanent deletion.
-   Build the new watch catalog only after agreeing on the category
    structure and shared product specifications.

## 5. Proposed Watch Catalog Structure

Treat these as a starting proposal to review before creating terms in
WooCommerce.

### Main categories

-   ساعات رجالي
-   ساعات حريمي
-   ساعات رياضية
-   ساعات كلاسيكية
-   ساعات كرونوغراف

Avoid duplicate or overlapping categories. A product may belong to one
primary category and, where appropriate, additional relevant categories.

### Shared product specifications

The core specification fields should be suitable for most watches and
reusable across categories: - العلامة التجارية - الموديل - نوع الساعة -
الفئة المستهدفة - نوع الحركة - مادة الهيكل - مادة السوار - لون الهيكل -
لون الميناء - مقاومة الماء - نوع الزجاج - نوع الإغلاق - الضمان --- only
when verified - بلد المنشأ --- only when verified

Category-specific specifications may be added where relevant, such as
chronograph functions or sport-specific features. Do not force
irrelevant fields onto every watch.

Before changing the existing category-specification system, inspect how
its fields are stored, rendered, and used by the product editor.
Preserve the existing implementation contracts and ensure the new fields
work in both product creation and product editing.

## 6. Technical Guardrails

-   Work inside the existing theme and Store Core plugin; do not replace
    the architecture.
-   Preserve existing WooCommerce cart, checkout, shipping, account,
    wishlist, search, AJAX, permissions, nonce, hook, and metadata
    behavior.
-   Do not rename internal functions, namespaces, hooks, nonces, option
    keys, or metadata keys merely to remove legacy naming.
-   Edit source CSS under `assets/src/css/` and rebuild with
    `npm run build`. Do not manually edit generated
    `assets/dist/css/main.min.css`.
-   Reuse the current design tokens instead of scattering hard-coded
    brand colors through templates.
-   Keep changes scoped to the current task. Inspect relevant code
    before editing and report files changed, tests run, and any
    remaining risks.
-   Do not claim a change, image generation, import, or test succeeded
    unless there is direct evidence.

## 7. Immediate Next Steps

1.  Verify the three original concept image files and Media Library
    attachments 36--38. Resolve any discrepancy without duplicating
    attachments.
2.  Review the current product catalog and confirm the backup/export and
    exact cleanup scope before deleting legacy products.
3.  Inspect the existing category editor and dynamic
    category-specification implementation.
4.  Agree on the final main/subcategory structure and shared watch
    specifications.
5.  Implement the category and specification changes first; verify
    product creation and editing.
6.  Prepare real watch product entries only when the user supplies or
    approves their names, images, prices, stock, SKUs, and factual
    specifications.
7.  Redesign the homepage around watches after the catalog structure is
    ready. Hide empty or unverified product sections rather than showing
    industrial products or fabricated content.
8.  Test desktop, tablet, and mobile layouts, including RTL, search,
    wishlist, cart, product pages, and checkout.

## 8. Agent Session Startup Instructions

At the start of each session:

1.  Read this file first.
2.  Inspect the current repository state and relevant implementation
    before making changes.
3.  Continue from **Immediate Next Steps**; do not restart the old
    migration plan or reproduce its historical details.
4.  Ask one concise question only if an essential business decision is
    missing.
5.  Make changes in small, verifiable steps and stop at the requested
    scope.
6.  Never delete catalog data or publish placeholder products without
    explicit approval.
7.  Update this file only when the project state materially changes.

**Current priority:** verify the watch image pilot, then prepare a safe
and deliberate transition from the legacy catalog to the Ben10 watch
catalog.
