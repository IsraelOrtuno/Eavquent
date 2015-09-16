<?php

class IsPropertyTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->setUpProperties();
    }

    public function test_it_recognizes_an_attribute_is_property()
    {
        $this->assertTrue($this->company->isProperty('foo'));
        $this->assertTrue($this->company->isProperty('bar'));
    }

    public function test_it_recognizes_an_attribute_is_not_property()
    {
        $this->assertFalse($this->company->isProperty('oof'));
        $this->assertFalse($this->company->isProperty('rab'));
    }

    public function it_if_attribute_is_table_column_is_not_property()
    {
        $this->assertFalse($this->company->isProperty('name'));
        $this->assertFalse($this->company->isProperty('created_at'));
    }

    public function test_if_attribute_is_relation_is_not_property()
    {
        $this->assertFalse($this->company->isProperty('values'));
        $this->assertFalse($this->company->isProperty('properties'));
    }
}