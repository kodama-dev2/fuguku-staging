<?php
/**
 * Add widget products to elementor
 *
 * @since   1.6.2
 * @package Claue
 */
 
class Claue_Elementor_Products_Widget extends \Elementor\Widget_Base {
    
    public function __construct($data = [], $args = null) {
       parent::__construct($data, $args);

      // wp_register_script( 'claue-widget-products-script', JAS_CLAUE_URL . '/core/libraries/vendors/elementor/assets/init_slick.js', [ 'elementor-frontend', 'jquery' ], '1.0.0', true );
    }

    public function get_script_depends() {
      return [ 'claue-widget-products-script' ];
    }

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'claue-products';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Products', 'claue' );
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
	   
       // Get all terms of woocommerce
       $product_cat = array();
       $terms = get_terms( 'product_cat' );
       if ( $terms && ! isset( $terms->errors ) ) {
		foreach ( $terms as $key => $value ) {
			$product_cat[$value->term_id] = $value->name;
		}
       }
       
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
			'claue_addons_products',
			array(
				'label' => __( 'Content', 'claue' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);
        
        $this->add_control(
			'style',
			array(
				'label' => esc_html__( 'List product style', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'grid' => esc_html__( 'Grid', 'claue' ),
					'masonry' => esc_html__( 'Masonry', 'claue' ),
					'metro' => esc_html__( 'Metro', 'claue' ),
				),
				'default' => 'grid',
			)
		);
        
        $this->add_control(
			'display',
			array(
				'label' => esc_html__( 'Display', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'all' => esc_html__( 'All products', 'claue' ),
					'recent' => esc_html__( 'Recent products', 'claue' ),
                    'featured' => esc_html__( 'Featured products', 'claue' ),
                    'sale' => esc_html__( 'Sale products', 'claue' ),
                    'best_selling_products' => esc_html__( 'Best selling products', 'claue' ),
                    'rated' => esc_html__( 'Top Rated Products', 'claue' ),
                    'cat' => esc_html__( 'Products by category', 'claue' ),
				),
				'default' => 'all',
			)
		);
        
        $this->add_control(
			'orderby',
			array(
				'label' => esc_html__( 'Order By', 'claue' ),
                'description' => sprintf( wp_kses_post( 'Select how to sort retrieved products. More at %s. Default by Title', 'claue' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'title' => esc_html__( 'Title', 'claue' ),
					'date' => esc_html__( 'Date', 'claue' ),
                    'ID' => esc_html__( 'ID', 'claue' ),
                    'author' => esc_html__( 'Author', 'claue' ),
                    'modified' => esc_html__( 'Modified', 'claue' ),
                    'rand' => esc_html__( 'Random', 'claue' ),
                    'comment_count' => esc_html__( 'Comment count', 'claue' ),
                    'menu_order' => esc_html__( 'Menu order', 'claue' ),
				),
				'default' => 'title',
			)
		);
        
        $this->add_control(
			'order',
			array(
				'label' => esc_html__( 'Order', 'claue' ),
                'description' => sprintf( __( 'Designates the ascending or descending order. More at %s. Default by ASC', 'claue' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'ASC' => esc_html__( 'Ascending', 'claue' ),
					'DESC' => esc_html__( 'Descending', 'claue' ),
				),
				'default' => 'ASC',
			)
		);
		
        $this->add_control(
			'post_id',
			array(
				'label' => esc_html__( 'Products', 'claue' ),
                'description' => esc_html__( 'Input product title to see suggestions', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
				'options' => $product_titles,
			)
		);
        
        
        $this->add_control(
			'cat_id',
			array(
				'label' => esc_html__( 'Product Category', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $product_cat,
                'default' => 'all',
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
                'default' => '12',
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
			'items',
			array(
				'label' => esc_html__( 'Items (Number only)', 'claue' ),
                'description' => esc_html__( 'Set the maximum amount of items displayed at a time with the widest browser width', 'claue' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 4,
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
			'columns',
			array(
				'label' => esc_html__( 'Columns', 'claue' ),
				'description' => esc_html__( 'This parameter will not working if slider has enabled', 'claue' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'6' => esc_html__( '2 Columns', 'claue' ),
					'4' => esc_html__( '3 Columns', 'claue' ),
					'3' => esc_html__( '4 Columns', 'claue' ),
                    '2' => esc_html__( '6 Columns', 'claue' ),
				),
				'default' => '3',
			)
		);		
        
        $this->add_control(
			'filter',
			array(
				'label' => esc_html__( 'Enable Isotope Category Filter', 'claue' ),
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
        $args_row .= $settings['flip'] == 'yes' ? ' flip="true"' : '';
        
        $args_row .= $settings['style'] ? ' style="'.esc_attr($settings['style']).'"' : '';
        $args_row .= !empty($settings['post_id']) ? ' id="'.esc_attr( implode(',', $settings['post_id']) ).'"' : '';
        $args_row .= $settings['display'] ? ' display="'.esc_attr($settings['display']).'"' : '';
        $args_row .= $settings['orderby'] ? ' orderby="'.esc_attr($settings['orderby']).'"' : '';
        $args_row .= $settings['order'] ? ' order="'.esc_attr($settings['order']).'"' : '';
        $args_row .= $settings['items'] ? ' items="'.absint($settings['items']).'"' : '';
        
        ///////////////////////
        
        $args_row .= $settings['columns'] ? ' columns="'.esc_attr($settings['columns']).'"' : '';
        
        $args_row .= $settings['cat_id'] ? ' cat_id="'.esc_attr($settings['cat_id']).'"' : '';
        
        $args_row .= absint($settings['limit']) ? ' limit="'.intval($settings['limit']).'"' : '';
        
        $args_row .= $settings['filter'] == 'yes' ? ' filter="true"' : ' filter="false"';
        
        $args_row .= $settings['css_animation'] ? ' css_animation="'.esc_attr($settings['css_animation']).'"' : '';
        
        $args_row .= $settings['class'] ? ' class="'.esc_attr($settings['class']).'"' : '';
        
       // error_log('$settings: '.print_r($settings, 1));
        
        return '[claue_addons_products'.$args_row.']';

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
