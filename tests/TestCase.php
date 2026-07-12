<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

abstract class TestCase extends BaseTestCase
{
    // Counts Mockery expectations as PHPUnit assertions (avoids "risky" tests)
    use MockeryPHPUnitIntegration;
}
