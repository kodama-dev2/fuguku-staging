<?php
/**
 * AJAX Handler for Gifts
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Handler Class
 */
class Fuguku_Gifts_Ajax_Handler {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_fuguku_filter_gifts', array($this, 'filter_gifts'));
        add_action('wp_ajax_nopriv_fuguku_filter_gifts', array($this, 'filter_gifts'));
        
        add_action('wp_ajax_fuguku_search_gifts', array($this, 'search_gifts'));
        add_action('wp_ajax_nopriv_fuguku_search_gifts', array($this, 'search_gifts'));
        
        add_action('wp_ajax_fuguku_load_more_gifts', array($this, 'load_more_gifts'));
        add_action('wp_ajax_nopriv_fuguku_load_more_gifts', array($this, 'load_more_gifts'));
        
        add_action('wp_ajax_fuguku_add_to_wishlist', array($this, 'add_to_wishlist'));
        add_action('wp_ajax_nopriv_fuguku_add_to_wishlist', array($this, 'add_to_wishlist'));
        
        add_action('wp_ajax_fuguku_remove_from_wishlist', array($this, 'remove_from_wishlist'));
        add_action('wp_ajax_nopriv_fuguku_remove_from_wishlist', array($this, 'remove_from_wishlist'));
    }

    /**
     * Filter Gifts AJAX Handler
     */
    public function filter_gifts() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        // Get filter parameters
        $category = sanitize_text_field($_POST['category'] ?? '');
        $price_range = sanitize_text_field($_POST['price_range'] ?? '');
        $availability = sanitize_text_field($_POST['availability'] ?? '');
        $featured = sanitize_text_field($_POST['featured'] ?? '');
        $search = sanitize_text_field($_POST['search'] ?? '');
        $page = intval($_POST['page'] ?? 1);
        $per_page = intval($_POST['per_page'] ?? 12);

        // Build query args
        $args = array(
            'post_type' => 'gifts',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        // Add category filter
        if (!empty($category)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'gift_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }

        // Add price range filter
        if (!empty($price_range)) {
            $price_parts = explode('-', $price_range);
            $min_price = intval($price_parts[0]);
            $max_price = intval($price_parts[1]);
            
            $args['meta_query'][] = array(
                'key' => '_gift_price',
                'value' => array($min_price, $max_price),
                'type' => 'NUMERIC',
                'compare' => 'BETWEEN',
            );
        }

        // Add availability filter
        if (!empty($availability)) {
            $args['meta_query'][] = array(
                'key' => '_gift_availability',
                'value' => $availability,
                'compare' => '=',
            );
        }

        // Add featured filter
        if ($featured === 'true') {
            $args['meta_query'][] = array(
                'key' => '_featured_gift',
                'value' => '1',
                'compare' => '=',
            );
        }

        // Add search filter
        if (!empty($search)) {
            $args['s'] = $search;
        }

        // Combine meta queries
        if (isset($args['meta_query']) && count($args['meta_query']) > 1) {
            $args['meta_query']['relation'] = 'AND';
        }

        // Execute query
        $gifts_query = new WP_Query($args);
        
        $response = array(
            'success' => true,
            'data' => array(),
            'pagination' => array(),
        );

        if ($gifts_query->have_posts()) {
            ob_start();
            
            while ($gifts_query->have_posts()) {
                $gifts_query->the_post();
                $this->render_gift_card();
            }
            
            $response['data']['html'] = ob_get_clean();
            $response['data']['found'] = $gifts_query->found_posts;
            
            // Pagination info
            $response['pagination'] = array(
                'current_page' => $page,
                'max_pages' => $gifts_query->max_num_pages,
                'has_more' => $page < $gifts_query->max_num_pages,
            );
            
            wp_reset_postdata();
        } else {
            $response['data']['html'] = $this->get_no_gifts_found_html();
            $response['data']['found'] = 0;
        }

        wp_send_json($response);
    }

    /**
     * Search Gifts AJAX Handler
     */
    public function search_gifts() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        $search_term = sanitize_text_field($_POST['search_term'] ?? '');
        
        if (empty($search_term)) {
            wp_send_json(array('success' => false, 'message' => 'Search term is required'));
        }

        $args = array(
            'post_type' => 'gifts',
            'posts_per_page' => 10,
            's' => $search_term,
            'post_status' => 'publish',
        );

        $gifts_query = new WP_Query($args);
        
        $results = array();
        
        if ($gifts_query->have_posts()) {
            while ($gifts_query->have_posts()) {
                $gifts_query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'price' => get_post_meta(get_the_ID(), '_gift_price', true),
                    'brand' => get_post_meta(get_the_ID(), '_gift_brand', true),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                );
            }
            wp_reset_postdata();
        }

        wp_send_json(array(
            'success' => true,
            'results' => $results,
        ));
    }

    /**
     * Load More Gifts AJAX Handler
     */
    public function load_more_gifts() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        $page = intval($_POST['page'] ?? 1);
        $per_page = intval($_POST['per_page'] ?? 12);

        $args = array(
            'post_type' => 'gifts',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $gifts_query = new WP_Query($args);
        
        $response = array(
            'success' => true,
            'data' => array(),
            'pagination' => array(),
        );

        if ($gifts_query->have_posts()) {
            ob_start();
            
            while ($gifts_query->have_posts()) {
                $gifts_query->the_post();
                $this->render_gift_card();
            }
            
            $response['data']['html'] = ob_get_clean();
            $response['pagination'] = array(
                'current_page' => $page,
                'max_pages' => $gifts_query->max_num_pages,
                'has_more' => $page < $gifts_query->max_num_pages,
            );
            
            wp_reset_postdata();
        }

        wp_send_json($response);
    }

    /**
     * Add to Wishlist AJAX Handler
     */
    public function add_to_wishlist() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        $gift_id = intval($_POST['gift_id'] ?? 0);
        
        if (!$gift_id) {
            wp_send_json(array('success' => false, 'message' => 'Invalid gift ID'));
        }

        // Get current user's wishlist
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, 'fuguku_wishlist', true);
        
        if (!is_array($wishlist)) {
            $wishlist = array();
        }

        // Add to wishlist if not already there
        if (!in_array($gift_id, $wishlist)) {
            $wishlist[] = $gift_id;
            update_user_meta($user_id, 'fuguku_wishlist', $wishlist);
            
            wp_send_json(array(
                'success' => true,
                'message' => 'Added to wishlist',
                'in_wishlist' => true,
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'message' => 'Already in wishlist',
                'in_wishlist' => true,
            ));
        }
    }

    /**
     * Remove from Wishlist AJAX Handler
     */
    public function remove_from_wishlist() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'fuguku_gifts_nonce')) {
            wp_die('Security check failed');
        }

        $gift_id = intval($_POST['gift_id'] ?? 0);
        
        if (!$gift_id) {
            wp_send_json(array('success' => false, 'message' => 'Invalid gift ID'));
        }

        // Get current user's wishlist
        $user_id = get_current_user_id();
        $wishlist = get_user_meta($user_id, 'fuguku_wishlist', true);
        
        if (!is_array($wishlist)) {
            $wishlist = array();
        }

        // Remove from wishlist
        $wishlist = array_diff($wishlist, array($gift_id));
        update_user_meta($user_id, 'fuguku_wishlist', $wishlist);
        
        wp_send_json(array(
            'success' => true,
            'message' => 'Removed from wishlist',
            'in_wishlist' => false,
        ));
    }

    /**
     * Render Gift Card HTML
     */
    private function render_gift_card() {
        ?>
        <article class="gift-card <?php echo (get_post_meta(get_the_ID(), '_featured_gift', true) ? 'featured' : 'regular'); ?>">
            
            <!-- Gift Image -->
            <div class="gift-image">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium_large', array('class' => 'gift-thumbnail')); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php the_permalink(); ?>">
                        <div class="gift-placeholder">
                            <i class="fa fa-gift"></i>
                        </div>
                    </a>
                <?php endif; ?>
                
                <!-- Featured Badge -->
                <?php if (get_post_meta(get_the_ID(), '_featured_gift', true)) : ?>
                    <div class="featured-badge">
                        <i class="fa fa-star"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gift Info -->
            <div class="gift-info">
                <h3 class="gift-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                
                <!-- Gift Price -->
                <?php 
                $gift_price = get_post_meta(get_the_ID(), '_gift_price', true);
                if ($gift_price) : ?>
                    <div class="gift-price">
                        <span class="currency">IDR</span>
                        <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                    </div>
                <?php endif; ?>
                
                <!-- Gift Brand -->
                <?php 
                $gift_brand = get_post_meta(get_the_ID(), '_gift_brand', true);
                if ($gift_brand) : ?>
                    <div class="gift-brand">
                        <?php echo esc_html($gift_brand); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Gift Availability -->
                <?php 
                $gift_availability = get_post_meta(get_the_ID(), '_gift_availability', true);
                if ($gift_availability) : ?>
                    <div class="gift-availability <?php echo esc_attr($gift_availability); ?>">
                        <?php 
                        switch ($gift_availability) {
                            case 'in_stock':
                                echo '<i class="fa fa-check-circle"></i> ' . esc_html__('In Stock', 'fuguku-gift');
                                break;
                            case 'limited':
                                echo '<i class="fa fa-exclamation-triangle"></i> ' . esc_html__('Limited Stock', 'fuguku-gift');
                                break;
                            case 'out_of_stock':
                                echo '<i class="fa fa-times-circle"></i> ' . esc_html__('Out of Stock', 'fuguku-gift');
                                break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Wishlist Button -->
                <div class="gift-actions">
                    <button class="gift-wishlist-btn" data-gift-id="<?php echo get_the_ID(); ?>">
                        <i class="fa fa-heart"></i>
                        <span class="wishlist-text"><?php echo esc_html__('Add to Wishlist', 'fuguku-gift'); ?></span>
                    </button>
                </div>
            </div>

        </article>
        <?php
    }

    /**
     * Get No Gifts Found HTML
     */
    private function get_no_gifts_found_html() {
        ob_start();
        ?>
        <div class="no-gifts-found">
            <div class="no-gifts-icon">
                <i class="fa fa-gift"></i>
            </div>
            <h3><?php echo esc_html__('No Gifts Found', 'fuguku-gift'); ?></h3>
            <p><?php echo esc_html__('We couldn\'t find any gifts matching your criteria.', 'fuguku-gift'); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Initialize AJAX handler
new Fuguku_Gifts_Ajax_Handler(); 