<?php
/**
 * Rafael Armenio <rafael.armenio@gmail.com>
 *
 * @link http://github.com/armenio for the source repository
 */
 
namespace CakePHP\Model;

use Cake\ORM\TableRegistry as CakeORMTableRegistry;

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
	public function get($alias, array $options = array())
	{
		return CakeORMTableRegistry::get($alias, $options);
	}
}