<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reply".
 *
 * @property int $id
 * @property int $p_id 父级id 0文章
 * @property int $msg_id 文章
 * @property int $puser 回复人员
 * @property string $comment 评论内容
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 * @property int $leval 级别 0顶级 1其他
 */
class Reply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reply';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['p_id', 'msg_id', 'puser', 'comment', 'created_at','leval'], 'required'],
            [['p_id', 'msg_id', 'created_at', 'leval'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['puser'],'string','max'=>255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'p_id' => 'P ID',
            'user_id' => '回复用户',
            'comment' => '回复内容',
            'created_at' => '回复时间',
            'updated_at' => 'Updated At',
            'msg_id' => 'Msg ID',
            'puser' => 'Puser',
            'leval' => 'Leval',

        ];
    }
    public function getMessage(){

        return $this->hasOne(Message::className(),['msg_id'=>'p_id']);

    }

    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
    public function getBeginning(){
        $tmpStr = strip_tags($this->comment);

        $tmpLen = mb_strlen($tmpStr);

        return mb_substr($tmpStr,0,20,'utf-8').(($tmpLen>20)?'....':'');
    }
}
