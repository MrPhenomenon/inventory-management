<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Units $model */

$this->title = 'Update Unit: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="units-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-secondary mt-3']) ?>

</div>
