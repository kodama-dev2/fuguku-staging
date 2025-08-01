<?php

/**
 * POK Helper Class
 */
class POK_Helper {

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
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler_3 $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler_3 $license ) {
		$this->license = $license;
		$this->setting = new POK_Setting();
	}

	/**
	 * Get courier names
	 *
	 * @param  string $courier Coureir code.
	 * @return string          Courier name.
	 */
	public function get_courier_name( $courier = '' ) {
		$couriers = apply_filters(
			'pok_courier_names', array(
				'jne'       => 'JNE',
				'pos'       => 'POS',
				'tiki'      => 'TIKI',
				'jnt'       => 'J&T',
				'jntcargo'  => 'J&T Cargo',
				'wahana'    => 'Wahana',
				'sicepat'   => 'Sicepat',
				'lion'		=> 'Lion Parcel',
				'ninja'		=> 'Ninja Xpress',
				'anteraja'	=> 'Anteraja',
				'esl'       => 'ESL',
				'ncs'       => 'NCS',
				'pcp'       => 'PCP Express',
				'rpx'       => 'RPX',
				'pandu'     => 'Pandu Logistics',
				'pahala'    => 'Pahala Express',
				'cahaya'    => 'Cahaya Logistik',
				'sap'       => 'SAP Express',
				// 'jet'       => 'JET Express',
				'indah'     => 'Indah Logistik Cargo',
				'dse'       => '21 Express',
				'slis'      => 'Solusi Express',
				'expedito'  => 'Expedito',
				'first'     => 'First Logistics',
				'star'      => 'Star Cargo',
				'nss'       => 'NSS Express',
				'idl'		=> 'IDL Cargo',
				'rex'		=> 'REX',
				'atlas'		=> 'Atlas Express',
				'ide'       => 'ID Express',
				'sentral'   => 'Sentral Cargo',
				'jtl'		=> 'JTL Express'
			)
		);
		if ( isset( $couriers[ $courier ] ) ) {
			return $couriers[ $courier ];
		}
		return $courier;
	}

	/**
	 * Get courier logo
	 *
	 * @param  string $courier Coureir code.
	 * @return string          Courier logo url.
	 */
	public function get_courier_logo( $courier = '' ) {
		if ( 'lion parcel' == $courier ) {
			$courier = 'lion';
		} elseif ( 'atlas express' == $courier ) {
			$courier = 'atlas';
		} elseif ( 'id express' == $courier ) {
			$courier = 'ide';
		}
		return apply_filters( 'pok_courier_logo_' . $courier, POK_PLUGIN_URL . '/assets/img/logo-' . $courier . '.png' );
	}

	/**
	 * Sanitize service name from API
	 *
	 * @param  string $courier Courier name.
	 * @param  string $service Original service name.
	 * @param  string $type    Long or Short name?.
	 * @return string          Sanitized name
	 */
	public function convert_service_name( $courier, $service ) {
		global $pok_core;
		$services = $pok_core->get_courier_service();
		if ( isset( $services[ strtolower( $courier ) ][ sanitize_title( $service ) ][ 'name' ] ) ) {
			return $services[ strtolower( $courier ) ][ sanitize_title( $service ) ][ 'name' ];
		}
		return $service;
	}

	/**
	 * Get custom service name.
	 * 
	 * @param  string $courier Courier name.
	 * @param  string $service Original service name.
	 * @return string          Custom name
	 */
	public function get_custom_service_name( $courier, $service ) {
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			return false;
		}
		$custom_names = $this->setting->get( 'custom_service_name' );
		if ( isset( $custom_names[ $this->setting->get( 'base_api' ) ] ) && ! empty( $custom_names[ $this->setting->get( 'base_api' ) ] ) ) {
			foreach ( $custom_names[ $this->setting->get( 'base_api' ) ] as $key => $value ) {
				if ( $value['courier'] === strtolower( $courier ) && $value['service'] === sanitize_title( $service ) ) {
					return $value['name'];
				}
			}
		}
		return false;
	}

	/**
	 * Get license type
	 *
	 * @return string Type.
	 */
	public function get_license_type() {
		if ( 'nusantara' === $this->setting->get( 'base_api' ) || 'rajaongkir2' === $this->setting->get( 'base_api' ) ) {
			return 'pro';
		} else {
			if ( 'pro' === $this->setting->get( 'rajaongkir_type' ) ) {
				return 'pro';
			} else {
				return 'default';
			}
		}
	}

	/**
	 * Get country on session
	 *
	 * @param  string $context    Context.
	 * @return string Country id.
	 */
	public function get_country_session( $context = 'billing' ) {
		$country      = '';

		if ( class_exists( 'WC' ) && null !== WC()->customer ) {
			if( 'billing' === $context ) {
				$country = WC()->customer->get_billing_country();
			} else {
				$country = WC()->customer->get_shipping_country();
			}
		}

		if( empty( $country ) ) {
			$country = 'ID';
		}

		// add filter hook to force change country ability.
		return apply_filters( 'pok_session_country', $country, $context );
	}

	/**
	 * Check the license status
	 *
	 * @param  boolean $force Force check?.
	 * @return boolean License status.
	 */
	public function is_license_active( $force = false ) {
		if ( true === $force ) {
			$this->license->check_status();
		}
		return $this->license->get( 'active' );
	}

	/**
	 * Get status of the API, useful for debugging.
	 *
	 * @param  string $api Base API.
	 * @return boolean      Check if api active or not.
	 */
	public function get_api_status( $api = '' ) {
		global $pok_core;
		if ( empty( $api ) ) {
			$api = $this->setting->get( 'base_api' );
		}
		return $pok_core->get_api_status( $api );
	}

	/**
	 * Get rajaongkir status
	 *
	 * @return boolean Rajaongkir status
	 */
	public function is_rajaongkir_active() {
		$rajaongkir_key = $this->setting->get( 'rajaongkir_key' );
		if ( empty( $rajaongkir_key ) ) {
			return false;
		}
		$rajaongkir_status = $this->setting->get( 'rajaongkir_status' );
		if ( false === $rajaongkir_status[0] ) {
			return false;
		}
		return true;
	}

	/**
	 * Is plugin active
	 *
	 * @return boolean Status.
	 */
	public function is_plugin_active() {
		// for front.
		if ( 'no' === $this->setting->get( 'enable' ) ) {
			return false;
		}

		// plugin ongkos kirim license status.
		if ( ! $this->is_license_active() ) {
			return false;
		}

		// curl must active.
		if ( ! function_exists( 'curl_version' ) ) {
			return false;
		}

		// rajaongkir status.
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			if ( ! $this->is_rajaongkir_active() ) {
				return false;
			}
		}

		// base city.
		$base_city = $this->setting->get( 'store_location' );
		if ( empty( $base_city ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Compare current WC version
	 *
	 * @param  string $operator Comparing operator.
	 * @param  string $version  Version to check.
	 * @return boolean           Version satatus.
	 */
	public function compare_wc_version( $operator = '>=', $version = '3.0' ) {
		global $woocommerce;
		return version_compare( $woocommerce->version, $version, $operator );
	}

	/**
	 * Is admin active
	 *
	 * @return boolean Status
	 */
	public function is_admin_active() {
		// for admin.
		// plugin ongkos kirim license status.
		if ( ! $this->is_license_active() ) {
			return false;
		}

		// rajaongkir status.
		if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			if ( ! $this->is_rajaongkir_active() ) {
				return false;
			}
		}

		if ( $this->license->is_license_expired() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if woocommerce is active
	 *
	 * @return boolean Is active.
	 */
	public static function is_woocommerce_active() {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		return class_exists( 'WooCommerce' ) || in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Check if WooCommerce Multilingual is active
	 *
	 * @return boolean Is active.
	 */
	public static function is_wpml_multi_currency_active() {
		return in_array( 'woocommerce-multilingual/wpml-woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) && wcml_is_multi_currency_on();
	}

	/**
	 * Clear all cached WC's shipping costs
	 */
	public function clear_cached_costs() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '%wc_ship%'" );
	}

	/**
	 * Get product weight.
	 * If volume calculation enabled, the weight is calculated from product dimensions
	 * But if product weight is higher than weight from calculated dimensions, use the product weight instead.
	 *
	 * @param  object $product Product object.
	 * @return float           Product weight on kg.
	 */
	public function get_product_weight( $product ) {
		if ( ! $product || $product->is_virtual() ) {
			return 0;
		}
		if ( 'yes' === $this->setting->get( 'enable_volume_calculation' ) && ! empty( $product->get_length() ) && ! empty( $product->get_width() ) && ! empty( $product->get_height() ) ) {
			$product_weight = ( $this->dimension_convert( $product->get_length() ) * $this->dimension_convert( $product->get_width() ) * $this->dimension_convert( $product->get_height() ) ) / 6000;
			if ( $product->has_weight() ) {
				$product_weight = max( $product_weight, $this->weight_convert( $product->get_weight() ) ); // get highest value between volumetric or weight.
			}
		} else {
			$product_weight = $product->has_weight() ? $this->weight_convert( $product->get_weight() ) : $this->setting->get( 'default_weight' );
		}
		return apply_filters( 'pok_get_product_weight', $product_weight, $product, $this->setting->get_all() );
	}

	/**
	 * Get order total weight
	 *
	 * @param  object $order Order object.
	 * @return float         Total weight.
	 */
	public function get_order_weight( $order ) {
		$weight = 0;
		foreach ( $order->get_items() as $item ) {
			$weight += ( $this->get_product_weight( $item->get_product() ) * $item->get_quantity() );
		}
		return apply_filters( 'pok_get_order_weight', $weight, $order );
	}

	/**
	 * Round weight
	 *
	 * @param  float $weight Weight.
	 * @return int           Rounded weight.
	 */
	public function round_weight( $weight = 0 ) {
		$method = $this->setting->get( 'round_weight' );
		if ( 'ceil' === $method ) {
			$round = ceil( $weight );
		} elseif ( 'floor' === $method ) {
			$round = floor( $weight );
		} elseif ( 'auto' === $method ) {
			$tolerance = ( $this->setting->get( 'round_weight_tolerance' ) + 0.0001 ) / 1000;
			$fraction = fmod( $weight, 1 );
			if ( $fraction <= $tolerance ) {
				$round = floor( $weight );
			} else {
				$round = ceil( $weight );
			}
		} else {
			$round = $weight;
		}
		return apply_filters( 'pok_round_weight', $round );
	}

	/**
	 * Get total weight of cart contents
	 *
	 * @param  array $contents Cart contents.
	 * @return float            Cart weight.
	 */
	public function get_total_weight( $contents ) {
		$weight = 0;
		foreach ( $contents as $content ) {
			$weight += ( $this->get_product_weight( $content['data'] ) * $content['quantity'] );
		}
		return apply_filters( 'pok_get_cart_weight', $weight, $contents );
	}

	/**
	 * Convert current weight to kilo
	 *
	 * @param  float $weight Current weight.
	 * @return float          Converted weight.
	 */
	public function weight_convert( $weight = 0 ) {
		$wc_unit = strtolower( get_option( 'woocommerce_weight_unit', 'kg' ) );
		if ( 'kg' !== $wc_unit ) {
			switch ( $wc_unit ) {
				case 'g':
					$weight *= 0.001;
					break;
				case 'lbs':
					$weight *= 0.4535;
					break;
				case 'oz':
					$weight *= 0.0283495;
					break;
			}
		}
		return apply_filters( 'pok_weight_convert', $weight );
	}

	/**
	 * Convert current dimension to cm
	 *
	 * @param  float $dimension Current dimension.
	 * @return float            Converted dimension.
	 */
	public function dimension_convert( $dimension = 0 ) {
		$dimension = floatval( $dimension );
		$wc_unit = strtolower( get_option( 'woocommerce_dimension_unit', 'cm' ) );
		if ( 'cm' !== $wc_unit ) {
			switch ( $wc_unit ) {
				case 'm':
					$dimension *= 100;
					break;
				case 'mm':
					$dimension *= 0.1;
					break;
				case 'in':
					$dimension *= 2.54;
					break;
				case 'yd':
					$dimension *= 91.44;
					break;
			}
		}
		return apply_filters( 'pok_dimension_convert', $dimension );
	}

	/**
	 * Get insurance from specific courier
	 *
	 * @param  string $courier Courier type.
	 * @param  float  $total   Total price (optional).
	 * @return float           Insurance fee.
	 */
	public function get_insurance( $courier, $total = null ) {
		if ( is_null( $total ) ) {
			$total = WC()->cart->get_subtotal();
		}
		if ( 0 === floatval( $total ) ) {
			return 0;
		}
		$insurance = 0;
		switch ( $courier ) {
			case 'jne':
				// https://harga.web.id/biaya-asuransi-pengiriman-jne.info.
				$insurance = ( 0.002 * $total ) + 5000;
				break;
			case 'pos':
				$insurance = 0.0024 * $total;
				break;
			case 'tiki':
				$insurance = 0.003 * $total;
				break;
			case 'jnt':
				//https://www.facebook.com/jntexpressindonesia/posts/taukah-kamubiaya-asuransi-dihitung-02-dari-harga-invoice-barang-yang-kamu-kirimk/2219505118367379/.
				$insurance = 0.002 * $total;
				break;
			case 'j&t': // alias.
				// https://www.facebook.com/jntexpressindonesia/posts/taukah-kamubiaya-asuransi-dihitung-02-dari-harga-invoice-barang-yang-kamu-kirimk/2219505118367379/.
				$insurance = 0.002 * $total;
				break;
			case 'wahana':
				$insurance = 0.005 * $total;
				break;
			case 'rpx':
				if ( 20000000 <= $total ) {
					$insurance = 0.005 * $total;
				}
				break;
			case 'esl':
				$insurance = ( 0.002 * $total ) + 5000;
				break;
			case 'pandu':
				$insurance = 0.0035 * $total;
				break;
			case 'pahala':
				$insurance = 0.002 * $total;
				break;
			case 'indah':
				if ( ( 0.003 * $total ) < 101000 ) {
					$insurance = 101000;
				} else {
					$insurance = 0.003 * $total;
				}
				break;
			case 'star':
				if ( ( 0.003 * $total ) < 101000 ) {
					$insurance = 101000;
				} else {
					$insurance = 0.003 * $total;
				}
				break;
			case 'sicepat':
				// https://web.facebook.com/sicepatekspresofficial/photos/biar-paket-kamu-makin-aman-saat-pengiriman-ada-baiknya-kamu-asuransikan-paket-ka/2498811827025189/?_rdc=1&_rdr
				$insurance = 0.002 * $total;
				break;
			case 'lion':
				// http://www.lionparcelsragen.com/tanyajawab
				$insurance = 0.0015 * $total;
				break;
		}
		return apply_filters( 'pok_set_insurance', round( $insurance ), $courier, $total );
	}

	/**
	 * Convert rajaongkir country name
	 *
	 * @param  string $country Country name.
	 * @return string          Country name.
	 */
	public function rajaongkir_country_name( $country ) {
		$countries = apply_filters(
			'pok_rajaongkir_country_name', array(
				'American Samoa'        => 'Western Samoa',
				'Andorra'               => 'Andora',
				'Bonaire, Saint Eustatius and Saba' => 'Bonaire',
				'Bosnia and Herzegovina' => 'Bosnia & Herzegovina',
				'British Virgin Islands' => 'Virgin Islands (British)',
				'Brunei'                => 'Brunei Darussalam',
				'Bulgaria'              => 'Bulgaria (rep)',
				'Central African Republic' => 'Central African Rep',
				'China'                 => 'China (people_s rep)',
				'Congo (Brazzaville)'   => 'Congo (rep)',
				'Congo (Kinshasa)'      => 'Zaire',
				'Cook Islands'          => 'Cook islands',
				'Costa Rica'            => 'Costa rica',
				'Curaçao'               => 'Curacao',
				'Czech Republic'        => 'Czech Rep',
				'Falkland Islands'      => 'Falkand Islands',
				'Guernsey'              => 'Guerensey',
				'Honduras'              => 'Honduras (rep)',
				'Hong Kong'             => 'Hongkong',
				'Hungary'               => 'Hungary (rep)',
				'Iran'                  => 'Iran (Islamic rep)',
				'Ivory Coast'           => 'Cote D_Ivoire (rep)- Pantai Gading',
				'Kyrgyzstan'            => 'Kyrgysztan',
				'Laos'                  => 'Laos People_s Dem Rep',
				'Libya'                 => 'Libyan Jamahiriya',
				'Macao S.A.R., China'   => 'Macao',
				'Macedonia'             => 'Former Yugoslavian  Rep. of Macedonia',
				'Moldova'               => 'Moldova, Rep. of',
				'Montenegro'            => 'Montenegro Rep The',
				'North Korea'           => 'Dem People_s Rep of Korea',
				'Northern Mariana Islands' => 'Mariana Islands',
				'Poland'                => 'Poland (rep)',
				'Reunion'               => 'Reunion, Island of',
				'Russia'                => 'Russian Federation',
				'Saint Barthélemy'      => 'St. Barthelemy',
				'Saint Kitts and Nevis' => 'St. Kitts',
				'Saint Lucia'           => 'St. Lucia',
				'Saint Martin (Dutch part)' => 'St. Maarten',
				'Saint Martin (French part)' => 'St. Maarten',
				'Saint Vincent and the Grenadines' => 'St. Vincent',
				'Samoa'                 => 'Samoa Barat',
				'Serbia'                => 'Serbia Rep The',
				'Slovakia'              => 'Slovak Rep',
				'South Korea'           => 'Korea (rep)',
				'South Sudan'           => 'Sudan',
				'Syria'                 => 'Syrian Arab Rep.',
				'Tanzania'              => 'Tanzania (United Rep)',
				'Turks and Caicos Islands' => 'Turks Cay Islands',
				'United Kingdom (UK)'   => 'Great Britain (Inggris)',
				'United States (US)'    => 'United States of America',
				'United States (US) Virgin Islands' => 'Virgin Islands (USA)',
				'Vietnam'               => 'Viet nam',
			)
		);
		if ( ! empty( $countries[ $country ] ) ) {
			return $countries[ $country ];
		}
		return $country;
	}

	/**
	 * Generate random numbers
	 *
	 * @param  integer $length Length.
	 * @return integer         Random number.
	 */
	public function random_number( $length = 1 ) {
		$char = '0123456789';
		$length = intval( $length );
		if ( in_array( $length, array(10,20,30), true ) ) {
			$string = '0.';
			$length = $length/10;
		} else {
			$string = '';
		}
		for ( $i = 0; $i < $length; $i++ ) {
			$pos = rand( 0, strlen( $char ) - 1 );
			$string .= $char[$pos];
		}
		return floatval( $string );
	}

	/**
	 * Convert currency
	 *
	 * @param  float  $price  Current price.
	 * @param  string $symbol Currency symbol.
	 * @return float          Converted price.
	 */
	public function currency_convert( $price = 0, $symbol = '' ) {
		if ( empty( $symbol ) ) {
			$symbol = get_option( 'woocommerce_currency', 'IDR' );
		}
		$method = $this->setting->get( 'currency_conversion' );
		if ( 'IDR' === $symbol || 'dont_convert' === $method ) {
			$rate = 1;
		} elseif ( 'fixer' === $method ) {
			if ( '' !== $this->setting->get( 'currency_fixer_api_key' ) ) {
				global $pok_core;
				$rate = $pok_core->get_fixer_rate( $symbol, $this->setting->get( 'currency_fixer_api_key' ) );
			}
		} elseif ( 'currencylayer' === $method ) {
			if ( '' !== $this->setting->get( 'currency_currencylayer_api_key' ) ) {
				global $pok_core;
				$rate = $pok_core->get_currencylayer_rate( $symbol, $this->setting->get( 'currency_currencylayer_api_key' ) );
			}
		} elseif ( 'wpml' === $method ) {
			if ( $this->is_wpml_multi_currency_active() ) {
				$wpml_rate = $this->get_wpml_rate();
				if ( false !== $wpml_rate ) {
					$rate = $wpml_rate;
				}
			}
		} elseif ( 'static' === $method ) {
			if ( 0 !== floatval( $this->setting->get( 'currency_static_conversion_rate' ) ) ) {
				$rate = floatval( $this->setting->get( 'currency_static_conversion_rate' ) );
			}
		}
		return $price *= apply_filters( 'pok_currency_conversion_rate', $rate, $method );
	}

	/**
	 * Get WPML's rupiah rate
	 *
	 * @return float Rupiah rate
	 */
	public function get_wpml_rate() {
		$wpml_option = get_option( '_wcml_settings', array() );
		if ( isset( $wpml_option['currency_options'] ) && isset( $wpml_option['currency_options']['IDR'] ) && isset( $wpml_option['currency_options']['IDR']['rate'] ) ) {
			$rate = floatval( $wpml_option['currency_options']['IDR']['rate'] );
			if ( 0 < $rate ) {
				return 1 / $rate;
			}
		}
		return false;
	}

	/**
	 * Get fixer's rate cache expiration
	 *
	 * @return integer Expiration in seconds
	 */
	public function get_fixer_rate_expiration() {
		$type = $this->setting->get( 'currency_fixer_api_type' );
		switch ( $type ) {
			case 'basic':
				$expiration = HOUR_IN_SECONDS;
				break;
			case 'professional':
				$expiration = 10 * MINUTE_IN_SECONDS;
				break;
			case 'professional_plus':
			case 'enterprise':
				$expiration = MINUTE_IN_SECONDS;
				break;
			default:
				$expiration = HOUR_IN_SECONDS;
				break;
		}
		return $expiration;
	}

	/**
	 * Get currencylayer's rate cache expiration
	 *
	 * @return integer Expiration in seconds
	 */
	public function get_currencylayer_rate_expiration() {
		$type = $this->setting->get( 'currency_currencylayer_api_type' );
		switch ( $type ) {
			case 'basic':
				$expiration = HOUR_IN_SECONDS;
				break;
			case 'professional':
				$expiration = 10 * MINUTE_IN_SECONDS;
				break;
			case 'enterprise':
				$expiration = MINUTE_IN_SECONDS;
				break;
			default:
				$expiration = HOUR_IN_SECONDS;
				break;
		}
		return $expiration;
	}

	/**
	 * Check if multi vendor addon active
	 *
	 * @return boolean Is active?
	 */
	public function is_multi_vendor_addon_active() {
		return ( class_exists( 'Plugin_Ongkos_Kirim_Multi_Vendor' ) || in_array( 'plugin-ongkos-kirim-multi-vendor/plugin-ongkos-kirim-multi-vendor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) && 'yes' === $this->setting->get('enable_multi_vendor');
	}

	/**
	 * Is insurance enable on given products
	 *
	 * @param  array $contents Cart contents.
	 * @return boolean           Enabled or not.
	 */
	public function is_enable_insurance( $contents ) {
		$enable = ( 'yes' === $this->setting->get( 'enable_insurance' ) ? true : false );
		if ( $contents instanceof WC_Order ) {
			foreach ( $contents->get_items() as $item ) {
				if ( 'set' === $this->setting->get( 'enable_insurance' ) && ( 'yes' === get_post_meta( $item->get_product_id(), 'enable_insurance', true ) || 'yes' === get_post_meta( $item->get_product()->get_parent_id(), 'enable_insurance', true ) ) ) {
					$enable = true;
				}
			}
		} elseif ( is_array( $contents ) ) {
			foreach ( $contents as $content ) {
				if ( 'set' === $this->setting->get( 'enable_insurance' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_insurance', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_insurance', true ) ) ) {
					$enable = true;
				}
			}
		}
		return apply_filters( 'pok_is_enable_insurance', $enable, $contents, $this->setting->get_all() );
	}

	/**
	 * Is timber packing enable on given products
	 *
	 * @param  array $contents Cart contents.
	 * @return boolean           Enabled or not.
	 */
	public function is_enable_timber_packing( $contents ) {
		$enable = ( 'yes' === $this->setting->get( 'enable_timber_packing' ) ? true : false );
		if ( $contents instanceof WC_Order ) {
			foreach ( $contents->get_items() as $content ) {
				$product = $content->get_product();
				if ( 'set' === $this->setting->get( 'enable_timber_packing' ) && ( 'yes' === get_post_meta( $product->get_id(), 'enable_timber_packing', true ) || 'yes' === get_post_meta( $product->get_parent_id(), 'enable_timber_packing', true ) ) ) {
					$enable = true;
				}
			}
		} elseif ( is_array( $contents ) ) {
			foreach ( $contents as $content ) {
				if ( 'set' === $this->setting->get( 'enable_timber_packing' ) && ( 'yes' === get_post_meta( $content['data']->get_id(), 'enable_timber_packing', true ) || 'yes' === get_post_meta( $content['data']->get_parent_id(), 'enable_timber_packing', true ) ) ) {
					$enable = true;
				}
			}
		}
		return apply_filters( 'pok_is_enable_timber_packing', $enable, $contents, $this->setting->get_all() );
	}

	/**
	 * Get address ids from order
	 *
	 * @param  integer $order_id Order ID.
	 * @param  string  $type     Address type.
	 * @return integer           Address id.
	 */
	public function get_address_id_from_order( $order_id = 0, $type = 'billing', $field = 'state' ) {
		if ( ! in_array( $type, array( 'billing', 'shipping' ), true ) || ! in_array( $field, array( 'country', 'state', 'city', 'district' ), true ) ) {
			return 0;
		}
		$field_to_check = array(
			"_{$type}_pok_{$field}", // > 3.8.0.
			"_{$type}_{$field}_id",
			"_{$type}_{$field}" // fallback.
		);
		$order = wc_get_order( $order_id );
		if ( false === $order ) {
			return 0;
		}
		foreach ( $field_to_check as $meta_key ) {
			$id = $order->get_meta( $meta_key, true );
			if ( ! empty( $id ) ) {
				return 'country' !== $field ? intval( $id ) : $id;
			}
		}
		return 0;
	}

	/**
	 * Get address ids from user
	 *
	 * @param  integer $user_id  User ID.
	 * @param  string  $type     Address type.
	 * @return integer           Address id.
	 */
	public function get_address_id_from_user( $user_id = 0, $type = 'billing', $field = 'state' ) {
		if ( ! in_array( $type, array( 'billing', 'shipping' ), true ) || ! in_array( $field, array( 'state', 'city', 'district' ), true ) ) {
			return 0;
		}
		$field_to_check = array(
			"{$type}_pok_{$field}", // > 3.8.0.
			"{$type}_{$field}_id",
			"{$type}_{$field}" // fallback.
		);
		foreach ( $field_to_check as $meta_key ) {
			$id = get_user_meta( $user_id, $meta_key, true );
			if ( ! empty( $id ) ) {
				return intval( $id );
			}
		}
		return 0;
	}

	/**
	 * Check if store only sell to Indonesia
	 *
	 * @return boolean Is sell to Indonesia
	 */
	public function is_store_only_sell_to_indonesia() {
		$allowed_countries = WC()->countries->get_allowed_countries();
		if ( 1 === count( $allowed_countries ) && isset( $allowed_countries['ID'] ) ) {
			$allowed_shipping = WC()->countries->get_shipping_countries();
			if ( 1 === count( $allowed_shipping ) && isset( $allowed_shipping['ID'] ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get store location
	 */
	public function get_store_location() {
		$store_location = $this->setting->get('store_location');
		if ( isset( $store_location[0] ) && ! empty( $store_location[0] ) ) {
			return $store_location[0];
		}
		return 0;
	}

	/**
	 * Check if need user to decide add insurance or not.
	 * 
	 * @return boolean Is need user decide
	 */
	public function is_let_user_decide_insurance() {
		return ( 'yes' === $this->setting->get( 'enable_insurance' ) || 'set' === $this->setting->get( 'enable_insurance' ) ) && 'by_user' === $this->setting->get( 'insurance_application' );
	}

	/**
	 * Get alternatice city id from other API
	 * 
	 * @param  integer $city_id Original City ID.
	 * @param  string  $target  Target API.
	 * @return integer          Alternative City ID.
	 */
	public function get_alt_city_id( $city_id = 0, $target = '' ) {
		global $pok_core;
		$new_city_id = 0;
		if ( empty( $city_id ) || empty( $target ) ) {
			return $city_id;
		}
		$origins = $pok_core->get_origins();
		foreach ( $origins as $origin ) {
			if ( 'nusantara' === $target && intval( $origin->roid ) === intval( $city_id ) ) {
				$new_city_id = $origin->id;
				break;
			} elseif ( intval( $origin->id ) === intval( $city_id ) ) {
				$new_city_id = $origin->roid;
				break;
			}
		}
		return $new_city_id;
	}

	/**
	 * Parse estimated time
	 * @param  string $etd Etd.
	 * @return string      Formatted Etd.
	 */
	public function format_etd( $etd ) {
		if ( '' === $etd || '-' === $etd ) {
			return '';
		}
		$explode = explode( '-', $etd );
		$from = ltrim( $explode[0], '0' );
		if ( ! isset( $explode[1] ) ) {
			return $from . ' ' . _n( 'day', 'days', intval( $from ), 'pok' );
		} else {
			$to = ltrim( $explode[1], '0' );
			if ( intval( $from ) === intval( $to ) ) {
				return $from . ' ' . _n( 'day', 'days', intval( $from ), 'pok' );
			} elseif ( empty( $from ) ) {
				return $to . ' ' . _n( 'day', 'days', intval( $to ), 'pok' );
			} else {
				return $from . '-' . $to . ' ' . __( 'days', 'pok' );
			}
		}
	}

	/**
	 * Check if current configuration is using simple address field
	 * 
	 * @return boolean Is use simple address field.
	 */
	public function is_use_simple_address_field() {
		$api = $this->setting->get( 'base_api' );
		return 'rajaongkir2' === $api || ( 'nusantara' === $api && 'yes' === $this->setting->get( 'use_simple_address_field' ) );
	}

	/**
	 * Check if override to indonesia
	 * @return boolean
	 */
	public function is_override_to_indonesia() {
		$override = $this->setting->get( 'override_default_location_to_indonesia' );
		if( empty( $override ) || 'yes' === $override ) {
			return true;
		}
		return false;
	}

	/**
	 * Map woocommerce state to ongkir state format
	 * @param $state string
	 * @return $states array
	 */
	public function map_ongkir_states( $state = '' ) {
		$states = array(
			'BA' => '1',
			'BB' => '2',
			'BT' => '3',
			'BE' => '4',
			'YO' => '5',
			'JK' => '6',
			'GO' => '7',
			'JA' => '8',
			'JB' => '9',
			'JT' => '10',
			'JI' => '11',
			'KB' => '12',
			'KS' => '13',
			'KT' => '14',
			'KI' => '15',
			'KU' => '16',
			'KR' => '17',
			'LA' => '18',
			'MA' => '19',
			'MU' => '20',
			'AC' => '21',
			'NB' => '22',
			'NT' => '23',
			'PA' => '24',
			'PB' => '25',
			'RI' => '26',
			'SR' => '27',
			'SN' => '28',
			'ST' => '29',
			'SG' => '30',
			'SA' => '31',
			'SB' => '32',
			'SS' => '33',
			'SU' => '34',
		);
		if( ! empty( $state ) ) {
			$states = isset( $states[$state] ) ? $states[$state] : false;
		}
		return $states;
	}

	/**
	 * Check if website is using checkout blocks
	 * 
	 * @return boolean
	 */
	public function is_using_checkout_block() {
		$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
		if ( ! empty( $checkout_page_id ) ) {
			$content = get_post_field( 'post_content', $checkout_page_id );
			return ( false !== stripos( $content, 'wp:woocommerce/checkout' ) );
		}
		return false;
	}

}
