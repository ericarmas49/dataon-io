<?php get_header(); ?>
<main id="main" class="main main-search">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<?php if ( have_posts() ) : ?>
					<header class="header">
						<h1 class="entry-title" itemprop="name"><?php printf( esc_html__( 'Search Results for: %s', 'blankslate' ), get_search_query() ); ?></h1>
					</header>
					<?php while ( have_posts() ) : the_post(); ?>
				    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				    	<div class="row">
				    		<div class="col-12 col-lg-3">
				    			<?php
				    			$postID = get_the_ID(); 
				    			$postImage = get_the_post_thumbnail_url($postID, 'full');
				    			if(empty($postImage)) {
				    				$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
				    			}
				    			?>
				    			<div class="post-img" style="background-image: url(<?php echo $postImage; ?>);"></div>
				    		</div>
				    		<div class="col-12 col-lg-9 col-post-content">
				    			<header>
										<?php if ( is_singular() ) { echo '<h1 class="entry-title" itemprop="headline">'; } else { echo '<h2 class="entry-title">'; } ?>
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
										<?php if ( is_singular() ) { echo '</h1>'; } else { echo '</h2>'; } ?>
										<?php edit_post_link(); ?>
										<?php if ( !is_search() ) { get_template_part( 'entry', 'meta' ); } ?>
									</header>
									<?php get_template_part( 'entry', ( is_front_page() || is_home() || is_front_page() && is_home() || is_archive() || is_search() ? 'summary' : 'content' ) ); ?>
									<?php if ( is_singular() ) { get_template_part( 'entry-footer' ); } ?>
				    		</div>
				    	</div>
				    </article>
			    	<?php endwhile; ?>
			    	<?php //get_template_part( 'nav', 'below' ); ?>
			    	<?php wp_pagenavi(); ?>
			    <?php else : ?>
			    	<article id="post-0" class="post no-results not-found">
							<header class="header">
							<h1 class="entry-title" itemprop="name"><?php esc_html_e( 'Nothing Found', 'blankslate' ); ?></h1>
							</header>
							<div class="entry-content" itemprop="mainContentOfPage">
							<p><?php esc_html_e( 'Sorry, nothing matched your search. Please try again.', 'blankslate' ); ?></p>
							<?php get_search_form(); ?>
							</div>
						</article>
			    <?php endif; ?>
			</div>
		</div>
	</div>
</main>
<?php get_footer(); ?>