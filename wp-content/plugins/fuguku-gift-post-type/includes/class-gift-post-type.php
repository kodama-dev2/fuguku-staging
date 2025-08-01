<?php
/**
 * Gift Post Type Registration
 */

class FugukuGiftPostType_Register {
    
    public function __construct() {
        // Register post type and taxonomies on init hook with high priority
        add_action('init', array($this, 'register_post_type'), 1);
        add_action('init', array($this, 'register_taxonomies'), 1);
        
        // Add admin filters
        add_filter('manage_gift_posts_columns', array($this, 'set_custom_columns'));
        add_action('manage_gift_posts_custom_column', array($this, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-gift_sortable_columns', array($this, 'sortable_columns'));
    }
    
    /**
     * Register Gift Post Type
     */
    public function register_post_type() {
        try {
            $labels = array(
                'name'                  => _x('Gifts', 'Post type general name', 'fuguku-gift'),
                'singular_name'         => _x('Gift', 'Post type singular name', 'fuguku-gift'),
                'menu_name'             => _x('Gifts', 'Admin Menu text', 'fuguku-gift'),
                'name_admin_bar'        => _x('Gift', 'Add New on Toolbar', 'fuguku-gift'),
                'add_new'               => __('Add New', 'fuguku-gift'),
                'add_new_item'          => __('Add New Gift', 'fuguku-gift'),
                'new_item'              => __('New Gift', 'fuguku-gift'),
                'edit_item'             => __('Edit Gift', 'fuguku-gift'),
                'view_item'             => __('View Gift', 'fuguku-gift'),
                'all_items'             => __('All Gifts', 'fuguku-gift'),
                'search_items'          => __('Search Gifts', 'fuguku-gift'),
                'parent_item_colon'     => __('Parent Gifts:', 'fuguku-gift'),
                'not_found'             => __('No gifts found.', 'fuguku-gift'),
                'not_found_in_trash'    => __('No gifts found in Trash.', 'fuguku-gift'),
                'featured_image'        => _x('Gift Cover Image', 'Overrides the "Featured Image" phrase for this post type.', 'fuguku-gift'),
                'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase for this post type.', 'fuguku-gift'),
                'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase for this post type.', 'fuguku-gift'),
                'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase for this post type.', 'fuguku-gift'),
                'archives'              => _x('Gift archives', 'The post type archive label used in nav menus. Default "Post Archives".', 'fuguku-gift'),
                'insert_into_item'      => _x('Insert into gift', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post).', 'fuguku-gift'),
                'uploaded_to_this_item' => _x('Uploaded to this gift', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post).', 'fuguku-gift'),
                'filter_items_list'     => _x('Filter gifts list', 'Screen reader text for the filter links.', 'fuguku-gift'),
                'items_list_navigation' => _x('Gifts list navigation', 'Screen reader text for the pagination.', 'fuguku-gift'),
                'items_list'            => _x('Gifts list', 'Screen reader text for the items list.', 'fuguku-gift'),
            );
            
            $args = array(
                'labels'             => $labels,
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => true,
                'query_var'          => true,
                'rewrite'            => array('slug' => 'gifts'),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => 20,
                'menu_icon'          => 'dashicons-gift',
                'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
                'show_in_rest'       => true, // Enable Gutenberg editor
                'rest_base'          => 'gifts',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
            );
            
            $result = register_post_type('gift', $args);
            
            if (is_wp_error($result)) {
                error_log('Fuguku Gift Plugin Error: ' . $result->get_error_message());
            }
            
        } catch (Exception $e) {
            error_log('Fuguku Gift Plugin Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Register Taxonomies
     */
    public function register_taxonomies() {
        // Gift Category
        $category_labels = array(
            'name'              => _x('Gift Categories', 'taxonomy general name', 'fuguku-gift'),
            'singular_name'     => _x('Gift Category', 'taxonomy singular name', 'fuguku-gift'),
            'search_items'      => __('Search Gift Categories', 'fuguku-gift'),
            'all_items'         => __('All Gift Categories', 'fuguku-gift'),
            'parent_item'       => __('Parent Gift Category', 'fuguku-gift'),
            'parent_item_colon' => __('Parent Gift Category:', 'fuguku-gift'),
            'edit_item'         => __('Edit Gift Category', 'fuguku-gift'),
            'update_item'       => __('Update Gift Category', 'fuguku-gift'),
            'add_new_item'      => __('Add New Gift Category', 'fuguku-gift'),
            'new_item_name'     => __('New Gift Category Name', 'fuguku-gift'),
            'menu_name'         => __('Categories', 'fuguku-gift'),
        );
        
        register_taxonomy('gift_category', array('gift'), array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'gift-category'),
            'show_in_rest'      => true,
            'rest_base'         => 'gift-categories',
        ));
        
        // Gift Tags
        $tag_labels = array(
            'name'              => _x('Gift Tags', 'taxonomy general name', 'fuguku-gift'),
            'singular_name'     => _x('Gift Tag', 'taxonomy singular name', 'fuguku-gift'),
            'search_items'      => __('Search Gift Tags', 'fuguku-gift'),
            'all_items'         => __('All Gift Tags', 'fuguku-gift'),
            'edit_item'         => __('Edit Gift Tag', 'fuguku-gift'),
            'update_item'       => __('Update Gift Tag', 'fuguku-gift'),
            'add_new_item'      => __('Add New Gift Tag', 'fuguku-gift'),
            'new_item_name'     => __('New Gift Tag Name', 'fuguku-gift'),
            'menu_name'         => __('Tags', 'fuguku-gift'),
        );
        
        register_taxonomy('gift_tag', array('gift'), array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'gift-tag'),
            'show_in_rest'      => true,
            'rest_base'         => 'gift-tags',
        ));
    }
    
    /**
     * Set custom columns
     */
    public function set_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['featured_image'] = __('Image', 'fuguku-gift');
        $new_columns['title'] = $columns['title'];
        $new_columns['gift_category'] = __('Category', 'fuguku-gift');
        $new_columns['gift_price'] = __('Price', 'fuguku-gift');
        $new_columns['gift_brand'] = __('Brand', 'fuguku-gift');
        $new_columns['gift_availability'] = __('Availability', 'fuguku-gift');
        $new_columns['gift_featured'] = __('Featured', 'fuguku-gift');
        $new_columns['date'] = $columns['date'];
        
        return $new_columns;
    }
    
    /**
     * Custom column content
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'featured_image':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '<span class="no-image">No Image</span>';
                }
                break;
                
            case 'gift_category':
                $terms = get_the_terms($post_id, 'gift_category');
                if ($terms && !is_wp_error($terms)) {
                    $term_names = array();
                    foreach ($terms as $term) {
                        $term_names[] = $term->name;
                    }
                    echo implode(', ', $term_names);
                }
                break;
                
            case 'gift_price':
                $price = get_post_meta($post_id, '_gift_price', true);
                echo $price ? esc_html($price) : '—';
                break;
                
            case 'gift_brand':
                $brand = get_post_meta($post_id, '_gift_brand', true);
                echo $brand ? esc_html($brand) : '—';
                break;
                
            case 'gift_availability':
                $availability = get_post_meta($post_id, '_gift_availability', true);
                $availability_labels = array(
                    'in_stock' => __('In Stock', 'fuguku-gift'),
                    'out_of_stock' => __('Out of Stock', 'fuguku-gift'),
                    'pre_order' => __('Pre-order', 'fuguku-gift')
                );
                echo isset($availability_labels[$availability]) ? $availability_labels[$availability] : '—';
                break;
                
            case 'gift_featured':
                $featured = get_post_meta($post_id, '_gift_featured', true);
                echo $featured ? '<span class="featured-yes">✓</span>' : '<span class="featured-no">—</span>';
                break;
        }
    }
    
    /**
     * Sortable columns
     */
    public function sortable_columns($columns) {
        $columns['gift_price'] = 'gift_price';
        $columns['gift_brand'] = 'gift_brand';
        $columns['gift_availability'] = 'gift_availability';
        $columns['gift_featured'] = 'gift_featured';
        return $columns;
    }
}
