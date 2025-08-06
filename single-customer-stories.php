<?php get_header(); ?>

<style>
.single-customer-stories 
{
	padding: 0px;
	background-color: transparent;
	min-height: auto;
}

.single-customer-stories .main-single
{
	/* padding: 50px 0px; */
}

.col-img 
{
	background-size: cover;
	display: flex;
	justify-content: center;
	min-height: 200px;
}

h1
{
	font-size: 3.5rem;
	margin-bottom: 2rem;
}

.cs-sidebar ul li 
{
	list-style-type: none;
	margin-bottom: 15px;
	padding-left: 0px;
	margin-left: 0px;
}

.cs-sidebar
{
	border: 1px solid #000;
	padding: 2rem;
	overflow: hidden;
}

.cs-sidebar figure:first-of-type 
{
	margin-top: -9rem;
	margin-bottom: -6rem;
}

.cs-sidebar figure:first-of-type.smallMarg
{
	margin-top: -5rem;
	margin-bottom: -1rem;
}
.cs-sidebar ul 
{
	margin: 1rem 0 4rem 0;
	padding-left: 0;
}

.cs-stories-main 
{
	/* padding-left: 2rem; */
}

.cs-stories-main p:first-of-type 
{
	margin-bottom: 4rem;
}

.cs-stories-main h2
{
	font-size: 18px;
	margin-bottom: 2rem;
	margin-top: 3rem;
}

.col-img .container 
{
	display: flex;
	justify-content: flex-end;
	padding-right: 15rem;
}

.cs-stories-main p strong, .cs-sidebar h3
{
	font-size: 18px;
	font-weight: 700;
}

.cs-stories h2.wp-block-heading
{

}

</style>


<?php 
	//$banner = get_field('banner');
	$banner = get_field('hero');
	$bgColor = $banner['background_color'];
	$bgImg = $banner['background_image'];
?>

<?php
	if(!empty($banner['featured_image'])) {
		// echo '<img src="'.$banner['featured_image'].'" alt="Feature Image"/>';
	} else {
		$featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'full');
		if(!empty($featuredImage)) {
		}
	}
?>

<div class="col-12 col-img" style='background-position: center; background-image: url("<?php echo $featuredImage; ?>");'></div>


<div id="main" class="main main-single container">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<div class="entry-content" itemprop="mainEntityOfPage">
				<meta itemprop="description" content="<?php echo esc_html( wp_strip_all_tags( get_the_excerpt(), true ) ); ?>" />
				<?php the_content(); ?>
				<div class="entry-links"><?php wp_link_pages(); ?></div>
			</div>
			
		</article>

	<?php endwhile; endif; ?>

</div>

<?php get_footer(); ?>