<?php

namespace JeffersonGoncalves\Gtag\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\Gtag\GtagServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelSettings\LaravelSettingsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSettingsServiceProvider::class,
            GtagServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function setUpDatabase(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('name');
            $table->boolean('locked')->default(false);
            $table->json('payload');
            $table->timestamps();

            $table->unique(['group', 'name']);
        });

        $this->seedDefaultSettings();
    }

    protected function seedDefaultSettings(): void
    {
        $defaults = [
            ['group' => 'gtag', 'name' => 'gtag_id', 'payload' => json_encode(null)],
            ['group' => 'gtag', 'name' => 'enabled', 'payload' => json_encode(true)],
            ['group' => 'gtag', 'name' => 'anonymize_ip', 'payload' => json_encode(false)],
            ['group' => 'gtag', 'name' => 'additional_config', 'payload' => json_encode([])],
        ];

        foreach ($defaults as $setting) {
            \Illuminate\Support\Facades\DB::table('settings')->insert(
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
