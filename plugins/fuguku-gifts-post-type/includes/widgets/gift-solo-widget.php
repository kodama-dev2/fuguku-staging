<?php
/**
 * Gift Solo Widget
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fuguku_Gift_Solo_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'fuguku-gift-solo';
    }

    public function get_title() {
        return __('Gift Solo', 'fuguku-gift');
    }

    public function get_icon() {
        return 'eicon-gift';
    }

    public function get_categories() {
        return ['fuguku-gifts'];
    }

    public function get_keywords() {
        return ['gift', 'solo', 'single', 'product'];
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

        $this->add_control(
            'gift_id',
            [
                'label' => __('Select Gift', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_gifts_list(),
                'default' => '',
                'description' => __('Choose a specific gift to display', 'fuguku-gift'),
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
                'default' => 'no',
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
                'selector' => '{{WRAPPER}} .gift-solo-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-solo-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-solo-card',
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label' => __('Padding', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Image
        $this->start_controls_section(
            'style_image_section',
            [
                'label' => __('Image', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_height',
            [
                'label' => __('Image Height', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                        'step' => 10,
                    ],
                    'em' => [
                        'min' => 5,
                        'max' => 30,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 5,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 250,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-image' => 'height: {{SIZE}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .gift-solo-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .gift-solo-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .gift-solo-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-solo-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get gifts list for select control
     */
    private function get_gifts_list() {
        $gifts = get_posts([
            'post_type' => 'gifts',
            'numberposts' => -1,
            'post_status' => 'publish',
        ]);

        $gifts_list = [];
        foreach ($gifts as $gift) {
            $gifts_list[$gift->ID] = $gift->post_title;
        }

        return $gifts_list;
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        if (empty($settings['gift_id'])) {
            echo '<div class="gift-solo-no-selection">';
            echo '<p>' . __('Please select a gift to display.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        $gift_id = $settings['gift_id'];
        $gift = get_post($gift_id);

        if (!$gift || $gift->post_type !== 'gifts') {
            echo '<div class="gift-solo-error">';
            echo '<p>' . __('Selected gift not found.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        $is_featured = get_post_meta($gift_id, '_featured_gift', true);
        ?>
        
        <div class="gift-solo-card">
            
            <?php if ($settings['show_image'] === 'yes') : ?>
            <div class="gift-solo-image">
                <?php if (has_post_thumbnail($gift_id)) : ?>
                    <a href="<?php echo get_permalink($gift_id); ?>">
                        <?php echo get_the_post_thumbnail($gift_id, 'large', array('class' => 'gift-solo-thumbnail')); ?>
                    </a>
                <?php else : ?>
                    <a href="<?php echo get_permalink($gift_id); ?>">
                        <div class="gift-solo-placeholder">
                            <i class="fa fa-gift"></i>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if ($is_featured) : ?>
                    <div class="gift-solo-featured-badge">
                        <i class="fa fa-star"></i>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="gift-solo-content">
                
                <?php if ($settings['show_title'] === 'yes') : ?>
                <h3 class="gift-solo-title">
                    <a href="<?php echo get_permalink($gift_id); ?>"><?php echo esc_html($gift->post_title); ?></a>
                </h3>
                <?php endif; ?>
                
                <?php if ($settings['show_price'] === 'yes') : ?>
                <?php 
                $gift_price = get_post_meta($gift_id, '_gift_price', true);
                if ($gift_price) : ?>
                    <div class="gift-solo-price">
                        <span class="currency">IDR</span>
                        <span class="price"><?php echo esc_html(number_format($gift_price, 0, ',', '.')); ?></span>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($settings['show_brand'] === 'yes') : ?>
                <?php 
                $gift_brand = get_post_meta($gift_id, '_gift_brand', true);
                if ($gift_brand) : ?>
                    <div class="gift-solo-brand">
                        <?php echo esc_html($gift_brand); ?>
                    </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($settings['show_availability'] === 'yes') : ?>
                <?php 
                $gift_availability = get_post_meta($gift_id, '_gift_availability', true);
                if ($gift_availability) : ?>
                    <div class="gift-solo-availability <?php echo esc_attr($gift_availability); ?>">
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
                <?php endif; ?>
                
                <?php if ($settings['show_description'] === 'yes') : ?>
                <div class="gift-solo-description">
                    <?php echo wp_kses_post(wp_trim_words($gift->post_content, 20, '...')); ?>
                </div>
                <?php endif; ?>
                
            </div>

        </div>

        <?php
    }
} 