<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace app\commands;

use yii\console\Controller;
use yii\helpers\Console;
use \app\rbac\UserGroupRule;
use \app\rbac\UserProfileOwnerRule;
//define ("e", "\r\n") ;
const e="\r\n";
class Rbac2Controller extends Controller{
    private $eWarn=0;
    private $eErr=0;
    private $eOk=0;
    private $roles=[
      3=>['guest',
          'actions'=>[
              'login',
              'logout',
              'error',
              'sign-up',
              'index',
              'view',
          ],
      ],
      2=>['user',
          'actions'=>[
              'update',
          ],
      ],
      1=>['moder',
          'actions'=>[
              'create',
          ],
      ],
      0=>['admin',
          'actions'=>[
              'delete',
          ],
      ],
    ];
   private function eEcho(){
      if ($this->eErr){
         //\Yii::$app->end();
         $out=$this->ansiFormat('Ошибка: '.$this->eErr.e,Console::FG_RED);
      }
      elseif($this->eWarn)
         $out=$this->ansiFormat('Предупр.: '.$this->eWarn.e,Console::FG_YELLOW);
      else{
         $out=$this->ansiFormat( ($this->eOk?$this->eOk:'Ok.'). e,Console::FG_GREEN );
         }
    $this->eOk=0;
    $this->eWarn=0;
    $this->eErr=0;
    echo $out;
   }
   
   public function init(){
      $this->on('afterAction', function($event) {
      //echo $this->ansiFormat('This will be red and underlined.', Console::FG_yellow
         $this->eEcho();
        });
   }
    //
    //движок*************************
    ///
    public function  actionCreateRole($val){
       $authManager = \Yii::$app->authManager;
       if (is_array($val))
          $tmp=$val;
       else
          $tmp=explode(' ',$val);
       $i=0;
       //print_r($tmp);
       foreach($tmp as $name){
           echo 'Добовляем новую роль ';
          echo $this->ansiFormat('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if($authManager->getRole($name)===null){
             $role=$authManager->createRole($name);
             $userGroupRule = new UserGroupRule();
             $authManager->add($userGroupRule);
 
        // Add rule "UserGroupRule" in roles
             $role->ruleName  = $userGroupRule->name;
             $authManager->add($role);
             $i++;
             $this->eEcho();
          }else{
             $this->eWarn="уже существует, игнор.";
             if(count($tmp)>1) $this->eEcho();
          }
       }
       $this->eOk='Добавлено '.$i.' роль(и(ей))';
    }
    public function actionCreatePermission($val) {
      $authManager = \Yii::$app->authManager;
      if (is_array($val))
          $tmp=$val;
       else
          $tmp=explode(' ',$val);
       $i=0;
       foreach($tmp as $name){
          echo 'Добовляем новое разрешение ';
          echo $this->ansiFormat('"' .$name. '"'."-\t",  Console::FG_CYAN);
          if($authManager->getPermission($name)===null){
             $permis=$authManager->createPermission($name);
             $authManager->add($permis);
             $i++;
             $this->eEcho();
          }else{
             $this->eWarn="уже существует, игнор.";
             if(count($tmp)>1) $this->eEcho();
          }
       }
       $this->eOk='Добавлено '.$i.' разрешени(ие(ий))';

    }
    public function actionAddPermissionToRole($roleN,$permis){
        if (is_array($permis))
            $tmp=$permis;
        else
            $tmp=explode(' ',$permis);
        echo 'Добовляем разрешения к роли "'.$roleN.'":'.e;
        $authManager = \Yii::$app->authManager;
        $role=$authManager->getRole($roleN);
        if($role){
            $i=0;
            foreach($tmp as $permissionN){
                echo $this->ansiFormat($permissionN."\t",Console::FG_CYAN);
                if ($tmp2=$authManager->getPermission($permissionN)){
                    if (!array_key_exists($permissionN,$authManager->getPermissionsByRole($roleN))){
                        $authManager->addChild($role,$tmp2);
                        $this->eEcho();
                        $i++;
                   }else{
                    $this->eWarn=' разрешение уже задано игнорируется.';
                    $this->eEcho();                    
                    }
                }else{
                    $this->eWarn=' разрешение не нйдено игнорируется.';
                    $this->eEcho();
                }
            }
            $this->eOk='Добавлено '.$i.' разрешение(ия(ий)).';
        }else{
            //\Yii::$app->end();
            $this->eErr='Роль не найдена.';
        }
    }
    public function actionAddChildRole($roleN, $childNames){
        if (is_array($childNames))
            $tmp=$childNames;
        else
            $tmp=explode(' ',$childNames);
        echo 'Добовляем роль потомка к роли "'.$roleN.'":'.e;
        $authManager = \Yii::$app->authManager;
        $role=$authManager->getRole($roleN);
        if($role){
            $i=0;
            foreach($tmp as $childN){
                echo $this->ansiFormat($childN."\t",Console::FG_CYAN);
                if ($tmp2=$authManager->getRole($childN)){
                    if (!$authManager->hasChild($role,$tmp2)){
                        $authManager->addChild($role,$tmp2);
                        $this->eEcho();
                        $i++;
                    }else{
                    $this->eWarn=' потомок уже задан игнорируется.';
                    $this->eEcho();                    
                    }
                }else{
                    $this->eWarn=' роль потоиок не нйдена игнорируется.';
                    $this->eEcho();
                }
            }
            $this->eOk='Добавлено '.$i.' потомок(а(ов)).';
        }else{
            //\Yii::$app->end();
            $this->eErr='Роль родитель не найдена.';
        }
    }
    private function CheckClassData($data){
        $r=array_keys($data);
        $p=[];
        foreach($r as $rn){
            if (isset($data[$rn]['permissions']))
                foreach($data[$rn]['permissions'] as $pn)
                    if (!in_array($data[$rn], $p))
                        $p[]=$pn;            
        }
        foreach($r as $rn){
            $r[$rn]['c']=[];
            $r[$rn]['p']=[];
            echo $this->ansiFormat(e.'Роль "'.$rn.'":'.e,Console::FG_CYAN);
            if (isset($data[$rn]['permissions'])){
                echo $this->ansiFormat("\tРазрешения: [",  Console::FG_PURPLE);
                foreach($data[$rn]['permissions'] as $pn){
                    if (!in_array($pn,$r[$rn]['p'])){
                        echo $this->ansiFormat($pn.', ',  Console::FG_GREEN);
                        $r[$rn]['p'][]=$pn;
                    }else{
                        $this->eErr=' разрешение "'.$pn.'" уже задано!';
                        return false;
                    }                
                }
                echo $this->ansiFormat(']'.e,Console::FG_PURPLE);
            }
                
            if (isset($data[$rn]['childrens'])){
                echo $this->ansiFormat(e."\tПотомки:[",  Console::FG_BLUE);
                foreach ($data[$rn]['childrens'] as $cn){
                    if (!in_array($cn,$r[$rn]['c'])){
                        echo $this->ansiFormat($cn.', ',  Console::FG_GREEN);
                        $r[$rn]['c'][]=$cn;
                    }else{
                        $this->eErr=' потомок "'.$cn.'" уже задан';
                        return false;
                    }
                }
                echo $this->ansiFormat(']'.e,Console::FG_BLUE);
            }
        }
        return ['pNames'=>$p,'rNames'=>array_keys($data)];
    }
    private function ClearAll($noAsk=false){
        if($rVal=$noAsk||$r_Val=Console::confirm($this->ansiFormat(e.'Внимание!!!',Console::FG_RED).
                ' все настройки будут заменены')){
            $authManager = \Yii::$app->authManager;
            $authManager->removeAll();
        }
        return $rVal;
    }

    public function actionCreateFromeClass($className){
        /* Test*/
        $class='\\app\\rbac\\'.$className;
        $data=new $class;
        $roles=$data->run();
        echo $this->ansiFormat('Новая структура:'.e,  Console::FG_CYAN);
        if ($p=$this->CheckClassData($roles)){
            if ($this->ClearAll()){
                $this->actionCreatePermission($p['pNames']);
                $this->eEcho();
                $this->actionCreateRole($p['rNames']);
                $this->eEcho();
                foreach($p['rNames'] as $rn){
                    if (isset($roles[$rn]['permissions'])){
                        $this->actionAddPermissionToRole($rn,$roles[$rn]['permissions']);
                        $this->eEcho();
                    }
                    if (isset($roles[$rn]['childrens'])){
                        $this->actionAddChildRole ($rn, $roles[$rn]['childrens']);
                        $this->eEcho();
                    }
                }
            }
        }
    }
    
    /*
     * Хэлп
     */
    public function getHelpSummary(){
        return 'Модуль для управления доступом через роли.';
    }
    public function getActionHelp($action){
        $rVal='';
        switch ($action->id){
            case 'create-frome-class':
                //$rVal=$this->parseDocCommentDetail($this->getActionMethodReflection($action));
                $rVal='Создаёт структуру ролей из указанного класса'.e.
                      'Класс должен находится в папке \\app\\rbac'.e;
                  
                 
                break;
            
        }
        return $rVal;
    }
    public function getActionHelpSummary($action){
        $rVal='';
        switch ($action->id){
            case 'create-frome-class':
                $rVal='Создаёт структуру ролей из указанного класса.';
                 break;
            case 'add-child-role':
                $rVal='Добовляет к роли потомка';
                 break;
            case 'add-permission-to-role':
                $rVal='Добовляет к роли разрешение.';
                break;
            case 'create-permission':
                $rVal='Добовляет в набор разрешение';
                break;
            case 'create-role':
                $rVal='Добовляет в набор роли';
                break;
        }
        return $rVal;
    }
    public function getActionArgsHelp($action){
        $rVal=parent::getActionArgsHelp($action);
        switch ($action->id){
            case 'create-frome-class':
                $rVal['className']['comment']='Имя класса.';
                $rVal['className']['required']=true;
                break;
            
        }
        return $rVal;
    }
}
