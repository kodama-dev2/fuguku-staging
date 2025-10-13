<form action="" method="page" method="post" class="pok-checker-form" id="form-checker">
	<?php if ( $this->helper->is_admin_active() ) : ?>
		<div class="pok-checker">
			<div class="sidebar">
				<input type="hidden" name="license_type" value="<?php echo $this->helper->get_license_type() ?>">
				<table>
					<tbody>
						<tr>
							<td class="index"><?php esc_html_e( 'Base API', 'pok' ); ?></td>
							<td class="value"><?php echo esc_html( 'nusantara' === $settings['base_api'] ? 'Tonjoo' : 'Rajaongkir' ); ?></td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'From', 'pok' ); ?></td>
							<td class="value">
								<?php if ( 'rajaongkir' === $settings['base_api'] ) : ?>
									<select name="origin" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pok' ); ?>">
										<option value=""><?php esc_html_e( 'Select your store location', 'pok' ); ?></option>
										<?php foreach ( $cities as $city ) : ?>
											<option value="<?php echo esc_attr( $city->city_id ); ?>" <?php echo ! empty( $settings['store_location'] ) && $settings['store_location'][0] === $city->city_id ? 'selected' : ''; ?>><?php echo esc_html( ( 'Kabupaten' === $city->type ? 'Kab. ' : 'Kota ' ) . $city->city_name . ', ' . $city->province ); ?></option>
										<?php endforeach; ?>
									</select>
								<?php elseif ( 'rajaongkir2' === $settings['base_api'] ) : ?>
									<select name="origin" class="select2-ajax rajaongkir2-store-location" placeholder="<?php esc_attr_e( 'Select your store location', 'pok' ); ?>" data-action="pok_search_simple_address" data-nonce="<?php echo wp_create_nonce( 'search_city' ) ;?>">
										<?php if ( ! empty( $settings['store_location'] ) && ! empty( $settings['store_location'][0] ) && ! empty( $settings['store_location'][1] ) ) : ?>
											<option value="<?php echo esc_attr( $settings['store_location'][0] ) ?>"><?php echo esc_html ( $settings['store_location'][1] ); ?></option>
										<?php else: ?>
											<option value=""><?php esc_html_e( 'Select your store location', 'pok' ); ?></option>
										<?php endif; ?>
									</select>
								<?php else : ?>
									<select name="origin" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pok' ); ?>">
										<?php foreach ( $origins as $city ) : ?>
											<option value="<?php echo esc_attr( $city->id ); ?>" <?php echo ! empty( $settings['store_location'] ) && $settings['store_location'][0] === $city->id ? 'selected' : ''; ?>><?php echo esc_html( $city->name ); ?></option>
										<?php endforeach; ?>
										?>
									</select>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Destination', 'pok' ); ?></td>
							<td class="value">
								<?php if ( 'rajaongkir2' === $settings['base_api'] ) : ?>
									<select name="district" class="select2-ajax rajaongkir2-store-location" placeholder="<?php esc_attr_e( 'Select your store location', 'pok' ); ?>" data-action="pok_search_simple_address" data-nonce="<?php echo wp_create_nonce( 'search_city' ) ;?>">
										<option value=""><?php esc_html_e( 'Select destination', 'pok' ); ?></option>
									</select>
								<?php else: ?>
									<select name="province" id="select_province" required>
										<option value=""><?php esc_html_e( 'Select province', 'pok' ); ?></option>
										<?php foreach ( $provinces as $key => $name ) : ?>
											<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></option>
										<?php endforeach; ?>
									</select>
									<select name="city" id="select_city" required>
										<option value=""><?php esc_html_e( 'Select city', 'pok' ); ?></option>
									</select>
									<?php if ( 'pro' === $this->helper->get_license_type() ) : ?>
										<select name="district" id="select_district" required>
											<option value=""><?php esc_html_e( 'Select district', 'pok' ); ?></option>
										</select>
									<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Weight (kg)', 'pok' ); ?></td>
							<td class="value">
								<input type="number" name="weight" min="0" step="0.1" value="<?php echo isset( $_GET['weight'] ) ? floatval( $_GET['weight'] ) : 1; ?>" required>
							</td>
						</tr>
						<tr>
							<td class="index"><?php esc_html_e( 'Courier', 'pok' ); ?></td>
							<td class="value">
								<div class="couriers-wrapper">
									<?php foreach ( $couriers as $courier ) : ?>
										<label><input type="checkbox" name="courier[]" value="<?php echo esc_attr( $courier ); ?>" <?php echo in_array( $courier, $couriers, true ) && in_array( $courier, $settings['couriers'], true ) ? 'checked' : ''; ?>> <?php echo esc_html( $this->helper->get_courier_name( $courier ) ); ?></label>
									<?php endforeach; ?>
								</div>
							</td>
						</tr>
						<!-- <tr>
							<td class="index"><?php esc_html_e( 'Insurance', 'pok' ); ?></td>
							<td class="value">
								<input type="checkbox" name="insurance" value="yes" id="check-insurance">
							</td>
						</tr> -->
						<tr class="total <?php echo isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] ? 'show' : ''; ?>">
							<td class="index"><?php esc_html_e( 'Total Price', 'pok' ); ?></td>
							<td class="value">
								<input type="number" min="0" name="total" value="<?php echo isset( $_GET['total'] ) ? floatval( $_GET['total'] ) : 0; ?>">
							</td>
						</tr>
					</tbody>
				</table>
				<div class="submit">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting&tab=checker' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'pok' ); ?></a>
					<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Get cost', 'pok' ); ?>">
				</div>
			</div>
			<div class="idle">
				<h4 class="start"><?php esc_html_e( 'Input shipping information and click Get Cost to get the costs', 'pok' ) ?></h4>
				<h4 class="loading"><?php esc_html_e( 'Loading...', 'pok' ) ?></h4>
			</div>
			<div class="result">

			</div>
		</div>
		<?php else : ?>
			<div class="pok-notice">
				<p><?php echo wp_kses_post( __( 'Shipping checker will show up here after you activate the license of Rajaongkir.', 'pok' ) ); ?></p>
			</div>
		<?php endif; ?>
</form>
<script>
	jQuery(function($) {
		$('#select_city').on('setvalue', function() {
			var value = '<?php echo isset( $_GET['city'] ) ? intval( $_GET['city'] ) : '0'; ?>';
			$('#select_city option').each(function() {
				if ( $(this).attr('value') == value ) {
					$('#select_city').val(value);
				}
			});
		});
		$('#select_district').on('setvalue', function() {
			var value = '<?php echo isset( $_GET['district'] ) ? intval( $_GET['district'] ) : '0'; ?>';
			$('#select_district option').each(function() {
				if ( $(this).attr('value') == value ) {
					$('#select_district').val(value);
				}
			});
		});
	});
</script>
