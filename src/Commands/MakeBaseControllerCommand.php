<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeBaseControllerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:base-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new BaseController';

    protected $type = 'BaseController';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/' . $this->type . '.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Http\\Controllers';
    }
}
