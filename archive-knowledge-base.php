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
    border: 1px solid #000;
    padding: 10px;
    margin: 2rem;
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
	width: 100px;
    vertical-align: baseline;
}

tr td 
{
	padding: 2px 0;
}

table 
{
	width: 100%;
}

.view_more 
{
    padding-top: 5rem;
    display: block;
}

.knowledge-base .col-12 
{
    margin-bottom: 50px;
}


</style>


<?php get_header(); ?>

<div class="banner" role="banner" style="";>

	<div class="container">
		<div class="row">
			<div class="col-12 col-lg-6 col-content">
				<h1>Knowledge Base</h1>
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

                        <?php   
                        $args5 = array(  
                            'post_type' => 'knowledge-base',
                            'posts_per_page' => 10, 
                            'orderby'   => 'meta_value',
                            'order'     => 'DESC',
                            'meta_key' => 'date', 
                            'tax_query' => array(
                                array(
                                'taxonomy' => 'knowledge-base-categories',
                                'field' => 'slug',
                                'terms' => 'azure-stack-hci',
                                ),
                            ),
                        );
                        $loop5 = new WP_Query( $args5 );?>

						<div class="col-12 col-lg-4">
							<div id="" class="kb-column">
								<h2>Azure Stack HCI</h2>
                                    <table>
                                        <tbody>


                                            <?php while ( $loop5->have_posts() ) : $loop5->the_post(); ?>
                                                
                                                <?php 
                                                $content_date = get_field('date');
                                                $content_title = get_field('title');
                                                $content_link = get_field('link'); 
                                                ?>

                                                <tr>
                                                    <td class="date"><?php echo $content_date; ?></td>
                                                    <td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
                                                </tr>
                                                        
                                            <?php endwhile;?>

                                        </tbody>
                                    </table>
                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-stack-hci/">View More</a>
							</div>
						</div>






                        <?php   
                    $args9 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'windows-server',
                        ),
                    ),
                );

                $loop9 = new WP_Query( $args9 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Windows Server</h2>

								<table>
									<tbody>


										<?php while ( $loop9->have_posts() ) : $loop9->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/windows-server/">View More</a>


							</div>
									
						</div>










                    <?php   
                    $args = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'azure-arc',
                        ),
                    ),
                );

                $loop = new WP_Query( $args ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Azure Arc</h2>

								<table>
									<tbody>


										<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                            <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-arc/">View More</a>

							</div>
									
						</div>





                        <?php   
                    $args8 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'windows-admin-center',
                        ),
                    ),
                );

                $loop8 = new WP_Query( $args8 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Windows Admin Center</h2>

								<table>
									<tbody>


										<?php while ( $loop8->have_posts() ) : $loop8->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                            <a class="view_more" href="https://dataon.io/knowledge-base-categories/windows-admin-center/">View More</a>


							</div>
									
						</div>





                        <?php   
                    $args6 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date',  
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'azure-virtual-desktop',
                        ),
                    ),
                );

                $loop6 = new WP_Query( $args6 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Azure Virtual Desktop</h2>

								<table>
									<tbody>


										<?php while ( $loop6->have_posts() ) : $loop6->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-virtual-desktop/">View More</a>


							</div>
									
						</div>





                        <?php   
                    $args3 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'azure-kubernetes-service',
                        ),
                    ),
                );

                $loop3 = new WP_Query( $args3 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Azure Kubernetes Service</h2>

								<table>
									<tbody>


										<?php while ( $loop3->have_posts() ) : $loop3->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-kubernetes-service/">View More</a>

							</div>
									
						</div>







                        <?php   
                    $args2 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'azure-file-sync',
                        ),
                    ),
                );

                $loop2 = new WP_Query( $args2 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Azure File Sync</h2>

								<table>
									<tbody>


										<?php while ( $loop2->have_posts() ) : $loop2->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-file-sync/">View More</a>

							</div>
									
						</div>








                        <?php   
                    $args4 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'azure-migrate',
                        ),
                    ),
                );

                $loop4 = new WP_Query( $args4 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Azure Migrate</h2>

								<table>
									<tbody>


										<?php while ( $loop4->have_posts() ) : $loop4->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/azure-migrate/">View More</a>

							</div>
									
						</div>



    


                        <?php   
                    $args7 = array(  
                    'post_type' => 'knowledge-base',
                    'posts_per_page' => 10, 
                    'orderby'   => 'meta_value',
                    'order'     => 'DESC',
                    'meta_key' => 'date', 
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'knowledge-base-categories',
                        'field' => 'slug',
                        'terms' => 'storage-migration-service',
                        ),
                    ),
                );

                $loop7 = new WP_Query( $args7 ); ?>

                    <div class="col-12 col-lg-4">

							<div id="" class="kb-column">

								<h2>Storage Migration Service</h2>

								<table>
									<tbody>


										<?php while ( $loop7->have_posts() ) : $loop7->the_post(); ?>
											
											<?php 
											$content_date = get_field('date');
											$content_title = get_field('title');
											$content_link = get_field('link'); 
											?>

											<tr>
												<td class="date"><?php echo $content_date; ?></td>
												<td class="article"><a target="_blank" href="<?php echo $content_link;?>"><?php echo $content_title; ?></a></td>
											</tr>
													
										<?php endwhile;?>

									</tbody>
								</table>

                                <a class="view_more" href="https://dataon.io/knowledge-base-categories/storage-migration-service/">View More</a>


							</div>
									
						</div>





					</div>

				</article>

            </div>
        </div>
    </div>
</div>







<?php get_template_part( 'template', 'cta' ); ?>


<?php get_footer(); ?>