<?php

namespace WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center;


use WazFactor\WazFrame\DesignSystem\CSS\StyleBuilder;

/**
 * Primarily powers the 'default' layout type from
 * LayoutSupport.
 */
class Center
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
		// WordPress Global Settings
		$layout_width = wp_get_global_settings( array('layout') );
		$content_size = $layout_width['contentSize'] ?? '';
		$wide_size = $layout_width['wideSize'] ?? '';
		
		$selector = '.wf-center';
		
		return array(
			// May need to remove this first value.
			"$selector"                         => array(
				'--wf--style--content-size'     => esc_html__( $content_size ),
				'--wf--style--wide-size'        => esc_html( $wide_size ),
				'box-sizing'                    => 'content-box',
				//'margin-inline'                 => 'auto',
				//'max-inline-size'               => 'var(--wf--style--content-size)',
			),
			
			"$selector > *"                     => array(
				'margin-inline'                 => 'auto',
				'max-inline-size'               => 'var(--wf--style--content-size)',
			),
			
			".alignwide"            => array(
				'max-inline-size'               => 'var(--wf--style--wide-size)',
			),
			
			".alignfull"              => array(
				'max-inline-size'               => '100vw',
				'margin-inline-start'           => 'calc(50% - 50vw)'
			),
			
			"*"                                 => array(
				'max-inline-size'               => esc_html__( $content_size ),
			),
			"html,\nbody,\ndiv,\nheader,\nnav,\nmain,\nsection,\narticle,\nfooter"     => array(
				'max-inline-size'               => 'none'
			)
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