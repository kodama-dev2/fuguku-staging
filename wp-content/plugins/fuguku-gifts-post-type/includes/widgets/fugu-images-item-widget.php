<?php
/**
 * FUGU Images Item Widget
 * 
 * @package Fuguku_Gifts
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Fugu_Images_Item_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'fugu-images-item';
    }

    public function get_title() {
        return __('FUGU Images Item', 'fuguku-gift');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['fuguku-gifts'];
    }

    public function get_keywords() {
        return ['fugu', 'images', 'item', 'gallery', 'overlay'];
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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => __('Link', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'fuguku-gift'),
                'default' => [
                    'url' => '',
                    'is_external' => '',
                    'nofollow' => '',
                ],
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Item Title', 'fuguku-gift'),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'subtitle',
            [
                'label' => __('Subtitle', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Item Subtitle', 'fuguku-gift'),
                'label_block' => true,
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
                'label' => __('Items', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => __('Item 1', 'fuguku-gift'),
                        'subtitle' => __('Subtitle 1', 'fuguku-gift'),
                        'sort_order' => 1,
                    ],
                    [
                        'title' => __('Item 2', 'fuguku-gift'),
                        'subtitle' => __('Subtitle 2', 'fuguku-gift'),
                        'sort_order' => 2,
                    ],
                ],
                'title_field' => '{{{ title }}}',
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
                    '{{WRAPPER}} .fugu-images-item' => 'height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .fugu-images-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'label' => __('Box Shadow', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-images-item',
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
                    '{{WRAPPER}} .fugu-images-overlay' => 'opacity: calc({{SIZE}} / 100);',
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
                    '{{WRAPPER}} .fugu-images-overlay' => 'background: linear-gradient(to top, {{VALUE}} 0%, transparent 100%);',
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
                'selector' => '{{WRAPPER}} .fugu-images-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#222222',
                'selectors' => [
                    '{{WRAPPER}} .fugu-images-title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .fugu-images-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Subtitle
        $this->start_controls_section(
            'style_subtitle_section',
            [
                'label' => __('Subtitle', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-images-subtitle',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .fugu-images-subtitle' => 'color: {{VALUE}};',
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
                    'size' => 40,
                ],
                'selectors' => [
                    '{{WRAPPER}} .fugu-images-nav-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .fugu-images-nav-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'nav_button_bg',
            [
                'label' => __('Button Background', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(0,0,0,0.5)',
                'selectors' => [
                    '{{WRAPPER}} .fugu-images-nav-btn' => 'background-color: {{VALUE}};',
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
            echo '<div class="fugu-images-no-items">';
            echo '<p>' . __('Please add some items to display.', 'fuguku-gift') . '</p>';
            echo '</div>';
            return;
        }

        // Sort items by sort_order
        $items = $settings['items'];
        usort($items, function($a, $b) {
            return $a['sort_order'] - $b['sort_order'];
        });

        $columns = $settings['columns'];
        $object_fit = $settings['object_fit'];
        $show_navigation = $settings['show_navigation'];
        ?>
        
        <div class="fugu-images-container" data-columns="<?php echo esc_attr($columns); ?>">
            <?php foreach ($items as $index => $item) : ?>
                <div class="fugu-images-item" data-index="<?php echo $index; ?>">
                    
                    <?php if (!empty($item['image']['url'])) : ?>
                        <div class="fugu-images-image">
                            <?php if (!empty($item['link']['url'])) : ?>
                                <a href="<?php echo esc_url($item['link']['url']); ?>" 
                                   <?php echo ($item['link']['is_external'] ? 'target="_blank"' : ''); ?>
                                   <?php echo ($item['link']['nofollow'] ? 'rel="nofollow"' : ''); ?>>
                            <?php endif; ?>
                            
                            <img src="<?php echo esc_url($item['image']['url']); ?>" 
                                 alt="<?php echo esc_attr($item['title']); ?>"
                                 style="object-fit: <?php echo esc_attr($object_fit); ?>;">
                            
                            <?php if (!empty($item['link']['url'])) : ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="fugu-images-overlay">
                        <div class="fugu-images-content">
                            <?php if (!empty($item['title'])) : ?>
                                <h3 class="fugu-images-title">
                                    <?php if (!empty($item['link']['url'])) : ?>
                                        <a href="<?php echo esc_url($item['link']['url']); ?>" 
                                           <?php echo ($item['link']['is_external'] ? 'target="_blank"' : ''); ?>
                                           <?php echo ($item['link']['nofollow'] ? 'rel="nofollow"' : ''); ?>>
                                            <?php echo esc_html($item['title']); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo esc_html($item['title']); ?>
                                    <?php endif; ?>
                                </h3>
                            <?php endif; ?>
                            
                            <?php if (!empty($item['subtitle'])) : ?>
                                <p class="fugu-images-subtitle"><?php echo esc_html($item['subtitle']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($show_navigation === 'yes' && count($items) > 1) : ?>
                        <div class="fugu-images-navigation">
                            <button class="fugu-images-nav-btn fugu-images-prev" data-direction="prev">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="fugu-images-nav-btn fugu-images-next" data-direction="next">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($show_navigation === 'yes' && count($items) > 1) : ?>
        <script>
        jQuery(document).ready(function($) {
            $('.fugu-images-container').each(function() {
                var container = $(this);
                var items = container.find('.fugu-images-item');
                var currentIndex = 0;
                
                container.find('.fugu-images-nav-btn').on('click', function() {
                    var direction = $(this).data('direction');
                    
                    if (direction === 'prev') {
                        currentIndex = (currentIndex - 1 + items.length) % items.length;
                    } else {
                        currentIndex = (currentIndex + 1) % items.length;
                    }
                    
                    items.hide();
                    items.eq(currentIndex).show();
                });
            });
        });
        </script>
        <?php endif; ?>

        <?php
    }
}
