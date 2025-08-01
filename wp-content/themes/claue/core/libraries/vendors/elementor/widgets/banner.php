<?php
/**
 * Add widget banner to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Banner_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'banner';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Banner', 'claue' );
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
		return array( 'banner' );
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
			'text',
			array(
				'label' => esc_html__( 'Text', 'claue' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 10,
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
        
        $args_row .= $settings['text'] ? ' text="'.esc_html($settings['text']).'"' : '';
        
        $args_row .= $settings['link']['url'] ? ' link_url="'.esc_url($settings['link']['url']).'"' : '';
        
        $args_row .= $settings['link']['is_external'] ? ' link_target="_blank"' : '';
        
        $args_row .= $settings['link']['nofollow'] ? ' link_rel="nofollow"' : '';
        
        //////////////////
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';
        
        return '[claue_addons_banner'.$args_row.']';

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
