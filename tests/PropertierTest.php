<?php

use Mockery as m;

class PropertierTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpProperties();
    }

    public function test_it_will_get_an_array_of_columns()
    {
        $cols = ['id', 'name', 'created_at', 'updated_at'];
        $this->assertEquals($cols, $this->company->fetchModelAttributes());
        $this->assertEquals($cols, $this->company->getModelAttributes());
    }

    public function test_get_magic_method_recognizes_property()
    {
        $model = m::mock($this->company);
        $model->shouldReceive('isProperty')->once();
        $model->name;
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

    public function test_it_if_attribute_is_table_column_is_not_property()
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