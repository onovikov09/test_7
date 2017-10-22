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
        $current_user = Yii::$app->user->getIdentity();
        $users = User::find()->select('nickname')->where(['NOT IN', 'id', [$current_user->getId()]])->all();

        return $this->render('index', [
            'users' => ArrayHelper::map($users, 'nickname', 'nickname'),
            'current_user' => $current_user
        ]);
    }

    /**
     * Transfer money.
     *
     * @return Response
     */
    public function actionSend()
    {
        $new_nicknames = []; $has_error = false;

        $money = Yii::$app->request->post('money', 0);
        $money = abs(floatval(str_replace('$', '', $money)) * 100);

        $nicknames = Yii::$app->request->post('nickname', []);

        if (empty($nicknames)) {
            return $this->json(false, ['message' => 'Specify nickname!']);
        }

        if (!$money) {
            return $this->json(false, ['message' => 'Specify amount of transfer!']);
        }

        $current_user = Yii::$app->user->getIdentity();

        if (in_array($current_user->nickname, $nicknames)) {
            $nicknames = array_filter($nicknames, function($value) use ($current_user){
                return $value != $current_user->nickname;
            });
        }

        if (empty($nicknames)) {
            return $this->json(false, ['message' => 'You can not send money to yourself!']);
        }

        foreach ($nicknames as $username)
        {
            $current_user->balance -= $money;
            $to_user = User::findOrCreateUser($username);
            $to_user->balance += $money;
            if ($to_user->isNew) {
                $new_nicknames[] = $username;
            }

            $history = new History();
            $history->to_user = $to_user->id;
            $history->dt = time();
            $history->value = $money;
            $history->link('user_from', $current_user);

            if ($current_user->validate() && $to_user->validate() && $history->validate())
            {
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();

                try {

                    $current_user->save(false);
                    $to_user->save(false);
                    $history->save(false);

                    $transaction->commit();
                } catch(\Exception $e) {
                    $transaction->rollBack();
                    $has_error = true;
                } catch(\Throwable $e) {
                    $transaction->rollBack();
                    $has_error = true;
                }

            } else {
                $has_error = true;
            }
        }

        if (!$has_error)
        {
            return $this->json(true,
                [
                    'message' => 'You sent ' . $money / 100 . '$ to ' . implode(', ', $nicknames),
                    'added_nickname' => $new_nicknames,
                    'balance' => $current_user->real_balance
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
        $user_id = Yii::$app->user->getIdentity()->getId();

        $dataProvider = new ActiveDataProvider([
            'query' => History::find()->where(['from_user' => $user_id])->orWhere(['to_user' => $user_id])
                ->with(['user_from','user_to']),
        ]);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'user_id' => $user_id
        ]);
    }
}
