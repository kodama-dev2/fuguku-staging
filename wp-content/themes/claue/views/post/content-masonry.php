<?php
/**
 * The template part for displaying content.
 * 
 * @since   1.0.0
 * @package Claue
 */

$column = '';
if ( apply_filters( 'jas_claue_blog_style', cs_get_option( 'blog-style' ) ) == 'masonry' ) {
	$column = ' jas-col-md-' . cs_get_option( 'blog-masonry-column' );
}
?>
<?php do_action( 'jas_claue_before_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'mb__30' . $column ); ?> <?php jas_claue_schema_metadata( array( 'context' => 'entry' ) ); ?>>
	<?php jas_claue_post_thumbnail(); ?>
	
	<div class="post-content">
		<?php
			$display_type = cs_get_option( 'blog-content-display' );
			
			if ( $display_type == 'content' ) {
				the_content( sprintf(
					__( 'Continue Reading<span class="screen-reader-text"> "%s"</span>', 'claue' ),
					get_the_title()
				) );
			} else {
				the_excerpt();
			}
		?>

		<div class="post-action flex between-xs middle-xs mt__30">
			<?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
				<div class="comments-link"><i class="pe-7s-comment dib pr mr__5"></i><?php comments_popup_link( esc_html__( '0 Comment', 'claue' ), esc_html__( '1 Comment', 'claue' ), esc_html__( '% Comments', 'claue' ) ); ?></div>
			<?php endif; ?>
			<a class="read-more pr" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Continue Reading', 'claue' ); ?><span> &rarr;</span></a>
		</div>
	</div><!-- .post-content -->
</article><!-- #post-# -->
<?php do_action( 'jas_claue_after_post' ); ?>