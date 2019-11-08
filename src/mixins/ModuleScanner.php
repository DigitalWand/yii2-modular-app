<?php

namespace digitalwand\yii2ModularApp\mixins;

use Yii;

/**
 * Trait ModuleScanner
 * @package digitalwand\yii2ModularApp\mixins
 *
 * Scan enabled custom modules and filter out matched to given interface
 * Should be used in function with Application instance available, ex. bootstrap($app)
 * Module which uses ModuleScanner, should also be listed in "bootstrap" array in config
 */
trait ModuleScanner
{
    public function searchModules($app, $filterByInterface)
    {

        foreach ($app->modules as $moduleId => $module) {
            if (is_array($module) and isset($module['class'])) {
                $moduleClassName = $module['class'];
                try {
                    $interfaces = class_implements($moduleClassName);

                    if ($interfaces && in_array($filterByInterface, $interfaces)) {
                        $module = Yii::$app->getModule($moduleId);
                    } else {
                        continue;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (is_subclass_of($module, $filterByInterface)) {
                yield $module;
            }
        }
    }
}