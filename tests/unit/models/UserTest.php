<?php
namespace tests\models;
use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($oUser = User::findIdentity(2));
        expect($oUser->nickname)->equals('demo');

        expect_not(User::findIdentity(1));
    }

    public function testFindUserByUsername()
    {
        expect_that(User::findByUsername('demo'));
        expect_not(User::findByUsername('not-demo'));
    }

    /**
     * @depends testFindUserByUsername
     */
    /*public function testValidateUser($user)
    {
        $user = User::findByUsername('demo');
        expect_that($user->validateAuthKey('test100key'));
        expect_not($user->validateAuthKey('test102key'));

        expect_that($user->validatePassword('admin'));
        expect_not($user->validatePassword('123456'));        
    }*/

    public function testLogin()
    {
        expect_that(User::login('demo'));
        expect_not(\Yii::$app->user->isGuest);
    }

    public function testGetNewUser()
    {
        $sUserName = 'demo_' . rand(1111, 9999);
        $oUser = User::getNewUser($sUserName);
        expect_that($oUser);
        expect($oUser->nickname)->equals($sUserName);
    }

    /**
     * @depends testGetNewUser
     * @depends testFindUserByUsername
     */
    public function testFindOrCreateUser()
    {
        $sUserName = 'demo_' . rand(1111, 9999);
        $oUser = User::findOrCreateUser($sUserName);
        expect_that($oUser);
        expect($oUser->nickname)->equals($sUserName);
    }

    /**
     * @depends testGetNewUser
     * @depends testFindUserByUsername
     */
    public function testRealBalance()
    {
        $oUser = User::findOrCreateUser('demo');
        expect_that($oUser);

        $oUser->balance = 155;
        expect($oUser->real_balance)->equals('1.55$');

        $oUser->balance = 0;
        expect($oUser->real_balance)->equals('0.00$');

        $oUser->balance = 500;
        expect($oUser->real_balance)->equals('5.00$');

        $oUser->balance = -555;
        expect($oUser->real_balance)->equals('-5.55$');
    }

}
