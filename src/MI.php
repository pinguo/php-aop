<?php
/**
 * 方法注入，通过 trait 方式
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */
namespace PG\AOP;

trait MI
{
    /**
     * get Context
     * @return mixed
     */
    public function getContext()
    {
        return $this->context ?? null;
    }

    /**
     * @param array $properties
     */
    public function resetProperties(array $properties = [])
    {
        if (empty($properties)) {
            return;
        }
        foreach ($properties as $prop => $val) {
            $this->{$prop} = $val;
        }
    }
}
