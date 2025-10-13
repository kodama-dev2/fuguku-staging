<?php
/**
 * Portfolio single.
 *
 * @package ClaueAddons
 * @since   1.0.0
 */

get_header(); ?>
	<div id="jas-content">
		<?php get_template_part( 'views/common/page', 'head' ); ?>
		
		<div class="jas-container mt__60 mb__60">
			<div class="jas-row jas-portfolio-single">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
					<div class="jas-container tc">
						<div class="portfolio-meta jas-row mb__60">
							<?php echo '<div class="jas-col-md-4 jas-col-sm-4 jas-col-xs-12"><span class="mb__5 tu ls__2 db f__mont">' . esc_html__( 'Categories: ', 'claue-addons' ) . '</span>' . get_the_term_list( $post->ID, 'portfolio_cat', '', ', ' ) . '</div>'; ?>
							<?php echo '<div class="jas-col-md-4 jas-col-sm-4 jas-col-xs-12"><span class="mb__5 tu ls__2 db f__mont">' . esc_html__( 'Clients: ', 'claue-addons' ) . '</span>' . get_the_term_list( $post->ID, 'portfolio_client', '', ', ' ) . '</div>'; ?>
							<?php echo '<div class="jas-col-md-4 jas-col-sm-4 jas-col-xs-12"><span class="mb__5 tu ls__2 db f__mont">' . esc_html__( 'Tags: ', 'claue-addons' ) . '</span>' . get_the_term_list( $post->ID, 'portfolio_tag', '', ', ' ) . '</div>'; ?>		
						</div>
						<?php jas_claue_social_share(); ?>
						<div class="portfolio-navigation mt__60 fs__40">
							<?php
								$next     = get_adjacent_post();
								$previous = get_adjacent_post( false, '', false );

								if ( $next ) {
									echo '<a href="' . esc_url( get_permalink( $next->ID ) ) . '" class="pl__30 pr__30 cd chp"><i class="pe-7s-angle-left"></i></a>';
								}

								echo '<a href="' . esc_url( get_post_type_archive_link( 'portfolio' ) ) . '" class="pl__30 pr__30 cd chp"><i class="pe-7s-keypad"></i></a>';

								if ( $previous ) {
									echo '<a href="' . esc_url( get_permalink( $previous->ID ) ) . '" class="pl__30 pr__30 cd chp"><i class="pe-7s-angle-right"></i></a>';
								}
							?>
						</div><!-- .portfolio-navigation -->
					</div>
				<?php endwhile; ?>
			</div><!-- .jas-row -->
		</div><!-- .jas-container -->

		<?php Claue_Addons_Portfolio::related(); ?>
	</div><!-- #jas-content -->
<?php get_footer(); ?>