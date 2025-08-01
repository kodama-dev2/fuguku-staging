<?php
/**
 * Initialize viusal composer.
 *
 * @since   1.0.0
 * @package Claue
 */

if ( ! class_exists( 'VC_Manager' ) ) return;

// Remove "Design options", "Custom CSS" tabs and prompt message.
vc_set_as_theme();

// Change default template directory.
vc_set_shortcodes_templates_dir( JAS_CLAUE_PATH . '/core/libraries/vendors/visual-composer/shortcodes' );

// Include custom functions of vc element
$maps = 'row, column, heading, service, portfolio, member, blog, product, products, google-maps, wc-categories, banner, meta-slider, promotion';
$maps = array_map( 'trim', explode( ',', $maps ) );
foreach ( $maps as $file ) {
	include JAS_CLAUE_PATH . '/core/libraries/vendors/visual-composer/maps/' . $file . '.php';
}

vc_map_update( 'vc_column_text', array( 'icon' => 'pe-7s-credit' ) );
vc_map_update( 'vc_icon', array( 'icon' => 'pe-7s-smile' ) );
vc_map_update( 'vc_separator', array( 'icon' => 'pe-7s-more' ) );
vc_map_update( 'vc_text_separator', array( 'icon' => 'pe-7s-more' ) );
vc_map_update( 'vc_message', array( 'icon' => 'pe-7s-comment' ) );
vc_map_update( 'vc_facebook', array( 'icon' => 'pe-7s-share' ) );
vc_map_update( 'vc_tweetmeme', array( 'icon' => 'pe-7s-share' ) );
vc_map_update( 'vc_googleplus', array( 'icon' => 'pe-7s-share' ) );
vc_map_update( 'vc_pinterest', array( 'icon' => 'pe-7s-share' ) );
vc_map_update( 'vc_toggle', array( 'icon' => 'pe-7s-albums' ) );
vc_map_update( 'vc_single_image', array( 'icon' => 'pe-7s-photo' ) );
vc_map_update( 'vc_gallery', array( 'icon' => 'pe-7s-safe' ) );
vc_map_update( 'vc_images_carousel', array( 'icon' => 'pe-7s-gym' ) );
vc_map_update( 'vc_tta_tabs', array( 'icon' => 'pe-7s-browser' ) );
vc_map_update( 'vc_tta_tour', array( 'icon' => 'pe-7s-box1' ) );
vc_map_update( 'vc_tta_accordion', array( 'icon' => 'pe-7s-map' ) );
vc_map_update( 'vc_tta_pageable', array( 'icon' => 'pe-7s-help2' ) );
vc_map_update( 'vc_custom_heading', array( 'icon' => 'pe-7s-pen' ) );
vc_map_update( 'vc_btn', array( 'icon' => 'pe-7s-switch' ) );
vc_map_update( 'vc_cta', array( 'icon' => 'pe-7s-print' ) );
vc_map_update( 'vc_widget_sidebar', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_posts_slider', array( 'icon' => 'pe-7s-gym' ) );
vc_map_update( 'vc_video', array( 'icon' => 'pe-7s-film' ) );
vc_map_update( 'vc_raw_html', array( 'icon' => 'pe-7s-keypad' ) );
vc_map_update( 'vc_raw_js', array( 'icon' => 'pe-7s-keypad' ) );
vc_map_update( 'vc_flickr', array( 'icon' => 'pe-7s-display1' ) );
vc_map_update( 'vc_progress_bar', array( 'icon' => 'pe-7s-edit' ) );
vc_map_update( 'vc_pie', array( 'icon' => 'pe-7s-refresh' ) );
vc_map_update( 'vc_round_chart', array( 'icon' => 'pe-7s-graph2' ) );
vc_map_update( 'vc_line_chart', array( 'icon' => 'pe-7s-graph3' ) );
vc_map_update( 'vc_empty_space', array( 'icon' => 'pe-7s-scissors' ) );
vc_map_update( 'vc_basic_grid', array( 'icon' => 'pe-7s-safe' ) );
vc_map_update( 'vc_media_grid', array( 'icon' => 'pe-7s-science' ) );
vc_map_update( 'vc_masonry_grid', array( 'icon' => 'pe-7s-exapnd2' ) );
vc_map_update( 'vc_masonry_media_grid', array( 'icon' => 'pe-7s-box2' ) );

vc_map_update( 'woocommerce_cart', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'woocommerce_checkout', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'woocommerce_order_tracking', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'woocommerce_my_account', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'add_to_cart', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'add_to_cart_url', array( 'icon' => 'pe-7s-shopbag' ) );
vc_map_update( 'product_page', array( 'icon' => 'pe-7s-shopbag' ) );

vc_map_update( 'vc_wp_search', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_meta', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_recentcomments', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_calendar', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_pages', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_tagcloud', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_custommenu', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_text', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_posts', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_categories', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_archives', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'vc_wp_rss', array( 'icon' => 'pe-7s-config' ) );
vc_map_update( 'contact-form-7', array( 'icon' => 'pe-7s-mail-open-file' ) );

/**
 * Suggester for autocomplete by id/name/title/sku
 *
 * @param $query
 *
 * @return array - id's from products with title/sku.
 */
function jas_claue_addons_autocomplete( $query ) {
	$filter = current_filter();

	switch ( $filter ) {
		case 'vc_autocomplete_claue_addons_product_id_callback' :
		case 'vc_autocomplete_claue_addons_products_id_callback' :
			$suggestions = apply_filters( 'vc_autocomplete_product_id_callback', $query );

		break;

		case 'vc_autocomplete_claue_addons_blog_id_callback':
			$query = array(
				'query' => 'post',
				'term'  => $query
			);
			$suggestions = apply_filters( 'vc_autocomplete_vc_basic_grid_exclude_callback', $query );

		break;

	}

	if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
		die( json_encode( $suggestions ) );
	}

	die( '' ); // if nothing found..

}
add_filter( 'vc_autocomplete_claue_addons_product_id_callback', 'jas_claue_addons_autocomplete', 1, 1 );
add_filter( 'vc_autocomplete_claue_addons_products_id_callback', 'jas_claue_addons_autocomplete', 1, 1 );
add_filter( 'vc_autocomplete_claue_addons_blog_id_callback', 'jas_claue_addons_autocomplete', 1, 1 );

/**
 * Add icon stroke for vc
 *
 * @return array
 */
function jas_claue_vc_icon_stroke( $icons ) {
	$stroke_icons = array(
		array( 'pe-7s-album' => 'Album' ),
		array( 'pe-7s-arc' => 'Arc' ),
		array( 'pe-7s-back-2' => 'Back-2' ),
		array( 'pe-7s-bandaid' => 'Bandaid' ),
		array( 'pe-7s-car' => 'Car' ),
		array( 'pe-7s-diamond' => 'Diamond' ),
		array( 'pe-7s-door-lock' => 'Door-lock' ),
		array( 'pe-7s-eyedropper' => 'Eyedropper' ),
		array( 'pe-7s-female' => 'Female' ),
		array( 'pe-7s-gym' => 'Gym' ),
		array( 'pe-7s-hammer' => 'Hammer' ),
		array( 'pe-7s-headphones' => 'Headphones' ),
		array( 'pe-7s-helm' => 'Helm' ),
		array( 'pe-7s-hourglass' => 'Hourglass' ),
		array( 'pe-7s-leaf' => 'Leaf' ),
		array( 'pe-7s-magic-wand' => 'Magic-wand' ),
		array( 'pe-7s-male' => 'Male' ),
		array( 'pe-7s-map-2' => 'Map-2' ),
		array( 'pe-7s-next-2' => 'Next-2' ),
		array( 'pe-7s-paint-bucket' => 'Paint-bucket' ),
		array( 'pe-7s-pendrive' => 'Pendrive' ),
		array( 'pe-7s-photo' => 'Photo' ),
		array( 'pe-7s-piggy' => 'Piggy' ),
		array( 'pe-7s-plugin' => 'Plugin' ),
		array( 'pe-7s-refresh-2' => 'Refresh-2' ),
		array( 'pe-7s-rocket' => 'Rocket' ),
		array( 'pe-7s-settings' => 'Settings' ),
		array( 'pe-7s-shield' => 'Shield' ),
		array( 'pe-7s-smile' => 'Smile' ),
		array( 'pe-7s-usb' => 'Usb' ),
		array( 'pe-7s-vector' => 'Vector' ),
		array( 'pe-7s-wine' => 'Wine' ),
		array( 'pe-7s-cloud-upload' => 'Cloud-upload' ),
		array( 'pe-7s-cash' => 'Cash' ),
		array( 'pe-7s-close' => 'Close' ),
		array( 'pe-7s-bluetooth' => 'Bluetooth' ),
		array( 'pe-7s-cloud-download' => 'Cloud-download' ),
		array( 'pe-7s-way' => 'Way' ),
		array( 'pe-7s-close-circle' => 'Close-circle' ),
		array( 'pe-7s-id' => 'Id' ),
		array( 'pe-7s-angle-up' => 'Angle-up' ),
		array( 'pe-7s-wristwatch' => 'Wristwatch' ),
		array( 'pe-7s-angle-up-circle' => 'Angle-up-circle' ),
		array( 'pe-7s-world' => 'World' ),
		array( 'pe-7s-angle-right' => 'Angle-right' ),
		array( 'pe-7s-volume' => 'Volume' ),
		array( 'pe-7s-angle-right-circle' => 'Angle-right-circle' ),
		array( 'pe-7s-users' => 'Users' ),
		array( 'pe-7s-angle-left' => 'Angle-left' ),
		array( 'pe-7s-user-female' => 'User-female' ),
		array( 'pe-7s-angle-left-circle' => 'Angle-left-circle' ),
		array( 'pe-7s-up-arrow' => 'Up-arrow' ),
		array( 'pe-7s-angle-down' => 'Angle-down' ),
		array( 'pe-7s-switch' => 'Switch' ),
		array( 'pe-7s-angle-down-circle' => 'Angle-down-circle' ),
		array( 'pe-7s-scissors' => 'Scissors' ),
		array( 'pe-7s-wallet' => 'Wallet' ),
		array( 'pe-7s-safe' => 'Safe' ),
		array( 'pe-7s-volume2' => 'Volume2' ),
		array( 'pe-7s-volume1' => 'Volume1' ),
		array( 'pe-7s-voicemail' => 'Voicemail' ),
		array( 'pe-7s-video' => 'Video' ),
		array( 'pe-7s-user' => 'User' ),
		array( 'pe-7s-upload' => 'Upload' ),
		array( 'pe-7s-unlock' => 'Unlock' ),
		array( 'pe-7s-umbrella' => 'Umbrella' ),
		array( 'pe-7s-trash' => 'Trash' ),
		array( 'pe-7s-tools' => 'Tools' ),
		array( 'pe-7s-timer' => 'Timer' ),
		array( 'pe-7s-ticket' => 'Ticket' ),
		array( 'pe-7s-target' => 'Target' ),
		array( 'pe-7s-sun' => 'Sun' ),
		array( 'pe-7s-study' => 'Study' ),
		array( 'pe-7s-stopwatch' => 'Stopwatch' ),
		array( 'pe-7s-star' => 'Star' ),
		array( 'pe-7s-speaker' => 'Speaker' ),
		array( 'pe-7s-signal' => 'Signal' ),
		array( 'pe-7s-shuffle' => 'Shuffle' ),
		array( 'pe-7s-shopbag' => 'Shopbag' ),
		array( 'pe-7s-share' => 'Share' ),
		array( 'pe-7s-server' => 'Server' ),
		array( 'pe-7s-search' => 'Search' ),
		array( 'pe-7s-film' => 'Film' ),
		array( 'pe-7s-science' => 'Science' ),
		array( 'pe-7s-disk' => 'Disk' ),
		array( 'pe-7s-ribbon' => 'Ribbon' ),
		array( 'pe-7s-repeat' => 'Repeat' ),
		array( 'pe-7s-refresh' => 'Refresh' ),
		array( 'pe-7s-add-user' => 'Add-user' ),
		array( 'pe-7s-refresh-cloud' => 'Refresh-cloud' ),
		array( 'pe-7s-paperclip' => 'Paperclip' ),
		array( 'pe-7s-radio' => 'Radio' ),
		array( 'pe-7s-note2' => 'Note2' ),
		array( 'pe-7s-print' => 'Print' ),
		array( 'pe-7s-network' => 'Network' ),
		array( 'pe-7s-prev' => 'Prev' ),
		array( 'pe-7s-mute' => 'Mute' ),
		array( 'pe-7s-power' => 'Power' ),
		array( 'pe-7s-medal' => 'Medal' ),
		array( 'pe-7s-portfolio' => 'Portfolio' ),
		array( 'pe-7s-like2' => 'Like2' ),
		array( 'pe-7s-plus' => 'Plus' ),
		array( 'pe-7s-left-arrow' => 'Left-arrow' ),
		array( 'pe-7s-play' => 'Play' ),
		array( 'pe-7s-key' => 'Key' ),
		array( 'pe-7s-plane' => 'Plane' ),
		array( 'pe-7s-joy' => 'Joy' ),
		array( 'pe-7s-photo-gallery' => 'Photo-gallery' ),
		array( 'pe-7s-pin' => 'Pin' ),
		array( 'pe-7s-phone' => 'Phone' ),
		array( 'pe-7s-plug' => 'Plug' ),
		array( 'pe-7s-pen' => 'Pen' ),
		array( 'pe-7s-right-arrow' => 'Right-arrow' ),
		array( 'pe-7s-paper-plane' => 'Paper-plane' ),
		array( 'pe-7s-delete-user' => 'Delete-user' ),
		array( 'pe-7s-paint' => 'Paint' ),
		array( 'pe-7s-bottom-arrow' => 'Bottom-arrow' ),
		array( 'pe-7s-notebook' => 'Notebook' ),
		array( 'pe-7s-note' => 'Note' ),
		array( 'pe-7s-next' => 'Next' ),
		array( 'pe-7s-news-paper' => 'News-paper' ),
		array( 'pe-7s-musiclist' => 'Musiclist' ),
		array( 'pe-7s-music' => 'Music' ),
		array( 'pe-7s-mouse' => 'Mouse' ),
		array( 'pe-7s-more' => 'More' ),
		array( 'pe-7s-moon' => 'Moon' ),
		array( 'pe-7s-monitor' => 'Monitor' ),
		array( 'pe-7s-micro' => 'Micro' ),
		array( 'pe-7s-menu' => 'Menu' ),
		array( 'pe-7s-map' => 'Map' ),
		array( 'pe-7s-map-marker' => 'Map-marker' ),
		array( 'pe-7s-mail' => 'Mail' ),
		array( 'pe-7s-mail-open' => 'Mail-open' ),
		array( 'pe-7s-mail-open-file' => 'Mail-open-file' ),
		array( 'pe-7s-magnet' => 'Magnet' ),
		array( 'pe-7s-loop' => 'Loop' ),
		array( 'pe-7s-look' => 'Look' ),
		array( 'pe-7s-lock' => 'Lock' ),
		array( 'pe-7s-lintern' => 'Lintern' ),
		array( 'pe-7s-link' => 'Link' ),
		array( 'pe-7s-like' => 'Like' ),
		array( 'pe-7s-light' => 'Light' ),
		array( 'pe-7s-less' => 'Less' ),
		array( 'pe-7s-keypad' => 'Keypad' ),
		array( 'pe-7s-junk' => 'Junk' ),
		array( 'pe-7s-info' => 'Info' ),
		array( 'pe-7s-home' => 'Home' ),
		array( 'pe-7s-help2' => 'Help2' ),
		array( 'pe-7s-help1' => 'Help1' ),
		array( 'pe-7s-graph3' => 'Graph3' ),
		array( 'pe-7s-graph2' => 'Graph2' ),
		array( 'pe-7s-graph1' => 'Graph1' ),
		array( 'pe-7s-graph' => 'Graph' ),
		array( 'pe-7s-global' => 'Global' ),
		array( 'pe-7s-gleam' => 'Gleam' ),
		array( 'pe-7s-glasses' => 'Glasses' ),
		array( 'pe-7s-gift' => 'Gift' ),
		array( 'pe-7s-folder' => 'Folder' ),
		array( 'pe-7s-flag' => 'Flag' ),
		array( 'pe-7s-filter' => 'Filter' ),
		array( 'pe-7s-file' => 'File' ),
		array( 'pe-7s-expand1' => 'Expand1' ),
		array( 'pe-7s-exapnd2' => 'Exapnd2' ),
		array( 'pe-7s-edit' => 'Edit' ),
		array( 'pe-7s-drop' => 'Drop' ),
		array( 'pe-7s-drawer' => 'Drawer' ),
		array( 'pe-7s-download' => 'Download' ),
		array( 'pe-7s-display2' => 'Display2' ),
		array( 'pe-7s-display1' => 'Display1' ),
		array( 'pe-7s-diskette' => 'Diskette' ),
		array( 'pe-7s-date' => 'Date' ),
		array( 'pe-7s-cup' => 'Cup' ),
		array( 'pe-7s-culture' => 'Culture' ),
		array( 'pe-7s-crop' => 'Crop' ),
		array( 'pe-7s-credit' => 'Credit' ),
		array( 'pe-7s-copy-file' => 'Copy-file' ),
		array( 'pe-7s-config' => 'Config' ),
		array( 'pe-7s-compass' => 'Compass' ),
		array( 'pe-7s-comment' => 'Comment' ),
		array( 'pe-7s-coffee' => 'Coffee' ),
		array( 'pe-7s-cloud' => 'Cloud' ),
		array( 'pe-7s-clock' => 'Clock' ),
		array( 'pe-7s-check' => 'Check' ),
		array( 'pe-7s-chat' => 'Chat' ),
		array( 'pe-7s-cart' => 'Cart' ),
		array( 'pe-7s-camera' => 'Camera' ),
		array( 'pe-7s-call' => 'Call' ),
		array( 'pe-7s-calculator' => 'Calculator' ),
		array( 'pe-7s-browser' => 'Browser' ),
		array( 'pe-7s-box2' => 'Box2' ),
		array( 'pe-7s-box1' => 'Box1' ),
		array( 'pe-7s-bookmarks' => 'Bookmarks' ),
		array( 'pe-7s-bicycle' => 'Bicycle' ),
		array( 'pe-7s-bell' => 'Bell' ),
		array( 'pe-7s-battery' => 'Battery' ),
		array( 'pe-7s-ball' => 'Ball' ),
		array( 'pe-7s-back' => 'Back' ),
		array( 'pe-7s-attention' => 'Attention' ),
		array( 'pe-7s-anchor' => 'Anchor' ),
		array( 'pe-7s-albums' => 'Albums' ),
		array( 'pe-7s-alarm' => 'Alarm' ),
		array( 'pe-7s-airplay' => 'Airplay' ),
	);

	return array_merge( $icons, $stroke_icons );
}
add_filter( 'vc_iconpicker-type-stroke', 'jas_claue_vc_icon_stroke' );

/**
 * Enqueue stylesheets and scripts in admin.
 *
 * @return  void
 */
function jas_claue_vc_enqueue_scripts() {
	if ( ! function_exists( 'vc_editor_post_types' ) ) {
		return;
	}

	// Get post type enabled for editing with Visual Composer.
	$types = vc_editor_post_types();

	// Check if current post type is enabled
	global $post;

	if ( isset( $post->post_type ) && in_array( $post->post_type, $types ) ) {
		wp_enqueue_style( 'font-stroke', JAS_CLAUE_URL . '/assets/vendors/font-stroke/css/font-stroke.min.css' );
	}

}
add_action( 'admin_enqueue_scripts', 'jas_claue_vc_enqueue_scripts' );