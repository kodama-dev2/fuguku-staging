<?php
    $getMembrshipInfo=(get_option('SRM_membershipInfo'))?get_option('SRM_membershipInfo'):'';
    
?>
<style>
  .header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stats {
  flex: 1;
}

.search-container {

    display: flex;
    gap: 8px;
    flex: 1;
    justify-content: flex-start;
}

div#statsContainer {
    text-align: end;
    width: 500px;
}

        #searchInput {

            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            width: 300px;
        }

        #searchInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 15px rgba(102, 126, 234, 0.2);
        }

        #searchBtn {
              margin-top: 7px;
            padding: 12px 20px;
            background: linear-gradient(45deg, #5ac349, #045307);
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            height: 40px;
            width: 120px;
        }

        #searchBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
        }

        #searchBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: translateY(0);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .wp-list-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0;
        }

        .wp-list-table thead th {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .wp-list-table thead th:first-child {
            border-radius: 15px 0 0 0;
        }

        .wp-list-table thead th:last-child {
            border-radius: 0 15px 0 0;
        }

        .wp-list-table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .wp-list-table tbody tr:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            transform: scale(1.001);
        }

        .wp-list-table tbody tr:last-child {
            border-bottom: none;
        }

        .wp-list-table td {
            padding: 15px 12px;
            border: none;
            font-size: 14px;
            vertical-align: middle;
        }

        .plugin-name {
            font-weight: 600;
            color: #333;
            max-width: 300px;
            line-height: 1.4;
        }

        .plugin-type {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .type-plugin {
            background: #e8f5e8;
            color: #28a745;
        }

        .type-theme {
            background: #fff3cd;
            color: #856404;
        }

        .version {
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .developer {
            color: #666;
            font-size: 13px;
            text-transform: capitalize;
        }

        .options {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .btn-primary {
            background: #667eea;
        }

        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #28a745;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        #loadMoreBtn {
            display: block;
            margin: 30px auto;
            padding: 15px 40px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #loadMoreBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(40, 167, 69, 0.3);
        }

        #loadMoreBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: translateY(0);
        }

     .loading {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.6); /* subtle transparent overlay */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #e5e7eb;       /* light gray */
  border-top: 4px solid #667eea;    /* accent color */
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-text {
  margin-top: 12px;
  font-size: 16px;
  font-weight: 500;
  color: #667eea;
  text-align: center;
}

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 18px;
        }

        .stats {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }

        .error {
            text-align: center;
            padding: 20px;
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            border-radius: 10px;
            margin: 20px 0;
        }

        .updated-date {
            color: #666;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .search-container {
                justify-content: flex-end;
            }

            #searchInput {
                width: 150px;
            }

            .table-container {
                font-size: 12px;
            }

            .wp-list-table td {
                padding: 10px 8px;
            }

            .options {
                flex-direction: column;
                gap: 4px;
            }

            h1 {
                font-size: 1.8em;
            }
        }
</style>
<div class="wrap srm_styles" id="setup_page">
	<?=srm_Base::showVersion()?>
	 <h3><?=SRMA_GLOBAL_PLUGIN_NAME?> <?php _e('Manager', 'srm'); ?></h3>
	 <hr class="wp-header-end">
<?php if(!Srm_Whitelebel::isWhiteLebelEnable()):?>
     <div class="postbox ilj-postbox admin-headline">
        <h2 class="srBg-primary"><span class="dashicons dashicons-bell"></span>Notifications!</h2>      
        <div class="inside">
            <?php if(isset($membershipInfo['au_notifications'])):?>
            <h5><?=$membershipInfo['au_notifications']?></h5>     
            <?php endif;?> 
        </div>
        <?php
            if(isset($return['error']) && $return['error']==1){
                echo '<p style="color:red">'.$return['msg'].'</p>';
            }
           
        ?>
    </div>
 
	 <div class="ilj-row">
   <div class="col-9">
      <div class="postbox ilj-postbox">
         <h2 class="srBg-primary">Plugin related</h2>
         <div class="inside">
            <ul class="ilj-ressources divide">
               <li><span class="dashicons dashicons-chart-bar"></span>Total Products <?=$total_products?></li>
               <li><span class="dashicons dashicons-update"></span><?=$backupCount?> Backups</li>
              
            </ul>
         </div>
      </div>
   </div>
   <div class="col-3 MembershipInfo">
      <div class="postbox ilj-postbox promo ">
         <h3 class="title srBg-primary">Membership info</h3>
         <div class="inside">
         <div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title">Welcome <?=(isset($membershipInfo['name']))?$membershipInfo['name']:''?> !</h3>						
			</div>
			<div class="panel-body">
				<table>
           

					<tbody><tr><td><strong>User ID: </strong></td><td><?=(isset($membershipInfo['userid']))?$membershipInfo['userid']:''?></td></tr>
					<tr><td><strong>User Name: </strong></td><td><?=(isset($membershipInfo['name']))?$membershipInfo['name']:''?></td></tr>
                    
                    <tr><td><strong>Active Plan: </strong></td><td><?=(isset($membershipInfo['membership_name']))?$membershipInfo['membership_name']:''?></td></tr>
					<tr><td><strong>Status: </strong></td><td><?=(isset($membershipInfo['membership_status']))?$membershipInfo['membership_status']:''?></td></tr>
                   
				</tbody></table>
			</div>
		</div>
         </div>
      </div>
   </div>
   <?php endif;?>
</div>


<div class="ilj-row">
     <div class="container">
        <div class="header">
             
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search plugins..." />
                <button id="searchBtn">Search</button>
            </div>
             <div id="statsContainer" class="stats" style="display: none;"></div>
        </div>

        <div id="loadingContainer" class="loading" style="display: none;">
             <div style="text-align:center;">
                <div class="spinner"></div>
                <div>Loading...</div>
            </div>
        </div>

      
        <div class="table-container">
            <table id="myTable" class="wp-list-table widefat plugins commonTable dashboardTable fffff">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-name column-primary srBg-primary">Plugin Name</th>
                        <th scope="col" class="manage-column srBg-primary">Type</th>
                        <th scope="col" class="manage-column srBg-primary">Developer</th>
                        <th scope="col" class="manage-column srBg-primary">Version</th>
                        <th scope="col" class="manage-column srBg-primary">Updated</th>
                        <th scope="col" class="manage-column srBg-primary">Options</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Results will be populated here -->
                </tbody>
            </table>
        </div>

        <button id="loadMoreBtn" style="display: none;">Load More</button>

        <div id="noResultsContainer" class="no-results" style="display: none;">
            <div style="font-size: 3em; margin-bottom: 20px;">🔍</div>
            <div>No products found for your search query.</div>
            <div style="margin-top: 10px; font-size: 0.9em; color: #999;">
                Try different keywords or check your spelling.
            </div>
        </div>

        <div id="errorContainer" class="error" style="display: none;"></div>
    </div>
</div>
</div>



<script>
       

        // Initialize the dashboard when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new PluginDashboard();
        });
    </script>
