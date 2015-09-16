<?php

use Devio\Propertier\Property;
use Devio\Propertier\ValueLinker;
use Illuminate\Support\Collection;
use Devio\Propertier\Exceptions\ValuesRelationAlreadyLoaded;

class ValueLinkerTest extends PHPUnit_Framework_TestCase
{

    public function test_it_will_handle_empty_collections_of_values()
    {
        list($linker, $property) = $this->instances();
        $linker->values(collect());
        $linker->properties($property);
        $result = $linker->link();

        $this->assertTrue($result->relationLoaded('values'));
    }

    public function test_it_wont_override_previous_values()
    {
        list($linker, $property) = $this->instances();
        $property->setRelation('values', collect());
        $linker->properties($property);

        $this->setExpectedException(ValuesRelationAlreadyLoaded::class);

        $linker->link();
    }

    public function test_it_can_find_the_values_linked_to_a_property()
    {
        list($linker, $property, $values) = $this->instances();
        $property->id = 11;
        $linker->values($values);
        $result = $linker->valuesOf($property);

        $this->assertEquals(collect([
            ['property_id' => 11, 'value' => 'foo'],
            ['property_id' => 11, 'value' => 'bar']
        ]), $result);
    }

    public function test_it_accepts_a_property_id_to_get_the_values_linked()
    {
        list($linker, , $values) = $this->instances();
        $property = 22;
        $linker->values($values);
        $result = $linker->valuesOf($property);

        $this->assertCount(1, $result);
        $this->assertEquals(['property_id' => 22, 'value' => 'baz'], $result->first());
    }

    public function test_it_will_accept_a_collection_of_properties()
    {
        list($linker, , $values, $properties) = $this->instances();
        $linker->properties($properties)->values($values);
        $result = $linker->link();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result->first()->values);
        $this->assertCount(1, $result->last()->values);
    }

    protected function instances()
    {
        return [
            new ValueLinker,
            new Property,
            $this->generateValues(),
            $this->generateProperties()
        ];
    }

    protected function generateValues()
    {
        return collect([
            ['property_id' => 11, 'value' => 'foo'],
            ['property_id' => 11, 'value' => 'bar'],
            ['property_id' => 22, 'value' => 'baz']
        ]);
    }

    protected function generateProperties()
    {
        $properties = collect([
            new Property,
            new Property
        ]);

        $i = 1;
        foreach ($properties as $property)
        {
            $property->id = (int) ($i . $i);
            $i++;
        }

        return $properties;
    }

}