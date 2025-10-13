<?php
/*
 * Log pin results
 */
 
 function wpgeoip_log()
 {
     // functionality
    if( 'Yes' != get_option( 'wpgeoip-functionality', 'No' ) ) {
        echo '<script>window.location.href="'.admin_url('admin.php?page=wpgeoip-license').'";</script>';
        exit;
    }

    if( isset( $_POST[ 'wpgeoip_logging' ] ) ) {
        update_option( 'wpgeoip_logging', trim( strip_tags( $_POST[ 'wpgeoip_logging' ] ) ) );
    }
    
     global $wpdb;
     
     $logs = $wpdb->get_results("SELECT * FROM wpgeoip_log ORDER BY logID DESC LIMIT 0, 100");
     
     ?>
     <div id="wrap" class="wpgeoip-wrapper">
        <img src="<?= plugin_dir_url(__FILE__) ?>/assets/images/icon32x32.png" style="float:left;"/> 
        <h1 style="float:left;margin-top: 10px;margin-left:10px;">WP GeoIP Log</h1>
        <div style="clear:both;"></div>
        <hr />
        <br />
        
        <div class="updated below-h2">
            <strong>Enable Logging ?</strong> <small>(it is recommended to disable the logging for speed and database optimisation - only use it for debugging purposes)</small> ?
                
            <?php
            $logging = get_option('wpgeoip_logging', 'No');
            ?>
            
            <br><br>

            <form method="POST">
            <input type="radio" name="wpgeoip_logging" value="Yes" <?php if('Yes' == $logging) echo 'checked'; ?>> Yes
            <br>
            <input type="radio" name="wpgeoip_logging" value="No" <?php if('No' == $logging) echo 'checked'; ?>> No
            <br><br>
            <input type="submit" name="sb" class="button" value="Save">
            </form>

        </div><!-- /.updated below-h2 -->

            <br />
     <?php
     
     if($logs) {
         
         print '<table class="widefat">';
         
         print '<thead>
                  <tr>
                    <th style="font-weight: bold;color: #01708C;">Action</th>
                    <th style="font-weight: bold;color: #01708C;">Result</th>
                  </tr>
                </thead>
                <tbody>';
         
         foreach($logs as $log) {
             print '<tr>
                    <td>'.$log->post.'</td>
                    <td>'.$log->message.'</td>
                    </tr>';
         }
         
         print '</tbody>
                </table>';
         
     }else{
         print '<div class="updated updated-red">Nothing logged yet.</div>';
     }
     
     ?>
     </div>
     <?php
     
 }
