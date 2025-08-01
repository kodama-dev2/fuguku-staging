<?php
/**
 * Tonjoo License Handler
 *
 * Version:         3.2.0
 * Contributors:    gama
 *
 * @package tonjoo-license-handler
 */

if ( ! class_exists( 'Tonjoo_License_Handler_3' ) ) {

	/**
	 * Tonjoo Plugin License Handler
	 */
	class Tonjoo_License_Handler_3 {

		/**
		 * Plugin update name
		 *
		 * @var string
		 */
		public $plugin;

		/**
		 * Plugin main file path
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Default args for license data
		 *
		 * @var string
		 */
		public $default_args;

		/**
		 * Licenses data from DB
		 * 
		 * @var array
		 */
		public $licenses;

		/**
		 * Tonjoo license API object
		 *
		 * @var object
		 */
		public $license;

		/**
		 * Max attempt number for license check
		 *
		 * @var int
		 */
		public $max_attempt;

		/**
		 * License check interval
		 *
		 * @var int
		 */
		public $check_interval;

		/**
		 * License form url
		 *
		 * @var string
		 */
		public $license_form;

		/**
		 * Constructor
		 *
		 * @param array $args Arguments.
		 */
		public function __construct( $name = '', $path = '' ) {
			add_action( 'admin_init', array( $this, 'init_updater' ), 35 );
			$this->plugin       = $name;
			$this->plugin_path  = $path;
			$this->default_args = array(
				'path'			=> $path,
				'active'    	=> false,
				'key'       	=> '',
				'type'      	=> '-',
				'expiry'    	=> '-',
				'last_check'    => time(),
				'hide_notice'	=> array(),
				'check_attempt' => 0,
				'email'         => '',
				'activation_date' => '-',
				'hash'          => ''
			);
			$this->licenses 	= get_option( TONJOO_PLUGINS_OPTION_NAME, array() );
			$this->license      = new Tonjoo_License_API( $this->plugin );

			// register update checker.
			if ( ! wp_next_scheduled( 'tonjoo_puc_' . $this->plugin ) ) {
				wp_schedule_event( time(), 'daily', 'tonjoo_puc_' . $this->plugin );
			}

			// clear old crons.
			if ( $time = wp_next_scheduled( $this->plugin . '_license_check' ) ) {
				wp_unschedule_event( $time, $this->plugin . '_license_check' );
			}

			// check plugin update.
			add_action( 'tonjoo_puc_' . $this->plugin, array( $this, 'check_plugin_update' ) );

			// ajax actions.
			add_action( 'wp_ajax_tj_activate_plugin_' . $this->plugin, array( $this, 'action_activation' ) );
			add_action( 'wp_ajax_tj_deactivate_plugin_' . $this->plugin, array( $this, 'action_deactivation' ) );
			add_action( 'wp_ajax_tj_license_debug_' . $this->plugin, array( $this, 'action_debug' ) );
		}

		/**
		 * Init option
		 */
		public function save_plugin() {
			$plugins = get_option( TONJOO_PLUGINS_OPTION_NAME, array() );
			if ( ! isset( $plugins[ $this->plugin ] ) ) {
				$old_license_data = get_option( 'tonjoo_plugin_license', array() );
				if ( isset( $old_license_data[ $this->plugin ] ) ) {
					$plugins[ $this->plugin ] = wp_parse_args( $old_license_data[ $this->plugin ], $this->default_args );
					unset( $old_license_data[ $this->plugin ] );
					if ( empty( $old_license_data ) ) {
						delete_option( 'tonjoo_plugin_license' );
					} else {
						update_option( 'tonjoo_plugin_license', $old_license_data );
					}
				} else {
					$plugins[ $this->plugin ] = $this->default_args;
				}
				update_option( TONJOO_PLUGINS_OPTION_NAME, $plugins );
			} elseif ( $plugins[ $this->plugin ]['path'] !== $this->plugin_path ) { // fix when changing hosting.
				$plugins[ $this->plugin ]['path'] = $this->plugin_path;
				update_option( TONJOO_PLUGINS_OPTION_NAME, $plugins );
			}
			$this->licenses = get_option( TONJOO_PLUGINS_OPTION_NAME );
			return $this->licenses;
		}

		/**
		 * Init plugin updater
		 */
		public function init_updater() {
			if ( $this->is_license_active() ) {
				$license = $this->get_status();
				// if user manually click Check for update.
				if ( isset( $_GET['puc_check_for_updates'], $_GET['puc_slug'] ) && $_GET['puc_slug'] == $this->plugin && current_user_can( 'update_plugins' ) && check_admin_referer( 'puc_check_for_updates' ) ) {
					$this->license->update_json( $license['key'], $license['hash'] );
				}
				$this->license->load_updater( $license['key'], $this->plugin_path, DAY_IN_SECONDS );
			}
		}

		/**
		 * Update local json every 12 hours to check the update.
		 */
		public function check_plugin_update() {
			if ( $this->is_license_active() ) {
				$license = $this->get_status();
				$this->license->update_json( $license['key'], $license['hash'] );
			}
		}

		/**
		 * Scheduled check license status on server
		 *
		 * @param  boolean $force Force check?.
		 */
		public function check_status( $force = false ) {
			$license = $this->get_status();

			// forget it if license key is empty.
			if ( empty( $license['key'] ) ) {
				return;
			}

			// current status on website is must active.
			if ( true === $force || true === $license['active'] ) {

				// check if current attempt is below max.
				if ( true === $force || $this->max_attempt > $license['check_attempt'] ) {
					$status = $this->license->status( $license['key'] );
					if ( true === $status['status'] ) {
						if ( strtotime( $status['data']->validUntil ) < time() ) { // expired.
							$this->license->logs->write( 'License ' . $this->plugin . ' marked as expired' );
							$args = array(
								'active'        => false,
								'key'           => $license['key'],
								'type'          => $license['type'],
								'expiry'        => strtotime( $status['data']->validUntil ),
								'last_check'    => time(),
								'check_attempt' => 0,
								'email'         => $license['email'],
								'activation_date' => $license['activation_date'],
							);
						} else { // license active.
							$args = array(
								'active'        => true,
								'key'           => $license['key'],
								'type'          => $status['data']->licenseType,
								'expiry'        => strtotime( $status['data']->validUntil ),
								'last_check'    => time(),
								'check_attempt' => 0,
								'email'         => $license['email'],
								'activation_date' => $license['activation_date'],
								'hide_notice'   => $license['hide_notice'],
							);
						}
					} else { // server returns false.
						if ( __( "Error: Can't connect to server", 'pok' ) !== $status['data'] ) {
							$args = array(
								'key'           => $license['key'],
								'active'        => false,
								'type'          => $license['type'],
								'expiry'        => $license['expiry'],
								'last_check'    => time(),
								'check_attempt' => 0,
								'email'         => $license['email'],
								'activation_date' => $license['activation_date'],
								'hide_notice'   => $license['hide_notice'],
							);
						} else { // can't connect to server.
							$this->license->logs->write( 'Failed to check license ' . $this->plugin . '. Attempt ' . ( intval( $license['check_attempt'] ) + 1 ) );
							$args = array(
								'key'           => $license['key'],
								'active'        => $license['active'],
								'type'          => $license['type'],
								'expiry'        => $license['expiry'],
								'last_check'    => time(),
								'check_attempt' => intval( $license['check_attempt'] ) + 1,
								'email'         => $license['email'],
								'activation_date' => $license['activation_date'],
								'hide_notice'   => $license['hide_notice'],
							);
						}
					}

					// if the attempt is reached max, then set plugin to deactive.
				} else {
					$this->license->logs->write( 'Failed to check license ' . $this->plugin . ' and reached max attempt. License deactivated.' );
					$args = array(
						'active'        => false,
						'key'           => $license['key'],
						'type'          => $license['type'],
						'expiry'        => $license['expiry'],
						'last_check'    => time(),
						'check_attempt' => 0,
						'email'         => $license['email'],
						'activation_date' => $license['activation_date'],
					);
				}
				$this->set_status( $args );
			}
		}

		/**
		 * Get current plugin license status on database
		 *
		 * @return array License status.
		 */
		public function get_status() {
			if ( ! isset( $this->licenses[ $this->plugin ] ) ) {
				$this->save_plugin();
			}
			return wp_parse_args( $this->licenses[ $this->plugin ], $this->default_args );
		}

		/**
		 * Set plugin license status to database
		 *
		 * @param array $args License array.
		 */
		public function set_status( $args = array() ) {
			$args = wp_parse_args( $args, $this->default_args );
			$this->licenses[ $this->plugin ] = $args;
			update_option( TONJOO_PLUGINS_OPTION_NAME, $this->licenses );
			$this->licenses = get_option( TONJOO_PLUGINS_OPTION_NAME );
		}

		/**
		 * Get license status item
		 *
		 * @param  string $index Index name.
		 * @return mixed         Status item.
		 */
		public function get( $index = 'active' ) {
			$status = $this->get_status();
			if ( isset( $status[ $index ] ) ) {
				return $status[ $index ];
			}
			return false;
		}

		/**
		 * Render license activation form
		 */
		public function render_form() {
			$plugin_info    = get_plugin_data( $this->plugin_path );
			$license_info   = $this->get_status();
			$is_active      = $this->is_license_active();
			$is_expired     = $this->is_license_expired();
			include 'view/license-form.php';
		}

		/**
		 * License activation handler
		 */
		public function action_activation() {
			$response = array(
				'status'        => false,
				'message'       => __( 'Error: Authentication Failed. Please try again', 'pok' ),
				'last_check'    => $this->get_local_time(),
			);
			$current = $this->get_status();
			if ( isset( $_POST['tj_license'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tj_license'] ) ), 'tonjoo-activate-license' ) ) { // Input var okay.
				if ( isset( $_POST['key'] ) ) { // Input var okay.
					$activation = $this->license->activate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
					if ( true === $activation['status'] ) {
						$status = $this->license->status( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
						if ( true === $status['status'] ) {
							if ( strtotime( $status['data']->validUntil ) < time() ) { // expired.
								$args = array(
									'active'        => false,
									'last_check'    => time(),
								);
								$response = array(
									'status'        => false,
									'message'       => sprintf( __( 'Your license has expired at %s', 'pok' ), $this->get_local_time( strtotime( $status['data']->validUntil ) ) ),
									'last_check'    => $this->get_local_time( $args['last_check'] ),
								);
							} else { // active.
								$args = array(
									'active'        => true,
									'key'           => sanitize_text_field( wp_unslash( $_POST['key'] ) ), // Input var okay.
									'type'          => $status['data']->licenseType,
									'expiry'        => strtotime( $status['data']->validUntil ),
									'last_check'    => time(),
									'check_attempt' => 0,
									'email'         => isset( $activation['activation_data']->email ) ? $activation['activation_data']->email : '',
									'activation_date' => time(),
									'hash'          => isset( $activation['activation_data']->hash ) ? $activation['activation_data']->hash : ''
								);
								$response = array(
									'status'        => true,
									'message'       => __( 'Activation Success', 'pok' ),
									'last_check'    => $this->get_local_time( $args['last_check'] ),
								);
							}
						} else {
							$this->license->deactivate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
							$args = array(
								'last_check' => time(),
							);
							$response = array(
								'status'        => false,
								'message'       => $status['data'],
								'last_check'    => $this->get_local_time( $args['last_check'] ),
							);
						}
					} else {
						$args = array(
							'last_check' => time(),
						);
						$response = array(
							'status'        => false,
							'message'       => $activation['data'],
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					}
				} else {
					$args = array(
						'last_check' => time(),
					);
					$response = array(
						'status'        => false,
						'message'       => __( 'License key is empty', 'pok' ),
						'last_check'    => $this->get_local_time(),
					);
				}
			}
			$this->set_status( $args );
			wp_send_json( $response );
		}

		/**
		 * License deactivation handler
		 */
		public function action_deactivation() {
			$response = array(
				'status'        => false,
				'message'       => __( 'Error: Authentication Failed. Please try again', 'pok' ),
				'last_check'    => $this->get_local_time(),
			);
			$current = $this->get_status();
			if ( isset( $_POST['tj_license'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tj_license'] ) ), 'tonjoo-deactivate-license' ) ) { // Input var okay.
				if ( isset( $_POST['key'] ) ) { // Input var okay.
					$deactivation = $this->license->deactivate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
					if ( true === $deactivation['status'] ) {
						$args = array(
							'last_check' => time(),
						);
						$response = array(
							'status'        => true,
							'message'       => __( 'Deactivation Success', 'pok' ),
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					} else {
						$args = $current;
						$args['last_check'] = time();
						$response = array(
							'status'        => false,
							'message'       => $deactivation['data'],
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					}
				} else {
					$args = array(
						'last_check' => time(),
					);
					$response = array(
						'status'        => false,
						'message'       => __( 'License key is empty', 'pok' ),
						'last_check'    => $this->get_local_time(),
					);
				}
			}
			$this->set_status( $args );
			wp_send_json( $response );
		}

		/**
		 * Check if plugin is active
		 *
		 * @return boolean License is active or not.
		 */
		public function is_license_active() {
			return $this->get( 'active' );
		}

		/**
		 * Check if license is expired
		 *
		 * @return boolean Check license expired or not.
		 */
		public function is_license_expired() {
			$license = $this->get_status();
			return ( '-' !== $license['expiry'] && time() > $license['expiry'] && ! $license['active'] );
		}

		/**
		 * Get local time from timestamps
		 *
		 * @param  integer $time Timestamps.
		 * @return string        Current time in local format.
		 */
		private function get_local_time( $time = 0 ) {
			if ( 0 === $time ) {
				$time = time();
			}
			$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time + $offset );
		}

		/**
		 * Ajax debug action
		 */
		public function action_debug() {
			if ( isset( $_POST['debug_action'] ) ) {
				if ( 'delete-license' === $_POST['debug_action'] ) {
					$this->set_status( array() );
					echo 'success';
				} elseif ( 'clear-logs' === $_POST['debug_action'] ) {
					if ( $this->license->logs->clear() ) {
						echo 'success';
					}
				} elseif ( 're-check' === $_POST['debug_action'] ) {
					$this->check_status( true );
					echo 'success';
				}
			}
			die;
		}
	}

}
