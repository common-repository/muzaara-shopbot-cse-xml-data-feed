<?php 
namespace Shopbot\ProductFeed\Helpers;
defined( "ABSPATH" ) || exit;

require_once SHOPBOT_WOOPF_OBJ_PATH . "Filter.php";

use \Shopbot\ProductFeed\Object\Filter;

if ( !function_exists( "filterInit" ) ) {
    function filterInit( $condition ) {
        return new Filter($condition);
    }
}