<?php

/**
 * Setup controller
 */
class Srm_Setup
{
	public static function init(){
		$file_path=SRM_PLUGIN_DIR.'/srmehranclub_automatic_update.php';
		$plugin_data = get_plugin_data($file_path);

		if(get_option('SRM_plugin_name') && get_option('SRM_plugin_description') && get_option('SRM_plugin_author') && get_option('SRM_plugin_author_URI')){
			
			 $PluginName=(get_option('SRM_plugin_name'))?get_option('SRM_plugin_name'):'';
			$PluginDescription=(get_option('SRM_plugin_description'))?strip_tags(get_option('SRM_plugin_description')):'';
			$PluginAuthor=(get_option('SRM_plugin_author'))?get_option('SRM_plugin_author'):'';
			$PluginAuthorURI=(get_option('SRM_plugin_author_URI'))?get_option('SRM_plugin_author_URI'):'';
		}else{
			$PluginName=($plugin_data['Name'])?$plugin_data['Name']:'';
			$PluginDescription=($plugin_data['Description'])?strip_tags($plugin_data['Description']):'';
			$PluginAuthor=($plugin_data['AuthorName'])?$plugin_data['AuthorName']:'';
			$PluginAuthorURI=($plugin_data['AuthorURI'])?$plugin_data['AuthorURI']:'';
		}
		


		$serverInfo=self::parsePHPModules();
		$phpVersion="";
		$allow_url_fopen="off";
		$bz2Zip="off";
		$file_uploads="off";
		$max_execution_time="off";
		$max_input_time="off";
		$max_input_vars="off";
		$cURLsupport="off";
		$SSLVersion="off";
		$jsonsupport="off";
		$mbstring="off";
		$opensslsupport="off";
			if(isset($serverInfo['Core']['PHP Version'])){
				$phpVersion=$serverInfo['Core']['PHP Version'];
			}
			if(isset($serverInfo['Core']['allow_url_fopen'][0])){
				$allow_url_fopen=$serverInfo['Core']['allow_url_fopen'][0];
			}
			if(isset($serverInfo['bz2']['BZip2 Support'])){
				$bz2Zip=$serverInfo['bz2']['BZip2 Support'];
			}
			if(isset($serverInfo['Core']['file_uploads'][0])){
				$file_uploads=$serverInfo['Core']['file_uploads'][0];
			}
			if(isset($serverInfo['Core']['max_execution_time'][0])){
				$max_execution_time=$serverInfo['Core']['max_execution_time'][0];
			}
			if(isset($serverInfo['Core']['max_input_time'][0])){
				$max_input_time=$serverInfo['Core']['max_input_time'][0];
			}
			if(isset($serverInfo['Core']['max_input_vars'][0])){
				$max_input_vars=$serverInfo['Core']['max_input_vars'][0];
			} 
			if(isset($serverInfo['Core']['post_max_size'][0])){
				$post_max_size=$serverInfo['Core']['post_max_size'][0];
			}
			if(isset($serverInfo['curl']['cURL support'])){
				$cURLsupport=$serverInfo['curl']['cURL support'];
			}
			if(isset($serverInfo['curl']['SSL Version'])){
				$SSLVersion=$serverInfo['curl']['SSL Version'];
			}
			if(isset($serverInfo['json']['json support'])){
				$jsonsupport=$serverInfo['curl']['SSL Version'];
			}
			if(isset($serverInfo['mbstring']['Multibyte Support'])){
				$mbstring=$serverInfo['mbstring']['Multibyte Support'];
			}
			
			if(isset($serverInfo['openssl']['OpenSSL support'])){
				$opensslsupport=$serverInfo['openssl']['OpenSSL support'];
			}
			if(isset($serverInfo['zip']['Zip'])){
				$Zip=$serverInfo['zip']['Zip'];
			}else{
				$Zip=false;
			}

			

			if(isset($_POST['submitPlugin'])){
				update_option('SRM_plugin_name', $_POST['plugin_name']);  
				update_option('SRM_plugin_description', $_POST['plugin_description']);  
				update_option('SRM_plugin_author', $_POST['plugin_author']);  
				update_option('SRM_plugin_author_URI', $_POST['plugin_author_URI']);  
				update_option('SRM_isSetupDone',true); 
				?>
				<script type="text/javascript">
					window.location = 'admin.php?page=sr-automatic-upgrade';
					</script>
				<?php 
			}
			
				include SRM_PLUGIN_DIR . '/templates/Srm_setup.php';

    }


	
	public static function isWhiteLabel(){
		$SRM_is_white_label = get_option('SRM_isSetupDone');
		if($SRM_is_white_label){
			return true;
		}
		return false;
	}
	
	/** parse php modules from phpinfo */

public static function parsePHPModules() {

	 ob_start();

	 phpinfo(INFO_MODULES);

	 $s = ob_get_contents();

	 ob_end_clean();

	 

	 $s = strip_tags($s,'<h2><th><td>');

	 $s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);

	 $s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);

	 $vTmp = preg_split('/(<h2>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);

	 $vModules = array();

	 for ($i=1;$i<count($vTmp);$i++) {

	  if (preg_match('/<h2>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {

	   $vName = trim($vMat[1]);

	   $vTmp2 = explode("\n",$vTmp[$i+1]);

	   foreach ($vTmp2 AS $vOne) {

		$vPat = '<info>([^<]+)<\/info>';

		$vPat3 = "/$vPat\s*$vPat\s*$vPat/";

		$vPat2 = "/$vPat\s*$vPat/";

		if (preg_match($vPat3,$vOne,$vMat)) { // 3cols

		 $vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3])); 

		} elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols

		 $vModules[$vName][trim($vMat[1])] = trim($vMat[2]); 

		} 

	   } 

	  } 

	 } 

	 return $vModules;

	}
}