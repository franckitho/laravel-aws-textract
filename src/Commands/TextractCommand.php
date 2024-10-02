<?php

namespace Franckitho\Textract\Commands;

use Illuminate\Console\Command;

class TextractCommand extends Command
{
    public $signature = 'laravel-aws-textract';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
