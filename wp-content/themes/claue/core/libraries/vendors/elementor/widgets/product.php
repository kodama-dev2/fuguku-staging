<?php
/**
 * Add widget product to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Product_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'claue-product';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product', 'claue' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-archive-posts';
	}
    
    /**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'products', 'product', 'woocommerce' );
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
	   
       $product_titles = array();
       
       $products = get_posts( array(
            'post_type'              => 'product',
			'posts_per_page'         => -1,
			'post_status'            => 'publish',
			'cache_results'          => false,
			'orderby'                => 'title',
			'order'                  => 'ASC',
       ) );
       if ( !empty($products) ){
        foreach($products as $product){
            $product_titles[$product->ID] = get_the_title($product->ID);
        }
       }
       
       /////////////////////

	    $this->start_controls_section(
			'claue_addons_product',
			array(
				'label' => __( 'Content', 'claue' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
		
        $this->add_control(
			'post_id',
			array(
				'label' => esc_html__( 'Product', 'claue' ),
                'description' => esc_html__( 'Input product title to see suggestions', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => false,
				'options' => $product_titles,
			)
		);

        $this->add_control(
			'countdown',
			array(
				'label' => esc_html__( 'Enable countdown for sale product', 'claue' ),
                'description' => esc_html__( 'Setup sale schedule in product page first. Only work with product type simple', 'claue' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'claue' ),
				'label_on' => esc_html__( 'Yes', 'claue' ),
                'default' => '',
			)
		);

        $this->add_control(
            'flip',
            array(
                'label' => esc_html__( 'Enable Flip Product Thumbnail', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'default' => '',
            )
        );

        ///////////////////
        
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

        $args_row .= $settings['countdown'] == 'yes' ? ' countdown="true"' : '';
        $args_row .= $settings['flip'] == 'yes' ? ' flip="true"' : '';

        $args_row .= !empty($settings['post_id']) ? ' id="'.esc_attr( $settings['post_id'] ).'"' : '';
        
        ///////////////////////
        
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';
        
       // error_log('$settings: '.print_r($settings, 1));
        
        return '[claue_addons_product'.$args_row.']';

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 */
	protected function render() {

		echo do_shortcode( $this->create_shortcode() );
        
        return;

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
