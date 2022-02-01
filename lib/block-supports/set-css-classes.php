<?php
/**
 * @param array $layout The Layout Object.
 * @param boolean $has_block_gap_support Whether the theme supports blockGap.
 *
 * @return string   CSS class to assign.
 */
function set_layout_class( array $layout, bool $has_block_gap_support = false ): string
{
	$layout_type = isset( $layout['type'] ) ? $layout['type'] : 'default';

	$assigned_class = 'class="';
	if ( 'default' === $layout_type ) {
		$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
		$wide_size    = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';

		if ( $content_size || $wide_size ) {
			$assigned_class .= 'wf-container__default ';
		}

		$assigned_class .= 'wf-container__inherit ';

		if ( $has_block_gap_support ) {
			$assigned_class .= 'wf-v_stack ';
		}
	}

	return $assigned_class;
}