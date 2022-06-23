<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport\Layout;

use WazFactor\WazFrame\DesignSystem\BlockSupport\BlockSupportInterface;
use WazFactor\WazFrame\DesignSystem\BlockSupport\BlockSupport;
use WazFactor\WazFrame\Utilities\ArrayUtil;

/**
 * Renders Layout Support Flag
 *
 * NOTE: if we use a custom implementation of wp_get_global_settings(), it breaks implementation on certain
 * blocks. Maybe.
 *
 */
class LayoutSupport implements BlockSupportInterface
{
	use ArrayUtil;
	
	/**
	 * Block Support Utilities
	 *
	 * @var LayoutStyles
	 */
	protected LayoutStyles $layout_styles;
	
	/**
	 * Block Support Utilities
	 *
	 * @var BlockSupport
	 */
	protected BlockSupport $block_manager;
	
	/**
	 * WP_Block_Supports object
	 *
	 * @var \WP_Block_Supports
	 */
	protected \WP_Block_Supports $wp_block_supports;
	
	/**
	 * Constructor
	 *
	 *
	 * @param LayoutStyles $layout_styles               LayoutStyle
	 * @param BlockSupport       $block_manager        Block Support Object
	 */
	public function __construct( LayoutStyles $layout_styles, BlockSupport $block_manager ) {
		$this->layout_styles = $layout_styles;
		$this->block_manager = $block_manager;
		
		\WP_Block_Supports::get_instance()->register(
			'layout',
			array(
				'register_attribute'    => array($this, 'register')
			)
		);
	}
	
	/**
	 * Registers the layout block attribute for block types that support it.
	 *
	 * @param \WP_Block_Type $block_type    Block Type.
	 */
	public function register( \WP_Block_Type $block_type ) {
		$supports = block_has_support( $block_type, array( '__experimentalLayout' ), false );
		
		if ( $supports ) {
			if ( ! $block_type->attributes ) {
				$block_type->attributes = array();
			}
			
			if ( ! array_key_exists( 'layout', $block_type->attributes ) ) {
				$block_type->attributes['layout'] = array(
					'type'  => 'object',
				);
			}
		}
	}
	
	/**
	 * Renders the layout config to the block wrapper.
	 *
	 * @param  string $block_content Rendered block content.
	 * @param  array  $block         Block object.
	 * @return string                Filtered block content.
	 */
	public function render( string $block_content, array $block ): string
	{
		// Retrieves the name of the block being parsed.
		$block_type                         = \WP_Block_Type_Registry::get_instance()
		                                                             ->get_registered( $block['blockName'] );
		
		// Checks if the block has support for given attr. Because blockGap settings come from support, this also
		// is a way of checking blockGap.
		$block_supports_layout              = block_has_support( $block_type,
																		array('__experimentalLayout' ),
																		false );
		
		// If layout support is not enabled, return the block content.
		if ( ! $block_supports_layout ) {
			return $block_content;
		}
		
		// Retrieve the merged global default values for layout & blockGap
		$global_layout_settings             = wp_get_global_settings( array( 'layout' ) );
		$global_block_gap_settings          = wp_get_global_settings( array( 'spacing', 'blockGap' ) );
		
		// check if global blockGap is set.
		$supports_block_gap_flag            =   isset( $global_block_gap_settings ) ?
												null !== $global_block_gap_settings :
												false;
		
		// retrieves the value of the block's default layout setting
		$block_default_layout               = $this->arrayGet( $block_type->supports,
																array( '__experimentalLayout', 'default' ),
																array() );
		
		// either pulls the block's custom layout properties, or sets default.
		$block_layout_value                 =   isset( $block['attrs']['layout'] ) ?
												$block['attrs']['layout'] :
												$block_default_layout;
		
		// if both the block's layout value is 'inherit' and it's set on the block, set it to the global setting.
		if ( isset( $block_layout_value['inherit'] ) && $block_layout_value['inherit'] ) {
			if ( ! $global_layout_settings ) {
				return $block_content;
			}
			$block_layout_value = $global_layout_settings;
		}
		
		// retrieve the blocks blockGap setting.
		$block_gap_value = $this->arrayGet( $block, array( 'attrs', 'style', 'spacing', 'blockGap' ) );
		
		// Skip if gap value contains unsupported characters.
		// Regex for CSS value borrowed from `safecss_filter_attr`, and used here
		// because we only want to match against the value, not the CSS attribute.
		if ( is_array( $block_gap_value ) ) {
			foreach( $block_gap_value as $key => $value ) {
				$block_gap_value[ $key ] = preg_match( '%[\\\(&=}]|/\*%', $value ) ? null : $value;
			}
		} else {
			$block_gap_value = preg_match( '%[\\\(&=}]|/\*%', $block_gap_value ) ? null : $block_gap_value;
		}
		
		// passes the block's layout value, whether theme supports blockGap & the blockGap value.
		// the block's content is passed to set css styles & classes.
		$style = $this->layout_styles->set( $block,
											$block_layout_value,
											$supports_block_gap_flag,
											$block_gap_value );
		
		$content = $this->add($style, $block_content);
		
		
		return $content;
	}
	
	public function add( $style_array, $block_content ) {
		$content = '';
		
		$classes = $this->arrayGet($style_array, array('class'));
		$check_for_styles = $this->hasArrayKey( $style_array, 'style');
		
		// We are using arrays in preg_replace to avoid either duplication of blocks (weirdly)
		// or cases where before if there was a custom style, it would not load the class.
		// when using arrays with preg_replace, you have to match the keys via ksort.
		if ( $check_for_styles ){
			$styles = $this->arrayGet($style_array, array('style'));
			
			$patterns = array();
			$patterns[0] = '/' . preg_quote( 'class="', '/' ) . '/';
			$patterns[1] = '/' . preg_quote( 'style="', '/' ) . '/';
			
			$replacements = array();
			$replacements[0] = 'class="' . $classes . ' ';
			$replacements[1] = 'style="' . $styles . ' ';
			
			ksort($patterns);
			ksort($replacements);
			
			$content = preg_replace( $patterns, $replacements, $block_content, 1 );
			
		} else {
			$content = preg_replace(
				'/' . preg_quote( 'class="', '/' ) . '/',
				'class="' . $classes . ' ',
				$block_content,
				1
			);
		}
		
		return $content;
	}
}