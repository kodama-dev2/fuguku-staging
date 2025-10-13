=== Cekongkir ===
Contributors: komerce
Tags: JNE, POS Indonesia, TIKI, PCP Express, RPX
Requires at least: 4.8
Tested up to: 6.4.3
Requires PHP: 7.4
Stable tag: 1.3.12
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

WooCommerce shipping rates calculator for Indonesia domestic and international shipment: JNE, J&T, TIKI, Pos Indonesia, SiCepat, and more.

== Description ==

WooCommerce shipping rates calculator for Indonesia domestic and international shipment: AnterAja, 21 Express, Expedito, IDexpress Service Solution, Indotama Domestik Lestari, Indah Logistic, JET Express, Jalur Nugraha Ekakurir (JNE), J&T Express, JTL Express, Lion Parcel, Ninja Xpress, Pahala Express, Pandu Logistics, PCP, POS Indonesia, Royal Express Indonesia, RPX, SAP Express, Sentral Cargo, SiCepat Express, Solusi Ekspres, Star Cargo, TIKI, Wahana Express.

= Key Features =

* Support domestic shipping couriers: AnterAja, 21 Express, IDexpress Service Solution, Indotama Domestik Lestari, Indah Logistic, JET Express, Jalur Nugraha Ekakurir (JNE), J&T Express, JTL Express, Lion Parcel, Ninja Xpress, Pahala Express, Pandu Logistics, PCP, POS Indonesia, Royal Express Indonesia, RPX, SAP Express, Sentral Cargo, SiCepat Express, Solusi Ekspres, Star Cargo, TIKI, Wahana Express.
* Support international shipping couriers: Expedito, Jalur Nugraha Ekakurir (JNE), POS Indonesia, Solusi Ekspres, TIKI.
* Support multiple couriers.
* Support shipping rates calculation from and to subdistrict location for domestic shipping.
* Support shipping rates calculation based on dimensions and weight.
* Enable or disable any of the shipping services provided by each courier.
* Set shipping couriers priority.
* Set base weight for cart content.
* Show or hide the estimated time of arrival.
* Real-time currency conversion to IDR for international shipping cost courier that using USD currency.
* Real-time API Key validation on settings update.

= Compatibility =
This plugin is not compatible with WooCommerce blocks. You MUST use [WooCommerce shortcode](https://woo.com/document/woocommerce-shortcodes) to build your cart and checkout page.


= Dependencies =

This plugin is using RajaOngkir.com API as the data source. You must have RajaOngkir.com API Key to use this plugin.

Please visit the link below to get RajaOngkir.com API Key. It is free.

[https://rajaongkir.com](https://rajaongkir.com)

= Donation =

If you enjoy using this plugin and find it useful, please consider donating. Your donation will help encourage and support the plugin’s continued development and better user support.

Please use the link below to if you would like to buy me some coffee:

[https://www.buymeacoffee.com/sofyansitorus](https://www.buymeacoffee.com/sofyansitorus?utm_source=woongkir_plugin_page&utm_medium=referral)

== Installation ==

= Minimum Requirements =

* WordPress 4.8 or later
* WooCommerce 3.0 or later

= AUTOMATIC INSTALLATION =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t even need to leave your web browser. To do an automatic install of Woongkir, log in to your WordPress admin panel, navigate to the Plugins menu, and click Add New.

In the search field type “Woongkir” and click Search Plugins. You can install it by simply clicking Install Now. After clicking that link you will be asked if you’re sure you want to install the plugin. Click yes and WordPress will automatically complete the installation. After the installation has finished, click the ‘activate plugin’ link.

= MANUAL INSTALLATION =

1. Download the plugin zip file to your computer
1. Go to the WordPress admin panel menu Plugins > Add New
1. Choose upload
1. Upload the plugin zip file, the plugin will now be installed
1. After the installation has finished, click the ‘activate plugin’ link

== Frequently Asked Questions ==

= I see the message "There are no shipping methods available" in the cart/checkout page, what should I do? =

I have no clue what is happening on your server during the WooCommerce doing the shipping calculation, and there are too many possibilities to guess that can cause the shipping method not available. To find out the causes and the solutions, please switch to “ON” for the WooCommerce Shipping Debug Mode setting. Then open your cart/checkout page. You will see a very informative and self-explanatory debug info printed on the cart/checkout page. Please note that this debug info only visible for users that already logged-in/authenticated as an administrator. You must include this debug info in case you are going to create a support ticket related to this issue.

[Click here](https://fast.wistia.net/embed/iframe/9c9008dxnr) for how to switch WooCommerce Shipping Debug Mode.

= How to switch WooCommerce Shipping Debug Mode setting? =

[Click here](https://fast.wistia.net/embed/iframe/9c9008dxnr) for how to switch WooCommerce Shipping Debug Mode setting.

= I see there is no city or subdistrict dropdown field in the checkout form or shipping calculator form, what should I do? =

The main cause of this issue is because you are using a theme or plugin that modifying the standard WooCommerce form structure. There is no way to fix this at the moment except you deactivate the theme or plugin that modifying the standard WooCommerce form structure. You may also need to check out the Browser's developer tools console to check if there is a JavaScript error/conflict. You must include this debug info in case you are going to create a support ticket related to this issue.

= How to set the plugin settings? =

You can set up the plugin setting from the WooCommerce Shipping Zones settings panel. Please [click here](https://fast.wistia.net/embed/iframe/95yiocro6p) for the video tutorial on how to set up the WooCommerce Shipping Zones.

= I got an error related with the API Key setting, what should I do? =

The error printed in there is coming from the RajaOngkir.com API server. Please check your account by login to RajaOngkir.com.

= Where can I get support or report a bug? =

You can create a support ticket at plugin support forum:

* [Plugin Support Forum](https://wordpress.org/plugins/woongkir)

= Can I contribute to developing this plugin? =

I always welcome and encourage contributions to this plugin. Please visit the plugin GitHub repository:

* [Plugin GitHub Repository](https://github.com/sofyansitorus/Woongkir)

== Screenshots ==
1. Settings panel: General Options
2. Settings panel: Domestic Shipping Options
3. Settings panel: International Shipping Options
4. Shipping Calculator Preview: Domestic Shipping
5. Shipping Calculator Preview: International Shipping

== Changelog ==

= 1.0.0 =

* Feature - Support multiple couriers for domestic shipping: JNE, TIKI, POS, PCP, RPX.
* Feature - Support multiple couriers for international shipping: JNE, POS.
* Feature - Support shipping rates calculation from and to subdistrict location for domestic shipping.
* Feature - Support shipping rates calculation based on dimensions and weight.
* Feature - Enable or disable any of the shipping services provided by each courier.
* Feature - Show or hide the estimated time of arrival.
* Feature - Real-time API Key validation on settings update.
