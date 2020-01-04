<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Message;

/**
 * MessageSearch represents the model behind the search form about `common\models\Message`.
 */
class MessageSearch extends Message
{
    // 重写attributes（）方法，增加一个属性
    public function attributes()
    {
        //使用array_merge 在原有的基础上添加一个属性
        return array_merge(parent::attributes(),['username']);
    }

    /**
     * @inheritdoc
     */
    //在子模型里面的rules方法里面添加进新加的属性
    public function rules()
    {
        return [
            [['msg_id', 'user_id', 'like_num', 'created_at', 'updated_at'], 'integer'],
            [['title', 'content','username'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Message::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([

            'query' => $query,
            'pagination' => ['pageSize'=>5],
            'sort' => [
                'defaultOrder' => [
                    'msg_id'=>SORT_DESC,
                ],
                'attributes'=>['msg_id'],
            ]

        ]);

        $this->load($params);
        //$this->validate判断提交过来的数据是否符合规则
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'msg_id' => $this->msg_id,
            'user_id' => $this->user_id,
            'like_num' => $this->like_num,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content]);
        //连表查询
        $query->join('INNER JOIN','user','user.id=message.user_id');
        $query->andFilterWhere(['like','user.username',$this->username]);
        //另外一种排序方式
        $dataProvider->sort->attributes['username']=[
            'asc'=>['user.username'=>SORT_ASC],
            'desc'=>['user.username'=>SORT_DESC]
        ];


        return $dataProvider;
    }
}
