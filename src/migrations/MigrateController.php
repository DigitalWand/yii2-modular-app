<?php
/**
 * Created by PhpStorm.
 * User: ASGAlex
 * Date: 16.01.2019
 * Time: 18:50
 */

namespace digitalwand\yii2ModularApp\migrations;


use DirectoryIterator;
use Yii;
use yii\console\controllers\MigrateController as BaseMigrateController;

/**
 * Custom migration controller for autoloading all migrations of custom project's modules
 * Class MigrateController
 * @package digitalwand\yii2ModularApp\lib
 */
class MigrateController extends BaseMigrateController
{
    public function beforeAction($action)
    {
        $this->searchMigrationNamespaces();
        return parent::beforeAction($action);
    }

    /**
     * Scanning modules directories and adding namespaces into migrations list
     *
     * Looking for migrations at this path template: app/modules/<type>/<module>/migrations
     * If the file app/modules/<type>/<module>/Module.php does not exists, the directory will be ignored
     */
    protected function searchMigrationNamespaces()
    {
        $namespaces = [];
        /** @var DirectoryIterator[] $moduleTypes */
        $moduleTypes = new DirectoryIterator(Yii::getAlias('@app/modules'));
        foreach ($moduleTypes as $moduleType) {
            if (!$moduleType->isDir() OR $moduleType->isDot()) continue;

            $moduleDirs = new DirectoryIterator($moduleType->getPath() . '/' . $moduleType->getFilename());
            foreach ($moduleDirs as $moduleDir) {
                if (!$moduleDir->isDir() OR $moduleDir->isDot()) continue;

                $moduleFilePath = $moduleDir->getPath() . '/' . $moduleDir->getFilename() . '/Module.php';
                $moduleMigrationsPath = $moduleDir->getPath() . '/' . $moduleDir->getFilename() . '/migrations';
                if (file_exists($moduleFilePath) and is_dir($moduleMigrationsPath)) {
                    $relativePath = str_replace(Yii::getAlias('@app'), '', $moduleMigrationsPath);
                    $namespaces[] = 'app' . str_replace('/', '\\', $relativePath);
                }
            }
        }

        if (!empty($namespaces)) {
            $this->migrationNamespaces = array_merge($this->migrationNamespaces, $namespaces);
        }
    }
}