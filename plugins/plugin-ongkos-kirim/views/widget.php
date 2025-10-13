<div class="pok-widget-content">
	<p class="row">
		<label><?php esc_html_e( 'Weight (kg)', 'pok' ); ?></label>
		<input type="number" name="weight" value="1" step="0.1" min="0">
	</p>
	<p class="row">
		<label><?php esc_html_e( 'Destination Province', 'pok' ); ?></label>
		<select class="input-select" name="province">
			<option value=""><?php esc_html_e( 'Select province', 'pok' ); ?></option>
			<?php foreach ( $provinces as $p ) : ?>
				<option value="<?php echo esc_attr( $p->id ); ?>"><?php echo esc_html( $p->nama ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class="row">
		<label><?php esc_html_e( 'Destination City', 'pok' ); ?></label>
		<select name="city">
			<option value=""><?php esc_html_e( 'Select city', 'pok' ); ?></option>
		</select>
	</p>
	<p class="row">
		<label><?php esc_html_e( 'Destination District', 'pok' ); ?></label>
		<select name="district">
			<option value=""><?php esc_html_e( 'Select district', 'pok' ); ?></option>
		</select>
	</p>
	<p class="row">
		<button class="button get-cost" disabled type="button"><?php esc_html_e( 'Get Cost', 'pok' ); ?></button>
	</p>
</div>
