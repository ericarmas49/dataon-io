<!-- /*  
*
* Template Name: Vertical
*
*
*/ -->


<?php get_header(); ?>

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
                        <div class="entry-links"><?php wp_link_pages(); ?></div>
                    </div>
                </article>
                <?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_template_part('section', 'home-cta'); ?>


<?php get_footer(); ?>