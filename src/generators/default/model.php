<?php
/**
 * This is the template for generating a model class file.
 */

/* @var $this yii\web\View */
/* @var $generator digitalwand\yii2ModularApp\generators\StrictModuleGenerator */

echo "<?php\n";
?>

namespace app\modules\<?= $generator->moduleClass ?>\<?= $generator->moduleID ?>\models;

use digitalwand\yii2ModularApp\models\ModularModel;

/**
* <?= $generator->moduleID ?> module definition class
*/

class ExampleModel extends ModularModel
{
    /**
     * Rules should be defined via variable to be modified through behaviors
     * Core function rules() also works fine
     * @var array
     */
    public $rules = [
    ];

    /**
     * Self-describing model information used by ModularModel::__toString()
     * Useful in building interfaces
     * @var array
     */
    static protected $modelName = [
        self::CAPTION_MODE_ALL => 'Name for a list of models',
        self::CAPTION_MODE_ONE => 'Name for an one model',
        self::CAPTION_MODE_EDIT => 'Name for editing model',
    ];

    /**
     * You may want to change the name (self::CAPTION_MODE_ONE) of just one special instance
     * This variable allow you to do this. Also works with magically accessed variables,
     * ex. for ActiveRecord models.
     * @var string
     */
    public $name = '';
}
