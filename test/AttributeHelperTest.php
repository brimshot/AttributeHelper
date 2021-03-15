<?php

include_once __DIR__ . "/../src/AttributeHelper.php";
include_once "doubles/MockClass.php";

use PHPUnit\Framework\TestCase;
use brimshot\attributehelper\AttributeHelper;
use brimshot\attributehelper\test\doubles\attributes\DummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\AnotherDummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\ThirdDummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\DummySmartAttribute;
use brimshot\attributehelper\test\doubles\attributes\DummyParentAttribute;

#[DummyAttribute]
function dummyFunction() : void
{

}

final class AttributeHelperTest extends TestCase
{
    // ~ Get

    public function testGetAttributes()
    {
        $this->assertEquals([DummyAttribute::class, AnotherDummyAttribute::class, DummySmartAttribute::class, DummySmartAttribute::class, 'FakeAttribute'], AttributeHelper::getAttributes(MockClass::class), "getAttributes should return qualified class names for attributes that resolve to classes and strings for those that don't");

        $this->assertEquals([], AttributeHelper::getAttributes(null), 'getAttributeNames should return empty array on empty argument');
    }

    public function testGetAttributeInstance()
    {
        $this->assertEquals(new DummyAttribute(), AttributeHelper::getAttributeInstance(MockClass::class, DummyAttribute::class), 'getAttributeInstance should accept qualified class name as item argument');

        $this->assertEquals(new DummyAttribute(), AttributeHelper::getAttributeInstance(new MockClass(), DummyAttribute::class), 'getAttributeInstance should accept instantiated class as item argument');

        $this->assertEquals(null, AttributeHelper::getAttributeInstance('bad data', DummyAttribute::class), 'getAttributeInstance should return null on bad item');

        $this->assertEquals(new DummySmartAttribute(true, false), AttributeHelper::getAttributeInstance(MockClass::class, DummySmartAttribute::class), 'getAttributeInstance should default to first of repeated attributes');

        $this->assertEquals(new DummySmartAttribute(false, true), AttributeHelper::getAttributeInstance(MockClass::class, DummySmartAttribute::class, 1), 'getAttributeInstance should correctly return desired index from repeated attributes');

        $this->assertNull(AttributeHelper::getAttributeInstance(MockClass::class, ThirdDummyAttribute::class), 'getAttributeInstance should return null on no match');

        $this->assertNull(AttributeHelper::getAttributeInstance(MockClass::class, ''), 'getAttributeInstance should return null on bad attribute argument');

        $this->assertNull(AttributeHelper::getAttributeInstance(MockClass::class, 'FakeAttribute'), 'getAttributeInstance should return null when attribute does not resolve to a class');

    }

    public function testGetAttributeInstances()
    {
        $expected = [new DummyAttribute(), new AnotherDummyAttribute(), new DummySmartAttribute(true, false), new DummySmartAttribute(false, true)];
        $this->assertEquals($expected, AttributeHelper::getAttributeInstances(MockClass::class));

        $this->assertEquals([], AttributeHelper::getAttributeInstances('bad data'), 'getAttributeInstances should return empty array on bad item argument');
    }

    public function testHasAttributeCallback()
    {
        $this->assertTrue(AttributeHelper::hasAttributeCallback(MockClass::class, DummySmartAttribute::class, fn($attr)=>$attr->aTrue()));

        $this->assertFalse(AttributeHelper::hasAttributeCallback(MockClass::class, DummySmartAttribute::class, fn($attr)=>$attr->bTrue()));
    }


    // ~ Has

    public function testHasAttribute()
    {
        $this->assertTrue(AttributeHelper::hasAttribute(MockClass::class, 'FakeAttribute'), 'hasAttribute should accept attributes that do not resolve to classes');

        $this->assertTrue(AttributeHelper::hasAttribute(MockClass::class, DummyAttribute::class), 'hasAttribute should return true when item has attribute class');

        $this->assertTrue(AttributeHelper::hasAttribute(MockClass::class, DummyParentAttribute::class), 'hasAttribute should match child attributes');

        $this->assertFalse(AttributeHelper::hasAttribute(MockClass::class, DummyParentAttribute::class, false), 'hasAttribute should not match child attributes when match children is false');

        $this->assertTrue(AttributeHelper::hasAttribute('dummyFunction', DummyAttribute::class), 'hasAttribute should work on functions');

        $this->assertTrue(AttributeHelper::hasAttribute([MockClass::class, 'foo'], DummyAttribute::class), 'hasAttribute should work on class methods');

        $this->assertTrue(AttributeHelper::hasAttribute([MockClass::class, 'baz'], DummyAttribute::class), 'hasAttribute should work on class constants');

        $this->assertFalse(AttributeHelper::hasAttribute([MockClass::class, 'fizz'], DummyAttribute::class), 'hasAttribute should return false on missing class member');

        $this->assertFalse(AttributeHelper::hasAttribute(['foo', 'bar', 'fizz', 'buzz'], DummyAttribute::class), 'hasAttribute should return false when item argument is an array with more than 2 entries');

        $this->assertFalse(AttributeHelper::hasAttribute(MockClass::class, ThirdDummyAttribute::class),  'hasAttribute should return false when class does not have attribute');

        $this->assertFalse(AttributeHelper::hasAttribute('', DummyAttribute::class), 'hasAttribute should return false when item is empty');

        $this->assertFalse(AttributeHelper::hasAttribute(0, ''), 'hasAttribute should return false when item is 0');

        $this->assertFalse(AttributeHelper::hasAttribute('\invalid\class\path', ''), 'hasAttribute should return false when item is non-existent class');

        $this->assertFalse(AttributeHelper::hasAttribute(MockClass::class, ''), 'hasAttribute should return false when attribute argument is empty');
    }

    public function testhasOneOfTheseAttributes()
    {
        $this->assertTrue(AttributeHelper::hasOneOfTheseAttributes(new MockClass(), [DummyAttribute::class]), "hasOneOfTheseAttributes should detect attribute presence when using instantiated objects");

        $this->assertTrue(AttributeHelper::hasOneOfTheseAttributes(new MockClass(), [DummyParentAttribute::class]), "hasOneOfTheseAttributes default behavior should match attribute children when parent class provided");

        $this->assertFalse(AttributeHelper::hasOneOfTheseAttributes(new MockClass(), [DummyParentAttribute::class], false), "hasOneOfTheseAttributes should NOT match attribute children when match children flag is false");

        $this->assertTrue(AttributeHelper::hasOneOfTheseAttributes(new MockClass(), ['FakeAttribute']), "hasOneOfTheseAttributes should detect presence of attributes that do not resolve to classes");

        $this->assertTrue(AttributeHelper::hasOneOfTheseAttributes(MockClass::class, [DummyAttribute::class]), "hasOneOfTheseAttributes should detect attribute presence when using qualified class names");

        $this->assertTrue(AttributeHelper::hasOneOfTheseAttributes(MockClass::class, [DummyAttribute::class, 'invalid name string', AnotherDummyAttribute::class]), "hasOneOfTheseAttributes should skip invalid attribute class names");

        $this->assertFalse(AttributeHelper::hasOneOfTheseAttributes(MockClass::class, [ThirdDummyAttribute::class]), "hasOneOfTheseAttributes should return false on no match");

        $this->assertFalse(AttributeHelper::hasOneOfTheseAttributes('bad data', [DummyAttribute::class]), "hasOneOfTheseAttributes should return false when class argument is invalid");

        $this->assertFalse(AttributeHelper::hasOneOfTheseAttributes(MockClass::class, ['bad data']), "hasOneOfTheseAttributes should return false when attribute argument is invalid");

        $this->assertFalse(AttributeHelper::hasOneOfTheseAttributes(MockClass::class, []), "hasOneOfTheseAttributes should return false when attribute argument is empty");
    }

    public function testHasAllOfTheseAttributes()
    {
        $this->assertTrue(AttributeHelper::hasAllOfTheseAttributes(new MockClass(), [DummyAttribute::class, AnotherDummyAttribute::class]), "hasAllOfTheseAttributes should return true when class has all attributes in list");

        $this->assertTrue(AttributeHelper::hasAllOfTheseAttributes(new MockClass(), [DummyParentAttribute::class, AnotherDummyAttribute::class]), "hasAllOfTheseAttributes should match attribute children by default");

        $this->assertFalse(AttributeHelper::hasAllOfTheseAttributes(new MockClass(), [DummyParentAttribute::class, AnotherDummyAttribute::class], false), "hasAllOfTheseAttributes should not match attribute children when flag is false");

        $this->assertFalse(AttributeHelper::hasAllOfTheseAttributes(new MockClass(), [DummyAttribute::class, ThirdDummyAttribute::class]), "hasAllOfTheseAttributes should return false when argument list contains attributes class does not have");

        $this->assertFalse(AttributeHelper::hasAllOfTheseAttributes(null, [DummyAttribute::class, AnotherDummyAttribute::class]), "hasAllOfTheseAttributes should return false when class argument is invalid");

        $this->assertTrue(AttributeHelper::hasAllOfTheseAttributes(new MockClass(), [AnotherDummyAttribute::class, DummyAttribute::class]), "hasAllOfTheseAttributes should be order agnostic");
    }

    public function testDoesNotHaveTheseAttributes()
    {
        $this->assertFalse(AttributeHelper::doesNotHaveTheseAttributes(new MockClass(), [DummyAttribute::class]), "classDoesNotHaveAttributes should return false when target attribute present");

        $this->assertFalse(AttributeHelper::doesNotHaveTheseAttributes(new MockClass(), [DummyParentAttribute::class]), "classDoesNotHaveAttributes should match child attributes by default");

        $this->assertTrue(AttributeHelper::doesNotHaveTheseAttributes(new MockClass(), [DummyParentAttribute::class], false), "classDoesNotHaveAttributes should not match child attributes when flag is false");

        $this->assertFalse(AttributeHelper::doesNotHaveTheseAttributes(new MockClass(), [DummyAttribute::class, AnotherDummyAttribute::class]), "classDoesNotHaveAttributes should return false when target attributes present");

        $this->assertTrue(AttributeHelper::doesNotHaveTheseAttributes(new MockClass(), [ThirdDummyAttribute::class]), "hasOneOfTheseAttributes should return true when target attribute not present");

        $this->assertTrue(AttributeHelper::doesNotHaveTheseAttributes(null, [ThirdDummyAttribute::class]), "hasOneOfTheseAttributes should return true when item argument not valid");
    }

    public function testHasExactlyTheseAttributes()
    {
        $this->assertTrue(AttributeHelper::hasExactlyTheseAttributes(new MockClass(), [DummyAttribute::class, AnotherDummyAttribute::class, DummySmartAttribute::class, 'FakeAttribute']), "classHasExactlyTheseAttributes should match class attribute list and collapse repeated names");

        $this->assertTrue(AttributeHelper::hasExactlyTheseAttributes(new MockClass(), ['FakeAttribute', DummySmartAttribute::class, AnotherDummyAttribute::class, DummyAttribute::class]), "classHasExactlyTheseAttributes should be order agnostic");

        $this->assertFalse(AttributeHelper::hasExactlyTheseAttributes(new MockClass(), [DummyAttribute::class]), "classHasExactlyTheseAttributes should return false when class has more attributes than search list");

        $this->assertFalse(AttributeHelper::hasExactlyTheseAttributes(new MockClass(), ['foo', 'bar', 'baz']), "classHasExactlyTheseAttributes should return false when comparison list longer than class attribute list");

        $this->assertFalse(AttributeHelper::hasExactlyTheseAttributes(null, [DummyAttribute::class, AnotherDummyAttribute::class]), "classHasExactlyTheseAttributes should return false on bad item");
    }

    public function testClassHasMethodsWithAttributes()
    {
        $this->assertTrue(AttributeHelper::classHasMethodsWithAttributes(new MockClass(), [DummyAttribute::class]), "classHasMethodsWithAttributes should return true when class method has desired attribute");

        $this->assertTrue(AttributeHelper::classHasMethodsWithAttributes(new MockClass(), [DummyAttribute::class, AnotherDummyAttribute::class]),  "classHasMethodsWithAttributes should return true when class method has desired list of attributes");

        $this->assertFalse(AttributeHelper::classHasMethodsWithAttributes(new MockClass(), ['falke attribute']), "classHasMethodsWithAttributes should return false when provided attribute does not exist");

        $this->assertFalse(AttributeHelper::classHasMethodsWithAttributes(new MockClass(), []), "classHasMethodsWithAttributes should return false on empty comparison list");
    }

    public function testGetClassMethodsWithAttributes()
    {
        $this->assertEquals(['foo', 'bar', 'baz'], AttributeHelper::getClassMethodsWithAttributes(MockClass::class, [DummyAttribute::class]));

        $this->assertEquals(['foo', 'bar', 'baz'], AttributeHelper::getClassMethodsWithAttributes(MockClass::class, [DummyParentAttribute::class]), 'getClassMethodsWithAttributes should match attribute children by default');

        $this->assertEquals([], AttributeHelper::getClassMethodsWithAttributes(MockClass::class, [DummyParentAttribute::class], false), 'getClassMethodsWithAttributes should not match attribute children when flag is false');

        $this->assertEquals(['bar', 'baz'], AttributeHelper::getClassMethodsWithAttributes(MockClass::class, [AnotherDummyAttribute::class]));

        $this->assertEquals([], AttributeHelper::getClassMethodsWithAttributes(MockClass::class, ['bad data']));
    }


    // ~ Call

    public function testCallClassMethodsWithAttributes()
    {
        $methodArgs = ['val_a', 'val_b', 'val_c'];

        $MockClass = new MockClass();
        $this->assertEquals(['foo'=>'foo_ret', 'bar'=>'bar_ret: val_a', 'baz'=>'baz_ret: val_a, val_b'], AttributeHelper::callClassMethodsWithAttributes($MockClass, [DummyAttribute::class], $methodArgs));
        $this->assertEquals(['foo', 'bar', 'baz'], $MockClass->getMethodsCalled());

        $MockClass = new MockClass();
        $this->assertEquals(['foo'=>'foo_ret', 'bar'=>'bar_ret: val_a', 'baz'=>'baz_ret: val_a, val_b'], AttributeHelper::callClassMethodsWithAttributes($MockClass, [DummyParentAttribute::class], $methodArgs), 'callClassMethodsWithAttributes should match child attributes by default');
        $this->assertEquals(['foo', 'bar', 'baz'], $MockClass->getMethodsCalled());
    }

}