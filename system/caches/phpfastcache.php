<?php

require_once __DIR__."/Phpfastcache/autoload.php";

//require_once __DIR__."/caches.php";

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;

final class Caches
{
    private $expire = 3600;

    private $phpfastcache;

    public $dirCache = DIR_CACHE . "caches/";

    public function __construct()
    {
        if (!file_exists($this->dirCache)) {
            mkdir($this->dirCache, 0755, true);
        }
        $this->expire = (defined('CACHE_EXPIRE')) ? CACHE_EXPIRE : 3600;

        (defined('CACHE_SETTINGS') && is_array(CACHE_SETTINGS)) ? CacheManager::setDefaultConfig(new ConfigurationOption(CACHE_SETTINGS)) : CacheManager::setDefaultConfig(new ConfigurationOption(array('path' => $this->dirCache)));

        $instancias = ((CacheManager::getInstances()));

        if(count($instancias) < 1)
            $this->phpfastcache = (defined('CACHE_DRIVER')) ? CacheManager::getInstance(CACHE_DRIVER) : CacheManager::getInstance('files');
        else
            $this->phpfastcache = CacheManager::getInstanceById(array_keys($instancias)[0]);


        //$this->phpfastcache->clear();

    }

    public function verify($key)
    {

        $CachedString = $this->phpfastcache->getItem($key);

        return $CachedString->isHit();
    }

    public function check($key)
    {
        return $this->verify($key);
    }

    public function get($key)
    {

        $CachedString = $this->phpfastcache->getItem($key);

        return $CachedString->get();

    }

    public function set($key, $value, $expire = true)
    {
        $this->delete($key);

        $CachedString = $this->phpfastcache->getItem($key);

        //$exp = ($expire == true) ? (time() + $this->expire) : 0;

        $CachedString->set($value)->expiresAfter($this->expire);//in seconds, also accepts Datetime

        $save = $this->phpfastcache->save($CachedString);


        return $save; // Save the cache item just like you do with doctrine and entities

    }

    public function delete($key)
    {

        $this->phpfastcache->deleteItem($key);
        clearstatcache();

    }

    public function deleteAll(){

        return $this->phpfastcache->clear();

    }

    public function clear() {
        return $this->deleteAll();
    }

    public function stats() {
        //var_dump($this->phpfastcache->getStats());

        $obj = new stdClass();

        $obj->size = $this->phpfastcache->getStats()->getSize();
        $obj->info = $this->phpfastcache->getStats()->getInfo();
        $obj->rawData = $this->phpfastcache->getStats()->getRawData();
        $obj->data = $this->phpfastcache->getStats()->getData();

        return $obj;
    }

}