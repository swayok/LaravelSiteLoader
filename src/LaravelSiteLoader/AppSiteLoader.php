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
     * Sets the locale if it exists in the session or uses preferred or default locale
     *
     * @return void
     */
    public function configureLocale() {
        $defaultLocale = static::getDefaultLocale();
        // set fallback locale
        \App('translator')->setFallback($defaultLocale);
        // set main locale
        \Request::setDefaultLocale($defaultLocale);
        $sessionKey = static::getLocaleSessionKey();
        $preferredLocale = \Request::getPreferredLanguage(static::getAllowedLocales());
        \App::setLocale($sessionKey ? \Session::get($sessionKey, $preferredLocale) : $preferredLocale);
    }

    /**
     * @return string|null - null: do not use session to set app's locale
     */
    static public function getLocaleSessionKey() {
        return get_called_class() . '_locale';
    }

    /**
     * Configure session for current site
     * @param string $connection - connection name
     * @param int $lifetime - session lifetime in minutes
     */
    public function configureSession($connection, $lifetime = 720) {
        $config = $this->getAppConfig()->get('session', ['table' => 'sessions', 'cookie' => 'session', 'driver' => 'file']);
        if ($config['driver'] === 'array' && env('SESSION_DRIVER') === null) {
            // fix for cached config.php created by "php artisan config:cache" command
            $possibleDriver = $this->getAppConfig()->get('env.SESSION_DRIVER');
            if ($possibleDriver) {
                $config['driver'] = $possibleDriver;
            } else {
                // well... life is pain
                $pathToFile = base_path('.env');
                if (file_exists($pathToFile) && preg_match('%^SESSION_DRIVER=(.+)$%m', file_get_contents($pathToFile), $matches)) {
                    $config['driver'] = trim(trim($matches[1]), '\'"');
                }
            }
        }
        $this->getAppConfig()->set('session', array_merge(
            $config,
            $this->getSessionConfig($config, $connection, $lifetime)
        ));
    }

    /**
     * @param array $originalConfig
     * @param string $connection - connection name
     * @param int $lifetime - session lifetime in minutes
     * @return array
     */
    protected function getSessionConfig(array $originalConfig, $connection, $lifetime) {
        return [
            'table' => $originalConfig['table'] . '_' . $connection,
            'cookie' => $originalConfig['cookie'] . '_' . $connection,
            'lifetime' => $lifetime,
            'connection' => $connection,
            'path' => static::getBaseUrl(),
        ];
    }

    static public function loadRoutes() {
        /**
        \Route::group(
            [
                'prefix' => 'section',
                'namespace' => '\App\Section\Http\Controllers',
                'middleware' => ['web'],
            ],
            function () {
                include base_path('routes/section.php');
            }
        );
        */
    }

    /**
     * Used to configure some defaults
     */
    static public function configureDefaults() {

    }

}