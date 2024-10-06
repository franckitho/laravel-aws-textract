<?php

declare(strict_types=1);

namespace Franckitho\Exceptions;

use Exception;

class FileOrBucketNotFoundException extends Exception
{
    protected $message = 'Method file() or s3() must be called before calling the analyse() method.';
}
