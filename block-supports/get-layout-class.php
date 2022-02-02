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
 * @param string $block_content   The content from the block.
 * @param array $layout   Layout object. The one that is passed has already checked the existence of default block layout.
 * @param boolean $has_block_gap_support Whether the theme has support for the block gap.
 * @param string  $gap_value The block gap value to apply.
 *
 * @return string CSS style.
 */
function wf_get_layout_style( string $block_content, array $block, array $layout, bool $has_block_gap_support, string $gap_value ) {
	$content = '';

	// Determines the layout type.
	$layout_type = isset( $layout['type'] ) ? $layout['type'] : 'default';
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

			$unique_id = uniqid();
			$id        = "wf-container__layout-$unique_id ";

			$build_class = wf_custom_layout_css_style( $id, $all_max_width_value, $wide_max_width_value );

			$assigned_classes = 'class="' . $id . ' ';
			$assigned_classes .= 'wf-container__inherit ';

			add_action(
				'wp_footer',
				function () use ( $build_class ) {
					echo '<style>' . $build_class . '</style>';
				}
				);

			$content = wf_replace_content_class( $assigned_classes, $block_content );
		} else  {

			$class = set_default_layout_class( $layout, $has_block_gap_support );

			$content = wf_replace_content_class( $class, $block_content );
		}

	} elseif ( 'flex' === $layout_type ) {
		$class   = set_flex_layout_class( $layout );
		$content = wf_replace_content_class( $class, $block_content );
	}

	return $content;
}
