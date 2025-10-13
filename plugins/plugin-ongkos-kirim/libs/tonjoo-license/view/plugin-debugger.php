<div id="tj-debug" class="postbox">
	<h3 class="hndle">Debugger</h3>
	<div class="inside" style="z-index:1;">
		<ul class="debug-value">
			<li>
				<h4>Current Config</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Plugin Update Name</td>
							<td class="value"><?php echo $plugin_name; ?></td>
						</tr>
						<tr>
							<td class="index">Plugin Path</td>
							<td class="value"><?php echo $plugin['path']; ?></td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>Current Status (on local)</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Is Activated?</td>
							<td class="value"><?php echo $plugin['active'] ? 'yes' : 'no'; ?></td>
						</tr>
						<tr>
							<td class="index">License Key</td>
							<td class="value"><?php echo $plugin['key']; ?></td>
						</tr>
						<tr>
							<td class="index">License Type</td>
							<td class="value"><?php echo $plugin['type']; ?></td>
						</tr>
						<tr>
							<td class="index">Registrar</td>
							<td class="value"><?php echo $plugin['email']; ?></td>
						</tr>
						<tr>
							<td class="index">Activation Date</td>
							<td class="value"><?php echo '-' === $plugin['activation_date'] ? '-' : $this->get_local_time( $plugin['activation_date'] ); ?></td>
						</tr>
						<tr>
							<td class="index">License Expiration</td>
							<td class="value"><?php echo '-' === $plugin['expiry'] ? '-' : $this->get_local_time( $plugin['expiry'] ); ?></td>
						</tr>
						<tr>
							<td class="index">Last Check</td>
							<td class="value"><?php echo $this->get_local_time( $plugin['last_check'] ); ?></td>
						</tr>
						<tr>
							<td class="index">Next Check</td>
							<td class="value"><?php echo $this->get_local_time( wp_next_scheduled( 'tonjoo_plugin_license_check' ) ); ?></td>
						</tr>
						<tr>
							<td class="index">Current Check Attempt</td>
							<td class="value"><?php echo intval( $plugin['check_attempt'] ); ?></td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>Updater Data</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Fetch URL</td>
							<td class="value">
								<?php
								if ( '' !== $plugin['key'] ) {
									echo $api->server . '/manage/ajax/license/?token=' . $plugin['key'] . '&file=' . $plugin_name;
								} else {
									echo 'Key not set';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Is Fetched?</td>
							<td class="value"><?php echo file_exists( $api->json_loc . $plugin_name . '.json' ) ? 'yes' : 'no'; ?></td>
						</tr>
						<tr>
							<td class="index">Last Fetch</td>
							<td class="value">
								<?php
								if ( file_exists( $api->json_loc . $plugin_name . '.json' ) ) {
									$local_json = json_decode( $api->read_json() );
									echo $this->get_local_time( $local_json->created );
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Installed Version</td>
							<td class="value"><?php echo $plugin_info['Version']; ?></td>
						</tr>
						<tr>
							<td class="index">Latest Version on Local Data</td>
							<td class="value">
								<?php
								if ( isset( $local_json ) ) {
									echo $local_json->version;
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Latest Version on Server</td>
							<td class="value">
								<?php
								if ( '' !== $plugin['key'] ) {
									$remote_json = $api->update_json( $plugin['key'], true );
									if ( ! is_wp_error( $remote_json ) ) {
										$response = json_decode( wp_remote_retrieve_body( $remote_json ) );
										echo isset( $response->version ) ? $response->version : '-';
									} else {
										echo '-';
									}
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Local Data URL</td>
							<td class="value">
								<?php
								if ( file_exists( $api->json_loc . $plugin_name . '.json' ) ) {
									echo str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $api->json_loc . $plugin_name . '.json' );
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Local Data Value</td>
							<td class="value hide">
								<?php
								if ( isset( $local_json ) ) {
									echo '<button type="button" class="button toggle">Show/Hide</button>';
									echo '<pre>';
									print_r( $local_json );
									echo '</pre>';
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Remote Data Value</td>
							<td class="value hide">
								<?php
								if ( isset( $remote_json ) ) {
									if ( ! is_wp_error( $remote_json ) ) {
										$response = json_decode( wp_remote_retrieve_body( $remote_json ) );
										echo '<button type="button" class="button toggle">Show/Hide</button>';
										echo '<pre>';
										print_r( $response );
										echo '</pre>';
									} else {
										echo $response->get_error_message();
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
				<h4>Actions</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Delete Local License Data</td>
							<td class="value"><button type="button" class="button plugin-debug-action" data-action="delete-license">Delete</button></td>
						</tr>
					</tbody>
				</table>
			</li>
		</ul>
	</div>
</div>
<script>
	jQuery(function($) {
		$('.plugin-debug-action').on('click', function() {
			var action = $(this).data('action');
			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					action: 'tj_license_debug_<?php echo $plugin_name ?>',
					debug_action: action
				},
				context: this,
				beforeSend: function() {
					$(this).prop('disabled',true);
				},
				success: function(response) {
					if ( 'success' === response ) {
						location.reload();
					} else {
						$(this).prop('disabled',false);
						console.log( response );
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
