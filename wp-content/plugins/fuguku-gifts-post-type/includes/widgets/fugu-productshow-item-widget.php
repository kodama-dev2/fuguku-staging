<?php
/**
 * FUGU ProductShow Item Widget
 * 
 * Displays WooCommerce product with images from gallery, name, price, and description.
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
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
        return ['fugu', 'product', 'woocommerce', 'show', 'item', 'gallery'];
    }

    protected function register_controls() {
        
        // Content Section - Product Selection
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Product', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Check if Elementor Pro Query Control is available
        if (class_exists('\ElementorPro\Modules\QueryControl\Module')) {
            $this->add_control(
                'product_id',
                [
                    'label' => __('Search Product', 'fuguku-gift'),
                    'type' => \ElementorPro\Modules\QueryControl\Module::QUERY_CONTROL_ID,
                    'autocomplete' => [
                        'object' => \ElementorPro\Modules\QueryControl\Types\Posts::QUERY_OBJECT_POST,
                        'query' => [
                            'post_type' => 'product',
                        ],
                    ],
                    'label_block' => true,
                    'description' => __('Type to search for a product', 'fuguku-gift'),
                ]
            );
        } else {
            // Fallback: Manual product ID input
            $this->add_control(
                'product_id',
                [
                    'label' => __('Product ID', 'fuguku-gift'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'default' => 0,
                    'description' => __('Enter the product ID manually (Elementor Pro required for product search)', 'fuguku-gift'),
                ]
            );
        }

        $this->add_control(
            'use_short_description',
            [
                'label' => __('Use Short Description', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'return_value' => 'yes',
                'default' => 'yes',
                'description' => __('Show short description instead of full product description', 'fuguku-gift'),
            ]
        );

        $this->add_control(
            'limit_images',
            [
                'label' => __('Limit Images', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 0,
                'min' => 0,
                'max' => 20,
                'description' => __('Limit number of images to show (0 = show all)', 'fuguku-gift'),
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
                    'em' => [
                        'min' => 0,
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 0.5,
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
                'description' => __('Show next/prev buttons when multiple images', 'fuguku-gift'),
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
                    'em' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
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
                    'em' => [
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 20,
                        'max' => 100,
                        'step' => 5,
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
                    'size' => 70,
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
                'label' => __('Button Size', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 60,
                        'step' => 2,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 32,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-nav-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'nav_button_color',
            [
                'label' => __('Button Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-nav-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'nav_button_bg',
            [
                'label' => __('Button Background', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.3)',
                'selectors' => [
                    '{{WRAPPER}} .fugu-productshow-nav-btn' => 'background-color: {{VALUE}};',
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

        // Get product ID from settings
        $product_id = 0;
        if (isset($settings['product_id'])) {
            if (is_array($settings['product_id']) && isset($settings['product_id']['id'])) {
                $product_id = (int) $settings['product_id']['id'];
            } else {
                $product_id = (int) $settings['product_id'];
            }
        }

        // Check if product ID is valid
        if (!$product_id) {
            echo '<div class="fugu-productshow-notice" style="padding: 20px; background: #f9f9f9; border: 1px solid #ddd; text-align: center;">';
            echo '<p>' . __('Please select a product.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Check if WooCommerce is active
        if (!function_exists('wc_get_product')) {
            echo '<div class="fugu-productshow-notice" style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; text-align: center;">';
            echo '<p>' . __('WooCommerce is required for this widget.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Get product
        $product = wc_get_product($product_id);
        if (!$product) {
            echo '<div class="fugu-productshow-notice" style="padding: 20px; background: #f8d7da; border: 1px solid #dc3545; text-align: center;">';
            echo '<p>' . __('Product not found.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Get product data
        $title = $product->get_name();
        $price = $product->get_price_html();
        $description = ('yes' === ($settings['use_short_description'] ?? 'yes'))
            ? $product->get_short_description()
            : get_post_field('post_content', $product_id);

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

        // Limit images if set
        $limit = (int) ($settings['limit_images'] ?? 0);
        if ($limit > 0) {
            $image_ids = array_slice($image_ids, 0, $limit);
        }

        // Get settings
        $columns = $settings['columns'];
        $object_fit = $settings['object_fit'];
        $show_navigation = $settings['show_navigation'];
        $product_url = get_permalink($product_id);
        ?>
        
        <div class="fugu-productshow-container" data-columns="<?php echo esc_attr($columns); ?>" data-object-fit="<?php echo esc_attr($object_fit); ?>">
            <div class="fugu-productshow-item" data-product-id="<?php echo esc_attr($product_id); ?>">
                
                <?php if (!empty($image_ids)) : ?>
                    <div class="fugu-productshow-image-container">
                        <?php foreach ($image_ids as $image_index => $image_id) : ?>
                            <div class="fugu-productshow-image <?php echo ($image_index === 0) ? 'active' : ''; ?>" 
                                 data-image-index="<?php echo $image_index; ?>">
                                <a href="<?php echo esc_url($product_url); ?>">
                                    <?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => esc_attr($title)]); ?>
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
                        
                        <?php if ($description) : ?>
                            <div class="fugu-productshow-description"><?php echo wp_kses_post(wpautop($description)); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($show_navigation === 'yes' && !empty($image_ids) && count($image_ids) > 1) : ?>
                    <div class="fugu-productshow-navigation">
                        <button class="fugu-productshow-nav-btn fugu-productshow-prev" data-direction="prev">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="fugu-productshow-nav-btn fugu-productshow-next" data-direction="next">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if ($show_navigation === 'yes' && !empty($image_ids) && count($image_ids) > 1) : ?>
        <script>
        jQuery(document).ready(function($) {
            $('.fugu-productshow-container').each(function() {
                var container = $(this);
                
                container.find('.fugu-productshow-nav-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var direction = $(this).data('direction');
                    var item = container.find('.fugu-productshow-item');
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
        /* Reuse styles from FUGU Images Item */
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
        .fugu-productshow-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
            background: linear-gradient(to top, #ffffff 0%, transparent 100%);
            opacity: 0.7;
            z-index: 2;
        }
        .fugu-productshow-content {
            position: relative;
            z-index: 3;
        }
        .fugu-productshow-title {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 600;
            color: #222222;
        }
        .fugu-productshow-title a {
            color: inherit;
            text-decoration: none;
        }
        .fugu-productshow-title a:hover {
            text-decoration: underline;
        }
        .fugu-productshow-price {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #666666;
        }
        .fugu-productshow-description {
            margin: 0;
            font-size: 14px;
            color: #888888;
            line-height: 1.5;
        }
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
        }
        .fugu-productshow-nav-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 50%;
            background-color: rgba(0,0,0,0.3);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
            pointer-events: auto;
        }
        .fugu-productshow-nav-btn:hover {
            background-color: rgba(0,0,0,0.5);
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

