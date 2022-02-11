<?php

/**
 * Generates CSS class 'wf-container__default' & replaces
 * the auto-generated .wp-container-{$id} classes by WordPress.
 *
 * This container accompanies any block that utilizes layout support.
 * @return string       The most common class handling sizing.
 */
function wf_default_layout_css(): string
{
	$selector       = '.wf-container__default';

	$layout         = wp_get_global_settings( array( 'layout' ) );
	$content_size   = $layout['contentSize'] ?? '';
	$wide_size      = $layout['wideSize'] ?? '';

	$style  = "$selector > * {";
	$style  .= 'max-width: ' . esc_html( $content_size ) . ';';
	$style  .= 'margin-left: auto !important;';
	$style  .= 'margin-right: auto !important;';
	$style  .= '}';

	$style  .= "$selector > .alignwide { max-width: " . esc_html( $wide_size ) . ';}';
	$style  .= "$selector .alignfull { max-width: none; }";

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
	$selector   = '.wf-container__flex';

	$style      = "$selector {";
	$style      .= 'display: flex;';
	$style      .= '}';
	$style      .= "$selector" . '_wrap { flex-wrap: wrap; }';

	// Currently this is the only option available in Gberg/Core.
	$style      .= "$selector" . '_items-center { align-items: center; }';

	$style      .= "$selector > * { margin: 0; }";

	// If column, rather than horizontal layout orientation.

	$style      .= "$selector-column {";
	$style      .= 'flex-direction: column;';
	$style      .= '}';

	return $style;

}

/**
 * Generates consistent blockGap spacing between elements.
 *
 * @param $gap_value
 *
 * @return array    The css classes related to gap properties.
 */
function wf_block_gap_css( $gap_value, $id ): array
{
	// Mostly unnecessary extra array - but prepping for making a php class.
	$selector       = array(
		'inherit'   => '.wf-container__inherit',
		'v_stack'   => '.wf-v_stack',
		'flex'      => '.wf-container__flex-gap',
		'id'        => $id,
	);

	$inherit        = $selector['inherit'];

	$gap_style      = $gap_value ? $gap_value : '--wp--style--block-gap';

	// the alignleft / alignright class applied to everyone.
	// added the blockGap property here because you'd likely want to use this by default.
	// using a fallback means that we can safely pass the --wp--style--block-gap var w/o
	// worrying about support.
	$inherit_style  = "$inherit .alignleft { float: left; margin-right: var( $gap_style, 2em); }";
	$inherit_style  .= "$inherit .alignright { float: right; margin-left: var( $gap_style, 2em); }";

	if ( $selector['id'] !== null ) {
		$stack          = $selector['v_stack'] . '-' . $selector['id'];
		$flex           = $selector['flex'] . '-' . $selector['id'];

		$stack_style    = "$stack > * { margin-top: 0; margin-bottom: 0; }";
		$stack_style    = "$stack > * + * { margin-top: $gap_style; margin-bottom: 0; }";

		$flex_style     = "$flex { gap: $gap_style; }";

	} else {
		$stack          = $selector['v_stack'];
		$flex           = $selector['flex'];

		$stack_style    = "$stack > * { margin-top: 0; margin-bottom: 0; }";
		$stack_style    .= "$stack > * + * { margin-top: var( $gap_style, 1.5rem ); margin-bottom: 0; }";

		$flex_style     = "$flex { gap: var( $gap_style, 0.5em ); }";
	}

	// Create our CSS class if blockGap is set.

	/*$stack_style    = "$stack > * { margin-top: 0; margin-bottom: 0; }";
	$stack_style    .= "$stack > * + * { margin-top: var( $gap_style, 1.5rem ); margin-bottom: 0; }";

	$flex_style     = "$flex { gap: var( $gap_style, 0.5em ); }";*/

	$styles         = array(
		'inherit'   => $inherit_style,
		'stack'     => $stack_style,
		'flex'      => $flex_style
	);

	return $styles;
}

/**
 * Creates a unique CSS class to handle custom layout sizes set by the user
 * on an individual block.
 *
 * @param string $id      Unique ID generated to create the class
 * @param string $content_size  The contentSize attr set by the user on the block.
 * @param string $wide_size     The wideSize attr set by the user on the block.
 * @param string|null $gap_value     The block's gap value to apply
 *
 * @return array   the custom CSS class containing user set attr's.
 */
function wf_custom_layout_css_style(
									string $id,
									string $content_size,
									string $wide_size,
									string $gap_value = null ): array
{

	$selector   = 'wf-container__layout' . '-' . $id;
	// Data already comes in cleaned from wf_get_layout_style()
	$style  = ".$selector > * {";
	$style .= 'max-width: ' . esc_html( $content_size ) . ';';
	$style .= 'margin-left: auto !important;';
	$style .= 'margin-right: auto !important;';
	$style .= '}';

	$style .= ".$selector > .alignwide { max-width: " . esc_html( $wide_size ) . ';}';
	$style .= ".$selector .alignfull { max-width: none; } ";

	// if the gap value is unique, generate a unique version.
	$gap_style = '';
	$flex_style = '';
	$gap_selector = '';
	$flex_selector = '';
	if ( $gap_value ) {
		$gap_selector   = 'wf-v_stack' . '-' . $id;
		$flex_selector  = 'wf-container__flex-gap' . '-' . $id;
		$gap_css        = wf_block_gap_css( $gap_value, $id );

		$gap_style      = $gap_css['stack'];
		$flex_style     = $gap_css['flex'];
	}

	$styles = array(
		'selector'      => $selector,
		'gap_selector'  => $gap_selector,
		'flex_selector' => $flex_selector,
		'base_style'    => $style,
		'gap_style'     => $gap_style,
		'flex_style'    => $flex_style,
	);

	return $styles;

}

/**
 * Generates the CSS class to be used once for all blocks sharing same values.
 *
 * @return void
 */
function wf_generate_css_styles() {

	$block_gap              = wp_get_global_settings( array( 'spacing', 'blockGap' ) );
	$has_block_gap_support  = isset( $block_gap ) ? null !== $block_gap : false;

	$layout     = wf_default_layout_css();
	$flex       = wf_flex_layout_css();

	if ( $has_block_gap_support ) {
		$block_gap  = wf_block_gap_css( '--wp--style--block-gap', null );
		echo '<style>'
		     . $layout
		     . $block_gap['inherit']
		     . $block_gap['stack']
		     . $flex
		     . $block_gap['flex']
		     . '</style>';
	}   else {
			echo '<style>' . $layout . $flex . '</style>';
	}
}
add_action( 'wp_footer', 'wf_generate_css_styles', 10, 2 );