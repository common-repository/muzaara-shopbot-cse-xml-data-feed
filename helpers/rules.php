<?php 
namespace Shopbot\ProductFeed\Helpers;
defined( "ABSPATH" ) || exit;

require_once SHOPBOT_WOOPF_OBJ_PATH . "Rule.php";

use \Shopbot\ProductFeed\Object\Rule;

if ( !function_exists( "ruleInit" ) ) {
    function ruleInit( int $condition ) {
        return new Rule($condition);
    }
}