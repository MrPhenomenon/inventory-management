<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Units';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="units-index">

    <p>
        <?= Html::a('Create Unit', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'symbol',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, ['class' => 'btn btn-info btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('Update', $url, ['class' => 'btn btn-warning btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('Delete', $url, [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Are you sure?', 'method' => 'post']
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        return ['unit/view', 'id' => $model->id];
                    }
                    if ($action === 'update') {
                        return ['unit/update', 'id' => $model->id];
                    }
                    if ($action === 'delete') {
                        return ['unit/delete', 'id' => $model->id];
                    }
                    return '#';
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
