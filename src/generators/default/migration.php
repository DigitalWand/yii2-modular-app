<?php
/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator digitalwand\yii2ModularApp\generators\StrictModuleGenerator */

echo "<?php\n";
?>

namespace app\modules\<?= $generator->moduleClass ?>\<?=$generator->moduleID?>\migrations;

use yii\db\Migration;

class m190315_210000_example_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }

}
