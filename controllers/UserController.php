<?php

namespace app\controllers;

use app\models\History;
use Yii;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * UserController implements the profile actions for user.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'send' => ['POST'],
                ],
            ]
        ];
    }

    /**
     * Render page for send money.
     *
     * @return Response
     */
    public function actionMoney()
    {
        $oCurrentUser = Yii::$app->user->getIdentity();
        $oUsers = User::find()->select('nickname')->where(['NOT IN', 'id', [$oCurrentUser->getId()]])->all();

        return $this->render('index', [
            'aUsers' => ArrayHelper::map($oUsers, 'nickname', 'nickname'),
            'oCurrentUser' => $oCurrentUser
        ]);
    }

    /**
     * Transfer money.
     *
     * @return Response
     */
    public function actionSend()
    {
        $aNewNickname = []; $isHasError = false;

        $nMoney = Yii::$app->request->post('money', 0);
        $nMoney = abs(floatval(str_replace('$', '', $nMoney)) * 100);

        $aUsersName = Yii::$app->request->post('nickname', []);

        if (empty($aUsersName)) {
            return $this->json(false, ['message' => 'Specify nickname!']);
        }

        if (!$nMoney) {
            return $this->json(false, ['message' => 'Specify amount of transfer!']);
        }

        $oFromUser = Yii::$app->user->getIdentity();

        if (in_array($oFromUser->nickname, $aUsersName)) {
            $aUsersName = array_filter($aUsersName, function($sValue) use ($oFromUser){
                return $sValue != $oFromUser->nickname;
            });
        }

        if (empty($aUsersName)) {
            return $this->json(false, ['message' => 'You can not send money to yourself!']);
        }

        foreach ($aUsersName as $sUserName)
        {
            $oFromUser->balance -= $nMoney;
            $oToUser = User::findOrCreateUser($sUserName);
            $oToUser->balance += $nMoney;
            if ($oToUser->isNew) {
                $aNewNickname[] = $sUserName;
            }

            $oHistory = new History();
            $oHistory->to_user = $oToUser->id;
            $oHistory->from_user = $oFromUser->id;
            $oHistory->dt = time();
            $oHistory->value = $nMoney;

            if ($oFromUser->validate() && $oToUser->validate() && $oHistory->validate())
            {
                $oFromUser->save(false);
                $oToUser->save(false);
                $oHistory->save(false);

            } else {
                $isHasError = true;
            }
        }

        if (!$isHasError)
        {
            return $this->json(true,
                [
                    'message' => 'You sent ' . $nMoney / 100 . '$ to ' . implode(', ', $aUsersName),
                    'added_nickname' => $aNewNickname,
                    'balance' => $oFromUser->real_balance
                ]);
        }

        return $this->json(false, ['message' => 'Error, please try again later!']);
    }

    /**
     * Show history transfers.
     *
     * @return Response
     */
    public function actionHistory()
    {
        $nUserId = Yii::$app->user->getIdentity()->getId();

        $dataProvider = new ActiveDataProvider([
            'query' => History::find()->where(['from_user' => $nUserId])->orWhere(['to_user' => $nUserId])
                ->with(['user_from','user_to']),
        ]);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'nUserId' => $nUserId
        ]);
    }
}
