<?php get_header(); ?>

<style>

.entry-content  
{
	display: flex;
}

p 
{
	flex: 5;
	margin-left: 3rem;
}

article .mod-featured-img
{
	flex: 1;
	width: 250px;
	height: 250px;
}


</style>



<div id="main" class="main main-single document">
	<div class="container">
		<div class="row">
			<?php $postLayout = get_field('post_layout'); ?>
			<!-- <div class="col-12 <?php echo ($postLayout === 'sidebar' || $postLayout === '') ? 'col-lg-9' : ''; ?>"> -->

			<div class="col-12">

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'entry' ); ?>

					<?php if( get_field('document') ): ?>
    					<a href="<?php the_field('document'); ?>" >Download Document</a>
					<?php endif; ?>
				<?php endwhile; endif; ?>
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