<?php

namespace Xgbnl\Business\Services;

use HttpRuntimeException;
use Illuminate\Database\Eloquent\Model;
use Xgbnl\Business\Contacts\Observer;
use Xgbnl\Business\Utils\Fail;

abstract class Observable
{
    protected ?Model $modelClass = null;

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
            $this->observer->{$this->trigger}($this->modelClass);
        }
    }

    /**
     * @throws HttpRuntimeException
     */
    protected function observer(string $observer): void
    {
        if (!is_subclass_of($observer, Observer::class)) {
            Fail::throwFailException('模型( ' . $observer . ' )错误，必须实现接口:[ ' . Observer::class . ' ]');
        }

        $this->observer = app($observer);
    }
}