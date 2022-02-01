<?php

function wf_create_layout_container( $selector ): bool
{
	// default_layout refers to __experimentalLayout 'default', flex to come.
	$setting                = wp_get_global_settings( array( 'layout' ) );

	// passed via layout_support_flag:
	// $used_layout           = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $default_block_layout;

	$content_size           = isset( $setting['contentSize'] ) ? $setting['contentSize'] : '';
	$wide_size              = $setting['wideSize'] ?? '';

	/*// This seems to set one value to another in case only one is set in theme.json
	$all_max_width_value    = $content_size ? $content_size : $wide_size;
	$wide_max_width_value   = $wide_size ? $wide_size : $content_size;

	// Ensure single CSS rule & all tags stripped for security.
	// Eventually replace with 'safecss_filter_attr': https://core.trac.wordpress.org/ticket/46197
	$all_max_width_value    = wp_strip_all_tags( explode( ';', $all_max_width_value)[0] );
	$wide_max_width_value   = wp_strip_all_tags( explode( ';', $wide_max_width_value )[0] );*/

	$style = '';

	if ( $content_size || $wide_size ) {
		// Maybe. Might have to do children?
		$style  = "$selector { box-sizing: content-box; }";

		// ContentSize
		$style  .= "$selector > * {";

		// Using esc_html()[0] here results in an integer result if set to e.g. rem
		$style  .= 'max-inline-size: ' . $content_size . ';';
		$style  .= 'margin-inline: auto;';

		// Adds wrapper padding if screen less than contentSize. Fixes some issues.
		// every-layout uses padding-inline-start & padding-inline-end, unsure why.
		$style  .= 'padding-inline: var( --wp--style--block-gap, 1rem )';

		$style  .= '}';

		// alignWide
		$style  .= "$selector > .alignwide { max-inline-size: " . esc_html( $wide_size ) . ';}';
		$style  .= "$selector .alignfull { max-inline-size: none; }";

		//$style  .= '.alignwide > * { max-inline-size: none; }';
		//$style  .= '.alignfull > * { max-inline-size: none; }';
	}

	// default if not set content / widesize - maybe make a plugin option.
	$style      .= "$selector .alignleft { float: left; margin-block-start: var( --wp--style--block-gap, 2em ); }";
	$style      .= "$selector .alignright { float: right; margin-block-end: var( --wp--style--block-gap, 2em ); }";

	// we re-use this a few times, maybe make into function.
	return add_action(
		'wp_footer',
		function () use ( $style ) {
			echo '<style>' . $style . '</style>';
		}
	);
}