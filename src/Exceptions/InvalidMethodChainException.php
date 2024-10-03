<?php

declare(strict_types=1);

namespace Franckitho\Exceptions;

use Exception;

class InvalidMethodChainException extends Exception
{
    protected $message = 'The file and s3 methods cannot be chained together in the same instance.';
}
