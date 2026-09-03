<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_production_seeding_does_not_create_the_fixed_local_admin(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--force' => true])->assertExitCode(0);

        $this->assertDatabaseMissing('users', [
            'email' => 'demo-admin@example.test',
        ]);
        $this->assertDatabaseCount('product_categories', 4);
        $this->assertDatabaseCount('products', 6);
        $this->assertDatabaseCount('product_category_translations', 4);
        $this->assertDatabaseCount('product_translations', 6);
    }
}
