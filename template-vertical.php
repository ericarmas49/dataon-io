<!-- /*  
*
* Template Name: Vertical
*
*
*/ -->


<?php get_header(); ?>

<style>
/* Add to Calendar Button Styling */
.add-to-calendar-wrapper {
    margin: 30px 0;
    text-align: center;
}

.addeventatc {
    display: inline-block;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    font-family: inherit;
}

.addeventatc:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white !important;
}

.addeventatc:active {
    transform: translateY(0);
}

/* Hide the hidden spans */
.addeventatc .start,
.addeventatc .end,
.addeventatc .timezone,
.addeventatc .title,
.addeventatc .description,
.addeventatc .location {
    display: none;
}

/* Style the dropdown menu */
.addeventatc_dropdown {
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    border: 1px solid #e0e0e0 !important;
}

.addeventatc_dropdown a {
    padding: 12px 16px !important;
    transition: background 0.2s !important;
}

.addeventatc_dropdown a:hover {
    background: #f8f9fa !important;
}
</style>

<?php
// $banner = get_field('banner');
// $bgColor = '';
// $bgImg = $banner['vt_banner_image'];
// $bannerTitle = $banner['vt_banner_title'];
// $bannerText = $banner['vt_banner_text'];
?>
<?php 
// if(!empty($bgImg)) {
//     echo '<div class="banner banner-vertical" role="banner" style="background-image: url('.$bgImg.')";>';
// } else {
//     echo '<div class="banner banner-vertical" role="banner" style="background-color:'.$bgColor.'";>';
// }
?>
    <!-- <div class="container">
        <div class="row">
            <div class="col-12 col-lg-6 col-content">
                <?php 
                if(!empty($bannerTitle)) {
                    echo '<h1>'.$bannerTitle.'</h1>';
                } else {
                    echo '<h1>'.get_the_title().'</h1>';
                }
                if(!empty($bannerText)) {
                    echo $bannerText;
                }
                ?>
            </div>
            <div class="col-12 col-lg-6">
                
            </div>
        </div>
    </div>
</div> -->

<?php 

$banner = get_field('hero');
$bgColor = $banner['background_color'];
$bgImg = $banner['background_image'];
?>
<?php 
if(!empty($bgImg)) {
	echo '<div class="banner" role="banner" style="background-image: url('.$bgImg.')";>';
} else {
	echo '<div class="banner" role="banner" style="background-color:'.$bgColor.'";>';
}
?>
	<div class="container">
		<div class="row">
			<div class="col-12 col-lg-6 col-content">
				<?php
				if(!empty($banner['storage_profile'])) {
					echo '<h2 class="title-stor-pfl">'.$banner['storage_profile'].'</h2>';
				}
				if(!empty($banner['project_name_a'])) {
					echo '<h1 class="title-proj-name">'.$banner['project_name_a'].'</h1>';
				} else {
					echo '<h1 class="title-proj-name">'.get_the_title().'</h1>';
				}
				if(!empty($banner['project_name_b'])) {
					echo '<h2 class="title-proj-name-b">'.$banner['project_name_b'].'</h2>';
				}
				if(!empty($banner['optimization_profile'])) {
					echo '<h2 class="title-opt-pfl">'.$banner['optimization_profile'].'</h2>';
				}
				?>
			</div>
			<div class="col-12 col-lg-6 col-img">
				<?php
				if(!empty($banner['featured_image'])) {
					echo '<img src="'.$banner['featured_image'].'" alt="Feature Image"/>';
				} else {
					$featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'full');
					if(!empty($featuredImage)) {
						echo '<img src="'.$featuredImage.'" alt="Feature Image"/>';
					}
				}
				?>
			</div>
		</div>
	</div>
</div>





<div id="main" class="main main-single">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-12">
                <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="entry-content" itemprop="mainContentOfPage">
                        <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); } ?>
                        <?php the_content(); ?>
                        
                        <?php
                        // Add to Calendar button (if event has a date)
                        $calendar_button = dataon_add_to_calendar_button();
                        if ( ! empty( $calendar_button ) ) {
                            echo '<div class="add-to-calendar-wrapper" style="margin: 30px 0;">';
                            echo $calendar_button;
                            echo '</div>';
                        }
                        ?>
                        
                        <div class="entry-links"><?php wp_link_pages(); ?></div>
                    </div>
                </article>
                <?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part( 'template', 'cta' ); ?>


<?php get_footer(); ?>