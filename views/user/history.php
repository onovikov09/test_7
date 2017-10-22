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
                'value' => function ($model) use ($user_id) {
                    if ($model->from_user == $user_id) {
                        return 'I am to';
                    }
                    return $model->user_from->nickname;
                },
            ],
            [
                'attribute' => 'To user',
                'value' => function ($model) use ($user_id) {
                    if ($model->to_user == $user_id) {
                        return 'To me';
                    }
                    return $model->user_to->nickname;
                },
            ],
            [
                'attribute' => 'Money',
                'value' => function ($model) use ($user_id)
                {
                    return (($model->from_user == $user_id) ? '-' : '+' ) . $model->real_money;
                },
            ],
            [
                'attribute' => 'Date send',
                'value' => function ($model) {
                    return date("d.m.Y H:i:s", $model->dt);
                },
            ]
        ],
    ]); ?>

</div>
