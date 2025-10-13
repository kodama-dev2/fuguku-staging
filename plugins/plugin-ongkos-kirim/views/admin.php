<div class="wrap pok-wrapper">
	<h1 class="wp-heading-inline"></h1>
	<div class="pok-admin-header">
		<div class="logo">
			<img src="<?php echo POK_PLUGIN_URL . '/assets/img/logo.png' ?>" alt="Plugin Ongkos Kirim">
		</div>
		<nav>
			<?php foreach ( $tabs as $key => $value ) : ?>
				<?php $url = 'setting' === $key ? admin_url( 'admin.php?page=pok_setting' ) : admin_url( 'admin.php?page=pok_setting&tab=' . $key ); ?>
				<a href="<?php echo esc_url( $url ); ?>" class="nav-tab <?php echo $tab === $key ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $value['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
	<div class="pok-setting-content">
		<?php
		if ( isset( $tabs[ $tab ]['callback'] ) ) {
			call_user_func( $tabs[ $tab ]['callback'] );
		}
		?>
	</div>
</div>
