<?php

namespace JeffersonGoncalves\Gtag\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $app['config']->set('database.connections.testing', $this->testing_connection());
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
            DB::table('settings')->insert(
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * The original in-memory SQLite connection by default; CI (tests.yml) sets
     * GTAG_TEST_DB_* to run the same suite on MySQL and PostgreSQL. Not DB_CONNECTION:
     * Testbench pins it to "testing", which would always win over a driver read from it.
     *
     * @return array<string, mixed>
     */
    protected function testing_connection(): array
    {
        $driver = env('GTAG_TEST_DB_DRIVER', 'sqlite');

        if ($driver === 'sqlite') {
            return [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ];
        }

        return [
            'driver' => $driver,
            'host' => env('GTAG_TEST_DB_HOST', '127.0.0.1'),
            'port' => env('GTAG_TEST_DB_PORT'),
            'database' => env('GTAG_TEST_DB_DATABASE', 'testing'),
            'username' => env('GTAG_TEST_DB_USERNAME', 'root'),
            'password' => env('GTAG_TEST_DB_PASSWORD', ''),
            'charset' => $driver === 'pgsql' ? 'utf8' : 'utf8mb4',
            'prefix' => '',
        ];
    }
}
