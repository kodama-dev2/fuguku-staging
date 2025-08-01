<?php

if ( ! defined( 'PLUGINONGKOSKIRIM_BASE_API' ) ) {
	define( 'PLUGINONGKOSKIRIM_BASE_API', 'https://pluginongkoskirim.com/cek-tarif-ongkir/api' );
}

/**
 * POK API Tonjoo
 */
class POK_API_Tonjoo {

	/**
	 * POK Settings
	 * 
	 * @var POK_Setting
	 */
	protected $setting;

	/**
	 * POK Logs
	 * 
	 * @var POK_Logs
	 */
	protected $logs;

	/**
	 * API base url
	 *
	 * @var string
	 */
	protected $base_url;

	/**
	 * API url param
	 *
	 * @var string
	 */
	protected $api_param;

	/**
	 * API default args
	 *
	 * @var array
	 */
	protected $default_args;

	/**
	 * Provinces data
	 *
	 * @var array
	 */
	protected $provinces;

	/**
	 * Cities data
	 *
	 * @var array
	 */
	protected $cities;

	/**
	 * Services data
	 *
	 * @var array
	 */
	protected $services;

	/**
	 * Origina data
	 *
	 * @var array
	 */
	protected $origins;

	/**
	 * Constructor
	 *
	 * @param string $license_key License key.
	 */
	public function __construct( $license_key ) {
		global $wp_version;
		$this->base_url     = PLUGINONGKOSKIRIM_BASE_API;
		$this->api_param    = '?license=' . $license_key . '&website=' . $this->get_web_url();
		$this->default_args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => false,
			'stream'      => false,
			'filename'    => null,
		);
		$this->logs         = new TJ_Logs( POK_LOG_NAME );
		$this->setting      = new POK_Setting();
		$this->load_local_data();
	}

	/**
	 * Get current web base URL
	 *
	 * @return string URL.
	 */
	public function get_web_url() {
		preg_match_all( '#^.+?[^\/:](?=[?\/]|$)#', get_site_url(), $matches );
		return $matches[0][0];
	}

	/**
	 * Load local JSON data
	 */
	private function load_local_data() {

		// using ob instead of wp_filesystem to avoid file permission problem.
		// stupid way, but works.
		ob_start();
		include POK_PLUGIN_PATH . 'data/provinces.json';
		$json_provinces = ob_get_contents();
		ob_end_clean();

		ob_start();
		include POK_PLUGIN_PATH . 'data/cities.json';
		$json_cities = ob_get_contents();
		ob_end_clean();

		ob_start();
		include POK_PLUGIN_PATH . 'data/tonjoo_services.json';
		$json_services = ob_get_contents();
		ob_end_clean();

		ob_start();
		include POK_PLUGIN_PATH . 'data/tonjoo_origins.json';
		$json_origins = ob_get_contents();
		ob_end_clean();

		$this->provinces = json_decode( $json_provinces );
		$this->cities    = json_decode( $json_cities );
		$this->services  = json_decode( $json_services, true );
		$this->origins   = json_decode( $json_origins );
	}

	/**
	 * Populate API response
	 *
	 * @param  string $url  URL to fetch.
	 * @param  array  $args Fetch args.
	 * @return array        Sanitized API response.
	 */
	private function remote_get( $url, $args ) {
		if ( 'yes' === $this->setting->get( 'temp_disable_api_tonjoo' ) ) {
			return array(
				'status'    => false,
				'data'      => __( 'API disabled by debugger', 'pok' ),
			);
		}
		$content = wp_remote_get( $url, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'data'      => 'Please try again ( Error: ' . $content->get_error_message() . ' )',
			);
		}
		if ( 200 !== $content['response']['code'] ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error code: ' . $content['response']['code'] );
			return array(
				'status'    => false,
				'data'      => 'Error code: ' . $content['response']['code'],
			);
		}
		$body = json_decode( $content['body'] );
		if ( isset( $body->error ) && $body->error ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error: ' . ( isset( $body->message ) ? $body->message : '' ) );
			return array(
				'status'    => false,
				'data'      => isset( $body->message ) ? $body->message : '',
			);
		}
		return array(
			'status'    => true,
			'data'      => isset( $body->data ) ? $body->data : $body,
		);
	}

	/**
	 * Get API status
	 *
	 * @return mixed API response.
	 */
	public function get_api_status() {
		$status = $this->remote_get( $this->base_url . '/ekspedisi/' . $this->api_param, $this->default_args );
		if ( true === $status['status'] ) {
			return true;
		} else {
			return $status['data'];
		}
	}

	/**
	 * Get courier services
	 * Since 3.3.0 we use local data to get courier services
	 *
	 * @return array Courier services.
	 */
	public function get_courier_service() {
		// return $this->remote_get( $this->base_url . '/ekspedisi/' . $this->api_param, $this->default_args );
		return $this->services;
	}

	/**
	 * Get origins
	 *
	 * @return array City options
	 */
	public function get_origins() {
		return $this->origins;
	}

	/**
	 * Get province
	 *
	 * @return array Province options
	 */
	public function get_province() {
		return $this->provinces;
	}

	/**
	 * Get city by province
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City options.
	 */
	public function get_city( $province_id = 0 ) {
		$cities = array();
		if ( isset( $this->provinces->{ $province_id }->cities ) ) {
			foreach ( $this->provinces->{ $province_id }->cities as $city_id ) {
				$cities[ $city_id ] = $this->cities->{ $city_id }->type . ' ' . $this->cities->{ $city_id }->name;
			}
		}
		return $cities;
	}

	/**
	 * Get single city by ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		$city = '';
		if ( isset( $this->cities->{$city_id} ) ) {
			$city = $this->cities->{$city_id}->type . ' ' . $this->cities->{$city_id}->name . ', ' . $this->provinces->{ $this->cities->{$city_id}->province }->name;
		}
		return $city;
	}

	/**
	 * Get all city by search param
	 *
	 * @param  string $search Search param.
	 * @return array          City options.
	 */
	public function get_all_city( $search = '' ) {
		return $this->remote_get( $this->base_url . '/asal' . $this->api_param . '&s=' . $search, $this->default_args );
	}

	/**
	 * Get disctricts
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District options.
	 */
	public function get_district( $city_id ) {
		if ( 0 === intval( $city_id ) ) {
			return array();
		}
		if ( ! isset( $this->cities->{ $city_id } ) ) {
			return array();
		}
		return json_decode( json_encode( $this->cities->{ $city_id }->districts ), true ); // avoid array casting problem.
	}

	/**
	 * Search city for simple address field
	 *
	 * @param  string $search Search param.
	 * @return array          City options.
	 */
	public function search_simple_address( $search = '' ) {
		$cities = array();
		ob_start();
		include POK_PLUGIN_PATH . 'data/cities_search.json';
		$json_cities = ob_get_contents();
		ob_end_clean();
		$cities = json_decode( $json_cities, true );
		$result = preg_grep( '/(.*?)' . str_replace( ' ', '(.*?)', $search ) . '(.*?)/i', $cities );
		return array_slice( $result, 0, 20 );
	}

	/**
	 * Get simple address by ID
	 *
	 * @param  string $id City ID.
	 * @return string     City name.
	 */
	public function get_simple_address( $id = '' ) {
		$cities = array();
		ob_start();
		include POK_PLUGIN_PATH . 'data/cities_search.json';
		$json_cities = ob_get_contents();
		ob_end_clean();
		$cities = json_decode( $json_cities, true );
		
		return isset( $cities[ $id ] ) ? $cities[ $id ] : '';
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination (district) ID.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Shipping costs.
	 */
	public function get_cost( $origin, $destination, $courier ) {
		$cur = '';
		foreach ( $courier as $key => $value ) {
			if ( 'jnt' === $value ) {
				$value = 'j&t';
			}
			$cur .= '&ekspedisi[' . $key . ']=' . urlencode( $value );
		}
		return $this->remote_get( $this->base_url . '/tarif/' . $origin . '/tujuan/' . $destination . $this->api_param . '&jenis=kecamatan' . $cur, $this->default_args );
	}

}
