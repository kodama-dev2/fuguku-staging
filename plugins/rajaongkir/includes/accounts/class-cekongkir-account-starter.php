<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cekongkir_Account_Starter extends Cekongkir_Account {

	/**
	 * Account priority
	 *
	 * @since 1.2.12
	 *
	 * @var int
	 */
	public $priority = 1;

	/**
	 * Account type
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $type = 'starter';

	/**
	 * Account label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'Starter';

	/**
	 * Account API URL
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	// public $api_url = 'https://api.collaborator.komerce.id/tariff/starter';

	/**
	 * Account features
	 *
	 * @since 1.2.12
	 *
	 * @var array
	 */
	protected $features = array(
		'subdistrict'       => true,
		'multiple_couriers' => true,
		'volumetric'        => true,
		'weight_over_30kg'  => true,
		'dedicated_server'  => true,
	);

	/**
	 * Parse API request parameters.
	 *
	 * @since 1.2.12
	 *
	 * @param array  $params   API request parameters to parse.
	 * @param string $endpoint API request endpoint.
	 *
	 * @return (array|WP_Error)
	 */
	public function api_request_parser( $params = array(), $endpoint = '' ) {
		if ( '/cost' === $endpoint ) {
			$this->api_request_params_required = array(
				'origin',
				'destination',
				'weight',
				'courier',
			);
		}

		return parent::api_request_parser( $params );
	}
}