<?php
/**
 * Layout block support flag.
 *
 * @package wazframe
 */

include 'generate-css-classes.php';
include 'set-css-classes.php';

/**
 * Registers the layout block attribute for block types that support it.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wf_register_layout_support( $block_type ) {
	$support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );
	if ( $support_layout ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'layout', $block_type->attributes ) ) {
			$block_type->attributes['layout'] = array(
				'type' => 'object',
			);
		}
	}
}

// TODO: add get_layout_style for custom values.
/**
 * Renders the layout config to the block wrapper.
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string                Filtered block content.
 */
function wf_render_layout_support_flag( string $block_content, array $block )
{
	// this function is super long - should break up into setter functions.
	$block_type             = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	$support_layout         = block_has_support( $block_type, array( '__experimentalLayout' ), false );

	if ( ! $support_layout ) {
		return $block_content;
	}

	// layout Settings
	$default_layout         = wp_get_global_settings( array( 'layout' ) );
	$default_block_layout   = _wp_array_get( $block_type->supports, array( '__experimentalLayout', 'default' ), array() );
	$layout                 = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $default_block_layout;

	// blockGap Settings
	$block_gap              = wp_get_global_settings( array( 'spacing', 'blockGap' ) );
	$has_block_gap_support  = isset( $block_gap ) ? null !== $block_gap : false;


	if ( isset( $layout['inherit'] ) && $layout['inherit'] ) {
		if ( ! $default_layout ) {
			return $block_content;
		}
		$layout = $default_layout;
	}

	$class      = set_layout_class( $layout, $has_block_gap_support );

	$content    = '';

	// Temporary, can remove later
	$layout_type    = isset( $layout['type'] ) ? $layout['type'] : 'default';

	if ( 'default' === $layout_type ) {
		$content = preg_replace(
			'/' . preg_quote( 'class="', '/' ) . '/',
			$class,
			$block_content,
			1
		);
	} elseif ( 'flex' === $layout_type ) {

		$css_class = 'class="wf-container__flex ';

		$flex_wrap_options  = array( 'wrap', 'nowrap' );
		$flex_wrap = ! empty ( $layout['flexWrap'] ) && in_array( $layout['flexWrap'], $flex_wrap_options, true ) ?
			$layout['flexWrap'] : 'wrap';

		if ( 'wrap' === $flex_wrap ) {
			$css_class  .= 'wf-container__flex_wrap ';
		}

		$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

		if ( 'horizontal' === $layout_orientation ) {
			$css_class  .= 'wf-container__flex_items-center ';
		}

		$justify_content_options    = array(
			'left'          => 'flex-start',
			'right'         => 'flex-end',
			'center'        => 'center',
			'space-between' => 'space-between',
		);

		if ( ! empty( $layout['justifyContent'] ) &&
		     array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
			// probably do switch/case here.
			if ( 'left' === $layout['justifyContent'] ) {
				$css_class  .= 'items-justified-left ';
			} elseif ( 'right' === $layout['justifyContent'] ) {
				$css_class  .= 'items-justified-right ';
			} elseif ( 'center' === $layout['justifyContent'] ) {
				$css_class  .= 'items-justified-center ';
			} elseif ( 'space-between' === $layout['justifyContent'] ) {
				$css_class  .= 'items-justified-space-between ';
			}
		}

		$content = preg_replace(
			'/' . preg_quote( 'class="', '/' ) . '/',
			$css_class,
			$block_content,
			1
		);

	}

	/*$block_gap             = wp_get_global_settings( array( 'spacing', 'blockGap' ) );

	$has_block_gap_support = isset( $block_gap ) ? null !== $block_gap : false;


	$id        = uniqid();
	$gap_value = _wp_array_get( $block, array( 'attrs', 'style', 'spacing', 'blockGap' ) );
	// Skip if gap value contains unsupported characters.
	// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
	// because we only want to match against the value, not the CSS attribute.
	$gap_value = preg_match( '%[\\\(&=}]|/\*%', $gap_value ) ? null : $gap_value;
	$style     = gutenberg_get_layout_style( ".wp-container-$id", $used_layout, $has_block_gap_support, $gap_value );
	// This assumes the hook only applies to blocks with a single wrapper.
	// I think this is a reasonable limitation for that particular hook.*/



/*	// Ideally styles should be loaded in the head, but blocks may be parsed
	// after that, so loading in the footer for now.
	// See https://core.trac.wordpress.org/ticket/53494.
	add_action(
		'wp_footer',
		function () use ( $style ) {
			echo '<style>' . $style . '</style>';
		}
	);*/

	return $content;
}

WP_Block_Supports::get_instance()->register(
	'layout',
	array(
		'register_attribute'    =>  'wf_register_layout_support'
	)
);

if ( function_exists( 'wp_render_layout_support_flag' ) ) {
	remove_filter( 'render_block', 'wp_render_layout_support_flag' );
}
add_filter( 'render_block', 'wf_render_layout_support_flag', 10, 2 );