<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeObserverCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:custom-observer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Custom Observer';

    protected $type = 'Observer';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/'.$this->type.'.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Services\\Observers';
    }
}
