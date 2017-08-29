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
use PG\MSF\Base\Pool;
use PG\MSF\Base\Core;
use PG\MSF\Marco;

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
     * @var array 反射类属性
     */
    public static $__reflections;

    /**
     * @var int 资源销毁级别
     */
    public $__DSLevel;

    /**
     * get context
     * @return Context|NULL
     */
    public function getContext()
    {
        return $this->context ?? null;
    }

    /**
     * 通过对象池生成对象
     *
     * @param array $args
     * @return \stdClass|mixed
     */
    public function getObject(...$args)
    {
        return $this->context->getObjectPool()->get(...$args);
    }

    /**
     * get parent
     * @return Controller|Core|null
     */
    public function getParent()
    {
        return $this->parent ?? null;
    }

    public function resetProperties()
    {
        // 销毁PUBLIC
        if ($this->__DSLevel & Marco::DS_PUBLIC) {
            foreach (MI::$__reflections[static::class][Marco::DS_PUBLIC] as $prop => $val) {
                $this->{$prop} = $val;
            }
        }

        // 销毁PROTECTED
        if ($this->__DSLevel & Marco::DS_PROTECTED) {
            foreach (MI::$__reflections[static::class][Marco::DS_PROTECTED] as $prop => $val) {
                $this->{$prop} = $val;
            }
        }

        // 销毁PRIVATE
        if ($this->__DSLevel & Marco::DS_PRIVATE) {
            foreach (MI::$__reflections[static::class][Marco::DS_PRIVATE] as $prop => $val) {
                $this->{$prop} = $val;
            }
        }
    }

    /**
     * 支持自动销毁public修饰的成员属性
     *
     * @param $className
     */
    public static function __supportAutoDestroy($className)
    {
        if (empty(MI::$__reflections[$className])) {
            $reflection  = new \ReflectionClass($className);
            $default     = $reflection->getDefaultProperties();
            $public      = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            $private     = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
            $protect     = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
            $static      = $reflection->getProperties(\ReflectionProperty::IS_STATIC);
            $autoDestroy = [];
            foreach ($public as $val) {
                $autoDestroy[Marco::DS_PUBLIC][$val->getName()] = $default[$val->getName()];
            }

            foreach ($private as $val) {
                $autoDestroy[Marco::DS_PRIVATE][$val->getName()] = $default[$val->getName()];
            }

            foreach ($protect as $val) {
                $autoDestroy[Marco::DS_PROTECTED][$val->getName()] = $default[$val->getName()];
            }

            foreach ($static as $val) {
                if (isset($autoDestroy[Marco::DS_PUBLIC])) {
                    unset($autoDestroy[Marco::DS_PUBLIC][$val->getName()]);
                }

                if (isset($autoDestroy[Marco::DS_PROTECTED])) {
                    unset($autoDestroy[Marco::DS_PROTECTED][$val->getName()]);
                }

                if (isset($autoDestroy[Marco::DS_PRIVATE])) {
                    unset($autoDestroy[Marco::DS_PRIVATE][$val->getName()]);
                }
            }

            unset($autoDestroy[Marco::DS_PUBLIC]['__useCount']);
            unset($autoDestroy[Marco::DS_PUBLIC]['__genTime']);
            unset($autoDestroy[Marco::DS_PUBLIC]['__coreName']);
            unset($autoDestroy[Marco::DS_PUBLIC]['__DSLevel']);
            unset($autoDestroy[Marco::DS_PUBLIC]['__reflections']);
            MI::$__reflections[$className] = $autoDestroy;
            unset($reflection);
            unset($default);
            unset($public);
            unset($private);
            unset($protect);
            unset($static);
        }
    }
}
