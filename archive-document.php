<!-- /*  
*
* Template Name: Documents Archive
*
*
*/ -->


<?php get_header(); ?>

<main id="main" class="main main-archive esting">
	
	<div class="container news-blog">
<!-- 
		<h1>Documents Library</h1> -->

		<aside class="filters">

			<div class="banner news-section" role="banner">

				<div class="blog-cats">


				<?php

echo '<ul><li><input class="cat-check" type="checkbox" name="type" value="blog" />ALL DOCUMENTS</li></ul>';  // Output term name

// Recursive function to display terms hierarchically
function display_terms_hierarchy($parent = 0, $taxonomy = 'document_category') {
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










					<!-- <?php $categories = get_terms( 'document_category' ); ?>
						
					<ul class="news cat-list">

						<li><input class="cat-check" type="checkbox" name="type" value="blog" /> All Documents</li>

						<?php foreach($categories as $category) : ?>

							<li
								<?php if ($category->parent != 0) {
									echo 'class="sub-cat"';
								} ?>
							>
									<input class="cat-check" type="checkbox" name="type" value="<?= $category->name; ?>" /> <?= $category->name; ?>
								</li>

							<?php endforeach; ?>

					</ul> -->

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
						// console.log(results2, 'query final'); // "sonic-free-games"

						$.ajax({
							type: 'POST',
							url: '/wp-admin/admin-ajax.php',
							dataType: 'html',
							data: {
								action: 'filter_documents',
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
						'post_type'      => 'document',
						'posts_per_page' => -1,
					);
					$the_query = new WP_Query( $args ); ?>

					<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
						<?php if(get_field('evergreen_1')) : ?>
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
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
									</div>
									<div class="col-post-content">
										<header>
										<div class="post-cat">
										<?php $categories = get_the_category();
												if ( ! empty( $categories ) ) { ?>
												<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
											<?php } ?>
										</div>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?></p></a></h2>
												<?php 
													$fileDownload = get_field('document', $postID) ?>

    												<a href="<?php echo $fileDownload; ?> " >Download PDF</a>

											</header>
											<?php 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>

					<?php endif; endwhile; endif; ?>

				</div>

				<div class="featured-blog right">

					<?php $args2 = array(
						'post_type'      => 'document',
						'posts_per_page' => -1,
					);
					$the_query2 = new WP_Query( $args2 ); ?>

					<?php if ( $the_query2->have_posts() ) : while ( $the_query2->have_posts() ) : $the_query2->the_post(); ?>
						<?php if(get_field('evergreen_2')) : ?>
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
													<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
											</div>
											<div class="col-post-content">
												<header>
												<div class="post-cat">
												<?php $categories = get_the_category();
														if ( ! empty( $categories ) ) { ?>
														<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
													<?php } ?>
												</div>
														<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?></p></a></h2>

														<?php 
															$fileDownload = get_field('document', $postID) ?>

															<a href="<?php echo $fileDownload; ?> " >Download PDF</a>

													</header>
													<?php 
													?>
													<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
											</div>
										</div>
									</article>

						<?php endif; endwhile; endif; ?>

					<?php wp_reset_postdata(); ?>

				</div>

			</div>

			<div class='project-tiles blogs-filtered lower row'>

					
					<?php

					$args3 = array(
						'post_type'      => 'document',
						'posts_per_page' => 9,
						// 'offset' => 2
					);
					$the_query3 = new WP_Query( $args3 ); ?>
				
					<div class="row">
						<div class="col-12 cont-content">
						<?php if ( $the_query3->have_posts() ) : while ( $the_query3->have_posts() ) : $the_query3->the_post(); ?>
							<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 post'); ?>>
								<div class="row">
									<div class="">
										<?php
										$postID = get_the_ID(); 
										$postImage = get_the_post_thumbnail_url($postID, 'full');
										if(empty($postImage)) {
											$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
										}
										?>
											<div class="post-img" style="background-size: cover; background-image: url(<?php echo $postImage; ?>);"></div>
									</div>
									<div class="col-post-content">
										<header>
										<div class="post-cat">
										<?php $categories = get_the_category();
												if ( ! empty( $categories ) ) { ?>
												<h5><?php echo esc_html( $categories[0]->name ); ?></h5>	
											<?php } ?>
										</div>
												<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><p><?php the_title(); ?></p></a></h2>

												<?php 
													$fileDownload = get_field('document', $postID) ?>

    												<a href="<?php echo $fileDownload; ?> " >Download PDF</a>

											</header>
											<?php 
											?>
											<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
									</div>
								</div>
							</article>
							<?php endwhile; endif; 

							global $wp_query;

							if (  $wp_query->max_num_pages > 1 ){
								echo '<div class="misha_loadmore">More Documents</div>'; // you can use <a> as well
							} else {
								echo $wp_query->max_num_pages; // you can use <a> as well

							}
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

		li.sub-cat 
		{
			padding-left: 2rem;
		}


		.document .row 
		{
			flex-direction: column;
		}

		.cont-content
		{
			display: flex;
			flex-wrap: wrap;
		}

		article.type-document 
		{
			padding: 0 20px;
			margin: 3rem 0;
		}

		article.type-document .post-img
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

		h2.entry-title a 
		{
			font-size: 16px;
		}

		h2.entry-title 
		{
			line-height: 22px;
			margin-top: 0px;
		}


		.featured.upper article.document .post-img
		{
			min-height: 250px;
		}


		</style>

	</main>


<?php get_footer(); ?>