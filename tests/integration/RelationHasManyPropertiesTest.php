<?php

use Devio\Propertier\Value;
use Devio\Propertier\Property;
use Devio\Propertier\Values\StringValue;

class RelationHasManyPropertiesTest extends TestCase
{
    /** @test */
    public function relation_eager_loads_properties_and_values()
    {
        $this->createModelsAndProperties();

        list($first, $last) = Company::with('properties')->get();

        $this->assertTrue($first->relationLoaded('values'));
        $this->assertEquals('Madrid', $first->getValue('city'));
        $this->assertEquals('white', $first->getValue('colors')->first());
        $this->assertEquals('black', $first->getValue('colors')->last());

        $this->assertEquals('Paris', $last->getValue('city'));
        $this->assertEquals('blue', $last->getValue('colors')->first());
        $this->assertEquals('orange', $last->getValue('colors')->last());
    }

    /** @test */
    public function values_wont_be_loaded_if_no_properties_found()
    {
        factory(Company::class)->create();

        $eager = Company::with('properties')->find(1);
        $dynamic = Company::find(1);
        $dynamic->properties;

        $this->assertFalse($eager->relationLoaded('values'));
        $this->assertFalse($dynamic->relationLoaded('values'));

    }
    
    /** @test */
    public function properties_can_be_dynamically_loaded()
    {
//        Company::setModelColumns(['name']);

        $this->createModelsAndProperties();

//        $result = Company::find(1);
//        $result->properties;

//        $this->assertTrue($result->relationLoaded('values'));
//        $this->assertTrue($result->relationLoaded('properties'));
//
//        $this->assertEquals('Madrid', $result->getValue('city'));
//        $this->assertCount(2, $result->getValueObject('colors'));
    }

    /** @test */
    public function property_is_filled_with_casted_values()
    {
        $this->createModelsAndProperties();

        $result = Company::with('properties')->find(1);

        $this->assertInstanceOf(StringValue::class, $result->getValueObject('city'));
        $this->assertInstanceOf(StringValue::class, $result->getValueObject('colors')->first());
        $this->assertInstanceOf(StringValue::class, $result->getValueObject('colors')->last());
    }

    protected function createModelsAndProperties()
    {
        $models = factory(Company::class, 2)->create();

        $city = factory(Property::class)->create(['name' => 'city']);
        // Value for city on first model
        factory(Value::class)->make(['value' => 'Madrid'])
            ->property()->associate($city)->entity()->associate($models->first())
            ->save();
        // Value for city on last model
        factory(Value::class)->make(['value' => 'Paris'])
            ->property()->associate($city)->entity()->associate($models->last())
            ->save();

        $color = factory(Property::class)->create(['name' => 'colors', 'multivalue' => 'true']);
        // Values for first model
        factory(Value::class)->make(['value' => 'white'])
            ->property()->associate($color)->entity()->associate($models->first())
            ->save();
        factory(Value::class)->make(['value' => 'black'])
            ->property()->associate($color)->entity()->associate($models->first())
            ->save();
        // Values for last model
        factory(Value::class)->make(['value' => 'blue'])
            ->property()->associate($color)->entity()->associate($models->last())
            ->save();
        factory(Value::class)->make(['value' => 'orange'])
            ->property()->associate($color)->entity()->associate($models->last())
            ->save();
    }

}