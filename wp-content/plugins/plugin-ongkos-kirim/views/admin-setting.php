<form action="" method="post" class="pok-setting-form">
	<div class="pok-setting" id="pok-setting">

		<ul class="sections-nav">
			<?php foreach ( $sections as $key => $section ) : ?>
				<li class="tab tab-<?php echo esc_attr( $key ) ?>"><a href="#tab-<?php echo esc_attr( $key ) ?>"><img src="<?php echo esc_url( $section['icon'] ) ?>" alt="icon-<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $section['title'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>

		<div class="sections-container">
			<?php foreach ( $sections as $key => $section ) : ?>
				<div class="section section-<?php echo esc_attr( $key ) ?>" id="tab-<?php echo esc_attr( $key ) ?>">
					<?php include_once $section['template']; ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
	<div class="pok-setting-actions">
		<?php wp_nonce_field( 'update_setting', 'pok_action' ); ?>
		<input type="submit" value="<?php esc_attr_e( 'Save Settings', 'pok' ); ?>" class="button button-primary">
	</div>
</form>
