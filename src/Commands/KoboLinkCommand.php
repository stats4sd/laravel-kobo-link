<?php

namespace Stats4sd\KoboLink\Commands;

use Illuminate\Console\Command;

class KoboLinkCommand extends Command
{
    public $signature = 'laravel-kobo-link';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
