<?php

namespace d3yii2\d3store2\dictionaries;

use d3yii2\d3store2\models\Store2Stack;
use Yii;
use yii\helpers\ArrayHelper;

class Store2StackDictionary
{

    private const CACHE_KEY_LIST = 'Store2StackDictionaryList';

    public static function getList(int $storeId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createKey($storeId, 0),
            static function () use ($storeId) {
                return ArrayHelper::map(
                    Store2Stack::find()
                        ->select([
                            'id' => 'id',
                            'name' => 'name',
                        ])
                        ->where([
                            'store_id' => $storeId,
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

    private static function createKey(int $storeId, int $isFull)
    {
        return self::CACHE_KEY_LIST . '-' . $storeId . '-' . $isFull;
    }

    public static function getFullList(int $storeId): array
    {
        return Yii::$app->cache->getOrSet(
            self::createKey($storeId, 1),
            static function () use ($storeId) {
                return ArrayHelper::map(
                    Store2Stack::find()
                        ->select([
                            'id' => 'id',
                            'name' => 'name',
                        ])
                        ->where([
                            'store_id' => $storeId
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
        foreach (Store2Stack::find()
                     ->distinct()
                     ->select('store_id')
                     ->column() as $storeId
        ) {
            Yii::$app->cache->delete(self::createKey($storeId, 0));
            Yii::$app->cache->delete(self::createKey($storeId, 1));
        }

    }
}
