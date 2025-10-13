<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Cekongkir_Courier_DSE extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'dse';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = '21 Express';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://21express.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic_default() {
		return array(
			'ECO' => 'Regular Service',
			'ONS' => 'Over Night Service',
			'SDS' => 'Same Day Service',
		);
	}

	/**
	 * Get courier account for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_account_domestic() {
		return array(
			'starter',
			'basic',
			'pro',
		);
	}
}