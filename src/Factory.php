<?php
/**
 * AOP类工厂
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace PG\AOP;

class Factory
{
    /**
     * 创建AOP包装器类
     *
     * @param $object
     * @param callable | null $beforeCallable
     * @param callable | null $afterCallable
     * @param callable | null $bothCallable
     *
     * @param \stdClass $object
     * @return Wrapper | \stdClass
     */
    public static function create($object, $beforeCallable = null, $afterCallable = null, $bothCallable = null)
    {
        $wrapperObject = new Wrapper($object);
        if (is_callable($beforeCallable)) {
            $wrapperObject->registerOnBefore($beforeCallable);
        }

        if (is_callable($afterCallable)) {
            $wrapperObject->registerOnAfter($afterCallable);
        }

        if (is_callable($bothCallable)) {
            $wrapperObject->registerOnBoth($bothCallable);
        }

        return $wrapperObject;
    }

    /**
     * 获取协程与非协程适配对象
     *
     * @param $object
     *
     * @param \stdClass $object
     * @return Wrapper | \stdClass
     */
    public static function createAdapterObject($object)
    {
        $wrapperObject = self::create($object, null, function ($method, $arguments, $result) {
            $data['method']    = $method;
            $data['arguments'] = $arguments;

            $appType = php_sapi_name();
            if ($appType == 'cli') {
                $processName = @cli_get_process_title();
                if (strpos($processName, 'msf') !== false) {
                    $appType = 'msf';
                }
            } else {
                $appType = 'web';
            }

            if ($appType != 'msf' && $result instanceof \Generator) {
                $data['result'] = $result->getReturn();
                return $data;
            }

            $data['result'] = $result;
            return $data;
        });

        return $wrapperObject;
    }
}
