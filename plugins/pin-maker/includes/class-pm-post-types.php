<?php
/**
 * Class post types
 * Register post type and taxonomy.
 *
 * @package PinMaker
 * @version 1.0.0
 * @author  WPAddon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Types Class.
 *
 * @version 1.0.0
 */
class WPA_PM_Post_types {
	/**
	 * Initialize.
	 *
	 * @return  void
	 */
	public static function init() {
		// Register pins custom post type.
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 100 );

		// Register pins taxonomy
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 100 );
	}

	/**
	 * Register core post types.
	 *
	 * @return  void
	 */
	public static function register_post_types() {
		if ( post_type_exists( 'pins' ) ) {
			return;
		}
		register_post_type(
			'pins', array(
				'public'              => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => false,
				'menu_position'       => 99,
				'menu_icon'           => WPA_PM()->plugin_url() . '/assets/img/logo.svg',
				'rewrite'             => false,
				'can_export'          => true,
				'delete_with_user'    => false,
				'labels'              => array(
					'name'         => esc_html__( 'Pin Maker', 'pin-maker' ),
					'menu_name'    => esc_html__( 'Pin Maker', 'pin-maker' ),
					'all_items'    => esc_html__( 'All Items', 'pin-maker' ),
					'edit_item'    => esc_html__( 'Edit Pin', 'pin-maker' ),
					'add_new_item' => esc_html__( 'Add New Pin', 'pin-maker' ),
				),
			)
		);
	}

	/**
	 * Register taxonomies
	 *
	 * @return  void
	 */
	public static function register_taxonomies() {
		if ( taxonomy_exists( 'pins_cat' ) ) {
			return;
		}
		register_taxonomy(
			'pins_cat', array( 'pins' ),
			array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'pins_cat' ),
				'labels'            => array(
					'name'              => esc_html__( 'Collections', 'pin-maker' ),
					'singular_name'     => esc_html__( 'Collection', 'pin-maker' ),
					'search_items'      => esc_html__( 'Search Collections', 'pin-maker' ),
					'all_items'         => esc_html__( 'All Collections', 'pin-maker' ),
					'parent_item'       => esc_html__( 'Parent Collection', 'pin-maker' ),
					'parent_item_colon' => esc_html__( 'Parent Collection:', 'pin-maker' ),
					'edit_item'         => esc_html__( 'Edit Collection', 'pin-maker' ),
					'update_item'       => esc_html__( 'Update Collection', 'pin-maker' ),
					'add_new_item'      => esc_html__( 'Add New Collection', 'pin-maker' ),
					'new_item_name'     => esc_html__( 'New Collection Name', 'pin-maker' ),
					'menu_name'         => esc_html__( 'Collection', 'pin-maker' ),
				),
			)
		);
	}
}

WPA_PM_Post_types::init();