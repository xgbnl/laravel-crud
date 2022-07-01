<?php

namespace Xgbnl\Business\Traits;

use ReflectionClass;
use ReflectionException;
use Xgbnl\Business\Utils\Fail;

trait ReflectionParse
{
    private ?string $refClass = null;

    /**
     * @throws ReflectionException
     */
    public function __call($method, $parameters)
    {
        $attributes = $this->parseAttributes();

        if (empty($attributes)) {
            Fail::throwFailException('调用方法(' . $method . ')不可用，请指定依赖类:#[Business(Foo:class)]');
        }

        $objects = $this->parseAttributes();

        foreach ($objects as $object) {

            if (!class_exists($object)) {
                Fail::throwFailException('所引入的类:(' . $object . ')不存在,请检查类');
            }

            if (method_exists($object, $method)) {
                $this->refClass = $object;
                break;
            }
        }

        $reflection = new ReflectionClass($this->refClass);

        if (!$reflection->hasMethod($method)) {
            return parent::__call($method, $parameters);
        }

        return $this->prepareMethod($reflection, $method, $parameters);
    }

    /**
     * @throws ReflectionException
     */
    private function prepareMethod(ReflectionClass $reflectionClass, string $method, array $parameters)
    {
        $magicMethod = $reflectionClass->getMethod($method);

        $params = [];

        foreach ($magicMethod->getParameters() as $key => $parameter) {

            if (isset($parameters[$parameter->getName()])) {

                $params[] = $parameters[$parameter->getName()];

            } elseif ($parameter->isDefaultValueAvailable() && !isset($parameters[$key])) {

                $params[] = $parameter->getDefaultValue();

            } elseif (isset($parameters[$key])) {

                $params[] = $parameters[$key];
            }
        }

        if ($magicMethod->isStatic()) {
            return $magicMethod->invoke(null, ...$params);
        }

        return $magicMethod->invoke($reflectionClass->newInstance(), $params);
    }

    protected function parseAttributes(): array
    {
        $class = new \ReflectionClass(self::class);

        $refAttributes = $class->getAttributes();

        if (empty($refAttributes)) {
            return [];
        }

        $arguments = array_map(fn($attr) => $attr->getArguments(), $refAttributes);

        return $this->flatten($arguments);
    }

    public function flatten(array $array, array $result = []): array
    {
        foreach ($array as $item) {
            is_array($item)
                ? ($result = $this->flatten($item, $result))
                : (!empty($item) ? $result[] = $item : $result = $item);
        }

        return $result;
    }
}