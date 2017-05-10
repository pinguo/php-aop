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
     *
     * @param \stdClass $object
     * @return Wrapper | \stdClass
     */
    public static function getAdapterObject($object)
    {
        $wrapperObject = new Wrapper($object);
        $wrapperObject->registerOnAfter(function ($method, $arguments, $result) {
            $data['method'] = $method;
            $data['arguments'] = $arguments;

            if (CommonHelper::getAppType() != 'msf' && $result instanceof \Generator) {
                $data['result'] = $result->getReturn();
                return $data;
            }

            $data['result'] = $result;
            return $data;
        });

        return $wrapperObject;
    }
}
