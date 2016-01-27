<?php

use Devio\Propertier\Propertier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PropertierTest extends PHPUnit_Framework_TestCase
{

    /** @test */
    public function it_should_prevent_properties_autoloading()
    {
        $propertier = new PropertierStub;
        $autoloaded = new PropertierPreventAutoloadingStub;

        $this->assertTrue($propertier->getPropertiesAutoloading());
        $this->assertTrue($propertier->isPropertiesRelationAccessible());

        $this->assertFalse($autoloaded->isPropertiesRelationAccessible());
        $autoloaded->setRelation('properties', new Collection());
        $this->assertTrue($autoloaded->isPropertiesRelationAccessible());
    }

}

class PropertierStub extends Model
{
    use Propertier;
}

class PropertierPreventAutoloadingStub extends Model
{
    use Propertier;

    protected $propertiesAutoloading = false;
}