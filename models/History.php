<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "history".
 *
 * @property integer $id
 * @property integer $from_user
 * @property integer $to_user
 * @property integer $value
 * @property integer $dt
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user', 'to_user', 'value', 'dt'], 'required'],
            [['from_user', 'to_user', 'value', 'dt'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user' => 'From User',
            'to_user' => 'To User',
            'value' => 'Value',
            'dt' => 'Dt',
        ];
    }

    public function getUser_from()
    {
        return $this->hasOne(User::className(), ['id' => 'from_user']);
    }

    public function getUser_to()
    {
        return $this->hasOne(User::className(), ['id' => 'to_user']);
    }

    public function getReal_money()
    {
        return $this->value / 100;
    }
}
