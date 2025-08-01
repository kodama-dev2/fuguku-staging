<?php
/**
 * The template display latest post.
 *
 * @since   1.0.0
 * @package Claue
 */
$query = new WP_Query( array( 'posts_per_page' => 10, 'ignore_sticky_posts' => true ) );
?>
<div class="jas-blog-slider jas-carousel" data-slick='{"slidesToShow": 3,"slidesToScroll": 1, "responsive":[{"breakpoint": 960,"settings":{"slidesToShow": 2}},{"breakpoint": 480,"settings":{"slidesToShow": 1}}]<?php echo ( is_rtl() ? ',"rtl":true' : '' ); ?>}'>
	<?php while ( $query->have_posts() ) : $query->the_post(); ?>
		<div class="post-thumbnail pr">
			<a href="<?php esc_url( the_permalink() ); ?>">
				<?php
					if ( has_post_thumbnail() ) :
						$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
						if ( $img[1] >= 640 && $img[2] >= 310 ) {
							$image = jas_resizer( $img[0], 640, 310, true );
							echo '<img src="' . esc_url( $image ) . '" width="640" height="310" alt="' . get_the_title() . '" />';
						} else {
							echo '<div class="pr placeholder mb__15">';
								echo '<img src="' . JAS_CLAUE_URL . '/assets/images/placeholder.png" width="640" height="310" alt="' . get_the_title() . '" />';
								echo '<div class="pa tc fs__10">' . esc_html__( 'The photos should be at least 640px x 310px', 'claue' ) . '</div>';
							echo '</div>';
						}
					else :
						echo '<img src="' . JAS_CLAUE_URL . '/assets/images/placeholder.png" width="640" height="310" alt="' . get_the_title() . '" />';
					endif;
				?>
			</a>
			<div class="pa tc cg w__100">
				<?php jas_claue_post_meta(); ?>
				<?php jas_claue_post_title(); ?>
				<?php jas_claue_posted_on(); ?>
			</div>
		</div>
	<?php
		endwhile;
		wp_reset_postdata();
	?>
</div><!-- .jas-blog-slider -->