<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Units $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="units-view">

    <div class="card">
        <div class="card-body">
            <table class="table table-sm">
                <tr>
                    <th style="width: 30%;">ID:</th>
                    <td><?= Html::encode($model->id) ?></td>
                </tr>
                <tr>
                    <th>Name:</th>
                    <td><?= Html::encode($model->name) ?></td>
                </tr>
                <tr>
                    <th>Symbol:</th>
                    <td><?= Html::encode($model->symbol) ?></td>
                </tr>
                <tr>
                    <th>Used in Products:</th>
                    <td><?= count($model->products) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Are you sure you want to delete this unit?', 'method' => 'post']
        ]) ?>
        <?= Html::a('Back to Units', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

</div>
