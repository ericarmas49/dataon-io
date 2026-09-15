<?php

if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

acf_add_local_field_group(
	array(
		'key'                   => 'group_do_icon_link_bar',
		'title'                 => 'Icon Link Bar',
		'fields'                => array(
			array(
				'key'          => 'field_ilb_items',
				'label'        => 'Links',
				'name'         => 'ilb_items',
				'type'         => 'repeater',
				'instructions' => 'Add icon cards. They display in a horizontal row on desktop.',
				'layout'       => 'row',
				'button_label' => 'Add Link',
				'min'          => 1,
				'max'          => 6,
				'sub_fields'   => array(
					array(
						'key'           => 'field_ilb_icon',
						'label'         => 'Icon',
						'name'          => 'icon',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'library'       => 'all',
						'mime_types'    => 'jpg,jpeg,png,svg,webp',
					),
					array(
						'key'   => 'field_ilb_label',
						'label' => 'Title',
						'name'  => 'label',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_ilb_subtitle',
						'label' => 'Subtitle',
						'name'  => 'subtitle',
						'type'  => 'text',
					),
					array(
						'key'           => 'field_ilb_link',
						'label'         => 'Link',
						'name'          => 'link',
						'type'          => 'link',
						'return_format' => 'array',
					),
				),
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/do-icon-link-bar',
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
