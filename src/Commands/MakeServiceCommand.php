<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeServiceCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Service';

    protected $type = 'Service';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/'.$this->type.'.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Services';
    }
}
