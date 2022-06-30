<?php

namespace Xgbnl\Business\Services;

use Xgbnl\Business\Contacts\Observer;
use Illuminate\Database\Eloquent\Model;

abstract class Observable
{
    protected ?Model $model = null;

    protected ?Observer $observer = null;

    protected ?string $trigger = null;

    final public function __construct()
    {
        $this->registerObserver();
    }

    abstract protected function registerObserver(): void;

    protected function notify(): void
    {
        if (!is_null($this->observer)) {
            $this->observer->{$this->trigger}($this->model);
        }
    }
}