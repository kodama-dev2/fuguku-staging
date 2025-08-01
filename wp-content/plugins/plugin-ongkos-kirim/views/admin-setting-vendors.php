<?php if ( defined('POKMV_VERSION') ) : ?>
	<div class="pok-setting-notice">
		<p><?php esc_html_e( 'You need to update your Multi Vendor Addon to the latest version to support current version of Plugin Ongkos Kirim.', 'pok' ) ?></p>
	</div>
<?php else: ?>
	<div class="pok-setting-notice">
		<p style="margin-bottom: 10px;"><?php esc_html_e( 'You must install Multi Vendor Addon to activate this feature. Multi vendor mode allows you to set multiple shipping origins intead of one.', 'pok' ) ?></p>
		<a href="https://tonjoostudio.com/product/woo-ongkir-multi-vendor/" target="_blank" class="button" style="margin-bottom: 5px;"><?php esc_html_e( 'Buy Addon Now', 'pok' ) ?></a>
	</div>
<?php endif; ?>