<?php

if (class_exists('WC_Integration')) {
    class Cekongkir_Integration extends WC_Integration
    {
        protected $api;

        public function __construct()
        {
            $this->id = "cekongkir";
            $this->method_title = __("Rajaongkir Official", "cekongkir");
            $this->method_description = __("An integration for utilizing Rajaongkir V2.", "cekongkir");

            $this->init_form_fields();
            $this->init_settings(); 
            $this->load_dependencies();

            add_action("woocommerce_update_options_integration_" . $this->id, [$this, "process_admin_options"]);
            add_filter('woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
        }

        private function load_dependencies()
        {
            $this->api= Cekongkir_API::get_instance();
        }

        public function init_form_fields()
        {
            $this->form_fields = [
                "api_key" => [
                    "title" => __("API Key", "cek-ongkir"),
                    "type" => "password",
                    "description" => __(
                        "The key that will be used when dealing with Rajaongkir. You can read how to get api key <a href='https://komerceapi.readme.io/reference/rajaongkir-api'>Rajaongkir documentation</a>.",
                        "cekongkir"
                    ),
                    "desc_tip" => false,
                    "default" => $this->get_option("api_key"),
                ],
            ];
        }

        public function process_admin_options() {
            parent::process_admin_options();
        
            if ( ! isset( $this->api ) ) {
                $this->api = Cekongkir_API::get_instance();
            }
    
            $info = $this->api->validate_api_key();
            if (is_wp_error($info)) {
                $error_messages = $info->get_error_messages();
                WC_Admin_Settings::add_error(__('API Key invalid, error: ' . implode(', ', $error_messages), 'cekongkir'));
                $this->update_option("api_key", ""); 
                return true;
            }
        }
        

       
        public function register_shipping_method( $methods ) {
            if ( class_exists( 'Cekongkir_Shipping_Method' ) ) {
                $methods[ CEKONGKIR_METHOD_ID ] = 'Cekongkir_Shipping_Method';
            }
    
            return $methods;
        }
        
    }
}