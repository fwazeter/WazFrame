<?php

namespace WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center;


use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;

/**
 * Primarily powers the 'flex' layout type from
 * LayoutSupport.
 *
 */
class IntrinsicCenter
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
		$selector = '.wf-center-flex';
		
		return array(
			"$selector"                             => array(
				'--wf--style--flex-direction'       => 'column',
				'--wf--style--flex-wrap'            => 'wrap',
				'--wf--style--align-items'          => 'center',
				'box-sizing'                        => 'content-box',
				'display'                           => 'flex',
				'margin-inline'                     => 'auto',
				'flex-direction'                    => 'var(--wf--style--flex-direction)',
				'flex-wrap'                         => 'var(--wf--style--flex-wrap)',
				'align-items'                       => 'var(--wf--style--align-items)',
				// May need to change placement of this
				'gap'                               => 'var(--wp--style--block-gap, 1.5rem)',
			),
			
			"$selector > *"                         => array(
				// Likely a better way to handle this, but this is default for now.
				'margin'                            => '0',
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