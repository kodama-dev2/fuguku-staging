<?php
/**
 * Portfolio custom post type.
 *
 * @package ClaueAddons
 * @since   1.0.0
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class Claue_Addons_Portfolio {
	/**
	 * Construct function.
	 *
	 * @return  void
	 */
	function __construct() {
		add_action( 'init', array( __CLASS__, 'portfolio_init' ) );
		add_filter( 'single_template', array( $this, 'portfolio_single' ) );	
		add_filter( 'archive_template', array( $this, 'portfolio_archive' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
	}

	/**
	 * Register a portfolio post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function portfolio_init() {
		register_post_type( 'portfolio',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'portfolio' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 99,
				'menu_icon'          => 'dashicons-welcome-widgets-menus',
				'supports'           => array( 'title', 'editor', 'thumbnail' ),
				'labels'             => array(
					'name'               => _x( 'Portfolio', 'claue-addons' ),
					'singular_name'      => _x( 'Portfolio', 'claue-addons' ),
					'menu_name'          => _x( 'Portfolio', 'claue-addons' ),
					'name_admin_bar'     => _x( 'Portfolio', 'claue-addons' ),
					'add_new'            => _x( 'Add New', 'claue-addons' ),
					'add_new_item'       => __( 'Add New Portfolio', 'claue-addons' ),
					'new_item'           => __( 'New Portfolio', 'claue-addons' ),
					'edit_item'          => __( 'Edit Portfolio', 'claue-addons' ),
					'view_item'          => __( 'View Portfolio', 'claue-addons' ),
					'all_items'          => __( 'All Portfolios', 'claue-addons' ),
					'search_items'       => __( 'Search Portfolios', 'claue-addons' ),
					'parent_item_colon'  => __( 'Parent Portfolios:', 'claue-addons' ),
					'not_found'          => __( 'No portfolios found.', 'claue-addons' ),
					'not_found_in_trash' => __( 'No portfolios found in Trash.', 'claue-addons' )
				),
			)
		);

		// Register portfolio category
		register_taxonomy( 'portfolio_cat',
			array( 'portfolio' ),
			array(
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'portfolio_cat' ),
				'labels'            => array(
					'name'              => _x( 'Categories', 'claue-addons' ),
					'singular_name'     => _x( 'Category', 'claue-addons' ),
					'search_items'      => __( 'Search Categories', 'claue-addons' ),
					'all_items'         => __( 'All Categories', 'claue-addons' ),
					'parent_item'       => __( 'Parent Category', 'claue-addons' ),
					'parent_item_colon' => __( 'Parent Category:', 'claue-addons' ),
					'edit_item'         => __( 'Edit Category', 'claue-addons' ),
					'update_item'       => __( 'Update Category', 'claue-addons' ),
					'add_new_item'      => __( 'Add New Category', 'claue-addons' ),
					'new_item_name'     => __( 'New Category Name', 'claue-addons' ),
					'menu_name'         => __( 'Categories', 'claue-addons' ),
				),
			)
		);

		// Register portfolio project client
		register_taxonomy( 'portfolio_client',
			'portfolio',
			array(
				'hierarchical'          => true,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'portfolio_client' ),
				'labels'                => array(
					'name'                       => _x( 'Clients', 'claue-addons' ),
					'singular_name'              => _x( 'Client', 'claue-addons' ),
					'search_items'               => __( 'Search Clients', 'claue-addons' ),
					'all_items'                  => __( 'All Clients', 'claue-addons' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __( 'Edit Client', 'claue-addons' ),
					'update_item'                => __( 'Update Client', 'claue-addons' ),
					'add_new_item'               => __( 'Add New Client', 'claue-addons' ),
					'new_item_name'              => __( 'New Client Name', 'claue-addons' ),
					'separate_items_with_commas' => __( 'Separate client with commas', 'claue-addons' ),
					'add_or_remove_items'        => __( 'Add or remove client', 'claue-addons' ),
					'choose_from_most_used'      => __( 'Choose from the most used client', 'claue-addons' ),
					'not_found'                  => __( 'No client found.', 'claue-addons' ),
					'menu_name'                  => __( 'Clients', 'claue-addons' ),
				),
			)
		);

		// Register portfolio tag
		register_taxonomy( 'portfolio_tag',
			'portfolio',
			array(
				'hierarchical'          => true,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'portfolio_tag' ),
				'labels'                => array(
					'name'                       => _x( 'Tags', 'claue-addons' ),
					'singular_name'              => _x( 'Tag', 'claue-addons' ),
					'search_items'               => __( 'Search Tags', 'claue-addons' ),
					'popular_items'              => __( 'Popular Tags', 'claue-addons' ),
					'all_items'                  => __( 'All Tags', 'claue-addons' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __( 'Edit Tag', 'claue-addons' ),
					'update_item'                => __( 'Update Tag', 'claue-addons' ),
					'add_new_item'               => __( 'Add New Tag', 'claue-addons' ),
					'new_item_name'              => __( 'New Tag Name', 'claue-addons' ),
					'separate_items_with_commas' => __( 'Separate tag with commas', 'claue-addons' ),
					'add_or_remove_items'        => __( 'Add or remove tag', 'claue-addons' ),
					'choose_from_most_used'      => __( 'Choose from the most used tag', 'claue-addons' ),
					'not_found'                  => __( 'No tag found.', 'claue-addons' ),
					'menu_name'                  => __( 'Tags', 'claue-addons' ),
				),
			)
		);
	}

	/**
	 * Load single item template file for the portfolio custom post type.
	 *
	 * @param   string  $template  Current template file.
	 *
	 * @return  string
	 */
	function portfolio_single( $template ) {
		global $post;

		if ( $post->post_type == 'portfolio' ) {
			$template = CLAUE_ADDONS_PATH . 'includes/portfolio/views/single.php';
		}

		return $template;
	}

	/**
	 * Load archive template file for the portfolio custom post type.
	 *
	 * @param   string  $template  Current template file.
	 *
	 * @return  string
	 */
	function portfolio_archive( $template ) {
		global $post;

		if ( isset( $post->post_type ) && $post->post_type == 'portfolio' ) {
			$template = CLAUE_ADDONS_PATH . 'includes/portfolio/views/archive.php';
		}

		return $template;
	}

	/**
	 * Define helper function to print related portfolio.
	 *
	 * @return  array
	 */
	public static function related() {
		global $post;

		// Get the portfolio tags.
		$tags = get_the_terms( $post, 'portfolio_tag' );

		if ( $tags ) {
			$tag_ids = array();

			foreach ( $tags as $tag ) {
				$tag_ids[] = $tag->term_id;
			}

			$args = array(
				'post_type'      => 'portfolio',
				'post__not_in'   => array( $post->ID ),
				'posts_per_page' => -1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'portfolio_tag',
						'field'    => 'id',
						'terms'    => $tag_ids,
					),
				)
			);

			// Get portfolio category
			$categories = wp_get_post_terms( get_the_ID(), 'portfolio_cat' );

			$the_query = new WP_Query( $args );
			?>
			<div class="jas-container mb__60 related-portfolio">
				<h4 class="mg__0 mb__30 tu tc fwb"><?php echo esc_html__( 'Related Portfolio', 'claue-addons' ); ?></h4>
				<div class="jas-carousel" data-slick='{"slidesToShow": 3,"slidesToScroll": 1,"responsive":[{"breakpoint": 1024,"settings":{"slidesToShow": 2}},{"breakpoint": 480,"settings":{"slidesToShow": 1}}]<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>
					<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<div id="portfolio-<?php the_ID(); ?>" class="portfolio-item pl__10 pr__10 pr">
							<a href="<?php the_permalink(); ?>" class="mask db pr chp">
								<?php
									if ( has_post_thumbnail() ) :
										the_post_thumbnail();
									endif;
								?>
							</a>
							<div class="pa tc ts__03 portfolio-title">
								<h4 class="fs__14 tu mg__0"><a class="cd chp" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
								<?php
									if ( $categories ) {
										echo '<span>' . get_the_term_list( $post->ID, 'portfolio_cat', '', ', ' ) . '</span>';
									}
								?>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
		<?php
		}

		wp_reset_postdata();
	}

	/**
	 * fix paginate not work from page 3
	 *
	 */
	public static function pre_get_posts($query) {
		if ( !is_admin() && $query->is_main_query() ) {
			if ($query->is_tax('portfolio_cat') || $query->is_tax('portfolio_client') || $query->is_tax('portfolio_tag')) {
				$query->set( 'posts_per_page', cs_get_option( 'portfolio-number-per-page' ) );
			}
		}
	}
}
$portfolio = new Claue_Addons_Portfolio;