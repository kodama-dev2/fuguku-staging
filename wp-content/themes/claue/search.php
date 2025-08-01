<?php
/**
 * The template for displaying search results pages.
 *
 * @since   1.0.0
 * @package Claue
 */
get_header();
$sidebar = cs_get_option( 'page-sidebar' );
?>
<div id="jas-content">
	<?php get_template_part( 'views/common/page', 'head' ); ?>
	
	<div class="jas-container">
		<div class="jas-row jas-page">
			<div class="jas-col-md-9 jas-col-xs-12 mt__60 mb__60" role="main">
				<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();
							get_template_part( 'views/post/content' );
						endwhile;

						jas_claue_pagination();

						// If no content, include the "No posts found" template.
					else :
						get_template_part( 'views/post/content', 'none' );
					endif;
				?>
			</div><!-- $classes -->
			<div class="jas-col-md-3 jas-col-xs-12" role="main">
				<?php
					echo '<div class="sidebar mt__60 mb__60" role="complementary" ' . jas_claue_schema_metadata( array( 'context' => 'sidebar', 'echo' => false ) ) . '>';

						if ( is_active_sidebar( $sidebar ) ) {
							dynamic_sidebar( $sidebar );
						} elseif ( is_active_sidebar( 'primary-sidebar' ) ) {
							dynamic_sidebar( 'primary-sidebar' );
						}
					echo '</div>';
				?>
			</div>

		</div><!-- .jas-row -->
	</div>
</div><!-- #jas-content -->
<?php get_footer();