<?php

namespace App\Providers;

use App\Services\AuthorService;
use App\Services\ComicService;
use App\Services\DateService;
use Illuminate\Support\Facades\Validator;
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
        $this->app->singleton(ComicService::class, function(){
            return new ComicService();
        });
        $this->app->singleton(AuthorService::class, function(){
            return new AuthorService();
        });
        $this->app->bind(DateService::class, function($params){
            return new DateService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('is_uri_or_url', function ($attribute, $value, $parameters, $validator) {
            return substr($value, 0, 5) == 'data:' || count(explode('/', $value)) > 1;
        });
    }
}
