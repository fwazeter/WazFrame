<?php
/**
 * Layout block support flag.
 *
 * @package wazframe
 */

include 'generate-css-styles.php';
include 'get-layout-style.php';

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

// TODO: add get-layout-style for custom blockGap values.
/**
 * Renders the layout config to the block wrapper.
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string                Filtered block content.
 */
function wf_render_layout_support_flag( string $block_content, array $block ): string
{
	// this function is super long - should break up into setter functions.
	$block_type             = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	$support_layout         = block_has_support( $block_type, array( '__experimentalLayout' ), false );

	if ( ! $support_layout ) {
		return $block_content;
	}

	// layout Settings
	$default_layout         = wp_get_global_settings( array( 'layout' ) );
	$default_block_layout   = wf_wp_array_get( $block_type->supports, array( '__experimentalLayout', 'default' ), array() );
	$used_layout            = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $default_block_layout;

	// blockGap Settings
	$block_gap              = wp_get_global_settings( array( 'spacing', 'blockGap' ) );
	$has_block_gap_support  = isset( $block_gap ) ? null !== $block_gap : false;

	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] ) {
		if ( ! $default_layout ) {
			return $block_content;
		}
		$used_layout = $default_layout;
	}

	$content    = wf_get_layout_style(
					$block_content,
					$block,
					$used_layout,
					$has_block_gap_support
	);

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