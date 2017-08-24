<?php
/**
 * @desc: AOP 包装器
 * @author: leandre <niulingyun@camera360.com>
 * @date: 2017/3/28
 * @copyright All rights reserved.
 */

namespace PG\AOP;

class Wrapper
{
    private $instance;
    private $attributes = [];

    private $onBeforeFunc = [];
    private $onAfterFunc = [];

    public function __construct($instance, $isClone = false)
    {
        $isClone && ($instance = clone $instance);
        $instance->__wrapper = $this;
        $this->instance = $instance;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __call($method, $arguments)
    {
        $data['method']    = $method;
        $data['arguments'] = $arguments;
        unset($data['result']);

        foreach ($this->onBeforeFunc as $func) {
            $data = $func(...array_values($data));
        }

        //支持提前返回结果 不需要继续调用
        if (isset($data['result'])) {
            return $data['result'];
        }

        $data['result'] = $this->instance->{$data['method']}(...$data['arguments']);

        foreach ($this->onAfterFunc as $func) {
            $data = $func(...array_values($data));
        }

        return $data['result'];
    }

    public function registerOnBefore(callable $callback)
    {
        $this->onBeforeFunc[] = $callback;
    }

    public function registerOnAfter(callable $callback)
    {
        $this->onAfterFunc[] = $callback;
    }

    public function registerOnBoth(callable $callback)
    {
        $this->onBeforeFunc[] = $callback;
        $this->onAfterFunc[]  = $callback;
    }

    public function destroy()
    {
        $this->instance     = null;
        $this->attributes   = [];
        $this->onBeforeFunc = [];
        $this->onAfterFunc  = [];
    }
}
