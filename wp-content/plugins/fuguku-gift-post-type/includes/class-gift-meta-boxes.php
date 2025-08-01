<?php
/**
 * Gift Meta Boxes
 */

class FugukuGiftPostType_MetaBoxes {
    
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }
    
    /**
     * Add Meta Boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'gift_details',
            __('Gift Details', 'fuguku-gift'),
            array($this, 'gift_details_callback'),
            'gift',
            'normal',
            'high'
        );
        
        add_meta_box(
            'gift_gallery',
            __('Gift Gallery', 'fuguku-gift'),
            array($this, 'gift_gallery_callback'),
            'gift',
            'normal',
            'high'
        );
        
        add_meta_box(
            'gift_seo',
            __('SEO Settings', 'fuguku-gift'),
            array($this, 'gift_seo_callback'),
            'gift',
            'side',
            'default'
        );
    }
    
    /**
     * Gift Details Meta Box Callback
     */
    public function gift_details_callback($post) {
        wp_nonce_field('gift_details_nonce', 'gift_details_nonce');
        
        $gift_price = get_post_meta($post->ID, '_gift_price', true);
        $gift_brand = get_post_meta($post->ID, '_gift_brand', true);
        $gift_availability = get_post_meta($post->ID, '_gift_availability', true);
        $gift_featured = get_post_meta($post->ID, '_gift_featured', true);
        $gift_sku = get_post_meta($post->ID, '_gift_sku', true);
        $gift_color = get_post_meta($post->ID, '_gift_color', true);
        $gift_material = get_post_meta($post->ID, '_gift_material', true);
        $gift_dimensions = get_post_meta($post->ID, '_gift_dimensions', true);
        $gift_weight = get_post_meta($post->ID, '_gift_weight', true);
        
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="gift_price"><?php _e('Price', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_price" name="gift_price" value="<?php echo esc_attr($gift_price); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the gift price (e.g., $50.00)', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_brand"><?php _e('Brand', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_brand" name="gift_brand" value="<?php echo esc_attr($gift_brand); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the gift brand', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_sku"><?php _e('SKU', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_sku" name="gift_sku" value="<?php echo esc_attr($gift_sku); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the product SKU', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_availability"><?php _e('Availability', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <select id="gift_availability" name="gift_availability">
                        <option value="in_stock" <?php selected($gift_availability, 'in_stock'); ?>><?php _e('In Stock', 'fuguku-gift'); ?></option>
                        <option value="out_of_stock" <?php selected($gift_availability, 'out_of_stock'); ?>><?php _e('Out of Stock', 'fuguku-gift'); ?></option>
                        <option value="pre_order" <?php selected($gift_availability, 'pre_order'); ?>><?php _e('Pre-order', 'fuguku-gift'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_color"><?php _e('Color', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_color" name="gift_color" value="<?php echo esc_attr($gift_color); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the gift color', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_material"><?php _e('Material', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_material" name="gift_material" value="<?php echo esc_attr($gift_material); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter the gift material', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_dimensions"><?php _e('Dimensions', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_dimensions" name="gift_dimensions" value="<?php echo esc_attr($gift_dimensions); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter dimensions (e.g., 10" x 5" x 2")', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_weight"><?php _e('Weight', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="text" id="gift_weight" name="gift_weight" value="<?php echo esc_attr($gift_weight); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter weight (e.g., 500g)', 'fuguku-gift'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="gift_featured"><?php _e('Featured Gift', 'fuguku-gift'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="gift_featured" name="gift_featured" value="1" <?php checked($gift_featured, '1'); ?> />
                    <label for="gift_featured"><?php _e('Mark as featured gift', 'fuguku-gift'); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Gift Gallery Meta Box Callback
     */
    public function gift_gallery_callback($post) {
        wp_nonce_field('gift_gallery_nonce', 'gift_gallery_nonce');
        
        $gallery_images = get_post_meta($post->ID, '_gift_gallery', true);
        if (!is_array($gallery_images)) {
            $gallery_images = array();
        }
        
        ?>
        <div class="gift-gallery-container">
            <input type="hidden" id="gift_gallery" name="gift_gallery" value="<?php echo esc_attr(implode(',', $gallery_images)); ?>" />
            <div id="gift-gallery-preview" class="gift-gallery-preview">
                <?php
                if (!empty($gallery_images)) {
                    foreach ($gallery_images as $image_id) {
                        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        if ($image_url) {
                            echo '<div class="gallery-image" data-id="' . esc_attr($image_id) . '">';
                            echo '<img src="' . esc_url($image_url) . '" alt="" />';
                            echo '<button type="button" class="remove-image">×</button>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <button type="button" id="add-gallery-images" class="button"><?php _e('Add Gallery Images', 'fuguku-gift'); ?></button>
        </div>
        <?php
    }
    
    /**
     * Gift SEO Meta Box Callback
     */
    public function gift_seo_callback($post) {
        wp_nonce_field('gift_seo_nonce', 'gift_seo_nonce');
        
        $seo_title = get_post_meta($post->ID, '_gift_seo_title', true);
        $seo_description = get_post_meta($post->ID, '_gift_seo_description', true);
        $seo_keywords = get_post_meta($post->ID, '_gift_seo_keywords', true);
        
        ?>
        <p>
            <label for="gift_seo_title"><?php _e('SEO Title', 'fuguku-gift'); ?></label>
            <input type="text" id="gift_seo_title" name="gift_seo_title" value="<?php echo esc_attr($seo_title); ?>" class="widefat" />
        </p>
        <p>
            <label for="gift_seo_description"><?php _e('SEO Description', 'fuguku-gift'); ?></label>
            <textarea id="gift_seo_description" name="gift_seo_description" class="widefat" rows="3"><?php echo esc_textarea($seo_description); ?></textarea>
        </p>
        <p>
            <label for="gift_seo_keywords"><?php _e('SEO Keywords', 'fuguku-gift'); ?></label>
            <input type="text" id="gift_seo_keywords" name="gift_seo_keywords" value="<?php echo esc_attr($seo_keywords); ?>" class="widefat" />
            <small><?php _e('Separate keywords with commas', 'fuguku-gift'); ?></small>
        </p>
        <?php
    }
    
    /**
     * Save Meta Boxes
     */
    public function save_meta_boxes($post_id) {
        // Check if nonce is valid
        if (!isset($_POST['gift_details_nonce']) || !wp_verify_nonce($_POST['gift_details_nonce'], 'gift_details_nonce')) {
            return;
        }
        
        // Check if user has permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if not an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Save gift details
        $fields = array(
            'gift_price' => 'text',
            'gift_brand' => 'text',
            'gift_sku' => 'text',
            'gift_availability' => 'text',
            'gift_color' => 'text',
            'gift_material' => 'text',
            'gift_dimensions' => 'text',
            'gift_weight' => 'text',
        );
        
        foreach ($fields as $field => $type) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Save featured checkbox
        $gift_featured = isset($_POST['gift_featured']) ? '1' : '0';
        update_post_meta($post_id, '_gift_featured', $gift_featured);
        
        // Save gallery
        if (isset($_POST['gift_gallery'])) {
            $gallery_images = array_filter(explode(',', sanitize_text_field($_POST['gift_gallery'])));
            update_post_meta($post_id, '_gift_gallery', $gallery_images);
        }
        
        // Save SEO fields
        $seo_fields = array('gift_seo_title', 'gift_seo_description', 'gift_seo_keywords');
        foreach ($seo_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Admin scripts
     */
    public function admin_scripts($hook) {
        global $post_type;
        
        if ($post_type === 'gift') {
            wp_enqueue_media();
            wp_enqueue_script('fuguku-gift-admin', FUGUKU_GIFT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), FUGUKU_GIFT_PLUGIN_VERSION, true);
        }
    }
}
