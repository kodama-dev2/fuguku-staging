<?php
/**
 * The file that defines the Cekongkir_Courier_SENTRAL class
 *
 * @link       https://github.com/sofyansitorus
 * @since      1.3.8
 *
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cekongkir_Courier_SENTRAL class.
 *
 * @since      1.3.8
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Cekongkir_Courier_SENTRAL extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.3.8
	 *
	 * @var string
	 */
	public $code = 'sentral';

	/**
	 * Courier Label
	 *
	 * @since 1.3.8
	 *
	 * @var string
	 */
	public $label = 'Sentral Cargo';

	/**
	 * Courier Website
	 *
	 * @since 1.3.8
	 *
	 * @var string
	 */
	public $website = 'http://sentralcargo.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.3.8
	 *
	 * @return array
	 */
	public function get_services_domestic_default() {
		return array(
			'DARAT ELEKTRONIK'     => 'DARAT ELEKTRONIK',
			'DARAT NON ELEKTRONIK' => 'DARAT NON ELEKTRONIK',
			'UDARA ELEKTRONIK'     => 'UDARA ELEKTRONIK',
			'UDARA NON ELEKTRONIK' => 'UDARA NON ELEKTRONIK',
		);
	}

	/**
	 * Get courier account for domestic shipping
	 *
	 * @since 1.3.8
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