<?php
/**
 * The template part for displaying single posts.
 * 
 * @since   1.0.0
 * @package Claue
 */
?>
<?php do_action( 'jas_claue_before_single_post' ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php jas_claue_schema_metadata( array( 'context' => 'entry' ) ); ?>>
	<div class="post-content">
		<?php
			the_content( sprintf(
				__( 'Continue Reading<span class="screen-reader-text"> "%s"</span>', 'claue' ),
				get_the_title()
			) );
		?>
	</div>
</article><!-- #post-# -->
<?php do_action( 'jas_claue_after_single_post' ); ?>