<?php
/*"******************************************************************************************************
*   (c) 2004-2006 by MulchProductions, www.mulchprod.de                                                 *
*   (c) 2007-2016 by Kajona, www.kajona.de                                                              *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\System\System;

use Kajona\System\System\CacheManager;

/**
 * The APC cache depends on the optional apc-bytecode cache.
 *
 * If not installed, the cache falls back to a static cache, storing objects during the current
 * request.
 * Make sure you only store relatively small entries, caching a complete net of objects will
 * lead to outOfMemory errors.
 *
 * @package module_system
 * @author sidler@mulchprod.de
 * @since 3.4.2
 *
 * @deprecated
 * @see \Kajona\System\System\CacheManager
 */
class ApcCache
{
    /**
     * @var ApcCache
     */
    private static $objInstance = null;

    /**
     * singleton, use getInstance instead
     */
    private function __construct()
    {
    }

    /**
     * Returns a valid instance
     *
     * @static
     * @return ApcCache
     */
    public static function getInstance()
    {
        if (self::$objInstance == null) {
            self::$objInstance = new ApcCache();
        }

        return self::$objInstance;
    }

    /**
     * Adds a value to the cache. The third param is the time to live in seconds, defaulted to 180
     *
     * @param string $strKey
     * @param mixed $objValue
     * @param int $intTtl
     *
     * @return array|bool
     */
    public function addValue($strKey, $objValue, $intTtl = 180)
    {
        return CacheManager::getInstance()->addValue($strKey, $objValue, $intTtl, CacheManager::TYPE_APC);
    }

    /**
     * Fetches a value from the cache
     *
     * @param string $strKey
     * @param bool|mixed &$objDefaultValue The value to be returned in case the key is not found in the store.
     *
     * @return bool|mixed false if the entry is not existing
     */
    public function getValue($strKey, &$objDefaultValue = false)
    {
        $strValue = CacheManager::getInstance()->getValue($strKey, CacheManager::TYPE_APC);

        return $strValue === false ? $objDefaultValue : $strValue;
    }

    /**
     * Clears the apc cache
     *
     * @return void
     */
    public function flushCache()
    {
        CacheManager::getInstance()->flushCache(CacheManager::TYPE_APC);
    }

    /**
     * @return bool
     */
    public function getBitAPCInstalled()
    {
        return function_exists("apc_store") && function_exists("apc_cache_info") && @apc_cache_info() !== false;
    }
}
