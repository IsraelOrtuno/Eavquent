<?php

use Mockery as m;
use Devio\Propertier\Property;
use Devio\Propertier\ValueLinker;
use Devio\Propertier\Exceptions\ValuesRelationAlreadyLoaded;

class ValueLinkerTest extends PHPUnit_Framework_TestCase
{

    public function test_it_wont_override_previous_values()
    {
        $linker = new ValueLinker();
        $property = new Property;
        $property->setRelation('values', collect());
        $linker->properties($property);

        $this->setExpectedException(ValuesRelationAlreadyLoaded::class);

        $linker->link();
    }

    public function test_it_can_find_the_values_linked_to_a_property()
    {
        $property = new Property();
        $property->id = 11;

        $linker = new ValueLinker();
        $linker->values($this->generateValues());
        $result = $linker->valuesOf($property);

        $this->assertEquals(collect([
            ['property_id' => 11, 'value' => 'foo'],
            ['property_id' => 11, 'value' => 'bar']
        ]), $result);
    }

    public function test_it_accepts_a_property_id_to_get_the_values_linked()
    {
        $property = 22;

        $linker = new ValueLinker();
        $linker->values($this->generateValues());
        $result = $linker->valuesOf($property);


        $this->assertCount(1, $result);
        $this->assertEquals(['property_id' => 22, 'value' => 'baz'], $result->first());
    }

    protected function generateValues()
    {
        return collect([
            ['property_id' => 11, 'value' => 'foo'],
            ['property_id' => 11, 'value' => 'bar'],
            ['property_id' => 22, 'value' => 'baz']
        ]);
    }

}