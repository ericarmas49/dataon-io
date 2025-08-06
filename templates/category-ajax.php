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

<style>

article.type-dataon-videos
		{
			padding: 0 20px;
			margin: 3rem 0;
		}

    article.type-dataon-videos .post-img
    {
      min-height: 170px;
      margin-bottom: 0px;
    }

</style>
