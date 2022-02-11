<?php

declare( strict_types=1 );

namespace WazFactor\WazFrame\Vendor\League\Container\Exception;

use WazFactor\WazFrame\Vendor\Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
