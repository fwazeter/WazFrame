<?php

namespace WazFactor\WazFrame\DesignSystem\CSS\Primitives\Stack;

use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;

/**
 * Used in BlockSupport under LayoutSupport.
 * blockGap property powers this.
 *
 */
class Stack
{
	/**
	 * The CSS StyleBuilder
	 *
	 * @var StyleBuilder Style Builder
	 */
	protected StyleBuilder $style_builder;
	
	/**
	 * Constructor
	 *
	 * @param StyleBuilder $style_builder
	 */
	public function __construct( StyleBuilder $style_builder )
	{
		$this->style_builder = $style_builder;
	}
	
	
	public function get(): array
	{
		
		$selector = '.wf-stack';
		
		return array(
			/*"$selector"                 => array(
				'display'               => 'flex',
				'flex-direction'        => 'column',
				'justify-content'       => 'flex-start'
			),*/
			
			"$selector > *"             => array(
				'margin-block'          => '0',
			),
			
			"$selector > * + *"         => array(
				'margin-block-start'    => 'var(--wp--style--block-gap, 1.5rem)'
			),
		);
	}
	
	public function set(): string
	{
		$center = $this->get();
		
		$styles = '';
		foreach ($center as $selector => $properties) {
			$styles .= $this->style_builder->buildDeclarationBlock( $selector, $properties );
		}
		
		return $styles;
	}
}