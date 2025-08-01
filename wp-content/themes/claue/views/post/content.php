<?php
/**
 * The template part for displaying content.
 * 
 * @since   1.0.0
 * @package Claue
 */
?>
<?php do_action( 'jas_claue_before_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'mb__100' ); ?> <?php jas_claue_schema_metadata( array( 'context' => 'entry' ) ); ?>>
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
				echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Continue Reading', 'claue' ) . '</a>';
			}
		?>
	</div><!-- .post-content -->
</article><!-- #post-# -->
<?php do_action( 'jas_claue_after_post' ); ?>