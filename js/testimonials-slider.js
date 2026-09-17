jQuery(function ($) {
	var $sliders = $('#testimonials, .testimonials').filter(function () {
		return $(this).children('img, .testimonial-slide').length > 0;
	});

	if (!$sliders.length || typeof $.fn.slick !== 'function') {
		return;
	}

	$sliders.each(function () {
		var $slider = $(this);

		if ($slider.hasClass('slick-initialized')) {
			return;
		}

		$slider.slick({
			infinite: true,
			slidesToShow: 3,
			slidesToScroll: 3,
			prevArrow:
				'<button class="slick-prev slick-arrow" aria-label="Previous testimonials" type="button"></button>',
			nextArrow:
				'<button class="slick-next slick-arrow" aria-label="Next testimonials" type="button">Next</button>',
			responsive: [
				{
					breakpoint: 992,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2,
					},
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
					},
				},
			],
			dots: true,
			arrows: true,
		});
	});
});
