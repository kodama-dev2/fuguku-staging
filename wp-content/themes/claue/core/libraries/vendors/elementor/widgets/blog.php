<?php
/**
 * Add widget blog to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Blog_Widget extends \Elementor\Widget_Base {

    /**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'claue-blog';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Blog', 'claue' );
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
		return array( 'blog', 'post' );
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
			'claue_addons_blog',
			array(
				'label' => __( 'Content', 'claue' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

        $this->add_control(
            'post_id',
            array(
                'label' => esc_html__( 'Include special posts', 'claue' ),
                'description' => esc_html__( 'Enter posts title to display only those records.', 'claue' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $product_titles,
            )
        );

        $this->add_control(
			'style',
			array(
				'label' => esc_html__( 'Style', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'outside' => esc_html__( 'Content Outside Thumbnail', 'claue' ),
					'inside' => esc_html__( 'Content Inside Thumbnail', 'claue' ),
				),
				'default' => 'outside',
			)
		);

		$this->add_control(
			'thumb_size',
			array(
				'label' => esc_html__( 'Thumbnail size', 'claue' ),
                'description' => esc_html__( 'width x height, example: 570x320', 'claue' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'placeholder' => '',
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
                'default' => '4',
            )
        );

        $this->add_control(
            'limit',
            array(
                'label' => esc_html__( 'Per Page', 'claue' ),
                'description' => esc_html__( 'How much items per page to show (-1 to show all products)', 'claue' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '',
                'default' => '3',
            )
        );

        ///////////////////

        $this->add_control(
            'slider',
            array(
                'label' => esc_html__( 'Enable Slider', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'separator' => 'before',
                'default' => '',
            )
        );

        $this->add_control(
            'autoplay',
            array(
                'label' => esc_html__( 'Enable Auto play', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'default' => '',
            )
        );

        $this->add_control(
            'arrows',
            array(
                'label' => esc_html__( 'Enable Navigation', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'default' => '',
            )
        );

        $this->add_control(
            'dots',
            array(
                'label' => esc_html__( 'Enable Pagination', 'claue' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'claue' ),
                'label_on' => esc_html__( 'Yes', 'claue' ),
                'separator' => 'after',
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
        
        $args_row .= $settings['slider'] == 'yes' ? ' slider="true"' : '';
        $args_row .= $settings['autoplay'] == 'yes' ? ' autoplay="true"' : '';
        $args_row .= $settings['arrows'] == 'yes' ? ' arrows="true"' : '';
        $args_row .= $settings['dots'] == 'yes' ? ' dots="true"' : '';
        
        $args_row .= $settings['style'] ? ' style="'.esc_attr($settings['style']).'"' : '';
        $args_row .= !empty($settings['post_id']) ? ' id="'.esc_attr( implode(',', $settings['post_id']) ).'"' : '';
        
        ///////////////////////
        
        $args_row .= $settings['columns'] ? ' columns="'.esc_attr($settings['columns']).'"' : '';

        $args_row .= $settings['thumb_size'] ? ' thumb_size="'.esc_attr($settings['thumb_size']).'"' : '';
        
        $args_row .= absint($settings['limit']) ? ' limit="'.intval($settings['limit']).'"' : '';
        
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';
        
       // error_log('$settings: '.print_r($settings, 1));
        
        return '[claue_addons_blog'.$args_row.']';

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 */
	protected function render() {

		echo do_shortcode( $this->create_shortcode() );

        if ( is_admin() ){
            echo "
         <script>
          if ( jQuery( '.jas-carousel' ).length > 0 ){
            jQuery( '.jas-carousel' ).not('.slick-initialized').slick({focusOnSelect: true});
          }
         </script>";
        }

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
