<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function roleRoute(string $name, string $roleSlug, array $parameters = []): string
    {
        return route($name, ['roleSlug' => $roleSlug] + $parameters);
    }
}
