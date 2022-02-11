<?php

namespace WazFactor\WazFrame\CSS;

interface StyleFactoryInterface
{
	/**
	 * @return object
	 */
	public function createStyle(): object;
}