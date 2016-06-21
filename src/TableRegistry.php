<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace Armenio\CakePHP;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Cake\ORM\TableRegistry as CakeORMTableRegistry;

/**
 *
 *
 * TableRegistry
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 *
 */
class TableRegistry implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

	public function get($alias, array $options = [])
	{
		$table = CakeORMTableRegistry::get($alias, $options);
        if( get_class($table) !== 'Cake\ORM\Table' ){
    		$table->setServiceLocator($this->getServiceLocator());
        }
		return $table;
	}
}