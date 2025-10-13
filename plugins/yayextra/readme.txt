=== YayExtra - WooCommerce Extra Product Options ===
Contributors: YayCommerce
Donate link: https://yaycommerce.com/yayextra-woocommerce-extra-product-options/
Tags: product addons, woocommerce product options, woocommerce product fields, product customizer, extra product options
Requires at least: 4.0
Requires PHP: 5.3
Tested up to: 6.8
Stable tag: 1.5.6
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt


== Description ==

YayExtra is the ultimate WooCommerce plugin for adding **customizable product options** to your online shop. Whether you're selling apparel, gifts, tech gadgets, or anything in between, YayExtra gives you the flexibility to personalize your products and boost customer satisfaction. 

It comes with a wide range of field types, including dropdown menus, checkboxes, radio buttons, text inputs, number fields, color swatches, multiple-choice lists, and more.

[DEMO](https://demo.yaycommerce.com/yayextra/product/t-shirt/) | [**YAYEXTRA PRO**](https://yaycommerce.com/yayextra-woocommerce-extra-product-options/) 💎

[youtube https://youtu.be/qytEac2_Yr0]

[Documentation](https://docs.yaycommerce.com/yayextra/getting-started/introduction) | [Free vs Pro](https://docs.yaycommerce.com/yayextra/why-upgrade)

###⚡️ FEATURES

**Powerful Custom Product Options**
YayExtra supports many [WooCommerce product field types](https://docs.yaycommerce.com/yayextra/how-it-works/option-types) to serve your diverse use cases:

- Allow customers to input text, number, email, etc.
- Add radio buttons to the original product
- Enable checkbox to allow for privacy policy acknowledgement
- Add button rows to customize the base product
- Add one time fee in percentage or fixed amount
- Add multiple fees to multiple product options
- Display the subtotal for the selected extras

**Multiple Options in an Option Set**
You can add many product custom fields in the same group. Related options can be displayed next to each other or vertically. A product field can trigger the display of the next product field.

**Apply Product Options in Bulk**
A group of product fields can be applied to all products, a group of products in a specific category, a group of products with a specific tag, or hand-picked products.

**Developer-Friendly**
This product options plugin allows using hooks for `before_calculate_totals` function to modify the [cart item line total](https://docs.yaycommerce.com/yayextra/developer-zone).

**WooCommerce Conditional Variations**
YayExtra allows you to create conditional logic that can be combined with the existing custom options. Conditional logic helps show the next product field if the user has selected a specific option value. 

Let's suppose that you sell car parts, so when the customer chooses to have "Accessories" then related options like "Front door items" or "Replacement kit" can be shown on the current product page. Otherwise, if the customer doesn't check the "Accessories" checkbox, then those options will not show up, which will keep your product page neat and clear.

###💎 PREMIUM-ONLY FEATURES

**Advanced Product Addons**
Multiple field types are built in the premium version:

- Image swatches
- Button (multi selectable)
- Swatches (multi selectable)
- Date picker
- Time picker
- File Upload

**Grouping and Bundling Products**
Similar to "related products," you can easily use an existing product as a swatch or option for another product.

- Assign the "Custom Stickers" product as an additional option for a range of "Bag" products.
- Offer a "Matched Cap" as an optional add-on for a "Baseball T-Shirt" to create a coordinated set.
- Quickly [set up product bundles](https://yaycommerce.com/best-ways-to-create-product-bundles/) and upsell opportunities to maximize sales.

###🔑 ENHANCED SETTINGS

Each field type comes with various elements to help you enhance the extra product options: 

- Required field: Require the customer to select an option or enter the information so it can be passed through in the order (Free)
- Placeholder: Add help text or expected value to be entered in the field (Free)
- Set as default: Enable a specific option value to be selected upon product page load (Free)
- Custom image: Use uploaded swatch image to show on product featured image (Premium-only)
- File upload: Add a single or multiple file uploads, make file uploads mandatory or optional, and many other options.
- File upload: Allow specific file formats like PNG, JPG, PDF, DOC, XLS, etc.


== Installation ==
1. Download the plugin from wordpress.org
2. From your WordPress admin dashboard, go to **Plugins** > **Add New**, and upload the yayextra.zip file
3. Install and activate it
4. After activating YayExtra, go to **WooCommerce** > **YayExtra** to create and manage extra product options

== Frequently Asked Questions ==

= I got issues. How can I get support? =
Please [create a topic](https://wordpress.org/support/plugin/yayextra/) or [contact us](https://yaycommerce.com/support/) to get help. We’re sure to resolve the glitch!
To quickly get the answers, please attach screenshots of currently active WooCommerce plugins on your website.

= How can I change the position of the section of extra product options on my product page?? =
In most cases, the added custom product fields will be displayed below the short description and above Add to cart button. If you wish to change its position, you can overwrite the template structure.

= Can I use an existing product as an option for another product? =
Yes, this feature is called Linked Product in YayExtra Pro settings. You can upgrade to [WooCommerce Extra Product Options Pro](https://yaycommerce.com/yayextra-woocommerce-extra-product-options/) to start cross-selling and upselling today!

== Screenshots ==
1. Extra product options on WooCommerce product page
2. Gift options in the product field/extra option list
3. Assign the product option set to the selected products in bulk
4. Admin dashboard settings for WooCommerce extra product options

== Changelog ==

= Jul 12, 2025 - Version 1.5.6 =
- Improved: Product query
- Fixed: SQL Injection for product query

= Jun 25, 2025 - Version 1.5.5 =
- Added: Disable Past Date setting for Date Picker
- Added: Limit setting for Checkbox option
- Added: Show/Hide Tooltip setting for Swatches/MultiSwatches, Button/MultiButton
- Improved: UI for File/Image Upload option
- Improved: Apply Customize settings and missing advanced settings for Preview
- Improved: Style basic for Date/Time Picker
- Fixed: Show/Hide extra options value in mini cart line items

= May 21, 2025 - Version 1.5.4 =
- Added: New setting for multi swatches option to allow a maximum number of options that can be selected

= Mar 26, 2025 - Version 1.5.3 =
- Updated: Change disable weekends to disable Sunday and add disable other days option
- Added: Authorization for Ajax hook 

= Mar 26, 2025 - Version 1.5.3 =
- Updated: Change disable weekends to disable Sunday and add disable other days option (PRO)
- Added: Authorization for Ajax hook 

= Mar 7, 2025 - Version 1.5.2 =
- Improved: JS processing
- Improved: Remove draft text and add required for some fields
- Fixed: The “All” (AND) condition in the products chose by conditions

= Sep 16, 2024 – Version 1.4.0 =
- Fixed: Issues about update total price for variation product
- Fixed: Issue about warning image types
- Updated: WC tested up to: 9.3.1

= Sep 1, 2024 – Version 1.3.9 =
- Updated: Priority of woocommerce_before_calculate_totals hook
- Updated: Compatible with product quantity select element

= Aug 1, 2024 - Version 1.3.8 =
- Fixed: Arbitrary file uploads due to missing file type validation

= Jul 20, 2024 - Version 1.3.7 =
- Updated: WC tested up to 9.1
- Updated: Compatible with WordPress version 6.6

= Apr 25, 2024 - Version 1.3.6 =
- Improved: Auto hide Extra subtotal when added value is 0
- Added: Hook for before_calculate_totals function
- Fixed: Bugs about text field validate

= Mar 15, 2024 - Version 1.3.4 =
- Fixed: Issue with ENT_QUOTES value for dropdown, button, swatches
- Fixed: Issue with Text/Textarea Action logic

= Jan 16, 2024 - Version 1.3.1 =
- Fixed: Dropdown placeholder

= Nov 28, 2023 - Version 1.3 =
- Fixed: Calculate tax for fee/discount
- Fixed: Get option data in JS
- Updated: Remove assigned product if it doesn't exist

= Nov 7, 2023 - Version 1.2.9 =
- Fixed: Double option set on product page

= Oct 18, 2023 - Version 1.2.8 =
- Fixed: Improve product query

= Oct 2, 2023 - Version 1.2.7 =
- Fixed: Compatible with PHP 8

= Sep 15, 2023 - Version 1.2.6 =
- Fixed: Processing

= Aug 30, 2023 – Version 1.2.5 =
- Fixed: Product matching
- Improved: JS processing 

= Aug 11, 2023 – Version 1.2.4 =
- Added: Textarea option type
- Improved: Action logics

= Aug 09, 2023 - Version 1.2.3 =
- Fixed: JS issues

= Aug 07, 2023 - Version 1.2.2 =
- Fixed: JS issues

= Jun 22, 2023 - Version 1.2.1 =
- Fixed: Conditional logic auto renews default value

= Jun 16, 2023 - Version 1.2 =
- Added: Support WooCommerce HPOS

= Jun 14, 2023 - Version 1.1.8 =
- Added: Filter: yayextra_load_js_file_allow 

= Jun 2, 2023 - Version 1.1.7 =
- Added: Priority for option sets 

= May 17, 2023 - Version 1.1.6 =
- Added: YayCommerce Menu 

= May 11, 2023 - Version 1.1.5 =
- Improved: Option field validation
- Fixed: Js issues and duplicate option set

= Apr 13, 2023 - Version 1.1.4 =
- Fixed: Pot file

= Mar 16, 2023 - Version 1.1.3 =
- Fixed: Issue about Add to cart by Ajax 
- Fixed: Issue of variable product 
- Fixed: Compatible with new YayCurrency version

= Jan 19, 2023 - Version 1.1.2 =
- Added: Badge with number
- Improved: UI and text

= Jan 10, 2023 - Version 1.1.1 =
- Improved: Warning when WooCommerce is not active

= Sep 21, 2022 - Version 1.1 =
- Improved: UI
- Fixed: Prefix and PHPCS

= Aug 20, 2022 - Version 1.0 =