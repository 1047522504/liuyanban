<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = '修改: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '留言管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->msg_id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
