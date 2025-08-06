<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 post'); ?>>
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