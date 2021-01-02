<?php

namespace d3yii2\d3store2\dictionaries;

use d3yii2\d3store2\models\Store2Store;
use Yii;
use yii\helpers\ArrayHelper;

class Store2StoreDictionary
{

    private const CACHE_KEY_LIST = 'Store2StoreDictionaryList';

    /**
     * @param int $companyId
     * @return array
     */
    public static function getActiveList(int $companyId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createKey($companyId, 1),
            static function () use ($companyId) {
                return ArrayHelper::map(
                    Store2Store::find()
                        ->select([
                            'id' => 'id',
                            'name' => 'name',
                        ])
                        ->where([
                            'company_id' => $companyId,
                            'active' => 1
                        ])
                        ->orderBy([
                            'name' => SORT_ASC,
                        ])
                        ->asArray()
                        ->all()
                    ,
                    'id',
                    'name'
                );
            },
            60 * 60
        );
    }

    private static function createKey(int $companyId, int $isActive)
    {
        return self::CACHE_KEY_LIST . '-' . $companyId . '-' . $isActive;
    }

    /**
     * @param int $companyId
     * @return array
     */
    public static function getFullList(int $companyId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createKey($companyId, 0),
            static function () use ($companyId) {
                return ArrayHelper::map(
                    Store2Store::find()
                        ->select([
                            'id' => 'id',
                            'name' => 'id',
                        ])
                        ->where([
                            'company_id' => $companyId,
                        ])
                        ->orderBy([
                            'name' => SORT_ASC,
                        ])
                        ->asArray()
                        ->all()
                    ,
                    'id',
                    'name'
                );
            },
            60 * 60
        );
    }

    public static function clearCache(): void
    {
        foreach (Store2Store::find()
                     ->distinct()
                     ->select('company_id')
                     ->column() as $companyId
        ) {
            Yii::$app->cache->delete(self::createKey($companyId, 0));
            Yii::$app->cache->delete(self::createKey($companyId, 1));
        }
    }
}
