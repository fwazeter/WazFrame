<?php

/**
 * Generates CSS class 'wf-container__default' & replaces
 * the auto-generated .wp-container-{$id} classes by WordPress.
 *
 * This container accompanies any block that utilizes layout support.
 * @return array
 */
function wf_default_layout_css(): array
{
	$selector       = [
		'default'   => '.wf-container__default',
		'inherit'   => '.wf-container__inherit',
	];
	$layout         = wp_get_global_settings( array( 'layout' ) );
	$content_size   = $layout['contentSize'] ?? '';
	$wide_size      = $layout['wideSize'] ?? '';

	$default    = $selector['default'];
	$inherit    = $selector['inherit'];

	// The base container class when contentSize & wideSize are set.
	$default_class  = "$default > * {";
	$default_class  .= 'box-sizing: border-box;';
	$default_class  .= 'max-inline-size: ' . esc_html( $content_size ) . ';';
	$default_class  .= 'margin-inline: auto;';
	$default_class  .= 'padding-inline: var( --wp--style--block-gap, 1rem )';
	$default_class  .= '}';

	$default_class  .= "$default > .alignwide { max-inline-size: " . esc_html( $wide_size ) . ';}';
	$default_class  .= "$default .alignfull { max-inline-size: none; }";

	$inherit_class  = "$inherit .alignleft { float: left; margin-block-start: var( --wp--style--block-gap, 2em ); }";
	$inherit_class  .= "$inherit .alignright { float: right; margin-block-end: var( --wp--style--block-gap, 2em ); }";

	$generated_classes = [
		'default'   => $default_class,
		'inherit'   => $inherit_class
	];

	// we re-use this a few times, maybe make into function.
	return $generated_classes;
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
	$selector       = '.wf-v_stack';
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

	echo '<style>' . $layout['default'] . $layout['inherit'] . $block_gap . $flex . '</style>';
}

add_action( 'wp_footer', 'wf_generate_css_classes', 10, 2 );