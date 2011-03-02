<?
/**
 * Data Caching Class
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

define('APP_DATA_CACHE', APP_DATA . 'cache/');

class Core_Cache
{
	private $cache;

	public function __construct()
	{
		$frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);

		$backendOptions = array('cache_dir' => APP_DATA_CACHE);

		$this->cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	}

	public function set($id, $value, $tags = array())
	{
		$this->cache->save($value, $id, $tags);
	}

	public function get($id)
	{
		$this->cache->load($id);
	}

	public function remove($id)
	{
		$this->cache->remove($id);
	}

	public function reset_all()
	{
		$this->cache->clean(Zend_Cache::CLEANING_MODE_ALL);
	}

	public function reset_old()
	{
		$this->cache->clean(Zend_Cache::CLEANING_MODE_OLD);
	}

	public function reset_tags($tags = array())
	{
		if (count($tags) > 0)
		{
			$this->cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
		}
	}

	public function __destruct()
	{

	}
}