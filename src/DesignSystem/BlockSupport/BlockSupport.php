<?php

namespace WazFactor\WazFrame\DesignSystem\BlockSupport;

use WazFactor\WazFrame\Utilities\ArrayUtil;

/**
 * Interacts with the WP_Blocks_Supports API in WordPress.
 */
class BlockSupport
{
	use ArrayUtil;
	
	
	/**
	 * Checks whether the block contains the given support
	 * flag in it's attributes.
	 *
	 * Replacement for WordPress block_has_support.
	 *
	 * @param \WP_Block_Type $block_type    Block type to check for support.
	 * @param array          $feature       Name of the feature to check support for.
	 * @param mixed          $default       Fallback value for feature support. Defaults to false.
	 *
	 * @throws \Exception       $feature is not an array.
	 * @return bool         Whether the feature is supported.
	 */
	public function has( \WP_Block_Type $block_type, array $feature, $default = false ): bool
	{
		$block_support = $default;
		if ( $block_type && property_exists( $block_type, 'supports' ) ) {
			$block_support = $this->arrayGet( $block_type->supports, $feature, $default );
		}
		
		return true === $block_support || is_array( $block_support );
	}
	
	/**
	 * Gets the styles resulting of merging core, theme and user data
	 * from WordPress blocks & themes. Replaces wp_get_global_settings().
	 *
	 * @param string    $global_type   The type of data to retrieve, either style or setting.
	 * @param array     $path           Path to the specific style or setting to retrieve, if empty returns all.
	 * @param array     $context    {
	 *     Metadata to know where to retrieve the $path from. Optional.
	 *
	 *     @type string $block_name Which block to retrieve the style or setting from.
	 *                              If empty, it'll return the style or setting for the global context.
	 *     @type string $origin     Which origin to take data from.
	 *                              Valid values are 'all' (core, theme, and user) or 'base' (core and theme).
	 *                              If empty or unknown, 'all' is used.
	 * }
	 *
	 * @return array|bool          The style or setting to retrieve.
	 */
	public function get( string $global_type, array $path = [], array $context = [] )
	{
		if ( $global_type === 'setting' ) {
			$result = wp_get_global_settings( $path, $context );
		} elseif( $global_type === 'style' ) {
			$result = wp_get_global_styles( $path, $context );
		}
		
		return $result;
	}
}