<?php

namespace brimshot\attributehelper;

include_once "AttributeHelper.php";

/**
 * Trait HasAttributes
 * @package brimshot\AttributeHelper
 * @author Chris Brim <chris.brim@protonmail.com>
 */
trait HasAttributes {

    /*
    private $__reflectorCache = null;

    final private function reflectorFactory($item) : \ReflectionClass
    {
        if($item == $this) {
            if (is_null($this->__reflectorCache))
                $this->__reflectorCache = new \ReflectionClass($this);

            return $this->__reflectorCache;
        }

    }
    */

    // ~ Get

    final public function getAttributes() : array
    {
        return AttributeHelper::getAttributes($this);
    }

    final public function getAttributeInstance(string $attribute) : object
    {
        return AttributeHelper::getAttributeInstance($this, $attribute);
    }

    final public function getAttributeInstances() : array
    {
        return AttributeHelper::getAttributeInstances($this);
    }

    final public function getAttributeInstancesCallback(callable $callback) : array
    {
        return AttributeHelper::getAttributeInstancesCallback($this, $callback);
    }

    final public function getMethodsWithAttributes(array $attributes) : array
    {
        return AttributeHelper::getClassMethodsWithAttributes($this, $attributes);
    }


    // ~ Has

    final public function hasAttribute(string $attribute) : bool
    {
        return AttributeHelper::hasAttribute($this, $attribute);
    }

    final public function hasAttributeCallback(string $attribute, callable $callback) : bool
    {
        return AttributeHelper::hasAttributeCallback($this, $attribute, $callback);
    }

    final public function hasOneOfTheseAttributes(array $attributes) : bool
    {
        return AttributeHelper::hasOneOfTheseAttributes($this, $attributes);
    }

    final public function hasAllOfTheseAttributes(array $attributes) : bool
    {
        return AttributeHelper::hasAllOfTheseAttributes($this, $attributes);
    }

    final public function hasExactlyTheseAttributes(array $attributes) : bool
    {
        return AttributeHelper::hasExactlyTheseAttributes($this, $attributes);
    }

    final public function doesNotHaveTheseAttributes(array $attributes) : bool
    {
        return AttributeHelper::doesNotHaveTheseAttributes($this, $attributes);
    }

    final public function hasMethodsWithAttributes(array $attributes) : bool
    {
        return AttributeHelper::classHasMethodsWithAttributes($this, $attributes);
    }



    // ~ Call

    final public function callMethodsWithAttributes(array $attributes, array $methodArgs = array()) : array
    {
        return AttributeHelper::callClassMethodsWithAttributes($this, $attributes, $methodArgs);
    }

}