<?php
/**
 * Created by PhpStorm.
 * User: ASGAlex
 * Date: 16.01.2019
 * Time: 18:07
 */

namespace digitalwand\yii2ModularApp\modules;

use digitalwand\yii2ModularApp\applications\CoreConsoleApplication;
use digitalwand\yii2ModularApp\mixins\ArrayCacheTrait;
use RuntimeException;
use Yii;
use yii\base\Module;
use yii\mail\BaseMailer;

/**
 * Core module class allows:
 * - Autoloading module while calling Module::getInstance() at every place of project
 * - Use own directory to store mail templates. Works when calling $module->getMailer(). Settings $useOwnMailer and $useOwnLayouts should be "true"
 * - Automatically find and setup path to controllers and console commands
 *
 * @see CoreModule::getInstance()
 * @see CoreModule::getMailer()
 *
 * Class CrmModule
 * @package digitalwand\yii2ModularApp\lib
 */
class CoreModule extends Module
{
    use ArrayCacheTrait;

    const EVENT_AFTER_INIT = 'afterInit';
    /**
     * @var bool should separate instance mailer class be used? Necessary for using templates from module
     */
    public $useOwnMailer = true;

    /**
     * @var bool use own paths to mail's header and footer
     * CoreModule::$useOwnMailer should be true
     * @see CoreModule::$useOwnMailer
     */
    public $useOwnLayouts = false;

    public function init()
    {
        Yii::setAlias($this->getPathAlias(),
            '@app/modules/' . static::getModuleType() . '/' . static::getModuleName());
        Yii::setAlias($this->getNamespaceAlias(),
            'app\modules\\' . static::getModuleType() . '\\' . static::getModuleName());

        if (Yii::$app instanceof CoreConsoleApplication) {
            $this->controllerNamespace = Yii::getAlias($this->getNamespaceAlias()) . '\commands';
        } else {
            $this->controllerNamespace = Yii::getAlias($this->getNamespaceAlias()) . '\controllers';
        }

        parent::init();

        $this->trigger(self::EVENT_AFTER_INIT);
    }

    public function getNamespaceAlias()
    {
        return '@' . static::getModuleName() . 'Namespace';
    }

    public function getPathAlias()
    {
        return '@' . static::getModuleName() . 'Path';
    }


    /**
     * Core Yii2 function returns module instance only when module was autoloaded while calling controller explicitly.
     * But if only module API is used in another controller, the module will not be autoloaded and function returns null
     * This behavior makes you to track every time, did module autoloaded successfully or not?
     *
     * This function determines module name by namespace, than autoloads the module if it wasn't autoloaded early.
     * Therefore, you can use Module::getInstance() everywhere, not only in module's controller context.
     *
     * @return static|Module|null
     */
    public static function getInstance()
    {
        $instance = parent::getInstance();
        if (is_null($instance)) {
            $instance = Yii::$app->getModule(static::getModuleName());
        }

        return $instance;
    }

    public static function getModuleName()
    {
        return static::cached(get_called_class() . '-moduleName', function () {
            $classPath = explode('\\', get_called_class());
            $modulePoint = count($classPath) - 2;
            $moduleName = $classPath[$modulePoint];
            if (empty($moduleName)) {
                throw new RuntimeException("Can't parse module name in class " . get_call_stack());
            }

            return $moduleName;
        });
    }

    public static function getModuleType()
    {
        return static::cached(get_called_class() . '-moduleType', function () {
            $classPath = explode('\\', get_called_class());
            $modulePoint = count($classPath) - 3;
            $moduleType = $classPath[$modulePoint];
            if (empty($moduleType)) {
                throw new RuntimeException("Can't parse module type in class " . get_call_stack());
            }

            return $moduleType;
        });
    }

    /** @noinspection PhpUnused */
    public static function getModuleFullName()
    {
        return static::getModuleType() . '.' . static::getModuleName();
    }

    public static function isProd()
    {
        return /*!YII_DEBUG AND */(YII_ENV == 'prod');
    }


    /**
     * @return BaseMailer instance of application's Mailer or separate module's Mailer instance - for using module's mail templates
     * @see CoreModule::$useOwnMailer
     * @see CoreModule::$useOwnLayouts
     */
    public function getMailer()
    {
        if ($this->useOwnMailer) {
            return static::cached('separateMailerInstance', function () {
                /** @var BaseMailer $mailer */
                $mailer = Yii::$app->getMailer();
                $ownMailer = clone $mailer;
                $ownMailer->setViewPath($this->getPathAlias() . '/mail');

                if ($this->useOwnLayouts) {
                    $ownMailer->htmlLayout = $this->getPathAlias() . '/layouts/html.php';
                    $ownMailer->textLayout = $this->getPathAlias() . '/layouts/text.php';
                } else {
                    $ownMailer->htmlLayout = '@app/mail/layouts/html.php';
                    $ownMailer->textLayout = '@app/mail/layouts/text.php';
                }

                return $ownMailer;
            });

        } else {
            return Yii::$app->getMailer();
        }
    }
}
