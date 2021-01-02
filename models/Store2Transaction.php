<?php

namespace d3yii2\d3store2\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3store2\models\base\Store2Transaction as BaseStore2Transaction;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "store2_transaction".
 */
class Store2Transaction extends BaseStore2Transaction
{

    /**
     * create store in transaction
     *
     * @param int $stackId
     * @param float $qnt
     * @return Store2Transaction
     * @throws D3ActiveRecordException
     */
    public static function createIn(
        int $stackId,
        float $qnt,
        int $userId = null,
        \DateTime $time = null
    ): self
    {
        if (!$time) {
            $time = new \DateTime();
        }
        $model = new self();
        $model->type = self::TYPE_IN;
        $model->time = $time->format('Y-m-d H:i:s');
        $model->user_id = $userId;
        $model->stack_id = $stackId;
        $model->qnt = $qnt;
        $model->remain_qnt = $qnt;
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }

        return $model;
    }

    /**
     * @param ActiveRecord $model
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function addRefModel($model)
    {
        $modelRef = new Store2TranRef();
        $modelRef->transaction_id = $this->id;
        $modelRef->model_id = SysModelsDictionary::getIdByClassName(get_class($model));
        $modelRef->model_record_id = $model->primaryKey;
        if (!$modelRef->save()) {
            throw new D3ActiveRecordException($modelRef);
        }

    }

    public function transfer(
        int $stackId,
        float $qnt,
        int $userId = null,
        \DateTime $time = null,
        $refs = true
    )
    {
        if (!$time) {
            $time = new \DateTime();
        }
        if ($this->remain_qnt < $qnt) {
            throw new Exception(\Yii::t('store2', 'Insufficient quantity for transfer'));
        }

        $this->remain_qnt -= $qnt;
        if (!$this->save()) {
            throw new D3ActiveRecordException($this);
        }

        $model = new self();
        $model->from_id = $this->id;
        $model->type = self::TYPE_TRANSFER;
        $model->time = $time->format('Y-m-d H:i:s');
        $model->user_id = $userId;
        $model->stack_id = $stackId;
        $model->qnt = $qnt;
        $model->remain_qnt = $qnt;
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }

        if ($refs === true) {
            foreach ($this->store2TranRefs as $ref) {
                $modelRef = new Store2TranRef();
                $modelRef->transaction_id = $model->id;
                $modelRef->model_id = $ref->model_id;
                $modelRef->model_record_id = $ref->model_record_id;
                if (!$modelRef->save()) {
                    throw new D3ActiveRecordException($modelRef);
                }
            }
        }

        return $model;
    }

    public function out(
        float $qnt,
        int $userId = null,
        \DateTime $time = null,
        $refs = true
    )
    {
        if (!$time) {
            $time = new \DateTime();
        }

        if ($this->remain_qnt < $qnt) {
            throw new Exception(\Yii::t('store2', 'Insufficient quantity for out'));
        }

        $this->remain_qnt -= $qnt;
        if (!$this->save()) {
            throw new D3ActiveRecordException($this);
        }

        $model = new self();
        $model->from_id = $this->id;
        $model->type = self::TYPE_OUT;
        $model->time = $time->format('Y-m-d H:i:s');
        $model->user_id = $userId;
        $model->qnt = $qnt;
        $model->remain_qnt = 0;
        if (!$model->save()) {
            throw new D3ActiveRecordException($model);
        }

        if ($refs === true) {
            foreach ($this->store2TranRefs as $ref) {
                $modelRef = new Store2TranRef();
                $modelRef->transaction_id = $model->id;
                $modelRef->model_id = $ref->model_id;
                $modelRef->model_record_id = $ref->model_record_id;
                if (!$modelRef->save()) {
                    throw new D3ActiveRecordException($modelRef);
                }
            }
        }

        return $model;
    }

    public function delete()
    {

        if (!$this->isTypeOut() && $this->qnt !== $this->remain_qnt) {
            throw new Exception(\Yii::t('store2', 'Can not delete. Transaction has dependent transactions'));
        }

        if (!$this->isTypeIn()) {
            $prevTran = $this->from;
            $prevTran->remain_qnt += $this->qnt;
            if (!$prevTran->save()) {
                throw new D3ActiveRecordException($prevTran);
            }
        }

        foreach($this->store2TranRefs as $ref){
            $ref->delete();
        }

        return parent::delete();
    }
}
