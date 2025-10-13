<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="wrap srm_styles" id="setup_page">
	<?=srm_Base::showVersion()?>
	 <h3><?=SRMA_GLOBAL_PLUGIN_NAME?> <?php _e('white labeling feature', 'srm'); ?></h3>
	 <hr class="wp-header-end">
     <div id="poststuff">
        <div class="pluginAction-heading">
            <p>White label Branding is the ability to rename and present plugin as your own. This helps you hide the actual identity of this plugin used and lets you use your brand name instead.</p>
            <p>Or you can skip this section if you don't want</p>
        </div>
					
        <div class="pluginAction-form ">
            <form id="sr_pluginactions" onsubmit="return false" method="post" class="whiteLabelForm">
                <div class="whiteLabelForm-col">
                    <div class="grp">
                        <label>Plugin Name:</label>
                        <input type="text" id="plugin_name" name="plugin_name" placeholder="Plugin Name" value="<?=get_option('SRM_plugin_name')?>" />
                    </div>
                    <div class="grp">
                        <label>Author:</label>
                        <input type="text" id="plugin_author" name="plugin_author" placeholder="Author"  value="<?=get_option('SRM_plugin_author')?>" />
                    </div>
                    <div class="grp">
                        <label>Author URI:</label>
                        <input type="text" id="plugin_author_URI" name="plugin_author_URI" placeholder="Author URI" value="<?=get_option('SRM_plugin_author_URI')?>" />
                    </div>
                    <div class="enableCheckBox"> 
                        <div class="grp enableCheck  enableCheck1" style="flex-direction: row; align-items: center">
                            <input type="checkbox" name="enable_white_label" id="enable_white_label" />
                            <label for="enable_white_label">Enable white label</label>
                        </div>
                        <div class="grp enableCheck" style="flex-direction: row; align-items: center"> 
                            <input type="checkbox" name="soft_whitelebal_activation" id="soft_whitelebal_activation" value="1" />
                            <label for="soft_whitelebal_activation">Soft whitelebal activation</label>
                        </div>
                    </div>
                    <div class="grp SRmt-20">
                        <input type="submit" name="submitPlugin" class="submitbtn whiteLbtn"/>
                    </div>
                    
                </div> 
                <div class="whiteLabelForm-col">
                    <div class="grp whiteLabelForm-decrip">
                        <div><label>Description:</label></div>
                        <textarea  id="plugin_description" name="plugin_description" placeholder="Description" style="width: 100%;height: 115px;"><?=get_option('SRM_plugin_description')?></textarea>
                    </div>
                    <div class="grp">
                        <label>Plugin Icon URL:</label>
                        <input id="plugin_icon_URI" type="text" name="plugin_icon_URI" placeholder="Plugin Icon URL"  />
                    </div>

                   
                    </div>
                   
                </div>
               
            </form>		
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        jQuery('.js-example-basic-multiple').select2();
});
</script>