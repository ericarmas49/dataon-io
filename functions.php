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

// AJAX handler for analytics data
add_action( 'wp_ajax_get_analytics_data', 'get_analytics_data' );
function get_analytics_data() {
    check_ajax_referer( 'analytics_nonce', 'nonce' );
    
    $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'day';
    
    // Generate comprehensive sample data
    $data = generate_sample_analytics_data($period);
    
    wp_send_json_success( $data );
}

function generate_sample_analytics_data($period = 'day') {
    $base_views = array(
        'day' => 1200,
        'week' => 8500,
        'month' => 35000,
        'all_time' => 125000
    );
    
    $base_users = array(
        'day' => 450,
        'week' => 3200,
        'month' => 12500,
        'all_time' => 45000
    );
    
    return array(
        'connection_status' => 'sample_data', // 'connected', 'sample_data', 'error'
        'period' => $period,
        'page_views' => array(
            'current' => $base_views[$period],
            'previous' => $base_views[$period] * 0.85,
            'change_percent' => 15.2,
            'trend' => 'up',
            'labels' => array('Home', 'Products', 'About', 'Contact', 'Blog'),
            'data' => array(
                $base_views[$period] * 0.25,
                $base_views[$period] * 0.18,
                $base_views[$period] * 0.15,
                $base_views[$period] * 0.12,
                $base_views[$period] * 0.10
            )
        ),
        'real_time_users' => rand(5, 25),
        'top_pages' => array(
            array('page' => '/', 'views' => $base_views[$period] * 0.25, 'bounce_rate' => 35),
            array('page' => '/products/', 'views' => $base_views[$period] * 0.18, 'bounce_rate' => 42),
            array('page' => '/about/', 'views' => $base_views[$period] * 0.15, 'bounce_rate' => 28),
            array('page' => '/contact/', 'views' => $base_views[$period] * 0.12, 'bounce_rate' => 55),
            array('page' => '/blog/', 'views' => $base_views[$period] * 0.10, 'bounce_rate' => 38),
            array('page' => '/downloads/', 'views' => $base_views[$period] * 0.08, 'bounce_rate' => 45),
            array('page' => '/support/', 'views' => $base_views[$period] * 0.07, 'bounce_rate' => 32),
            array('page' => '/pricing/', 'views' => $base_views[$period] * 0.05, 'bounce_rate' => 48),
            array('page' => '/news/', 'views' => $base_views[$period] * 0.03, 'bounce_rate' => 25),
            array('page' => '/careers/', 'views' => $base_views[$period] * 0.02, 'bounce_rate' => 60)
        ),
        'traffic_sources' => array(
            'labels' => array('Organic Search', 'Direct', 'Social', 'Referral', 'Email'),
            'data' => array(45, 25, 15, 10, 5),
            'sources' => array(
                array('source' => 'Organic Search', 'sessions' => 45, 'percentage' => 45),
                array('source' => 'Direct', 'sessions' => 25, 'percentage' => 25),
                array('source' => 'Social', 'sessions' => 15, 'percentage' => 15),
                array('source' => 'Referral', 'sessions' => 10, 'percentage' => 10),
                array('source' => 'Email', 'sessions' => 5, 'percentage' => 5)
            )
        ),
        'pdf_downloads' => array(
            array('file' => 'DataON-HCI-Solution-Guide.pdf', 'downloads' => 156, 'last_download' => '2 hours ago'),
            array('file' => 'Azure-Stack-HCI-Datasheet.pdf', 'downloads' => 89, 'last_download' => '1 hour ago'),
            array('file' => 'Storage-Solutions-Whitepaper.pdf', 'downloads' => 67, 'last_download' => '3 hours ago'),
            array('file' => 'Performance-Benchmarks.pdf', 'downloads' => 43, 'last_download' => '5 hours ago'),
            array('file' => 'Deployment-Guide.pdf', 'downloads' => 34, 'last_download' => '1 day ago'),
            array('file' => 'Troubleshooting-Manual.pdf', 'downloads' => 28, 'last_download' => '2 days ago'),
            array('file' => 'Security-Protocols.pdf', 'downloads' => 22, 'last_download' => '3 days ago'),
            array('file' => 'Integration-Guide.pdf', 'downloads' => 19, 'last_download' => '4 days ago'),
            array('file' => 'Best-Practices.pdf', 'downloads' => 15, 'last_download' => '1 week ago'),
            array('file' => 'Case-Study-Enterprise.pdf', 'downloads' => 12, 'last_download' => '1 week ago')
        ),
        'trending_insights' => array(
            array('type' => 'spike', 'title' => 'Traffic Spike', 'description' => '50% increase in organic traffic from "hyper-converged infrastructure" searches', 'impact' => 'high'),
            array('type' => 'trend', 'title' => 'PDF Downloads Up', 'description' => 'DataON-HCI-Solution-Guide.pdf downloads increased 25% this week', 'impact' => 'medium'),
            array('type' => 'source', 'title' => 'New Traffic Source', 'description' => 'LinkedIn referrals increased 40% in the last 7 days', 'impact' => 'medium'),
            array('type' => 'page', 'title' => 'Product Page Performance', 'description' => '/products/ page bounce rate dropped 15% after recent updates', 'impact' => 'high'),
            array('type' => 'device', 'title' => 'Mobile Usage', 'description' => 'Mobile traffic increased 30% compared to last month', 'impact' => 'medium')
        ),
        'recent_activity' => array(
            array('time' => '2 minutes ago', 'event' => 'PDF Download', 'page' => '/downloads/', 'file' => 'DataON-HCI-Solution-Guide.pdf'),
            array('time' => '5 minutes ago', 'event' => 'Form Submission', 'page' => '/contact/', 'details' => 'Contact form submitted'),
            array('time' => '8 minutes ago', 'event' => 'Page View', 'page' => '/products/', 'details' => 'Product page viewed'),
            array('time' => '12 minutes ago', 'event' => 'Email Signup', 'page' => '/newsletter/', 'details' => 'Newsletter subscription'),
            array('time' => '15 minutes ago', 'event' => 'PDF Download', 'page' => '/downloads/', 'file' => 'Azure-Stack-HCI-Datasheet.pdf'),
            array('time' => '18 minutes ago', 'event' => 'Page View', 'page' => '/about/', 'details' => 'About page viewed'),
            array('time' => '22 minutes ago', 'event' => 'Form Submission', 'page' => '/support/', 'details' => 'Support ticket submitted'),
            array('time' => '25 minutes ago', 'event' => 'Page View', 'page' => '/blog/', 'details' => 'Blog post viewed')
        )
    );
}

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
</style>

<div class="wrap analytics-dashboard">
    <div class="analytics-header">
        <div>
            <h1>Analytics Dashboard</h1>
            <p>Comprehensive insights and real-time data</p>
        </div>
        <div class="connection-status sample" id="connection-status">
            <span>📊 Sample Data</span>
        </div>
    </div>

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

    <div class="tab-navigation">
        <button class="tab-button active" data-tab="overview">Overview</button>
        <button class="tab-button" data-tab="pages">Top Pages</button>
        <button class="tab-button" data-tab="traffic">Traffic Sources</button>
        <button class="tab-button" data-tab="downloads">PDF Downloads</button>
        <button class="tab-button" data-tab="insights">Trending Insights</button>
        <button class="tab-button" data-tab="activity">Recent Activity</button>
    </div>

    <div id="analytics-content">
        <div class="loading">Loading analytics data...</div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentPeriod = 'day';
    let currentTab = 'overview';
    
    // Initialize dashboard
    loadAnalyticsData();
    
    // Tab navigation
    $('.tab-button').on('click', function() {
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        currentTab = $(this).data('tab');
        loadAnalyticsData();
    });
    
    // Period filters
    $(document).on('click', '.period-button', function() {
        $('.period-button').removeClass('active');
        $(this).addClass('active');
        currentPeriod = $(this).data('period');
        loadAnalyticsData();
    });
    
    function loadAnalyticsData() {
        $.ajax({
            url: analytics_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_analytics_data',
                nonce: analytics_ajax.nonce,
                period: currentPeriod
            },
            success: function(response) {
                if (response.success) {
                    renderAnalyticsDashboard(response.data);
                } else {
                    showError('Failed to load analytics data');
                }
            },
            error: function() {
                showError('Failed to connect to analytics service');
            }
        });
    }
    
    function renderAnalyticsDashboard(data) {
        updateMetrics(data);
        updateConnectionStatus(data.connection_status);
        
        const content = renderTabContent(data, currentTab);
        $('#analytics-content').html(content);
        
        if (currentTab === 'overview') {
            renderOverviewCharts(data);
        }
    }
    
    function updateMetrics(data) {
        $('#page-views-value').text(data.page_views.current.toLocaleString());
        $('#real-time-users').text(data.real_time_users);
        $('#unique-users').text(Math.round(data.page_views.current * 0.4).toLocaleString());
        $('#bounce-rate').text('42.5%');
        
        $('#page-views-change').text('+' + data.page_views.change_percent + '%');
        $('#unique-users-change').text('+8.5%');
        $('#bounce-rate-change').text('-2.1%');
    }
    
    function updateConnectionStatus(status) {
        const statusElement = $('#connection-status');
        statusElement.removeClass('connected sample error');
        
        switch(status) {
            case 'connected':
                statusElement.addClass('connected').html('<span>✅ Connected to Google Analytics</span>');
                break;
            case 'sample_data':
                statusElement.addClass('sample').html('<span>📊 Sample Data</span>');
                break;
            case 'error':
                statusElement.addClass('error').html('<span>❌ Connection Error</span>');
                break;
        }
    }
    
    function renderTabContent(data, tab) {
        switch(tab) {
            case 'overview':
                return renderOverviewTab(data);
            case 'pages':
                return renderPagesTab(data);
            case 'traffic':
                return renderTrafficTab(data);
            case 'downloads':
                return renderDownloadsTab(data);
            case 'insights':
                return renderInsightsTab(data);
            case 'activity':
                return renderActivityTab(data);
            default:
                return renderOverviewTab(data);
        }
    }
    
    function renderOverviewTab(data) {
        return `
            <div class="tab-content active">
                <div class="period-filters">
                    <button class="period-button active" data-period="day">Day</button>
                    <button class="period-button" data-period="week">Week</button>
                    <button class="period-button" data-period="month">Month</button>
                    <button class="period-button" data-period="all_time">All Time</button>
                </div>
                
                <div class="charts-grid">
                    <div class="chart-container">
                        <h3>Page Views Trend</h3>
                        <canvas id="pageViewsChart" class="chart-canvas"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>Traffic Sources</h3>
                        <canvas id="trafficSourcesChart" class="chart-canvas"></canvas>
                    </div>
                </div>
                
                <div class="insights-grid">
                    ${data.trending_insights.slice(0, 3).map(insight => `
                        <div class="insight-card ${insight.impact}-impact">
                            <div class="insight-title">${insight.title}</div>
                            <div class="insight-description">${insight.description}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    function renderPagesTab(data) {
        return `
            <div class="tab-content active">
                <div class="period-filters">
                    <button class="period-button active" data-period="day">Day</button>
                    <button class="period-button" data-period="week">Week</button>
                    <button class="period-button" data-period="month">Month</button>
                    <button class="period-button" data-period="all_time">All Time</button>
                </div>
                
                <div class="chart-container">
                    <h3>Top 10 Pages</h3>
                    <canvas id="topPagesChart" class="chart-canvas"></canvas>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Views</th>
                            <th>Bounce Rate</th>
                            <th>Avg. Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.top_pages.map(page => `
                            <tr>
                                <td>${page.page}</td>
                                <td>${page.views.toLocaleString()}</td>
                                <td>${page.bounce_rate}%</td>
                                <td>${Math.floor(Math.random() * 5) + 1}m ${Math.floor(Math.random() * 60)}s</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }
    
    function renderTrafficTab(data) {
        return `
            <div class="tab-content active">
                <div class="period-filters">
                    <button class="period-button active" data-period="day">Day</button>
                    <button class="period-button" data-period="week">Week</button>
                    <button class="period-button" data-period="month">Month</button>
                    <button class="period-button" data-period="all_time">All Time</button>
                </div>
                
                <div class="charts-grid">
                    <div class="chart-container">
                        <h3>Traffic Sources Breakdown</h3>
                        <canvas id="trafficBreakdownChart" class="chart-canvas"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>Source Performance</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th>Sessions</th>
                                    <th>%</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.traffic_sources.map(source => `
                                    <tr>
                                        <td>${source.source}</td>
                                        <td>${source.sessions}</td>
                                        <td>${source.percentage}%</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    function renderDownloadsTab(data) {
        return `
            <div class="tab-content active">
                <div class="period-filters">
                    <button class="period-button active" data-period="day">Day</button>
                    <button class="period-button" data-period="week">Week</button>
                    <button class="period-button" data-period="month">Month</button>
                    <button class="period-button" data-period="all_time">All Time</button>
                </div>
                
                <div class="chart-container">
                    <h3>PDF Download Trends</h3>
                    <canvas id="pdfDownloadsChart" class="chart-canvas"></canvas>
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>PDF File</th>
                            <th>Downloads</th>
                            <th>Last Download</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.pdf_downloads.map(pdf => `
                            <tr>
                                <td>${pdf.file}</td>
                                <td>${pdf.downloads}</td>
                                <td>${pdf.last_download}</td>
                                <td>
                                    <span class="metric-change positive">+${Math.floor(Math.random() * 20) + 5}%</span>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }
    
    function renderInsightsTab(data) {
        return `
            <div class="tab-content active">
                <h3>Trending Insights & Anomalies</h3>
                
                <div class="insights-grid">
                    ${data.trending_insights.map(insight => `
                        <div class="insight-card ${insight.impact}-impact">
                            <div class="insight-title">${insight.title}</div>
                            <div class="insight-description">${insight.description}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    function renderActivityTab(data) {
        return `
            <div class="tab-content active">
                <h3>Recent Activity Feed</h3>
                
                <div class="activity-feed">
                    ${data.recent_activity.map(activity => `
                        <div class="activity-item">
                            <div class="activity-icon">📊</div>
                            <div class="activity-content">
                                <div class="activity-event">${activity.event}</div>
                                <div class="activity-time">${activity.time} • ${activity.page}${activity.file ? ' • ' + activity.file : ''}</div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }
    
    function renderOverviewCharts(data) {
        // Page Views Chart
        const pageViewsCtx = document.getElementById('pageViewsChart');
        if (pageViewsCtx) {
            new Chart(pageViewsCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Page Views',
                        data: [1200, 1350, 1100, 1400, 1600, 1800, 1500],
                        borderColor: 'rgba(102, 126, 234, 1)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Traffic Sources Chart
        const trafficSourcesCtx = document.getElementById('trafficSourcesChart');
        if (trafficSourcesCtx) {
            new Chart(trafficSourcesCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: data.traffic_sources.map(s => s.source),
                    datasets: [{
                        data: data.traffic_sources.map(s => s.percentage),
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.8)',
                            'rgba(118, 75, 162, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }
    
    function showError(message) {
        $('#analytics-content').html(`
            <div class="error-message">
                <strong>Error:</strong> ${message}
            </div>
        `);
    }
    
    // Auto-refresh real-time data every 30 seconds
    setInterval(function() {
        $('#real-time-users').text(Math.floor(Math.random() * 20) + 5);
    }, 30000);
});
</script>

<?php 
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



  add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'knowledge-base') return false;
    return $current_status;
}