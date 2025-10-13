<div class="setting-row">
	<div class="setting-index">
		<label for="pok-enable_insurance"><?php esc_html_e( 'Shipping Insurance', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Add insurance fee to shipping cost', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<select name="pok_setting[enable_insurance]" id="pok-enable_insurance">
			<option <?php echo 'set' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="set"><?php esc_html_e( 'Only apply on specific product (can be set via edit product)', 'pok' ); ?></option>
			<option <?php echo 'yes' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Apply to all products', 'pok' ); ?></option>
			<option <?php echo 'no' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="no"><?php esc_html_e( 'Do not add insurance fee', 'pok' ); ?></option>
		</select>
		<div class="setting-sub-option options-enable-insurance <?php echo 'set' === $settings['enable_insurance'] || 'yes' === $settings['enable_insurance'] ? 'show' : ''; ?>">
			<label for="pok-insurance_application"><?php esc_html_e( 'How insurance fee will be applied to the cost?', 'pok' ); ?></label>
			<select name="pok_setting[insurance_application]" id="pok-insurance_application">
				<option <?php echo 'by_user' === $settings['insurance_application'] ? 'selected' : ''; ?> value="by_user"><?php esc_html_e( 'Let customer decide', 'pok' ); ?></option>
				<option <?php echo 'force' === $settings['insurance_application'] ? 'selected' : ''; ?> value="force"><?php esc_html_e( 'Always apply insurance fee', 'pok' ); ?></option>
			</select>
			<p class="helper">
				<?php esc_html_e( 'If you let customer to decide, a checkbox will be shown on checkout page to let customer to choose to add insurance fee or not.', 'pok' ); ?>
			</p>
		</div>
		<p class="helper"><?php printf( __( 'Each courier applies different rules for insurance calculations. For more info, <a href="%s">check here</a>.', 'pok' ), 'http://pustaka.tonjoostudio.com/plugins/woo-ongkir-manual/#section-shipping-insurance' ); ?></p>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label for="pok-enable_timber_packing"><?php esc_html_e( 'Timber Packing Fee', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'Add timber packing fee to shipping cost.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<select name="pok_setting[enable_timber_packing]" id="pok-enable_timber_packing">
			<option <?php echo 'set' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="set"><?php esc_html_e( 'Only apply on specific product (can be set via edit product)', 'pok' ); ?></option>
			<option <?php echo 'yes' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Apply to all products', 'pok' ); ?></option>
			<option <?php echo 'no' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="no"><?php esc_html_e( 'Do not add timber packing fee', 'pok' ); ?></option>
		</select>
		<div class="setting-sub-option options-enable-timber_packing <?php echo 'set' === $settings['enable_timber_packing'] || 'yes' === $settings['enable_timber_packing'] ? 'show' : ''; ?>">
			<label for="pok-timber_packing_multiplier"><?php esc_html_e( 'Shipping cost multiplier', 'pok' ); ?></label>
			<input type="number" name="pok_setting[timber_packing_multiplier]" id="pok-timber_packing_multiplier" value="<?php echo esc_attr( $settings['timber_packing_multiplier'] ); ?>" step="0.1" min="0">
			<p class="helper">
				<?php esc_html_e( 'The shipping cost multiplier is used to determine how much the timber packing fee is. The value "1" means the timber packing fee is equal to the selected shipping cost.', 'pok' ); ?>
			</p>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<label><?php esc_html_e( 'Shipping Cost Markup', 'pok' ); ?></label>
		<p class="helper"><?php esc_html_e( 'You can mark-up/mark-down your shipping cost based on your need.', 'pok' ); ?></p>
	</div>
	<div class="setting-option">
		<div class="setting-repeater-wrapper markup-repeater <?php echo empty( $settings['markup'] ) ? 'empty' : ''; ?>">
			<div class="repeater-container">
				<div class="repeater-head">
					<div class="repeater-col">
						<?php esc_html_e( 'Courier', 'pok' ) ?>
					</div>
					<div class="repeater-col">
						<?php esc_html_e( 'Service', 'pok' ) ?>
					</div>
					<div class="repeater-col">
						<?php esc_html_e( 'Cost Markup', 'pok' ) ?>
					</div>
					<div class="repeater-col">
					</div>
				</div>
				<?php foreach ( $settings['markup'] as $markup_key => $markup ) : ?>
					<div class="repeater-row" data-id="<?php echo esc_attr( $markup_key ) ?>">
						<div class="repeater-col">
							<select name="pok_setting[markup][<?php echo esc_attr( $markup_key ) ?>][courier]" class="markup-courier">
								<option <?php echo ! isset( $markup['courier'] ) || '' === $markup['courier'] ? 'selected' : '' ?> value=""><?php esc_html_e( 'All Courier', 'pok' ) ?></option>
								<?php foreach ( $settings['couriers'] as $courier ) : ?>
									<option <?php echo isset( $markup['courier'] ) &&  $courier === $markup['courier'] ? 'selected' : '' ?> value="<?php echo esc_attr( $courier ) ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ) ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="repeater-col">
							<select name="pok_setting[markup][<?php echo esc_attr( $markup_key ) ?>][service]" class="markup-service">
								<option <?php echo ! isset( $markup['service'] ) || '' === $markup['service'] ? 'selected' : '' ?> value=""><?php esc_html_e( 'All Service', 'pok' ) ?></option>
								<?php if ( isset( $markup['courier'] ) && isset( $services[ $markup['courier'] ] ) ) : ?>
									<?php foreach ( $services[ $markup['courier'] ] as $key => $service ) : ?>
										<option <?php echo isset( $markup['service'] ) &&  $key === $markup['service'] ? 'selected' : '' ?> value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $service['name'] ) ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
						<div class="repeater-col">
							<input type="number" name="pok_setting[markup][<?php echo esc_attr( $markup_key ) ?>][amount]" value="<?php echo isset( $markup['amount'] ) ? esc_attr( $markup['amount'] ) : 0 ?>">
						</div>
						<div class="repeater-col nowrap">
							<button type="button" class="delete-repeater-row button button-small"><span class="dashicons dashicons-trash"></span></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="repeater-base">
				<div class="repeater-col">
					<select name="pok_setting[markup][{id}][courier]" class="markup-courier" disabled>
						<option value=""><?php esc_html_e( 'All Courier', 'pok' ) ?></option>
						<?php foreach ( $settings['couriers'] as $courier ) : ?>
							<option value="<?php echo esc_attr( $courier ) ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="repeater-col">
					<select name="pok_setting[markup][{id}][service]" class="markup-service" disabled>
						<option value=""><?php esc_html_e( 'All Service', 'pok' ) ?></option>
					</select>
				</div>
				<div class="repeater-col">
					<input type="number" name="pok_setting[markup][{id}][amount]" value="0" disabled>
				</div>
				<div class="repeater-col nowrap">
					<button type="button" class="delete-repeater-row button button-small"><span class="dashicons dashicons-trash"></span></button>
				</div>
			</div>
			<button type="button" id="add-markup" class="add-repeater-row button"><?php esc_html_e( 'Add Cost Markup', 'pok' ) ?></button>
			<p class="helper"><?php esc_html_e( "Use a negative value on the Cost Markup to set a price decrease on cost.", 'pok' ); ?></p>
		</div>
	</div>
</div>
<div class="setting-row">
	<div class="setting-index">
		<?php $wc_currency = get_woocommerce_currency(); ?>
		<label for="pok-currency_conversion"><?php esc_html_e( 'Currency Conversion', 'pok' ); ?></label>
		<?php if ( 'IDR' !== $wc_currency ) : ?>
			<p class="helper"><?php esc_html_e( "We provide the shipping costs data in Indonesian Rupiah. You need to set up conversions to convert costs to your site's currency.", 'pok' ); ?></p>
		<?php endif; ?>
	</div>
	<div class="setting-option">
		<select name="pok_setting[currency_conversion]" id="pok-currency_conversion" <?php echo 'IDR' === $wc_currency ? 'disabled' : ''; ?>>
			<option <?php echo 'dont_convert' === $settings['currency_conversion'] ? 'selected' : ''; ?> value="dont_convert"><?php esc_html_e( 'Do not convert', 'pok' ); ?></option>
			<option <?php echo 'fixer' === $settings['currency_conversion'] && 'IDR' !== $wc_currency ? 'selected' : ''; ?> value="fixer"><?php esc_html_e( 'Use Fixer API', 'pok' ); ?></option>
			<option <?php echo 'currencylayer' === $settings['currency_conversion'] && 'IDR' !== $wc_currency ? 'selected' : ''; ?> value="currencylayer"><?php esc_html_e( 'Use Currency Layer API', 'pok' ); ?></option>
			<option <?php echo 'wpml' === $settings['currency_conversion'] && 'IDR' !== $wc_currency ? 'selected' : ''; ?> value="wpml"><?php esc_html_e( "Use WPML's multi currency", 'pok' ); ?></option>
			<option <?php echo 'static' === $settings['currency_conversion'] && 'IDR' !== $wc_currency ? 'selected' : ''; ?> value="static"><?php esc_html_e( 'Static conversion rate', 'pok' ); ?></option>
		</select>
		<?php if ( 'IDR' === $wc_currency ) : ?>
			<p class="helper"><?php esc_html_e( 'Your site is currently using IDR as the currency. So this option is not required.', 'pok' ); ?></p>
		<?php else : ?>
			<div class="setting-sub-option options-currency options-currency-fixer <?php echo 'fixer' === $settings['currency_conversion'] ? 'show' : ''; ?>">
				<label for="pok-currency_fixer_api_type"><?php esc_html_e( 'Fixer Subscription Plan', 'pok' ); ?></label>
				<select name="pok_setting[currency_fixer_api_type]" id="pok-currency_fixer_api_type">
					<option <?php echo 'basic' === $settings['currency_fixer_api_type'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Basic', 'pok' ); ?></option>
					<option <?php echo 'professional' === $settings['currency_fixer_api_type'] ? 'selected' : ''; ?> value="professional"><?php esc_html_e( 'Professional', 'pok' ); ?></option>
					<option <?php echo 'professional_plus' === $settings['currency_fixer_api_type'] ? 'selected' : ''; ?> value="professional"><?php esc_html_e( 'Professional Plus', 'pok' ); ?></option>
					<option <?php echo 'enterprise' === $settings['currency_fixer_api_type'] ? 'selected' : ''; ?> value="professional"><?php esc_html_e( 'Enterprise', 'pok' ); ?></option>
				</select>
				<p class="helper"><?php printf( __( "You need at least Basic subscription plan on Fixer to use this function. We store the rates from Fixer to cache, so you don't have to worry about API calls limitation. Cache expiration is based on update interval on your plan. <a target='_blank' href='%s'>learn more here</a>", 'pok' ), 'https://fixer.io/product' ); ?></p>
				<label for="pok-currency_fixer_api_key"><?php esc_html_e( 'Fixer API Key', 'pok' ); ?></label>
				<input id="pok-currency_fixer_api_key" type="text" name="pok_setting[currency_fixer_api_key]" value="<?php echo esc_attr( $settings['currency_fixer_api_key'] ); ?>">
				<p class="helper"><?php printf( __( 'Find your API key <a target="_blank" href="%s">here</a>', 'pok' ), 'https://fixer.io/dashboard' ); ?></p>
				<div class="check">
					<button type="button" id="check-fixer-api" class="button button-secondary"><?php esc_html_e( 'Check API Status', 'pok' ); ?></button>
					<p class="api-response"></p>
				</div>
			</div>
			<div class="setting-sub-option options-currency options-currency-currencylayer <?php echo 'currencylayer' === $settings['currency_conversion'] ? 'show' : ''; ?>">
				<label for="pok-currency_currencylayer_api_type"><?php esc_html_e( 'Currency Layer Subscription Plan', 'pok' ); ?></label>
				<select name="pok_setting[currency_currencylayer_api_type]" id="pok-currency_currencylayer_api_type">
					<option <?php echo 'basic' === $settings['currency_currencylayer_api_type'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Basic', 'pok' ); ?></option>
					<option <?php echo 'professional' === $settings['currency_currencylayer_api_type'] ? 'selected' : ''; ?> value="professional"><?php esc_html_e( 'Professional', 'pok' ); ?></option>
					<option <?php echo 'enterprise' === $settings['currency_currencylayer_api_type'] ? 'selected' : ''; ?> value="professional"><?php esc_html_e( 'Enterprise', 'pok' ); ?></option>
				</select>
				<p class="helper"><?php printf( __( "You need at least Basic subscription plan on Currency Layer to use this function. We store the rates from Currency Layer to cache, so you don't have to worry about API calls limitation. Cache expiration is based on update interval on your plan. <a target='_blank' href='%s'>learn more here</a>", 'pok' ), 'https://currencylayer.com/product' ); ?></p>
				<label for="pok-currency_currencylayer_api_key"><?php esc_html_e( 'Currency Layer API Key', 'pok' ); ?></label>
				<input id="pok-currency_currencylayer_api_key" type="text" name="pok_setting[currency_currencylayer_api_key]" value="<?php echo esc_attr( $settings['currency_currencylayer_api_key'] ); ?>">
				<p class="helper"><?php printf( __( 'Find your API key <a target="_blank" href="%s">here</a>', 'pok' ), 'https://currencylayer.com/dashboard' ); ?></p>
				<div class="check">
					<button type="button" id="check-currencylayer-api" class="button button-secondary"><?php esc_html_e( 'Check API Status', 'pok' ); ?></button>
					<p class="api-response"></p>
				</div>
			</div>
			<?php if ( ! $this->helper->is_wpml_multi_currency_active() ) : ?>
				<div class="setting-sub-option options-currency options-currency-wpml <?php echo 'wpml' === $settings['currency_conversion'] ? 'show' : ''; ?>">
					<p class="helper"><?php esc_html_e( "You need to install WPML's WooCommerce Multilingual and enable the multi-currency mode to use this function", 'pok' ); ?></p>
				</div>
			<?php elseif ( false === $this->helper->get_wpml_rate() ) : ?>
				<div class="setting-sub-option options-currency options-currency-wpml <?php echo 'wpml' === $settings['currency_conversion'] ? 'show' : ''; ?>">
					<p class="helper"><?php printf( __( "You need to set your currency conversion rate to IDR to use this function. <a href='%s'>click here to configure</a>", 'pok' ), admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ) ); ?></p>
				</div>
			<?php else : ?>
				<div class="setting-sub-option options-currency options-currency-wpml <?php echo 'wpml' === $settings['currency_conversion'] ? 'show' : ''; ?>">
					<p class="helper"><?php printf( __( "Current conversion rate on WPML's Multi-Currency setting: 1 IDR = %1\$s %2\$s. <a href='%3\$s'>click here to configure</a>", 'pok' ), number_format( floatval( $this->helper->get_wpml_rate() ), 6 ), $wc_currency, admin_url( 'admin.php?page=wpml-wcml&tab=multi-currency' ) ); ?></p>
				</div>
			<?php endif; ?>
			<div class="setting-sub-option options-currency options-currency-static <?php echo 'static' === $settings['currency_conversion'] ? 'show' : ''; ?>">
				<label for="pok-currency_static_conversion_rate"><?php printf( __( '1 IDR to %s conversion rate', 'pok' ), $wc_currency ); ?></label>
				<input id="pok-currency_static_conversion_rate" type="number" name="pok_setting[currency_static_conversion_rate]" value="<?php echo esc_attr( $settings['currency_static_conversion_rate'] ); ?>" step="any">
				<p class="helper"><?php printf( __( "Click <a target='_blank' href='%s'>here</a> to get latest conversion rate", 'pok' ), 'https://www.google.com/search?q=1+IDR+to+' . $wc_currency ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'pok_setting_cost', $settings ); ?>