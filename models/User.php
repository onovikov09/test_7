<?php

namespace app\models;

use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    public $isNew = false;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id){
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['nickname' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nickname'], 'required'],
            [['balance', 'is_active'], 'integer'],
            [['nickname'], 'string', 'max' => 255],
            [['nickname'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nickname' => 'Username',
            'balance' => 'Balance',
            'is_active' => 'Is Active',
        ];
    }

    /**
     * Login user be username
     *
     * @param string $username
     * @param boolean $remember
     * @return bool
     */
    public static function login($username, $remember = true)
    {
        return Yii::$app->user->login(static::findOrCreateUser($username), ($remember ? 3600*24*30 : 0));
    }

    /**
     * Create new user
     *
     * @param string $username
     * @return User
     */
    public static function getNewUser($username)
    {
        $user = new static();
        $user->nickname = $username;
        $user->isNew = true;
        if ($user->validate()) {
            $user->save(false);
        }
        return $user;
    }

    /**
     * If user isset - return, else registration by username
     *
     * @param string $username
     * @return User
     */
    public static function findOrCreateUser($username)
    {
        $user = static::findByUsername($username);
        if(empty($user))
        {
            $user = static::getNewUser($username);
        }
        return $user;
    }

    /**
     * Return float balance. Only for display!
     *
     * @return float
     */
    public function getReal_balance()
    {
        if (!$this->balance) {
            return '0.00$';
        }
        return number_format(($this->balance / 100), 2) . '$';
    }
}
