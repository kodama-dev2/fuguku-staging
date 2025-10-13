<div class="wrap srm_styles srBackup srActivites Sr-pagination" id="setup_page">
	<?=srm_Base::showVersion()?>
	 <h3><?=SRMA_GLOBAL_PLUGIN_NAME?> <?php _e('Manager', 'srm'); ?></h3>
	 <hr class="wp-header-end">
     
    <div class="postbox ilj-postbox admin-headline">
        <h2 class='srBg-primary'><span class="dashicons dashicons-database-import"></span>Activities!</h2>      
  
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
						<?php
						$activitiesTab_obj->prepare_items();
						$activitiesTab_obj->display(); ?>
					</form>
        		</div>
    </div>

</div>