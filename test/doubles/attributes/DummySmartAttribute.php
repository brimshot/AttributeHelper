<?php

namespace brimshot\attributehelper\test\doubles\attributes;

include_once "DummyParentAttribute.php";

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS)]
class DummySmartAttribute extends DummyParentAttribute {

    public function __construct(private bool $a, private bool $b) {}

    public function aTrue() { return $this->a; }

    public function bTrue() { return $this->b; }

}