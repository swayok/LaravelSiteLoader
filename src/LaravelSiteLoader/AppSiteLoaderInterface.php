<?php

namespace LaravelSiteLoader;

use LaravelSiteLoader\Providers\AppSitesServiceProvider;

interface AppSiteLoaderInterface {

    public function __construct(AppSitesServiceProvider $provider, $app);

    static public function canBeUsed();

    static public function getBaseUrl();

    /**
     * Get default locale code (ru, en, fr, etc)
     * @return string
     */
    static public function getDefaultLocale();

    /**
     * Get list of allowed locales.
     * Example: ['en', 'ru', 'fr']
     * @return array
     */
    static public function getAllowedLocales();

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

    /**
     * Resources publishing
     * @param AppSitesServiceProvider $provider
     */
    static public function configurePublishes(AppSitesServiceProvider $provider);

}