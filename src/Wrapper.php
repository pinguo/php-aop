<?php
/**
 * AOP 包装器
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace PG\AOP;

class Wrapper
{
    /**
     * @var object 实例对象
     */
    private $instance;

    /**
     * @var array 属性
     */
    private $attributes = [];

    /**
     * @var array 前置方法
     */
    private $onBeforeFunc = [];

    /**
     * @var array 后置方法
     */
    private $onAfterFunc = [];

    /**
     * 包装器构造方法代理对象
     *
     * Wrapper constructor.
     *
     * @param object $instance
     * @param bool   $isClone
     */
    public function __construct($instance, $isClone = false)
    {
        $isClone && ($instance = clone $instance);
        $instance->__wrapper = $this;
        $this->instance = $instance;
    }

    /**
     * 动态get属性
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * 动态设置属性
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * 方法调用
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
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

    /**
     * 注入前置方法
     *
     * @param callable $callback
     */
    public function registerOnBefore(callable $callback)
    {
        $this->onBeforeFunc[] = $callback;
    }

    /**
     * 注入后置方法
     *
     * @param callable $callback
     */
    public function registerOnAfter(callable $callback)
    {
        $this->onAfterFunc[] = $callback;
    }

    /**
     * 注入前后置方法
     *
     * @param callable $callback
     */
    public function registerOnBoth(callable $callback)
    {
        $this->onBeforeFunc[] = $callback;
        $this->onAfterFunc[]  = $callback;
    }

    /**
     * 销毁属性
     */
    public function destroy()
    {
        $this->instance     = null;
        $this->attributes   = [];
        $this->onBeforeFunc = [];
        $this->onAfterFunc  = [];
    }
}
