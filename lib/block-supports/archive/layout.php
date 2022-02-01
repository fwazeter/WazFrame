<?php

// There's some permissions with wp_get_global_settings requiring this to work, else uncaught error
// that's unable to find the function wp_get_current_user().
include_once( ABSPATH . 'wp-includes/pluggable.php' );

// Has to load after plugins loaded.
include   'generate-css/stack.css.php';
include   'generate-css/content-size.php';

/**
 * Overriding Core's version of this function.
 *
 * Registers the layout block attribute for block types that support it.
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wf_register_layout_support( WP_Block_Type $block_type ) {
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

/**
 * Renders layout config to the block wrapper,
 * replaces WordPress core block-supports/layout.php with a single
 * CSS class.
 */
function wf_render_layout_support_flag( $block_content, $block ) {

	// Pull from universal data at some point.
	$selectors = [
		'blockGap'  => 'wf-stack',
		'layout'    => 'wf-container',
	];
	// pulls registered blocks from block registry
	$block_type         = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	// does the block support Layout settings.
	$support_layout     = block_has_support( $block_type, array( '__experimentalLayout' ), false );

	// if the block doesn't support layout settings, return.
	if ( ! $support_layout ) {
		return $block_content;
	}

	$default_layout         = wp_get_global_settings( array( 'layout' ) );
	// checks block's experimentalLayout setting for default.
	$block_default_layout   = _wp_array_get( $block_type->supports, array( '__experimentalLayout', 'default' ), array() );

	// checks if block layout attr is set or if it matches default.
	$used_layout            = isset( $block['attrs']['layout'] ) ? $block['attrs']['layout'] : $block_default_layout;

	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] ) {
		if ( ! $default_layout ) {
			return $block_content;
		}
		// sets used_layout to default of theme. Maybe unnecessary for us here. b/c this gets passed to generate css.
		$used_layout    = $default_layout;
	}

	// Set desired CSS selector for block gap & create the css class.
	$content = preg_replace(
		'/' . preg_quote( 'class="', '/' ) . '/',
		'class="' . $selectors['blockGap'] . ' ' . $selectors['layout'] . ' ',
		$block_content,
		1
	);

	return $content;
}

// Register the block support. (overrides core one).
WP_Block_Supports::get_instance()->register(
	'layout',
	array(
		'register_attribute' => 'wf_register_layout_support',
	)
);

// Remove WordPress Core wp_render_layout_support_flag
if ( function_exists( 'wp_render_layout_support_flag' ) ) {
	remove_filter( 'render_block', 'wp_render_layout_support_flag' );
}
// If Gutenberg is enabled, remove that one too.
if ( function_exists( 'gutenberg_render_layout_support_flag' ) ) {
	remove_filter( 'render_block', 'gutenberg_render_layout_support_flag' );
}
// add ours.
add_filter( 'render_block', 'wf_render_layout_support_flag', 10, 2 );

// if center = wp-block-post-content, do the thing > * thing, else no.

$selectors = [
	'blockGap'  => '.wf-stack',
	'layout'    => '.wf-container',
];

wf_create_stack( $selectors['blockGap'] );
wf_create_layout_container( $selectors['layout'] );
