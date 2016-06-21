<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace Armenio\CakePHP\Model;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Armenio\CakePHP\TableRegistry;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Cache\Storage\StorageInterface;

use Cake\ORM\Table as CakeORMTable;

use Cake\Datasource\EntityInterface;

use Zend\Paginator\Paginator as ZendPaginator;
use Armenio\CakePHP\Paginator\Adapter\CakePHP as ArmenioCakePHPPaginatorAdapter;

use DateTime;

/**
 *
 *
 * Table
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 *
 */
class Table extends CakeORMTable implements ServiceLocatorAwareInterface
{
	protected $serviceLocator;
	protected $tableRegistry;
	protected $zendDb;
    protected $cache;

	public $fields = array();
	public $belongsTo = array();
	public $hasMany = array();

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setTableRegistry(TableRegistry $tableRegistry)
	{
		$this->tableRegistry = $tableRegistry;
		return $this;
	}
	
	public function getTableRegistry()
	{
		if( $this->tableRegistry === null ){
			$this->setTableRegistry($this->getServiceLocator()->get('Armenio\CakePHP\TableRegistry'));
		}

		return $this->tableRegistry;
	}

    public function setZendDb(AdapterInterface $zendDb)
    {
        $this->zendDb = $zendDb;
        return $this;
    }

    public function getZendDb()
    {

        if( $this->zendDb === null ){
			$this->setZendDb($this->getServiceLocator()->get('Zend\Db\Adapter'));
		}

		return $this->zendDb;
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }
    
    public function getCache()
    {
        if( $this->cache === null ){
            $this->setCache($this->getServiceLocator()->get('Zend\Cache'));
        }

        return $this->cache;
    }

	/**
	 * saveX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function saveX(EntityInterface $entity, $options = array()) 
	{
		$cacheTag = sprintf('table_%s', $this->table());

		$cache = $this->getCache();
		
		$cache->clearByTags(array($cacheTag));

		return parent::save($entity, $options);
	}

	/**
	 * insertX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function insertX(array $data)
	{
		$dateTime = new DateTime();
		$now = $dateTime->format('Y-m-d H:i:s');

		$columns = $this->schema()->columns();

		if( in_array('status', $columns) ){
			$data['status'] = 1;
		}
		if( in_array('created', $columns) ){
			$data['created'] = $now;
		}
		if( in_array('updated', $columns) ){
			$data['updated'] = $now;
		}

		$entity = $this->newEntity($data);
		
		if( ! empty($entity) ){
			return $this->saveX($entity)->toArray();
		}

		return false;
	}

	/**
	 * updateX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function updateX(array $data)
	{
		$dateTime = new DateTime();
		$now = $dateTime->format('Y-m-d H:i:s');

		$dataFind = array(
			'id' => $data['id'],
		);

		$columns = $this->schema()->columns();

		if( in_array('status', $columns) ){
			$dataFind['status'] = 1;
		}
		if( in_array('updated', $columns) ){
			$data['updated'] = $now;
		}

		$entity = $this->find('all', array(
			'conditions' => $dataFind,
		))->first();

		if( ! empty($entity) ){
			$this->patchEntity($entity, $data);
			
			return $this->saveX($entity)->toArray();
		}

		return false;
	}

	/**
	 * deleteX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function deleteX($id, array $data = array()) 
	{
		$dateTime = new DateTime();
		$now = $dateTime->format('Y-m-d H:i:s');
		
		$columns = $this->schema()->columns();

		$data['id'] = $id;

		if( in_array('status', $columns) ){
			$data['status'] = 0;
		}
		if( in_array('deleted', $columns) ){
			$data['deleted'] = $now;
		}

		return $this->updateX($data);
	}

	/**
	 * formatResult
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	protected function formatResult($result)
	{
		if( $result instanceof \Cake\Database\Query ){
			$result = $result->toArray();

			if( ! empty($result) ){
				foreach ( $result as $key => $row ) {
					$result[$key] = $this->formatResult($row);
				}
			}

			return $result;
		}elseif( $result instanceof \Cake\ORM\Entity ){
			return $result->toArray();
		}else{
			return $result;
		}
	}

	/**
	 * find
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function find($type = 'all', $options = []) 
	{
		$result = false;
		
		if( ! isset($options['conditions']) ){
			$options['conditions'] = array();
		}

		if( ! is_array($options['conditions']) ){
			$options['conditions'] = array($options['conditions']);
		}

		$columns = $this->schema()->columns();

		if( in_array('status', $columns) ){
			if( ( ! isset($options['conditions'][sprintf('%s.status', $this->alias())]) ) && ( ! isset($options['conditions']['status']) ) ){
				$options['conditions'][sprintf('%s.status', $this->alias())] = 1;
			}
		}

		if( $type == 'first' ){
			$options['limit'] = 1;
		}

		if( $type == 'first' || $type == 'last' || $type == 'count' ){
			$finder = parent::find('all', $options);

			$result = $finder->$type();
		}else{
			$result = parent::find($type, $options);
		}

		return $result;
	}

	/**
	 * findX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function findX($type = 'all', $options = array()) 
	{
		$result = false;
		
		$args = array();
		$args[] = array('model' => $this->alias());
		$args[] = array('type' => $type);
		$args[] = array('options' => $options);

		$cacheIndex = sprintf('find_%s', md5(serialize($args)));
		
		unset($args);

		$cache = $this->getCache();

		$result = $cache->getItem($cacheIndex, $success);
		
		if( $success === false ){

			$find = $this->find($type, $options);
			$result = $this->formatResult($find);

			$cacheTags = array();

			$cacheTag = sprintf('table_%s', $this->table());
			$cacheTags[$cacheTag] = $cacheTag;
			
			if( class_exists('\Custom\Configure\Model') ){
				if( ! empty($this->belongsTo) ){
					foreach($this->belongsTo as $modelName => $relation){
						if( ! empty(\Custom\Configure\Model::$config[$modelName]['table']) ){
							$cacheTag = sprintf('table_%s', \Custom\Configure\Model::$config[$modelName]['table']);
							$cacheTags[$cacheTag] = $cacheTag;
						}
					}
				}

				if( ! empty($this->hasMany) ){
					foreach($this->hasMany as $modelName => $relation){
						if( ! empty(\Custom\Configure\Model::$config[$modelName]['table']) ){
							$cacheTag = sprintf('table_%s', \Custom\Configure\Model::$config[$modelName]['table']);
							$cacheTags[$cacheTag] = $cacheTag;
						}
					}
				}
			}

			unset($modelName);
			unset($relation);

			$cache->setItem($cacheIndex, $result);
			$cache->setTags($cacheIndex, $cacheTags);
			
		}

		return $result;
	}

	/**
	 * paginate
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function paginateX($params = array(), $page = 1, $itemCountPerPage = 50, $pageRange = 5, $keyPage = 'pagina')
	{
		$pager = false;

		try{
			
			$pager = new ZendPaginator(new ArmenioCakePHPPaginatorAdapter($this, $params));
			$pager->setItemCountPerPage($itemCountPerPage);
			$pager->setPageRange($pageRange);
			$pager->setCurrentPageNumber($page);
		}catch (ZendPaginator\Exception\RuntimeException $e){
			$pager = false;
		}
		
		return $pager;
	}
}