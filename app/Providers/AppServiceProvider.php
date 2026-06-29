<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Gallery;
use App\Services\CloudinaryAdapter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

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
        Storage::extend('cloudinary', function () {
            $config = [
                'driver'     => 'cloudinary',
                'cloud_name' => AppSetting::get('cloudinary_cloud_name', ''),
                'api_key'    => AppSetting::get('cloudinary_api_key', ''),
                'api_secret' => AppSetting::get('cloudinary_api_secret', ''),
                'folder'     => AppSetting::get('cloudinary_folder', 'styledinee'),
            ];

            $adapter = new CloudinaryAdapter($config);

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
                $config,
            );
        });

        if (AppSetting::bool('cloudinary_enabled') && AppSetting::get('cloudinary_cloud_name')) {
            config(['filesystems.default' => 'cloudinary']);
        }

        View::composer('layouts.app', function ($view) {
            $view->with('footerGallery', Gallery::forSection('footer')->limit(8)->get());
        });
    }
}
