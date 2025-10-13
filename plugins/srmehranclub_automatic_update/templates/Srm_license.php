<?php
	/**
	* This file is a template file of license checking page
	*
	*/

    // get the license status data
    $license    = get_option(SRM_OPTION_LICENSE_KEY, '');
    $status     = get_option(SRM_OPTION_LICENSE_STATUS);
    $last_error = get_option(SRM_OPTION_LICENSE_LAST_ERROR, '');

    // get the current licensing state
    $licensing_state;
    if(empty($license) && empty($last_error) || ('invalid' === $status && 'Deactivated manually' === $last_error)){
        $licensing_state = 'not_activated';
    }elseif(!empty($license) && 'valid' === $status){
        $licensing_state = 'activated';
    }else{
        $licensing_state = 'error';
    }

    // create titles for the license statuses
    $status_titles   = array(
        'not_activated' => __('License Not Active', 'srm'),
        'activated'     => __('License Active', 'srm'),
        'error'         => __('License Error', 'srm')
    );

    // create some helpful text to tell the user what's going on
    $status_messages = array(
        'not_activated' => __('Please enter your '.SRMA_GLOBAL_PLUGIN_NAME.' License Key to activate Internal Link Master.', 'srm'),
        'activated'     => __('Congratulations! Your '.SRMA_GLOBAL_PLUGIN_NAME.' License Key has been confirmed and '.SRMA_GLOBAL_PLUGIN_NAME.' is now active!', 'srm'),
        'error'         => $last_error
    );
?>
<div class="wrap srm_styles" id="licensing_page">
    <?=srm_Base::showVersion()?>
    
    <hr class="wp-header-end">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
           
			<div class="license-main-section">
				<div id='license_container'>
					<div class="main-wrap">
						<?php 
						
							$srm_license_key_status = get_option('srm_license_key_status');
							$show_activated = false;
							if($srm_license_key_status == 'Activated'){
								$show_activated = true;
							}
												
						?>
						<div class="license-page-child-section">
					
						<div id="license_page" class='license-page-cls'>
							<div class="license-page-main-div">
								<div class="license-page-mainHeader">
									<h3 class="license-page-cls-title srTitle">
										<?php _e('Activate Your Licenses', 'srm'); ?>
									</h3>
									<div>
										<?php if($show_activated): ?>							
											<div class="srm_license_status">
												<span class='status'>
													Status: <?php echo get_option('srm_license_key_status');?>
												</span>
											</div>
										<?php endif ?>
									</div>
								</div>
								<form action="" method="post" id="srm_license_page_form" class="common-license-form">
									<div class="common-license-formImg">
										<img src="<?php echo SRM_PLUGIN_URL.'/assets/img/aa.png'?>" />
									</div>
									<div class="common-license-formBox">
										<div class='message'><span class='msg'></span></div>
										
										<div class="form-field-text">
											<p>To create your API and secret API key, please follow the link provided below:</p>
											<p><a href="https://srmehranclub.com/my-account/our-license-manager" target="_blank">Click here</a></p>
										</div>
										<div class="form-field-cls">
											<label><?php echo esc_html( 'License Key','srm' );?></label>
											<input type='text' name='srm_license_key' class="txt-box" id='srm_license_key' minlength="6" value="<?php echo get_option('SRM_license_key')?>" placeholder='<?php echo esc_html( 'Enter License Key','srm' );?>' required>
										</div>
										<div class="form-btn-cls">
											<?php if(!$show_activated){?>	
											<div class="input-group-loader">
												<input type='button' id='' class="activate-license-btn activate-license-btn-cls" value='<?php echo esc_html( 'Activate','srm' );?>'></input>
												<div class='loader' style="display:none;">
													<img class="loader1" src="<?php echo SRM_PLUGIN_URL.'/assets/img/loader.gif';?>" alt="loader" >
												</div>
											</div>
											<?php }?>
											<?php if($show_activated): ?>
											<div class="input-group-loader">
												<input type='button' id='' class="deactivate-license-btn" value='<?php echo esc_html( 'Deactivate','srm' );?>'></input>	
												<div class='loader2' style="display:none;">
													<img class="loader1" src="<?php echo SRM_PLUGIN_URL.'/assets/img/loader.gif';?>" alt="loader" >
												</div>
											</div>
											<?php endif ?>
										</div>	
										
									</div>
								</form>
								<!-- <div class="">
									<img src="https://srmehranclub.com/wp-content/uploads/2020/10/24seven-right.png " />
								</div>						 -->
								
							</div>
						</div>	
						</div>
					</div>
					</div>
				</div>

        </div>
    </div>
</div>
<!-- <div class="license-rightImg">
	<img src="https://srmehranclub.com/wp-content/uploads/2020/10/24seven-right.png ">
</div>
<div class="license-leftImg">
	<img src="https://srmehranclub.com/wp-content/uploads/2020/10/24seven-right.png ">
</div> -->

<?php
