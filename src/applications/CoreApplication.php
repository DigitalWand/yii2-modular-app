<?php
/**
 * Created by PhpStorm.
 * User: ASGAlex
 * Date: 16.01.2019
 * Time: 23:00
 */

namespace digitalwand\yii2ModularApp\applications;


use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\helpers\Inflector;
use yii\web\Application;

/**
 * Trait CrmBaseApplication
 * @package digitalwand\yii2ModularApp\lib
 * @mixin Application
 */
trait CoreApplication
{
    /**
     * @var array custom modules list
     * @see Application::$modules
     */
    public $customModules = [];

    /**
     * @var string template for custom modules naming
     */
    private $moduleClassTemplate = 'app\modules\%TYPE%\%MODULE%\Module';

    /**
     * Allows to list custom modules in this way:
     * ```
     * [
     *      'type.moduleName', // You can specify only module's name
     *      'type.moduleName' => [
     *          // Or you can specify additional parameters, if needed
     *      ]
     * ]
     * ```
     * Specifying module's class is not mandatory - it parsed automatically from name
     * Recommended way of modules naming is "type.moduleName", but you also could use:
     *  - type.moduleName
     *  - type\moduleName
     *  - type/moduleName
     *
     * @throws InvalidConfigException
     * @see $moduleClassTemplate
     *
     */
    public function bootstrap()
    {
        foreach ($this->customModules as $moduleName => $moduleSettings) {
            if (is_string($moduleSettings)) {

                list($type, $name) = $this->parseModuleTypeAndName($moduleSettings);
                $this->setModule($name, [
                    'class' => $this->makeModuleClass($type, $name)
                ]);

                unset($this->customModules[$moduleName]);

            } elseif (is_array($moduleSettings)) {

                list($type, $name) = $this->parseModuleTypeAndName($moduleName);
                $moduleSettings['class'] = $this->makeModuleClass($type, $name);
                $this->setModule($name, $moduleSettings);

                unset($this->customModules[$moduleName]);

            }
        }

        parent::bootstrap();

    }

    /**
     * @param $route
     * @return array|bool
     * @throws InvalidConfigException
     */
    public function createController($route)
    {
        if (($result = parent::createController($route)) !== false) {
            return $result;
        }
        $customModuleRoute = $route;
        list($namespace, $customModuleId, $customModuleRoute) = array_pad(explode('/', $customModuleRoute, 3), 3, null);
        if ($customModuleId !== null) {
            $module = $this->getModule($customModuleId);
            if ($module !== null) {
                if (is_null($customModuleRoute)) {
                    $customModuleRoute = 'default';
                }
                return $module->createController($customModuleRoute);
            }
        }

        return false;
    }

    /**
     * @param $id
     * @param bool $load
     * @return null|Module
     * @throws InvalidConfigException
     */
    public function getModule($id, $load = true)
    {
        if (($result = parent::getModule($id, $load)) !== null) {
            return $result;
        }

        $ids = $this->getModuleIdVariation($id);
        $moduleKey = $this->findModuleKeyByListIds($ids);

        if (!is_null($moduleKey)) {
            if ($this->getModules()[$moduleKey] instanceof self) {
                return $this->getModules()[$moduleKey];
            } elseif ($load) {
                Yii::debug("Loading module: $moduleKey", __METHOD__);
                /* @var $module Module */
                $module = Yii::createObject($this->getModules()[$moduleKey], [$moduleKey, $this]);
                $module->setInstance($module);

                return $this->getModules()[$moduleKey] = $module;
            }
        }
    }

    private function parseModuleTypeAndName($moduleName)
    {
        list($type, $name) = explode('.', $moduleName);
        if (is_null($type) OR is_null($name)) {
            list($type, $name) = explode('/', $moduleName);
            if (is_null($type) OR is_null($name)) {
                list($type, $name) = explode('\\', $moduleName);
                if (is_null($type) OR is_null($name)) return [null, null];
            }
        }

        return [$type, $name];
    }

    private function makeModuleClass($type, $name)
    {
        return str_replace([
            '%TYPE%',
            '%MODULE%'
        ], [
            $type,
            $name
        ], $this->moduleClassTemplate);
    }

    /**
     * Generate keys variations for module naming
     * @param $id
     * @return array
     */
    private function getModuleIdVariation($id) {
        return [
            $id,
            Inflector::camelize($id),
            Inflector::variablize($id)
        ];
    }

    /**
     * Searching the module by keys variants
     * @param $keys
     * @return mixed|null
     */
    private function findModuleKeyByListIds($keys)
    {
        if (!is_array($keys)) {
            $keys = func_get_args();
            array_shift($keys);
        }

        foreach ($keys as $key) {
            if (isset($this->getModules()[$key]) || array_key_exists($key, $this->getModules())) {
                return $key;
            }
        }

        return null;
    }
}
