<?php

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

acf_add_local_field_group(
	array(
		'key'                   => 'group_do_home_banner_slides',
		'title'                 => 'Home Banner Slides',
		'fields'                => array(
			array(
				'key'          => 'field_hb_slides',
				'label'        => 'Banner Slides',
				'name'         => 'hb_slides',
				'type'         => 'repeater',
				'instructions' => 'Add one or more slides. Use multiple slides to enable the carousel.',
				'layout'       => 'block',
				'button_label' => 'Add Slide',
				'sub_fields'   => array(
					array(
						'key'           => 'field_hb_slide_layout',
						'label'         => 'Slide Layout',
						'name'          => 'slide_layout',
						'type'          => 'select',
						'choices'       => array(
							'split'      => 'Split (text + featured grid)',
							'full_width' => 'Full width (clickable image)',
						),
						'default_value' => 'split',
					),
					array(
						'key'           => 'field_hb_slide_background_image',
						'label'         => 'Background Image',
						'name'          => 'hb_background_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
					),
					array(
						'key'               => 'field_hb_slide_mobile_background_image',
						'label'             => 'Mobile Background Image',
						'name'              => 'mobile_background_image',
						'type'              => 'image',
						'return_format'     => 'array',
						'preview_size'      => 'medium',
						'instructions'      => 'Optional. Falls back to the desktop image when empty.',
					),
					array(
						'key'               => 'field_hb_slide_banner_link',
						'label'             => 'Banner Link',
						'name'              => 'banner_link',
						'type'              => 'link',
						'return_format'     => 'array',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'full_width',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_image_title',
						'label'             => 'Logo / Image Title',
						'name'              => 'image_title',
						'type'              => 'image',
						'return_format'     => 'url',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_text',
						'label'             => 'Banner Text',
						'name'              => 'hb_text',
						'type'              => 'textarea',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_button_label',
						'label'             => 'Button Label',
						'name'              => 'hb_button_label',
						'type'              => 'text',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_button_link',
						'label'             => 'Button Link',
						'name'              => 'hb_button_link',
						'type'              => 'url',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_featured_post',
						'label'             => 'Featured Posts',
						'name'              => 'hb_featured_post',
						'type'              => 'repeater',
						'layout'            => 'table',
						'button_label'      => 'Add Featured Item',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
						'sub_fields'        => array(
							array(
								'key'   => 'field_hb_slide_featured_title',
								'label' => 'Title',
								'name'  => 'title',
								'type'  => 'text',
							),
							array(
								'key'           => 'field_hb_slide_featured_image',
								'label'         => 'Icon',
								'name'          => 'image',
								'type'          => 'image',
								'return_format' => 'url',
							),
							array(
								'key'           => 'field_hb_slide_featured_post_object',
								'label'         => 'Post',
								'name'          => 'post',
								'type'          => 'post_object',
								'return_format' => 'object',
								'post_type'     => array( 'post', 'page', 'product' ),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_mobile_text',
						'label'             => 'Mobile Text',
						'name'              => 'mobile_text',
						'type'              => 'textarea',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_mobile_button_label',
						'label'             => 'Mobile Button Label',
						'name'              => 'mobile_button_label',
						'type'              => 'text',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
					array(
						'key'               => 'field_hb_slide_mobile_button_link',
						'label'             => 'Mobile Button Link',
						'name'              => 'mobile_button_link',
						'type'              => 'url',
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_hb_slide_layout',
									'operator' => '==',
									'value'    => 'split',
								),
							),
						),
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/do-home-banner',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	)
);
