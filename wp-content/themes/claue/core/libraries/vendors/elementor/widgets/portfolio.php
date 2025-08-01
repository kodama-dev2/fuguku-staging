<?php
/**
 * Add widget portfolio to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Portfolio_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'portfolio';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Portfolio', 'claue' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}
    
    /**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'portfolio' );
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
	   
       $portfolio_cat = array(
		  'all' => esc_html__( 'All category', 'claue' ),
       );
       
       // Get all terms of portfolio
       $terms = get_terms( 'portfolio_cat' );
       if ( $terms && ! isset( $terms->errors ) ) {
		foreach ( $terms as $key => $value ) {
			$portfolio_cat[$value->term_id] = $value->name;
		}
       }

	    $this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Content', 'claue' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
        
        $this->add_control(
			'columns',
			array(
				'label' => esc_html__( 'Columns', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'6' => esc_html__( '2 Columns', 'claue' ),
					'4' => esc_html__( '3 Columns', 'claue' ),
					'3' => esc_html__( '4 Columns', 'claue' ),
				),
				'default' => '6',
			)
		);

		$this->add_control(
			'limit',
			array(
				'label' => esc_html__( 'Per Page', 'claue' ),
                'description' => esc_html__( 'How much items per page to show (-1 to show all portfolio)', 'claue' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'placeholder' => '',
                'default' => '10',
			)
		);
        
        $this->add_control(
			'cat',
			array(
				'label' => esc_html__( 'Category', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $portfolio_cat,
                'default' => 'all',
			)
		);
        
        $this->add_control(
			'filter',
			array(
				'label' => esc_html__( 'Enable Filter?', 'claue' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Off', 'claue' ),
				'label_on' => esc_html__( 'On', 'claue' ),
				'separator' => 'before',
                'default' => 'yes',
			)
		);
        
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
        
        $args_row .= $settings['columns'] ? ' columns="'.esc_attr($settings['columns']).'"' : '';
        
        $args_row .= $settings['cat'] ? ' cat="'.esc_attr($settings['cat']).'"' : '';
        
        $args_row .= absint($settings['limit']) ? ' limit="'.intval($settings['limit']).'"' : '';
        
        $args_row .= $settings['filter'] == 'yes' ? ' filter="true"' : '';
        
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';
        
        return '[claue_addons_portfolio'.$args_row.']';

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
