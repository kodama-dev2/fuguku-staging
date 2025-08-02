<?php
/**
 * Gift Filter Widget for Elementor
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Gift Filter Widget Class
 */
class Fuguku_Gift_Filter_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'fuguku_gift_filter';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Gift Filter', 'fuguku-gift');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-filter';
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
        return ['gift', 'filter', 'search', 'category'];
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
            'show_search',
            [
                'label' => __('Show Search', 'fuguku-gift'),
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
            'show_price_filter',
            [
                'label' => __('Show Price Filter', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_availability_filter',
            [
                'label' => __('Show Availability Filter', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'fuguku-gift'),
                'label_off' => __('No', 'fuguku-gift'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'search_placeholder',
            [
                'label' => __('Search Placeholder', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Search gifts...', 'fuguku-gift'),
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Filter Bar
        $this->start_controls_section(
            'style_filter_section',
            [
                'label' => __('Filter Bar', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'filter_background',
                'label' => __('Background', 'fuguku-gift'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .gifts-filter-bar',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'filter_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gifts-filter-bar',
            ]
        );

        $this->add_control(
            'filter_padding',
            [
                'label' => __('Padding', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gifts-filter-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Search Input
        $this->start_controls_section(
            'style_search_section',
            [
                'label' => __('Search Input', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_search' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'search_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-search-input',
            ]
        );

        $this->add_control(
            'search_color',
            [
                'label' => __('Text Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-search-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'search_background',
            [
                'label' => __('Background Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gift-search-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'search_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .gift-search-input',
            ]
        );

        $this->add_control(
            'search_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .gift-search-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Filter Buttons
        $this->start_controls_section(
            'style_buttons_section',
            [
                'label' => __('Filter Buttons', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .filter-btn',
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
                    '{{WRAPPER}} .filter-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background',
            [
                'label' => __('Background Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .filter-btn' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .filter-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_background',
            [
                'label' => __('Background Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .filter-btn:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .filter-btn',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .filter-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
        ?>
        
        <div class="gifts-filter-bar elementor-gifts-filter">
            
            <?php if ($settings['show_search'] === 'yes') : ?>
                <div class="filter-search">
                    <input type="text" 
                           class="gift-search-input" 
                           placeholder="<?php echo esc_attr($settings['search_placeholder']); ?>"
                           data-filter="search">
                </div>
            <?php endif; ?>

            <?php if ($settings['show_categories'] === 'yes') : ?>
                <div class="filter-categories">
                    <div class="filter-dropdown">
                        <button class="filter-btn" data-filter="category">
                            <span><?php echo esc_html__('Categories', 'fuguku-gift'); ?></span>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <div class="filter-dropdown-content">
                            <a href="#" data-category="" class="active"><?php echo esc_html__('All Categories', 'fuguku-gift'); ?></a>
                            <?php
                            $gift_categories = get_terms(array(
                                'taxonomy' => 'gift_category',
                                'hide_empty' => true,
                            ));
                            
                            if (!empty($gift_categories) && !is_wp_error($gift_categories)) {
                                foreach ($gift_categories as $category) {
                                    echo '<a href="#" data-category="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_price_filter'] === 'yes') : ?>
                <div class="filter-price">
                    <div class="filter-dropdown">
                        <button class="filter-btn" data-filter="price">
                            <span><?php echo esc_html__('Price Range', 'fuguku-gift'); ?></span>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <div class="filter-dropdown-content">
                            <a href="#" data-price="" class="active"><?php echo esc_html__('All Prices', 'fuguku-gift'); ?></a>
                            <a href="#" data-price="0-100000"><?php echo esc_html__('Under IDR 100,000', 'fuguku-gift'); ?></a>
                            <a href="#" data-price="100000-500000"><?php echo esc_html__('IDR 100,000 - 500,000', 'fuguku-gift'); ?></a>
                            <a href="#" data-price="500000-1000000"><?php echo esc_html__('IDR 500,000 - 1,000,000', 'fuguku-gift'); ?></a>
                            <a href="#" data-price="1000000-999999999"><?php echo esc_html__('Over IDR 1,000,000', 'fuguku-gift'); ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_availability_filter'] === 'yes') : ?>
                <div class="filter-availability">
                    <div class="filter-dropdown">
                        <button class="filter-btn" data-filter="availability">
                            <span><?php echo esc_html__('Availability', 'fuguku-gift'); ?></span>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <div class="filter-dropdown-content">
                            <a href="#" data-availability="" class="active"><?php echo esc_html__('All', 'fuguku-gift'); ?></a>
                            <a href="#" data-availability="in_stock"><?php echo esc_html__('In Stock', 'fuguku-gift'); ?></a>
                            <a href="#" data-availability="limited"><?php echo esc_html__('Limited Stock', 'fuguku-gift'); ?></a>
                            <a href="#" data-availability="out_of_stock"><?php echo esc_html__('Out of Stock', 'fuguku-gift'); ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="filter-actions">
                <button class="filter-btn filter-clear" data-action="clear">
                    <i class="fa fa-times"></i>
                    <span><?php echo esc_html__('Clear Filters', 'fuguku-gift'); ?></span>
                </button>
            </div>

        </div>

        <script>
        jQuery(document).ready(function($) {
            // Filter functionality will be implemented here
            $('.gift-search-input').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                // Implement search logic
            });

            $('.filter-dropdown-content a').on('click', function(e) {
                e.preventDefault();
                var filterType = $(this).closest('.filter-dropdown').find('.filter-btn').data('filter');
                var filterValue = $(this).data(filterType);
                // Implement filter logic
            });

            $('.filter-clear').on('click', function() {
                // Clear all filters
                $('.gift-search-input').val('');
                $('.filter-dropdown-content a').removeClass('active');
                $('.filter-dropdown-content a[data-category=""]').addClass('active');
                $('.filter-dropdown-content a[data-price=""]').addClass('active');
                $('.filter-dropdown-content a[data-availability=""]').addClass('active');
            });
        });
        </script>
        
        <?php
    }
} 