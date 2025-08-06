<?php get_header(); ?>

<div id="main" class="main main-archive">
	
	<div class="container news-blog">

		<aside class="filters">

			<div class="banner news-section" role="banner">

	      <div class="blog-cats">

		  <?php echo '<ul><li><input class="cat-check" type="checkbox" name="type" value="blog" />ALL POSTS</li></ul>';  // Output term name

          $categories = get_categories(array(
              'orderby' => 'name',        // Order categories by name
              'order' => 'ASC',           // Ascending order
              'hide_empty' => false,      // Show empty categories
              'hierarchical' => true,     // Fetch categories hierarchically
          ));

          function display_categories_hierarchically($categories, $parent_id = 0) {
            $has_children = false;

            foreach ($categories as $category) {
              if ($category->parent == $parent_id) {
                if (!$has_children) {
                  $has_children = true;
                  echo '<ul>';
                }

                echo '<li> <input class="cat-check" type="checkbox" name="type" value="' . $category->name . '" />' . esc_html($category->name) . '';
                // Recursive call to fetch child categories
                display_categories_hierarchically($categories, $category->term_id);
                echo '</li>';
              }
            }

            if ($has_children) {
                echo '</ul>';
            }
          }

        display_categories_hierarchically($categories); ?>
       

				<div id="GFG_DOWN"></div>

			</div>

		</div>

    <script>

      jQuery(function($){ 
        $('.cat-list_item').on('click', function() {
          $('.cat-list_item').removeClass('active');
          $(this).addClass('active');
          var res = $(this).html();
        //   console.log('res', res);
          var div1 = '<h2 style="margin: 2rem 0;" class="news-title">';
          var div2 = '</h2>';
          var complete = div1.concat(res, div2);
          $('.news-title').replaceWith(complete);

          $.ajax({
            type: 'POST',
            url: '/wp-admin/admin-ajax.php',
            dataType: 'html',
            data: {
            action: 'filter_projects',
            category: $(this).data('slug'),
            },
            success: function(res) {
            $('.project-tiles').html(res);
            }
          })
        });
      });

      jQuery(function($){ 
            $('.cat-check').on('click', function(e) { 
          $('.cat-check').prop('checked', false);
          $(this).prop('checked', true);
          let results = $("input:checkbox[name=type]:checked").val(); 

          if(results.length){
            // var str = results;
            var commaFirst = results.replace(',','');
            var stripped = commaFirst.replace('& ','');
            results2 = stripped.replace(/\s+/g, '-').toLowerCase();

            
            switch (results2) {
              case "ai-at-the-edge":
                results2 = "azure-ai-edge"
                break;
              case "arc-enabled-data-services":
                results2 = "azure-arc-enabled-data-services"
                break;
              case "hyper-v":
                results2 = "azure-hyper-v"
                break;
              case "windows-server":
                results2 = "azure-windows-server"
                break;
              case "integrated-solutions-for-azure-hybrid-cloud":
                results2 = "azure-ai-edge"
                break;
              case "integrated-solutions-for-azure-hybrid-cloud":
                results2 = "dataon-integrated-solutions-azure-hybrid-cloud"
                break;
              case "integrated-systems-for-azure-stack-hci":
                results2 = "dataon-integrated-systems-azure-stack-hci"
                break;
              case "dataon-must-must-pro":
                results2 = "dataon-must-pro"
                break;
              case "servers-cpus,-gpus,-networking-storage":
                results2 = "servers-cpu-gpu-networking-storage"
                break;
            }

            $.ajax({
              type: 'POST',
              url: '/wp-admin/admin-ajax.php',
              dataType: 'html',
              data: {
              action: 'filter_projects',
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
						'post_type'      => 'post',
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
									<a href="<?php the_permalink(); ?>">
										<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
									</a>
									</div>
									<div class="col-post-content">
										<header>
										<!-- <div class="post-cat">
										<?php // $categories = get_the_category();
												// if ( ! empty( $categories ) ) { ?>
												<h5><?php // echo esc_html( $categories[0]->name ); ?></h5>	
											<?php // } ?>
										</div> -->
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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

				<div class="featured-blog right">

					<?php $args2 = array(
						'post_type'      => 'post',
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
										$postImage = 'https://dataon.wpengine.com/wp-content/uploads/2023/06/DataON-Logo-Light.png';
									}
									?>
<a href="<?php the_permalink(); ?>">
									<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
								</a>
								</div>
								<div class="col-post-content">
									<header>
										
											<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
				?>
					<!-- <article id="post-<?php echo $fpPostID; ?>" class="post post-featured col-lg-4">
						<div class="row">
							<div class="col-12">
								<?php
							$fpPostImage = get_the_post_thumbnail_url($fpPostID, 'full');
							if(empty($fpPostImage)) {
								echo '<div class="post-img" style="background-image: url(https://dataon.wpengine.com/wp-content/uploads/2023/06/DataON-Logo-Light.png); "></div>';
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
								wp_reset_postdata(); 
								?>
							</div>
						</div>
					</article> -->
				<?php // endif; ?>



				<?php $args3 = array(
					'post_type'      => 'post',
					'posts_per_page' => 9,
					'offset' => 2
				);
				$the_query3 = new WP_Query( $args3 ); ?>


				<div class="row">
					<div class="col-12 cont-content">
					<?php if ( $the_query3->have_posts() ) : while ( $the_query3->have_posts() ) : $the_query3->the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4'); ?>>
							<div class="row">
								<div class="col-12">
									<?php
									$postID = get_the_ID(); 
									$postImage = get_the_post_thumbnail_url($postID, 'full');
									if(empty($postImage)) {
										$postImage = 'https://dataon.wpengine.com/wp-content/uploads/2023/06/DataON-Logo-Light.png';
									}
									?>
								<a href="<?php the_permalink(); ?>">
									<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
								</a>
								</div>
								<div class="col-12 col-post-content">
									<header>
									
											<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
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
							echo '<div class="misha_loadmore">More posts</div>'; // you can use <a> as well
						?>

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

		body.blog .entry-title
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

		li.sub-cat 
		{
			padding-left: 2rem;
      font-weight: 300 !important;
      margin: 7px 7px !important;
		}

    ul
    {
      list-style-type: none;
    }

		</style>

	</div>


<?php get_footer(); ?>