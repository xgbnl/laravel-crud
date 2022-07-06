<?php

namespace Xgbnl\Business\Services;

use Illuminate\Database\Eloquent\Model;
use Xgbnl\Business\Contacts\Observer;
use Xgbnl\Business\Utils\Fail;

abstract class Observable
{
    protected ?Model $modelClass = null;

    protected ?Observer $observer = null;

    protected  ?string $trigger = null;

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
     * 为服务注册观察者
     * @param string $observer
     * @return void
     */
    protected function observer(string $observer): void
    {
        if (!is_subclass_of($observer, Observer::class)) {
            Fail::throwFailException('模型( ' . $observer . ' )错误，必须实现接口:[ ' . Observer::class . ' ]');
        }

        $this->observer = app($observer);
    }

    /**
     * 设置触发方法
     * @param string $method
     * @return void
     */
    final protected function triggerMethod(string $method): void
    {
        $this->trigger = $method;
    }
}