<?php

/**
 * Class POK_Checker
 */
class POK_Checker {

	/**
	 * POK core
	 * 
	 * @var POK_Core
	 */
	protected $core;

	/**
	 * POK Helper
	 * 
	 * @var POK_Helper
	 */
	protected $helper;

	/**
	 * POK Setting
	 * 
	 * @var POK_Setting
	 */
	protected $setting;

	/**
	 * POK License
	 * 
	 * @var POK_License
	 */
	protected $license;

	/**
	 * POK API
	 * 
	 * @var POK_API
	 */
	protected $api;

	/**
	 * Debug mode
	 * 
	 * @var boolean;
	 */
	protected $debug;

	/**
	 * Constructor
	 *
	 * @param object $license License object.
	 */
	public function __construct( $license ) {
		global $pok_core;
		global $pok_helper;
		$this->setting      = new POK_Setting();
		$this->core         = $pok_core;
		$this->debug        = isset( $_GET['debug'] ) || POK_DEBUG;
		$rajaongkir_status  = $this->setting->get( 'rajaongkir_status' );
		if ( $rajaongkir_status[0] && 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			require_once POK_PLUGIN_PATH . 'classes/api/class-pok-api-rajaongkir.php';
			$this->api = new POK_API_RajaOngkir( $this->setting->get( 'rajaongkir_key' ), $this->setting->get( 'rajaongkir_type' ) );
		} else {
			require_once POK_PLUGIN_PATH . 'classes/api/class-pok-api-tonjoo.php';
			$this->api = new POK_API_Tonjoo( $license->get( 'key' ) );
		}
		$this->helper       = $pok_helper;
	}

	/**
	 * Render page
	 */
	public function render() {
		$settings   = $this->setting->get_all();
		$provinces  = $this->core->get_province();
		if ( isset( $_REQUEST['pok_action'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'check_cost' ) ) { // Input var okay.
			$errors = array();
			if ( isset( $_GET['weight'] ) && 0 <= floatval( $_GET['weight'] ) ) { // Input var okay.
				if ( 'pro' === $this->helper->get_license_type() ) {
					if ( isset( $_GET['district'] ) && 0 !== intval( $_GET['district'] ) ) { // Input var okay.
						$destination = intval( $_GET['district'] ); // Input var okay.
					} else {
						$errors[] = __( 'District not set', 'pok' );
					}
				} else {
					if ( isset( $_GET['city'] ) && 0 !== intval( $_GET['city'] ) ) { // Input var okay.
						$destination = intval( $_GET['city'] ); // Input var okay.
					} else {
						$errors[] = __( 'City not set', 'pok' );
					}
				}
				$weight = floatval( $_GET['weight'] ); // Input var okay.
				if ( isset( $destination ) ) {
					$result = $this->core->get_cost( $destination, $weight );
					if ( $this->debug ) {
						$original_cost = $this->get_original_cost( $destination, $weight );
						$formatted_cost = $this->get_formatted_cost(
							array(
								'province'  => isset( $_GET['province'] ) ? intval( $_GET['province'] ) : 0, // Input var okay.
								'city'      => isset( $_GET['city'] ) ? intval( $_GET['city'] ) : 0, // Input var okay.
								'district'  => isset( $_GET['district'] ) ? intval( $_GET['district'] ) : 0, // Input var okay.
								'weight'    => $weight,
							)
						);
					}
				}
			} else {
				$errors[] = __( 'Weight must not empty', 'pok' );
			}
			if ( isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] ) {
				if ( ! isset( $_GET['total'] ) || empty( $_GET['total'] ) ) {
					$errors[] = __( 'Total price must not empty', 'pok' );
				} else {
					$total = floatval( $_GET['total'] );
				}
			}
		}
		include_once POK_PLUGIN_PATH . 'views/setting-checker.php';
	}

	/**
	 * Get response
	 *
	 * @param  int   $destination Destination ID.
	 * @param  float $weight      Calculated weight.
	 * @return mixed               API Response.
	 */
	public function get_original_cost( $destination, $weight ) {
		$origin = $this->setting->get( 'store_location' );
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			return $this->api->get_cost( $origin[0], $destination, $weight, $this->setting->get( 'couriers' ) );
		} else {
			return $this->api->get_cost( $origin[0], $destination, $this->setting->get( 'couriers' ) );
		}
	}

	/**
	 * Get formatted cost
	 *
	 * @param  array $args   Args.
	 * @return array         Filtered costs.
	 */
	public function get_formatted_cost( $args ) {
		$final_rates = array();
		if ( 'pro' === $this->helper->get_license_type() ) {
			$destination_id = $args['district'];
			$destination_type = 'district';
		} else {
			$destination_id = $args['city'];
			$destination_type = 'city';
		}
		$rates          = $this->core->get_cost( $destination_id, $args['weight'] );
		$custom_costs   = $this->core->get_custom_cost( $args, $destination_type, $args['weight'] );
		if ( 'replace' === $this->setting->get( 'custom_cost_type' ) && ! empty( $custom_costs ) ) {
			$rates = $custom_costs;
		} else {
			if ( ! empty( $custom_costs ) ) {
				$rates = array_merge( $custom_costs, $rates );
			}
		}

		if ( ! empty( $rates ) ) {
			foreach ( $rates as $rate ) {

				// if filter courier active.
				if ( 'nusantara' === $this->setting->get( 'base_api' ) && 'yes' === $this->setting->get( 'specific_service' ) && ( ! isset( $rate['source'] ) || 'custom' !== $rate['source'] ) ) {
					if ( ! in_array( sanitize_title( $rate['class'] ), $this->setting->get( 'specific_service_option' ), true ) ) {
						continue;
					}
				}

				// add timber packing.
				if ( 'yes' === $this->setting->get( 'enable_timber_packing' ) ) {
					$rate['cost'] += apply_filters( 'pok_timber_packing_fee', $rate['cost'], $rate );
				}

				// add insurance fee.
				if ( 'yes' === $this->setting->get( 'enable_insurance' ) ) {
					$rate['cost'] += $this->helper->get_insurance( $rate['courier'] );
				}

				// additional fee.
				if ( 'yes' === $this->setting->get( 'markup_fee' ) ) {
					$rate['cost'] += apply_filters( 'pok_custom_markup', floatval( $this->setting->get( 'markup_fee_amount' ) ), $rate );
				}

				$final_rates[] = array(
					'label'     => strtoupper( $rate['courier'] ) . ' - ' . $rate['service'],
					'cost'      => $this->helper->currency_convert( $rate['cost'] ),
					'meta_data' => array(
						'courier'   => $rate['courier'],
						'source'    => isset( $rate['source'] ) ? $rate['source'] : 'api-' . $this->setting->get( 'base_api' ),
						'etd'       => $rate['time'],
					),
				);
			}
		}
		return $final_rates;
	}
}
