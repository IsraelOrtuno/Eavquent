<?php

use Illuminate\Support\Collection;

class PropertyTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();


    }

    public function testValidateFunctionRunsValidatorOnSpecifiedRules()
    {
        $request = FoundationTestFormRequestStub::create('/', 'GET', ['name' => 'abigail']);
        $request->setContainer(new Container);
        $factory = m::mock('Illuminate\Validation\Factory');
        $factory->shouldReceive('make')->once()->with(['name' => 'abigail'], ['name' => 'required'])->andReturn(
            $validator = m::mock('Illuminate\Validation\Validator')
        );
        $validator->shouldReceive('fails')->once()->andReturn(false);
        $request->validate($factory);
        $this->assertTrue($_SERVER['__request.validated']);
    }

}