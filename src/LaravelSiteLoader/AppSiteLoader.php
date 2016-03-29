<?php

namespace LaravelSiteLoader;

use LaravelSiteLoader\Providers\AppSitesServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpFoundation\ParameterBag;

abstract class AppSiteLoader implements AppSiteLoaderInterface {

    /** @var AppSitesServiceProvider */
    protected $provider;
    /** @var Application */
    protected $app;

    public function __construct(AppSitesServiceProvider $provider, $app) {
        $this->provider = $provider;
        $this->app = $app;
    }

    static public function canBeUsed() {
        return (
            !empty($_SERVER['REQUEST_URI'])
            && starts_with($_SERVER['REQUEST_URI'], static::getBaseUrl())
        );
    }

    public function boot() {

    }

    public function register() {

    }

    public function provides() {
        return [];
    }

    /**
     * @return ParameterBag
     */
    protected function getAppConfig() {
        return config();
    }

    /**
     * Sets the locale if it exists in the session and also exists in the locales option
     *
     * @return void
     */
    static public function setLocale() {
        $locale = session()->get(get_called_class() . '_locale');
        \App::setLocale($locale ?: static::getDefaultLocale());
    }

}