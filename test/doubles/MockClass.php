<?php

include_once __DIR__ . "/../../src/HasAttributes.php";
include_once "attributes/DummyAttribute.php";
include_once "attributes/AnotherDummyAttribute.php";
include_once "attributes/ThirdDummyAttribute.php";
include_once "attributes/DummySmartAttribute.php";

use brimshot\attributehelper\test\doubles\attributes\DummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\AnotherDummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\ThirdDummyAttribute;
use brimshot\attributehelper\test\doubles\attributes\DummySmartAttribute;
use brimshot\attributehelper\HasAttributes;

#[
    DummyAttribute,
    AnotherDummyAttribute,
    DummySmartAttribute(true, false),
    DummySmartAttribute(false, true),
    FakeAttribute
]
final class MockClass 
{
    use HasAttributes;

    private $methodsCalled = [];

    #[DummyAttribute]
    private function shouldNotReport() : void
    {

    }

    #[DummyAttribute]
    public function foo() : string
    {
        $this->methodsCalled[] = 'foo';

        return 'foo_ret';
    }

    #[
        DummyAttribute,
        AnotherDummyAttribute
    ]
    public function bar(string $a) : string
    {
        $this->methodsCalled[] = 'bar';

        return "bar_ret: $a";
    }

    #[
        DummyAttribute,
        AnotherDummyAttribute,
        ThirdDummyAttribute
    ]
    public function baz(string $a, string $b) : string
    {
        $this->methodsCalled[] = 'baz';

        return "baz_ret: $a, $b";
    }

    public function noAttributeMethod() : void
    {

    }

    public function getMethodsCalled() : array
    {
        return $this->methodsCalled;
    }

}