<?php

require_once get_template_directory() . '/modules/acf-blocks/do-icon-link-bar/do-icon-link-bar-functions.php';

$id = 'icon-link-bar-' . $block['id'];
if ( ! empty( $block['anchor'] ) ) {
	$id = $block['anchor'];
}

$items = get_field( 'ilb_items' );
if ( empty( $items ) || ! is_array( $items ) ) {
	echo '<div class="do-icon-link-bar section"><div class="do-icon-link-bar__empty">' . esc_html__( 'Add links in the block settings.', 'blankslate-dataon' ) . '</div></div>';
	return;
}

do_render_icon_link_bar( $items, $id );
