<?php
	/**
	* This file is a template file of plugin setup and add whilte label
	*
	*/
?>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.js"></script>

<div class="wrap srm_styles ServerInfo" id="setup_page">
	<?=srm_Base::showVersion()?>
<section class="SR-step-container">
        <div class="container">
                    <div class="wizard ServerInfoTabs ">
                        <div class="wizard-inner">
                            <!-- <div class="connecting-line"></div> -->
                            <ul class="nav SR-step-list" role="tablist">
                                <li id="step1" role="presentation" class="SR-step-active-done"> 
                                    <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab"
                                        aria-expanded="true"><span class="round-tab">1 </span> <i>Step 1</i></a>
                                </li>
                                <li id="step2"  role="presentation" class="SR-step-active">
                                    <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab"
                                        aria-expanded="false"><span class="round-tab">2</span> <i>Step 2</i></a>
                                </li>
								<li id="step3" role="presentation" class="disabled">
                                    <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab"><span
                                            class="round-tab">3</span> <i>Step 3</i></a>
                                </li>
                                <li id="step4" role="presentation" class="disabled">
                                    <a href="#step4" data-toggle="tab" aria-controls="step3" role="tab"><span
                                            class="round-tab">4</span> <i>complete</i></a>
                                </li>
                            </ul>
                       
                </div>
            </div>
        </div>
    </section>
	
	 <h3><?php _e('Server Info', 'srm'); ?></h3>
	 <hr class="wp-header-end">
	  <div id="poststuff">
		 <div id="post-body" class="metabox-holder pluginAction">
				 <div class="main-wrap">
					<div class="pluginAction-heading">
					</div>
					<div class="section-wrp ServerInfoTable">	
					<div class="srm-table pluginAction-serverInfo">
						<h4 class="headingBg ">Server Info</h4>
						<table class="srmtb">
							<tr>
								<th class="ServerInfoTable-th1">Info</th>
								<th class="ServerInfoTable-th2">Recommended</th>
								<th class="ServerInfoTable-th2">Installed</th>
								<th class="ServerInfoTable-th3">Status</th>
							</tr>
							<tr>
								<td>PHP version</td>
								<td>^7.3|^8.0</td>
								<td><?=$phpVersion?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>Allow url fopen</td>
								<td>On</td>
								<td><?=$allow_url_fopen?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>BZip2 Support</td>
								<td>Enabled</td>
								<td><?=$bz2Zip?></td>
								<td><span>&#10003;</span></td>
							</tr><tr>
								<td>File uploads</td>
								<td>On</td>
								<td><?=$file_uploads?></td>
								<td><span>&#10003;</span></td>
							</tr><tr>
								<td>Max execution time</td>
								<td>^1500</td>
								<td><?=$max_execution_time?></td>
								<td><span>&#10003;</span></td>
							</tr><tr>
								<td>Max input time</td>
								<td>^1500</td>
								<td><?=$max_input_time?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>Max input vars</td>
								<td>^1800</td>
								<td><?=$max_input_vars?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>Post max size</td>
								<td>^1024M</td>
								<td><?=$post_max_size?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>cURL support</td>
								<td>Enabled</td>
								<td><?=$cURLsupport?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>SSL Version</td>
								<td>OpenSSL/1.1.1p</td>
								<td><?=$SSLVersion?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>json support</td>
								<td>Yes</td>
								<td><?=$jsonsupport?></td>
								<td><span>&#10003;</span></td>
							</tr>
							<tr>
								<td>mbstring</td>
								<td>Enabled</td>
								<td><?=$mbstring?></td>
								<td><span>&#10003;</span></td>
							</tr><tr>
								<td>OpenSSL support</td>
								<td>Enabled</td>
								<td><?=$opensslsupport?></td>
								<td><span>&#10003;</span></td>
							</tr><tr>
								<td>Zip support</td>
								<td>Enabled</td>
								<td><?=$Zip?></td>
								<td><span>&#10003;</span></td>
							</tr>
						</table>
					</div>
						<a class="ServerInfoTable-btn" href="javascript:void(0)" id="nextsetp">Next</a>	
					</div>
					

					<section class="nextstp question step-content circle-loader active" style="display:none">
						<div class="step-heading text-center"><a href="#debtFinderForm" class="back_step"></a></div>
							<div class="step-form">
								<div class="row">
									<div class="col-sm-12 text-center">
										<div class="flex-wrapper">
											<div class="single-chart">
												<svg id="circleProgressSvg" viewBox="0 0 36 36" class="circular-chart grey">
													<path class="circle-bg"
													d="M18 2.0845
													a 15.9155 15.9155 0 0 1 0 31.831
													a 15.9155 15.9155 0 0 1 0 -31.831"/>
													<path id="circleProgress" class="circle"
													stroke-dasharray="100, 100"
													d="M18 2.0845
													a 15.9155 15.9155 0 0 1 0 31.831
													a 15.9155 15.9155 0 0 1 0 -31.831"/>
													<text id="percentageCounter" x="18" y="20.35" class="percentage">0%</text>
												</svg>
											</div>
										</div>
										<ul class="load_steps p-0">
											<li id="stp-1" class="step active">Analysing your results</li>
											<li id="stp-2" class="step">Checking your server info</li>
											<li id="stp-3" class="step">All Done</li>  
										</ul>
									</div>
								</div>
								    
							</div>
							
						</section>

						<section class="sucesquestion step-content" style="display:none">
							<div class="step-heading ">
								<img src="https://s3.us-east-2.wasabisys.com/srmehranclub/2023/05/5610944.png" /> 
								  <p> Your plugin has been </p>
								  <span>sucessfully setup</span>  
							</div>
						</section>

						
				 </div>
		 </div>
	  </div>
</div>
<script>
// jQuery.validator.setDefaults({
//   debug: true,
//   success: "valid"
// });

$( "#sr_pluginactions" ).validate({
  rules: {
    plugin_name: {required: true},
    plugin_description: {required: true},
    plugin_author: {required: true},
    plugin_author_URI: {required: true,url: true},
  }
});
</script>