<?php


namespace d3yii2\d3store2\tests\models;


use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3yii2\d3store2\dictionaries\Store2StackDictionary;
use d3yii2\d3store2\dictionaries\Store2StoreDictionary;
use d3yii2\d3store2\models\Store2Stack;
use d3yii2\d3store2\models\Store2Store;
use d3yii2\d3store2\models\Store2Transaction;
use PHPUnit\Framework\TestCase;


class Store2TransactionTest extends TestCase
{
    public const STORE_NAME = 'TEST01';
    public const STACK1_NAME = 'S1';
    public const STACK2_NAME = 'S2';
    public const COMPANY_ID = 777;


    public static function setUpBeforeClass()
    {

    }

    public function testFull()
    {
        $time = new \DateTime();
        foreach (Store2StackDictionary::getList(self::getStoreId()) as $stackId => $stackName) {
            foreach ($this->getStackRemainTran($stackId)
                         ->all() as $tran
            ) {
                $time->add(new \DateInterval('PT1H'));
                $out = $tran->out($tran->remain_qnt, 1, $time);
            }
        }

        $inStackId = self::getStackId(self::STACK1_NAME);
        $this->assertEquals(0, $this->getStackBalance($inStackId));

        $transferStackId = self::getStackId(self::STACK2_NAME);
        $this->assertEquals(0, $this->getStackBalance($transferStackId));

        $storeModel = Store2Stack::findOne(self::getStoreId());
        $storeModelSysId = SysModelsDictionary::getIdByClassName(Store2Stack::class);

        $time->add(new \DateInterval('PT1H'));
        $tranIn = Store2Transaction::createIn($inStackId, 100, 1, $time);
        $tranIn->addRefModel($storeModel);
        $this->assertEquals(100, $this->getStackBalance($inStackId));

        foreach($tranIn->store2TranRefs as $ref){
            $this->assertEquals($storeModelSysId, $ref->model_id);
            $this->assertEquals($storeModel->id, $ref->model_record_id);
        }

        $time->add(new \DateInterval('PT1H'));
        $tranTransfer = $tranIn->transfer($transferStackId, 50, 1, $time);
        $this->assertEquals(50, $this->getStackBalance($inStackId));
        $this->assertEquals(50, $this->getStackBalance($transferStackId));
        foreach($tranTransfer->store2TranRefs as $ref){
            $this->assertEquals($storeModelSysId, $ref->model_id);
            $this->assertEquals($storeModel->id, $ref->model_record_id);
        }


        $time->add(new \DateInterval('PT1H'));
        $out = $tranTransfer->out(50, 1, $time);
        $this->assertEquals(0, $this->getStackBalance($transferStackId));

        $out->delete();
        $this->assertEquals(50, $this->getStackBalance($transferStackId));

        $tranTransfer->refresh();
        $tranTransfer->delete();
        $this->assertEquals(100, $this->getStackBalance($inStackId));

        $tranIn->refresh();
        $tranIn->delete();
        $this->assertEquals(0, $this->getStackBalance($inStackId));

    }

    /**
     * @return array
     * @throws D3ActiveRecordException
     */
    public static function getStoreId(): int
    {
        $storeList = Store2StoreDictionary::getActiveList(self::COMPANY_ID);
        if ($storeId = array_search(self::STORE_NAME, $storeList)) {
            return $storeId;
        }

        $store = new Store2Store();
        $store->company_id = self::COMPANY_ID;
        $store->name = self::STORE_NAME;
        $store->active = 1;
        if (!$store->save()) {
            throw new D3ActiveRecordException($store);
        }
        return $store->id;


    }

    /**
     * @param int $stackId
     * @return \yii\db\ActiveQuery
     */
    public function getStackRemainTran(int $stackId): \yii\db\ActiveQuery
    {
        return \d3yii2\d3store2\models\Store2Transaction::find()
            ->where([
                'stack_id' => $stackId
            ])
            ->andWhere('remain_qnt>0');
    }

    public static function getStackId($stackName)
    {
        $storeId = self::getStoreId();
        $stackList = Store2StackDictionary::getList($storeId);
        if ($sackId = array_search($stackName, $stackList)) {
            return $sackId;
        }
        $stack = new Store2Stack();
        $stack->store_id = $storeId;
        $stack->name = $stackName;
        $stack->active = 1;
        if (!$stack->save()) {
            throw new D3ActiveRecordException($stack);
        }
        return $stack->id;

    }

    public function getStackBalance(int $stackId): float
    {
        return $this->getStackRemainTran($stackId)->sum('remain_qnt') ?? 0;
    }
}