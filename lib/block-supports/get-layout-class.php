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
function wf_get_layout_style( string $block_content, array $layout, bool $has_block_gap_support, string $gap_value )
{
	$content        = '';

	// Determines the layout type.
	$layout_type    = isset( $layout['type'] ) ? $layout['type'] : 'default';
	if ( 'default' === $layout_type ) {

		$class      = set_default_layout_class( $layout, $has_block_gap_support );

		$content    = wf_replace_content_class( $class, $block_content );

	} elseif ( 'flex' === $layout_type ) {

		$class      = set_flex_layout_class( $layout );

		$content    = wf_replace_content_class( $class, $block_content );

	}

	return $content;
}
// ultimately, this should return the $content to be replaced.