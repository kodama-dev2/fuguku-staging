<?php
/**
 * Gutenberg Support for Gift Post Type
 */

class FugukuGiftPostType_Gutenberg {
    
    public function __construct() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_assets'));
    }
    
    /**
     * Register custom blocks
     */
    public function register_blocks() {
        // Register gift grid block
        register_block_type('fuguku-gift/gift-grid', array(
            'editor_script' => 'fuguku-gift-gutenberg',
            'editor_style' => 'fuguku-gift-gutenberg-editor',
            'style' => 'fuguku-gift-gutenberg-style',
            'render_callback' => array($this, 'render_gift_grid_block'),
        ));
        
        // Register gift slider block
        register_block_type('fuguku-gift/gift-slider', array(
            'editor_script' => 'fuguku-gift-gutenberg',
            'editor_style' => 'fuguku-gift-gutenberg-editor',
            'style' => 'fuguku-gift-gutenberg-style',
            'render_callback' => array($this, 'render_gift_slider_block'),
        ));
    }
    
    /**
     * Enqueue block assets
     */
    public function enqueue_block_assets() {
        wp_enqueue_script(
            'fuguku-gift-gutenberg',
            FUGUKU_GIFT_PLUGIN_URL . 'assets/js/gutenberg.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            FUGUKU_GIFT_PLUGIN_VERSION
        );
        
        wp_enqueue_style(
            'fuguku-gift-gutenberg-editor',
            FUGUKU_GIFT_PLUGIN_URL . 'assets/css/gutenberg-editor.css',
            array('wp-edit-blocks'),
            FUGUKU_GIFT_PLUGIN_VERSION
        );
    }
    
    /**
     * Render gift grid block
     */
    public function render_gift_grid_block($attributes) {
        $args = array(
            'post_type' => 'gift',
            'posts_per_page' => isset($attributes['postsPerPage']) ? $attributes['postsPerPage'] : 12,
            'post_status' => 'publish',
        );
        
        if (isset($attributes['category']) && !empty($attributes['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'gift_category',
                    'field' => 'term_id',
                    'terms' => $attributes['category'],
                ),
            );
        }
        
        $gifts = new WP_Query($args);
        
        ob_start();
        if ($gifts->have_posts()) {
            echo '<div class="gift-grid-block">';
            while ($gifts->have_posts()) {
                $gifts->the_post();
                $this->render_gift_card();
            }
            echo '</div>';
        }
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Render gift slider block
     */
    public function render_gift_slider_block($attributes) {
        $args = array(
            'post_type' => 'gift',
            'posts_per_page' => isset($attributes['postsPerPage']) ? $attributes['postsPerPage'] : 6,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_gift_featured',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        $gifts = new WP_Query($args);
        
        ob_start();
        if ($gifts->have_posts()) {
            echo '<div class="gift-slider-block">';
            while ($gifts->have_posts()) {
                $gifts->the_post();
                $this->render_gift_card();
            }
            echo '</div>';
        }
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Render gift card
     */
    private function render_gift_card() {
        $price = get_post_meta(get_the_ID(), '_gift_price', true);
        $brand = get_post_meta(get_the_ID(), '_gift_brand', true);
        ?>
        <div class="gift-card">
            <div class="gift-image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                <?php endif; ?>
            </div>
            <div class="gift-content">
                <h3 class="gift-title"><?php the_title(); ?></h3>
                <?php if ($brand) : ?>
                    <p class="gift-brand"><?php echo esc_html($brand); ?></p>
                <?php endif; ?>
                <?php if ($price) : ?>
                    <p class="gift-price"><?php echo esc_html($price); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
