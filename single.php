<?php get_header(); ?>

<div id="main" class="main main-single">
	<div class="container">
		<div class="row">
			<?php $postLayout = get_field('post_layout'); ?>
			<!-- <div class="col-12 <?php echo ($postLayout === 'sidebar' || $postLayout === '') ? 'col-lg-9' : ''; ?>"> -->

			<div class="col-12">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'entry' ); ?>
				<?php if ( comments_open() && !post_password_required() ) { comments_template( '', true ); } ?>
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
</div>

<?php get_footer(); ?>