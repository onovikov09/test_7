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

    <h1><?= Html::encode($this->title) . '<span class="balance">' . $oCurrentUser->real_balance . '</span>$' ?></h1>

    <div>
        <div class="control-transfer" style="width: 70%;">
            <?php
                echo Select2::widget([
                    'name' => 'user_to',
                    'value' => '',
                    'data' => $aUsers,
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

    $css = <<< CSS
        .control-transfer {
            display: inline-block;
            vertical-align: top;
            margin: 15px 0 0 0;
        }
        .modal-body{
            font-size: 20px;
        }
CSS;

    $this->registerCss($css);

    $script = <<< JS
    
    $(document).on('click', '.js_send', function(e) {
        
        var jSelect2 = $("[name='user_to[]']"),
            jMoney = $('[name="money"]'),
            jsOption, sErrors = '';
        
        $('.control-transfer').removeClass('has-error');
        
        if ( !jSelect2.select2('val') || !jMoney.val()) {
            
            if (!jSelect2.select2('val')) {
                jSelect2.closest('.control-transfer').addClass('has-error');
            }
            
            if (!jMoney.val()) {
                jMoney.closest('.control-transfer').addClass('has-error');
            }
            return false;
        }
        
         $.ajax({
            type     :'POST',
            cache    : false,
            url  : '/user/send/',
            data: { nickname: $('[name="user_to[]"]').val(), money: $('[name=money]').val() },
            success  : function(response) {
                if (response.success) {
                    
                    if (response.added_nickname) {
                        for(var k in response.added_nickname) {
                            jsOption = new Option(response.added_nickname[k], response.added_nickname[k], false, false);
                            jSelect2.append(jsOption).trigger('change');       
                        }
                    }
                    
                    jSelect2.select2('val', '[]');
                    jMoney.val(0);
                    
                    $('.balance').text(response.balance);     
                }
                
                $('.modal-body').html(response.message);
                $("#alert-modal").modal('show');
            }
        });
         
         return false;
    });
JS;
    $this->registerJs($script, \yii\web\View::POS_READY);
?>
