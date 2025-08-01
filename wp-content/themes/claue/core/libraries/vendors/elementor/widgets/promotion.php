<?php
/**
 * Add widget banner to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Promotion_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'promotion';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Promotion Banner', 'claue' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-image';
	}
    
    /**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'promotion', 'banner' );
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'claue-elements' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function _register_controls() {
	   
        $this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'claue' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
        
        $this->add_control(
			'image',
			array(
				'label' => esc_html__( 'Image', 'claue' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			)
		);

        $this->add_control(
            'link',
            array(
                'label' => esc_html__( 'Link to', 'claue' ),
                'type' => \Elementor\Controls_Manager::URL,
                'show_external' => true,
                'default' => array(
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ),
            )
        );

        $this->add_control(
            'v_align',
            array(
                'label' => esc_html__( 'Text vertical align', 'claue' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'top' => esc_html__( 'Top', 'claue' ),
                    'middle' => esc_html__( 'Middle', 'claue' ),
                    'bottom' => esc_html__( 'Bottom', 'claue' ),
                ),
                'default' => 'bottom',
            )
        );

        $this->add_control(
            'h_align',
            array(
                'label' => esc_html__( 'Text horizontal align', 'claue' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'left' => esc_html__( 'Left', 'claue' ),
                    'center' => esc_html__( 'Center', 'claue' ),
                    'right' => esc_html__( 'Right', 'claue' ),
                ),
                'default' => 'center',
            )
        );

        ///////////////////////
        
        $this->add_control(
			'css_animation',
			array(
				'label' => esc_html__( 'CSS Animation', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
                    'top-to-bottom' => esc_html__( 'Top to bottom', 'claue' ),
					'bottom-to-top' => esc_html__( 'Bottom to top', 'claue' ),
					'left-to-right' => esc_html__( 'Left to right', 'claue' ),
					'right-to-left' => esc_html__( 'Right to left', 'claue' ),
					'appear' => esc_html__( 'Appear from center', 'claue' ),
				),
				'default' => 'top-to-bottom',
			)
		);
        
        $this->add_control(
			'class',
			array(
				'label' => esc_html__( 'Extra class name', 'claue' ),
                'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'claue' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'placeholder' => '',
			)
		);

        //////////////////////////////

        $this->add_control(
            'text_1',
            array(
                'label' => esc_html__( 'Text 1', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
                'separator' => 'before',
            )
        );

        $this->add_control(
            'text_1_font_size',
            array(
                'label' => esc_html__( 'Font size 1', 'claue' ),
                'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_1_line_height',
            array(
                'label' => esc_html__( 'Line height 1', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_1_color',
            array(
                'label' => esc_html__( 'Color 1', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::COLOR,
            )
        );

        $this->add_control(
            'text_1_margin',
            array(
                'label' => esc_html__( 'Margin bottom 1', 'claue' ),
                'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        ////////////////////

        $this->add_control(
            'text_2',
            array(
                'label' => esc_html__( 'Text 2', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
                'separator' => 'before',
            )
        );

        $this->add_control(
            'text_2_font_size',
            array(
                'label' => esc_html__( 'Font size 2', 'claue' ),
                'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_2_line_height',
            array(
                'label' => esc_html__( 'Line height 2', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_2_color',
            array(
                'label' => esc_html__( 'Color 2', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::COLOR,
            )
        );

        $this->add_control(
            'text_2_margin',
            array(
                'label' => esc_html__( 'Margin bottom 2', 'claue' ),
                'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        //////////////////

        $this->add_control(
            'text_3',
            array(
                'label' => esc_html__( 'Text 3', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
                'separator' => 'before',
            )
        );

        $this->add_control(
            'text_3_font_size',
            array(
                'label' => esc_html__( 'Font size 3', 'claue' ),
                'description' => esc_html__( 'Defined in pixels. Do not add the \'px\' unit.', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_3_line_height',
            array(
                'label' => esc_html__( 'Line height 3', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
            )
        );

        $this->add_control(
            'text_3_color',
            array(
                'label' => esc_html__( 'Color 3', 'claue' ),
                'description' => '',
                'type' => \Elementor\Controls_Manager::COLOR,
            )
        );

        $this->add_control(
            'text_3_button',
            array(
                'label' => esc_html__( 'Make it as button', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'separator' => 'after',
                'default' => '',
            )
        );

		$this->end_controls_section();

	}

	/**
     * Create shortcode row
	 * 
	 * @return string
	 */
	public function create_shortcode() {

		$settings = $this->get_settings_for_display();
        
        $args_row = '';
        
        $args_row .= !empty($settings['image']['id']) ? ' image_id="'.esc_attr($settings['image']['id']).'"' : '';
        
        $args_row .= $settings['link']['url'] ? ' link_url="'.esc_url($settings['link']['url']).'"' : '';
        
        $args_row .= $settings['link']['is_external'] ? ' link_target="_blank"' : '';
        
        $args_row .= $settings['link']['nofollow'] ? ' link_rel="nofollow"' : '';
        
        //////////////////
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';

        /////////////////////
        $args_row .= $settings['v_align'] ? ' v_align="'.esc_attr($settings['v_align']).'"' : '';
        $args_row .= $settings['h_align'] ? ' h_align="'.esc_attr($settings['h_align']).'"' : '';

        $args_row .= $settings['text_1'] ? ' text_1="'.esc_html($settings['text_1']).'"' : '';
        $args_row .= $settings['text_1_font_size'] ? ' text_1_font_size="'.esc_html($settings['text_1_font_size']).'"' : '';
        $args_row .= $settings['text_1_line_height'] ? ' text_1_line_height="'.esc_html($settings['text_1_line_height']).'"' : '';
        $args_row .= $settings['text_1_color'] ? ' text_1_color="'.esc_html($settings['text_1_color']).'"' : '';
        $args_row .= $settings['text_1_margin'] ? ' text_1_margin="'.esc_html($settings['text_1_margin']).'"' : '';

        $args_row .= $settings['text_2'] ? ' text_2="'.esc_html($settings['text_2']).'"' : '';
        $args_row .= $settings['text_2_font_size'] ? ' text_2_font_size="'.esc_html($settings['text_2_font_size']).'"' : '';
        $args_row .= $settings['text_2_line_height'] ? ' text_2_line_height="'.esc_html($settings['text_2_line_height']).'"' : '';
        $args_row .= $settings['text_2_color'] ? ' text_2_color="'.esc_html($settings['text_2_color']).'"' : '';
        $args_row .= $settings['text_2_margin'] ? ' text_2_margin="'.esc_html($settings['text_2_margin']).'"' : '';

        $args_row .= $settings['text_3'] ? ' text_3="'.esc_html($settings['text_3']).'"' : '';
        $args_row .= $settings['text_3_font_size'] ? ' text_3_font_size="'.esc_html($settings['text_3_font_size']).'"' : '';
        $args_row .= $settings['text_3_line_height'] ? ' text_3_line_height="'.esc_html($settings['text_3_line_height']).'"' : '';
        $args_row .= $settings['text_3_color'] ? ' text_3_color="'.esc_html($settings['text_3_color']).'"' : '';
        $args_row .= $settings['text_3_button'] == 'yes' ? ' text_3_button="true"' : '';
        
        return '[claue_addons_promotion'.$args_row.']';

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 */
	protected function render() {

		echo do_shortcode( $this->create_shortcode() );
        //[vc_row css=".vc_custom_1460184023435{margin-top: 90px !important;margin-bottom: 90px !important;}"][vc_column][claue_addons_portfolio limit="9" css_animation="fadeInUp" filter="true"][/vc_column][/vc_row]

	}
    
    /**
	 * Render widget as plain content.
	 *
	 * Override the default behavior by printing the shortcode instead of rendering it.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	 public function render_plain_content() {
	   
		// In plain mode, render without shortcode
		echo wp_kses_post( $this->create_shortcode() );
        
	 }

}
/////////////////////
