<?php
namespace YayExtra;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'YayExtra\\YayeDeactiveLiteNotice' ) ) { 
	add_action( 'network_admin_notices', 'YayExtra\\YayeDeactiveLiteNotice' );
	add_action( 'admin_notices', 'YayExtra\\YayeDeactiveLiteNotice' );
	function YayeDeactiveLiteNotice() {
		if ( current_user_can( 'activate_plugins' ) ) {
			?>
		<div class="notice notice-info is-dismissible" style="border-left-color: #007cba;">
		<p>
			<strong><?php esc_html_e( 'You have activated the YayExtra Pro version, so the YayExtra Lite version has been automatically deactivated. All settings have been saved.', 'yayextra' ); ?>
			</strong>
		</p>
		</div>
			<?php
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
}