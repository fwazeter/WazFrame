<?php

include 'set-css-classes.php';
/**
 * Adds the desired classes to the blocks CSS class.
 *
 * @param string $class           The class string to replace default.
 * @param string $block_content   The content from the block.
 *
 * @return  string                  The content being added to the container 'class'
 */
function wf_replace_content_class( string $class, string $block_content ): string
{

	$content = preg_replace(
		'/' . preg_quote( 'class="', '/' ) . '/',
		$class,
		$block_content,
		1
	);

	return $content;
}

/**
 * Gets the appropriate CSS class corresponding to the provided layout.
 *
 * @param string $block_content         The content from the block.
 * @param array $block                  The block object.
 * @param array $layout                 Layout object. The one that is passed has already checked the existence of default block layout.
 * @param bool $has_block_gap_support   Whether the theme has support for the block gap.
 *
 * @return string CSS style.
 */
function wf_get_layout_style(   string $block_content,
								array $block,
								array $layout,
								bool $has_block_gap_support ): string
{
	$content = '';

	// Determines the layout type.
	$layout_type    = isset( $layout['type'] ) ? $layout['type'] : 'default';

	// unique id generated to share across properties if created.

	$unique_id      = uniqid();
	$gap_value      = wf_wp_array_get( $block, array( 'attrs', 'style', 'spacing', 'blockGap' ) );
	$gap_value      = preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null: $gap_value;

	if ( 'default' === $layout_type ) {
		/**
		 * If a block has a 'layout' attribute of 'contentSize' or
		 * 'wideSize' set, then it is by default a custom entry.
		 *
		 * Because blocks that do not have this attribute inherit their
		 * settings from either their parent block, cannot set an
		 * explicit content & wide width.
		 *
		 * Blocks will inherit their sizing from the theme settings
		 * without adding the attribute directly to the block.
		 * Instead, they have an 'align' attribute set to wide or full.
		 */
		$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
		$wide_size    = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';

		$block_content_size = wf_wp_array_get( $block, array( 'attrs', 'layout', 'contentSize' ) );
		$block_wide_size    = wf_wp_array_get( $block, array( 'attrs', 'layout', 'wideSize' ) );

		if ( $block_content_size || $block_wide_size ) {

			$all_max_width_value  = $block_content_size ? $block_content_size : $block_wide_size;
			$wide_max_width_value = $block_wide_size ? $block_wide_size : $block_content_size;

			// Make sure there is a single CSS rule, and all tags are stripped for security.
			// TODO: Use `safecss_filter_attr` instead - once https://core.trac.wordpress.org/ticket/46197 is patched.
			$all_max_width_value  = wp_strip_all_tags( explode( ';', $all_max_width_value )[0] );
			$wide_max_width_value = wp_strip_all_tags( explode( ';', $wide_max_width_value )[0] );

			$id     = "$unique_id";

			$build_class = wf_custom_layout_css_style(  $id,
														$all_max_width_value,
														$wide_max_width_value,
														$gap_value
			);

			$assigned_classes = 'class="' . $build_class['selector'] . ' ';

			if ( $build_class['gap_style'] ) {
				// when assigning names, need to have a different identifier as string, else array.
				$assigned_classes .= $build_class['gap_selector']  . ' ';
				$assigned_classes .= 'wf-container__inherit ';

				add_action(
					'wp_footer',
					function () use ( $build_class ) {
						echo '<style>'
						     . $build_class['base_style']
						     . $build_class['gap_style']
						     . '</style>';
					}
				);
			} else {
				add_action(
					'wp_footer',
					function () use ( $build_class ) {
						echo '<style>' . $build_class['base_style'] . '</style>';
					}
				);
			}

			$content = wf_replace_content_class( $assigned_classes, $block_content );
		} else  {

			if ( $gap_value ) {

				$id = $unique_id;

				$create_new_style = wf_custom_layout_css_style( $id, '', '', $gap_value );

				$new_class_id = $create_new_style['gap_selector'];

				$assigned_classes = set_default_layout_class( $layout, $has_block_gap_support, $new_class_id );

				add_action(
					'wp_footer',
					function () use ( $create_new_style ) {
						echo '<style>' . $create_new_style['gap_style'] . '</style>';
					}
				);

				$content = wf_replace_content_class( $assigned_classes, $block_content );

			} else {
				$class = set_default_layout_class( $layout, $has_block_gap_support );

				$content = wf_replace_content_class( $class, $block_content );
			}

		}

	} elseif ( 'flex' === $layout_type ) {

		if ( $gap_value ) {

			$id = $unique_id;

			$create_new_flex_gap    = wf_custom_layout_css_style( $id, '', '', $gap_value );

			$new_class_id           = $create_new_flex_gap['flex_selector'];

			$assigned_classes       = set_flex_layout_class( $layout, $new_class_id );

			add_action(
				'wp_footer',
				function () use ( $create_new_flex_gap ) {
					echo '<style>' . $create_new_flex_gap['flex_style'] . '</style>';
				}
			);

			$content = wf_replace_content_class( $assigned_classes, $block_content );

		} else {
			$class   = set_flex_layout_class( $layout );
			$content = wf_replace_content_class( $class, $block_content );
		}

	}

	return $content;
}
