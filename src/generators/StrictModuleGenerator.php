<?php


namespace digitalwand\yii2ModularApp\generators;


use Yii;
use yii\gii\CodeFile;
use yii\gii\Generator;
use yii\gii\generators\module\Generator as ModuleGenerator;

class StrictModuleGenerator extends ModuleGenerator
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(Generator::rules(), [
            [['moduleID', 'moduleClass'], 'filter', 'filter' => 'trim'],
            [['moduleID', 'moduleClass'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'moduleID' => 'Module ID',
            'moduleClass' => 'Module Class',
        ];
    }

    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is simple string, describing a group this module belong to, e.g., <code>core</code> or <code>user</code>',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return [
            'module.php',
            'controller.php',
            'view.php',
            'model.php',
            'migration.php',
            'mixin.php',
            'service.php'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/Module.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/DefaultController.php',
            $this->render("controller.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/default/index.php',
            $this->render("view.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/models/ExampleModel.php',
            $this->render("model.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/migrations/m190315_210000_example_migration.php',
            $this->render("migration.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/mixins/ExampleMixin.php',
            $this->render("mixin.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/services/ExampleService.php',
            $this->render("service.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/mail/example-mail-template.php',
            file_get_contents(Yii::getAlias('@vendor/digitalwand/yii2-modular-app/src/generators/default/mail.php'))
        );

        return $files;
    }

    public function getModulePath()
    {
        return Yii::getAlias('@app/modules/' . $this->moduleClass . '/' . $this->moduleID);
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        return 'app\modules\\' . $this->moduleClass . '\\' . $this->moduleID . '\controllers';
    }
}