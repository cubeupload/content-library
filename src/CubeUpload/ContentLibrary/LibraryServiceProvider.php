<?php namespace CubeUpload\ContentLibrary;

use Illuminate\Support\ServiceProvider;

class LibraryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cube_library', function ($app) {
            return new Library();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['cube_library'];
    }
}
