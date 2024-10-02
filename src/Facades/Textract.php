<?php

namespace Franckitho\Textract\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Franckitho\Textract\Textract
 */
class Textract extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Franckitho\Textract\Textract::class;
    }
}
