<?php
/**
 * Tonjoo License Manager
 *
 * Version:         3.2.0
 * Contributors:    gama
 *
 * @package tonjoo-license-manager
 */

if ( ! defined( 'TONJOO_LICENSE_MANAGER_VERSION' ) ) {
	define( 'TONJOO_LICENSE_MANAGER_VERSION', '3.2.1' );
}

if ( ! defined( 'TONJOO_PLUGINS_OPTION_NAME' ) ) {
	define( 'TONJOO_PLUGINS_OPTION_NAME', 'tonjoo_plugins' );
}

if ( ! class_exists( 'Tonjoo_License_Manager' ) ) {

	require_once 'tonjoo-license-api.php';
	require_once 'tonjoo-license-handler.php';

	class Tonjoo_License_Manager {

		/**
		 * API class
		 * 
		 * @var object
		 */
		protected $api;

		/**
		 * Max license check attempts
		 * 
		 * @var integer
		 */
		protected $max_attempt;

		/**
		 * License check interval
		 * 
		 * @var string
		 */
		protected $check_interval;

		/**
		 * Registered plugins
		 * 
		 * @var array
		 */
		protected $plugins;

		public function __construct() {
			$this->api 				= new Tonjoo_License_API();
			$this->max_attempt   	= defined( 'TONJOO_PLUGINS_MAX_ATTEMPT' ) ? TONJOO_PLUGINS_MAX_ATTEMPT : 5;
			$this->check_interval 	= defined( 'TONJOO_PLUGINS_CHECK_INTERVAL' ) && in_array( TONJOO_PLUGINS_CHECK_INTERVAL, array( 'hourly', 'twicedaily', 'daily' ), true ) ? TONJOO_PLUGINS_CHECK_INTERVAL : 'twicedaily';
			$this->plugins 			= get_option( TONJOO_PLUGINS_OPTION_NAME, array() );
			if ( ! wp_next_scheduled( 'tonjoo_plugin_license_check' ) ) {
				wp_schedule_event( time(), $this->check_interval, 'tonjoo_plugin_license_check' );
			}
			add_action( 'tonjoo_plugin_license_check', array( $this, 'check_status' ) );
			add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
			add_action( 'admin_init', array( $this, 'hide_notices' ) );
			add_action( 'wp_ajax_tj_license_debug', array( $this, 'action_debug' ) );
		}

		/**
		 * Create license manager page
		 *
		 * @since 2.1.0
		 */
		public function register_admin_menu() {
			add_plugins_page( 'Tonjoo License Manager', 'Tonjoo License Manager', 'manage_options', 'tonjoo_license_manager', array( $this, 'license_manager_page' ) );
		}

		public function license_manager_page() {
			include 'view/license-manager.php';
		}

		public function register_plugin( $name = '', $path = '' ) {
			$plugin =  new Tonjoo_License_Handler_3( $name, $path );
			$this->plugins = $plugin->save_plugin();
			return $plugin;
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
		 * Show admin notices
		 */
		public function show_notice() {
			global $current_screen;
			if ( 'plugins_page_tonjoo_license_manager' === $current_screen->id ) {
				return;
			}

			$notices = array();
			foreach ( $this->plugins as $name => $license ) {
				if ( empty( $license['path'] ) || ! $this->is_plugin_active( plugin_basename( $license['path'] ) ) ) {
					continue;
				}
				$notices[ $name ] = array();
				$plugin = get_plugin_data( $license['path'] );
				$is_expired = ( '-' !== $license['expiry'] && time() > $license['expiry'] && ! $license['active'] );
				if ( ! $license['active'] ) {
					if ( $is_expired && ! in_array( 'expired', $license['hide_notice'], true ) ) {
						if ( 'trial' === $license['type'] ) {
							$notices[ $name ]['expired'] = sprintf( __( 'Your trial for <strong>%s</strong> has ended.', 'pok' ), esc_html( $plugin['Name'] ) );
						} else {
							$notices[ $name ]['expired'] = sprintf( __( 'Your license for <strong>%s</strong> has expired.', 'pok' ), esc_html( $plugin['Name'] ) );
						}
					} elseif ( ! in_array( 'activate', $license['hide_notice'], true ) ) {
						$notices[ $name ]['activate'] = sprintf( __( 'Please activate your <strong>%s</strong> license.', 'pok' ), esc_html( $plugin['Name'] ) );
					}
				} else {
					if ( 'trial' !== $license['type'] && ( $license['expiry'] - time() ) < MONTH_IN_SECONDS && ! in_array( 'month_to_expired', $license['hide_notice'], true ) ) {
						$notices[ $name ]['month_to_expired'] = sprintf( __( 'Your license for <strong>%s</strong> will ended soon.', 'pok' ), esc_html( $plugin['Name'] ) );
					} elseif ( 'trial' === $license['type'] && ( $license['expiry'] - time() ) < ( 3 * DAY_IN_SECONDS ) && ! in_array( '3day_to_expired', $license['hide_notice'], true ) ) {
						$notices[ $name ]['3day_to_expired'] = sprintf( __( 'Your trial license for <strong>%s</strong> will ended soon.', 'pok' ), esc_html( $plugin['Name'] ) );
					}
				}
			}
			$empty = true;
			$hide = array();
			foreach ( $notices as $plugin => $messages ) {
				if ( ! empty( $messages ) ) {
					$empty = false;
					$hide[ $plugin ] = array_keys( $messages );
				}
			}
			if ( ! $empty ) {
				?>
				<div class="notice notice-warning is-dismissible">
					<div class="messages" style="padding: 10px 0;">
						<?php foreach ( $notices as $plugin => $messages ) : ?>
							<?php foreach ( $messages as $message) : ?>
								<p style="margin:0; padding: 1px 0;"><?php echo wp_kses_post( $message ) ?></p>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
					<div class="buttons" style="margin: 10px 0;">
						<a href="<?php echo esc_url( admin_url( 'plugins.php?page=tonjoo_license_manager' ) ); ?>" class="button-primary"><?php esc_html_e( 'Manage License', 'pok' ); ?></a>
						<a href="<?php echo esc_url( add_query_arg( array( 'hide' => $hide, 'ref' => $this->get_current_url() ), admin_url( 'plugins.php?page=tonjoo_license_manager' ) ) ); ?>" class="button"><?php esc_html_e( 'Hide Notice', 'pok' ) ?></a>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Hide admin notices for temporary
		 */
		public function hide_notices() {
			if ( isset( $_GET['page'] ) && 'tonjoo_license_manager' === sanitize_text_field( $_GET['page'] ) && isset( $_GET['hide'] ) && ! empty( $_GET['hide'] ) ) {
				$hide = wp_unslash( $_GET['hide'] );
				foreach ( $this->plugins as $name => $license ) {
					if ( isset( $hide[ $name ] ) ) {
						$this->plugins[ $name ]['hide_notice'] = array_merge( $license['hide_notice'], $hide[ $name ] );
					}
				}
				update_option( TONJOO_PLUGINS_OPTION_NAME, $this->plugins );
				$this->plugins = get_option( TONJOO_PLUGINS_OPTION_NAME );
				if ( isset( $_GET['ref'] ) && ! empty( $_GET['ref'] ) ) {
					wp_safe_redirect( sanitize_text_field( $_GET['ref'] ) );
				} else {
					wp_safe_redirect( admin_url( 'plugins.php?page=tonjoo_license_manager' ) );
				}
				exit;
			}
		}

		/**
		 * Get current URL
		 *
		 * @return string Current URL.
		 */
		private function get_current_url() {
			return ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		/**
		 * Determines whether a plugin is active.
		 *
		 * @param string $plugin Path to the plugin file relative to the plugins directory.
		 * @return bool
		 */
		public function is_plugin_active( $plugin ) {
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true );
		}

		/**
		 * Scheduled check license status on server
		 *
		 * @param boolean $force Force check?.
		 */
		public function check_status( $force = false ) {
			$keys = array();
			foreach ( $this->plugins as $name => $license ) {
				if ( empty( $license['key'] ) ) {
					continue;
				}

				if ( empty( $license['path'] ) || ! $this->is_plugin_active( plugin_basename( $license['path'] ) ) ) {
					continue;
				}

				if ( empty( $license['active'] ) && false === $force ) {
					continue;
				}

				if ( $this->max_attempt <= $license['check_attempt'] && false === $force ) {
					continue;
				}

				$keys[] = $license['key'];
			}

			if ( empty( $keys ) ) {
				return;
			}

			$status = $this->api->bulk_status( $keys );

			foreach ( $this->plugins as $name => $license ) {
				// forget it if license key is empty.
				if ( empty( $license['key'] ) ) {
					continue;
				}

				if ( empty( $license['path'] ) || ! $this->is_plugin_active( plugin_basename( $license['path'] ) ) ) {
					continue;
				}

				// current status on website is must active.
				if ( true === $force || true === $license['active'] ) {

					// check if current attempt is below max.
					if ( true === $force || $this->max_attempt > $license['check_attempt'] ) {

						if ( true === $status['status'] ) {

							if ( empty( $status['data'][ $license['key'] ]['validUntil'] ) ) {
								$this->api->logs->write( 'Failed to check license data ' . $name . '. Attempt ' . ( intval( $license['check_attempt'] ) + 1 ) );
								$args = array(
									'key'             => $license['key'],
									'active'          => $license['active'],
									'type'            => $license['type'],
									'expiry'          => $license['expiry'],
									'last_check'      => time(),
									'check_attempt'   => intval( $license['check_attempt'] ) + 1,
									'email'           => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'     => $license['hide_notice'],
								);

							} else {

								if ( strtotime( $status['data'][ $license['key'] ]['validUntil'] ) < time() ) { // expired.
									$this->api->logs->write( 'License ' . $name . ' marked as expired' );
									$args = array(
										'active'          => false,
										'key'             => $license['key'],
										'type'            => $license['type'],
										'expiry'          => strtotime( $status['data'][ $license['key'] ]['validUntil'] ),
										'last_check'      => time(),
										'check_attempt'   => 0,
										'email'           => $license['email'],
										'activation_date' => $license['activation_date'],
									);
								} else { // license active.
									$args = array(
										'active'          => true,
										'key'             => $license['key'],
										'type'            => $status['data'][ $license['key'] ]['licenseType'],
										'expiry'          => strtotime( $status['data'][ $license['key'] ]['validUntil'] ),
										'last_check'      => time(),
										'check_attempt'   => 0,
										'email'           => $license['email'],
										'activation_date' => $license['activation_date'],
										'hide_notice'     => $license['hide_notice'],
									);
								}
							}
						} else { // server returns false.
							if ( __( "Error: Can't connect to server", 'pok' ) !== $status['data'] ) {
								$args = array(
									'key'             => $license['key'],
									'active'          => false,
									'type'            => $license['type'],
									'expiry'          => $license['expiry'],
									'last_check'      => time(),
									'check_attempt'   => 0,
									'email'           => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'     => $license['hide_notice'],
								);
							} else { // can't connect to server.
								$this->api->logs->write( 'Failed to check license ' . $name . '. Attempt ' . ( intval( $license['check_attempt'] ) + 1 ) );
								$args = array(
									'key'             => $license['key'],
									'active'          => $license['active'],
									'type'            => $license['type'],
									'expiry'          => $license['expiry'],
									'last_check'      => time(),
									'check_attempt'   => intval( $license['check_attempt'] ) + 1,
									'email'           => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'     => $license['hide_notice'],
								);
							}
						}

						// if the attempt is reached max, then set plugin to deactive.
					} else {
						$this->api->logs->write( 'Failed to check license ' . $name . ' and reached max attempt. License deactivated.' );
						$args = array(
							'active'          => false,
							'key'             => $license['key'],
							'type'            => $license['type'],
							'expiry'          => $license['expiry'],
							'last_check'      => time(),
							'check_attempt'   => 0,
							'email'           => $license['email'],
							'activation_date' => $license['activation_date'],
						);
					}

					$args = wp_parse_args( $args, $license );
					$this->plugins[ $name ] = $args;
				}
			}

			update_option( TONJOO_PLUGINS_OPTION_NAME, $this->plugins );
			$this->plugins = get_option( TONJOO_PLUGINS_OPTION_NAME );
		}

		/**
		 * Ajax debug action
		 */
		public function action_debug() {
			if ( isset( $_POST['debug_action'] ) ) {
				if ( 'clear-logs' === $_POST['debug_action'] ) {
					if ( $this->api->logs->clear() ) {
						echo 'success';
					}
				} elseif ( 're-check' === $_POST['debug_action'] ) {
					$this->check_status( true );
					echo 'success';
				} elseif ( 'clear-license' === $_POST['debug_action'] ) {
					delete_option( TONJOO_PLUGINS_OPTION_NAME );
					echo 'success';
				} elseif ( 'check-ip' === $_POST['debug_action'] ) {
					$response = wp_remote_get( 'https://pluginongkoskirim.com/response.php' );
					if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
						echo wp_remote_retrieve_body( $response );
					} else {
						echo "Error " . wp_remote_retrieve_response_code( $response );
					}
				}
			}
			die;
		}
	}

	global $tonjoo_license_manager;
	$tonjoo_license_manager = new Tonjoo_License_Manager();
}
