<?php


namespace digitalwand\yii2ModularApp\interfaces;


use yii\data\ActiveDataProvider;

interface SearchModelPreset
{
    public function search(array $params = null): ActiveDataProvider;

    public function setSearchParams(array $params): void;

    public function getSearchParams(array $params = null): array;
}