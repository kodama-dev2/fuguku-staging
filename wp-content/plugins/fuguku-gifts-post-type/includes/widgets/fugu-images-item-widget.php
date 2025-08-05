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

        // Multiple images per item
        $repeater->add_control(
            'images',
            [
                'label' => __('Images', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::GALLERY,
                'default' => [],
                'description' => __('Upload multiple images for this item. Use prev/next arrows to navigate between images.', 'fuguku-gift'),
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
            'text',
            [
                'label' => __('Text', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Item description text goes here...', 'fuguku-gift'),
                'label_block' => true,
                'rows' => 3,
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
                        'text' => __('Description for item 1...', 'fuguku-gift'),
                        'sort_order' => 1,
                    ],
                    [
                        'title' => __('Item 2', 'fuguku-gift'),
                        'subtitle' => __('Subtitle 2', 'fuguku-gift'),
                        'text' => __('Description for item 2...', 'fuguku-gift'),
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
                    '{{WRAPPER}} .fugu-images-container' => 'gap: {{SIZE}}{{UNIT}};',
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
                'description' => __('Show next/prev buttons when multiple images in item', 'fuguku-gift'),
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
                    '{{WRAPPER}} .fugu-images-item' => 'width: {{SIZE}}{{UNIT}};',
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

        // Style Section - Text
        $this->start_controls_section(
            'style_text_section',
            [
                'label' => __('Text', 'fuguku-gift'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __('Typography', 'fuguku-gift'),
                'selector' => '{{WRAPPER}} .fugu-images-text',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Color', 'fuguku-gift'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#888888',
                'selectors' => [
                    '{{WRAPPER}} .fugu-images-text' => 'color: {{VALUE}};',
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
                'default' => 'rgba(0,0,0,0.3)',
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
        
        <div class="fugu-images-container" data-columns="<?php echo esc_attr($columns); ?>" data-object-fit="<?php echo esc_attr($object_fit); ?>">
            <?php foreach ($items as $item_index => $item) : ?>
                <div class="fugu-images-item" data-item-index="<?php echo $item_index; ?>">
                    
                    <?php if (!empty($item['images']) && is_array($item['images'])) : ?>
                        <div class="fugu-images-image-container">
                            <?php foreach ($item['images'] as $image_index => $image) : ?>
                                <div class="fugu-images-image <?php echo ($image_index === 0) ? 'active' : ''; ?>" 
                                     data-image-index="<?php echo $image_index; ?>">
                                    
                                    <?php if (!empty($item['link']['url'])) : ?>
                                        <a href="<?php echo esc_url($item['link']['url']); ?>" 
                                           <?php echo ($item['link']['is_external'] ? 'target="_blank"' : ''); ?>
                                           <?php echo ($item['link']['nofollow'] ? 'rel="nofollow"' : ''); ?>>
                                    <?php endif; ?>
                                    
                                    <img src="<?php echo esc_url($image['url']); ?>" 
                                         alt="<?php echo esc_attr($item['title']); ?>">
                                    
                                    <?php if (!empty($item['link']['url'])) : ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
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
                            
                            <?php if (!empty($item['text'])) : ?>
                                <p class="fugu-images-text"><?php echo esc_html($item['text']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($show_navigation === 'yes' && !empty($item['images']) && count($item['images']) > 1) : ?>
                        <div class="fugu-images-navigation">
                            <button class="fugu-images-nav-btn fugu-images-prev" data-direction="prev" data-item-index="<?php echo $item_index; ?>">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <button class="fugu-images-nav-btn fugu-images-next" data-direction="next" data-item-index="<?php echo $item_index; ?>">
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
            $('.fugu-images-container').each(function() {
                var container = $(this);
                
                container.find('.fugu-images-nav-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var direction = $(this).data('direction');
                    var itemIndex = $(this).data('item-index');
                    var item = container.find('.fugu-images-item[data-item-index="' + itemIndex + '"]');
                    var images = item.find('.fugu-images-image');
                    var currentImage = item.find('.fugu-images-image.active');
                    var currentIndex = currentImage.data('image-index');
                    var totalImages = images.length;
                    
                    if (direction === 'prev') {
                        var newIndex = (currentIndex - 1 + totalImages) % totalImages;
                    } else {
                        var newIndex = (currentIndex + 1) % totalImages;
                    }
                    
                    currentImage.removeClass('active');
                    item.find('.fugu-images-image[data-image-index="' + newIndex + '"]').addClass('active');
                });
            });
        });
        </script>
        <?php endif; ?>

        <?php
    }
}
