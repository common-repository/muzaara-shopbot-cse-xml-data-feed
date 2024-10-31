<?php 
namespace Shopbot\ProductFeed\Helpers;
defined( "ABSPATH" ) || exit;

require_once SHOPBOT_WOOPF_OBJ_PATH . "GField.php";

use \Shopbot\ProductFeed\Object\GField;

if ( !function_exists( "gFieldInit" ) ) {
    function gFieldInit( \StdClass $field ) {
        return new GField($field);
    }
}