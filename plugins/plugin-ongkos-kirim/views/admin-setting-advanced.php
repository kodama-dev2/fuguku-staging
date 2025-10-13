<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Cache Expiration (in hours)', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Cache expiration is a feature that keep your shipping costs data or addresses data as a stored cache. This feature will significally increase your website speed.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<label for="pok-cache_expiration_costs"><?php esc_html_e( 'Shipping Costs Data', 'pok' ); ?></label>
		<input type="number" id="pok-cache_expiration_costs" name="pok_setting[cache_expiration_costs]" value="<?php echo esc_attr( $settings['cache_expiration_costs'] ); ?>" min="1">
		<br>
		<label for="pok-cache_expiration_addresses"><?php esc_html_e( 'Addresses Data (province list, city list, etc)', 'pok' ); ?></label>
		<input type="number" id="pok-cache_expiration_addresses" name="pok_setting[cache_expiration_addresses]" value="<?php echo esc_attr( $settings['cache_expiration_addresses'] ); ?>" min="1">
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Flush Cache', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Delete all cached data', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=pok_setting' ), 'flush_cache', 'pok_action' ) ); ?>" class="button" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'pok' ); ?>')">Flush Cache</a>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Reset Configuration', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Delete all saved configuration just like fresh install.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=pok_setting' ), 'reset', 'pok_action' ) ); ?>" class="button" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'pok' ); ?>')">Reset</a>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Override Default Location to Indonesia', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Override default location to Indonesia in checkout page.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[override_default_location_to_indonesia]" id="pok-override_default_location_to_indonesia-no" <?php echo 'no' === $settings['override_default_location_to_indonesia'] ? 'checked' : ''; ?> value="no">
			<label for="pok-override_default_location_to_indonesia-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[override_default_location_to_indonesia]" id="pok-override_default_location_to_indonesia-yes" <?php echo 'yes' === $settings['override_default_location_to_indonesia'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-override_default_location_to_indonesia-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Debug Mode', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'You will be able to access the error logs on the new menu tab that will show up after you enable this option. Be careful, this option will disable caching feature!', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[debug_mode]" id="pok-debug_mode-no" <?php echo 'no' === $settings['debug_mode'] ? 'checked' : ''; ?> value="no">
			<label for="pok-debug_mode-no"><?php esc_html_e( 'Disable', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[debug_mode]" id="pok-debug_mode-yes" <?php echo 'yes' === $settings['debug_mode'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-debug_mode-yes"><?php esc_html_e( 'Enable', 'pok' ); ?></label>
		</div>
	</div>
</div>

<?php do_action( 'pok_setting_advanced', $settings ); ?>