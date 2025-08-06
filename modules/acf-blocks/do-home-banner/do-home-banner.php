
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js" integrity="sha512-XtmMtDEcNz2j7ekrtHvOVR4iwwaD6o/FUJe6+Zq+HgcCsk3kj4uSQQR8weQ2QVj1o0Pk6PwYLohm206ZzNfubg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css" integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" integrity="sha512-6lLUdeQ5uheMFbWm3CP271l14RsX1xtx+J5x2yeIDkkiBpeVTNhTqijME7GgRKKi6hCqovwCoBTlRBEC20M8Mg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php
// Create id attribute allowing for custom "anchor" value.
$id = 'home-banner-' . $block['id'];
if( !empty($block['anchor']) ) {
  $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'home-banner section';
if( !empty($block['className']) ) {
  $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
  $className .= ' align' . $block['align'];
}

// Load values and handle defaults.
$bgImage = get_field('hb_background_image');
$title = get_field('hb_title');
$imageTitle = get_field('image_title');
$text = get_field('hb_text');
$buttonLabel = get_field('hb_button_label');
$buttonLink = get_field('hb_button_link');
$featuredPosts = get_field('hb_featured_post');

$mobileBgImage = get_field('mobile_background_image');
$mobileText = get_field('mobile_text');
$mobileButtonLabel = get_field('mobile_button_label');
$mobileButtonLink = get_field('mobile_button_link');

if(empty($bgImage)) {
	$bgImage = "";
}

?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
	<div class="container-fluid">
		<div class="row">


		<div class="col-12 col-lg-6 col-banner mobile-block">
      			<div class="row">
			  		<div class="col-12 mod-banner" style="background: transparent;">

						<div class="block-txt">
							<div class="banner-title">
								<div class="banner-text"><?php echo $mobileText; ?></div>
							</div>
							<h1><a href="<?php echo $mobileButtonLink; ?>" class="btn"><?php echo $mobileButtonLabel; ?></a></h1>
						</div>
      				</div>
      			</div>
      		</div>




      		<div class="col-12 col-lg-6 col-banner">
      			<div class="row">
			  		<div class="col-12 mod-banner" style="background: transparent;">

						<div class="block-txt">
							<div class="banner-title"><img src="<?php echo $imageTitle; ?>" alt="Banner image title"/></div>
							<h1><div class="banner-text"><?php echo $text; ?></div></h1>
							<a href="<?php echo $buttonLink; ?>" class="btn"><?php echo $buttonLabel; ?></a>
						</div>
      				</div>
      			</div>
      		</div>


			<div class="col-12 col-lg-6 col-fp">
				<div class="row">
					<?php if( have_rows('hb_featured_post') ):

						// Loop through rows.
						while( have_rows('hb_featured_post') ) : the_row();

							$sub_title = get_sub_field('title');
							$sub_icon = get_sub_field('image');
							$sub_post = get_sub_field('post');
							$sub_post_ID = $sub_post->ID;

							echo '<a href="' . get_permalink($sub_post_ID) . '" class="col-12 col-lg-6 mod-fp">';
								echo '<div class="fp-container" style="background: transparent;">';
									echo '<div class="fp-text">';
									echo '<img alt="icon" src="' . $sub_icon . '" />';
										echo '<h2>'.$sub_title.'</h2>';
									echo '</div>';
								echo '</div>';
							echo '</a>';
							
							endwhile; endif; ?>

				</div>
   		 	</div>
	
		</div>

	</div>

</div>

<style type="text/css">

.slick-prev, .slick-next
{
	font-size: 11px;
}

.col-fp .row a:nth-child(2) .fp-container, .col-fp .row a:nth-child(1) .fp-container  
{
	border-bottom: 0px !important;
}

.col-fp .row a:nth-child(2) .fp-container, .col-fp .row a:nth-child(4) .fp-container  
{
	border-right: 3px solid #fff;
}

.col-fp 
{
	overflow: hidden;
	border-right: 3px solid #fff;
}

.mobile-block {
		display: none;
	}

	.mod-banner h2 .banner-text {
		margin-bottom: 2rem;
		font-size: 4rem;
		color: #fff;
		font-weight: normal;
	}

@media screen and (max-width: 767px) {

	.mobile-block {
		display: block;
	}

}


	#<?php echo $id; ?> {   
		background-image: url("wp-content/uploads/2023/11/home_bg_2500.jpeg"); 
		background-size: cover;

	}
	#<?php echo $id; ?> .col-banner {
		padding: 0;
	}
	#<?php echo $id; ?> .col-banner .row {
		margin: 0px;
	}
	#<?php echo $id; ?> .mod-banner {
		display: flex;
		align-items: flex-end;
		padding: 5rem;    
		min-height: 70vh;
		background-repeat: no-repeat;
		background-size: cover;
		border: 3px solid #fff;
	}
	#<?php echo $id; ?> .mod-banner .banner-title {
		margin-bottom: 2rem;
		color: #fff;
	}
	#<?php echo $id; ?> .mod-banner .banner-text, .mod-banner h2 .banner-text {
		margin-bottom: 2rem;
		font-size: 4rem;
		color: #fff;
	}
	#<?php echo $id; ?> .btn {
		padding: 6px 25px;
		border-radius: 0;
		background-color: #fff;
		/* border: 2px solid rgb(255, 181, 8); */
		border: 2px solid #000;
		font-size: 1.5rem;
		color: #000;
	}

	#<?php echo $id; ?> .col-fp {
		padding: 0;
	}
	#<?php echo $id; ?> .col-fp > .row {
	
	}
	#<?php echo $id; ?> .mod-fp {
		padding: 0px;
		text-decoration: none;
	}

	#<?php echo $id; ?> .fp-container {
		display: flex;
		align-items: flex-end;
		height: 35vh;
		padding: 2rem;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		background-color: rgba(0, 0, 0, 0.5);
		border-top: 3px solid #fff;
		border-right: 3px solid #fff;
		border-bottom: 3px solid #fff;
	}
	#<?php echo $id; ?> .fp-container .fp-text {
		padding: 2rem;
		background-color: rgba(0, 0, 0, 0.5);
		color: #fff;
	}
	#<?php echo $id; ?> .fp-container .fp-text h2 {
		font-size: 1.5rem;
	}

	/* .three-cont, .in-container  */
	{
		max-width: 1400px;
		margin: 0rem auto;
	}

	.section
	{
		max-width: 1400px;
	}

	.three-stack .wp-block-column
	{
		padding: 1.5rem;
	}

	.diff-logo 
	{
		display: flex;
		flex-wrap: wrap;
	}

	.diff-logo figure 
	{
		width: 33%;
		margin-bottom: 0px;
	}

	.diff-logo img 
	{
		width: 100%;
		height: auto !important;
	}

	.slick-track .slick-slide
{
		height: auto;
	}

	.slick-next:before, .slick-prev:before
	{
		color: #000;
	}

	.difference .left 
	{
		padding-right: 16rem;
	}

	.difference p
	{
		font-size: 20px;
		margin-top: 2rem;
	}

	.three-stack h3 strong
	{
		font-weight: 500;
		font-size: 18px;
	}

	.section p 
	{
		font-size: 20px;
	}

	.section h2 
	{
		font-size: 35px; 
		margin-bottom: 3rem;
		font-weight: 500;
	}


	@media(min-width: 768px) {


		.journey
		{
			max-width: 1400px;
			margin: 0 auto !important;
			padding: 10rem 0;
		}

		.fullw 
		{
			margin-bottom: 15rem;
		}



	}

	@media(min-width: 992px) {

	}

	@media(min-width: 1200px) {

	}

	@media(min-width: 1400px) {

	}
</style>


<script>
jQuery(document).ready( function( ) {
jQuery('.testimonials').slick({
  infinite: true,
  slidesToShow: 3,
  slidesToScroll: 3,
  prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button" style="" ></button>', 
  nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button" style="" >Next</button>',
  responsive: [
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ],
  dots: true,
  arrows: true,
});
});

</script>