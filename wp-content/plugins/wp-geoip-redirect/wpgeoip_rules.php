<?php

/**
 * Function to return 
 */
function wpgeoip_iso_countries() {

    $csv = array_map('str_getcsv', file( plugin_dir_path(__FILE__) . '/GeoLite2-Country/isoCountries.csv'));
    $countries = array(  );

    foreach( $csv as $entry ) {
        $countries[ $entry[0] ] = $entry[1];
    }

    return $countries;

}

/**
 * Function to manage rules into database
 */
function wpgeoip_admin()
{

    // include geoip class
    // require_once 'geoip.inc';

	global $wpdb; 
	
    // get country list for dropdown
    $countryList = wpgeoip_iso_countries();


    // remove rule
	if(isset($_GET['delID']))
    {
        $ruleID = intval($_GET['delID']);
        $wpdb->query("DELETE FROM `".$wpdb->prefix."grules` WHERE `ruleID` = $ruleID");
        print '<meta http-equiv="refresh" content="0;url=index.php?page=wpgeoip-admin" />';
        exit;
    }


    // get all plugin list
    $all_plugins = get_plugins();
    $_plugins = array();

    // functionality
    if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
        echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
        exit;
    }

    foreach($all_plugins as $plugin_name => $plugin_array) $_plugins[] = $plugin_name;

    $fl_array = preg_grep("/cache/i", $_plugins);

	?>
	<div id="wrap" class="wpgeoip-wrapper">
        <br />
        <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
		<h1 style="float:left;margin-top: 10px;margin-left:10px;">WP GeoIP Redirect Rules</h1>
	    <div style="clear:both;"></div>
        <hr />
	
        <div class="updated below-h2">
        <?php if(count($fl_array)) echo '<h3 style="color:#cc0000;">If you have any CACHING plugins ACTIVE this plugin will not work properly simply because it will also cache the 1st visitor location and assume everyone else is from the same country. Ignore this message if it\'s not the case.!</h3>'; ?>
    	<h2>QUICK NOTES!</h2><hr/> 
            - <strong>CATEGORY:</strong> If you choose a category <strong>all traffic</strong> for that specific category will be redirected.<br/>
    		- <strong>PAGE/POST:</strong> If you want to redirect <strong>a single POST</strong> LEAVE category as -None-<br/>
            - <strong>SITEWIDE:</strong> If you want to <strong>redirect</strong> no matter what category/page/post choose <strong>"SITEWIDE RULE"</strong><br />
            - <strong>WILDCARD:</strong> If you want to create a wildcard <strong>redirect</strong>. <br/>Widlcard Example: http://yoursite.com/page-A will redirect user to http://othersite.com/page-A. Use it for redirecting to OTHER domain otherwise you'll get a loop.
    	</p>
        </div>
    
    <h3>Add New Redirect Rule</h3>
    <hr />

    <div style="width: 440px;float:left;">
    <form method="post">
        <table>
            <tr>
            <td>Country:</td>
            <td>
            <select name="country" class="chosen-select">
                <option value="ALL">All Countries</option>
                <?php 
                foreach($countryList as $countryCode => $countryName) {
                    if($countryCode == 'AP') continue;
                    if($countryCode == 'EU') $countryName = 'European Union';
                    printf('<option value="%s">%s</option>', $countryCode, $countryName);
                }

                ?>    
            </select>
            </td>
            </tr>
            <tr>
            <td>For This Category:</td>
            <td><select name="catID" class="chosen-select">
                <option value="0">-None-</option>
                <?php
                $categories = get_categories();
				foreach($categories as $cat) {
                        print '<option value="'.$cat->cat_ID.'">'.$cat->name.'</option>';
                        print "\n";
                }
                ?>
                </select></td>
            </tr>
            <tr>
            <td>OR For This POST/PAGE</td>
            <td><select name="postID" class="chosen-select">
                <option value="0">-None-</option>
                <option value="999999">SITEWIDE RULE - ALL PAGES</option>
                <option value="home">!HOMEPAGE!</option>
                <option value="999990">WILDCARD</option>
                <?php
                $all_posts = get_posts('numberposts=1000&offset=0');
                if( $all_posts ) :
    				foreach($all_posts as $post) {
                            print '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
                            print "\n";
                    }
                endif;
				$all_pages = get_pages('numberposts=1000&offset=0');
                if( $all_pages ) :
    				foreach($all_pages as $post) {
                            print '<option value="'.$post->ID.'">'.$post->post_title.'</option>';
                            print "\n";
                    }
                endif;
                $all_products = get_posts('numberposts=1000&offset=0&post_type=product');
                if( $all_products ) :
                    foreach($all_products as $post) {
                            print '<option value="'.$post->ID.'">Product: '.$post->post_title.'</option>';
                            print "\n";
                    }
                endif;

                $all_products = get_posts('numberposts=1000&offset=0&post_type=download');
                if( $all_products ) :
                    foreach($all_products as $post) {
                            print '<option value="'.$post->ID.'">Download: '.$post->post_title.'</option>';
                            print "\n";
                    }
                endif;
                ?>
                </select></td>
            </tr>
            <tr>
                <td>Target URL:</td>
                <td><input type="text" name="target" value="http://www." style="width:250px;" /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="submit" name="sbRule" value="Add Rule" class="button"/></td>
            </tr>
        </table>
        </form>

        <?php
    if(isset($_POST['sbRule']))
    {

        $eu_countries = array
                        ( 
                            0 => 'Austria',
                            1 => 'Belgium',
                            2 => 'Bulgaria',
                            3 => 'Croatia',
                            4 => 'Cyprus',
                            5 => 'Czech Republic',
                            6 => 'Denmark',
                            7 => 'Estonia',
                            8 => 'Finland',
                            9 => 'France',
                            10 => 'Germany',
                            11 => 'Greece',
                            12 => 'Hungary',
                            13 => 'Ireland',
                            14 => 'Italy',
                            15 => 'Latvia',
                            16 => 'Lithuania',
                            17 => 'Luxembourg',
                            18 => 'Malta',
                            19 => 'Netherlands',
                            20 => 'Poland',
                            21 => 'Portugal',
                            22 => 'Romania',
                            23 => 'Slovakia',
                            24 => 'Slovenia',
                            25 => 'Spain',
                            26 => 'Sweden',
                            27 => 'United Kingdom'
                        );

        if($_POST['target'] != "http://www." && $_POST['target'] != "" && $_POST['country'] != "")
        {
                $country = esc_sql($_POST['country']);
                $target = esc_sql(trim($_POST['target']));
                $catID = intval($_POST['catID']);
                
                if($_POST['postID'] == 'home') {
                    $postID = 0;

                    // insert homepage rule
                    if($country == 'ALL') {

                        $values = array();
                        $place_holders = array();

                        $query = "INSERT INTO ".$wpdb->prefix."grules (`countryID`,`targetURL`,`catID`,`postID`,`home_rule`) VALUES ";

                        foreach($countryList as $countryCode => $countryName) {
                            if($countryCode == 'AP') continue;
                            array_push($values, $countryCode, $target, $catID, $postID, 1);
                            $place_holders[] = "('%s', '%s', '%d', '%d', '%d')";
                        }

                        $query .= implode(', ', $place_holders);
                        $wpdb->query( $wpdb->prepare("$query ", $values));

                    }else{
                        $rs = $wpdb->query("INSERT INTO `".$wpdb->prefix."grules` (`countryID`,`targetURL`,
                                `catID`,`postID`,`home_rule`) VALUES 
                                ('$country', '$target','$catID','$postID', '1')");
                    }
                    
                    
                }else{

                     // set post id to integer
                     $postID = intval($_POST['postID']);

                     // if country == European Union insert a rule for each of the 27 countries
                    if($country == 'EU') {

                        $reverse_countries = array_flip($countryList);
                        foreach($eu_countries as $country_name) {
                            $country = $reverse_countries[$country_name];

                            $rs = $wpdb->query("INSERT INTO `".$wpdb->prefix."grules` (`countryID`,`targetURL`,
                                    `catID`,`postID`) VALUES 
                                    ('$country', '$target','$catID','$postID')");
                        }

                    }elseif($country == 'ALL') {

                        $values = array();
                        $place_holders = array();

                        $query = "INSERT INTO ".$wpdb->prefix."grules (`countryID`,`targetURL`,`catID`,`postID`) VALUES ";

                        foreach($countryList as $countryCode => $countryName) {
                            if($countryCode == 'AP') continue;
                            array_push($values, $countryCode, $target, $catID, $postID);
                            $place_holders[] = "('%s', '%s', '%d', '%d')";
                        }

                        $query .= implode(', ', $place_holders);
                        $wpdb->query( $wpdb->prepare("$query ", $values));

                    }else{
                        $rs = $wpdb->query("INSERT INTO `".$wpdb->prefix."grules` (`countryID`,`targetURL`,
                                    `catID`,`postID`) VALUES 
                                    ('$country', '$target','$catID','$postID')");
                    }
                }
                                    
                if($_POST['country'] == 'EU')
                    print '<div class="updated below-h2 updated-red">Rules created for all member countries of the European Union.</div>';                    
                else
                    print '<div class="updated below-h2 updated-red">Rule successfully created!</div>';    

            }else{
                print '<div class="updated below-h2 updated-red">Country & Target URL must be specified</div>';
            }
        }
        ?>
    
        </div><!-- float left create rule form -->

        <div style="float:left; width: 440px;">
        <h3>Export WPGeoIP Existent Rules</h3>
        <?php 
        if(isset($_GET['export'])) : 
            $rules = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."grules`", ARRAY_A);   
            if(!count($rules)) {
                echo 'Nothing to export - no rules in DB';
            }else{
               $geoip_csv = fopen(plugin_dir_path(__FILE__) . "/wpgeoip.csv", 'w');
               fputcsv($geoip_csv, array_keys(reset($rules)));
               foreach ($rules as $row) {
                  fputcsv($geoip_csv, $row);
               }
               fclose($geoip_csv);
               echo '<div class="updated updated-red">Successfully generated: <a href="'.plugin_dir_url(__FILE__).'/wpgeoip.csv" style="color:#cc0000" target="_blank">Download CSV</a></div>';
            }
        ?>
        <?php else: ?>
            <a href="admin.php?page=wpgeoip-admin&amp;export=rules">Export WPGeoIP.csv</a>
        <?php endif; ?>

        <h3>Import WPGeoIP Rules <small>(previously generated by plugin)</small></h3>

        <?php 
        if(isset($_POST['sbgeoip_csv'])) {
            echo '<div class="updated below-h2 updated-red">';
            if(isset($_FILES['wpgeoip_csv'])) {
                $csv = $_FILES['wpgeoip_csv'];
                if(pathinfo($csv['name'], PATHINFO_EXTENSION) != "csv") {
                    echo 'Extension ' . pathinfo($csv['name'], PATHINFO_EXTENSION) . ' is not a valid CSV file.';
                }else{
                    $row = 1;
                    if (($handle = fopen($csv['tmp_name'], "r")) !== FALSE) {
                        $i = 0;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            $i++;
                            if($i == 1) continue;

                            $num = count($data);
                            if($num != 6) {
                                echo 'Invalid field count: should be 6';
                                break;
                            }else{
                                list(, $country, $target, $catID, $postID, $home_rule) = $data;
                                $rs = $wpdb->query(
                                        $wpdb->prepare("INSERT INTO `".$wpdb->prefix."grules` 
                                                    (`countryID`,`targetURL`,`catID`,`postID`,`home_rule`) 
                                                    VALUES 
                                                    (%s, %s, %d, %d, %d)", 
                                                    array($country, $target, $catID, $postID, $home_rule)));

                                echo 'Inserted rule ID #'.$wpdb->insert_id.'<br/>';

                            }   
                        }
                        fclose($handle);
                    }else{
                        echo 'Could not read from temporary uploaded CSV file';
                    }
                }
            }else{
                echo 'Pick a CSV file!';
            }
            echo '</div>';
        }
        ?>
        <form method="POST" enctype="multipart/form-data">
        <input type="file" name="wpgeoip_csv" >
        <input type="submit" name="sbgeoip_csv" value="Upload &amp; Import" class="button" />
        </form>
        </div><!-- float left import-export forms -->

        <div style="clear:both;"></div>
        <hr noshade="" width="100%" />
        
        <h3>Current Rules</h3>
        
        <?php

        if(isset($_GET['removeall']) AND ($_GET['removeall'] == 'sure')) {
            $wpdb->query("TRUNCATE ".$wpdb->prefix."grules");
            echo '<meta http-equiv="refresh" content="0; url='.admin_url('admin.php?page=wpgeoip-admin&allremoved=true').'">';
            exit;
        }

        if(isset($_GET['allremoved'])) {
            print '<div class="updated below-h2 updated-red">All redirect rules removed! (<a href="'.admin_url('admin.php?page=wpgeoip-admin').'" style="color:white;text-decoration:underline;">Remove this notice</a>)</div>';    
        }

        $rs = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."grules`");
        if(count($rs))
        {
            ?>
            <p><a href="<?= admin_url('admin.php?page=wpgeoip-admin&removeall=sure') ?>" onclick="return confirm('Are you SURE you want to REMOVE all REDIRECT RULES?');">Delete All Rules</a></p>
            <table width="100%" class="table widefat posts">
            <thead>
                <tr>
                    <th style="font-weight: bold;color: #01708C;">Country</th>
                    <th style="font-weight: bold;color: #01708C;">Target URL</th>
                    <th style="font-weight: bold;color: #01708C;">For Category/Post/Page</th>
                    <th style="font-weight: bold;color: #01708C;">Remove</th>
                </tr>
            </thead>
            <tbody>    
            <?php
            foreach($rs as $row)
            {
            	$postID = $row->postID;
				$catID = $row->catID;
				
				if($catID != 0)
				{
					$target = get_category($catID);
					$target = '<strong>Category</strong> : ' . $target->cat_name;
				}elseif($postID != 0){
                    if($postID == 999999) {
					   $target = '<strong>SITEWIDE REDIRECT</strong>';
                    }elseif( $postID == 999990) {
                       $target = '<strong>WILDCARD REDIRECT</strong><small> for external domain redirects';
                    }else{
                        $target = get_post($postID);
                        $target = '<strong>'.ucfirst($target->post_type) . '</strong> : ' . $target->post_title;
                    }
				}else{
					$target = "<strong>!HOMEPAGE!</strong>";
				}
				
                print '<tr>
                        <td>'.$countryList[$row->countryID].'</td>
                        <td>'.$row->targetURL.'</td>
                        <td>'.($target).'</td>
                        <td><a href="?page=wpgeoip-admin&delID='.$row->ruleID.'" onclick="return confirm(\'Are you SURE you want to REMOVE this redirect rule?\');">[x]</a>
                        </tr>';
            }
            ?>
            </tbody>
            </table>
            <?php   
        }else{
            print 'No rules yet!';
        }
        ?>
        
    </form>
		
	</div>	
	<?php
}
