<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

defined('SRM_SECRET_KEY') || define('SRM_SECRET_KEY', '60f584484f4626.98370693');
defined('LICENSE_SERVER_URL') || define('LICENSE_SERVER_URL', 'https://database.srmehranclub.com/api/v2');
defined('SRM_ITEM_REFERENCE') || define('SRM_ITEM_REFERENCE', 'Automatic-upate-api');

class Srm_License
{
    public function register()
    {
        add_action('wp_ajax_activate_license', [$this, 'activate_license']);
        add_action('wp_ajax_deactivate_license', [$this, 'deactivate_license']);
        add_action('wp_ajax_complete_setup', [$this, 'complete_setup']);
        
    }

    public static function membershipInfo(){
           $info = self::verifiedmembershipInfo();
         if ( ! empty( $info['success'] ) && 
             ! empty( $info['data']['success'] ) && 
             ! empty( $info['data']['user'] ) ) {
            
            return $info['data']['user']; // ✅ Return only user array
        }

        // If not valid, return empty array
        return [];  
    }

public static function verifiedmembershipInfo() {
    try {
        $cache_key = 'verified_license_data';
        $licenseId = self::get_license_key();
     

        // 1. Check cached data (valid for 1 hour)
        $cached_data = get_transient($cache_key);

        if ($cached_data !== false) {
            return [
                'success' => true,
                'data'    => $cached_data,
                'cached'  => true,
            ];
        }

        // 2. Call license server
        $response = wp_remote_get(LICENSE_SERVER_URL . '/license/verify/' . $licenseId, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
       


        $http_code = wp_remote_retrieve_response_code($response);
        $body      = wp_remote_retrieve_body($response);


        if ($http_code !== 200) {
           self::deactivateSelfLicense();
            return [
                'success' => false,
                'data'    => [],
                'cached'  => false,
                'message' => "Server returned HTTP {$http_code}",
            ];
        }

        $data = json_decode($body, true);
      

        if (json_last_error() !== JSON_ERROR_NONE) {
           self::deactivateSelfLicense();
            return [
                'success' => false,
                'data'    => [],
                'cached'  => false,
                'message' => 'Invalid JSON: ' . json_last_error_msg(),
            ];
        }

        // 3. Save response in cache for 1 hour
        if (!empty($data['success']) && $data['success'] === true) {
            set_transient($cache_key, $data, HOUR_IN_SECONDS);
            return [
                'success' => true,
                'data'    => $data,
                'cached'  => false,
            ];
        }
         self::deactivateSelfLicense();
        return [
            'success' => false,
            'data'    => [],
            'cached'  => false,
        ];

    } catch (\Exception $e) {
       self::deactivateSelfLicense();
        return [
            'success' => false,
            'data'    => [],
            'cached'  => false,
            'message' => $e->getMessage(),
        ];
    }
}




    public static function getItemId()
    {
        return get_current_user_id();
    }

    public static function init()
    {
        if (!empty($_GET['SRM_deactivate'])) {
            update_option('SRM_license_status', 'invalid');
            update_option('SRM_license_last_error', 'Deactivated manually');
        }

        include SRM_PLUGIN_DIR . '/templates/Srm_license.php';
    }



    public static function isMembership(){
            return get_option('SRMform_memberships');
    }
    /**
     * Validate license periodically
     */
    public static function validate_license()
    {
        $prev = get_option('SRM_license_key_time');
        $delta = $prev ? time() - strtotime($prev) : 0;

        if (empty($prev) || $delta > (60 * 60 * 24)) {
            $license = get_option('SRM_license_key');
            self::check_license($license);
        }

        $status = get_option('SRM_license_key_valid');

        return ($status === 'valid');
    }

    public static function get_license_key($key = false)
    {
        if (empty($key)) {
            $key = get_option('SRM_license_key');
        }
        return $key;
    }

    /**
     * Check license (currently mocked)
     */
    public static function check_license($key)
    {
        return true; // Laravel API handles the real check
    }

    /**
     * Activate license via Laravel API
     */
    public function activate_license()
    {
        $user_id = get_current_user_id();
        $license_key = sanitize_text_field($_POST['srm_license_key'] ?? '');
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $domain = $protocol . $_SERVER['SERVER_NAME'];
     

        if (empty($license_key)) {
            wp_send_json([
                'success' => false,
                'message' => 'License key is required.'
            ], 200);
        }

        $api_response = wp_remote_post(LICENSE_SERVER_URL . '/license/activate', [
            'method'    => 'POST',
            'body'      => [
                'license_key' => $license_key,
                'domain'      => $domain
            ],
            'sslverify' => false
        ]);
      

        if (is_wp_error($api_response)) {
            wp_send_json([
                'success' => false,
                'message' => $api_response->get_error_message()
            ], 200);
        }

        $data = json_decode(wp_remote_retrieve_body($api_response));

        if (!empty($data->success)) {
            update_option('SRM_license_key', $license_key);
            update_option('SRM_license_key_status', 'Activated');
            update_option('SRM_license_key_valid', 'valid');
            update_option('SRM_domain_name', $domain);
            update_option('SRM_license_last_error', '');
            update_option('SRMform_memberships',true);  
            wp_send_json([
                'success' => true,
                'message' => 'License activated successfully.',
                'data'    => $data
            ]);
        } else {
            update_option('SRM_license_key_valid', 'invalid');
            update_option('SRMform_memberships',false);  
            wp_send_json([
                'success' => false,
                'message' => $data->message ?? 'Activation failed'
            ], 200);
        }
    }

    /**
     * Deactivate license via Laravel API
     */
    public  function deactivate_license()
    {
        $user_id = get_current_user_id();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $domain = $protocol . $_SERVER['SERVER_NAME'];
         $license_key=get_option('SRM_license_key');
     

        $api_response = wp_remote_post(LICENSE_SERVER_URL . '/license/deactivate', [
            'method'    => 'POST',
            'body'      => [
                'user_id'     => $user_id,
                'license_key' => $license_key,
                'domain'      => $domain
            ],
            'sslverify' => false
        ]);

        if (is_wp_error($api_response)) {
            wp_send_json([
                'success' => false,
                'message' => $api_response->get_error_message()
            ]);
        }

        $data = json_decode(wp_remote_retrieve_body($api_response));

        if (!empty($data->success)) {
            // Remove local options
            update_option('SRM_license_key', '');
            update_option('SRM_license_key_status', 'Deactivated');
            update_option('SRM_license_key_valid', 'invalid');
            update_option('SRM_domain_name', '');
            update_option('SRM_license_last_error', '');
            update_option('SRM_isSetupDone',false); 
            update_option('SRMform_memberships',false);   

            wp_send_json([
                'success' => true,
                'message' => 'License deactivated successfully.',
                'data'    => $data
            ]);
        } else {
             update_option('SRM_license_key', '');
            update_option('SRM_license_key_status', 'Deactivated');
            update_option('SRM_license_key_valid', 'invalid');
            update_option('SRM_domain_name', '');
            update_option('SRM_license_last_error', '');
            update_option('SRM_isSetupDone',false); 
            update_option('SRMform_memberships',false);  
              wp_send_json([
                'success' => true,
                'message' => 'License deactivated successfully.',
                'data'    => $data
            ]);
        }
    }

    public static function deactivateSelfLicense()
    {
        $user_id = get_current_user_id();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $domain = $protocol . $_SERVER['SERVER_NAME'];
         $license_key=get_option('SRM_license_key');
     

        $api_response = wp_remote_post(LICENSE_SERVER_URL . '/license/deactivate', [
            'method'    => 'POST',
            'body'      => [
                'user_id'     => $user_id,
                'license_key' => $license_key,
                'domain'      => $domain
            ],
            'sslverify' => false
        ]);

        if (is_wp_error($api_response)) {
            wp_send_json([
                'success' => false,
                'message' => $api_response->get_error_message()
            ]);
        }

        $data = json_decode(wp_remote_retrieve_body($api_response));

         update_option('SRM_license_key', '');
            update_option('SRM_license_key_status', 'Deactivated');
            update_option('SRM_license_key_valid', 'invalid');
            update_option('SRM_domain_name', '');
            update_option('SRM_license_last_error', '');
            update_option('SRM_isSetupDone',false); 
            update_option('SRMform_memberships',false);   
              wp_safe_redirect( admin_url( 'admin.php?page=srm-license' ) );
        exit;
    }




    /**
     * Complete setup
     */
    public function complete_setup()
    {
        update_option('SRM_isSetupDone', true);
        wp_send_json([
            'success' => true,
            'redirect' => admin_url('admin.php?page=srm-automatic-upgrade')
        ]);
    }

    /**
     * Get current license domains from Laravel
     */
    public static function get_domains($license_key)
    {
        $user_id = get_current_user_id();

        $api_response = wp_remote_get(add_query_arg([
            'user_id' => $user_id,
            'license_key' => $license_key
        ], LICENSE_SERVER_URL . '/license/domains'), [
            'sslverify' => false
        ]);

        if (is_wp_error($api_response)) return [];

        $data = json_decode(wp_remote_retrieve_body($api_response));
        return $data->domains ?? [];
    }
}
