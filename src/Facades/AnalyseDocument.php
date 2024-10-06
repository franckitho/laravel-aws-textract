<?php

namespace Franckitho\Textract\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Franckitho\Textract\Textract
 */
class AnalyseDocument extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Franckitho\Textract\AnalyseDocument::class;
    }
}
