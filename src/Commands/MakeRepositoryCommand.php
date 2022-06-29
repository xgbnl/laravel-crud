<?php

namespace Xgbnl\Business\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository';

    protected $type = 'Repository';

    protected function getStub(): string
    {
        return __DIR__ . '/Stubs/'.$this->type.'.stub';
    }

    protected function getDefaultNameSpace($rootNamespace): string
    {
        return $rootNamespace . '\\' . 'Repositories';
    }
}
