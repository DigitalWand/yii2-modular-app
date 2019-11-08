<?php


namespace digitalwand\yii2ModularApp\assets;


use RuntimeException;
use yii\web\AssetBundle;

class CoreAssetBundle extends AssetBundle
{
    public function __construct($config = [])
    {
        $classPath = explode('\\', get_called_class());
        $modulePoint = count($classPath) - 3;
        $moduleName = $classPath[$modulePoint];
        $moduleTypePoint = count($classPath) - 4;
        $moduleType = $classPath[$moduleTypePoint];
        if (empty($moduleName) || empty($moduleType)) {
            throw new RuntimeException("Can't parse module name in class " . get_call_stack());
        }

        $this->sourcePath = "@app/modules/$moduleType/$moduleName/assets";

        parent::__construct($config);
    }
}