<?php

if ( ! function_exists( 'do_hb_get_image_url' ) ) {
	function do_hb_get_image_url( $image, $fallback = '' ) {
		if ( empty( $image ) ) {
			return $fallback;
		}

		if ( is_array( $image ) ) {
			return isset( $image['url'] ) ? $image['url'] : $fallback;
		}

		if ( is_numeric( $image ) ) {
			$attachment_url = wp_get_attachment_image_url( (int) $image, 'full' );
			return $attachment_url ? $attachment_url : $fallback;
		}

		return (string) $image;
	}
}

if ( ! function_exists( 'do_hb_get_link_url' ) ) {
	function do_hb_get_link_url( $link ) {
		if ( is_array( $link ) && ! empty( $link['url'] ) ) {
			return $link['url'];
		}

		return is_string( $link ) ? $link : '';
	}
}

if ( ! function_exists( 'do_hb_get_link_target' ) ) {
	function do_hb_get_link_target( $link ) {
		if ( is_array( $link ) && ! empty( $link['target'] ) ) {
			return $link['target'];
		}

		return '';
	}
}

if ( ! function_exists( 'do_hb_get_slides' ) ) {
	function do_hb_get_slides() {
		$slides = get_field( 'hb_slides' );

		if ( ! empty( $slides ) && is_array( $slides ) ) {
			return $slides;
		}

		$legacy_fields = array(
			'hb_background_image',
			'hb_text',
			'hb_button_label',
			'hb_button_link',
			'image_title',
			'mobile_background_image',
			'mobile_text',
			'mobile_button_label',
			'mobile_button_link',
		);

		$has_legacy_content = false;
		foreach ( $legacy_fields as $field_name ) {
			if ( ! empty( get_field( $field_name ) ) ) {
				$has_legacy_content = true;
				break;
			}
		}

		if ( ! $has_legacy_content && empty( get_field( 'hb_featured_post' ) ) ) {
			return array();
		}

		return array(
			array(
				'slide_layout'            => 'split',
				'hb_background_image'     => get_field( 'hb_background_image' ),
				'mobile_background_image' => get_field( 'mobile_background_image' ),
				'image_title'             => get_field( 'image_title' ),
				'hb_text'                 => get_field( 'hb_text' ),
				'hb_button_label'         => get_field( 'hb_button_label' ),
				'hb_button_link'          => get_field( 'hb_button_link' ),
				'hb_featured_post'        => get_field( 'hb_featured_post' ),
				'mobile_text'             => get_field( 'mobile_text' ),
				'mobile_button_label'     => get_field( 'mobile_button_label' ),
				'mobile_button_link'      => get_field( 'mobile_button_link' ),
			),
		);
	}
}

if ( ! function_exists( 'do_hb_render_split_slide' ) ) {
	function do_hb_render_split_slide( $slide, $block_id ) {
		$bg_image_url     = do_hb_get_image_url( $slide['hb_background_image'] ?? null, content_url( 'uploads/2023/11/home_bg_2500.jpeg' ) );
		$image_title      = do_hb_get_image_url( $slide['image_title'] ?? null );
		$text             = $slide['hb_text'] ?? '';
		$button_label     = $slide['hb_button_label'] ?? '';
		$button_link      = $slide['hb_button_link'] ?? '';
		$mobile_text      = $slide['mobile_text'] ?? '';
		$mobile_label     = $slide['mobile_button_label'] ?? '';
		$mobile_link      = $slide['mobile_button_link'] ?? '';
		$featured_posts   = $slide['hb_featured_post'] ?? array();
		?>
		<div class="home-banner-slide home-banner-slide--split" style="--hb-slide-bg: url('<?php echo esc_url( $bg_image_url ); ?>');">
			<div class="container-fluid home-banner-slide-inner">
				<div class="row">
					<div class="col-12 col-lg-6 col-banner mobile-block">
						<div class="row">
							<div class="col-12 mod-banner">
								<div class="block-txt">
									<div class="banner-title">
										<div class="banner-text"><?php echo wp_kses_post( $mobile_text ); ?></div>
									</div>
									<?php if ( $mobile_link && $mobile_label ) : ?>
										<h1><a href="<?php echo esc_url( $mobile_link ); ?>" class="btn"><?php echo esc_html( $mobile_label ); ?></a></h1>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12 col-lg-6 col-banner">
						<div class="row">
							<div class="col-12 mod-banner">
								<div class="block-txt">
									<?php if ( $image_title ) : ?>
										<div class="banner-title"><img src="<?php echo esc_url( $image_title ); ?>" alt="" /></div>
									<?php endif; ?>
									<?php if ( $text ) : ?>
										<h1><div class="banner-text"><?php echo wp_kses_post( $text ); ?></div></h1>
									<?php endif; ?>
									<?php if ( $button_link && $button_label ) : ?>
										<a href="<?php echo esc_url( $button_link ); ?>" class="btn"><?php echo esc_html( $button_label ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>

					<div class="col-12 col-lg-6 col-fp">
						<div class="row">
							<?php
							if ( ! empty( $featured_posts ) && is_array( $featured_posts ) ) :
								foreach ( $featured_posts as $featured_post ) :
									$sub_title = $featured_post['title'] ?? '';
									$sub_icon  = do_hb_get_image_url( $featured_post['image'] ?? null );
									$sub_post  = $featured_post['post'] ?? null;
									$post_url  = ( is_object( $sub_post ) && isset( $sub_post->ID ) ) ? get_permalink( $sub_post->ID ) : '#';
									?>
									<a href="<?php echo esc_url( $post_url ); ?>" class="col-12 col-lg-6 mod-fp">
										<div class="fp-container">
											<div class="fp-text">
												<?php if ( $sub_icon ) : ?>
													<img alt="" src="<?php echo esc_url( $sub_icon ); ?>" />
												<?php endif; ?>
												<?php if ( $sub_title ) : ?>
													<h2><?php echo esc_html( $sub_title ); ?></h2>
												<?php endif; ?>
											</div>
										</div>
									</a>
									<?php
								endforeach;
							endif;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'do_hb_render_full_width_slide' ) ) {
	function do_hb_render_full_width_slide( $slide ) {
		$default_image = content_url( 'uploads/2023/11/home_bg_2500.jpeg' );
		$desktop_image = do_hb_get_image_url( $slide['hb_background_image'] ?? null, $default_image );
		$mobile_image  = do_hb_get_image_url( $slide['mobile_background_image'] ?? null, $desktop_image );
		$banner_link   = do_hb_get_link_url( $slide['banner_link'] ?? '' );
		$link_target   = do_hb_get_link_target( $slide['banner_link'] ?? '' );
		$link_title    = is_array( $slide['banner_link'] ?? null ) && ! empty( $slide['banner_link']['title'] )
			? $slide['banner_link']['title']
			: __( 'View banner', 'blankslate-dataon' );
		?>
		<div class="home-banner-slide home-banner-slide--full-width">
			<?php if ( $banner_link ) : ?>
				<a
					href="<?php echo esc_url( $banner_link ); ?>"
					class="home-banner-full-width-link"
					style="--hb-bg-desktop: url('<?php echo esc_url( $desktop_image ); ?>'); --hb-bg-mobile: url('<?php echo esc_url( $mobile_image ); ?>');"
					<?php echo $link_target ? 'target="' . esc_attr( $link_target ) . '"' : ''; ?>
					<?php echo $link_target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
				>
					<span class="screen-reader-text"><?php echo esc_html( $link_title ); ?></span>
				</a>
			<?php else : ?>
				<div
					class="home-banner-full-width-link home-banner-full-width-link--static"
					style="--hb-bg-desktop: url('<?php echo esc_url( $desktop_image ); ?>'); --hb-bg-mobile: url('<?php echo esc_url( $mobile_image ); ?>');"
					role="img"
					aria-label="<?php esc_attr_e( 'Homepage banner image', 'blankslate-dataon' ); ?>"
				></div>
			<?php endif; ?>
		</div>
		<?php
	}
}

$id = 'home-banner-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$className = 'home-banner section';
if ( ! empty( $block['className'] ) ) {
	$className .= ' ' . $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$className .= ' align' . $block['align'];
}

$slides              = do_hb_get_slides();
$slide_count         = count( $slides );
$is_carousel         = $slide_count > 1;

if ( $is_carousel ) {
	$className .= ' home-banner--carousel';
}

wp_enqueue_script(
	'do-home-banner-slick',
	'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js',
	array( 'jquery' ),
	'1.8.1',
	true
);
wp_enqueue_style(
	'do-home-banner-slick',
	'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css',
	array(),
	'1.9.0'
);
wp_enqueue_style(
	'do-home-banner-slick-theme',
	'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css',
	array( 'do-home-banner-slick' ),
	'1.9.0'
);
?>
<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $className ); ?>">
	<?php if ( empty( $slides ) ) : ?>
		<div class="home-banner-empty"><?php esc_html_e( 'Add banner slides in the block settings.', 'blankslate-dataon' ); ?></div>
	<?php else : ?>
		<div class="home-banner-carousel<?php echo $is_carousel ? ' home-banner-carousel--active' : ''; ?>">
			<?php
			foreach ( $slides as $slide ) {
				$layout = $slide['slide_layout'] ?? 'split';

				if ( $layout === 'full_width' ) {
					do_hb_render_full_width_slide( $slide );
				} else {
					do_hb_render_split_slide( $slide, $id );
				}
			}
			?>
		</div>
	<?php endif; ?>
</div>

<style type="text/css">
	#<?php echo esc_attr( $id ); ?> .home-banner-empty {
		padding: 3rem;
		text-align: center;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-slide-inner {
		max-width: 1400px;
		margin: 0 auto;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-slide--full-width {
		width: 100%;
		max-width: 100vw;
		margin-left: calc(50% - 50vw);
		margin-right: calc(50% - 50vw);
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-slide--split {
		background-image: var(--hb-slide-bg);
		background-size: cover;
		background-position: center;
	}

	#<?php echo esc_attr( $id ); ?> .col-fp {
		overflow: hidden;
		padding: 0;
		border: 0;
	}

	#<?php echo esc_attr( $id ); ?> .mobile-block {
		display: none;
	}

	#<?php echo esc_attr( $id ); ?> .col-banner {
		padding: 0;
	}

	#<?php echo esc_attr( $id ); ?> .col-banner .row {
		margin: 0;
	}

	#<?php echo esc_attr( $id ); ?> .mod-banner {
		display: flex;
		align-items: flex-end;
		padding: 5rem;
		min-height: 50vh;
		max-height: 50vh;
		background-repeat: no-repeat;
		background-size: cover;
		border: 0;
	}

	#<?php echo esc_attr( $id ); ?> .mod-banner .banner-title {
		margin-bottom: 2rem;
		color: #fff;
	}

	#<?php echo esc_attr( $id ); ?> .mod-banner .banner-text,
	#<?php echo esc_attr( $id ); ?> .mod-banner h2 .banner-text {
		margin-bottom: 2rem;
		font-size: 4rem;
		color: #fff;
		font-weight: normal;
	}

	#<?php echo esc_attr( $id ); ?> .btn {
		padding: 6px 25px;
		border-radius: 0;
		background-color: #fff;
		border: 2px solid #000;
		font-size: 1.5rem;
		color: #000;
		text-decoration: none;
		display: inline-block;
	}

	#<?php echo esc_attr( $id ); ?> .mod-fp {
		padding: 0;
		text-decoration: none;
	}

	#<?php echo esc_attr( $id ); ?> .fp-container {
		display: flex;
		align-items: flex-end;
		height: 35vh;
		padding: 2rem;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		background-color: rgba(0, 0, 0, 0.5);
		border: 0;
		transition: 0.5s;
	}

	#<?php echo esc_attr( $id ); ?> .fp-container .fp-text {
		padding: 2rem;
		background-color: rgba(0, 0, 0, 0.5);
		color: #fff;
		height: 150px;
		width: 100%;
		transition: 0.5s;
		display: flex;
		flex-direction: column;
		justify-content: center;
	}

	#<?php echo esc_attr( $id ); ?> .fp-container .fp-text h2 {
		font-size: 2rem;
		font-weight: 500;
		text-align: left;
	}

	#<?php echo esc_attr( $id ); ?> .fp-text img {
		filter: invert(1);
		width: 50px;
		height: 50px;
	}

	#<?php echo esc_attr( $id ); ?> .mod-fp:nth-child(1) .fp-container.active {
		background: #f1511b !important;
	}

	#<?php echo esc_attr( $id ); ?> .mod-fp:nth-child(2) .fp-container.active {
		background: #80cc28 !important;
	}

	#<?php echo esc_attr( $id ); ?> .mod-fp:nth-child(3) .fp-container.active {
		background: #00adef !important;
	}

	#<?php echo esc_attr( $id ); ?> .mod-fp:nth-child(4) .fp-container.active {
		background: #fbbc09 !important;
	}

	#<?php echo esc_attr( $id ); ?> .fp-container.active .fp-text {
		height: 100%;
		background-color: transparent;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-full-width-link {
		display: block;
		min-height: 50vh;
		max-height: 50vh;
		width: 100%;
		background-image: var(--hb-bg-desktop);
		background-size: cover;
		background-position: center;
		background-repeat: no-repeat;
		text-decoration: none;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-carousel--active .slick-prev,
	#<?php echo esc_attr( $id ); ?> .home-banner-carousel--active .slick-next {
		z-index: 2;
		width: 44px;
		height: 44px;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-carousel--active .slick-prev:before,
	#<?php echo esc_attr( $id ); ?> .home-banner-carousel--active .slick-next:before {
		font-size: 28px;
		color: #fff;
		opacity: 0.9;
	}

	#<?php echo esc_attr( $id ); ?> .home-banner-carousel--active .slick-dots {
		bottom: 18px;
	}

	@media screen and (max-width: 767px) {
		#<?php echo esc_attr( $id ); ?> .mobile-block {
			display: block;
		}

		#<?php echo esc_attr( $id ); ?> .home-banner-slide--split .col-banner:not(.mobile-block) {
			display: none;
		}

		#<?php echo esc_attr( $id ); ?> .home-banner-slide--split .col-fp {
			display: none;
		}

		#<?php echo esc_attr( $id ); ?> .home-banner-full-width-link {
			min-height: 50vh;
			background-image: var(--hb-bg-mobile);
		}
	}
</style>

<?php if ( ! empty( $slides ) ) : ?>
<script>
jQuery(function($) {
	var $banner = $('#<?php echo esc_js( $id ); ?>');
	var $carousel = $banner.find('.home-banner-carousel');

	$banner.find('.fp-container').on('mouseenter', function() {
		$(this).addClass('active');
	}).on('mouseleave', function() {
		$(this).removeClass('active');
	});

	$banner.find('.fp-text').on('mouseenter', function() {
		$(this).parent().addClass('active');
	});

	<?php if ( $is_carousel ) : ?>
	if ($carousel.hasClass('home-banner-carousel--active') && typeof $carousel.slick === 'function') {
		$carousel.slick({
			infinite: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			adaptiveHeight: true,
			dots: true,
			arrows: true,
			autoplay: false
		});
	}
	<?php endif; ?>
});
</script>
<?php endif; ?>
