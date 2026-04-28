<?php get_header(); ?>

<main id="main" class="main">
	<div class="container">
		<header class="header">
			<?php the_post(); ?>
			<h1 class="entry-title author" itemprop="name"><?php the_author_link(); ?></h1>
			<div class="archive-meta" itemprop="description"><?php if ( '' != get_the_author_meta( 'user_description' ) ) { echo esc_html( get_the_author_meta( 'user_description' ) ); } ?></div>
			<?php rewind_posts(); ?>
		</header>
		<div class="row">
			<div class="col-12">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			    	<div class="row">
			    		<div class="col-12 col-lg-3">
			    			<?php
			    			$postID = get_the_ID(); 
			    			$postImage = get_the_post_thumbnail_url($postID, 'full');
			    			if(empty($postImage)) {
			    				$postImage = 'https://dataon.wpengine.com/wp-content/uploads/2023/06/DataON-Logo-Light.png';
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
			    <?php endwhile; endif; ?>
				<?php get_template_part( 'nav', 'below' ); ?>
			</div>
		</div>
	</div>
</main>

<?php get_footer(); ?>