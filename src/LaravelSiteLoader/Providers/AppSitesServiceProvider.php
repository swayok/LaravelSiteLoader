<?php

namespace LaravelSiteLoader\Providers;

use LaravelSiteLoader\AppSiteLoader;
use LaravelSiteLoader\AppSiteLoaderInterface;
use Illuminate\Support\ServiceProvider;

abstract class AppSitesServiceProvider extends ServiceProvider {

    /**
     * Default site loader class name. Used when other site loaders does not match conditions
     * @var string
     */
    protected $defaultSectionLoaderClass;
    /**
     * Console site loader class name. Used when other site loaders does not match conditions and console
     * usage is detected (artisan, queue, schedule, etc.)
     * @var string
     */
    protected $consoleSectionLoaderClass = null;
    /**
     * List of full class names that implement AppSiteLoaderInterface and extend AppSiteLoader
     * Note: no need to add $defaultSection here
     * @var array
     */
    protected $additionalSectionLoaderClasses = [];

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
        foreach ($this->additionalSectionLoaderClasses as $className) {
            if ($className::canBeUsed()) {
                static::$siteLoader = new $className($this, $app);
                break;
            }
        }
        if (static::$siteLoader === null) {
            if ($this->consoleSectionLoaderClass !== null && \App::runningInConsole()) {
                $className = $this->consoleSectionLoaderClass;
            } else {
                $className = $this->defaultSectionLoaderClass;
            }
            static::$siteLoader = new $className($this, $app);
        }
        parent::__construct($app);
    }

    public function boot() {
        if (method_exists(self::$siteLoader, 'boot')) {
            static::$siteLoader->boot();
        }
        /** @var AppSiteLoader $className */
        foreach ($this->additionalSectionLoaderClasses as $className) {
            $className::loadRoutes();
        }
        $className = $this->defaultSectionLoaderClass;
        $className::configureDefaults();
        if (!in_array($this->defaultSectionLoaderClass, $this->additionalSectionLoaderClasses, true)) {
            $className::loadRoutes();
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

}