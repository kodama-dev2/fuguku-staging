<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cekongkir_Account_Enterprise extends Cekongkir_Account {

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
	public $type = 'enterprise';

	/**
	 * Account label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'Enterprise';

	/**
	 * Account API URL
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $api_url = 'https://api.collaborator.komerce.id/tariff/st';

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


	public function api_request_parser( $params = array(), $endpoint = '' ) {
		if ( '/cost' === $endpoint ) {
			$this->api_request_params_required = array(
				'origin',
				'originType',
				'destination',
				'destinationType',
				'weight',
				'courier',
			);

			$this->api_request_params_optional = array(
				'length',
				'width',
				'height',
				'diameter',
			);
		} elseif ( '/v2/internationalCost' === $endpoint ) {
			$this->api_request_params_required = array(
				'origin',
				'destination',
				'weight',
				'courier',
			);

			$this->api_request_params_optional = array(
				'length',
				'width',
				'height',
			);
		}

		return parent::api_request_parser( $params );
	}
}