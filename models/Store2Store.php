<?php

namespace d3yii2\d3store2\models;

use d3yii2\d3store2\dictionaries\Store2StoreDictionary;
use d3yii2\d3store2\models\base\Store2Store as BaseStore2Store;

/**
 * This is the model class for table "store2_store".
 */
class Store2Store extends BaseStore2Store
{
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Store2StoreDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Store2StoreDictionary::clearCache();
    }
}
