<?php 
/**
 * Plugin Name: XML Data Feed for Shopbot CSE
 * Description: XML Data Feed for Shopbot CSE
 * Author:      Muzaara
 * Author URI:  https://muzaara.com 
 * Plugin URI:  https://go.muzaara.com
 * Version:     1.0
 * Text Domain: shopbot-woopf
 */

defined( "ABSPATH" ) || exit;

define( "SHOPBOT_WOOPF_VERSION", 1.0 );
define( "SHOPBOT_WOOPF_PATH", sprintf( "%s/", __DIR__ ) );
define( "SHOPBOT_WOOPF_OBJ_PATH", sprintf( "%sclass/objects/", SHOPBOT_WOOPF_PATH ) );
define( "SHOPBOT_WOOPF_URL", sprintf( "%s/", plugins_url( "", __FILE__ )));
define( "SHOPBOT_WOOPF_ASSET_URL", sprintf( "%sasset/", SHOPBOT_WOOPF_URL ) );
define( "SHOPBOT_WOOPF_BASE", plugin_basename( __FILE__ ));
define( "SHOPBOT_WOOPF_GOOGLE_CAT_URL", sprintf( "https://www.google.com/basepages/producttype/taxonomy-with-ids.%s.txt", str_replace( "_", "-", get_locale() ) ) );
define( "SHOPBOT_WOOPF_GOOGLE_CAT_URL_FALLBACK", "https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt" );
define( "SHOPBOT_WOOPF_POST_TYPE", "shopbot-woopf" );
define( "SHOPBOT_WOOPF_CRON_ACTION", "shopbot_woopf_cron_action" );

$upload_dir = wp_upload_dir();
if ( empty( $upload_dir[ "error" ] ) ) {
    define( "SHOPBOT_WOOPF_DUMP_PATH", sprintf( "%s/shopbot-woopf/", $upload_dir[ "basedir" ] ) );
    define( "SHOPBOT_WOOPF_DUMP_URL", sprintf( "%s/shopbot-woopf/", $upload_dir[ "baseurl" ] ) );
}

// require_once "lib/muzaara/muzaara.php";
require_once "class/App.php";

$GLOBALS[ "shopbot_woopf" ] = new \Shopbot\ProductFeed\App();

register_activation_hook( __FILE__, array( "\Shopbot\ProductFeed\App", "activation" ) );
register_deactivation_hook( __FILE__, array( "\Shopbot\ProductFeed\App", "deactivation" ) );
