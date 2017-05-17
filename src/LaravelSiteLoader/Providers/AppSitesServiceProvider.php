<?php

namespace LaravelSiteLoader\Providers;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use LaravelSiteLoader\AppSiteLoader;
use LaravelSiteLoader\AppSiteLoaderInterface;
use Illuminate\Support\ServiceProvider;
use LaravelSiteLoader\DummySiteLoader;

abstract class AppSitesServiceProvider extends ServiceProvider {

    /**
     * Default site loader class name. Used when other site loaders does not match conditions
     * @var string
     */
    protected $defaultSiteLoaderClass;
    /**
     * Console site loader class name. Used when other site loaders does not match conditions and console
     * usage is detected (artisan, queue, schedule, etc.)
     * @var string
     */
    protected $consoleSiteLoaderClass = null;
    /**
     * List of full class names that implement AppSiteLoaderInterface and extend AppSiteLoader
     * Note: no need to add $defaultSection here
     * @var array
     */
    protected $additionalSiteLoaderClasses = [];

    /**
     * Service provider object that matched required conditions
     * @var AppSiteLoader|AppSiteLoaderInterface
     */
    static protected $siteLoader;

    protected function getSiteLoader() {
        if (!static::$siteLoader) {
            // detect site section and make its service provider and import
            /** @var AppSiteLoaderInterface|AppSiteLoader $className */
            foreach ($this->additionalSiteLoaderClasses as $className) {
                if ($className::canBeUsed()) {
                    static::$siteLoader = new $className($this, $this->app);
                    break;
                }
            }
            if (static::$siteLoader === null) {
                if ($this->consoleSiteLoaderClass !== null && $this->app->runningInConsole()) {
                    $className = $this->consoleSiteLoaderClass;
                } else {
                    $className = $this->defaultSiteLoaderClass;
                }
                if (isset($className)) {
                    static::$siteLoader = new $className($this, $this->app);
                }
            }
            if (!(static::$siteLoader instanceof AppSiteLoaderInterface)) {
                throw new UnexpectedValueException(
                    'Site loader ' . get_class(static::$siteLoader) . ' must implement ' . AppSiteLoaderInterface::class . ' interface'
                );
            }
            if (!static::$siteLoader) {
                static::$siteLoader = new DummySiteLoader($this, $this->app);
            }
        }
        return static::$siteLoader;
    }

    public function boot() {
        /** @var AppSiteLoader $className */
        foreach ($this->additionalSiteLoaderClasses as $className) {
            $className::loadRoutes();
        }
        if ($this->defaultSiteLoaderClass) {
            $className = $this->defaultSiteLoaderClass;
            $className::configureDefaults();
            if (!in_array($this->defaultSiteLoaderClass, $this->additionalSiteLoaderClasses, true)) {
                $className::loadRoutes();
            }
        }
        $this->getSiteLoader()->boot();
    }

    public function register() {
        $siteLoader = $this->getSiteLoader();
        if (method_exists($siteLoader, 'register')) {
            $siteLoader->register();
        }
    }

    public function provides() {
        $siteLoader = $this->getSiteLoader();
        if (method_exists($siteLoader, 'provides')) {
            $siteLoader->provides();
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