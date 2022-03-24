<?php

namespace WazFactor\WazFrame\DesignSystem\CSS\Primitives\Cluster;


use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;

/**
 * Primarily powers the 'flex' layout type from
 * LayoutSupport.
 *
 */
class Cluster
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
		$selector = '.wf-cluster';
		
		return array(
			"$selector"                             => array(
				'--wf--style--flex-direction'       => 'row',
				'--wf--style--flex-wrap'            => 'wrap',
				'--wf--style--align-items'          => 'center',
				'--wf--style--justify-content'      => 'flex-start',
				'display'                           => 'flex',
				'margin-inline'                     => 'auto',
				'flex-direction'                    => 'var(--wf--style--flex-direction)',
				'flex-wrap'                         => 'var(--wf--style--flex-wrap)',
				'align-items'                       => 'var(--wf--style--align-items)',
				'justify-content'                   => 'var(--wf--style--justify-content)',
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