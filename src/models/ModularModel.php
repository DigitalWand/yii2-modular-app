<?php


namespace digitalwand\yii2ModularApp\models;


use digitalwand\yii2ModularApp\interfaces\EmptyModelInterface;
use digitalwand\yii2ModularApp\interfaces\NamedModelInterface;
use digitalwand\yii2ModularApp\interfaces\SearchModelPreset;
use ReflectionClass;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class ModularModel extends ActiveRecord
    implements NamedModelInterface, EmptyModelInterface, SearchModelPreset
{
    static protected $modelName = [];

    protected $rules = [];

    protected $_searchParams = [];

    public function rules(): array
    {
        return $this->rules;
    }

    public function setRules($newRules): void
    {
        $this->rules = $newRules;
    }

    static public function getModelName($mode = self::CAPTION_MODE_ALL)
    {
        if ($mode == self::CAPTION_MODE_EDIT) {
            return strtolower(static::$modelName[$mode]);
        }

        return static::$modelName[$mode];
    }

    public function isEmpty()
    {
        return !isset($this->id);
    }

    public function __toString()
    {
        if ($this->isEmpty()) {
            return static::getModelName();
        }

        if(isset($this->name)) {
            return $this->name;
        }

        return '';
    }

    public function setSearchParams(array $params): void
    {
        $this->_searchParams = $params;
    }

    public function getSearchParams(array $params = null): array
    {
        if (is_array($params)) {
            return array_merge($this->_searchParams, $params);
        }

        return $this->_searchParams;
    }

    public function search(array $params = null): ActiveDataProvider
    {
    }

    public function getAttribute($name)
    {
        $value = parent::getAttribute($name);
        if (is_null($value) && isset($this->{$name})) {
            return $this->{$name};
        }
    }

    public function setAttribute($name, $value)
    {
        if (property_exists(get_called_class(), $name)) {
            $this->{$name} = $value;
        } else {
            parent::setAttribute($name, $value);
        }
    }

    public static function loadMultipleNew(&$models, $data, $formName = null)
    {
        if ($formName === null) {
            if (is_string($models)) {
                $reflector = new ReflectionClass($models);
                $formName = $reflector->getShortName();
            } else {
                /* @var $first Model|false */
                $first = reset($models);
                if ($first === false) {
                    return false;
                }
                $formName = $first->formName();
            }
        }

        if(!isset($data[$formName])) return false;

        if (is_string($models)) {
            $className = $models;
            $models = [];
            foreach ($data[$formName] as $i => $v) {
                $models[$i] = new $className();
            }
        }

        $className = get_class(reset($models));
        while (count($models) < count($data[$formName])) {
            $models[] = new $className();
        }

        $successes = [];
        foreach ($models as $i => $model) {
            $successes[$i] = false;
            /* @var $model Model */
            if ($formName == '') {
                if (!empty($data[$i]) && $model->load($data[$i], '')) {
                    $successes[$i] = true;
                }
            } elseif (!empty($data[$formName][$i]) && $model->load($data[$formName][$i], '')) {
                $successes[$i] = true;
            }
        }

        return $successes;
    }

}