<?php
/**
 * Sets the appropriate layout classes when the layout type is default.
 *
 * @param array $layout The Layout Object.
 * @param boolean $has_block_gap_support Whether the theme supports blockGap.
 *
 * @return string   CSS class to assign.
 */
function set_default_layout_class( array $layout, bool $has_block_gap_support = false, $id = null ): string
{
	$assigned_class = 'class="';

	$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
	$wide_size    = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';

	if ( $content_size || $wide_size ) {
		$assigned_class .= 'wf-container__default ';
	}

	if ( $id !== null ) {
		// will create name & property - but here, we just want to set the name
		$assigned_class .= "$id ";

	} elseif ( $has_block_gap_support ) {
		$assigned_class .= 'wf-v_stack ';
	}

	$assigned_class .= 'wf-container__inherit ';

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
function set_flex_layout_class( $layout, $id = null ): string
{
	// do not currently need custom class for flex, except blockGap.
	$assigned_class = 'class="wf-container__flex ';

	$flex_wrap_options  = array( 'wrap', 'nowrap' );
	$flex_wrap = ! empty ( $layout['flexWrap'] ) && in_array( $layout['flexWrap'], $flex_wrap_options, true ) ?
		$layout['flexWrap'] : 'wrap';

	if ( 'wrap' === $flex_wrap ) {
		$assigned_class  .= 'wf-container__flex_wrap ';
	}

	if ( $id !== null ) {
		$assigned_class .= "$id ";
	} else {
		$assigned_class .= 'wf-container__flex-gap ';
	}

	$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';

	if ( 'horizontal' === $layout_orientation ) {
		$assigned_class     .= 'wf-container__flex_items-center ';
	} else {
		$assigned_class     .= 'wf-container__flex-column ';
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
