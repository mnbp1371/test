<?php

namespace App\Providers;

use App\Helpers\ConsoleCommandHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        JsonResponse::macro('toArray', function () {
            return json_decode($this->getContent(), true);
        });

        $this->app->singleton(ConsoleCommandHelper::class, ConsoleCommandHelper::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

//        Request::macro('toArray', function () {
//            return json_decode($this->getContent(), true);
//        });
    }
}
