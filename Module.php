<?php

namespace d3yii2\d3store2;

use Yii;
use d3system\yii2\base\D3Module;

class Module extends D3Module
{
    public $controllerNamespace = 'd3yii2\d3store2\controllers';

    public function getLabel(): string
    {
        return Yii::t('store2','d3store2');
    }
}
