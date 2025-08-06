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
    height: 100%;
}

.kb-column .content 
{
    display: flex;
    margin-bottom: 1rem;
}

.archive .banner 
{
	min-height: auto;
}

.archive .banner h1 
{
	margin: 4rem 0 0 0;
	color: #000;
}

td.date 
{
	width: 150px;
}

tr td 
{
	padding: 10px;
}

th 
{
	font-size: 25px;
}

tr:nth-child(even) 
{
	background: #f0f0f0;
}

table 
{
	width: 100%;
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
            <h1 class="entry-title" itemprop="name"><?php single_term_title(); ?></h1>
			<div class="archive-meta" itemprop="description"><?php if ( '' != get_the_archive_description() ) { echo esc_html( get_the_archive_description() ); } ?></div>
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
					
						<div class="col-12">

							<div id="" class="kb-column">

								<h2><?php echo $azure_stack['knowledge_base_title']; ?></h2>

								<table>
									<tbody>
										<tr>
											<th class="date">Date</th>
											<th class="article">Article</th>
										</tr>

										<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile; endif; ?>

									</tbody>
								</table>


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