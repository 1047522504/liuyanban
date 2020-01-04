<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '留言管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>

        <?= Html::a('删除', ['delete', 'id' => $model->msg_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,//显示相对应的model里面的数据
        'attributes' => [//需要显示那些数据
            'msg_id',
            //'user_id',
            ['label'=>'用户名',
              'value'=>$model->user->username,
            ],
            'title',
            'content:ntext',
            'like_num',
            //'created_at',
            ['attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['attribute'=>'updated_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            //'updated_at',
        ],
    ]) ?>

</div>
