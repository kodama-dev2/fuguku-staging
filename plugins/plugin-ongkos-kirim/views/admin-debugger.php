<div class="pok-debugger">
	<div class="debugger-setting debugger-setting-status">
		<h4><?php esc_html_e( 'Status', 'pok' ); ?></h4>
		<div class="setting-content">
			<table>
				<tbody>
					<tr>
						<td class="index"><?php esc_html_e( 'License status', 'pok' ); ?></td>
						<td class="separator">:</td>
						<td class="value"><?php echo $this->helper->is_license_active() ? '<span class="yes">ACTIVE</span>' : '<span class="no">INACTIVE</span>'; ?></td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'Selected API', 'pok' ); ?></td>
						<td class="separator">:</td>
						<td class="value"><span><?php echo 'nusantara' === $this->setting->get( 'base_api' ) ? 'TONJOO' : 'RAJAONGKIR'; ?></span></td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'API Status', 'pok' ); ?></td>
						<td class="separator">:</td>
						<td class="value">
							<?php
							$status = $this->helper->get_api_status();
							if ( true === $status ) {
								echo '<span class="yes">OK</span>';
							} else {
								echo '<span class="no">NOT OK</span> <span class="info">' . esc_html( $status ) . '</span>';
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="debugger-setting debugger-setting-setting">
		<h4><?php esc_html_e( 'Tools', 'pok' ); ?></h4>
		<div class="setting-content">
			<table>
				<tbody>
					<?php if ( 'nusantara' === $this->setting->get( 'base_api' ) ) : ?>
						<tr>
							<td class="index"><?php esc_html_e( 'Simulate Disable API Tonjoo', 'pok' ); ?></td>
							<td class="value">
								<?php if ( $this->helper->is_license_active() ) : ?>
									<?php if ( 'yes' === $this->setting->get( 'temp_disable_api_tonjoo' ) ) : ?>
										<button class="button" type="button" id="enable-api-tonjoo"><?php esc_html_e( 'Re-Enable API', 'pok' ); ?></button>
									<?php else : ?>
										<button class="button" type="button" id="disable-api-tonjoo"><?php esc_html_e( 'Disable API', 'pok' ); ?></button>
									<?php endif; ?>
								<?php else : ?>
									<?php esc_html_e( 'Your license for Plugin Ongkos Kirim is not activated, please activate it on the License page.', 'pok' ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php elseif ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) : ?>
						<tr>
							<td class="index"><?php esc_html_e( 'Simulate Disable API Rajaongkir', 'pok' ); ?></td>
							<td class="value">
								<?php if ( $this->helper->is_rajaongkir_active() ) : ?>
									<?php if ( 'yes' === $this->setting->get( 'temp_disable_api_rajaongkir' ) ) : ?>
										<button class="button" type="button" id="enable-api-rajaongkir"><?php esc_html_e( 'Re-Enable API', 'pok' ); ?></button>
									<?php else : ?>
										<button class="button" type="button" id="disable-api-rajaongkir"><?php esc_html_e( 'Disable API', 'pok' ); ?></button>
									<?php endif; ?>
								<?php else : ?>
									<?php esc_html_e( 'Your Rajaongkir API is currently inactive, please activate it on the setting tab.', 'pok' ); ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td class="index"><?php esc_html_e( 'Check My IP', 'pok' ); ?></td>
						<td class="value">
							<button class="button" type="button" id="check-ip"><?php esc_html_e( 'Check My IP', 'pok' ); ?></button><br>
							<div class="ip-result"></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="debugger-setting debugger-setting-logs">
		<h4><?php esc_html_e( 'API Error logs', 'pok' ); ?></h4>
		<div class="setting-content">
			<div class="logs">
				<?php
				if ( $is_writable && ! $error ) {
					if ( ! empty( $logs ) ) {
						?>
						<table>
							<tbody>
								<?php
								foreach ( $logs as $l ) {
									$date = array();
									preg_match( '/\[([\d-]*) ([\d:]*)\]/', $l, $date );
									$context = array();
									preg_match( '/\(Error[^\]]*\)/', $l, $context );
									?>
									<tr>
										<td class="date"><?php echo isset( $date[0] ) ? esc_html( str_replace( array( '[', ']' ), '', $date[0] ) ) : ''; ?></td>
										<td class="message"><?php echo esc_html( preg_replace( array( '/\[([\d-]*) ([\d:]*)\] /', '/\(Error[^\]]*\) /' ), '', $l ) ); ?></td>
										<td class="context"><?php echo isset( $context[0] ) ? esc_html( str_replace( array( '(Error ', ')' ), '', $context[0] ) ) : '-'; ?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
						<?php
					} else {
						?>
						<p class="notice"><?php esc_html_e( 'No logs to display.', 'pok' ); ?></p>
						<?php
					}
				} else {
					echo '<p class="notice">';
					printf( __( "The error logs can't be displayed because we can't access the file at %s. Consider to check your file permission and make sure that directory is writable.", 'pok' ), '<strong>' . $log->file . '</strong>' );
					echo '</p>';
				}
				?>
			</div>
		</div>
	</div>
</div>
