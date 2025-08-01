<?php
/**
 * Admin Template: Overview
 *
 * @package pok
 * @since   1.0.0
 * @version 1.0.0
 */

do_action( 'pok_about_overview_before' );
?>
<div class="pok-onboard-content">
	<div class="row">
		<div class="col-half main-logo">

			<img src="<?php echo esc_attr( POK_PLUGIN_URL . '/assets/img/logo.png' ); ?>" alt="pok-logo" class="pok-logo">
			<img src="<?php echo esc_attr( POK_PLUGIN_URL . '/assets/img/map.png' ); ?>" alt="pok-map" class="pok-map">

			<p class="info"><?php esc_html_e( 'Version:', 'pok' ) ?> <?php echo POK_VERSION; ?> <a href="https://pluginongkoskirim.com/changelog/"><?php esc_html_e( 'View Changelogs', 'pok' ); ?></a></p>
		</div>
		<div class="col-half video">
			<?php if ( ! empty( $tutorials ) ) : ?>
				<h3><?php esc_html_e( 'Tutorials', 'pok' ); ?></h3>
				<ul>
					<?php foreach ( $tutorials as $tutorial ) : ?>
						<li><a href="<?php echo esc_url( $tutorial['link'] ) ?>" target="_blank"><?php echo esc_html( $tutorial['title'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<iframe css="display:block;margin:0px auto;max-height:300px" width="100%" height="300px" src="https://www.youtube.com/embed/1OTbvlfi7ao" frameborder="0" allowfullscreen=""></iframe>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php do_action( 'pok_about_overview_middle' ); ?>

<div class="pok-onboard-footer">
	<div class="row">
		<div class="col-fourth">
			<div class="more-content">
				<div class="more-text">
					<h3><?php esc_html_e( 'Documentation', 'pok' ); ?></h3>
					<p><?php esc_html_e( "Confused with Plugin Ongkos Kirim configuration? Our documentation will assists you to discover all of the plugin's true potential.", 'pok' ); ?></p>
				</div>
				<div class="more-btn">
					<a href="http://pustaka.tonjoostudio.com/plugins/woo-ongkir-manual/?utm_source=wp_wooongkir-premium&utm_medium=onboarding_overview&utm_campaign=upsell" class="button-primary">
						<?php esc_html_e( 'View Documentation', 'pok' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="col-fourth">
			<div class="more-content">
				<div class="more-text">
					<h3><?php esc_html_e( 'FAQ', 'pok' ); ?></h3>
					<p><?php esc_html_e( "Have a problem with Plugin Ongkos Kirim? Let's check our Frequently Asked Questions before you ask something to our support.", 'pok' ); ?></p>
				</div>
				<div class="more-btn">
					<a href="https://forum.tonjoostudio.com/thread/plugin-ongkir-f-a-q-troubleshot/?utm_source=wp_wooongkir-premium&utm_medium=onboarding_overview&utm_campaign=upsell" class="button-primary">
						<?php esc_html_e( 'View FAQ', 'pok' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="col-fourth">
			<div class="more-content">
				<div class="more-text">
					<h3><?php esc_html_e( 'Support Forum', 'pok' ); ?></h3>
					<p><?php esc_html_e( 'We offer outstanding support through our forum. To get our support you need to register or login using Tonjoostudio account and start a thread.', 'pok' ); ?></p>
				</div>
				<div class="more-btn">
					<a href="https://forum.tonjoostudio.com/thread-category/woo-ongkir/?utm_source=wp_wooongkir-premium&utm_medium=onboarding_overview&utm_campaign=upsell" class="button-primary">
						<?php esc_html_e( 'Visit Forum', 'pok' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="col-fourth">
			<div class="more-content">
				<div class="more-text">
					<h3><?php esc_html_e( 'Inaccurate Costs?', 'pok' ); ?></h3>
					<p><?php esc_html_e( 'Our data are updated semi-automatically, some shipping costs may be inaccurate. Help us to find it by reporting them through provided form.', 'pok' ); ?></p>
				</div>
				<div class="more-btn">
					<a href="https://airtable.com/shry2kKGHmFyLLyvR" class="button-primary">
						<?php esc_html_e( 'Report', 'pok' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'pok_about_overview_after' ); ?>
