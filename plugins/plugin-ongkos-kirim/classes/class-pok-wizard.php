<?php
/**
 * Setup wizard class
 *
 * Walkthrough to the basic setup upon installation
 */

/**
 * The class
 */
class POK_Wizard {

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
	 * POK License
	 * 
	 * @var POK_License
	 */
	protected $license;

	/**
	 * Currenct Step
	 *
	 * @var string
	 */
	protected $step   = '';

	/**
	 * Steps for the setup wizard
	 *
	 * @var array
	 */
	protected $steps  = array();

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler_3 $license License Handler.
	 */
	public function __construct( Tonjoo_License_Handler_3 $license ) {
		global $pok_core;
		global $pok_helper;
		$this->core = $pok_core;
		$this->helper = $pok_helper;
		$this->setting = new POK_Setting();
		$this->license = $license;
		$this->setup_wizard();
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts & styles from woocommerce plugin.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! isset( $_GET['wizard'] ) ) {
			return;
		}
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );

		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.1' );
		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ) );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );

		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'wc-setup', WC()->plugin_url() . '/assets/css/wc-setup.css', array( 'dashicons', 'install' ), WC_VERSION );

		wp_register_script( 'wc-setup', WC()->plugin_url() . '/assets/js/admin/wc-setup' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'select2', 'jquery-tiptip' ), WC_VERSION );
		wp_localize_script( 'wc-setup', 'wc_setup_params', array() );

		wp_enqueue_style( 'pok-admin', POK_PLUGIN_URL . '/assets/css/admin.css', array( 'select2' ), POK_VERSION );
		wp_enqueue_style( 'pok-wizard', POK_PLUGIN_URL . '/assets/css/wizard.css', array(), POK_VERSION );

		remove_action( 'admin_enqueue_scripts', array( 'Automattic\WooCommerce\Admin\Loader', 'load_scripts' ), 15 );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'pok_setting' !== $_GET['page'] || ! isset( $_GET['wizard'] ) ) {
			return;
		}
		$this->steps = array(
			'introduction' => array(
				'name'    => __( 'Introduction', 'pok' ),
				'view'    => array( $this, 'setup_introduction' ),
				'handler' => '',
			),
			'license' => array(
				'name'    => __( 'License', 'pok' ),
				'view'    => array( $this, 'setup_license' ),
				'handler' => '',
			),
			'origin' => array(
				'name'    => __( 'Shipping Origin', 'pok' ),
				'view'    => array( $this, 'setup_origin' ),
				'handler' => array( $this, 'setup_origin_save' ),
			),
			'courier' => array(
				'name'    => __( 'Couriers', 'pok' ),
				'view'    => array( $this, 'setup_courier' ),
				'handler' => array( $this, 'setup_courier_save' ),
			),
			'compatibility' => array(
				'name'    => __( 'Compatibility', 'pok' ),
				'view'    => array( $this, 'setup_compatibility' ),
				'handler' => array( $this, 'setup_compatibility_save' ),
			),
			'next_steps' => array(
				'name'    => __( 'Ready!', 'pok' ),
				'view'    => array( $this, 'setup_ready' ),
				'handler' => '',
			),
		);
		$this->step = isset( $_GET['wizard'] ) ? sanitize_key( $_GET['wizard'] ) : current( array_keys( $this->steps ) );

		$this->enqueue_scripts();

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) { // WPCS: CSRF ok.
			call_user_func( $this->steps[ $this->step ]['handler'] );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Get next step link
	 *
	 * @return string Next step URL.
	 */
	public function get_next_step_link() {
		$keys = array_keys( $this->steps );

		return add_query_arg( 'wizard', $keys[ array_search( $this->step, array_keys( $this->steps ), true ) + 1 ] );
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php esc_html_e( 'Plugin Ongkos Kirim &rsaquo; Setup Wizard', 'pok' ); ?></title>
			<?php do_action( 'admin_enqueue_scripts' ); ?>
			<?php wp_print_scripts( 'wc-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
			<style type="text/css">
				.wc-setup-steps {
					justify-content: center;
				}
				.wc-setup-content a {
					color: #faa026;
				}
				.wc-setup-steps li.active:before {
					border-color: #faa026;
				}
				.wc-setup-steps li.active {
					border-color: #faa026;
					color: #faa026;
				}
				.wc-setup-steps li.done:before {
					border-color: #faa026;
				}
				.wc-setup-steps li.done {
					border-color: #faa026;
					color: #faa026;
				}
				.wc-setup .wc-setup-actions .button, .wc-setup .wc-setup-actions .button, .wc-setup .wc-setup-actions .button {
					color: #9C27B0 !important;
					border-color: #9C27B0 !important;
					text-shadow: none;
				}
				.wc-setup .wc-setup-actions .button:active, .wc-setup .wc-setup-actions .button:focus, .wc-setup .wc-setup-actions .button:hover {
					color: #6d1b7b !important;
					border-color: #6d1b7b !important;
				}
				.wc-setup .wc-setup-actions .button-primary, .wc-setup .wc-setup-actions .button-primary, .wc-setup .wc-setup-actions .button-primary {
					background: #9C27B0 !important;
					border-color: #9C27B0 !important;
					color: #fff !important;
					text-shadow: none;
				}
				.wc-setup .wc-setup-actions .button-primary:active, .wc-setup .wc-setup-actions .button-primary:focus, .wc-setup .wc-setup-actions .button-primary:hover {
					background: #6d1b7b !important;
					border-color: #6d1b7b !important;
					color: #fff !important;
				}
				.wc-setup-content .wc-setup-next-steps ul .setup-product a, .wc-setup-content .wc-setup-next-steps ul .setup-product a, .wc-setup-content .wc-setup-next-steps ul .setup-product a {
					background: #9C27B0 !important;
					border-color: #9C27B0 !important;
					color: #fff !important;
					box-shadow: none;
				}
				.wc-setup-content .wc-setup-next-steps ul .setup-product a:active, .wc-setup-content .wc-setup-next-steps ul .setup-product a:focus, .wc-setup-content .wc-setup-next-steps ul .setup-product a:hover {
					background: #6d1b7b !important;
					border-color: #6d1b7b !important;
					color: #fff !important;
					box-shadow: none;
				}
				ul.wc-wizard-payment-gateways li.wc-wizard-gateway .wc-wizard-gateway-enable input:checked+label:before {
					background: #faa026 !important;
					border-color: #faa026 !important;
				}
				#wc-logo img {
					max-width: none;
				}
				.wc-setup-content p {
					line-height: 1.5em;
				}
			</style>
		</head>
		<body class="wc-setup wp-core-ui">
			<?php
				$logo_url = ( ! empty( $this->custom_logo ) ) ? $this->custom_logo : POK_PLUGIN_URL . '/assets/img/logo.png';
			?>
			<h1 id="wc-logo"><a href="https://pluginongkoskirim.com/" target="_blank"><img src="<?php echo esc_url( $logo_url ); ?>" alt="PLugin Ongkos Kirim" /></a></h1>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
			<?php if ( 'next_steps' === $this->step ) : ?>
				<a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard', 'pok' ); ?></a>
			<?php endif; ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		array_shift( $ouput_steps );
		?>
		<ol class="wc-setup-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<li class="
				<?php
				if ( $step_key === $this->step ) {
					echo 'active';
				} elseif ( array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
					echo 'done';
				}
				?>
				"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="wc-setup-content">';
		call_user_func( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	/**
	 * Introduction step.
	 */
	public function setup_introduction() {
		?>
		<h1><?php esc_html_e( 'Welcome to Plugin Ongkos Kirim!', 'pok' ); ?></h1>
		<p><?php esc_html_e( 'Thank you for choosing Plugin Ongkos Kirim to power your online marketplace! This quick setup wizard will help you configure the basic settings of Ongkos Kirim until it ready to use.', 'pok' ); ?></p>
		<p><?php esc_html_e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'pok' ); ?></p>
		<p class="wc-setup-actions step">
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!', 'pok' ); ?></a>
			<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'pok' ); ?></a>
		</p>
		<?php
	}

	/**
	 * License step.
	 */
	public function setup_license() {
		if ( ! $this->license->is_license_active() ) {
			?>
			<h1><?php esc_html_e( 'Activate Your License', 'pok' ); ?></h1>
			<p><?php esc_html_e( 'Plugin Ongkos Kirim can not be used unless you activate the license', 'pok' ); ?></p>
			<form method="post">
				<table class="form-table">
					<tr>
						<th scope="row"><label for="license_key"><?php esc_html_e( 'Your License Key', 'pok' ); ?></label></th>
						<td>
							<input type="text" id="license_key" name="key" value="<?php echo esc_attr( $this->license->get( 'key' ) ); ?>" />
							<p class="description"><?php printf( __( 'Get your license key from <a href="%s">here</a>', 'pok' ), 'https://tonjoostudio.com/manage/plugin/' ); ?></p>
							<p class="license-error" style="color:red;"></p>
						</td>
					</tr>
				</table>
				<p class="wc-setup-actions step">
					<button id="activate" type="button" class="button-primary button button-large button-next"><?php esc_html_e( 'Activate', 'pok' ); ?></button>
					<a href="<?php echo esc_url( admin_url() ); ?>" class="button button-large"><?php esc_html_e( 'Not right now', 'pok' ); ?></a>
				</p>
			</form>
			<script>
				jQuery( function($) {
					$('#activate').on('click', function() {
						var key = $('#license_key').val();
						var data = {
							action: 'tj_activate_plugin_wooongkir-premium',
							key: key,
							tj_license: '<?php echo esc_html( wp_create_nonce( 'tonjoo-activate-license' ) ); ?>'
						}
						$.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							dataType: 'json',
							type: 'post',
							data: data,
							context: this,
							success: function(response) {
								if ( response.status ) {
									window.location = '<?php echo esc_url_raw( $this->get_next_step_link() ); ?>';
								} else {
									$('p.license-error').html( response.message );
									$('.wc-setup-content').unblock();
								}
							},
							error: function(data) {
								console.log(data);
								$('p.license-error').html('<?php esc_html_e( 'Unknown Error. Please Try Again', 'pok' ); ?>');
								$('.wc-setup-content').unblock();
							}
						});
					});
				});
			</script>
			<?php
		} else {
			wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
			exit;
		}
	}

	/**
	 * Selling step.
	 */
	public function setup_origin() {
		if ( ! $this->license->is_license_active() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=introduction' ) );
			exit;
		}
		$origin = $this->setting->get( 'store_location' );
		?>
		<h1><?php esc_html_e( 'Shipping Origin', 'pok' ); ?></h1>
		<form method="post">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="store_location"><?php esc_html_e( 'Your Store Location', 'pok' ); ?></label></th>
					<td>
						<?php if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) : ?>
							<?php $cities = $this->core->get_all_city(); ?>
							<select name="store_location" id="store_location" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pokmv' ); ?>">
								<option value=""><?php esc_html_e( 'Select your store location', 'pokmv' ); ?></option>
								<?php foreach ( $cities as $city ) : ?>
									<option value="<?php echo esc_attr( $city->city_id ); ?>" <?php echo isset( $origin[0] ) && $origin[0] === $city->city_id ? 'selected' : ''; ?>><?php echo esc_html( ( 'Kabupaten' === $city->type ? 'Kab. ' : 'Kota ' ) . $city->city_name . ', ' . $city->province ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php else : ?>
							<select name="store_location" id="store_location" class="select2-ajax" data-action="pok_search_city" data-nonce="<?php echo esc_attr( wp_create_nonce( 'search_city' ) ); ?>" placeholder="<?php esc_attr_e( 'Input city name...', 'pokmv' ); ?>">
								<?php
								if ( isset( $origin ) ) {
									$city = $this->core->get_single_city( $origin[0] );
									if ( isset( $city ) && ! empty( $city ) ) {
										?>
										<option selected value="<?php echo esc_attr( $origin[0] ); ?>"><?php echo esc_html( $city ); ?></option>
										<?php
									}
								}
								?>
							</select>
						<?php endif; ?>
					</td>
				</tr>
			</table>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'pok' ); ?>" name="save_step" />
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'pok' ); ?></a>
				<?php wp_nonce_field( 'pok_wizard' ); ?>
			</p>
		</form>
		<script>
			jQuery(function($) {
				$('.select2-ajax').each(function() {
					var action 	= $(this).data('action');
					var phrase	= $(this).val();
					var nonce 	= $(this).data('nonce');
					$(this).select2({
						ajax: {
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							dataType: 'json',
							data: function( params ) {
								return {
									pok_action: nonce,
									action: action,
									q: params.term
								}
							},
							processResults: function (data, params) {
								return {
									results: data
								};
							},
							cache: true
						},
						minimumInputLength: 3,
						placeholder: $(this).attr('placeholder')
					});
				});

				$('.init-select2').each(function() {
					$(this).select2();
				});
			});
		</script>
		<?php
	}

	/**
	 * Save selling options.
	 */
	public function setup_origin_save() {
		check_admin_referer( 'pok_wizard' );

		if ( isset( $_POST['store_location'] ) && 0 !== intval( $_POST['store_location'] ) ) { // Input var okay.
			$this->setting->set( 'store_location', array( intval( $_POST['store_location'] ) ) ); // Input var okay.
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Withdraw Step.
	 */
	public function setup_courier() {
		if ( ! $this->license->is_license_active() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=introduction' ) );
			exit;
		}
		$all_couriers   = $this->core->get_courier( $this->setting->get( 'base_api' ), $this->setting->get( 'rajaongkir_type' ) );
		$couriers       = $this->setting->get( 'couriers' );
		?>
		<style>
			.courier-options {
				font-size: 0;
				display: flex;
				flex-wrap: wrap;
				gap: 10px;
			}
			.courier-options input {
				display: none;
			}
			.courier-options label {
				background-color: #fff;
				padding: 6px;
				position: relative;
				color: rgba(0,0,0,.7);
				height: 55px;
				width: calc(25% - 24px);
				flex-grow: 0;
				text-align: center;
				border: 2px dashed #ddd;
				transition: all .2s;
			}
			.courier-options label img {
				display: inline-block;
				height: 100%;
				width: auto;
				max-width: 100%;
				opacity: .2;
				transition: all .2s;
			}
			.courier-options label:hover img {
				opacity: 1;
			}
			.courier-options input:checked + label {
				border: 2px solid #faa026;
				background: #fff;
			}
			.courier-options input:checked + label img {
				opacity: 1;
			}
			.courier-options input:disabled + label {
				color: rgba(0,0,0,.3);
				cursor: not-allowed;
			}
			.courier-options input:disabled + label img {
				opacity: 0.1;
				filter: none;
			    -webkit-filter: grayscale(0%);
			    -moz-filter:    grayscale(0%);
			    -ms-filter:     grayscale(0%);
			    -o-filter:      grayscale(0%);
			}
		</style>
		<h1><?php esc_html_e( 'Select Your Couriers', 'pok' ); ?></h1>
		<form method="post">
			<div class="courier-options">
				<?php
				foreach ( $all_couriers as $courier ) {
					?>
					<input type="checkbox" value="<?php echo esc_attr( $courier ); ?>" name="courier[]" id="setting-cour-<?php echo esc_attr( $courier ); ?>" <?php echo in_array( $courier, $all_couriers, true ) && in_array( $courier, $couriers, true ) ? 'checked' : ''; ?> <?php echo ! in_array( $courier, $all_couriers, true ) ? 'disabled' : ''; ?>>
					<label for="setting-cour-<?php echo esc_attr( $courier ); ?>">
						<img src="<?php echo esc_url( POK_PLUGIN_URL . '/assets/img/logo-' . $courier . '.png' ) ?>" alt="<?php echo esc_attr( $this->helper->get_courier_name( $courier ) ); ?>" title="<?php echo esc_attr( $this->helper->get_courier_name( $courier ) ); ?>">
					</label>
					<?php
				}
				?>
			</div>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'pok' ); ?>" name="save_step" />
				<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'pok' ); ?></a>
				<?php wp_nonce_field( 'pok_wizard' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save withdraw options.
	 */
	public function setup_courier_save() {
		check_admin_referer( 'pok_wizard' );

		if ( isset( $_POST['courier'] ) && ! empty( $_POST['courier'] ) ) { // Input var okay.
			$this->setting->set( 'couriers', $_POST['courier'] ); // Input var okay.
		}

		wp_safe_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Withdraw Step.
	 */
	public function setup_compatibility() {
		if ( ! $this->license->is_license_active() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=introduction' ) );
			exit;
		}
		if ( ! $this->helper->is_using_checkout_block() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=next_steps' ) );
			exit;
		}
		?>
		<h1><?php esc_html_e( 'This plugin may not work properly!', 'pok' ); ?></h1>
		<p><?php esc_html_e( 'Plugin Ongkos Kirim does not currently support block-based checkout page. To get our plugin working properly, you need to switch your checkout page to classic mode.', 'pok' ); ?></p>
		<p class="wc-setup-actions step">
			<a href="<?php echo esc_url( add_query_arg( [ 'page' => 'pok_setting', 'pok_action' => wp_create_nonce( 'switch_classic_checkout' ), 'context' => 'wizard' ], admin_url( 'admin.php' ) ) ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Switch to classic checkout', 'pok' ); ?></a>
			<a href="https://tonjoostudio.com/cara-mengubah-halaman-checkout-woocommerce-ke-classic-editor/" target="_blank" class="button button-large button-next"><?php esc_html_e( 'Learn more', 'pok' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Final step.
	 */
	public function setup_ready() {
		if ( ! $this->license->is_license_active() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=introduction' ) );
			exit;
		}
		if ( $this->helper->is_using_checkout_block() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&wizard=compatibility' ) );
			exit;
		}
		?>
		<h1><?php esc_html_e( 'Your Site is Ready!', 'pok' ); ?></h1>
		<p><?php esc_html_e( 'Plugin Ongkos Kirim is configured, shipping costs should be shown on the checkout page when customers fill in the address. Why do not you try it yourself?' ); ?></p>
		<div class="wc-setup-next-steps">
			<div class="wc-setup-next-steps-first">
				<span style="color:#666;"><?php esc_html_e( 'Thank you for using Plugin Ongkos Kirim.', 'pok' ); ?></span><br>
				<span style="color:#666;"><?php esc_html_e( 'If you need an assist, click the link below to find a way that may you need.', 'pok' ); ?></span>
				<h3 style="margin-top: 5px;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting&tab=help' ) ); ?>"><?php esc_html_e( 'Plugin Ongkos Kirim Help Center', 'pok' ); ?></a></h3>
			</div>
			<div class="wc-setup-next-steps-last" style="padding-left: 30px;">
				<h2><?php esc_html_e( 'Next Steps', 'pok' ); ?></h2>
				<ul style="padding: 0;">
					<li class="setup-product"><a class="button button-primary button-large" href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting' ) ); ?>"><?php esc_html_e( 'Set up advanced settings', 'pok' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}
}
