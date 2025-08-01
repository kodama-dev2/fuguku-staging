<?php
/**
 * Action hooks.
 *
 * @since   1.0.0
 * @package Claue
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_setup' ) ) {
	function jas_claue_setup() {
		/**
		 * Set the content width in pixels, based on the theme's design and stylesheet.
		 *
		 * @since 1.0.0
		 */
		$GLOBALS['content_width'] = apply_filters( 'claue_content_width', 1170 );

		/**
		 * Make theme available for translation.
		 * Translations can be filed in the /language/ directory.
		 *
		 * @since 1.0.0
		 */
		load_theme_textdomain( 'claue', JAS_CLAUE_PATH . '/core/libraries/janstudio/language' );

		/**
		 * Add theme support.
		 *
		 * @since 1.0.0
		 */
		add_theme_support( 'title-tag' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'custom-header' );
		add_theme_support( 'custom-background' );

		/**
		 * Register theme location.
		 *
		 * @since 1.0.0
		 */
		register_nav_menus(
			array(
				'primary-menu' => esc_html__( 'Primary Menu', 'claue' ),
				'left-menu'    => esc_html__( 'Left Menu', 'claue' ),
				'right-menu'   => esc_html__( 'Right Menu', 'claue' ),
				'footer-menu'  => esc_html__( 'Footer Menu', 'claue' ),
			)
		);

		// Tell TinyMCE editor to use a custom stylesheet.
		add_editor_style( JAS_CLAUE_URL . '/assets/css/editor-style.css' );
	}
}
add_action( 'after_setup_theme', 'jas_claue_setup' );

/**
 * Register widget area.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_register_sidebars' ) ) {
	function jas_claue_register_sidebars() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Primary Sidebar', 'claue' ),
				'id'            => 'primary-sidebar',
				'description'   => esc_html__( 'The Primary Sidebar', 'claue' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title fwm">',
				'after_title'   => '</h4>',
			)
		);
		for ( $i = 1, $n = 5; $i <= $n; $i++ ) {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Footer Area #', 'claue' ) . $i,
					'id'            => 'footer-' . $i,
					'description'   => sprintf( esc_html__( 'The #%s column in footer area', 'claue' ), $i ),
					'before_widget' => '<aside id="%1$s" class="widget %2$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h3 class="widget-title fwsb fs__16 mg__0 mb__30">',
					'after_title'   => '</h3>',
				)
			);
		}
	}
}
add_action( 'widgets_init', 'jas_claue_register_sidebars' );


/**
 * Add Menu Page Link.
 *
 * @return void
 * @since  1.0.0
 */
if ( ! function_exists( 'jas_claue_add_framework_menu' ) ) {
	function jas_claue_add_framework_menu() {
		$menu = 'add_menu_' . 'page';
		$menu(
			'jas_panel',
			esc_html__( 'Claue', 'claue' ),
			'',
			'jas',
			NULL,
			JAS_CLAUE_URL . '/core/admin/assets/images/admin-icon.svg',
			99
		);
	}
}
add_action( 'admin_menu', 'jas_claue_add_framework_menu' );

/**
 * Enqueue scripts and styles.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_enqueue_scripts' ) ) {
	function jas_claue_enqueue_scripts() {

		// Google font
		if ( cs_get_option( 'enable-google-font' ) ){		
			wp_enqueue_style( 'jas-font-google', jas_claue_google_font_url() );
		}

		// Font Awesome
		wp_dequeue_style( 'font-awesome' );
		wp_enqueue_style( 'fontawesome', JAS_CLAUE_URL . '/assets/vendors/font-awesome/css/font-awesome.min.css', [], JAS_CLAUE_VERSION );

		// Font Stroke
		wp_enqueue_style( 'font-stroke', JAS_CLAUE_URL . '/assets/vendors/font-stroke/css/font-stroke.min.css', [], JAS_CLAUE_VERSION );

		// Slick Carousel
		wp_enqueue_style( 'slick', JAS_CLAUE_URL . '/assets/vendors/slick/slick.css', [], JAS_CLAUE_VERSION );
		wp_enqueue_script( 'slick', JAS_CLAUE_URL . '/assets/vendors/slick/slick.min.js', [], JAS_CLAUE_VERSION, true );

		// Magnific Popup
		wp_enqueue_script( 'magnific-popup', JAS_CLAUE_URL . '/assets/vendors/magnific-popup/jquery.magnific-popup.min.js', [], JAS_CLAUE_VERSION, true );

		// Isotope
		wp_enqueue_script( 'isotope', JAS_CLAUE_URL . '/assets/vendors/isotope/isotope.pkgd.min.js', [], JAS_CLAUE_VERSION, true );

		// Scroll Reveal
		wp_enqueue_script( 'scrollreveal', JAS_CLAUE_URL . '/assets/vendors/scrollreveal/scrollreveal.min.js', [], JAS_CLAUE_VERSION, true );

		// jQuery Countdown
		wp_enqueue_script( 'countdown', JAS_CLAUE_URL . '/assets/vendors/jquery-countdown/jquery.countdown.min.js', [], JAS_CLAUE_VERSION, true );

		// Enqueue script on single product
		if ( ( function_exists( 'is_product' ) && is_product() ) || (function_exists( 'is_product' ) && is_product_category()) || (function_exists( 'is_product' ) && is_shop() ) ) {
			wp_enqueue_script( 'sticky-kit', JAS_CLAUE_URL . '/assets/vendors/jquery-sticky-kit/sticky-kit.min.js', [], JAS_CLAUE_VERSION, true );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			// Zoom image
			if ( is_singular( 'product' ) && cs_get_option( 'wc-single-zoom' ) && !wp_is_mobile() ) {
				wp_enqueue_script( 'zoom' );
			}
		}

		// Main scripts
		wp_enqueue_script( 'jas-claue-script', JAS_CLAUE_URL . '/assets/js/theme.js', array( 'jquery', 'imagesloaded' ), JAS_CLAUE_VERSION, true );

		// Inline script
		wp_add_inline_script( 'jas-claue-script', jas_claue_custom_js() );

		// Custom localize script
		wp_localize_script( 'jas-claue-script', 'JAS_Data_Js', jas_claue_custom_data_js() );

		// Responsive stylesheet
		wp_enqueue_style( 'jas-claue-animated', JAS_CLAUE_URL . '/assets/css/animate.css', [], JAS_CLAUE_VERSION);

		// Main stylesheet
		wp_enqueue_style( 'jas-claue-style', get_stylesheet_uri(), [], JAS_CLAUE_VERSION );

		// RTL stylesheet
		if ( is_rtl() ) {
            wp_enqueue_style('jas-claue-rtl', JAS_CLAUE_URL . '/assets/css/rtl.css', [], JAS_CLAUE_VERSION);
        }

		// Inline stylesheet
		wp_add_inline_style( 'jas-claue-style', jas_claue_custom_css() );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		do_action( 'claue_scripts');
	}
}
add_action( 'wp_enqueue_scripts', 'jas_claue_enqueue_scripts', 10 );

/**
 * Dequeue style of some plugins that load same file with theme
 *
 * @since 1.5.8
 */
if ( ! function_exists( 'jas_dequeue_style' ) ) {
	function jas_dequeue_style() {
	    wp_dequeue_style( 'dokan-fontawesome' );
	    wp_deregister_style( 'dokan-fontawesome' );
	}
	add_action( 'wp_print_styles', 'jas_dequeue_style' );
}

/**
 * Redirect to under construction page
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_offline' ) ) {
	function jas_claue_offline() {
		// Check if under construction page is enabled
		if ( cs_get_option( 'maintenance' ) ) {
			if ( ! is_feed() ) {
				// Check if user is not logged in
				if ( ! is_user_logged_in() ) {
					// Load under construction page
					include JAS_CLAUE_PATH . '/views/pages/offline.php';
					exit;
				}
			}

			// Check if user is logged in
			if ( is_user_logged_in() ) {
				global $current_user;

				// Get user role
				wp_get_current_user();

				$loggedInUserID = $current_user->ID;
				$userData = get_userdata( $loggedInUserID );

				// If user role is not 'administrator' then redirect to under construction page
				if ( 'administrator' != $userData->roles[0] ) {
					if ( ! is_feed() ) {
						include JAS_CLAUE_PATH . '/views/pages/offline.php';
						exit;
					}
				}
			}
		}
	}
}
add_action( 'template_redirect', 'jas_claue_offline' );

/**
 * Custom social share iamge
 *
 * @since 1.1.3
 */
if ( ! function_exists( 'jas_claue_social_meta' ) && ! function_exists( 'wpseo_activate' ) && ! class_exists('RankMath') )  {
	function jas_claue_social_meta() {
		global $post;
        global $allowedtags;
        
        $alltags = (array)$allowedtags + array(
            'meta' => array(
                'itemprop' => array(),
                'content' => array(),
                'name' => array(),
                'property' => array(),
            ),
        );
        
		if ($post) {
			$output = '';
			$image_src_array = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full', true );

			$output .= '<meta itemprop="name" content="' . esc_attr( strip_tags( get_the_title() ) ) . '">';
			$output .= '<meta itemprop="description" content="' . esc_attr( strip_tags( $post->the_excerpt ) ) . '">';
			$output .= '<meta itemprop="image" content="' . esc_url( $image_src_array[0] ) . '">';

			$output .= '<meta name="twitter:card" content="summary_large_image">';
			$output .= '<meta name="twitter:site" content="@' . str_replace( ' ', '', get_bloginfo( 'name' ) ) . '">';
			$output .= '<meta name="twitter:title" content="' . esc_attr( strip_tags( get_the_title() ) ) . '">';
			$output .= '<meta name="twitter:description" content="' . esc_attr( strip_tags( $post->the_excerpt ) ) . '">';
			$output .= '<meta name="twitter:creator" content="@' . str_replace( ' ', '', get_bloginfo( 'name' ) ) . '">';
			$output .= '<meta name="twitter:image:src" content="' . esc_url( $image_src_array[0] ) . '">';

			$output .= '<meta property="og:title" content="' . esc_attr( strip_tags( get_the_title() ) ) . '" />';
			$output .= '<meta property="og:url" content="' . esc_url( get_permalink() ) . '" />';
			$output .= '<meta property="og:image" content="' . esc_url( $image_src_array[0] ) . '" />';
			$output .= '<meta property="og:image:url" content="' . $image_src_array[ 0 ] . '"/>'. "\n";
			$output .= '<meta property="og:description" content="' . esc_attr( strip_tags( $post->the_excerpt ) ) . '" />';
			$output .= '<meta property="og:site_name" content="' . get_bloginfo( 'name') . '" />';

			if ( function_exists( 'is_product' ) && is_product() ) {
				$output .= '<meta property="og:type" content="product"/>'. "\n";
			} else {
				$output .= '<meta property="og:type" content="article"/>'. "\n";
			}
	 
			echo force_balance_tags( wp_kses($output, $alltags) );
		}
	}
	add_action( 'wp_head', 'jas_claue_social_meta', 0 );
}