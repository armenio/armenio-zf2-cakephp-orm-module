<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace CakePHP;

use Cake\ORM\TableRegistry as CakeORMTableRegistry;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Cache\Storage\StorageInterface;

/**
 *
 *
 * TableRegistry
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 *
 */
class TableRegistry
{
	protected $zendDb;
	protected $cache;

	public function setZendDb(AdapterInterface $zendDb)
    {
        $this->zendDb = $zendDb;
        return $this;
    }

    public function getZendDb()
    {
        return $this->zendDb;
    }

    public function setCache(StorageInterface $cache)
	{
		$this->cache = $cache;
		return $this;
	}
	
	public function getCache()
	{
		return $this->cache;
	}
	public function get($alias, array $options = array())
	{
		$table = CakeORMTableRegistry::get($alias, $options);
		$table->setZendDb($this->zendDb);
		$table->setCache($this->cache);
		return $table;
	}
}