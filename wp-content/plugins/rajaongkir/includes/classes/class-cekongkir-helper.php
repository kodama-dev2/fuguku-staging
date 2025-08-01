<?php

// Exit if accessed directly.
if (!defined("ABSPATH")) {
    exit();
}

class Cekongkir_Helper 
{
    public static function meta_key($str)
    {
        return "_wc_bts_" . $str;
    }

    public static function option_key($str)
    {
        return "wc_bts_" . $str;
    }

    public static function notice_key(...$args)
    {
        return "wc-bts-" . implode("-", $args);
    }

    public static function get_option($key)
    {
        return get_option(self::option_key($key));
    }

    public static function get_setting($key)
    {
        $settings = get_option("woocommerce_cekongkir_settings");
        return isset($settings[$key]) ? $settings[$key] : null;
    }

}