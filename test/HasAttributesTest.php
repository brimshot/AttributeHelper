<?php

include_once __DIR__ . "/../src/AttributeHelper.php";
include_once "doubles/MockClass.php";

use PHPUnit\Framework\TestCase;
use brimshot\attributehelper\test\doubles\attributes\DummyAttribute;

final class HasAttributesTest extends TestCase
{
    public function testHasAttribute()
    {
        $MockClass = new MockClass();
        $this->assertTrue($MockClass->hasAttribute(DummyAttribute::class));
    }

    public function testCallMethodsWithAttributes()
    {
        $MockClass = new MockClass();
        $methodArgs = ['val_a', 'val_b', 'val_c'];
        $this->assertEquals(['foo'=>'foo_ret', 'bar'=>'bar_ret: val_a', 'baz'=>'baz_ret: val_a, val_b'], $MockClass->callMethodsWithAttributes([DummyAttribute::class], $methodArgs));
        $this->assertEquals(['foo', 'bar', 'baz'], $MockClass->getMethodsCalled());
    }
}