<div class="setting-row">
	<div class="setting-index">
		<label for="pok-enable"><?php esc_html_e( 'Enabled', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Enable this shipping method?', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[enable]" id="pok-enable-no" <?php echo 'no' === $settings['enable'] ? 'checked' : ''; ?> value="no">
			<label for="pok-enable-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[enable]" id="pok-enable-yes" <?php echo 'yes' === $settings['enable'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-enable-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
	</div>
</div>

<div class="setting-row">
	<div class="setting-index">
		<label for="pok-base_api"><?php esc_html_e( 'Base API', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Use our default premium API, or Rajaongkir API ', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[base_api]" id="pok-base_api-nusantara" <?php echo 'nusantara' === $settings['base_api'] ? 'checked' : ''; ?> value="nusantara">
			<label for="pok-base_api-nusantara"><?php esc_html_e( 'Tonjoo', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[base_api]" id="pok-base_api-rajaongkir" <?php echo 'rajaongkir' === $settings['base_api'] ? 'checked' : ''; ?> value="rajaongkir">
			<label for="pok-base_api-rajaongkir"><?php esc_html_e( 'Rajaongkir', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[base_api]" id="pok-base_api-rajaongkir2" <?php echo 'rajaongkir2' === $settings['base_api'] ? 'checked' : ''; ?> value="rajaongkir2">
			<label for="pok-base_api-rajaongkir2"><?php esc_html_e( 'Rajaongkir V2', 'pok' ); ?></label>
		</div>
		<div class="setting-sub-option rajakongkir-api-fields <?php echo 'rajaongkir' === $settings['base_api'] ? 'show' : ''; ?>">
			<label class="field-type"><?php esc_html_e( 'Type', 'pok' ); ?>
				<select name="pok_setting[rajaongkir_type]">
					<option value="starter" <?php echo 'starter' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Starter</option>
					<option value="basic" <?php echo 'basic' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Basic</option>
					<option value="pro" <?php echo 'pro' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Pro</option>
				</select>
			</label>
			<label class="field-key"><?php esc_html_e( 'API Key', 'pok' ); ?>
				<input type="text" name="pok_setting[rajaongkir_key]" value="<?php echo esc_attr( $settings['rajaongkir_key'] ); ?>">
			</label>
			<div class="check">
				<button type="button" id="set-rajaongkir-key" class="button button-secondary"><?php esc_html_e( 'Check Rajaongkir Status', 'pok' ); ?></button>
				<span class="rajaongkir-key-response <?php echo $settings['rajaongkir_status'][0] ? 'success' : ''; ?>">
					<?php
					if ( $settings['rajaongkir_status'][0] ) {
						esc_html_e( 'API is active', 'pok' );
					} else {
						esc_html_e( 'API is inactive', 'pok' );
					}
					?>
				</span>
			</div>
		</div>
		<div class="setting-sub-option rajakongkir2-api-fields <?php echo 'rajaongkir2' === $settings['base_api'] ? 'show' : ''; ?>">
			<label class="field-key"><?php esc_html_e( 'API Key', 'pok' ); ?>
				<input type="text" name="pok_setting[rajaongkir2_key]" value="<?php echo esc_attr( $settings['rajaongkir2_key'] ); ?>">
			</label>
			<div class="check">
				<button type="button" id="set-rajaongkir2-key" class="button button-secondary"><?php esc_html_e( 'Check Rajaongkir Status', 'pok' ); ?></button>
				<span class="rajaongkir2-key-response <?php echo $settings['rajaongkir2_status'][0] ? 'success' : ''; ?>">
					<?php
					if ( $settings['rajaongkir2_status'][0] ) {
						esc_html_e( 'API is active', 'pok' );
					} else {
						esc_html_e( 'API is inactive', 'pok' );
					}
					?>
				</span>
			</div>
		</div>
		<p class="helper">
			<?php 
				if ( $this->helper->is_multi_vendor_addon_active() ) {
					esc_html_e( 'Switching the Base API will impact the deletion of previously stored data, including: Store Location (including the location of each vendor), Custom Shipping Costs, Customer Address Data, Courier Service Filters, Custom Service Names and Shipping Cost Data stored in the cache. We did this removal to adjust the chosen API because each API provides different courier and city data. So be wise in switching the Base API, only make changes if it is really needed.', 'pok' );
				} else {
					esc_html_e( 'Switching the Base API will impact the deletion of previously stored data, including: Store Location, Custom Shipping Costs, Customer Address Data, Courier Service Filters, Custom Service Names and Shipping Cost Data stored in the cache. We did this removal to adjust the chosen API because each API provides different courier and city data. So be wise in switching the Base API, only make changes if it is really needed.', 'pok' );
				}
			?>
		</p>
	</div>
</div>

<?php if ( $pok_helper->is_admin_active() ) : ?>
	<div class="setting-row <?php echo empty( $settings['store_location'] ) || ! isset( $settings['store_location'][0] ) ? 'setting-error' : ''; ?>">
		<div class="setting-index">
			<label for="pok-store_location"><?php esc_html_e( 'Store Location', 'pok' ); ?></label>
			<p class="helper"><?php esc_html_e( 'Location of your store', 'pok' ); ?></p>
		</div>
		<div class="setting-option">
			<?php if ( 'rajaongkir' === $settings['base_api'] ) : ?>
				<select name="pok_setting[store_location][]" id="pok-store_location" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pok' ); ?>">
					<option value=""><?php esc_html_e( 'Select your store location', 'pok' ); ?></option>
					<?php foreach ( $cities as $city ) : ?>
						<option value="<?php echo esc_attr( $city->city_id ); ?>" <?php echo ! empty( $settings['store_location'] ) && intval( $settings['store_location'][0] ) === intval( $city->city_id ) ? 'selected' : ''; ?>><?php echo esc_html( ( 'Kabupaten' === $city->type ? 'Kab. ' : 'Kota ' ) . $city->city_name . ', ' . $city->province ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( 'rajaongkir2' === $settings['base_api'] ) : ?>
				<select name="pok_setting[store_location][0]" id="pok-store_location" class="select2-ajax rajaongkir2-store-location" placeholder="<?php esc_attr_e( 'Select your store location', 'pok' ); ?>" data-action="pok_search_simple_address" data-nonce="<?php echo wp_create_nonce( 'search_city' ) ;?>">
					<?php if ( ! empty( $settings['store_location'] ) && ! empty( $settings['store_location'][0] ) && ! empty( $settings['store_location'][1] ) ) : ?>
						<option value="<?php echo esc_attr( $settings['store_location'][0] ) ?>"><?php echo esc_html ( $settings['store_location'][1] ); ?></option>
					<?php else: ?>
						<option value=""><?php esc_html_e( 'Select your store location', 'pok' ); ?></option>
					<?php endif; ?>
				</select>
				<input type="hidden" name="pok_setting[store_location][1]" value="<?php echo ! empty( $settings['store_location'] ) && isset( $settings['store_location'][1] ) ? esc_html( $settings['store_location'][1] ) : ''; ?>">
			<?php else : ?>
				<select name="pok_setting[store_location][]" id="pok-store_location" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pok' ); ?>">
					<?php foreach ( $origins as $city ) : ?>
						<option value="<?php echo esc_attr( $city->id ); ?>" <?php echo ! empty( $settings['store_location'] ) && intval( $settings['store_location'][0] ) === intval( $city->id ) ? 'selected' : ''; ?>><?php echo esc_html( $city->name ); ?></option>
					<?php endforeach; ?>
					?>
				</select>
			<?php endif; ?>
			<?php if ( $this->helper->is_multi_vendor_addon_active() ) : ?>
				<p class="helper"><?php printf( __( 'This is your main store location. To manage vendor store locations, <a href="%s">click here</a>.', 'pok' ), admin_url( 'admin.php?page=pok_setting&tab=vendor' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<?php do_action( 'pok_setting_basic', $settings ); ?>