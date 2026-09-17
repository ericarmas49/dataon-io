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
    wp_enqueue_style( 'do-icon-link-bar', get_stylesheet_directory_uri() . '/assets/css/icon-link-bar.css', array(), '1.1.2' );
    wp_enqueue_script( 'jquery' );

    if ( blankslate_needs_testimonials_slider() ) {
        blankslate_enqueue_testimonials_slider_assets();
    }
}

function blankslate_needs_testimonials_slider() {
    if ( is_front_page() ) {
        return true;
    }

    if ( ! is_singular() ) {
        return false;
    }

    $post = get_post();
    if ( ! $post || empty( $post->post_content ) ) {
        return false;
    }

    return false !== strpos( $post->post_content, 'testimonials' );
}

function blankslate_enqueue_testimonials_slider_assets() {
    $version = '1.0.0';

    wp_enqueue_style(
        'slick-carousel',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css',
        array(),
        '1.9.0'
    );
    wp_enqueue_style(
        'slick-carousel-theme',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css',
        array( 'slick-carousel' ),
        '1.9.0'
    );
    wp_enqueue_style(
        'do-testimonials',
        get_stylesheet_directory_uri() . '/assets/css/testimonials.css',
        array( 'slick-carousel-theme' ),
        $version
    );

    wp_enqueue_script(
        'slick-carousel',
        'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
        array( 'jquery' ),
        '1.8.1',
        true
    );
    wp_enqueue_script(
        'do-testimonials-slider',
        get_stylesheet_directory_uri() . '/js/testimonials-slider.js',
        array( 'jquery', 'slick-carousel' ),
        $version,
        true
    );
}

/**
 * ADA: Remove role="main" from embedded/content so only theme main landmark exists.
 */
add_filter( 'the_content', 'blankslate_strip_content_role_main', 20 );
function blankslate_strip_content_role_main( $content ) {
    return preg_replace( '/\s*role=["\']main["\']/i', '', $content );
}

/**
 * ADA: Remove role="tabpanel" from img and other non-panel elements (e.g. slider markup).
 */
add_filter( 'the_content', 'blankslate_strip_tabpanel_from_non_panels', 20 );
function blankslate_strip_tabpanel_from_non_panels( $content ) {
    return preg_replace( '/<img([^>]*)\s+role=["\']tabpanel["\']([^>]*)>/i', '<img$1$2>', $content );
}

/**
 * ADA: Remove Slick tab-pattern roles when carousel is not a true tab interface.
 */
add_filter( 'the_content', 'blankslate_strip_slick_tab_roles', 20 );
function blankslate_strip_slick_tab_roles( $content ) {
    // Remove tablist role from Slick dots containers.
    $content = preg_replace(
        '/(<ul[^>]*class=["\'][^"\']*slick-dots[^"\']*["\'][^>]*)\s+role=["\']tablist["\']([^>]*>)/i',
        '$1$2',
        $content
    );

    // Remove tab semantics from Slick pagination buttons.
    $content = preg_replace(
        '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+role=["\']tab["\']/i',
        '$1',
        $content
    );
    $content = preg_replace(
        '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+aria-controls=["\'][^"\']*["\']/i',
        '$1',
        $content
    );
    $content = preg_replace(
        '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+aria-selected=["\'][^"\']*["\']/i',
        '$1',
        $content
    );
    $content = preg_replace(
        '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+tabindex=["\'][^"\']*["\']/i',
        '$1',
        $content
    );

    return $content;
}

/**
 * ADA: Final HTML pass to remove invalid menu/tab roles injected outside the_content.
 */
function blankslate_ada_sanitize_html( $html ) {
    // Navigation menu roles used for app menus, not site nav.
    $html = preg_replace( '/\s+role=["\']menubar["\']/i', '', $html );
    $html = preg_replace( '/\s+role=["\']menuitem["\']/i', '', $html );
    $html = preg_replace( '/\s+role=["\']menu["\']/i', '', $html );
    $html = preg_replace( '/\s+role=["\']none["\']/i', '', $html );
    $html = preg_replace( '/\s+role=["\']group["\']/i', '', $html );

    // Remove menu-popup state from non-menu nav anchors.
    $html = preg_replace( '/(<a[^>]*class=["\'][^"\']*nav-link[^"\']*dropdown-toggle[^"\']*["\'][^>]*)\s+aria-haspopup=["\']true["\']/i', '$1', $html );

    // Slick uses tab roles for dots; remove because these are not tabs.
    $html = preg_replace( '/\s+id=(["\'])\1/i', '', $html );
    $html = preg_replace( '/(<ul[^>]*class=["\'][^"\']*slick-dots[^"\']*["\'][^>]*)\s+role=["\']tablist["\']/i', '$1', $html );
    $html = preg_replace( '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+role=["\']tab["\']/i', '$1', $html );
    $html = preg_replace( '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+aria-controls=["\'][^"\']*["\']/i', '$1', $html );
    $html = preg_replace( '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+aria-selected=["\'][^"\']*["\']/i', '$1', $html );
    $html = preg_replace( '/(<button[^>]*id=["\']slick-slide-control[^"\']*["\'][^>]*)\s+tabindex=["\'][^"\']*["\']/i', '$1', $html );
    $html = preg_replace( '/(<img[^>]*?)\s+role=["\']tabpanel["\']([^>]*>)/i', '$1$2', $html );
    $html = preg_replace( '/(<img[^>]*?)\s+aria-labelledby=["\'][^"\']*["\']([^>]*>)/i', '$1$2', $html );

    return $html;
}

function blankslate_ada_output_buffer_start() {
    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }
    ob_start( 'blankslate_ada_sanitize_html' );
}
add_action( 'template_redirect', 'blankslate_ada_output_buffer_start', 0 );

add_action( 'wp_footer', 'blankslate_footer' );
function blankslate_footer() {
?>
    <script>
    jQuery(document).ready(function($) {
        function normalizeSlickA11y() {
            $('[id=""]').removeAttr('id');
            $('.slick-dots[role="tablist"]').removeAttr('role');
            $('button[id^="slick-slide-control"][role="tab"]').removeAttr('role aria-controls aria-selected tabindex');
            $('img[role="tabpanel"]').removeAttr('role aria-labelledby tabindex');
        }

        // Slick can inject markup after load; normalize immediately and on slider lifecycle events.
        normalizeSlickA11y();
        $(document).on('init reInit afterChange setPosition', '.slick-slider', normalizeSlickA11y);
        setTimeout(normalizeSlickA11y, 300);
        setTimeout(normalizeSlickA11y, 1200);

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
    echo '<nav aria-label="' . esc_attr__( 'Skip links', 'blankslate' ) . '"><a href="#main" class="skip-link screen-reader-text">' . esc_html__( 'Skip to the content', 'blankslate' ) . '</a></nav>';
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

require_once get_template_directory() . '/includes/class-ga4-analytics.php';

add_action( 'admin_enqueue_scripts', 'analytics_admin_scripts' );
function analytics_admin_scripts( $hook ) {
    if ( $hook != 'toplevel_page_admin-analytics' ) {
        return;
    }

    wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true );
    wp_enqueue_script( 'analytics-dashboard', get_template_directory_uri() . '/js/analytics-dashboard.js', array( 'jquery', 'chart-js' ), '1.4', true );

    wp_localize_script( 'analytics-dashboard', 'analytics_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'analytics_nonce' ),
        'settings_url' => admin_url( 'options-general.php?page=ga4-settings' ),
    ));
}

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
        check_admin_referer('ga4_settings_save');
        update_option('ga4_enabled', isset($_POST['ga4_enabled']));
        update_option('ga4_api_key', sanitize_text_field($_POST['ga4_api_key']));
        update_option('ga4_property_id', sanitize_text_field($_POST['ga4_property_id']));
        update_option('ga4_measurement_id', sanitize_text_field($_POST['ga4_measurement_id']));
        update_option('ga4_service_account_email', sanitize_email($_POST['ga4_service_account_email']));
        update_option('ga4_private_key', sanitize_textarea_field($_POST['ga4_private_key']));
        update_option('ga4_project_id', sanitize_text_field($_POST['ga4_project_id']));
        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
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
            <?php wp_nonce_field('ga4_settings_save'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable GA4 Integration</th>
                    <td><input type="checkbox" name="ga4_enabled" value="1" <?php checked($ga4_enabled); ?> /></td>
                </tr>
                <tr>
                    <th scope="row">API Key</th>
                    <td><input type="text" name="ga4_api_key" value="<?php echo esc_attr($ga4_api_key); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Property ID</th>
                    <td><input type="text" name="ga4_property_id" value="<?php echo esc_attr($ga4_property_id); ?>" class="regular-text" /><p class="description">Numeric GA4 property ID</p></td>
                </tr>
                <tr>
                    <th scope="row">Measurement ID</th>
                    <td><input type="text" name="ga4_measurement_id" value="<?php echo esc_attr($ga4_measurement_id); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Service Account Email</th>
                    <td><input type="email" name="ga4_service_account_email" value="<?php echo esc_attr($ga4_service_account_email); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row">Private Key</th>
                    <td><textarea name="ga4_private_key" rows="8" cols="70" class="large-text code"><?php echo esc_textarea($ga4_private_key); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row">Project ID</th>
                    <td><input type="text" name="ga4_project_id" value="<?php echo esc_attr($ga4_project_id); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <?php if ($ga4_enabled && !empty($ga4_property_id)) : ?>
        <h2>Test Connection</h2>
        <p>
            <button type="button" id="test-ga4-connection" class="button">Test GA4 Connection</button>
            <span id="connection-status"></span>
        </p>
        <script>
        jQuery(function($) {
            $('#test-ga4-connection').on('click', function() {
                var button = $(this);
                var status = $('#connection-status');
                button.prop('disabled', true).text('Testing...');
                status.html('');
                $.post(ajaxurl, {
                    action: 'test_ga4_connection',
                    nonce: '<?php echo wp_create_nonce('ga4_test_nonce'); ?>'
                }).done(function(response) {
                    if (response.success) {
                        status.html('<span style="color:green;">✅ ' + response.data.message + '</span>');
                    } else {
                        status.html('<span style="color:red;">❌ ' + (response.data && response.data.message ? response.data.message : 'Connection failed') + '</span>');
                    }
                }).fail(function() {
                    status.html('<span style="color:red;">❌ Connection test failed</span>');
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

add_action('wp_ajax_test_ga4_connection', 'test_ga4_connection');
function test_ga4_connection() {
    check_ajax_referer('ga4_test_nonce', 'nonce');
    $ga4 = new GA4_Analytics();
    $result = $ga4->test_connection();
    if ($result['success']) {
        wp_send_json_success($result);
    }
    wp_send_json_error($result);
}

add_action( 'wp_ajax_get_analytics_data', 'get_analytics_data' );
function get_analytics_data() {
    check_ajax_referer( 'analytics_nonce', 'nonce' );

    $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'week';
    $start_date = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null;
    $end_date = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null;

    if ($period === 'custom') {
        if (empty($start_date) || empty($end_date)) {
            wp_send_json_error('Custom range requires a start and end date.');
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
            wp_send_json_error('Dates must use YYYY-MM-DD format.');
        }

        if (strtotime($start_date) > strtotime($end_date)) {
            wp_send_json_error('Start date must be before end date.');
        }
    }

    $ga4_enabled = get_option('ga4_enabled', false);
    $ga4_property_id = get_option('ga4_property_id', '');

    if (!$ga4_enabled || empty($ga4_property_id)) {
        wp_send_json_error('Google Analytics 4 is not configured. Go to Settings → Google Analytics.');
    }

    $ga4 = new GA4_Analytics();
    if ($period === 'custom' && $start_date && $end_date) {
        $live_data = $ga4->get_analytics_data($period, $start_date, $end_date);
    } else {
        $live_data = $ga4->get_analytics_data($period);
    }

    if ($live_data && isset($live_data['connection_status']) && $live_data['connection_status'] === 'connected') {
        wp_send_json_success($live_data);
    }

    wp_send_json_error('Failed to load live Google Analytics data. Check Settings → Google Analytics and test the connection.');
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
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.period-filters__buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.period-filters__range {
    font-size: 0.95em;
    font-weight: 600;
    color: #4a5568;
}

.period-filters__custom {
    display: flex;
    gap: 12px;
    align-items: flex-end;
    flex-wrap: wrap;
    padding: 14px 16px;
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

.period-date-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.85em;
    color: #4a5568;
}

.period-date-field input[type="date"] {
    min-height: 34px;
    padding: 4px 8px;
}

.period-apply-custom {
    min-height: 34px;
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

.analytics-notice {
    background: #fff8e5;
    color: #7a5b00;
    padding: 12px 15px;
    border-radius: 5px;
    margin: 0 0 20px;
    border-left: 4px solid #f0b429;
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

.period-range-label {
    display: inline-block;
    margin-left: 8px;
    font-size: 0.55em;
    font-weight: 500;
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    padding: 4px 10px;
    border-radius: 12px;
    vertical-align: middle;
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
            <span>Loading live data...</span>
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

/**
 * Agent readiness support:
 * - Markdown negotiation for HTML pages
 * - Content Signals in robots.txt
 * - .well-known discovery endpoints
 */

function dataon_request_path() {
    $path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '/';
    return untrailingslashit( $path ? $path : '/' );
}

function dataon_accepts_markdown() {
    if ( isset( $_GET['format'] ) && sanitize_key( wp_unslash( $_GET['format'] ) ) === 'markdown' ) {
        return true;
    }

    $accept = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( (string) $_SERVER['HTTP_ACCEPT'] ) : '';
    if ( $accept === '' ) {
        return false;
    }

    // Normal browsers (including mobile Safari) always accept text/html.
    // Only serve markdown when HTML is not accepted — e.g. AI agents requesting markdown explicitly.
    if ( strpos( $accept, 'text/html' ) !== false ) {
        return false;
    }

    return strpos( $accept, 'text/markdown' ) !== false;
}

function dataon_html_to_markdown( $html ) {
    $markdown = $html;

    // Remove non-content sections first.
    $markdown = preg_replace( '#<(script|style|noscript)[^>]*>.*?</\1>#is', '', $markdown );
    $markdown = preg_replace( '#<head[^>]*>.*?</head>#is', '', $markdown );

    // Headings.
    for ( $i = 6; $i >= 1; $i-- ) {
        $markdown = preg_replace( '#<h' . $i . '[^>]*>(.*?)</h' . $i . '>#is', "\n" . str_repeat( '#', $i ) . " $1\n\n", $markdown );
    }

    // Basic formatting.
    $markdown = preg_replace( '#<(strong|b)[^>]*>(.*?)</\1>#is', '**$2**', $markdown );
    $markdown = preg_replace( '#<(em|i)[^>]*>(.*?)</\1>#is', '*$2*', $markdown );
    $markdown = preg_replace( '#<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#is', '[$2]($1)', $markdown );
    $markdown = preg_replace( '#<li[^>]*>(.*?)</li>#is', "- $1\n", $markdown );

    // Block separators.
    $markdown = preg_replace( '#<(br|br/)\s*>#i', "\n", $markdown );
    $markdown = preg_replace( '#</(p|div|section|article|main|header|footer|ul|ol|table|tr)>#i', "\n\n", $markdown );

    // Strip any remaining tags and normalize spacing.
    $markdown = wp_strip_all_tags( $markdown, false );
    $markdown = html_entity_decode( $markdown, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    $markdown = preg_replace( "/\n{3,}/", "\n\n", $markdown );
    $markdown = trim( $markdown );

    return $markdown !== '' ? $markdown . "\n" : "# " . get_bloginfo( 'name' ) . "\n";
}

function dataon_markdown_negotiate_template() {
    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }

    if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
        return;
    }

    if ( ! dataon_accepts_markdown() ) {
        return;
    }

    $path = dataon_request_path();
    if ( strpos( $path, '/.well-known/' ) === 0 || $path === '/robots.txt' || is_feed() ) {
        return;
    }

    if ( headers_sent() ) {
        return;
    }

    ob_start(
        function( $buffer ) {
            $markdown = dataon_html_to_markdown( $buffer );
            $token_count = str_word_count( wp_strip_all_tags( $markdown ) );
            header( 'Content-Type: text/markdown; charset=UTF-8' );
            header( 'X-Markdown-Tokens: ' . (int) $token_count );
            return $markdown;
        }
    );
}
add_action( 'template_redirect', 'dataon_markdown_negotiate_template', 0 );

function dataon_content_signal_line() {
    return 'Content-Signal: ai-train=no, search=yes, ai-input=no';
}

function dataon_robots_content_signals( $output, $public ) {
    $line = dataon_content_signal_line();
    if ( strpos( $output, $line ) === false ) {
        $output = rtrim( $output ) . "\n" . $line . "\n";
    }
    return $output;
}
add_filter( 'robots_txt', 'dataon_robots_content_signals', 10, 2 );

function dataon_sha256_b64( $content ) {
    return 'sha256-' . base64_encode( hash( 'sha256', $content, true ) );
}

function dataon_json_response( $payload, $content_type = 'application/json; charset=UTF-8' ) {
    status_header( 200 );
    header( 'Content-Type: ' . $content_type );
    echo wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    exit;
}

function dataon_text_response( $content, $content_type = 'text/plain; charset=UTF-8' ) {
    status_header( 200 );
    header( 'Content-Type: ' . $content_type );
    echo $content;
    exit;
}

function dataon_handle_well_known_endpoints() {
    if ( is_admin() || wp_doing_ajax() ) {
        return;
    }

    $path = dataon_request_path();
    $home = home_url( '/' );
    $api_base = home_url( '/wp-json/' );
    $issuer = home_url( '/' );

    if ( $path === '/.well-known/api-catalog' ) {
        $payload = array(
            'linkset' => array(
                array(
                    'anchor' => $api_base,
                    'service-desc' => array(
                        array( 'href' => home_url( '/wp-json/' ) ),
                    ),
                    'service-doc' => array(
                        array( 'href' => home_url( '/wp-json/' ) ),
                    ),
                    'status' => array(
                        array( 'href' => home_url( '/wp-json/' ) ),
                    ),
                ),
            ),
        );
        dataon_json_response( $payload, 'application/linkset+json; charset=UTF-8' );
    }

    if ( $path === '/.well-known/openid-configuration' || $path === '/.well-known/oauth-authorization-server' ) {
        $payload = array(
            'issuer' => untrailingslashit( $issuer ),
            'authorization_endpoint' => home_url( '/wp-login.php' ),
            'token_endpoint' => home_url( '/wp-json/jwt-auth/v1/token' ),
            'jwks_uri' => home_url( '/.well-known/jwks.json' ),
            'grant_types_supported' => array( 'authorization_code', 'client_credentials', 'refresh_token' ),
            'response_types_supported' => array( 'code' ),
            'token_endpoint_auth_methods_supported' => array( 'client_secret_basic', 'client_secret_post' ),
            'scopes_supported' => array( 'openid', 'profile', 'email', 'read', 'write' ),
        );
        dataon_json_response( $payload );
    }

    if ( $path === '/.well-known/oauth-protected-resource' ) {
        $payload = array(
            'resource' => untrailingslashit( $home ),
            'authorization_servers' => array( untrailingslashit( $issuer ) ),
            'scopes_supported' => array( 'read', 'write', 'openid', 'profile', 'email' ),
            'bearer_methods_supported' => array( 'header' ),
            'resource_documentation' => home_url( '/wp-json/' ),
        );
        dataon_json_response( $payload );
    }

    if ( $path === '/.well-known/mcp/server-card.json' ) {
        $payload = array(
            'serverInfo' => array(
                'name' => get_bloginfo( 'name' ) . ' MCP',
                'version' => '1.0.0',
            ),
            'transports' => array(
                array(
                    'type' => 'http',
                    'url' => home_url( '/wp-json/' ),
                ),
            ),
            'capabilities' => array(
                'tools' => true,
                'resources' => true,
                'prompts' => false,
            ),
        );
        dataon_json_response( $payload );
    }

    $skill_markdown = "# Website Overview\n\nUse this skill to understand key website pages and navigation.\n";
    $skill_actions = "# Website Actions\n\nUse this skill to discover read-only website actions exposed by tools.\n";

    if ( $path === '/.well-known/agent-skills/site-overview.md' ) {
        dataon_text_response( $skill_markdown, 'text/markdown; charset=UTF-8' );
    }

    if ( $path === '/.well-known/agent-skills/site-actions.md' ) {
        dataon_text_response( $skill_actions, 'text/markdown; charset=UTF-8' );
    }

    if ( $path === '/.well-known/agent-skills/index.json' ) {
        $payload = array(
            '$schema' => 'https://agentskills.io/schemas/agent-skills-index.v0.2.0.json',
            'skills' => array(
                array(
                    'name' => 'site-overview',
                    'type' => 'knowledge',
                    'description' => 'High-level website context and navigation',
                    'url' => home_url( '/.well-known/agent-skills/site-overview.md' ),
                    'sha256' => dataon_sha256_b64( $skill_markdown ),
                ),
                array(
                    'name' => 'site-actions',
                    'type' => 'tooling',
                    'description' => 'Action-oriented guidance for interacting with this site',
                    'url' => home_url( '/.well-known/agent-skills/site-actions.md' ),
                    'sha256' => dataon_sha256_b64( $skill_actions ),
                ),
            ),
        );
        dataon_json_response( $payload );
    }
}
add_action( 'template_redirect', 'dataon_handle_well_known_endpoints', 1 );

function dataon_webmcp_footer_script() {
    if ( is_admin() ) {
        return;
    }
    ?>
    <script>
    (function () {
      if (!navigator.modelContext) {
        return;
      }

      var tools = [
        {
          name: 'open_page',
          description: 'Open a relative URL from this site.',
          inputSchema: {
            type: 'object',
            properties: { path: { type: 'string', description: 'Relative site path beginning with /' } },
            required: ['path']
          },
          execute: async function (input) {
            var path = (input && input.path) ? input.path : '/';
            window.location.href = path;
            return { ok: true, navigatedTo: path };
          }
        },
        {
          name: 'get_page_metadata',
          description: 'Return title, URL and meta description for the current page.',
          inputSchema: { type: 'object', properties: {} },
          execute: async function () {
            var metaDescription = document.querySelector('meta[name="description"]');
            return {
              title: document.title,
              url: window.location.href,
              description: metaDescription ? metaDescription.content : ''
            };
          }
        },
        {
          name: 'search_site',
          description: 'Search this site and navigate to the results page.',
          inputSchema: {
            type: 'object',
            properties: { query: { type: 'string', description: 'Search query string' } },
            required: ['query']
          },
          execute: async function (input) {
            var query = (input && input.query) ? String(input.query) : '';
            var target = '/?s=' + encodeURIComponent(query);
            window.location.href = target;
            return { ok: true, navigatedTo: target };
          }
        }
      ];

      // Always provide context for scanners/agents expecting provideContext on load.
      if (typeof navigator.modelContext.provideContext === 'function') {
        navigator.modelContext.provideContext({ tools: tools });
      }

      // Also register tools individually when supported.
      if (typeof navigator.modelContext.registerTool === 'function') {
        var controller = new AbortController();
        tools.forEach(function (tool) {
          navigator.modelContext.registerTool(tool, { signal: controller.signal });
        });
        window.addEventListener('pagehide', function () {
          controller.abort();
        }, { once: true });
      }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'dataon_webmcp_footer_script', 99 );