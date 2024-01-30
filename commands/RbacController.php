<?php
/*
 * Проверка способ 1
 * public function actionAbout()
 *{
 *   if (!\Yii::$app->user->can('about')) {
 *       throw new ForbiddenHttpException('Access denied');
 *   }
 *   return $this->render('about');
 *}
 * ************************
 * 
 * Способ 2
 * public function beforeAction($action)
 *{
 *   if (parent::beforeAction($action)) {
 *       if (!\Yii::$app->user->can($action->id)) {
 *           throw new ForbiddenHttpException('Access denied');
 *       }
 *       return true;
 *   } else {
 *       return false;
 *   }
 *}
 * ************************
 * Команда генерации ключей
 * ./yii rbac/init
 * ************************
 * 
 */

namespace app\commands;
 
use Yii;
use yii\console\Controller;
use \app\rbac\UserGroupRule;
 
class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = \Yii::$app->authManager;
 
        // Create roles
        $guest  = $authManager->createRole('guest');
        $user  = $authManager->createRole('user');
        $moder = $authManager->createRole('moder');
        $admin  = $authManager->createRole('admin');
 
        // Create simple, based on action{$NAME} permissions
        $login  = $authManager->createPermission('login');
        $logout = $authManager->createPermission('logout');
        $error  = $authManager->createPermission('error');
        $signUp = $authManager->createPermission('sign-up');
        $index  = $authManager->createPermission('index');
        $view   = $authManager->createPermission('view');
        $update = $authManager->createPermission('update');
        $delete = $authManager->createPermission('delete');
        $contact = $authManager->createPermission('contact');
 
        // Add permissions in Yii::$app->authManager
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($error);
        $authManager->add($signUp);
        $authManager->add($index);
        $authManager->add($contact);
        $authManager->add($view);
        $authManager->add($update);
        $authManager->add($delete);
 
 
        // Add rule, based on UserExt->group === $user->group
        $userGroupRule = new UserGroupRule();
        $authManager->add($userGroupRule);
 
        // Add rule "UserGroupRule" in roles
        $guest->ruleName  = $userGroupRule->name;
        $user->ruleName  = $userGroupRule->name;
        $moder->ruleName = $userGroupRule->name;
        $admin->ruleName  = $userGroupRule->name;
 
        // Add roles in Yii::$app->authManager
        $authManager->add($guest);
        $authManager->add($user);
        $authManager->add($moder);
        $authManager->add($admin);
 
        // Add permission-per-role in Yii::$app->authManager
        // Guest
        $authManager->addChild($guest, $login);
        $authManager->addChild($guest, $logout);
        $authManager->addChild($guest, $error);
        $authManager->addChild($guest, $signUp);
        $authManager->addChild($guest, $index);
        $authManager->addChild($guest, $view);
 
        // user
        $authManager->addChild($user, $update);
        $authManager->addChild($user, $guest);
 
        // moder
        $authManager->addChild($moder, $contact);
        $authManager->addChild($moder, $guest);
         $authManager->addChild($moder, $user);

        // Admin
        $authManager->addChild($admin, $delete);
        $authManager->addChild($admin, $moder);
    }
}