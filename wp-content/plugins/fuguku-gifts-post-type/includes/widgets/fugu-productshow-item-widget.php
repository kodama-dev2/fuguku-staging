<?php
/**
 * FUGU ProductShow Item Widget
 * 
 * Displays WooCommerce products with query filter + manual selection.
 * User filters by category/tag first, then manually picks products from filtered results.
 * 
 * @package Fuguku_Gifts
 * @version 3.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fugu_ProductShow_Item_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'fugu-productshow-item';
    }

    public function get_title() {
        return __('FUGU ProductShow Item', 'fuguku-gift');
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_categories() {
        return ['fuguku-gifts'];
    }

    public function get_keywords() {
        return ['fugu', 'product', 'woocommerce', 'show', 'item', 'gallery', 'category'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Get product categories
        $categories = ['' => __('All Categories', 'fuguku-gift')];
        if (function_exists('get_terms')) {
            $product_categories = get_terms([
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ]);
            if (!is_wp_error($product_categories) && !empty($product_categories)) {
                foreach ($product_categories as $cat) {
                    $categories[$cat->term_id] = $cat->name . ' (' . $cat->count . ')';
                }
            }
        }

        // Get product tags
        $tags = ['' => __('All Tags', 'fuguku-gift')];
        if (function_exists('get_terms')) {
            $product_tags = get_terms([
                'taxonomy' => 'product_tag',
                'hide_empty' => false,
            ]);
            if (!is_wp_error($product_tags) && !empty($product_tags)) {
                foreach ($product_tags as $tag) {
                    $tags[$tag->term_id] = $tag->name . ' (' . $tag->count . ')';
                }
            }
        }

        $repeater = new \Elementor\Repeater();

        // Step 1: Filter by category or tag
        $repeater->add_control(
            'filter_type',
            [
                'label' => __('Filter By', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('No Filter (All Products)', 'fuguku-gift'),
                    'category' => __('Category', 'fuguku-gift'),
                    'tag' => __('Tag', 'fuguku-gift'),
                ],
            ]
        );

        $repeater->add_control(
            'filter_category',
            [
                'label' => __('Filter Category', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $categories,
                'default' => '',
                'condition' => [
                    'filter_type' => 'category',
                ],
            ]
        );

        $repeater->add_control(
            'filter_tag',
            [
                'label' => __('Filter Tag', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $tags,
                'default' => '',
                'condition' => [
                    'filter_type' => 'tag',
                ],
            ]
        );

        // Step 2: Manual product selection
        // SELECT2 has built-in client-side search - fast even with 1000+ products
        $all_products = ['' => __('Type to search product...', 'fuguku-gift')];
        if (function_exists('wc_get_products')) {
            $wc_products = wc_get_products([
                'limit' => -1, // Load ALL products (SELECT2 search handles it efficiently)
                'status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
            ]);
            foreach ($wc_products as $product) {
                $product_cats = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']);
                $cat_label = !empty($product_cats) ? ' [' . $product_cats[0] . ']' : '';
                $all_products[$product->get_id()] = $product->get_name() . $cat_label . ' (#' . $product->get_id() . ')';
            }
        }

        $repeater->add_control(
            'product_id',
            [
                'label' => __('Search Product', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $all_products,
                'default' => '',
                'label_block' => true,
                'description' => __('Type to search product name instantly (SELECT2 built-in search). Category shown in [brackets].', 'fuguku-gift'),
            ]
        );

        $repeater->add_control(
            'sort_order',
            [
                'label' => __('Sort Order', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => __('Products', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'filter_type' => 'none',
                        'product_id' => '',
                        'sort_order' => 1,
                    ],
                ],
                'title_field' => 'Product #{{{ sort_order }}}',
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __('Layout', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '3' => __('3 Columns', 'fuguku-gift'),
                    '4' => __('4 Columns', 'fuguku-gift'),
                    '5' => __('5 Columns', 'fuguku-gift'),
                ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label' => __('Gap Between Items', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-container' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'object_fit',
            [
                'label' => __('Image Object Fit', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'cover' => __('Cover', 'fuguku-gift'),
                    'contain' => __('Contain', 'fuguku-gift'),
                    'fill' => __('Fill', 'fuguku-gift'),
                ],
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __('Show Navigation', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'description_length',
            [
                'label' => __('Description Length (words)', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 0,
                'max' => 50,
            ]
        );

        $this->end_controls_section();

        // Style Section - Item
        $this->start_controls_section(
            'style_item_section',
            [
                'label' => __('Item', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_width',
            [
                'label' => __('Item Width', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 800,
                        'step' => 10,
                    ],
                    '%' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-item' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_height',
            [
                'label' => __('Item Height', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 200,
                        'max' => 600,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 300,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-item' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'label' => __('Box Shadow', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-productshow-item',
            ]
        );

        $this->end_controls_section();

        // Style Section - Overlay
        $this->start_controls_section(
            'style_overlay_section',
            [
                'label' => __('Overlay', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_opacity',
            [
                'label' => __('Overlay Opacity', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['%'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => '%',
                    'size' => 100,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-overlay' => 'opacity: calc({{SIZE}} / 100);',
                ],
            ]
        );

        $this->add_control(
            'overlay_color',
            [
                'label' => __('Overlay Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-overlay' => 'background: linear-gradient(to top, {{VALUE}} 0%, transparent 100%);',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'style_title_section',
            [
                'label' => __('Title', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-productshow-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#222222',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_margin',
            [
                'label' => __('Margin', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Price
        $this->start_controls_section(
            'style_price_section',
            [
                'label' => __('Price', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-productshow-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Description
        $this->start_controls_section(
            'style_description_section',
            [
                'label' => __('Description', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-productshow-description',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#888888',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Navigation
        $this->start_controls_section(
            'style_navigation_section',
            [
                'label' => __('Navigation', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'nav_button_size',
            [
                'label' => __('Arrow Size', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 16,
                        'max' => 48,
                        'step' => 2,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-nav-btn' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'nav_button_color',
            [
                'label' => __('Arrow Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-nav-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['items'])) {
            echo '<div class="fugu-productshow-notice">';
            echo '<p>' . __('Please add some products.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Check if WooCommerce is active
        if (!function_exists('wc_get_product')) {
            echo '<div class="fugu-productshow-notice">';
            echo '<p>' . __('WooCommerce is required for this widget.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Sort items by sort_order
        $items = $settings['items'];
        usort($items, function($a, $b) {
            return ($a['sort_order'] ?? 1) - ($b['sort_order'] ?? 1);
        });

        $columns = $settings['columns'];
        $object_fit = $settings['object_fit'];
        $show_navigation = $settings['show_navigation'];
        $description_length = (int) ($settings['description_length'] ?? 6);
        ?>
        
        <div class="fugu-productshow-container" data-columns="<?php echo esc_attr($columns); ?>" data-object-fit="<?php echo esc_attr($object_fit); ?>">
            <?php foreach ($items as $item_index => $item) : 
                $product_id = (int) ($item['product_id'] ?? 0);
                
                // Skip if empty or separator (starts with _cat_)
                if (!$product_id || strpos((string)$product_id, '_cat_') === 0) continue;
                
                $product = wc_get_product($product_id);
                if (!$product) continue;

                // Get product data
                $title = $product->get_name();
                $price = $product->get_price_html();
                $short_desc = $product->get_short_description();
                
                // Truncate description to X words
                $description_output = '';
                if ($description_length > 0 && $short_desc) {
                    $clean_desc = strip_tags($short_desc);
                    $words = preg_split('/\s+/', $clean_desc, -1, PREG_SPLIT_NO_EMPTY);
                    if (count($words) > $description_length) {
                        $description_output = implode(' ', array_slice($words, 0, $description_length)) . '...';
                    } else {
                        $description_output = $clean_desc;
                    }
                }

                // Get product images
                $image_ids = [];
                $featured_image_id = (int) $product->get_image_id();
                if ($featured_image_id) {
                    $image_ids[] = $featured_image_id;
                }
                $gallery_ids = $product->get_gallery_image_ids();
                if (!empty($gallery_ids)) {
                    $image_ids = array_merge($image_ids, $gallery_ids);
                }

                $product_url = get_permalink($product_id);
            ?>
                <div class="fugu-productshow-item" data-item-index="<?php echo $item_index; ?>">
                    
                    <?php if (!empty($image_ids)) : ?>
                        <div class="fugu-productshow-image-container">
                            <?php foreach ($image_ids as $image_index => $image_id) : ?>
                                <div class="fugu-productshow-image <?php echo ($image_index === 0) ? 'active' : ''; ?>" 
                                     data-image-index="<?php echo $image_index; ?>">
                                    <a href="<?php echo esc_url($product_url); ?>">
                                        <?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => esc_attr($title), 'loading' => 'lazy']); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="fugu-productshow-image-container">
                            <div class="fugu-productshow-image active">
                                <a href="<?php echo esc_url($product_url); ?>">
                                    <?php echo wc_placeholder_img('large'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="fugu-productshow-overlay">
                        <div class="fugu-productshow-content">
                            <?php if ($title) : ?>
                                <h3 class="fugu-productshow-title">
                                    <a href="<?php echo esc_url($product_url); ?>">
                                        <?php echo esc_html($title); ?>
                                    </a>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if ($price) : ?>
                                <div class="fugu-productshow-price"><?php echo wp_kses_post($price); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($description_output) : ?>
                                <div class="fugu-productshow-description"><?php echo esc_html($description_output); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($show_navigation === 'yes' && !empty($image_ids) && count($image_ids) > 1) : ?>
                        <div class="fugu-productshow-navigation">
                            <button class="fugu-productshow-nav-btn fugu-productshow-prev" data-direction="prev" data-item-index="<?php echo $item_index; ?>" aria-label="Previous image">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="fugu-productshow-nav-btn fugu-productshow-next" data-direction="next" data-item-index="<?php echo $item_index; ?>" aria-label="Next image">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($show_navigation === 'yes') : ?>
        <script>
        jQuery(document).ready(function($) {
            $('.fugu-productshow-container').each(function() {
                var container = $(this);
                
                container.find('.fugu-productshow-nav-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var direction = $(this).data('direction');
                    var itemIndex = $(this).data('item-index');
                    var item = container.find('.fugu-productshow-item[data-item-index="' + itemIndex + '"]');
                    var images = item.find('.fugu-productshow-image');
                    var currentImage = item.find('.fugu-productshow-image.active');
                    var currentIndex = currentImage.data('image-index');
                    var totalImages = images.length;
                    
                    if (direction === 'prev') {
                        var newIndex = (currentIndex - 1 + totalImages) % totalImages;
                    } else {
                        var newIndex = (currentIndex + 1) % totalImages;
                    }
                    
                    currentImage.removeClass('active');
                    item.find('.fugu-productshow-image[data-image-index="' + newIndex + '"]').addClass('active');
                });
            });
        });
        </script>
        <?php endif; ?>

        <style>
        /* Match FUGU Images Item styling exactly */
        .fugu-productshow-container {
            display: grid;
            gap: 30px;
            width: 100%;
        }
        .fugu-productshow-container[data-columns="3"] {
            grid-template-columns: repeat(3, 1fr);
        }
        .fugu-productshow-container[data-columns="4"] {
            grid-template-columns: repeat(4, 1fr);
        }
        .fugu-productshow-container[data-columns="5"] {
            grid-template-columns: repeat(5, 1fr);
        }
        .fugu-productshow-item {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            border-radius: 0;
        }
        .fugu-productshow-image-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .fugu-productshow-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .fugu-productshow-image.active {
            opacity: 1;
        }
        .fugu-productshow-image a {
            display: block;
            width: 100%;
            height: 100%;
        }
        .fugu-productshow-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .fugu-productshow-container[data-object-fit="contain"] .fugu-productshow-image img {
            object-fit: contain;
        }
        .fugu-productshow-container[data-object-fit="fill"] .fugu-productshow-image img {
            object-fit: fill;
        }
        /* Overlay with gradient - same as FUGU Images Item */
        .fugu-productshow-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, rgba(255,255,255,0.9) 0%, transparent 100%);
            z-index: 2;
            pointer-events: none;
        }
        .fugu-productshow-content {
            position: relative;
            z-index: 3;
        }
        .fugu-productshow-content * {
            pointer-events: auto;
        }
        .fugu-productshow-title {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: 600;
            color: #222222;
            line-height: 1.3;
        }
        .fugu-productshow-title a {
            color: inherit;
            text-decoration: none;
        }
        .fugu-productshow-title a:hover {
            text-decoration: underline;
        }
        .fugu-productshow-price {
            margin: 0 0 8px 0;
            font-size: 16px;
            color: #666666;
            line-height: 1.3;
        }
        .fugu-productshow-description {
            margin: 0;
            font-size: 14px;
            color: #888888;
            line-height: 1.5;
        }
        /* Navigation - hidden by default, show on item hover */
        .fugu-productshow-navigation {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            z-index: 10;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .fugu-productshow-item:hover .fugu-productshow-navigation {
            opacity: 1;
        }
        .fugu-productshow-nav-btn {
            width: auto !important;
            height: auto !important;
            border: none !important;
            background: transparent !important;
            color: #ffffff !important;
            font-size: 24px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            text-shadow: none !important;
            transition: none !important;
            border-radius: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            border-color: transparent !important;
            text-decoration: none !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
        }
        .fugu-productshow-nav-btn:hover,
        .fugu-productshow-nav-btn:focus,
        .fugu-productshow-nav-btn:active,
        .fugu-productshow-nav-btn:visited,
        .fugu-productshow-nav-btn:link {
            background: transparent !important;
            color: #ffffff !important;
            outline: none !important;
            box-shadow: none !important;
            text-shadow: none !important;
            transform: none !important;
            border: none !important;
            border-radius: 0 !important;
            border-width: 0 !important;
            border-style: none !important;
            border-color: transparent !important;
            text-decoration: none !important;
        }
        .fugu-productshow-nav-btn i {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .fugu-productshow-notice {
            padding: 20px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            text-align: center;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .fugu-productshow-container {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media (max-width: 480px) {
            .fugu-productshow-container {
                grid-template-columns: 1fr !important;
            }
        }
        </style>

        <?php
    }
}
