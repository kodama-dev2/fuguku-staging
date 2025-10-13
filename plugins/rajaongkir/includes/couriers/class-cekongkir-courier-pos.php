<?php
/**
 * The file that defines the Cekongkir_Courier_POS class
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
 * The Cekongkir_Courier_POS class.
 *
 * @since      1.2.12
 * @package    Cekongkir
 * @subpackage Cekongkir/includes
 * @author     Sofyan Sitorus <sofyansitorus@gmail.com>
 */
class Cekongkir_Courier_POS extends Cekongkir_Courier {

	/**
	 * Courier Code
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $code = 'pos';

	/**
	 * Courier Label
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $label = 'POS Indonesia';

	/**
	 * Courier Website
	 *
	 * @since 1.2.12
	 *
	 * @var string
	 */
	public $website = 'http://www.posindonesia.co.id';

	/**
	 * Get courier services for domestic shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_domestic_default() {
		return array(
			'Pos Reguler' => 'Reguler',
			'Pos Nextday' => 'Nextday',
			'PAKETPOS DANGEROUS GOODS' => 'PAKETPOS DANGEROUS GOODS',
			'PAKETPOS VALUABLE GOODS' => 'PAKETPOS VALUABLE GOODS',
			'Pos Sameday' => 'Sameday',
			'POS KARGO' => 'POS KARGO',
			
		);
	}



	/**
	 * Get courier services for international shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_services_international_default() {
		return array(
			'PAKETPOS BIASA LN' => 'PAKETPOS BIASA LN',
			'EMS BARANG'        => 'EMS BARANG',
			'ePacket LP APP'    => 'ePacket LP APP',
			'POS EKSPOR'        => 'POS EKSPOR',
			'PAKETPOS CEPAT LN' => 'PAKETPOS CEPAT LN',
			'R LN'              => 'R LN',
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

	/**
	 * Get courier account for international shipping
	 *
	 * @since 1.2.12
	 *
	 * @return array
	 */
	public function get_account_international() {
		return array(
			'starter',
			'basic',
			'pro',
		);
	}
}