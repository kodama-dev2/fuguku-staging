<?php

/**
 * Files controller
 */
class Srm_Files
{	
	 /**
     * Register services
     */
    public static function register(){
		
	}
	
	public static function init(){
		
		global $wpdb;
		$total_bkp=Srm_Dashboard::getBackupCount();
		$backups = $wpdb->get_results('SELECT * FROM `'.$wpdb->prefix.'srclubplugins_history` WHERE `action`="backup" ORDER BY date DESC', ARRAY_A);
		$new_bkp = array();

				if (!empty($backups)) {
					foreach ($backups as $each_backups) {
						//Add it
						$new_bkp[$each_backups['product_name']][$each_backups['version']] = $each_backups;
					}
				}

				$backups_list = array();

				foreach ($new_bkp as $flat_arr_elem) {
					$current = 0;
					foreach ($flat_arr_elem as $inner) {
						if ($current < $total_bkp)
							array_push($backups_list, $inner);
						$current++;
					}
				}

				$backups = $backups_list;

				if(!empty($_POST['delete_id'])){
					$id = (int)$_POST['delete_id'];
					if($ret = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'srclubplugins_history WHERE `id`="'.$id.'"',ARRAY_A)){
						$wpdb->delete($wpdb->prefix.'srclubplugins_history', array('id' => $id));
						@unlink($ret['path']);
						
					}
				}
		
				if(!empty($_POST['restore_id'])){
					$id = (int)$_POST['restore_id'];
					self::restoreId($id);
						
				}

		include SRM_PLUGIN_DIR . '/templates/Srm_files.php';
	}


	public static function restoreId($productId){
		global $wpdb;
					
		 $sql='SELECT * FROM `'.$wpdb->prefix.'srclubplugins_history` WHERE `action`="backup" AND id="'.$productId.'" ORDER BY id DESC';
		$result = $wpdb->get_results($sql, ARRAY_A);
		$ret = array();
		
		$version_old = 0;	
		foreach ($result as $eachres) {
			$new_version = str_replace('.', '', $eachres['version']);
			if ($new_version > str_replace('.', '', $version_old)) {
				$ret = $eachres;
			}
		}
		if(!empty($productId)){
			$isPlugin = ($ret['type'] == 'plugin');

			$dir = Srm_Base::getBackupDirectory();
			$pluginRootDir = get_home_path().'wp-content/'.(($isPlugin) ? 'plugins' : 'themes').'/';
			if(is_dir($dir.'temp/')){
				Srm_Dashboard::rrmdir ($dir.'temp/');
		
				//self::deleteDir($dir.'temp/');
			}

			$mkdir = mkdir($dir.'temp/');
			
			$zip = new ZipArchive; 
			$zip->open($ret['path']); 
			$zip->extractTo($dir.'temp/'); 
			$path = "";
			

			for ($i = 0; $i < $zip->numFiles; $i++) {
				$filename = basename($zip->getNameIndex($i));

				if (preg_match('/^installable/', $filename)) {
					$dir_content = file_get_contents($dir.'temp/'.$zip->getNameIndex($i));
					
					if (!preg_match('/^\//', $dir_content))
						$dir_content .= '/';

					$path = $dir.'temp'.$dir_content;

					if (!file_exists($path)) {
						$pluginDirectory = scandir($dir.'temp/');
						foreach ($pluginDirectory as $key => $value) {
							if ($value != "." && $value != "..") {
								if (file_exists($dir.'temp/'.$value.$dir_content)) {
									$path = $dir.'temp/'.$value.$dir_content;
								}
							}
						}
					}
					
					if (file_exists($path)) {
						// Copy folder to tmp and remove unnecessary folder
						// Find Style
						if (!preg_match('/\/$/', $path))
							$path = $path.'/';
					}
				}
		   }
				$zip->close();

					$pluginDirectory = @reset(array_diff(scandir($dir.'temp/'), array('.', '..')));
					if ($path != "") {
						$pluginDirectory = preg_replace('/^(.*?)temp\//', '', $path);
					}
					if (strlen($pluginDirectory) > 3) {
						//rrmdir($pluginRootDir.$pluginDirectory);
						
					
						preg_match('/[\/]\w+[\/]?$/', $pluginDirectory, $dest);
						$dest = $pluginDirectory.$dest[0];
						$dest = preg_replace('/^\//', '', $dest);
						
						$source = preg_replace("/\/$/", "", $dir.'temp/'.$pluginDirectory);
						$destination = preg_replace("/\/$/", "", $pluginRootDir.$pluginDirectory);
						$destination = preg_replace('/wp-content\/themes\/(.*?)\//', 'wp-content/themes/', $destination);
						Srm_Dashboard::rcopy($source, $destination);
					
					}

					
					
					Srm_Dashboard::rrmdir($dir.'temp/');
					echo "".(($isPlugin) ? 'Plugin' : 'Theme')." Installed!";

			
		}else{
			echo 'Error';
			}
	

	}
	 
	 
}