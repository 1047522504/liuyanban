<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Admin */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => '管理员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定要删除吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            //'password',
            'nickname',
            'profile',
            'ip',

            ['attribute'=>'created_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            ['attribute'=>'updated_at',
                'format'=>['date','php:Y-m-d H:i:s'],
            ],
            //'created_at',
            //'updated_at',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
        ],
    ]) ?>

</div>
