<?php
/**
 * 方法注入，通过 trait 方式
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */
namespace PG\AOP;

use PG\MSF\Helpers\Context;
use PG\MSF\Controllers\Controller;
use PG\MSF\Base\Core;

trait MI
{
    /**
     * @var Context
     */
    public $context;

    /**
     * @var Controller|Core
     */
    public $parent;

    /**
     * get context
     * @return Context|NULL
     */
    public function getContext()
    {
        return $this->context ?? null;
    }

    /**
     * get parent
     * @return Controller|Core|null
     */
    public function getParent()
    {
        return $this->parent ?? null;
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
