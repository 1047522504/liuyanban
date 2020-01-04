<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '留言管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('创建留言', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'msg_id',
            ['attribute'=>'msg_id',
                'contentOptions'=>['width'=>'25px']
                ],
           ['attribute' => 'username',
                'label'=>'用户名',
                'value' => 'user.username',

               ],

            'title',
            'content:ntext',
            'like_num',

            ['attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],


            ['class' => 'yii\grid\ActionColumn',

                'template'=>'{view}{update}{delete}'
                ],
        ],
    ]); ?>
</div>
