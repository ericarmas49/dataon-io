<?php get_header(); ?>

<?php 
$banner = get_field('hero');
$bgColor = $banner['background_color'];
$bgImg = $banner['background_image'];
?>
<?php 
if(!empty($bgImg)) {
	echo '<div class="banner" role="banner" style="background-position: right top; background-size: cover; background-image: url('.$bgImg.')";>';
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
                    echo '<style>.banner {background: transparent !important;}</style>';
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

<main id="main" class="main">
    <div class="container">

        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

            <section id="post-<?php the_ID(); ?>" <?php post_class('container'); ?> aria-label="<?php echo esc_attr( get_the_title() ); ?>">

                <header class="header">

                <?php if(empty($banner['project_name_a'])) { ?>
                    <h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1> <?php edit_post_link(); ?>
                <?php } ?>
                </header>

                <div class="entry-content" itemprop="mainContentOfPage">
                    
                    <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full', array( 'itemprop' => 'image' ) ); } ?>
                    <?php the_content(); ?>
                    
                    <div class="entry-links"><?php wp_link_pages(); ?></div>

                </div>

            </section>

            <?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>

        <?php endwhile; endif; ?>

    </div>
</main>

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