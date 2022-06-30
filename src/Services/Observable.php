<?php

namespace Xgbnl\Business\Services;

use Xgbnl\Business\Contacts\Observer;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Business\Fail;

abstract class Observable
{
    protected ?Model $model = null;

    protected ?Observer $observer = null;

    protected ?string $trigger = null;

    final public function __construct()
    {
        $this->configure();
    }

    abstract protected function configure(): void;

    protected function notify(): void
    {
        if (!is_null($this->observer)) {
            $this->observer->{$this->trigger}($this->model);
        }
    }

    protected function registerObserver(string $observer): void
    {
        if (!is_subclass_of($observer, Observer::class)) {
            Fail::throwFailException('模型( ' . $observer . ' )错误，必须实现接口:[ ' . Observer::class . ' ]');
        }

        $this->observer = app($observer);
    }
}