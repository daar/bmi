<?php

namespace Daar\Bmi\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Daar\Bmi\BmiServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            BmiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        //
    }
}