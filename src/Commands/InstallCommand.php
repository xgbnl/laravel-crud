<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install business helper';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            "--provider" => "Xgbnl\Business\Providers\BusinessServiceProvider"
        ]);
    }
}
