<?php
namespace app\models;
use Yii;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
   public $authKey;      //????
   public $accessToken;  //????
   private static $_RBAC=[];
   private $_utypesList=null;
   public static $lastUserId=null;

        public function getName(){return $this->username;}
    	public static function tableName()
	{  
		return 'tbl_user';
	}
        public function getRole(){
            if (!$this->_utypesList){
                $this->_utypesList=self::getUtypesListS();
            }
            if ($this->utype){
                return $this->_utypesList[$this->utype];
            }else
                $rVal='guest';
           return $rVal;
        }
    public static function getUtypesListS(){
        $rVal=[];
        foreach (MyRbac::find()->select(['id','engname'])->asArray()->all() as $val){
            $rVal[(int)$val['id']]=$val['engname'];
        }
        return $rVal;
    }
    public function getUtypesList(){
        if (!$this->_utypesList){
            $this->_utypesList=self::getUtypesListS();
        }
        return $this->_utypesList;
    }

    private static $_identityCache=[];

    /**
     * @inheritdoc
     */
    public static function findIdentity($_id)
    {
        $id=(int)$_id;
        Yii::debug(['id'=>$id,'cache'=>self::$_identityCache],'Cache');
        if (array_key_exists($id, self::$_identityCache)){
            return self::$_identityCache[$id];
        }

        if ($md=self::findOne($id)){
           self::$_identityCache[$id]=new static($md);
           Yii::debug(['id'=>$id,'cache'=>self::$_identityCache],'Cache - create');
           return self::$_identityCache[$id];
        }else{
            self::$_identityCache[$id]=false;
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
      /*
      * проверка токена и возврат
      * return new static($user);
      * пока не использ.
      */ 

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {       
        if(  $md=User::findOne(['username' => $username])){
           return new static( $md);
           }
        else
           return null;
           
    }    

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc   чё за фигня хз.!!!
     */
     
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc то-же хз!!!
     */
     
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
            return Yii::$app->getSecurity()->validatePassword($password,$this->password);
    }
    public function hashPassword($password)
    {
            return Yii::$app->security->generatePasswordHash($password);
            return CPasswordHelper::hashPassword($password);
    }
    public function can($val){
        if (is_string($val)){
            return (bool)$this->_can($val);
        }elseif(is_array($val)){
            foreach ($val as $el){
                if (!$this->_can($el)) return false;
            }
            return true;
        }else{
            return false;
        }
    }
    private function _can($val){
        if ($val==='site/login'||$val==='site/logout')
            return true;
        elseif ($this->getRole()==='admin'){
            return true;
        }elseif(Yii::$app->user->isGuest){
            return false;
        }
        $val=$val[0]=='/'?substr($val, 1):$val;
        
        //Кэш
        if (array_key_exists($this->utype, self::$_RBAC)){
            $model=self::$_RBAC[$this->utype];
        }else{
            $model= MyRbac::findOne($this->utype);
            if ($model){
                self::$_RBAC[$this->utype]=$model;
            }
        }
        
        if ($model){
            $tmp=\yii\helpers\Json::decode($model->value);
        }else{
            $tmp= $this->controllers_actions?\yii\helpers\Json::decode($this->controllers_actions):['allow'=>[],'denied'=>[]];
        }
        $tmpRToController= explode('/', $val);
        if (count($tmpRToController)>2){
            $allStr=$tmpRToController[0].'/'.$tmpRToController[1].'/all';
        }else{
            $allStr=$tmpRToController[0].'/all';
        }
        if (!$chk= in_array($allStr, $tmp['allow']))
            $chk=in_array($val, $tmp['allow']);
        if ($chk) $chk=!in_array($val, $tmp['denied']);
//        Yii::trace(\yii\helpers\VarDumper::dumpAsString($tmpRToController),'user');
//        Yii::trace(\yii\helpers\VarDumper::dumpAsString($tmp),'user');
//        Yii::trace('route: '.$val,'user');
//        Yii::trace('routeToAll: '.$allStr,'user');
        return (bool)($chk!=false || $this->getRole()==='admin');
    }
    public function userInfo(){
        $lOut=$this->logout_time;
        if ($this->control_time+Yii::$app->user->authTimeout>time() || $lOut>$this->control_time){
            $isLin=false;
            if ($lOut < $this->control_time) $lOut=$this->control_time+Yii::$app->user->authTimeout;
        }else{
            $isLin=true;
        }
        return [
            'isLoggetIn'=>$isLin,
            'lastLoginText'=>$this->login_time?Yii::$app->formatter->asDate($this->login_time):'',
            'lastLogoutText'=>$lOut?Yii::$app->formatter->asDate($lOut):'',
            'timeLeft'=>Yii::$app->formatter->asDuration(($this->control_time+Yii::$app->user->authTimeout)-time()),
            'name'=>$this->realname,
            'activitiTime'=>Yii::$app->formatter->asDatetime($this->control_time)
        ];
    }
    public function activeUserInfo(){
        $rVal=[];
        foreach (TblUser::find()
            ->select(['id','login_time','control_time','logout_time','realname'])
            ->where(['>','tbl_user.control_time',time()-Yii::$app->user->authTimeout])
            ->all() as $el){
            $rVal[$el->id]=$el->userInfo();
        }
        return $rVal;
    }
    public function setLastZakaz($val){
        if ($user=TblUser::findOne($this->id)){
            $user->last_zakaz=$val;
            $user->update(['last_zakaz']);
        }
    }
    public function getLastZakaz(){
        if ($user=TblUser::findOne($this->id)){
            return $user->last_zakaz;
        }        
    }
}
