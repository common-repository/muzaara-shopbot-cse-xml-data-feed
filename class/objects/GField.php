<?php 
namespace Shopbot\ProductFeed\Object;

class GField {
    protected $slug, $description, $schema, $group;

    public function __construct(\StdClass $field) {
        if ( !empty( $field->slug ) ) {
            $this->slug = $field->slug;
            $this->description = (!empty($field->description) ? $field->description : "" );
            $this->schema = (!empty($field->schema) ? $field->schema : "" );
            $this->group = (!empty($field->group) ? $field->group : "" );
        }
    }

    public function getSlug() : string {
        return apply_filters( "shopbot_woopf_get_gfield_slug", $this->slug, $this );
    }

    public function getName() : string {
        $name = str_replace( "_", " ", $this->slug );
        $name = str_replace( "min", __( "Minimum", "shopbot-woopf" ), $name );
        $name = str_replace( "max", __( "Maxium", "shopbot-woopf" ), $name );
        $name = trim( $name );

        return apply_filters( "shopbot_woopf_get_gfield_name", ucwords($name), $this );
    }

    public function getGroup() : string {
        return apply_filters( "shopbot_woopf_get_gfield_group", $this->group, $this );
    }

    public function getGroupName() : string {
        $ret = "";

        switch( $this->group ) {
            case "basic":
                $ret = __( "Basic product data", "shopbot-woopf" );
            break;
            case "price":
                $ret = __( "Price & availability", "shopbot-woopf" );
            break;
            case "category":
                $ret = __( "Product category", "shopbot-woopf" );
            break;
            case "identifiers":
                $ret = __( "Product identifiers", "shopbot-woopf" );
            break;
            case "product_description":
                $ret = __( "Detailed product description", "shopbot-woopf" );
            break;
            case "shopping_campaigns":
                $ret = __( "Shopping campaigns and other configurations", "shopbot-woopf" );
            break;
            case "destinations":
                $ret = __( "Destinations", "shopbot-woopf" );
            break;
            case "shipping":
                $ret = __( "Shipping", "shopbot-woopf" );
            break;
            case "tax":
                $ret = __( "Tax", "shopbot-woopf" );
            break;
            default:
                $ret = "";
        }
        
        return $ret;
    }
}