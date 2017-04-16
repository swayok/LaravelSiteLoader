<?php

namespace LaravelSiteLoader;

class DummySiteLoader extends AppSiteLoader {

    static public function canBeUsed() {
        return false;
    }

    static public function getBaseUrl() {
        return '/';
    }

    /**
     * Get default locale code (ru, en, fr, etc)
     * @return string
     */
    static public function getDefaultLocale() {
        return 'en';
    }

    /**
     * Get list of allowed locales.
     * Example: ['en', 'ru', 'fr']
     * @return array
     */
    static public function getAllowedLocales() {
        return ['en'];
    }

    public function configureSession($connection, $lifetime = 720) {

    }

    public function configureLocale() {

    }


}