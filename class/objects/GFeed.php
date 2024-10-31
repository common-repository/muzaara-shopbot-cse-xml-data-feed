<?php 
namespace Shopbot\ProductFeed\Object;
use function Shopbot\ProductFeed\Helpers\gFieldInit;
use function Shopbot\ProductFeed\Helpers\filterInit;
use function Shopbot\ProductFeed\Helpers\fieldInit;
use function Shopbot\ProductFeed\Helpers\ruleInit;

class GFeed extends \Shopbot\ProductFeed\Abs\Feed {
    const PUSH_TO_GOOGLE = 1;
    const PUSH_TO_URL = 2;

    protected $type;
    protected $merchantId;
    protected $noticeEmail;

    protected $utm = array(
        "utm_source"    => "",
        "utm_term"      => "",
        "utm_content"   => "",
        "utm_medium"    => "",
        "utm_campaign"  => ""
    );

    public function __construct(int $id = 0) {
        
        parent::__construct($id);

        $this->setType( "google" );
    }

    public function setMerchantId( $id ) {
        $this->merchantId = $id;

        return $this;
    }

    public function setNoticeEmail( $email ) {
        if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
            $this->noticeEmail = $email;
            return true;
        }

        return false;
    }

    public function getMerchantId() {
        return apply_filters( "shopbot_woopf_get_feed_merchant_id", $this->merchantId, $this );
    }

    public function getNoticeEmail() {
        return apply_filters( "shopbot_woopf_get_feed_notice_email", (!$this->noticeEmail ? get_bloginfo( "admin_email" ) : $this->noticeEmail), $this );
    }

    public function setUtm(array $utms) {
        foreach( $this->utm as $key => $value ) {
            $this->utm[ $key ] = !empty( $utms[$key] ) ? esc_attr(sanitize_text_field( $utms[ $key ] ) ) : "";
        }

        return $this;
    }

    public function getUtm( $product = null ) {
        $utm = $this->utm;
        if ( $product ) {
            $utm[ "utm_term" ] = str_ireplace( "[product_id]", $product->get_id(), $this->utm[ "utm_term" ] );
        }
        return apply_filters( "shopbot_woopf_get_feed_utm", $utm, $this );
    }

    public function setMapping(array $mapping) {
        if ( !empty( $mapping[ "slug" ] ) ) {
            $field = new Field($mapping["slug"], "", $mapping[ "type" ]);
            
            if ( $mapping[ "type" ] == Field::CUSTOM_FIELD ) {
                $field->setValue($mapping["slug"]);
            }

            if ( isset( $mapping[ "prefix" ] ) ) {
                $field->setPrefix($mapping["prefix"]);
            }

            if ( isset( $mapping[ "suffix" ] ) ) {
                $field->setSuffix($mapping["suffix"]);
            }

            if ( isset( $mapping[ "gField" ] ) ) {
                $field->setGField(
                    gFieldInit( (object) array( "slug" => $mapping[ "gField" ] ) ) 
                );
            }

            $this->mappings[] = $field;
        }

        return $this;
    }

    public function setFilter(array $filter) {
        if ( !empty( $filter[ "if" ] ) ) {
            $condition = filterInit($filter[ "condition" ]);
            
            if ( $condition->getCondition() ) {
                $fieldA = fieldInit( $filter[ "if" ], $filter[ "if_type" ] );
                $fieldB = fieldInit( $filter[ "value" ], $filter[ "value_type" ] );

                $condition->setStmt($fieldA, $fieldB, $filter[ "then" ] );
                $this->filters[] = $condition;
            }
        }
    }

    public function setRule(array $rule) {
        if ( !empty( $rule[ "if" ] ) ) {
            $condition = ruleInit( $rule[ "condition" ] );

            if ( $condition->getCondition() ) {
                $fieldA = fieldInit( $rule[ "if" ], $rule[ "if_type" ] );
                $fieldB = fieldInit( $rule[ "value" ], $rule[ "value_type" ] );
                $fieldC = fieldInit( $rule[ "then" ], $rule[ "then_type" ] );
                $fieldD = fieldInit( $rule[ "is" ], $rule[ "is_type"] );

                $condition->setStmt($fieldA, $fieldB, $fieldC, $fieldD );
                $this->rules[] = $condition;
            }
        }
    }

    public function executeFilters( $product ) {
        $ret = true;

        foreach( $this->filters as $filter ) {
            $ret = $filter->execute( $product ) == Filter::__INCLUDE__;
        }

        return $ret;
    }

    public function executeRules( $product ) {
        foreach( $this->getRules() as $rule ) {
            if ( $rule->execute( $product ) ) {
                $stmt = $rule->getStmt();

                foreach( $this->getMappings() as $index => $field) {
                    if ( $field->getType() != Field::CUSTOM_FIELD && $field->getSlug() == $stmt->C->getSlug() ) {
                        $newValue = new Field($stmt->D->getSlug(), "", $stmt->D->getType() );
                        $newValue->setGField( $this->mappings[$index]->getGField() ); // Retain the Google field
                        $newValue->setPrefix( $this->mappings[$index]->getPrefix() );
                        $newValue->setSuffix( $this->mappings[$index]->getSuffix() );

                        $this->mappings[$index] = $newValue;
                    }
                }
            }
        }
    }

    public function getFilters() : array {
        return apply_filters( "shopbot_woopf_get_feed_filters", $this->filters, $this );
    }

    public function getRules() : array {
        return apply_filters( "shopbot_woopf_get_feed_rules", $this->rules, $this );
    }

    public function toArgs() {
        $postargs = array(
            "post_title" => $this->name,
            "post_status"       =>  $this->status,
            "post_type"         =>  SHOPBOT_WOOPF_POST_TYPE,
            "meta_input"        =>  array(
                "shopbot_woopf_push_type"           =>  $this->pushType,
                "shopbot_woopf_refresh_rate"        =>  $this->refreshRate,
                "shopbot_woopf_country"             =>  $this->country,
                "shopbot_woopf_category_mapping"    =>  $this->categoryMapping,
                "shopbot_woopf_mappings"            =>  array(),
                "shopbot_woopf_filters"             =>  array(),
                "shopbot_woopf_rules"               =>  array(),
                "shopbot_woopf_utm"                 =>  $this->utm,
                "shopbot_woopf_feed_type"           =>  $this->type,
                "shopbot_woopf_merchant_id"         =>  $this->merchantId,
                "shopbot_woopf_notice_email"        =>  $this->noticeEmail,
                "shopbot_woopf_running_status"      =>  $this->runningStatus === true,
                "shopbot_woopf_product_ids"         =>  $this->productIds
            )
        );

        
        if ( !$this->id || ( $this->id && $this->lastRefreshed ) ) {
            $postargs[ "meta_input" ][ "shopbot_woopf_last_refreshed" ] = $this->lastRefreshed;
        }

        if ( $this->dumpURL ) {
            $postargs[ "meta_input" ][ "shopbot_woopf_dump_url" ] = $this->dumpURL;
        }
        
        if ( $this->id ) {
            $postargs[ "ID" ] = $this->id;
        }

        if ( $this->mappings ) {
            $mappings = array_map(function( $field ) {
                return array(
                    "slug"      =>  $field->getSlug(),
                    "type"      =>  $field->getType(),
                    "prefix"    =>  $field->getPrefix(),
                    "suffix"    =>  $field->getSuffix(),
                    "gField"    =>  $field->getGField()->getSlug()
                );
            }, $this->mappings );

            $postargs[ "meta_input" ][ "shopbot_woopf_mappings" ] = $mappings;
        }

        if ( $this->filters ) {
            $filters = array_map(function( $filter ) {
                $stmt = $filter->getStmt();
                return array(
                    "if"            =>  $stmt->A->getSlug(),
                    "if_type"       =>  $stmt->A->getType(),
                    "value"         =>  $stmt->B->getSlug(),
                    "value_type"    =>  $stmt->B->getType(),
                    "condition"     =>  $filter->getCondition(),
                    "then"          =>  $stmt->istrue == Filter::__EXCLUDE__ ? 0 : 1
                );
            }, $this->filters );

            $postargs[ "meta_input" ][ "shopbot_woopf_filters" ] = $filters;
        }

        if ( $this->rules ) {
            $rules = array_map(function( $rule ) {
                $stmt = $rule->getStmt();
                return array(
                    "if"            =>  $stmt->A->getSlug(),
                    "if_type"       =>  $stmt->A->getType(),
                    "value"         =>  $stmt->B->getSlug(),
                    "value_type"    =>  $stmt->B->getType(),
                    "then"          =>  $stmt->C->getSlug(),
                    "then_type"     =>  $stmt->C->getType(),
                    "is"            =>  $stmt->D->getSlug(),
                    "is_type"       =>  $stmt->D->getType(),
                    "condition"     =>  $rule->getCondition()
                );
            }, $this->rules );

            $postargs[ "meta_input" ][ "shopbot_woopf_rules" ] = $rules;
        }

        return apply_filters( "shopbot_woopf_feed_to_postargs", $postargs, $this );
    }

    public function save( $push_products = false ) {
        $postargs = $this->toArgs();
        $post_id = 0;

        do_action( "shopbot_woopf_before_feed_create", $this );
        if ( !$this->id ) {
            $post_id = wp_insert_post( $postargs, true );
            
        } else {
            $post_id = wp_update_post( $postargs, true );
        }

        if ( !is_wp_error( $post_id ) && $post_id ) {
            /* 
                Because we need to add duplicate key of product types so as to be able to do a meta_query in WP_Query for faster product matching; we need to add it separately, as duplicate field is not supported in $postarr. So, product type will not appear in $this->toArgs()
            */

            delete_post_meta( $post_id, "shopbot_woopf_product_types" ); // delete and add again to prevent duplicates

            foreach( $this->productTypes as $productType ) {
                add_post_meta($post_id, "shopbot_woopf_product_types", $productType, false);
            }

            $this->id = $post_id;
            if ( $this->pushType == 1 && $postargs[ "post_status" ] == "publish" && $push_products ) {
                $this->pushBatchProducts();
            }
        }

        do_action( "shopbot_woopf_after_feed_create", $this );

        return $post_id;
    }

    public function findProducts( $exclude_already_pushed = true ) : array {
        $args = array(
            "limit"     =>  -1,
            "status"    =>  "publish",
            "type"      =>  $this->getProductTypes()
        );

        if ( $this->pushType == 1 && $exclude_already_pushed ) { // It pushes to Google 
            $args[ "custom_meta" ] = array(
                array(
                    "key"       =>  "shopbot_woopf_content_id",
                    "value"     =>  "foo", // The bug https://core.trac.wordpress.org/ticket/23268
                    "compare"   =>  "NOT EXISTS"
                )
            );
        }

        $query = new \WC_Product_Query( $args );

        $products = array_filter($query->get_products(), array( $this, "executeFilters" )); 

        return apply_filters( "shopbot_woopf_find_feed_products", $products, $this );
    }

    private function generateXML( $products ) {
        $xw = new \XMLWriter();
        $xw->openMemory();
        $xw->startDocument( "1.0" );
        $xw->startElement( "feed" );
        $xw->startAttribute("xmlns");
        $xw->text("http://www.w3.org/2005/Atom");
        $xw->endAttribute();

        $xw->startAttributeNs( "xmlns", "g", null );
        $xw->text( "http://base.google.com/ns/1.0" );
        $xw->endAttribute();

        $xw->startElement("title");
        $xw->text($this->getName());
        $xw->endElement();

        $xw->startElement( "updated" );
        $xw->text( date( "c" ) );
        $xw->endElement();

        foreach( $products as $product ) {
            $hasCat = false;
            $xw->startElement( "entry" );
            $this->executeRules($product);

            foreach( $this->getMappings() as $field ) {
                $value = sprintf( "%s%s%s", $field->getPrefix(), $field->getValue( $product ), $field->getSuffix() );
                $gfield = $field->getGField()->getSlug();

                if ( $field->getSlug() == "link" ) {
                    $value = sprintf( "%s?%s", $value, http_build_query( $this->getUtm( $product ) ) );
                }
                
                if ( in_array( $gfield, [ "price", "sale_price" ] ) ) {
                    $value = sprintf( "%s %s", $value, get_woocommerce_currency() );
                } else if ( in_array( $gfield, ["shipping_width", "shipping_length" ] ) ) {
                    $value = sprintf( "%s %s", $value, get_option('woocommerce_dimension_unit') );
                } else if ( $gfield == "shipping_weight" ) {
                    $value = sprintf( "%s %s", $value, get_option('woocommerce_weight_unit') );
                }

                if ( !empty( $value ) ) {
                    if ( $xw->startElementNs( "g", $field->getGField()->getSlug(), null ) ) {
                        $xw->text( $value );
                        $xw->endElement();
                    }

                    if ( $field->getSlug() == "google_product_category" ) {
                        $hasCat = true;
                    }
                }
            }

            if ( !$hasCat ) {
                $terms = get_the_terms( $product->get_id(), "product_cat" );
                $mappedCategories = $this->getCategoryMapping();
                $terms = array_filter( $terms, function( $term ) use ( $mappedCategories ) {
                    $ret = array_filter( $mappedCategories, function( $category ) use ( $term ) {
                        return $category[ "term_id" ] == $term->term_id;
                    });

                    return !empty( $ret );
                });

                if ( $terms ) {
                    $mappedCategory = array_filter($mappedCategories, function( $m ) use( $terms ) {
                        return $m[ "term_id" ] = $terms[0]->term_id;
                    });

                    $xw->startElementNs( "g", "google_product_category", null );
                    $xw->text( $mappedCategories[0][ "category" ] );
                    $xw->endElement();
                    
                }
            }
            $xw->endElement();
        }

        $xw->endElement();
        $xw->endDocument();

        return $xw;
    }

    public function generateDump() {
        set_time_limit(0);
        $products = $this->findProducts();
        $filename = strtolower(sprintf( "%s_%d.xml", preg_replace( "/[^\w\d]/", "_", $this->getName() ), $this->getId() ));
        $this->setRunningStatus( true )->save();

        if ( !file_exists( SHOPBOT_WOOPF_DUMP_PATH ) ) {
            mkdir(SHOPBOT_WOOPF_DUMP_PATH);
        }

        $filepath = sprintf( "%s%s", SHOPBOT_WOOPF_DUMP_PATH, $filename );

        $file = fopen($filepath, "w");
        $xw = $this->generateXML($products);

        fwrite($file, $xw->outputMemory() );

        fclose($file);

        $this->setDumpURL( sprintf( "%s%s", SHOPBOT_WOOPF_DUMP_URL, $filename ) );
        $this->setRunningStatus( false );
        $affected_products = array_map( function( $product ) { return $product->get_id(); }, $products );
        $this->setProductIds( $affected_products );
        $this->setLastRefreshed(time())->save();
    }

    public function createGProduct($wc_product, \Google_Service_ShoppingContent_Product &$product) {
        $this->executeRules( $wc_product );

        $product->setOfferId( $wc_product->get_id() );
        foreach( $this->getMappings() as $field ) {
            $gfield = $field->getGField()->getSlug();
            $methodName = sprintf( "set%s", str_replace( "_", "", ucwords( $gfield, "_" ) ) );
            $value = sprintf( "%s%s%s", $field->getPrefix(), $field->getValue( $wc_product ), $field->getSuffix() );

            if ( in_array( $gfield, array( "sale_price", "price" ) ) ) {
                $obj = new \Google_Service_ShoppingContent_Price();
                $obj->setValue( $value );
                $obj->setCurrency(get_woocommerce_currency());
                $value = $obj;
            } else if ( in_array( $gfield, array( "shipping_weight" ) ) ) {
                $obj = new \Google_Service_ShoppingContent_ProductShippingWeight();
                $obj->setValue( $value );
                $obj->setUnit(get_option('woocommerce_weight_unit'));
                $value = $obj;
            } else if ( in_array( $gfield, ["shipping_width", "shipping_length" ] ) ) {
                $obj = new \Google_Service_ShoppingContent_ProductShippingDimension();
                $obj->setValue( $value );
                $obj->setUnit( get_option('woocommerce_dimension_unit') );
                $value = $obj;
            }

            if ( method_exists( $product, $methodName ) ) {
                $product->{$methodName}( $value );
            }

            $product->setTargetCountry($this->getCountry());
            $product->setContentLanguage( strstr( get_locale(), "_", true ) );
            $product->setChannel( "online" );
        }

        return $product;
    }

    public function pushBatchProducts() {
        $products = $this->findProducts();
        if ( !$products ) {
            return;
        }
        
        $entries = array();
        foreach( $products as $index => $product ) {
            $gprd = new \Google_Service_ShoppingContent_Product();
            $this->createGProduct( $product, $gprd );

            $entry = new \Google_Service_ShoppingContent_ProductsCustomBatchRequestEntry();
            $entry->setMethod( "insert" );
            $entry->setBatchId( $index );
            $entry->setProduct( $gprd );
            $entry->setMerchantId( $this->getMerchantId() );

            $entries[] = $entry;
        } 

        $batchRequest = new \Google_Service_ShoppingContent_ProductsCustomBatchRequest();
        $batchRequest->setEntries($entries);

        try {
            $session = new \Google_Service_ShoppingContent( \Shopbot\Engine\Functions\Google\getClient() );
            $batchResponse = $session->products->custombatch($batchRequest);
            $productIds = [];

            foreach( $batchResponse->entries as $index => $entry ) {
                if ( !empty( $entry->getErrors() ) ) {
                   
                } else {
                    $products[$index]->update_meta_data( "shopbot_woopf_content_id", sanitize_text_field( $entry->product->getId() ) );
                    $products[$index]->save_meta_data();
                    $productIds[] = $products[$index]->get_id();
                }
            }

            $this->setProductIds($productIds)->save();
        } catch ( \Exception $e ) {
            wp_mail( $noticeEmail, sprintf( __( "Error pushing Bulk Product using Content API", "shopbot-woopf" ) ), $e->getMessage() );
        }
    }

    public function pushProduct( $product_id ) {
        $product = wc_get_product( $product_id );
        $merchantId = $this->getMerchantId();
        $existingId = $product->get_meta( "shopbot_woopf_content_id", true );

        if ( $this->getPushType() == 1 && $product && $merchantId ) {
            try {
                $session = new \Google_Service_ShoppingContent( \Shopbot\Engine\Functions\Google\getClient() );
                $readyProduct = new \Google_Service_ShoppingContent_Product();
                $this->setRunningStatus( true )->save();

                if ( $existingId ) {
                    $readyProduct->setId( $existingId );
                }
                
                $this->createGProduct($product, $readyProduct );

                $newProduct = $session->products->insert( $this->getMerchantId(), $readyProduct );
                $product->update_meta_data( "shopbot_woopf_content_id", sanitize_text_field( $newProduct->getId() ) );
                $product->save_meta_data();
                $this->setProductId( $product->get_id() );
            } catch ( \Exception $e ) {
                $noticeEmail = $this->getNoticeEmail();
                // $noticeEmail = !$noticeEmail ? get_bloginfo( "admin_email" ) : $noticeEmail;

                wp_mail( $noticeEmail, sprintf( __( "Error pushing Product %s using Content API", "shopbot-woopf" ), $product->get_name() ), $e->getMessage() );
                // print_r( $e->getMessage() ); die;
            } finally {
                $this->setRunningStatus( false )->save();
            }
        }
    }
}