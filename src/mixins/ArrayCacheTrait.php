<?php


namespace digitalwand\yii2ModularApp\mixins;


/**
 * Class ArrayCacheTrait
 * @package app\helpers
 */
trait ArrayCacheTrait
{
    /**
     * @var array
     */
    private static $cache = [];


    /**
     * @param $key
     * @param $value
     */
    protected static function setCacheValue($key, $value) {
        self::$cache[$key] = $value;
    }

    /**
     * @param $key
     */
    protected static function unsetCacheValue($key) {
        unset(self::$cache[$key]);
    }

    /**
     * @param $key
     * @return null
     */
    protected static function getCacheValue($key) {
        return self::hasCacheValue($key) ? self::$cache[$key] : null;
    }

    /**
     * @param $key
     * @return bool
     */
    protected static function hasCacheValue($key) {
        return array_key_exists($key, self::$cache);
    }

    /**
     * @void
     */
    protected static function clearCache() {
        self::$cache = [];
    }

    /**
     * @param $key
     * @param callable $getter
     * @return null
     */
    protected static function cached($key, callable $getter) {
        if (false === self::hasCacheValue($key)) {
            self::setCacheValue($key, $getter());
        }

        return self::getCacheValue($key);
    }
}