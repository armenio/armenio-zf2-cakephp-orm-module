<?php
/**
 * CakePHP config
 *
 * @author Rafael Armenio <rafael.armenio@gmail.com>
 */

return array(
	'CakePHP' => array(
		'Configure' => array(
			'App' => array(
				'namespace' => 'Custom',
			),
		),
		'Cache' => array(
			'_cake_model_' => array(
				'className' => 'File',
				'prefix' => '',
				'path' => 'data/cakephp/cache/models/',
				'serialize' => true,
				'duration' => 0,
			),
		),
		'Log' => array(
			'queries' => array(
				'className' => 'File',
				'path' => 'data/cakephp/logs/',
				'file' => 'queries.log',
				'scopes' => ['queriesLog'],
			),
		),
		'Datasources' => array(
			'default' => array(
				'className' => 'Cake\Database\Connection',
				'driver' => 'Cake\Database\Driver\Mysql',
				'persistent' => false,
				'host' => 'localhost',
				//'port' => 'non_standard_port_number',
				'username' => 'root',
				'password' => '',
				'database' => 'custom',
				//'encoding' => 'utf8',
				'timezone' => 'UTC',
				//'flags' => [],
				'cacheMetadata' => true,
				'log' => false,
				'quoteIdentifiers' => true,
				//'init' => array('SET GLOBAL innodb_stats_on_metadata = 0'),
			),
		),
	),
    'service_manager' => array(
        'factories' => array(
            'Armenio\CakePHP\TableRegistry' => 'Armenio\CakePHP\TableRegistryServiceFactory',
        ),
    ),
	/*
	 * Remover este comentário caso não tenha o zf2 cache configurado em outro módulo
	 *
	'caches' => array(
		'Zend\Cache' => array(
			'adapter' => array(
				'name' => 'filesystem',
			),
			'options' => array(
				'cache_dir' => 'data/cache/',
				'ttl' => 31557600,
			),
			'plugins' => array(
				'Serializer',
			),
		),
	),
	*/
	
	/*
	 * Remover este comentário caso não tenha o zf2 db configurado em outro módulo
	 *
	'db' => array(
		'adapters' => array(
			'Zend\Db\Adapter' => array(
				'driver' => 'Pdo_Mysql',
				'host' => 'localhost',
				'username' => 'root',
				'password' => '',
				'dbname' => 'custom',
			),
		),
	),
	*/
);
