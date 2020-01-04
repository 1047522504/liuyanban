<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "like_ip".
 *
 * @property int $id
 * @property int $msg_id
 * @property int $user_id
 * @property int $created_at
 */
class LikeIp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'like_ip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['msg_id', 'user_id', 'created_at'], 'required'],
            [['msg_id', 'user_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'msg_id' => 'Msg ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public function getMessage(){
        return $this->hasOne(Message::className(),['msg_id'=>'msg_id']);
    }
}
