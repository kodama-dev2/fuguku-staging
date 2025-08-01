<?php
/**
 * Custom template tags.
 *
 * @since   1.0.0
 * @package Claue
 */

/**
 * Render header layout.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_header' ) ) {
	function jas_claue_header() {
		$layout = cs_get_option( 'header-layout' ) ? cs_get_option( 'header-layout' ) : 3;

		ob_start();
		get_template_part( 'views/header/layout', $layout );
		$output = ob_get_clean();

		echo apply_filters( 'jas_claue_header', $output );
	}
}

/**
 * Render footer layout.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_footer' ) ) {
	function jas_claue_footer() {
		$layout = cs_get_option( 'footer-layout' ) ? cs_get_option( 'footer-layout' ) : 1;

		ob_start();
		get_template_part( 'views/footer/layout', $layout );
		$output = ob_get_clean();

		echo apply_filters( 'jas_claue_footer', $output );
	}
}

/**
 * Render logo.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_logo' ) ) {
	function jas_claue_logo() {
		$output = '';

		$output .= '<div class="jas-branding ts__05">';
			$output .= '<a class="db" href="' . esc_url( home_url( '/' ) ) . '">';
				if ( ! cs_get_option( 'header-transparent' ) ) {
					if ( cs_get_option( 'logo' ) ) {
						$logo = wp_get_attachment_image_src( cs_get_option( 'logo' ), 'full', true );

						$output .= '<img class="regular-logo normal-logo" src="' . esc_url( $logo[0] ) . '" width="' . esc_attr( $logo[1] ? $logo[1] : '' ) . '" height="' . esc_attr( $logo[2] ? $logo[2] : '' ) . '" alt="' . get_bloginfo( 'name' ) . '" />';
						if ( cs_get_option( 'logo-sticky' ) ) {
							$logo = wp_get_attachment_image_src( cs_get_option( 'logo-sticky' ), 'full', true );
							$output .= '<img class="sticky-logo" src="' . esc_url( $logo[0] ) . '" width="' . esc_attr( $logo[1] ? $logo[1] : '' ) . '" height="' . esc_attr( $logo[2] ? $logo[2] : '' ) . '" alt="' . get_bloginfo( 'name' ) . '" />';
						}
					} else {
						$output .= '<img class="regular-logo" src="' . JAS_CLAUE_URL . '/assets/images/logo.png' . '" width="96" height="29" alt="' . get_bloginfo( 'name' ) . '" />';
					}

					if ( cs_get_option( 'logo-retina' ) ) {
						$logo_retina = wp_get_attachment_image_src( cs_get_option( 'logo-retina' ), 'full', true );

						$output .= '<img class="retina-logo normal-logo" src="' . esc_url( $logo_retina[0] ) . '" width="' . esc_attr( $logo_retina[1] ? $logo_retina[1] / 2 : '' ) . '" height="' . esc_attr( $logo_retina[2] ? $logo_retina[2] / 2 : '' ) . '" alt="' . get_bloginfo( 'name' ) . '" />';
					} else {
						$output .= '<img class="retina-logo" src="' . JAS_CLAUE_URL . '/assets/images/logo-2x.png' . '" width="96" height="29" alt="' . get_bloginfo( 'name' ) . '" />';
					}
				} else {
					if ( cs_get_option( 'logo-transparent' ) ) {
						$logo = wp_get_attachment_image_src( cs_get_option( 'logo-transparent' ), 'full', true );

						$output .= '<img class="regular-logo normal-logo" src="' . esc_url( $logo[0] ) . '" width="' . esc_attr( $logo[1] ? $logo[1] : '' ) . '" height="' . esc_attr( $logo[2] ? $logo[2] : '' ) . '" alt="' . get_bloginfo( 'name' ) . '" />';
						if ( cs_get_option( 'logo-sticky' ) ) {
							$logo = wp_get_attachment_image_src( cs_get_option( 'logo-sticky' ), 'full', true );
							$output .= '<img class="sticky-logo" src="' . esc_url( $logo[0] ) . '" width="' . esc_attr( $logo[1] ? $logo[1] : '' ) . '" height="' . esc_attr( $logo[2] ? $logo[2] : '' ) . '" alt="' . get_bloginfo( 'name' ) . '" />';
						}
					} else {
						$output .= '<img class="regular-logo" src="' . JAS_CLAUE_URL . '/assets/images/logo.png' . '" width="96" height="29" alt="' . get_bloginfo( 'name' ) . '" />';
					}

					if ( cs_get_option( 'logo-transparent-retina' ) ) {
						$logo_retina = wp_get_attachment_image_src( cs_get_option( 'logo-transparent-retina' ), 'full', true );

						$output .= '<img class="retina-logo normal-logo" src="' . esc_url( $logo_retina[0] ) . '" width="' . esc_attr( $logo_retina[1] ? $logo_retina[1] : '' ) . '/2" height="' . esc_attr( $logo_retina[2] ? $logo_retina[2] : '' ) . '/2" alt="' . get_bloginfo( 'name' ) . '" />';
					} else {
						$output .= '<img class="retina-logo" src="' . JAS_CLAUE_URL . '/assets/images/logo-2x.png' . '" width="96" height="29" alt="' . get_bloginfo( 'name' ) . '" />';
					}
				}
			$output .= '</a>';
		$output .= '</div>';

		echo apply_filters( 'jas_claue_logo', $output );
	}
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_posted_on' ) ) {
	function jas_claue_posted_on() {
		$output = '';
		$time = '<a class="cg" href="%3$s"><time class="entry-date published updated" ' . jas_claue_schema_metadata( array( 'context' => 'entry_time', 'echo' => false ) ) . '>%2$s</time></a>';


		$output .= sprintf( $time,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_permalink() )
		);

		echo apply_filters( 'jas_claue_posted_on', '<span class="posted-on fs__12">' . $output . '</span>' );
	}
}

/**
 * Prints post title.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_post_title' ) ) {
	function jas_claue_post_title( $link = true ) {
		$output = '';

		if ( $link ) {
			$output .= sprintf( '<h2 class="post-title fs__14 ls__2 mt__10 mb__5 tu" ' . jas_claue_schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '><a class="chp" href="%2$s" rel="bookmark">%1$s</a></h2>', get_the_title(), esc_url( get_permalink() ) );
		} else {
			$output .= sprintf( '<h2 class="post-title fs__14 ls__2 tu" ' . jas_claue_schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '>%s</h2>', get_the_title() );

		}

		echo apply_filters( 'jas_claue_post_title', $output );
	}
}

/**
 * Prints post meta with the post author, categories and post comments.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_post_meta' ) ) {
	function jas_claue_post_meta() {
		$output = '';
		// Post author
		$output .= sprintf(
			esc_html__( '%1$s', 'claue' ),
			'<span class="author vcard pr" ' . jas_claue_schema_metadata( array( 'context' => 'author', 'echo' => false ) ) . '>' . esc_html__( 'By ', 'claue' ) . '<a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'

		);

		// Post categories
		$categories = get_the_category_list( esc_html__( ', ', 'claue' ) );
		if ( $categories ) {
			$output .= sprintf(
				'<span class="cat pr">' . esc_html__( 'In %1$s', 'claue' ) . '</span>', $categories 
			);
		}

		// Post comments
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		    $comments_number = get_comments_number();
		    
			if ( '1' === $comments_number ) {
				$output .= '<span class="comment-number pr"><a href="' . get_comments_link() . '">' . sprintf( _x( '1 Comment', 'comments title', 'claue' ), get_the_title() );
				$output .= '</a></span>';
			} else {
				$output .= '<span class="comment-number pr"><a href="' . get_comments_link() . '">' . sprintf(
					_nx(
						'%1$s Comment',
						'%1$s Comments',
						$comments_number,
						'comments title',
						'claue'
					),
					number_format_i18n( $comments_number ),
					get_the_title()
				);
				$output .= '</a></span>';
			}
		}

		echo apply_filters( 'jas_claue_post_meta', '<div class="post-meta fs__12">' . $output . '</div>' );
	}
}

/**
 * Render post tags.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_get_tags' ) ) :
	function jas_claue_get_tags() {
		$output = '';

		// Get the tag list
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'claue' ) );
		if ( $tags_list ) {
			$output .= sprintf( '<div class="post-tags"><i class="fa fa-tags"></i> ' . esc_html__( '%1$s', 'claue' ) . '</div>', $tags_list );
		}
		return apply_filters( 'jas_claue_get_tags', $output );
	}
endif;

/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_post_thumbnail' ) ) {
	function jas_claue_post_thumbnail() {
		?>
			<div class="post-thumbnail pr mb__25">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php esc_url( the_permalink() ); ?>" aria-hidden="true">
						<?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) ); ?>
					</a>
				<?php endif; ?>
				<div class="pa inside-thumb tc cg">
					<?php jas_claue_post_meta(); ?>
					<?php jas_claue_post_title(); ?>
					<?php jas_claue_posted_on(); ?>
				</div>
			</div>
		<?php
	}
}

/**
 * Display navigation to next/previous set of posts when applicable.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_pagination' ) ) {
	function jas_claue_pagination( $nav_query = false ) {

		global $wp_query, $wp_rewrite;

		// Don't print empty markup if there's only one page.
		if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
			return;
		}

		// Prepare variables
		$query        = $nav_query ? $nav_query : $wp_query;
		$max          = $query->max_num_pages;
		$current_page = max( 1, get_query_var( 'paged' ) );
		$big          = 999999;
		?>
		<nav class="jas-pagination">
			<?php
				echo '' . paginate_links(
					array(
						'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'    => '?paged=%#%',
						'current'   => $current_page,
						'total'     => $max,
						'type'      => 'list',
						'prev_text' => esc_html__( 'Prev', 'claue' ),
						'next_text' => esc_html__( 'Next', 'claue' ),
					)
				) . ' ';
			?>
		</nav><!-- .page-nav -->
		<?php
	}
}

/**
 * Create a breadcrumb menu.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_breadcrumb' ) ) {
	function jas_claue_breadcrumb() {
		// Settings
		$sep   = '<i class="fa fa-angle-right"></i>';
		$home  = esc_html__( 'Home', 'claue' );
		$blog  = esc_html__( 'Blog', 'claue' );
		$shop  = esc_html__( 'Shop', 'claue' );
		 
		// Get the query & post information
		global $post, $wp_query;

		// Get post category
		$category = get_the_category();

		// Get product category
		$product_cat = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

		if ( $product_cat ) {
			$tax_title = $product_cat->name;
		}

		$output = '';
		 
		// Build the breadcrums
		$output .= '<div class="bgbl w__100"><ul class="jas-breadcrumb oh">';
		 
		// Do not display on the homepage
		if ( ! is_front_page() ) {

			if ( is_home() ) {
				
				// Home page
				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl separator"> ' . $blog . ' </li>';

			} elseif ( function_exists( 'is_shop' ) && is_shop() ) {

				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl">' . $shop . '</li>';

			} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {

				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl"><a href="' . esc_url( get_post_type_archive_link( 'product' ) ) . '" title="' . esc_attr( $home ) . '">' . $shop . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl separator"> ' . esc_html__( 'Category', 'claue' ) . ' </li>';

			} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {

				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl"><a href="' . esc_url( get_post_type_archive_link( 'product' ) ) . '" title="' . esc_attr( $home ) . '">' . $shop . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl separator"> ' . esc_html__( 'Tag', 'claue' ) . ' </li>';

			} elseif ( is_post_type_archive() ) {

				$post_type = get_post_type_object( get_post_type() );

				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl current">' . $post_type->labels->singular_name . '</li>';
			} elseif ( is_tax() ) {

				$term = $GLOBALS['wp_query']->get_queried_object();

				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl current">' . $term->name . '</li>';

			} elseif ( is_single() ) {
				 
				// Single post (Only display the first category)
				if ( ! empty( $category ) ) {
					$output .= '<li class="fl"><a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '" title="' . esc_attr( $category[0]->cat_name ) . '">' . $category[0]->cat_name . '</a></li>';
				}
				
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl current">' . get_the_title() . '</li>';
				 
			} elseif ( is_category() ) {
				 
				$thisCat = get_category( get_query_var( 'cat' ), false );
				if ( $thisCat->parent != 0 ) echo get_category_parents( $thisCat->parent, TRUE, ' ' );

				// Category page
				$output .= '<li class="fl current">' . single_cat_title( '', false ) . '</li>';
				 
			} elseif ( is_page() ) {
				 
				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';

				// Standard page
				if ( $post->post_parent ) {
					 
					// If child page, get parents 
					$anc = get_post_ancestors( $post->ID );
					 
					// Get parents in the right order
					$anc = array_reverse($anc);
					 
					// Parent page loop
					foreach ( $anc as $ancestor ) {
						$parents = '<li class="fl"><a href="' . esc_url( get_permalink( $ancestor ) ) . '" title="' . esc_attr( get_the_title( $ancestor ) ) . '">' . get_the_title( $ancestor ) . '</a></li>';
						$parents .= '<li class="fl separator"> ' . $sep . ' </li>';
					}
					 
					// Display parent pages
					$output .= $parents;
					 
					// Current page
					$output .= '<li class="fl current"> ' . get_the_title() . '</li>';
					 
				} else {
					 
					// Just display current page if not parents
					$output .= '<li class="fl current"> ' . get_the_title() . '</li>';
					 
				}
				 
			} elseif ( is_tag() ) {
				 
				// Tag page
				 
				// Get tag information
				$term_id  = get_query_var( 'tag_id' );
				$taxonomy = 'post_tag';
				$args     = 'include=' . $term_id;
				$terms    = get_terms( $taxonomy, $args );
				 
				// Display the tag name
				$output .= '<li class="fl current">' . $terms[0]->name . '</li>';
			 
			} elseif ( is_day() ) {
				 
				// Day archive
				 
				// Year link
				$output .= '<li class="fl"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . esc_html__( ' Archives', 'claue' ) . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				 
				// Month link
				$output .= '<li class="fl"><a href="' . esc_url( get_month_link( get_the_time('Y'), get_the_time( 'm' ) ) ) . '" title="' . esc_attr( get_the_time( 'M' ) ) . '">' . get_the_time( 'M' ) . esc_html__( ' Archives', 'claue' ) . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				 
				// Day display
				$output .= '<li class="fl current"> ' . get_the_time('jS') . ' ' . get_the_time('M') . esc_html__( ' Archives', 'claue' ) . '</li>';
				 
			} elseif ( is_month() ) {
				 
				// Month Archive
				 
				// Year link
				$output .= '<li class="fl"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '" title="' . esc_attr( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . esc_html__( ' Archives', 'claue' ) . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				 
				// Month display
				$output .= '<li class="fl">' . get_the_time( 'M' ) . esc_html__( ' Archives', 'claue' ) . '</li>';
				 
			} elseif ( is_year() ) {
				 
				// Display year archive
				$output .= '<li class="fl current">' . get_the_time('Y') . esc_html__( 'Archives', 'claue' ) . '</li>';
				 
			} elseif ( is_author() ) {
				 
				// Auhor archive
				 
				// Get the author information
				global $author;
				$userdata = get_userdata( $author );
				 
				// Display author name
				$output .= '<li class="fl current">' . esc_html__( 'Author: ', 'claue' ) . $userdata->display_name . '</li>';
			 
			} elseif ( get_query_var('paged') ) {
				 
				// Paginated archives
				$output .= '<li class="fl current">' .  esc_html__( 'Page', 'claue' ) . ' ' . get_query_var( 'paged' ) . '</li>';
				 
			} elseif ( is_search() ) {
			 
				// Search results page
				$output .= '<li class="fl current">' .  esc_html__( 'Search results for: ', 'claue' ) . get_search_query() . '</li>';
			 
			} elseif ( is_404() ) {
				 
				// 404 page
				$output .= '<li class="fl home"><a href="' . esc_url( get_home_url() ) . '" title="' . esc_attr( $home ) . '">' . $home . '</a></li>';
				$output .= '<li class="fl separator"> ' . $sep . ' </li>';
				$output .= '<li class="fl current">' . esc_html__( 'Error 404', 'claue' ) . '</li>';
			}
			 
		} else  {
			$output .= '<li class="fl current">' . esc_html__( 'Front Page', 'claue' ) . '</li>';
		}
		 
		$output .= '</ul></div>';

		return apply_filters( 'jas_claue_breadcrumb', $output );
	}
}

/**
 * Print HTML for social share.
 *
 * @return  void
 */
if ( ! function_exists( 'jas_claue_social_share' ) ) {
	function jas_claue_social_share() {
		global $post;
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );
		?>
			<div class="social-share">
				<div class="jas-social">
					<?php if ( cs_get_option( 'facebook-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on Facebook', 'claue' ); ?>" class="cb facebook" href="http://www.facebook.com/sharer.php?u=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=380,width=660');return false;">
							<i class="fa fa-facebook"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'twitter-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on Twitter', 'claue' ); ?>" class="cb twitter" href="https://twitter.com/share?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=380,width=660');return false;">
							<i class="fa fa-twitter"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'pinterest-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on Pinterest', 'claue' ); ?>" class="cb pinterest" href="//pinterest.com/pin/create/button/?url=<?php esc_url( the_permalink() ); ?>&media=<?php echo esc_url( $src[0] ); ?>&description=<?php the_title(); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
							<i class="fa fa-pinterest"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'tumblr-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on Tumblr', 'claue' ); ?>" class="cb tumblr" data-content="<?php echo esc_url( $src[0] ); ?>" href="//tumblr.com/widgets/share/tool?canonicalUrl=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=540');return false;">
							<i class="fa fa-tumblr"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'vk-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on VK', 'claue' ); ?>" class="cb vk" data-content="<?php echo esc_url( $src[0] ); ?>" href="//vk.com/share.php?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=540');return false;">
							<i class="fa fa-vk"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'linkedin-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on LinkedIn', 'claue' ); ?>" class="cb linkedin" data-content="<?php echo esc_url( $src[0] ); ?>" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=540');return false;">
							<i class="fa fa-linkedin"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'whatsapp-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on WhatsApp', 'claue' ); ?>" class="cb whatsapp" data-content="<?php echo esc_url( $src[0] ); ?>" href="whatsapp://send?text=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=540');return false;">
							<i class="fa fa-whatsapp"></i>
						</a>
					<?php } ?>
					<?php if ( cs_get_option( 'telegram-share' ) ) { ?>
						<a title="<?php echo esc_html__( 'Share this post on Telegram', 'claue' ); ?>" class="cb telegram" data-content="<?php echo esc_url( $src[0] ); ?>" href="https://t.me/share/url?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=540');return false;">
							<i class="fa fa-telegram"></i>
						</a>
					<?php } ?>
				</div>
			</div>
		<?php
	}
}

/**
 * Print HTML for social list.
 *
 * @return  void
 */
if ( ! function_exists( 'jas_claue_social' ) ) {
	function jas_claue_social() {
		$output = '';

		$socials = cs_get_option( 'social-network' );
		if ( empty( $socials ) ) return;

		$output .= '<div class="jas-social">';
			foreach ( $socials as $social) {
				$output .= '<a class="dib br__50 tc ' . esc_attr( str_replace( 'fa fa-', '', $social['icon'] ) ) . '" href="' . esc_url( $social['link'] ) . '" target="_blank"><i class="' . esc_attr( $social['icon'] ) . '"></i></a>';
			}
		$output .= '</div>';

		return apply_filters( 'jas_claue_social', $output );
	}
}

/**
 * Render author information.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_author_info' ) ) {
	function jas_claue_author_info() {
		$author = sprintf(
			wp_kses_post( '<div class="post-author">%1$s<div class="clearfix">%2$s%3$s</div></div>', 'claue' ),
			'<h4 class="mg__0 mb__35 pr dib tu cp head__1">' . esc_html__( 'About Author', 'claue' ) . '</h4>',
			'<div class="fl">' . get_avatar( get_the_author_meta( 'user_email' ), '100', '' ) . '</div>',
			'<div class="oh pl__70"><a class="f__mont cb chp fwb db mb__10 mt__5" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a><p>' . get_the_author_meta( 'description' ) . '</p></div>'

		);
		echo apply_filters( 'jas_claue_author_info', $author );
	}
}

/**
 * Render related post based on post tags.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_related_post' ) ) {
	function jas_claue_related_post() {
		global $post;

		// Get post's tags
		$tags = wp_get_post_tags( $post->ID );

		if ( $tags ) {
			// Get id for all tags
			$tag_ids = array();

			foreach ( $tags as $tag ) {
				$tag_ids[] = $tag->term_id;
			}

			// Build arguments to query for related posts
			$args = array(
				'tag__in'             => $tag_ids,
				'post__not_in'        => array( $post->ID ),
				'posts_per_page'      => apply_filters( 'jas_claue_related_post_per_page', '5' ),
				'ignore_sticky_posts' => 1,
				'orderby'             => 'rand',
			);

			// Get related post
			$related = new wp_query( $args );

			$output = '';
			$output .= '<div class="post-related mt__50">';
				$output .= '<h4 class="mg__0 mb__30 tu">' . esc_html__( 'Related Articles', 'claue' ) . '</h4>';
				$output .= '<div class="jas-carousel" data-slick=\'{"slidesToShow": 3,"slidesToScroll": 1, "responsive":[{"breakpoint": 960,"settings":{"slidesToShow": 2}},{"breakpoint": 480,"settings":{"slidesToShow": 1}}]'. ( is_rtl() ? ',"rtl":true' : '' ) .'}\'>';
					while ( $related->have_posts() ) :
						// Update global post data
						$related->the_post();

						$output .= '<div class="item">';
							if ( has_post_thumbnail() ) {
								$output .= '<a class="db mb__20" href="' . esc_url( get_permalink() ) . '">';
									$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

									if ( $img[1] >= 370 && $img[2] >= 210 ) {
										$image = jas_resizer( $img[0], 370, 210, true );
										$output .= '<img src="' . esc_url( $image ) . '" width="370" height="210" alt="' . get_the_title() . '" />';
									} else {
										$output .= '<div class="pr placeholder mb__15">';
											$output .= '<img src="' . JAS_CLAUE_URL . '/assets/images/placeholder.png" width="370" height="210" alt="' . get_the_title() . '" />';
											$output .= '<div class="pa tc fs__10">' . esc_html__( 'The photos should be at least 370px x 210px', 'claue' ) . '</div>';
										$output .= '</div>';
									}
								$output .= '</a>';
							}

							$output .= '<h5 class="mg__0 fs__14"><a class="cd chp" href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h5>';
							$output .= '<span class="f__libre">' . get_the_date( 'j F Y' ) . '</span>';
						$output .= '</div>';
					endwhile;
				$output .= '</div>';	
			$output .= '</div>';
			
			// Reset global query object
			wp_reset_postdata();

			echo apply_filters( 'jas_claue_related_post', $output );
		}
	}
}

/**
 * custom function to use to open and display each comment
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_comments_list' ) ) {
	function jas_claue_comments_list( $comment, $args, $depth ) {
	// Globalize comment object
		$GLOBALS['comment'] = $comment;

		switch ( $comment->comment_type ) :

			case 'pingback'  :
			case 'trackback' :
				?>
				<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
					<p>
						<?php
							echo esc_html__( 'Pingback:', 'claue' );
							comment_author_link();
							edit_comment_link( esc_html__( 'Edit', 'claue' ), '<span class="edit-link">', '</span>' );
						?>
					</p>
				<?php
			break;

			default :
				global $post;
				?>
				<li <?php comment_class( 'mt__30' ); ?> id="li-comment-<?php comment_ID(); ?>">
                    <article id="comment-<?php comment_ID(); ?>" class="comment_container" <?php jas_claue_schema_metadata( array( 'context' => 'comment' ) ); ?>>
                    	<?php echo get_avatar( $comment, 80 ); ?>

						<div class="comment-text">
							<?php if ( '0' == $comment->comment_approved ) : ?>
								<p class="comment-awaiting-moderation"><?php echo esc_html__( 'Your comment is awaiting moderation.', 'claue' ); ?></p>
							<?php endif; ?>

							<?php
								printf(
								'<h5 class="comment-author mg__0 mb__5 tu cb" ' . jas_claue_schema_metadata( array( 'context' => 'comment_author', 'echo' => false ) ) . '><span ' . jas_claue_schema_metadata( array( 'context' => 'author_name', 'echo' => false ) ) . '>%1$s</span></h5>',
									get_comment_author_link(),
									( $comment->user_id == $post->post_author ) ? '<span class="author-post">' . esc_html__( 'Post author', 'claue' ) . '</span>' : ''
								);
							?>
							<div <?php jas_claue_schema_metadata( array( 'context' => 'entry_content' ) ); ?>>
								<?php comment_text(); ?>
							</div>


							<div class="flex">
								<?php
									printf(
										'<time class="grow f__libre" ' . jas_claue_schema_metadata( array( 'context' => 'entry_time', 'echo' => false ) ) . '>%3$s</time>',
										esc_url( get_comment_link( $comment->comment_ID ) ),
										get_comment_time( 'c' ),
										sprintf( wp_kses_post( '%1$s '. esc_html__( 'at', 'claue') .' %2$s', 'claue' ), get_comment_date(), get_comment_time() )
									);
								?>
								<?php
									edit_comment_link( wp_kses_post( '<span>' . esc_html__( 'Edit', 'claue' ) . '</span>', 'claue' ) );
									comment_reply_link(
										array_merge(
											$args,
											array(
												'reply_text' => wp_kses_post( '<span class="ml__10">' . esc_html__( 'Reply', 'claue' ) . '</span>', 'claue' ),
												'depth'      => $depth,
												'max_depth'  => $args['max_depth'],
											)
										)
									);
								?>
							</div><!-- .action-link -->
						</div><!-- .comment-content -->
					</article><!-- #comment- -->
				<?php
			break;

		endswitch;
	}
}

/**
 * Render custom styles.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_custom_css' ) ) {
	function jas_claue_custom_css( $css = array() ) {

		// Content width
		$content_width = cs_get_option( 'content-width' );
		if ( $content_width != '1170' ) {
			$css[] = '
				@media only screen and (min-width: 75em) {
					.jas-container, .boxed .header__top, .boxed .jas-wc-single .bgbl, .boxed #jas-footer, .dokan-dashboard .dokan-dashboard-wrap, .dokan-store .dokan-dashboard-wrap {
						width: ' . esc_attr( $content_width ) . ( ! is_numeric( $content_width ) ? '' : 'px' ) . ' !important;
					}
				}
			';
		}

		// Logo width
		$logo_width = cs_get_option( 'logo-max-width' );
		if ( ! empty( $logo_width ) ) {
			$css[] = '
				.jas-branding {
					max-width: ' . esc_attr( $logo_width ) . 'px;
					margin: auto;
				}
			';
		}
		
		// Boxed layout
		$boxed_bg = cs_get_option( 'boxed-bg' );

		if ( ! empty( $boxed_bg['image'] ) ) {
			$css[] = '.boxed {';
				$css[] = '
					background-image:  url(' .  esc_url( $boxed_bg['image'] ) . ');
					background-size:       ' .  $boxed_bg['size'] .       ';
					background-repeat:     ' .  $boxed_bg['repeat'] .     ';
					background-position:   ' .  $boxed_bg['position'] .   ';
					background-attachment: ' .  $boxed_bg['attachment'] . ';
				';
				if ( ! empty( $boxed_bg['color'] ) ) {
					$css[] = 'background-color: ' .  $boxed_bg['color'] .';';
				}
			$css[] = '}';
		}

		// WC page title
		$wc_head_bg = cs_get_option( 'wc-pagehead-bg' );

		if ( ( function_exists( 'is_shop' ) && is_shop() || function_exists( 'is_product' ) && is_product() ) && ! empty( $wc_head_bg ) ) {
			$css[] = '.jas-wc .page-head, .jas-wc-single .page-head {';
				$css[] = '
					background-image:  url(' .  esc_url( $wc_head_bg['image'] ) . ');
					background-size:       ' .  $wc_head_bg['size'] .       ';
					background-repeat:     ' .  $wc_head_bg['repeat'] .     ';
					background-position:   ' .  $wc_head_bg['position'] .   ';
					background-attachment: ' .  $wc_head_bg['attachment'] . ';
				';
				if ( ! empty( $wc_head_bg['color'] ) ) {
					$css[] = 'background-color: ' .  $wc_head_bg['color'] .';';
				}
			$css[] = '}';
		} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
			global $wp_query;
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
			$tmp = wp_get_attachment_image_src( $thumbnail_id,'full' );
			if ( !empty( $tmp ) )  {
				$css[] = '.jas-wc .page-head {';
					$css[] = '
						background-image:  url(' . esc_url( $tmp[0] ) . ');
						background-position: center center;
						background-size: cover;
					';
				$css[] = '}';
			}
		}

		// Portfolio page title
		$portfolio_head_bg = cs_get_option( 'portfolio-pagehead-bg' );
		if ( ! empty( $portfolio_head_bg['image'] ) ) {
			$css[] = '.jas-portfolio .page-head {';
				$css[] = '
					background-image:  url(' .  esc_url( $portfolio_head_bg['image'] ) . ');
					background-size:       ' .  $portfolio_head_bg['size'] .       ';
					background-repeat:     ' .  $portfolio_head_bg['repeat'] .     ';
					background-position:   ' .  $portfolio_head_bg['position'] .   ';
					background-attachment: ' .  $portfolio_head_bg['attachment'] . ';
				';
				if ( ! empty( $portfolio_head_bg['color'] ) ) {
					$css[] = 'background-color: ' .  $portfolio_head_bg['color'] .';';
				}
			$css[] = '}';
		}


		// Footer background
		$footer_bg = cs_get_option( 'footer-bg' );

		if ( ! empty( $footer_bg['image'] ) ) {
			$css[] = '.footer__top {';
				$css[] = '
					background-image:  url(' .  esc_url( $footer_bg['image'] ) . ')     ;
					background-size:       ' .  esc_attr( $footer_bg['size'] ) .       ';
					background-repeat:     ' .  esc_attr( $footer_bg['repeat'] ) .     ';
					background-position:   ' .  esc_attr( $footer_bg['position'] ) .   ';
					background-attachment: ' .  esc_attr( $footer_bg['attachment'] ) . ';
				';
				if ( ! empty( $footer_bg['color'] ) ) {
					$css[] = 'background-color: ' .  $footer_bg['color'] .';';
				}
			$css[] = '}';
		}

		// Maintenance background
		$offline = cs_get_option( 'maintenance-bg' );

		if ( ! empty( $offline['image'] ) ) {
			$css[] = '.jas-offline-content {';
				$css[] = '
					background-image:  url(' .  esc_url( $offline['image'] ) . ')     ;
					background-size:       ' .  esc_attr( $offline['size'] ) .       ';
					background-repeat:     ' .  esc_attr( $offline['repeat'] ) .     ';
					background-position:   ' .  esc_attr( $offline['position'] ) .   ';
					background-attachment: ' .  esc_attr( $offline['attachment'] ) . ';
				';
				if ( ! empty( $offline['color'] ) ) {
					$css[] = 'background-color: ' .  $offline['color'] .';';
				}
			$css[] = '}';
		}

		// Typography
		$body_font    = cs_get_option( 'body-font' );
		$heading_font = cs_get_option( 'heading-font' );

		$css[] = 'body, .jas-menu > li > a, .f__pop, .jas-menu ul li a {';
			// Body font family
			if ( !empty( $body_font['family'] )) {
				$css[] = 'font-family: "' . $body_font['family'] . '";';
			}
			if ( !empty( $body_font['variant'] ) && '100italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 100;
					font-style: italic;
				';
			} elseif ( !empty( $body_font['variant'] ) && '300italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 300;
					font-style: italic;
				';
			} elseif ( !empty( $body_font['variant'] ) && '400italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 400;
					font-style: italic;
				';
			} elseif ( !empty( $body_font['variant'] ) && '700italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 700;
					font-style: italic;
				';
			} elseif ( !empty( $body_font['variant'] ) && '800italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 700;
					font-style: italic;
				';

			} elseif ( !empty( $body_font['variant'] ) && '900italic' == $body_font['variant'] ) {
				$css[] = '
					font-weight: 900;
					font-style: italic;
				';
			} elseif ( !empty( $body_font['variant'] ) && 'regular' == $body_font['variant'] ) {
				$css[] = 'font-weight: 400;';
			} elseif ( !empty( $body_font['variant'] ) && 'italic' == $body_font['variant'] ) {
				$css[] = 'font-style: italic;';
			} else {
				$css[] = 'font-weight:' . $body_font['variant'] . ';';
			}

			// Body font size
			if ( cs_get_option( 'body-font-size' ) ) {
				$css[] = 'font-size:' . cs_get_option( 'body-font-size' ) . 'px;';
			}

			// Body color
			if ( cs_get_option( 'body-color' ) ) {
				$css[] = 'color:' . cs_get_option( 'body-color' );
			}
		$css[] = '}';

		$css[] = 'h1, h2, h3, h4, h5, h6, .f__pop {';
			$css[] = 'font-family: "' . $heading_font['family'] . '";';
			if ( !empty( $heading_font['variant'] ) && '100italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 100;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '300italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 300;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '400italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 400;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '500italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 500;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '600italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 600;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '700italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 700;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && '900italic' == $heading_font['variant'] ) {
				$css[] = '
					font-weight: 900;
					font-style: italic;
				';
			} elseif ( !empty( $heading_font['variant'] ) && 'regular' == $heading_font['variant'] ) {
				$css[] = 'font-weight: 400;';
			} elseif ( !empty( $heading_font['variant'] ) && 'italic' == $heading_font['variant'] ) {
				$css[] = 'font-style: italic;';
			} else {
				$css[] = 'font-weight:' . $heading_font['variant'];
			}
		$css[] = '}';
		
		if ( cs_get_option( 'heading-color' ) ) {
			$css[] = 'h1, h2, h3, h4, h5, h6 {';
				$css[] = 'color:' . cs_get_option( 'heading-color' );
			$css[] = '}';
		}

		if ( cs_get_option( 'h1-font-size' ) ) {
			$css[] = 'h1 { font-size:' . cs_get_option( 'h1-font-size' ) . 'px; }';
		}
		if ( cs_get_option( 'h2-font-size' ) ) {
			$css[] = 'h2 { font-size:' . cs_get_option( 'h2-font-size' ) . 'px; }';
		}
		if ( cs_get_option( 'h3-font-size' ) ) {
			$css[] = 'h3 { font-size:' . cs_get_option( 'h3-font-size' ) . 'px; }';
		}
		if ( cs_get_option( 'h4-font-size' ) ) {
			$css[] = 'h4 { font-size:' . cs_get_option( 'h4-font-size' ) . 'px; }';
		}
		if ( cs_get_option( 'h5-font-size' ) ) {
			$css[] = 'h5 { font-size:' . cs_get_option( 'h5-font-size' ) . 'px; }';
		}
		if ( cs_get_option( 'h6-font-size' ) ) {
			$css[] = 'h6 { font-size:' . cs_get_option( 'h6-font-size' ) . 'px; }';
		}

		// Primary color
		$primary_color = cs_get_option( 'primary-color' );
		if ( $primary_color ) {
			$css[] = '
				a:hover, a:active,
				.cp, .chp:hover,
				.inside-thumb a:hover,
				.jas-blog-slider .post-thumbnail > div a:hover,
				.shop-top-sidebar .product-categories li.current-cat > a,
				.quantity .qty a:hover,
				.cart .yith-wcwl-add-to-wishlist a:hover,
				.woocommerce-MyAccount-navigation ul li:hover a,
				.woocommerce-MyAccount-navigation ul li.is-active a {
					color: ' . esc_attr( $primary_color ) . ';
				}
			
				input[type="submit"]:hover,
				button:hover,
				a.button:hover,
				.jas-ajax-load a:hover,
				.widget .tagcloud a:hover,
				.jas-ajax-load a:hover,
				.cart .yith-wcwl-add-to-wishlist:hover,
				.p-video a:hover,
				button.button:hover {
					border-color: ' . esc_attr( $primary_color ) . ';
				}
			
				input[type="submit"]:hover,
				button:hover,
				a.button:hover,
				button.button:hover,
				.jas-ajax-load a:hover,
				.bgp, .bghp:hover,
				.signup-newsletter-form input.submit-btn:hover,
				.widget .tagcloud a:hover,
				.widget_price_filter .ui-slider-range,
				.widget_price_filter .ui-state-default,
				.jas-mini-cart .checkout,
				.jas-ajax-load a:hover,
				.metaslider .flexslider .flex-prev, 
				.metaslider .flexslider .flex-next,
				button.single_add_to_cart_button,
				.jas_wcpb_add_to_cart.single_add_to_cart_button,
				.jas-service[class*="icon-"] .icon:before,
				.cart__popup-action .checkout-button,
				div.wpforms-container-full .wpforms-form button[type="submit"],
				div.wpforms-container-full .wpforms-form button[type="submit"]:hover {
					background-color: ' . esc_attr( $primary_color ) . ';
				}
			';
		}

		// Secondary color
		$secondary_color = cs_get_option( 'secondary-color' );
		if ( $secondary_color ) {
			$css[] = '
				a,
				h1, h2, h3, h4, h5, h6,
				input[type="submit"],
				button,
				a.button,
				.jas-ajax-load a,
				.cd,
				.wp-caption-text,
				#jas-header .jas-social a,
				#jas-backtop:hover span i,
				.page-numbers li,
				.page-numbers li a,
				.jas-portfolio-single .portfolio-meta span,
				.sidebar .widget ul li:before,
				.widget ul.product_list_widget li a span.product-title,
				.widget .tagcloud a,
				.filter-trigger:hover,
				.filter-trigger:focus,
				.jas-mini-cart .mini_cart_item a:nth-child(2),
				.btn-atc .yith-wcwl-add-to-wishlist .ajax-loading,
				.product-category h3,
				.quantity input.input-text[type="number"],
				.cart .yith-wcwl-add-to-wishlist a,
				.wc-tabs li.active a,
				.wc-tabs li a:hover,
				.shop_table th,
				.order-total,
				.order-total td,
				.woocommerce-MyAccount-navigation ul li a,
				.jas-filter a.selected,
				.jas-filter a:hover,
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:focus, 
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:hover,
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab.vc_active > a,
				.metaslider .flexslider .caption-wrap h2,
				.metaslider .flexslider .caption-wrap h3,
				.metaslider .flexslider .caption-wrap h4,
				.jas-menu ul li:hover > a,
				#jas-mobile-menu ul li a,
				.holder {
					color: ' . esc_attr( $secondary_color ) . ';
				}
				input:not([type="submit"]):not([type="checkbox"]):focus,
				textarea:focus,
				.error-404.not-found a,
				.more-link,
				.widget .tagcloud a,
				.widget .woocommerce-product-search,
				.widget .search-form,
				.woocommerce .widget_layered_nav ul.yith-wcan-label li a:hover,
				.woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover,
				.woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a,
				.woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
				.jas-ajax-load a,
				form .quantity,
				.quantity input.input-text[type="number"]:focus,
				.cart .yith-wcwl-add-to-wishlist,
				.wc-tabs li.active a,
				.p-video a,
				.jas-filter a.selected,
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:focus, 
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab > a:hover,
				.jas-row .vc_tta-color-grey.vc_tta-style-classic .vc_tta-tab.vc_active > a {
					border-color: ' . esc_attr( $secondary_color ) . ';
				}
				mark,
				.signup-newsletter-form input.submit-btn,
				.error-404.not-found a:hover,
				.more-link:hover,
				.widget h4.widget-title:after,
				.widget .woocommerce-product-search input[type="submit"],
				.widget .search-form .search-submit,
				.woocommerce .widget_layered_nav ul.yith-wcan-label li a:hover,
				.woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover,
				.woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a,
				.woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
				.jas-mini-cart .button,
				.btn-quickview:hover,
				.tab-heading:after,
				.product-extra-title h2:before,
				.product-extra-title h2:after,
				.section-title:before,
				.section-title:after {
					background-color: ' . esc_attr( $secondary_color ) . ';
				}
			';
		}
		// Body color
		if ( cs_get_option( 'body-background-color' ) ) {
			$css[] = 'body { background-color: ' . esc_attr( cs_get_option( 'body-background-color' ) ) . '}';
		}
		if ( cs_get_option( 'body-color' ) ) {
			$css[] = 'body { color: ' . esc_attr( cs_get_option( 'body-color' ) ) . '}';
		}
		if ( cs_get_option( 'heading-color' ) ) {
			$css[] = 'h1, h2, h3, h4, h5, h6 { color: ' . esc_attr( cs_get_option( 'heading-color' ) ) . '}';
		}
		// Header Top color
		$header_top_color = cs_get_option( 'header-top-color' );
		if ( $header_top_color ) {
			$css[] = '
				.jas-socials a,
				.header-text,
				.header__top .jas-action a {
					color: ' . esc_attr( $header_top_color ) . ';
				}
			';
		}
		// Header color
		if ( cs_get_option( 'header-background' ) ) {
			$css[] = '#jas-header, .jas-my-account ul { background-color: ' . esc_attr( cs_get_option( 'header-background' ) ) . '}';
		}

		// Header top
		if ( cs_get_option( 'header-top-background' ) ) {
			$css[] = '.header__top { background-color: ' . esc_attr( cs_get_option( 'header-top-background' ) ) . '}';
		}

		// Menu color
		if ( cs_get_option( 'main-menu-color' ) ) {
			$css[] = '
				.jas-menu > li > a,
				#jas-mobile-menu ul > li:hover > a, 
				#jas-mobile-menu ul > li.current-menu-item > a, 
				#jas-mobile-menu ul > li.current-menu-parent > a, 
				#jas-mobile-menu ul > li.current-menu-ancestor > a,
				#jas-mobile-menu ul > li:hover > .holder, 
				#jas-mobile-menu ul > li.current-menu-item > .holder,
				#jas-mobile-menu ul > li.current-menu-parent  > .holder,
				#jas-mobile-menu ul > li.current-menu-ancestor > .holder,
				.jas-menu li.current-product_cat-ancestor > a,
				.jas-action a {
					color: ' . esc_attr( cs_get_option( 'main-menu-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'main-menu-hover-color' ) ) {
			$css[] = '
				.jas-menu li > a:hover,
				.jas-menu li.current-menu-ancestor > a,
				.jas-menu li.current-menu-item > a,
				.jas-account-menu a:hover,
				.jas-action a:hover {
					color: ' . esc_attr( cs_get_option( 'main-menu-hover-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sub-menu-color' ) ) {
			$css[] = '
				.jas-menu ul a, .jas-account-menu ul a, .jas-menu ul li a {
					color: ' . esc_attr( cs_get_option( 'sub-menu-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sub-menu-hover-color' ) ) {
			$css[] = '
				.jas-menu ul li a:hover {
					color: ' . esc_attr( cs_get_option( 'sub-menu-hover-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sub-menu-background-color' ) ) {
			$css[] = '
				.jas-account-menu ul, .jas-menu > li > ul {
					background: ' . esc_attr( cs_get_option( 'sub-menu-background-color' ) ) . ';
				}
			';
		}

		//Header Transparent Menu color
		if ( cs_get_option( 'transparent-main-menu-color' ) ) {
			$css[] = '
				.home .header__transparent .jas-menu > li > a {
					color: ' . esc_attr( cs_get_option( 'transparent-main-menu-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'transparent-main-menu-hover-color' ) ) {
			$css[] = '
				.home .header__transparent .jas-menu li > a:hover {
					color: ' . esc_attr( cs_get_option( 'transparent-main-menu-hover-color' ) ) . ';
				}
				.home .header-sticky .jas-menu > li > a,
				.home .header-sticky .jas-action a  {
					color: ' . esc_attr( cs_get_option( 'sticky-main-menu-color' ) ) . ';
				}
			';
		}

		//Header Sticky Color
		if ( cs_get_option( 'header-sticky-background' ) ) {
			$css[] = '
				.header-sticky .header__mid {
					background: ' . esc_attr( cs_get_option( 'header-sticky-background' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sticky-main-menu-color' ) ) {
			$css[] = '
				.header-sticky .jas-menu > li > a,
				.header-sticky .jas-action a  {
					color: ' . esc_attr( cs_get_option( 'sticky-main-menu-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sticky-main-menu-hover-color' ) ) {
			$css[] = '
				.header-sticky .jas-menu li a:hover, 
				.header-sticky .jas-menu li.current-menu-ancestor > a, 
				.header-sticky .jas-menu li.current-menu-item > a,
				.header-sticky .jas-action a:hover {
					color: ' . esc_attr( cs_get_option( 'sticky-main-menu-hover-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sticky-sub-menu-background-color' ) ) {
			$css[] = '
				.header-sticky .jas-account-menu ul, 
				.header-sticky .jas-menu > li > ul {
					background: ' . esc_attr( cs_get_option( 'sticky-sub-menu-background-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sticky-sub-menu-color' ) ) {
			$css[] = '
				.header-sticky .jas-menu ul li a {
					color: ' . esc_attr( cs_get_option( 'sticky-sub-menu-color' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'sticky-sub-menu-color-hover' ) ) {
			$css[] = '
				.header-sticky .jas-menu ul li a:hover,
				.header-sticky .jas-menu ul li.current-menu-item a,
				.header-sticky .jas-menu ul li.current-menu-ancestor > a {
					color: ' . esc_attr( cs_get_option( 'sticky-sub-menu-color-hover' ) ) . ';
				}
			';
		}

		// Footer color
		if ( cs_get_option( 'footer-background' ) ) {
			$css[] = '
				#jas-footer {
					background: ' . esc_attr( cs_get_option( 'footer-background' ) ) . ';
				}
			';
		}
		if ( cs_get_option( 'footer-color' ) ) {
			$css[] = '
				#jas-footer {
					color: ' . esc_attr( cs_get_option( 'footer-color' ) ) . ';
				}
			';
		}

		if ( cs_get_option( 'footer-link-color' ) ) {
			$css[] = '
				.footer__top a, .footer__bot a {
					color: ' . esc_attr( cs_get_option( 'footer-link-color' ) ) . ';
				}
			';
		}

		if ( cs_get_option( 'footer-widget-color' ) ) {
			$css[] = '
				.footer__top .widget-title {
					color: ' . esc_attr( cs_get_option( 'footer-widget-color' ) ) . ';
				}
			';
		}

		if ( cs_get_option( 'footer-link-hover-color' ) ) {
			$css[] = '
				.footer__top a:hover, .footer__bot a:hover {
					color: ' . esc_attr( cs_get_option( 'footer-link-hover-color' ) ) . ';
				}
			';
		}

		if( cs_get_option('product-sale-color') ) {
			$css[] = '
				.badge span {
					background:'. esc_attr( cs_get_option( 'product-sale-color' ) ) .';
				}
			';
		}

		if( cs_get_option('product-text-color') ) {
			$css[] = '
				.badge span {
					color:'. esc_attr( cs_get_option( 'product-text-color' ) ) .';
				}
			';
		}

		if( cs_get_option('product-new-color') ) {
			$css[] = '
				.badge .new {
					background:'. esc_attr( cs_get_option( 'product-new-color' ) ) .';
				}
			';
		}

		if( cs_get_option('wc-attr-background') ) {
			$css[] = '
				.product-image .product-attr {
					background:'. esc_attr( cs_get_option( 'wc-attr-background' ) ) .';
				}
			';
		}

		// Custom css
		if ( cs_get_option( 'custom-css' ) ) {
			$css[] = cs_get_option( 'custom-css' );
		}

		return preg_replace( '/\n|\t/i', '', implode( '', $css ) );
	}
}

/**
 * Add custom javascript code
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_custom_js' ) ) {
	function jas_claue_custom_js( $data = array() ) {
		$data[] = '
			var JASAjaxURL = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";
			var JASSiteURL = "' . get_home_url() . '/index.php' . '";
		';
		$data[] = cs_get_option( 'custom-js' );
		
		return preg_replace( '/\n|\t/i', '', implode( '', $data ) );
	}
}

/**
 * Get custom data to js.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_custom_data_js' ) ) {
	function jas_claue_custom_data_js() {
		$data = array(
			'load_more'    => esc_html__( 'Load More', 'claue' ),
			'no_more_item' => esc_html__( 'No More Items To Show', 'claue' ),
			'days'         => esc_html__( 'days', 'claue' ),
			'hrs'          => esc_html__( 'hrs', 'claue' ),
			'mins'         => esc_html__( 'mins', 'claue' ),
			'secs'         => esc_html__( 'secs', 'claue' ),
			'popup_remove' => esc_html__( 'Removed from the cart', 'claue' ),
			'popup_undo'   => esc_html__( 'Undo?', 'claue' ),
		);

		// Header Sticky
		$data['header_sticky'] = cs_get_option( 'header-sticky' );

		// Get option for permalink
		$data['permalink'] = ( get_option( 'permalink_structure' ) == '' ) ? 'plain' : '';

		$data['wc-column'] = ( cs_get_option( 'wc-column' ) !== '' ) ? cs_get_option( 'wc-column' ) : 3;

		$data['portfolio-column'] = ( cs_get_option( 'portfolio-column' ) !== '' ) ? cs_get_option( 'portfolio-column' ) : 3;

		$data['wc-single-zoom'] = ( cs_get_option( 'wc-single-zoom' ) !== '' ) ? cs_get_option( 'wc-single-zoom' ) : 0;

        $data['wc-sticky-atc'] = ( cs_get_option( 'wc-sticky-atc' ) !== '' ) ? cs_get_option( 'wc-sticky-atc' ) : 0;

		return $data;
	}
}

/**
 * Render title of page.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_page_title' ) ) {
	function jas_claue_page_title() {
		$output = '';

		// Get title of blog list
		$blog_title      = cs_get_option( 'blog-page-title' );
		$portfolio_title = cs_get_option( 'portfolio-page-title' );

		$output .= '<h1 class="tu mb__10 cw" ' . jas_claue_schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '>';
			if ( is_page() ) {

				$output .= get_the_title();

			} elseif ( is_home() ) {

				if ( ! empty( $blog_title ) ) {
					$output .= $blog_title;
				} else {
					$output .= esc_html__( 'Article', 'claue' );
				}

			} elseif ( is_post_type_archive( 'portfolio' ) ) {
				
				if ( ! empty( $portfolio_title ) ) {
					$output .= $portfolio_title;
				} else {
					$output .= esc_html__( 'Portfolio', 'claue' );
				}

			} elseif ( is_tax() ) {
				$term = $GLOBALS['wp_query']->get_queried_object();
				$output .= $term->name;
			}
		$output .= '</h1>';

		return apply_filters( 'jas_claue_page_title', $output );
	}
}

/**
 * Render sub title of page.
 *
 * @return string
 */
if ( ! function_exists( 'jas_claue_page_sub_title' ) ) {
	function jas_claue_page_sub_title() {
		$output = '';

		// Get sub title
		if ( is_page() ) {
			$subtitle = get_post_meta( get_the_ID(), '_custom_page_options', true );
			if ( isset( $subtitle['subtitle'] ) && ! $subtitle['subtitle'] ) return;

			$output .= '<p>';
				if ( isset( $subtitle['subtitle'] ) && $subtitle['subtitle'] && ! empty( $subtitle['title'] ) ) {
					$output .= $subtitle['title'];
				}
			$output .= '</p>';
		} elseif ( is_post_type_archive( 'portfolio' ) ) {
			$portfolio_subtitle = cs_get_option( 'portfolio-sub-title' );
			if ( ! empty( $portfolio_subtitle ) ) {
				$output .= '<p>' . esc_html( $portfolio_subtitle ) . '</p>';
			}
		} elseif ( is_tax() ) {
			$output .= category_description();
		}

		return apply_filters( 'jas_claue_page_sub_title', $output );
	}
}

/**
 * Render page heading for page.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_head_page' ) ) {
	function jas_claue_head_page() {
		$output = $atts = '';

		if ( ! is_post_type_archive( 'portfolio' ) && ! is_tax( 'portfolio_cat' ) ) {
			// Get post or page thumbnail
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full', false );

			if ( $image ) {
				$atts = 'style="background: url(' . esc_url( $image[0] ) . ') no-repeat center center / cover;"';
			}
		}

		$output .= '<div class="page-head pr tc" ' . $atts . '>';
			$output .= '<div class="jas-container pr">';
				$output .= jas_claue_page_title();
				$output .= jas_claue_page_sub_title();
			$output .= '</div>';
		$output .= '</div>';

		return apply_filters( 'jas_claue_head_page', $output );
	}
}

/**
 * Render page heading for single post.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_head_single' ) ) {
	function jas_claue_head_single() {
		$output = $atts = '';

		// Get post or page thumbnail
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full', false );

		if ( $image ) {
			$atts = 'style="background: url(' . esc_url( $image[0] ) . ') no-repeat center center / cover;"';
		}

		// Get posted on
		$time = '<time class="entry-date published updated f__libre"' . jas_claue_schema_metadata( array( 'context' => 'entry_time', 'echo' => false ) ) . '>%2$s</time>';

		// Post categories
		$categories = get_the_category_list( esc_html__( ', ', 'claue' ) );

		$output .= '<div class="page-head pr tc" ' . $atts . '>';
			$output .= '<div class="jas-container pr">';
				$output .= sprintf( '<h1 class="tu cw mb__10" ' . jas_claue_schema_metadata( array( 'context' => 'entry_title', 'echo' => false ) ) . '>%s</h1>', get_the_title() );
				$output .= sprintf( $time,
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() )
				);
				$output .= '<div class="pr mt__10">';
					if ( $categories ) {
						$output .= sprintf( '<span>' . esc_html__( 'In %1$s ', 'claue' ) . '</span>', $categories );
					}
					// Post comments
					if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
						$comments_number = get_comments_number();
		    
						if ( '1' === $comments_number ) {
							$output .= '<span class="comment-number pr"><a href="' . get_comments_link() . '">' .sprintf( _x( '1 Comment', 'comments title', 'claue' ), get_the_title() );
							$output .= '</a></span>';
						} else {
							$output .= '<span class="comment-number pr"><a href="' . get_comments_link() . '">' . sprintf(
								_nx(
									'%1$s Comment',
									'%1$s Comments',
									$comments_number,
									'comments title',
									'claue'
								),
								number_format_i18n( $comments_number ),
								get_the_title()
							);
							$output .= '</a></span>';
						}

					}
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		return apply_filters( 'jas_claue_head_single', $output );
	}
}

/**
 * Get all registered sidebars.
 *
 * @return  array
 */
function jas_claue_get_sidebars() {
	global $wp_registered_sidebars;

	// Get custom sidebars.
	$custom_sidebars = get_option( 'claue_custom_sidebars' );

	// Prepare output.
	$output = array();

	$output[] = esc_html__( 'Select a sidebar', 'claue' );

	if ( ! empty( $wp_registered_sidebars ) ) {
		foreach ( $wp_registered_sidebars as $sidebar ) {
			$output[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	if ( ! empty( $custom_sidebars ) ) {
		foreach ( $custom_sidebars as $sidebar ) {
			$output[ $sidebar['id'] ] = $sidebar['name'];
		}
	}


	return $output;
}

/**
 * Render google font link
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'jas_claue_google_font_url' ) ) {
	function jas_claue_google_font_url() {
		// Google font
		$fonts = $font_parse = array();

		// Font default
		$fonts['Poppins'] = array(
			'300',
			'400',
			'500',
			'600',
			'700',
		);
		$fonts['Libre Baskerville'] = array( '400italic' );

		// Body font
		$body_font    = cs_get_option( 'body-font' );
		$heading_font = cs_get_option( 'heading-font' );

		if ( $body_font && $body_font['font'] == 'google' ) {
			$font_family = esc_attr( $body_font['family'] );
			if ( '100italic' == $body_font['variant'] ) {
				$font_weight = array( '100' );
			} elseif ( '300italic' == $body_font['variant'] ) {
				$font_weight = array( '300' );
			} elseif ( '400italic' == $body_font['variant'] ) {
				$font_weight = array( '400' );
			} elseif ( '700italic' == $body_font['variant'] ) {
				$font_weight = array( '700' );
			} elseif ( '900italic' == $body_font['variant'] ) {
				$font_weight = array( '900' );
			} elseif ( 'regular' == $body_font['variant'] ) {
				$font_weight = array( '400' );
			} else {
				$font_weight = array( $body_font['variant'] );
			}

			// Merge array and delete values duplicated
			$fonts[$font_family] = isset( $fonts[$font_family] ) ? array_unique( array_merge( $fonts[$font_family], $font_weight ) ) : $font_weight;
		}

		if ( $heading_font && $heading_font['font'] == 'google' ) {
			$font_family = esc_attr( $heading_font['family'] );
			if ( '100italic' == $heading_font['variant'] ) {
				$font_weight = array( '100' );
			} elseif ( '300italic' == $heading_font['variant'] ) {
				$font_weight = array( '300' );
			} elseif ( '400italic' == $heading_font['variant'] ) {
				$font_weight = array( '400' );
			} elseif ( '700italic' == $heading_font['variant'] ) {
				$font_weight = array( '700' );
			} elseif ( '900italic' == $heading_font['variant'] ) {
				$font_weight = array( '900' );
			} elseif ( 'regular' == $heading_font['variant'] ) {
				$font_weight = array( '400' );
			} else {
				$font_weight = array( $heading_font['variant'] );
			}

			// Merge array and delete values duplicated
			$fonts[$font_family] = isset( $fonts[$font_family] ) ? array_unique( array_merge( $fonts[$font_family], $font_weight ) ) : $font_weight;
		}

		// Parse array to string for url Google fonts
		foreach ( $fonts as $font_name => $font_weight ) {
			$font_parse[] = $font_name . ':'. implode( ',' , $font_weight );
		}

		$query_args = array(
			'family' => urldecode( implode( '|', $font_parse ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );

		return esc_url_raw( $fonts_url );
	}
}


/**
 * Setup schema metadata.
 *
 * @param   array  $args  Arguments.
 *
 * @return  void
 */
if ( ! function_exists( 'jas_claue_schema_metadata' ) ) {
	function jas_claue_schema_metadata( $args ) {
		// Set default arguments
		$default_args = array(
			'post_type' => '',
			'context'   => '',
			'echo'      => true,
		);

		$args = apply_filters( 'jas_claue_schema_metadata_args', wp_parse_args( $args, $default_args ) );

		if ( empty( $args['context'] ) ) {
			return;
		}

		// Markup string - stores markup output
		$markup     = ' ';
		$attributes = array();

		// Try to fetch the right markup
		switch ( $args['context'] ) {
			case 'body':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WebPage';
			break;

			case 'header':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPHeader';
			break;

			case 'nav':
				$attributes['role']      = 'navigation';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/SiteNavigationElement';
			break;

			case 'content':
				$attributes['role']     = 'main';
				$attributes['itemprop'] = 'mainContentOfPage';

				// Frontpage, Blog, Archive & Single Post
				if ( is_singular( 'post' ) || is_archive() || is_home() ) {
					$attributes['itemscope'] = 'itemscope';
					$attributes['itemtype']  = 'http://schema.org/Blog';
				}

				// Search Results Pages
				if ( is_search() ) {
					$attributes['itemscope'] = 'itemscope';
					$attributes['itemtype']  = 'http://schema.org/SearchResultsPage';
				}
			break;

			case 'entry':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/CreativeWork';
			break;

			case 'image':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/ImageObject';
			break;

			case 'image_url':
				$attributes['itemprop'] = 'contentURL';
			break;

			case 'name':
				$attributes['itemprop'] = 'name';
			break;

			case 'email':
				$attributes['itemprop'] = 'email';
			break;

			case 'url':
				$attributes['itemprop'] = 'url';
			break;

			case 'author':
				$attributes['itemprop']  = 'author';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
			break;

			case 'author_link':
				$attributes['itemprop'] = 'url';
			break;

			case 'author_name':
				$attributes['itemprop'] = 'name';
			break;

			case 'author_description':
				$attributes['itemprop'] = 'description';
			break;

			case 'entry_time':
				$attributes['itemprop'] = 'datePublished';
				$attributes['datetime'] = get_the_time( 'c' );
			break;

			case 'entry_title':
				$attributes['itemprop'] = 'headline';
			break;

			case 'entry_content':
				$attributes['itemprop'] = 'text';
			break;

			case 'comment':
				$attributes['itemprop']  = 'comment';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Comment';
			break;

			case 'comment_author':
				$attributes['itemprop']  = 'creator';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
			break;

			case 'comment_author_link':
				$attributes['itemprop']  = 'creator';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
				$attributes['rel']       = 'external nofollow';
			break;

			case 'comment_time':
				$attributes['itemprop']  = 'commentTime';
				$attributes['itemscope'] = 'itemscope';
				$attributes['datetime']  = get_the_time( 'c' );
			break;

			case 'comment_text':
				$attributes['itemprop'] = 'commentText';
			break;

			case 'sidebar':
				$attributes['role']      = 'complementary';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPSideBar';
			break;

			case 'search_form':
				$attributes['itemprop']  = 'potentialAction';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/SearchAction';
			break;

			case 'footer':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPFooter';
			break;
		}

		$attributes = apply_filters( 'jas_claue_schema_metadata_attributes', $attributes, $args );

		// If failed to fetch the attributes - let's stop
		if ( empty( $attributes ) ) {
			return;
		}

		// Cycle through attributes, build tag attribute string
		foreach ( $attributes as $key => $value ) {
			$markup .= $key . '="' . $value . '" ';
		}

		$markup = apply_filters( 'jas_claue_schema_metadata_output', $markup, $args );

		if ( $args['echo'] ) {
			echo '' . $markup;
		} else {
			return $markup;
		}
	}
}