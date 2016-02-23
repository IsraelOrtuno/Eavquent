<?php

use Mockery as m;
use Devio\Propertier\Factory;
use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Model;

class PropertierTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_access_fields_only_if_accessible()
    {
        $entityAutoloading = new PropertierStub;
        $entityNonAutoloading = new PropertierNonAutoloadingStub;
        $entityNonAutoloadingWithRelations = new PropertierNonAutoloadingStub;

        $entityNonAutoloadingWithRelations->setFieldRelation('foo', function () {
        });

        $this->assertTrue($entityAutoloading->areFieldsAccessible());
        $this->assertTrue($entityAutoloading->getFieldsAutoloading());

        $this->assertFalse($entityNonAutoloading->areFieldsAccessible());
        $this->assertFalse($entityNonAutoloading->getFieldsAutoloading());

        $this->assertTrue($entityNonAutoloadingWithRelations->areFieldsAccessible());
        $this->assertFalse($entityNonAutoloadingWithRelations->getFieldsAutoloading());
    }

    /** @test */
    public function it_can_register_relations_dynamically()
    {
        $entity = new PropertierStub;

        $entity->setFieldRelation('foo', function () {
            return 'bar';
        });

        $this->assertEquals('bar', $entity->foo());
    }
}

class PropertierStub extends Model
{
    use Propertier;

    public function factory()
    {
        return m::mock(Factory::class);
    }
}

class PropertierNonAutoloadingStub extends Model
{
    use Propertier;

    protected $fieldsAutoloading = false;
}