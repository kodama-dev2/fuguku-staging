<?php

/**
 * POK Core class
 */
class POK_Core {

	/**
	 * POK API
	 *
	 * @var object
	 */
	protected $api;

	/**
	 * Cache key prefix
	 *
	 * @var string
	 */
	protected $key_prefix;

	/**
	 * License handler
	 * @var Tonjoo_License_Handler_3
	 */
	protected $license;

	/**
	 * Enable cache
	 * 
	 * @var boolean
	 */
	protected $enable_cache;

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
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler_3 $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler_3 $license ) {
		global $pok_helper;
		$this->license = $license;
		$this->api = new POK_API( $license );
		$this->setting = new POK_Setting();
		$this->key_prefix = 'pok_data_';
		// $this->enable_cache = ! POK_DEBUG; // for debugging purpose.
		$this->enable_cache = ( 'no' === $this->setting->get('debug_mode') );
		$this->helper = $pok_helper;
	}

	/**
	 * Re init
	 */
	public function reinit() {
		$this->api = new POK_API( $this->license );
		$this->setting = new POK_Setting();
	}

	/**
	 * Check cache
	 *
	 * @param  string $key Cache key.
	 * @return boolean      Is exists or not
	 */
	private function is_cache_exists( $key ) {
		if ( $this->enable_cache ) {
			$data = get_option( $this->key_prefix . sanitize_title_for_query( $key ), false );
			if ( $data && ! empty( $data ) ) {
				if ( false !== get_transient( $this->key_prefix . sanitize_title_for_query( $key ) ) ) {
					return true;
				}
			} else {
				delete_transient( $this->key_prefix . sanitize_title_for_query( $key ) );
			}
		}
		return false;
	}

	/**
	 * Cache requested data
	 *
	 * @param  string  $key        Cache key.
	 * @param  mixed   $new_value  Cache value.
	 * @param  integer $expiration Cache expiration in seconds.
	 * @return mixed               Cached data.
	 */
	private function cache_it( $key, $new_value = null, $expiration = 86400 ) {
		$expiration = 60 * 60 * $expiration;
		if ( ! is_null( $new_value ) ) {
			if ( $this->enable_cache ) {
				update_option( $this->key_prefix . sanitize_title_for_query( $key ), $new_value, 'no' );
				set_transient( $this->key_prefix . sanitize_title_for_query( $key ), true, $expiration ); // we store data with option, so no need to set value on transient.
			}
			$return = $new_value;
		} else {
			$return = get_option( $this->key_prefix . sanitize_title_for_query( $key ), false );
		}
		return apply_filters( 'pok_get_cached_' . $key, $return );
	}

	/**
	 * Delete all cached data by type
	 *
	 * @param  string $key_type Key type.
	 */
	public function purge_cache( $key_type = '' ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s", array(
					$this->key_prefix . $key_type . '%',
					'_transient_' . $this->key_prefix . $key_type . '%',
					'_transient_timeout_' . $this->key_prefix . $key_type . '%',
				)
			)
		);
	}

	/**
	 * Delete cache by key
	 *
	 * @param  string $key Cache key.
	 */
	public function delete_cache( $key ) {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s", array(
					$this->key_prefix . sanitize_title_for_query( $key ),
					'_transient_' . $this->key_prefix . sanitize_title_for_query( $key ),
					'_transient_timeout_' . $this->key_prefix . sanitize_title_for_query( $key ),
				)
			)
		);
	}

	/**
	 * Get courier options
	 *
	 * @param  string $vendor Vendor name.
	 * @param  string $type   Rajaongkir type.
	 * @return array          Courier list.
	 */
	public function get_courier( $vendor = 'nusantara', $type = 'pro' ) {
		if ( '' === $vendor ) {
			$vendor = $this->setting->get( 'base_api' );
		}
		if ( '' === $type ) {
			$type = $this->setting->get( 'rajaongkir_type' );
		}
		if ( 'nusantara' === $vendor ) { // tonjoo.
			$courier = array( 'jne', 'pos', 'tiki', 'jnt', 'sicepat', 'lion', 'anteraja', 'ide', 'wahana', 'ncs', 'atlas' );
		} else {
			if ( 'pro' === $type || 'rajaongkir2' === $vendor ) { // rajaongkir pro.
				$courier = array( 'jne', 'pos', 'tiki', 'jnt', 'sicepat', 'lion', 'anteraja', 'wahana', 'esl', 'ncs', 'pcp', 'rpx', 'pandu', 'pahala', 'sap', 'dse', 'slis', 'expedito', 'first', 'star', 'ninja', 'idl', 'rex', 'ide', 'sentral', 'jtl' );
			} elseif ( 'basic' === $type ) { // rajaongkir basic.
				$courier = array( 'jne', 'pos', 'tiki', 'pcp', 'rpx', 'esl' );
			} else { // rajaongkir free.
				$courier = array( 'jne', 'pos', 'tiki' );
			}
		}
		return apply_filters( 'pok_couriers', $courier, $vendor, $type );
	}

	/**
	 * Get all couriers
	 *
	 * @return array All couriers.
	 */
	public function get_all_couriers() {
		return apply_filters( 'pok_all_couriers', array( 'jne', 'pos', 'tiki', 'jnt', 'sicepat', 'lion', 'anteraja', 'wahana', 'ninja', 'esl', 'ncs', 'pcp', 'rpx', 'pandu', 'pahala', 'sap', 'dse', 'slis', 'expedito', 'first', 'star', 'idl', 'rex', 'atlas', 'ide', 'sentral', 'jtl' ) );
	}

	/**
	 * Get courier services
	 *
	 * @return array Courier services
	 */
	public function get_courier_service() {
		return $this->api->get_courier_service();
	}

	/**
	 * Get province options
	 *
	 * @return array Province options
	 */
	public function get_province() {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			return $this->api->get_province();
		} elseif ( 'rajaongkir2' === $this->setting->get( 'base_api' ) ) {
			return [];
		} else {
			if ( ! $this->is_cache_exists( 'province' ) ) {
				$result = $this->api->get_province();
			}
			return $this->cache_it( 'province', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
		}
	}

	/**
	 * Get single province name
	 *
	 * @param  integer $province_id Province ID.
	 * @return string               Province name.
	 */
	public function get_single_province( $province_id = 0 ) {
		$provinces = $this->get_province();
		if ( ! is_null( $provinces ) && ! empty( $provinces ) && isset( $provinces[ $province_id ] ) ) {
			return $provinces[ $province_id ];
		}
		return false;
	}

	/**
	 * Get city options based on province id
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City list.
	 */
	public function get_city( $province_id = 0 ) {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			return $this->api->get_city( $province_id );
		} else {
			if ( ! $this->is_cache_exists( 'city_' . $province_id ) ) {
				$result = $this->api->get_city( $province_id );
			}
			return $this->cache_it( 'city_' . $province_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
		}
	}

	/**
	 * Get single city by id
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			return $this->api->get_single_city( $city_id );
		} else {
			if ( ! $this->is_cache_exists( 'city_single_' . $city_id ) ) {
				$result = $this->api->get_single_city( $city_id );
			}
			return $this->cache_it( 'city_single_' . $city_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
		}
	}

	/**
	 * Get single city by id, but without province name
	 *
	 * @param  integer $city_id City ID.
	 * @return string           City name.
	 */
	public function get_single_city_without_province( $city_id = 0 ) {
		$city = $this->get_single_city( $city_id );
		if ( $city ) {
			$split = explode( ', ', $city );
			return $split[0];
		}
		return $city;
	}

	/**
	 * Get all city (only for API rajaongkir)
	 *
	 * @return array City list
	 */
	public function get_all_city() {
		if ( ! $this->is_cache_exists( 'all_city' ) ) {
			$result = $this->api->get_all_city();
		}
		return $this->cache_it( 'all_city', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get all origins
	 *
	 * @return array City list
	 */
	public function get_origins() {
		return $this->api->get_origins();
	}

	/**
	 * Search city (only for API tonjoo)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_city( $search = '' ) {
		if ( ! $this->is_cache_exists( 'search_city_' . $search ) ) {
			$result = $this->api->search_city( $search );
		}
		return $this->cache_it( 'search_city_' . $search, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Search city for simple address field (only for API tonjoo)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_simple_address( $search = '' ) {
		return $this->api->search_simple_address( $search );
	}

	/**
	 * Get simple address by ID (only for API tonjoo)
	 *
	 * @param  string $id City ID.
	 * @return string     City name.
	 */
	public function get_simple_address( $id = '' ) {
		return $this->api->get_simple_address( $id );
	}

	/**
	 * Get district by the City ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District list.
	 */
	public function get_district( $city_id = 0 ) {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			return $this->api->get_district( $city_id );
		} else {
			if ( ! $this->is_cache_exists( 'district_' . $city_id ) ) {
				$result = $this->api->get_district( $city_id );
			}
			return $this->cache_it( 'district_' . $city_id, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
		}
	}

	/**
	 * Get single district
	 *
	 * @param  integer $city_id     City ID.
	 * @param  integer $district_id District ID.
	 * @return string               District name.
	 */
	public function get_single_district( $city_id = 0, $district_id = 0 ) {
		$districts = $this->get_district( $city_id );
		if ( ! is_null( $districts ) && ! empty( $districts ) && isset( $districts[ $district_id ] ) ) {
			return $districts[ $district_id ];
		}
		return false;
	}

	/**
	 * Get all countries
	 *
	 * @return array Country
	 */
	public function get_all_country() {
		if ( ! $this->is_cache_exists( 'country' ) ) {
			$result = $this->api->get_all_country();
		}
		return $this->cache_it( 'country', isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_addresses' ) );
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $destination Destination ID (city or district).
	 * @param  integer $weight      Weight in kilograms.
	 * @param  integer $set_origin  Set this if wanna get cost from specific origin (ignore setting).
	 * @param  array   $courier     Set this if wanna get cost from specific couriers (ignore setting).
	 * @return array                Costs.
	 */
	public function get_cost( $destination, $weight, $set_origin = 0, $courier = array() ) {
		if ( empty( $courier ) ) {
			$courier = $this->setting->get( 'couriers' );
		}
		$store_location = $this->setting->get( 'store_location' );
		if ( $weight < 1 ) {
			$weight = 1;
		}
		if ( 'nusantara' === $this->setting->get( 'base_api' ) || ( ( 'rajaongkir' === $this->setting->get( 'base_api' ) || 'rajaongkir2' === $this->setting->get( 'base_api' ) ) && 'no' !== $this->setting->get( 'round_weight' ) ) ) {
			$weight = $this->helper->round_weight( $weight );
		}
		$weight = $weight * 1000;
		if ( ( false === $store_location || empty( $store_location ) || empty( $store_location[0] ) ) && 0 === $set_origin ) {
			return false;
		}
		$origin = apply_filters( 'pok_origin', ( 0 < intval( $set_origin ) ? $set_origin : $store_location[0] ) ); // wrap it with filters, given the ability to change the origin.

		$cost_cache_name = apply_filters( 'pok_cost_cache_name', 'cost_' . $origin . '_' . $destination . '_' . $weight, $origin, $destination, $weight, $courier );
		if ( ! $this->is_cache_exists( $cost_cache_name ) ) {
			$result = $this->api->get_cost( $origin, $destination, $weight, $courier );
		}
		return $this->cache_it( $cost_cache_name, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get international shipping cost
	 *
	 * @param  integer $destination Destination ID (country).
	 * @param  integer $weight      Weight in kilograms.
	 * @param  integer $set_origin  Set this if wanna get cost from specific origin (ignore setting).
	 * @param  array   $courier     Set this if wanna get cost from specific couriers (ignore setting).
	 * @return array                Costs.
	 */
	public function get_cost_international( $destination, $weight, $set_origin = 0, $courier = array() ) {
		if ( empty( $courier ) ) {
			$courier = $this->setting->get( 'couriers' );
		}
		$store_location = $this->setting->get( 'store_location' );
		$weight = $weight * 1000;
		if ( ( false === $store_location || empty( $store_location ) || empty( $store_location[0] ) ) && 0 === $set_origin ) {
			return false;
		}
		$origin = apply_filters( 'pok_origin', ( 0 < intval( $set_origin ) ? $set_origin : $store_location[0] ) ); // wrap it with filters, given the ability to change the origin.
		$cost_cache_name = apply_filters( 'pok_cost_international_cache_name', 'cost_international_' . $origin . '_' . $destination . '_' . $weight, $origin, $destination, $weight, $courier );
		if ( ! $this->is_cache_exists( $cost_cache_name ) ) {
			$result = $this->api->get_cost_international( $origin, $destination, $weight, $courier );
		}
		return $this->cache_it( $cost_cache_name, isset( $result ) ? $result : null, $this->setting->get( 'cache_expiration_costs' ) );
	}

	/**
	 * Get custom shipping cost
	 *
	 * @param  integer $destination      Destination ID (city/district).
	 * @param  string  $destination_type Destination type (city/district).
	 * @param  integer $weight           Weight in grams.
	 * @return array                     Costs.
	 */
	public function get_custom_cost( $destination, $destination_type, $weight ) {
		$data = $this->setting->get_custom_costs();
		$custom_courier = $this->setting->get( 'custom_cost_courier' );
		$roundweight = $this->helper->round_weight( $weight );
		$costs = array();
		if ( ! empty( $data ) ) {
			foreach ( $data as $d ) {
				if ( isset( $d['min'] ) && $weight < floatval( $d['min'] ) ) {
					continue;
				}
				if ( isset( $d['max'] ) && 0 < floatval( $d['max'] ) && $weight > floatval( $d['max'] ) ) {
					continue;
				}
				$found = false;
				if ( '*' === $d['province_id'] ) {
					$found = true;
				} elseif ( intval( $destination['state'] ) === intval( $d['province_id'] ) ) {
					if ( '*' === $d['city_id'] ) {
						$found = true;
					} elseif ( intval( $destination['city'] ) === intval( $d['city_id'] ) ) {
						if ( 'city' === $destination_type ) {
							$found = true;
						} elseif ( 'district' === $destination_type ) {
							if ( '*' === $d['district_id'] || intval( $destination['district'] ) === intval( $d['district_id'] ) ) {
								$found = true;
							}
						}
					}
				}
				if ( $found ) {
					$courier = 'custom' === $d['courier'] ? $custom_courier : strtolower( $d['courier'] );
					if ( ( empty( $d['package_name'] ) || '-' === $d['package_name'] ) && ( '' === $courier ) ) {
						$class = __( 'Custom Shipping', 'pok' );
					} else {
						if ( empty( $d['package_name'] ) || '-' === $d['package_name'] ) {
							$class = strtoupper( $courier );
						} elseif ( '' === $courier ) {
							$class = $d['package_name'];
						} else {
							$class = strtoupper( $courier ) . ' - ' . $d['package_name'];
						}
					}
					$costs[] = array(
						'class'         => $class,
						'courier'       => $courier,
						'service'       => $d['package_name'],
						'description'   => 'custom',
						'cost'          => floatval( $d['cost'] ) * $roundweight,
						'time'          => '',
						'source'        => 'custom',
					);
				}
			}
		}
		return $costs;
	}

	/**
	 * Get api status.
	 *
	 * @param  string $api Base API.
	 * @return boolean     API status.
	 */
	public function get_api_status( $api ) {
		return $this->api->get_api_status( $api );
	}

	/**
	 * Get fixer rates
	 *
	 * @param  string $currency Currency code.
	 * @return float            Currency rate.
	 */
	public function get_fixer_rate( $currency ) {
		if ( ! $this->is_cache_exists( 'fixer_rate_' . $currency ) ) {
			$result = $this->api->get_fixer_rate( $currency, $this->setting->get( 'currency_fixer_api_key' ) );
		}
		return $this->cache_it( 'fixer_rate_' . $currency, ( true === $result['status'] ? $result['result'] : 1 ), $this->helper->get_fixer_rate_expiration() );
	}

	/**
	 * Get Fixer API status
	 *
	 * @param  string $api_key API Key.
	 * @return mixed           Response.
	 */
	public function get_fixer_status( $api_key ) {
		$result = $this->api->get_fixer_rate( get_woocommerce_currency(), $api_key );
		if ( true === $result['status'] ) {
			return true;
		} else {
			return $result['result'];
		}
	}

	/**
	 * Get currencylayer rates
	 *
	 * @param  string $currency Currency code.
	 * @return float            Currency rate.
	 */
	public function get_currencylayer_rate( $currency ) {
		if ( ! $this->is_cache_exists( 'currencylayer_rate_' . $currency ) ) {
			$result = $this->api->get_currencylayer_rate( $currency, $this->setting->get( 'currency_currencylayer_api_key' ) );
		}
		return $this->cache_it( 'currencylayer_rate_' . $currency, ( true === $result['status'] ? $result['result'] : 1 ), $this->helper->get_currencylayer_rate_expiration() );
	}

	/**
	 * Get currencylayer API status
	 *
	 * @param  string $api_key API Key.
	 * @return mixed           Response.
	 */
	public function get_currencylayer_status( $api_key ) {
		$result = $this->api->get_currencylayer_rate( get_woocommerce_currency(), $api_key );
		if ( true === $result['status'] ) {
			return true;
		} else {
			return $result['result'];
		}
	}

	/**
	 * Get rajaongkir status
	 *
	 * @param  string $api_key  API key.
	 * @param  string $api_type API type.
	 * @return boolean          API status.
	 */
	public function get_rajaongkir_status( $api_key, $api_type ) {
		return $this->api->get_rajaongkir_status( $api_key, $api_type );
	}

	/**
	 * Get rajaongkir v2 status
	 *
	 * @param  string $api_key  API key.
	 * @return boolean          API status.
	 */
	public function get_rajaongkir2_status( $api_key ) {
		return $this->api->get_rajaongkir2_status( $api_key );
	}
}
