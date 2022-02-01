<?php

/**
 * Generates CSS class 'wf-container__default' & replaces
 * the auto-generated .wp-container-{$id} classes by WordPress.
 *
 * This container accompanies any block that utilizes layout support.
 * @return string
 */
function wf_default_layout_css(): string
{
	$selector       = '.wf-container__default';
	$layout         = wp_get_global_settings( array( 'layout' ) );
	$content_size   = $layout['contentSize'] ?? '';
	$wide_size      = $layout['wideSize'] ?? '';

	$style = '';

	if ( $content_size || $wide_size ) {
		// Maybe. Might have to do children?
		$style  = "$selector { box-sizing: content-box; }";
		$style  .= "$selector > * {";
		$style  .= 'box-sizing: border-box;';
		$style  .= 'max-inline-size: ' . esc_html( $content_size ) . ';';
		$style  .= 'margin-inline: auto;';
		$style  .= 'padding-inline: var( --wp--style--block-gap, 1rem )';
		$style  .= '}';

		$style  .= "$selector > .alignwide { max-inline-size: " . esc_html( $wide_size ) . ';}';
		$style  .= "$selector .alignfull { max-inline-size: none; }";

		$style  .= '.alignwide > * { max-inline-size:' . esc_html( $wide_size ) . '; }';
		// $style  .= '.alignfull > * { max-inline-size: none; }';
	}

	// default if not set content / widesize - maybe make a plugin option.
	$style      .= "$selector .alignleft { float: left; margin-block-start: var( --wp--style--block-gap, 2em ); }";
	$style      .= "$selector .alignright { float: right; margin-block-end: var( --wp--style--block-gap, 2em ); }";

	// we re-use this a few times, maybe make into function.
	return $style;
}

/**
 * Same as wf_default_layout_css(), except for blocks that use
 * the 'flex' property in __experimentalLayout, such as the
 * 'row' variant of the group block.
 *
 * @return string
 */
function wf_flex_layout_css(): string
{
	$selector       = '.wf-container__flex';
	//$get_layout     = wp_get_global_settings( array( 'layout' ) );
	//$get_block_gap  = wp_get_global_settings( array( 'spacing', 'blockGap' ) );

	// flex alignment options
/*	$justify_content_options    = array(
		'left'          => 'flex-start',
		'right'         => 'flex-end',
		'center'        => 'center',
		'space-between' => 'space-between',
	);*/

	// Orientation will be added sooner or later to WP
	// $layout_orientation  = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

	/**
	 * In the block editor, with flex items the user can
	 * select options: justify items: left, center, right, space between
	 * and whether or not to apply wrap or no wrap.
	 *
	 * In common.css for blocks, items-justified-right appears.
	 *
	 * We should just use this for those options via logic.
	 */
	//$flex_wrap_options  = array( 'wrap', 'nowrap' );

	/**
	 * Creating:
	 * display: flex;
	 * gap: var( --wp--style--block-gap, 0.5em );
	 * flex-wrap: wrap; <-- default value is nowrap. util class.
	 * align-items: center / flex-start / flex-end
	 * justify-content: space-between;
	 */
	$style  = "$selector {";
	$style  .= 'display: flex;';
	$style  .= 'gap: var( --wp--style--block-gap, 1.5rem)';
	$style  .= '}';

	$style  .= "$selector" . '_wrap { flex-wrap: wrap; }';

	// Currently this is the only option available in Gberg/Core.
	$style  .= "$selector" . '_items-center { align-items: center; }';

	$style  .= "$selector > * { margin: 0; }";

	return $style;

}

/**
 * Generates consistent vertical spacing between elements.
 *
 * @return string
 */
function wf_vertical_stack_css(): string
{
	$selector       = '.wf-vstack';
	// pull theme.json blockGap setting
	$get_block_gap  = wp_get_global_settings( array( 'spacing', 'blockGap' ) );

	// Create our CSS class if blockGap is set.
	$style = '';
	if ( isset( $get_block_gap ) ) {
		// use WP default custom prop
		$gap_style = '--wp--style--block-gap';

		// fallback value
		$fallback = '1.5rem';

		// First child, no margin margin-block = margin-top & margin-bottom.
		$style .= "$selector > * { margin-block: 0; }";

		// Apply margin-top (margin-block-start) to children.
		// We use margin-block-start because it applies to ltr / rtl etc by default.
		$style .= "$selector > * + * { margin-block-start: var($gap_style, $fallback); }";

	}
	return $style;
}

function wf_generate_css_classes() {
	$layout     = wf_default_layout_css();
	$block_gap  = wf_vertical_stack_css();
	$flex       = wf_flex_layout_css();

	echo '<style>' . $layout . $block_gap . $flex . '</style>';
}

add_action( 'wp_footer', 'wf_generate_css_classes', 10, 2 );