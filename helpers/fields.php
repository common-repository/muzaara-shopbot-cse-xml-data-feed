<?php 
namespace Shopbot\ProductFeed\Helpers;
defined( "ABSPATH" ) || exit;

require_once SHOPBOT_WOOPF_OBJ_PATH . "Field.php";

use \Shopbot\ProductFeed\Object\Field;

if ( !function_exists( "fieldInit" ) ) {
    function fieldInit( $id, $type, $name = "" ) {
        return new Field($id, $name, $type);
    }
}