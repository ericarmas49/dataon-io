<!-- /*  
*
* Template Name: Home
*
*
*/ -->

<style>

#home-banner-block_647e730909db1 .fp-container
{
    padding: 0px !important;
    transition: .5s;
}

#home-banner-block_647e730909db1 .fp-container .fp-text
{
    height: 150px;
    width: 100%;
    transition: .5s;
    display:flex;
	flex-direction:column;
	justify-content:center;
}

#home-banner-block_647e730909db1 .fp-container .fp-text h2
{
    font-size: 2rem  !important;
    font-weight: 500;
    text-align: left;
}

.fp-text img 
{
    filter: invert(1);
    width: 50px;
	height: 50px;
}


.fp-container.active
{
    	transition: .5s;
}

.mod-fp:nth-child(1) .fp-container.active
{
    background: #f1511b !important;

}

.mod-fp:nth-child(2) .fp-container.active
{
    background: #80cc28 !important;
}

.mod-fp:nth-child(3) .fp-container.active
{
    background: #00adef !important;

}

.mod-fp:nth-child(4) .fp-container.active
{
    background: #fbbc09 !important;

}



#home-banner-block_647e730909db1 .fp-container.active .fp-text
{
	height: 100%;
	transition: 1s;
	background-color: transparent;
	display:flex;
	flex-direction:column;
	justify-content:center;
}


</style>

<?php get_header(); ?>



<?php
$banner = get_field('banner');
$bgColor = '';
$bgImg = $banner['vt_banner_image'];
$bannerTitle = $banner['vt_banner_title'];
$bannerText = $banner['vt_banner_text'];
?>
<?php 
if(!empty($bgImg)) {
    //echo '<div class="banner banner-vertical" role="banner" style="background-image: url('.$bgImg.')";>';
} else {
    //echo '<div class="banner banner-vertical" role="banner" style="background-color:'.$bgColor.'";>';
}
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

<main id="main" class="main main-home">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <section id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-label="<?php echo esc_attr( get_the_title() ); ?>">
        <div class="entry-content" itemprop="mainContentOfPage">
            <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); } ?>
            <?php the_content(); ?>
            <div class="entry-links"><?php wp_link_pages(); ?></div>
        </div>
    </section>
    <?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>
    <?php endwhile; endif; ?>
</main>

<?php get_template_part('section', 'home-cta'); ?>

<script>

jQuery('.fp-container').mouseover(function(){
    jQuery(this).addClass('active');
  }),

  jQuery('.fp-container').mouseout(function(){
    jQuery(this).removeClass('active');
  }),
  
  jQuery('.fp-text').mouseover(function(){
    jQuery(this).parent.addClass('active');
  }),

//   jQuery('.fp-text').mouseout(function(){
//     jQuery(this).parent.removeClass('active');
//   }),

</script>



<?php get_footer(); ?>