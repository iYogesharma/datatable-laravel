<?php

namespace YS\Datatable\Tests;

use YS\Datatable\Tests\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        $this->migrateDatabase();

        $this->seedDatabase();
    }

    protected function migrateDatabase()
    {
        /** @var \Illuminate\Database\Schema\Builder $schemaBuilder */
        $schemaBuilder = $this->app['db']->connection()->getSchemaBuilder();
        if (! $schemaBuilder->hasTable('users')) {
            $schemaBuilder->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email');
                $table->timestamps();
            });
        }

    }

    protected function seedDatabase()
    {

        collect(range(1, 20))->each(function ($i)  {
            /** @var User $user */
            $user = User::query()->create([
                'name'  => 'Record-' . $i,
                'email' => 'Email-' . $i . '@example.com',
            ]);
        });
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
           \YS\Datatable\DatatableServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Datatable' =>\YS\Datatable\Facades\Datatable::class,
        ];
    }

    protected function  request()
    {
        $request = app('request');
        $request->merge([
            "draw" => "1",
            "columns" =>[
                0 =>[
                    "data" => "name",
                    "name" => null,
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" =>[
                    "value" => null,
                    "regex" => "false",
                    ],
                ],
                1 =>[
                    "data" => "email",
                    "name" => null,
                    "searchable" => "true",
                    "orderable" => "true",
                    "search" =>[
                        "value" => null,
                        "regex" => "false",
                    ],
                ],
            ],
            "order" =>[
                0 =>[
                    "column" => "0",
                    "dir" => "desc",
                ]
            ],
            "start" => "0",
            "length" => "10",
            "search" =>[
            "value" => null,
            "regex" => "false",
            ],
            "_" => "1583671917308",
        ]);
    }
}