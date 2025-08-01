<div id="tj-debug" class="postbox">
	<h3 class="hndle">Debugger</h3>
	<div class="inside" style="z-index:1;">
		<ul class="debug-value">
			<li>
				<h4>WP Info</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">License Manager Version</td>
							<td class="value"><?php echo defined( 'TONJOO_LICENSE_MANAGER_VERSION' ) ? TONJOO_LICENSE_MANAGER_VERSION : '3.2.0'; ?></td>
						</tr>
						<tr>
							<td class="index">Loaded From</td>
							<td class="value"><?php echo str_replace( '\libs\tonjoo-license\view\general-debugger.php', '', __FILE__ ); ?></td>
						</tr>
						<tr>
							<td class="index">API Server</td>
							<td class="value"><?php echo TONJOO_LICENSE_BASE_API; ?></td>
						</tr>
						<tr>
							<td class="index">PHP Version</td>
							<td class="value"><?php echo esc_html( PHP_VERSION ); ?></td>
						</tr>
						<tr>
							<td class="index">WordPress Version</td>
							<td class="value">
								<?php
								global $wp_version;
								echo esc_html( $wp_version );
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Is WooCommerce Installed</td>
							<td class="value">
								<?php echo file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ? 'yes' : 'no'; ?>
							</td>
						</tr>
						<tr>
							<td class="index">Is WooCommerce Active</td>
							<td class="value">
								<?php echo in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ? 'yes' : 'no'; ?>
							</td>
						</tr>
						<tr>
							<td class="index">WooCommerce Version</td>
							<td class="value">
								<?php
								global $woocommerce;
								echo ! is_null( $woocommerce ) ? esc_html( $woocommerce->version ) : '-';
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<?php
				$keys = array();
				foreach ( $this->plugins as $name => $license ) {
					if ( empty( $license['key'] ) ) {
						continue;
					}

					if ( empty( $license['path'] ) || ! $this->is_plugin_active( plugin_basename( $license['path'] ) ) ) {
						continue;
					}

					$keys[] = $license['key'];
				}
				$status = $this->api->bulk_status( $keys, true );
			?>
			<li>
				<h4>License Status API Hit</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">URL</td>
							<td class="value"><?php echo $status['url']; ?></td>
						</tr>
						<tr>
							<td class="index">Response</td>
							<td class="value">
								<?php
								if ( ! is_null( $status['response'] ) ) {
									if ( ! is_wp_error( $status['response'] ) ) {
										$response = json_decode( wp_remote_retrieve_body( $status['response'] ) );
										echo '<pre>';
										print_r( $response );
										echo '</pre>';
									} else {
										echo $status['response']->get_error_message();
									}
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>Logs <a href="<?php echo esc_url( $logs->get_file_url() ); ?>" target="_blank">(view file)</a></h4>
				<div class="logs">
					<?php
					$log_contents = $logs->read( true );
					if ( is_array( $log_contents ) ) {
						$log_contents = array_reverse( $log_contents );
						foreach ( $log_contents as $line ) {
							echo esc_html( $line ) . '<br>';
						}
					}
					?>
				</div>
			</li>
			<li>
				<h4>Actions</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Force Re-Check License</td>
							<td class="value"><button type="button" class="button debug-action" data-action="re-check">Re-Check</button></td>
						</tr>
						<tr>
							<td class="index">Delete All Local License Data</td>
							<td class="value"><button type="button" class="button debug-action" data-action="clear-license">Delete All</button></td>
						</tr>
						<tr>
							<td class="index">Check My IP</td>
							<td class="value"><button type="button" class="button debug-action" data-action="check-ip">Check My IP</button> <span class="ip-result"></span></td>
						</tr>
						<tr>
							<td class="index">Clear Logs</td>
							<td class="value"><button type="button" class="button debug-action" data-action="clear-logs">Clear</button></td>
						</tr>
					</tbody>
				</table>
			</li>
		</ul>
	</div>
</div>
<script>
	jQuery(function($) {
		$('.button.toggle').on('click',function() {
			$(this).parents('.value').toggleClass('hide');
		});
		$('.debug-action').on('click', function() {
			var action = $(this).data('action');
			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					action: 'tj_license_debug',
					debug_action: action
				},
				context: this,
				beforeSend: function() {
					$(this).prop('disabled',true);
				},
				success: function(response) {
					if ( 'check-ip' === action ) {
						$('.ip-result').html( response );
						$(this).prop('disabled',false);
					} else {
						if ( 'success' === response ) {
							location.reload();
						} else {
							$(this).prop('disabled',false);
							console.log( response );
						}
					}
				},
				error: function(data) {
					$(this).prop('disabled',false);
					console.log( data );
				}
			});
		});
	});
</script>
