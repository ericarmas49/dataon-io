<article id="post-<?php the_ID(); ?>" <?php post_class( 'col-lg-4 post'); ?>>
	<div class="row">
		<div class="">
			<?php
			$postID = get_the_ID(); 
			// $postImage = get_the_post_thumbnail_url($postID, 'full');
			// if(empty($postImage)) {
				$postImage = 'https://dataon.io/wp-content/uploads/2024/02/DataON-default-image-1600x900-1.jpg';
			// }
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

<style>

article.dataon-videos
		{
			padding: 0 20px;
			margin: 3rem 0;
		}

</style>