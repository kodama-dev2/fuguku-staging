<?php
/**
 * The file that defines the Cekongkir_Courier_RPX class
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
 * The Cekongkir_Courier_RPX class.
 *
 * @since      1.2.12
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Cekongkir_Courier_RPX extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'rpx';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'RPX';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://www.rpx.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic_default() {
		return array(
			'Regular Package (RGP)' => 'Regular Package',
			'Next Day Package (NDP)' => 'Next Day Package',
			'SameDay Package (SDP)' => 'SameDay Package',
			'MidDay Package (MDP)' => 'MidDay Package',
			'Economy Delivery (ECP)' => 'Economy Delivery',
			'Heavy Weight Delivery (HWP)' => 'Heavy Weight Delivery',
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