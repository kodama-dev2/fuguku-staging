<div class="setting-row">
	<div class="setting-index">
		<label for="pok-default_weight"><?php esc_html_e( 'Default Shipping Weight (kg)', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Default shipping weight if total weight is unknown.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<input id="pok-default_weight" type="number" name="pok_setting[default_weight]" value="<?php echo esc_attr( $settings['default_weight'] ); ?>" step="0.1" min="0.1">
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Round Shipping Weight', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'How shipping weight will be rounded', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<?php if ( 'rajaongkir' === $settings['base_api'] || 'rajaongkir2' === $settings['base_api'] ) : ?>
				<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-no" <?php echo 'no' === $settings['round_weight'] ? 'checked' : ''; ?> value="no">
				<label for="pok-round_weight-no"><?php esc_html_e( "Don't Round", 'pok' ); ?></label>
			<?php endif; ?>
			<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-auto" <?php echo 'auto' === $settings['round_weight'] ? 'checked' : ''; ?> value="auto">
			<label for="pok-round_weight-auto"><?php esc_html_e( 'Auto Round', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-ceil" <?php echo 'ceil' === $settings['round_weight'] ? 'checked' : ''; ?> value="ceil">
			<label for="pok-round_weight-ceil"><?php esc_html_e( 'Round Up', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-floor" <?php echo 'floor' === $settings['round_weight'] ? 'checked' : ''; ?> value="floor">
			<label for="pok-round_weight-floor"><?php esc_html_e( 'Round Down', 'pok' ); ?></label>
		</div>
		<div class="setting-sub-option options-round-weight <?php echo 'auto' === $settings['round_weight'] ? 'show' : ''; ?>">
			<label for="pok-round_weight_tolerance"><?php esc_html_e( 'Weight Tolerance (gram)', 'pok' ); ?></label>
			<input id="pok-round_weight_tolerance" name="pok_setting[round_weight_tolerance]" type="number" value="<?php echo esc_attr( $settings['round_weight_tolerance'] ); ?>" min="0" max="1000">
			<p class="helper"><?php esc_html_e( 'If shipping weight is less equal to the limit, it will rounding down. Otherwise, it will be rounding up.', 'pok' ); ?></p>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Use Volume Metric Calculation', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Calculate shipping weight using product dimension. If the dimension is not set, it will use weight instead.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[enable_volume_calculation]" id="pok-enable_volume_calculation-no" <?php echo 'no' === $settings['enable_volume_calculation'] ? 'checked' : ''; ?> value="no">
			<label for="pok-enable_volume_calculation-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[enable_volume_calculation]" id="pok-enable_volume_calculation-yes" <?php echo 'yes' === $settings['enable_volume_calculation'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-enable_volume_calculation-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
		<p class="helper"><?php esc_html_e( 'The weight of the product will calculated with the formula:', 'pok' ); ?> <code>( <?php esc_html_e( 'length', 'pok' ); ?> * <?php esc_html_e( 'width', 'pok' ); ?> * <?php esc_html_e( 'height', 'pok' ); ?> ) / 6000</code></p>
	</div>
</div>

<?php do_action( 'pok_setting_weight', $settings ); ?>