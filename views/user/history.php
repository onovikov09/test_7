<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'History of money transfers';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'From user',
                'format' => 'raw',
                'value' => function ($model) use ($nUserId) {
                    if ($model->from_user == $nUserId) {
                        return 'I am to';
                    }
                    return $model->user_from->nickname;
                },
            ],
            [
                'attribute' => 'To user',
                'format' => 'raw',
                'value' => function ($model) use ($nUserId) {
                    if ($model->to_user == $nUserId) {
                        return 'To me';
                    }
                    return $model->user_to->nickname;
                },
            ],
            [
                'attribute' => 'Money',
                'format' => 'raw',
                'value' => function ($model) use ($nUserId)
                {
                    return (($model->from_user == $nUserId) ? '-' : '+' ) . $model->real_money;
                },
            ],
            [
                'attribute' => 'Date send',
                'format' => 'raw',
                'value' => function ($model) {
                    return date("d.m.Y H:i:s", $model->dt);
                },
            ]
        ],
    ]); ?>

</div>
