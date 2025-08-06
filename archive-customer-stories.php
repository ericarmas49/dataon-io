<!-- /*  
*
* Template Name: Customer Stories Archive
*
*
*/ -->


<?php get_header(); ?>

<div id="main" class="main main-archive">
	
	<div class="container news-blog">

		<aside class="filters">

			<div class="banner news-section" role="banner">

				<div class="blog-cats">

					<?php 
					
					$categories = get_terms( 'customer-stories-category' ); ?>
						
					<ul class="news cat-list test">

						<li><input class="cat-check" type="checkbox" name="type" value="blog" /> All Projects</li>

						<?php foreach($categories as $category) : ?>

								<li class="<?php echo $category->name ?>">
									<input class="cat-check" type="checkbox" name="type" value="<?= $category->name; ?>" /> <?= $category->name; ?>
								</li>

						<?php endforeach; ?>

					</ul>

					<div id="GFG_DOWN"></div>

				</div>

			</div>


			<script>

			jQuery(function($){ 
        		$('.cat-check').on('click', function(e) { 
					$('.cat-check').prop('checked', false);
					$(this).prop('checked', true);
					let results = $("input:checkbox[name=type]:checked").val(); 

					if(results.length){
						// var str = results;
						var commaFirst = results.replace(',','');
						var stripped = commaFirst.replace('& ','');
						// console.log('stripped', stripped);
						results2 = stripped.replace(/\s+/g, '-').toLowerCase();
						// console.log(results2, 'query final'); // "sonic-free-games"

						if (results2 === 'cloud-hosting,-msps-and-csps'){
							results2 = 'cloud-hosting-msp-csp';
						}

						$.ajax({
							type: 'POST',
							url: '/wp-admin/admin-ajax.php',
							dataType: 'html',
							data: {
								action: 'filter_customer_stories',
								// category: $(this).data('slug'),
								category: results2,
							},
							success: function(res) {
								$('.project-tiles').html(res);
								}
						});
					}
				});
			});

    </script> 

		</aside>

		<div class="content">

			<div class="featured upper">

				<div class="featured-blog left">

					<?php 
					
					$args = array(
						'post_type'      => 'customer-stories',
						'posts_per_page' => 1,
					);
					$the_query = new WP_Query( $args ); ?>

					<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div class="row">
									<div class="">
										<?php
										$postID = get_the_ID(); 
										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
										}
										?>
										<a href="<?php the_permalink(); ?>" >
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
										</a>
									</div>
									<div class="col-post-content">
										<header>
										<div class="post-cat">
										<?php $categories = get_the_category();
												if ( ! empty( $categories ) ) { ?>
												<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
											<?php } ?>
										</div>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_excerpt(); ?></a></h2>
												<a href="<?php the_permalink(); ?>">Read more</a>
											</header>
											<?php 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>
					<?php endwhile; endif; ?>

				</div>

				<div class="featured-blog right">

					<?php $args2 = array(
						'post_type'      => 'customer-stories',
						'posts_per_page' => 1,
						'offset' => 1
					);
					$the_query2 = new WP_Query( $args2 ); ?>

					<?php if ( $the_query2->have_posts() ) : while ( $the_query2->have_posts() ) : $the_query2->the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="row">
							<div class="">
								<?php
								$postID = get_the_ID(); 
								$postImage = get_the_post_thumbnail_url($postID, 'full');
								if(empty($postImage)) {
									$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
								}
								?>
								<a href="<?php the_permalink(); ?>" >
									<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
								</a>
							</div>
							<div class="col-post-content">
								<header>
									<div class="post-cat">
									<?php $categories = get_the_category();
											if ( ! empty( $categories ) ) { ?>
											<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
										<?php } ?>
									</div>
										<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_excerpt(); ?></a></h2>
										<a href="<?php the_permalink(); ?>">Read more</a>
									</header>
									<?php 
								//	echo get_the_excerpt(); 
									?>
									<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
							</div>
						</div>
					</article>

					<?php endwhile; endif; ?>

				</div>

			</div>

			<div class='project-tiles blogs-filtered lower row'>

					
					<?php
					global $post;
					$post = get_field('blog_fp', 'option');
					$fpPostID = $post->ID;
					
					// if(!empty($post)) :
					?>
						<!-- <article id="post-<?php echo $fpPostID; ?>" class="post post-featured col-lg-4">
							<div class="row">
								<div class="col-12">
									<?php
								$fpPostImage = get_the_post_thumbnail_url($fpPostID, 'full');
								if(empty($fpPostImage)) {
									echo '<div class="post-img" style="background-image: url(https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg); "></div>';
								} else {
									echo '<div class="post-img" style="background-image: url('.$fpPostImage.'); background-size: cover; "></div>';
								}
								?>
								</div>
								<div class="col-12 col-post-content">
									<h2 class="entry-title">
										<a href="<?php echo get_permalink($fpPostID); ?>" title="<?php echo $post->post_title; ?>" rel="bookmark"><?php echo $post->post_title; ?></a>
									</h2>
									<?php
									setup_postdata($post);
									echo get_the_excerpt();
									// wp_reset_postdata(); 
									?>
								</div>
							</div>
						</article> -->
					<?php // endif; ?>


					<?php $args3 = array(
						'post_type'      => 'customer-stories',
						'posts_per_page' => 9,
						'offset' => 2
					);
					$the_query3 = new WP_Query( $args3 ); ?>

					<div class="row">
						<div class="col-12 cont-content">
						<?php if ( $the_query3->have_posts() ) : while ( $the_query3->have_posts() ) : $the_query3->the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 post'); ?>>
								<div class="row">
									<div class="col-12">
										<?php
										$postID = get_the_ID(); 
										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
										}
										?>
										<a href="<?php the_permalink(); ?>" >
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
										</a>
									</div>
									<div class="col-12 col-post-content">
										<header>
										<div class="post-cat">
											<?php $categories = get_the_category();
												if ( ! empty( $categories ) ) { ?>
												<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
											<?php } ?>
										</div>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_excerpt(); ?></a></h2>
												<?php // edit_post_link(); ?>
												<a href="<?php the_permalink(); ?>">Read more</a>
											</header>
											<?php 
										//	echo get_the_excerpt(); 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>
							<?php endwhile; endif; 

							global $wp_query;

							if (  $wp_query->max_num_pages > 1 )
								echo '<div class="misha_loadmore">More customer stories</div>'; // you can use <a> as well
							?>






							<?php // wp_pagenavi(); ?>
						</div>
					</div> <!-- end Row -->
				</div>
			</div>

			</div>

		</div>


		<style>


		.news-blog 
		{
			display: flex;
		}

		ul li 
		{
			padding-left: 0px;
		}

		.filters 
		{
			flex: 1;
		}

		.news-blog .content 
		{
			flex: 3;
		}

		ul.news 
		{

		}

		.news.cat-list
		{
			flex-direction: column;
		}

		.featured-left 
		{
			display: flex;
		}

		.featured-blog 
		{
			flex: 1;
		}

		.featured.upper 
		{
			display: flex;
		}

		.featured-blog.left article 
		{
			margin-right: 2rem;
		}

		.featured-blog.right article 
		{
			margin-left: 2rem;
		}



		.post .row 
		{
			flex-direction: column;
		}

		.cont-content
		{
			display: flex;
			flex-wrap: wrap;
		}

		article.post 
		{
			padding: 0 20px;
			margin: 3rem 0;
		}

		article.post .post-img
		{
			min-height: 170px;
		}

		.customer-stories h2, .type-customer-stories h2
		{
			font-size: 2rem;
			margin-top: -10px;
		}

		.col-post-content
		{
			padding: 0 1rem;
		}
 
		.post-cat h5
		{

		}

		.featured.upper article.post .post-img
		{
			min-height: 250px;
		}


		</style>

	</div>


<?php get_footer(); ?>