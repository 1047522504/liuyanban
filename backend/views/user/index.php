<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建用户', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],


            ['attribute'=>'id',
                'contentOptions'=>['width'=>'30px']
                ],
            'username',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
             'email:email',
            ['attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],

            ['attribute'=>'updated_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['attribute'=>'status',
                'value'=>'StatusStr'
            ],
            //'created_at',
            // 'updated_at',
            // 'verification_token',

            ['class' => 'yii\grid\ActionColumn',
                'template'=>'{update}'
                ],
        ],
    ]); ?>
</div>
