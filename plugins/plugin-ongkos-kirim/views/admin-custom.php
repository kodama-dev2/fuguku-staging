<?php if ( 'rajaongkir2' === $this->setting->get('base_api') ) : ?>
	<div class="pok-setting-notice">
		<p><?php esc_html_e( 'Sorry, this feature is unavailable with API Rajaongkir v2', 'pok' ); ?></p>
	</div>
<?php else: ?>

<form action="" method="post" class="pok-custom-form">
	<div class="pok-setting">
		<div class="sections-container">
			<div class="setting-row">
				<div class="setting-index">
					<label for="pok-enable"><?php esc_html_e( 'Custom Courier Name', 'pok' ); ?></label>
					<p class="helper"><?php esc_html_e( 'Your custom courier name', 'pok' ); ?></p>
				</div>
				<div class="setting-option">
					<input type="text" value="<?php echo esc_attr( $settings['custom_cost_courier'] ); ?>" name="pok_setting[custom_cost_courier]"/>
				</div>
			</div>
			<div class="setting-row">
				<div class="setting-index">
					<label for="pok-enable"><?php esc_html_e( 'Custom Shipping Cost Type', 'pok' ); ?></label>
					<p class="helper"><?php esc_html_e( 'How your custom costs will be displayed on the checkout page', 'pok' ) ?></p>
				</div>
				<div class="setting-option">
					<div class="toggle">
						<input type="radio" name="pok_setting[custom_cost_type]" id="pok-custom_cost_type-append" <?php echo 'append' === $settings['custom_cost_type'] ? 'checked' : ''; ?> value="append">
						<label for="pok-custom_cost_type-append"><?php esc_html_e( 'Append', 'pok' ); ?></label>
						<input type="radio" name="pok_setting[custom_cost_type]" id="pok-custom_cost_type-replace" <?php echo 'replace' === $settings['custom_cost_type'] ? 'checked' : ''; ?> value="replace">
						<label for="pok-custom_cost_type-replace"><?php esc_html_e( 'Replace', 'pok' ); ?></label>
					</div>
					<p class="helper"><?php echo wp_kses_post( __( 'Append: Append custom shipping options to original options. <br/>Replace: Replace original options with custom cost if address match.', 'pok' ) ); ?></p>
				</div>
			</div>
		</div>
	</div>

	<?php
		$max_row = 100;
	if ( function_exists( 'ini_get' ) ) {
		$max_input_vars = ini_get( 'max_input_vars' );
		$max_row = floor( ( $max_input_vars / 11 ) - 2 );
	}
	?>
<!-- 	<div class="info-warning">
		<p><?php printf( __( 'Based on your current server configuration, only %1$d custom shipping costs are allowed to input. To increase this limitation please refer to this <a href="%2$s" target="blank">link</a>.', 'pok' ), $max_row, 'https://forum.tonjoostudio.com/thread/plugin-ongkir-f-a-q-troubleshot/' ); ?></p>
	</div> -->

	<div class="pok-custom-cost">
		<table class="wc_shipping widefat wp-list-table custom-costs striped">
			<thead>
				<tr>
					<th colspan="3" width="46%"><?php esc_html_e( 'Destination', 'pok' ); ?></th>
					<th width="10%"><?php esc_html_e( 'Courier', 'pok' ); ?></th>
					<th width="10%"><?php esc_html_e( 'Service Name', 'pok' ); ?></th>
					<th width="10%"><?php esc_html_e( 'Cost per Kg', 'pok' ); ?></th>
					<th width="14%" colspan="2"><?php esc_html_e( 'Min/Max Weight (Kg)', 'pok' ); ?></th>
					<th width="10%" class="nowrap"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$key = 0;
				if ( ! empty( $costs ) ) {
					foreach ( $costs as $key => $cost ) {
						?>
						<tr>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['province_id'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][province_id]">
								<input type="hidden" value="<?php echo esc_attr( $cost['province_text'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][province_text]">
								<?php echo esc_html( $cost['province_text'] ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['city_id'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][city_id]">
								<input type="hidden" value="<?php echo esc_attr( $cost['city_text'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][city_text]">
								<?php echo esc_html( $cost['city_text'] ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['district_id'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][district_id]">
								<input type="hidden" value="<?php echo esc_attr( $cost['district_text'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][district_text]">
								<?php echo esc_html( $cost['district_text'] ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['courier'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][courier]">
								<?php echo esc_html( 'custom' === $cost['courier'] ? $settings['custom_cost_courier'] : $this->helper->get_courier_name( $cost['courier'] ) ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['package_name'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][package_name]">
								<?php echo esc_html( $cost['package_name'] ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['cost'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][cost]">
								<?php echo wp_kses_post( wc_price( $cost['cost'] ) ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['min'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][min]">
								<?php echo ! isset( $cost['min'] ) || empty( $cost['min'] ) ? 0 : esc_html( $cost['min'] ); ?>
							</td>
							<td>
								<input type="hidden" value="<?php echo esc_attr( $cost['max'] ); ?>" name="custom_cost[<?php echo esc_attr( $key ); ?>][max]">
								<?php echo ! isset( $cost['max'] ) || empty( $cost['max'] ) ? '&infin;' : esc_html( $cost['max'] ); ?>
							</td>
							<td>
								<a class="remove-manual"><?php esc_html_e( 'Delete', 'pok' ); ?></a>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
			<tfoot>
				<tr>
					<td width="20%">
						<select id="select_province">
							<option value="*"><?php esc_html_e( 'All Province', 'pok' ); ?></option>
							<?php if ( ! empty( $provinces ) ) : ?>
								<?php foreach ( $provinces as $province_id => $province ) : ?>
									<option value="<?php echo esc_attr( $province_id ); ?>"><?php echo esc_html( $province ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</td>
					<td width="15%">
						<select id="select_city">
							<option value="*"><?php esc_html_e( 'All City', 'pok' ); ?></option>
						</select>
					</td>
					<td width="15%">
						<select id="select_district">
							<option value="*"><?php esc_html_e( 'All District', 'pok' ); ?></option>
						</select>
					</td>
					<td>
						<select id="select_courier">
							<option value="custom"><?php echo ! empty( $settings['custom_cost_courier'] ) ? esc_html( $settings['custom_cost_courier'] ) : 'custom'; ?></option>
							<?php if ( ! empty( $couriers ) ) : ?>
								<?php foreach ( $couriers as $c ) : ?>
									<option value="<?php echo esc_attr( $c ); ?>"><?php echo esc_html( $this->helper->get_courier_name( $c ) ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</td>
					<td>
						<input id="input_package_name" type="text">
					</td>
					<td>
						<input id="input_cost" type="number" min="0" value="0" step="0.1">
					</td>
					<td>
						<input id="input_min" type="number" min="0" value="0" step="1">
					</td>
					<td>
						<input id="input_max" type="number" min="0" value="0" step="1">
					</td>
					<td>
						<?php if ( $key <= $max_row ) : ?>
							<button type="button" class="button button-primary" id="add-cost"><?php esc_html_e( 'Add', 'pok' ); ?></button>
						<?php else : ?>
							<?php esc_html_e( 'Limit reached', 'pok' ); ?>
						<?php endif; ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<br>
	<?php wp_nonce_field( 'update_custom_costs', 'pok_action' ); ?>
	<input type="submit" value="<?php esc_attr_e( 'Save Custom Costs', 'pok' ); ?>" class="button button-primary">
</form>
<?php endif; ?>