<?php
/**
 * @desc: AOP类工厂
 * @author: leandre <niulingyun@camera360.com>
 * @date: 2017/3/28
 * @copyright All rights reserved.
 */

namespace PG\AOP;

use PG\Helper\CommonHelper;

class Factory
{
    /**
     * 获取协程与非协程适配对象
     * @param $object
     * @param callable | null $beforeCallable
     * @param callable | null $afterCallable
     * @param callable | null $bothCallable
     *
     * @param \stdClass $object
     * @return Wrapper | \stdClass
     */
    public static function getAdapterObject($object, $beforeCallable = null, $afterCallable = null, $bothCallable = null)
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
}
