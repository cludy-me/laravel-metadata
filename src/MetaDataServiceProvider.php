<?php

namespace CludyMe\MetaData;

use Illuminate\Support\ServiceProvider;
use CludyMe\MetaData\Console\MetaModelMakeCommand;
use CludyMe\MetaData\Console\MetaMigrationMakeCommand;

class MetaDataServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'command.meta.model.make',
            function ($app) {
                return new MetaModelMakeCommand($app['files']);
            }
        );

        $this->app->singleton(
            'command.meta.migration.make',
            function ($app) {
                return new MetaMigrationMakeCommand($app['files'], $app['composer']);
            }
        );

        $this->commands(
            'command.meta.model.make',
            'command.meta.migration.make'
        );
    }
}
