<?php
/**
 * The main template file.
 *
 * @since   1.0.0
 * @package Claue
 */

// Get blog layout
$layout = apply_filters( 'jas_claue_blog_layout', cs_get_option( 'blog-layout' ) );
if ( $layout == 'left-sidebar' ) {
	$content_class = 'jas-col-md-9 jas-col-xs-12';
	$sidebar_class = 'jas-col-md-3 jas-col-xs-12 first-md';
} elseif ( $layout == 'right-sidebar' ) {
	$content_class = 'jas-col-md-9 jas-col-xs-12';
	$sidebar_class = 'jas-col-md-3 jas-col-xs-12';
} else {
	$content_class = 'jas-col-md-12 jas-col-xs-12 mt__60 mb__60';
	$sidebar_class = '';
}

// Blog style
$class = $data = $sizer = '';
$style = apply_filters( 'jas_claue_blog_style', cs_get_option( 'blog-style' ) );
if ( $style == 'masonry' ) {
	$class = ' jas-masonry';
	$data  = 'data-masonry=\'{"selector":".post", "columnWidth":".grid-sizer", "layoutMode":"masonry"' . ( is_rtl() ? ',"rtl": false' : ',"rtl": true' ) . '}\'';
	$sizer = '<div class="grid-sizer size-' . cs_get_option( 'blog-masonry-column' ) . '"></div>';
}

get_header(); ?>

<div id="jas-content">
	<?php
		if ( cs_get_option( 'blog-latest-slider' ) ) {
			get_template_part( 'views/post/latest' );
		}
	?>

	<div class="jas-container">
		<div class="jas-row jas-blog">
			<div class="<?php echo esc_attr( $content_class ); ?>">
				<div class="posts mt__60<?php echo esc_attr( $class ); ?>" <?php echo wp_kses_post( $data ); ?>>
					<?php
						if ( $style == 'masonry' ) {
							echo wp_kses_post( $sizer );
						}
						if ( have_posts() ) :
							while ( have_posts() ) : the_post();
								if ( $style == 'masonry' ) {
									get_template_part( 'views/post/content', 'masonry' );
								} else {
									get_template_part( 'views/post/content', get_post_format() );
								}
							endwhile;
						else :
							get_template_part( 'content', 'none' );
						endif;
					?>
				</div><!-- .posts -->
				<?php jas_claue_pagination(); ?>
			</div><!-- .jas-col-md-9 -->
			
			<?php if ( 'no-sidebar' != $layout ) { ?>
				<div class="<?php echo esc_attr( $sidebar_class ); ?>">
					<?php get_sidebar(); ?>
				</div><!-- .jas-col-md-3 -->
			<?php } ?>
		</div><!-- .jas-row -->
	</div><!-- .jas-container -->
</div><!-- #jas-content -->

<?php get_footer(); ?>