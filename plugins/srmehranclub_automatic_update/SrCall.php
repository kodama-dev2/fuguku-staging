<?php
define( 'SRM_PLUGIN_VERSION', '5.0.7' );
define( 'SRM_PLUGIN_SLUG', 'srmehranclub_automatic_update' );
define( 'SRM_PLUGIN_FILE', 'srmehranclub_automatic_update/srmehranclub_automatic_update.php' );

###################
# Automatic updates
###################
/**
 * Pop-up with details about plugin update visible when there is a new release
 */

function srm_mehran_request_ajax($url, $params = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    if ($params != null) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close($ch);

    return $server_output;
}

function srm_plugin_info($res, $action, $args) {
    // Do nothing if this is not about getting plugin information
    if ($action !== 'plugin_information') {
        return $res;
    }

    // Do nothing if it is not our plugin
    if (SRM_PLUGIN_SLUG !== $args->slug) {
        return $res;
    }

    // Trying to get from cache first
    $remote = get_transient('srm_upgrade_' . SRM_PLUGIN_SLUG);
    
    if (false === $remote) {
        // Info.json is the file with the actual information about plug-in on your server
        $remote = srm_mehran_request_ajax('https://srmehranclub.com/plugins_host/info.json?v=' . rand());
        
        if ($remote) {
            set_transient('srm_upgrade_' . SRM_PLUGIN_SLUG, $remote, 21600); // 6 hours cache
        }
    }

    if ($remote) {
        $remote = json_decode($remote);
        
        if (is_object($remote)) {
            $res = new stdClass();
            $res->name = $remote->name;
            $res->slug = $remote->slug;
            $res->version = $remote->version;
            $res->tested = $remote->tested;
            $res->requires = $remote->requires;
            $res->author = $remote->author;
            $res->author_profile = $remote->author_homepage;
            $res->download_link = $remote->download_url;
            $res->trunk = $remote->download_url;
            $res->last_updated = $remote->last_updated;
            $res->sections = array(
                'description' => $remote->sections->description, // description tab
                'installation' => $remote->sections->installation, // installation tab
                // You can add your custom sections (tabs) here like 'changelog'
            );
            $res->banners = array(
                'low' => $remote->banners->low,
                'high' => $remote->banners->high,
            );

            return $res;
        }
    }

    return false;
}

add_filter('plugins_api', 'srm_plugin_info', 20, 3);

/**
 * Push update itself for plugin or theme
 */
function srm_push_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    // Trying to get from cache first
    $remote = get_transient('srm_upgrade_' . SRM_PLUGIN_SLUG);

    if (false === $remote) {
        // Fetch the plugin info
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://srmehranclub.com/plugins_host/info.json?v=' . rand(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $remote = curl_exec($curl);
        curl_close($curl);

        if ($remote) {
            set_transient('srm_upgrade_' . SRM_PLUGIN_SLUG, $remote, 21600); // 6 hours cache
        }
    }

    if ($remote) {
        if (is_object($remote)) {
            // Remote is already an object
        } else {
            $remote = json_decode($remote);
        }
        
        if (!is_object($remote)) {
            return $transient;
        }
       
        // Check if update is available
        if (version_compare(SRM_PLUGIN_VERSION, $remote->version, '<') && 
            version_compare($remote->requires, get_bloginfo('version'), '<')) {
            
            $res = new stdClass();
            $res->slug = $remote->slug;
            $res->plugin = SRM_PLUGIN_FILE;
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $transient->response[SRM_PLUGIN_FILE] = $res;
        } else {
            // Make sure to remove the update notification if we're already on the current version
            if (isset($transient->response[SRM_PLUGIN_FILE])) {
                unset($transient->response[SRM_PLUGIN_FILE]);
            }
            
            // Ensure the plugin is properly registered in the checked list with current version
            $transient->checked[SRM_PLUGIN_FILE] = SRM_PLUGIN_VERSION;
        }
    }
    
    return $transient;
}

add_filter('site_transient_update_plugins', 'srm_push_update');

/**
 * Cache the results to make update process fast
 */
function srm_after_update($upgrader_object, $options) {
    if ($options['action'] == 'update' && $options['type'] === 'plugin') {
        // Just clean the cache when new plugin version is installed
        delete_transient('srm_upgrade_' . SRM_PLUGIN_SLUG);
    }
}

add_action('upgrader_process_complete', 'srm_after_update', 10, 2);