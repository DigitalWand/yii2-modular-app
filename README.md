
Yii2 modular application
========================
API to build modular Yii2 applications.

Abstract
--------
Yii2 have flexible module system. But if we have project, consists of many modules, we would face this list of problems:
- Every time write boilerplate code to make module controllers work properly and to enable module in config file 
- Migrations are project-wide, and you need a bit more code to store migrations into module's code. More boilerplate again.
- Mail templates are project-wide, we also need additional code...
- Module autoloading works only when calling module's controller. But if you module provide only API? We need to control module loading manually.
- Everything mess is possible inside Yii2 module. You have not any strict patterns, how to organise structure. 
    So, modules from one project might look very different without an evil teamlead 
    
This extension's purpose is to avoid that annoying rakes and make development of modular project more fast and code - more clean and well-structured 


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist digitalwand/yii2-modular-app "*"
```

or add

```
"digitalwand/yii2-modular-app": "*"
```

to the require section of your `composer.json` file.

Now you should use module's CoreConsoleApplication and CoreWebApplication instead Yii2
Modify you web/index.php to look like this: 

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

use digitalwand\yii2ModularApp\applications\CoreWebApplication;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
require(__DIR__ . '/../vendor/digitalwand/yii2-modular-app/src/applications/CoreApplication.php');
require(__DIR__ . '/../vendor/digitalwand/yii2-modular-app/src/applications/CoreWebApplication.php');

$config = require __DIR__ . '/../config/web.php';

(new CoreWebApplication($config))->run();
```

and yii file to look like this: 

```php
#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

use digitalwand\yii2ModularApp\applications\CoreConsoleApplication;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require(__DIR__ . '/vendor/digitalwand/yii2-modular-app/src/applications/CoreApplication.php');
require(__DIR__ . '/vendor/digitalwand/yii2-modular-app/src/applications/CoreConsoleApplication.php');

$config = require __DIR__ . '/config/console.php';

$application = new CoreConsoleApplication($config);
$exitCode = $application->run();
exit($exitCode);
```

If everything works like usual, installation is successful!

Creating new modules
--------------------

Using Gii is simplest way to start learning and using new module system. 

In console try 
```shell script
yii gii/strict-module --moduleClass example --moduleID moduleName
```

After generation you will see new directory  @app/modules/example/moduleName 
with controllers, migrations, mail templates and so on, working from the box! 

Enabling and disabling modules
------------------------------

Modules, created this way, could be simply enabled at application's config by special array key:

```php
$config = [
    'customModules' => [
        'example.moduleName'
    ]
];
```
No need to provide module's class name, but if you need - you could pass additional module settings like usual

Module naming
-------------

Keep in mind, that real module name is "moduleName", not "example.moduleName"! 
It is critical for `$app->hasModule()` function, for example. 

The "example" part of module, moduleCalss or moduleNamespace, is simple extension to make your's modules more structured.
But modules with same name but with different namespace will conflict, because Yii see module name only! 