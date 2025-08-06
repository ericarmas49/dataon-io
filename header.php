<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php blankslate_schema_type(); ?>>
<head>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7SY0LJPFVC"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-7SY0LJPFVC');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-P9W5CQKS');</script>
    <!-- End Google Tag Manager -->

    <!-- <meta name="description" content="Dataon.io - Azure HCI"> -->
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYfZ8osu1Nd6Z89ify25QV9guujx43ITvfi12/QExE" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js" integrity="sha384-Y4oOpwW3duJdCWv5ly8SCFYWqFDsfob/3GkgExXKV4idmbt98QcxXYs9UoXAB7BZ" crossorigin="anonymous"></script> -->

    <!--- JQUERY --->
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script> -->

    <!-- FONTAWESOME --->
    <link href="<?php echo get_template_directory_uri(); ?>/assets/fontawesome/css/fontawesome.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/fontawesome/css/brands.css" rel="stylesheet">
    <link href="<?php echo get_template_directory_uri(); ?>/assets/fontawesome/css/solid.css" rel="stylesheet">

    <meta name="google-site-verification" content="pjAvqvXPwzwFyeaPFP__7mDTLRGoGfFZxakpkhmDenw" />


    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P9W5CQKS"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <?php wp_body_open(); ?>

    <div id="wrapper" class="hfeed container-fluid gx-0">

        <header id="header" role="banner">

            <nav class="navbar navbar-expand-xl container" data-bs-theme="dark" role="navigation">

                <div class="container">

                    <div id="branding">
                        <div id="site-title" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                        <?php echo get_custom_logo(); ?>
                        </div>
                        <div id="site-description"<?php if ( !is_single() ) { echo ' itemprop="description"'; } ?>><?php bloginfo( 'description' ); ?></div>
                    </div>

                    <!-- Brand and toggle get grouped for better mobile display -->
                    <button 
                        class="navbar-toggler" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#navbarSupportedContent" 
                        aria-controls="navbarSupportedContent" 
                        aria-expanded="false" 
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <!-- <a class="navbar-brand" href="#">Navbar</a> -->
                    <?php
                    // wp_nav_menu( array(
                    //     'theme_location'    => 'main-menu',
                    //     'depth'             => 2,
                    //     'container'         => 'div',
                    //     'container_class'   => 'collapse navbar-collapse',
                    //     'container_id'      => 'bs-example-navbar-collapse-1',
                    //     'menu_class'        => 'nav navbar-nav',
                    //     'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                    //     'walker'            => new WP_Bootstrap_Navwalker(),
                    // ) );
                    ?>
                    <?php
                    $mainmenu = get_field('menu', 'option');
                    if(!empty($mainmenu)) :
                    ?>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <?php 
                            foreach($mainmenu as $m) {
                                if(!empty($m['add_dropdown'])) {
                                    $dropdownMenuType = $m['dropdown_menu_type'];
                                    echo '<li class="nav-item dropdown '.$dropdownMenuType.'">';
                                        echo '<a class="nav-link dropdown-toggle" href="#" aria-haspopup="true" role="button" data-bs-toggle="dropdown" aria-expanded="false">'.$m['menu_label'].'</a>';

                                    if($dropdownMenuType === 'dropdown-megamenu') {
                                        echo '
                                            <div class="dropdown-menu">
                                                <div class="container-fluid">
                                                    <div class="row">';
                                                        $dropdownCol = $m['dropdown_mega_menu']['mega_menu_column'];
                                                        if(!empty($dropdownCol)) {
                                                            foreach($dropdownCol as $dc) {
                                                                echo '<div class="col-lg-3">';
                                                                    $listGroup = $dc['megamenu_listgroup'];
                                                                    if(!empty($listGroup)) {
                                                                        foreach($listGroup as $lg) {
                                                                            echo '<div class="megamenu-listgroup">';
                                                                                echo '<div class="title-container">';
                                                                                    if ($lg['listgroup_icon']) {
                                                                                    echo '<img src="' . $lg['listgroup_icon'] .'" />';
                                                                                    } else {
                                                                                        echo '<img src="https://dataon.wpengine.com/wp-content/uploads/2023/07/4-solid-left-top-50x50-1.png" />';
                                                                                    }
                                                                                    echo '<h5>'.$lg['listgroup_title'].'</h5>';
                                                                                echo '</div>';
                                                                                $listGroupLinks = $lg['listgroup_link'];                                                                                
                                                                                if(!empty($listGroupLinks)) {
                                                                                    echo '<ul>';
                                                                                        foreach($listGroupLinks as $l) {
                                                                                            echo '<li><a class="nav-link" href="'.$l['menu_link'].'">'.$l['menu_label'].'</a></li>';
                                                                                        }
                                                                                    echo '</ul>';
                                                                                }
                                                                            echo '</div>';
                                                                        } 
                                                                    }
                                                                echo '</div>';
                                                            }
                                                        }
                                                echo '</div>';
                                            echo '</div>';
                                        echo '</div>';

                                    } else {
                                        $dropdownMenu = $m['dropdown_menu'];
                                        if(!empty($dropdownMenu)) {
                                            echo '<div class="dropdown-menu">';
                                                echo '<ul>';
                                                    foreach($dropdownMenu as $dm) {
                                                        echo '<li><a class="dropdown-item" href="'.$dm['menu_link'].'">'.$dm['menu_label'].'</a></li>';
                                                    }
                                                echo '</ul>';
                                            echo '</div>';
                                        }
                                    }
                                
                                } else {
                                    echo '<li class="nav-item">';
                                        echo '<a class="nav-link" href="'.$m['menu_link'].'">'.$m['menu_label'].'</a>';
                                }

                                echo '</li>';
                            }
                            ?>
                        </ul>
                        <?php get_search_form(); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- <a href="#" class="btn-search">Search</a> -->
            
            </nav>
        
        </header>

        <div id="container-fluid">

