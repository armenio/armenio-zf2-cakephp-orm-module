<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace CakePHP;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 *
 * TableRegistryServiceFactory
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 *
 *
 */
class TableRegistryServiceFactory implements FactoryInterface
{
    /**
     * zend-servicemanager v2 factory for creating TableRegistry instance.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @returns TableRegistry
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $tableRegistry = new TableRegistry();
        $zendDb = $serviceLocator->get('Zend\Db\Adapter');
        $tableRegistry->setZendDb($zendDb);
        $cache = $serviceLocator->get('Zend\Cache');
        $tableRegistry->setCache($cache);
        return $tableRegistry;
    }
}
