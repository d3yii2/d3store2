<?php

namespace d3yii2\d3store2\models;

use d3yii2\d3store2\dictionaries\Store2StackDictionary;
use d3yii2\d3store2\models\base\Store2Stack as BaseStore2Stack;

/**
 * This is the model class for table "store2_stack".
 */
class Store2Stack extends BaseStore2Stack
{
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Store2StackDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        Store2StackDictionary::clearCache();
    }
}
