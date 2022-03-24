<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport\Layout;

use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;
use WazFactor\WazFrame\Utilities\ArrayUtil;

class LayoutStyles
{
	use ArrayUtil;
	
	/**
	 * CSS Style Builder
	 *
	 * @var StyleBuilder
	 */
	protected StyleBuilder $style_builder;
	
	/**
	 * Constructor
	 *
	 * @param $style_builder
	 */
	public function __construct( $style_builder )
	{
		$this->style_builder = $style_builder;
	}
	
	/**
	 * Gets the appropriate CSS class corresponding to the provided layout.
	 *
	 * @param array       $block                    Block object.
	 * @param array       $layout                   Layout object. The one that is passed has already checked the existence
	 *                                              of default block layout.
	 * @param bool        $has_block_gap_support    Whether the theme has support for the block gap.
	 * @param string|null $block_gap_value          The block gap value to apply.
	 *
	 * @throws \Exception
	 * @return array CSS style.
	 */
	public function set( array $block, array $layout, bool $has_block_gap_support, string $block_gap_value = null ): array
	{
		$assigned_classes = '';
		$assigned_styles = null;
		// determine if layout is default or flex.
		$layout_type = isset($layout['type']) ? $layout['type'] : 'default';
		
		if ( 'default' === $layout_type ) {
			/**
			 * If a block has a 'layout' attribute of 'contentSize' or
			 * 'wideSize' set, then it is by default a custom entry.
			 *
			 * Because blocks that do not have this attribute inherit their
			 * settings from either their parent block, cannot set an
			 * explicit content & wide width.
			 *
			 * Blocks will inherit their sizing from the theme settings
			 * without adding the attribute directly to the block.
			 * Instead, they have an 'align' attribute set to wide or full.
			 *
			 * With the following isset & blank check alone, we can assign defaults.
			 */
			$content_size = isset( $layout['contentSize'] ) ? $layout['contentSize'] : '';
			$wide_size = isset( $layout['wideSize'] ) ? $layout['wideSize'] : '';
			
			/*if ( $content_size || $wide_size ) {
				$assigned_classes = $this->getDefaultClass( $has_block_gap_support );
			}*/
			
			// if the block has an entry here, we know it's a custom value.
			$custom_content_size    = $this->arrayGet( $block, array('attrs', 'layout', 'contentSize') );
			$custom_wide_size       = $this->arrayGet( $block, array('attrs', 'layout', 'wideSize') );
			
			// declare beginning of style here, so we don't duplicate values if custom block gap.
			$assigned_styles = '';
			
			if ( $content_size || $wide_size ) {
				
				
				
				
				$all_max_width_value = $custom_content_size ? $custom_content_size : $custom_wide_size;
				$wide_max_width_value = $custom_wide_size ? $custom_wide_size : $custom_content_size;
				
				// Make sure there is a single CSS rule, and all tags are stripped for security.
				// TODO: Use `safecss_filter_attr` instead - once https://core.trac.wordpress.org/ticket/46197 is patched.
				$all_max_width_value  = wp_strip_all_tags( explode( ';', $all_max_width_value )[0] );
				$wide_max_width_value = wp_strip_all_tags( explode( ';', $wide_max_width_value )[0] );
				
				
				if ( $custom_content_size ) {
					$assigned_styles .= "--wf--style--content-size: $all_max_width_value; ";
				}
				
				if ( $custom_wide_size ) {
					$assigned_styles .= "--wf--style--wide-size: $wide_max_width_value; ";
				}
				
				$assigned_classes = $this->getDefaultClass( $has_block_gap_support );
				if ( $block_gap_value ) {
					$assigned_styles .= "--wp--style--block-gap: $block_gap_value; ";
				}
			}
		} elseif ( 'flex' === $layout_type ) {
			$layout_orientation = isset( $layout['orientation'] ) ? $layout['orientation'] : 'horizontal';
			
			$justify_content_options = array(
				'left'   => 'flex-start',
				'right'  => 'flex-end',
				'center' => 'center',
			);
			
			$assigned_styles = '';
			
			if ( 'horizontal' === $layout_orientation ) {
				// Add space-between justify-content option.
				$justify_content_options += array( 'space-between' => 'space-between' );
				
				$assigned_classes = $this->getFlexClass( true );
				
				if ( ! empty( $layout['justifyContent'] ) &&
				     array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
					if ( $layout['justifyContent'] !== 'center' ) {
						$assigned_styles .= "--wf--style--justify-content: {$justify_content_options[ $layout['justifyContent'] ]}; ";
					}
				}
			} else {
				$assigned_classes = $this->getFlexClass();
				
				if ( ! empty( $layout['justifyContent'] ) &&
				     array_key_exists( $layout['justifyContent'], $justify_content_options ) ) {
					if ( $layout['justifyContent'] !== 'center' ) {
						$assigned_styles .= "--wf--style--align-items: {$justify_content_options[ $layout['justifyContent'] ]}; ";
					}
				}
			}
			
			//flex wrap options
			$flex_wrap_options = array( 'wrap', 'nowrap' );
			$has_flex_wrap   = $this->arrayGet( $block, array('attrs', 'layout', 'flexWrap') );
			if ( $has_flex_wrap ) {
				$flex_wrap         = ! empty( $layout['flexWrap'] ) &&
				                     in_array( $layout['flexWrap'], $flex_wrap_options, true ) ?
					$layout['flexWrap'] :
					'wrap';
				
				if ( $flex_wrap && $layout['flexWrap'] !== 'wrap' ) {
					$assigned_styles .= "--wf--style--flex-wrap: $flex_wrap; ";
				}
			}
			
			
			//block gap
			
			if ( $has_block_gap_support && $block_gap_value ) {
				$assigned_styles .= "--wp--style--block-gap: $block_gap_value; ";
			}
		}
		
		
		$styles = array(
			'class' => "$assigned_classes",
		);
		
		if ( $assigned_styles ) {
			$styles += array( 'style' => "$assigned_styles" );
		}
		
		return $styles;
	}
	
	/**
	 * Sets shared layout classes to each block.
	 *
	 * @param bool $has_block_gap_support       Whether or not the block supports blockGap.
	 * @return string
	 */
	public function getDefaultClass( bool $has_block_gap_support ): string
	{
		//set the default layout class
		$assigned_classes = 'wf-center ';
		
		if ( $has_block_gap_support ) {
			$assigned_classes .= 'wf-stack ';
		}
		return $assigned_classes;
	}
	
	/**
	 * Sets shared layout classes to each block.
	 *
	 * @param bool $is_horizontal       Whether layout orientation is horizontal.
	 * @return string
	 */
	public function getFlexClass( bool $is_horizontal = false ): string
	{
		$assigned_classes = '';
		//set the default layout class
		
		if ( $is_horizontal ) {
			$assigned_classes .= 'wf-cluster ';
		} else {
			$assigned_classes .= 'wf-center-flex ';
		}
		
		return $assigned_classes;
	}
}