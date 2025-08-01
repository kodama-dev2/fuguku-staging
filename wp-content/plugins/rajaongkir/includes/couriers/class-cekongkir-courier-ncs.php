<?php
/**
 * The file that defines the Cekongkir_Courier_NCS class
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
 * The Cekongkir_Courier_NCS class.
 *
 * @since      1.2.12
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Cekongkir_Courier_NCS extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'ncs';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'Nusantara Card Semesta';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://www.ptncs.com';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic_default() {
		return array(
			'DARAT' => 'Regular Darat',
			'NRS' => 'REGULAR SERVICE',
			'ONS' => 'One Night Service',
			'JMR' => 'Jawa Murah',
			'NFR' => 'Food-Regular Service',
			'NFO' => 'Food-One Night Service',
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