<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users list';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'nickname',
            [
                'attribute' => 'balance',
                'value' => function ($model) {
                    return $model->real_balance;
                },
            ],
        ],
    ]); ?>

</div>
