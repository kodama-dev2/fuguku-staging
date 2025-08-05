<?php
/**
 * Gift Grid Widget for Elementor - Louis Vuitton Style
 * 
 * @package Fuguku_Gifts
 * @version 1.1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gift Grid Widget Class
 */
class Fuguku_Gift_Grid_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'fuguku_gift_grid';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Gift Grid', 'fuguku-gift');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
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
        return ['gift', 'grid', 'products', 'shop', 'louis vuitton'];
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
            'layout_type',
            [
                'label' => __('Layout Type', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'section_1',
                'options' => [
                    'section_1' => __('Section 1 (4 Items: 30% 20% 20% 30%)', 'fuguku-gift'),
                    'section_2' => __('Section 2 (5 Items: 15% 15% 40% 15% 15%)', 'fuguku-gift'),
                    'compact' => __('Compact Grid (4 Columns No Gap)', 'fuguku-gift'),
                    'regular' => __('Regular Grid', 'fuguku-gift'),
                    'lv_mixed' => __('Louis Vuitton Mixed Grid', 'fuguku-gift'),
                ],
            ]
        );

        $this->add_control(
            'show_info',
            [
                'label' => __('Show Item Info', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
                'description' => __('Show title, price, and other info below image', 'fuguku-gift'),
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'show_featured_only',
            [
                'label' => __('Show Featured Only', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => '',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'fuguku-gift'),
                    'title' => __('Title', 'fuguku-gift'),
                    'menu_order' => __('Menu Order', 'fuguku-gift'),
                    'rand' => __('Random', 'fuguku-gift'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'DESC' => __('Descending', 'fuguku-gift'),
                    'ASC' => __('Ascending', 'fuguku-gift'),
                ],
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

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-gifts-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ],
                'condition' => [
                    'layout_type' => 'regular',
                ],
            ]
        );

        $this->add_control(
            'gap',
            [
                'label' => __('Gap', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
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
                    '{{WRAPPER}} .elementor-gifts-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout_type' => 'regular',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Cards
        $this->start_controls_section(
            'style_cards_section',
            [
                'label' => __('Cards', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'card_background',
                'label' => __('Background', 'fuguku-gift'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .gift-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-card',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Image Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'card_padding',
            [
                'label' => __('Card Padding', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Titles
        $this->start_controls_section(
            'style_titles_section',
            [
                'label' => __('Titles', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Prices
        $this->start_controls_section(
            'style_prices_section',
            [
                'label' => __('Prices', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-price' => 'color: {{VALUE}};',
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

        // Build query args
        $args = array(
            'post_type' => 'gifts',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
            'post_status' => 'publish',
        );

        // Add featured filter
        if ($settings['show_featured_only'] === 'yes') {
            $args['meta_query'] = array(
                array(
                    'key' => '_featured_gift',
                    'value' => '1',
                    'compare' => '='
                )
            );
        }

        $gifts_query = new WP_Query($args);

        if ($gifts_query->have_posts()) :
            // Determine grid class based on layout type
            $grid_class = 'gifts-grid';
            if ($settings['layout_type'] === 'section_1') {
                $grid_class .= ' gifts-grid-section-1';
            } elseif ($settings['layout_type'] === 'section_2') {
                $grid_class .= ' gifts-grid-section-2';
            } elseif ($settings['layout_type'] === 'compact') {
                $grid_class .= ' gifts-grid-compact';
            } elseif ($settings['layout_type'] === 'lv_mixed') {
                $grid_class .= ' gifts-grid-lv';
            } else {
                $grid_class .= ' elementor-gifts-grid';
            }
            ?>
            <div class="<?php echo esc_attr($grid_class); ?>">
                <?php 
                $counter = 0;
                while ($gifts_query->have_posts()) : $gifts_query->the_post(); 
                    $counter++;
                    $is_featured = get_post_meta(get_the_ID(), '_featured_gift', true);
                    
                    // Determine card class based on layout type
                    if ($settings['layout_type'] === 'section_1') {
                        $card_class = 'gift-card';
                        if ($counter === 1 || $counter === 4) {
                            $card_class .= ' section-1-wide';
                        } else {
                            $card_class .= ' section-1-regular';
                        }
                    } elseif ($settings['layout_type'] === 'section_2') {
                        $card_class = 'gift-card';
                        if ($counter === 3) {
                            $card_class .= ' section-2-center';
                        } else {
                            $card_class .= ' section-2-regular';
                        }
                    } elseif ($settings['layout_type'] === 'compact') {
                        $card_class = 'gift-card compact';
                    } elseif ($settings['layout_type'] === 'lv_mixed') {
                        $card_class = 'gift-card';
                        if ($is_featured) {
                            $card_class .= ' featured-lv';
                        } elseif ($counter % 6 == 0) {
                            $card_class .= ' medium-lv';
                        } else {
                            $card_class .= ' regular-lv';
                        }
                    } else {
                        $card_class = 'gift-card ' . ($is_featured ? 'featured' : 'regular');
                    }
                    ?>
                    
                    <article class="<?php echo esc_attr($card_class); ?>">
                        
                        <!-- Gift Image -->
                        <div class="gift-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large', array('class' => 'gift-thumbnail')); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>">
                                    <div class="gift-placeholder">
                                        <i class="fa fa-gift"></i>
                                    </div>
                                </a>
                            <?php endif; ?>
                            
                            <!-- Featured Badge -->
                            <?php if ($is_featured) : ?>
                                <div class="featured-badge">
                                    <i class="fa fa-star"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Gift Info -->
                        <?php if ($settings['show_info'] === 'yes') : ?>
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
                        </div>
                        <?php endif; ?>

                    </article>

                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            ?>
            <div class="no-gifts-found">
                <div class="no-gifts-icon">
                    <i class="fa fa-gift"></i>
                </div>
                <h3><?php echo esc_html__('No Gifts Found', 'fuguku-gift'); ?></h3>
                <p><?php echo esc_html__('We couldn\'t find any gifts matching your criteria.', 'fuguku-gift'); ?></p>
            </div>
            <?php
        endif;
    }
} 