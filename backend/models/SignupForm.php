<?php
namespace backend\models;

use yii\base\Model;
use common\models\Admin;
use yii\helpers\VarDumper;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $password;
    public $nickname;
    public $password_repeat;
    public $profile;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\Admin', 'message' => '用户名已经存在.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password_repeat','compare','compareAttribute'=>'password','message'=>'两次输入不一致'],
            ['nickname','required'],
            ['nickname','string','max'=>128],
            ['profile','string'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password' => '密码',
            'password_repeat'=>'重置密码',
            'nickname' => '昵称',
            'profile' => '简介',



        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {

        if (!$this->validate()) {

            return null;

        }

        $user = new Admin();
        $user->username = $this->username;
        $user->nickname = $this->nickname;
        $user->profile = $this->profile;
        $user->ip = $_SERVER['REMOTE_ADDR'];
        $user->password = '***';
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }
}
