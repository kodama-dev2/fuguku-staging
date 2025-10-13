<div class="setting-row <?php echo empty( $settings['couriers'] ) ? 'setting-error' : ''; ?>">
	<div class="setting-index">
		<label for="pok-couriers"><?php esc_html_e( 'Couriers', 'pok' ); ?></label>
		<p class="helper">
			<?php
			esc_html_e( 'Select couriers to display', 'pok' );
			?>
		</p>
	</div>
	<div class="setting-option">
		<div class="courier-options">
			<?php
			foreach ( $couriers as $courier ) {
				?>
				<input type="checkbox" value="<?php echo esc_attr( $courier ); ?>" name="pok_setting[couriers][]" id="setting-cour-<?php echo esc_attr( $courier ); ?>" <?php echo in_array( $courier, $couriers, true ) && in_array( $courier, $settings['couriers'], true ) ? 'checked' : ''; ?> <?php echo ! in_array( $courier, $couriers, true ) ? 'disabled' : ''; ?>>
				<label for="setting-cour-<?php echo esc_attr( $courier ); ?>">
					<img src="<?php echo esc_url( POK_PLUGIN_URL . '/assets/img/logo-' . $courier . '.png' ) ?>" alt="<?php echo esc_attr( $this->helper->get_courier_name( $courier ) ); ?>" title="<?php echo esc_attr( $this->helper->get_courier_name( $courier ) ); ?>">
				</label>
				<?php
			}
			?>
		</div>
		<p class="helper">
			<?php
			printf( __( 'Available couriers depends on the base API you choose. <a href="%s">Click here</a> to learn more.', 'pok' ), 'https://pluginongkoskirim.com/kurir/' );
			if ( 'rajaongkir' === $settings['base_api'] && 'starter' !== $settings['rajaongkir_type'] ) {
				echo ' ';
				esc_html_e( 'We recommend to use only 3 of these couriers to optimize the load speed', 'pok' );
			}
			?>
		</p>
	</div>
</div>

<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Filter Courier Services', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Use specific services for each courier', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[specific_service]" id="pok-specific_service-no" <?php echo 'no' === $settings['specific_service'] ? 'checked' : ''; ?> value="no">
			<label for="pok-specific_service-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[specific_service]" id="pok-specific_service-yes" <?php echo 'yes' === $settings['specific_service'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-specific_service-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
		<div class="setting-sub-option options-specific-service <?php echo 'yes' === $settings['specific_service'] ? 'show' : ''; ?>">
			<?php foreach ( $services as $courier => $courier_services ) : ?>
				<div class="options-specific-service-<?php echo esc_attr( $courier ); ?> service-options">
					<h5><?php echo esc_html( $this->helper->get_courier_name( $courier ) ); ?></h5>
					<p>
						<?php
							$selected = array();
							foreach ( $courier_services as $key => $service ) {
								if ( in_array( $courier . '-' . $key, $settings['specific_service_option'], true ) ) {
									$selected[] = $service['name'];
								}
							}
							if ( ! empty( $selected ) ) {
								echo implode( ", ", $selected );
							} else {
								esc_html_e( "No service selected, click here to select services", "pok" );
							}
						?>
					</p>
					<div class="courier-service-options">
						<?php
						foreach ( $courier_services as $key => $service ) {
							?>
							<input type="checkbox" value="<?php echo esc_attr( $courier . '-' . $key ); ?>" data-short="<?php echo esc_attr( $service['name'] ); ?>" name="pok_setting[specific_service_option][]" id="setting-service-<?php echo esc_attr( $courier . '-' . $key ); ?>" <?php echo in_array( $courier . '-' . $key, $settings['specific_service_option'], true ) ? 'checked' : ''; ?>>
							<label for="setting-service-<?php echo esc_attr( $courier . '-' . $key ); ?>"><?php echo esc_html( $service['name'] ); ?><?php echo ! empty( $service['desc'] ) ? ' - ' . esc_html( $service['desc'] ) : ''; ?></label>
							<?php
						}
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Only Show Cargo Services When Minimum Weight is Reached', 'pok' ); ?></label>
		<p class="helper"><?php echo wp_kses_post( __( 'If minimum weight is not reached, cargo services will be hidden from the result', 'pok' ) ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[only_show_cargo_on_min_weight]" id="pok-only_show_cargo_on_min_weight-no" <?php echo 'no' === $settings['only_show_cargo_on_min_weight'] ? 'checked' : ''; ?> value="no">
			<label for="pok-only_show_cargo_on_min_weight-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[only_show_cargo_on_min_weight]" id="pok-only_show_cargo_on_min_weight-yes" <?php echo 'yes' === $settings['only_show_cargo_on_min_weight'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-only_show_cargo_on_min_weight-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
		<p class="helper"><?php echo wp_kses_post( sprintf( __( "Cargo services includes: %s.", 'pok' ), "JNE JTR, Pos Kargo Barang, Sicepat GOKIL, TIKI TRC, Wahana Kargo, Lion Parcel Bigpack, ID Express iDtruck, AnterAja Cargo" ) ); ?></p>
	</div>
</div>

<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Custom Service Name', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Change courier service name with your custom name.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="setting-repeater-wrapper markup-repeater <?php echo empty( $settings['custom_service_name'][ $settings['base_api'] ] ) ? 'empty' : ''; ?>">
			<div class="repeater-container">
				<div class="repeater-head">
					<div class="repeater-col">
						<?php esc_html_e( 'Courier', 'pok' ) ?>
					</div>
					<div class="repeater-col">
						<?php esc_html_e( 'Service Name', 'pok' ) ?>
					</div>
					<div class="repeater-col">
						<?php esc_html_e( 'Custom Name', 'pok' ) ?>
					</div>
					<div class="repeater-col">
					</div>
				</div>
				<?php foreach ( $settings['custom_service_name'][ $settings['base_api'] ] as $name_key => $name ) : ?>
					<div class="repeater-row" data-id="<?php echo esc_attr( $name_key ) ?>">
						<div class="repeater-col">
							<select name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][<?php echo esc_attr( $name_key ) ?>][courier]" class="custom-service-courier">
								<?php foreach ( $couriers as $courier ) : ?>
									<option <?php echo isset( $name['courier'] ) &&  $courier === $name['courier'] ? 'selected' : '' ?> value="<?php echo esc_attr( $courier ) ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="repeater-col">
							<select name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][<?php echo esc_attr( $name_key ) ?>][service]" class="custom-service-service">
								<?php if ( isset( $name['courier'] ) && isset( $services[ $name['courier'] ] ) ) : ?>
									<?php foreach ( $services[ $name['courier'] ] as $key => $service ) : ?>
										<option <?php echo isset( $name['service'] ) &&  $key === $name['service'] ? 'selected' : '' ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $service['name'] ) ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
						<div class="repeater-col">
							<input type="text" name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][<?php echo esc_attr( $name_key ) ?>][name]" value="<?php echo isset( $name['name'] ) ? esc_attr( $name['name'] ) : '' ?>">
						</div>
						<div class="repeater-col nowrap">
							<button type="button" class="delete-repeater-row button button-small"><span class="dashicons dashicons-trash"></span></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="repeater-base">
				<div class="repeater-col">
					<select name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][{id}][courier]" class="custom-service-courier" disabled>
						<option value=""><?php esc_html_e( 'Select Courier', 'pok' ) ?></option>
						<?php foreach ( $couriers as $courier ) : ?>
							<option value="<?php echo esc_attr( $courier ) ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="repeater-col">
					<select name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][{id}][service]" class="custom-service-service" disabled>
						<option value=""><?php esc_html_e( 'Select Service', 'pok' ) ?></option>
					</select>
				</div>
				<div class="repeater-col">
					<input type="text" name="pok_setting[custom_service_name][<?php echo esc_attr( $settings['base_api'] ) ?>][{id}][name]" value="" disabled>
				</div>
				<div class="repeater-col nowrap">
					<button type="button" class="delete-repeater-row button button-small"><span class="dashicons dashicons-trash"></span></button>
				</div>
			</div>
			<button type="button" id="add-custom-service" class="add-repeater-row button"><?php esc_html_e( 'Add Custom Name', 'pok' ) ?></button>
		</div>
	</div>
</div>

<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Enable International Shipping', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Show international shipping costs on checkout page', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<?php if ( 'rajaongkir' === $settings['base_api'] && 'starter' !== $settings['rajaongkir_type'] ) : ?>
			<div class="toggle">
				<input type="radio" name="pok_setting[international_shipping]" id="pok-international_shipping-no" <?php echo 'no' === $settings['international_shipping'] ? 'checked' : ''; ?> value="no">
				<label for="pok-international_shipping-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
				<input type="radio" name="pok_setting[international_shipping]" id="pok-international_shipping-yes" <?php echo 'yes' === $settings['international_shipping'] ? 'checked' : ''; ?> value="yes">
				<label for="pok-international_shipping-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
			</div>
		<?php else: ?>
			<p class="helper limited-feature" style="margin:0;"><?php esc_html_e( 'International shipping costs only available on API Rajaongkir with Basic or Pro type.', 'pok' ) ?></p>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'pok_setting_courier', $settings ); ?>