<?php
namespace tests\models;
use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(1));
        expect($user->nickname)->equals('demo');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByUsername()
    {
        expect_that(User::findByUsername('demo'));
        expect_not(User::findByUsername('not-demo'));
    }

    public function testLogin()
    {
        expect_that(User::login('demo'));
        expect_not(\Yii::$app->user->isGuest);
    }

    public function testGetNewUser()
    {
        $username = 'demo_' . rand(1111, 9999);
        $user = User::getNewUser($username);
        expect_that($user);
        expect($user->nickname)->equals($username);
    }

    /**
     * @depends testGetNewUser
     * @depends testFindUserByUsername
     */
    public function testFindOrCreateUser()
    {
        $username = 'demo_' . rand(1111, 9999);
        $user = User::findOrCreateUser($username);
        expect_that($user);
        expect($user->nickname)->equals($username);
    }

    /**
     * @depends testGetNewUser
     * @depends testFindUserByUsername
     */
    public function testRealBalance()
    {
        $user = User::findOrCreateUser('demo');
        expect_that($user);

        $user->balance = 155;
        expect($user->real_balance)->equals('1.55$');

        $user->balance = 0;
        expect($user->real_balance)->equals('0.00$');

        $user->balance = 500;
        expect($user->real_balance)->equals('5.00$');

        $user->balance = -555;
        expect($user->real_balance)->equals('-5.55$');
    }

}
