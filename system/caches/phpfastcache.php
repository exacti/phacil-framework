<?php

namespace Phacil\Framework;

//require_once __DIR__."/Phpfastcache/autoload.php";

use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Core\Pool\ExtendedCacheItemPoolInterface;
use Phpfastcache\Exceptions\PhpfastcacheDriverCheckException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use Phpfastcache\Exceptions\PhpfastcacheDriverNotFoundException;
use Phpfastcache\Exceptions\PhpfastcacheInvalidArgumentException;
use Phpfastcache\Exceptions\PhpfastcacheDriverException;
use Phpfastcache\Exceptions\PhpfastcacheInstanceNotFoundException;
use stdClass;
use Phacil\Framework\Config;

/** @package Phacil\Framework */
final class Caches
{
    /**
     * 
     * @var int
     */
    private $expire = (int) 3600;

    /**
     * 
     * @var ExtendedCacheItemPoolInterface
     */
    private $phpfastcache;

    /**
     * 
     * @var string
     */
    public $dirCache = DIR_CACHE . "caches/";

    /**
     * @return void 
     * @throws PhpfastcacheDriverCheckException 
     * @throws PhpfastcacheInvalidConfigurationException 
     * @throws PhpfastcacheDriverNotFoundException 
     * @throws PhpfastcacheInvalidArgumentException 
     * @throws PhpfastcacheDriverException 
     * @throws PhpfastcacheInstanceNotFoundException 
     */
    public function __construct()
    {
        if (!file_exists($this->dirCache)) {
            mkdir($this->dirCache, 0755, true);
        }
        $this->expire = Config::CACHE_EXPIRE() ?: 3600;

        (Config::CACHE_SETTINGS() && is_array(Config::CACHE_SETTINGS())) ? CacheManager::setDefaultConfig(new ConfigurationOption(Config::CACHE_SETTINGS())) : CacheManager::setDefaultConfig(new ConfigurationOption(array('path' => $this->dirCache)));

        $instancias = ((CacheManager::getInstances()));

        if(count($instancias) < 1)
            $this->phpfastcache = (Config::CACHE_DRIVER()) ? CacheManager::getInstance(Config::CACHE_DRIVER()) : CacheManager::getInstance('files');
        else
            $this->phpfastcache = CacheManager::getInstanceById(array_keys($instancias)[0]);


        //$this->phpfastcache->clear();

    }

    /**
     * @param string $key 
     * @return bool 
     * @throws PhpfastcacheInvalidArgumentException 
     */
    public function verify($key)
    {

        $CachedString = $this->phpfastcache->getItem($key);

        return $CachedString->isHit();
    }

    /**
     * @param string $key 
     * @return bool 
     * @throws PhpfastcacheInvalidArgumentException 
     */
    public function check($key)
    {
        return $this->verify($key);
    }

    /**
     * @param string $key 
     * @return mixed 
     * @throws PhpfastcacheInvalidArgumentException 
     */
    public function get($key)
    {

        $CachedString = $this->phpfastcache->getItem($key);

        return $CachedString->get();

    }

    /**
     * @param string $key 
     * @param mixed $value 
     * @param bool $expire 
     * @return bool 
     * @throws PhpfastcacheInvalidArgumentException 
     */
    public function set($key, $value, $expire = true)
    {
        $this->delete($key);

        $CachedString = $this->phpfastcache->getItem($key);

        //$exp = ($expire == true) ? (time() + $this->expire) : 0;

        $CachedString->set($value)->expiresAfter($this->expire);//in seconds, also accepts Datetime

        $save = $this->phpfastcache->save($CachedString);


        return $save; // Save the cache item just like you do with doctrine and entities

    }

    /**
     * @param string $key 
     * @return void 
     */
    public function delete($key)
    {

        $this->phpfastcache->deleteItem($key);
        clearstatcache();

    }

    /** @return bool  */
    public function deleteAll(){

        return $this->phpfastcache->clear();

    }

    /** @return bool  */
    public function clear() {
        return $this->deleteAll();
    }

    /** @return stdClass  */
    public function stats() {
        //var_dump($this->phpfastcache->getStats());

        $obj = new \stdClass();

        $obj->size = $this->phpfastcache->getStats()->getSize();
        $obj->info = $this->phpfastcache->getStats()->getInfo();
        $obj->rawData = $this->phpfastcache->getStats()->getRawData();
        $obj->data = $this->phpfastcache->getStats()->getData();

        return $obj;
    }

}