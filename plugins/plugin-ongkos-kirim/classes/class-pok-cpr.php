<?php

/**
 * CPR filter class
 */
class POK_CPR {

	/**
	 * POK core
	 * 
	 * @var POK_Core
	 */
	protected $core;

	/**
	 * POK Helper
	 * 
	 * @var POK_Helper
	 */
	protected $helper;

	/**
	 * POK Setting
	 * 
	 * @var POK_Setting
	 */
	protected $setting;

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $pok_helper;
		global $pok_core;
		$this->core     = $pok_core;
		$this->setting  = new POK_Setting();
		$this->helper   = $pok_helper;
		add_filter( 'cpr_restriction_cart_fields', array( $this, 'custom_cart_restriction' ) );
		add_filter( 'cpr_restriction_validate_cart_courier', array( $this, 'check_shipping_courier' ), 10, 3 );
		add_filter( 'cpr_restriction_validate_cart_service', array( $this, 'check_shipping_service' ), 10, 3 );
		add_filter( 'cpr_restriction_validate_cart_cost', array( $this, 'check_shipping_cost' ), 10, 3 );
	}

	/**
	 * Register custom restriction
	 *
	 * @param  array $fields Restriction fields.
	 * @return array         Restriction fields.
	 */
	public function custom_cart_restriction( $fields ) {

		// change shipping weight label.
		$fields['total_weight']['label'] = __( 'Weight', 'pok' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')';
		$fields['total_weight']['group'] = __( 'Shipping', 'pok' );

		// add courier restriction.
		$fields['courier'] = array(
			'label'     => __( 'Courier', 'pok' ),
			'operator'  => 'list',
			'group'     => __( 'Shipping', 'pok' ),
			'value'     => array(
				'type'  => 'select_multiple',
				'options' => array(),
			),
		);
		foreach ( $this->core->get_courier() as $courier ) {
			$fields['courier']['value']['options'][ $courier ] = $this->helper->get_courier_name( $courier );
		}

		// add service restriction.
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			$fields['service'] = array(
				'label'     => __( 'Service', 'pok' ),
				'operator'  => 'list',
				'group'     => __( 'Shipping', 'pok' ),
				'value'     => array(
					'type'  => 'select_multiple',
					'options' => array(),
				),
			);
			foreach ( $this->core->get_courier_service() as $courier => $services ) {
				foreach ( $services as $key => $label ) {
					$fields['service']['value']['options'][ $key ] = $this->helper->get_courier_name( $courier ) . ' - ' . $label;
				}
			}
		}

		// add cost restriction.
		$fields['cost'] = array(
			'label'     => __( 'Cost', 'pok' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'operator'  => 'compare',
			'group'     => __( 'Shipping', 'pok' ),
			'value'     => array(
				'type'  => 'number',
			),
		);
		return $fields;
	}

	/**
	 * Check if a rate is from specific courier
	 *
	 * @param  boolean $is_valid Is valid.
	 * @param  array   $rule     Rule value.
	 * @param  object  $object   Rate object.
	 * @return boolean           Is valid.
	 */
	public function check_shipping_courier( $is_valid, $rule, $object ) {
		if ( $object instanceof WC_Shipping_Rate ) {
			$meta = $object->get_meta_data();
			if ( isset( $meta['courier'] ) ) {
				if ( 'in_list' === $rule['operator'] ) {
					if ( in_array( $meta['courier'], $rule['value'], true ) ) {
						return true;
					}
				} else {
					if ( ! in_array( $meta['courier'], $rule['value'], true ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check if a rate is from specific service
	 *
	 * @param  boolean $is_valid Is valid.
	 * @param  array   $rule     Rule value.
	 * @param  object  $object   Rate object.
	 * @return boolean           Is valid.
	 */
	public function check_shipping_service( $is_valid, $rule, $object ) {
		if ( $object instanceof WC_Shipping_Rate ) {
			$meta = $object->get_meta_data();
			if ( isset( $meta['service'] ) ) {
				if ( 'in_list' === $rule['operator'] ) {
					if ( in_array( sanitize_title( $meta['service'] ), $rule['value'], true ) ) {
						return true;
					}
				} else {
					if ( ! in_array( sanitize_title( $meta['service'] ), $rule['value'], true ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Compare the rate cost
	 *
	 * @param  boolean $is_valid Is valid.
	 * @param  array   $rule     Rule value.
	 * @param  object  $object   Rate object.
	 * @return boolean           Is valid.
	 */
	public function check_shipping_cost( $is_valid, $rule, $object ) {
		if ( $object instanceof WC_Shipping_Rate ) {
			$cost = $object->get_cost();
			return cpr_compare( (float) $cost, $rule['value'], $rule['operator'] );
		}
		return false;
	}

}
