<?php
/**
 * Created by PhpStorm.
 * User: ASGAlex
 * Date: 16.01.2019
 * Time: 23:12
 */

namespace digitalwand\yii2ModularApp\applications;


use digitalwand\yii2ModularApp\generators\StrictModuleGenerator;
use yii\console\Application;

/**
 * Main purpose of this class - initialise custom migration controller
 * @see \digitalwand\yii2ModularApp\migrations\MigrateController
 *
 * Class CrmConsoleApplication
 * @package digitalwand\yii2ModularApp\lib
 */
class CoreConsoleApplication extends Application
{
    use CoreApplication {
        bootstrap as public bootstrapBase;
    }

    public function __construct($config = [])
    {
        $this->initGenerators($config);
        parent::__construct($config);
    }

    /**
     * @param array $config
     */
    protected function initGenerators(&$config = [])
    {
        if (!YII_ENV_DEV OR !isset($config['modules']['gii'])) {
            return;
        }

        if (!isset($config['modules']['gii']['generators'])) {
            $config['modules']['gii']['generators'] = [];
        }

        $config['modules']['gii']['generators']['strict-module'] = [
            'class' => StrictModuleGenerator::class,
            'templates' => [
                'default' => '@vendor/digitalwand/yii2-modular-app/src/generators/default',
            ]
        ];

    }
    /**
     * @see \digitalwand\yii2ModularApp\migrations\MigrateController
     */
    public function bootstrap()
    {
        if(!isset($this->controllerMap['migrate']['class'])){
            $this->controllerMap['migrate']['class'] = 'digitalwand\yii2ModularApp\migrations\MigrateController';
        }

        $this->bootstrapBase();
    }


}