<?php

namespace Xbrn\LaravelModular;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModularServiceProvider extends ServiceProvider
{
        public function boot()
        {
                // Auto load routes every modules
                foreach (glob(base_path('modules/*/Routes/web.php')) as $webRoute) {
                        Route::middleware('web')->group($webRoute);
                }

                foreach (glob(base_path('modules/*/Routes/api.php')) as $apiRoute) {
                        Route::prefix('api')->middleware('api')->group($apiRoute);
                }

                // Register artisan commands
                if ($this->app->runningInConsole()) {
                        $this->commands([
                                Console\MakeModuleCommand::class
                        ]);
                }
        }

        public function register()
        {
                //
        }
}
