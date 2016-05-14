<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace CakePHP\Model;

use Cake\ORM\Table as CakeORMTable;
use Cake\Datasource\EntityInterface;

use Zend\Paginator\Paginator;
use CakePHP\Paginator\Adapter\Cake as CakePaginatorAdapter;

use DateTime;

/**
 *
 *
 * Table
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 *
 */
class Table extends CakeORMTable 
{
	public static $zendDbAdapter;
	public static $zendCache;

	public $fields = array();
	public $belongsTo = array();
	public $hasMany = array();

	/**
	 * saveX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function saveX(EntityInterface $entity, $options = array()) 
	{
		$cacheTag = sprintf('table_%s', $this->table());
		self::$zendCache->clearByTags(array($cacheTag));

		return parent::save($entity, $options);
	}

	/**
	 * insertX
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	public function insertX(array $data)
	{
		//$dateFormat = new HelperDate();
		//$now = $dateFormat->formatDbDateTime();
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
		//$dateFormat = new HelperDate();
		//$now = $dateFormat->formatDbDateTime();
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
		//$dateFormat = new HelperDate();
		//$now = $dateFormat->formatDbDateTime();
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
	 * _formatResult
	 *
	 * @author Rafael Armenio <rafael.armenio@gmail.com>
	 */
	protected function _formatResult($result){
		if( $result instanceof \Cake\Database\Query ){
			$result = $result->toArray();
		}elseif( $result instanceof \Cake\ORM\Entity ){
			return $result->toArray();
		}else{
			return $result;
		}

		if( ! empty($result) ){
			foreach ( $result as $key => $row ) {
				
				$result[$key] = $this->_formatResult($row);
			}
		}

		return $result;
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
		
		/*if( ! isset($options['conditions']) ){
			$options['conditions'] = array();
		}*/

		/*if( ! is_array($options['conditions']) ){
			$options['conditions'] = array($options['conditions']);
		}*/

		/*if( ( ! isset($options['conditions'][sprintf('%s.status', $this->alias())]) ) && ( ! isset($options['conditions']['status']) ) ){
			$options['conditions'][sprintf('%s.status', $this->alias())] = 1;
		}*/

		/*if( $type == 'first' ){
			$options['limit'] = 1;
		}*/
		
		$args = array();
		$args[] = array('model' => $this->alias());
		$args[] = array('type' => $type);
		$args[] = array('options' => $options);

		$cacheIndex = sprintf('find_%s', md5(serialize($args)));
		
		unset($args);

		$result = self::$zendCache->getItem($cacheIndex, $success);
		
		if( $success === false ){

			$find = $this->find($type, $options);
			$result = $this->_formatResult($find);

			$cacheTags = array();

			$cacheTag = sprintf('table_%s', $this->table());
			$cacheTags[$cacheTag] = $cacheTag;
			
			if( class_exists('\Custom\Configure\Model') ){
				if( ! empty($this->belongsTo) ){
					foreach($this->belongsTo as $modelName => $relation){
						if( ! empty(\Custom\Configure\Model::$tables[$modelName]) ){
							$cacheTag = sprintf('table_%s', \Custom\Configure\Model::$tables[$modelName]);
							$cacheTags[$cacheTag] = $cacheTag;
						}
					}
				}

				if( ! empty($this->hasMany) ){
					foreach($this->hasMany as $modelName => $relation){
						if( ! empty(\Custom\Configure\Model::$tables[$modelName]) ){
							$cacheTag = sprintf('table_%s', \Custom\Configure\Model::$tables[$modelName]);
							$cacheTags[$cacheTag] = $cacheTag;
						}
					}
				}
			}

			unset($modelName);
			unset($relation);

			self::$zendCache->setItem($cacheIndex, $result);
			self::$zendCache->setTags($cacheIndex, $cacheTags);
			
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
			
			$pager = new Paginator(new CakePaginatorAdapter($this, $params));
			$pager->setItemCountPerPage($itemCountPerPage);
			$pager->setPageRange($pageRange);
			$pager->setCurrentPageNumber($page);
		}catch (\Zend\Paginator\Paginator\Exception\RuntimeException $e){
			$pager = false;
		}
		
		return $pager;
	}
}