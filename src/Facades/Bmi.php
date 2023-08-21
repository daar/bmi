<?php

namespace Daar\Bmi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Daar\Bmi\Bmi
 */
class Bmi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Daar\Bmi\Bmi::class;
    }
}
