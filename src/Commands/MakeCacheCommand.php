<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeCacheCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Cache';

    protected $type = 'Cache';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/'.$this->type.'.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Caches';
    }
}
