<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

// adapted from this article https://dev.to/dsazup/testing-laravel-form-requests-853
abstract class BaseRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    abstract protected function validationProvider(): array;

    /**
     * @test
     * @dataProvider validationProvider
     * @param bool $shouldPass
     * @param array $mockedRequestData
     */
    public function validation_results_as_expected($shouldPass, $mockedRequestData)
    {
        $this->assertEquals(
            $shouldPass, 
            $this->validate($mockedRequestData)
        );
    }

    protected function validate($mockedRequestData)
    {
        $this->app->resolving($this->requestClass, function ($resolved) use ($mockedRequestData){
            $resolved->merge($mockedRequestData);
        });
    
        try 
        {
            app($this->requestClass);
            return true;
        } 
        catch (ValidationException $e) 
        {
            return false;
        }
    }
}