<?php

if ( ! function_exists( 'do_ilb_get_image_url' ) ) {
	function do_ilb_get_image_url( $image ) {
		if ( empty( $image ) ) {
			return '';
		}

		if ( is_array( $image ) ) {
			return isset( $image['url'] ) ? $image['url'] : '';
		}

		if ( is_numeric( $image ) ) {
			$url = wp_get_attachment_image_url( (int) $image, 'full' );
			return $url ? $url : '';
		}

		return (string) $image;
	}
}

if ( ! function_exists( 'do_ilb_get_default_items' ) ) {
	function do_ilb_get_default_items() {
		$icon_base = get_template_directory_uri() . '/assets/icons/';

		return array(
			array(
				'icon'     => $icon_base . '1_Azure Local.svg',
				'label'    => 'Azure Local',
				'subtitle' => 'Hybrid cloud infrastructure solutions',
				'link'     => array(
					'url'    => home_url( '/microsoft-azure-local/' ),
					'title'  => 'Azure Local',
					'target' => '',
				),
			),
			array(
				'icon'     => $icon_base . '2_DataON Servers.svg',
				'label'    => 'DataON Servers for Azure Local',
				'subtitle' => 'Enterprise-grade HCI platforms',
				'link'     => array(
					'url'    => home_url( '/product/dataon-azl-premier-solution-for-azure-local/' ),
					'title'  => 'DataON Servers for Azure Local',
					'target' => '',
				),
			),
			array(
				'icon'     => $icon_base . '3_VMware Migration.svg',
				'label'    => 'VMware Migration',
				'subtitle' => 'Move workloads to Azure Local',
				'link'     => array(
					'url'    => home_url( '/make-the-switch-to-microsoft-azure-hybrid-cloud/' ),
					'title'  => 'VMware Migration',
					'target' => '',
				),
			),
			array(
				'icon'     => $icon_base . '4_DataON Support.svg',
				'label'    => 'DataON Support',
				'subtitle' => 'Expert services and assistance',
				'link'     => array(
					'url'    => 'https://dataonsupport.dataonstorage.com/support/home',
					'title'  => 'DataON Support',
					'target' => '',
				),
			),
		);
	}
}

if ( ! function_exists( 'do_ilb_get_icon_attachment_id' ) ) {
	function do_ilb_get_icon_attachment_id( $filename ) {
		static $cache = array();

		if ( isset( $cache[ $filename ] ) ) {
			return $cache[ $filename ];
		}

		$option_key = 'do_ilb_icon_' . md5( $filename );
		$stored_id  = (int) get_option( $option_key, 0 );
		if ( $stored_id && get_post( $stored_id ) ) {
			$cache[ $filename ] = $stored_id;
			return $stored_id;
		}

		$file_path = get_template_directory() . '/assets/icons/' . $filename;
		if ( ! file_exists( $file_path ) ) {
			$cache[ $filename ] = 0;
			return 0;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => 1,
				'post_status'    => 'inherit',
				'meta_query'     => array(
					array(
						'key'   => '_do_ilb_theme_icon',
						'value' => $filename,
					),
				),
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $existing[0] ) ) {
			update_option( $option_key, (int) $existing[0] );
			$cache[ $filename ] = (int) $existing[0];
			return $cache[ $filename ];
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( $filename );
		if ( ! $tmp || ! copy( $file_path, $tmp ) ) {
			$cache[ $filename ] = 0;
			return 0;
		}

		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload( $file_array, 0, null, array( 'test_form' => false ) );
		if ( file_exists( $tmp ) ) {
			@unlink( $tmp );
		}

		if ( is_wp_error( $attachment_id ) ) {
			$cache[ $filename ] = 0;
			return 0;
		}

		update_post_meta( $attachment_id, '_do_ilb_theme_icon', $filename );
		update_option( $option_key, (int) $attachment_id );
		$cache[ $filename ] = (int) $attachment_id;

		return $cache[ $filename ];
	}
}

if ( ! function_exists( 'do_ilb_get_pattern_items' ) ) {
	function do_ilb_get_pattern_items() {
		$icon_files = array(
			'1_Azure Local.svg',
			'2_DataON Servers.svg',
			'3_VMware Migration.svg',
			'4_DataON Support.svg',
		);

		$items = do_ilb_get_default_items();

		foreach ( $items as $index => $item ) {
			if ( isset( $icon_files[ $index ] ) ) {
				$items[ $index ]['icon'] = do_ilb_get_icon_attachment_id( $icon_files[ $index ] );
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'do_ilb_build_acf_block_data' ) ) {
	function do_ilb_build_acf_block_data( array $items ) {
		$data = array(
			'ilb_items'  => count( $items ),
			'_ilb_items' => 'field_ilb_items',
		);

		foreach ( $items as $index => $item ) {
			$prefix = 'ilb_items_' . $index . '_';
			$link   = is_array( $item['link'] ?? null ) ? $item['link'] : array();

			$data[ $prefix . 'icon' ]       = $item['icon'] ?? 0;
			$data[ '_' . $prefix . 'icon' ]  = 'field_ilb_icon';
			$data[ $prefix . 'label' ]       = $item['label'] ?? '';
			$data[ '_' . $prefix . 'label' ]  = 'field_ilb_label';
			$data[ $prefix . 'subtitle' ]    = $item['subtitle'] ?? '';
			$data[ '_' . $prefix . 'subtitle' ] = 'field_ilb_subtitle';
			$data[ $prefix . 'link' ]        = array(
				'title'  => $link['title'] ?? ( $item['label'] ?? '' ),
				'url'    => $link['url'] ?? '',
				'target' => $link['target'] ?? '',
			);
			$data[ '_' . $prefix . 'link' ] = 'field_ilb_link';
		}

		return $data;
	}
}

if ( ! function_exists( 'do_ilb_get_pattern_content' ) ) {
	function do_ilb_get_pattern_content() {
		$attributes = array(
			'name' => 'acf/do-icon-link-bar',
			'data' => do_ilb_build_acf_block_data( do_ilb_get_pattern_items() ),
			'mode' => 'preview',
		);

		return '<!-- wp:acf/do-icon-link-bar ' . wp_json_encode( $attributes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
	}
}

if ( ! function_exists( 'do_render_icon_link_bar' ) ) {
	function do_render_icon_link_bar( $items, $id = 'icon-link-bar-default' ) {
		if ( empty( $items ) || ! is_array( $items ) ) {
			return;
		}
		?>
		<div id="<?php echo esc_attr( $id ); ?>" class="do-icon-link-bar section">
			<div class="do-icon-link-bar__grid">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$icon_url   = do_ilb_get_image_url( $item['icon'] ?? null );
					$label      = $item['label'] ?? '';
					$subtitle   = $item['subtitle'] ?? '';
					$link       = $item['link'] ?? null;
					$link_url   = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : '';
					$link_title = is_array( $link ) && ! empty( $link['title'] ) ? $link['title'] : $label;
					$target     = is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '';
					$item_class = 'do-icon-link-bar__item';
					?>
					<?php if ( $link_url ) : ?>
						<a
							href="<?php echo esc_url( $link_url ); ?>"
							class="<?php echo esc_attr( $item_class ); ?>"
							<?php echo $target ? 'target="' . esc_attr( $target ) . '"' : ''; ?>
							<?php echo $target === '_blank' ? 'rel="noopener noreferrer"' : ''; ?>
						>
							<?php if ( $icon_url ) : ?>
								<span class="do-icon-link-bar__icon-wrap">
									<img class="do-icon-link-bar__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" />
								</span>
							<?php endif; ?>
							<span class="do-icon-link-bar__text">
								<?php if ( $label ) : ?>
									<span class="do-icon-link-bar__title"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
								<?php if ( $subtitle ) : ?>
									<span class="do-icon-link-bar__subtitle"><?php echo esc_html( $subtitle ); ?></span>
								<?php endif; ?>
							</span>
							<span class="screen-reader-text"><?php echo esc_html( $link_title ); ?></span>
						</a>
					<?php else : ?>
						<div class="<?php echo esc_attr( $item_class ); ?> do-icon-link-bar__item--static">
							<?php if ( $icon_url ) : ?>
								<span class="do-icon-link-bar__icon-wrap">
									<img class="do-icon-link-bar__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" />
								</span>
							<?php endif; ?>
							<span class="do-icon-link-bar__text">
								<?php if ( $label ) : ?>
									<span class="do-icon-link-bar__title"><?php echo esc_html( $label ); ?></span>
								<?php endif; ?>
								<?php if ( $subtitle ) : ?>
									<span class="do-icon-link-bar__subtitle"><?php echo esc_html( $subtitle ); ?></span>
								<?php endif; ?>
							</span>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
