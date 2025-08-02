<?php
/**
 * Gift Slider Widget for Elementor
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gift Slider Widget Class
 */
class Fuguku_Gift_Slider_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'fuguku_gift_slider';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Gift Slider', 'fuguku-gift');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-slider-push';
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
        return ['gift', 'slider', 'carousel', 'products'];
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
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 20,
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

        // Slider Settings
        $this->start_controls_section(
            'slider_section',
            [
                'label' => __('Slider Settings', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'slides_to_show',
            [
                'label' => __('Slides to Show', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 6,
            ]
        );

        $this->add_control(
            'slides_to_scroll',
            [
                'label' => __('Slides to Scroll', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 3,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay Speed', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'min' => 1000,
                'max' => 10000,
                'step' => 500,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'label' => __('Pause on Hover', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => __('Show Arrows', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => __('Show Dots', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => __('Infinite Loop', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
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
            'arrow_color',
            [
                'label' => __('Arrow Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-arrow' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dot_color',
            [
                'label' => __('Dot Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-dots li button' => 'background-color: {{VALUE}};',
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
            ?>
            <div class="gifts-slider elementor-gifts-slider" 
                 data-slides-to-show="<?php echo esc_attr($settings['slides_to_show']); ?>"
                 data-slides-to-scroll="<?php echo esc_attr($settings['slides_to_scroll']); ?>"
                 data-autoplay="<?php echo esc_attr($settings['autoplay']); ?>"
                 data-autoplay-speed="<?php echo esc_attr($settings['autoplay_speed']); ?>"
                 data-pause-on-hover="<?php echo esc_attr($settings['pause_on_hover']); ?>"
                 data-show-arrows="<?php echo esc_attr($settings['show_arrows']); ?>"
                 data-show-dots="<?php echo esc_attr($settings['show_dots']); ?>"
                 data-infinite="<?php echo esc_attr($settings['infinite']); ?>">
                
                <?php while ($gifts_query->have_posts()) : $gifts_query->the_post(); ?>
                    
                    <div class="gift-slide">
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
                            </div>

                        </article>
                    </div>

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