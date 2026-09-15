<?php

if ( ! function_exists( 'do_ilb_register_block_pattern' ) ) {
	function do_ilb_register_block_pattern() {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}

		register_block_pattern_category(
			'dataon',
			array(
				'label' => __( 'DataON', 'blankslate-dataon' ),
			)
		);

		register_block_pattern(
			'blankslate-dataon/icon-link-bar',
			array(
				'title'       => __( 'Icon Link Bar', 'blankslate-dataon' ),
				'description' => __( 'Four linked icon cards in a horizontal row — Azure Local, DataON Servers, VMware Migration, and DataON Support.', 'blankslate-dataon' ),
				'categories'  => array( 'dataon' ),
				'keywords'    => array( 'icon', 'links', 'cards', 'quick links', 'azure', 'dataon' ),
				'content'     => do_ilb_get_pattern_content(),
			)
		);
	}
}

add_action( 'acf/init', 'do_ilb_register_block_pattern', 20 );
