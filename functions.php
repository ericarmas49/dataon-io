<?php
add_action( 'after_setup_theme', 'blankslate_setup' );
function blankslate_setup() {
    load_theme_textdomain( 'blankslate', get_template_directory() . '/languages' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'navigation-widgets' ) );
    add_theme_support( 'woocommerce' );
    global $content_width;
    if ( !isset( $content_width ) ) { $content_width = 1920; }
    register_nav_menus( array( 'main-menu' => esc_html__( 'Main Menu', 'blankslate' ) ) );

    add_theme_support(
        'custom-logo',
        array(
          'height'      => 250,
          'width'       => 250,
          'flex-width'  => true,
          'flex-height' => true,
        )
    );
}

add_action( 'admin_notices', 'blankslate_notice' );
function blankslate_notice() {
    $user_id = get_current_user_id();
    $admin_url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $param = ( count( $_GET ) ) ? '&' : '?';
    if ( !get_user_meta( $user_id, 'blankslate_notice_dismissed_8' ) && current_user_can( 'manage_options' ) )
        echo '<div class="notice notice-info"><p><a href="' . esc_url( $admin_url ), esc_html( $param ) . 'dismiss" class="alignright" style="text-decoration:none"><big>' . esc_html__( 'Ⓧ', 'blankslate' ) . '</big></a>' . wp_kses_post( __( '<big><strong>📝 Thank you for using BlankSlate!</strong></big>', 'blankslate' ) ) . '<br /><br /><a href="https://wordpress.org/support/theme/blankslate/reviews/#new-post" class="button-primary" target="_blank">' . esc_html__( 'Review', 'blankslate' ) . '</a> <a href="https://github.com/tidythemes/blankslate/issues" class="button-primary" target="_blank">' . esc_html__( 'Feature Requests & Support', 'blankslate' ) . '</a> <a href="https://calmestghost.com/donate" class="button-primary" target="_blank">' . esc_html__( 'Donate', 'blankslate' ) . '</a></p></div>';
}

add_action( 'admin_init', 'blankslate_notice_dismissed' );
function blankslate_notice_dismissed() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['dismiss'] ) )
        add_user_meta( $user_id, 'blankslate_notice_dismissed_8', 'true', true );
}

add_action( 'wp_enqueue_scripts', 'blankslate_enqueue' );
function blankslate_enqueue() {
    wp_enqueue_style( 'blankslate-style', get_stylesheet_uri() );
    wp_enqueue_style('bs-header', get_stylesheet_directory_uri() . '/assets/css/header.css');
    wp_enqueue_style('bs-content', get_stylesheet_directory_uri() . '/assets/css/content.css');
    wp_enqueue_script( 'jquery' );
}

add_action( 'wp_footer', 'blankslate_footer' );
function blankslate_footer() {
?>
    <script>
    jQuery(document).ready(function($) {
        var deviceAgent = navigator.userAgent.toLowerCase();
        if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
            $("html").addClass("ios");
            $("html").addClass("mobile");
        }
        if (deviceAgent.match(/(Android)/)) {
            $("html").addClass("android");
            $("html").addClass("mobile");
        }
        if (navigator.userAgent.search("MSIE") >= 0) {
            $("html").addClass("ie");
        }
        else if (navigator.userAgent.search("Chrome") >= 0) {
            $("html").addClass("chrome");
        }
        else if (navigator.userAgent.search("Firefox") >= 0) {
            $("html").addClass("firefox");
        }
        else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
            $("html").addClass("safari");
        }
        else if (navigator.userAgent.search("Opera") >= 0) {
            $("html").addClass("opera");
        }
    });
    </script>
<?php
}

add_filter( 'document_title_separator', 'blankslate_document_title_separator' );
function blankslate_document_title_separator( $sep ) {
    $sep = esc_html( '|' );
    return $sep;
}

add_filter( 'the_title', 'blankslate_title' );
function blankslate_title( $title ) {
    if ( $title == '' ) {
        return esc_html( '...' );
    } else {
        return wp_kses_post( $title );
    }
}

function blankslate_schema_type() {
    $schema = 'https://schema.org/';
    if ( is_single() ) {
        $type = "Article";
    } elseif ( is_author() ) {
        $type = 'ProfilePage';
    } elseif ( is_search() ) {
        $type = 'SearchResultsPage';
    } else {
        $type = 'WebPage';
    }
    echo 'itemscope itemtype="' . esc_url( $schema ) . esc_attr( $type ) . '"';
}

add_filter( 'nav_menu_link_attributes', 'blankslate_schema_url', 10 );
function blankslate_schema_url( $atts ) {
    $atts['itemprop'] = 'url';
    return $atts;
}

if ( !function_exists( 'blankslate_wp_body_open' ) ) {
    function blankslate_wp_body_open() {
    do_action( 'wp_body_open' );
    }
}

add_action( 'wp_body_open', 'blankslate_skip_link', 5 );
function blankslate_skip_link() {
    echo '<a href="#content" class="skip-link screen-reader-text">' . esc_html__( 'Skip to the content', 'blankslate' ) . '</a>';
}

add_filter( 'the_content_more_link', 'blankslate_read_more_link' );
function blankslate_read_more_link() {
    if ( !is_admin() ) {
    return ' <a href="' . esc_url( get_permalink() ) . '" class="more-link">' . sprintf( __( '...%s', 'blankslate' ), '<span class="screen-reader-text">  ' . esc_html( get_the_title() ) . '</span>' ) . '</a>';
    }
}

add_filter( 'excerpt_more', 'blankslate_excerpt_read_more_link' );
function blankslate_excerpt_read_more_link( $more ) {
    if ( !is_admin() ) {
    global $post;
    return ' <a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="more-link">' . sprintf( __( '...%s', 'blankslate' ), '<span class="screen-reader-text">  ' . esc_html( get_the_title() ) . '</span>' ) . '</a>';
    }
}

add_filter( 'big_image_size_threshold', '__return_false' );

add_filter( 'intermediate_image_sizes_advanced', 'blankslate_image_insert_override' );
function blankslate_image_insert_override( $sizes ) {
    unset( $sizes['medium_large'] );
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );
    return $sizes;
}

add_action( 'widgets_init', 'blankslate_widgets_init' );
function blankslate_widgets_init() {
    register_sidebar( 
        array(
            'name' => esc_html__( 'Sidebar Widget Area', 'blankslate' ),
            'id' => 'primary-widget-area',
            'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</li>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );

    register_sidebar( 
        array(
            'name' => esc_html__( 'Social Widget Area', 'blankslate' ),
            'id' => 'social-widget-area',
            'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ) 
    );
}

add_action( 'wp_head', 'blankslate_pingback_header' );
function blankslate_pingback_header() {
    if ( is_singular() && pings_open() ) {
    printf( '<link rel="pingback" href="%s" />' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}

add_action( 'comment_form_before', 'blankslate_enqueue_comment_reply_script' );
function blankslate_enqueue_comment_reply_script() {
    if ( get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
    }
}

function blankslate_custom_pings( $comment ) {
?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url( comment_author_link() ); ?></li>
<?php
}
add_filter( 'get_comments_number', 'blankslate_comment_count', 0 );
function blankslate_comment_count( $count ) {
    if ( !is_admin() ) {
    global $id;
    $get_comments = get_comments( 'status=approve&post_id=' . $id );
    $comments_by_type = separate_comments( $get_comments );
    return count( $comments_by_type['comment'] );
    } else {
    return $count;
    }
}

// CUSTOM ADDED FOR DATAON

/**
 * Register Custom Navigation Walker
 */
function register_navwalker(){
    require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );


function wpb_custom_new_menu() {
    register_nav_menu('footer_block_1',__( 'Footer Block 1' ));
    register_nav_menu('footer_block_2',__( 'Footer Block 2' ));
    register_nav_menu('footer_block_3',__( 'Footer Block 3' ));
    register_nav_menu('footer_block_4',__( 'Footer Block 4' ));
}
  
add_action( 'init', 'wpb_custom_new_menu' );

// ACF blocks
require get_template_directory() . '/modules/acf-blocks/acf-blocks.php';

// Options page
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}

/*|----------------------------------------------------------------------------------------------------
* | CPT, Tax
* |----------------------------------------------------------------------------------------------------  
*/
add_action( 'init', 'dataon_register_cpt_product' );

function dataon_register_cpt_product() {

    $labels = array(
        'name' => __( 'Product', 'product' ),
        'singular_name' => __( 'Product', 'product' ),
        'add_new' => __( 'Add New Product', 'product' ),
        'add_new_item' => __( 'Add new Product', 'product' ),
        'edit_item' => __( 'Edit Product', 'product' ),
        'new_item' => __( 'New Product', 'product' ),
        'view_item' => __( 'View Product', 'product' ),
        'search_items' => __( 'Search Product', 'product' ),
        'not_found' => __( 'Product not Found !', 'product' ),
        'not_found_in_trash' => __( 'Product not found in Trash !', 'product' ),
        'parent_item_colon' => __( 'Product', 'product' ),
        'menu_name' => __( 'Product', 'product' ),
    );
    
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-schedule',
        'public' => true,
        'taxonomies' => array('product_category'),
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title','editor','thumbnail', 'page-attributes', 'excerpt', 'author' )
    );

    register_post_type( 'product', $args );
}


add_action( 'init', 'dataon_register_cpt_documents' );

function dataon_register_cpt_documents() {

    $labels = array(
        'name' => __( 'Document', 'document' ),
        'singular_name' => __( 'Document', 'document' ),
        'add_new' => __( 'Add New Document', 'document' ),
        'add_new_item' => __( 'Add new Document', 'document' ),
        'edit_item' => __( 'Edit Document', 'document' ),
        'new_item' => __( 'New Document', 'document' ),
        'view_item' => __( 'View Document', 'document' ),
        'search_items' => __( 'Search Document', 'document' ),
        'not_found' => __( 'Document not Found !', 'document' ),
        'not_found_in_trash' => __( 'Document not found in Trash !', 'document' ),
        'parent_item_colon' => __( 'Document', 'document' ),
        'menu_name' => __( 'Document', 'document' ),
    );
    
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-schedule',
        'public' => true,
        'taxonomies' => array('product_category'),
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title','editor','thumbnail', 'page-attributes', 'excerpt' )
    );

    register_post_type( 'document', $args );
}




function dataon_document_taxonomy() {  
    register_taxonomy(  
        'document_category',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'document', // post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Document Categories',  //Display name
            'query_var' => true,
            'rewrite'   => array( 'slug' => 'document_cat' ),
            'show_ui' => true,
            'show_in_rest' => true,        
        )  
    );  
}  
add_action( 'init', 'dataon_document_taxonomy');






// Customer stories cpt
add_action( 'init', 'dataon_register_cpt_customer_stories' );

function dataon_register_cpt_customer_stories() {

    $labels = array(
        'name' => __( 'Customer Stories', 'customer-stories' ),
        'singular_name' => __( 'Customer Story', 'customer-stories' ),
        'add_new' => __( 'Add New Customer Story', 'customer-stories' ),
        'add_new_item' => __( 'Add new Customer Story', 'customer-stories' ),
        'edit_item' => __( 'Edit Customer Story', 'customer-stories' ),
        'new_item' => __( 'New Customer Story', 'customer-stories' ),
        'view_item' => __( 'View Customer Story', 'customer-stories' ),
        'search_items' => __( 'Search Customer Stories', 'customer-stories' ),
        'not_found' => __( 'Customer Story not Found !', 'customer-stories' ),
        'not_found_in_trash' => __( 'Customer Story not found in Trash !', 'customer-stories' ),
        'parent_item_colon' => __( 'Customer Story', 'customer-stories' ),
        'menu_name' => __( 'Customer Stories', 'customer-stories' ),
    );
    
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-status',
        'public' => true,
        'taxonomies' => array('customer-stories-category'),
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title','editor','thumbnail', 'page-attributes', 'excerpt', 'author', 'customer-stories')
    );

    register_post_type( 'customer-stories', $args );
}

function dataon_customer_stories_taxonomy() {  
    register_taxonomy(  
        'customer-stories-category',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'customer-stories', // post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Customer Stories Categories',  // Display name
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite'   => array( 'slug' => 'customer-stories-category' )            
        )  
    );  
}  
add_action( 'init', 'dataon_customer_stories_taxonomy');










// Customer stories cpt
add_action( 'init', 'dataon_register_cpt_knowledge_base' );

function dataon_register_cpt_knowledge_base() {

    $labels = array(
        'name' => __( 'Knowledge Base', 'knowledge-base' ),
        'singular_name' => __( 'Knowledge Base', 'knowledge-base' ),
        'add_new' => __( 'Add New Knowledge Base item', 'knowledge-base' ),
        'add_new_item' => __( 'Add new Knowledge Base item', 'knowledge-base' ),
        'edit_item' => __( 'Edit Knowledge Base', 'knowledge-base' ),
        'new_item' => __( 'New Knowledge Base', 'knowledge-base' ),
        'view_item' => __( 'View Knowledge Base', 'knowledge-base' ),
        'search_items' => __( 'Search Knowledge Base', 'knowledge-base' ),
        'not_found' => __( 'Knowledge Base not Found !', 'knowledge-base' ),
        'not_found_in_trash' => __( 'Knowledge Base not found in Trash !', 'knowledge-base' ),
        'parent_item_colon' => __( 'Knowledge Base', 'knowledge-base' ),
        'menu_name' => __( 'Knowledge Base', 'knowledge-base' ),
    );
    
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-status',
        'public' => true,
        'taxonomies' => array('knowledge-base'),
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title','thumbnail', 'page-attributes', 'excerpt', 'author', 'knowledge-base')
    );

    register_post_type( 'knowledge-base', $args );
}

function dataon_knowledge_base_taxonomy() {  
    register_taxonomy(  
        'knowledge-base-categories',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'knowledge-base', // post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Knowledge Base Categories',  // Display name
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite'   => array( 'slug' => 'knowledge-base-categories' )            
        )  
    );  
}  
add_action( 'init', 'dataon_knowledge_base_taxonomy');












// Customer stories cpt
add_action( 'init', 'dataon_register_cpt_videos' );

function dataon_register_cpt_videos() {

    $labels = array(
        'name' => __( 'Videos', 'dataon-videos' ),
        'singular_name' => __( 'Video', 'dataon-videos' ),
        'add_new' => __( 'Add New Video', 'dataon-videos' ),
        'add_new_item' => __( 'Add new Video', 'dataon-videos' ),
        'edit_item' => __( 'Edit Video', 'dataon-videos' ),
        'new_item' => __( 'New Video', 'dataon-videos' ),
        'view_item' => __( 'View Video', 'dataon-videos' ),
        'search_items' => __( 'Search Video', 'dataon-videos' ),
        'not_found' => __( 'Video not Found !', 'dataon-videos' ),
        'not_found_in_trash' => __( 'CVideo not found in Trash !', 'dataon-videos' ),
        'parent_item_colon' => __( 'Video', 'dataon-videos' ),
        'menu_name' => __( 'Videos', 'dataon-videos' ),
    );
    
    $args = array(
        'labels' => $labels,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-status',
        'public' => true,
        'taxonomies' => array('dataon-videos-category'),
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title','thumbnail', 'page-attributes', 'dataon-videos')
    );

    register_post_type( 'dataon-videos', $args );
}

function dataon_videos_taxonomy() {  
    register_taxonomy(  
        'dataon-videos-category',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'dataon-videos', // post type name
        array(  
            'hierarchical' => true,  
            'label' => 'Video Categories',  // Display name
            'query_var' => true,
            'show_in_rest' => true,
            'rewrite'   => array( 'slug' => 'dataon-videos-category' )            
        )  
    );  
}  
add_action( 'init', 'dataon_videos_taxonomy');













// CUSTOM ADMIN PAGE FOR ANALYTICS 

add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {
	add_menu_page( 'Analytics', 'Analytics', 'manage_options', 'admin-analytics.php', 'myplguin_admin_page', 'dashicons-chart-area', 6  );
}

// Enqueue Google Analytics API script
add_action( 'admin_enqueue_scripts', 'analytics_admin_scripts' );
function analytics_admin_scripts( $hook ) {
    if ( $hook != 'toplevel_page_admin-analytics' ) {
        return;
    }
    
    wp_enqueue_script( 'google-identity-services', 'https://accounts.google.com/gsi/client', array(), null, true );
    wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true );
    wp_enqueue_script( 'analytics-config', get_template_directory_uri() . '/js/analytics-config.js', array(), '1.0', true );
    wp_enqueue_script( 'analytics-admin', get_template_directory_uri() . '/js/analytics-admin.js', array( 'jquery', 'google-identity-services', 'chart-js', 'analytics-config' ), '1.0', true );
    wp_enqueue_script( 'analytics-test', get_template_directory_uri() . '/js/analytics-test.js', array( 'jquery', 'analytics-admin' ), '1.0', true );
    
    // Pass AJAX URL and nonce to JavaScript
    wp_localize_script( 'analytics-admin', 'analytics_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'analytics_nonce' )
    ));
}

// Include GA4 Analytics class
require_once get_template_directory() . '/includes/class-ga4-analytics.php';

// Add Google Analytics settings page
add_action('admin_menu', 'add_ga4_settings_page');
function add_ga4_settings_page() {
    add_options_page(
        'Google Analytics Settings',
        'Google Analytics',
        'manage_options',
        'ga4-settings',
        'ga4_settings_page'
    );
}

function ga4_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('ga4_enabled', isset($_POST['ga4_enabled']));
        update_option('ga4_api_key', sanitize_text_field($_POST['ga4_api_key']));
        update_option('ga4_property_id', sanitize_text_field($_POST['ga4_property_id']));
        update_option('ga4_measurement_id', sanitize_text_field($_POST['ga4_measurement_id']));
        update_option('ga4_service_account_email', sanitize_email($_POST['ga4_service_account_email']));
        update_option('ga4_private_key', sanitize_textarea_field($_POST['ga4_private_key']));
        update_option('ga4_project_id', sanitize_text_field($_POST['ga4_project_id']));
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    $ga4_enabled = get_option('ga4_enabled', false);
    $ga4_api_key = get_option('ga4_api_key', '');
    $ga4_property_id = get_option('ga4_property_id', '');
    $ga4_measurement_id = get_option('ga4_measurement_id', '');
    $ga4_service_account_email = get_option('ga4_service_account_email', '');
    $ga4_private_key = get_option('ga4_private_key', '');
    $ga4_project_id = get_option('ga4_project_id', '');
    ?>
    <div class="wrap">
        <h1>Google Analytics 4 Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">Enable GA4 Integration</th>
                    <td>
                        <input type="checkbox" name="ga4_enabled" value="1" <?php checked($ga4_enabled); ?> />
                        <p class="description">Enable live Google Analytics data integration</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="text" name="ga4_api_key" value="<?php echo esc_attr($ga4_api_key); ?>" class="regular-text" />
                        <p class="description">Google Cloud Console API Key</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Property ID</th>
                    <td>
                        <input type="text" name="ga4_property_id" value="<?php echo esc_attr($ga4_property_id); ?>" class="regular-text" />
                        <p class="description">GA4 Property ID (numeric, e.g., 123456789)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Measurement ID</th>
                    <td>
                        <input type="text" name="ga4_measurement_id" value="<?php echo esc_attr($ga4_measurement_id); ?>" class="regular-text" />
                        <p class="description">GA4 Measurement ID (e.g., G-XXXXXXXXXX)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Service Account Email</th>
                    <td>
                        <input type="email" name="ga4_service_account_email" value="<?php echo esc_attr($ga4_service_account_email); ?>" class="regular-text" />
                        <p class="description">Service account email from Google Cloud Console</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Private Key</th>
                    <td>
                        <textarea name="ga4_private_key" rows="5" cols="50" class="large-text"><?php echo esc_textarea($ga4_private_key); ?></textarea>
                        <p class="description">Private key from service account JSON file (include -----BEGIN PRIVATE KEY----- and -----END PRIVATE KEY-----)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Project ID</th>
                    <td>
                        <input type="text" name="ga4_project_id" value="<?php echo esc_attr($ga4_project_id); ?>" class="regular-text" />
                        <p class="description">Google Cloud Project ID</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <?php if ($ga4_enabled && !empty($ga4_property_id)): ?>
        <h2>Test Connection</h2>
        <p>
            <button type="button" id="test-ga4-connection" class="button">Test GA4 Connection</button>
            <span id="connection-status"></span>
        </p>
        <script>
        jQuery(document).ready(function($) {
            $('#test-ga4-connection').click(function() {
                var button = $(this);
                var status = $('#connection-status');
                
                button.prop('disabled', true).text('Testing...');
                status.html('');
                
                $.post(ajaxurl, {
                    action: 'test_ga4_connection',
                    nonce: '<?php echo wp_create_nonce('ga4_test_nonce'); ?>'
                }, function(response) {
                    if (response.success) {
                        status.html('<span style="color: green;">✅ ' + response.data.message + '</span>');
                    } else {
                        status.html('<span style="color: red;">❌ ' + response.data.message + '</span>');
                    }
                }).fail(function() {
                    status.html('<span style="color: red;">❌ Connection test failed</span>');
                }).always(function() {
                    button.prop('disabled', false).text('Test GA4 Connection');
                });
            });
        });
        </script>
        <?php endif; ?>
    </div>
    <?php
}

// AJAX handler for testing GA4 connection
add_action('wp_ajax_test_ga4_connection', 'test_ga4_connection');
function test_ga4_connection() {
    check_ajax_referer('ga4_test_nonce', 'nonce');
    
    $ga4 = new GA4_Analytics();
    $result = $ga4->test_connection();
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}

// AJAX handler for analytics data
add_action( 'wp_ajax_get_analytics_data', 'get_analytics_data' );
function get_analytics_data() {
    check_ajax_referer( 'analytics_nonce', 'nonce' );
    
    $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'day';
    
    // Check if GA4 is configured
    $ga4_enabled = get_option('ga4_enabled', false);
    $ga4_property_id = get_option('ga4_property_id', '');
    
    if ($ga4_enabled && !empty($ga4_property_id)) {
        // Try to get live data from GA4
        $ga4 = new GA4_Analytics();
        $live_data = $ga4->get_analytics_data($period);
        
        if ($live_data && $live_data['connection_status'] === 'connected') {
            wp_send_json_success($live_data);
        } else {
            // Return error if GA4 fails - NO sample data
            error_log('GA4 Analytics failed, returning error');
            wp_send_json_error('Failed to connect to Google Analytics. Please check your GA4 configuration.');
        }
    } else {
        // Return error if GA4 not configured - NO sample data
        wp_send_json_error('Google Analytics 4 not configured. Please set up GA4 credentials.');
    }
}

// Sample data generation function removed - only real GA4 data will be used

function myplguin_admin_page(){
	?>

<style>
.analytics-dashboard {
    padding: 20px;
    max-width: 1600px;
}

.analytics-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.analytics-header h1 {
    margin: 0;
    font-size: 2.5em;
    font-weight: 300;
}

.analytics-header p {
    margin: 10px 0 0 0;
    opacity: 0.9;
}

.connection-status {
    background: rgba(255, 255, 255, 0.2);
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 0.9em;
}

.connection-status.connected {
    background: rgba(40, 167, 69, 0.3);
}

.connection-status.sample {
    background: rgba(255, 193, 7, 0.3);
}

.connection-status.error {
    background: rgba(220, 53, 69, 0.3);
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.metric-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #667eea;
    transition: transform 0.2s ease;
    position: relative;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.metric-card.real-time {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.metric-value {
    font-size: 2.5em;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.metric-card.real-time .metric-value {
    color: #28a745;
}

.metric-label {
    color: #666;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.metric-change {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 0.8em;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
}

.metric-change.positive {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.metric-change.negative {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.tab-navigation {
    background: white;
    border-radius: 10px;
    padding: 0;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    overflow-x: auto;
}

.tab-button {
    background: none;
    border: none;
    padding: 15px 25px;
    cursor: pointer;
    font-size: 1em;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.tab-button:hover {
    background: #f8f9fa;
    color: #333;
}

.tab-button.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: #f8f9fa;
}

.tab-content {
    display: none;
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.tab-content.active {
    display: block;
}

.period-filters {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.period-button {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 8px 16px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 0.9em;
    transition: all 0.3s ease;
}

.period-button:hover {
    background: #e9ecef;
}

.period-button.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.chart-container {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.chart-container h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.3em;
}

.chart-canvas {
    width: 100% !important;
    height: 300px !important;
    max-height: 300px !important;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.insight-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #667eea;
}

.insight-card.high-impact {
    border-left-color: #dc3545;
}

.insight-card.medium-impact {
    border-left-color: #ffc107;
}

.insight-card.low-impact {
    border-left-color: #28a745;
}

.insight-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.insight-description {
    color: #666;
    font-size: 0.9em;
    line-height: 1.4;
}

.activity-feed {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #667eea;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 0.9em;
}

.activity-content {
    flex: 1;
}

.activity-event {
    font-weight: 500;
    color: #333;
    margin-bottom: 2px;
}

.activity-time {
    font-size: 0.8em;
    color: #666;
}

.loading {
    text-align: center;
    padding: 50px;
    color: #666;
}

.loading::after {
    content: '';
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-left: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.error-message {
    background: #fee;
    color: #c33;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
    border-left: 4px solid #c33;
}

.real-time-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
    margin-right: 8px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .metrics-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .analytics-header {
        flex-direction: column;
        text-align: center;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
    
    .tab-button {
        border-bottom: none;
        border-right: 3px solid transparent;
    }
    
    .tab-button.active {
        border-right-color: #667eea;
        border-bottom-color: transparent;
    }
}

/* Unavailable data styling */
.unavailable-data {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #fff5f5;
    border: 2px solid #dc3545;
    border-radius: 5px;
    margin: 10px 0;
}

.unavailable-icon {
    font-size: 20px;
    margin-right: 10px;
    color: #dc3545;
}

.unavailable-message {
    color: #dc3545;
    font-weight: bold;
    margin: 0;
}

.unavailable-note {
    color: #6c757d;
    font-style: italic;
    margin: 5px 0 0 0;
    font-size: 12px;
}

.no-data {
    padding: 20px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    border-radius: 5px;
    margin: 10px 0;
}
</style>

<div class="wrap analytics-dashboard">
    <div class="analytics-header">
        <div>
            <h1>Analytics Dashboard</h1>
            <p>Comprehensive insights and real-time data</p>
        </div>
        <div class="connection-status" id="connection-status">
            <span>🔄 Loading...</span>
        </div>
    </div>

    <!-- 
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-value" id="page-views-value">--</div>
            <div class="metric-label">Page Views</div>
            <div class="metric-change positive" id="page-views-change">+15.2%</div>
        </div>
        <div class="metric-card real-time">
            <div class="metric-value" id="real-time-users">--</div>
            <div class="metric-label">
                <span class="real-time-indicator"></span>
                Active Users
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-value" id="unique-users">--</div>
            <div class="metric-label">Unique Users</div>
            <div class="metric-change positive" id="unique-users-change">+8.5%</div>
        </div>
        <div class="metric-card">
            <div class="metric-value" id="bounce-rate">--</div>
            <div class="metric-label">Bounce Rate</div>
            <div class="metric-change negative" id="bounce-rate-change">-2.1%</div>
        </div>
    </div>
    -->

    <div class="tab-navigation">
        <button class="tab-button active" data-tab="overview">Overview</button>
        <button class="tab-button" data-tab="pages">Top Pages</button>
        <button class="tab-button" data-tab="traffic">Traffic Sources</button>
        <button class="tab-button" data-tab="downloads">PDF Downloads</button>
        <button class="tab-button" data-tab="insights">Trending Insights</button>
        <button class="tab-button" data-tab="activity">Recent Activity</button>
    </div>

    <div id="analytics-content">
        <!-- Completely empty - being rebuilt -->
    </div>
</div>

<script>
// All analytics JavaScript completely removed - being rebuilt from scratch
</script>

<?php 
}


add_action('wp_ajax_filter_documents', 'filter_documents');
add_action('wp_ajax_nopriv_filter_documents', 'filter_documents');


add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'knowledge-base') return false;
    return $current_status;
}
function misha_my_load_more_scripts() {
 
	global $wp_query; 
 
	// In most cases it is already included on the page and this line can be removed
	wp_enqueue_script('jquery');
 
	// register our main script but do not enqueue it yet
	wp_register_script( 'my_loadmore', get_stylesheet_directory_uri() . '/myloadmore.js', array('jquery') );
 
	// now the most interesting part
	// we have to pass parameters to myloadmore.js script but we can get the parameters values only in PHP
	// you can define variables directly in your HTML but I decided that the most proper way is wp_localize_script()
	wp_localize_script( 'my_loadmore', 'misha_loadmore_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
		'max_page' => $wp_query->max_num_pages
	) );
 
 	wp_enqueue_script( 'my_loadmore' );
}
 
add_action( 'wp_enqueue_scripts', 'misha_my_load_more_scripts' );






function misha_loadmore_ajax_handler(){
 

	// prepare our arguments for the query
	$args = json_decode( stripslashes( $_POST['query'] ), true );
	$args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
	$args['post_status'] = 'publish';
 
	// it is always better to use WP_Query but not here
	query_posts( $args );
 
	if( have_posts() ) :
 
		// run the loop
		while( have_posts() ): the_post();
 
			// look into your theme code how the posts are inserted, but you can use your own HTML of course
			// do you remember? - my example is adapted for Twenty Seventeen theme
			// get_template_part( 'template-parts/post/content', get_post_format() );
			// for the test purposes comment the line above and uncomment the below one
			// the_title();
           //  echo "testing"; ?>


        <article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 post'); ?>>
            <div class="row">
                <div class="col-12">
                    <?php
                    $postID = get_the_ID(); 
                    $postImage = get_the_post_thumbnail_url($postID, 'full');
                    if(empty($postImage)) {
                        $postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
                    }
                    ?>

                    <div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
                </div>
                <div class="col-12 col-post-content">
                    <header>
                    <!-- <div class="post-cat">
                        <?php $categories = get_the_category();
                            if ( ! empty( $categories ) ) { ?>
                            <h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
                        <?php } ?>
                    </div> -->
                    <h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                            <?php // edit_post_link(); ?>
                            <a href="<?php the_permalink(); ?>">Read more</a>
                        </header>
                        <?php 
                    //	echo get_the_excerpt(); 
                        ?>
                        <?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
                </div>
            </div>
        </article>

		<?php endwhile;
 
	endif;
	die; // here we exit the script and even no wp_reset_query() required!
}
 
 
 
add_action('wp_ajax_loadmore', 'misha_loadmore_ajax_handler'); // wp_ajax_{action}
add_action('wp_ajax_nopriv_loadmore', 'misha_loadmore_ajax_handler'); // wp_ajax_nopriv_{action}


function filter_projects() {

    $catSlug = $_POST['category'];

    if ($catSlug === 'blog'){
        $ajaxposts = new WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 100,
            'offset' => 2,
        ]);
    } else {
        $ajaxposts = new WP_Query([
              'posts_per_page' => -1,
              'tax_query' => array(
                array (
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => $catSlug,
                )),
              'orderby' => 'menu_order', 
              'order' => 'desc',
            ]);
    }

  
    if($ajaxposts->have_posts()) {
      while($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $response .= get_template_part('templates/category-ajax-posts');
      endwhile;
    } else {
      $response = 'empty';
    }
  
    echo $response;
    exit;
  }
  add_action('wp_ajax_filter_projects', 'filter_projects');
  add_action('wp_ajax_nopriv_filter_projects', 'filter_projects');





  function filter_customer_stories() {

    $catSlug = $_POST['category'];

    if ($catSlug === 'blog'){
        $ajaxposts = new WP_Query([
            'post_type'      => 'customer-stories',
            'posts_per_page' => 100,
            'offset' => 2,
        ]);
    } else {
        $ajaxposts = new WP_Query([
              'posts_per_page' => -1,
              'tax_query' => array(
                array (
                    'taxonomy' => 'customer-stories-category',
                    'field' => 'slug',
                    'terms' => $catSlug,
                )),
              'orderby' => 'menu_order', 
              'order' => 'desc',
            ]);
    }

  
    if($ajaxposts->have_posts()) {
      while($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $response .= get_template_part('templates/customerStories-ajax');
      endwhile;
    } else {
      $response = 'empty';
    }
  
    echo $response;
    exit;
  }
  add_action('wp_ajax_filter_customer_stories', 'filter_customer_stories');
  add_action('wp_ajax_nopriv_filter_customer_stories', 'filter_customer_stories');



  function filter_videos() {

    $catSlug = $_POST['category'];

    if ($catSlug === 'blog'){
        $ajaxposts = new WP_Query([
            'post_type'      => 'dataon-videos',
            'posts_per_page' => 100,
            'offset' => 2,
            'order' => 'desc',
        ]);
    } else {
        $ajaxposts = new WP_Query([
            'posts_per_page' => -1,
            'tax_query' => array(
                array (
                    'taxonomy' => 'dataon-videos-category',
                    'field' => 'slug',
                    'terms' => $catSlug,
                )
            ),
            'orderby' => 'menu_order', 
            'order' => 'desc',
        ]);
    }
    $response = '';
  
    if($ajaxposts->have_posts()) {
      while($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $response .= get_template_part('templates/category-ajax');
      endwhile;
    } else {
      $response = 'empty';
    }
  
    echo $response;
    exit;
  }
  add_action('wp_ajax_filter_videos', 'filter_videos');
  add_action('wp_ajax_nopriv_filter_videos', 'filter_videos');



function filter_documents() {

    $catSlug = $_POST['category'];

    if ($catSlug === 'blog'){
        $ajaxposts = new WP_Query([
            'post_type'      => 'document',
            'posts_per_page' => -1,
        ]);
    } else {
        $ajaxposts = new WP_Query([
        'posts_per_page' => -1,
        'tax_query' => array(
            array (
                'taxonomy' => 'document_category',
                'field' => 'slug',
                'terms' => $catSlug,
            )),
        'orderby' => 'menu_order', 
        'order' => 'desc',
        ]);
    }
    
    $response = '';
  
    if($ajaxposts->have_posts()) {
      while($ajaxposts->have_posts()) : $ajaxposts->the_post();
        $response .= get_template_part('templates/category-ajax-doc');
      endwhile;
    } else {
      $response = 'empty';
    }
  
    echo $response;
    exit;
  }
  add_action('wp_ajax_filter_documents', 'filter_documents');
  add_action('wp_ajax_nopriv_filter_documents', 'filter_documents');


// ============================================================================
// AI-POWERED INDEXING MONITORING SYSTEM
// ============================================================================

/**
 * Create database tables for indexing monitoring
 */
function create_indexing_monitoring_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    
    // Table for page indexing history
    $table_name = $wpdb->prefix . 'indexing_history';
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        post_url varchar(500) NOT NULL,
        post_type varchar(50) NOT NULL,
        post_title varchar(500) NOT NULL,
        indexed_status varchar(50) NOT NULL,
        last_check_date datetime DEFAULT CURRENT_TIMESTAMP,
        last_crawl_date datetime NULL,
        coverage_state varchar(100) NULL,
        indexing_issues text NULL,
        health_score int(3) DEFAULT 0,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY indexed_status (indexed_status),
        KEY last_check_date (last_check_date)
    ) $charset_collate;";
    
    // Table for indexing statistics
    $table_stats = $wpdb->prefix . 'indexing_stats';
    $sql_stats = "CREATE TABLE IF NOT EXISTS $table_stats (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        check_date datetime DEFAULT CURRENT_TIMESTAMP,
        total_pages int(11) NOT NULL,
        indexed_pages int(11) NOT NULL,
        not_indexed_pages int(11) NOT NULL,
        errors_pages int(11) DEFAULT 0,
        avg_health_score decimal(5,2) DEFAULT 0,
        notes text NULL,
        PRIMARY KEY  (id),
        KEY check_date (check_date)
    ) $charset_collate;";
    
    // Table for page health issues
    $table_issues = $wpdb->prefix . 'indexing_issues';
    $sql_issues = "CREATE TABLE IF NOT EXISTS $table_issues (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        issue_type varchar(100) NOT NULL,
        issue_severity varchar(20) NOT NULL,
        issue_description text NOT NULL,
        ai_recommendation text NULL,
        detected_date datetime DEFAULT CURRENT_TIMESTAMP,
        resolved tinyint(1) DEFAULT 0,
        resolved_date datetime NULL,
        PRIMARY KEY  (id),
        KEY post_id (post_id),
        KEY issue_type (issue_type),
        KEY resolved (resolved)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    dbDelta($sql_stats);
    dbDelta($sql_issues);
}
add_action('after_switch_theme', 'create_indexing_monitoring_tables');
register_activation_hook(__FILE__, 'create_indexing_monitoring_tables');

/**
 * AI-Powered Page Health Checker
 * Analyzes pages for common indexing issues
 */
function ai_analyze_page_health($post_id) {
    $post = get_post($post_id);
    if (!$post) return null;
    
    $health_score = 100;
    $issues = array();
    
    // 1. Check robots meta
    $robots_meta = get_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', true);
    if ($robots_meta == '1') {
        $issues[] = array(
            'type' => 'robots_noindex',
            'severity' => 'critical',
            'description' => 'Page has noindex meta tag',
            'recommendation' => 'Remove noindex meta tag if you want this page indexed'
        );
        $health_score -= 50;
    }
    
    // 2. Check content length
    $content = strip_tags($post->post_content);
    $word_count = str_word_count($content);
    if ($word_count < 300) {
        $issues[] = array(
            'type' => 'thin_content',
            'severity' => 'high',
            'description' => "Content is too thin ($word_count words)",
            'recommendation' => 'Add more valuable content (aim for 300+ words). Google often deindexes thin content pages.'
        );
        $health_score -= 30;
    }
    
    // 3. Check for duplicate content indicators
    $title_length = strlen($post->post_title);
    if ($title_length < 30) {
        $issues[] = array(
            'type' => 'short_title',
            'severity' => 'medium',
            'description' => 'Title is too short',
            'recommendation' => 'Use descriptive titles (30-60 characters) for better SEO'
        );
        $health_score -= 10;
    }
    
    // 4. Check if page has images
    $has_images = has_post_thumbnail($post_id) || preg_match('/<img[^>]+>/i', $post->post_content);
    if (!$has_images) {
        $issues[] = array(
            'type' => 'no_images',
            'severity' => 'low',
            'description' => 'Page has no images',
            'recommendation' => 'Add relevant images to improve user experience and engagement signals'
        );
        $health_score -= 5;
    }
    
    // 5. Check internal links
    $internal_links = preg_match_all('/<a[^>]+href=["\']' . preg_quote(home_url(), '/') . '[^"\']*["\']/i', $post->post_content);
    if ($internal_links < 2) {
        $issues[] = array(
            'type' => 'few_internal_links',
            'severity' => 'medium',
            'description' => 'Page has few internal links',
            'recommendation' => 'Add 2-5 relevant internal links to improve site structure and crawlability'
        );
        $health_score -= 10;
    }
    
    // 6. Check page speed indicators (large images)
    if (preg_match_all('/<img[^>]+>/i', $post->post_content, $matches) > 10) {
        $issues[] = array(
            'type' => 'many_images',
            'severity' => 'low',
            'description' => 'Page has many images (potential speed issue)',
            'recommendation' => 'Optimize images and implement lazy loading'
        );
        $health_score -= 5;
    }
    
    // 7. Check canonical URL
    $canonical = get_post_meta($post_id, '_yoast_wpseo_canonical', true);
    if ($canonical && $canonical !== get_permalink($post_id)) {
        $issues[] = array(
            'type' => 'canonical_mismatch',
            'severity' => 'high',
            'description' => 'Canonical URL points to different page',
            'recommendation' => 'This page tells Google to index a different URL instead. Remove or correct canonical URL.'
        );
        $health_score -= 40;
    }
    
    // 8. Check if page is in sitemap
    // This would require sitemap parsing - simplified check
    if (get_post_meta($post_id, '_yoast_wpseo_sitemap-include', true) === 'never') {
        $issues[] = array(
            'type' => 'excluded_sitemap',
            'severity' => 'critical',
            'description' => 'Page excluded from XML sitemap',
            'recommendation' => 'Include page in sitemap so search engines can discover it'
        );
        $health_score -= 45;
    }
    
    return array(
        'health_score' => max(0, $health_score),
        'issues' => $issues,
        'total_issues' => count($issues)
    );
}

/**
 * Save page health analysis to database
 */
function save_page_indexing_status($post_id, $indexed_status, $health_data = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'indexing_history';
    $table_issues = $wpdb->prefix . 'indexing_issues';
    
    $post = get_post($post_id);
    if (!$post) return;
    
    // Get health analysis
    if (!$health_data) {
        $health_data = ai_analyze_page_health($post_id);
    }
    
    // Save/update indexing history
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT id FROM $table_name WHERE post_id = %d ORDER BY last_check_date DESC LIMIT 1",
        $post_id
    ));
    
    $data = array(
        'post_id' => $post_id,
        'post_url' => get_permalink($post_id),
        'post_type' => $post->post_type,
        'post_title' => $post->post_title,
        'indexed_status' => $indexed_status,
        'last_check_date' => current_time('mysql'),
        'health_score' => $health_data['health_score'],
        'indexing_issues' => json_encode($health_data['issues'])
    );
    
    if ($existing) {
        $wpdb->update($table_name, $data, array('id' => $existing->id));
    } else {
        $wpdb->insert($table_name, $data);
    }
    
    // Save individual issues
    if (!empty($health_data['issues'])) {
        // Clear old unresolved issues for this post
        $wpdb->update(
            $table_issues,
            array('resolved' => 1, 'resolved_date' => current_time('mysql')),
            array('post_id' => $post_id, 'resolved' => 0)
        );
        
        // Add new issues
        foreach ($health_data['issues'] as $issue) {
            $wpdb->insert($table_issues, array(
                'post_id' => $post_id,
                'issue_type' => $issue['type'],
                'issue_severity' => $issue['severity'],
                'issue_description' => $issue['description'],
                'ai_recommendation' => $issue['recommendation'],
                'detected_date' => current_time('mysql'),
                'resolved' => 0
            ));
        }
    }
}

/**
 * Scan all published pages and check their indexing health
 */
function scan_all_pages_indexing_health() {
    global $wpdb;
    
    $post_types = array('post', 'page', 'product', 'knowledge-base', 'documents', 'customer-stories', 'videos');
    
    $args = array(
        'post_type' => $post_types,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
    );
    
    $posts = get_posts($args);
    $total_pages = count($posts);
    $indexed_count = 0;
    $not_indexed_count = 0;
    $total_health = 0;
    
    foreach ($posts as $post_id) {
        $health_data = ai_analyze_page_health($post_id);
        
        // Determine if page should be indexed based on health score
        $indexed_status = 'indexed'; // Default assumption
        if ($health_data['health_score'] < 50) {
            $indexed_status = 'at_risk';
            $not_indexed_count++;
        } else {
            $indexed_count++;
        }
        
        save_page_indexing_status($post_id, $indexed_status, $health_data);
        $total_health += $health_data['health_score'];
    }
    
    // Save statistics
    $table_stats = $wpdb->prefix . 'indexing_stats';
    $wpdb->insert($table_stats, array(
        'check_date' => current_time('mysql'),
        'total_pages' => $total_pages,
        'indexed_pages' => $indexed_count,
        'not_indexed_pages' => $not_indexed_count,
        'avg_health_score' => $total_pages > 0 ? round($total_health / $total_pages, 2) : 0
    ));
    
    return array(
        'total' => $total_pages,
        'indexed' => $indexed_count,
        'not_indexed' => $not_indexed_count,
        'avg_health' => $total_pages > 0 ? round($total_health / $total_pages, 2) : 0
    );
}

/**
 * Check Google Search Console API for real indexing status
 * Note: Requires Google Search Console API credentials
 */
function check_gsc_indexing_status($post_url = null) {
    // This requires Google Search Console API setup
    // For now, return a placeholder that can be integrated with GSC API
    
    $gsc_credentials = get_option('gsc_api_credentials');
    if (!$gsc_credentials) {
        return array(
            'status' => 'error',
            'message' => 'Google Search Console API not configured. Set up in Settings → Indexing Monitor.'
        );
    }
    
    // Placeholder for GSC API integration
    // Real implementation would use Google_Service_SearchConsole
    return array(
        'status' => 'pending',
        'message' => 'GSC API integration pending. Using local health analysis for now.'
    );
}

/**
 * Add admin menu for Indexing Monitor
 */
function add_indexing_monitor_menu() {
    add_menu_page(
        'Indexing Monitor',
        'Indexing Monitor',
        'manage_options',
        'indexing-monitor',
        'render_indexing_monitor_page',
        'dashicons-chart-line',
        30
    );
    
    add_submenu_page(
        'indexing-monitor',
        'Health Issues',
        'Health Issues',
        'manage_options',
        'indexing-issues',
        'render_indexing_issues_page'
    );
    
    add_submenu_page(
        'indexing-monitor',
        'Settings',
        'Settings',
        'manage_options',
        'indexing-settings',
        'render_indexing_settings_page'
    );
}
add_action('admin_menu', 'add_indexing_monitor_menu');

/**
 * Render main indexing monitor dashboard
 */
function render_indexing_monitor_page() {
    global $wpdb;
    
    // Handle manual scan trigger
    if (isset($_POST['trigger_scan']) && check_admin_referer('indexing_scan_action', 'indexing_scan_nonce')) {
        $results = scan_all_pages_indexing_health();
        echo '<div class="notice notice-success"><p>Scan completed! Total: ' . $results['total'] . ', Healthy: ' . $results['indexed'] . ', At Risk: ' . $results['not_indexed'] . '</p></div>';
    }
    
    // Get latest statistics
    $table_stats = $wpdb->prefix . 'indexing_stats';
    $latest_stats = $wpdb->get_row("SELECT * FROM $table_stats ORDER BY check_date DESC LIMIT 1");
    
    // Get historical data (last 30 days)
    $historical_data = $wpdb->get_results("
        SELECT DATE(check_date) as date, indexed_pages, not_indexed_pages, total_pages 
        FROM $table_stats 
        WHERE check_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY check_date ASC
    ");
    
    // Get pages with issues
    $table_history = $wpdb->prefix . 'indexing_history';
    $problem_pages = $wpdb->get_results("
        SELECT * FROM $table_history 
        WHERE health_score < 70 
        ORDER BY health_score ASC 
        LIMIT 20
    ");
    
    ?>
    <div class="wrap">
        <h1>🔍 AI-Powered Indexing Monitor</h1>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>📊 Current Indexing Status</h2>
            
            <?php if ($latest_stats): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                    <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6;">
                        <div style="font-size: 14px; color: #64748b;">Total Pages</div>
                        <div style="font-size: 32px; font-weight: bold; color: #1e293b;"><?php echo $latest_stats->total_pages; ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">Last checked: <?php echo date('M j, Y g:i A', strtotime($latest_stats->check_date)); ?></div>
                    </div>
                    
                    <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 4px solid #22c55e;">
                        <div style="font-size: 14px; color: #64748b;">Healthy Pages</div>
                        <div style="font-size: 32px; font-weight: bold; color: #15803d;"><?php echo $latest_stats->indexed_pages; ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                            <?php echo round(($latest_stats->indexed_pages / $latest_stats->total_pages) * 100, 1); ?>% of total
                        </div>
                    </div>
                    
                    <div style="background: #fef2f2; padding: 20px; border-radius: 8px; border-left: 4px solid #ef4444;">
                        <div style="font-size: 14px; color: #64748b;">At Risk</div>
                        <div style="font-size: 32px; font-weight: bold; color: #dc2626;"><?php echo $latest_stats->not_indexed_pages; ?></div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                            <?php echo round(($latest_stats->not_indexed_pages / $latest_stats->total_pages) * 100, 1); ?>% of total
                        </div>
                    </div>
                    
                    <div style="background: #fefce8; padding: 20px; border-radius: 8px; border-left: 4px solid #eab308;">
                        <div style="font-size: 14px; color: #64748b;">Avg Health Score</div>
                        <div style="font-size: 32px; font-weight: bold; color: #ca8a04;"><?php echo $latest_stats->avg_health_score; ?>/100</div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px;">
                            <?php 
                            if ($latest_stats->avg_health_score >= 80) echo '✅ Excellent';
                            elseif ($latest_stats->avg_health_score >= 60) echo '⚠️ Good';
                            else echo '🚨 Needs Attention';
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($historical_data && count($historical_data) > 1): ?>
                    <div style="margin-top: 30px;">
                        <h3>📈 30-Day Indexing Trend</h3>
                        <canvas id="indexingChart" style="max-height: 300px;"></canvas>
                        <script>
                        jQuery(document).ready(function($) {
                            const ctx = document.getElementById('indexingChart');
                            if (ctx) {
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: <?php echo json_encode(array_map(function($d) { return date('M j', strtotime($d->date)); }, $historical_data)); ?>,
                                        datasets: [{
                                            label: 'Healthy Pages',
                                            data: <?php echo json_encode(array_map(function($d) { return $d->indexed_pages; }, $historical_data)); ?>,
                                            borderColor: '#22c55e',
                                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                            tension: 0.4
                                        }, {
                                            label: 'At Risk Pages',
                                            data: <?php echo json_encode(array_map(function($d) { return $d->not_indexed_pages; }, $historical_data)); ?>,
                                            borderColor: '#ef4444',
                                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                            tension: 0.4
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: true,
                                        plugins: {
                                            legend: {
                                                position: 'bottom'
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });
                            }
                        });
                        </script>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div style="background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <p><strong>⚠️ No scan data available yet.</strong></p>
                    <p>Click "Run Full Scan" below to analyze all your pages.</p>
                </div>
            <?php endif; ?>
            
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field('indexing_scan_action', 'indexing_scan_nonce'); ?>
                <button type="submit" name="trigger_scan" class="button button-primary button-hero">
                    🔄 Run Full Scan (Analyze All Pages)
                </button>
                <p class="description">This will analyze all published pages for indexing issues. May take a few minutes for large sites.</p>
            </form>
        </div>
        
        <?php if (!empty($problem_pages)): ?>
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2>🚨 Pages Needing Attention (Lowest Health Scores)</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Page Title</th>
                        <th>Type</th>
                        <th>Health Score</th>
                        <th>Status</th>
                        <th>Issues Found</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($problem_pages as $page): 
                        $issues = json_decode($page->indexing_issues, true);
                        $health_color = $page->health_score >= 70 ? '#22c55e' : ($page->health_score >= 50 ? '#f59e0b' : '#ef4444');
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($page->post_title); ?></strong>
                            <div style="font-size: 12px; color: #64748b;">
                                <a href="<?php echo esc_url($page->post_url); ?>" target="_blank">View Page</a> | 
                                <a href="<?php echo get_edit_post_link($page->post_id); ?>">Edit</a>
                            </div>
                        </td>
                        <td><?php echo esc_html($page->post_type); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                    <div style="width: <?php echo $page->health_score; ?>%; background: <?php echo $health_color; ?>; height: 100%;"></div>
                                </div>
                                <strong style="color: <?php echo $health_color; ?>;"><?php echo $page->health_score; ?></strong>
                            </div>
                        </td>
                        <td>
                            <?php if ($page->indexed_status == 'indexed'): ?>
                                <span style="background: #dcfce7; color: #15803d; padding: 4px 8px; border-radius: 4px; font-size: 12px;">✓ Indexed</span>
                            <?php else: ?>
                                <span style="background: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 4px; font-size: 12px;">⚠ At Risk</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo count($issues); ?> issues</td>
                        <td>
                            <a href="admin.php?page=indexing-issues&post_id=<?php echo $page->post_id; ?>" class="button button-small">
                                View Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div style="background: #eff6ff; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <h3>💡 AI Recommendations</h3>
            <ul style="line-height: 1.8;">
                <li><strong>Run daily scans</strong> to monitor indexing health trends</li>
                <li><strong>Focus on pages below 50 health score</strong> - they're most at risk of deindexing</li>
                <li><strong>Check "Health Issues" tab</strong> for specific AI recommendations for each page</li>
                <li><strong>Set up Google Search Console API</strong> in Settings for real indexing data</li>
                <li><strong>Monitor the trend chart</strong> - sudden drops indicate site-wide issues</li>
            </ul>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php
}

/**
 * Render health issues page
 */
function render_indexing_issues_page() {
    global $wpdb;
    $table_issues = $wpdb->prefix . 'indexing_issues';
    
    // Get filter
    $filter_severity = isset($_GET['severity']) ? sanitize_text_field($_GET['severity']) : 'all';
    $filter_post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    
    // Build query
    $where = "WHERE resolved = 0";
    if ($filter_severity != 'all') {
        $where .= $wpdb->prepare(" AND issue_severity = %s", $filter_severity);
    }
    if ($filter_post_id > 0) {
        $where .= $wpdb->prepare(" AND post_id = %d", $filter_post_id);
    }
    
    $issues = $wpdb->get_results("
        SELECT i.*, p.post_title, p.post_type 
        FROM $table_issues i
        LEFT JOIN {$wpdb->posts} p ON i.post_id = p.ID
        $where
        ORDER BY 
            CASE issue_severity 
                WHEN 'critical' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                ELSE 4 
            END,
            i.detected_date DESC
    ");
    
    // Get severity counts
    $severity_counts = $wpdb->get_results("
        SELECT issue_severity, COUNT(*) as count 
        FROM $table_issues 
        WHERE resolved = 0 
        GROUP BY issue_severity
    ", OBJECT_K);
    
    ?>
    <div class="wrap">
        <h1>🔧 Page Health Issues</h1>
        
        <div style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <a href="?page=indexing-issues&severity=all" class="button <?php echo $filter_severity == 'all' ? 'button-primary' : ''; ?>">
                    All Issues (<?php echo array_sum(array_map(function($c) { return $c->count; }, $severity_counts)); ?>)
                </a>
                <a href="?page=indexing-issues&severity=critical" class="button <?php echo $filter_severity == 'critical' ? 'button-primary' : ''; ?>" style="border-color: #dc2626;">
                    🔴 Critical (<?php echo isset($severity_counts['critical']) ? $severity_counts['critical']->count : 0; ?>)
                </a>
                <a href="?page=indexing-issues&severity=high" class="button <?php echo $filter_severity == 'high' ? 'button-primary' : ''; ?>" style="border-color: #f59e0b;">
                    🟡 High (<?php echo isset($severity_counts['high']) ? $severity_counts['high']->count : 0; ?>)
                </a>
                <a href="?page=indexing-issues&severity=medium" class="button <?php echo $filter_severity == 'medium' ? 'button-primary' : ''; ?>">
                    🟢 Medium (<?php echo isset($severity_counts['medium']) ? $severity_counts['medium']->count : 0; ?>)
                </a>
                <a href="?page=indexing-issues&severity=low" class="button <?php echo $filter_severity == 'low' ? 'button-primary' : ''; ?>">
                    ⚪ Low (<?php echo isset($severity_counts['low']) ? $severity_counts['low']->count : 0; ?>)
                </a>
            </div>
            
            <?php if (empty($issues)): ?>
                <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; text-align: center;">
                    <p style="font-size: 18px;">🎉 <strong>Great news!</strong> No issues found!</p>
                    <p>Your pages are in good health for indexing.</p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%">Severity</th>
                            <th width="25%">Page</th>
                            <th width="15%">Issue Type</th>
                            <th width="25%">Description</th>
                            <th width="25%">AI Recommendation</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues as $issue): 
                            $severity_badge = array(
                                'critical' => array('color' => '#dc2626', 'bg' => '#fee2e2', 'icon' => '🔴'),
                                'high' => array('color' => '#f59e0b', 'bg' => '#fef3c7', 'icon' => '🟡'),
                                'medium' => array('color' => '#3b82f6', 'bg' => '#dbeafe', 'icon' => '🔵'),
                                'low' => array('color' => '#64748b', 'bg' => '#f1f5f9', 'icon' => '⚪')
                            );
                            $badge = $severity_badge[$issue->issue_severity] ?? $severity_badge['low'];
                        ?>
                        <tr>
                            <td>
                                <span style="background: <?php echo $badge['bg']; ?>; color: <?php echo $badge['color']; ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block;">
                                    <?php echo $badge['icon']; ?> <?php echo strtoupper($issue->issue_severity); ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo esc_html($issue->post_title); ?></strong>
                                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                    <?php echo esc_html($issue->post_type); ?> | 
                                    <a href="<?php echo get_edit_post_link($issue->post_id); ?>">Edit</a>
                                </div>
                            </td>
                            <td>
                                <code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12px;">
                                    <?php echo esc_html(str_replace('_', ' ', $issue->issue_type)); ?>
                                </code>
                            </td>
                            <td><?php echo esc_html($issue->issue_description); ?></td>
                            <td>
                                <div style="background: #eff6ff; padding: 10px; border-radius: 4px; border-left: 3px solid #3b82f6;">
                                    <?php echo esc_html($issue->ai_recommendation); ?>
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo get_edit_post_link($issue->post_id); ?>" class="button button-small button-primary">
                                    Fix Now
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div style="background: #fefce8; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #eab308;">
            <h3>💡 Understanding Issue Severities</h3>
            <ul style="line-height: 1.8;">
                <li><strong>🔴 Critical:</strong> Will definitely prevent indexing (e.g., noindex tag, canonical to different page, excluded from sitemap)</li>
                <li><strong>🟡 High:</strong> Likely to cause deindexing (e.g., thin content, duplicate content)</li>
                <li><strong>🔵 Medium:</strong> May affect indexing quality (e.g., short titles, few internal links)</li>
                <li><strong>⚪ Low:</strong> Minor improvements (e.g., missing images, optimization opportunities)</li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Render settings page
 */
function render_indexing_settings_page() {
    // Handle settings save
    if (isset($_POST['save_settings']) && check_admin_referer('indexing_settings_action', 'indexing_settings_nonce')) {
        update_option('indexing_monitor_enabled', isset($_POST['monitor_enabled']) ? 1 : 0);
        update_option('indexing_alert_email', sanitize_email($_POST['alert_email']));
        update_option('indexing_alert_threshold', intval($_POST['alert_threshold']));
        update_option('indexing_scan_frequency', sanitize_text_field($_POST['scan_frequency']));
        
        // Save GSC credentials if provided
        if (!empty($_POST['gsc_json_key'])) {
            update_option('gsc_api_credentials', sanitize_textarea_field($_POST['gsc_json_key']));
        }
        
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }
    
    $monitor_enabled = get_option('indexing_monitor_enabled', 1);
    $alert_email = get_option('indexing_alert_email', get_option('admin_email'));
    $alert_threshold = get_option('indexing_alert_threshold', 10);
    $scan_frequency = get_option('indexing_scan_frequency', 'daily');
    $gsc_credentials = get_option('gsc_api_credentials', '');
    
    ?>
    <div class="wrap">
        <h1>⚙️ Indexing Monitor Settings</h1>
        
        <form method="post" style="background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <?php wp_nonce_field('indexing_settings_action', 'indexing_settings_nonce'); ?>
            
            <h2>General Settings</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Monitoring</th>
                    <td>
                        <label>
                            <input type="checkbox" name="monitor_enabled" value="1" <?php checked($monitor_enabled, 1); ?>>
                            Enable automatic indexing health monitoring
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Scan Frequency</th>
                    <td>
                        <select name="scan_frequency">
                            <option value="hourly" <?php selected($scan_frequency, 'hourly'); ?>>Every Hour</option>
                            <option value="twicedaily" <?php selected($scan_frequency, 'twicedaily'); ?>>Twice Daily</option>
                            <option value="daily" <?php selected($scan_frequency, 'daily'); ?>>Once Daily</option>
                            <option value="weekly" <?php selected($scan_frequency, 'weekly'); ?>>Once Weekly</option>
                        </select>
                        <p class="description">How often to automatically scan pages for indexing issues</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Alert Email</th>
                    <td>
                        <input type="email" name="alert_email" value="<?php echo esc_attr($alert_email); ?>" class="regular-text">
                        <p class="description">Email address to receive indexing alerts</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Alert Threshold</th>
                    <td>
                        <input type="number" name="alert_threshold" value="<?php echo esc_attr($alert_threshold); ?>" min="1" max="100">
                        <span>pages</span>
                        <p class="description">Send alert when this many pages drop below health score of 50</p>
                    </td>
                </tr>
            </table>
            
            <h2>Google Search Console API (Optional)</h2>
            <p>Connect to Google Search Console for real-time indexing data from Google.</p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Service Account JSON Key</th>
                    <td>
                        <textarea name="gsc_json_key" rows="6" class="large-text code" placeholder='{"type": "service_account", "project_id": "...", ...}'><?php echo esc_textarea($gsc_credentials); ?></textarea>
                        <p class="description">
                            <strong>Setup Instructions:</strong><br>
                            1. Go to <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a><br>
                            2. Create a new project or select existing<br>
                            3. Enable "Google Search Console API"<br>
                            4. Create Service Account and download JSON key<br>
                            5. Add service account email to your Search Console property<br>
                            6. Paste JSON key contents above
                        </p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" name="save_settings" class="button button-primary button-large">
                    💾 Save Settings
                </button>
            </p>
        </form>
        
        <div style="background: #eff6ff; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <h3>📚 How This System Works</h3>
            <ol style="line-height: 1.8;">
                <li><strong>AI Health Analysis:</strong> Automatically scans all published pages for common indexing issues</li>
                <li><strong>Health Scoring:</strong> Each page gets a score (0-100) based on best practices</li>
                <li><strong>Issue Detection:</strong> Identifies specific problems like noindex tags, thin content, missing sitemaps</li>
                <li><strong>Smart Recommendations:</strong> Provides actionable advice to fix each issue</li>
                <li><strong>Trend Tracking:</strong> Monitors changes over time to catch sudden drops</li>
                <li><strong>Automated Alerts:</strong> Emails you when issues are detected</li>
            </ol>
        </div>
        
        <div style="background: #fef3c7; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <h3>⚠️ Common Reasons for Deindexing</h3>
            <ul style="line-height: 1.8;">
                <li><strong>Thin Content:</strong> Pages with less than 300 words often get deindexed</li>
                <li><strong>Duplicate Content:</strong> Multiple pages with similar content</li>
                <li><strong>Technical Issues:</strong> Noindex tags, robots.txt blocks, canonical issues</li>
                <li><strong>Low Quality:</strong> Pages with no images, no internal links, or poor formatting</li>
                <li><strong>Crawl Budget:</strong> Large sites may not have all pages crawled regularly</li>
                <li><strong>Manual Actions:</strong> Google penalties (check Search Console)</li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Schedule automated scans
 */
function schedule_indexing_scans() {
    $frequency = get_option('indexing_scan_frequency', 'daily');
    
    if (!wp_next_scheduled('run_indexing_scan')) {
        wp_schedule_event(time(), $frequency, 'run_indexing_scan');
    }
}
add_action('wp', 'schedule_indexing_scans');

/**
 * Run scheduled scan
 */
function run_scheduled_indexing_scan() {
    if (get_option('indexing_monitor_enabled', 1)) {
        $results = scan_all_pages_indexing_health();
        
        // Check if we should send alert
        $threshold = get_option('indexing_alert_threshold', 10);
        if ($results['not_indexed'] >= $threshold) {
            $alert_email = get_option('indexing_alert_email', get_option('admin_email'));
            
            $subject = '⚠️ Indexing Alert: ' . $results['not_indexed'] . ' Pages at Risk';
            $message = "Your site has " . $results['not_indexed'] . " pages at risk of being deindexed.\n\n";
            $message .= "Total Pages: " . $results['total'] . "\n";
            $message .= "Healthy Pages: " . $results['indexed'] . "\n";
            $message .= "At Risk Pages: " . $results['not_indexed'] . "\n";
            $message .= "Average Health Score: " . $results['avg_health'] . "/100\n\n";
            $message .= "View details: " . admin_url('admin.php?page=indexing-monitor') . "\n";
            
            wp_mail($alert_email, $subject, $message);
        }
    }
}
add_action('run_indexing_scan', 'run_scheduled_indexing_scan');

/**
 * AJAX handler to check single page
 */
function ajax_check_single_page() {
    check_ajax_referer('indexing_nonce', 'nonce');
    
    $post_id = intval($_POST['post_id']);
    $health_data = ai_analyze_page_health($post_id);
    
    wp_send_json_success($health_data);
}
add_action('wp_ajax_check_single_page', 'ajax_check_single_page');

/**
 * Add meta box to posts/pages for quick health check
 */
function add_indexing_health_meta_box() {
    $post_types = array('post', 'page', 'product', 'knowledge-base', 'documents', 'customer-stories');
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'indexing_health_check',
            '🔍 Indexing Health Check',
            'render_indexing_health_meta_box',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_indexing_health_meta_box');

/**
 * Render indexing health meta box
 */
function render_indexing_health_meta_box($post) {
    $health_data = ai_analyze_page_health($post->ID);
    $health_score = $health_data['health_score'];
    $health_color = $health_score >= 70 ? '#22c55e' : ($health_score >= 50 ? '#f59e0b' : '#ef4444');
    
    wp_nonce_field('indexing_nonce', 'indexing_nonce');
    ?>
    <div style="padding: 10px;">
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="font-size: 48px; font-weight: bold; color: <?php echo $health_color; ?>;">
                <?php echo $health_score; ?>
            </div>
            <div style="font-size: 14px; color: #64748b;">Health Score</div>
        </div>
        
        <div style="background: #f1f5f9; height: 12px; border-radius: 6px; overflow: hidden; margin-bottom: 15px;">
            <div style="width: <?php echo $health_score; ?>%; background: <?php echo $health_color; ?>; height: 100%; transition: width 0.3s;"></div>
        </div>
        
        <?php if (empty($health_data['issues'])): ?>
            <div style="background: #f0fdf4; padding: 10px; border-radius: 4px; text-align: center; color: #15803d;">
                ✅ No issues found!
            </div>
        <?php else: ?>
            <div style="margin-bottom: 10px;">
                <strong><?php echo count($health_data['issues']); ?> Issue<?php echo count($health_data['issues']) > 1 ? 's' : ''; ?> Found:</strong>
            </div>
            <?php foreach ($health_data['issues'] as $issue): 
                $severity_colors = array(
                    'critical' => '#dc2626',
                    'high' => '#f59e0b',
                    'medium' => '#3b82f6',
                    'low' => '#64748b'
                );
                $color = $severity_colors[$issue['severity']] ?? '#64748b';
            ?>
                <div style="background: #f9fafb; padding: 10px; margin-bottom: 8px; border-radius: 4px; border-left: 3px solid <?php echo $color; ?>;">
                    <div style="font-size: 12px; font-weight: bold; color: <?php echo $color; ?>; text-transform: uppercase; margin-bottom: 4px;">
                        <?php echo esc_html($issue['severity']); ?>
                    </div>
                    <div style="font-size: 13px; margin-bottom: 4px;">
                        <?php echo esc_html($issue['description']); ?>
                    </div>
                    <div style="font-size: 12px; color: #64748b; font-style: italic;">
                        💡 <?php echo esc_html($issue['recommendation']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <a href="<?php echo admin_url('admin.php?page=indexing-monitor'); ?>" class="button button-secondary" style="width: 100%; text-align: center; margin-top: 10px;">
            View Full Report
        </a>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Auto-refresh health check when saving
        $(document).on('click', '#publish, #save-post', function() {
            setTimeout(function() {
                location.reload();
            }, 1000);
        });
    });
    </script>
    <?php
}

// Initialize database tables on theme activation
create_indexing_monitoring_tables();