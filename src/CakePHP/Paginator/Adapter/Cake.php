<?php
namespace CakePHP\Paginator\Adapter;

use \Zend\Paginator\Adapter\AdapterInterface;

/**
 * Cake
 * 
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 * @version 1.0
 */
class Cake implements AdapterInterface
{

	protected $_query;
	protected $_count_query;

	protected $_model;
	protected $_params;
	
	public function __construct($model, $params)
	{
		$this->_model = $model;
		$this->_params = $params;
	}
	
	public function getItems($offset, $itemsPerPage)
	{
		$params = $this->_params;
		
		$params['limit'] = $itemsPerPage;
		$params['page'] = null;
		$params['offset'] = $offset;
		
		return $this->_model->findX('all', $params);
	}
	
	public function count()
	{
		$params = $this->_params;
		return $this->_model->findX('count', $params);
	}
	
}