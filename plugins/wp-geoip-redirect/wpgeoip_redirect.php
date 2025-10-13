<?php
/**
 * Redirect Function
 */
/*
 * Get User Public IP Address
 */

function WPGeoIP_getIP()
{
  // check if ip is passed from cloudflare
  if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
    $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
  } else {
    $ip = $_SERVER["REMOTE_ADDR"];
  }

  // if ip contains commas, take first
  if (strpos($ip, ",") !== false) {
    $ip = explode(",", $ip);
    return trim($ip[0]);
  }

  // if ip contains semicolon (port), take first part
  if (substr_count($ip, ":") === 1) {
    $ip = explode(":", $ip);
    return trim($ip[0]);
  }

  return $ip;
}

// detect search engines|bots
function WPGeoIP_bot_detected()
{
  if (
    isset($_SERVER["HTTP_USER_AGENT"]) &&
    preg_match("/bot|crawl|slurp|spider/i", $_SERVER["HTTP_USER_AGENT"])
  ) {
    return true;
  } else {
    return false;
  }
}

// set cookie ( for once redirect )
function WPGeoIP_Once_Cookie()
{
  if (get_option("wpgeoip_once_redirect", 0) != 0) {
    $cookie_duration = get_option("wpgeoip_cookie_duration", 60);
    $cookie_value = strtotime("+" . $cookie_duration);
    setcookie("wpgeoip_once_redirect", $cookie_value, $cookie_value);
  }
}

// the core of this plugin
function wpgeoip_redirect()
{
  global $wpdb;
  global $wp_query;
  global $post;

  // check if logging enabled
  $logging = get_option("wpgeoip_logging", "No");

  // setup $_GET params if any
  $appendGetParams = "";
  if (isset($_GET) and count($_GET)) {
    $appendGetParams = "?" . http_build_query($_GET);
  }

  // set ip
  $ip = WPGeoIP_getIP();

  // if we're on CloudFlare, get country from themselves
  if (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
    $countryCode = trim(strip_tags($_SERVER["HTTP_CF_IPCOUNTRY"]));

    // debugging useful
    if (isset($_GET["wpgeoip_debug"])) {
      echo "USING_DATABASE: CLOUDFLARE";
      echo "CountryCode Detected: " . $countryCode . "<br/>";
      echo "IP Detected: " . $ip . "<br/>";
      exit();
    }
  } else {
    // otherwise, get from maxmind geoipv2
    // include GeoIP functions
    require_once "vendor/autoload.php";

    // This creates the Reader object, which should be reused across
    // lookups.
    $reader = new GeoIp2\Database\Reader(
      plugin_dir_path(__FILE__) . "/GeoLite2-Country/GeoLite2-Country.mmdb"
    );

    // Set Country Code
    try {
      $record = $reader->country($ip);
      $countryCode = $record->country->isoCode;

      // debugging useful
      if (isset($_GET["wpgeoip_debug"])) {
        echo "USING_DATABASE: MAXMIND";
        echo "CountryCode Detected: " . $countryCode . "<br/>";
        echo "IP Detected: " . $ip . "<br/>";
        exit();
      }
    } catch (\Exception $e) {
      if ($logging != "No") {
        $wpdb->query(
          "INSERT INTO wpgeoip_log VALUES (null, 'IP Not in Database', 'Skipping redirect for IP Address <strong>" .
            WPGeoIP_getIP() .
            " as it isn\'t into the maxmind database.</strong>')"
        );
      }

      // debugging useful
      if (isset($_GET["wpgeoip_debug"])) {
        echo "CountryCode Detected: " . $e->getMessage() . "<br/>";
        echo "IP Detected: " . $ip . "<br/>";
        exit();
      }

      return false;
    }
  }

  // if spider is detected -> SKIP
  if (WPGeoIP_bot_detected()) {
    return false;
  }

  // if $_GET['noredirect'] -> SKIP
  if (
    get_option("wpgeoip_no_redirect", 0) == 1 and
    isset($_GET["noredirect"])
  ) {
    return false;
  }

  // if current IP is in the excluded list -> SKIP
  if ($excluded = get_option("wpgeoip_excluded")) {
    $excluded = explode(PHP_EOL, $excluded);
    $excluded = array_map("trim", $excluded);

    if (in_array(WPGeoIP_getIP(), $excluded)) {
      $wpdb->query(
        "INSERT INTO wpgeoip_log VALUES (null, 'Redirect SKIP', 'Skipping redirect for IP Address <strong>" .
          WPGeoIP_getIP() .
          "</strong>')"
      );
      return false;
    }
  }

  // if once redirect
  if (
    isset($_COOKIE["wpgeoip_once_redirect"]) and
    get_option("wpgeoip_once_redirect", 0) != 0
  ) {
    if ($logging != "No") {
      $wpdb->query(
        "INSERT INTO wpgeoip_log VALUES (null, 'Cookie SKIP', 'Skipping redirect for IP Address <strong>" .
          WPGeoIP_getIP() .
          "</strong> due to existent cookie ( Expires on " .
          date("jS F Y H:iA", $_COOKIE["wpgeoip_once_redirect"]) .
          ")</strong>')"
      );
    }
    return false;
  }

  // set db tables prefix
  $prefix = $wpdb->prefix;

  // set post Id
  $postID = $post->ID;

  // set category Id
  $catID = intval($wp_query->query_vars["cat"]);

  // is this frontpage
  $isHome = is_front_page();

  // set page name for logging purposes
  $the_page_name = "";

  //sitewide rule
  $rs_redirect = $wpdb->get_row(
    "SELECT `targetURL` FROM `" .
      $prefix .
      "grules` 
	                              WHERE `countryID` = '$countryCode' 
								  AND `postID` = 999999 LIMIT 1"
  );

  // if we have a sitewide rule -> REDIRECT
  if ($rs_redirect) {
    // set page name for logging purposes
    $the_page_name = get_the_title($postID);

    // log this redirect
    if ($logging != "No") {
      $wpdb->query(
        "INSERT INTO wpgeoip_log VALUES 
		             (null, 'SITEWIDE Redirect', 
		              'Redirecting Country <strong>" .
          $countryCode .
          "</strong> to 
		              " .
          $rs_redirect->targetURL .
          $appendGetParams .
          "')"
      );
    }

    // cookie if required
    WPGeoIP_Once_Cookie();

    // do the redirect
    print '<meta http-equiv="refresh" content="0;url=' .
      $rs_redirect->targetURL .
      $appendGetParams .
      '"/>';
    exit();
  }

  // wildcard rule
  $rs_redirect = $wpdb->get_row(
    "SELECT `targetURL` FROM `" .
      $prefix .
      "grules` 
	                              WHERE `countryID` = '$countryCode' 
								  AND `postID` = 999990 LIMIT 1"
  );

  // if we have a widlcard rule -> REDIRECT
  if ($rs_redirect) {
    // set page name for logging purposes
    $the_page_name = get_the_title($postID);

    // log this redirect
    if ($logging != "No") {
      $wpdb->query(
        "INSERT INTO wpgeoip_log VALUES 
		             (null, 'WILDCARD Redirect', 
		              'Redirecting Country <strong>" .
          $countryCode .
          "</strong> to 
		              " .
          $rs_redirect->targetURL .
          $_SERVER["REQUEST_URI"] .
          "')"
      );
    }

    // cookie if required
    WPGeoIP_Once_Cookie();

    // do the redirect
    print '<meta http-equiv="refresh" content="0;url=' .
      $rs_redirect->targetURL .
      $_SERVER["REQUEST_URI"] .
      '"/>';
    exit();
  }

  // if we have a redirect for this post Id  -> REDIRECT
  if ($postID != 0) {
    $rs_redirect = $wpdb->get_row(
      "SELECT `targetURL` FROM `" .
        $prefix .
        "grules` 
		                              WHERE `countryID` = '$countryCode' 
									  AND `postID` = $postID"
    );

    // set page name for loggin purposes
    $the_page_name = get_the_title($postID);

    // if rule found
    if ($rs_redirect) {
      // log this redirect
      if ($logging != "No") {
        $wpdb->query(
          "INSERT INTO wpgeoip_log VALUES 
			             (null, 'Redirect <em>" .
            $the_page_name .
            "</em>', 
			              'Redirecting Country <strong>" .
            $countryCode .
            "</strong> 
			              to " .
            $rs_redirect->targetURL .
            $appendGetParams .
            "')"
        );
      }

      // cookie if required
      WPGeoIP_Once_Cookie();

      // do the redirect
      print '<meta http-equiv="refresh" content="0;url=' .
        $rs_redirect->targetURL .
        $appendGetParams .
        '"/>';
      exit();
    }
  }

  // if category rule
  if ($catID != 0) {
    $rs_redirect = $wpdb->get_row(
      "SELECT `targetURL` FROM `" .
        $prefix .
        "grules` WHERE `countryID` = '$countryCode' 
							AND `catID` = $catID"
    );
    $the_page_name = "Category : " . get_the_category_by_ID($catID);
  }

  // if homepage rule
  if ($isHome) {
    $rs_redirect = $wpdb->get_row(
      "SELECT `targetURL` FROM `" .
        $prefix .
        "grules` WHERE `countryID` = '$countryCode' 
							AND `home_rule` = 1"
    );
    $the_page_name = "Homepage";
  }

  // if page/post is in category
  if ($catID == 0 && ($post_categories = get_the_category($postID))) {
    $inCategories = [];
    foreach ($post_categories as $pc) {
      $inCategories[$pc->cat_ID] = $pc->name;
    }
    $inCategoriesSQL = implode(",", array_flip($inCategories));

    $rs_redirect = $wpdb->get_row(
      "SELECT `catID`, `targetURL` FROM `" .
        $prefix .
        "grules` 
		                              WHERE `countryID` = '$countryCode' 
									  AND `catID` IN ($inCategoriesSQL) LIMIT 1"
    );

    if ($rs_redirect) {
      $the_page_name =
        "Post in Redirected Category : " .
        get_the_category_by_ID($rs_redirect->catID);
    }
  }

  // if redirect rule for category/home was found
  if ($rs_redirect) {
    // log this action
    if ($logging != "No") {
      $wpdb->query(
        "INSERT INTO wpgeoip_log VALUES 
		             (null, 'Redirect <em>" .
          $the_page_name .
          "</em>', 'Redirecting Country 
		              <strong>" .
          $countryCode .
          "</strong> to " .
          $rs_redirect->targetURL .
          $appendGetParams .
          "')"
      );
    }

    // cookie if required
    WPGeoIP_Once_Cookie();

    // do the redirect
    print '<meta http-equiv="refresh" content="0;url=' .
      $rs_redirect->targetURL .
      $appendGetParams .
      '"/>';
    exit();
  } else {
    // if mass redirect is enabled
    if (get_option("wpgeoip_mass_redirect") != "0") {
      // get mass redirect destination URL
      $mass_url = get_option("wpgeoip_mass_url");

      // log this action
      if ($logging != "No") {
        $wpdb->query(
          "INSERT INTO wpgeoip_log VALUES 
			             (null, 'Mass Redirect', 
			              'Redirecting Country <strong>" .
            $countryCode .
            "</strong> 
			              to " .
            $rs_redirect->targetURL .
            "')"
        );
      }

      // do the redirect
      print '<meta http-equiv="refresh" content="0;url=' . $mass_url . '"/>';
      exit();
    }
  }
}
