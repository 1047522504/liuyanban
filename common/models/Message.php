<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property integer $msg_id
 * @property integer $pid
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property integer $like_num
 * @property integer $created_at
 * @property integer $updated_at
 */
class Message extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'content'], 'required'],
            [['user_id', 'like_num', 'created_at', 'updated_at','pid'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'msg_id' => 'Msg ID',
            'user_id' => '用户名',
            'title' => '标题',
            'content' => '留言内容',
            'like_num' => '点赞数',
            'created_at' => '留言时间',
            'updated_at' => '修改时间',
        ];
    }
    //关联User表 获取user_id相对应的username
    public function getUser(){

        return $this->hasOne(User::className(),['id'=>'user_id']);

    }
    public function getReply(){
        return $this->hasOne(Reply::className(),['p_id'=>'user_id']);
    }
    //重写beforeSave方法  加入时间修改
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($insert){

                $this->created_at = time();
                $this->updated_at = time();
            }else{

                $this->updated_at = time();
            }

            return true;
        }else{

            return false;
        }
    }

    /**
     * @param $content
     * @return mixed
     * 过滤脏字符串
     */
    public function getWords($content){

        $word = $this->getWord();
        $words = explode(',',$word);
        $words = array_unique($words);
        unset($words[2]);

        //var_dump($words);

        $replace ='****';
        foreach($words as $key=>$word){
            $content=str_replace($word,$replace,$content);
        }
        //var_dump($content);
        return $content;

    }

    public function getWord(){

        $file = "mingan.txt";
        if(file_exists($file)){

            $res = file_get_contents($file);
            $res_new = str_replace("|",",",$res);
            return $res_new;

        }else{

            echo "file not exists!";
        }

    }

}
