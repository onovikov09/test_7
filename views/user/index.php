<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Send money. You balance = ';
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) . '<span class="balance">' . $current_user->real_balance . '</span>' ?></h1>

    <div>
        <div class="control-transfer" style="width: 70%;">
            <?php
                echo Select2::widget([
                    'name' => 'user_to',
                    'value' => '',
                    'data' => $users,
                    'options' => ['placeholder' => 'Select user for send ...', 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'tags' => true
                    ],
                ]);
            ?>
        </div>
        <div class="control-transfer" style="width: 100px;">
            <?php
                echo MaskedInput::widget([
                    'name' => 'money',
                    'clientOptions' => [
                        'alias' =>  'currency',
                        'digits' => 2,
                        'digitsOptional' => false,
                        'radixPoint' => '.',
                        'groupSeparator' => ',',
                        'autoGroup' => true,
                        'placeholder' => '0.00',
                        'allowMinus'=> false,
                    ],
                ]);
            ?>
        </div>
        <div class="control-transfer">
            <?= Html::a('Send', ['send'], ['class' => 'btn btn-success js_send']) ?>
        </div>
    </div>

</div>

<?php

    Modal::begin([
        'size' => 'modal-lg',
        'options' => [
            'id' => 'alert-modal',
        ],
        'header' => '<h2>Information</h2>'
    ]);

    Modal::end();
?>
