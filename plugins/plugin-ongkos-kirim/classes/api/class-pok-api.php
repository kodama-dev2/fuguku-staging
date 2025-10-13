<?php

/**
 * POK API Handler
 */
class POK_API {

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
	 * POK Logs
	 * 
	 * @var POK_Logs
	 */
	protected $logs;

	/**
	 * API tonjoo
	 *
	 * @var object
	 */
	protected $tonjoo;

	/**
	 * API rajaongkir
	 *
	 * @var object
	 */
	protected $rajaongkir;

	/**
	 * API rajaongkir v2
	 *
	 * @var object
	 */
	protected $rajaongkir2;

	/**
	 * Active vendor
	 *
	 * @var string
	 */
	protected $vendor;

	/**
	 * Rajaongkir type
	 *
	 * @var string
	 */
	protected $rajaongkir_type;

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler_3 $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler_3 $license ) {
		global $pok_helper;
		$this->setting          = new POK_Setting();
		$this->vendor           = $this->setting->get( 'base_api' );
		$this->rajaongkir_type  = $this->setting->get( 'rajaongkir_type' );
		$rajaongkir_status      = $this->setting->get( 'rajaongkir_status' );
		$rajaongkir2_status     = $this->setting->get( 'rajaongkir2_status' );
		if ( $license->is_license_active() ) {
			require_once 'class-pok-api-tonjoo.php';
			$this->tonjoo = new POK_API_Tonjoo( $license->get( 'key' ) );
		}
		if ( $rajaongkir_status[0] ) {
			require_once 'class-pok-api-rajaongkir.php';
			$this->rajaongkir = new POK_API_RajaOngkir( $this->setting->get( 'rajaongkir_key' ), $this->rajaongkir_type );
		}
		if ( $rajaongkir2_status[0] ) {
			require_once 'class-pok-api-rajaongkir2.php';
			$this->rajaongkir2 = new POK_API_RajaOngkir2( $this->setting->get( 'rajaongkir2_key' ) );
		}
		$this->helper = $pok_helper;
		$this->logs = new TJ_Logs( POK_LOG_NAME );
	}

	/**
	 * Get courier service (only for API tonjoo)
	 *
	 * @return array Courier package
	 */
	public function get_courier_service() {
		$result = array();
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_courier_service();
		} elseif ( 'rajaongkir' === $this->vendor ) {
			$result = $this->rajaongkir->get_courier_service();
		} else {
			$result = $this->rajaongkir2->get_courier_service();
		}
		return apply_filters( 'pok_courier_services', $result, $this->vendor );
	}

	/**
	 * Get province options
	 *
	 * @return array Province options
	 */
	public function get_province() {
		$provinces = array();
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_province();
			foreach ( $result as $state_id => $state ) {
				$provinces[ $state_id ] = $state->name;
			}
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return $provinces;
			}
			$result = $this->rajaongkir->get_province();
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $d ) {
					$provinces[ $d->province_id ] = $d->province;
				};
			}
		}
		return $provinces;
	}

	/**
	 * Get city options based on province id
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City list.
	 */
	public function get_city( $province_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$cities = array();
			$result = $this->tonjoo->get_city( $province_id );
			foreach ( $result as $city_id => $city ) {
				$cities[ $city_id ] = $city;
			};
			return $cities;
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return null;
			}
			$result = $this->rajaongkir->get_city( $province_id );
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				$cities = array();
				foreach ( $result['data'] as $d ) {
					$d->nama    = $d->city_name;
					if ( 'Kota' === $d->type ) {
						$d->nama = 'Kota ' . $d->nama;
					} else {
						$d->nama = 'Kab. ' . $d->nama;
					}
					$cities[ $d->city_id ] = $d->nama;
				};
				return $cities;
			}
		}
		return null;
	}

	/**
	 * Get single city by id
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_single_city( $city_id );
			return $result;
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return null;
			}
			$result = $this->rajaongkir->get_single_city( $city_id );
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				return ( 'Kabupaten' === $result['data']->type ? 'Kab.' : $result['data']->type ) . ' ' . $result['data']->city_name . ', ' . $result['data']->province;
			}
		}
		return null;
	}

	/**
	 * Get all city (only for API rajaongkir)
	 *
	 * @return array City list
	 */
	public function get_all_city() {
		if ( 'rajaongkir' !== $this->vendor || is_null( $this->rajaongkir ) ) {
			return null;
		}
		$result = $this->rajaongkir->get_all_city();
		if ( $result['status'] ) {
			return $result['data'];
		}
		return null;
	}

	/**
	 * Get origins
	 *
	 * @return array City list
	 */
	public function get_origins() {
		return $this->tonjoo->get_origins();
	}

	/**
	 * Search city (only for API tonjoo)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_city( $search = '' ) {
		if ( strlen( $search ) < 3 ) {
			return array();
		}
		$result = $this->tonjoo->get_all_city( $search );
		if ( $result['status'] ) {
			foreach ( $result['data'] as $d ) {
				$d->type = $d->jenis;
			};
			return $result['data'];
		}
		return null;
	}

	/**
	 * Search city for simple address field (only for API tonjoo & rajaongkir v2)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_simple_address( $search = '' ) {
		if ( strlen( $search ) < 3 ) {
			return array();
		}
		$provinces = $this->get_province();
		$result = array();
		if ( 'nusantara' === $this->vendor ) {
			$raw = $this->tonjoo->search_simple_address( $search );
			foreach ( $raw as $key => $value ) {
				$ids = explode( "_", $key );
				$result[ $key ] = $value . ", " . $provinces[ $ids[2] ];
			}
		} elseif ( 'rajaongkir2' === $this->vendor ) {
			$raw = $this->rajaongkir2->search_domestic_destination( $search );
			if ( $raw['status'] ) {
				foreach ( $raw['data'] as $key => $value ) {
					$result[ $value->id ] = $value->label;
				}
			}
		}
		return $result;
	}

	/**
	 * Get simple address by ID (only for API tonjoo)
	 *
	 * @param  string $id City ID.
	 * @return string     City name.
	 */
	public function get_simple_address( $id = '' ) {
		$raw = $this->tonjoo->get_simple_address( $id );
		if ( ! empty( $id ) && ! empty( $raw ) ) {
			$provinces = $this->get_province();
			$ids = explode( "_", $id );
			return $raw . ", " . $provinces[ $ids[2] ];
		}
		return '';
	}

	/**
	 * Get district by the City ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District list.
	 */
	public function get_district( $city_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_district( $city_id );
			return $result;
		} else {
			if ( 'pro' !== $this->rajaongkir_type ) {
				return null;
			}
			$result = $this->rajaongkir->get_district( $city_id );
			$districts = array();
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $d ) {
					$districts[ $d->subdistrict_id ] = $d->subdistrict_name;
				};
				return $districts;
			}
		}
		return null;
	}

	/**
	 * Get all countries
	 *
	 * @return array Contry
	 */
	public function get_all_country() {
		if ( 'rajaongkir' === $this->vendor && 'starter' !== $this->rajaongkir_type ) {
			$result = $this->rajaongkir->get_all_country();
			if ( $result['status'] ) {
				$res = array();
				foreach ( $result['data'] as $d ) {
					// TODO: need filter to sanitize country name (exp: United States (US) -> United States of America).
					$res[ (int) $d->country_id ] = $d->country_name;
				}
				return $res;
			}
		}
		return null;
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination ID (city or district).
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Costs.
	 */
	public function get_cost( $origin, $destination, $weight, $courier ) {
		if ( 'rajaongkir' === $this->vendor ) { // API rajaongkir.
			if ( is_null( $this->rajaongkir ) ) {
				return array();
			}
			$result = $this->rajaongkir->get_cost( $origin, $destination, $weight, $courier );
			if ( ! empty( $result ) ) {
				$costs = array();
				foreach ( $result as $c ) {
					if ( is_array( $c->costs ) && ! empty( $c->costs ) ) {
						foreach ( $c->costs as $t ) {
							if ( $t->cost[0]->value > 0 ) {
								if ( 'j&t' === strtolower( $c->code ) ) {
									$c->code = 'jnt';
								}
								$costs[] = array(
									'class'         => strtoupper( $c->code ) . ' - ' . $t->service,
									'courier'       => strtolower( $c->code ),
									'service'       => $t->service,
									'description'   => $t->description,
									'cost'          => $t->cost[0]->value,
									'time'          => trim( str_replace( ' HARI', '', $t->cost[0]->etd ) ),
								);
							}
						}
					}
				}
				return $costs;
			}
		} elseif ( 'rajaongkir2' === $this->vendor ) { // API rajaongkir v2.
			if ( is_null( $this->rajaongkir2 ) ) {
				return array();
			}
			$result = $this->rajaongkir2->get_cost( $origin, $destination, $weight, $courier );
			if ( ! empty( $result ) ) {
				$costs = array();
				foreach ( $result as $c ) {
					if ( $c->cost > 0 ) {
						if ( in_array( $c->service, array( 'T15', 'T25', 'T60', 'JTR<130', 'JTR>130', 'JTR>200', 'OTOPACK150', 'OTOPACK250' ) ) ) {
							continue;
						}
						$costs[] = array(
							'class'         => strtoupper( $c->code ) . ' - ' . $c->service,
							'courier'       => strtolower( $c->code ),
							'service'       => $c->service,
							'description'   => $c->description,
							'cost'          => $c->cost,
							'time'          => trim( str_replace( ' day', '', $c->etd ) ),
						);
					}
				}
				return $costs;
			}
		} else { // API tonjoo.
			$result = $this->tonjoo->get_cost( $origin, $destination, $courier );
			$hide_cargo = $this->setting->get( 'only_show_cargo_on_min_weight' );
			if ( $result['status'] ) {
				$costs = array();
				foreach ( $result['data'] as $c ) {
					if ( ! empty( $c->tarif ) ) {
						foreach ( $c->tarif as $t ) {

							// Remove unwanted services
							if (
								( 'jne' === strtolower( $c->nama ) && 0 === strpos( $t->namaLayanan, 'JTR' ) && 'JTR' !== $t->namaLayanan ) ||
								( 'tiki' === strtolower( $c->nama ) && in_array( $t->namaLayanan, array( 'T25', 'T15', 'T60', 'TRC' ) ) ) ||
								( 'pos' === strtolower( $c->nama ) && 'Kargo Motor' === $t->namaLayanan )
							) {
								continue;
							}

							// Hide cargo when minimum weight is not reached.
							if ( 'yes' === $hide_cargo && (
								( 'jne' === strtolower( $c->nama ) && 'JTR' === $t->namaLayanan && $weight < 10000 ) ||
								( 'sicepat' === strtolower( $c->nama ) && 'GOKIL' === $t->namaLayanan && $weight < 10000 ) ||
								( 'lion' === strtolower( $c->nama ) && 'BIGPACK' === $t->namaLayanan && $weight < 10000 ) ||
								( 'tiki' === strtolower( $c->nama ) && 'TRC' === $t->namaLayanan && $weight < 10000 ) ||
								( 'wahana' === strtolower( $c->nama ) && 'Kargo' === $t->namaLayanan && $weight < 10000 ) ||
								( 'anteraja' === strtolower( $c->nama ) && 'Cargo' === $t->namaLayanan && $weight < 30000 ) ||
								( 'pos' === strtolower( $c->nama ) && 'Kargo Barang' === $t->namaLayanan && $weight < 10000 ) ||
								( 'ide' === strtolower( $c->nama ) && 'iDtruck' === $t->namaLayanan && $weight < 10000 )
							) ) {
								continue;
							}

							if (
								( 'jne' === strtolower( $c->nama ) && 'JTR' === $t->namaLayanan ) ||
								( 'tiki' === strtolower( $c->nama ) && 'TRC' === $t->namaLayanan )
							) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 10 >= $new_weight ) {
									$cost = $t->tarif;
								} else {
									$cost = $t->tarif + ( ( $new_weight - 10 ) * ( isset( $t->tarif_11_kg ) && ! empty( $t->tarif_11_kg ) ? ( $t->tarif_11_kg - $t->tarif ) : 10000 ) );
								}
							} elseif (
								( 'wahana' === strtolower( $c->nama ) && 'Kargo' === $t->namaLayanan ) ||
								( 'sicepat' === strtolower( $c->nama ) && 'GOKIL' === $t->namaLayanan )
							) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 10 >= $new_weight ) {
									$cost = $t->tarif;
								} else {
									$cost = $t->tarif + ( ( $new_weight - 10 ) * $t->tarif_1_kg );
								}
							} elseif ( 'anteraja' === strtolower( $c->nama ) && 'Cargo' === $t->namaLayanan ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 30 >= $new_weight ) {
									$cost = $t->tarif;
								} else {
									$cost = $t->tarif + ( ( $new_weight - 30 ) * ( isset( $t->tarif_31_kg ) && ! empty( $t->tarif_31_kg ) ? ( $t->tarif_31_kg - $t->tarif ) : 10000 ) );
								}
							} elseif ( 'ide' === strtolower( $c->nama ) && 'iDtruck' === $t->namaLayanan ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 10 >= $new_weight ) {
									$cost = $t->tarif;
								} else {
									$cost = $t->tarif + ( ( $new_weight - 10 ) * ( isset( $t->tarif_11 ) && ! empty( $t->tarif_11 ) ? ( $t->tarif_11 - $t->tarif ) : 10000 ) );
								}
							} elseif ( 'lion' === strtolower( $c->nama ) && 'BIGPACK' === $t->namaLayanan ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 10 >= $new_weight ) {
									$cost = $t->tarif * 10;
								} else {
									$cost = $t->tarif * $new_weight;
								}
							} elseif ( 'wahana' === strtolower( $c->nama ) && 'PopBox' === $t->namaLayanan ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 2 >= $new_weight ) {
									$cost = $t->tarif;
								} else {
									$cost = $t->tarif + ( ( $new_weight - 2 ) * $t->tarif_1_kg );
								}
							} elseif ( 'ncs' === strtolower( $c->nama ) ) {
								if ( 'SDS' === $t->namaLayanan ) {
					                if ( $this->helper->round_weight( $weight / 1000 ) <= intval( $t->min_kg ) ) {
					                    $cost = $t->tarif + ( intval( $t->tarif_1_kg ) * ( intval( $t->min_kg ) - 1 ) );
					                } else {
					                    $cost = $t->tarif + ( intval( $t->tarif_1_kg ) * ( $this->helper->round_weight( $weight / 1000 ) - 1 ) );
					                }
					            } else {
					                if ( 1 < intval( $t->min_kg ) ) {
					                    if ( 0 < intval( $t->tarif_1_kg ) ) {
					                        if ( $this->helper->round_weight( $weight / 1000 ) <= intval( $t->min_kg ) ) {
					                            $cost = $t->tarif;
					                        } else {
					                            $cost = $t->tarif + ( intval( $t->tarif_1_kg ) * ( $this->helper->round_weight( $weight / 1000 ) - intval( $t->min_kg ) ) );
					                        }
					                    } else {
					                        if ( $this->helper->round_weight( $weight / 1000 ) <= intval( $t->min_kg ) ) {
					                            $cost = $t->tarif * intval( $t->min_kg );
					                        } else {
					                            $cost = $t->tarif * $this->helper->round_weight( $weight / 1000 );
					                        }
					                    }
					                } else {
					                    if ( 0 < intval( $t->tarif_1_kg ) ) {
					                        $cost = $t->tarif + ( intval( $t->tarif_1_kg ) * ( $this->helper->round_weight( $weight / 1000 ) - 1 ) );
					                    } else {
					                        $cost = $t->tarif * $this->helper->round_weight( $weight / 1000 );
					                    }
					                }
					            }
							} elseif ( 'pos' === strtolower( $c->nama ) ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( $t->namaLayanan == 'Kargo Barang' ) {
					                $cost = $t->tarif + ( ( 0.1 * $t->tarif ) * max( 0, ( $new_weight - 10 ) ) );
					            } elseif ( $t->namaLayanan == 'Ekonomi Paket' ) {
					                $cost = $t->tarif + ( ( $t->tarif / 3 ) * max( 0, ( $new_weight - 3 ) ) );
					            } elseif ( $t->namaLayanan == 'Reguler' || $t->namaLayanan == 'Nextday' ) {
					                $cost = $t->tarif + ( $t->tarif * max( 0, ( $new_weight - 2 ) ) );
					            } else {
					                $cost = $t->tarif * $new_weight;
					            }
							} elseif ( 'jntcargo' === strtolower( $c->nama ) ) {
								$new_weight = $this->helper->round_weight( $weight / 1000 );
								if ( 20 >= $new_weight ) {
									$cost = intval( $t->tarif );
								} elseif ( 21 === $new_weight ) {
					                $cost = intval( $t->tarif_21 );
					            } elseif ( 50 >= $new_weight ) {
					                $cost = intval( $t->tarif ) + ( ( intval( $t->tarif_21 ) - intval( $t->tarif ) ) * ( $new_weight - 20 ) );
					            } elseif ( 51 === $new_weight ) {
					                $cost = $t->tarif_51;
					            } elseif ( 100 >= $new_weight ) {
					                $cost = intval( $t->tarif_51 ) + ( ( intval( $t->tarif_52 ) - intval( $t->tarif_51 ) ) * ( $new_weight - 51 ) );
					            } elseif ( 101 === $new_weight ) {
					                $cost = $t->tarif_101;
					            } elseif ( 300 >= $new_weight ) {
					                $cost = intval( $t->tarif_101 ) + ( ( intval( $t->tarif_102 ) - intval( $t->tarif_101 ) ) * ( $new_weight - 101 ) );
					            } elseif ( 301 === $new_weight ) {
					                $cost = $t->tarif_301;
					            } else {
					                $cost = intval( $t->tarif_301 ) + ( ( intval( $t->tarif_302 ) - intval( $t->tarif_301 ) ) * ( $new_weight - 301 ) );
					            }
							} else {
								$cost = $t->tarif * $this->helper->round_weight( $weight / 1000 );
							}

							// fix etd time not valid.
							$etd = trim( str_replace( array( 'Hari', 'HARI' ), '', $t->etd ) );
							if ( false === strpos( $etd, '-' ) ) {
								if ( 0 !== intval( $etd ) ) {
									if ( 0 < floor( intval( $etd ) / 24 ) ) {
										$etd = ceil( intval( $etd ) / 24 );
									}
								}
							}

							// override courier name
							if ( 'j&t' === strtolower( $c->nama ) ) {
								$c->nama = 'jnt';
							} elseif ( 'j&t cargo' === strtolower( $c->nama ) ) {
								$c->nama = 'jntcargo';
							} elseif ( 'lion parcel' === strtolower( $c->nama ) ) {
								$c->nama = 'Lion';
							}

							$costs[] = array(
								'class'         => $c->nama . ' - ' . $t->namaLayanan,
								'courier'       => strtolower( $c->nama ),
								'service'       => $t->namaLayanan,
								'description'   => $t->jenis,
								'cost'          => $cost,
								'time'          => $etd,
							);
						}
					}
				}
				return $costs;
			}
		}
	}

	/**
	 * Get international shipping cost (only for API rajaongkir)
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination ID (country).
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Costs.
	 */
	public function get_cost_international( $origin, $destination, $weight, $courier ) {
		if ( 'rajaongkir' === $this->vendor && ! is_null( $this->rajaongkir ) ) {
			$result = $this->rajaongkir->get_cost_international( $origin, $destination, $weight, $courier );
			if ( ! empty( $result ) ) {
				$costs = array();
				$rate = $this->rajaongkir->get_currency();
				$exchange = isset( $rate['data']->value ) ? $rate['data']->value : 1;
				foreach ( $result as $c ) {
					if ( is_array( $c->costs ) && ! empty( $c->costs ) ) {
						foreach ( $c->costs as $t ) {
							$etd = ! empty( $t->etd ) ? $t->etd : '';
							$etd = trim( str_replace( array( 'Hari', 'HARI' ), '', $etd ) );
							$costs[] = array(
								'class'         => strtoupper( $c->code ) . ' - ' . $t->service,
								'courier'       => strtolower( $c->code ),
								'service'       => $t->service,
								'description'   => '',
								'cost'          => 'USD' === $t->currency ? floatval( $t->cost ) * floatval( $exchange ) : floatval( $t->cost ),
								'time'          => $etd,
							);
						}
					}
				}
				return $costs;
			}
		}
	}

	/**
	 * Get rajaongkir status
	 *
	 * @param  string $api_key API Key.
	 * @param  string $type    API Type.
	 * @return boolean          Status.
	 */
	public function get_rajaongkir_status( $api_key, $type ) {
		global $wp_version;
		if ( 'pro' === $type ) {
			$base_url   = 'http://pro.rajaongkir.com/api';
		} else {
			$base_url   = 'http://api.rajaongkir.com/' . $type;
		}
		$args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(
				'key' => $api_key,
			),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$content = wp_remote_get( $base_url . '/province', $args );
		if ( ! is_wp_error( $content ) ) {
			$body = json_decode( $content['body'] );
			if ( isset( $body->rajaongkir->status->code ) && 200 === $body->rajaongkir->status->code ) {
				return true;
			}
		} else {
			return __( 'can not connect server', 'pok' );
		}
		return false;
	}

	/**
	 * Get rajaongkir v2 status
	 *
	 * @param  string $api_key API Key.
	 * @return boolean          Status.
	 */
	public function get_rajaongkir2_status( $api_key ) {
		global $wp_version;
		$base_url = 'https://rajaongkir.komerce.id';
		$args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(
				'key' => $api_key,
			),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$response = wp_remote_get( $base_url . '/api/v1/destination/domestic-destination?search=sleman', $args );
		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( wp_remote_retrieve_body( $response ) );
			if ( isset( $body->meta->code ) && 200 === $body->meta->code ) {
				return true;
			}
		} else {
			return __( 'can not connect server', 'pok' );
		}
		return false;
	}

	/**
	 * Get status of the API.
	 *
	 * @param  string $api Base API.
	 * @return mixed       API Status.
	 */
	public function get_api_status( $api = 'nusantara' ) {
		if ( 'rajaongkir' === $api ) {
			$status = $this->setting->get( 'rajaongkir_status' );
			if ( $status[0] ) {
				return $this->rajaongkir->get_api_status();
			} else {
				return 'API Not Activated';
			}
		} else {
			return $this->tonjoo->get_api_status();
		}
	}

	/**
	 * Get latest currency rate from Fixer
	 *
	 * @param  string $currency Currency code.
	 * @param  string $api_key  API key.
	 * @return float            Rates.
	 */
	public function get_fixer_rate( $currency = 'USD', $api_key = '' ) {
		global $wp_version;
		$args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$content = wp_remote_get( 'http://data.fixer.io/api/latest?access_key=' . $api_key . '&base=IDR&symbols=' . $currency, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API Fixer) http://data.fixer.io/api/latest?access_key=' . $api_key . '&base=IDR&symbols=' . $currency . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'result'    => $content->get_error_message(),
			);
		}
		if ( 200 !== $content['response']['code'] ) {
			$this->logs->write( '(Error API Fixer) http://data.fixer.io/api/latest?access_key=' . $api_key . '&base=IDR&symbols=' . $currency . '. Error code: ' . $content['response']['code'] );
			return array(
				'status'    => false,
				'result'    => 'Error ' . $content['response']['code'],
			);
		}
		$body = json_decode( $content['body'] );
		if ( ! $body->success ) {
			$this->logs->write( '(Error API Fixer) http://data.fixer.io/api/latest?access_key=' . $api_key . '&base=IDR&symbols=' . $currency . '. Error: ' . ( isset( $body->error->info ) ? $body->error->info : $body->error->type ) );
			return array(
				'status'    => false,
				'result'    => 'Error: ' . ( isset( $body->error->info ) ? $body->error->info : $body->error->type ),
			);
		}
		if ( isset( $body->rates->{ $currency } ) ) {
			return array(
				'status'    => true,
				'result'    => $body->rates->{ $currency },
			);
		}
		return array(
			'status'    => false,
			'result'    => 'Unknown error',
		);
	}

	/**
	 * Get latest currency rate from Currency Layer
	 *
	 * @param  string $currency Currency code.
	 * @param  string $api_key  API key.
	 * @return float            Rates.
	 */
	public function get_currencylayer_rate( $currency = 'USD', $api_key = '' ) {
		global $wp_version;
		$args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$content = wp_remote_get( 'https://api.currencylayer.com/api/live?access_key=' . $api_key . '&source=IDR&symbols=' . $currency, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API CurrencyLayer) https://api.currencylayer.com/api/live?access_key=' . $api_key . '&source=IDR&currencies=' . $currency . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'result'    => $content->get_error_message(),
			);
		}
		if ( 200 !== $content['response']['code'] ) {
			$this->logs->write( '(Error API CurrencyLayer) https://api.currencylayer.com/api/live?access_key=' . $api_key . '&source=IDR&currencies=' . $currency . '. Error code: ' . $content['response']['code'] );
			return array(
				'status'    => false,
				'result'    => 'Error ' . $content['response']['code'],
			);
		}
		$body = json_decode( $content['body'] );
		if ( ! $body->success ) {
			$this->logs->write( '(Error API CurrencyLayer) https://api.currencylayer.com/api/live?access_key=' . $api_key . '&source=IDR&currencies=' . $currency . '. Error: ' . ( isset( $body->error->info ) ? $body->error->info : $body->error->type ) );
			return array(
				'status'    => false,
				'result'    => 'Error: ' . ( isset( $body->error->info ) ? $body->error->info : $body->error->type ),
			);
		}
		if ( isset( $body->quotes->{ 'IDR' . $currency } ) ) {
			return array(
				'status'    => true,
				'result'    => $body->quotes->{ 'IDR' . $currency },
			);
		}
		$this->logs->write( '(Error API CurrencyLayer) https://api.currencylayer.com/api/live?access_key=' . $api_key . '&source=IDR&currencies=' . $currency . '. Error: ' . $body );
		return array(
			'status'    => false,
			'result'    => 'Unknown error',
		);
	}

}
