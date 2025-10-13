<?php

/**
 * Product Hooks
 */
class POK_Hooks_Product {

	/**
	 * POK Core
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * POK Helper
	 *
	 * @var object
	 */
	protected $helper;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		add_action( 'woocommerce_product_options_shipping', array( $this, 'custom_product_shipping_options' ) );
		add_action( 'save_post_product', array( $this, 'save_product' ), 20, 3 );
		if ( $this->helper->is_plugin_active() && 'rajaongkir2' !== $this->setting->get('base_api') ) {
			add_shortcode( 'pok_shipping_estimation', array( $this, 'shortcode_shipping_estimation' ) );
			add_shortcode( 'pok_shipping_calculator', array( $this, 'shortcode_shipping_estimation' ) );
			add_filter( 'woocommerce_product_tabs', array( $this, 'show_shipping_estimation' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		// Let 3rd parties unhook the above via this hook.
		do_action( 'pok_hooks_product', $this );
	}

	/**
	 * Add cutom shipping options on edit product
	 */
	public function custom_product_shipping_options() {
		global $post;
		$enable_insurance = $this->setting->get('enable_insurance');
		$enable_timber_packing = $this->setting->get('enable_timber_packing');
		if ( 'set' !== $enable_insurance && 'set' !== $enable_timber_packing ) {
			return;
		}
		?>
		</div>
		<div class="options_group">
			<?php if ( 'set' === $enable_insurance ) : ?>
				<p class="form-field">
					<label for="product_shipping_insurance"><?php esc_html_e( 'Shipping insurance', 'pok' ); ?></label>
					<input type="checkbox" name="enable_insurance" id="product_shipping_insurance" <?php echo 'yes' === get_post_meta( $post->ID, 'enable_insurance', true ) ? 'checked' : ''; ?> value="yes">
					<span class="description"><?php esc_html_e( 'Add shipping insurance fee on checkout', 'pok' ); ?></span>
					<?php echo wc_help_tip( __( 'If checked, the insurance fee will be added to the shipping cost.', 'pok' ) ); ?>
				</p>
			<?php endif; ?>
			<?php if ( 'set' === $enable_timber_packing ) : ?>
				<p class="form-field">
					<label for="product_timber_packing"><?php esc_html_e( 'Timber packing', 'pok' ); ?></label>
					<input type="checkbox" name="enable_timber_packing" id="product_timber_packing" <?php echo 'yes' === get_post_meta( $post->ID, 'enable_timber_packing', true ) ? 'checked' : ''; ?> value="yes">
					<span class="description"><?php esc_html_e( 'Add timber packing fee on checkout', 'pok' ); ?></span>
					<?php echo wc_help_tip( __( 'If checked, the timber packing fee will be added to the shipping cost.', 'pok' ) ); ?>
				</p>
			<?php endif; ?>
		<?php
	}

	/**
	 * Save product shipping options on save product
	 *
	 * @param  int    $post_id Product ID.
	 * @param  object $post    Product data.
	 * @param  mbuh   $update  Embuh.
	 */
	public function save_product( $post_id, $post, $update ) {
		if ( $product = wc_get_product( $post_id ) ) {
			if ( isset( $_POST['enable_insurance'] ) && 'yes' === $_POST['enable_insurance'] ) {
				update_post_meta( $post_id, 'enable_insurance', 'yes' );
			} else {
				update_post_meta( $post_id, 'enable_insurance', 'no' );
			}
			if ( isset( $_POST['enable_timber_packing'] ) && 'yes' === $_POST['enable_timber_packing'] ) {
				update_post_meta( $post_id, 'enable_timber_packing', 'yes' );
			} else {
				update_post_meta( $post_id, 'enable_timber_packing', 'no' );
			}
		}
	}

	/**
	 * Show shipping estimation on prodcut tabs
	 * 
	 * @param  array $tabs Product tabs.
	 * @return array       Product tabs.
	 */
	public function show_shipping_estimation( $tabs ) {
		global $product;
		if ( 'yes' === $this->setting->get( 'show_shipping_estimation' ) && ! $product->is_virtual() ) {
			$tabs['shipping_estimation'] = array(
				'title'		=> __( 'Shipping Estimation', 'pok' ),
				'priority'	=> 40,
				'callback'	=> array( $this, 'shipping_estimation_callback' )
			);
		}
		return $tabs;
	}

	/**
	 * Show shipping estimation using shortcode
	 * 
	 * @param  array  $atts    Attributes.
	 * @param  string $content Content.
	 */
	public function shortcode_shipping_estimation( $atts, $content = "" ) {
		global $product;
		if ( is_product() && 'no' === $this->setting->get( 'show_shipping_estimation' ) && ! $product->is_virtual() ) {
			ob_start();
			$this->shipping_estimation_callback();
			return ob_get_clean();
		}
		return;
	}

	/**
	 * Callback for displaying shipping estimation
	 */
	public function shipping_estimation_callback() {
		$provinces = $this->core->get_province();
		if ( ! $provinces ) {
			return;
		}
		global $product;
		$selected_province 	= 0;
		$selected_city      = 0;
		$selected_district  = 0;
		$selected_simple_city = array();
		$cities				= array();
		$districts 			= array();
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$selected_province 	= $this->helper->get_address_id_from_user( $user_id, 'shipping', 'state' );
			$selected_city      = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'city' );
			$selected_district  = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'district' );
			$cities 			= $this->core->get_city( $selected_province );
			if ( 'pro' === $this->helper->get_license_type() ) {
				$districts 		= $this->core->get_district( $selected_city );
			}
			if ( $this->helper->is_use_simple_address_field() ) {
				$user_id = get_current_user_id();
				$default[ 'shipping_state' ]      = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'state' );
				$default[ 'shipping_city' ]       = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'city' );
				$default[ 'shipping_district' ]   = $this->helper->get_address_id_from_user( $user_id, 'shipping', 'district' );
				
				if ( ! empty( $selected_province ) && ! empty( $selected_city ) && ! empty( $selected_district ) ) {
					$city_id = $selected_district . '_' . $selected_city . '_' . $selected_province;
					$city = $this->core->get_simple_address( $city_id );
					if ( ! empty( $city ) ) {
						$selected_simple_city = array( $city_id, $city );
					}
				}
			}
		}
		$origin = 0;
		$store_location = $this->setting->get( 'store_location' );
		if ( isset( $store_location[0] ) && ! empty( $store_location[0] ) ) {
			$origin = $store_location[0];
		}
		$origin = apply_filters( 'pok_shipping_estimation_origin', $origin, $product );
		?>
		<h2><?php esc_html_e( 'Shipping Calculator', 'pok' ) ?></h2>
		<table class="shop_attributes pok-shipping-estimation-input">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Quantity', 'pok' ) ?></th>
					<td class="shipping-qty">
						<input type="hidden" value="<?php echo esc_attr( $product->get_id() ) ?>" class="pok_shipping_product">
						<input type="number" class="pok_shipping_qty" value="1">
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Destination', 'pok' ) ?></th>
					<td class="shipping-destination">
						<?php if ( $this->helper->is_use_simple_address_field() ) : ?>
							<select id="select_province" class="select2-ajax pok_check_shipping" data-action="pok_search_simple_address" data-nonce="<?php echo wp_create_nonce( 'search_city' ); ?>">
								<?php if ( ! empty( $selected_simple_city ) ) : ?>
									<option value="<?php echo esc_attr( $selected_simple_city[0] ); ?>" selected><?php echo esc_attr( $selected_simple_city[1] ); ?></option>
								<?php else: ?>
									<option value="0" selected><?php esc_html_e( 'Search Town / City', 'pok' ) ?></option>
								<?php endif; ?>
							</select>
						<?php else: ?>
							<div>
								<span><?php esc_html_e( 'Province', 'pok' ) ?></span>
								<select id="select_province" class="pok_shipping_province init-select2 <?php 'pro' !== $this->helper->get_license_type() ? 'pok_check_shipping' : '' ?>">
									<option value="0" <?php echo 0 === $selected_province ? 'selected' : ''; ?>><?php esc_html_e( 'Select province', 'pok' ) ?></option>
									<?php foreach ( $provinces as $province_key => $province_name ) : ?>
										<option value="<?php echo esc_attr( $province_key ) ?>" <?php echo intval( $province_key ) === $selected_province ? 'selected' : ''; ?>><?php echo esc_html( $province_name ) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div>
								<span><?php esc_html_e( 'City', 'pok' ) ?></span>
								<select class="pok_shipping_city init-select2" id="select_city">
									<option value="0" <?php echo 0 === $selected_city ? 'selected' : ''; ?>><?php esc_html_e( 'Select city', 'pok' ) ?></option>
									<?php if ( $cities ) : ?>
										<?php foreach ( $cities as $city_key => $city_name ) : ?>
											<option value="<?php echo esc_attr( $city_key ) ?>" <?php echo intval( $city_key ) === $selected_city ? 'selected' : ''; ?>><?php echo esc_html( $city_name ) ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<?php if ( 'pro' === $this->helper->get_license_type() ) : ?>
								<div>
									<span><?php esc_html_e( 'District', 'pok' ) ?></span>
									<select class="pok_shipping_district pok_check_shipping init-select2" id="select_district">
										<option value="0" <?php echo 0 === $selected_district ? 'selected' : ''; ?>><?php esc_html_e( 'Select district', 'pok' ) ?></option>
										<?php if ( $districts ) : ?>
											<?php foreach ( $districts as $district_key => $district_name ) : ?>
												<option value="<?php echo esc_attr( $district_key ) ?>" <?php echo intval( $district_key ) === $selected_district ? 'selected' : ''; ?>><?php echo esc_html( $district_name ) ?></option>
											<?php endforeach; ?>
										<?php endif; ?>
									</select>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" class="pok_shipping_origin" value="<?php echo esc_attr( $origin ); ?>">
		<div class="pok-shipping-estimation-result-wrapper"></div>
		<?php
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		global $post;
		if ( is_product() && ( 'yes' === $this->setting->get( 'show_shipping_estimation' ) || has_shortcode( $post->post_content, 'pok_shipping_estimation' ) || has_shortcode( $post->post_content, 'pok_shipping_calculator' ) ) ) {
			wp_enqueue_style( 'pok-product', POK_PLUGIN_URL . '/assets/css/product.css', array( 'select2' ), POK_VERSION );
			wp_enqueue_script( 'pok-product', POK_PLUGIN_URL . '/assets/js/product.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			$localize = array(
				'ajaxurl'				=> admin_url( 'admin-ajax.php' ),
				'labelFailedCity'       => __( 'Failed to load city list. Try again?', 'pok' ),
				'labelFailedDistrict'   => __( 'Failed to load district list. Try again?', 'pok' ),
				'labelSelectCity'       => __( 'Select city', 'pok' ),
				'labelLoadingCity'      => __( 'Loading city options...', 'pok' ),
				'labelSelectDistrict'   => __( 'Select district', 'pok' ),
				'labelLoadingDistrict'  => __( 'Loading district options...', 'pok' ),
				'insertQty'  			=> __( 'Please insert quantity', 'pok' ),
				'selectDestination'		=> __( 'Please select your destination', 'pok' ),
				'loading'  				=> __( 'Loading...', 'pok' ),
				'enableDistrict'        => ( 'pro' === $this->helper->get_license_type() ? true : false ),
			);
			wp_localize_script( 'pok-product', 'pok', $localize );
			wp_localize_script(
				'pok-product', 'pok_nonces', array(
					'get_list_city'   		=> wp_create_nonce( 'get_list_city' ),
					'get_list_district' 	=> wp_create_nonce( 'get_list_district' ),
					'get_cost'              => wp_create_nonce( 'get_cost' ),
					'set_order_shipping'    => wp_create_nonce( 'set_order_shipping' ),
				)
			);
		}
	}

}
