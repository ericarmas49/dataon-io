<?php

// Adding js library being used by blocks
function do_selectively_enqueue_admin_script( $hook ) {
  if ( 'post.php' != $hook ) {
    return;
  }
  // wp_enqueue_style( 'admin-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap', array(), '1.0' );
  // wp_enqueue_style( 'admin-slick', get_template_directory_uri() . '/templates-cb/assets/css/slick.css', array(), '1.0' );
  // wp_enqueue_script( 'admin-slick', get_template_directory_uri() . '/templates-cb/assets/js/slick.js', array('jquery'), '1.0', true );
  // wp_enqueue_script( 'admin-hubspot', '//js.hsforms.net/forms/embed/v2.js', array(), '1.0', true );
}
add_action( 'admin_enqueue_scripts', 'do_selectively_enqueue_admin_script' );


// ACF Pro below v6
add_action('acf/init', 'do_acf_blocks_init');

function do_acf_blocks_init() {
  // Check function exists.
  if( function_exists('acf_register_block_type') ) {

    // Register a chapter block.
    acf_register_block_type(array(
      'name'              => 'do-home-banner',
      'title'             => __('Home Banner'),
      'description'       => __('A custom home banner block.'),
      'render_template'   => '/modules/acf-blocks/do-home-banner/do-home-banner.php',
      'enqueue_style'     => '',
      'category'          => 'formatting'
    ));
  }
}