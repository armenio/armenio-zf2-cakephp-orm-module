<?php
/**
 * CakePHP Module
 *
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 */

namespace CakePHP;

use Zend\Mvc\MvcEvent;

use CakePHP\Model\Table;

use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Log\Log;
use Cake\Datasource\ConnectionManager;

class Module
{
	public function onBootstrap(MvcEvent $e)
	{
		/* 
		 * pega as configurações
		 */
		$config = $e->getApplication()->getServiceManager()->get('Config');

		/* 
		 * cache start
		 */
		Table::$zendCache = $e->getApplication()->getServiceManager()->get('Zend\Cache');

		/* 
		 * db adapter do zend para validações (Db\RecordExists|Db\NoRecordExists)
		 */
		Table::$zendDbAdapter = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter');

		/* 
		 * arruma a configuração do cakePHP
		 */
		$config['CakePHP']['Cache']['_cake_model_']['duration']  = $config['caches']['Zend\Cache']['options']['ttl'];
		$config['CakePHP']['Datasources']['default']['host']	 = $config['db']['adapters']['Zend\Db\Adapter']['host'];
		$config['CakePHP']['Datasources']['default']['username'] = $config['db']['adapters']['Zend\Db\Adapter']['username'];
		$config['CakePHP']['Datasources']['default']['password'] = $config['db']['adapters']['Zend\Db\Adapter']['password'];
		$config['CakePHP']['Datasources']['default']['database'] = $config['db']['adapters']['Zend\Db\Adapter']['dbname'];

		/* 
		 * seta o namespace padrão do CakePHP (App\Model)
		 */
		foreach ($config['CakePHP']['Configure'] as $configKey => $configValue) {
			Configure::write($configKey, $configValue);
		}

		/* 
		 * configura o cache do CakePHP
		 */
		foreach ($config['CakePHP']['Cache'] as $configKey => $configValue) {
			$cacheDir = sprintf('%s/%s', ROOT_PATH, $configValue['path']);
			if( ! is_dir($cacheDir) ){
				@mkdir($cacheDir, 0755, true);
			}
			Cache::config($configKey, $configValue);
		}

		/* 
		 * configura o log do CakePHP
		 */
		foreach ($config['CakePHP']['Log'] as $configKey => $configValue) {
			Log::config($configKey, $configValue);
		}

		/* 
		 * setup da conexão com banco de dados no CakePHP
		 */
		foreach ($config['CakePHP']['Datasources'] as $configKey => $configValue) {
			ConnectionManager::config($configKey, $configValue);
		}
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}
}
