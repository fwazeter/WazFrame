<?php

/**
 * WordPress KSES safe inline styles does not include logical properties
 * like margin-inline-start, so we're adding them.
 *
 * Mozilla Logical Properties List & Compatibility
 * @url https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Logical_Properties
 *
 * WordPress safecss_filter_attr
 * @url https://github.com/WordPress/WordPress/blob/15e552e1055a55176500f8440200f21b4d16fb4d/wp-includes/kses.php#L2170
 *
 * @param $attrs string[] Array of allowed CSS attributes.
 *
 * @return string[] CSS attributes.
 * @since 0.0.1
 */
function wf_safe_style_attrs( array $attrs ): array {
	$add_attrs = array(
		// Properties for sizing
		'block-size',
		'inline-size',
		'max-block-size',
		'max-inline-size',
		'min-block-size',
		'min-inline-size',

		// Properties for borders
		'border-block',
		'border-block-color',
		'border-block-end',
		'border-block-end-color',
		'border-block-end-style',
		'border-block-end-width',
		'border-block-start',
		'border-block-start-color',
		'border-block-start-style',
		'border-block-start-width',
		'border-block-style',
		'border-block-width',
		'border-inline',
		'border-inline-color',
		'border-inline-end',
		'border-inline-end-color',
		'border-inline-end-style',
		'border-inline-end-width',
		'border-inline-start',
		'border-inline-start-color',
		'border-inline-start-style',
		'border-inline-start-width',
		'border-inline-style',
		'border-inline-width',
		'border-start-start-radius',
		'border-start-end-radius',

		// Properties for margin & padding
		'margin-block',
		'margin-block-end',
		'margin-block-start',
		'margin-inline',
		'margin-inline-end',
		'margin-inline-start',
		'padding-block',
		'padding-block-end',
		'padding-block-start',
		'padding-inline-end',
		'padding-inline-start',

		// Properties for float & positioning
		'inset',
		'inset-block',
		'inset-block-end',
		'inset-block-start',
		'inset-inline',
		'inset-inline-end',
		'inset-inline-start',

		// Other Misc Props
		'overflow-block',
		'overflow-inline',
		'overscroll-behavior-block',
		'overscroll-behavior-inline',
		'resize',

	);
	foreach ( $add_attrs as $attr ) {
		$attrs[] = $attr;
	}

	return $attrs;
}

add_filter( 'safe_style_css', 'wf_safe_style_attrs' );

// maybe this one:
// add_filter( 'safecss_filter_attr_allow_css', '', 10, 2 );