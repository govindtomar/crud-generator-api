<?php

namespace GovindTomar\CrudGeneratorApi;

use Illuminate\Support\ServiceProvider;
use GovindTomar\CrudGeneratorApi\Commands\CrudGeneratorApi;

class CrudGeneratorApiServiceProvider extends ServiceProvider
{
	public function boot(){
        $this->mergeConfigFrom(__DIR__.'/config/gt-crud-api.php', 'gt-crud-api');

        $this->publishes([__DIR__.'/config/gt-crud-api.php' => config_path('gt-crud-api.php')], 'config');
        $this->publishes([__DIR__.'/Http/Controllers/ApiController.php' => app_path('/Http/Controllers/ApiController.php')], 'controller');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudGeneratorApi::class,
            ]);
        }
	}

	public function register(){

	}
}

// "autoload-dev": {
//     "psr-4": {
//         "Tests\\": "tests/",
//         "GovindTomar\\CrudGeneratorApi\\": "package/govindtomar/crud-generator-api/src/"
//     }
// },
