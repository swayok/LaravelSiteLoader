<?php

namespace LaravelSiteLoader;

use LaravelSiteLoader\Providers\AppSitesServiceProvider;

interface AppSiteLoaderInterface {

    public function __construct(AppSitesServiceProvider $provider, $app);

    static public function canBeUsed();

    static public function getBaseUrl();

    /**
     * Get default locale code (RU, EN, FR, etc)
     * @return string
     */
    static public function getDefaultLocale();

    /**
     * Same as ServiceProvider->register()
     * @return void
     */
    public function register();

    /**
     * Same as ServiceProvider->boot()
     * @return void
     */
    public function boot();

    /**
     * Same as ServiceProvider->provides()
     * @return array
     */
    public function provides();

}