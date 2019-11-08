<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator digitalwand\yii2ModularApp\generators\StrictModuleGenerator */

echo "<?php\n";
?>

namespace app\modules\<?= $generator->moduleClass ?>\<?=$generator->moduleID?>;

use digitalwand\yii2ModularApp\modules\CoreModule;

/**
 * <?= $generator->moduleID ?> module definition class
 */
class Module extends CoreModule
{
}
