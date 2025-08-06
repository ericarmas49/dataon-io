<!-- /*  
*
* Template Name: Knowledge Base
*
*
*/ -->


<style>

.entry-content 
{
    display: flex;
    flex-wrap: wrap;
}

.entry-content .col-4 
{
    margin: 2rem 0;
}

.kb-column
{
    padding: 2rem;
    border: 1px solid #000;
    margin: 1rem;
    height: 100%;
}

.kb-column .content 
{
    display: flex;
    margin-bottom: 1rem;
}

</style>


<?php get_header(); 

$banner = get_field('hero');
$bgColor = $banner['background_color'];
$bgImg = $banner['background_image'];
 
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
    </div>
</div>

        </div>



<div id="main" class="main main-single">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-12">

                
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <div class="entry-content" itemprop="mainContentOfPage">
                       
                        <div class="col-4">

                            <div id="" class="kb-column">

                                <h2><?php echo $azure_stack['knowledge_base_title']; ?></h2>


                                <?php if( have_rows('content') ): while ( have_rows('content') ) : the_row(); 
                                    
                                    
                                    $content_date = get_sub_field('date');
                                    $content_title = get_sub_field('title');
                                    $content_link = get_sub_field('link'); 
                                    $content_cat = get_sub_field('category');
                                    
                                    ?>

                                        <div class="content col-12">

                                            <div class="col-3">
                                                <?php echo $content_date; ?>
                                            </div>
                                            <div class="col-9">
                                                <a href="<?php echo $content_link;?>"><?php echo $content_title; ?></a>
                                            </div>

                                        </div>
                                    
                                <?php // End loop.
                                endwhile; endif; ?>

                            </div>
                                
                        </div>

                    </div>

                </article>


            </div>
        </div>
    </div>
</div>







<section class="container-fluid cta">
    <div class="container">
        <div class="row cta">
            <div class="col-12 col-lg-6 col-md-12 cta-content">

                <h4>No one knows Microsoft hybrid cloud like DataON</h4>
                <p>We can help you make the leap to hybrid cloud</p>

            </div>


            <div class="col-12 col-lg-6 col-md-12 cta-items">

                <div class="row">

                <div class="col-12 col-lg-3 col-md-6 col-sm-12 call">
                    <a href="tel:1-888-726-8588">
                        <div class="cta-icon">
                            <img src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Call.svg" />
                            Call DataON
                        </div>
                    </a>
                </div>

                
                <div class="col-12 col-lg-3 col-md-6 col-sm-12 chat">
                    <a href="#">
                            <div class="cta-icon">
                                <img src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Chat-now.svg" />
                                Chat Now
                            </div>
                    </a>
                </div>
                
                <div class="col-12 col-lg-3 col-md-6 col-sm-12 email-call">
                    <a href="mailto:sales@dataonstorage.com?subject=Please Contact Me about Azure Hybrid Cloud" target="_blank">
                        <div class="cta-icon">
                            <img src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Email-Sales.svg" />
                            Email Sales
                        </div>
                    </a>
                </div>

                <div class="col-12 col-lg-3 col-md-6 col-sm-12 email-support">
                    <a href="mailto:support@dataonstorage.com?subject=I Need Help with my Azure Hybrid Cloud Deployment" target="_blank">
                        <div class="cta-icon">
                            <img src="https://dataon.wpengine.com/wp-content/uploads/2023/12/Email-Support.svg" />
                            Email Support
                        </div>
                    </a>
                </div>

                </div>

            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>