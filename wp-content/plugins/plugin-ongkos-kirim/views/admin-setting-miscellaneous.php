<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Show Shipping Calculator on Product Page', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Show shipping cost estimation calculator on product tabs', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<?php if ( 'rajaongkir2' !== $settings['base_api'] ) : ?>
			<div class="toggle">
				<input type="radio" name="pok_setting[show_shipping_estimation]" id="pok-show_shipping_estimation-no" <?php echo 'no' === $settings['show_shipping_estimation'] ? 'checked' : ''; ?> value="no">
				<label for="pok-show_shipping_estimation-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
				<input type="radio" name="pok_setting[show_shipping_estimation]" id="pok-show_shipping_estimation-yes" <?php echo 'yes' === $settings['show_shipping_estimation'] ? 'checked' : ''; ?> value="yes">
				<label for="pok-show_shipping_estimation-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
			</div>
			<p class="helper"><?php printf( __( "Or simply use %s shortcode on the product description. But make sure to set this option to 'No' first.", 'pok' ), '<code>[pok_shipping_calculator]</code>' ); ?></p>
		<?php else: ?>
			<p class="helper" style="margin:0;"><?php esc_html_e( 'This option is not available on API Rajaongkir v2.', 'pok' ) ?></p>
		<?php endif; ?>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Add Unique Number on Checkout', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Add unique number to total purchase to easily differ an order from another', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="toggle">
			<input type="radio" name="pok_setting[unique_number]" id="pok-unique_number-no" <?php echo 'no' === $settings['unique_number'] ? 'checked' : ''; ?> value="no">
			<label for="pok-unique_number-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
			<input type="radio" name="pok_setting[unique_number]" id="pok-unique_number-yes" <?php echo 'yes' === $settings['unique_number'] ? 'checked' : ''; ?> value="yes">
			<label for="pok-unique_number-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
		</div>
		<div class="setting-sub-option options-unique-number <?php echo 'yes' === $settings['unique_number'] ? 'show' : ''; ?>">
			<label for="pok-unique_number_length"><?php esc_html_e( 'Unique Number Length', 'pok' ); ?></label>
			<select name="pok_setting[unique_number_length]" id="pok-unique_number_length">
				<?php
				$lengths = array(
					1 	=> 'x (0-9)',
					2 	=> 'xx (0-99)',
					3 	=> 'xxx (0-999)',
					10	=> '0.x (0-0.9)',
					20	=> '0.xx (0-0.99)',
					30	=> '0.xxx (0-0.999)'
				);
				foreach ( $lengths as $key => $label ) {
					?>
					<option <?php echo $key === intval( $settings['unique_number_length'] ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php
				}
				?>
			</select>
			<p class="helper"><?php esc_html_e( 'Length of you unique number.', 'pok' ); ?></p>
		</div>
	</div>
</div>

<?php do_action( 'pok_setting_miscellaneous', $settings ); ?>