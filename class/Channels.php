<?php 
namespace Shopbot\ProductFeed;
use \Shopbot\ProductFeed\Object\Field;
use \Shopbot\ProductFeed\Object\GField;
use \Shopbot\ProductFeed\Object\Filter;

use function Shopbot\ProductFeed\Helpers\gFieldInit;

class Channels {
    private $app;
    protected $fields, $customFields, $googleFields;
    protected $filterConditions = array();

    public function __construct() {
        $this->fields = array(
            new Field("id",__("Product ID","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("name",__("Product Name","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("description",__("Product Description","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("link",__("Product Link","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("image",__("Product Image","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("height",__("Product Height","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("length",__("Product Length","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("regular_price",__("Product Regular Price","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("sku",__("Product SKU","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("weight",__("Product Weight","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("categories",__("Product Categories","shopbot-woopf"),Field::PRODUCT_PROP),
            
            new Field("width",__("Product Width","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("date_created",__("Product Created Date","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("date_modified",__("Product Modified Date","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("display_price",__("Product Display Price","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("price",__("Product Price","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("price_suffix",__("Product Price Suffix","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("purchase_note",__("Product Purchase Note","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("rating_count",__("Product Rating Count","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("review_count",__("Product Review Count","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("sale_price",__("Product Sale Price","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("short_description",__("Product Short Description","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("slug",__("Product Slug","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("stock_quantity",__("Product Stock Quantity","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("stock_status",__("Product Stock Status","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("total_sales",__("Product Total Sales","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("total_stock",__("Product Total Stock","shopbot-woopf"),Field::PRODUCT_PROP),
            new Field("type",__("Product Type","shopbot-woopf"),Field::PRODUCT_PROP)
        );

        $this->filterConditions = array(
            new Filter( Filter::CONDITION_EQUALS ),
            new Filter( Filter::CONDITION_NOT_EQUALS ),
            new Filter( Filter::CONDITION_CONTAINS ),
            new Filter( Filter::CONDITION_NOT_CONTAINS ),
            new Filter( Filter::CONDITION_IS_IN ),
            new Filter( Filter::CONDITION_IS_NOT_IN ),
            new Filter( Filter::CONDITION_BETWEEN ),
            new Filter( Filter::CONDITION_NOT_BETWEEN ),
            new Filter( Filter::CONDITION_GREATER_THAN ),
            new Filter( Filter::CONDITION_GREATER_EQUALS ),
            new Filter( Filter::CONDITION_LESS_THAN ),
            new Filter( Filter::CONDITION_LESS_EQUALS ),
            new Filter( Filter::CONDITION_IS_EMPTY ),
            new Filter( Filter::CONDITION_IS_NOT_EMPTY )
        );
        
        $this->loadMetaFields();
        $this->loadGoogleFields();
    }

    private function actions() {

    }

    public function getProductFields() {
        return apply_filters( "shopbot_woopf_get_product_fields", array_merge($this->fields, $this->customFields) );
    }

    public function getGoogleFields() {
        return apply_filters( "shopbot_woopf_get_google_fields", $this->googleFields );
    }

    public function loadGoogleFields() {
        $file = sprintf( "%sgoogle_product_fields.json", SHOPBOT_WOOPF_PATH );
        $this->googleFields = array();

        if ( file_exists($file) && ($content = file_get_contents($file)) && ($fields = json_decode($content)) ) {
            foreach( $fields as $field ) {
                $this->googleFields[] = gFieldInit( $field ); // new GField($field);
            }
        }
    }

    public function getFilterConditions() {
        return apply_filters( "shopbot_woopf_get_filter_conditions", $this->filterConditions);
    }

    public function loadMetaFields() {
        global $wpdb;

        $this->customFields = array();

        $fields = $wpdb->get_results( "SELECT DISTINCT `meta_key` FROM `{$wpdb->postmeta}` INNER JOIN `{$wpdb->posts}` ON ID = post_id WHERE post_type = 'product'" );
        if ( $fields ) {
            foreach( $fields as $field ) {
                $this->customFields[] = new Field( $field->meta_key, $field->meta_key, Field::PRODUCT_META );
            }
        }
    }
}