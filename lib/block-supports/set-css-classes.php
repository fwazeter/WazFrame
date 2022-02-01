<?php
/**
 * Sets the appropriate layout classes when the layout type is default.
 *
 * @param array $layout The Layout Object.
 * @param boolean $has_block_gap_support Whether the theme supports blockGap.
 *
 * @return string   CSS class to assign.
 */
function set_default_layout_class( array $layout, bool $has_block_gap_support = false ): string
{
	$assigned_class = 'class="';

	$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
	$wide_size    = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';

	if ( $content_size || $wide_size ) {
		$assigned_class .= 'wf-container__default ';
	}

	$assigned_class .= 'wf-container__inherit ';

	if ( $has_block_gap_support ) {
		$assigned_class .= 'wf-v_stack ';
	}

	return $assigned_class;
}

/**
 * Sets the appropriate layout classes when the layout type is flex.
 *
 * @param array $layout The Layout Object.
 * @param boolean $has_block_gap_support Whether the theme supports blockGap.
 *
 * @return string   CSS class to assign.
 */
function set_flex_layout_class( $layout ): string
{
	$assigned_class = 'class="wf-container__flex ';

	$flex_wrap_options  = array( 'wrap', 'nowrap' );
	$flex_wrap = ! empty ( $layout['flexWrap'] ) && in_array( $layout['flexWrap'], $flex_wrap_options, true ) ?
		$layout['flexWrap'] : 'wrap';

	if ( 'wrap' === $flex_wrap ) {
		$assigned_class  .= 'wf-container__flex_wrap ';
	}

	$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

	if ( 'horizontal' === $layout_orientation ) {
		$assigned_class  .= 'wf-container__flex_items-center ';
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
			$assigned_class  .= 'items-justified-left ';
		} elseif ( 'right' === $layout['justifyContent'] ) {
			$assigned_class  .= 'items-justified-right ';
		} elseif ( 'center' === $layout['justifyContent'] ) {
			$assigned_class  .= 'items-justified-center ';
		} elseif ( 'space-between' === $layout['justifyContent'] ) {
			$assigned_class  .= 'items-justified-space-between ';
		}
	}

	return $assigned_class;
}

function set_custom_css_style(){

}