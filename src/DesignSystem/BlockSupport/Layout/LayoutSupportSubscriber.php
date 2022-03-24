<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport\Layout;

use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center\Center;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Center\IntrinsicCenter;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Stack\Stack;
use WazFactor\WazFrame\DesignSystem\CSS\Primitives\Cluster\Cluster;
use WazFactor\WazFrame\Internal\EventManagement\AbstractEventSubscriber;
use WazFactor\WazFrame\Internal\EventManagement\EventManager;
use WazFactor\WazFrame\Internal\EventManagement\RemoveSubscriberInterface;

class LayoutSupportSubscriber extends AbstractEventSubscriber implements RemoveSubscriberInterface
{
	/**
	 * LayoutSupport object.
	 *
	 * @var LayoutSupport
	 */
	protected LayoutSupport $layout_support;
	
	/**
	 * LayoutSupport object.
	 *
	 * @var Center
	 */
	protected Center $center_style;
	
	/**
	 * LayoutSupport object.
	 *
	 * @var IntrinsicCenter
	 */
	protected IntrinsicCenter $intrinsic_center_style;
	
	/**
	 * LayoutSupport object.
	 *
	 * @var Cluster
	 */
	protected Cluster $cluster_style;
	
	/**
	 * LayoutSupport object.
	 *
	 * @var Stack
	 */
	protected Stack $stack_style;
	
	/**
	 * Constructor
	 *
	 * @param LayoutSupport   $layout_support
	 * @param Center          $center_style
	 * @param IntrinsicCenter $intrinsic_center_style
	 * @param Cluster         $cluster_style
	 * @param Stack           $stack_style
	 * @param EventManager      $event_manager
	 */
	public function __construct(    LayoutSupport $layout_support,
									Center $center_style,
									IntrinsicCenter $intrinsic_center_style,
									Cluster $cluster_style,
									Stack $stack_style,
									// Loading event manager here prevents it the pre-initialization error.
									EventManager $event_manager )
	{
		$this->layout_support = $layout_support;
		$this->center_style = $center_style;
		$this->intrinsic_center_style = $intrinsic_center_style;
		$this->cluster_style = $cluster_style;
		$this->stack_style = $stack_style;
		$this->event_manager = $event_manager;
		
		$this->merge( $this->styles_to_merge() );
	}
	
	public static function getUnSubscribedEvents(): array
	{
		return array(
			'render_block'      => array(
				'gutenberg_render_layout_support_flag',
				'wp_render_layout_support_flag'
			)
		);
	}
	
	public static function getSubscribedEvents(): array
	{
		return array(
			'render_block'      =>  array( 'render', 10, 2 ),
		);
	}
	
	public function render( $block_content, $block ): string
	{
		return $this->layout_support->render( $block_content, $block );
	}
	
	
	public function styles_to_merge(): array
	{
		$array = [];
		
		$array[] = $this->center_style->set();
		$array[] = $this->intrinsic_center_style->set();
		$array[] = $this->cluster_style->set();
		$array[] = $this->stack_style->set();
		
		return $array;
	}
	
	/**
	 * This function takes care of adding inline styles
	 * in the proper place, depending on the theme in use.
	 *
	 * For block themes, it's loaded in the head.
	 * For classic ones, it's loaded in the body
	 * because the wp_head action  happens before
	 * the render_block.
	 *
	 * @link https://core.trac.wordpress.org/ticket/53494.
	 *
	 * @param array $styles    String containing the CSS styles to be added.
	 */
	public function merge( array $styles ){
		
		$action_hook_name = 'wp_footer';
		
		$stylesheet = '';
		
		foreach ( $styles as $style ) {
			$stylesheet .= "$style \n";
		}
		
		if ( wp_is_block_theme() ) {
			$action_hook_name = 'wp_head';
		}
		
		$this->event_manager->add( $action_hook_name,
			static function () use ( $stylesheet ) {
				echo "<style id='wazframe-styles'>$stylesheet</style>\n";
			}
		);
	}
}