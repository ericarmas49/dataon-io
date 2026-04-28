<?php get_header(); ?>

<style>

.youtube-video {
  aspect-ratio: 16 / 9;
  width: 100%;
}

</style>


<main id="main" class="main main-single videos">
	<div class="container">
		<div class="row">
			<?php $postLayout = get_field('post_layout'); ?>
			<!-- <div class="col-12 <?php echo ($postLayout === 'sidebar' || $postLayout === '') ? 'col-lg-9' : ''; ?>"> -->

			<div class="col-12">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'entry' ); ?>
				
				
				<?php $str =  esc_html( get_field('youtube_video_url') ); 

				if (str_contains($str, 'watch')) {
					$str1 = substr($str, 32); 

				} else {
				
				$str1 = substr($str, 17); 

				}
				// echo $str1;

				// echo '<img src="https://i.ytimg.com/vi/' . $str1 . '/hqdefault.jpg" />';

				?>
				
				
				</div>


				<iframe class="youtube-video" height="auto" src="https://www.youtube.com/embed/<?php echo $str1; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

				<?php endwhile; endif; ?>
				<footer class="footer">
				<?php get_template_part( 'nav', 'below-single' ); ?>
				</footer>
			</div>

			<?php // if($postLayout === 'sidebar' || $postLayout === '') : ?>
				<!-- <div class="col-12 col-lg-3"> -->
					<?php // get_sidebar(); ?>
				<!-- </div> -->
			<?php // endif; ?>

		</div>
	</div>
</main>

<?php get_footer(); ?>