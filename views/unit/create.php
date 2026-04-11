<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Units $model */

$this->title = 'Create Unit';
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="units-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary mt-3']) ?>

</div>
