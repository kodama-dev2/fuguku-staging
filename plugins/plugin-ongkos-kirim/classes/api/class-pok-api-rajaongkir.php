<?php

/**
 * POK API Rajaongkir
 */
class POK_API_RajaOngkir {

	/**
	 * API base URL
	 *
	 * @var string
	 */
	protected $base_url;

	/**
	 * Services data
	 *
	 * @var array
	 */
	protected $services;

	/**
	 * API key
	 * 
	 * @var string
	 */
	protected $api_key;

	/**
	 * API type
	 * 
	 * @var string
	 */
	protected $type;

	/**
	 * REST default args
	 * 
	 * @var array;
	 */
	protected $default_args;

	/**
	 * POK Settings
	 * 
	 * @var POK_Setting
	 */
	protected $setting;

	/**
	 * POK Logs
	 * 
	 * @var POK_LOGS
	 */
	protected $logs;

	/**
	 * Constructor
	 *
	 * @param string $api_key Rajaongkir API key.
	 * @param string $type    API type.
	 */
	public function __construct( $api_key, $type ) {
		global $wp_version;
		$this->api_key = $api_key;
		$this->type = $type;
		if ( 'pro' === $type ) {
			$this->base_url     = 'http://pro.rajaongkir.com/api';
		} else {
			$this->base_url     = 'http://api.rajaongkir.com/' . $type;
		}
		$this->default_args = array(
			'timeout'     => 60,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(
				'key' => $this->api_key,
			),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$this->setting      = new POK_Setting();
		$this->logs         = new TJ_Logs( POK_LOG_NAME );
		$this->load_local_data();
	}

	/**
	 * Load local JSON data
	 */
	private function load_local_data() {

		// using ob instead of wp_filesystem to avoid file permission problem.
		// stupid way, but works.
		ob_start();
		include POK_PLUGIN_PATH . 'data/rajaongkir_services.json';
		$json_services = ob_get_contents();
		ob_end_clean();

		$this->services     = json_decode( $json_services, true );
	}

	/**
	 * Populate output from API response
	 *
	 * @param  string $url  URL to fetch.
	 * @param  array  $args Fetch args.
	 * @return array        Sanitized API response.
	 */
	private function remote_get( $url, $args ) {
		if ( 'yes' === $this->setting->get( 'temp_disable_api_rajaongkir' ) ) {
			return array(
				'status'    => false,
				'data'      => __( 'API disabled by debugger', 'pok' ),
			);
		}
		$content = wp_remote_get( $url, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API Rajaongkir) Trying fetch ' . $url . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'data'      => 'Please try again ( Error: ' . $content->get_error_message() . ' )',
			);
		}
		$body = json_decode( $content['body'] );
		if ( isset( $body->rajaongkir->status->code ) && 200 !== $body->rajaongkir->status->code ) {
			$this->logs->write( '(Error API Rajaongkir) Trying fetch ' . $url . '. Error: ' . ( isset( $body->rajaongkir->status->description ) ? $body->rajaongkir->status->description : '' ) );
			return array(
				'status'    => false,
				'data'      => isset( $body->rajaongkir->status->description ) ? $body->rajaongkir->status->description : '',
			);
		}
		return array(
			'status'    => true,
			'data'      => isset( $body->rajaongkir->result ) ? $body->rajaongkir->result : $body->rajaongkir->results,
		);
	}

	/**
	 * Populate output from API response
	 *
	 * @param  string $url  URL to fetch.
	 * @param  array  $args Fetch args.
	 * @return array        Sanitized API response.
	 */
	private function remote_post( $url, $args ) {
		$content = wp_remote_post( $url, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API Rajaongkir) Trying fetch ' . $url . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'data'      => 'Please try again ( Error: ' . $content->get_error_message() . ' )',
			);
		}
		$body = json_decode( $content['body'] );
		if ( isset( $body->rajaongkir->status->code ) && 200 !== $body->rajaongkir->status->code ) {
			$this->logs->write( '(Error API Rajaongkir) Trying fetch ' . $url . '. Error: ' . ( isset( $body->rajaongkir->status->description ) ? $body->rajaongkir->status->description : '' ) );
			return array(
				'status'    => false,
				'data'      => isset( $body->rajaongkir->status->description ) ? $body->rajaongkir->status->description : '',
			);
		}
		if ( ! isset( $body->rajaongkir->results ) ) {
			return array(
				'status'    => false,
				'data'      => isset( $body->rajaongkir->status->description ) ? $body->rajaongkir->status->description : '',
			);
		}
		return array(
			'status'    => true,
			'data'      => $body->rajaongkir->results,
		);
	}

	/**
	 * Get API status
	 *
	 * @return mixed API response.
	 */
	public function get_api_status() {
		$status = $this->get_province();
		if ( true === $status['status'] ) {
			return true;
		} else {
			return $status['data'];
		}
	}

	/**
	 * Get courier services
	 *
	 * @return array Courier services.
	 */
	public function get_courier_service() {
		return $this->services;
	}

	/**
	 * Get province list
	 *
	 * @return array Province options.
	 */
	public function get_province() {
		return $this->remote_get( $this->base_url . '/province', $this->default_args );
	}

	/**
	 * Get cities by province
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City list.
	 */
	public function get_city( $province_id = 0 ) {
		return $this->remote_get( $this->base_url . '/city?province=' . $province_id, $this->default_args );
	}

	/**
	 * Get single city
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		return $this->remote_get( $this->base_url . '/city?id=' . $city_id, $this->default_args );
	}

	/**
	 * Get all cities
	 *
	 * @return array City list.
	 */
	public function get_all_city() {
		return $this->remote_get( $this->base_url . '/city', $this->default_args );
	}

	/**
	 * Get disctricts by city
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District list
	 */
	public function get_district( $city_id = 0 ) {
		if ( 'pro' !== $this->type ) {
			return array(
				'status'    => false,
				'data'      => 'This method only for PRO license',
			);
		}
		return $this->remote_get( $this->base_url . '/subdistrict?city=' . $city_id, $this->default_args );
	}

	/**
	 * Get all countries
	 *
	 * @return array Country list
	 */
	public function get_all_country() {
		if ( 'starter' === $this->type ) {
			return array(
				'status'    => false,
				'data'      => 'This method only for PRO/BASIC license',
			);
		}
		return $this->remote_get( $this->base_url . '/v2/internationalDestination', $this->default_args );
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination (district/city) ID.
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Shipping costs.
	 */
	public function get_cost( $origin, $destination, $weight, $courier ) {
		$weight = intval( $weight ); // fix error when weight is float.

		// remove international only couriers.
		$key = array_search( 'expedito', $courier, true );
		if ( false !== $key ) {
			unset( $courier[ $key ] );
		}

		// remove POS if weight above 50kg.
		if ( 50000 < $weight ) {
			$key = array_search( 'pos', $courier, true );
			if ( false !== $key ) {
				unset( $courier[ $key ] );
			}
		}

		$result = array();
		$params = array(
			'origin'        => $origin,
			'originType'    => 'city',
			'destination'   => $destination,
			'destinationType' => 'pro' === $this->type ? 'subdistrict' : 'city',
			'weight'        => $weight,
		);
		if ( 'pro' === $this->type || 'basic' === $this->type ) {
			$params['courier'] = implode( ':',$courier );
			$args = $this->default_args;
			$args['headers']['Content-Type'] = 'application/json';
			$args['body'] = wp_json_encode( $params );
			$response = $this->remote_post( $this->base_url . '/cost', $args );
			if ( $response['status'] && ! empty( $response['data'] ) ) {
				$result = $response['data'];
			}
		} else {
			// terpaksa dibuat loop karena rajaongkir ngasih response Bad Request kalo pake multiple courier sekali request.
			foreach ( $courier as $key => $value ) {
				$params['courier'] = $value;
				$args = $this->default_args;
				$args['headers']['Content-Type'] = 'application/json';
				$args['body'] = wp_json_encode( $params );

				$response = $this->remote_post( $this->base_url . '/cost', $args );
				if ( $response['status'] && ! empty( $response['data'][0] ) ) {
					$result[] = $response['data'][0];
				}
			}
		}
		return $result;
	}

	/**
	 * Get international shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination country ID.
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Shipping costs.
	 */
	public function get_cost_international( $origin, $destination, $weight, $courier ) {
		$result = array();
		$courier = array_intersect( $courier, array( 'pos', 'tiki', 'jne', 'slis', 'expedito' ) );
		foreach ( $courier as $key => $value ) {
			$params = array(
				'origin'        => $origin,
				'originType'    => 'city',
				'destination'   => $destination,
				'weight'        => $weight,
				'courier'       => $value,
			);
			$args = $this->default_args;
			$args['headers']['Content-Type'] = 'application/json';
			$args['body'] = wp_json_encode( $params );

			$response = $this->remote_post( $this->base_url . '/v2/internationalCost', $args );
			if ( $response['status'] && ! empty( $response['data'][0] ) ) {
				$result[] = $response['data'][0];
			}
		}
		return $result;
	}

	/**
	 * Get API key status
	 *
	 * @param  string $api_key Rajaongkir API Key.
	 * @param  string $type    API type.
	 * @return boolean         Key status.
	 */
	public function get_key_status( $api_key, $type ) {
		if ( 'pro' === $type ) {
			$base_url   = 'http://pro.rajaongkir.com/api';
		} else {
			$base_url   = 'https://api.rajaongkir.com/' . $type;
		}
		$args = $this->default_args;
		$args['headers']['key'] = $api_key;
		$content = wp_remote_get( $base_url . '/province', $args );
		if ( ! is_wp_error( $content ) ) {
			$body = json_decode( $content['body'] );
			if ( isset( $body->rajaongkir->status->code ) && 200 === $body->rajaongkir->status->code ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get currency
	 *
	 * @return array Currency data.
	 */
	public function get_currency() {
		if ( 'starter' === $this->type ) {
			return array(
				'status'    => false,
				'data'      => 'This method only for PRO/BASIC license',
			);
		}
		return $this->remote_get( $this->base_url . '/currency', $this->default_args );
	}

}
