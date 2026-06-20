<?php

namespace App\Providers;

use App\Models\Gallery;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('footerGallery', Gallery::where('is_active', true)->inRandomOrder()->limit(8)->get());
        });
    }
}
