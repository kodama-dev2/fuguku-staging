<?php

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! function_exists('cekongkir_is_plugin_active') ) :
    function cekongkir_is_plugin_active( $plugin_file) {
        if ( ! function_exists('is_plugin_active') ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }


        return is_plugin_active( $plugin_file );
    }
endif;

if ( ! function_exists('cekongkir_autoload')):
    
    
    function cekongkir_autoload( $class) {
        $class = strtolower($class);

		if ( strpos( $class, 'cekongkir' ) !== 0 ) {
			return;
		}

        if ( strpos( $class, 'cekongkir_account_' ) === 0 ) {
			require_once CEKONGKIR_PATH . 'includes/accounts/class-' . str_replace( '_', '-', $class ) . '.php';
		} elseif ( strpos( $class, 'cekongkir_courier_' ) === 0 ) {
			require_once CEKONGKIR_PATH . 'includes/couriers/class-' . str_replace( '_', '-', $class ) . '.php';
		} else {
			require_once CEKONGKIR_PATH . 'includes/classes/class-' . str_replace( '_', '-', $class ) . '.php';
		}
    }

endif;

if ( ! function_exists( 'cekongkir_get_json_path' ) ) :
	/**
	 * Generate relative path to JSON file.
	 *
	 * @param string $file_name JSON file name.
	 *
	 * @return array
	 */
	function cekongkir_get_json_path( $file_name ) {
		return apply_filters( 'cekongkir_get_json_path', 'data/cekongkir-' . sanitize_file_name( $file_name ) . '.json', $file_name );
	}
endif;


if ( ! function_exists( 'cekongkir_get_json_data' ) ) :
	/**
	 * Get json file data.
	 *
	 * @since 1.0.0
	 * @param array $file_name File name for the json data.
	 * @param array $search Search keyword data.
	 * @throws  Exception If WordPress Filesystem Abstraction classes is not available.
	 * @return array
	 */
    function cekongkir_get_json_data( $file_name, $search = array())  {
        global $wp_filesystem;

        $file_url = CEKONGKIR_URL . cekongkir_get_json_path( $file_name );
        $file_path = CEKONGKIR_PATH . cekongkir_get_json_path( $file_name );

        try {
            require_once ABSPATH . 'wp-admin/includes/file.php';

            if ( is_null( $wp_filesystem ) ) {
                WP_Filesystem();
            }

            if ( ! $wp_filesystem instanceof WP_Filesystem_Base ||  ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code()) ) {
                throw new Exception('Wordpress Filesystem Abstractionn classes is not available', 1);   
            }

            if ( ! $wp_filesystem->exists( $file_path ) ) {
                throw new Exception('JSON file is not exists or unreadable', 1);
            }

            $json = $wp_filesystem->get_contents($file_path);
        } catch ( Exception $e ) {
            // Get JSON data by HTTO if the WP_Filesystem API Procedure failed.
            $json = wp_remote_retrieve_body( wp_remote_get( esc_url_raw( $file_url ) ) );
        }

        if ( ! $json ) {
            return false;
        }

        $json_data = json_decode( $json, true);
        $json_error = json_last_error_msg();

        if ( ! $json_data || ( $json_error && 'no error' !== strtolower( $json_error ) ) ) {
            return false;
        }

            // Search JSON data by associative array. Return the match row or false if not found.
            if ( $search ) {
                foreach ( $json_data as $row ) {
                    if ( array_intersect_assoc( $search, $row ) === $search ) {
                        return $row;
                    }
                }

                return false;
            }

            return $json_data;

    }
endif;



if ( ! function_exists( 'cekongkir_scripts_params' ) ) :
	/**
	 * Get localized scripts parameters.
	 *
	 * @since 1.2.11
	 *
	 * @param array $params Custom localized scripts parameters.
	 *
	 * @return array
	 */
	function cekongkir_scripts_params( $params = array() ) {
		$json_version = str_replace( '.', '_', cekongkir_get_plugin_data( 'Version' ) );
		$json_keys    = array( 'country', 'state', 'city', 'address_2' );
		$json_data    = array();

		foreach ( $json_keys as $json_key ) {
			$json_data[ $json_key ] = array(
				'url' => CEKONGKIR_URL . cekongkir_get_json_path( $json_key ),
				'key' => 'cekongkir_data_' . $json_key . '_v_' . $json_version,
			);
		}

		return wp_parse_args(
			$params,
			array(
				'json'   => $json_data,
				'locale' => WC()->countries->get_country_locale(),
			)
		);
	}
endif;

if ( ! function_exists( 'cekongkir_sort_by_priority' ) ) :
	/**
	 * Sort data by priority
	 *
	 * @param array $a Item to compare.
	 * @param array $b Item to compare.
	 *
	 * @return int
	 */
	function cekongkir_sort_by_priority( $a, $b ) {
		$a_priority = 0;

		if ( is_object( $a ) && is_callable( array( $a, 'get_priority' ) ) ) {
			$a_priority = $a->get_priority();
		} elseif ( isset( $a['priority'] ) ) {
			$a_priority = $a['priority'];
		}

		$b_priority = 0;

		if ( is_object( $b ) && is_callable( array( $b, 'get_priority' ) ) ) {
			$b_priority = $b->get_priority();
		} elseif ( isset( $b['priority'] ) ) {
			$b_priority = $b['priority'];
		}

		return strcasecmp( $a_priority, $b_priority );
	}
endif;

if ( ! function_exists( 'cekongkir_is_dev' ) ) :
	/**
	 * Check is in development environment.
	 *
	 * @since 1.2.11
	 *
	 * @return bool
	 */
	function cekongkir_is_dev() {
		if ( defined( 'CEKONGKIR_DEV' ) && CEKONGKIR_DEV ) {
			return true;
		}

		if ( function_exists( 'getenv' ) && getenv( 'CEKONGKIR_DEV' ) ) {
			return true;
		}

		return false;
	}
endif;

if ( ! function_exists( 'cekongkir_get_plugin_data' ) ) :
	/**
	 * Get plugin data
	 *
	 * @since 1.2.13
	 *
	 * @param string $selected Selected data key.
	 * @param string $selected_default Selected data key default value.
	 * @param bool   $markup If the returned data should have HTML markup applied.
	 * @param bool   $translate If the returned data should be translated.
	 *
	 * @return (string|array)
	 */
	function cekongkir_get_plugin_data( $selected = null, $selected_default = '', $markup = false, $translate = true ) {
		static $plugin_data;

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if ( is_null( $plugin_data ) ) {
			$plugin_data = get_plugin_data( CEKONGKIR_FILE, $markup, $translate );
		}

		if ( ! is_null( $selected ) ) {
			return isset( $plugin_data[ $selected ] ) ? $plugin_data[ $selected ] : $selected_default;
		}

		return $plugin_data;
	}
endif;

if ( ! function_exists( 'cekongkir_instances' ) ) :
	/**
	 * Get shipping method instances
	 *
	 * @since 1.3.0
	 *
	 * @param bool $enabled_only Filter to includes only enabled instances.
	 * @return array
	 */
	function cekongkir_instances( $enabled_only = true ) {
		$instances = array();

		$zone_data_store = new WC_Shipping_Zone_Data_Store();

		$shipping_methods = $zone_data_store->get_methods( '0', $enabled_only );

		if ( $shipping_methods ) {
			foreach ( $shipping_methods as $shipping_method ) {
				if ( CEKONGKIR_METHOD_ID !== $shipping_method->method_id ) {
					continue;
				}

				$instances[] = array(
					'zone_id'     => 0,
					'method_id'   => $shipping_method->method_id,
					'instance_id' => $shipping_method->instance_id,
				);
			}
		}

		$zones = WC_Shipping_Zones::get_zones();

		if ( ! empty( $zones ) ) {
			foreach ( $zones as $zone ) {
				$shipping_methods = $zone_data_store->get_methods( $zone['id'], $enabled_only );
				if ( $shipping_methods ) {
					foreach ( $shipping_methods as $shipping_method ) {
						if ( CEKONGKIR_METHOD_ID !== $shipping_method->method_id ) {
							continue;
						}

						$instances[] = array(
							'zone_id'     => 0,
							'method_id'   => $shipping_method->method_id,
							'instance_id' => $shipping_method->instance_id,
						);
					}
				}
			}
		}

		return apply_filters( 'cekongkir_instances', $instances );
	}
endif;

if ( ! function_exists( 'cekongkir_is_enable_cache' ) ) :
	/**
	 * Check wether api response should be cached
	 *
	 * @return boolean
	 */
	function cekongkir_is_enable_cache() {
		return defined( 'CEKONGKIR_ENABLE_CACHE' ) ? CEKONGKIR_ENABLE_CACHE : true;
	}
endif;

if ( ! function_exists( 'cekongkir_parse_etd' ) ) :
	/**
	 * Parse API response ETD data.
	 *
	 * @since 1.3
	 *
	 * @param string $etd API response ETD data.
	 *
	 * @return string
	 */
	function cekongkir_parse_etd( $etd ) {
		if ( ! $etd ) {
			return '';
		}

		$etd = strtolower( $etd );
		$etd = preg_replace( '/([0-9]+) - ([0-9]+)/', '$1-$2', $etd );
		$etd = str_replace( '1-1', '1', $etd );
		$etd = str_replace( '0-0', '0', $etd );

		if ( false !== strpos( $etd, 'jam' ) ) {
			$etd = trim( str_replace( 'jam', '', $etd ) );

			// translators: %s is number of hours.
			$etd = is_numeric( $etd ) && intval( $etd ) === 1 ? __( '1 hour', 'cekongkir' ) : sprintf( __( '%s', 'cekongkir' ), $etd );
		} else {
			$etd = trim( str_replace( 'hari', '', $etd ) );

			// translators: %s is number day.
			$etd = is_numeric( $etd ) && intval( $etd ) === 1 ? __( '1 day', 'cekongkir' ) : sprintf( __( '%s', 'cekongkir' ), $etd );
		}

		return $etd;
	}
endif;

if ( ! function_exists( 'cekongkir_custom_address_fields' ) ) :
	/**
	 * Get custom address fields data.
	 *
	 * @since 1.3
	 *
	 * @return array
	 */
	function cekongkir_custom_address_fields() {
		$custom_address_fields = array(
			'state'     => array(
				'label'       => __( 'Province', 'cekongkir' ),
				'placeholder' => __( 'Province', 'cekongkir' ),
				'priority'    => 41,
			),
			'city'      => array(
				'label'       => __( 'Town / City', 'cekongkir' ),
				'placeholder' => __( 'Town / City', 'cekongkir' ),
				'priority'    => 42,
			),
			'address_2' => array(
				'label'       => __( 'Subdistrict', 'cekongkir' ),
				'placeholder' => __( 'Subdistrict', 'cekongkir' ),
				'priority'    => 43,
			),
		);

		return apply_filters( 'cekongkir_custom_address_fields', $custom_address_fields );
	}
endif;