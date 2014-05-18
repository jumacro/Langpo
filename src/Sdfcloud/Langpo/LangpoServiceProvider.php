<?php

namespace Sdfcloud\Langpo;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Symfony\Component\Process\ProcessBuilder;
use Illuminate\Filesystem\Filesystem;

/**
 * LangpoServiceProvider Service Provider
 * 
 * 
 * 
 * PHP 5.0 / Laravel 4.0
 * 
 * @author        Mithun Das (mithundas79) on behalf of Pinpoint Media Design (pinpointgraphics)
 * @copyright     Copyright 2014, Pinpoint Media Design
 * @package       Sdfcloud.Langpo
 * @property      Langpo $Langpo
 * @since         SDFCloud 3.0
 * 
 */
class LangpoServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->package('sdfcloud/langpo');

        // include custom exceptions
        include_once __DIR__ . '/Exceptions.php';

        // include routes
        include_once __DIR__ . '/../../routes.php';

        // include functions
        include_once __DIR__ . '/HelperFunctions.php';

        $gt = new Langpo();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //register Langpo  and alias
        $this->registerLangpo();

        // register blade compiler
        $this->registerLangpoBladeCompiler();

        // register commands
        $this->registerCompileCommand();
        $this->registerExtractCommand();
        $this->registerListCommand();
        $this->registerFetchCommand();
    }

    /**
     * register Langpo
     */
    public function registerLangpo() {
        // register Langpo
        $this->app['langpo'] = $this->app->share(function($app) {
            return new Langpo();
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('Langpo', 'Sdfcloud\Langpo\Facades\Langpo');
        });
    }

    /**
     * register bladecompiler
     */
    public function registerLangpoBladeCompiler() {
        // register bladecompiler
        $this->app['bladecompiler'] = $this->app->share(function($app) {
            return new Compilers\BladeCompiler(new Filesystem, "");
        });

        // Shortcut so developers don't need to add an Alias in app/config/app.php
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('BladeCompiler', 'Sdfcloud\Langpo\Facades\BladeCompiler');
        });
    }

    /**
     * register compile command
     */
    public function registerCompileCommand() {
        // add compile command to artisan
        $this->app['langpo.compile'] = $this->app->share(function($app) {
            return new Commands\CompileCommand();
        });
        $this->commands('langpo.compile');
    }

    /**
     * register extract command
     */
    public function registerExtractCommand() {
        // add extract command to artisan
        $this->app['langpo.extract'] = $this->app->share(function($app) {
            return new Commands\ExtractCommand(new ProcessBuilder);
        });
        $this->commands('langpo.extract');
    }

    /**
     * register list command
     */
    public function registerListCommand() {
        // add list command to artisan
        $this->app['langpo.list'] = $this->app->share(function($app) {
            return new Commands\ListCommand();
        });
        $this->commands('langpo.list');
    }

    /**
     * register fetch command
     */
    public function registerFetchCommand() {
        // add fetch command to artisan
        $this->app['langpo.fetch'] = $this->app->share(function($app) {
            return new Commands\FetchCommand(new ProcessBuilder);
        });
        $this->commands('langpo.fetch');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array("Langpo");
    }

}
