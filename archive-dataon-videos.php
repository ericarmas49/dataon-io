<!-- /*  
*
* Template Name: DataOn Videos
*
*
*/ -->


<?php get_header(); ?>

<div id="main" class="main main-archive rrr">
	
	<div class="container news-blog">

		<aside class="filters">

			<div class="banner news-section" role="banner">

				<div class="blog-cats">

					<?php

					echo '<ul><li><input class="cat-check" type="checkbox" name="type" value="blog" />ALL VIDEOS</li></ul>';  // Output term name

					// Recursive function to display terms hierarchically
					function display_terms_hierarchy($parent = 0, $taxonomy = 'dataon-videos-category') {
						// Get terms for the taxonomy
						$terms = get_terms(array(
							'taxonomy' => $taxonomy,
							'parent' => $parent,    // Get child terms of the current parent
							'hide_empty' => true,  // Show empty terms as well
              				'hierarchical' => true
						));

						if (!empty($terms) && !is_wp_error($terms)) {
							echo '<ul>';

							foreach ($terms as $term) { ?>
                
								<?php echo '<li><input class="cat-check" type="checkbox" name="type" value="' . esc_html($term->name) . '" />' . esc_html($term->name);  // Output term name
								display_terms_hierarchy($term->term_id, $taxonomy);  // Recursive call for children
								echo '</li>';
							}
							echo '</ul>';
						}
					}

					// Call the function to display terms starting from the top level
					display_terms_hierarchy();
					?>

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
						console.log('stripped', stripped);
						results2 = stripped.replace(/\s+/g, '-').toLowerCase();
						console.log(results2, 'query final'); // "sonic-free-games"

            switch (results2) {
              case "events-webinars":
                results2 = "dataon-events-webinars"
                break;
              case "news-announcements":
                results2 = "dataon-news-announcements"
                break;
              case "servers-cpus,-gpus,-networking-storage":
                results2 = "servers"
                break;
            //   default:
            //   results2 = "blog"
            }



						$.ajax({
							type: 'POST',
							url: '/wp-admin/admin-ajax.php',
							dataType: 'html',
							data: {
								action: 'filter_videos',
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
						'post_type'      => 'dataon-videos',
						'posts_per_page' => 1,
					);
					$the_query = new WP_Query( $args );


					if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div class="row">
									<div class="">
										<?php
										$postID = get_the_ID(); 
										
										$str = get_field('youtube_video_url', $postID ); 

										if (str_contains($str, 'watch')) {
											$str1 = substr($str, 32); 
					
										} else {
					
										$str1 = substr($str, 17);

										}

										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											// $postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
											$postImage = 'https://i.ytimg.com/vi/' . $str1 . '/hqdefault.jpg';
										}
										?>        
										
										<a href="<?php echo get_permalink($postID); ?>" title="<?php echo $post->post_title; ?>" rel="bookmark">
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
										</a>
									</div>
									<div class="col-post-content">
										<header>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?><p></a></h2>
											</header>
											<?php 
											echo get_the_excerpt(); 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>
					<?php endwhile; endif; ?>

				</div>

				<div class="featured-blog right">

					<?php $args2 = array(
						'post_type'      => 'dataon-videos',
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
									$str = get_field('youtube_video_url', $postID ); 

										if (str_contains($str, 'watch')) {
											$str1 = substr($str, 32); 
					
										} else {
					
										$str1 = substr($str, 17);

										}

										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											// $postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
											$postImage = 'https://i.ytimg.com/vi/' . $str1 . '/hqdefault.jpg';
										}
										?>      
									<a href="<?php echo get_permalink($postID); ?>" title="<?php echo $post->post_title; ?>" rel="bookmark">
										<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
									</a>
								</div>
								<div class="col-post-content">
									<header>
											<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?></p></a></h2>
										</header>
										<?php 
										echo get_the_excerpt(); 
										?>
										<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
								</div>
							</div>
						</article>

					<?php endwhile; endif; ?>

				</div>

			</div>

			<div class='project-tiles blogs-filtered lower'>

			<?php $args3 = array(
						'post_type'      => 'dataon-videos',
						'posts_per_page' => 12,
						'offset' => 2
					);
					$the_query3 = new WP_Query( $args3 ); ?>

					
					<?php
					global $post;
					$post = get_field('blog_fp', 'option');
					$fpPostID = $post->ID; ?>

					<div class="row">
						<div class="col-12 video-content">
						<?php if ( $the_query3->have_posts() ) : while ( $the_query3->have_posts() ) : $the_query3->the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class('col-lg-4'); ?>>
								<div class="row">
									<div class="col-12">
										<?php
										$postID = get_the_ID(); 
										$str = get_field('youtube_video_url', $postID ); 

										if (str_contains($str, 'watch')) {
											$str1 = substr($str, 32); 
					
										} else {
					
										$str1 = substr($str, 17);

										}

										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											// $postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
											$postImage = 'https://i.ytimg.com/vi/' . $str1 . '/hqdefault.jpg';
										}
										?>      
										<a href="<?php echo get_permalink($postID); ?>" title="<?php echo $post->post_title; ?>" rel="bookmark">
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
										</a>
									</div>
									<div class="col-12  col-post-content">
										<header>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?></p></a></h2>
											</header>
											<?php 
											echo get_the_excerpt(); 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>
							<?php endwhile; endif; 

							global $wp_query;

							if (  $wp_query->max_num_pages > 1 )
								echo '<div class="misha_loadmore">More posts</div>'; // you can use <a> as well
							?>






							<?php // wp_pagenavi(); ?>
						</div>
					</div> <!-- end Row -->
				</div>
			</div>

			</div>

		</div>


		<style>

		.video-content 
		{
			display: flex;
			flex-wrap: wrap;
		}

		.misha_loadmore{
			background-color: #ddd;
			border-radius: 2px;
			display: block;
			text-align: center;
			font-size: 14px;
			font-size: 0.875rem;
			font-weight: 800;
			letter-spacing:1px;
			cursor:pointer;
			text-transform: uppercase;
			padding: 10px 0;
			transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.3s ease-in-out;  
		}
		.misha_loadmore:hover{
			background-color: #767676;
			color: #fff;
		}

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

		body.post-type-archive-dataon-videos .entry-title
		{
			font-size: 2rem;
			margin-top: -10px;
		}

		article.dataon-videos
		{
			padding: 0 20px;
			margin: 3rem 0;
		}

		article.dataon-videos .post-img
		{
			min-height: 170px;
		}

		.col-post-content
		{
			padding: 0 1rem;
		}

		.featured.upper article.dataon-videos .post-img
		{
			min-height: 250px;
		}

		.project-tiles.blogs-filtered.lower
		{
			display: flex;
			flex-wrap: wrap;
		}

		li.sub-cat 
		{
			padding-left: 2rem;
		}

		article.dataon-videos .post-img
		{
			margin-bottom: 0px;
		}

    ul, ul li
    {
      list-style-type: none;
    }

		</style>

	</div>


<?php get_footer(); ?>