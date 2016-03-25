# armenio-zf2-cakephp-orm-module
The CakePHP Module for Zend Framework 2

## How to install


1. Install via composer. Don't know how? [Look here](http://getcomposer.org/doc/00-intro.md#introduction)

2. `cd my/project/directory`

3. Edit composer.json :

```json
{
	"require": {
		"armenio/armenio-zf2-cakephp-orm-module": "1.*"
	}
}
```

4. Edit config/application.config.php :

```php
'modules' => array(
	 'Application',
	 'CakePHP', //<==============================
)
```

5. Change your Model namespace in cd my/project/directory/vendor/armenio/armenio-zf2-cakephp-orm-module/config/module.config.php

```php
	'CakePHP' => array(
		'Configure' => array(
			'App' => array(
				'namespace' => 'Custom' //<======= put your App/Module namespace HERE!
			),
		),
	),
```

6. Create your models
	
	6.1. Go to my/project/directory/your/app/namespace

	6.2. Create a directory Model/Table/

	6.3. Go to my/project/directory/your/app/namespace/Model/Table/

	6.4. Create the File MyModelTable.php


```php
<?php
namespace Custom\Model\Table;

use CakePHP\Model\Table as CakePHPTable;

class MyModelTable extends CakePHPTable
{

	protected $_table = 'my_table';

	protected $_alias = 'MyModel';

	protected $_primaryKey = 'id';
}
```

See more here: http://book.cakephp.org/3.0/en/orm.html

## How to use

```php
<?php
use Cake\ORM\TableRegistry;
$table = TableRegistry::get('MyModel');
$all = $table->find('all');
```