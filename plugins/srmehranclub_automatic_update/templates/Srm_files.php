<div class="wrap srm_styles srBackup Sr-pagination" id="setup_page">
	<?=srm_Base::showVersion()?>
	 <h3><?=SRMA_GLOBAL_PLUGIN_NAME?> <?php _e('Manager', 'srm'); ?></h3>
	 <hr class="wp-header-end">
     
    <div class="postbox ilj-postbox admin-headline">
        <h2 class='srBg-primary'><span class="dashicons dashicons-database-import"></span>Backups!</h2>      
        <div class="inside">
                <div class="postbox ilj-postbox">
                <table class="wp-list-table widefat plugins"  style="width: calc( 100% - 16px )!important; border: 0;">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name column-primary">Plugin</th>
					<th scope="col" id="description" class="manage-column">Date</th>
					<th scope="col" id="description" class="manage-column">Version</th>
					<th scope="col" id="description" class="manage-column">Size</th>
					<th scope="col" id="description" class="manage-column"></th>
				</tr>
			</thead>
			<tbody id="the-list">
			<?php foreach($backups as $d){?>
				<?php $plugin_size = Srm_Base::formatSize(filesize(WP_CONTENT_DIR.'/'.$d['path'])); ?>
				<?php if (!preg_match('/bytes/i', $plugin_size)): ?>
					<tr>
						<td class="plugin-title column-primary">
							<div><b><?php echo $d['product_name'];?></b></div>
						</td>
						<td class="">
							<div>
							<?php echo $d['date'];?>
							</div>
						</td>
						<td class="">
							<div><?php echo $d['version'];?></div>
						</td>
						<td class="">
							<div><?php echo Srm_Base::formatSize(filesize(WP_CONTENT_DIR.'/'.$d['path']));?></div>
						</td>
						<td class="">
							<div class='srBackup-btns'>
								<span class="download">
									<a class="download" href="<?php echo content_url().'/'.$d['path'];?>"><i class="fa fa-arrow-down" aria-hidden="true"></i></a>
									|
									<a class="restore" data-id="<?php echo $d['id'];?>" data-name="<?php echo $d['product_name'];?>" href="javascript:void(0)"><i class="fa fa-history" aria-hidden="true"></i></a>
									|
									<a class="history-remove" data-id="<?php echo $d['id'];?>"><i class="fa fa-times" aria-hidden="true"></i></a>
								</span>
							</div>
						</td>

					</tr>
				<?php endif; ?>
			<?php } ?>
			</tbody>
		</table>
                </div>
        </div>
    </div>

</div>