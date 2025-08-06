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

<div id="main" class="main main-home">
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

<section class="container-fluid cta">
    <div class="container">
        <div class="row cta">
            <div class="col-12 col-lg-6 col-md-12 cta-content">

                <h4>No one knows Microsoft hybrid cloud like DataON</h4>
                <p role="heading" aria-label="We can help">We can help you make the leap to hybrid cloud</p>

            </div>


            <div class="col-12 col-lg-6 col-md-12 cta-items">

                <div class="row">

                <div class="col-12 col-lg-3 col-md-6 col-sm-12 call">
                    <a tabindex="0" href="tel:1-888-726-8588">
                        <div class="cta-icon">
                            <img alt="Call DataON" src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Call.svg" />
                            Call DataON
                        </div>
                    </a>
                </div>

                
                <div id="chatNow" onclick="liveChat()" class="col-12 col-lg-3 col-md-6 col-sm-12 chat">
                    
                            <div class="cta-icon" tabindex="0">
                                <img alt="Chat Now" src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Chat-now.svg" />
                                Chat Now
                            </div>

                </div>
                
                <div class="col-12 col-lg-3 col-md-6 col-sm-12 email-call">
                    <a aria-label="Email Sales" href="mailto:sales@dataonstorage.com?subject=Please Contact Me about Azure Hybrid Cloud" target="_blank">
                        <div class="cta-icon">
                            <img alt="Email Sales" src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Email-Sales.svg" />
                            Email Sales
                        </div>
                    </a>
                </div>

                <div class="col-12 col-lg-3 col-md-6 col-sm-12 email-support">
                    <a aria-label="Email Support" href="mailto:support@dataonstorage.com?subject=I Need Help with my Azure Hybrid Cloud Deployment" target="_blank">
                        <div class="cta-icon">
                            <img alt="Email Support" src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Email-Support.svg" />
                            Email Support
                        </div>
                    </a>
                </div>

                </div>

            </div>
        </div>
    </div>
</section>

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