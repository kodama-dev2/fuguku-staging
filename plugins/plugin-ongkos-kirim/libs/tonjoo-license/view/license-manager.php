<style>
	#tonjoo-license-manager	{
		padding-top: 10px;
		min-width: 763px;
	}
	.license-wrapper {
		margin-right: 300px;
	}
	.license-content {
		width: 100%;
		min-width: 463px;
		float: left;
	}
	.license-sidebar {
		float: right;
		margin-right: -300px;
		width: 280px;
	}
	.postbox .hndle {
		margin: 0;
		padding: .5em 15px;
	}
	.postbox .form td {
		padding: 10px 0;
		vertical-align: top;
	}
	.postbox .form .title {
		padding-right: 60px;
	}
	.postbox .form .license-status {
		margin: 0;
		display: inline-block;
		color: #fff;
		padding: 2px 7px 2px 2px;
		border-radius: 4px;
	}
	.postbox .form .license-status.stat-error {
		background-color: #ca4a1f;
	}
	.postbox .form .license-status.stat-success {
		background-color: #46b450;
	}
	.postbox .form .license-status span {
		display: inline-block;
		vertical-align: top;
		line-height: 20px;
	}
	.postbox .form .license-status .dashicons {
		line-height: 22px;
	}
	.postbox .form .license-field {
	}
	.postbox .form .license-field .input-key {
		width: 300px;
	}
	.postbox .form .license-field .status {
		padding-top: 5px;
		min-height: 23px;
	}
	.postbox .form .license-field .status.stat-error {
		color: #ca4a1f;
	}
	.postbox .form .license-field .status.stat-success {
		color: #46b450;
	}
	.license-sidebar .item {
		border-bottom: 1px solid #eee;
	}
	.license-sidebar .item {
		padding: 10px;
	}
	.license-sidebar .item p {
		margin: 0;
		margin-bottom: 5px;
	}
	.license-sidebar h3 span {
		margin-top: 3px;
		margin-left: -4px;
	}
	#tj-debug .debug-value {
		padding: 0 3px;
	}
	#tj-debug .debug-value li {
		margin-bottom: 15px;
	}
	#tj-debug .debug-value h4 {
		margin: 0 0 5px;
		font-size: 14px;
	}
	#tj-debug .debug-value table {
		background-color: #f8f8f8;
		width: 100%;
		border: 1px solid #ddd;
		border-collapse: collapse;
	}
	#tj-debug .debug-value table td {
		border: 1px solid #ddd;
		padding: 5px 10px;
		vertical-align: top;
	}
	#tj-debug .debug-value table td.index {
		width: 200px;
		font-weight: 600;
	}
	#tj-debug .debug-value table td.value pre {
		white-space: pre-wrap;
	}
	#tj-debug .debug-value table td.value.hide pre {
		display: none;
	}
	#tj-debug .logs {
		width: 100%;
		height: 200px;
		overflow-y: auto;
		background-color: #f0f0f0;
		font-family: monospace;
		box-shadow: 0 1px 1px rgba(0,0,0,.04);
		border: 1px solid #ddd;
		padding: 5px;
		box-sizing:border-box;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
	}
	.upsell-separator {
		padding-top: 20px;
		position: relative;
		clear: both;
	}
	.upsell-separator h3 {
		color: #444;
		font-size: 20px;
		margin: 0;
		margin-bottom: 15px;
	}
</style>
<div class="wrap">
	<h1 class="wp-heading-inline">Tonjoo License Manager</h1>
	<hr class="wp-header-end">
	<div id="tonjoo-license-manager">
		<div class="license-wrapper">
			<div class="license-content">
				<?php
				if ( isset( $_GET['debug'] ) ) {
					$logs = new TJ_Logs( 'tonjoo-plugins' );
					include 'general-debugger.php';
				}
				?>
				<?php foreach ( $this->plugins as $plugin_name => $plugin ) : ?>
					<?php
						if ( empty( $plugin['path'] ) || ! is_plugin_active( plugin_basename( $plugin['path'] ) ) ) {
							continue;
						}
						$plugin_info 	= get_plugin_data( $plugin['path'] );
						$is_active 		= $plugin['active'];
						$is_expired 	= ( '-' !== $plugin['expiry'] && time() > $plugin['expiry'] && ! $plugin['active'] );
						$api 			= new Tonjoo_License_API( $plugin_name );
					?>
					<div class="meta-box">
						<div class="postbox">
							<h3 class="hndle"><?php echo esc_html( $plugin_info['Name'] ); ?> <?php echo esc_html( $plugin_info['Version'] ); ?></h3>
							<div class="inside" style="z-index:1;">

								<table class="form">
									<tr>
										<td class="title"><?php esc_html_e( 'License Status', 'pok' ); ?></td>
										<td class="field">
											<?php if ( $is_active ) : ?>
												<p class="license-status stat-success">
													<span class="dashicons dashicons-yes"></span>
													<?php if ( 'trial' === $plugin['type'] ) : ?>
														<span><?php esc_html_e( 'Trial Activated', 'pok' ); ?></span>
													<?php else : ?>
														<span><?php esc_html_e( 'Activated', 'pok' ); ?></span>
													<?php endif; ?>
												</p>
											<?php else : ?>
												<?php if ( $is_expired ) : ?>
													<p class="license-status stat-error">
														<span class="dashicons dashicons-no-alt"></span>
														<?php if ( 'trial' === $plugin['type'] ) : ?>
															<span><?php esc_html_e( 'Trial Expired', 'pok' ); ?></span>
														<?php else : ?>
															<span><?php esc_html_e( 'Expired', 'pok' ); ?></span>
														<?php endif; ?>
													</p>
												<?php else : ?>
													<p class="license-status stat-error">
														<span class="dashicons dashicons-no-alt"></span>
														<span><?php esc_html_e( 'Not Activated', 'pok' ); ?></span>
													</p>
												<?php endif; ?>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<td class="title"><?php esc_html_e( 'Your License Code', 'pok' ); ?></td>
										<td class="field">
											<div class="license-field">
												<div class="field">
													<input type="text" class="input-key" value="<?php echo esc_attr( $plugin['key'] ); ?>" <?php echo $is_active ? 'readonly' : ''; ?>>
													<input type="hidden" class="plugin-name" value="<?php echo esc_attr( $plugin_name ); ?>">
													<?php if ( $is_active ) : ?>
														<button data-context="deactivate" class="submit-key button-primary"><?php esc_html_e( 'Deactivate', 'pok' ); ?></button>
													<?php else : ?>
														<button data-context="activate" class="submit-key button-primary"><?php echo $is_expired ? __( 'Renew License', 'pok' ) : __( 'Check License', 'pok' ); ?></button>
													<?php endif; ?>
												</div>
												<div class="status <?php echo $is_active ? 'stat-success' : 'stat-error'; ?>">
													<?php
													if ( $is_active ) {
														printf( __( 'Valid until: %s', 'pok' ), $this->get_local_time( $plugin['expiry'] ) );
														if ( ( $plugin['expiry'] - time() ) < WEEK_IN_SECONDS && 'trial' !== $plugin['type'] ) {
															echo ' ';
															printf( __( '(expired soon, <a href="%s" target="_blank">click to renew</a>)', 'pok' ), 'https://tonjoostudio.com/manage/user/myItem/' );
														} elseif ( ( $plugin['expiry'] - time() ) < DAY_IN_SECONDS && 'trial' === $plugin['type'] ) {
															echo ' ';
															printf( __( '(expired soon, <a href="%s" target="_blank">click to renew</a>)', 'pok' ), 'https://tonjoostudio.com/manage/user/myItem/' );
														}
													} elseif ( $is_expired ) {
														printf( __( 'Expired at: %s', 'pok' ), $this->get_local_time( $plugin['expiry'] ) );
													} else {
														esc_html_e( 'Input your license key', 'pok' );
													}
													?>
												</div>
											</div>
										</td>
									</tr>
									<tr>
										<td class="title"><?php esc_html_e( 'Last Check' ,'pok' ); ?></td>
										<td class="field last-check">
											<?php echo esc_html( $this->get_local_time( $plugin['last_check'] ) ); ?>
										</td>
									</tr>
								</table>
								<?php
								if ( isset( $_GET['debug'] ) ) {
									include 'plugin-debugger.php';
								}
								?>
							</div>
						</div>
					</div>
				<?php endforeach ?>
				<div class="upsell-separator">
					<h3><?php esc_html_e( 'Have you tried our other cool stuffs?', 'pok' ) ?></h3>
				</div>
				<?php
					require_once __DIR__ . '/../../class-tonjoo-plugins-upsell.php';
					$upsell = new Tonjoo_Plugins_Upsell( 'wooongkir-premium' );
					$upsell->render();
				?>
			</div>
			<div class="license-sidebar">
				<div class="meta-box">
					<div class="postbox">
						<h3 class="hndle"><span class="dashicons dashicons-sos"></span> <?php echo esc_html_e( 'Help Center', 'pok' ); ?></h3>
						<div class="item">
							<p><?php esc_html_e( 'Where can I get my licenses?', 'pok' ) ?></p>
							<a href="https://tonjoostudio.com/account/items/" class="button" target="_blank"><?php echo esc_html_e( 'My Licenses', 'pok' ) ?></a>
						</div>
						<div class="item">
							<p><?php esc_html_e( 'I have a problem with my license', 'pok' ) ?></p>
							<a href="https://forum.tonjoostudio.com/" class="button" target="_blank"><?php echo esc_html_e( 'Support Forum', 'pok' ) ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(function($) {

		function setStatus( status, message, parent ) {
			if ( "error" === status ) {
				parent.find('.license-field .status').removeClass('stat-success').addClass('stat-error').html(message);
			} else {
				parent.find('.license-field .status').removeClass('stat-error').addClass('stat-success').html(message);
			}
		}
		$('button.submit-key').on('click', function() {
			var parent 	= $(this).parents('.postbox');
			var key 	= parent.find('input.input-key').val();
			var plugin  = parent.find('input.plugin-name').val();
			var context = $(this).data('context');
			if ( 'activate' === context ) {
				var data = {
					action: 'tj_activate_plugin_' + plugin,
					key: key,
					tj_license: '<?php echo esc_html( wp_create_nonce( 'tonjoo-activate-license' ) ); ?>'
				}
			} else {
				var data = {
					action: 'tj_deactivate_plugin_' + plugin,
					key: key,
					tj_license: '<?php echo esc_html( wp_create_nonce( 'tonjoo-deactivate-license' ) ); ?>'
				}
			}
			$.ajax({
				url: ajaxurl,
				dataType: 'json',
				type: 'post',
				data: data,
				context: this,
				beforeSend: function() {
					$(this).prop('disabled',true);
					parent.find('.license-field .status').removeClass('stat-success').addClass('stat-error').html('Waiting for response...');
				},
				success: function(response) {
					if ( response.status ) {
						location.reload();
					} else {
						setStatus( 'error', response.message, parent );
						parent.find('.last-check').html(response.last_check);
						$(this).prop('disabled',false);
					}
				},
				error: function(data) {
					$(this).prop('disabled',false);
					setStatus( 'error', 'Unknown error', parent );
				}
			});
		});

	});
</script>