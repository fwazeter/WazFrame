<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport;


/**
 * RenderSupportInterface provides a method for classes to render
 * css classes in BlockSupport API based on $block_content available.
 */
interface BlockSupportInterface
{
	/**
	 * Registers block attributes for block types that support
	 * the given attribute.
	 *
	 * @param \WP_Block_Type $block_type    The block type passed.
	 */
	public function register( \WP_Block_Type $block_type );
	
	/**
	 * Renders support config, such as layout from the rendered block.
	 *
	 * Takes in rendered block content & block object on page load.
	 *
	 * @param string $block_content         Rendered block content
	 * @param array $block                  Block Object.
	 *
	 * @return string                       The filtered content.
	 */
	public function render( string $block_content, array $block ): string;
}