<?php 
namespace Shopbot\ProductFeed;
defined( "ABSPATH" ) || exit;
use function \Shopbot\ProductFeed\Helpers\findFeedMatch;

class App {
    private $l10n;
    public $cap = "manage_options";
    public $google_cat = array();

    public function __construct() {
        $this->includes();
        $this->load_texts();
        $this->actions();
        
        new Ajax($this);
    }

    private function load_texts() {
        $this->l10n = new \StdClass;

        $this->l10n->hello = "Hello World";
        $this->l10n->parentHeader = __( "ShopBot Data Feed", "shopbot-woopf" );
        $this->l10n->linkGoogle = __( "Link Google Account", "shopbot-woopf" );
        $this->l10n->linkingAccount = __( "Linking Google Account...", "shopbot-woopf" );
        $this->l10n->linkGoogleDesc = __( "You are required to authenticate your Google account before using this service", "shopbot-woopf" );
        $this->l10n->no_account_found = __( "No Google Ads account found.", "shopbot-woopf" );
        $this->l10n->chooseAccount = __( "Choose Ad Account", "shopbot-woopf");
        $this->l10n->linkAccount = __( "Link Account", "shopbot-woopf" );
        $this->l10n->linking = __( "Linking...", "shopbot-woopf" );
        $this->l10n->linkError = __( "Unable to link account, please try another account. If error persists, kindly contact plugin support with the below error:", "shopbot-woopf" );
        $this->l10n->active = __( "Active", "shopbot-woopf" );
        $this->l10n->channelName = __( "Channel Name", "shopbot-woopf" );
        $this->l10n->refreshRate = __( "Refresh Rate", "shopbot-woopf" );
        $this->l10n->noChannels = __( "No channels created yet.", "shopbot-woopf");
        $this->l10n->createNew = __( "Create New Channel", "shopbot-woopf" );
        $this->l10n->channelCountry = __( "Channel Country", "shopbot-woopf" );
        $this->l10n->pushType = __( "Push Type", "shopbot-woopf" );
        $this->l10n->refreshRate = __( "Refresh Rate", "shopbot-woopf" );
        $this->l10n->continue = __( "Continue", "shopbot-woopf" );
        $this->l10n->daily = __( "Daily", "shopbot-woopf" );
        $this->l10n->weekly = __( "Weekly", "shopbot-woopf" );
        $this->l10n->hourly = __( "Hourly", "shopbot-woopf" );
        $this->l10n->pushToGoogle = __( "Push to Google", "shopbot-woopf" );
        $this->l10n->pushURL = __( "Generate URL", "shopbot-woopf" );
        $this->l10n->cancel = __( "Cancel", "shopbot-woopf" );
        $this->l10n->googleFields = __( "Google Product Fields", "shopbot-woopf" );
        $this->l10n->prefix = __( "Prefix", "shopbot-woopf" );
        $this->l10n->suffix = __( "Suffix", "shopbot-woopf" );
        $this->l10n->productField = __( "Product Field", "shopbot-woopf" );
        $this->l10n->fieldMapping = __( "Field Mapping", "shopbot-woopf" );
        $this->l10n->goBack = __( "Go back", "shopbot-woopf" );
        $this->l10n->categoryMapping = __( "Category Mapping", "shopbot-woopf" );
        $this->l10n->freeText = __( "Free Text?", "shopbot-woopf" );
        $this->l10n->remove = __( "Remove", "shopbot-woopf" );
        $this->l10n->addNewMapping = __( "Add New Mapping", "shopbot-woopf" );
        $this->l10n->productCategory = __( "Product Category", "shopbot-woopf" );
        $this->l10n->googleCategory = __( "Google Category", "shopbot-woopf" );
        $this->l10n->enterToSearch = __( "Enter category name to search", "shopbot-woopf" );
        $this->l10n->catMappingDesc = sprintf( __( "Map WooCommerce Categories to Google Product Categories. Enter in the below text input to search. More guide can be found here <a href='%s' target='_blank'>here</a>", "shopbot-woopf" ), "https://support.google.com/merchants/answer/6324436?hl=en" );
        $this->l10n->filters = __( "Filters", "shopbot-woopf" );
        $this->l10n->if = __( "If", "shopbot-woopf" );
        $this->l10n->condition = __( "Condition", "shopbot-woopf" );
        $this->l10n->then = __( "Then", "shopbot-woopf" );
        $this->l10n->value = __( "Value", "shopbot-woopf" );
        $this->l10n->include = __( "Include", "shopbot-woopf" );
        $this->l10n->exclude = __( "Exclude", "shopbot-woopf" );
        $this->l10n->newFilter = __( "Add New Filter", "shopbot-woopf" );
        $this->l10n->rules = __( "Rules", "shopbot-woopf" );
        $this->l10n->addRule = __( "Add New Rule", "shopbot-woopf" );
        $this->l10n->becomes = __( "Becomes", "shopbot-woopf" );
        $this->l10n->saveContinue = __( "Save & Continue", "shopbot-woopf" );
        $this->l10n->noRules = __( "No rules created yet", "shopbot-woopf" );
        $this->l10n->googleAnalytics = __( "Google Analytics", "shopbot-woopf" );
        $this->l10n->campaignSource = __( "Campaign Source", "shopbot-woopf" );
        $this->l10n->campaignMedium = __( "Campaign Medium", "shopbot-woopf" );
        $this->l10n->campaignTerm = __( "Campaign Term (use [product_id] to be replaced with product ID)", "shopbot-woopf" );
        $this->l10n->campaignContent = __( "Campaign Content", "shopbot-woopf" );
        $this->l10n->campaignCampaign = __( "Campaign Name", "shopbot-woopf" );
        $this->l10n->createChannel = __( "Create Channel", "shopbot-woopf" );
        $this->l10n->errorCheckRules = __( "Unable to proceed. Check Rules and fill missing fields", "shopbot-woopf" );
        $this->l10n->errorCheckFilters = __( "Unable to proceed. Check Filters and fill missing fields", "shopbot-woopf" );
        $this->l10n->errorCheckMaps = __( "Unable to proceed. Check field mapping and fill missing fields", "shopbot-woopf" );
        $this->l10n->channelSummary = __( "Channel Summary", "shopbot-woopf" );
        $this->l10n->includeProductTypes = __( "Product Types", "shopbot-woopf" );
        $this->l10n->productTypes = __( "Product Types", "shopbot-woopf" );
        $this->l10n->dateCreated = __( "Date Created", "shopbot-woopf" );
        $this->l10n->lastRefreshed = __( "Last Refreshed", "shopbot-woopf" );
        $this->l10n->running = __( "Running", "shopbot-woopf" );
        $this->l10n->paused = __( "Paused", "shopbot-woopf" );  
        $this->l10n->status = __( "Status", "shopbot-woopf" );
        $this->l10n->everyHours = __( "Every %d hours", "shopbot-woopf" );
        $this->l10n->country = __( "Country", "shopbot-woopf" );
        $this->l10n->nextRefresh = __( "Next Refresh Time", "shopbot-woopf" );
        $this->l10n->pause = __( "Pause", "shopbot-woopf" );
        $this->l10n->resume = __( "Resume", "shopbot-woopf" );
        $this->l10n->deleteConfirmation = __( "You are about to delete %s Channel. Continue?", "shopbot-woopf" );
        $this->l10n->edit = __( "Edit", "shopbot-woopf" );
        $this->l10n->editChannel = __( "Edit Channel", "shopbot-woopf" );
        $this->l10n->saveChanges = __( "Save Changes", "shopbot-woopf" );
        $this->l10n->savingChanges = __( "Saving Changes", "shopbot-woopf" );
        $this->l10n->creatingChannel = __( "Creating Channel", "shopbot-woopf" );
        $this->l10n->dumpURL        =   __( "URL/Merchant ID", "shopbot-woopf" );
        $this->l10n->merchantId     =   __( "Merchant ID", "shopbot-woopf" );
        $this->l10n->noticeEmail    =   __( "Notification E-mail", "shopbot-woopf" );
        $this->l10n->runNow         =   __( "Run Now", "shopbot-woopf" );
        $this->l10n->totalProducts  =   __( "Total Products", "shopbot-woopf" );
        $this->l10n->searching      =   __( "Searching...", "shopbot-woopf" );
        $this->l10n->start_here     =   __( 'Start Here', 'shopbot-woopf' );
        $this->l10n->ads_info       =   __( 'Once you have generated your feed, please sign up here to get your product listed on Shopbot', 'shopbot-woopf' );
    }

    public function search_category($q, $fallback = false) {
        $this->google_cat = get_transient( "shopbot_woopf_google_categories" );

        do_action( "shopbot_woopf_before_category_search", $q);

        if ( empty( $this->google_cat ) ) {
            $req = wp_remote_get($fallback ? SHOPBOT_WOOPF_GOOGLE_CAT_URL_FALLBACK : SHOPBOT_WOOPF_GOOGLE_CAT_URL);
            if ( !is_wp_error( $req ) ) {
                if ( $req[ "response" ][ "code" ] != 200 && !$fallback ) {
                    $this->search_category($q, true);
                } else {
                    if ( $req["body"] ) {
                        foreach( explode( "\n", trim($req[ "body" ]) ) as $line ) {
                            if ( $line[0] == "#" )
                                continue;

                            $split = preg_split( "/\s\-\s/", $line);
                            $this->google_cat[$split[0]] = $split[1];
                        }
                        set_transient( "shopbot_woopf_google_categories", $this->google_cat, 900 );
                    } else {
                        $this->google_cat = array();
                        // delete_transient( "shopbot_woopf_google_categories" );
                    }
                    
                }
            }
        }
        
        $ret = array();

        if ( $q && ( strlen($q) >= 3 || is_numeric($q ) ) ) {
            if ( is_numeric($q) && isset( $this->google_cat[$q] ) ) {
                $ret = array( $this->google_cat[$q] );
            } else {
                $ret = preg_grep( sprintf('/%s/i', preg_quote($q)), $this->google_cat );
            }

            do_action( "shopbot_woopf_after_category_search", $ret, $q);
        }

        return apply_filters( "shopbot_woopf_category_search_results", $ret, $q );
    }

    public function includes() {
        // Abstracts
        require_once "abstract/WooField.php";
        require_once "abstract/Condition.php";
        require_once "abstract/Feed.php";

        // Helpers
        require_once SHOPBOT_WOOPF_PATH . "helpers/filters.php";
        require_once SHOPBOT_WOOPF_PATH . "helpers/rules.php";
        require_once SHOPBOT_WOOPF_PATH . "helpers/fields.php";
        require_once SHOPBOT_WOOPF_PATH . "helpers/gfields.php";
        require_once SHOPBOT_WOOPF_PATH . "helpers/feeds.php";
        require_once SHOPBOT_WOOPF_PATH . "helpers/cron.php";

        // Mains
        require_once "Ajax.php";
        require_once "Channels.php";

        do_action( "shopbot_woopf_include_files" );
    }

    public function getInstance($classname) {
        if (empty($this->{$classname})) {
            $this->{$classname} = new $classname($this);
        }

        return $this->{$classname};
    }

    public function has_access() {
        return true;
        // return \Shopbot\Engine\Functions\Access\hasAccess( [ SHOPBOT_GOOGLE_SCOPES[ "content" ] ] );
    }

    public static function activation() {
        // if ( defined( "SHOPBOT_FUNC_PATH" ) ) {
        //     require_once SHOPBOT_FUNC_PATH . "plugins.php";

        //     \Shopbot\Engine\Functions\Plugins\addActive(SHOPBOT_WOOPF_BASE);
        // }

        if ( !defined( "SHOPBOT_WOOPF_DUMP_PATH" ) ) {
            trigger_error( __( "WordPress upload path could not be determined. Please contact plugin support", "shopbot-woopf" ), E_USER_ERROR  );
        }

        if ( !wp_next_scheduled( SHOPBOT_WOOPF_CRON_ACTION ) ) {
            wp_schedule_event( time(), "3mins", SHOPBOT_WOOPF_CRON_ACTION );
        }
    }

    public static function deactivation() {
        // if ( defined( "SHOPBOT_FUNC_PATH" ) ) {
        //     require_once SHOPBOT_FUNC_PATH . "plugins.php";

        //     \Shopbot\Engine\Functions\Plugins\removeActive(SHOPBOT_WOOPF_BASE);
        //     \Shopbot\Engine\Functions\Access\unlink();
        // }

        if ( ( $timestamp = wp_next_scheduled( SHOPBOT_WOOPF_CRON_ACTION ) ) ) {
            wp_unschedule_event($timestamp, SHOPBOT_WOOPF_CRON_ACTION );
        }
    }

    private function actions() {
        add_action( "admin_menu", array( $this, "create_menu" ) );
        add_action( "admin_enqueue_scripts", array( $this, "enqueue" ) );
        add_action( "admin_init", array($this, "check_dep"));

     
            add_action( "init", array($this, "register_post_type" ) );
            add_filter( "manage_edit-product_columns", array( $this, "add_wc_column" ) );
            add_action( "manage_product_posts_custom_column", array($this, "wc_col_val" ), 10, 2 );
            add_filter( "cron_schedules", array($this, "add_cron_schedules" ) );
            add_action( SHOPBOT_WOOPF_CRON_ACTION, "\Shopbot\ProductFeed\Helpers\processSchedules" );
            add_filter( "woocommerce_product_data_store_cpt_get_products_query", array( $this, "append_custom_field" ), 10, 2 );

            add_action( "woocommerce_update_product", "\Shopbot\ProductFeed\Helpers\pushProduct" );
        
    }

    public function append_custom_field( $query, $vars ) {
        if ( !empty( $vars[ "custom_meta" ] ) ) {
            $query[ "meta_query" ] = $vars[ "custom_meta" ];
        }

        return $query;
    }

    public function add_cron_schedules( $schedules ) {
        $schedules[ "3mins" ] = array(
            "interval" => 3*60,
            "display" => __( "Every 3 minutes", "shopbot-woopf" )
        );

        return $schedules;
    }

    public function add_wc_column($cols) {
        $n = [];
        foreach( $cols as $key => $name ) {
            $n[$key] = $name;
            if ( $key == "product_cat" ) {
                $n[ "matching_feeds" ] = __( "Matching Feeds", "shopbot-woopf" );
            }
        }

        if ( !isset( $n[ "matching_feeds" ] ) ) { // In case somehow they deleted the product_cat column
            $n[ "matching_feeds" ] = __( "Matching Feeds", "shopbot-woopf" );
        }

        return $n;
    }

    public function wc_col_val( $colname, $post_id ) {
        if ( $colname == "matching_feeds" ) {
            $matches = array_map( function( $feed ) {
                return $feed->getName();
            }, findFeedMatch( $post_id ) );

            echo implode( ", ", $matches);
        }
    }

    public function register_post_type() {
        register_post_type(SHOPBOT_WOOPF_POST_TYPE, array(
            "public" => false,
            "exclude_from_search" => true,
            "publicly_queryable" => false,
            "show_in_rest" => false,
            "delete_with_user" => false
        ));
    }

    public function check_dep() {
        if ( is_admin() && current_user_can("activate_plugins") && ( !class_exists( 'woocommerce' ) ) ) {
            add_action( "admin_notices", function() {
                ?><div class="error"><p><?php printf( __( "%s plugin requires WooCommerce to be installed and active", "shopbot-woopf" ), SHOPBOT_WOOPF_BASE )?></p></div><?php 
            });

            deactivate_plugins( SHOPBOT_WOOPF_BASE );
            
            if ( isset( $_GET[ "activate" ] ) ) 
                unset( $_GET[ "activate" ] );
        }
    }

    public function enqueue( $hook ) {
        if ( $hook != "toplevel_page_shopbot-woopf" ) {
            return;
        }
        // require_once SHOPBOT_FUNC_PATH . "access.php";
        wp_enqueue_script( "shopbot-woopf", sprintf( "%sjs/shopbot-woopf.js", SHOPBOT_WOOPF_ASSET_URL ), array(), null, true );
        wp_enqueue_style(  "shopbot-woopf", sprintf( "%scss/admin.css", SHOPBOT_WOOPF_ASSET_URL ) );
        wp_localize_script( "shopbot-woopf", "SHOPBOT_WOOPF", array(
            "ajax" => admin_url( "admin-ajax.php" ),
            "l10n" => $this->l10n,
            "hasAccess" => $this->is_ready() ? 1 : 0,
            // "oauthUrl" => \Shopbot\Engine\Functions\Access\generateOAuthURL(array( SHOPBOT_GOOGLE_SCOPES[ "content" ], SHOPBOT_GOOGLE_SCOPES[ "adwords" ] ) )
        ));
    }

    public function is_ready() {
        return $this->has_access(); // && isManagerLinked();
    }
    
    public function get_icon() {
        return base64_encode( file_get_contents( SHOPBOT_WOOPF_PATH . 'asset/image/feedibot-shop.svg' ) );
    }

    public function create_menu() {
        add_menu_page(
            __( "Shopbot Data Feed", "shopbot-woopf" ),
            __( "Shopbot Data Feed", "shopbot-woopf" ),
            "manage_options",
            "shopbot-woopf",
            array( $this, "show_page" ),
            sprintf( 'data:image/svg+xml;base64,%s', $this->get_icon() )
        );
    }

    public function show_page() {
        require_once sprintf( "%stemplate/page.php", SHOPBOT_WOOPF_PATH );
    }
}
