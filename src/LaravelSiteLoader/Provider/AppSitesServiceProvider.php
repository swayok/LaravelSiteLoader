<?php

namespace LaravelSiteLoader\Providers;

use LaravelSiteLoader\AppSiteLoader;
use LaravelSiteLoader\AppSiteLoaderInterface;
use Illuminate\Support\ServiceProvider;

abstract class AppSitesServiceProvider extends ServiceProvider {

    /**
     * Default service provider class name. Used when other providers does not match conditions
     * @var string
     */
    protected $defaultSectionLoaderClass;
    /**
     * List of full class names that implement AppSiteLoaderInterface and extend AppSiteLoader
     * Note: no need to add $defaultSection here
     * @var array
     */
    protected $sectionLoaderClasses = [];

    /**
     * Service provider object that matched required conditions
     * @var AppSiteLoader|AppSiteLoaderInterface
     */
    static protected $siteLoader;

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($app) {
        // detect site section and make its service provider and import
        /** @var AppSiteLoaderInterface|AppSiteLoader $className */
        foreach ($this->sectionLoaderClasses as $className) {
            if ($className::canBeUsed()) {
                static::$siteLoader = new $className($this, $app);
                break;
            }
        }
        if (empty(static::$siteLoader)) {
            $className = $this->defaultSectionLoaderClass;
            static::$siteLoader = new $className($this, $app);
        }
        parent::__construct($app);
    }

    public function boot() {
        if (method_exists(self::$siteLoader, 'boot')) {
            static::$siteLoader->boot();
        }
    }

    public function register() {
        if (method_exists(self::$siteLoader, 'register')) {
            static::$siteLoader->register();
        }
    }

    public function provides() {
        if (method_exists(self::$siteLoader, 'provides')) {
            static::$siteLoader->provides();
        }
    }

    public function loadTranslationsFrom($path, $namespace) {
        // just make public access
        parent::loadTranslationsFrom($path, $namespace);
    }

    public function loadViewsFrom($path, $namespace) {
        // just make public access
        parent::loadViewsFrom($path, $namespace);
    }

    public function publishes(array $paths, $group = null) {
        // just make public access
        parent::publishes($paths, $group);
    }

}