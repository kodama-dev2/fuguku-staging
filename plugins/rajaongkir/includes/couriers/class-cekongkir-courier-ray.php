<?php
/**
 * The file that defines the Cekongkir_Courier_TIKI class
 *
 * @link       https://github.com/sofyansitorus
 * @since      1.2.12
 *
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cekongkir_Courier_TIKI class.
 *
 * @since      1.2.12
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Cekongkir_Courier_Ray extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'ray';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'RAY';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'https://rayspeed.com/';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */


	public function get_services_international_default() {
		return array(
			'Reguler Service'             => 'Reguler Service',
			'Express Service'             => 'Express Service',
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