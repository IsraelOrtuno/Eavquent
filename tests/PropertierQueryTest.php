<?php

use Mockery as m;
use Devio\Propertier\Property;
use Devio\Propertier\Propertier;
use Illuminate\Database\Connection;
use Devio\Propertier\PropertierQuery;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class PropertierQueryTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();

        EntityStub::setModelColumns([]);
    }

    /** @test */
    public function it_should_get_a_property()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('properties')
            ->andReturn(new Collection(['foo' => 'bar']));

        $this->assertEquals('bar', $query->getProperty('foo'));
        $this->assertNull($query->getProperty('bar'));
    }

    /** @test */
    public function it_should_get_a_collection_of_values_keyed_by_property_name()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $city = m::mock(Property::class);
        $city->shouldReceive('get')->once()->andReturn('Madrid');
        $color = m::mock(Property::class);
        $color->shouldReceive('get')->once()->andReturn('blue');

        $values = new Collection(compact('city', 'color'));

        $entity->shouldReceive('getRelationValue')->with('properties')
            ->andReturn($values);

        $this->assertEquals(['city' => 'Madrid', 'color' => 'blue'], $query->getValues()->toArray());
    }

    /** @test */
    public function it_should_not_set_values_on_unexisting_properties()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('properties')->once()->andReturn(new Collection());
        $this->setExpectedException(RuntimeException::class);

        $query->setValue('foo', 'bar');
    }

    /** @test */
    public function it_should_retrieve_model_columns()
    {
        $entity = m::mock(EntityStub::class);
        $query = new PropertierQueryColumnFetchingStub($entity);

        $query->fetchModelColumns();
    }

    /** @test */
    public function it_should_check_model_columns()
    {
        list($query,) = $this->getQueryAndEntity();

        $this->assertEquals(['name'], $query->getModelColumns());

        EntityStub::setModelColumns('name', 'city');

        $this->assertEquals(['name', 'city'], $query->getModelColumns());

        $this->assertTrue($query->isModelColumn('name'));
        $this->assertTrue($query->isModelColumn('city'));
        $this->assertFalse($query->isModelColumn('color'));
    }

    /** @test */
    public function it_should_throw_exception_when_property_not_found()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('properties')
            ->andReturn(new Collection());

        $exceptions = 0;

        try {
            $query->getValue('color');
        } catch (RuntimeException $e) {
            $exceptions++;
        }

        try {
            $query->getValueObject('color');
        } catch (RuntimeException $e) {
            $exceptions++;
        }

        $this->assertEquals(2, $exceptions);
    }

    /** @test */
    public function it_should_get_the_value_of_a_property()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $property = m::mock(Property::class);
        $property->shouldReceive('get')->once()->andReturn('foo');

        $entity->shouldReceive('getRelationValue')->with('properties')
            ->andReturn(new Collection(['city' => $property]));

        $this->assertEquals('foo', $query->getValue('city'));
    }

    /** @test */
    public function it_should_recognize_if_property_exists()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('city')->andReturn(false);
        $entity->shouldReceive('getRelationValue')->with('color')->andReturn(false);
        $entity->shouldReceive('getKeyName')->andReturn('id');
        $entity->shouldReceive('getRelationValue')->with('properties')
            ->andReturn(new Collection(['city' => 'foo']));

        $this->assertTrue($query->isProperty('city'));
        $this->assertFalse($query->isProperty('color'));
    }

    /** @test */
    public function it_should_not_recognize_relation_as_property()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('employee')->once()->andReturn(true);

        $this->assertFalse($query->isProperty('employee'));
    }

    /** @test */
    public function it_should_not_recognize_attribute_as_property()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('name')->once()->andReturn(false);

        $this->assertFalse($query->isProperty('name'));
    }

    /** @test */
    public function it_should_not_recognize_pk_as_property()
    {
        list($query, $entity) = $this->getQueryAndEntity();

        $entity->shouldReceive('getRelationValue')->with('id')->andReturn(false);
        $entity->shouldReceive('getKeyName')->andReturn('id');

        $this->assertFalse($query->isProperty('id'));
    }

    protected function getQueryAndEntity()
    {
        $entity = m::mock(EntityStub::class);
        $query = new PropertierQueryColumnsStub($entity);

        return [$query, $entity];
    }
}

class EntityStub extends Model
{
    use Propertier;
}

class PropertierQueryColumnsStub extends PropertierQuery
{
    public function fetchModelColumns()
    {
        return ['name'];
    }
}

class PropertierQueryColumnFetchingStub extends PropertierQuery
{
    public function fetchModelColumns()
    {
        $entity = $this->getEntity();
        $entity->shouldReceive('getTable')->once()->andReturn('entity');
        $schemaBuilder = m::mock(Builder::class);
        $schemaBuilder->shouldReceive('getColumnListing')->once()->andReturn(['name']);
        $collection = m::mock(Connection::class);
        $collection->shouldReceive('getSchemaBuilder')->once()->andReturn($schemaBuilder);
        $entity->shouldReceive('getConnection')->once()->andReturn($collection);

        return parent::fetchModelColumns();
    }
}