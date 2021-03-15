<?php

namespace brimshot\attributehelper;

/**
 * Class AttributeHelper
 * @package brimshot\AttributeHelper
 * @author Chris Brim <chris.brim@protonmail.com>
 */
final class AttributeHelper {

    private function __construct() {}

    // ~ Get

    /**
     * Return the names of an items attributes. If the attributes resolve to classes, these will be qualified classnames
     *
     * @param mixed $item
     * @return array
     */
    public static function getAttributes(mixed $item) : array
    {
        return array_map(fn($a) => $a->getName(), self::reflectorFactory($item)->getAttributes());
    }

    /**
     * Return an instance of the given attribute from an item or null if the item does not have the attribute requested or attribue does not resolve to a class. Optional index parameter can be provided when dealing with repeated attributes to choose which should be returned.
     *
     * @param mixed $item
     * @param string $attribute
     * @return object|null
     */
    public static function getAttributeInstance(mixed $item, string $attribute, int $index = 0) : ?object
    {
        foreach((self::reflectorFactory($item))->getAttributes() as $a) {
            if($a->getName() === $attribute) {
                if(! $index)
                    return self::safeNewInstance($a);
                $index--;
            }
        }

        return null;
    }

    /**
     * Return an array with instances of attributes from the provided item that resolve to classes
     *
     * @param mixed $item
     * @return array
     */
    public static function getAttributeInstances(mixed $item) : array
    {
        return array_filter(array_map(fn($a) => self::safeNewInstance($a), self::reflectorFactory($item)->getAttributes()), fn($a)=> !is_null($a));
    }

    /**
     * Return an array of instances of attributes from an item filtered by the provided callback
     *
     * @param mixed $item
     * @param callable $callback
     * @return array
     */
    public static function getAttributeInstancesCallback(mixed $item, callable $callback) : array
    {
        return array_filter(self::getAttributeInstances($item), $callback);
    }

    /**
     * Returns an array of method names from the provided class that have the given attributes
     *
     * @param object|string $objectOrClass
     * @param array $attributesList
     * @return array
     */
    public static function getClassMethodsWithAttributes(object|string $objectOrClass, array $attributesList, $matchAttributeChildren = true) : array
    {
        if(! is_object($objectOrClass)) {
            if(! class_exists($objectOrClass))
                return [];
        }

        $methods = [];
        foreach(get_class_methods($objectOrClass) as $methodName)
        {
            if(self::hasAllOfTheseAttributes([$objectOrClass, $methodName], $attributesList, $matchAttributeChildren))
                $methods[] = $methodName;
        }

        return $methods;
    }



    // ~ Has

    /**
     * Returns true / false whether an item has a given attribute
     *
     * @param mixed $item
     * @param string $attribute
     * @return bool
     */
    public static function hasAttribute(mixed $item, string $attribute, $matchAttributeChildren = true) : bool
    {
        if(class_exists($attribute))
            return (!! (self::reflectorFactory($item))->getAttributes($attribute, ($matchAttributeChildren? \ReflectionAttribute::IS_INSTANCEOF:0)));

        return (!! (self::reflectorFactory($item))->getAttributes($attribute));
    }

    /**
     * Returns true / false whether an item has a given attribute and that the provided callback function returns when passed the matched attribute
     *
     * @param mixed $item
     * @param object|string $attribute
     * @param callable $callback
     * @return bool
     */
    public static function hasAttributeCallback(mixed $item, object|string $attribute, callable $callback, bool $matchAttributeChildren = true) : bool
    {
        if(self::hasAttribute($item, $attribute, $matchAttributeChildren))
            return (!! $callback(self::getAttributeInstance($item, $attribute)));

        return false;
    }

    /**
     * Returns true / false whether an item has one of the attributes in the provided list
     *
     * @param mixed $item
     * @param array $attributesList
     * @return bool
     */
    public static function hasOneOfTheseAttributes(mixed $item, array $attributesList, bool $matchAttributeChildren = true) : bool
    {
        return (! empty(array_filter($attributesList, function ($a) use ($item, $matchAttributeChildren) { return self::hasAttribute($item, $a, $matchAttributeChildren); })));
    }

    /**
     * Returns true / false whether an item has all of the provided attributes
     *
     * @param mixed $item
     * @param array $attributesList
     * @return bool
     */
    public static function hasAllOfTheseAttributes(mixed $item, array $attributesList, bool $matchAttributeChildren = true) : bool
    {
        $matchingAttributes = array_filter($attributesList, function ($a) use ($item, $matchAttributeChildren) { return self::hasAttribute($item, $a, $matchAttributeChildren); });
        return count($matchingAttributes) && empty(array_diff($attributesList, $matchingAttributes));
    }

    /**
     * Returns true / false whether an item has an exact list of attributes. If the item has additional attributes beyond the list in question, this method returns false.
     *
     * @param mixed $item
     * @param array $attributesList
     * @return bool
     */
    public static function hasExactlyTheseAttributes(mixed $item, array $attributesList) : bool
    {
        $itemAttributes = array_unique(self::getAttributes($item));
        $attributesList = array_unique($attributesList);
        return (count($itemAttributes) == count($attributesList)) && empty(array_diff($attributesList, $itemAttributes));
    }

    /**
     * Returns true when the given item does not have any of the attributes in the provided list.
     *
     * @param mixed $item
     * @param array $attributesList
     * @return bool
     */
    public static function doesNotHaveTheseAttributes(mixed $item, array $attributesList, bool $matchAttributeChildren = true) : bool
    {
        return empty(array_filter($attributesList, function ($a) use ($item, $matchAttributeChildren) { return self::hasAttribute($item, $a, $matchAttributeChildren); }));
    }

    /**
     * Returns true when the given class contains methods which have the attributes in the provided list.
     *
     * @param object|string $objectOrClass
     * @param array $attributesList
     * @return bool
     */
    public static function classHasMethodsWithAttributes(object|string $objectOrClass, array $attributesList, bool $matchAttributeChildren = true) : bool
    {
        if(! is_object($objectOrClass)) {
            if(! class_exists($objectOrClass))
              return false;
        }

        foreach(get_class_methods($objectOrClass) as $methodName)
        {
            if(self::hasAllOfTheseAttributes([$objectOrClass, $methodName], $attributesList, $matchAttributeChildren))
                return true;
        }

        return false;
    }



    // ~ Call

    /**
     * Sequentially calls all methods on a given object that have the attributes in the provided list. Returns an array, indexed by method name, with the result of each method called.
     *
     * @param object|string $objectOrClass
     * @param array $attributesList
     * @param array $methodArguments
     * @return array
     */
    public static function callClassMethodsWithAttributes(object|string $objectOrClass, array $attributesList, array $methodArguments = array(), bool $matchAttributeChildren = true)
    {
        $methods = self::getClassMethodsWithAttributes($objectOrClass, $attributesList, $matchAttributeChildren);

        $res = [];
        foreach($methods as $method) {
            $res[$method] = $objectOrClass->$method(...$methodArguments);
        }

        return $res;
    }


    /*--------
    Private
    */

    /**
     * @param mixed $item
     * @return \Reflector|null
     */
    private static function reflectorFactory(mixed $item) : \Reflector
    {
        try {

            if (is_object($item))
                return new \ReflectionClass($item);

            if(is_string($item)) {
                if (class_exists($item))
                    return new \ReflectionClass($item);

                if (function_exists($item))
                    return new \ReflectionFunction($item);
            }

            if(is_array($item) && (count($item) == 2)) {

                if(method_exists(...$item))
                    return new \ReflectionMethod(...$item);

                return new \ReflectionClassConstant(...$item);
            }

        } catch (\ReflectionException $e) {
            // Fall through to null object return
        }

        return new \ReflectionClass(new class {});
    }

    /**
     * @param \ReflectionAttribute $a
     * @return object|null
     */
    private static function safeNewInstance(\ReflectionAttribute $a) : ?object
    {
        return class_exists($a->getName())? $a->newInstance():null;
    }
}