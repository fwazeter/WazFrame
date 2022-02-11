<?php

namespace WazFactor\WazFrame\CSS;

class Style
{
	/**
	 * The selector(s) that will be
	 * assigned to our CSS class.
	 *
	 * @var string
	 */
	private string $selector;
	
	/**
	 * The key-value pair matching
	 * CSS class property: value.
	 *
	 * @var array
	 */
	private array $properties;
	
	/**
	 * Constructor function.
	 *
	 * @param string $selector
	 * @param array  $properties
	 */
	public function __construct( string $selector, array $properties )
	{
		$this->selector = $selector;
		$this->properties = $properties;
	}
	
	public function getSelector()
	{
	}
	
	
	public function getProperties()
	{
	}
	
	public function set()
	{
	
	}
}