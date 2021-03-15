<?php

namespace brimshot\attributehelper;

include_once "AttributeHelper.php";

/**
 * Trait HasAttributes
 * @package brimshot\AttributeHelper
 * @author Chris Brim <chris.brim@protonmail.com>
 */
trait HasAttributes {

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

    final public function getMethodsWithAttributes(array $attributes, bool $matchAttributeChildren = true) : array
    {
        return AttributeHelper::getClassMethodsWithAttributes($this, $attributes);
    }


    // ~ Has

    final public function hasAttribute(string $attribute, bool $matchAttributeChildren = true) : bool
    {
        return AttributeHelper::hasAttribute($this, $attribute);
    }

    final public function hasAttributeCallback(string $attribute, callable $callback) : bool
    {
        return AttributeHelper::hasAttributeCallback($this, $attribute, $callback);
    }

    final public function hasOneOfTheseAttributes(array $attributes, bool $matchAttributeChildren = true) : bool
    {
        return AttributeHelper::hasOneOfTheseAttributes($this, $attributes, $matchAttributeChildren);
    }

    final public function hasAllOfTheseAttributes(array $attributes, bool $matchAttributeChildren = true) : bool
    {
        return AttributeHelper::hasAllOfTheseAttributes($this, $attributes, $matchAttributeChildren);
    }

    final public function hasExactlyTheseAttributes(array $attributes) : bool
    {
        return AttributeHelper::hasExactlyTheseAttributes($this, $attributes);
    }

    final public function doesNotHaveTheseAttributes(array $attributes, bool $matchAttributeChildren = true) : bool
    {
        return AttributeHelper::doesNotHaveTheseAttributes($this, $attributes, $matchAttributeChildren);
    }

    final public function hasMethodsWithAttributes(array $attributes, bool $matchAttributeChildren = true) : bool
    {
        return AttributeHelper::classHasMethodsWithAttributes($this, $attributes, $matchAttributeChildren);
    }



    // ~ Call

    final public function callMethodsWithAttributes(array $attributes, array $methodArgs = array(), bool $matchAttributeChildren = true) : array
    {
        return AttributeHelper::callClassMethodsWithAttributes($this, $attributes, $methodArgs, $matchAttributeChildren);
    }

}