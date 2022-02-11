<?php

declare( strict_types=1 );

namespace WazFactor\WazFrame\Vendor\League\Container\Exception;

use WazFactor\WazFrame\Vendor\Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
