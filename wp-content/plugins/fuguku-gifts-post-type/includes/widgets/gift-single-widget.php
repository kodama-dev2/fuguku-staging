<?php
/**
 * Gift Single Widget for Elementor
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gift Single Widget Class
 */
class Fuguku_Gift_Single_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'fuguku_gift_single';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Gift Single', 'fuguku-gift');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-single-page';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['fuguku-gifts'];
    }

    /**
     * Get widget keywords
     */
    public function get_keywords() {
        return ['gift', 'single', 'product', 'detail'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'gift_id',
            [
                'label' => __('Gift ID', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'description' => __('Enter the Gift ID to display. Leave empty to use current post.', 'fuguku-gift'),
                'default' => '',
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => __('Show Image', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Show Title', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => __('Show Price', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_brand',
            [
                'label' => __('Show Brand', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_availability',
            [
                'label' => __('Show Availability', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => __('Show Description', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_categories',
            [
                'label' => __('Show Categories', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_tags',
            [
                'label' => __('Show Tags', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_actions',
            [
                'label' => __('Show Action Buttons', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
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
            'layout_style',
            [
                'label' => __('Layout Style', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal (Image Left)', 'fuguku-gift'),
                    'vertical' => __('Vertical (Image Top)', 'fuguku-gift'),
                    'minimal' => __('Minimal', 'fuguku-gift'),
                ],
            ]
        );

        $this->add_control(
            'image_size',
            [
                'label' => __('Image Size', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'medium_large',
                'options' => [
                    'thumbnail' => __('Thumbnail', 'fuguku-gift'),
                    'medium' => __('Medium', 'fuguku-gift'),
                    'medium_large' => __('Medium Large', 'fuguku-gift'),
                    'large' => __('Large', 'fuguku-gift'),
                    'full' => __('Full Size', 'fuguku-gift'),
                ],
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Card
        $this->start_controls_section(
            'style_card_section',
            [
                'label' => __('Card', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'card_background',
                'label' => __('Background', 'fuguku-gift'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .gift-single-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-single-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-single-card',
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label' => __('Padding', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-single-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-single-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-single-title' => 'color: {{VALUE}};',
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
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-single-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-single-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Actions
        $this->start_controls_section(
            'style_actions_section',
            [
                'label' => __('Action Buttons', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_actions' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-action-btn',
            ]
        );

        $this->start_controls_tabs('button_styles');

        $this->start_controls_tab(
            'button_normal',
            [
                'label' => __('Normal', 'fuguku-gift'),
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label' => __('Text Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-action-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-action-btn' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => __('Hover', 'fuguku-gift'),
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Text Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-action-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => __('Background Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-action-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Get gift post
        $gift_id = $settings['gift_id'] ? $settings['gift_id'] : get_the_ID();
        $gift_post = get_post($gift_id);

        if (!$gift_post || $gift_post->post_type !== 'gifts') {
            echo '<div class="gift-not-found">' . esc_html__('Gift not found.', 'fuguku-gift') . '</div>';
            return;
        }

        // Setup post data
        setup_postdata($gift_post);
        ?>
        
        <div class="gift-single-card elementor-gift-single layout-<?php echo esc_attr($settings['layout_style']); ?>">
            
            <?php if ($settings['show_image'] === 'yes') : ?>
                <div class="gift-single-image">
                    <?php if (has_post_thumbnail($gift_id)) : ?>
                        <?php echo get_the_post_thumbnail($gift_id, $settings['image_size'], array('class' => 'gift-single-thumbnail')); ?>
                    <?php else : ?>
                        <div class="gift-placeholder">
                            <i class="fa fa-gift"></i>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (get_post_meta($gift_id, '_featured_gift', true)) : ?>
                        <div class="featured-badge">
                            <i class="fa fa-star"></i>
                            <span><?php echo esc_html__('Featured', 'fuguku-gift'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="gift-single-content">
                
                <?php if ($settings['show_title'] === 'yes') : ?>
                    <h2 class="gift-single-title">
                        <a href="<?php echo esc_url(get_permalink($gift_id)); ?>"><?php echo esc_html(get_the_title($gift_id)); ?></a>
                    </h2>
                <?php endif; ?>

                <?php if ($settings['show_price'] === 'yes') : 
                    $gift_price = get_post_meta($gift_id, '_gift_price', true);
                    if ($gift_price) : ?>
                        <div class="gift-single-price">
                            <span class="currency">IDR</span>
                            <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                        </div>
                    <?php endif;
                endif; ?>

                <?php if ($settings['show_brand'] === 'yes') : 
                    $gift_brand = get_post_meta($gift_id, '_gift_brand', true);
                    if ($gift_brand) : ?>
                        <div class="gift-single-brand">
                            <strong><?php echo esc_html__('Brand:', 'fuguku-gift'); ?></strong>
                            <span><?php echo esc_html($gift_brand); ?></span>
                        </div>
                    <?php endif;
                endif; ?>

                <?php if ($settings['show_availability'] === 'yes') : 
                    $gift_availability = get_post_meta($gift_id, '_gift_availability', true);
                    if ($gift_availability) : ?>
                        <div class="gift-single-availability <?php echo esc_attr($gift_availability); ?>">
                            <strong><?php echo esc_html__('Availability:', 'fuguku-gift'); ?></strong>
                            <?php 
                            switch ($gift_availability) {
                                case 'in_stock':
                                    echo '<span class="status in-stock"><i class="fa fa-check-circle"></i> ' . esc_html__('In Stock', 'fuguku-gift') . '</span>';
                                    break;
                                case 'limited':
                                    echo '<span class="status limited"><i class="fa fa-exclamation-triangle"></i> ' . esc_html__('Limited Stock', 'fuguku-gift') . '</span>';
                                    break;
                                case 'out_of_stock':
                                    echo '<span class="status out-of-stock"><i class="fa fa-times-circle"></i> ' . esc_html__('Out of Stock', 'fuguku-gift') . '</span>';
                                    break;
                            }
                            ?>
                        </div>
                    <?php endif;
                endif; ?>

                <?php if ($settings['show_description'] === 'yes') : ?>
                    <div class="gift-single-description">
                        <?php echo wp_kses_post(get_the_excerpt($gift_id)); ?>
                    </div>
                <?php endif; ?>

                <?php if ($settings['show_categories'] === 'yes') : 
                    $gift_categories = get_the_terms($gift_id, 'gift_category');
                    if ($gift_categories && !is_wp_error($gift_categories)) : ?>
                        <div class="gift-single-categories">
                            <strong><?php echo esc_html__('Categories:', 'fuguku-gift'); ?></strong>
                            <?php foreach ($gift_categories as $category) : ?>
                                <a href="<?php echo esc_url(get_term_link($category)); ?>" class="gift-category-tag">
                                    <?php echo esc_html($category->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif;
                endif; ?>

                <?php if ($settings['show_tags'] === 'yes') : 
                    $gift_tags = get_the_terms($gift_id, 'gift_tag');
                    if ($gift_tags && !is_wp_error($gift_tags)) : ?>
                        <div class="gift-single-tags">
                            <strong><?php echo esc_html__('Tags:', 'fuguku-gift'); ?></strong>
                            <?php foreach ($gift_tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="gift-tag">
                                    <?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif;
                endif; ?>

                <?php if ($settings['show_actions'] === 'yes') : ?>
                    <div class="gift-single-actions">
                        <a href="<?php echo esc_url(get_permalink($gift_id)); ?>" class="gift-action-btn btn-primary">
                            <i class="fa fa-eye"></i>
                            <?php echo esc_html__('View Details', 'fuguku-gift'); ?>
                        </a>
                        <button class="gift-action-btn btn-secondary">
                            <i class="fa fa-heart"></i>
                            <?php echo esc_html__('Add to Wishlist', 'fuguku-gift'); ?>
                        </button>
                        <button class="gift-action-btn btn-outline">
                            <i class="fa fa-share"></i>
                            <?php echo esc_html__('Share', 'fuguku-gift'); ?>
                        </button>
                    </div>
                <?php endif; ?>

            </div>

        </div>
        
        <?php
        wp_reset_postdata();
    }
} 