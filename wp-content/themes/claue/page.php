<?php
/**
 * The template for displaying all pages.
 *
 * @since   1.0.0
 * @package Claue
 */
get_header();

// Get VC setting
$vc = get_post_meta( get_the_ID(), '_wpb_vc_js_status', true );

// Get page options
$options = get_post_meta( get_the_ID(), '_custom_page_options', true );

$sidebar = cs_get_option( 'page-sidebar' );

// Render columns number
if ( isset( $options['page-layout'] ) ) {
	switch ( $options['page-layout'] ) {
		case 'left-sidebar' :
			$content_class = 'jas-col-md-9 jas-col-xs-12 last-md mt__60 mb__60';
			$sidebar_class = 'jas-col-md-3 jas-col-xs-12 first-md';
			break;

		case 'right-sidebar' :
			$content_class = 'jas-col-md-9 jas-col-xs-12 mt__60 mb__60';
			$sidebar_class = 'jas-col-md-3 jas-col-xs-12';
			break;

		case 'no-sidebar' :
		default:
			$content_class = 'jas-col-md-12 jas-col-xs-12 mt__60 mb__60';
			break;
	}
} else {
	$content_class = 'jas-col-md-12 jas-col-xs-12 mt__60 mb__60';
	$sidebar_class = '';
}
// Get all sidebars
if ( isset( $options['page-sidebar'] ) ) {
	$sidebar = $options['page-sidebar'];
}
?>
<div id="jas-content">
	<?php get_template_part( 'views/common/page', 'head' ); ?>

	<?php
		if ( isset( $options['breadcrumb'] ) && $options['breadcrumb'] ) {
			echo jas_claue_breadcrumb();
		}
	?>

	<?php if ( $vc == 'false' || empty( $vc ) || ( isset( $options['page-layout'] ) && $options['page-layout'] != 'no-sidebar' ) ) echo '<div class="jas-container">'; ?>
		<div class="jas-row jas-page">
			<div class="<?php echo esc_attr( $content_class ); ?>" role="main" <?php jas_claue_schema_metadata( array( 'context' => 'entry' ) ); ?>>
				<?php
					while ( have_posts() ) : the_post();
						the_content();

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) {
							echo '<div class="jas-container">';
								comments_template();
							echo '</div>';
						}
					endwhile;

					// Displays page-links
					wp_link_pages();
				?>
			</div><!-- $classes -->
			
			<?php if ( isset( $options['page-layout'] ) && $options['page-layout'] != 'no-sidebar' ) { ?>
				<div class="<?php echo esc_attr( $sidebar_class ); ?>" role="main">
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
			<?php } ?>
		</div><!-- .jas-row -->
	<?php if ( $vc == 'false' || empty( $vc ) || ( isset( $options['page-layout'] ) && 'no-sidebar' != $options['page-layout'] ) ) echo '</div>'; ?>
</div><!-- #jas-content -->
<?php get_footer();