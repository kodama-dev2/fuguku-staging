<?php

if ( class_exists( 'QuadLayers\\WP_License_Client\\Load' ) ) {

	if ( ! function_exists( 'qlwcdc_license_client_integration' ) ) {

		function qlwcdc_license_client_integration() {

			global $qlwcdc_license_client;

			if ( ! isset( $qlwcdc_license_client ) ) {

				$qlwcdc_license_client = new QuadLayers\WP_License_Client\Load(
					array(
						'api_url'           => 'https://quadlayers.com/wp-json/wc/wlm/',
						'product_key'       => '16cb9ea2107b1ac236800dd5168c3c0f',
						'plugin_file'       => QLWCDC_PRO_PLUGIN_FILE,
						'parent_menu_slug'  => 'wc-settings',
						'license_menu_slug' => 'qlwcdc_license',
						'support_url'       => QLWCDC_PRO_SUPPORT_URL
					)
				);
			}

			return $qlwcdc_license_client;
		}

		qlwcdc_license_client_integration();
	}
}
