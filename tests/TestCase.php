<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Scout\EngineManager;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);
    }
}
