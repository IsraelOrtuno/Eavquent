<?php

use Mockery as m;
use Devio\Propertier\Factory;
use Devio\Propertier\Manager;
use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_sets_the_value_of_an_entity()
    {
        $partner = m::mock(PartnerStub::class);
        $manager = m::mock(Manager::class);

        $manager->shouldReceive('getFields')->andReturn($this->getFields());

        $partner->shouldReceive('relationLoaded')->once()->andReturn(false);
        $partner->shouldReceive('getKeyName')->once()->andReturn('id');
        $partner->shouldReceive('getMorphClass')->once();
        $partner->shouldReceive('setRelation')->once()->with('city', 'foo');

        $factory = new BootedFactoryStub($partner, $manager);
        $factory->set('city', 'foo');
    }

    /** @test */
    public function it_boots_existing_fields_as_relations()
    {
        $partner = m::mock(PartnerStub::class);
        $manager = m::mock(Manager::class);
        $manager->shouldReceive('getFields')->andReturn($this->getFields());

        $partner->shouldReceive('getMorphClass')->once();
        $partner->shouldReceive('morphOne')->twice();
        $partner->shouldReceive('morphMany')->once();
        $partner->shouldReceive('setFieldRelation')->times(3);

        new Factory($partner, $manager);
    }

    protected function getFields()
    {
        return collect([
            'city' => new \Devio\Propertier\Field(['name' => 'city']),
            'address' => new \Devio\Propertier\Field(['name' => 'address']),
            'colors' => new \Devio\Propertier\Field(['name' => 'colors', 'multivalue' => true])
        ]);
    }

}

class PartnerStub extends Model
{
    use Propertier;
}

class BootedFactoryStub extends Factory
{
    protected function bootPartnerRelations()
    {
        // do nothing
    }

    public function fetchModelColumns()
    {
        return ['name'];
    }
}