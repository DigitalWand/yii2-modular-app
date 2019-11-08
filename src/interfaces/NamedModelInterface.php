<?php


namespace digitalwand\yii2ModularApp\interfaces;

/**
 * Allow add names to model class itself, not only for instances
 *
 * Interface NamedModelInterface
 * @package digitalwand\yii2ModularApp\interfaces
 */
interface NamedModelInterface
{
    const CAPTION_MODE_ALL = 0;
    const CAPTION_MODE_ONE = 1;
    const CAPTION_MODE_EDIT = 2;

    static public function getModelName($mode);
}